<?php 

/*

	data & matching
	on initial load, the entire data-set is fed into data. dataset in matrix.js via
	setDataSet() in index.php/tpl. from that point on, each time a character state
	is (un)set, setState() is called, which only returns an array of taxon id's with
	scores, which are applied to data.dataset in applyScores(), resulting in
	data.resultset, which contains the actual results as they are displayed.
	n.b.: as loading everything at once in the PHP-page caused some browsers to stall
	or crash (large keys on iPad), the initial loading is now done in two steps.
	first, the first `$this->settings->items_per_page` items of the dataset are fully
	loaded in the PHP-page (padded with empty array-cells to fool the various dataset
	counters), then the entire dataset is loaded through AJAX, overwriting the initial
	dataset (it is unclear why this does not cause any memory problems, as the size of
	the data is no different, but it appears to work).

	sorting
	initial sorting is done in MatrixKeyController::sortDataSet(), using one of the
	following fields:
		- by field name defined by setting 'initial_sort_column'
		- by taxon concept
		- by label (always exists for each taxon, variation and matrix)
	for each of compared items $a and $b the first existing field from the list above
	is used for comparison (theoretically, a variation's label could be compared with
	a taxon's scientific name)
	sorting after selection of states is done by score (matching percentage). data.scores
	is sorted this way, causing the filtered data.resultset to be sorted in the same way.
	should the setting 'always_sort_by_initial' be true, them data.resultset is
	re-sorted in JS::sortResults(), using the field defined by 'initial_sort_column'.
	this way, the score sort can be overridden. additionally, there is a hook-function
	hook_postSortResults() which is called after sortResults(), allowing specific
	implementations to further alter the results' order (function is called regardless
	of the values of 'always_sort_by_initial' or 'initial_sort_column').

*/


/*
	needs to be undone of NBC:
		private $_nbcImageRoot=true;

*/


include_once ('Controller.php');
include_once ('MediaController.php');
include_once ('ModuleSettingsReaderController.php');

class MatrixKeyController extends Controller
{

    public $usedModels = array(
        'matrices',
        'matrices_names',
        'matrices_taxa',
        'matrices_taxa_states',
        'characteristics',
		'characteristics_chargroups',
        'characteristics_matrices',
        'characteristics_labels',
        'characteristics_states',
        'chargroups_labels',
        'chargroups',
        'characteristics_chargroups',
        'matrices_variations',
        'nbc_extras',
        'variation_relations',
		'matrices_variations',
		'taxa_relations',
		'gui_menu_order',
		'module_settings',
		'content_introduction',
		'media_taxon'
    );

    public $controllerPublicName = 'Matrix key';
    public $controllerBaseName = 'matrix';

	private $_totalEntityCount=0;
	private $_activeMatrix=null;
	private $_useCorrectedHValue=true;
	private $_dataSet=null;
	private $_facetmenu=null;
	private $_scores=null;
	private $_related=null;
	private $_searchTerm=null;
	private $_searchResults=null;
	private $_introductionLinks=array();
	private $_incUnknowns=false;
	private $_master_matrix;
	private $settings;

	private $_mc;
	private $_smc;
	private $use_media;

    public $cssToLoad = array('matrix.css');

    public $jsToLoad = array(
        'all' => array('scrollfix.js', 'script.js', 'main.js', 'matrix.js'),
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
		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->moduleSettings->assignModuleSettings( $this->settings );

				
		if ( isset($this->settings->generic_image_names) )
		{
			$images=json_decode( $this->settings->generic_image_names, true );
			if ( json_last_error() == JSON_ERROR_NONE)
			{
				$this->_genericImages=$images;
			}
			else
			{
				$this->_genericImages=[ 'portrait'=>$this->settings->generic_image_names,'landscape'=>$this->settings->generic_image_names ];
			}
		}
		else
		{
			$this->_genericImages=[ 'portrait'=>'noImagePortrait.jpg','landscape'=>'noImageLandscape.jpg' ];
		}
		
		foreach((array)$this->_genericImages as $orientation => $imagename)
		{
			$this->_genericImages[$orientation] = $this->getProjectUrl('systemMedia').$imagename;
		}

		$this->initializeMatrixId();
		$this->setActiveMatrix();

		if (is_null($this->getCurrentMatrixId()))
		{
			$this->addError($this->translate('No matrices have been defined.'));
		}

		$this->setIntroductionLinks();

		$this->_search_presence_help_url = $this->moduleSettings->getModuleSetting( array('setting'=>'url_help_search_presence','module'=>'utilities') );

		$this->smarty->assign( 'image_root_skin', $this->getProjectUrl('systemMedia') );
		$this->smarty->assign( 'introduction_links', $this->getIntroductionLinks() );
		$this->smarty->assign( 'settings', $this->settings );
		$this->smarty->assign( 'generic_images', $this->_genericImages );

		$this->setFacetMenu();
		$this->setIncUnknowns( false );
		$this->setMediaControllers();
	}


