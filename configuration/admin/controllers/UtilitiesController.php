<?php

include_once ('Controller.php');

class UtilitiesController extends Controller
{
    
    public $usedModels = array(
        'heartbeat', 
        'user'
    );
    
    public $controllerPublicName = 'Utilities';

	public $cssToLoad = array('prettyPhoto/prettyPhoto.css');

    public $jsToLoad = array(
        'all' => array(
            'main.js', 
            'prettyPhoto/jquery.prettyPhoto.js', 
        ), 
        'IE' => array()
    );

    public $usedHelpers = array(
        'file_upload_helper', 
    );

    

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();
        
        $this->cleanUpHeartbeats();
    
    }



    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }



    /**
     * View displaying 'not authorized'
     *
     * Users can be redirected to notAuthorizedAction from every controller,
     * so the controller name is hidden in the output to avoid confusion.
     *
     * @access    public
     */
    public function notAuthorizedAction ()
    {

        $this->smarty->assign('hideControllerPublicName', true);

        $this->addError($this->translate('You are not authorized to do that.'));

		if (isset($_SESSION['admin']['project']['lead_experts'])) {

			if (count((array)$_SESSION['admin']['project']['lead_experts'])==1) {
	
				$this->addMessage($this->translate('To gain access to the page you were attempting to view, please contact the lead expert of your project:'));
	
			} else {
	
				$this->addMessage($this->translate('To gain access to the page you were attempting to view, please contact one of the lead experts of your project:'));
	
			}
	
			foreach((array)$_SESSION['admin']['project']['lead_experts'] as $key => $val) {
	
				$this->addMessage($val['first_name'].' '.$val['last_name'].' (<a href="mailto:'.$val['email_address'].'">'.$val['email_address'].'</a>)');
			}
			
		}
        
        $this->printPage();
    
    }



    /**
     * View displaying warning that the accessed module is not part of the current project
     *
     * Users can be redirected to moduleNotPresentAction from every controller,
     * so the controller name is hidden in the output to avoid confusion.
     *
     * @access    public
     */
    public function moduleNotPresentAction ()
    {

        $this->smarty->assign('hideControllerPublicName', true);
        
        $this->addError(sprintf($this->translate('The module "%s" is not part of your project.'),$_SESSION['admin']['system']['last_module_name']));
        
        $this->printPage();
    
    }


	public function rootIndexAction()
	{
	
		$this->isMultiLingual = false;
		$this->includeLocalMenu = false;
		$this->printBreadcrumbs = false;
		
		$projects = $this->models->Project->_get(array('id' => array('published' => 1)));

		$this->smarty->assign('projects',$projects);
		$this->smarty->assign('excludeLogout',true);
		$this->smarty->assign('breadcrumbs',false);


		$this->printPage('utilities/root_index');
	
	}
	
	public function massUploadAction()
	{

        $this->checkAuthorisation();
		
		$this->setPageName('Mass upload');

		if ($this->requestDataFiles && !$this->isFormResubmit()) {

			$this->loadControllerConfig('Species');
			$filesToSave = $this->getUploadedMediaFiles(array('overwrite'=>$this->requestData['overwrite']));
			$this->loadControllerConfig();

			if ($filesToSave) {
                        
				foreach ((array) $filesToSave as $key => $file) {
					
					$this->addMessage(sprintf('Uploaded %s','<code>'.$file['name'].'</code>'));

                }
				
			}
			
		}

		$this->loadControllerConfig('Species');

		$this->smarty->assign('allowedFormats', $this->controllerSettings['media']['allowedFormats']);
            
		$this->smarty->assign('iniSettings', array(
			'upload_max_filesize' => ini_get('upload_max_filesize'), 
			'post_max_size' => ini_get('post_max_size')
		));

		
        $this->printPage();
		
	}

	public function browseMediaAction()
	{

        $this->checkAuthorisation();
		
		$this->setPageName('Browse media');

		function deleteFile($id) {

			if (isset($_SESSION['admin']['system']['mediaFiles']['files'][$id])) {

				return unlink(UtilitiesController::getProjectsMediaStorageDir().$_SESSION['admin']['system']['mediaFiles']['files'][$id]);

			} else {
				
				$this->addError('Unknown file index.');

				return false;
				
			}

		}

		if ($this->rHasVal('action','delete') && $this->rHasId()) {
		
			if (deleteFile($this->requestData['id'])) {

				$this->redirect('browse_media.php#');

			}
			
		} else
		if ($this->rHasVal('action','delete') && $this->rHasVal('delete') && !$this->isFormResubmit()) {
			
			foreach((array)$this->requestData['delete'] as $val) {

				deleteFile($val);

			}

		}

		foreach(glob($this->getProjectsMediaStorageDir().'/*') as $file) {  

			if (filetype($file)=='dir')
					$r['dirs'][] = basename($file);
			else
			if (filetype($file)=='file')
					$r['files'][] = basename($file);
		
		}
		
		$_SESSION['admin']['system']['mediaFiles'] = $r;

		$this->smarty->assign('files',$r);

		$this->printPage();
	
	}


	private function renameMedia($p)
	{

		$id = isset($p['id']) ? $p['id'] : null;
		$name = isset($p['name']) ? $p['name'] : null;
		
		if (!isset($id) || !isset($name))
			return false;

		if (isset($_SESSION['admin']['system']['mediaFiles']['files'][$id])) {
			if (file_exists($this->getProjectsMediaStorageDir().$name))
				return false;
			return rename(
				$this->getProjectsMediaStorageDir().$_SESSION['admin']['system']['mediaFiles']['files'][$id],
				$this->getProjectsMediaStorageDir().$name
			);
		} else {
			return false;
		}
	
	}

    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {
        
        if (!isset($this->requestData['action'])) return;
        
        if ($this->requestData['action'] == 'heartbeat') {
            
            $this->ajaxActionHeartbeat();
        
        }
        else if ($this->requestData['action'] == 'get_taxa_edit_states') {
            
            $this->ajaxActionGetTaxaEditStates();
        
        }
        else if ($this->requestData['action'] == 'translate') {
            
			$this->smarty->assign('returnText',$this->javascriptTranslate($this->requestData['text']));

        }
        else if ($this->requestData['action'] == 'change_media_name') {
            
			$this->smarty->assign('returnText',$this->renameMedia($this->requestData));

        }
        
        $this->printPage();
    
    }


    private function cleanUpHeartbeats()
    {

        $this->models->Heartbeat->cleanUp(
        	$this->getCurrentProjectId(),
        	($this->generalSettings['heartbeatFrequency'])
       	);

    }



    private function ajaxActionHeartbeat ()
    {

        if (
			empty($this->requestData["user_id"]) || 
			empty($this->requestData["app"]) || 
			empty($this->requestData["ctrllr"]) || 
			empty($this->requestData["view"])
		) return;
        
        if (!empty($this->requestData["params"])) $this->requestData["params"] = serialize($this->requestData["params"]);
		
		$d = array(
				'project_id' => $this->getCurrentProjectId(), 
				'user_id' => $this->requestData["user_id"], 
				'app' => $this->requestData["app"], 
				'ctrllr' => $this->requestData["ctrllr"], 
				'view' => $this->requestData["view"], 
				'params_hash' => md5($this->requestData["params"]),
			);

        $h = $this->models->Heartbeat->_get(array('id' => $d));

        $this->models->Heartbeat->save(
			array(
				'id' => $h[0]['id'] ? $h[0]['id'] : null, 
				'project_id' => $this->getCurrentProjectId(), 
				'user_id' => $this->requestData["user_id"], 
				'app' => $this->requestData["app"], 
				'ctrllr' => $this->requestData["ctrllr"], 
				'view' => $this->requestData["view"], 
				'params' => $this->requestData["params"],
				'params_hash' => md5($this->requestData["params"]),
				'last_change' => 'CURRENT_TIMESTAMP'
			)
		);
    
    }



    private function ajaxActionGetTaxaEditStates ()
    {

		/*
		It is possible to use FRAC_SECOND in place of MICROSECOND, but FRAC_SECOND is deprecated. FRAC_SECOND was removed in MySQL 5.5.3.
		*/
        // the 1.2 factor is a safety margin (last heartbeat has to be 1.2 times the refresh frequency old
        // before we assume it is dead)
        $h = $this->models->Heartbeat->_get(
			array(
				'id' => "select * 
						from %table% 
						where project_id = " . $this->getCurrentProjectId() . "
							and last_change >= TIMESTAMPADD(MICROSECOND,-" . 
							($this->generalSettings['heartbeatFrequency'] * 1200 * 1000) . ",CURRENT_TIMESTAMP)
							and app = '" . $this->getAppName() . "'
							and ctrllr = 'species'
							and (view = 'taxon' or view = 'media' or view = 'media_upload')"
			)
		);

        foreach ((array) $h as $key => $val) {
            
            if (!empty($val['params'])) {
                
                $u = @unserialize($val['params']);
                
                if ($u[0][0] == 'taxon_id')
                    $h[$key]['taxon_id'] = $u[0][1];
            
            }
            
            $u = $this->models->User->_get(array('id' => $val['user_id']));
            
            if (isset($u)) {
                
                $h[$key]['first_name'] = $u['first_name'];
                
                $h[$key]['last_name'] = $u['last_name'];
            
            }
        
        }
        
        $this->smarty->assign('returnText', isset($h) ? json_encode($h) : null);
    
    }

}