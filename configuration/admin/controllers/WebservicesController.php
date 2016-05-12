<?php

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

    public function __construct($p=null)
    {
        parent::__construct( $p );
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }



	public function indexAction()
	{
		$this->smarty->assign( 'base_url', 'http://' . '%AUTH%' . $_SERVER['HTTP_HOST'] . pathinfo($_SERVER['PHP_SELF'])['dirname'] . '/' );
		$this->smarty->assign( 'services', $this->_services );
		$this->smarty->assign( 'general_parameters', $this->_generalParameters );

		$this->printPage();
	}




	public function projectsAction()
	{
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

		$this->printOutput();
	}

	public function projectUsersAction()
	{
		$this->authenticateProject();
		$this->_head->service='project_users';
		$this->_head->description=sprintf('list of all users in project "%s"' , $this->_project['sys_name'] );
		$data=$this->models->UsersModel->getProjectUsers(array('project_id'=>$this->getCurrentProjectId()));
		array_walk($data, function(&$a) { unset($a['password']);});
		$this->_data=$data;
		$this->printOutput();
	}

	public function usersAction()
	{
		$this->_head->service='users';
		$this->_head->description=sprintf('list of all users' );
		$data=$this->models->UsersModel->getAllUsers();
		array_walk($data, function(&$a) { unset($a['password']);});
		$this->_data=$data;
		$this->printOutput();
	}



	private function setProjectId()
	{
		$this->_projectId=$this->rGetVal('pid');
	}

	private function getProjectId()
	{
		return $this->_projectId;
	}

    private function initialise()
    {
		$this->setProjectId();
		$this->authenticateUser();
		$this->checkJSONPCallback();

		$this->_head=new stdClass;
	}

    private function setProject()
    {
		$d=$this->models->ProjectsModel->getUserProjects( array( 'user_id'=>$this->getCurrentUserId(), 'show_all'=>true, 'project_id'=> $this->getProjectId()) );
		$this->_project=$d[0];
	}

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

	private function checkJSONPCallback()
	{
		if ($this->rHasVal('callback'))
		{
			$this->setJSONPCallback($this->rGetVal('callback'));
		}
	}

	private function setJSONPCallback($callback)
	{
		$this->_JSONPCallback=$callback;
	}

	private function getJSONPCallback()
	{
		return $this->_JSONPCallback;
	}

	private function hasJSONPCallback()
	{
		return $this->getJSONPCallback()!=false;
	}

}