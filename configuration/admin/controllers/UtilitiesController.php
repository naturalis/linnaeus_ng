<?php 

include_once ('Controller.php');

class UtilitiesController extends Controller
{

    public $usedModels = array(
    );

    public $controllerPublicName = 'Utilities';

	public $cssToLoad = array('../vendor/prettyPhoto/css/prettyPhoto.css');

    public $jsToLoad = array(
        'all' => array(
            'main.js'
        ),
        'IE' => array()
    );

    public $usedHelpers = array(
        'file_upload_helper',
        'session_module_settings'
    );


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
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
		
        $this->addError( $this->translate('You are not authorized to do that.') );

		if ( isset($_SESSION['admin']['user']['authorization_fail_message']) )
		{
			$this->addError(
				'(' . $_SESSION['admin']['user']['authorization_fail_message'] . ')' . "\n" .
				'<!-- ' . $_SESSION['admin']['user']['authorization_fail_page'] . ' -->' . 
				'<!-- ' . $_SESSION['admin']['user']['authorization_fail_add'] . ' -->'
			 );
		}

		// for wiki link
		$this->wikiPageOverride['basename']='notAuthorized';
		$this->setPageName( 'not authorized' ); 

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

		if (isset($_SESSION['admin']['system']['last_module_name']))
	        $this->addError(sprintf($this->translate('The module "%s" is not part of your project.'),$_SESSION['admin']['system']['last_module_name']));
		else
	        $this->addError(sprintf($this->translate('The module you tried to start is not part of your project.')));

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

		if ($this->requestDataFiles && !$this->isFormResubmit())
		{
			$this->loadControllerConfig('Species');
			$filesToSave = $this->getUploadedMediaFiles(array('overwrite'=>$this->rGetVal('overwrite')));
			$this->loadControllerConfig();

			if ($filesToSave)
			{
				foreach ((array) $filesToSave as $key => $file)
				{
					$this->addMessage(sprintf('Uploaded %s','<code>'.$file['name'].'</code>'));
                }
			}
		}

		$this->loadControllerConfig('Species');

		$this->smarty->assign('allowedFormats', $this->controllerSettings['media']['allowedFormats']);

		$this->smarty->assign('iniSettings', array(
			'upload_max_filesize' => ini_get('upload_max_filesize'),
			'post_max_size' => ini_get('post_max_size'),
			'maximum' => min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'))
		));

        $this->printPage();
	}

	private function deleteFile($id)
	{
		$d=$this->moduleSession->getModuleSetting( 'mediaFiles' );

		if (isset($d['files'][$id]))
		{
			return unlink($this->getProjectsMediaStorageDir().$d['files'][$id]);
		}
		else
		{
			$this->addError('Unknown file index.');
			return false;
		}
	}

	public function browseMediaAction()
	{

        $this->checkAuthorisation();

		$this->setPageName('Browse media');

		if ($this->rHasVal('action','delete') && $this->rHasId())
		{
			if ($this->deleteFile($this->rGetId()))
			{
				$this->redirect('browse_media.php#');
			}
		}
		else
		if ($this->rHasVal('action','delete') && $this->rHasVal('delete') && !$this->isFormResubmit())
		{
			foreach((array)$this->rGetVal('delete') as $val)
			{
				$this->deleteFile($val);
			}

		}
		else
		if ($this->rHasVal('action','purge') && !$this->isFormResubmit())
		{
			foreach(glob($this->getProjectsMediaStorageDir().'/*') as $file)
			{
				if (filetype($file)=='file')
				{
					unlink($file);
				}
			}
		}

		foreach(glob($this->getProjectsMediaStorageDir().'/*') as $file)
		{
			if (filetype($file)=='dir')
				$r['dirs'][] = basename($file);
			else
			if (filetype($file)=='file')
				$r['files'][] = basename($file);
		}

		$this->moduleSession->setModuleSetting( array('setting'=>'mediaFiles','value'=>$r ) );

		$this->smarty->assign('files',$r);

		$this->printPage();

	}

	private function renameMedia($p)
	{

		$id = isset($p['id']) ? $p['id'] : null;
		$name = isset($p['name']) ? $p['name'] : null;

		if (!isset($id) || !isset($name))
			return false;

		$d=$this->moduleSession->getModuleSetting( 'mediaFiles' );

		if (isset($d['files'][$id]))
		{
			if (file_exists($this->getProjectsMediaStorageDir().$name))
			{
				return false;
			}
			else
			{
				return rename(
					$this->getProjectsMediaStorageDir().$d['files'][$id],
					$this->getProjectsMediaStorageDir().$name
				);
			}
		}
		else
		{
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
        if (null==$this->rHasVal('action')) return;

		if ($this->rGetVal('action')=='translate')
		{
			$this->smarty->assign('returnText',$this->javascriptTranslate($this->rGetVal('text')));
		}
		else
		if ($this->rGetVal('action')=='change_media_name')
		{
			$this->smarty->assign('returnText',$this->renameMedia($this->rGetAll()));
		}
		else
		if ($this->rGetVal('action')=='set_something')
		{
			$this->moduleSession->setModuleSetting( array('setting'=>$this->rGetVal('name'),'value'=>$this->rGetVal('value') ) );
		}
		else
		if ($this->rGetVal('action')=='get_something')
		{
			$this->smarty->assign('returnText',json_encode($this->moduleSession->getModuleSetting( $this->rGetVal('name') ) ) );
		}

		$this->printPage();
    }

}