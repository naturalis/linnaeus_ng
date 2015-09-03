<?php

/*
	data & matching
	on initial load, the entire data-set is fed into data.dataset in matrix.js via
	setDataSet() in index.php/tpl. from that point on, each time a character state
	is (un)set, setState() is called, which only returns an array of taxon id's with
	scores, which are applied to data.dataset in applyScores(), resulting in
	data.resultset, which contains the actual results as they are displayed.
	
	sorting
	initial sorting is done in MatrixKeyController::sortDataSet(), using one of the
	following fields:
		- by field name defined by setting 'initial_sort_column'
		- by taxon concept
		- by label (always exists for each taxon, variation and matrix)
	for each of compared items $a and $b the first existing field from the list above
	is used for comparison (theoretically, a variation's label could be compared with
	a taxon's scientific name)
	sorting after selection of states is done by score (matching percentage).
	data.scores sorted this way, causing data.resultset to be sorted in the same way.
	should the setting 'always_sort_by_initial' be true, them data.resultset is
	re-sorted in JS::sortResults(), using the field defined by 'initial_sort_column'.
	this way, the score sort can be overridden. additionally, there is a hook-function
	hook_postSortResults() which is called after sortResults(), allowing specific
	implementations to further alter the results' order (function is called regardless
	of the values of 'always_sort_by_initial' or 'initial_sort_column'.

*/


/*
	needs to be undone of NBC:
		private $_nbcImageRoot=true;

*/


include_once ('Controller.php');
include_once ('ModuleSettingsController.php');
class MatrixKeyController extends Controller
{

    public $usedModels = array(
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
//        'commonname', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
//        'characteristic_label_state', 
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup', 
        'matrix_variation', 
        'nbc_extras', 
        'variation_relations',
		'gui_menu_order',
		'module_settings',
		'content_introduction',
		'media_taxon'
    );

    public $controllerPublicName = 'Matrix key';
    public $controllerBaseName = 'matrix';
	
	private $_totalEntityCount=0;
	private $_activeMatrix=null;

//	private $_characters=null;
	private $_dataSet=null;
	private $_facetmenu=null;
	private $_scores=null;
	private $_related=null;
	private $_searchTerm=null;
	private $_searchResults=null;
	private $_introductionLinks=null;
	private $_incUnknowns=false;

	private $_master_matrix;
	private $settings;

	private $_nbc_image_root=true;
	
	
    public $cssToLoad = array('matrix.css');

    public $jsToLoad = array(
        'all' => array('main.js','matrix.js'), 
        'IE' => array()
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

    private function initialize()
    {
		$this->moduleSettings=new ModuleSettingsController;
		
		$this->moduleSettings->setUseDefaultWhenNoValue( true );
		$this->moduleSettings->assignModuleSettings( $this->settings );

		$this->initializeMatrixId();
		$this->setActiveMatrix();

		if (is_null($this->getCurrentMatrixId()))
		{
			$this->printGenericError($this->translate('No matrices have been defined.'));
		}
		
		$this->setIntroductionLinks();

		$this->_nbc_image_root = $this->getSetting('nbc_image_root');

		$this->smarty->assign( 'image_root_skin', $this->_nbc_image_root );
		$this->smarty->assign( 'introduction_links', $this->getIntroductionLinks() );
		$this->smarty->assign( 'settings', $this->settings );

		$this->setFacetMenu();
		$this->setIncUnknowns( false );
    }

    public function indexAction()
    {
		$matrix=$this->getActivematrix();

        $this->setPageName(sprintf($this->translate('Matrix "%s": identify'), $matrix['name']));

		$this->setMasterMatrix();
		$this->setDataSet();
		$this->setScores();

		$this->smarty->assign('session_scores',json_encode( $this->getScores() ));
		$this->smarty->assign('session_states',json_encode( $this->getSessionStates() ));
		$this->smarty->assign('session_characters',json_encode( $this->getCharacterCounts() ));
		$this->smarty->assign('session_statecount',json_encode( $this->setRemainingStateCount() ));
		$this->smarty->assign('full_dataset',json_encode( $this->getDataSet() ));

        $this->smarty->assign('matrix', $matrix);
		$this->smarty->assign('master_matrix', $this->getMasterMatrix() );
		$this->smarty->assign('facetmenu', $this->getFacetMenu());
		$this->smarty->assign('states', $this->getCharacterStates(array("id"=>"*")) );

        $this->printPage();
    }

    public function identifyAction()
    {
		// backward compatibility
		$this->redirect( str_replace('identify.php','index.php',$_SERVER["REQUEST_URI"]));
	}

    public function characterStatesAction()
	{
		$character=$this->getCharacter(array('id'=>$this->rGetVal( 'id' )));
		$states=$this->getCharacterStates(array('char'=>$this->rGetVal( 'id' )));

		$this->smarty->assign('character', $character);
		$this->smarty->assign('states', $states);
		$this->smarty->assign('states_selected', $this->getSessionStates( array('char'=>$this->rGetVal( 'id' ),'reindex'=>true)));
		$this->smarty->assign('states_remain_count', $this->setRemainingStateCount(array('char'=>$this->rGetVal( 'id' ))));
	
		$this->printPage();
	}
			
    public function ajaxInterfaceAction ()
    {
		if ($this->rHasVar('key'))
		{
			$this->setCurrentMatrixId($this->rGetVal('key'));
		}
	
		if ($this->rHasVal('action', 'get_menu'))
		{
			$this->smarty->assign('returnText', json_encode($this->getFacetMenu()));
        }	
		
        else					

		if ($this->rHasVal('action','set_state'))
		{
			if ($this->rHasVal('state') && $this->rHasVal('value'))
			{
				$state=$this->rGetVal('state') . ':' . $this->rGetVal('value');
			}
			else
			if ($this->rHasVal('state'))
			{
				$state=$this->rGetVal('state');
			}
			else
			{
				return;
			}
		
			$this->sessionStateStore( $state );

			$this->setScores();
			$this->smarty->assign('returnText',
				json_encode( array(
					'scores'=>$this->getScores(),
					'states'=>$this->getSessionStates(),
					'characters'=>$this->getCharacterCounts(),
					'statecount'=>$this->setRemainingStateCount()
				)));
		}

        else					

		if ($this->rHasVal('action','clear_state'))
		{
			if ($this->rHasVal('state'))
			{
				$this->sessionStateUnset($this->rGetVal('state'));
			}
			else
			{
				$this->sessionStateUnset();
			}

			$this->setScores();
			$this->smarty->assign('returnText',
				json_encode( array(
					'scores'=>$this->getScores(),
					'states'=>$this->getSessionStates(),
					'characters'=>$this->getCharacterCounts(),
					'statecount'=>$this->setRemainingStateCount()
				)));
		}

		else
		
		if ($this->rHasVal('action', 'get_similar'))
		{
			$this->setRelatedEntities( array('id'=>$this->rGetVal('id'),'type'=>$this->rGetVal('type')) );
			$this->smarty->assign('returnText',json_encode( $this->getRelatedEntities()) );
		}

		else
		
		if ($this->rHasVal('action', 'get_search'))
		{
			$this->setSearchTerm( array('search'=>$this->rGetVal('search')) );
			$this->setSearchResults();
			$this->smarty->assign('returnText',json_encode( $this->getSearchResults()) );
		}

		else
		
		if ($this->rHasVal('action', 'set_unknowns'))
		{

			if (!$this->settings->enable_treat_unknowns_as_matches) return;

			$this->setIncUnknowns( $this->rGetVal( 'value' )==1 );
			$this->setScores();
			$this->smarty->assign('returnText',
				json_encode( array(
					'scores'=>$this->getScores(),
					'states'=>$this->getSessionStates(),
					'characters'=>$this->getCharacterCounts(),
					'statecount'=>$this->setRemainingStateCount()
				)));
		}

		$this->printPage();	
	}
	
	private function initializeMatrixId()
	{
		$id=$this->getCurrentMatrixId();

		if ( empty($id) && $id!==0 )
		{
			$m=$this->getMatrix( array('id'=>'*') ); // get all
			
			if ( $m ) 
			{
				$m=array_shift($m);
				$this->setCurrentMatrixId( $m['id'] );
			}
		}

        $this->checkMatrixIdOverride();
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

    private function setMasterMatrix()
    {
        $mts = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id !=' => $this->getCurrentMatrixId(), 
                'ref_matrix_id' => $this->getCurrentMatrixId()
            ), 
            'columns' => 'distinct matrix_id'
        ));		
		
