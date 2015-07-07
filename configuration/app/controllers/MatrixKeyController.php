<?php
/*

	?mtrx=i --> override for specific matrix --> is this actually in use


*/


include_once ('Controller.php');
class MatrixKeyController extends Controller
{


    public $usedModels = array(
        'matrix', 
        'matrix_name', 
//        'matrix_taxon', 
        'matrix_taxon_state', 
//        'commonname', 
//        'characteristic', 
//        'characteristic_matrix', 
//        'characteristic_label', 
//        'characteristic_state', 
//        'characteristic_label_state', 
//        'chargroup_label', 
//        'chargroup', 
//        'characteristic_chargroup', 
//        'matrix_variation', 
//        'nbc_extras', 
//        'variation_relations',
//		'gui_menu_order'
    );
	
    public function __construct($p = null)
    {
        parent::__construct($p);

        $this->initialize();

    }

    public function __destruct()
    {
        parent::__destruct();
    }


    private function initialize ($force = false)
    {
		$this->initializeMatrixId();

//        $this->_matrixType = strtolower( $this->getSetting('matrixtype') );
// this should be per matrix not per project

		/*
        $this->_useCharacterGroups = $this->getSetting('matrix_use_character_groups') == '1';
        $this->useVariations = $this->getSetting('taxa_use_variations') == '1';
        $this->smarty->assign('useCharacterGroups', $this->_useCharacterGroups);
        $this->_useSepCoeffAsWeight = false; // $this->getSetting('matrix_use_sc_as_weight');
        $this->_matrixStateImageMaxHeight = $this->getSetting('matrix_state_image_max_height');
        $this->_externalSpeciesUrlTarget = $this->getSetting('external_species_url_target');
        $this->_matrixSuppressDetails = $this->getSetting('matrix_suppress_details','0')=='1';
		$this->_externalSpeciesUrlPrefix = $this->getSetting('external_species_url_prefix');
		$this->_matrix_use_emerging_characters = $this->getSetting('matrix_use_emerging_characters',true);
		$this->_matrix_calc_char_h_val = $this->getSetting('matrix_calc_char_h_val',true);


        if ($this->_matrixType == 'nbc') {
			$_SESSION['app']['system']['urls']['nbcImageRoot']=$this->_nbcImageRoot = $this->getSetting('nbc_image_root');
        }
		*/
    }

    public function identifyAction()
    {
		// backward compat
		// forward as is to index
	}
	
	private function initializeMatrixId()
	{
		$id=$this->getCurrentMatrixId();
		
		if ( is_null($id) )
		{
			$m=$this->getMatrix( null ); // get all
			
			if ( $m ) 
			{
				$m=array_shift($m);
				$this->setCurrentMatrixId( $m['id'] );
			}
		}

        $this->checkMatrixIdOverride();
        $this->checkMasterMatrixId();
		$this->setTotalEntityCount();
	}
		
    public function indexAction()
    {









die();
        $this->checkMatrixIdOverride();
		$this->setStoreHistory(false);
		


		$this->printGenericError($this->translate('No matrices have been defined.'));

    }





    public function identifsayAction()
    {

		
		
		
		
		
		
		
		
		
		
		
		
		die();
		

        if (!isset($id)) {
            $this->storeHistory = false;
            $this->redirect('index.php');
        }
        
		$matrix = $this->getMatrix($id);

        if (empty($matrix)) {
            $this->storeHistory = false;
            $this->redirect('index.php');
        }


        $this->setPageName(sprintf($this->translate('Matrix "%s": identify'), $matrix['name']));
		
		$characters = $this->getCharacteristics();

        if ($this->_matrixType == 'nbc')
		{
            $states = $this->stateMemoryRecall();
            $taxa = $this->nbcGetTaxaScores($states);
			$groups = $this->getCharacterGroups();

			$activeChars = array();

            foreach ((array) $states as $val)
                $activeChars[$val['characteristic_id']] = true;

			$countPerState = $this->getRemainingStateCount(array(
				'states' => $states
			));

			$countPerCharacter = $this->getRemainingCharacterCount();

			$menu = $this->getGUIMenu(
					array(
						'groups'=>$groups,
						'characters'=>$characters,
						'appendExcluded'=>false,
						'checkImages'=>true
					)
				);
				
            $this->smarty->assign('taxaJSON', json_encode(
            array(
                'results' => $taxa, 
				'paramCount' => count((array)$states),
                'count' => array(
                    'results' => count((array) $taxa),
                ),
				'menu' => array(
					'groups' => $menu,
					'activeChars' => $activeChars,
					'storedStates' => $states
				),
				'countPerState' => $countPerState,
				'countPerCharacter' => $countPerCharacter,
				'selectedStates' => $states,
				'matrix' => $this->getCurrentMatrixId()
            ),JSON_HEX_APOS | JSON_HEX_QUOT));

            $this->smarty->assign('guiMenu',$menu);

			if ($this->_useSepCoeffAsWeight)
				$this->smarty->assign('coefficients', $this->getRelevantCoefficients($states));

            $this->smarty->assign('nbcImageRoot', $this->_nbcImageRoot);
            $this->smarty->assign('nbcFullDatasetCount', $_SESSION['app'][$this->spid()]['matrix'][$this->getCurrentMatrixId()]['totalEntityCount']);
            $this->smarty->assign('nbcStart', $this->getSessionSetting('nbcStart'));
            $this->smarty->assign('nbcSimilar', $this->getSessionSetting('nbcSimilar'));
			$this->smarty->assign('nbcPerLine', $this->getSetting('matrix_items_per_line'));
			$this->smarty->assign('nbcPerPage', $this->getSetting('matrix_items_per_page'));
			$this->smarty->assign('nbcBrowseStyle', $this->getSetting('matrix_browse_style'));
			$this->smarty->assign('matrix_items_per_page', $this->getSetting('matrix_items_per_page'));
			$this->smarty->assign('master_matrix', $this->getMatrix($this->getMasterMatrixId()) );
			
			$this->smarty->assign('nbcDataSource', 
				array(
					'author' => $this->getSetting('source_author'),
					'title' => $this->getSetting('source_title'),
					'photoCredit' => $this->getSetting('source_photocredit'),
					'url' => $this->getSetting('source_url')
				)
			);

        }
        else {
            
            $taxa = $this->getTaxaInMatrix();
            
            $this->smarty->assign('matrices', $this->getMatricesInMatrix());
            $this->smarty->assign('matrixCount', $this->getMatrixCount());
            $this->smarty->assign('storedStates', $this->stateMemoryRecall());
            $this->smarty->assign('storedShowState', $this->showStateRecall());
        }

        if (isset($taxa))
			$this->smarty->assign('taxa', $taxa);

        $this->smarty->assign('matrix', $matrix);
        $this->smarty->assign('projectId', $this->getCurrentProjectId());
        $this->smarty->assign('function', 'Identify');
        $this->smarty->assign('characteristics', $characters);
		$this->smarty->assign('matrix_use_emerging_characters', $this->_matrix_use_emerging_characters);

        $this->printPage('identify');
    }





