<?php
/*


	quit working on 4 november due to more pressing obligations.
	import works, except states of range characters are somehow
	saved with the wrong charcater id. fuck knows why.





// vermoeden dat er dingen ontbreken die wel in het lucid file staan
//hierarchy => q($xml->Dataset->TaxonHierarchies,1); // there is none - for now...
//		q($xml->Dataset->CharacterTrees,1); ignoring character tree, defaulting to group -> char -> state
TRIED SINGLE TREE ONLY!

EVERYTHING IS SPECIES!

min/ Umethmin (whatever) reduced to one value (idem max)
measurement lables &units get lost

multi-level characters reduced to a single character, top level to group

*/

include_once ('ImportController.php');

class ImportSDDController extends ImportController
{

	private $_defaultKingdom = '(import generated master taxon)';
    public $controllerPublicName = 'SDD file Import';
	public $controllerPublicNameMask="Matrix key";
	private $_useGroupsAsUpperTreeLevel = false;//true;

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
        error_reporting(E_ERROR | E_PARSE);
		set_time_limit(2400); // RIGHT!
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    public function sddImport1Action ()
    {

        $this->checkAuthorisation();

        if ($this->rHasVal('process', '1'))
            $this->redirect('sdd_import_2.php');

        $this->setPageName($this->translate('SDD Import'));

        if (isset($this->requestDataFiles[0]) && !$this->rHasVal('clear', 'file'))
		{
            $tmp = tempnam(sys_get_temp_dir(), 'lng');

            if (copy($this->requestDataFiles[0]['tmp_name'], $tmp)) {

                $_SESSION['admin']['system']['import']['file'] = array(
                    'path' => $tmp,
                    'name' => $this->requestDataFiles[0]['name'],
                    'src' => 'upload'
                );
            }
            else
			{
                unset($_SESSION['admin']['system']['import']['file']);
            }
        }
        else 
		if ($this->rHasVal('serverFile') && !$this->rHasVal('clear', 'file'))
		{
            if (file_exists($this->rGetVal('serverFile')))
			{
                $_SESSION['admin']['system']['import']['file'] = array(
                    'path' => $this->rGetVal('serverFile'),
                    'name' => $this->rGetVal('serverFile'),
                    'src' => 'existing'
                );
            }
            else
			{
                $this->addError('File "' . $this->rGetVal('serverFile') . '" does not exist.');
                unset($_SESSION['admin']['system']['import']['file']);
            }
        }

        if ($this->rHasVal('imagePath') || $this->rHasVal('noImages'))
		{
            if ($this->rHasVal('noImages'))
			{
                $_SESSION['admin']['system']['import']['imagePath'] = false;
            }
            else 
			if (file_exists($this->rGetVal('imagePath')))
			{
                $_SESSION['admin']['system']['import']['imagePath'] = rtrim($this->rGetVal('imagePath'), '/') . '/';

            }
            else
			{
                $this->addError('Image path "' . $this->rGetVal('imagePath') . '" does not exist or unreachable.');
                unset($_SESSION['admin']['system']['import']['imagePath']);
            }
        }

		$_SESSION['admin']['system']['import']['thumbsPath'] = false;

        if ($this->rHasVal('clear', 'file'))
		{
            unset($_SESSION['admin']['system']['import']['file']);
            unset($_SESSION['admin']['system']['import']['raw']);
        }

        if ($this->rHasVal('clear', 'imagePath'))
            unset($_SESSION['admin']['system']['import']['imagePath']);

        if ($this->rHasVal('clear', 'thumbsPath'))
            unset($_SESSION['admin']['system']['import']['thumbsPath']);

        if (isset($_SESSION['admin']['system']['import']))
            $this->smarty->assign('s', $_SESSION['admin']['system']['import']);

        clearstatcache(true, $this->generalSettings['directories']['mediaDirProject']);

        $this->smarty->assign('mediaDir', $this->generalSettings['directories']['mediaDirProject']);

        $this->smarty->assign('isSharedMediaDirWritable', is_writable($this->generalSettings['directories']['mediaDirProject']));

        $this->printPage();
    }



