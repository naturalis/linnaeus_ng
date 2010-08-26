<?php

	include_once(__DIR__."/../BaseClass.php");
	include_once(__DIR__."/../../../smarty/Smarty.class.php");

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


		private $usedModelsBase = array('helptext');

		public function __construct() {

			parent::__construct();

			$this->setDebugMode();

			$this->startSession();

			$this->setNames();

			$this->setSmarty();

			$this->setHistory();

			$this->setRequestData();

			$this->loadModels();

			$this->setHelpTexts();

		}

		public function __destruct() {

			parent::__destruct();

		}

		/* initialise */
		private function startSession() {
		
			session_start();
		
		}


		private function setDebugMode() {

			$this->debugMode = $this->generalSettings['debugMode'];

		}


		private function setNames() {

			$this->fullPath = $_SERVER['PHP_SELF'];

			$path = pathinfo($this->fullPath);
			//$this->viewName = ucfirst($path['filename']);
			$this->viewName = $path['filename'];

			$dirs = explode('/',$path['dirname']);
			$this->controllerBaseName = strtolower($dirs[3]);
			$this->appName = strtolower($dirs[1]);
			//$this->controllerName = ucfirst($this->getControllerBaseName()).'Controller';

		}

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

		private function setRequestData() {

			$this->requestData = $_REQUEST;

		}

		private function loadModels() {
		
			$d = array_unique(array_merge($this->usedModelsBase,$this->usedModels));

			foreach((array)$d as $key) {

				require_once(__DIR__.'/../models/'.$key.'.php');

				$d = str_replace(' ','',ucwords(str_replace('_',' ',$key)));

				$this->models->$d = new $d();

			}

		}

		private function setHelpTexts() {

			$this->helpTexts = 
				$this->models->Helptext->get(
					array(
						'controller'=>$this->getControllerBaseName(),
						'view'=>$this->getViewName()
					), false, ' order by show_order'
				);
			
		}


		/* history */
		private function setHistory() {
		
			$d = &$_SESSION['history'];

			// do not store current url if it is identical to the last (= page reload), just update timestamp
			if ($d[count((array)$d)-1]['url']==$this->fullPath) {

				$d[count((array)$d)-1]['time'] = time();
				return;

			}

			// store current url and time of visiting
			$d[] = array('time' => time(), 'url' => $this->fullPath);

			// keep total history to configurated maximum
			while (count((array)$d)> $this->generalSettings['maxSessionHistorySteps']) {

				array_shift($d);

			}

		}

		public function getHistory($stepsBack = 1, $ignoreCurrent = true) {

			if ($ignoreCurrent) {

				foreach(array_reverse($_SESSION['history']) as $key => $val) {

					if ($stepsBack >= ($key+1) && $val['url'] != $this->fullPath) {

						return $val;

					} else {

						$last = $val;

					}

				}
			
				return $last['url'];

			} else {

				return $_SESSION['history'][count((array)$_SESSION['history'])-$stepsBack]['url'];

			}

		}
		
		public function destroyHistory() {

			unset($_SESSION['history']);

		}


		/* basics */
		public function printPage() {

			$this->smarty->assign('debugMode', $this->debugMode);

			$this->smarty->assign('session', $_SESSION);
			
			$this->smarty->assign('errors', $this->getErrors());
			$this->smarty->assign('messages', $this->getMessages());
			$this->smarty->assign('helpTexts', $this->getHelpTexts());

			$this->smarty->assign('applicationName', $this->generalSettings['applicationName']);
			$this->smarty->assign('applicationVersion', $this->generalSettings['applicationVersion']);
			$this->smarty->assign('pageName', $this->getPageName());

			$this->smarty->display(strtolower($this->getViewName().'.tpl'));

		}

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


		private function getHelpTexts() {

			return $this->helpTexts;
			
		}


		/* set and get messages and errors */
		public function addError($err) {
		
			$this->errors[] = $err;
		
		}
		
		public function getErrors() {
		
			return $this->errors;
		
		}
		
		public function addMessage($err) {
		
			$this->messages[] = $err;
		
		}
		
		public function getMessages() {
		
			return $this->messages;
		
		}


		/* set and get app, view, controller names (most set functions in initialise block) */
		public function getControllerBaseName() {

			return $this->controllerBaseName;

		}

		public function getAppName() {

			return $this->appName;

		}


		public function getViewName() {

			return $this->viewName;

		}


		public function setPageName($name) {

			$this->pageName = $name;

		}

		public function getPageName() {

			return $this->pageName;

		}


		/* set and get user, project names and id's */
		public function setCurrentUserId($userData) {

			$this->currentUserId = $userData['id'];

		}

		public function getCurrentUserId() {

			return $this->currentUserId;

		}

		public function getCurrentUserProjects() {

			foreach((array)$_SESSION['user']['_roles'] as $key => $val) {

				$r = array('id' => $val['project_id'] , 'name' => $val['project_name'] );
				
				if (!isset($cup) || !in_array($r,(array)$cup)) {

					$cup[] = $r;

				}

			}

			return $cup;

		}

		public function getCurrentUserRights() {

			$pru = $this->models->ProjectRoleUser->get(array('user_id' => $this->getCurrentUserId()));

			foreach((array)$pru as $key => $val) {

				$p = $this->models->Project->get($val['project_id']);
				
				$pru[$key]['project_name'] = $p['name'];

				$r = $this->models->Role->get($val['role_id']);

				$pru[$key]['role_name'] = $r['role'];

				$pru[$key]['role_description'] = $r['description'];

				$rr = $this->models->RightRole->get(array('role_id' => $val['role_id']));
				
				foreach((array)$rr as $rr_key => $rr_val) {

					$r = $this->models->Right->get($rr_val['right_id']);

					$rs[$val['project_id']][$r['controller']][$r['id']] = $r['view'];

				}				

			}

			return array('roles' => $pru,'rights' => $rs);

		}

		public function setCurrentProjectId($id) {

			$_SESSION['_current_project_id'] = $id;

		}

		public function getCurrentProjectId() {

			return $_SESSION['_current_project_id'];

		}

		public function setCurrentProjectName() {

			foreach((array)$_SESSION['user']['_roles'] as $key => $val) {

				if ($val['project_id'] == $this->getCurrentProjectId())  {

					$_SESSION['_current_project_name'] = $val['project_name'];

					return;

				}

			}
			
		}

		public function getCurrentProjectName() {

			return $_SESSION['_current_project_name'];
		
		}


		/* logging in and out */
		private function setLoginStartPage() {

			$_SESSION['login_start_page'] = $this->fullPath;		

		}

		public function getLoginStartPage() {

			if ($_SESSION['login_start_page']) {

				return $_SESSION['login_start_page'];

			} else {

				//return 'index.php';
				return '/'.$this->getAppName().'/'.$this->getAppName().'-index.php';
						
			}

		}

		public function setUserSession($userData,$userRolesAndRights) {

			if (!$userData) return;

			$userData['_login']['time'] = time();
			$userData['_login']['remember'] = false;

			$userData['_roles'] = $userRolesAndRights['roles'];
			$userData['_rights'] = $userRolesAndRights['rights'];

			$_SESSION['user'] = $userData;

		}

		public function destroyUserSession() {

			session_destroy();

		}


		/* authorization etc. */
		private function isUserLoggedIn() {

			return (isset($_SESSION['user']) && $_SESSION['user'] != '');

		}
		
		private function isUserAuthorisedForProjectPage() {

			$d = $_SESSION['user']['_rights'][$this->getCurrentProjectId()][$this->getControllerBaseName()];

			foreach((array)$d as $key => $val) {

				if ($val == '*' || $val == $this->getViewName()) {

					return true;
				
				}

			}

			return false;

		}

		public function checkAuthorisation() {
		
			// check if user is logged in, otherwise redirect to login page
			if ($this->isUserLoggedIn()) {
			
				// check if there is an active project, otherwise redirect to choose project page
				if ($this->getCurrentProjectId()) {

					// check if the user is authorised for the combination of current page / current project
					if ($this->isUserAuthorisedForProjectPage()) {
	
						return true;
	
					} else {
					
						$this->redirect('not_authorized.php');

						/*
							user is not authorized and redirected to the index.page; 
							if he already *is* on the index.page (and not authorized for that),
							he is logged out to avoid circular reference.
						*/
						if ($this->getViewName()=='Index') {

							$this->redirect('logout.php');

						} else {

							$this->redirect('index.php');

						}

					}

				} else {

					$this->redirect('choose_project.php');

				}

			} else {
			
				$this->setLoginStartPage();

				$this->redirect('login.php');
			
			}

		}

		public function isCurrentUserAuthorizedForProject($id) {
		
			foreach((array)$this->getCurrentUserProjects() as $key => $val) {

				if ($val['id'] == $id) return true;

			}

			return false;

		}


		/*  sorting  */
		private function setSortField($field) {

			$this->sortField = $field;
			
		}

		private function getSortField() {

			return $this->sortField ? $this->sortField : 'id' ;
			
		}

		private function setSortDirection($dir) {

			$this->sortDirection = $dir;
			
		}

		private function getSortDirection() {

			return $this->sortDirection ? $this->sortDirection : 'asc' ;
			
		}
		
		private function setSortCaseSensitivity($sens) {

			$this->sortCaseSensitivity = $sens;
			
		}

		private function getSortCaseSensitivity() {

			return $this->sortCaseSensitivity ? $this->sortCaseSensitivity : 'i';

		}

		private function doSortUserArray($a,$b) {
		
			$f = $this->getSortField();
			$d = $this->getSortDirection();
			$c = $this->getSortCaseSensitivity();
			
			if ($c!='s') {

				$a[$f] = strtolower($a[$f]);
				$b[$f] = strtolower($b[$f]);

			}

			return ($a[$f] > $b[$f] ? ($d=='asc' ? 1 : -1) : ($a[$f] < $b[$f] ? ($d=='asc' ? -1 : 1) : 0));

		}

		public function sortUserArray(&$array,$sortBy) {

			$this->setSortField($sortBy['key']);
			$this->setSortDirection($sortBy['dir']);
			$this->setSortCaseSensitivity($sortBy['case']);

			usort($array,array($this,'doSortUserArray'));

		}


	}

?>