		if ($mts)
		{
			$this->_master_matrix=$this->getMatrix( array("id"=>$mts[0]['matrix_id']) );
		}
		else
		{
			$this->_master_matrix=null;
		}
    }

    private function getMasterMatrix()
    {
		return $this->_master_matrix;
    }

	private function setActiveMatrix()
	{
		$this->_activeMatrix=$this->getMatrix( array('id'=>$this->getCurrentMatrixId()) );
	}

	private function getActiveMatrix()
	{
		return $this->_activeMatrix;
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
		else
		{
			$this->setCurrentMatrixId( $m['id'] );
		}
    }

    private function getMatrix( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		
		if (is_null($id))
			return;
		
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
					" . ( isset($id) && $id!='*' ? "and _a.id = " . $id : "" ) . "
				order by
					_a.default desc
		",
		"fieldAsIndex" => "id"
		));
		
		return ( isset($id) && $id!='*' && isset($m[$id]) ? $m[$id] : ( isset($m) ? $m  : null ) ) ;

	}

    private function getCharacterStates( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$char=isset($p['char']) ? $p['char'] : null;

		if (is_null($id) && is_null($char))
			return;

        $cs=$this->models->CharacteristicState->freeQuery("
			select 
				_a.id,
				_a.characteristic_id,
				_a.file_name,
				_a.file_dimensions,
				_a.lower,
				_a.upper,
				_a.mean,
				_a.sd,
				_a.got_labels,
				_a.show_order,
				_b.type,
				_c.label,
				_c.text
				
			from %PRE%characteristics_states _a
			
			left join %PRE%characteristics _b
				on _a.characteristic_id = _b.id
				and _a.project_id=_b.project_id

			left join %PRE%characteristics_labels_states _c
				on _a.id = _c.state_id
				and _a.project_id=_c.project_id
				and _c.language_id=".$this->getCurrentLanguageId()."

			where 
				_a.project_id=".$this->getCurrentProjectId()." 
				".( empty($id) || $id=='*' ? "" : "and _a.id=" . $id )." 
				".( empty($char) ? "" : "and _a.characteristic_id=" . $char )." 

			order by 
				_a.show_order
			"
		);

        foreach ((array) $cs as $key => $val)
		{
            $cs[$key]['img_dimensions']=explode(':',$val['file_dimensions']);
		}

        return (isset($id) && $id!='*' && isset($cs[0]) ? $cs[0] : $cs);
    }

    private function getCharacteristicHValue( $p )
    {
		$charId=isset($p['id']) ? $p['id'] : null;
		$states=isset($p['states']) ? $p['states'] : null;
		
		if ( !isset($states) || !isset($states) ) return 0;

        $taxa=array();

        $tot=0;
			        
        foreach((array)$states as $key=>$val)
		{
            $states[$key]['n'] = 0;
            
            $mts = $this->models->MatrixTaxonState->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $this->getCurrentMatrixId(), 
                    'characteristic_id' => $charId, 
                    'state_id' => $val['id']
                ), 
                'columns' => 'distinct taxon_id'
            ));
            
            foreach ((array) $mts as $val2)
			{
                @$taxa[$val2['taxon_id']]['states']++;
                $states[$key]['n']++;
                $tot++;
            }
        }
		
        if ($tot==0) return 0;
        
		$hValue=0;
        
        foreach ((array) $states as $val)
		{
            $hValue += ($val['n'] / $tot) * log($val['n'] / $tot, 10);
		}
        
		$hValue = is_nan($hValue) ? 0 : -1 * $hValue;
        
        $uniqueTaxa = 0;
        
        foreach ((array) $taxa as $val)
		{
            if ($val['states'] == 1)
			{
                $uniqueTaxa++;
			}
		}
        
		$corrFactor = $uniqueTaxa / $tot;
        
        return $hValue * ($this->controllerSettings['useCorrectedHValue'] == true ? $corrFactor : 1);
    }

    private function setDataSet()
    {
		$this->_dataSet=$this->getAllIdentifiableEntities();
	}

    private function getDataSet()
    {
		return $this->_dataSet;
	}
	

	private function induceThumbNailFromImage( &$item )
	{
		if( 
			!empty($this->settings->img_to_thumb_regexp_pattern) &&
			!isset($item['url_thumb']) &&
			isset($item['url_image'])
		)
		{
			$item['url_thumb']=
				@preg_replace(
					$this->settings->img_to_thumb_regexp_pattern,
					$this->settings->img_to_thumb_regexp_replacement,
					$item['url_image']
				);
		}
	}

	private function getAllIdentifiableEntities()
	{
		$taxa=$this->getTaxaInMatrix();
		$variations=$this->getVariationsInMatrix();
		$matrices=$this->getMatricesInMatrix();

		$info=$this->getAllNBCExtras();

		if ( !empty($info) )
		{
			foreach((array)$taxa as $key=>$val)
			{
				$taxa[$key]['info']=isset($info['taxon'][$val['id']]) ? $info['taxon'][$val['id']] : null;
				$this->induceThumbNailFromImage( $taxa[$key]['info'] );
			}
			foreach((array)$variations as $key=>$val)
			{
				$variations[$key]['info']=isset($info['variation'][$val['id']]) ? $info['variation'][$val['id']] : null;
				$this->induceThumbNailFromImage( $variations[$key]['info'] );
			}
		}

		if ( isset($this->settings->use_overview_image) && $this->settings->use_overview_image )
		{
			foreach((array)$taxa as $key=>$val)
			{
				$d=$this->models->MediaTaxon->_get(array("id"=>
					array(
						"taxon_id"=>$val['id'],
						"project_id" => $this->getCurrentProjectId(), 
						"overview_image"=>"1"
				)));
				
				if ( $d )
				{
					$taxa[$key]['info']['url_image']=$d[0]['file_name'];
				}
			}
		}

		$all=array_merge((array)$taxa,(array)$variations,(array)$matrices);

		if ($all) 
		{
			usort($all, array($this,'sortDataSet'));
		}
		
		return $all;
		
	}

    private function getTaxaInMatrix()
    {
		$m=$this->models->MatrixTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'matrix_id' => $this->getCurrentMatrixId()
				), 
				'columns' => 'taxon_id'
			));

		$taxa=array();

        foreach ( (array)$m as $key=>$val )
		{
			$d=$this->getTaxonById( $val['taxon_id'] );
			
			if (
				($this->settings->allow_empty_species) ||
				(!isset($val['is_empty'])) ||
				(!$this->settings->allow_empty_species && $val['is_empty']==1))
			{
				$d['type']='taxon';
				if ($this->settings->suppress_details!=1)
				{
					$d['states']=$this->getTaxonStates( $val['taxon_id'] );
				}
				$d['related_count']=$this->getRelatedEntityCount( array('id'=>$val['taxon_id'],'type'=>'taxon') );
				$taxa[]=$d;
			}

        }
		
		$this->customSortArray($taxa, array(
			'key' => 'taxon', 
			'case' => 'i'
		));

        return $taxa;
    }

    private function getVariationsInMatrix()
    {

		if ( !$this->models->MatrixVariation->getTableExists() )
			return null;
		
        $m = $this->models->MatrixVariation->freeQuery(array(
			'query'=> "
				select
					_b.id,
					_b.taxon_id,
					_b.label,
					_c.language_id,
					_c.label,
					_c.label_type,
					'variation' as type

				from %PRE%matrices_variations _a

				left join %PRE%taxa_variations _b
					on _a.project_id=_b.project_id
					and _a.variation_id = _b.id

				left join %PRE%variations_labels _c
					on _a.project_id=_c.project_id
					and _a.variation_id = _c.variation_id
					and _c.language_id = ". $this->getCurrentLanguageId() ."
			
				where 
					_a.project_id = ". $this->getCurrentProjectId() ."
					and _a.matrix_id = ". $this->getCurrentMatrixId()."
				order by
					_c.label",

            'fieldAsIndex' => 'variation_id'
        ));

        foreach ( (array)$m as $key=>$val )
		{
			$m[$key]['taxon']=$this->getTaxonById( $val['taxon_id'] );
			$m[$key]['gender']=$this->extractGenderTag( $val['label'] );
			if ($this->settings->suppress_details!=1)
			{
				$m[$key]['states']=$this->getVariationStates( $val['id'] );
			}
			$m[$key]['related_count']=$this->getRelatedEntityCount( array('id'=>$val['id'],'type'=>'variation') );
        }

        return $m;
    }

    private function getMatricesInMatrix()
    {
        $matrices = $this->models->MatrixTaxonState->freeQuery("
			select 
				distinct _a.ref_matrix_id as id,
				_b.name as label,
				'matrix' as type 
			from 
				%PRE%matrices_taxa_states _a

			left join %PRE%matrices_names _b
				on _a.project_id = _b.project_id
				and _a.ref_matrix_id = _b.matrix_id
				and _b.language_id = " . $this->getCurrentLanguageId() . "

			where 
				_a.project_id = " . $this->getCurrentProjectId() . " 
				and _a.matrix_id = " . $this->getCurrentMatrixId() . " 
				and _a.ref_matrix_id is not null
			");
			
		foreach((array)$matrices as $key=>$val)
		{
			if ($this->settings->suppress_details!=1)
			{
				$matrices[$key]['states']=$this->getMatrixStates( $val['id'] );
			}
		}

        return $matrices;
    }



	// REFAC2015: should be moved to kenmerkenmodule!!!!!
	// maybe rethink location of thumbs & images?
	// rethink induceThumbNailFromImage() as well
	// and induceEncTypeFromRemoteUrl()
    private function getAllNBCExtras()
    {
		if ( !$this->models->NbcExtras->getTableExists() )
			return null;

		$d=$this->models->NbcExtras->freeQuery(array(
			"query"=>"
				select 
					ref_id,
					ref_type,
					name,
					value
				from 
					%PRE%nbc_extras 
				where 
					project_id = ".$this->getCurrentProjectId(),
			"fieldAsIndex"=>"temp_index"));
			
		$res=array();
		foreach((array)$d as $key=>$val)
		{
			$res[$val['ref_type']][$val['ref_id']][$val['name']]=$val['value'];
		}
		return $res;
    }

	private function extractGenderTag( $label )
	{
		$gender=$gender_label=null;

		if (
			preg_match('/^(.*)(man|vrouw|beide)$/', $label, $matches) ||
			preg_match('/^(.*)(male|female|both)$/', $label, $matches)
			) {

			switch($matches[2]) {
				case 'man' :
				case 'male':
					$gender='m';
					$gender_label='man';
					break;
				case 'vrouw' :
				case 'female':
					$gender='f';
					$gender_label='vrouw';
					break;
			}
				
		}

		return
			array(
				'gender' => $gender,
				'gender_label' => $this->translate($gender_label)
			);
		
	}

    private function getEntityStates( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$type=isset($p['type']) ? $p['type'] : null;
		
		if ( is_null($id) || is_null($type) ) return;
		
		$mts=$this->models->MatrixTaxonState->freeQuery("
			select 
				_c.label as characteristic,
				_b.type,
				_b.type,
				_a.project_id,
				_a.state_id,
				_d.characteristic_id,
				_d.file_name,
				_d.lower,
				_d.upper,
				_d.mean,
				_d.sd,
				_d.got_labels,
				_e.label,
				_g.label as group_label
				
			from %PRE%matrices_taxa_states _a
			
			left join %PRE%characteristics_states _d
				on _a.project_id=_d.project_id
				and _a.state_id=_d.id
	
			left join %PRE%characteristics _b
				on _a.project_id=_b.project_id
				and _a.characteristic_id=_b.id
	
			left join %PRE%characteristics_labels _c
				on _a.project_id=_c.project_id
				and _a.characteristic_id=_c.characteristic_id
				and _c.language_id=".$this->getCurrentLanguageId()."
	
			left join %PRE%characteristics_labels_states _e
				on _a.state_id = _e.state_id
				and _a.project_id=_e.project_id
				and _e.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%characteristics_chargroups _f
				on _a.project_id=_f.project_id
				and _a.characteristic_id=_f.characteristic_id
			
			left join %PRE%chargroups_labels _g
				on _f.project_id=_g.project_id
				and _f.chargroup_id=_g.chargroup_id
				and _g.language_id=".$this->getCurrentLanguageId()."

			where 
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.matrix_id = ".$this->getCurrentMatrixId()." 
				and _a.".($type=="variation" ? "variation_id" : ($type=="matrix" ? "ref_matrix_id" : "taxon_id"))."=".$id
		);		
		
		$res=array();
		foreach((array)$mts as $val)
		{
			$d=explode('|',$val['characteristic']);
			$res[$val['characteristic_id']]['characteristic']=$d[0];
			//$res[$val['characteristic_id']]['explanation']=$d[1];
			$res[$val['characteristic_id']]['type'] = $val['type'];
			$res[$val['characteristic_id']]['group_label'] = $val['group_label'];
			$res[$val['characteristic_id']]['states'][$val['state_id']] = array(
				'characteristic_id'=>$val['characteristic_id'],
				'id'=>$val['state_id'],
				'file_name'=>$val['file_name'],
				'lower'=>$val['lower'],
				'upper'=>$val['upper'],
				'mean'=>$val['mean'],
				'sd'=>$val['sd'],
				'got_labels'=>$val['got_labels'],
				'label'=>$val['label']
			);
		}
		
		return $res;
    }

    private function getTaxonStates( $id )
    {
        return $this->getEntityStates( array('id'=>$id,'type'=>'taxon') );
    }

    private function getVariationStates( $id )
    {
        return $this->getEntityStates( array('id'=>$id,'type'=>'variation') );
    }
    
    private function getMatrixStates( $id )
    {
        return $this->getEntityStates( array('id'=>$id,'type'=>'matrix') );
    }



	private function setFacetMenu()
	{
		$menu=$this->models->GuiMenuOrder->freeQuery("
		
			select 
				id,
				label,
				type,
				show_order_main,
				show_order_sub
			 from (
		
				select 
					_a.id,
					ifnull(_c.label,_cdef.label) as label,
					'char' as type,
					_gmo.show_order as show_order_main,
					_b.show_order as show_order_sub
	
				from
					%PRE%characteristics _a
				
				right join %PRE%characteristics_matrices _b
					on _a.id = _b.characteristic_id
					and _a.project_id = _b.project_id
					and _b.matrix_id = " . $this->getCurrentMatrixId() . "
	
				right join %PRE%characteristics_labels _c
					on _a.project_id = _c.project_id
					and _a.id = _c.characteristic_id
					and _c.language_id = ". $this->getCurrentLanguageId()."

				right join %PRE%characteristics_labels _cdef
					on _a.project_id = _cdef.project_id
					and _a.id = _cdef.characteristic_id
					and _cdef.language_id = ". $this->getDefaultLanguageId()."
					
				left join %PRE%characteristics_chargroups _d
					on _a.project_id = _d.project_id
					and _a.id = _d.characteristic_id
					
				left join %PRE%chargroups _e
					on _d.project_id = _e.project_id
					and _d.chargroup_id = _e.id
					and _e.matrix_id = " . $this->getCurrentMatrixId() . "
					
				left join %PRE%gui_menu_order _gmo
					on _a.project_id = _gmo.project_id
					and _gmo.matrix_id = " . $this->getCurrentMatrixId() . "
					and _gmo.ref_id = _a.id
					and _gmo.ref_type='char'
					
				where 
					_a.project_id = " . $this->getCurrentProjectId() . " 
					and _d.id is null
	
				union		
	
				select 
					_a.id,
					ifnull(_c.label,_a.label) as label,
					'group' as type,
					_gmo.show_order as show_order_main,
					_a.show_order as show_order_sub
	
				from
					%PRE%chargroups _a
	
				left join %PRE%chargroups_labels _c
					on _a.project_id = _c.project_id
					and _a.id = _c.chargroup_id
					and _c.language_id = ". $this->getCurrentLanguageId()."
	
				left join %PRE%gui_menu_order _gmo
					on _a.project_id = _gmo.project_id
					and _gmo.matrix_id = " . $this->getCurrentMatrixId() . "
					and _gmo.ref_id = _a.id
					and _gmo.ref_type='group'
	
				where 
					_a.project_id = " . $this->getCurrentProjectId() . " 
					and _a.matrix_id = " . $this->getCurrentMatrixId() ." 			
			
			) as unionized
			
			order by show_order_main, show_order_sub, label
		
		");

		foreach((array)$menu as $key=>$val)
		{
			if ($val['type']=='group')
			{
				$menu[$key]['chars']=$this->getGroupCharacters( $val['id'] );
			}
			else
			if ($val['type']=='char')
			{
				$d=$this->getCharacter( $val );
				$menu[$key]['prefix']=$d['prefix'];
			}
		}
		
		$this->_facetmenu=$menu;	
	}

	private function getFacetMenu()
	{
		return $this->_facetmenu;
	}

    private function getGroupCharacters( $id )
    {	
		$c=$this->models->CharacteristicChargroup->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'chargroup_id' => $id
				), 
				'order' => 'show_order'
			));

		foreach ((array)$c as $val)
		{
			$d[]=$this->getCharacter( array('id'=>$val['characteristic_id']) );
		}

        return $d;
    }

    private function getCharacter( $p )
    {
		$id = isset($p['id']) ? $p['id'] : null;
		
		if (empty($id)) return;

        $c = $this->models->Characteristic->freeQuery("
			select 
				_a.id,
				_a.type,
				_c.label,
				if (_a.type='media'||_a.type='text','c','f') as prefix

			from 
				%PRE%characteristics _a

			left join %PRE%characteristics_labels _c
				on _a.project_id=_c.project_id
				and _a.id=_c.characteristic_id
				and _c.language_id=".$this->getCurrentLanguageId()."

			where 
				_a.project_id = " . $this->getCurrentProjectId() . "
				and _a.id = " . $id . "
		");
		
		$char=$c[0];
        
		if (strpos($char['label'],'|')!==false)
		{
			$d = explode('|',$char['label'],3);
			$char['label'] = isset($d[0]) ? $d[0] : '';
			$char['info'] = isset($d[1]) ? $d[1] : '';
			$char['unit'] = isset($d[2]) ? $d[2] : '';
		}
					
		if ($char['type']=='range' || $char['type']=='distribution')
		{
			$cs = $this->models->CharacteristicState->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'characteristic_id' => $id
					), 
					'columns' => 'min(lower) as lowest,max(upper) as most_upper'
				));

			$char['min'] = 1.5;//$cs[0]['lowest'];
			$char['max'] = $cs[0]['most_upper'];
			$char['min_display'] = round($char['min'],(is_float($char['min']) ? 1 : 0));
			$char['max_display'] = round($char['max'],(is_float($char['max']) ? 1 : 0));
		}
		
        return $char;
    }




    private function sessionStateStore( $data )
    {
        foreach ((array)$data as $key=>$val)
		{
            $d = explode(':', $val);
            
            if ($d[0] == 'f') // f:x:n[:sd] (free values)
			{
                $_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$d[0] . ':' . $d[1]] = $val;
			}
            else // c:x:y (fixed values)
			{
                $_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$val] = $val;
			}
        }
    }

    private function sessionStateUnset( $id=null )
    {
        if (empty($id))
		{
            unset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()]);
		}
        else
		{
            $d = explode(':', $id);
            if ($d[0] == 'f')
			{
	            unset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$d[0] . ":" . $d[1]]);
			}
			else
			{
	            unset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$id]);
			}
		}
    }

    private function getSessionStates( $p=null )
    {
		$char = isset($p['char']) ? $p['char'] : null;
		$reindex = isset($p['reindex']) ? $p['reindex'] : null;

        if (isset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()]))
		{
            $states=array();
            
            foreach ((array) $_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()] as $key=>$val)
			{
                $states[$key]['val'] = $val;
                
                $d = explode(':', $val);
                
                if ($d[0]=='c' && isset($d[2]))
				{
                    $d = $this->getCharacterStates( array('id'=>$d[2]) );
                    $states[$key]['type'] = 'c';
                    $states[$key]['id'] = $d['id'];
                    $states[$key]['characteristic_id'] = $d['characteristic_id'];
                    $states[$key]['label'] = $d['label'];
                    $states[$key]['file_name'] = $d['file_name'];
                }
                else
				if ($d[0]=='f' && isset($d[2]))
				{
                    $states[$key]['type'] = 'f';
                    $states[$key]['characteristic_id'] = $d[1];
                    $states[$key]['value'] = $d[2];
                }
                
                if (!empty($char) && !in_array($states[$key]['characteristic_id'],(array)$char))
				{
                    unset($states[$key]);
				}
                if (empty($states[$key]['characteristic_id']))
				{
                    unset($states[$key]);
				}
            }

			if ($reindex && !empty($states))
			{
				$d=array();

				foreach((array)$states as $key=>$val)
				{
					if (isset($val['id']))
					{
						$d[$val['id']]=$val;
					}
					else
					{
						$d[$val['characteristic_id']]=$val;
					}
				}

				$states=$d;
			}
            
            return !empty($states) ? $states : null;
        }
        else
		{
            return null;
        }
    }
	


    private function setScores()
    {
		$scores=$this->getScoresRestrictive(
			array(
				'states'=>$this->getSessionStates(),
				'incUnknowns'=>$this->getIncUnknowns()
			)
		);
		//$scores = $this->getScoresLiberal( array('states'=>$this->getSessionStates(),'incUnknowns'=>$incUnknowns);

		
		if ($scores && (!isset($this->settings->always_sort_by_initial) || (isset($this->settings->always_sort_by_initial) && $this->settings->always_sort_by_initial==0)))
		{
			usort($scores, array($this,'sortDataSet'));
		}

		$this->_scores=$scores;
    }

    private function getScores()
    {
		return $this->_scores;
    }
	
	/*
		DOES NOT WORK YET - WORK IN PROGRESS
	
		states within the same charachters expand the result set,
		selected states across characters restrict the result set
		example: (red OR black) AND round
		
		REFAC2015: finish!
		
	*/
    private function getScoresLiberal( $p )
    {
		$states=isset($p['states']) ? $p['states'] : null;
		$incUnknowns=isset($p['incUnknowns']) ? $p['incUnknowns'] : false;
		
		if (!isset($states))
			return;
		
        $s = $c = array();
        $stateCount = 0;
        
        // we have to find out which states we are looking for
        foreach ((array) $states as $sKey => $sVal) {
            $d = explode(':', $sVal);
            
            $charId = isset($d[1]) ? $d[1] : null;
            $value = isset($d[2]) ? $d[2] : null;

            // which is easy for the non-range characters...
            if ($d[0] != 'f') {
                
                if (isset($d[2]))
                    $s[$d[2]] = $d[2];
                $stateCount++;
            }
            // ...but requires calculation for the ranged ones
            else {

                // is there a standard dev?
                $sd = (isset($d[3]) ? $d[3] : null);
                
                // where-clause basics
                $d = array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'characteristic_id' => $charId
                );
				
				$value = str_replace(',','.',$value);

                // calculate the spread around the mean...
                if (isset($sd)) {

					$sd = str_replace(',','.',$sd);
                    
                    $d['mean >=#'] = '(' . strval(floatval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
                    $d['mean <=#'] = '(' . strval(floatval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';
                }
                // or mark just mark the upper and lower boundaries of the value
                else {
                    
                    $d['lower <='] = $d['upper >='] =  floatval($value);
                }
                
                // get any states that correspond with these values
                $cs = $this->models->CharacteristicState->_get(array(
                    'id' => $d
                ));

                // and store them
                foreach ((array) $cs as $key => $val)
                    $s[] = $val['id'];
                
                $stateCount++;
            }
            
            $c[$charId] = $charId;
        }
        
        if (empty($s))
            return;
        
        $n = $stateCount + ($incUnknowns ? 1 : 0);
        $s = implode(',', $s);
        $c = implode(',', $c);
        
        $q = "
        	select 'taxon' as type, _a.taxon_id as id, _b.state_id, _b.characteristic_id,
       				_c.is_hybrid as h, trim(_c.taxon) as l
        		from %PRE%matrices_taxa _a
        		left join %PRE%matrices_taxa_states _b
        			on _a.project_id = _b.project_id
        			and _a.matrix_id = _b.matrix_id
        			and _a.taxon_id = _b.taxon_id
        			and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
		        left join %PRE%taxa _c
			        on _a.taxon_id = _c.id
		        where _a.project_id = " . $this->getCurrentProjectId() . "
			        and _a.matrix_id = " . $this->getCurrentMatrixId() . "
        		group by _a.taxon_id
        	union all
        	select 'matrix' as type, _a.id as id, _b.state_id, _b.characteristic_id,
			        0 as h, trim(_c.name) as l
        		from  %PRE%matrices _a
        		join %PRE%matrices_taxa_states _b
        			on _a.project_id = _b.project_id
        			and _a.id = _b.ref_matrix_id
        			and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
		        left join %PRE%matrices_names _c
			        on _b.ref_matrix_id = _c.id
        			and _c.language_id = " . $this->getCurrentLanguageId() . "
        		where _a.project_id = " . $this->getCurrentProjectId() . "
        			and _b.matrix_id = " . $this->getCurrentMatrixId() . "
        		group by id" . ($this->models->MatrixVariation->getTableExists() ? "
			union all
			select 'variation' as type, _a.variation_id as id, _b.state_id, _b.characteristic_id,
				0 as h, trim(_c.label) as l
				from  %PRE%matrices_variations _a        		
				left join %PRE%matrices_taxa_states _b
					on _a.project_id = _b.project_id
					and _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
					and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
				left join %PRE%taxa_variations _c
					on _a.variation_id = _c.id
				where _a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				group by _a.variation_id" : "")."
			order by characteristic_id"
        ;

        $results = $this->models->MatrixTaxonState->freeQuery($q);

		$d = array();
		$prevChar = -99;
		foreach((array)$results as $val) {

			if (!isset($d[$val['id']]))
				$d[$val['id']] = $val;

			if (!isset($d[$val['id']]['s']))
				$d[$val['id']]['s'] = 0;
				
			if (!empty($val['state_id'])) {
				if ($val['state_id']==$prevChar)
					$d[$val['id']]['s']++;
				else
					$d[$val['id']]['s']++;
			}
			
			$prevChar = $val['characteristic_id'];

		}

        return $results;
    }

	/*
		each selected state further restricts the result set
		example: red AND black AND round
	*/
    private function getScoresRestrictive( $p )
    {
		$states=isset($p['states']) ? $p['states'] : null;
		$incUnknowns=isset($p['incUnknowns']) ? $p['incUnknowns'] : false;

		if (!isset($states))
			return;
		
        $s=$c=array();
        $stateCount = 0;
        
        // we have to find out which states we are looking for
        foreach ((array) $states as $sKey => $sVal)
		{
            $d = explode(':', $sVal['val']);
            
            $charId = isset($d[1]) ? $d[1] : null;
            $value = isset($d[2]) ? $d[2] : null;

            // which is easy for the non-range characters...
            if ($d[0] != 'f')
			{
                if (isset($d[2])) $s[$d[2]] = $d[2];
                $stateCount++;
            }
            else
            // ...but requires calculation for the ranged ones
			{

                // is there a standard dev?
                $sd = (isset($d[3]) ? $d[3] : null);
                
                // where-clause basics
                $d = array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'characteristic_id' => $charId
                );
				
				// normalising decimal separator
				$value = str_replace(',','.',$value);

                // if there is a sd, calculate the spread around the mean...
                if (isset($sd))
				{
					$sd = str_replace(',','.',$sd);
                    $d['mean >=#'] = '(' . strval(floatval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
                    $d['mean <=#'] = '(' . strval(floatval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';
                }
                // if there isn't, just mark the upper and lower boundaries of the value
                else
				{
                    $d['lower <='] = $d['upper >='] =  floatval($value);
                }
                
                // get any states that correspond with these values...
                $cs = $this->models->CharacteristicState->_get(array(
                    'id' => $d
                ));

                // ...and store them
                foreach ((array) $cs as $key => $val)
				{
                    $s[] = $val['id'];
				}
                
				// keep count of the number of states we're interested in
                $stateCount++;
            }
            
            $c[$charId] = $charId;
        }
        
        if (empty($s))
            return;
        
        $n = $stateCount;
        $si = implode(',', $s);
        $ci = implode(',', $c);

		// query to get all taxa, matrices and variations, including their matching percentage
        $q = "
        	select
				'taxon' as type,
				_a.taxon_id as id,
				count(_b.state_id) as matching_states,
				round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as score,
				trim(_c.taxon) as label

			from
				%PRE%matrices_taxa _a

			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _a.matrix_id = _b.matrix_id
				and _a.taxon_id = _b.taxon_id
				and _b.state_id in (" . $si . ")

			left join %PRE%taxa _c
				on _a.project_id = _c.project_id
				and _a.taxon_id = _c.id

			where
				_a.project_id = " . $this->getCurrentProjectId() . "
				and _a.matrix_id = " . $this->getCurrentMatrixId() . "

			group by _a.taxon_id

        	union all

        	select
				'matrix' as type,
				_a.id as id, 
				count(_b.state_id) as matching_states, 
				round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as score,
				trim(_c.name) as label

			from
				%PRE%matrices _a

			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _b.matrix_id = " . $this->getCurrentMatrixId() . "
				and _a.id = _b.ref_matrix_id
				and _b.state_id in (" . $si . ")

			left join %PRE%matrices_names _c
				on _a.project_id = _c.project_id
				and _a.id = _c.matrix_id
				and _c.language_id = " . $this->getCurrentLanguageId() . "

			where
				_a.project_id = " . $this->getCurrentProjectId() . "
				and _a.id != " . $this->getCurrentMatrixId() . "

			group by id" . 
			
			( $this->models->MatrixVariation->getTableExists() ? "

				union all
	
				select
					'variation' as type,
					_a.variation_id as id, 
					count(_b.state_id) as matching_states, 
					round((if(count(_b.state_id)>" . $n . "," . $n . ", count(_b.state_id))/" . $n . ")*100,0) as score,
					trim(_d.taxon) as label

				from
					%PRE%matrices_variations _a        		

				left join %PRE%matrices_taxa_states _b
					on _a.project_id = _b.project_id
					and _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
					and _b.state_id in (" . $si . ")

				left join %PRE%taxa_variations _c
					on _a.project_id = _c.project_id
					and _a.variation_id = _c.id

				left join %PRE%taxa _d
					on _a.project_id = _d.project_id
					and _c.taxon_id = _d.id

				where
					_a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "

				group by
					_a.variation_id" :  ""
			)
		;

        $results = $this->models->MatrixTaxonState->freeQuery($q);

		/*
			"unknowns" are taxa for which *no* state has been defined within a certain character.
			note that this is different froam having a *different* state within that character. if
			there is a character "colour", and taxon A has the state "green", taxon B has the 
			state "brown" and taxon C has no state for colour, then selecting "brown" with 'Treat 
			unknowns as matches' set to false will yield A:0%, B:100%, C:0%. selecting "brown" 
			with 'Treat unknowns as matches' set to true will yield A:0%, B:100%, C:100%.
			it can be seen as a 'rather safe than sorry' setting.
		*/
		if ($incUnknowns)
		{
			
			$unknowns=array('taxon'=>array(),'matrix'=>array(),'variation'=>array());
			
			foreach((array)$c as $character)
			{
				$q = "
					select
						'taxon' as type, 
						_a.taxon_id as id,
						trim(_c.taxon) as label
					from
						%PRE%matrices_taxa _a

					left join %PRE%taxa _c
						on _a.project_id = _c.project_id
						and _a.taxon_id = _c.id

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.taxon_id = _b.taxon_id
						and _b.characteristic_id =".$character."


					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "

					group by
						_a.taxon_id

					having
						count(_b.id)=0

				union all

					select
						'matrix' as type, 
						_a.id as id,
						trim(_c.name) as label
					from
						%PRE%matrices _a

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _b.matrix_id = " . $this->getCurrentMatrixId() . "
						and _a.id = _b.ref_matrix_id
						and _b.characteristic_id =".$character."

					left join %PRE%matrices_names _c
						on _a.project_id = _c.project_id
						and _a.id = _c.matrix_id
						and _c.language_id = " . $this->getCurrentLanguageId() . "

					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.id != " . $this->getCurrentMatrixId() . "

					group by
						_a.id
					having
						count(_b.id)=0

				".( $this->models->MatrixVariation->getTableExists() ? "
		
				union all

					select
						'variation' as type, 
						_a.variation_id as id,
						trim(_d.taxon) as label

					from
						%PRE%matrices_variations _a

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.variation_id = _b.variation_id
						and _b.characteristic_id =".$character."

					left join %PRE%taxa_variations _c
						on _a.project_id = _c.project_id
						and _a.variation_id = _c.id

					left join %PRE%taxa _d
						on _a.project_id = _d.project_id
						and _c.taxon_id = _d.id

					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "

					group by
						_a.variation_id

					having
						count(_b.id)=0
				" : "" );

		        $rrr=$this->models->MatrixTaxonState->freeQuery( $q );

				foreach((array)$rrr as $r)
				{
					switch($r['type'])
					{
						case 'taxon': 
							$unknowns['taxon'][$r['id']]=$r;
							isset($unknowns['taxon'][$r['id']]['matching_states']) ?
								$unknowns['taxon'][$r['id']]['matching_states']++ : 
								$unknowns['taxon'][$r['id']]['matching_states']=1;

							$unknowns['taxon'][$r['id']]['score'] = 
								round(($unknowns['taxon'][$r['id']]['matching_states']/$n)*100);
							break;

						case 'matrix':
							$unknowns['matrix'][$r['id']]=$r;
							isset($unknowns['matrix'][$r['id']]['matching_states']) ?
								$unknowns['matrix'][$r['id']]['matching_states']++ : 
								$unknowns['matrix'][$r['id']]['matching_states']=1;

							$unknowns['matrix'][$r['id']]['score'] = 
								round(($unknowns['matrix'][$r['id']]['matching_states']/$n)*100);
							break;

						case 'variation':
							$unknowns['variation'][$r['id']]=$r;
							isset($unknowns['variation'][$r['id']]['matching_states']) ?
								$unknowns['variation'][$r['id']]['matching_states']++ : 
								$unknowns['variation'][$r['id']]['matching_states']=1;

							$unknowns['variation'][$r['id']]['score'] = 
								round(($unknowns['variation'][$r['id']]['matching_states']/$n)*100);
							break;
					}
				}
			}

			foreach((array)$results as $key => $val)
			{
				if (isset($unknowns[$val['type']][$val['id']]))
				{
					$temp=$unknowns[$val['type']][$val['id']];
					$results[$key]['matching_states']+=$temp['matching_states'];
					$results[$key]['score']=round(($results[$key]['matching_states']/$n)*100);
					unset($unknowns[$val['type']][$val['id']]);
				}
			}
	
			foreach((array)$unknowns as $type)
			{
				foreach((array)$type as $key => $val)
				{
					array_push($results,$val);
				}
			}
		}

        return $results;
    }
	
    private function sortDataSet( $a,$b )
    {
		/*
			sorting strategies:
			
			* column name defined by setting 'initial_sort_column'
			* matching percentage (100 > 0)
			* taxon concept
			* label
		*/
		
		if ( !empty($this->settings->initial_sort_column) )
		{
			if (isset($a[$this->settings->initial_sort_column]) && isset($b[$this->settings->initial_sort_column]))
			{
		        if ($a[$this->settings->initial_sort_column]>$b[$this->settings->initial_sort_column]) return 1;
		        if ($a[$this->settings->initial_sort_column]<$b[$this->settings->initial_sort_column]) return -1;
			}
		}
		
		if (isset($a['score']) && isset($b['score']))
		{
			if ($a['score']<$b['score']) return 1;
			if ($a['score']>$b['score']) return -1;
		}

		if (isset($a['taxon']))
		{
			$aa=strtolower(is_array($a['taxon']) ? strip_tags($a['taxon']['taxon']) : strip_tags($a['taxon']));
		}
		else
		if (isset($a['label']))
		{
			$aa=strtolower(strip_tags($a['label']));
		}
		else
		{
			$aa=0;
		}

		if (isset($b['taxon']))
		{
			$bb=strtolower(is_array($b['taxon']) ? strip_tags($b['taxon']['taxon']) : strip_tags($b['taxon']));
		}
		else
		if (isset($b['label']))
		{
			$bb=strtolower(strip_tags($b['label']));
		}
		else
		{
			$bb=0;
		}

		if ($aa<$bb) return -1;
		if ($aa>$bb) return 1;
		return 0;
    }



    private function setRemainingStateCount( $p=null )
    {
        $char = isset($p['char']) ? $p['char'] : null;
        $groupByChar = isset($p['groupByChar']) ? $p['groupByChar'] : false;

   		$c=$this->makeRemainingCountClauses($p);

		$dT = $c['dT'];
		$fsT = $c['fsT'];
		$dV = $c['dV'];
		$fsV = $c['fsV'];
		$dM = $c['dM'];
		$fsM = $c['fsM'];	

		$s = array();

      
        /*
        find the number of taxon/state-connections that exist, grouped by state, but only for taxa that
        have the already selected states, unless no states have been selected at all, in which case we just
        return them all
        */
        
        $q = "
        	select sum(tot) as tot, state_id, characteristic_id 
        	from (
        		select count(distinct _a.taxon_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
	        		from %PRE%matrices_taxa_states _a
	        		" . (!empty($dT) ? $dT : "") . "
					" . (!empty($fsT) ?  $fsT : ""). "
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
						and _a.taxon_id not in
							(select taxon_id from %PRE%taxa_variations where project_id = " . $this->getCurrentProjectId() . ")
					group by _a.state_id
				union all
				select count(distinct _a.variation_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
					from %PRE%matrices_taxa_states _a
					" . (!empty($dV) ? $dV : "") . "
					" . (!empty($fsV) ? $fsV : "") . "
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					group by _a.state_id
				union all
				select count(distinct _a.ref_matrix_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
					from %PRE%matrices_taxa_states _a
					" . (!empty($dM) ? $dM : "") . "
					" . (!empty($fsM) ? $fsM : "") . "
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					group by _a.state_id


			) as q1 
			group by q1.state_id
			";

        $all=$this->models->MatrixTaxonState->freeQuery( $q );
		
        $results=array();
    
        foreach ((array)$all as $val)
		{
            if (!is_null($char) && $val['characteristic_id']!=$char) continue;
    
			if ($groupByChar)
			{
	            $results[$val['characteristic_id']]['states'][$val['state_id']] = intval($val['tot']);
	            $results[$val['characteristic_id']]['tot'] =
					(isset($all[$val['characteristic_id']]['tot']) ? $all[$val['characteristic_id']]['tot'] : 0) + intval($val['tot']);
			} 
			else
			{
	            $results[$val['state_id']] = intval($val['tot']);
			}
    
        }
		
        return $results;
    }

    private function getCharacterCounts()
    {
		$c=$this->makeRemainingCountClauses();

		$dT = $c['dT'];
		$fsT = $c['fsT'];
		$dV = $c['dV'];
		$fsV = $c['fsV'];
		$dM = $c['dM'];
		$fsM = $c['fsM'];

        $q = "
        	select
				sum(taxon_count) as taxon_count, 
				sum(distinct_state_count) as distinct_state_count, 
				characteristic_id 
        	from (
        		select
					_a.characteristic_id as characteristic_id,
					count(distinct _a.taxon_id) as taxon_count, 
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dT) ? $dT : "") . "
					" . (!empty($fsT) ?  $fsT : ""). "
				where
					_a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					and _a.taxon_id not in
						(select taxon_id from %PRE%taxa_variations where project_id = " . $this->getCurrentProjectId() . ")
				group by
					_a.characteristic_id
				
				union all

				select 
					_a.characteristic_id as characteristic_id,
					count(distinct _a.variation_id) as taxon_count, 
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dV) ? $dV : "") . "
					" . (!empty($fsV) ? $fsV : "") . "
				where
					_a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				group by
					_a.characteristic_id

				union all

				select 
					_a.characteristic_id as characteristic_id,
					count(distinct _a.ref_matrix_id) as taxon_count, 
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dM) ? $dM : "") . "
					" . (!empty($fsM) ? $fsM : "") . "
				where
					_a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				group by
					_a.characteristic_id

			) as q1 
			group by q1.characteristic_id
			";
			

        $all = $this->models->MatrixTaxonState->freeQuery($q);
		
		//q($this->models->MatrixTaxonState->q(),1);

        $results=array();
    
        foreach ((array)$all as $val)
		{
			$results[$val['characteristic_id']]=
				array('taxon_count'=>$val['taxon_count'],'distinct_state_count'=>$val['distinct_state_count']);
        }

        return $results;
    }

    private function makeRemainingCountClauses()
    {
        $states=$this->getSessionStates();

        $dT = $dV = $dM = '';
        $i = 0;
		$s = array();

        if (!empty($states))
		{
            // we want all taxa/variations that have the already selected states so we create a list of unique state id's of those states
            foreach ((array) $states as $val)
			{
                if ($val['type'] != 'f')
				{
                    $dT .= "right join %PRE%matrices_taxa_states _c" . $i . "
						on _a.taxon_id = _c" . $i . ".taxon_id
						and _a.matrix_id = _c" . $i . ".matrix_id
						and _c" . $i . ".state_id  = " . $val['id'] . "
						and _a.project_id = _c" . $i++ . ".project_id
						and (_a.taxon_id is not null or _a.variation_id is null or _a.ref_matrix_id is null)
						";

                }
				else
				{
					$d = explode(':',$val['val']);
					$value = $val['value'];

					$sd = (isset($d[3]) ? $d[3] : null);
					
					$d = array(
						'project_id' => $this->getCurrentProjectId(), 
						'characteristic_id' => $val['characteristic_id']
					);
					
					if (isset($sd))
					{
						$d['mean >=#'] = '(' . strval(intval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
						$d['mean <=#'] = '(' . strval(intval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';

					} 
					else
					{
						$d['lower <='] = $d['upper >='] = intval($value);
					}					

					$cs = $this->models->CharacteristicState->_get(array('id' => $d));

					foreach ((array) $cs as $key => $cVal)
					{
						$s[] = $cVal['id'];
					}
				}
            }
        }
		
        if ($s)
		{
			$s = implode(',', $s);

			$fsT = "right join %PRE%matrices_taxa_states _s
					on _s.project_id = _a.project_id 
					and _s.matrix_id = _a.matrix_id
					and _s.taxon_id = _a.taxon_id 
					and _s.taxon_id is not null
					and _s.state_id in (" . $s . ")";

			$fsV = str_replace('variation_id is null', 'taxon_id is null', str_replace('taxon_id', 'variation_id', $fsT));
		}

        if (!empty($dT))
		{
            $dV = str_replace('variation_id is null', 'taxon_id is null', str_replace('taxon_id', 'variation_id', $dT));
            $dM = str_replace('ref_matrix_id is null', 'variation_id is null', str_replace('taxon_id', 'ref_matrix_id', $dT));
		}
		
		return
			array(
				'dT' => isset($dT) ? $dT : null,
				'fsT' => isset($fsT) ? $fsT : null,
				'dV' => isset($dV) ? $dV : null,
				'fsV' => isset($fsV) ? $fsV : null,
				'dM' => isset($dM) ? $dM : null,
				'fsM' => isset($fsM) ? $fsM : null
			);
						
	}



	private function getRelatedEntityCount( $p )
	{
		
		$id=isset($p['id']) ? $p['id'] : null;
		$type=isset($p['type']) ? $p['type'] : null;
	
		if ( is_null($id) || is_null($type) ) return;
		
		if ($type=='taxon')
		{
			$rel = $this->models->TaxaRelations->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'taxon_id' => $id
				)
			));
			
			return count((array)$rel);
			
		}
		else 
		if ($type=='variation')
		{
			
			$rel = $this->models->VariationRelations->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'variation_id' => $id
				)
			));
			
			return count((array)$rel);
			

		}
	}

    private function setRelatedEntities( $p )
    {
        $id = isset($p['id']) ? $p['id'] : null;
        $type = isset($p['type']) ? $p['type'] : null;
        $inclSelf = isset($p['inclSelf']) ? $p['inclSelf'] : true;

        if (!isset($type) || !isset($id))
            return;
        
        if ($type=='variation')
		{
			$this->_related=$this->models->VariationRelations->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'variation_id' => $id
				),
				'columns'=>'relation_id,ref_type'
			));
			
        }
        else
		if ($type=='taxon')
		{
			$this->_related=$this->models->TaxaRelations->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'taxon_id' => $id
				),
				'columns'=>'relation_id,ref_type'
			));
        }

		if ($inclSelf) array_unshift($this->_related,array('relation_id'=>$id,'ref_type'=>$type));

    }

	private function getRelatedEntities()
	{
		return $this->_related;
	}
	
	

    private function setSearchTerm( $p )
    {
		$search=isset($p['search']) ? $p['search'] : null;
		
		if (is_null($search))
			return;
		
		$this->_searchTerm=$search;
    }

    private function getSearchTerm()
    {
		return $this->_searchTerm;
    }

	private function setSearchResults()
    {
		$search=$this->getSearchTerm();
		
		if (empty($search))
			return;
		
		$search=mysql_real_escape_string(strtolower($search));

		// n.b. don't change to 'union all'
        $q = "
			select * from (
				select
					'variation' as type,
					_a.variation_id as id,
					trim(_c.label) as label,
					_c.taxon_id as taxon_id,
					_d.taxon as taxon, 
					null as commonname
	
				from 
					%PRE%matrices_variations _a        		
	
				left join %PRE%matrices_taxa_states _b
					on _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
					and _b.project_id = " . $this->getCurrentProjectId() . "
	
				left join %PRE%taxa_variations _c
					on _a.variation_id = _c.id
					and _c.project_id = " . $this->getCurrentProjectId() . "
	
				left join %PRE%taxa _d
					on _c.taxon_id = _d.id						
					and _d.project_id = " . $this->getCurrentProjectId() . "
	
				where _a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					and (lower(_c.label) like '%". $search ."%' or lower(_d.taxon) like '%". $search ."%')
	
				union
	
				select 
					'taxon' as type,
					_a.taxon_id as id, 
					trim(_c.taxon) as label, 
					_a.taxon_id as taxon_id,
					_c.taxon as taxon, 
					_d.commonname as commonname
	
				from
					%PRE%matrices_taxa _a
	
				left join %PRE%matrices_taxa_states _b
					on _a.matrix_id = _b.matrix_id
					and _a.taxon_id = _b.taxon_id
					and _b.project_id = " . $this->getCurrentProjectId() . "
	
				left join %PRE%taxa _c
					on _a.taxon_id = _c.id
					and _c.project_id = " . $this->getCurrentProjectId() . "
	
				left join %PRE%commonnames _d
					on _a.taxon_id = _d.taxon_id
					and _d.language_id = ".$this->getCurrentLanguageId() ." 
					and _d.project_id = " . $this->getCurrentProjectId() . "
	
				where _a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					and (lower(_c.taxon) like '%". $search ."%' or lower(_d.commonname) like '%". $search ."%')
			) as unionized
			order by label
			";

        $this->_searchResults = $this->models->MatrixTaxonState->freeQuery( $q );
		
    }

	private function getSearchResults()
    {
		return $this->_searchResults;
    }
	
	private function getModuleSetting( $p )
	{
		return $this->moduleSettings->getModuleSetting( $p );
	}

	private function setIntroductionLinks()
    {
		$a=$this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'topic' => $this->settings->introduction_topic_colophon_citation
				),
				'columns'=>'page_id,topic,content'
			)
		);

		$b=$this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'topic' => $this->settings->introduction_topic_versions
				),
				'columns'=>'page_id,topic,content'
			)
		);

		$c=$this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'topic' => $this->settings->introduction_topic_inline_info
				),
				'columns'=>'page_id,topic,content'
			)
		);

		$content_a=strip_tags($a[0]['content']);
		$content_b=strip_tags($b[0]['content']);
		$content_c=strip_tags($c[0]['content']);

		$this->_introductionLinks=array(
			$this->settings->introduction_topic_colophon_citation=>$a && (!empty($content_a)) ? $a[0] : null,
			$this->settings->introduction_topic_versions=>$b && (!empty($content_b)) ? $b[0] : null,
			$this->settings->introduction_topic_inline_info=>$c && (!empty($content_c)) ? $c[0] : null,
		);
    }
	
	private function getIntroductionLinks()
	{
		return $this->_introductionLinks;
	}
	
	private function setIncUnknowns( $state )
	{
		if ( is_bool($state) )
		{
			$this->_incUnknowns=$state;
		}
	}
	
	private function getIncUnknowns()
	{
		return $this->_incUnknowns;
	}
	
	


}	
