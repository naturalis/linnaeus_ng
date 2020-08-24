<?php /** @noinspection PhpMissingParentCallMagicInspection */
/**
 *  The webservices controller handles the Webservices api
 */
include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');
include_once ('LoginController.php');
include_once ('ProjectsController.php');

class WebservicesController extends Controller
{
    public $controllerPublicName = 'Webservices';

	private $_projectId;
	private $_project;

	private $_data=array();
	private $_head;

	private $_JSONPCallback=false;
	private $_JSON=null;

	// Hard-coded for the time being...
    // @todo: this should also move to the settings?
	private $_key = 'gNXhIb4LDKrA7MQmNo7wpV';

    public $usedHelpers = array(
        'http_basic_authentication'
    );

    public $extraModels = array(
        'ProjectsModel',
        'UsersModel'
    );

	private $_services = array(
		'projects.php'=>array(
			'description'=>'all projects on this server'
		),
		'project_users.php'=>array(
			'description'=>'all users in a project',
			'parameters'=>array(
				'pid'=>array('mandatory'=>true,'description'=>'project ID')
				)
		),
		'users.php'=>array(
			'description'=>'all users on this server'
		),
	);

	private $_generalParameters=array('callback'=>array('mandatory'=>false,'description'=>'name of JSONP callback function'));

    /**
     * WebservicesController constructor.
     * @param null $p
     */
    public function __construct($p=null)
    {
        parent::__construct( $p );
		$this->initialise();
    }

    /**
     * WebservicesController destructor.
     */
    public function __destruct ()
    {
        parent::__destruct();
    }


    /**
     * Show the index page
     */
    public function indexAction()
	{
		$this->authenticateUser();
	    $this->smarty->assign( 'base_url', 'https://' . '%AUTH%' . $_SERVER['HTTP_HOST'] . pathinfo($_SERVER['PHP_SELF'])['dirname'] . '/' );
		$this->smarty->assign( 'services', $this->_services );
		$this->smarty->assign( 'general_parameters', $this->_generalParameters );

		$this->printPage();
	}


    /**
     * Show the projects page
     */
    public function projectsAction()
	{
		$this->authenticateUser();
	    $this->_head->service='projects';
		$this->_head->description='list of all projects on this server';

		foreach($this->models->ProjectsModel->getUserProjects( array( 'user_id'=>$this->getCurrentUserId(), 'show_all'=>true ) ) as $project)
		{
			$d=new stdClass;

			$d->id=$project['id'];
			$d->sys_name=$project['sys_name'];
			$d->sys_description=$project['sys_description'];
			$d->title=$project['title'];
			$d->published=$project['published'];

			$this->_data[]=$d;
		}

        header('Content-Type: application/json');
		$this->printOutput();
	}

    /**
     * Show the project users page
     */
	public function projectUsersAction()
	{
		$this->authenticateUser();
	    $this->authenticateProject();
		$this->_head->service='project_users';
		$this->_head->description=sprintf('list of all users in project "%s"' , $this->_project['sys_name'] );
		$data=$this->models->UsersModel->getProjectUsers(array('project_id'=>$this->getCurrentProjectId()));
		array_walk($data, function(&$a) { unset($a['password']);});
		$this->_data=$data;
		$this->printOutput();
	}

    /**
     * Show the users page
     */
	public function usersAction()
	{
		$this->authenticateUser();
	    $this->_head->service='users';
		$this->_head->description=sprintf('list of all users' );
		$data=$this->models->UsersModel->getAllUsers();
		array_walk($data, function(&$a) { unset($a['password']);});
		$this->_data=$data;
		$this->printOutput();
	}

    /**
     * Get the linnaeus data
     */
	private function getLinnaeusData()
    {
        $branch = [];
        $hash = '';
        $latestHash = '';
        $ahead = 0;
        $behind = 0;

        exec('git rev-parse --abbrev-ref HEAD', $branch);
        exec('git fetch ' . $branch[0]);
        exec('git rev-parse HEAD', $hash);
        exec('git rev-parse origin/' . $branch[0], $latestHash);
        exec('git rev-list origin/' . $branch[0] . '..HEAD --count', $ahead);
        exec('git rev-list HEAD..origin/' . $branch[0] . ' --count', $behind);
        exec('git describe', $description);
        exec('git show -s --format=%ci', $lastcommit);

        $data = $this->models->ProjectsModel->getProjectsWithUsers();

        foreach ($data as $i => $row) {
            $data[$i]['git_branch'] = $branch[0];
            $data[$i]['git_hash'] = $hash[0];
            $data[$i]['git_latest_hash'] = $latestHash[0];
            $data[$i]['git_commits_ahead'] = $ahead[0];
            $data[$i]['git_commits_behind'] = $behind[0];
            $data[$i]['git_commit_date'] = $lastcommit[0];
            $data[$i]['code_up_to_date'] =
                ($data[$i]['git_hash'] == $data[$i]['git_latest_hash']) ? 'yes' : 'no';
            $data[$i]['project_is_published'] =
                ($data[$i]['project_is_published'] == 1) ? 'yes' : 'no';
            $data[$i]['user_is_active'] =
                ($data[$i]['user_is_active'] == 1) ? 'yes' : 'no';
            $data[$i]['server_ip'] = $_SERVER['SERVER_ADDR'];
            $data[$i]['server_name'] = gethostbyaddr($data[$i]['server_ip']);
            $data[$i]['check_date'] = date("Y-m-d H:m:s");
        }

        return $data;
    }

