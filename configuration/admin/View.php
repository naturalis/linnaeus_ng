<?php

	class View {
	
		private $appName = false;
		private $controllerBaseName = false;
		private $controllerName = false;
		private $controllerPath = false;

		public $viewName = false;

		public function __construct() {

			$this->setNames();
			
			$this->loadController() or die(_('FATAL: cannot load controller').' ('.$this->controllerName.'; '.$this->controllerPath.')');

			$this->setTemplateDir();

		}
		
		private function setNames() {

			$path = pathinfo ($_SERVER['PHP_SELF']);

			$dirs = explode('/',$path['dirname']);

			$this->appName = strtolower($dirs[1]);
			$this->controllerBaseName = strtolower($dirs[3]);
			$this->controllerName = ucfirst($this->controllerBaseName).'Controller';
			$this->viewName = ucfirst($path['filename']);

		}
		
		private function loadController() {

			$this->controllerPath = __DIR__.'/controllers/'.$this->controllerName.'.php';

			if (file_exists($this->controllerPath)) {

				require_once($this->controllerPath);

				$this->controller = new $this->controllerName();

				return true;

			} else {

				return false;
	
			}

		}

		private function setTemplateDir() {

			$this->controller->smarty->template_dir .= '/'.$this->controllerBaseName.'/';

		}

		public function doAction() {

			$controllerFunctionName = $this->viewName.'Action';

			$this->controller->$controllerFunctionName();

		}


	}

?>
