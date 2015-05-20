<?php


include_once ('NsrController.php');

class NsrImageController extends NsrController
{
	private $mediaMetaFields=
		array(
			'nsrId' => 'NSR ID taxon',
			'fileName' => 'bestandsnaam',
			'beeldbankAdresMaker' => 'adres maker',
			'beeldbankCopyright' => 'copyright',
			'beeldbankDatumAanmaak' => 'datum aanmaak',
			'beeldbankDatumVervaardiging' => 'datum vervaardiging',
			'beeldbankFotograaf' => 'fotograaf',
			'beeldbankLokatie' => 'lokatie',
			'beeldbankOmschrijving' => 'omschrijving',
			'beeldbankValidator' => 'validator',
			'verspreidingsKaart' => 'is verspreidingskaart?',
			'verspreidingsKaartBron' => 'verspreidingskaart: bron',
			'verspreidingsKaartTitel' => 'verspreidingskaart: titel',
		);

    public $usedModels = array(
		'taxon_quick_parentage',
		'name_types'
    );
    public $usedHelpers = array('csv_parser_helper');
    public $cacheFiles = array();
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_tree.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_tree.js'
        )
    );

    public $controllerPublicName = 'Soortenregister beheer';

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
    }

    public function bulkUploadAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Bulk upload (matching)'));
		
		$raw=null;
		$ignorefirst=false;
		$lines=null;
		$delcols=null;
		$emptycols=null;
		$fields=null;
		$matches=null;
		$firstline=null;

		$ignorefirst=$this->rHasVal('ignorefirst','1');
		$this->setSessionVar('ignorefirst',$ignorefirst);

		if ($this->rHasVal('fields'))
		{
			$fields=$this->rGetVal('fields');
			$this->setSessionVar('fields',$fields);
		}
		
		if ($this->rHasVal('raw'))
		{
			$raw=$this->rGetVal('raw');
			$hash=md5($raw);
			if ($hash!=$this->getSessionVar('hash'))
			{
				$this->setSessionVar('delcols',null);
				$this->setSessionVar('match_ref',null);
				$this->setSessionVar('new_ref',null);
				$fields=null;
			}
			$this->setSessionVar('hash',$hash);
			
			$lines=$this->parseRawCsvData($raw);

			if (!$ignorefirst) $firstline=null;
			
			foreach($lines as $key=>$val)
			{
				if ($key==0)
				{
					foreach($val as $c=>$cell) $emptycols[$c]=true;
				}

				if ($ignorefirst && $key==0) 
				{
					$firstline=$val;
					continue;
				}

				foreach($val as $c=>$cell)
				{
					if (strlen(trim($cell))!=0)
					{
						$emptycols[$c]=false;
					}
				}
			}
		}
		
		if ($lines)
		{
			if ($this->rHasVal('action','delcolreset'))
			{
				$this->setSessionVar('delcols',null);
			}
			else
			if ($this->rHasVal('action','delcol') && $this->rHasVal('value'))
			{
				$delcols=(array)$this->getSessionVar('delcols');
				$delcols[$this->rGetVal('value')]=true;
				$this->setSessionVar('delcols',$delcols);
			}

			$this->setSessionVar('lines',$lines);
		}

		if ($lines && $fields) 
		{
			$matches=$this->matchPossibleReferences(array('lines'=>$lines,'ignorefirst'=>$ignorefirst,'fields'=>$fields));
			$this->setSessionVar('matches',$matches);

			foreach((array)$lines[0] as $c=>$cell)
			{
				if(isset($fields[$c]) && $fields[$c]=='author')
				{
					$this->setSessionVar('field_author',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='label')
				{
					$this->setSessionVar('field_label',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='date')
				{
					$this->setSessionVar('field_date',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='publishedin')
				{
					$this->setSessionVar('field_publishedin',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='periodical')
				{
					$this->setSessionVar('field_periodical',$c);
				}
			}


			//q($matches,1);

		}

		$this->smarty->assign('field_author',$this->getSessionVar('field_author'));
		$this->smarty->assign('field_label',$this->getSessionVar('field_label'));
		$this->smarty->assign('field_date',$this->getSessionVar('field_date'));
		$this->smarty->assign('field_publishedin',$this->getSessionVar('field_publishedin'));
		$this->smarty->assign('field_periodical',$this->getSessionVar('field_periodical'));

		$this->smarty->assign('matches',$matches);
		$this->smarty->assign('emptycols',$emptycols);
		$this->smarty->assign('fields',$fields);
		$this->smarty->assign('cols',$this->mediaMetaFields);
		$this->smarty->assign('delcols',$this->getSessionVar('delcols'));
		$this->smarty->assign('raw',$raw);
		$this->smarty->assign('ignorefirst',$ignorefirst);
		$this->smarty->assign('firstline',$firstline);
		$this->smarty->assign('lines',$lines);

		$this->printPage();
	}

	private function setSessionVar($var,$val=null)
	{
		if (is_null($val))
		{
			unset($_SESSION['admin']['system']['literature2'][$var]);
		}
		else
		{
			$_SESSION['admin']['system']['literature2'][$var]=$val;
		}
	}

	private function getSessionVar($var)
	{
		return isset($_SESSION['admin']['system']['literature2'][$var]) ? $_SESSION['admin']['system']['literature2'][$var] : null;
	}

	private function parseRawCsvData($raw)
	{
		$this->helpers->CsvParserHelper->setFieldDelimiter("\t");
		$this->helpers->CsvParserHelper->setFieldMax(99);
		$this->helpers->CsvParserHelper->parseRawData($raw);
		$this->addError($this->helpers->CsvParserHelper->getErrors());

		if (!$this->getErrors())
		{
			return $this->helpers->CsvParserHelper->getResults();
		}
	}




}
