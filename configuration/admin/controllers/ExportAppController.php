<?php

include_once ('Controller.php');

class ExportAppController extends Controller
{

    public $usedModels = array(
		'content_taxon',
		'page_taxon', 
		'page_taxon_title', 
		'commonname',
		'synonym',
		'media_taxon',
		'media_descriptions_taxon',
		'content',
		'literature',
		'literature_taxon',
		'keystep',
		'content_keystep',
		'choice_keystep',
		'choice_content_keystep',
        'module_project_user', 
		'matrix',
		'matrix_name',
		'matrix_taxon',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_matrix',
		'characteristic_label',
		'characteristic_state',
		'characteristic_label_state',
		'glossary',
		'glossary_synonym',
		'glossary_media',
		'free_module_project',
		'free_module_project_user',
		'free_module_page',
		'free_module_media',
		'content_free_module',
		'occurrence_taxon',
		'l2_occurrence_taxon_combi',
		'l2_diversity_index',
		'l2_map',
		'geodata_type',
		'geodata_type_title',
		'content_introduction',
		'introduction_page',
		'introduction_media',
		'user_taxon',
		'nbc_extras',
		'taxa_relations',
		'matrix_variation',
		'variation_relations',
		'chargroup',
		'characteristic_chargroup',
		'chargroup_label',
		'gui_menu_order',
		'taxon_quick_parentage',
		'taxongroups',
		'taxongroups_labels',
		'taxongroups_taxa',
		'label_language',
    );
   
    public $controllerPublicName = 'Export';

    public $usedHelpers = array('array_to_xml','mysql_2_sqlite');

	public $cssToLoad = array();
	public $jsToLoad = array();

	private $_appExpSkipCols = array('created','last_change');
	private $_sqliteQueriesDDL=null;
	private $_sqliteQueriesDML=null;
	private $_sqliteDropQueries=null;
	private $_projectLanguage=null;
	private $_removePrefix=false;
	private $_includeCode=true;
	private $_dataSize=0;
	private $_listOfEmbeddedImages=array();