    /**
     * scan the servers show the data
     */
    public function scanServersAction ()
    {
		$this->_head->service = 'projects and users';
		$this->_head->description = sprintf('"flat file" list of all projects and their users');

        if (!$this->rHasVal('key') || $this->rGetVal('key') !== $this->_key) {

            die('<p>Incorrect key!</p><p><img src="../../media/system/access-denied.gif"></p>');

        } else {
    		$this->_data = $this->getLinnaeusData();
        }

		$this->printOutput();
    }

    /**
     * scan the servers push the data to a central server
     */
    public function linnaeusDataPushAction ()
    {
		$this->_head->service = 'push Linnaeus data';
		$this->_head->description = sprintf('push Linnaeus data to central server');

        if (!$this->rHasVal('key') || $this->rGetVal('key') !== $this->_key) {

            die('<p>Incorrect key!</p><p><img src="../../media/system/access-denied.gif"></p>');

        } else {

            $data = $this->getLinnaeusData();

            /** @setting 'push_Url' defines the central push server to push installation information to */
        	$url = !empty($this->generalSettings['pushUrl']) ?
        	   $this->generalSettings['pushUrl'] : 'http://linnaeus.naturalis.nl/admin/server_csv.php';

        	/**
             * Post the information to the central server by http post
             * @var _data
             */
    		$this->_data = $this->getCurlResult(array(
                'url' => $url,
                'post' => http_build_query(array('lng_data' => json_encode($data)))
    		));
        }

		$this->printOutput();
    }


    /**
     * set project Id
     */
	private function setProjectId()
	{
		$this->_projectId=$this->rGetVal('pid');
	}

    /**
     * get project Id
     */
	private function getProjectId()
	{
		return $this->_projectId;
	}

    /**
     * initialise the web services controller
     */
    private function initialise()
    {
		$this->setProjectId();
		$this->checkJSONPCallback();

		$this->_head=new stdClass;
	}

    /**
     * set the Project
     */
    private function setProject()
    {
		$d=$this->models->ProjectsModel->getUserProjects( array( 'user_id'=>$this->getCurrentUserId(), 'show_all'=>true, 'project_id'=> $this->getProjectId()) );
		$this->_project=$d[0];
	}

    /**
     * authenticate User
     */
	private function authenticateUser()
	{
		$this->loginController=new LoginController;

		$this->helpers->HttpBasicAuthentication->setVerificationCallback(
			function($username,$password)
			{
				return $this->loginController->loginUser(array('username'=>$username,'password'=>$password));
			}
		);

		$this->helpers->HttpBasicAuthentication->authenticate() or die('not authorized');
	}

    /**
     * authenticate Project
     */
	private function authenticateProject()
	{
		if ( is_null($this->getProjectId()) ) die('no project id');

		$this->_head->projectId=$this->getProjectId();
		$this->setProject();

		if ( !$this->_project ) die('unknown project id');

		$this->projectsController=new ProjectsController;
		$this->projectsController->doChooseProject( $this->getProjectId() );
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );

		if ( !$this->getAuthorisationState() ) die('user not authorized for project');
    }

    /**
     * print the result in json
     */
	private function printOutput()
	{
		$this->_server=new stdClass;
		$this->_server->address=$_SERVER['SERVER_ADDR'];
		$this->_server->name=$_SERVER['SERVER_NAME'];
		$this->_server->host_name=$_SERVER['HTTP_HOST'];

		$this->_JSON=json_encode(array_merge((array)$this->_head,array("results"=>(array)$this->_data,(array)$this->_server)));

		if ( $this->hasJSONPCallback() )
		{
			$this->_JSON = $this->getJSONPCallback() . '(' . $this->_JSON .');';
		}

		header('Content-Type: application/json');

		echo $this->_JSON;
	}

    /**
     *  check if jsonp callback
     */
	private function checkJSONPCallback()
	{
		if ($this->rHasVal('callback'))
		{
			$this->setJSONPCallback($this->rGetVal('callback'));
		}
	}

    /**
     *  set callback
     */
	private function setJSONPCallback($callback)
	{
		$this->_JSONPCallback=$callback;
	}

    /**
     *  get callback
     */
	private function getJSONPCallback()
	{
		return $this->_JSONPCallback;
	}

    /**
     *  check callback
     */
	private function hasJSONPCallback()
	{
		return $this->getJSONPCallback()!=false;
	}

}