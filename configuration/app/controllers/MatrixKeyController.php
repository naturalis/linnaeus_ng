<?php
/*

	?mtrx=i --> override for specific matrix --> is this actually in use

	needs to be undone of NBC:
		private $_nbcImageRoot=true;

	vergeet matrixen als resultaat niet!

	alleen in ajax:
		// if ($this->rGetVal( 'key' )) setmatrix
		// if ($this->rGetVal( 'p' )) setproject


	???
	{$prevRangeValue}

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

	private $_matrix_calc_char_h_val=true;
	private $_matrix_allow_empty_species=true;
	private $_matrix_use_emerging_characters=true;
	private $_matrix_browse_style;
	private $_matrix_state_image_per_row;
	
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
		$this->initializeMatrixId();
		$this->setActiveMatrix();
		
		if (is_null($this->getCurrentMatrixId()))
		{
			$this->printGenericError($this->translate('No matrices have been defined.'));
		}

		$this->_matrix_calc_char_h_val=$this->getSetting('matrix_calc_char_h_val',true); // calculate characters H-value? enhances performance
		$this->_matrix_allow_empty_species=$this->getSetting('matrix_allow_empty_species',true);
		$this->_matrix_use_emerging_characters = $this->getSetting('matrix_use_emerging_characters',true);
		$this->_matrix_browse_style=$this->getSetting('matrix_browse_style','paginate');
		$this->_matrix_state_image_per_row=$this->getSetting('matrix_state_image_per_row',4);
		

		$this->_nbcImageRoot = $this->getSetting('nbc_image_root');


		$this->setTotalEntityCount();
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


		$this->fuck();die();


        $this->smarty->assign('matrix', $matrix);
		$this->smarty->assign('nbcImageRoot', $this->_nbcImageRoot);
		$this->smarty->assign('matrix_use_emerging_characters', $this->_matrix_use_emerging_characters);
		$this->smarty->assign('matrix_browse_style', $this->_matrix_browse_style);
			
        $this->printPage();
    }

    public function identifyAction()
    {
		// backward compatibility
		$this->redirect( str_replace('identify.php','index.php',$_SERVER["REQUEST_URI"]));
	}
	
    public function ajaxInterfaceAction ()
    {
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
		
		if ($this->rHasVal('action', 'get_states'))
		{
			$character=$this->getCharacter(array('id'=>$this->rGetVal( 'id' )));
			$states=$this->getCharacterStates(array('id'=>$this->rGetVal( 'id' )));

			$this->smarty->assign('character', $character);
			$this->smarty->assign('states', $states);
            $this->smarty->assign('stateImagesPerRow', $this->_matrix_state_image_per_row);

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
		}

				
//			$this->smarty->assign('returnText', json_encode($this->getDataSet()));



/*

            $c['prefix'] = ($c['type'] == 'media' || $c['type'] == 'text' ? 'c' : 'f');
            
            $s = $this->getCharacteristicStates($this->requestData['id']);
            $s = $this->fixStateLabels($s); // temporary measure (hopefully!)

            $states = $this->stateMemoryRecall();

            $countPerState = $this->getRemainingStateCount(array(
                'charId' => $this->requestData['id'], 
                'states' => $states
            ));
            $this->smarty->assign('remainingStateCount', $countPerState);

            $states = $this->nbcStateMemoryReformat($states);
		
            $this->smarty->assign('c', $c);
            $this->smarty->assign('s', $s);
            $this->smarty->assign('states', $states);

            $this->smarty->assign('returnText', 
				json_encode(
				array(
					'character' => $c,
					'page' => 
					$this->fetchPage('formatted_states'),
					'showOk' => ($c['type'] == 'media' || $c['type'] == 'text' ? false : true)
				)));

        
					
		}
		
		
		
		
		
		
		
		if ($this->rHasVal('action', 'get_menu'))
		{
			$this->setMenu();
			$this->smarty->assign('returnText', json_encode($this->getMenu()));
			
									
			
			
//			$includeGroups = isset($this->requestData['params']['noGroups']) ? $this->requestData['params']['noGroups']!='1' : true;
//			$includeActiveChars = isset($this->requestData['params']['noActiveChars']) ? $this->requestData['params']['noActiveChars']!='1' : true;



//            $states = $this->stateMemoryRecall();


// 			if (isset($this->requestData['params']['action']) && $this->requestData['params']['action'] == 'similar')                
//                $results = $this->nbcGetSimilar($this->requestData['params']);
//            else                
//                $results = $this->nbcGetTaxaScores($states);
		
			if ($includeActiveChars) {

				$d = array();
	
				foreach ((array) $states as $val)
					$d[$val['characteristic_id']] = true;
					
			} else {
				
				$d=null;
				
			}

			array_walk($results, create_function('&$v,$k', '$v["l"] = ucfirst($v["l"]);'));

			$countPerState = $this->getRemainingStateCount(array(
				'states' => $states
			));
			
			$countPerCharacter = $this->getRemainingCharacterCount();
			
			if ($includeGroups) {
			
				$groups = $this->getGUIMenu(
					array(
						'groups' => $this->getCharacterGroups(),
						'characters' => $this->getCharacteristics(),
						'appendExcluded' => false,
						'checkImages' => true
					)
				);
	
				if (empty($groups))
					$groups = $this->getCharacterGroups();
					
			} else {
				
				$groups=null;
				
			}

			$result = 
				json_encode(
					array(
						'paramCount' => count((array)$states),
						'count' => array(
							'results' => count((array)$results)
						),
						'menu' => array(
							'groups' => $groups,
							'activeChars' => $d,
							'storedStates' => $this->stateMemoryRecall()
						),
						'countPerState' => $countPerState,
						'countPerCharacter' => $countPerCharacter,
						'selectedStates' => $states,
						'matrix' => $this->getCurrentMatrixId()	
					)
				);
			
			$this->smarty->assign('returnText', $result	);
		}
		*/	
        
		

		
		$this->printPage();	
	}
	






    public function identifsayAction()
    {
        if ($this->_matrixType == 'nbc')
		{
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


            $this->smarty->assign('guiMenu',$menu);

			if ($this->_useSepCoeffAsWeight)
				$this->smarty->assign('coefficients', $this->getRelevantCoefficients($states));

            $this->smarty->assign('nbcFullDatasetCount', $_SESSION['app'][$this->spid()]['matrix'][$this->getCurrentMatrixId()]['totalEntityCount']);
            $this->smarty->assign('nbcStart', $this->getSessionSetting('nbcStart'));
            $this->smarty->assign('nbcSimilar', $this->getSessionSetting('nbcSimilar'));
			$this->smarty->assign('nbcPerLine', $this->getSetting('matrix_items_per_line'));
			$this->smarty->assign('nbcPerPage', $this->getSetting('matrix_items_per_page'));
			$this->smarty->assign('settings.browseStyle', $this->getSetting('matrix_browse_style'));
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
            $this->smarty->assign('storedStates', $this->getSessionStates());
            $this->smarty->assign('storedShowState', $this->showStateRecall());
        }

        if (isset($taxa))
			$this->smarty->assign('taxa', $taxa);

        $this->smarty->assign('projectId', $this->getCurrentProjectId());
        $this->smarty->assign('function', 'Identify');
        $this->smarty->assign('characteristics', $characters);

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

	private function setTotalEntityCount()
	{
		if ( is_null($this->getCurrentMatrixId()) )
		{
			return;
		}
		
		$q = "
			select
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

        //if ($this->_matrixType == 'nbc')
		if ( $this->models->MatrixVariation->getTableExists() )
		{
			$q .=  "
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

		$results = $this->models->MatrixTaxonState->freeQuery( $q );

		$this->_totalEntityCount = $results[0]['tot']+(isset($results[1]['tot']) ? $results[1]['tot'] : 0);
		
	}

	private function getTotalEntityCount()
	{
		return $this->_totalEntityCount;
		
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
			


/*
    private function setCharacters()
    {
        $characters=$this->models->CharacteristicMatrix->freeQuery("
			select
				_a.characteristic_id as id,
				_b.type,
				_c.label,
				if (_b.type='media'||_b.type='text','c','f') as prefix,
				_a.show_order,
				if(locate('|',_c.label)=0,_c.label,substring(_c.label,1,locate('|',_c.label)-1)) as label_short,
				if(locate('|',_c.label)=0,_c.label,substring(_c.label,locate('|',_c.label)+1)) as description

			from 
				%PRE%characteristics_matrices _a
				
			right join %PRE%characteristics _b
				on _a.project_id = _b.project_id
				and _a.characteristic_id = _b.id

			left join %PRE%characteristics_labels _c
				on _a.project_id=_c.project_id
				and _a.characteristic_id=_c.characteristic_id
				and _c.language_id=".$this->getCurrentLanguageId()."
				
			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.matrix_id = ".$this->getCurrentMatrixId()." 
			order by 
				_a.show_order
		");
		
		$states=array();

		foreach((array)$this->getCharacterStates( array('id'=>'*') ) as $val)
		{
			$states[$val['characteristic_id']][]=$val;
		}

        foreach ((array) $characters as $key => $val)
		{
			$characters[$key]['states']=$states[$val['id']];

			$characters[$key]['sort_by']=
				array(
					'alphabet' =>
						isset($val['label']) ? strtolower(preg_replace('/[^A-Za-z0-9]/', '', $val['label'])) : '',
					'characterType' =>
						strtolower(preg_replace('/[^A-Za-z0-9]/', '', $val['type'])),
					'numberOfStates' =>
						-1 * count((array)$characters[$key]['states']), // -1 to avoid asc/desc hassles in JS-sorting
					'entryOrder' =>
						intval($val['show_order']),
					'separationCoefficient' => 
						$this->_matrix_calc_char_h_val ?
							-1 * $this->getCharacteristicHValue( array('id'=>$val['id'],'states'=>$states[$val['id']]) )  : 
							null
				);
        }
		
        $this->_characters=isset($characters) ? $characters : null;

    }

    private function getCharacters()
    {
        return $this->_characters;
    }
*/
    private function getCharacterStates( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		
		if (is_null($id))
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
				".($id=='*' ? "" : "and _a.characteristic_id=".$id)." 

			order by 
				_a.show_order
			"
		);
		
        foreach ((array) $cs as $key => $val)
		{
            $cs[$key]['img_dimensions']=explode(':',$val['file_dimensions']);
		}

        return $cs;
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
				
		$all=array_merge($taxa,$var);
		
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


private function getRelatedEntities( $p )
{
	
	$id=isset($p['id']) ? $p['id'] : null;
	$type=isset($p['type']) ? $p['type'] : null;
	$includeSelf=isset($p['includeSelf']) ? $p['includeSelf'] : false;

	if ( is_null($id) || is_null($type) ) return;
	
	if ($type=='taxon')
	{
		$rel = $this->models->TaxaRelations->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'taxon_id' => $id
			)
		));
		
		if ($includeSelf && isset($id))
		{
			array_unshift($rel, array(
				'id' => $id, 
				'relation_id' => $id, 
				'ref_type' => 'taxon'
			));
		}
		
		foreach ((array) $rel as $key => $val)
		{
			if ($val['ref_type'] == 'taxon')
			{
				$rel[$key]['label'] = $this->formatTaxon($this->getTaxonById($val['relation_id']));
			}
			else
			{
				$d = $this->getVariation($val['relation_id']);
				$rel[$key]['label'] = $d['label'];
				$rel[$key]['taxon_id'] = $d['taxon_id'];
			}
		}
	}
	else if ($vId) {
		
		$rel = $this->models->VariationRelations->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'variation_id' => $vId
			)
		));
		
		if ($includeSelf && isset($vId))
			array_unshift($rel, array(
				'id' => $vId, 
				'relation_id' => $vId, 
				'ref_type' => 'variation'
			));
		

		foreach ((array) $rel as $key => $val) {
			
			if ($val['ref_type'] == 'taxon') {
				$rel[$key]['label'] = $this->formatTaxon($d = $this->getTaxonById($val['relation_id'])); // is that even legal, semantically?
			}
			else {
				$d = $this->getVariation($val['relation_id']);
				$rel[$key]['label'] = $d['label'];
			}
		}
	}
	
	return $rel;
}
/*
	private function getGUIMenu($p=null)
	{

        $g = isset($p['groups']) ? $p['groups'] : null;
        $c = isset($p['characters']) ? $p['characters'] : null;

		// need groups or isolated characters, otherwise nothing to show
		if (is_null($g) || is_null($c))
			return;
			
		// check if images actually exist
        $checkImages = isset($p['checkImages']) ? $p['checkImages'] : false;
		// append characters that do not appear in the menu order
        $appendExcluded = (isset($p['appendExcluded']) ? $p['appendExcluded'] : false);
		
		if ($checkImages) {

			foreach((array)$c as $key => $val) {
				if (isset($val['states'])) {
					foreach((array)$val['states'] as $sKey => $sVal) {
						$c[$key]['states'][$sKey]['file_exists'] = 
							file_exists($this->getProjectUrl('projectMedia').$sVal["file_name"]);
					}
				}
			}

		}
		
		foreach ((array) $c as $val)
			$dummy[$val['id']] = $val;

		$c = $dummy;

		$m = $this->models->GuiMenuOrder->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $this->getCurrentMatrixId(), 
			),
			'columns' => 'ref_id,ref_type,show_order',
			'order' => 'show_order'
		));

		if (!$m) {
			$i=0;
			foreach((array)$g as $val)
				$m[] = array('ref_id'=>$val['id'],'ref_type'=>'group','show_order'=>$i++);

			$dummy = $this->models->Characteristic->freeQuery("
				select _a.id 
				from %PRE%characteristics _a
				left join %PRE%characteristics_chargroups _b
					on _a.id = _b.characteristic_id
				left join %PRE%characteristics_matrices _c
					on _a.id = _c.characteristic_id
				where _a.project_id = ".$this->getCurrentProjectId()."  
				and _b.id is null 
				and _c.matrix_id = ".$this->getCurrentMatrixId()
			);

			foreach((array)$dummy as $val)
				$m[] = array('ref_id'=>$val['id'],'ref_type'=>'char','show_order'=>$i++);
			
		}

		$d=array();
		$i=0;		

		foreach((array)$m as $val) {
			if ($val['ref_type']=='group') {
				foreach((array)$g[$val['ref_id']]['chars'] as $ckey => $char)
					$g[$val['ref_id']]['chars'][$ckey]['states'] = $c[$char['id']]['states'];
				$d[$i] = $g[$val['ref_id']];
				unset($g[$val['ref_id']]);
			} else {
				$d[$i] = $c[$val['ref_id']];
				unset($c[$val['ref_id']]);
			}
			$d[$i]['icon'] = '__menu'.preg_replace('/\W/','',ucwords($d[$i]['label'])).'.png';
			if ($checkImages)
				$d[$i]['icon_exists'] = file_exists($this->getProjectUrl('projectMedia').$d[$i]['icon']);
			$d[$i]['type'] = $val['ref_type'];
			$i++;
		}

		if ($appendExcluded) {

			// append the items for some reason missing from the menu-order
			foreach((array)$c as $val)
				$d[] = $val;
		
			foreach((array)$g as $val)
				$d[] = $val;

		}

		return $d;
		
	}

*/

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
				//$menu[$key]['states']=$this->getCharacterStates( array('id'=>$val['id']) );
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
            unset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$id]);
		}
    }

    private function getSessionStates( $p=null )
    {
        if (isset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()]))
		{
            $charId = isset($p['charId']) ? $p['charId'] : null;
            $states = array();
            
            foreach ((array) $_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()] as $key => $val)
			{
                $states[$key]['val'] = $val;
                
                $d = explode(':', $val);
                
                if ($d[0] == 'c' && isset($d[2]))
				{
                    $d = $this->getCharacteristicState($d[2]);
                    $states[$key]['type'] = 'c';
                    $states[$key]['id'] = $d['id'];
                    $states[$key]['characteristic_id'] = $d['characteristic_id'];
                    $states[$key]['label'] = $d['label'];
                }
                else
				if ($d[0] == 'f' && isset($d[2]))
				{
                    $states[$key]['type'] = 'f';
                    $states[$key]['characteristic_id'] = $d[1];
                    $states[$key]['value'] = $d[2];
                }
                
                if (!empty($charId) && !in_array($states[$key]['characteristic_id'], (array) $charId))
				{
                    unset($states[$key]);
				}
                if (empty($states[$key]['characteristic_id']))
				{
                    unset($states[$key]);
				}
            }
            
            return !empty($states) ? $states : null;
        }
        else
		{
            return null;
        }
    }
	



	private function fuck()
	{
        $states = array();
		$d=$this->getSessionStates();
        q($d,1);
        // get all stored selected states
        foreach ((array) $d as $val)
		{
            $states[] = $val['val'];
		}

		// calculate scores
        $matches = $this->getTaxaScores($states, false);

		$fullhits = 0;
		foreach((array)$matches as $match)
			if ($match['s']) $fullhits++;

        // only keep the 100% scores, no partial matches for naturalis
        $res = array();
        foreach ((array) $matches as $match) {

            if ($match['s'] == 100) {

                if ($match['type'] == 'variation') {

                    $val = $this->getVariation($match['id']);

                    $nbc = $this->models->NbcExtras->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'ref_id' => $val['id'], 
                            'ref_type' => 'variation'
                        ), 
                        'columns' => 'name,value', 
                        'fieldAsIndex' => 'name'
                    ));

                    $label = $val['label'];
                    
					$d = $this->nbcExtractGenderTag($label);
                    
                    $res[] = $this->createDatasetEntry(
                    array(
                        'val' => $val, 
                        'nbc' => $nbc, 
                        'label' => $val['label'], 
						'common' => $this->getCommonname($val['taxon_id']),
                        'gender' => array($d['gender'], $d['gender_label']),
                        'related' => $this->getRelatedEntities(array(
                            'vId' => $val['id']
                        )), 
                        'type' => 'v', 
                        'inclRelated' => false,
						'details' => $this->_matrixSuppressDetails ? null : $this->getVariationStates($val['id'])
                    ));
					
                } else
                if ($match['type'] == 'matrix') {

					$image = $match['l'].'.jpg';

                    $res[] = $this->createDatasetEntry(
                    array(
                        'val' => $match, 
                        'label' => $match['l'], 
                        'type' => 'm', 
						'image' => file_exists($this->getProjectUrl('projectMedia').$image) ? $image : null,
                        'inclRelated' => false,
						'details' => $this->_matrixSuppressDetails ? null : $this->getMatrixStates($match['id'])
                    ));

                }
                else {

                    $c = $this->models->Commonname->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $match['id'], 
                            'language_id' => $this->getCurrentLanguageId()
                        )
                    ));
                    
                    $common = $match['l'];

                    foreach ((array) $c as $cVal) {
                        if ($cVal['commonname'] != $match['l']) {
                            $common = $cVal['commonname'];
                            break;
                        }
                    }
                    
                    $nbc = $this->models->NbcExtras->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'ref_id' => $match['id'], 
                            'ref_type' => 'taxon'
                        ), 
                        'columns' => 'name,value', 
                        'fieldAsIndex' => 'name'
                    ));
                    

                    $res[] = $this->createDatasetEntry(

                    array(
                        'val' => $match, 
                        'nbc' => $nbc, 
                        'label' => $common, 
                        'related' => $this->getRelatedEntities(array(
                            'tId' => $match['id']
                        )), 
                        'type' => 't', 
                        'highlight' => 0,
						'details' => $this->_matrixSuppressDetails ? null : $this->getTaxonStates($match['id'])
                    ));

                }
            }
        }

        if (count((array)$res)==1) {
            
            $res[0]['d'] =
            	$res[0]['y']=='t' ?
	            	$this->getTaxonStates($res[0]['i']) :
	            	$this->getVariationStates($res[0]['i']);
            
        } else {
        
	        $this->customSortArray($res, array(
	            'key' => 'l', 
	            'case' => 'i',
				'dir' => 'asc'
	        ));

        }
		
		if ($this->_matrixSuppressDetails!=true && count((array)$res)!=0)
			$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));

        return $res;
	}






























}	