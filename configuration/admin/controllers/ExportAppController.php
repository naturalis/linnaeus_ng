<?php 

include_once ('Controller.php');

class ExportAppController extends Controller
{

    public $usedModels = array(
		'characteristics',
		'characteristics_chargroups',
		'characteristics_labels',
		'characteristics_labels_states',
		'characteristics_matrices',
		'characteristics_states',
		'chargroups',
		'chargroups_labels',
		'choices_content_keysteps',
		'choices_keysteps',
		'commonnames',
		'content',
		'content_free_modules',
		'content_introduction',
		'content_keysteps',
		'content_taxa',
		'free_module_media',
		'free_modules_pages',
		'free_modules_projects',
		'free_modules_projects_users',
		'geodata_types',
		'geodata_types_titles',
		'glossary',
		'glossary_media',
		'glossary_synonyms',
		'gui_menu_order',
		'introduction_media',
		'introduction_pages',
		'keysteps',
		'l2_diversity_index',
		'l2_maps',
		'l2_occurrences_taxa_combi',
		'labels_languages',
		'literature',
		'literature_taxa',
		'matrices',
		'matrices_names',
		'matrices_taxa',
		'matrices_taxa_states',
		'matrices_variations',
		'media_descriptions_taxon',
		'media_taxon',
        'modules_projects_users',
        'nbc_extras',
		'occurrences_taxa',
		'pages_taxa_titles',
		'pages_taxa',
		'synonyms',
		'tab_order',
		'taxa_relations',
		'taxon_quick_parentage',
		'taxongroups',
		'taxongroups_labels',
		'taxongroups_taxa',
		'users_taxa',
		'variation_relations',
		'traits_taxon_freevalues',
		'names'
    );

    public $controllerPublicName = 'Export';

    public $usedHelpers = array(
        'array_to_xml',
        'mysql_2_sqlite'
    );

	public $cssToLoad = array();
	public $jsToLoad = array();

	private $_appExpSkipCols = array('created','last_change');
	private $_sqliteQueriesDDL=array();
	private $_sqliteQueriesDML=array();
	private $_sqliteDropQueries=null;
	private $_projectLanguage=null;
	private $_removePrefix=false;
	private $_includeCode=true;
	private $_dataSize=0;
	private $_imageList=array();
	private $_listOfEmbeddedImages=array();
	private $_exportDump;

	private $_projectVersion='1.0';
	private $_matrixStandAloneExportVersion='3.0';

	const NSR_ID_TRAIT_ID = 1;

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
		define('APP_SUMMARY_TAB_NAME','APP_SUMMARY');
        $this->_exportDump = new stdClass();
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
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
			$this->_removePrefix = $this->rHasVar('removePrefix', 'y') ? $dbSettings['tablePrefix'] : false;
			$this->_includeCode = $this->rHasVar('includeCode', 'y') ? true : false;
			$this->_downloadFile = $this->rHasVar('downloadFile', 'y') ? true : false;
			$this->_separateDrop = $this->rHasVar('separateDrop', 'y') ? true : false;
			$this->_reduceURLs = $this->rHasVar('reduceURLs', 'y') ? true : false;
			$this->_makeImageList = $this->rHasVar('imageList', 'y') ? true : false;
			$this->_projectVersion = $this->rHasVar('version') ? $this->rGetVal('version') : $this->_projectVersion;

			$d = explode('-',$this->rGetVal('id')); # don't change to rGetID(), which forces an int val
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

