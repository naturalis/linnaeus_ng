<?php

	include_once(dirname(__FILE__)."/../BaseClass.php");

	include_once(dirname(__FILE__)."/../../../smarty/Smarty.class.php");

	class Controller extends BaseClass {
	
		public $smarty;
		private $smartySettings;
		private $viewName;
		private $controllerBaseName;
		private $fullPath;
		public $pageName;
		public $requestData;
		public $data;
		public $errors;
		public $messages;
		private $currentUserId;
		private $currentProjectId;
		public $sortField;
		public $sortDirection;
		public $sortCaseSensitivity;
		private $helpTexts;
		public $controllerPublicName;
		public $randomValue;
		public $usedHelpers;

		private $usedModelsBase = array('helptext');

		/**
		* Constructor, calls parent's constructor and all initialisation functions
		*
		* @access 	public
		*/
		public function __construct() {

			parent::__construct();

			$this->setDebugMode();

			$this->startSession();

			$this->setNames();

			$this->setSmarty();

			$this->setRequestData();

			$this->loadModels();

			$this->loadHelpers();

			$this->setHelpTexts();
			
			$this->setMiscellaneous();
			
			$this->setPaths();

		}

		/**
		* Destroys!
		*
		* @access 	public
		*/
		public function __destruct() {

			parent::__destruct();

		}

		/**
		* Starts the user's session
		*
		* @access 	private
		*/
		private function startSession() {
		
			session_start();
		
		}

		/**
		* Sets a global 'debug' mode, based on a general setting in the config file
		*
		* @access 	private
		*/
		private function setDebugMode() {

			$this->debugMode = $this->generalSettings['debugMode'];

		}

		/**
		* Sets class variables, based on a page's url
		* 
		* Sets the following:
		*   full path ('/admin/views/projects/collaborators.php')
		*   application name ('admin')
		*   controller's base name ('projects' for 'ProjectsController')
		*   view name ('collaborators')
		*
		* @access 	private
		*/
		private function setNames() {

			$this->fullPath = $_SERVER['PHP_SELF'];

			$path = pathinfo(substr_replace($this->fullPath,'',0,strlen($this->generalSettings['rootWebUrl'])-1));

			$dirs = explode('/',$path['dirname']);

			if (!empty($dirs[1])) $this->appName = strtolower($dirs[1]);

			if (!empty($dirs[3])) $this->controllerBaseName = strtolower($dirs[3]);
			
			/*
			if (!$this->controllerBaseName) {

				$this->controllerBaseName = strtolower(str_replace('Controller','',get_class($this)));

			}
			*/

			if (!empty($path['filename'])) $this->viewName = $path['filename'];

		}

		/**
		* Returns the application name
		*
		* @return 	string	application name
		* @access 	public
		*/
		public function getAppName() {

			return $this->appName;

		}

		/**
		* Returns the controller's base name
		*
		* @return 	string	controller's base name
		* @access 	public
		*/
		public function getControllerBaseName() {

			return $this->controllerBaseName;

		}

		/**
		* Returns the current view's name
		*
		* @return 	string	current view's name
		* @access 	public
		*/
		public function getViewName() {

			return $this->viewName;

		}

		/**
		* Sets general Smarty variables (paths, compilder directives)
		*
		* @access 	private
		*/
		private function setSmarty() {

			$this->smartySettings = $this->config->getSmartySettings();

			$this->smarty = new Smarty();

			/* DEBUG */
			$this->smarty->force_compile = true;

			$this->smarty->template_dir = $this->smartySettings['dir_template'].'/'.$this->getControllerBaseName().'/';
			$this->smarty->compile_dir = $this->smartySettings['dir_compile'];
			$this->smarty->cache_dir = $this->smartySettings['dir_cache'];
			$this->smarty->config_dir = $this->smartySettings['dir_config'];
			$this->smarty->caching = $this->smartySettings['caching'];
			$this->smarty->compile_check = $this->smartySettings['compile_check'];

		}

		/**
		* Assigns POST and GET variables to a class variable 'requestData'; posted files to 'requestDataFiles'
		*
		* @access 	private
		*/
		private function setRequestData() {

			$this->requestData = false;

			$this->requestDataFiles = false;

			//$this->requestData = $_REQUEST; // also contains cookies
			$this->requestData = array_merge((array)$_GET,(array)$_POST); // don't want no cookies!

			foreach((array)$_FILES as $key => $val) {

				if (isset($val['size']) && $val['size']>0) $this->requestDataFiles[] = $val;

			}

		}

		/**
		* Loads the required models (database abstraction classes for the various tables)
		*
		* Takes the model's names specified in the class variables usedModelsBase and usedModels,
		* loads the corresponding class files, and initiates an instance of each model class 
		* as object of the class variable 'models'.
		*
		* @access 	private
		*/
		private function loadModels() {
		
			$d = array_unique(array_merge((array)$this->usedModelsBase,(array)$this->usedModels));

			foreach((array)$d as $key) {
			
				if (file_exists(dirname(__FILE__).'/../models/'.$key.'.php')) {

					require_once(dirname(__FILE__).'/../models/'.$key.'.php');
	
					$t = str_replace(' ','',ucwords(str_replace('_',' ',$key)));
	
	
					if (class_exists($t)) {

						$this->models->$t = new $t();

						//echo $t.chr(10);
					}

				} 

			}

		}

		/**
		* Loads the required helpers (separate multi-use classes)
		*
		* Takes the helper's names specified in the class variables usedHelpers,
		* loads the corresponding class files, and initiates an instance of each helper class 
		* as object of the class variable 'helpers'.
		*
		* @access 	private
		*/
		private function loadHelpers() {

			foreach((array)$this->usedHelpers as $key) {

				if (file_exists(dirname(__FILE__).'/../helpers/'.$key.'.php')) {
	
					require_once(dirname(__FILE__).'/../helpers/'.$key.'.php');
	
					$d = str_replace(' ','',ucwords(str_replace('_',' ',$key)));
	
					if (class_exists($d)) {

						$this->helpers->$d = new $d();
					
					}

				}

			}

		}

		/**
		* Loads the help texts for the current view into the class variable 'helpTexts'
		*
		* @access 	private
		*/
		private function setHelpTexts() {

			$this->helpTexts = 
				$this->models->Helptext->get(
					array(
						'controller'=>$this->getControllerBaseName() ? $this->getControllerBaseName() : '-',
						'view'=>$this->getViewName()
					), false, 'show_order'
				);
			
		}

		/**
		* Returns the class variable 'helptexts', which contains all the pages's help texts
		*
		* @return 	array	array with all help texts
		* @access 	private
		*/
		private function getHelpTexts() {

			return $this->helpTexts;
			
		}

		/**
		* Sets project paths for image uploads etc. and makes sure they actually exist
		* 
		* @access 	private
		*/
		private function setPaths() {
		
			$p = $this->getCurrentProjectId();
			
			if ($p) {
		
				$_SESSION['project']['paths']['project_images'] = 
					$this->generalSettings['directories']["imageDirProject"].'/'.
					sprintf('%04s',$p).'/';
	
				$_SESSION['project']['paths']['uploads_images'] = 
					$this->generalSettings['directories']["imageDirUpload"].'/'.
					sprintf('%04s',$p).'/';
	
				foreach((array)$_SESSION['project']['paths'] as $key => $val) {
	
					if (!file_exists($val)) mkdir($val);
	
				}	

			}

		}

		
		/**
		* Initialises miscellaneous variables
		*
		* sets default filemask
		* sets default max upload size
		* sets a random value
		*
		* @access 	private
		*/		
		private function setMiscellaneous() {

			$this->setDefaultUploadSaveDir();

			$this->setDefaultUploadFilemask();

			$this->setDefaultUploadMaxSize();

			$this->setRandomValue();

		}

		/**
		* Assigns basic Smarty variables and renders the page
		*
		* @access 	public
		*/
		public function printPage() {

			$this->smarty->assign('debugMode', $this->debugMode);

			$this->smarty->assign('rootWebUrl', $this->generalSettings['rootWebUrl']);
			$this->smarty->assign('controllerPublicName', $this->controllerPublicName);
			$this->smarty->assign('session', $_SESSION);

			$this->smarty->assign('rnd', $this->getRandomValue());


			$this->smarty->assign('errors', $this->getErrors());
			$this->smarty->assign('messages', $this->getMessages());
			$this->smarty->assign('helpTexts', $this->getHelpTexts());

			$this->smarty->assign('applicationName', $this->generalSettings['applicationName']);
			$this->smarty->assign('applicationVersion', $this->generalSettings['applicationVersion']);
			$this->smarty->assign('pageName', $this->getPageName());

			$this->smarty->display(strtolower($this->getViewName().'.tpl'));

		}

		/**
		* Redirects the user to another page (and avoids circular redirection)
		*
		* @param  	string	$url	url to redirect to; can be false, in which case HTTP_REFERER is used
		* @access 	public
		*/
		public function redirect($url = false) {
		
			if (!$url) {

				$url = $_SERVER['HTTP_REFERER'];
				
			}


			if (basename($url)==$url) {

				$circular = (basename($this->fullPath) == $url);

			} else {
			
				$circular = ($this->fullPath == $url);			
			
			}

			if ($url && !$circular) {

				header('Location:'.$url);

				die();

			}

		}

		/**
		* Adds an error to the class's stack of errors stored in class variable 'errors'
		*
		* @param  	type	$error	the error
		* @access 	public
		*/
		public function addError($error) {
		
			$this->errors[] = $error;
		
		}

		/**
		* Returns the class's stack of errors stored in class variable 'errors'
		*
		* @return 	array	stack of errors
		* @access 	public
		*/
		public function getErrors() {
		
			return $this->errors;
		
		}

		/**
		* Adds a message to the class's stack of messages stored in class variable 'messages'
		*
		* @param  	type	$message	the message
		* @access 	public
		*/
		public function addMessage($message) {
		
			$this->messages[] = $message;
		
		}
		
		/**
		* Returns the class's stack of messages stored in class variable 'messages'
		*
		* @return 	array	stack of messages
		* @access 	public
		*/
		public function getMessages() {
		
			return $this->messages;
		
		}

		/**
		* Sets the name of the current page, for display purposes, in a class variable 'pageName'.
		*
		* @param  	string	$name	the page's name
		* @access 	public
		*/
		public function setPageName($name) {

			$this->pageName = $name;

		}

		/**
		* Returns the name of the current page.
		*
		* @return 	string	the page's name
		* @access 	public
		*/
		public function getPageName() {

			return $this->pageName;

		}

		/**
		* Sets the current user's id as a class variable
		*
		* @param  	array	$userData	basic user data
		* @access 	public
		*/
		public function setCurrentUserId($userData) {

			$this->currentUserId = $userData['id'];

		}

		/**
		* Returns the current user's id class variable
		*
		* @return 	integer	user id
		* @access 	public
		*/
		public function getCurrentUserId() {

			return $this->currentUserId;

		}

		/**
		* Returns the projects the current user has been assigned to
		*
		* @return 	array	array of project's id's and names
		* @access 	public
		*/
		public function getCurrentUserProjects() {

			foreach((array)$_SESSION['user']['_roles'] as $key => $val) {

				$r = array('id' => $val['project_id'] , 'name' => $val['project_name'] );
				
				if (!isset($cup) || !in_array($r,(array)$cup)) {

					$cup[] = $r;

				}

			}

			return $cup;

		}

		/**
		* Sets the active project's id as class variable
		*
		* @param  	integer	$id	new active project's id
		* @access 	public
		*/
		public function setCurrentProjectId($id) {

			$_SESSION['project']['id'] = $id;

		}

		/**
		* Returns the active project's id class variable
		*
		* @return 	integer	active project's id
		* @access 	public
		*/
		public function getCurrentProjectId() {

			return isset($_SESSION['project']['id']) ? $_SESSION['project']['id'] : null;

		}

		/**
		* Sets the active project's name as a session variable (for display purposes)
		*
		* @access 	public
		*/
		public function setCurrentProjectName() {

			foreach((array)$_SESSION['user']['_roles'] as $key => $val) {

				if ($val['project_id'] == $this->getCurrentProjectId())  {

					$_SESSION['project']['name'] = $val['project_name'];

					return;

				}

			}
			
		}

		/**
		* Gets the active project's name from the session
		*
		* @return 	string	active project's name
		* @access 	public
		*/
		public function getCurrentProjectName() {

			return $_SESSION['project']['name'];
		
		}

		/**
		* Sets the default project for the current user
		*
		* After logging in, the app requires an active project is set, the project the user actually works on.
		* If the user is assigned to several projects, a choice of project is required; if he's assigned to only one,
		* the choice should be automatic. This function decides what project should be the active one, and sets it.
		*
		* @access 	public
		*/
		public function setDefaultProject() {

			$d = (array)$_SESSION['user']['_roles'];

			// if user has no roles, do nothing
			if (count($d) == 0) return;

			// if user has only one role, set the corresponding project as the active project
			if (count($d) == 1) {

				$this->setCurrentProjectId($d[0]['project_id']);

			}
			// if user has more roles, set the project in which he has the lowest role_id as the active project
			// (this assumes that the roles with the most permissions have the lowest ids)
			else {

				$t = false;
			
				foreach((array)$d as $key => $val) {
					
					if (!$t || $val['role_id'] < $t) { 

						$t = $val['role_id'];
						
						$p = $val['project_id'];

					}

				}

				$this->setCurrentProjectId($p);

			}
			
			$this->setCurrentProjectName();

		}

		/**
		* Sets the page to redirect to after logging in
		*
		* Pages that require login redirect the user towards the login. By setting the 'login_start_page' 
		* the app can direct the to the desired page after they have succesfully logged in.
		*
		* @access 	private
		*/
		private function setLoginStartPage() {

			$_SESSION['login_start_page'] = $this->fullPath;

		}

		/**
		* Returns the page to redirect to after logging in
		*
		* @return 	string	path if page to redirect to
		* @access 	public
		*/
		public function getLoginStartPage() {

			if (!empty($_SESSION['login_start_page'])) {

				return $_SESSION['login_start_page'];

			} else {

				return $this->generalSettings['rootWebUrl'].$this->getAppName().'/'.$this->getAppName().'-index.php';
						
			}

		}

		/**
		* Sets user's data in a session after logging in
		*
		* User data retrieved after logging in is stored in a session for faster access.
		* Data includes basic personal data, the user's various roles within projects,
		* the user's rights to see actual pages and the number of projects he is assigned to.
		*
		* @param  	array	$userData	basic user data
		* @param  	array	$roles	user's roles
		* @param  	array	$rights	user's rights
		* @param  	integer	$numberOfProjects	number of assigned projects
		* @access 	public
		*/
		public function setUserSession($userData,$roles,$rights,$numberOfProjects) {

			if (!$userData) return;

			$userData['_login']['time'] = time();
			$userData['_login']['remember'] = false;

			$userData['_roles'] = $roles;
			$userData['_rights'] = $rights;
			$userData['_number_of_projects'] = $numberOfProjects;

			$_SESSION['user'] = $userData;

		}

		/**
		* Destroys a user's session (when logging out)
		*
		* @access 	public
		*/
		public function destroyUserSession() {

			session_destroy();

		}

		/**
		* Checks whether a user is logged in
		*
		* @return 	boolean		logged in or not
		* @access 	public
		*/
		public function isUserLoggedIn() {

			return (!empty($_SESSION['user']));

		}
		

		/**
		* Checks whether a user is authorized to view/use a page within a project
		*
		* @return 	boolean		authorized or not
		* @access 	private
		*/
		private function isUserAuthorisedForProjectPage() {

			$d = $_SESSION['user']['_rights'][$this->getCurrentProjectId()][$this->getControllerBaseName()];

			foreach((array)$d as $key => $val) {

				if ($val == '*' || $val == $this->getViewName()) {

					return true;
				
				}

			}

			return false;

		}

		/**
		* Checks whether a user is authorized to view/use a certain page and redirects if necessary
		*
		* Subsequently checks: 
		*   Is the user logged in? 
		*   Has the user selected an active project?
		*   Is the user authorized to see a specific page?
		*
		* @return 	boolean		returns true if authorized, or redirects if not
		* @access 	public
		*/
		public function checkAuthorisation() {

			// check if user is logged in, otherwise redirect to login page
			if ($this->isUserLoggedIn()) {
			
				// check if there is an active project, otherwise redirect to choose project page
				if ($this->getCurrentProjectId()) {

					// check if the user is authorised for the combination of current page / current project
					if ($this->isUserAuthorisedForProjectPage()) {
	
						return true;
	
					} else {
					
						$this->redirect(
							$this->generalSettings['rootWebUrl'].
							$this->appName.
							$this->generalSettings['paths']['notAuthorized']
						);

						/*
							user is not authorized and redirected to the index.page; 
							if he already *is* on the index.page (and not authorized for that),
							he is logged out to avoid circular reference.
						*/
						if ($this->getViewName()=='Index') {

							$this->redirect(
								$this->generalSettings['rootWebUrl'].
								$this->appName.
								$this->generalSettings['paths']['logout']
							);

						} else {

							$this->redirect('index.php');

						}

					}

				} else {

					$this->redirect(
						$this->generalSettings['rootWebUrl'].
						$this->appName.
						$this->generalSettings['paths']['chooseProject']
					);

				}

			} else {
			
				$this->setLoginStartPage();

				$this->redirect(
					$this->generalSettings['rootWebUrl'].
					$this->appName.
					$this->generalSettings['paths']['login']
				);

			}

		}

		/**
		* Judges whether the user is authorized to work at a specific project
		*
		* @param  	integer	$id	project id
		* @return 	boolean	is or is not authorized
		* @access 	public
		*/
		public function isCurrentUserAuthorizedForProject($id) {
		
			foreach((array)$this->getCurrentUserProjects() as $key => $val) {

				if ($val['id'] == $id) return true;

			}

			return false;

		}

		/**
		* Sets key to sort by for doCustomSortArray
		*
		* @param string	name of the field to sort by
		* @access 	private
		*/
		private function setSortField($field) {

			$this->sortField = $field;
			
		}

		/**
		* Returns key to sort by; called by doCustomSortArray
		*
		* @return string	name of the field to sort by; defaults to 'id'
		* @access 	private
		*/
		private function getSortField() {

			return !empty($this->sortField) ? $this->sortField : 'id' ;
			
		}

		/**
		* Sets sort direction for doCustomSortArray
		*
		* @param string	$a	asc or desc
		* @access 	private
		*/
		private function setSortDirection($dir) {

			$this->sortDirection = $dir;
			
		}

		/**
		* Returns direction to sort in; called by doCustomSortArray
		*
		* @return string	asc or desc
		* @access 	private
		*/
		private function getSortDirection() {

			return !empty($this->sortDirection) ? $this->sortDirection : 'asc' ;
			
		}
		
		/**
		* Sets case sensitivity for doCustomSortArray
		*
		* @param string	$a	i(nsensitive) or s(ensitive)
		* @access 	private
		*/
		private function setSortCaseSensitivity($sens) {

			$this->sortCaseSensitivity = $sens;
			
		}

		/**
		* Returns setting for case-sensitivity while sorting; called by doCustomSortArray
		*
		* @return string	i(nsensitive) or s(ensitive)
		* @access 	private
		*/
		private function getSortCaseSensitivity() {

			return !empty($this->sortCaseSensitivity) ? $this->sortCaseSensitivity : 'i';

		}

		/**
		* Performs the actual usort; called by customSortArray
		*
		* @param array	$a	value of one array-element
		* @param array	$b	value of the other
		* @access 	private
		*/
		private function doCustomSortArray($a,$b) {

			$f = $this->getSortField();

			$d = $this->getSortDirection();

			$c = $this->getSortCaseSensitivity();

			if (empty($a[$f]) || empty($b[$f])) return;

			if ($c!='s') {

				$a[$f] = strtolower($a[$f]);
				$b[$f] = strtolower($b[$f]);

			}

			return ($a[$f] > $b[$f] ? ($d=='asc' ? 1 : -1) : ($a[$f] < $b[$f] ? ($d=='asc' ? -1 : 1) : 0));

		}

		/**
		* Perfoms a usort, using user defined sort by-field, sort direction and case-sensitivity
		*
		* @param array	$array	array to sort
		* @param array	$sortBy	array to array of key, direction and case-sensitivity
		* @access 	public
		*/
		public function customSortArray(&$array,$sortBy) {

			$this->setSortField($sortBy['key']);

			$this->setSortDirection($sortBy['dir']);

			$this->setSortCaseSensitivity($sortBy['case']);

			usort($array,array($this,'doCustomSortArray'));

		}

		/**
		* Sets a random integer value for general use
		*
		* @access 	private
		*/
		private function setRandomValue() {

			$this->randomValue = mt_rand(99999,mt_getrandmax());

		}

		/**
		* Returns random integer value
		*
		* @return integer	anything between 99999 and mt_getrandmax()
		* @access 	private
		*/
		private function getRandomValue() {

			return $this->randomValue;

		}


		/**
		* Sets the default path to save uploads to, based on the value in general settings
		*
		* @access 	private
		*/
		private function setDefaultUploadSaveDir() {
		
			if (!empty($this->generalSettings['directories']['defaultUploadDir']))
				$this->defaultUploadSaveDir= $this->generalSettings['directories']['defaultUploadDir'];
		
		}
		
		/**
		* Returns the default save path for file uploads
		*
		* @return string	path
		* @access 	public
		*/
		public function getDefaultUploadSaveDir() {
		
			return isset($this->defaultUploadSaveDir) ? $this->defaultUploadSaveDir : null;
		
		}
		
		/**
		* Sets the default allowed file mask for file uploads, based on the value in general settings
		*
		* @access 	private
		*/
		private function setDefaultUploadFilemask() {
		
			if (!empty($this->generalSettings['uploading']['defaultUploadFilemask']))
				$this->defaultUploadFilemask = $this->generalSettings['uploading']['defaultUploadFilemask'];
		
		}
		
		/**
		* Returns the default file allowed mask for file uploads
		*
		* @return array	array of allowed file extensions
		* @access 	public
		*/
		public function getDefaultUploadFilemask() {
		
			return isset($this->defaultUploadFilemask) ? $this->defaultUploadFilemask : null;
		
		}
		
		/**
		* Sets the default maximum size of file uploads, based on the value in general settings
		*
		* @access 	private
		*/
		private function setDefaultUploadMaxSize() {
		
			if (!empty($this->generalSettings['uploading']['defaultUploadMaxSize']))
				$this->defaultUploadMaxSize = $this->generalSettings['uploading']['defaultUploadMaxSize'];
		
		}

		/**
		* Returns the default maximum size of file uploads
		*
		* @return integer max upload size in bytes
		* @access 	public
		*/
		public function getDefaultUploadMaxSize() {
		
			return isset($this->defaultUploadMaxSize) ? $this->defaultUploadMaxSize : null;
		
		}
	
	}

?>