	private function setMediaControllers()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());

        // Dedicated controller for taxa
	    $this->_smc = new MediaController();
        $this->_smc->setModuleId($this->getCurrentModuleId('species'));
	}



    public function indexAction()
    {
		$matrix=$this->getActivematrix();

        $this->setPageName( sprintf( $this->translate('Matrix "%s": identify'), $matrix['name'] ) );

		$this->setMasterMatrix();
		$this->setDataSet();
		$this->setScores();
		
		$this->smarty->assign( 'session_scores', json_encode( $this->getScores() ) );
		$this->smarty->assign( 'session_states', json_encode( $this->getSessionStates() ) );
		$this->smarty->assign( 'session_characters', json_encode( $this->getCharacterCounts() ) );
		$this->smarty->assign( 'session_statecount', json_encode( $this->setRemainingStateCount() ) );
		$this->smarty->assign( 'session_menu', json_encode( $this->getFacetMenu() ) );
		$this->smarty->assign( 'full_dataset', json_encode( $this->getDataSet() ) );
        $this->smarty->assign( 'matrix', $matrix );
        $this->smarty->assign( 'matrices', $this->getAllMatrices() );
		$this->smarty->assign( 'master_matrix', $this->getMasterMatrix() );
		$this->smarty->assign( 'facetmenu', $this->getFacetMenu() );
		$this->smarty->assign( 'states', $this->getCharacterStates() );

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
		if ($this->rHasVal('action', 'get_data_set'))
		{
			$this->setDataSet();
			$this->smarty->assign('returnText', json_encode($this->getDataSet()));
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
        $mts = $this->models->MatricesTaxaStates->_get(
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
//			$this->redirect( "index.php" );
		}
    }

    private function getMatrix( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;

		if (is_null($id))
			return;

		$m = $this->models->MatrixkeyModel->getMatrix(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"matrix_id"=>$id
		));

		return ( isset($id) && $id!='*' && isset($m[$id]) ? $m[$id] : ( isset($m) ? $m  : null ) ) ;
	}

    private function getCharacterStates( $p=null )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$char=isset($p['char']) ? $p['char'] : null;

		//if (is_null($id) && is_null($char))
		//  return;

        $states = $this->models->MatrixkeyModel->getCharacterStates(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"state_id"=>$id,
			"characteristic_id"=>$char
		));
		
        // getCharacterStates may return array of records or single record...
        if (isset($states[0]))
		{
            foreach ($states as $i => $state)
			{
				//if (!filter_var($state['file_name'], FILTER_VALIDATE_URL) === false)
				if (!empty($state['file_name']))
					continue;

                $this->_mc->setItemId($state['id']);
                $media = $this->_mc->getItemMediaFiles();

                $states[$i]['file_name'] = $states[$i]['file_dimensions'] =
                    $states[$i]['img_dimensions'] = null;

                if (!empty($media))
				{
                    $states[$i]['file_name'] = $media[0]['rs_original'];
                    $states[$i]['file_dimensions'] =
                        $media[0]['width'] . ':' . $media[0]['height'];
                    $states[$i]['img_dimensions'] =
                        array($media[0]['width'], $media[0]['height']);
                }
            }
        } else
		if (isset($states['id']))
		{
			//if (!filter_var($states['file_name'], FILTER_VALIDATE_URL))
			if (empty($states['file_name']))
			{
				$this->_mc->setItemId($states['id']);
				$media = $this->_mc->getItemMediaFiles();

				$states['file_name'] = $states['file_dimensions'] =
					$states['img_dimensions'] = null;

				if (!empty($media))
				{
					$states['file_name'] = $media[0]['rs_original'];
				$states['file_dimensions'] =
					$media[0]['width'] . ':' . $media[0]['height'];
				$states['img_dimensions'] =
					array($media[0]['width'], $media[0]['height']);
				}
			}
        }

		foreach((array)$states as $key=>$state)
		{
			if (isset($state['file_name']))
			{
				$states[$key]['file_name_is_full_url']=(!filter_var($state['file_name'], FILTER_VALIDATE_URL) === false);
			}
		}

        return $states;
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

            $mts = $this->models->MatricesTaxaStates->_get(
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

        return $hValue * ($this->_useCorrectedHValue == true ? $corrFactor : 1);
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
		    $item['url_thumb'] = 
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
			foreach((array)$matrices as $key=>$val)
			{
				$matrices[$key]['info']=isset($info['matrix'][$val['id']]) ? $info['matrix'][$val['id']] : null;
				$this->induceThumbNailFromImage( $matrices[$key]['info'] );
			}
		}

		if ( isset($this->settings->use_overview_image) && $this->settings->use_overview_image )
		{
			foreach((array)$taxa as $key=>$val)
			{
			    $this->_smc->setItemId($val['id']);
			    $taxa[$key]['info']['url_image'] = $this->_smc->getOverview();

			}
		}

		foreach((array)$taxa as $key=>$val)
		{
			$taxa[$key]['images']=
				$this->models->MediaTaxon->_get( [ "id"=> [
					"taxon_id"=>$val['id'],
					"project_id" => $this->getCurrentProjectId()
				 ],
				 "columns" => "file_name,thumb_name,overview_image" 
			] );
		}

		$all=array_merge((array)$taxa,(array)$variations);

		if ( isset($this->settings->species_module_link) || isset($this->settings->species_module_link_force) )
		{
			foreach((array)$all as $key=>$val)
			{
				if ( $val['type']=='taxon' )
				{
					$id=$val['id'];
				}
				else
				if ( $val['type']=='variation' )
				{
					$id=$val['taxon']['id'];
				}

				if ( $this->settings->species_module_link_force && isset($id) )
				{
					$all[$key]['info']['url_external_page']=str_replace( [ '%s','%PID%','%TAXON%' ],[$id,$this->getCurrentProjectId(),$id],$this->settings->species_module_link);
				}
				else
				if ( !isset($val['info']['url_external_page']) && isset($this->settings->species_module_link) )
				{
					$all[$key]['info']['url_external_page']=str_replace( [ '%s','%PID%','%TAXON%' ],[$id,$this->getCurrentProjectId(),$id],$this->settings->species_module_link);
				}
			}
		}

		$all=array_merge((array)$all,(array)$matrices);

		if ($all)
		{
			usort($all, array($this,'sortDataSet'));
		}
		
		return $all;

	}

    private function getTaxaInMatrix()
    {
        $m=$this->models->MatrixkeyModel->getTaxaInMatrix(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"matrix_id"=>$this->getCurrentMatrixId(),
			"preferred_nametype_id"=>$this->getNameTypeId(PREDICATE_PREFERRED_NAME)
        ));

		$taxa=array();
		$ranks=$this->getProjectRanks() ;

        foreach ((array)$m as $key=>$val)
		{
			//$d=$this->getTaxonById( $val['taxon_id'] );
			$val['label']=$this->formatTaxon(['taxon'=>$val,'ranks'=>$ranks]);
			$d=$val;

			if (
				!isset($val['is_empty']) ||
				!isset($this->settings->allow_empty_species) ||
				(isset($this->settings->allow_empty_species) && $this->settings->allow_empty_species==true) ||
				(isset($this->settings->allow_empty_species) && $this->settings->allow_empty_species==false && $val['is_empty']==0)
			)
			{
				$d['type']='taxon';
				if (
					!isset($this->settings->suppress_details) ||
					(isset($this->settings->suppress_details) && $this->settings->suppress_details!=1))
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
		if ( !$this->models->MatricesVariations->getTableExists() )
			return null;

        $m=$this->models->MatrixkeyModel->getVariationsInMatrix(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"matrix_id"=>$this->getCurrentMatrixId()
        ));

        foreach ( (array)$m as $key=>$val )
		{
			$d=$this->getTaxonById( $val['taxon_id'] );
			$d['label']=$this->formatTaxon($d);
			$m[$key]['taxon']=$d;

			// Try to fetch common name from `names` table if this is not present
			// in `common_names` table.
            if (empty($m[$key]['taxon']['commonname'])) {
				$m[$key]['taxon']['commonname']	= $this->getTaxonCommonNameAlternate($val['taxon_id']);
            }

			$m[$key]['gender']=$this->extractGenderTag( $val['label'] );
			if (!isset($this->settings->suppress_details) || (
				isset($this->settings->suppress_details) && $this->settings->suppress_details!=1))
			{
				$m[$key]['states']=$this->getVariationStates( $val['id'] );
			}
			$m[$key]['related_count']=$this->getRelatedEntityCount( array('id'=>$val['id'],'type'=>'variation') );
        }

        return $m;
    }

    private function getMatricesInMatrix()
    {
        $m=$this->models->MatrixkeyModel->getMatricesInMatrix(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"matrix_id"=>$this->getCurrentMatrixId()
		));

		foreach((array)$m as $key=>$val)
		{
			if (!isset($this->settings->suppress_details) ||
				(isset($this->settings->suppress_details) && $this->settings->suppress_details!=1))
			{
				$m[$key]['states']=$this->getMatrixStates( $val['id'] );
			}
		}

		return $m;
    }

    private function getAllMatrices()
    {
		return $this->models->MatrixkeyModel->getAllMatrices(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
		));
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

		return $this->models->MatrixkeyModel->getEntityStates(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"matrix_id"=>$this->getCurrentMatrixId(),
			"type"=>$type,
			"id"=>$id
		));
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
		$menu=$this->models->MatrixkeyModel->getFacetMenu(array(
			"matrix_id"=>$this->getCurrentMatrixId(),
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId()
		));

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
		$c=$this->models->CharacteristicsChargroups->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'chargroup_id' => $id
				),
				'order' => 'show_order'
			));

		$d=array();

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

        $char=$this->models->MatrixkeyModel->getCharacter(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"characteristic_id"=>$id
		));

		if (strpos($char['label'],'|')!==false)
		{
			$d = explode('|',$char['label'],3);
			$char['label'] = isset($d[0]) ? $d[0] : '';
			$char['info'] = isset($d[1]) ? $d[1] : '';
			$char['unit'] = isset($d[2]) ? $d[2] : '';
		}

		if ($char['type']=='range' || $char['type']=='distribution')
		{
			$cs = $this->models->CharacteristicsStates->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'characteristic_id' => $id
					),
					'columns' => 'min(lower) as lowest,max(upper) as most_upper'
				));

			$char['min'] = $cs[0]['lowest'];
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
                    //$states[$key]['file_name'] = $d['file_name'];

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

                    $d['mean >=#'] = '(' . (string)(float)$value . ' - (' . (string)(int)$sd . ' * sd))';
                    $d['mean <=#'] = '(' . (string)(float)$value . ' + (' . (string)(int)$sd . ' * sd))';
                }
                // or mark just mark the upper and lower boundaries of the value
                else {

                    $d['lower <='] = $d['upper >='] = (float)$value;
                }

                // get any states that correspond with these values
                $cs = $this->models->CharacteristicsStates->_get(array(
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

        $results = $this->models->MatrixkeyModel->getScoresLiberal(array(
			'project_id'=>$this->getCurrentProjectId(),
			'matrix_id'=>$this->getCurrentMatrixId(),
			'language_id'=>$this->getCurrentLanguageId(),
			'state_ids'=>$s,
			'character_ids'=>$c,
			'incUnknowns'=>$incUnknowns,
			'stateCount'=>$stateCount,
			'matrixVariationExists'=>$this->models->MatricesVariations->getTableExists()
		));

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
                    $d['mean >=#'] = '(' . (string)(float)$value . ' - (' . (string)(int)$sd . ' * sd))';
                    $d['mean <=#'] = '(' . (string)(float)$value . ' + (' . (string)(int)$sd . ' * sd))';
                }
                // if there isn't, just mark the upper and lower boundaries of the value
                else
				{
                    $d['lower <='] = $d['upper >='] = (float)$value;
                }

                // get any states that correspond with these values...
                $cs = $this->models->CharacteristicsStates->_get(array(
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

		return $this->models->MatrixkeyModel->getScoresRestrictive(array(
			'project_id'=>$this->getCurrentProjectId(),
			'matrix_id'=>$this->getCurrentMatrixId(),
			'language_id'=>$this->getCurrentLanguageId(),
			'state_ids'=>$s,
			'character_ids'=>$c,
			'incUnknowns'=>$incUnknowns,
			'stateCount'=>$stateCount,
			'matrixVariationExists'=>$this->models->MatricesVariations->getTableExists()
		));
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

		$this->models->MatrixkeyModel->setRemainingCountClauses( $this->makeRemainingCountClauses() );

        $all=$this->models->MatrixkeyModel->getRemainingStateCount(array(
			"project_id"=>$this->getCurrentProjectId(),
			"matrix_id"=>$this->getCurrentMatrixId()
		));

        $results=array();

        foreach ((array)$all as $val)
		{
            if (!is_null($char) && $val['characteristic_id']!=$char) continue;

			if ($groupByChar)
			{
	            $results[$val['characteristic_id']]['states'][$val['state_id']] = (int)$val['tot'];
	            $results[$val['characteristic_id']]['tot'] =
					(isset($all[$val['characteristic_id']]['tot']) ? $all[$val['characteristic_id']]['tot'] : 0) + (int)$val['tot'];
			}
			else
			{
	            $results[$val['state_id']] = (int)$val['tot'];
			}
        }

        return $results;
    }

    private function getCharacterCounts()
    {
		//$c=$this->makeRemainingCountClauses();
		$this->models->MatrixkeyModel->setRemainingCountClauses( $this->makeRemainingCountClauses() );

        $all=$this->models->MatrixkeyModel->getCharacterCounts(array(
			"project_id"=>$this->getCurrentProjectId(),
			"matrix_id"=>$this->getCurrentMatrixId()
		));

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
						$d['mean >=#'] = '(' . (string)(int)$value . ' - (' . (string)(int)$sd . ' * sd))';
						$d['mean <=#'] = '(' . (string)(int)$value . ' + (' . (string)(int)$sd . ' * sd))';

					}
					else
					{
						$d['lower <='] = $d['upper >='] = (int)$value;
					}

					$cs = $this->models->CharacteristicsStates->_get(array('id' => $d));

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

        // @check_this: fsM not defined or set in this scope
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
        $this->_searchResults = $this->models->MatrixkeyModel->getSearchResults(array(
			"project_id"=>$this->getCurrentProjectId(),
			"language_id"=>$this->getCurrentLanguageId(),
			"matrix_id"=>$this->getCurrentMatrixId(),
			"search"=>$this->getSearchTerm()
		));
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
		$topics=array();

		if ( isset($this->settings->introduction_topic_colophon_citation) )
		{
			array_push($topics,$this->settings->introduction_topic_colophon_citation);
		}

		if ( isset($this->settings->introduction_topic_versions) )
		{
			array_push($topics,$this->settings->introduction_topic_versions);
		}

		if ( isset($this->settings->introduction_topic_inline_info) )
		{
			array_push($topics,$this->settings->introduction_topic_inline_info);
		}

		$this->_introductionLinks=array();

		foreach((array)$topics as $topic)
		{
			$d=$this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'topic' => $topic
					),
					'columns'=>'page_id,topic,content'
				)
			);

			if ($d)
			{
				$this->_introductionLinks[$topic]=!empty(strip_tags($d[0]['content'])) ? $d[0] : null;
			}
		}
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
