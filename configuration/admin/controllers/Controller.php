<?php

	include_once(__DIR__."/../BaseClass.php");
	include_once(__DIR__."/../../../smarty/Smarty.class.php");

	class Controller extends BaseClass {
	
		public $smarty;
		private $smartySettings;
		private $viewName;
		private $controllerBaseName;
		private $fullPath;
		public $requestData;
		public $data;
		public $errors;
		private $currentUserId;
		private $currentProjectId;

		public function __construct() {

			parent::__construct();

			$this->startSession();

			$this->setNames();

			$this->setSmarty();

			$this->setHistory();

			$this->setRequestData();

			$this->loadModels();

		}

		public function __destruct() {

			parent::__destruct();

		}

		private function startSession() {
		
			session_start();
		
		}

		private function setNames() {

			$this->fullPath = $_SERVER['PHP_SELF'];

			$path = pathinfo($this->fullPath);
			$this->viewName = ucfirst($path['filename']);

			$dirs = explode('/',$path['dirname']);
			$this->controllerBaseName = strtolower($dirs[3]);
			//$this->appName = strtolower($dirs[1]);
			//$this->controllerName = ucfirst($this->controllerBaseName).'Controller';

		}

		private function setSmarty() {

			$this->smartySettings = $this->config->getSmartySettings();

			$this->smarty = new Smarty();

			/* DEBUG */
			$this->smarty->force_compile = true;

			$this->smarty->template_dir = $this->smartySettings['dir_template'].'/'.$this->controllerBaseName.'/';
			$this->smarty->compile_dir = $this->smartySettings['dir_compile'];
			$this->smarty->cache_dir = $this->smartySettings['dir_cache'];
			$this->smarty->config_dir = $this->smartySettings['dir_config'];
			$this->smarty->caching = $this->smartySettings['caching'];
			$this->smarty->compile_check = $this->smartySettings['compile_check'];

		}

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

		private function setLoginStartPage() {

			$_SESSION['login_start_page'] = $this->fullPath;		

		}

		public function getLoginStartPage() {

			if ($_SESSION['login_start_page']) {

				return $_SESSION['login_start_page'];

			} else {

				return ('index.php');
				
			}

		}

		private function setRequestData() {

			$this->requestData = $_REQUEST;

		}

		private function loadModels() {

			foreach((array)$this->usedModels as $key) {

				require_once(__DIR__.'/../models/'.$key.'.php');

				$d = str_replace(' ','',ucwords(str_replace('_',' ',$key)));

				$this->models->$d = new $d();

			}

		}

		public function printPage() {

			/* DEBUG */
			$this->smarty->assign('session', $_SESSION);
			$this->smarty->assign('errors', $this->errors);

			$this->smarty->display(strtolower($this->viewName.'.tpl'));

		}

		public function redirect($url = false) {

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


		private function isUserLoggedIn() {

			return (isset($_SESSION['user']) && $_SESSION['user'] != '');

		}
		
		private function isUserAuthorisedForProjectPage() {

			foreach((array)$_SESSION['user']['_rights'] as $key => $val) {
		
				if (
					$val['project_id'] == $this->getCurrentProjectId() && 
					$val['full_path'] == $this->fullPath
					) return true;

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
	
						$this->redirect('index.php');
	
					}

				} else {

					$this->redirect('choose_project.php');

				}

			} else {
			
				$this->setLoginStartPage();

				$this->redirect('login.php');
			
			}

		}

		public function setCurrentUserId($userData) {

			$this->currentUserId = $userData['id'];

		}

		public function getCurrentUserId() {

			return $this->currentUserId;

		}

		public function setUserSession($userData,$userRights) {

			if (!$userData) return;

			$userData['_login']['time'] = time();
			$userData['_login']['remember'] = false;

			$userData['_rights'] = $userRights;

			$_SESSION['user'] = $userData;

		}

		public function destroyUserSession() {

			unset($_SESSION['user']);

		}

		public function getCurrentUserProjects() {

			foreach((array)$_SESSION['user']['_rights'] as $key => $val) {

				$r = array('id' => $val['project_id'] , 'name' => $val['project'] );
				
				if (!isset($cup) || !in_array($r,(array)$cup)) {

					$cup[] = $r;

				}

			}
			
			return $cup;

		}

		public function setCurrentProjectId($id) {

			$_SESSION['_current_project_id'] = $id;

		}

		public function getCurrentProjectId() {

			return $_SESSION['_current_project_id'];

		}

		public function setCurrentProjectName() {

			foreach((array)$_SESSION['user']['_rights'] as $key => $val) {

				if ($val['project_id'] == $this->getCurrentProjectId())  {

					$_SESSION['_current_project_name'] = $val['project'];
					
					return;

				}

			}
			
		}
		
		public function isCurrentUserAuthorizedForProject($id) {
		
			foreach((array)$this->getCurrentUserProjects() as $key => $val) {

				if ($val['id'] == $id) return true;

			}
			
			return false;

		}

	}


?>