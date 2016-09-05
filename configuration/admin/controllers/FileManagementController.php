<?php

include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');

class FileManagementController extends Controller
{
    public $controllerPublicName = 'File management';

    public $usedHelpers = array(
        'file_upload_helper',
		'paginator',
		'zip_file'
    );

	private $_fileDir;
	private $_basePath;
	private $_files=array();
	private $_selectedfiles=array();

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
	}

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialize()
    {
		$this->moduleSettings=new ModuleSettingsReaderController;
		if ( !$this->moduleSettings->getGeneralSetting( [ 'setting'=>'enable_file_management', 'subst'=>false ] ) )
		{
			$this->redirect('../projects/overview.php');	
		}

		$this->allowed_extensions=json_decode($this->moduleSettings->getGeneralSetting( [ 'setting'=>'allowed_file_management_extensions' ]));
		if (!empty($this->allowed_extensions))
		{
			array_walk($this->allowed_extensions,function(&$a) { $a=strtolower(trim($a,'. ') ); } );
		}

		$this->setFileDir();
		$this->setBasePath();
		$this->scanMediaDir();
	}

    public function indexAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Browse'));
		
		if ( $this->rHasVal( 'action', 'delete' ) && !$this->isFormResubmit() )
		{
	        $this->setSelectedFiles( $this->rGetVal('delete') );
	        $this->deleteFiles();
			$this->scanMediaDir();
		} else
		if ( $this->rHasVal( 'action', 'download' ) )
		{
	        $this->setSelectedFiles( $this->rGetVal('delete') );
	        $this->downloadFiles();
			die();
		}
		
		$paginated=$this->getPaginationWithPager( $this->_files, 25 );
		
        //$this->smarty->assign( 'files',  $this->_files );
        $this->smarty->assign( 'paginated',  $paginated );
        $this->smarty->assign( 'basePath',  $this->_basePath );
        $this->printPage();
	}

    public function uploadAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Upload'));

		if ( $this->requestDataFiles )
		{
			foreach($this->requestDataFiles as $file)
			{
			
				$ext=pathinfo($file['name'], PATHINFO_EXTENSION);
				
				if( in_array($ext,$this->allowed_extensions) )
				{
					$target=$this->_fileDir . $file['name'];
					$d=file_exists($target);
					
					if (move_uploaded_file($file['tmp_name'], $target))
					{
						if ($d)
						{
							if ( $this->rHasVal('action','multi') )
								$this->addMessage( $this->translate('updated file') );
							else
								$this->addMessage( sprintf($this->translate('Updated file "%s"'),$file['name']) );
						}
						else
						{
							if ( $this->rHasVal('action','multi') )
								$this->addMessage( $this->translate('saved file') );
							else
								$this->addMessage( sprintf($this->translate('Saved file "%s"'),$file['name']) );
						}
					}
					else
					{
						if ( $this->rHasVal('action','multi') )
							$this->addError( $this->translate('failed') );
						else
							$this->addMessage( sprintf($this->translate('Failed saving "%s"'),$file['name']) );
					}

					
				}
				else
				{
					if ( $this->rHasVal('action','multi') )
						$this->addError( sprintf($this->translate('discarded (disallowed extension "%s")'),$ext) );
					else
						$this->addMessage( sprintf($this->translate('Discarding uploaded file "%s" (disallowed extension "%s")'),$file['name'],$ext) );
				}
			}
			
		}

        $this->smarty->assign( 'response_only',  $this->rHasVal('action','multi') );
        $this->smarty->assign( 'allowed_extensions',  $this->allowed_extensions );
        $this->printPage();
	}

	private function setFileDir()
	{
		$this->_fileDir=$this->getProjectsMediaStorageDir();
	}

	private function setBasePath()
	{
		$this->_basePath=
			"http://$_SERVER[HTTP_HOST]/linnaeus_ng/" .
			ltrim($_SESSION['admin']['project']['urls']['project_media'],'./');
	}
	
	private function setSelectedFiles( $files )
	{
		$this->_selectedfiles=$files;
	}

	private function deleteFiles()
	{
		foreach((array)$this->_selectedfiles as $key)
		{
			if ( isset($this->_files[$key]) )
			{
				if ( file_exists($this->_files[$key]['pathName']) )
				{
					if ( unlink($this->_files[$key]['pathName']) )
					{
						$this->addMessage( sprintf($this->translate('Deleted file "%s"'),$this->_files[$key]['fileName']));
					}
					else
					{
						$this->addError( sprintf($this->translate('Could not delete file "%s"'),$this->_files[$key]['fileName']));
					}
				}
				else
				{
					$this->addError( sprintf($this->translate('File "%s" no longer exists'),$this->_files[$key]['fileName']));
				}
			}
			else
			{
				$this->addError( sprintf($this->translate('File "%s" no longer exists'),$key));
			}
		}
		
	}	
	
	private function scanMediaDir()
	{
		$this->_files=null;

		foreach (glob("$this->_fileDir/*") as $name)
		{
			if( !is_dir($name) )
			{
				$this->_files[md5($name)]= [ 'pathName'=>$name, 'fileName'=>basename($name) ];
			}
		}
		
		/*
		$this->_files=null;
		$path=$this->_fileDir;
		$objects=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name=>$object)
		{
			if( !is_dir($name) )
			{
				$this->_files[md5($object->getPathName())]= [ 'pathName'=>$object->getPathName(), 'fileName'=>$object->getFileName() ];
			}
		}
		*/
	}

	private function downloadFiles()
	{
		$this->helpers->ZipFile->createArchive( "file-archive" );
	
		foreach((array)$this->_selectedfiles as $key)
		{
			
			if ( isset($this->_files[$key])  && file_exists($this->_files[$key]['pathName']) )
			{
				$this->helpers->ZipFile->addFile( realpath($this->_files[$key]['pathName']), $this->_files[$key]['fileName'] );
			}
		}
		
		$this->helpers->ZipFile->downloadArchive();
	}	
	
}