    public function sddImport2Action ()
    {
        $this->checkAuthorisation();

        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('sdd_import_1.php');

        $this->setPageName($this->translate('SDD Import Results'));

        if ($_SESSION['admin']['system']['import']['file']['path']  && !$this->isFormResubmit()) {

			$xml = simplexml_load_string(file_get_contents($_SESSION['admin']['system']['import']['file']['path']));

			// matrix name
			$matrixname=(string)$xml->Dataset->Representation->Label;



			// taxa [t1]
			$taxa=array();
			$files_to_copy=array();
			foreach($xml->Dataset->TaxonNames->TaxonName as $val)
			{

				$m=array();
				foreach($val->Representation->MediaObject as $media) {
					$d=explode(':',(string)$media['debuglabel'],2);
					$m[$d[0]]=$d[1];
				}

				$taxa[(string)$val['id']]=array(
					'label' => (string)$val->Representation->Label,
					'media' => $m
				);

			}



			// descriptive concepts (labels) [dc1]
			$DescriptiveConcepts=array();
			foreach($xml->Dataset->DescriptiveConcepts->DescriptiveConcept as $val)
			{
				// ignoring q($val->Modifiers);
				$DescriptiveConcepts[(string)$val['id']]['label']=(string)$val->Representation->Label;
			}



			// characters [c1] & states
			$characters=array();
			foreach($xml->Dataset->Characters->CategoricalCharacter as $val)
			{

				$s=$m=array();
				$type='text';
				foreach($val->States->StateDefinition as $state) {
					if (isset($state->Representation->MediaObject['ref']) && isset($state->Representation->MediaObject['debuglabel'])) {
						$d=explode(':',(string)$state->Representation->MediaObject['debuglabel'],2);
						$m[(string)$state->Representation->MediaObject['ref']]=$d[1];
					} else
						$m=null;

					$s[(string)$state['id']]=array(
						'label'=>(string)$state->Representation->Label,
						'media'=>$m
						);
					if (!is_null($m)) $type='media';
					$m=array();
				}

				$characters[(string)$val['id']]=array(
					'type'=>$type,
					'label'=>(string)$val->Representation->Label,
					'states'=>$s
				);

			}

			foreach($xml->Dataset->Characters->QuantitativeCharacter as $val)
			{

				foreach($val->MeasurementUnit->Label as $key => $label)
					if ($key==0) break;

				$characters[(string)$val['id']]=array(
					'type'=>'range',
					'label'=>(string)$val->Representation->Label,
					'unit'=>(string)$label
				);

			}



			// nodes - but as we don't have a tree in LNG, we'll transform the character's names (and later turn level 1 into a group)
			$nodes=$characterindex=array();
			$i=0;
			foreach($xml->Dataset->CharacterTrees->CharacterTree as $val)
			{

				foreach($val->Nodes->children() as $nVal) {
					$charref=(string)$nVal->Character['ref'];
					$dcref=(string)$nVal->DescriptiveConcept['ref'];
					$attr=$nVal->attributes();
					$nodes[$i]=array(
						'id'=>(string)$nVal['id'],
						'label'=>(string)$nVal['debuglabel'],
						'ref'=>$dcref,
						'char'=>$charref,
						'parent'=>(string)$nVal->Parent['ref'],
					);

					if (isset($DescriptiveConcepts[$dcref]['label'])) {
						$nodes[$i]['label']=$DescriptiveConcepts[$dcref]['label'];
					}

					if (!empty($charref))
						$characterindex[$charref]=$i;
					$i++;

				}

			}

			function getNodeById($nodes,$id)
			{
				foreach((array)$nodes as $node) {
					if ($node['id']==$id) {
						return $node;
					}
				}
			}

			//transform the character's names (and later turn level 1 into a group)
			foreach((array)$characters as $key=>$char)
			{
				$thisnode=$nodes[$characterindex[$key]];

				$parent=$thisnode['parent'];
				$prefix='';
				while(!empty($parent)) {
					$d=getNodeById($nodes,$parent);
					$parent=$d['parent'];
					$prefix=$d['label'].chr(31).$prefix;
				}
				$characters[$key]['prefix']=$prefix;
			}

			$groups=array();

			if ($this->_useGroupsAsUpperTreeLevel)
			{

				// ah well, let's turn the first bit into groups (and fix the separators)
				foreach((array)$characters as $key=>$char)
				{
					$d=explode(chr(31),$char['prefix']);

					if (count($d)>0) {
						$g=array_shift($d);
						$characters[$key]['prefix']=trim(implode(':',$d));

						if (!empty($g))
							$characters[$key]['group']=$groups[$g]=$g;
						else
							$characters[$key]['group']=null;

					} else {
						$characters[$key]['prefix']=null;
						$characters[$key]['group']=null;
					}

				}

			}
				else
			{

				foreach((array)$characters as $key=>$char)
				{
					$characters[$key]['prefix']=str_replace(chr(31),':',$char['prefix']);
				}

			}


			// taxon / state relations
			$coded_descriptions=array();
			foreach($xml->Dataset->CodedDescriptions->CodedDescription as $val)
			{

				$taxon=(string)$val->Scope->TaxonName['ref']; // local taxon id

				$coded_descriptions[$taxon]=array();
				$quantitative_states[$taxon]=array();

				foreach($val->SummaryData->Categorical as $cVal)
				{
					//echo '==',$cVal['ref'],'===>',chr(10);  // local character id
					foreach($cVal->State as $mVal)
					{
						//echo $mVal['ref'],chr(10);
						$categorical_states[(string)$cVal['ref']][]=(string)$mVal['ref'];
					}
				}

				$coded_descriptions[$taxon]['categorical_states']=$categorical_states;
				unset($categorical_states);

				foreach($val->SummaryData->Quantitative as $cVal)
				{

					//echo '==',$cVal['ref'],'===>',chr(10);  // local character id -> ignored, as states have unique id's
					$d=array();

					foreach($cVal->Measure as $mVal)
					{
						//echo $mVal['type'],':',$mVal['value'],chr(10);
						$d[(string)$mVal['type']]=(string)$mVal['value'];
					}

					$quantitative_states[(string)$cVal['ref']]=array(
						'min'=>min(array($d['Min'],$d['UMethLower'])),
						'max'=>max(array($d['Max'],$d['UMethUpper'])),
					);

				}

				$coded_descriptions[$taxon]['quantitative_states']=$quantitative_states;
				unset($quantitative_states);

			}

			// media
			$media_objects=array();
			foreach($xml->Dataset->MediaObjects->MediaObject as $val)
			{
				$file=basename((string)$val->Source['href']);
				$media_objects[(string)$val['id']]=array(
					'label'=>(string)$val->Representation->Label,
					'detail'=>(string)$val->Representation->Detail,
					'type'=>(string)$val->Type,
					'href'=>(string)$val->Source['href'],
					'basename'=>$file
				);
			}

			$pr = $this->models->ProjectsRanks->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'rank_id' => SPECIES_RANK_ID
				)
			));


			$speciesRankId=$pr[0]['id'];

			if (count($taxa)==0)
			{

				$this->addError('Found no taxa (import stopped)');

			} else
			if (empty($speciesRankId))
			{

				$this->addError('Project has no Species rank (import stopped)');

			} else
			if (count($characters)==0)
			{

				$this->addError('Found no characters (import stopped)');

			} else
			if (count($coded_descriptions)==0)
			{

				$this->addError('Found no taxon/state-relations (import stopped)');

			} else
			{

				$mId=$this->createMatrix(array('matrixname'=>$matrixname));
				$this->addMessage('Created matrix "'.$matrixname.'"');

				foreach((array)$groups as $key => $val)
				{

					$this->models->Chargroups->save(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'matrix_id' => $mId,
							'label' => $key,
							'show_order' => 99
					));

					$groups[$key]=$this->models->Chargroups->getNewId();

					$this->models->ChargroupsLabels->save(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'chargroup_id' => $groups[$key],
							'label' => $key,
							'language_id' => $this->getDefaultProjectLanguage()
					));

					$this->addMessage('Created group "'.$key.'"');

				}


				$show_order=0;
				$state_list=array();
				foreach ((array)$characters as $key => $val)
				{

					$characters[$key]['id'] = $this->createMatrixCharacter(
						array(
							'type'=>$val['type'],
							'label'=>$val['prefix'].$val['label'],
							'matrix_id'=>$mId,
							'showOrder'=>$show_order++
						)
					);

					if (isset($val['group'])) {

						$this->models->CharacteristicsChargroups->save(array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'chargroup_id' => $groups[$val['group']],
							'characteristic_id' =>$characters[$key]['id'],
							'show_order' => $show_order
						));

					}

					if (isset($val['states'])) {

						//saving states
						foreach ((array)$val['states'] as $sKey => $sVal) {

							$d=array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(),
								'characteristic_id' => $characters[$key]['id'],
								'got_labels' => 1
							);

							if (isset($sVal['media'])) {
								// why loop? we can only store one image per state! but... who knows what the future will bring.
								foreach((array)$sVal['media'] as $mkey=>$mVal) {
									// we're not checking existence just dumbly copying image names. pwah.
									$files_to_copy[]=$d['file_name']=basename($mVal);
								}
							}

							$this->models->CharacteristicsStates->save($d);

							$characters[$key]['states'][$sKey]['id']=
								$state_list[$sKey]=
								$this->models->CharacteristicsStates->getNewId();

							$this->models->CharacteristicsLabelsStates->save(
							array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(),
								'state_id' => $characters[$key]['states'][$sKey]['id'],
								'language_id' => $this->getDefaultProjectLanguage(),
								'label' => $sVal['label']
							));


						}

						$this->addMessage('Created character "'.$val['prefix'].$val['label'].'" with '.count((array)$val['states']).' states');

					} else {

						$this->addMessage('Created character "'.$val['prefix'].$val['label'].'"');
					}



				}

				$d = $this->models->Taxa->_get(array('id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon' => $this->_defaultKingdom
					)));

				if ($d) {

					$kingdomId = $d[0]['id'];

				} else {

					$pr = $this->models->ProjectsRanks->_get(array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'rank_id' => REGNUM_RANK_ID
						)
					));

					// default kingdom
					$this->models->Taxa->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(),
						'taxon' => $this->_defaultKingdom,
						'parent_id' => 'null',
						'rank_id' => $pr[0]['id'],
						'taxon_order' => 0,
						'is_hybrid' => 0,
						'list_level' => 0
					));

					$kingdomId = $this->models->Taxa->getNewId();

				}


				$taxon_list=array();
				foreach((array)$taxa as $key=>$val)
				{
					if (empty($val['label'])) {
						$this->addMessage('skipping empty taxon ('.$key.')');
						continue;
					}

					$t=$this->getTaxonByName($val['label']);

					if (empty($t)) {

						$d = $this->models->Taxa->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'taxon' => $val['label'],
							'rank_id' => $speciesRankId,
							'parent_id' => $kingdomId
						));

						$taxonId=$this->models->Taxa->getNewId();
						$this->addMessage('Created taxon "'.$val['label'].'"');

					} else {

						$taxonId=$t['id'];

					}

					$this->models->MatricesTaxa->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $mId,
						'taxon_id' => $taxonId
					));

					$taxon_list[$key]=$taxonId;
					$o=0;

					foreach((array)$val['media'] as $mVal)
					{

						if(filter_var($mVal, FILTER_VALIDATE_URL) !== FALSE)
						{

							$this->models->NbcExtras->save(
								array(
									'id' => null,
									'project_id' => $this->getCurrentProjectId(),
									'ref_id' => $taxonId,
									'ref_type' => 'taxon',
									'name' => 'URL',
									'value' => $mVal
								));

						} else {

							$file=basename($mVal);
							$mime=$this->mimeContentType($file);


							$mt = $this->models->MediaTaxon->save(
							array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(),
								'taxon_id' => $taxonId,
								'file_name' => $file,
								'original_name' => $file,
								'mime_type' => $mime,
								'file_size' => -1,
								'thumb_name' => null,
								'sort_order' => $o++
							));

							$files_to_copy[]=$file;

						}

					}

				}

				$this->models->MatricesTaxa->setNoKeyViolationLogging(true);
				$this->models->MatricesTaxaStates->setNoKeyViolationLogging(true);

				$i=0;
				foreach ((array)$coded_descriptions as $taxon => $val)
				{

					if (!isset($taxon_list[$taxon]))
						continue;

					$tId = $taxon_list[$taxon];

					foreach ($val['categorical_states'] as $char => $states)
					{

						$cId=$characters[$char]['id'];

						if (is_array($states)) {

							foreach ($states as $state)
							{

								$sId=$state_list[$state];

								$this->models->MatricesTaxaStates->insert(
									array(
										'project_id' => $this->getCurrentProjectId(),
										'matrix_id' => $mId,
										'characteristic_id' => $cId,
										'state_id' => $sId,
										'taxon_id' => $tId
									));

								$i++;

							}

						}

					}

					foreach ($val['quantitative_states'] as $char => $sVal)
					{

						if (!isset($characters[$char]['id']) || !isset($sVal['min']) || !isset($sVal['max']))
							continue;

						$cId=$characters[$char]['id'];

						$cs = $this->models->CharacteristicsStates->_get(array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'characteristic_id' => $cId,
								'lower' => $sVal['min'],
								'upper' => $sVal['max']
							)
						));

						if ($cs) {

							$sId=$cs[0]['id'];

						} else {

							$this->models->CharacteristicsStates->save(
							array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(),
								'characteristic_id' => $cId,
								'lower' => $sVal['min'],
								'upper' => $sVal['max']
							));

							$sId=$this->models->CharacteristicsStates->getNewId();

							$this->models->CharacteristicsLabelsStates->save(
							array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(),
								'state_id' => $sId,
								'language_id' => $this->getDefaultProjectLanguage(),
								'label' =>  $sVal['min'].'-'.$sVal['max'].' '.$characters[$sKey]['unit']
							));

						}

						$this->models->MatricesTaxaStates->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'matrix_id' => $mId,
							'characteristic_id' => $cId,
							'state_id' => $sId,
							'taxon_id' => $tId
						));


						$i++;

					}

				}

				if (isset($_SESSION['admin']['system']['import']['imagePath'])) {
					$j=0;
					foreach((array)$files_to_copy as $val) {
						$src=$_SESSION['admin']['system']['import']['imagePath'].$val;
						$tgt=$this->getProjectsMediaStorageDir().$val;
						if (file_exists($src)) {
							if (copy($src,$tgt))
								$j++;
							else
								$this->addError(sprintf('Could not copy "%s"',$val));

						} else {
							$this->addError(sprintf('File does not exist: %s',$src));
						}
					}
				} else {
					$this->addMessage('Skipped copying images');
				}

				$this->smarty->assign('matrix',$mId);
				$this->addMessage('Created '.$i.' taxon/state-connections');
				$this->addMessage('Copied '.$j.' images');

			}

	    } else {

			$this->addError('?');

		}

		$this->printPage();

	}

}