        $d = $this->models->ModulesProjects->_get(
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
		$m = $this->models->Matrices->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'fieldAsIndex' => 'id',
			'columns' => 'id,\'matrix\' as type, `default`'
		));

		foreach ((array) $m as $key => $val) {

			$mn = $this->models->MatricesNames->_get(
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

    private function makeStandAloneMatrixDump($matrixId,$languageId)
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

		$this->_exportDump->Characteristics = $this->models->Characteristics->_get(array('id' => $where));
		$this->_exportDump->CharacteristicsLabels = $this->models->CharacteristicsLabels->_get(array('id' => $where));

		$this->_exportDump->CharacteristicsStates = $this->models->CharacteristicsStates->_get(array('id' => $where));
		foreach ($this->_exportDump->CharacteristicsStates as $key => $value)
		{
			$this->_exportDump->CharacteristicsStates[$key]['file_name']=basename($value['file_name']);
		}

		$this->_exportDump->CharacteristicsLabelsStates = $this->models->CharacteristicsLabelsStates->_get(array('id' => $where));
		$this->_exportDump->Chargroups = $this->models->Chargroups->_get(array('id' => $where));
		$this->_exportDump->ChargroupsLabels = $this->models->ChargroupsLabels->_get(array('id' => $where));
		$this->_exportDump->CharacteristicsChargroups = $this->models->CharacteristicsChargroups->_get(array('id' => $where));
		$this->_exportDump->MatricesTaxa = $this->models->MatricesTaxa->_get(array('id' => $where));
		$this->_exportDump->MatricesTaxaStates = $this->models->MatricesTaxaStates->_get(array('id' => $where));

		$this->_exportDump->Taxa = $this->models->Taxa->_get(array('id' => $where));
		foreach((array)$this->_exportDump->Taxa as $key=>$val)
		{
			$trait=$this->models->TraitsTaxonFreevalues->_get( [
				'id' => $where + ['taxon_id'=>$val['id'], 'trait_id' => $this::NSR_ID_TRAIT_ID ],
				'limit' => 1
			] );

			/* 
				WARNING: blatant abuse of the taxa.author column to store the NSR ID. not feeling like
				adding to the datamodel for a single field. shoot me.
			*/
			$this->_exportDump->Taxa[$key]['author'] = $trait[0]['string_value'];
		}

		$this->_exportDump->Names = $this->models->Names->_get(array('id' =>
			$where+['type_id'=>$this->getNameTypeId(PREDICATE_PREFERRED_NAME),'language_id'=>LANGUAGE_ID_DUTCH]
		));

		$this->_exportDump->TaxaRelations = $this->models->TaxaRelations->_get(array('id' => $where));
		$this->_exportDump->ContentTaxa = $this->models->ContentTaxa->_get(array('id' => $where));
		foreach((array)$this->_exportDump->ContentTaxa as $key=>$val)
		{
			$this->_exportDump->ContentTaxa[$key]['content']=trim(html_entity_decode(preg_replace('/<([^>]*)>/i', ' ', $val['content'])));
		}
		$this->_exportDump->TabOrder = $this->models->TabOrder->_get(array('id' => $where));

		$this->_exportDump->MediaTaxon = $this->models->MediaTaxon->_get(array('id' => $where + ['overview_image' => 1 ]));
		foreach((array)$this->_exportDump->MediaTaxon as $key=>$val)
		{
			$baseName=basename($val['file_name']);
			if ($this->_reduceURLs) $this->_exportDump->MediaTaxon[$key]['file_name']=$baseName;
			if ($this->_reduceURLs) $this->_exportDump->MediaTaxon[$key]['thumb_name']=$baseName;

			if ( !empty($baseName) )
			{
				$this->_exportDump->MediaTaxon[$key]['thumb_name']=pathinfo($baseName)['filename'].'_thumb.'.pathinfo($baseName)['extension'];
			}
			$this->_exportDump->MediaTaxon[$key]['original_name']='';
		}

		// no variations in dierenzoeker (and we're not going to use this code for anything else any longer)
		//$this->_exportDump->MatricesVariations = $this->models->MatricesVariations->_get(array('id' => $where));
		//$this->_exportDump->TaxaVariations = $this->models->TaxaVariations->_get(array('id' => $where));
		//$this->_exportDump->VariationRelations = $this->models->VariationRelations->_get(array('id' => $where));
		//$this->_exportDump->VariationsLabels = $this->models->VariationsLabels->_get(array('id' => $where));

		$this->_exportDump->GuiMenuOrder = $this->models->GuiMenuOrder->_get(array('id' => $where));
		$this->_exportDump->PagesTaxa = $this->models->PagesTaxa->_get(array('id' => $where));
		$this->_exportDump->PagesTaxaTitles = $this->models->PagesTaxaTitles->_get(array('id' => $where));
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
			$this->_modules = $this->rGetVal('modules');
			$this->_removePrefix = $this->rGetVal('removePrefix')=='y' ? $dbSettings['tablePrefix'] : false;
			$this->_includeCode = $this->rGetVal('includeCode')=='y';
			$this->_downloadFile = $this->rGetVal('downloadFile')=='y';
			$this->_separateDrop = $this->rGetVal('separateDrop')=='y';
			$this->_reduceURLs = $this->rGetVal('reduceURLs')=='y';
			$this->_fixImageNames = $this->rGetVal('fixImageNames')=='y';
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

			if ($this->_hasSpecies) $this->makeSpeciesDump();
			if ($this->_hasMatrix) $this->makeMatrixDump();
			if ($this->_hasKey) $this->makeKeyDump();
			if ($this->_hasMap) $this->makeMapDump();
			if ($this->_hasIntroduction) $this->makeIntroductionDump();

			if ($this->_fixImageNames) $this->fixImageNames();
			if ($this->_makeImageList) $this->makeImageList();

			$this->convertDumpToSQLite();
			$output=$this->downloadSQLite();

			if (!$this->_downloadFile)
			{
				$this->smarty->assign('output',$output);
			}

			if ($this->_fixImageNames)
			{
				$this->smarty->assign( 'fixImageNames',$this->_fixImageNames );
				$this->smarty->assign( 'renameImageListCount', count((array)$this->_renameImageList) );
			}

		}


		$this->smarty->assign('appTitle',$_SESSION['admin']['project']['sys_name']);
		$this->smarty->assign('appTitle',$_SESSION['admin']['project']['sys_name']);
		$this->smarty->assign('projectModules',$pModules);
		$this->smarty->assign('getTaxonTabs',$this->getTaxonTabs());
		$this->smarty->assign('getProjectLanguages',$this->getProjectLanguages());
		$this->smarty->assign('dbSettings',$dbSettings);
		$this->smarty->assign('default_langauge',$this->getDefaultProjectLanguage());

        $this->printPage();
	}

	public function imageRenameScriptAction()
	{
        $this->checkAuthorisation();

		$platform=$this->rGetVal( "p" );
		$list=$this->getRenameImageList();
		$buffer=array();

		if ( $platform=="lin" )
		{
			$buffer[]='#!/bin/bash';
			$cmd="mv";
			$file="rename_images.sh";
			foreach((array)$list as $val)
			{
				$buffer[]=$cmd." ".escapeshellarg($val[0])." ".escapeshellarg($val[1]);
			}
		}
		else
		{
			$cmd="ren";
			$file="rename_images.bat";
			foreach((array)$list as $val)
			{
				$buffer[]=$cmd.' "'.($val[0]).'" "'.($val[1]).'"';
			}
		}

		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename='.$file);

		foreach($buffer as $val)
			echo $val,"\n";


	}


	public function deleteUnusedScriptAction()
	{
        $this->checkAuthorisation();

		$platform=$this->rGetVal( "p" );

		$list=$this->getImageList();

		$buffer=array();

		$file="delete_images.bat";
		$tempdir="___temp_".time();

		$buffer[]="mkdir ".$tempdir;

		foreach((array)$list as $val)
		{
			$buffer[]='ren "'.$val.'" "'.$tempdir."/".$val.'"';
		}

		$buffer[]='del *.* /Q';

		foreach((array)$list as $val)
		{
			$buffer[]='ren "'.$tempdir."/".$val.'" "'.$val.'"';
		}

		$buffer[]="rmdir ".$tempdir;
/*
		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename='.$file);
*/
		foreach($buffer as $val)
			echo $val,"\n";


	}

	private function makeFileName($projectName,$ext='xml')
	{
		return strtolower(preg_replace('/\W/','_',$projectName)).(is_null($ext) ? null : '.'.$ext);
	}

	private function removeUnwantedColumns($s)
	{
		$d = explode(chr(10),$s);
		foreach((array)$d as $key => $val)
		{
			if (preg_match('/^(\s*)(`?)('.implode('|',$this->_appExpSkipCols).')(`?)/',$val)==1)
				unset($d[$key]);
		}
		return implode(chr(10),$d);
	}

	private function removeUnwantedColumnsFromKeys($k)
	{
		$b=[];
		foreach ($k as $key => $val)
		{
			$pass=true;
			foreach ($this->_appExpSkipCols as $col)
			{
				if ($pass) $pass=!preg_match('/\(([^)]*)`'.$col.'`([^)]*)\)/',$val);
			}
			if ($pass) $b[]=$val;
		}
		
		return $b;
	}

	private function fixTablePrefix($s,$table=null)
	{
		if ($this->_removePrefix===false) return $s;

		if (is_null($table))
		{
			return str_ireplace($this->_removePrefix,'',$s);
		}
		else
		{
			return preg_replace('/(`'.$table.'`)/','`'.str_ireplace($this->_removePrefix,'',$table).'`',$s);
		}
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

		return '<img class="inline-image" src="'.$this->_imgRootPlaceholder.$newpath.'">';
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

		$this->_exportDump->ProjectsRanks = $this->models->ProjectsRanks->_get(array('id' => $where));
		$this->_exportDump->LabelsProjectsRanks = $this->models->LabelsProjectsRanks->_get(array('id' => $where));
		$this->_exportDump->TaxonQuickParentage = $this->models->TaxonQuickParentage->_get(array('id' => $where));
		$this->_exportDump->Taxa = $this->models->Taxa->_get(array('id' => $where));
		$this->_exportDump->Commonnames = $this->models->Commonnames->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));
		$this->_exportDump->LabelsLanguages = $this->models->LabelsLanguages->_get(array('id' => $where));
		$this->_exportDump->ContentTaxa = $this->models->ContentTaxa->_get(array('id' => array_merge($where,array('page_id'=>$this->_summaryTabId))));
		$this->_exportDump->MediaTaxon = $this->models->MediaTaxon->_get(array('id' => $where));
		$this->_exportDump->MediaDescriptionsTaxon = $this->models->MediaDescriptionsTaxon->_get(array('id' => $where));
		$this->_exportDump->NbcExtras = $this->models->NbcExtras->_get(array('id' => $where));
		$this->_exportDump->TaxaRelations = $this->models->TaxaRelations->_get(array('id' => $where));
		$this->_exportDump->TaxaVariations = $this->models->TaxaVariations->_get(array('id' => $where));
		$this->_exportDump->VariationRelations = $this->models->VariationRelations->_get(array('id' => $where));
		$this->_exportDump->VariationsLabels = $this->models->VariationsLabels->_get(array('id' => $where));
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

			foreach((array)$this->_exportDump->ContentTaxa as $key => $val)
			{
				$this->_exportDump->ContentTaxa[$key]['content']=$this->supplantEmbeddedImgURLs($val['content']);
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

		$this->_exportDump->Matrices = $this->models->Matrices->_get(array('id' => $where));
		$this->_exportDump->MatricesNames = $this->models->MatricesNames->_get(array('id' => $where));
		$this->_exportDump->Characteristics = $this->models->Characteristics->_get(array('id' => $where));
		$this->_exportDump->CharacteristicsLabels = $this->models->CharacteristicsLabels->_get(array('id' => $where));
		$this->_exportDump->CharacteristicsStates = $this->models->CharacteristicsStates->_get(array('id' => $where));
		$this->_exportDump->CharacteristicsLabelsStates = $this->models->CharacteristicsLabelsStates->_get(array('id' => $where));
		$this->_exportDump->CharacteristicsMatrices = $this->models->CharacteristicsMatrices->_get(array('id' => $where));
		$this->_exportDump->Chargroups = $this->models->Chargroups->_get(array('id' => $where));
		$this->_exportDump->ChargroupsLabels = $this->models->ChargroupsLabels->_get(array('id' => $where));
		$this->_exportDump->CharacteristicsChargroups = $this->models->CharacteristicsChargroups->_get(array('id' => $where));
		$this->_exportDump->MatricesTaxa = $this->models->MatricesTaxa->_get(array('id' => $where));
		$this->_exportDump->MatricesVariations = $this->models->MatricesVariations->_get(array('id' => $where));
		$this->_exportDump->MatricesTaxaStates = $this->models->MatricesTaxaStates->_get(array('id' => $where));
		$this->_exportDump->GuiMenuOrder = $this->models->GuiMenuOrder->_get(array('id' => $where));

		$this->_defaultMatrixId = $this->_exportDump->Matrices[0]['id'];
	}

    private function makeKeyDump()
	{
		$where =
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->_projectLanguage
			);

		$this->_exportDump->Keysteps = $this->models->Keysteps->_get(array('id' => $where));
		$this->_exportDump->ContentKeysteps = $this->models->ContentKeysteps->_get(array('id' => $where));
		$this->_exportDump->ChoicesKeysteps = $this->models->ChoicesKeysteps->_get(array('id' => $where));
		$this->_exportDump->ChoicesContentKeysteps = $this->models->ChoicesContentKeysteps->_get(array('id' => $where));

		if ($this->_reduceURLs)
		{
			foreach((array)$this->_exportDump->ContentKeysteps as $key => $val)
			{
				$this->_exportDump->ContentKeysteps[$key]['content']=$this->supplantEmbeddedImgURLs($val['content']);
			}
			foreach((array)$this->_exportDump->ChoicesContentKeysteps as $key => $val)
			{
				$this->_exportDump->ChoicesContentKeysteps[$key]['choice_txt']=$this->supplantEmbeddedImgURLs($val['choice_txt']);
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

		$this->_exportDump->L2Maps = $this->models->L2Maps->_get(array('id' => $where));
		$this->_exportDump->L2OccurrencesTaxaCombi = $this->models->L2OccurrencesTaxaCombi->_get(array('id' => $where));
		$this->_exportDump->GeodataTypes = $this->models->GeodataTypes->_get(array('id' => $where));
		$this->_exportDump->GeodataTypesTitles = $this->models->GeodataTypesTitles->_get(array('id' => $where));

		if ($this->_reduceURLs)
		{
			foreach((array)$this->_exportDump->L2Maps as $key => $val)
			{
				if (stripos($val['image'],'http://')!==false || stripos($val['image'],'https://')!==false)
				{
					$d=pathinfo($val['image']);
					$this->_exportDump->L2Maps[$key]['image']=$d['basename'];
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

		$this->_exportDump->IntroductionPages = $this->models->IntroductionPages->_get(array('id' => $where));
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


	private function fixImageNames()
	{
		function needs_fixing( $s )
		{
			return preg_match( '/(\s+)|(\(|\))/',$s );
		}

		function do_fixing( $s )
		{
			return preg_replace( '/(\s+)|(\(|\))/', '_', $s );
		}

		function dont_duplicate( $s, $list1, $list2 )
		{
			$i=0;
			while ( in_array( $s,$list1 ) || in_array( $s,$list2 ) )
			{
				$s .= "_".$i;
			}
			return $s;
		}

		$this->makeImageList();
		$this->_renameImageList=array();

		if (isset($this->_exportDump->MediaTaxon))
		{
			foreach((array)$this->_exportDump->MediaTaxon as $key=>$val)
			{
				if ( needs_fixing( $val['file_name'] ) )
				{
					$p=do_fixing( $val['file_name'] );
					$p=dont_duplicate( $p, $this->_imageList, $this->_renameImageList );
					$this->_exportDump->MediaTaxon[$key]['file_name']=$p;
					$this->_renameImageList[]=array( $val['file_name'],$p );
				}
			}
		}

		if (isset($this->_exportDump->CharacteristicsStates))
		{
			foreach((array)$this->_exportDump->CharacteristicsStates as $key=>$val)
			{
				if ( needs_fixing( $val['file_name'] ) )
				{
					$p=do_fixing( $val['file_name'] );
					$p=dont_duplicate( $p, $this->_imageList, $this->_renameImageList );
					$this->_exportDump->CharacteristicsStates[$key]['file_name']=$p;
					$this->_renameImageList[]=array( $val['file_name'],$p );
				}
			}
		}

		if (isset($this->_exportDump->NbcExtras))
		{
			foreach((array)$this->_exportDump->NbcExtras as $key=>$val)
			{
				if ( ($val['name']=='url_thumbnail'||$val['name']=='url_image') && needs_fixing( $val['value'] ) )
				{
					$p=do_fixing( $val['value'] );
					$p=dont_duplicate( $p, $this->_imageList, $this->_renameImageList );
					$this->_exportDump->NbcExtras[$key]['value']=$p;
					$this->_renameImageList[]=array( $val['value'],$p );
				}
			}
		}

		if (isset($this->_exportDump->Keysteps))
		{
			foreach((array)$this->_exportDump->Keysteps as $key=>$val)
			{
				if ( needs_fixing( $val['image'] ) )
				{
					$p=do_fixing( $val['image'] );
					$p=dont_duplicate( $p, $this->_imageList, $this->_renameImageList );
					$this->_exportDump->Keysteps[$key]['image']=$p;
					$this->_renameImageList[]=array( $val['image'],$p );
				}
			}
		}

		if (isset($this->_exportDump->ChoicesKeysteps))
		{
			foreach((array)$this->_exportDump->ChoicesKeysteps as $key=>$val)
			{
				if ( needs_fixing( $val['choice_img'] ) )
				{
					$p=do_fixing( $val['choice_img'] );
					$p=dont_duplicate( $p, $this->_imageList, $this->_renameImageList );
					$this->_exportDump->ChoicesKeysteps[$key]['choice_img']=$p;
					$this->_renameImageList[]=array( $val['choice_img'],$p );
				}
			}
		}

		$this->setRenameImageList( $this->_renameImageList );
		unset($this->_imageList);
	}

	private function makeImageList()
	{
		if (isset($this->_exportDump->MediaTaxon))
		{
			foreach((array)$this->_exportDump->MediaTaxon as $val)
				$this->_imageList[]=$val['file_name'];
		}

		if (isset($this->_exportDump->CharacteristicsStates))
		{
			foreach((array)$this->_exportDump->CharacteristicsStates as $val)
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

		if (isset($this->_exportDump->Keysteps))
		{
			foreach((array)$this->_exportDump->Keysteps as $val)
			{
				if (!empty($val['image']))
					$this->_imageList[]=$val['image'];
			}
		}

		if (isset($this->_exportDump->ChoicesKeysteps))
		{
			foreach((array)$this->_exportDump->ChoicesKeysteps as $val)
			{
				if (!empty($val['choice_img']))
					$this->_imageList[]=$val['choice_img'];
			}
		}

		$this->_imageList=array_merge($this->_imageList,$this->_listOfEmbeddedImages);

		$this->setImageList( $this->_imageList );

	}

	private function convertDumpToSQLite()
	{

		$setsPerInsert = 1; // phonegap webdb doesn't seem to support inserting multiple sets at once

		$this->helpers->Mysql2Sqlite->setRemoveUniqueConstraints(true);

		foreach((array)$this->_exportDump as $class => $data)
		{

			$table = $this->models->$class->getTableName();
			$inserts = array();

			$c = $this->models->Taxa->freeQuery('show create table '.$table);
			$this->helpers->Mysql2Sqlite->convert($this->fixTablePrefix($this->removeUnwantedColumns($c[0]['Create Table'].';'),$table));

			$this->_sqliteQueriesDDL[] = $this->helpers->Mysql2Sqlite->getSqlDropTable();
			$this->_sqliteQueriesDDL = array_merge($this->_sqliteQueriesDDL,$this->helpers->Mysql2Sqlite->getSqlDropKeys());
			$this->_sqliteQueriesDDL[] = $this->helpers->Mysql2Sqlite->getSqlTable();

			$keys=$this->helpers->Mysql2Sqlite->getSqlKeys();
			$keys=$this->removeUnwantedColumnsFromKeys($keys);
			$this->_sqliteQueriesDDL = array_merge($this->_sqliteQueriesDDL,$keys);

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
		return $this->models->PagesTaxa->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'order' => 'show_order',
			'fieldAsIndex' => 'page_id'
		));

    }


    private function setRenameImageList( $list )
    {
		$this->moduleSession->setModuleSetting(array(
            'setting' => '_renameImageList',
            'value' => $list
        ));
    }

    private function getRenameImageList()
    {
		return $this->moduleSession->getModuleSetting('_renameImageList');
	}

    private function setImageList( $list )
    {
		$this->moduleSession->setModuleSetting(array(
            'setting' => '_imageList',
            'value' => $list
        ));
    }

    private function getImageList()
    {
		return $this->moduleSession->getModuleSetting('_imageList');
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
    FAMILY_RANK_ID : ".FAMILIA_RANK_ID.",
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
  exportVersion:'".$this->_matrixStandAloneExportVersion." (".date("Y-m-d H:i:s").")',
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