	private $_projectVersion='1.0';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
		define('APP_SUMMARY_TAB_NAME','APP_SUMMARY');
    }

    public function __destruct ()
    {
        parent::__destruct();
    }


	/* version 1 (dierenzoeker) */

	public function matrixAppExportAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Export matrix key database for Linnaeus Mobile'));
		
		$pModules = $this->getProjectModules();
		
		$matrices = $this->getMatrices();

		$config = new configuration;
		$dbSettings = $config->getDatabaseSettings();

		if ($this->rHasVal('action','export'))
		{
			$this->_removePrefix = isset($this->requestData['removePrefix']) && $this->requestData['removePrefix']=='y' ? $dbSettings['tablePrefix'] : false;
			$this->_includeCode = isset($this->requestData['includeCode']) && $this->requestData['includeCode']=='y' ? true : false;
			$this->_downloadFile = isset($this->requestData['downloadFile']) && $this->requestData['downloadFile']=='y' ? true : false;
			$this->_separateDrop = isset($this->requestData['separateDrop']) && $this->requestData['separateDrop']=='y' ? true : false;
			$this->_reduceURLs = isset($this->requestData['reduceURLs']) && $this->requestData['reduceURLs']=='y' ? true : false;
			$this->_makeImageList = isset($this->requestData['imageList']) && $this->requestData['imageList']=='y' ? true : false;
			$this->_projectVersion = $this->rHasVar('version') ? $this->rGetVal('version') : $this->_projectVersion;
			
			$d = explode('-',$this->requestData['id']);
			$matrixId = $d[0];
			$languageId = $d[1];

			$this->_filename =
				$this->makeFileName($matrices[$matrixId]['names'][$languageId]['name'].' '.$matrices[$matrixId]['names'][$languageId]['language'],'sql');
			$this->_dbName =
				$this->makeDatabaseName($matrices[$matrixId]['names'][$languageId]['name'].' '.$matrices[$matrixId]['names'][$languageId]['language']);
			$this->_dbDisplayName = $matrices[$matrixId]['names'][$languageId]['name'];

			$this->makeStandAloneMatrixDump($matrixId,$languageId);
			if ($this->_makeImageList) $this->makeImageList();
			$this->convertDumpToSQLite();
			$this->_appType = 'standAloneMatrix';
			$output = $this->downloadSQLite();

			if (!$this->_downloadFile)
				$this->smarty->assign('output',$output);
			
		}

        $d = $this->models->ModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'active' => 'y',
					'module_id' => MODCODE_MATRIXKEY
				), 
				'columns' => 'id'
			));
			
		if ($d[0]['id'])
		{
			$this->smarty->assign('dbSettings',$dbSettings);
			$this->smarty->assign('matrices',$matrices);
			$this->smarty->assign('default_langauge',$this->getDefaultProjectLanguage());
		} 
		else
		{
			$this->smarty->assign('matrices',false);
		}

		$this->smarty->assign('version', $this->_projectVersion);
		
        $this->printPage();
		
	}

    private function getMatrices()
    {
		$m = $this->models->Matrix->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'got_names' => 1
			), 
			'fieldAsIndex' => 'id', 
			'columns' => 'id,got_names,\'matrix\' as type, `default`'
		));
		
		foreach ((array) $m as $key => $val) {
			
			$mn = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'matrix_id' => $val['id']
				), 
				'columns' => 'name,language_id',
				'fieldAsIndex' => 'language_id'
			));

			foreach((array)$mn as $mKey =>$mVal)
				$mn[$mKey]['language'] = $_SESSION['admin']['project']['languageList'][$mVal['language_id']]['language'];
		

			$m[$key]['names']= $mn;

		}

        return $m;
    }

    private function makeStandAloneMatrixDump ($matrixId,$languageId)
	{
		
		$where = 
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $matrixId,
				'language_id' => $languageId
			);
			
		/*
			theoretically, we could be exporting too much data: taxa that are not in MatrixTaxon should
			be ignored when querying Commonname, ContentTaxon, MediaTaxon etc. the most transparent way
			of doing this would be filtering all data of all elements in the _exportDump object and 
			deleting each set of values that has a taxon_id that is not in _exportDump->MatrixTaxon.
			however, once we're exporting complete LNG-projects, we're going to need all data anyway,
			and the "matrix-only"-projects - dierenzoeker, boktorren - hardly have any data beyond that
			needed in the matrix.
		*/

		$this->_exportDump->Characteristic = $this->models->Characteristic->_get(array('id' => $where));
		$this->_exportDump->CharacteristicLabel = $this->models->CharacteristicLabel->_get(array('id' => $where));
		$this->_exportDump->CharacteristicState = $this->models->CharacteristicState->_get(array('id' => $where));
		$this->_exportDump->CharacteristicLabelState = $this->models->CharacteristicLabelState->_get(array('id' => $where));
		//$this->_exportDump->CharacteristicMatrix = $this->models->CharacteristicMatrix->_get(array('id' => $where));  // exporting one matrix at a time
		$this->_exportDump->Chargroup = $this->models->Chargroup->_get(array('id' => $where));
		$this->_exportDump->ChargroupLabel = $this->models->ChargroupLabel->_get(array('id' => $where));
		$this->_exportDump->CharacteristicChargroup = $this->models->CharacteristicChargroup->_get(array('id' => $where));

		$this->_exportDump->MatrixTaxon = $this->models->MatrixTaxon->_get(array('id' => $where));
		$this->_exportDump->Taxon = $this->models->Taxon->_get(array('id' => $where));
		$this->_exportDump->Commonname = $this->models->Commonname->_get(array('id' => $where));
		$this->_exportDump->TaxaRelations = $this->models->TaxaRelations->_get(array('id' => $where));
		$this->_exportDump->ContentTaxon = $this->models->ContentTaxon->_get(array('id' => $where));
		$this->_exportDump->MediaTaxon = $this->models->MediaTaxon->_get(array('id' => $where));

		$this->_exportDump->MatrixVariation = $this->models->MatrixVariation->_get(array('id' => $where));
		$this->_exportDump->TaxonVariation = $this->models->TaxonVariation->_get(array('id' => $where));
		$this->_exportDump->VariationRelations = $this->models->VariationRelations->_get(array('id' => $where));
		$this->_exportDump->VariationLabel = $this->models->VariationLabel->_get(array('id' => $where));

		$this->_exportDump->MatrixTaxonState = $this->models->MatrixTaxonState->_get(array('id' => $where));

		$this->_exportDump->NbcExtras = array_merge(
			(array)$this->models->NbcExtras->_get(array('id' => array_merge($where,array('ref_type'=>'taxon')))),
			(array)$this->models->NbcExtras->_get(array('id' => array_merge($where,array('ref_type'=>'variation'))))
		);

		if ($this->_reduceURLs) {
			foreach((array)$this->_exportDump->NbcExtras as $key => $val) {
				if (($val['name']=='url_image' || $val['name']=='url_thumbnail') && (stripos($val['value'],'http://')!==false || stripos($val['value'],'https://')!==false)) {
					$d=pathinfo($val['value']);
					$this->_exportDump->NbcExtras[$key]['value']=$d['basename'];
				}
			}

			foreach((array)$this->_exportDump->MediaTaxon as $key => $val) {
				if (stripos($val['file_name'],'http://')!==false || stripos($val['file_name'],'https://')!==false) {
					$d=pathinfo($val['file_name']);
					$this->_exportDump->MediaTaxon[$key]['file_name']=$d['basename'];
				}
				if (stripos($val['thumb_name'],'http://')!==false || stripos($val['thumb_name'],'https://')!==false) {
					$d=pathinfo($val['thumb_name']);
					$this->_exportDump->MediaTaxon[$key]['thumb_name']=$d['basename'];
				}
			}
		}
		
		$this->_exportDump->GuiMenuOrder = $this->models->GuiMenuOrder->_get(array('id' => $where));
		$this->_exportDump->PageTaxon = $this->models->PageTaxon->_get(array('id' => $where));
		$this->_exportDump->PageTaxonTitle = $this->models->PageTaxonTitle->_get(array('id' => $where));
		
	}




	/* version 2 (eti apps) */

	public function appExportAction()
	{
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Export database for Linnaeus Mobile'));
		
		$pModules = $this->getProjectModules();
		
		$config = new configuration;
		$dbSettings = $config->getDatabaseSettings();

		if ($this->rHasVal('action','export'))
		{
			$this->_modules = $this->requestData['modules'];
			$this->_removePrefix = $this->rGetVal('removePrefix')=='y' ? $dbSettings['tablePrefix'] : false;
			$this->_includeCode = $this->rGetVal('includeCode')=='y';
			$this->_downloadFile = $this->rGetVal('downloadFile')=='y';
			$this->_separateDrop = $this->rGetVal('separateDrop')=='y';
			$this->_reduceURLs = $this->rGetVal('reduceURLs')=='y';
			$this->_keepSubURLs = $this->rGetVal('keepSubURLs')=='y';
			$this->_imgRootPlaceholder = $this->rGetVal('imgRootPlaceholder');
			$this->_makeImageList =  $this->rGetVal('imageList')=='y';
			$this->_summaryTabId = $this->rGetVal('taxonTab');
			$this->_projectLanguage = $this->rGetVal('projectLanguage');
			$this->_appTitle = $this->rGetVal('appTitle');
			$this->_appType = 'completeLNGApp';
			$this->_dbDisplayName = $_SESSION['admin']['project']['sys_name'];

			$this->_hasSpecies = in_array('species',$this->_modules);
			$this->_hasMatrix = in_array('matrixkey',$this->_modules);
			$this->_hasKey = in_array('key',$this->_modules);
			$this->_hasMap = in_array('mapkey',$this->_modules);
			$this->_hasIntroduction = in_array('introduction',$this->_modules);

			$name=$_SESSION['admin']['project']['sys_name'].' '.$_SESSION['admin']['project']['languageList'][$this->_projectLanguage]['language'];

			$this->_filename = $this->makeFileName($name,'sql');
			$this->_dbName = $this->makeDatabaseName($name);

			$this->_exportDump=new stdClass();

			if ($this->_hasSpecies) $this->makeSpeciesDump();
			if ($this->_hasMatrix) $this->makeMatrixDump();
			if ($this->_hasKey) $this->makeKeyDump();
			if ($this->_hasMap) $this->makeMapDump();
			if ($this->_hasIntroduction) $this->makeIntroductionDump();
	
			if ($this->_makeImageList) $this->makeImageList();

			$this->convertDumpToSQLite();
			$output=$this->downloadSQLite();

			if (!$this->_downloadFile)
				$this->smarty->assign('output',$output);
			
		}

		$this->smarty->assign('appTitle',$_SESSION['admin']['project']['sys_name']);
		$this->smarty->assign('projectModules',$pModules);
		$this->smarty->assign('getTaxonTabs',$this->getTaxonTabs());
		$this->smarty->assign('getProjectLanguages',$this->getProjectLanguages());
		$this->smarty->assign('dbSettings',$dbSettings);
		$this->smarty->assign('default_langauge',$this->getDefaultProjectLanguage());
		
        $this->printPage();
		
	}

	private function makeFileName($projectName,$ext='xml')
	{

		return strtolower(preg_replace('/\W/','_',$projectName)).(is_null($ext) ? null : '.'.$ext);

	}

	private function removeUnwantedColumns($s)
	{
		$d = explode(chr(10),$s);
		foreach((array)$d as $key => $val) {
			if (preg_match('/^(\s*)(`?)('.implode('|',$this->_appExpSkipCols).')(`?)/',$val)==1)
				unset($d[$key]);
		}
		return implode(chr(10),$d);
	}
	
	private function fixTablePrefix($s,$table=null)
	{
		if ($this->_removePrefix===false) return $s;
		
		if (is_null($table))
			return str_ireplace($this->_removePrefix,'',$s);
		else
			return preg_replace('/(`'.$table.'`)/','`'.str_ireplace($this->_removePrefix,'',$table).'`',$s);
	}


	private function reduceEmbeddedImgURLs($matches)
	{
		if ($this->_keepSubURLs)
		{
			$newpath=str_replace($_SESSION['admin']['project']['urls']['project_media'],'',$matches[4]);
		}
		else
		{
			$d=pathinfo($matches[4]);
			$newpath=$d['basename'];
		}
		
		$this->_listOfEmbeddedImages[]=$newpath;

		return
			$matches[1].$matches[2].$matches[3].
			$this->_imgRootPlaceholder.$newpath.
			$matches[5].$matches[6];
	}
	
	private function reduceEmbeddedImgSpans($matches)
	{
		if ($this->_keepSubURLs)
		{
			$newpath=str_replace($_SESSION['admin']['project']['urls']['project_media'],'',$matches[6]);
		}
		else
		{
			$d=pathinfo($matches[6]);
			$newpath=$d['basename'];
		}
		
		$this->_listOfEmbeddedImages[]=$newpath;
		
		return '<img src="'.$this->_imgRootPlaceholder.$newpath.'">';
	}
	
	private function supplantEmbeddedImgURLs($content)
	{
		if (stripos($content,'<img')!==false)
		{
			$content=preg_replace_callback('/(\<img)(.*?)(src=")([^"]*?)(")[^>]*?(\>)/is',array($this,'reduceEmbeddedImgURLs'),$content);
		}

		if (stripos($content,'class="inline-image"')!==false)
		{
			$content=preg_replace_callback('/(\<span)(.*?)(class="inline-image")(.*?)(onclick="showMedia\(\')([^\']*?)(\')(.*?)(\<\/span\>)/is',array($this,'reduceEmbeddedImgSpans'),$content);
		}
		return $content;		
	}



    private function makeSpeciesDump()
	{
		$where = 
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->_projectLanguage
			);

		$this->_exportDump->ProjectRank = $this->models->ProjectRank->_get(array('id' => $where));
		$this->_exportDump->LabelProjectRank = $this->models->LabelProjectRank->_get(array('id' => $where));
		$this->_exportDump->TaxonQuickParentage = $this->models->TaxonQuickParentage->_get(array('id' => $where));
		$this->_exportDump->Taxon = $this->models->Taxon->_get(array('id' => $where));
		//$this->_exportDump->Commonname = $this->models->Commonname->_get(array('id' => $where));
		$this->_exportDump->Commonname = $this->models->Commonname->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));
		$this->_exportDump->LabelLanguage = $this->models->LabelLanguage->_get(array('id' => $where));
		$this->_exportDump->ContentTaxon = $this->models->ContentTaxon->_get(array('id' => array_merge($where,array('page_id'=>$this->_summaryTabId))));
		//$this->_exportDump->PageTaxon = $this->models->PageTaxon->_get(array('id' => $where));  // exporting a single tab
		//$this->_exportDump->PageTaxonTitle = $this->models->PageTaxonTitle->_get(array('id' => $where));
		$this->_exportDump->MediaTaxon = $this->models->MediaTaxon->_get(array('id' => $where));
		$this->_exportDump->MediaDescriptionsTaxon = $this->models->MediaDescriptionsTaxon->_get(array('id' => $where));
		$this->_exportDump->NbcExtras = $this->models->NbcExtras->_get(array('id' => $where));
		$this->_exportDump->TaxaRelations = $this->models->TaxaRelations->_get(array('id' => $where));
		$this->_exportDump->TaxonVariation = $this->models->TaxonVariation->_get(array('id' => $where));
		$this->_exportDump->VariationRelations = $this->models->VariationRelations->_get(array('id' => $where));
		$this->_exportDump->VariationLabel = $this->models->VariationLabel->_get(array('id' => $where));
		$this->_exportDump->Taxongroups = $this->models->Taxongroups->_get(array('id' => $where));
		$this->_exportDump->TaxongroupsLabels = $this->models->TaxongroupsLabels->_get(array('id' => $where));
		$this->_exportDump->TaxongroupsTaxa = $this->models->TaxongroupsTaxa->_get(array('id' => $where));


		if ($this->_reduceURLs)
		{
			foreach((array)$this->_exportDump->MediaTaxon as $key => $val)
			{
				if (stripos($val['file_name'],'http://')!==false || stripos($val['file_name'],'https://')!==false)
				{
					$d=pathinfo($val['file_name']);
					$this->_exportDump->MediaTaxon[$key]['file_name']=$d['basename'];
				}
			}

			foreach((array)$this->_exportDump->NbcExtras as $key => $val)
			{
				if (($val['name']=='url_image' || $val['name']=='url_thumbnail') && 
					(stripos($val['value'],'http://')!==false || stripos($val['value'],'https://')!==false))
				{
					$d=pathinfo($val['value']);
					$this->_exportDump->NbcExtras[$key]['value']=$d['basename'];
				}
			}

			foreach((array)$this->_exportDump->ContentTaxon as $key => $val)
			{
				$this->_exportDump->ContentTaxon[$key]['content']=$this->supplantEmbeddedImgURLs($val['content']);
			}
		}

	}
	
    private function makeMatrixDump()
	{
		$where = 
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->_projectLanguage
			);

		$this->_exportDump->Matrix = $this->models->Matrix->_get(array('id' => $where));
		$this->_exportDump->MatrixName = $this->models->MatrixName->_get(array('id' => $where));
		$this->_exportDump->Characteristic = $this->models->Characteristic->_get(array('id' => $where));
		$this->_exportDump->CharacteristicLabel = $this->models->CharacteristicLabel->_get(array('id' => $where));
		$this->_exportDump->CharacteristicState = $this->models->CharacteristicState->_get(array('id' => $where));
		$this->_exportDump->CharacteristicLabelState = $this->models->CharacteristicLabelState->_get(array('id' => $where));
		$this->_exportDump->CharacteristicMatrix = $this->models->CharacteristicMatrix->_get(array('id' => $where));
		$this->_exportDump->Chargroup = $this->models->Chargroup->_get(array('id' => $where));
		$this->_exportDump->ChargroupLabel = $this->models->ChargroupLabel->_get(array('id' => $where));
		$this->_exportDump->CharacteristicChargroup = $this->models->CharacteristicChargroup->_get(array('id' => $where));
		$this->_exportDump->MatrixTaxon = $this->models->MatrixTaxon->_get(array('id' => $where));
		$this->_exportDump->MatrixVariation = $this->models->MatrixVariation->_get(array('id' => $where));
		$this->_exportDump->MatrixTaxonState = $this->models->MatrixTaxonState->_get(array('id' => $where));
		$this->_exportDump->GuiMenuOrder = $this->models->GuiMenuOrder->_get(array('id' => $where));

		$this->_defaultMatrixId = $this->_exportDump->Matrix[0]['id'];
	}

    private function makeKeyDump()
	{
		$where = 
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->_projectLanguage
			);
				
		$this->_exportDump->Keystep = $this->models->Keystep->_get(array('id' => $where));
		$this->_exportDump->ContentKeystep = $this->models->ContentKeystep->_get(array('id' => $where));
		$this->_exportDump->ChoiceKeystep = $this->models->ChoiceKeystep->_get(array('id' => $where));
		$this->_exportDump->ChoiceContentKeystep = $this->models->ChoiceContentKeystep->_get(array('id' => $where));
		//$this->_exportDump->Keytree = $this->models->Keytree->_get(array('id' => $where));

		if ($this->_reduceURLs)
		{
			foreach((array)$this->_exportDump->ContentKeystep as $key => $val)
			{
				$this->_exportDump->ContentKeystep[$key]['content']=$this->supplantEmbeddedImgURLs($val['content']);
			}
			foreach((array)$this->_exportDump->ChoiceContentKeystep as $key => $val)
			{
				$this->_exportDump->ChoiceContentKeystep[$key]['choice_txt']=$this->supplantEmbeddedImgURLs($val['choice_txt']);
			}
		}

	}

    private function makeMapDump()
	{
		$where = 
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->_projectLanguage
			);
				
		$this->_exportDump->L2Map = $this->models->L2Map->_get(array('id' => $where));
		$this->_exportDump->L2OccurrenceTaxonCombi = $this->models->L2OccurrenceTaxonCombi->_get(array('id' => $where));
		$this->_exportDump->GeodataType = $this->models->GeodataType->_get(array('id' => $where));
		$this->_exportDump->GeodataTypeTitle = $this->models->GeodataTypeTitle->_get(array('id' => $where));

		if ($this->_reduceURLs)
		{
			foreach((array)$this->_exportDump->L2Map as $key => $val)
			{
				if (stripos($val['image'],'http://')!==false || stripos($val['image'],'https://')!==false)
				{
					$d=pathinfo($val['image']);
					$this->_exportDump->L2Map[$key]['image']=$d['basename'];
				}
			}
		}
	}

    private function makeIntroductionDump()
	{
		$where = 
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->_projectLanguage
			);

		$this->_exportDump->IntroductionPage = $this->models->IntroductionPage->_get(array('id' => $where));
		$this->_exportDump->ContentIntroduction = $this->models->ContentIntroduction->_get(array('id' => $where));
		$this->_exportDump->IntroductionMedia = $this->models->IntroductionMedia->_get(array('id' => $where));

		if ($this->_reduceURLs)
		{
			foreach((array)$this->_exportDump->IntroductionMedia as $key => $val)
			{
				if (stripos($val['file_name'],'http://')!==false || stripos($val['file_name'],'https://')!==false)
				{
					$d=pathinfo($val['file_name']);
					$this->_exportDump->IntroductionMedia[$key]['file_name']=$d['basename'];
				}
				if (stripos($val['thumb_name'],'http://')!==false || stripos($val['thumb_name'],'https://')!==false)
				{
					$d=pathinfo($val['thumb_name']);
					$this->_exportDump->IntroductionMedia[$key]['thumb_name']=$d['basename'];
				}
			}

			foreach((array)$this->_exportDump->ContentIntroduction as $key => $val)
			{
				$this->_exportDump->ContentIntroduction[$key]['content']=$this->supplantEmbeddedImgURLs($val['content']);
			}

		}

	}
	

	

	private function makeImageList()
	{
		if (isset($this->_exportDump->MediaTaxon))
		{
			foreach((array)$this->_exportDump->MediaTaxon as $val)
				$this->_imageList[]=$val['file_name'];
		}

		if (isset($this->_exportDump->CharacteristicState))
		{
			foreach((array)$this->_exportDump->CharacteristicState as $val)
				if (!empty($val['file_name'])) $this->_imageList[]=$val['file_name'];
		}

		if (isset($this->_exportDump->NbcExtras))
		{
			foreach((array)$this->_exportDump->NbcExtras as $val)
			{
				if ($val['name']=='url_thumbnail'||$val['name']=='url_image')
					$this->_imageList[]=$val['value'];
			}
		}		

		if (isset($this->_exportDump->Keystep))
		{
			foreach((array)$this->_exportDump->Keystep as $val)
			{
				if (!empty($val['image']))
					$this->_imageList[]=$val['image'];
			}
		}		

		if (isset($this->_exportDump->ChoiceKeystep))
		{
			foreach((array)$this->_exportDump->ChoiceKeystep as $val)
			{
				if (!empty($val['choice_img']))
					$this->_imageList[]=$val['choice_img'];
			}
		}		

		$this->_imageList=array_merge($this->_imageList,$this->_listOfEmbeddedImages);

	}
	
	private function convertDumpToSQLite()
	{

		$setsPerInsert = 1; // phonegap webdb doesn't seem to support inserting multiple sets at once
		
		$this->helpers->Mysql2Sqlite->setRemoveUniqueConstraints(true);
		
		foreach((array)$this->_exportDump as $class => $data)
		{

			$table = $this->models->$class->getTableName();
			$inserts = array();
			
			$c = $this->models->Taxon->freeQuery('show create table '.$table);
			$this->helpers->Mysql2Sqlite->convert($this->fixTablePrefix($this->removeUnwantedColumns($c[0]['Create Table'].';'),$table));

			$this->_sqliteQueriesDDL[] = $this->helpers->Mysql2Sqlite->getSqlDropTable();
			$this->_sqliteQueriesDDL = array_merge($this->_sqliteQueriesDDL,$this->helpers->Mysql2Sqlite->getSqlDropKeys());
			$this->_sqliteQueriesDDL[] = $this->helpers->Mysql2Sqlite->getSqlTable();
			$this->_sqliteQueriesDDL = array_merge($this->_sqliteQueriesDDL,$this->helpers->Mysql2Sqlite->getSqlKeys());

			if ($this->_separateDrop)
			{
				$this->_sqliteDropQueries[] = $this->helpers->Mysql2Sqlite->getSqlDropTable();			
				$this->_sqliteDropQueries = array_merge($this->_sqliteDropQueries,$this->helpers->Mysql2Sqlite->getSqlDropKeys());
			}
			
			$this->dataCount[$this->fixTablePrefix($table)] = count((array)$data);

			foreach((array)$data as $row)
			{
				foreach((array)$row as $column => $value)
				{
					if (in_array($column,$this->_appExpSkipCols))
						unset($row[$column]);
				}
				
				$inserts[] = "('".implode("','",array_map(function($str){return trim(preg_replace(array('/\\\'/','/\\\"/','/(\n|\r)/'),array("''",'"',' '), $str));},$row))."')";
				
				if (count((array)$inserts)>=$setsPerInsert)
				{
					$d = implode(',',$inserts);
					$this->_sqliteQueriesDML[] = $this->fixTablePrefix('insert into `'.$table.'` values '.$d.';',$table);
					$this->_dataSize += strlen($d);
					$inserts = array();
				}
			}

			if (count((array)$inserts)!=0)
			{
				$d = implode(',',$inserts);
				$this->_sqliteQueriesDML[] = $this->fixTablePrefix('insert into `'.$table.'` values '.$d.';',$table);
				$this->_dataSize += strlen($d);
			}
			
			/*
				// skipping the possibly redundant re-indexing of the tables
				$this->_sqliteQueriesDML = array_merge($this->_sqliteQueriesDML,$this->helpers->Mysql2Sqlite->getSqlReindexKeys());
			*/

		}
		
		unset($this->_exportDump);
		
	}

	private function makeDatabaseName($projectName)
	{
		return strtolower(preg_replace(array('/\W/','/[aeiouy]/i'),array('_',''),$projectName));
	}

    private function getTaxonTabs()
    {
		return $this->models->PageTaxon->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			), 
			'order' => 'show_order', 
			'fieldAsIndex' => 'page_id'
		));

    }



	
	private function downloadSQLite()
	{

		$output = '';
		$exportId = md5(time());
		
		function entitizeThis(&$item1, $key)
		{
			$item1 = htmlentities($item1,ENT_NOQUOTES,'UTF-8');
		}

		array_walk($this->_sqliteQueriesDML, 'entitizeThis');

		if ($this->_downloadFile)
		{
			header('Cache-Control: public');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename='.$this->_dbName.'-app-installer.js');
			header('Content-Type: text/javascript ');
		}

		if (!$this->_includeCode)
		{

			if ($this->_separateDrop)
				$output .= implode(chr(10),$this->_sqliteDropQueries).chr(10).chr(10);

			$output .= implode(chr(10),$this->_sqliteQueriesDDL) . implode(chr(10),$this->_sqliteQueriesDML);
			
			$output = "begin transaction;\n\n".$output."\nend transaction;";

		}
		else
		{
			
			$bufferDDL='';
			foreach ((array)$this->_sqliteQueriesDDL as $val)
			{
				$bufferDDL.=$val.chr(10);
			}
			$bufferDDL=base64_encode(bzcompress($bufferDDL));

			$bufferDML='';
			foreach ((array)$this->_sqliteQueriesDML as $val)
			{
				$bufferDML.=$val.chr(10);
			}
			$bufferDML=base64_encode(bzcompress($bufferDML));



			if ($this->_separateDrop)
			{
				$drop='';
				foreach ((array)$this->_sqliteDropQueries as $val)
				{
					$drop.=$val.chr(10);
				}
				$drop=base64_encode(bzcompress($drop));
			}

		
	/*
		please note: "dbEstimatedSize" is used when opening the database, and is required to be as large
		or larger than the actual database, at least on android. if not, the installer will fail, but
		most likely succeed the next time around, as android-apps seem to remember lack of storage space
		in previous sessions, and in response make available more space to the application (1mb at a
		time).
		"$this->_dataSize" roughly corresponds to the size of the data (value is based on prepared insert 
		statements), which is multiplied bij 5 to account for SQLite indexes (1-to-5 ratio based on 
		native SQLite databases with the same data and both with, and without keys)

		the installer is called from main program file:

			db = appController.connect();
			if (db)
				installDb(db,main);
			else
				main();

	*/

	if ($this->_appType=='standAloneMatrix')
	{
		
		$output = "/*
    // cut the block below & paste into app-controller.js --------------------------------
		
    var credentials = {
      dbName:'".$this->_dbName."',
      dbVersion: '".$this->_projectVersion."', 
      dbDisplayName: '".$this->_dbDisplayName."', 
      dbEstimatedSize: ".floor($this->_dataSize * 5).",
      exportId:'".$exportId."'
    };
    
    var pId = ".$this->getCurrentProjectId().";

    //cut --------------------------------------------------------------------------------
    // 
    // to add the project data to your PhoneGap app:
    // - remove this entire comment block
    // - copy and paste the remaining code into
    //     /js/data/app-data.js
    //   overwriting any existing code if the file already exists.
    //
    // a new download is automatically installed on the of, as exportID
    // always has a new value (you can force a re-install of the same file by manually
    // altering the value of exportID IN THE CONTROLLER, not the data file)
    ";
	} 
	else
	if ($this->_appType=='completeLNGApp')
	{
		$output = "/* goes into app-config.js:

var exportedVariables = {
   
    credentials : {
      dbName:'".$this->_dbName."',
      dbVersion: '".$this->_projectVersion."', 
      dbDisplayName: '".$this->_dbDisplayName."', 
      dbEstimatedSize: ".floor($this->_dataSize * 5).",
      exportId:'".$exportId."'
    },
    
    APP_TITLE :'".addslashes($this->_appTitle)."',
    PROJECT_ID : ".$this->getCurrentProjectId().",
    LANGUAGE_ID : ".$this->_projectLanguage.",
    SPECIES_RANK_ID : ".SPECIES_RANK_ID.",
    FAMILY_RANK_ID : ".FAMILY_RANK_ID.",
    CONTENT_TAB_ID : ".$this->_summaryTabId.",
    ".( !empty($this->_imgRootPlaceholder) ? "IMAGE_ROOT_PLACEHOLDER : '".addslashes($this->_imgRootPlaceholder)."', " : "" )."
    ".( $this->_hasMatrix ? "DEFAULT_MATRIX_ID : ".$this->_defaultMatrixId.", " : "" )."	
}
*/


/*  goes into database-installer.js:

    to add this data to your PhoneGap app:
    - remove the block above (\"goes into app-config.js\")
    - remove this comment block
    - copy and paste the remaining code into the folder
        /js/data/database-installer.js
      of the app, overwriting any existing code if the file already exists.

    a new download is automatically installed on the device, as exportID
    always has a new value (you can force a re-install of the same file by manually
    altering the value of exportID in the credentials in /js/data/app-config.js)
";
	}

	$output .= 
	($this->_separateDrop ? "
    there is a separate variable \"encodedDropQueries\" in the script that holds
    compressed drop table & index queries. these are *not* automatically
    executed in the install script; you'll have to do this manually by setting
    forceInstall to true and changing 
        window.atob(installConfig.encodedData)
    to
        window.atob(installConfig.encodedDropQueries)
    in the function loadRecords()
" : '' )."
".($this->_makeImageList ? "
    below is a list of images referred to in the data:
    --------------------------------------------------
    ".implode(chr(10).'    ',$this->_imageList)."
" : '')."
*/

var installConfig = {
  installProject:'".addslashes($this->_dbDisplayName)."',
  installDbName:'".$this->_dbName."',
  installDbVersion:'".$this->_projectVersion."',".
"
  queryCountDDL:".count((array)$this->_sqliteQueriesDDL).",
  queryCountDML:".count((array)$this->_sqliteQueriesDML).",
  exportVersion:'1.0 (".date("Y-m-d H:i:s").")',  
  exportID:'".$exportId."',  
  encodedDataDDL:'".$bufferDDL."',
  encodedDataDML:'".$bufferDML."'".($this->_separateDrop ? ",\n  encodedDropQueries:'".$drop."'": '')."
}
//file end
";
		}

		if (!$this->_downloadFile)
		{
			return $output;
		} 
		else
		{
			echo $output;
			die();
		}
	
	
	}

}
