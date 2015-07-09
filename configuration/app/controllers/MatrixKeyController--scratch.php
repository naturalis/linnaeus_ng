<?php

/*

	!!!! needs optimization: $this->getVariationStates($val['id'])

	on NSR-style keys with landscape-orientated pictures:
	change two settings:
		matrix_items_per_line =  3
		matrix_items_per_page = 18 (or something else divisible by 3)
	and stylesheet overrides in project custom stylesheet:
		www/app/style/custom/[pId].css 
	containing:
		.result {
		  width: 222px;
		  margin:15px 15px 10px 0;
		  float:left;
		  border: 1px solid #ddd;
		}
		
		.result-image-container {
		  width:202px;
		  height:150px;
		}
		
		.result-image {
		  width:200px;
		  height:auto;
		  width:auto;
		  max-width:202px;
		  height:auto;
		  max-height:150px;
		  margin-left:auto;
		  margin-right:auto;
		  display: block;
		  margin-bottom:2px;
		}
		
		.result-labels {
		  width:100%;
		  height:45px;
		  margin-top:6px;
		}
		
		.result-icon {
		  width:74px;
		}

*/

include_once ('Controller.php');
class MatrixKeyController extends Controller
{












































    private $_matrixType = 'default';
	private $_useSepCoeffAsWeight = false;
	private $_matrixStateImageMaxHeight = null;
	private $_externalSpeciesUrlTarget = '_blank';
	private $_matrixSuppressDetails = false;
	private $_nbcImageRoot = null;
	private $_externalSpeciesUrlPrefix = null;
	private $_matrix_use_emerging_characters = null;
	private $__matrix_calc_char_h_val=true;

    public $usedHelpers = array();
    public $controllerPublicName = 'Matrix key';
    public $controllerBaseName = 'matrixkey';
    public $jsToLoad = array(
        'all' => array(
            'main.js', 
            'matrix.js', 
            'prettyPhoto/jquery.prettyPhoto.js', 
            'dialog/jquery.modaldialog.js'
        ), 
        'IE' => array()
    );



    public function matricesAction()
    {
		
		$this->storeHistory = false;

		if (!$this->rHasVal('action','popup'))
			$this->redirect('identify.php');

		$matrices = $this->getMatrices();
		$this->smarty->assign('matrices', $matrices);
		$this->smarty->assign('currentMatrixId', $this->getCurrentMatrixId());
        $this->printPage();

    }

    public function useMatrixAction()
    {
        if ($this->rHasId()) {

            $this->storeHistory = false;

            $this->setCurrentMatrixId($this->requestData['id']);

			$this->setTotalEntityCount();

            $this->redirect('identify.php');
        }
        else {

            $this->printGenericError($this->translate('Missing matrix ID.'));
        }
    }

    public function examineAction ()
    {
        $this->checkMatrixIdOverride();
        
        $id = $this->getCurrentMatrixId();

        if (!isset($id))
		{
            $this->storeHistory = false;
            $this->redirect('matrices.php');
        }
        
		$matrix = $this->getMatrix($id);

        $this->smarty->assign('function', 'Examine');
        
        $this->setPageName(sprintf($this->translate('Matrix "%s": examine'), $matrix['name']));

        $this->smarty->assign('projectId', $this->getCurrentProjectId());
        $this->smarty->assign('taxa', $this->getTaxaInMatrix());
        $this->smarty->assign('matrixCount', $this->getMatrixCount());
        $this->smarty->assign('matrix', $matrix);
        $this->smarty->assign('examineSpeciesRecall', $this->examineSpeciesRecall());
        
        $this->printPage();
    }

