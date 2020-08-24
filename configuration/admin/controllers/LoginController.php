<?php /** @noinspection PhpMissingParentCallMagicInspection */

include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');

class LoginController extends Controller
{

    public $usedHelpers = array(
        'password_encoder',
    );

	private $_user;

	public $controllerPublicName = 'Login';

    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function initialize()
	{
		$this->moduleSettings=new ModuleSettingsReaderController;
	}

    public function loginAction()
    {
        $user=$this->getRememberedUser();

		if ( $user && $user['active']==1 )
		{
			$this->setUser( $user );
			$this->doLogin();
			$this->doRememberMe( array('remember'=>true) );
			$this->redirect($this->getLoginStartPage());
		}

        $this->setPageName( $this->translate('Login') );

		$this->includeLocalMenu=false;

        // check wheter the user has entered a username and password
        if ( $this->rHasVal('username') && $this->rHasVal('password') )
		{
            if ( $this->authenticateUser( array( 'username'=>$this->rGetVal('username'),'password'=> $this->rGetVal('password') ) ) )
			{
				$user=$this->getUserByUsername( $this->rGetVal('username') );

				if ( $user['active']==1 )
				{
					$this->setUser( $user );
					$this->doLogin();
					$this->doRememberMe( array('remember'=>(null!==$this->rGetVal('remember_me') && $this->rGetVal('remember_me')=='1') ) );
					$this->redirect( $this->getLoginStartPage() );
				}
			}

			$this->addError( $this->translate('Login failed.') );
        }

        /** @setting support_email (string, e-mail) */
		$this->smarty->assign( 'support_email', $this->moduleSettings->getGeneralSetting( ['setting'=>'support_email','subst'=>'linnaeus@naturalis.nl','no_auth_check'=>true ] ) );

        $this->printPage();
    }

	public function loginUser( $p )
	{
		if ( $this->authenticateUser( $p ) )
		{
			$user=$this->getUserByUsername( $p['username'] );

			if ( $user['active']==1 )
			{
				$this->setUser( $user );
				$this->doLogin();
				return true;
			}
		}

		return false;
	}


    private function getLoginStartPage()
    {
        if (!empty($_SESSION['admin']['login_start_page']))
		{
            $startpage=$_SESSION['admin']['login_start_page'];
			unset($_SESSION['admin']['login_start_page']);
        }
		else
		{
			$startpage = $this->baseUrl . $this->getAppName();

//			if (isset($_SESSION['admin']['user']) && $_SESSION['admin']['user']['_number_of_projects']==1)
//			{
//				$startpage.=$this->generalSettings['paths']['projectIndex'];
//			}
//			else
			{
				$startpage.=$this->generalSettings['paths']['chooseProject'];
			}

        }

		return $startpage;
    }

    public function logoutAction ()
    {
        $this->setPageName($this->translate('Logout'));
        $this->destroyUserSession();
        $this->unsetRememberMeCookie();
        $this->redirect('login.php');
    }

    private function destroyUserSession ()
    {
		unset($_SESSION['admin']);
        session_destroy();
    }

    private function setUser( $user )
    {
		$this->_user=$user;
    }

    private function getUser()
    {
		return $this->_user;
    }

    private function setRememberMeCookie()
    {
        setcookie(
            $this->generalSettings['login-cookie']['name'],
            $this->getCurrentUserId(),
            time() + (86400 * $this->generalSettings['login-cookie']['lifetime'])
        );
    }

    private function getRememberMeCookie()
    {
        return isset($_COOKIE[$this->generalSettings['login-cookie']['name']]) ? $_COOKIE[$this->generalSettings['login-cookie']['name']] : false;
    }

    private function unsetRememberMeCookie()
    {
        setcookie(
            $this->generalSettings['login-cookie']['name'],
            false,
            time() - 86400
        );
    }

    private function getRememberedUser()
    {
        $c = $this->getRememberMeCookie();

        if ($c)
		{
            $d=$this->models->Users->_get( array('id'=>array('id' => $c, 'active' => '1') ) );
			if ($d) return $d[0];
        }
		else
		{
            return false;
        }
    }

	private function authenticateUser( $p )
	{
		$username=isset($p['username']) ? $p['username'] : null;
		$password=isset($p['password']) ? $p['password'] : null;

		if ( is_null($username) || is_null($password) ) return false;

		$user = $this->getUserByUsername( $username );

		// unknown user or user without password
		if ( is_null($user) || !isset($user['password']) )
		{
			return false;
		}

		// checking password
		if (password_verify( $password, $user['password'] ) )
		{
			return true;
		}

		// password fail can mean old md5 encryption: need to update (after verifying)
		$this->helpers->PasswordEncoder->setForceMd5( true );
		$this->helpers->PasswordEncoder->setPassword( $password );
		$this->helpers->PasswordEncoder->encodePassword();

		// verifying
		if ( $this->helpers->PasswordEncoder->getHash()==$user['password'] )
		{
			// generating new password hash
			$this->helpers->PasswordEncoder->setForceMd5( false );
			$this->helpers->PasswordEncoder->encodePassword();

			// updating
			$this->models->Users->update(
				array( 'password'=>$this->helpers->PasswordEncoder->getHash() ),
				array( 'id'=>$user['id'],'username'=>$username )
			);

			return true;
		}

		return false;
	}

	private function getUserByUsername( $username )
	{
		if ( empty($username) ) return;

		$d=$this->models->Users->_get( array( 'id' => array('username' => $username ) ) );

		return isset($d[0]) ? $d[0] : null;
	}

    private function doLogin()
    {
		$user=$this->getUser();

		if ( !isset($user) ) return;

        // update last and number of logins
        $this->models->Users->save(
            array(
                'id' => $user['id'],
                'last_login' => 'now()',
                'logins' => 'logins+1'
            )
        );


        $this->initUserSession( array('user'=>$user) );



		/*
        // save all relevant data to the session
        $this->initUserSession(array(
			'user'=>$user,
			'roles'=>$cur['roles'],
			'rights'=>$cur['rights'],
			'number_of_projects'=>$cur['number_of_projects']
		));
		*/

        // determine and set the default active project
        //$this->setDefaultProject();
		//$this->setCurrentUserRoleId();

		 return isset($_SESSION['admin']['user']['id']) ? $_SESSION['admin']['user']['id'] : null;
	}

	private function doRememberMe( $p )
	{
		$remember=isset($p['remember']) ? $p['remember'] : null;

        if ( $remember )
		{
            $this->setRememberMeCookie();
        }
		else
		{
            $this->unsetRememberMeCookie();
        }
    }

    private function initUserSession( $p )
    {
		$user=isset($p['user']) ? $p['user'] : null;
//		$roles=isset($p['roles']) ? $p['roles'] : null;
//		$rights=isset($p['rights']) ? $p['rights'] : null;
//		$number_of_projects=isset($p['number_of_projects']) ? $p['number_of_projects'] : null;

        if (!$user) return;

        $_SESSION['admin']['user'] = $user;
        $_SESSION['admin']['user']['_login']['time'] = time();
        $_SESSION['admin']['user']['_said_welcome'] = false;
        $_SESSION['admin']['user']['_logged_in'] = true;
    }

}