    private function setCurrentMatrixId( $id=null )
    {
		if (is_null($id))
		{
			unset($_SESSION['app'][$this->spid()]['matrix']['active']);
		}
		else
		{
	        $_SESSION['app'][$this->spid()]['matrix']['active'] = $id;
		}
    }
	
    private function getCurrentMatrixId()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['active']) ? $_SESSION['app'][$this->spid()]['matrix']['active'] : null;
    }

    private function setMasterMatrixId( $id=null )
    {
		if ( is_null($id) )
		{
			unset($_SESSION['app'][$this->spid()]['matrix']['master_id']);
		}
		else
		{
			$_SESSION['app'][$this->spid()]['matrix']['master_id']=$id;
		}
    }

    private function getMasterMatrixId()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['v']) ? $_SESSION['app'][$this->spid()]['matrix']['master_id'] : null;
    }

	private function setTotalEntityCount()
	{
		if ( is_null($this->getCurrentMatrixId()) )
		{
			return;
		}
		
		echo 'a';
        if ($this->_matrixType == 'nbc')
		{
			$q = 
				"select
					count(distinct _a.taxon_id) as tot
				from
					%PRE%matrices_taxa _a
				left join %PRE%matrices_taxa_states _b
					on _a.project_id = _b.project_id
					and _a.matrix_id = _b.matrix_id
					and _a.taxon_id = _b.taxon_id
				where _a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
	
				union all
	
				select
					count(distinct _a.variation_id) as tot
					from
						%PRE%matrices_variations _a
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.variation_id = _b.variation_id
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId()
					;

		}
		else
		{
			$q = 
				"select
					count(distinct _a.taxon_id) as tot
					from
						%PRE%matrices_taxa _a
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.taxon_id = _b.taxon_id
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId();
					;
		}

		$results = $this->models->MatrixTaxonState->freeQuery( $q );
		
		q($q,1);

		return $results[0]['tot']+(isset($results[1]['tot']) ? $results[1]['tot'] : 0);
		
	}





	private function checkMatrixIdOverride()
    {
        if ( !$this->rHasVal('mtrx') )
		{
			return;
		}
		
		$m = $this->getMatrix( array( 'id'=>$this->rGetVal('mtrx') ) );

        if ( empty($m) )
		{
			return;
		}
		
		$this->setCurrentMatrixId( $m['id'] );
		//$this->setTotalEntityCount();
    }

    private function checkMasterMatrixId()
    {
        $m=$this->models->MatrixTaxonState->_get(
			array(
				'id' =>
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'matrix_id !=' => $this->getCurrentMatrixId(), 
						'ref_matrix_id' => $this->getCurrentMatrixId()
					), 
				'columns' => 
					'distinct matrix_id'
			));		
		
		if ($m)
		{
			$this->setMasterMatrixId( $m[0]['matrix_id'] );
		}
		else
		{
			$this->setMasterMatrixId();
		}

    }




    private function getMatrix( $p )
    {
		
		$id=isset($p['id']) ? $p['id'] : null;
		
		$m = $this->models->Matrix->freeQuery(array(
			"query" => "
				select 
					_a.id,
					_a.default,
					_b.name
				from
					%PRE%matrices _a
					
				left join %PRE%matrices_names _b
					on _a.project_id = _b.project_id
					and _a.id = _b.matrix_id
					and _b.language_id = " . $this->getCurrentLanguageId() ."
	
				where
					_a.project_id = " .  $this->getCurrentProjectId() ."
					and _a.got_names = 1
					" . ( isset($id) ? "and _a.id = " . $id : "" ) . "
				order by
					_a.default desc
		",
		"fieldAsIndex" => "id"
		));
		
		return $m;

	}
			




}	