    public function compareAction ()
    {
		
        $this->checkMatrixIdOverride();
        
        $id = $this->getCurrentMatrixId();
        
        if (!isset($id)) {
            
            $this->storeHistory = false;
            
            $this->redirect('matrices.php');
        }
        
		$matrix = $this->getMatrix($id);

        $this->smarty->assign('function', 'Compare');
        
        $this->setPageName(sprintf($this->translate('Matrix "%s": compare'), $matrix['name']));
        
        $this->smarty->assign('projectId', $this->getCurrentProjectId());
        $this->smarty->assign('taxa', $this->getTaxaInMatrix());
        $this->smarty->assign('matrixCount', $this->getMatrixCount());
        $this->smarty->assign('matrix', $matrix);
        $this->smarty->assign('compareSpeciesRecall', $this->compareSpeciesRecall());
        
        $this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
		if ($this->rHasVar('key'))
		{
			$this->setCurrentMatrixId($this->requestData['key']);
			$this->setTotalEntityCount();
		}
		
		if (!$this->rHasVal('action'))
		{
            $this->smarty->assign('returnText', 'error');
        } else
		if ($this->rHasVal('action', 'get_states'))
		{
            $this->smarty->assign('returnText', json_encode($this->getCharacteristicStates($this->requestData['id'])));
        } else 
		if ($this->rHasVal('action', 'get_taxa'))
		{
            $this->stateMemoryUnset();

            $this->stateMemoryStore($this->requestData['id']);
            
            $this->smarty->assign(
				'returnText',
				json_encode(
					(array)$this->getTaxaScores($this->requestData['id'],
					isset($this->requestData['inc_unknowns']) ? ($this->requestData['inc_unknowns'] == '1') : false)
				)
			);
        }
        else
		if ($this->rHasVal('action', 'get_taxon_states'))
		{
            
            $this->smarty->assign('returnText', json_encode((array) $this->getTaxonStates($this->requestData['id'])));
        }
        else
		if ($this->rHasVal('action', 'compare'))
		{
            $this->smarty->assign('returnText', json_encode((array) $this->getTaxonComparison($this->requestData['id'])));
        }
        else
		if ($this->rHasVal('action', 'store_showstate_results'))
		{
            $this->showStateStore('results');
        }
        else
		if ($this->rHasVal('action', 'store_showstate_pattern'))
		{
            $this->showStateStore('pattern');
        }
        else
		if ($this->rHasVal('action', 'store_examine_val'))
		{
            $this->examineSpeciesStore($this->requestData['id']);
        }
        else
		if ($this->rHasVal('action', 'store_compare_vals'))
		{
            $this->compareSpeciesStore($this->requestData['id']);
        }
        else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','do_search'))
		{
			$results = $this->nbcDoSearch($this->requestData['params']);

			$this->smarty->assign('returnText', 
				json_encode(
					array(
						'results' => $results, 
						'count' => array(
							'results' => count((array) $results)
						)
					)
				)
			);

        }
        else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','save_session_setting'))
		{
            $this->saveSessionSetting($this->requestData['setting']);
        }
        else

		if ($this->_matrixType=='nbc' && $this->rHasVal('action','get_initial_values'))
		{
			// state image urls
			$cs = $this->models->CharacteristicState->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					), 
					'columns' => 'id,file_name',
					'fieldAsIndex' => 'id'
				));

			$cl = $this->models->CharacteristicLabel->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId()
					),
					'columns' => 'characteristic_id,label',
					'fieldAsIndex' => 'characteristic_id'
				));

            $this->smarty->assign('returnText', 
				json_encode(
				array(
					'stateImageUrls' => 
						array(
							'baseUrl' => $this->getProjectUrl('projectMedia'),
							'baseUrlSystem' => $this->getProjectUrl('systemMedia'),
							'fileNames' => $cs
						),
					'characterNames' => $cl
					)
				)
			);

		}
		
        $this->allowEditPageOverlay = false;
        
        $this->printPage(isset($tpl) ? $tpl : null);

    }



    public function cacheAllTaxaInMatrix ()
    {
        $mt = $this->models->MatrixTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'taxon_id,matrix_id', 
            'order' => 'matrix_id'
        ));
        
        $tree = $this->getTreeList();
        
        $taxa = array();
        
        foreach ((array) $mt as $key => $val) {
            
            if (!isset($tree[$val['taxon_id']]))
                continue;
            
            $d = $tree[$val['taxon_id']];
            
            $taxa[$val['matrix_id']][] = array(
                'id' => $d['id'], 
                'h' => $d['is_hybrid'], 
                'l' => $d['taxon'], 
                'type' => 'tx'
            );
        }
        
        foreach ((array) $taxa as $key => $val) {
            $dummy = array();
            foreach ((array) $val as $tVal) {
                $dummy[] = $tVal;
            }
            $this->customSortArray($dummy, array(
                'key' => 'taxon', 
                'case' => 'i'
            ));
            
            if (!$this->getCache('matrix-taxa-' . $key))
                $this->saveCache('matrix-taxa-' . $key, isset($dummy) ? $dummy : null);
        }
    }




    private function getDefaultMatrixId ()
    {

		$m = $this->models->Matrix->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'got_names' => 1,
				'default' => 1
			), 
			'columns' => 'id'
		));
		
		return isset($m[0]['id']) ? $m[0]['id'] : null;

    }

    private function getFirstMatrixId ()
    {

		$m = $this->getMatrices();
		$m = array_shift($m);
		return $m['id'];

    }



    private function getMatrixCount ()
    {
        $m = $this->getMatrices();
        
        return count((array) $m);
    }

    private function getMatrix($id)
    {
        if (is_null($id))
            return;

        $m = $this->getMatrices();
		
        return isset($m[$id]) ? $m[$id] : null;
    }

    private function getMatricesInMatrix ()
    {
        $mts = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId(), 
                'ref_matrix_id is not' => 'null'
            ), 
            'columns' => 'distinct ref_matrix_id,\'matrix\' as type'
        ));
        
        foreach ((array) $mts as $key => $val) {
            
            $d = $this->getMatrix($val['ref_matrix_id']);
            
            if (isset($d)) {
                
                $matrices[$val['ref_matrix_id']] = array(
                    'id' => $d['id'], 
                    'l' => $d['name'], 
                    'type' => 'mtx'
                );
            }
        }
        
        return isset($matrices) ? $matrices : null;
    }

	private function scaleDimensions($d)
	{
		
		if (is_null($this->_matrixStateImageMaxHeight) || ($d[1] < $this->_matrixStateImageMaxHeight))
			return $d;

		return array(round(($this->_matrixStateImageMaxHeight / $d[1]) * $d[0]),$this->_matrixStateImageMaxHeight);
		
	}



	/*
		each selected state further restricts the result set
		example: red AND black AND round
	*/
    private function _getTaxaScoresRestrictive ($states, $incUnknowns = false)
    {
        $s = $c = array();
        $stateCount = 0;
        
        // we have to find out which states we are looking for
        foreach ((array) $states as $sKey => $sVal)
		{
            $d = explode(':', $sVal);
            
            $charId = isset($d[1]) ? $d[1] : null;
            $value = isset($d[2]) ? $d[2] : null;

            // which is easy for the non-range characters...
            if ($d[0] != 'f') {
                
                if (isset($d[2]))
                    $s[$d[2]] = $d[2];
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
				
				$value = str_replace(',','.',$value);

                if (isset($sd))
                // calculate the spread around the mean...
				{
					$sd = str_replace(',','.',$sd);
                    $d['mean >=#'] = '(' . strval(floatval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
                    $d['mean <=#'] = '(' . strval(floatval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';
                }
                else
                // or mark just mark the upper and lower boundaries of the value
				{
                    
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
        
        $n = $stateCount;
        $si = implode(',', $s);
        $ci = implode(',', $c);

		// query to get all taxa, matrices and variations, including their matching percentage
        $q = "
        	select 'taxon' as type, _a.taxon_id as id,
       				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
       				_c.is_hybrid as h, trim(_c.taxon) as l
			from %PRE%matrices_taxa _a
			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _a.matrix_id = _b.matrix_id
				and _a.taxon_id = _b.taxon_id
				and _b.state_id in (" . $si . ")
			left join %PRE%taxa _c
				on _a.taxon_id = _c.id
			where _a.project_id = " . $this->getCurrentProjectId() . "
				and _a.matrix_id = " . $this->getCurrentMatrixId() . "
			group by _a.taxon_id

        	union all

        	select 'matrix' as type, _a.id as id, 
				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
				0 as h, trim(_c.name) as l
			from  %PRE%matrices _a
			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _b.matrix_id = " . $this->getCurrentMatrixId() . "
				and _a.id = _b.ref_matrix_id
				and _b.state_id in (" . $si . ")
			left join %PRE%matrices_names _c
				on _a.id = _c.matrix_id
				and _c.language_id = " . $this->getCurrentLanguageId() . "
			where _a.project_id = " . $this->getCurrentProjectId() . "
				and _a.id != " . $this->getCurrentMatrixId() . "
			group by id" . ($this->_matrixType == 'nbc' ? "

			union all

			select 'variation' as type, _a.variation_id as id, 
				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
				0 as h, trim(_c.label) as l
			from  %PRE%matrices_variations _a        		
			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _a.matrix_id = _b.matrix_id
				and _a.variation_id = _b.variation_id
				and _b.state_id in (" . $si . ")
			left join %PRE%taxa_variations _c
				on _a.variation_id = _c.id
			where _a.project_id = " . $this->getCurrentProjectId() . "
				and _a.matrix_id = " . $this->getCurrentMatrixId() . "
			group by _a.variation_id" : "")
		;

        $results = $this->models->MatrixTaxonState->freeQuery($q);

		/*
			"unknowns" are taxa for which no state has been defined within a certain character.
			note that this is different froam having a differen state within that character. if
			there is a character "colour", and taxon A has the state "green", taxon B has the 
			state "brown" and taxon C has no state for colour, then selecting "brown" with 'Treat 
			unknowns as matches' set to false will yield A:0%, B:100%, C:0%. selecting "brown" 
			with 'Treat unknowns as matches' set to true will yield A:0%, B:100%, C:100%. it can
			be seen as a 'rather safe than sorry' setting.
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
						_c.is_hybrid as h, 
						trim(_c.taxon) as l
					from
						%PRE%matrices_taxa _a
					left join %PRE%taxa _c
						on _a.taxon_id = _c.id
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
					having count(_b.id)=0

					union all

					select
						'matrix' as type, 
						_a.id as id,
						0 as h, 
						trim(_c.name) as l
					from
						%PRE%matrices _a

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _b.matrix_id = " . $this->getCurrentMatrixId() . "
						and _a.id = _b.ref_matrix_id
						and _b.characteristic_id =".$character."
					left join %PRE%matrices_names _c
						on _a.id = _c.matrix_id
						and _c.language_id = " . $this->getCurrentLanguageId() . "
					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.id != " . $this->getCurrentMatrixId() . "

					group by
						_a.id
					having count(_b.id)=0

				".($this->_matrixType == 'nbc' ? "
		
					union all

					select
						'variation' as type, 
						_a.variation_id as id,
						0 as h, 
						trim(_c.label) as l
					from
						%PRE%matrices_variations _a
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.variation_id = _b.variation_id
						and _b.characteristic_id =".$character."
					left join %PRE%taxa_variations _c
						on _a.variation_id = _c.id
					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "

					group by
						_a.variation_id
					having count(_b.id)=0
				":"");

		        $rrr=$this->models->MatrixTaxonState->freeQuery($q);

				foreach((array)$rrr as $r)
				{
					switch($r['type'])
					{
						case 'taxon': 
							$unknowns['taxon'][$r['id']]=$r;
							isset($unknowns['taxon'][$r['id']]['tot']) ?
								$unknowns['taxon'][$r['id']]['tot']++ : 
								$unknowns['taxon'][$r['id']]['tot']=1;

							$unknowns['taxon'][$r['id']]['s'] = 
								round(($unknowns['taxon'][$r['id']]['tot']/$n)*100);

							break;
						case 'matrix':
							$unknowns['matrix'][$r['id']]=$r;
							isset($unknowns['matrix'][$r['id']]['tot']) ?
								$unknowns['matrix'][$r['id']]['tot']++ : 
								$unknowns['matrix'][$r['id']]['tot']=1;

							$unknowns['matrix'][$r['id']]['s'] = 
								round(($unknowns['matrix'][$r['id']]['tot']/$n)*100);


							break;
						case 'variation':
							$unknowns['variation'][$r['id']]=$r;
							isset($unknowns['variation'][$r['id']]['tot']) ?
								$unknowns['variation'][$r['id']]['tot']++ : 
								$unknowns['variation'][$r['id']]['tot']=1;

							$unknowns['variation'][$r['id']]['s'] = 
								round(($unknowns['variation'][$r['id']]['tot']/$n)*100);

							break;
					}
				}
			}

			foreach((array)$results as $key => $val)
			{
				if (isset($unknowns[$val['type']][$val['id']]))
				{
					$temp=$unknowns[$val['type']][$val['id']];
					$results[$key]['tot']+=$temp['tot'];
					$results[$key]['s']=round(($results[$key]['tot']/$n)*100);
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


        usort($results, array(
            $this, 
            'sortQueryResultsByScoreThenLabel'
        ));

        return $results;
    }
	

	/*
	
		DOES NOT WORK YET - WORK IN PROGRESS
	
		states within the same charachters expand the result set,
		selected states across characters restrict the result set
		example: (red OR black) AND round
	*/
    private function _getTaxaScoresLiberal ($states, $incUnknowns = false)
    {
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
        		group by id" . ($this->_matrixType == 'nbc' ? "
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

        usort($results, array(
            $this, 
            'sortQueryResultsByScoreThenLabel'
        ));

        return $results;
    }

    private function getTaxaScores ($states, $incUnknowns = false)
	{

		$res = $this->_getTaxaScoresRestrictive ($states,$incUnknowns);
		//$res = $this->_getTaxaScoresLiberal ($states,$incUnknowns);

		return $res;

	}

    private function getTaxonComparison($id)
    {
        if (empty($id[0]) || empty($id[1]))
            return;

		$cl = $this->models->CharacteristicLabel->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'language_id' => $this->getCurrentLanguageId()
			),
			'fieldAsIndex' => 'characteristic_id'
		));
			        
        $mts1 = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $this->getCurrentMatrixId(),
				'taxon_id' => $id[0]
				), 
            'columns' => 'taxon_id,characteristic_id,state_id',
			'fieldAsIndex' => 'state_id'
        ));

        $mts2 = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $this->getCurrentMatrixId(),
				'taxon_id' => $id[1]
				), 
            'columns' => 'taxon_id,characteristic_id,state_id',
			'fieldAsIndex' => 'state_id'
        ));

        $overlap = $states1 = $states2 = array();
        
        foreach ((array) $mts1 as $key => $val) {
			
			$state = $this->getCharacteristicState($key);
			$state['characteristic'] = $cl[$val['characteristic_id']]['label'];
            
            if (isset($mts2[$key]))
                $overlap[] = $state;
            else
                $states1[] = $state;
        }

        foreach ((array) $mts2 as $key => $val) {

            if (!isset($mts1[$key])) {
				$state = $this->getCharacteristicState($key);
				$state['characteristic'] = $cl[$val['characteristic_id']]['label'];
                $states2[] = $state;
			}

        }
	
		$t1 = $this->getTaxonById($id[0]);
		$t2 = $this->getTaxonById($id[1]);
		
		$c = $this->getCharacteristics();

		$total = 0;
		foreach((array)$c as $cVal)
			foreach((array)$cVal['states'] as $sVal)
				$total++;
		
		
		$count1 = count((array)$states1);
		$count2 = count((array)$states2);
		$both = count((array)$overlap);
		$neither = $total - $both - $count1 - $count2;
        
        return array(
            'taxon_1' => $t1['taxon'], 
            'taxon_2' => $t2['taxon'], 
            'count_1' => $count1, 
            'count_2' => $count2, 
			'neither' => $neither, 
			'both' => $both, 
            'total' => $total, 
            'coefficients' => $this->calculateDistances($count1, $count2, $both, $neither), 
            'taxon_states_1' => $states1, 
            'taxon_states_2' => $states2, 
            'taxon_states_overlap' => $overlap
        );
    }

    private function calculateDistances ($u1, $u2, $co, $ca)
    {
        $prec = 3;
        
        return array(
            0 => array(
                'name' => $this->translate('Simple dissimilarity coefficient'), 
                'value' => ($u1 + $u2 + $co + $ca) == 0 ? 'NaN' : round(1 - (($co + $ca) / ($u1 + $u2 + $co + $ca)), $prec)
            ), 
            1 => array(
                'name' => 'Russel & Rao', 
                'value' => ($u1 + $u2 + $co + $ca) == 0 ? 'NaN' : round(1 - ($co / ($u1 + $u2 + $co + $ca)), $prec)
            ), 
            2 => array(
                'name' => 'Rogers & Tanimoto', 
                'value' => ($co + $ca + (2 * $u1) + (2 * $u2)) == 0 ? 'NaN' : round(1 - (($co + $ca) / ($co + $ca + (2 * $u1) + (2 * $u2))), $prec)
            ), 
            3 => array(
                'name' => 'Harmann', 
                'value' => ($u1 + $u2 + $co + $ca) == 0 ? 'NaN' : round(1 - ((($co + $ca - $u1 - $u2) / ($u1 + $u2 + $co + $ca)) + 1) / 2, $prec)
            ), 
            4 => array(
                'name' => 'Sokal & Sneath', 
                'value' => (2 * ($co + $ca) + $u1 + $u2) == 0 ? 'NaN' : round(1 - ((2 * ($co + $ca) / (2 * ($co + $ca) + $u1 + $u2))), $prec)
            ), 
            5 => array(
                'name' => 'Jaccard', 
                'value' => ($co + $u1 + $u2) == 0 ? 'NaN' : round(1 - ($co / ($co + $u1 + $u2)), $prec)
            ), 
            6 => array(
                'name' => 'Czekanowski', 
                'value' => ((2 * $co) + $u1 + $u2) == 0 ? 'NaN' : round(1 - ((2 * $co) / ((2 * $co) + $u1 + $u2)), $prec)
            ), 
            7 => array(
                'name' => 'Kulczyski', 
                'value' => (($co + $u1) == 0 || ($co + $u2) == 0) ? 'NaN' : round(1 - (($co / 2) * ((1 / ($co + $u1)) + (1 / ($co + $u2)))), $prec)
            ), 
            8 => array(
                'name' => 'Ochiai', 
                'value' => ($co + $u1) * ($co + $u2) == 0 ? 'NaN' : round(1 - ($co / sqrt(($co + $u1) * ($co + $u2))), $prec)
            )
        );
    }

    private function showStateStore ($state)
    {
        $_SESSION['app'][$this->spid()]['matrix']['storesShowState'][$this->getCurrentMatrixId()] = $state;
    }

    private function showStateRecall ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['storesShowState'][$this->getCurrentMatrixId()]) ? $_SESSION['app'][$this->spid()]['matrix']['storesShowState'][$this->getCurrentMatrixId()] : 'pattern';
    }

    private function examineSpeciesStore ($id)
    {
        $_SESSION['app'][$this->spid()]['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()] = $id;
    }

    private function examineSpeciesRecall ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()]) ? $_SESSION['app'][$this->spid()]['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()] : null;
    }

    private function compareSpeciesStore ($id)
    {
        $_SESSION['app'][$this->spid()]['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()] = $id;
    }

    private function compareSpeciesRecall ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()]) ? $_SESSION['app'][$this->spid()]['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()] : null;
    }

    /* "NBC-style" functions below */
    private function saveSessionSetting ($setting)
    {
        if (!isset($setting['name']))
            return;
        
        if (empty($setting['value']))
            unset($_SESSION['app'][$this->spid()]['matrix']['settings'][$setting['name']]);
        else
            $_SESSION['app'][$this->spid()]['matrix']['settings'][$setting['name']] = $setting['value'];
    }

    private function getSessionSetting ($name)
    {
        if (!isset($name) || !isset($_SESSION['app'][$this->spid()]['matrix']['settings'][$name]))
            return;

        return $_SESSION['app'][$this->spid()]['matrix']['settings'][$name];
    }

    public function getVariations ($tId = null)
    {
        $d = array(
            'project_id' => $this->getCurrentProjectId()
        );
        
        if (isset($tId))
            $d['taxon_id'] = $tId;
        
        $tv = $this->models->TaxonVariation->_get(array(
            'id' => $d, 
            'columns' => 'id,taxon_id,label', 
            'order' => 'label'
        ));
        
        foreach ((array) $tv as $key => $val) {
            
            $tv[$key]['taxon'] = $this->getTaxonById($val['taxon_id']);
            
            $tv[$key]['labels'] = $this->models->VariationLabel->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'variation_id' => $val['id']
                ), 
                'columns' => 'id,language_id,label,label_type'
            ));
        }
        
        return $tv;
    }

    private function createDatasetEntry ($p)
    {
        $val = isset($p['val']) ? $p['val'] : null;
        $nbc = isset($p['nbc']) ? $p['nbc'] : null;
        $label = isset($p['label']) ? $p['label'] : null;

        $common = isset($p['common']) ? $p['common'] : null;
        $gender = isset($p['gender']) ? $p['gender'] : null;
		
        $related = isset($p['related']) ? $p['related'] : null;
        $type = isset($p['type']) ? $p['type'] : null;
        $inclRelated = isset($p['inclRelated']) ? $p['inclRelated'] : false;
        $highlight = isset($p['highlight']) ? $p['highlight'] : false;
        $details = isset($p['details']) ? $p['details'] : null;
        $image = isset($p['image']) ? $p['image'] : null;

		$type = ($type=='variation' ? 'v' : ($type=='matrix' ? 'm' : ($type=='taxon' ? 't' : $type)));

		$sciName = 
			strip_tags(($type=='t'||$type=='m' ? $val['l'] : (isset($val['taxon']) && !is_array($val['taxon']) ? $val['taxon'] : (isset($val['taxon']['taxon']) ? $val['taxon']['taxon'] : null))));

		if (isset($nbc['url_external_page'])) {
			if (preg_match('/^(https?|ftps?):\/\//i',trim($nbc['url_external_page']['value']))===1)
				$urlExternalPage = $nbc['url_external_page']['value'];
			else
				$urlExternalPage = $this->_externalSpeciesUrlPrefix.$nbc['url_external_page']['value'];
		} else {
			$urlExternalPage = null;
		}
		
		$image = (isset($nbc['url_image']) ? $nbc['url_image']['value'] : (isset($image) ? $image : null));

        $d = array(
            'i' => $val['id'], 
            'l' => trim(strip_tags($label)), 
			'c' => $common,
            'y' => $type, 
            's' => trim(strip_tags($sciName)),
            'm' => $image, 
            'n' => isset($image),
			'x' => isset($image) ? null : $this->_nbcImageRoot.'noimage.gif',
            'b' => isset($nbc['url_thumbnail']) ? $nbc['url_thumbnail']['value'] : null, 
            'p' => isset($nbc['photographer']) ? $nbc['photographer']['value'] : null, 
            'u' => $urlExternalPage, 
			'v' => $this->_externalSpeciesUrlTarget,  // default _blank
            'r' => count((array) $related), 
            'h' => $highlight, 
            'd' => isset($details) ? $details : null
        );

		if (isset($val['taxon_id'])) $d['t'] = $val['taxon_id'];
        if (isset($gender[0])) $d['g']=$gender[0];
        if (isset($gender[1])) $d['e']=$gender[1];
        if ($inclRelated && !empty($related)) $d['related'] = $related;
        
        return $d;
    }

    private function nbcStateMemoryReformat ($d)
    {
        $states = array();
        
        foreach ((array) $d as $key => $val) {
            
            $states[$val['characteristic_id']][(isset($val['id']) ? $val['id'] : count((array) $states))] = array(
                'id' => isset($val['id']) ? $val['id'] : null, 
                'label' => isset($val['label']) ? $val['label'] : null, 
                'val' => isset($val['val']) ? $val['val'] : null, 
                'value' => isset($val['value']) ? $val['value'] : null, 
                'key' => $val['type'] == 'f' ? $val['type'] . ':' . $val['characteristic_id'] : $val['val']
            );
        }
        
        return $states;
    }

    private function fixStateLabels ($s)
    {
        
        // from "hs_zijrand_1_zijdoorn" to "1 zijdoorn"
        $shortest = null;
        foreach ((array) $s as $val) {
            if (is_null($shortest) || strlen($val['label']) < $shortest)
                $shortest = $val['label'];
        }
        
        $prefix = '';
        for ($i = strlen($shortest) - 1; $i >= 4; $i--) {
            $bit = substr($shortest, 0, $i);
            //echo $bit;
            

            $hit = true;
            foreach ((array) $s as $val) {
                if (strpos($val['label'], $bit) !== 0)
                    $hit = false;
            }
            
            //echo ':'.($hit ? 1 : 0).'<br/>';
            if ($hit && strlen($prefix) < strlen($bit)) {
                $prefix = $bit;
                //echo '<b>'.$prefix.'</b><br>';
            }
        }
        
        if (strlen($prefix) != 0)
            array_walk($s, create_function('&$elem', '$elem["label"] = preg_replace("/_/"," ",preg_replace("/^(' . $prefix . ')/","",$elem["label"]));'));
        
        return $s;
    }

    private function sortStates ($s)
    {
        uasort($s, create_function('$a,$b', 'return ($a["label"]>$b["label"]?1:($a["label"]<$b["label"]?-1:0));'));
        
        return $s;

    }



    private function nbcGetSimilar ($p = null)
    {
        if (!isset($p['type']) || !isset($p['id']))
            return;
        
        if ($p['type'] == 'v') {
            $d['vId'] = $p['id'];
        }
        else if ($p['type'] == 't') {
            $d['tId'] = $p['id'];
        }
        else
            return;
        
        $d['includeSelf'] = true;
        
        $rel = $this->getRelatedEntities($d);
        
        foreach ((array) $rel as $val) {
            
            if ($val['ref_type'] == 'variation') {
                
                $variation = $this->getVariation($val['relation_id']);
                $val['taxon'] = $this->getTaxonById($variation['taxon_id']);

                $val['taxon_id'] = $variation['taxon_id'];
                
                $nbc = $this->models->NbcExtras->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'ref_id' => $val['relation_id'], 
                        'ref_type' => 'variation'
                    ), 
                    'columns' => 'name,value', 
                    'fieldAsIndex' => 'name'
                ));
                
                $label = $val['label'];
                $val['id'] = $val['relation_id'];
                
				$d = $this->nbcExtractGenderTag($label);
				
                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'nbc' => $nbc, 
                    'label' => $d['label'], 
					'common' => $this->getCommonname($val['taxon_id']), 
                    'gender' => array($d['gender'], $d['gender_label']),
                    'type' => 'v', 
                    'highlight' => $val['id'] == $p['id'], 
                    'details' => $this->_matrixSuppressDetails ? null : $this->getVariationStates($val['relation_id'])
                ));
            }
            else {
                
                $taxon = $this->getTaxonById($val['relation_id']);

                $val['l'] = $taxon['label'];
                
                $c = $this->models->Commonname->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon_id' => $taxon['id'], 
                        'language_id' => $this->getCurrentLanguageId()
                    )
                ));
                

                $common = $val['l'];
                foreach ((array) $c as $cVal) {
                    if ($cVal['commonname'] != $val['l']) {
                        $common = $cVal['commonname'];
                        break;
                    }
                }
                
                $nbc = $this->models->NbcExtras->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'ref_id' => $val['relation_id'], 
                        'ref_type' => 'taxon'
                    ), 
                    'columns' => 'name,value', 
                    'fieldAsIndex' => 'name'
                ));
                
                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'nbc' => $nbc, 
                    'label' => $common, 
                    'type' => 't', 
                    'highlight' => $val['relation_id'] == $p['id'], 
                    'details' => $this->_matrixSuppressDetails ? null : $this->getTaxonStates($taxon['id'])
                ));
            }
        }
		
		$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));
        
        return $res;
    }

	private function nbcDoSearch($p=null)
    {
        if (!isset($p['term']))
            return;
			
		$term = mysql_real_escape_string(strtolower($p['term']));

		// n.b. don't change to 'union all'
        $q = "
			select
				'variation' as type,
				_a.variation_id as id,
				trim(_c.label) as label,
				trim(_c.label) as l,
				_c.taxon_id as taxon_id,
				_d.taxon as taxon, 
				1 as s, 
				null as commonname
			from  %PRE%matrices_variations _a        		
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
			and (lower(_c.label) like '%". $term ."%' or lower(_d.taxon) like '%". $term ."%')

			union

			select 
				'taxon' as type,
				_a.taxon_id as id, 
				trim(_c.taxon) as label, 
				trim(_c.taxon) as l, 
				_a.taxon_id as taxon_id,
				_c.taxon as taxon, 
				1 as s, 
				_d.commonname as commonname
			from %PRE%matrices_taxa _a
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
				and (lower(_c.taxon) like '%". $term ."%' or lower(_d.commonname) like '%". $term ."%')
				";

        $results = $this->models->MatrixTaxonState->freeQuery($q);

        if (!$results)
			return null;
		
		usort($results, array($this, 'sortQueryResultsByScoreThenLabel'));

		$res = $tmp = array();
		$i = 0;
		
		foreach((array)$results as $val) {

			if ($val['type']=='taxon' && isset($tmp[$val['id']]))
				continue;

			if ($val['type']=='variation')
			{
				$d=$this->nbcExtractGenderTag($val['label']);
				$gender=array($d['gender'], $d['gender_label']);
				$common=$this->getCommonname($val['taxon_id']);
			}
			else
			{
				$gender=array();

				if ($val['commonname'] != $val['label'])
					$label = $val['commonname'];

				$common = $val['commonname'];

			}

			$res[$i] = $this->createDatasetEntry(
				array(
					'val' => $val, 
					'nbc' => $this->models->NbcExtras->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(), 
								'ref_id' => $val['id'], 
								'ref_type' => $val['type']
							), 
							'columns' => 'name,value', 'fieldAsIndex' => 'name'
						)
					), 
					'label' => $val['label'], 
					'common' => $common,
					'gender' => $gender,
					'related' => $this->getRelatedEntities(array(($val['type']=='variation' ? 'vId' : 'tId') => $val['id'])), 
					'type' => $val['type'], 
					'inclRelated' => true,
					'details' => ($val['type']=='variation' ? $this->getVariationStates($val['id']) : $this->getTaxonStates($val['id']))
				)
			);

			// post processing; createDatasetEntry() strips tages, hence.
			$res[$i]['l'] = preg_replace_callback(
				'/(' . $term . ')/i', 
				create_function('$matches', 'if (trim($matches[0]) == "") return $matches[0]; else return "<span class=\"seachStringHighlight\">".$matches[0]."</span>";'), 
				$res[$i]['l']
			);

			$res[$i]['s'] = preg_replace_callback(
				'/(' . $term . ')/i', 
				create_function('$matches', 'if (trim($matches[0]) == "") return $matches[0]; else return "<span class=\"seachStringHighlight\">".$matches[0]."</span>";'), 
				$res[$i]['s']
			);

			if ($val['type']=='variation')
				$tmp[$val['taxon_id']] = true;
				
			$i++;
                    
		}
		
		$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));

        return $res;
		
    }
 
 	private function nbcHandleOverlappingItemsFromDetails($p)
	{
		//return $p['data'];

        $data = isset($p['data']) ? $p['data'] : null;
        $action = isset($p['action']) ? $p['action'] : 'remove';

		if (count((array)$data)==1)
			return $data;

		$d = array();
		foreach((array)$data as $key => $dVal) {
			foreach((array)$dVal['d'] as $characteristic_id => $cVal)	{
				foreach((array)$cVal['states'] as $state)	{
					if (isset($state['id']))
						$d[$key][] = $characteristic_id.':'.$state['id']; // characteristic_id:state_id
				}
			}
		}

		$common = call_user_func_array('array_intersect',$d);

		foreach((array)$data as $key => $dVal) {
			foreach((array)$dVal['d'] as $characteristic_id => $cVal) {
				foreach((array)$cVal['states'] as $sVal => $state)	{
					
					if (isset($state['id'])) {

						if (in_array($characteristic_id.':'.$state['id'],$common)) {
							if ($action=='remove') {
								unset($data[$key]['d'][$characteristic_id]['states'][$sVal]);
							} else
							if ($action=='tag') {
								$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'] = '<span class="overlapState">'.$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'].'</span>';
							}
						}
						
					}
					
				}
				
				if (count((array)$data[$key]['d'][$characteristic_id]['states'])==0 && $action=='remove') {

					unset($data[$key]['d'][$characteristic_id]);

				}
			}
		}

		return $data;
		
	}

	private function getRelevantCoefficients($states=null)
	{

		$smallIsSignificant = true;

		$res = $this->getRemainingStateCount(array('states' => $states, 'groupByCharId' => true));

		$l1['s'] = $l2['s'] = $l3['s'] = ($smallIsSignificant ? 1 : 0);
		
		foreach ((array)$res as $key => $val) {
			$c = $this->getCharacteristic(array('id'=>$key));

            if ($c['type'] != 'media' && $c['type'] != 'text')
				continue;
			
			$v = $this->getCharacteristicHValue($key);
			$res[$key]['separationCoefficient'] = $v;
			if (($smallIsSignificant && $v<$l3['s']) || (!$smallIsSignificant && $v>$l3['s'])) {
				if (($smallIsSignificant && $v<$l2['s']) || (!$smallIsSignificant && $v>$l2['s'])) {
					if (($smallIsSignificant && $v<$l1['s']) || (!$smallIsSignificant && $v>$l1['s'])) {
						$l1['s']=$v;
						$l1['i']=$key;
					} else {
						$l2['s']=$v;
						$l2['i']=$key;
					}
				} else {
					$l3['s']=$v;
					$l3['i']=$key;
				}
			}
			unset($res[$key]['states']);
		}
		
		$res[$l1['i']]['rank']=1;
		$res[$l2['i']]['rank']=2;
		$res[$l3['i']]['rank']=3;
		
		return $res;

	}





}	