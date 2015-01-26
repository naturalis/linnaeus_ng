<?php
/*

	export from excel NOT as csv, but as tab-delimited text

*/

include_once ('Controller.php');

class TraitsDataController extends Controller
{

    public $usedModels = array(
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'text_translations',
		'traits_values'
    );
   
    public $controllerPublicName = 'Kenmerken';

    public $cacheFiles = array();
    
    public $cssToLoad = array(
		'traits.css',
//		'taxon_groups.css'
	);

	public $jsToLoad=array(
        'all' => array('traits.js','jquery.mjs.nestedSortable.js')
	);

    public $usedHelpers = array(
        'session_messages'
    );

    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
    }

    public function dataUploadAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Data upload'));

        if (isset($this->requestDataFiles[0]))
		{
            $tmp = tempnam(sys_get_temp_dir(),'lng');
            
            if (copy($this->requestDataFiles[0]['tmp_name'], $tmp))
			{
                $this->setSessionFile(array('path'=>$tmp,'name'=>$this->requestDataFiles[0]['name']));
            }
            else
			{
				$this->setSessionFile(null);
            }
        }

		if ($this->getSessionFile())
		{
			$this->setSessionLines($this->parseSessionFile());
			$this->redirect('data_raw.php');
		}
		
		
		$this->printPage();
    }

    public function dataRawAction()
    {
		$this->checkAuthorisation();

		if ($this->rHasVal('action','clear'))
		{
			$this->setSessionFile(null);
			$this->getSessionLines(null);
			$this->redirect('data_upload.php');
		}


        $this->setPageName($this->translate('Data upload'));

		$this->smarty->assign('lines',$this->getSessionLines());

		$this->printPage();
    }


	private function setSessionFile($p)
	{
		//array('path'=>$tmp,'name'=>$this->requestDataFiles[0]['name'])
		$_SESSION['admin']['traits']['file']=$p;
	}

	private function getSessionFile()
	{
		return isset($_SESSION['admin']['traits']['file']) ? $_SESSION['admin']['traits']['file'] : null;
	}

	private function setSessionLines($lines)
	{
		$_SESSION['admin']['traits']['lines']=$lines;
	}

	private function getSessionLines()
	{
		return isset($_SESSION['admin']['traits']['lines']) ? $_SESSION['admin']['traits']['lines'] : null;
	}

	private function parseSessionFile()
	{
		$this->fieldSep=chr(9);//";";
		$this->fieldEnclose='"';

		$file=$this->getSessionFile();
		$raw=file($file['path'],FILE_IGNORE_NEW_LINES);

		foreach((array)$raw as $line)
		{
			$l=explode($this->fieldSep,$line);
			$b=array();

			for($i=count($l)-1;$i>=0;$i--)
			{
				if (empty($l[$i]) && empty($b)) continue;
				array_unshift($b,trim($l[$i],$this->fieldEnclose));
			}
			if (isset($b))
			{
				$lines[]=$b;
			}
		}
		
		return $lines;
	}


}



