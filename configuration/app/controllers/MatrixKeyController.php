<?php
/*

	?mtrx=i --> override for specific matrix --> is this actually in use

	needs to be undone of NBC:
		private $_nbcImageRoot=true;

	vergeet matrixen als resultaat niet!

	alleen in ajax:
		// if ($this->rGetVal( 'key' )) setmatrix
		// if ($this->rGetVal( 'p' )) setproject


	!!! handle in JS (= only show species details that are not the same for all remaining species)
	if ($this->_matrixSuppressDetails!=true && count((array)$res)!=0)
		$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));
		
		
	getRemainingCharacterCount  --> is it actually in use??

*/


include_once ('Controller.php');
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
		'gui_menu_order'
    );
	
	private $_totalEntityCount=0;
	private $_activeMatrix=null;

//	private $_characters=null;
	private $_dataSet=null;
	private $_menu=null;
	private $_scores=null;
	private $_related=null;
	private $_searchTerm=null;
	private $_searchResults=null;

	private $_matrix_calc_char_h_val=true;
	private $_matrix_allow_empty_species=true;
	private $_matrix_use_emerging_characters=true;
	private $_matrix_browse_style;
	private $_matrix_state_image_per_row;
	private $_matrix_score_threshold;

		private $_nbcImageRoot=true;
	
	
    public $cssToLoad = array('matrix.css');

    public $jsToLoad = array(
        'all' => array(
            'main.js', 
//            'matrix.js', 
//            'prettyPhoto/jquery.prettyPhoto.js', 
//            'dialog/jquery.modaldialog.js'
        ), 
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


    private function initialize ($force = false)
    {
		echo '1:',$this->getCurrentMatrixId(),'<br />';
		$this->initializeMatrixId();
		echo '2:',$this->getCurrentMatrixId(),'<br />';
		$this->setActiveMatrix();
		echo '3:',$this->getCurrentMatrixId(),'<br />';

		if (is_null($this->getCurrentMatrixId()))
		{
			$this->printGenericError($this->translate('No matrices have been defined.'));
		}

		$this->_matrix_calc_char_h_val=$this->getSetting('matrix_calc_char_h_val',true); // calculate characters H-value? enhances performance
		$this->_matrix_allow_empty_species=$this->getSetting('matrix_allow_empty_species',true);
		$this->_matrix_use_emerging_characters = $this->getSetting('matrix_use_emerging_characters',true);
		$this->_matrix_browse_style=$this->getSetting('matrix_browse_style','paginate');
		$this->_matrix_state_image_per_row=$this->getSetting('matrix_state_image_per_row',4);
		$this->_matrix_score_threshold=$this->getSetting('matrix_score_threshold',100);

		$this->_nbcImageRoot = $this->getSetting('nbc_image_root');

		$this->setMenu();

//			$_SESSION['app']['system']['urls']['nbcImageRoot']=

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


        if ($this->_matrixType == 'nbc') {
        }
		*/
    }

    public function indexAction()
    {
		$matrix=$this->getActivematrix();

        $this->setPageName(sprintf($this->translate('Matrix "%s": identify'), $matrix['name']));
		
		$this->setScores();

		$this->smarty->assign('session_scores',json_encode( $this->getScores() ));
		$this->smarty->assign('session_states',json_encode( $this->getSessionStates() ));
        $this->smarty->assign('matrix', $matrix);
		$this->smarty->assign('nbcImageRoot', $this->_nbcImageRoot);
		$this->smarty->assign('matrix_use_emerging_characters', $this->_matrix_use_emerging_characters);
		$this->smarty->assign('matrix_browse_style', $this->_matrix_browse_style);
		$this->smarty->assign('matrix_score_threshold', $this->_matrix_score_threshold);
			
        $this->printPage();
    }

    public function identifyAction()
    {
		// backward compatibility
		$this->redirect( str_replace('identify.php','index.php',$_SERVER["REQUEST_URI"]));
	}
	
    public function ajaxInterfaceAction ()
    {
		if ($this->rHasVar('key'))
		{
			$this->setCurrentMatrixId($this->rGetVal('key'));
		}
	
		if ($this->rHasVal('action', 'get_menu'))
		{
			$this->smarty->assign('returnText', json_encode($this->getMenu()));
        }	
		
		else
		
		if ($this->rHasVal('action', 'get_dataset'))
		{
			$this->setDataSet();
			$this->smarty->assign('returnText', json_encode($this->getDataSet()));
        }	
		
		else
		
		if ($this->rHasVal('action', 'get_character_states'))
		{
			$character=$this->getCharacter(array('id'=>$this->rGetVal( 'id' )));
			$states=$this->getCharacterStates(array('char'=>$this->rGetVal( 'id' )));

			$this->smarty->assign('character', $character);
			$this->smarty->assign('states', $states);
			$this->smarty->assign('states_selected', $this->getSessionStates( array('char'=>$this->rGetVal( 'id' ),'reindex'=>true)));
			$this->smarty->assign('states_remain_count', $this->setRemainingStateCount(array('char'=>$this->rGetVal( 'id' ))));
            $this->smarty->assign('state_images_per_row', $this->_matrix_state_image_per_row);

            $this->smarty->assign('returnText', 
				json_encode(
					array(
						'title'=>$character['label'],
						'page'=>$this->fetchPage('formatted_states'),
						'showOk'=>($character['type'] == 'media' || $character['type'] == 'text' ? false : true)
					)));
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
			$this->smarty->assign('returnText',json_encode( array('scores'=>$this->getScores(),'states'=>$this->getSessionStates())));
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
			$this->smarty->assign('returnText',json_encode( array('scores'=>$this->getScores(),'states'=>$this->getSessionStates())));
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

		$this->printPage();	
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

	private function setActiveMatrix()
	{
		$this->_activeMatrix=$this->getMatrix( array('id'=>$this->getCurrentMatrixId()) );
		$this->_activeMatrix=$this->_activeMatrix[$this->getCurrentMatrixId()];
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
		
		$this->setCurrentMatrixId( $m['id'] );
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
					" . ( isset($id) ? "and _a.id = " . $id : "" ) . "
				order by
					_a.default desc
		",
		"fieldAsIndex" => "id"
		));
		
		return $m;

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

        return (isset($id) && isset($cs[0]) ? $cs[0] : $cs);
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
	
	private function getAllIdentifiableEntities()
	{
		$taxa=$this->getTaxaInMatrix();
		$var=$this->getVariationsInMatrix();
		$info=$this->getAllNBCExtras();

		if ( !empty($info) )
		{
			foreach((array)$taxa as $key=>$val)
			{
				$taxa[$key]['info']=isset($info['taxon'][$val['id']]) ? $info['taxon'][$val['id']] : null;
			}
			foreach((array)$var as $key=>$val)
			{
				$var[$key]['info']=isset($info['variation'][$val['id']]) ? $info['variation'][$val['id']] : null;
			}
		}

		$all=array_merge((array)$taxa,(array)$var);
		
		usort($all,function($a,$b)
		{
			$aa=$bb='';

			if ($a['type']=='taxon')
				$aa=$a['taxon'];
			else
			if ($a['type']=='variation')
				$aa=$a['taxon']['taxon'];

			if ($b['type']=='taxon')
				$bb=$b['taxon'];
			else
			if ($b['type']=='variation')
				$bb=$b['taxon']['taxon'];
				

			return ($aa==$bb ? 0 : ($aa>$bb ? 1 : -1 ));

		});
		
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
			
			if ($this->_matrix_allow_empty_species || (!$this->_matrix_allow_empty_species && $val['is_empty']==1))
			{
				$d['type']='taxon';
				$d['states']=$this->getTaxonStates( $val['taxon_id'] );
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
				order by _c.label",

            'fieldAsIndex' => 'variation_id'
        ));

        foreach ( (array)$m as $key=>$val )
		{
			$m[$key]['taxon']=$this->getTaxonById( $val['taxon_id'] );
			$m[$key]['gender']=$this->extractGenderTag( $val['label'] );
			$m[$key]['states']=$this->getVariationStates( $val['id'] );
			$m[$key]['related_count']=$this->getRelatedEntityCount( array('id'=>$val['id'],'type'=>'variation') );
        }

        return $m;
    }

	//REFAC2015: should be moved to kenmerkenmodule!
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
				_e.label
				
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



	private function setMenu()
	{
		$menu=$this->models->GuiMenuOrder->freeQuery("
			select
				ref_id as id,
				ref_type as type,
				label
			from (
				select
					_a.ref_id,
					_a.ref_type,
					_a.show_order,
					ifnull(_c.label,_b.label) as label
					
				from  %PRE%gui_menu_order _a
				
				left join %PRE%chargroups _b
					on _a.project_id = _b.project_id
					and _a.ref_id = _b.id

				left join %PRE%chargroups_labels _c
					on _b.project_id = _c.project_id
					and _b.id = _c.chargroup_id
					and _c.language_id = ". $this->getCurrentLanguageId()."

				where 
					_a.project_id = " . $this->getCurrentProjectId() . " 
					and _a.matrix_id = " . $this->getCurrentMatrixId() ." 
					and _a.ref_type = 'group'
					
				union
	
				select
					_a.ref_id,
					_a.ref_type,
					_a.show_order,
					_c.label
					
				from  %PRE%gui_menu_order _a

				left join %PRE%characteristics _b
					on _a.project_id = _b.project_id
					and _a.ref_id = _b.id

				left join %PRE%characteristics_labels _c
					on _b.project_id = _c.project_id
					and _b.id = _c.characteristic_id
					and _c.language_id = ". $this->getCurrentLanguageId()."

				where 
					_a.project_id = " . $this->getCurrentProjectId() . " 
					and _a.matrix_id = " . $this->getCurrentMatrixId() ." 
					and _a.ref_type = 'char'

			) as joined
			order by 
				show_order
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
				//$menu[$key]['states']=$this->getCharacterStates( array('char'=>$val['id']) );
			}
		}
		
		$this->_menu=$menu;	
	}

	private function getMenu()
	{
		return $this->_menu;
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
			$char['label'] = isset($d[0]) ? $d[0] : null;
			$char['info'] = isset($d[1]) ? $d[1] : null;
			$char['unit'] = isset($d[2]) ? $d[2] : null;
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
		$scores=$this->getScoresRestrictive( array('states'=>$this->getSessionStates()) );
		//$scores = $this->getScoresLiberal( array('states'=>$this->getSessionStates(),'incUnknowns'=>$incUnknowns);
		if ($scores) usort($scores, array($this,'sortMatchesByScoreThenLabel'));
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
	
    private function sortMatchesByScoreThenLabel( $a,$b )
    {
        if ($a['score'] == $b['score'])
		{
			$aa = strtolower(strip_tags($a['label']));
			$bb = strtolower(strip_tags($b['label']));

            if ($aa==$bb) return 0;

            return ($aa<$bb) ? -1 : 1;
        }

        return ($a['score']>$b['score']) ? -1 : 1;
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

    private function getRemainingCharacterCount()
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

}	