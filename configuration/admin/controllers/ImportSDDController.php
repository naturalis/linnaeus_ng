<?php
/*


// vermoeden dat er dingen ontbreken die wel in het lucid file staan
//hierarchy => q($xml->Dataset->TaxonHierarchies,1); // there is none - for now...
//		q($xml->Dataset->CharacterTrees,1); ignoring character tree, defaulting to group -> char -> state
TRIED SINGLE TREE ONLY!

EVERYTHING IS SPECIES!

min/ Umethmin (whatever) reduced to one value (idem max)
measurement lables &units get lost

*/

include_once ('Controller.php');

class ImportSDDController extends Controller
{
    public $usedModels = array(
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
        'characteristic_label_state', 
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup', 
        'matrix_variation',
		'gui_menu_order',
		'nbc_extras',
		'media_taxon'
    );
    public $usedHelpers = array(
        'file_upload_helper', 
        'xml_parser'
    );
    public $controllerPublicName = 'Linnaeus 2 Import';
    public $cssToLoad = array(
        'import.css'
    );
    public $jsToLoad = array();
    private $_deleteOldMediaAfterImport = false; // might become a switch later, but let's not overdo it
    private $_knownModules = array(
        'file', 
        'project', 
        'proj_literature', 
        'glossary', 
        'introduction', 
        'tree', 
        'records', 
        'text_key', 
        'pict_key', 
        'diversity'
    );
    private $_sawModule = false;
	private $_retainInternalLinks = false; // keep false, as internal links are re-created by means of hotwords
	
	private $_tempTeller = 0;


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
        
        error_reporting(E_ERROR | E_PARSE);
        
        $this->setBreadcrumbRootName($this->translate('Data import'));
        
    }



    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        parent::__destruct();
    }



    public function sddImport1Action ()
    {
		
        $this->checkAuthorisation();

        if ($this->rHasVal('process', '1'))
            $this->redirect('sdd_import_2.php');
        
        if (isset($this->requestDataFiles[0]) && !$this->rHasVal('clear', 'file')) {
            
            $tmp = tempnam(sys_get_temp_dir(), 'lng');
            
            if (copy($this->requestDataFiles[0]['tmp_name'], $tmp)) {
                
                $_SESSION['admin']['system']['import']['file'] = array(
                    'path' => $tmp, 
                    'name' => $this->requestDataFiles[0]['name'], 
                    'src' => 'upload'
                );
            }
            else {
                
                unset($_SESSION['admin']['system']['import']['file']);
            }
        }
        else if ($this->rHasVal('serverFile') && !$this->rHasVal('clear', 'file')) {
            
            if (file_exists($this->requestData['serverFile'])) {
                
                $_SESSION['admin']['system']['import']['file'] = array(
                    'path' => $this->requestData['serverFile'], 
                    'name' => $this->requestData['serverFile'], 
                    'src' => 'existing'
                );
            }
            else {
                
                $this->addError('File "' . $this->requestData['serverFile'] . '" does not exist.');
                unset($_SESSION['admin']['system']['import']['file']);
            }
        }
        
        if ($this->rHasVal('imagePath') || $this->rHasVal('noImages')) {
            
            if ($this->rHasVal('noImages')) {
                
                $_SESSION['admin']['system']['import']['imagePath'] = false;
            }
            else if (file_exists($this->requestData['imagePath'])) {
                
                $_SESSION['admin']['system']['import']['imagePath'] = rtrim($this->requestData['imagePath'], '/') . '/';
                
            }
            else {
                
                $this->addError('Image path "' . $this->requestData['imagePath'] . '" does not exist or unreachable.');
                unset($_SESSION['admin']['system']['import']['imagePath']);
            }
        }
        
		$_SESSION['admin']['system']['import']['thumbsPath'] = false;
        
        if ($this->rHasVal('clear', 'file')) {
            
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
        $this->checkAuthorisation(true);

        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('sdd_import_1.php');

        $this->setPageName($this->translate('SDD file upload'));
        
        if ($_SESSION['admin']['system']['import']['file']['path']) {
		
			$xml=simplexml_load_file($_SESSION['admin']['system']['import']['file']['path']);
			
			$matrixname=(string)$xml->Dataset->Representation->Label;
			$taxa=array();
	
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
			
			$groups=array();
		
			foreach($xml->Dataset->DescriptiveConcepts->DescriptiveConcept as $val)
			{
				// ignoring q($val->Modifiers);
				$groups[(string)$val['id']]['label']=(string)$val->Representation->Label;
			}
	
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
	
			$parentage=array();
	
			foreach($xml->Dataset->CharacterTrees->CharacterTree as $val)
			{
				foreach($val->Nodes->Node as $nVal)
				{
					$parentage[(string)$nVal['id']]=(string)$nVal->DescriptiveConcept['ref'];
				}
				foreach($val->Nodes->CharNode as $nVal)
				{
					if (isset($parentage[(string)$nVal->Parent['ref']]))
						$characters[(string)$nVal->Character['ref']]['group']=$parentage[(string)$nVal->Parent['ref']];
					else
						$characters[(string)$nVal->Character['ref']]['group']=null;
				}
			}
	
			$coded_descriptions=array();
	
			foreach($xml->Dataset->CodedDescriptions->CodedDescription as $val)
			{
	
				$taxon=(string)$val->Scope->TaxonName['ref']; // local taxon id
				
				$coded_descriptions[$taxon]=array();
				$quantitative_states=array();
	
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
				
	
			}
	
			$media_objects=array();
	
			foreach($xml->Dataset->MediaObjects->MediaObject as $val)
			{
				$media_objects[(string)$val['id']]=array(
					'label'=>(string)$val->Representation->Label,
					'detail'=>(string)$val->Representation->Detail,
					'type'=>(string)$val->Type,
					'href'=>(string)$val->Source['href'],
					'basename'=>basename ((string)$val->Source['href'])
				);
	
			}
	

			if (count($taxa)==0)
			{

				$this->addError('found no taxa (import stopped)');

			} else
			if (count($characters)==0)
			{

				$this->addError('found no characters (import stopped)');

			} else
			if (count($coded_descriptions)==0)
			{

				$this->addError('found no taxon/state-relations (import stopped)');

			} else {

				//creating matrix
				$this->models->Matrix->save(array(
					'id' => null, 
					'project_id' => $this->getCurrentProjectId(), 
					'got_names' => 1
				));
            
				$mId = $this->models->Matrix->getNewId();

				$this->models->MatrixName->save(
				array(
					'id' => null, 
					'project_id' => $this->getCurrentProjectId(), 
					'matrix_id' => $mId, 
					'language_id' => $this->getDefaultProjectLanguage(), 
					'name' => $matrixname 
				));

				$this->addMessage('created matrix "'.$matrixname.'"');

				foreach((array)$groups as $key => $val)
				{

					$this->models->Chargroup->save(
						array(
							'project_id' => $this->getCurrentProjectId(), 
							'matrix_id' => $mId,
							'label' => $val['label'],
							'show_order' => 99
					));
					
					$groups[$key]['id']=$this->models->Chargroup->getNewId();
		
					$this->models->ChargroupLabel->save(
						array(
							'project_id' => $this->getCurrentProjectId(), 
							'chargroup_id' => $groups[$key]['id'],
							'label' => $label,
							'language_id' => $this->getDefaultProjectLanguage()
					));	
					
					$this->addMessage('created group "'.$val['label'].'"');	
					
				}
				

				$show_order=0;
				$state_list=array();
				foreach ((array)$characters as $key => $val)
				{
					
					$this->models->Characteristic->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'type' => $val['type'], 
						'got_labels' => 1
					));
	
					$characters[$key]['id'] = $this->models->Characteristic->getNewId();

					$this->models->CharacteristicLabel->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'characteristic_id' => $characters[$key]['id'], 
						'language_id' => $this->getDefaultProjectLanguage(), 
						'label' => $val['label']
					));
					
					$this->models->CharacteristicMatrix->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'matrix_id' => $mId, 
						'characteristic_id' => $characters[$key]['id'], 
						'show_order' => $show_order++
					));
					
					if (isset($val['group'])) {

						$this->models->CharacteristicChargroup->save(array(
							'id' => null, 
							'project_id' => $this->getCurrentProjectId(), 
							'chargroup_id' => $groups[$val['group']]['id'], 
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
									$d['file_name']=basename($mVal);
								}
							}
		
							$this->models->CharacteristicState->save($d);
							
							$characters[$key]['states'][$sKey]['id']=
								$state_list[$sKey]=
								$this->models->CharacteristicState->getNewId();
						
							$this->models->CharacteristicLabelState->save(
							array(
								'id' => null, 
								'project_id' => $this->getCurrentProjectId(), 
								'state_id' => $characters[$key]['states'][$sKey]['id'], 
								'language_id' => $this->getDefaultProjectLanguage(), 
								'label' => $sVal['label']
							));
	
		
						}
						
					}
					
					$this->addMessage('created charater "'.$val['label'].'" with '.count((array)$val['states']).' states');
					
				}


				$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
				$taxon_list=array();
				foreach((array)$taxa as $key=>$val)
				{
					if (empty($val['label'])) {
						$this->addMessage('skipping empty taxon ('.$key.')');
						continue;
					}
					
					$t=$this->getTaxonByName($val['label']);

					if (empty($t)) {

						$d = $this->models->Taxon->save(
						array(
							'id' => null, 
							'project_id' => $this->getCurrentProjectId(), 
							'taxon' => $val['label']
						));
						
						$taxonId=$this->models->Taxon->getNewId();
						$this->addMessage('created taxon "'.$val['label'].'"');

					} else {
						
						$taxonId=$t['id'];

					}

					$this->models->MatrixTaxon->save(
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
							
							$mime=finfo_file($finfo,basename($mVal));
	
							$mt = $this->models->MediaTaxon->save(
							array(
								'id' => null, 
								'project_id' => $this->getCurrentProjectId(), 
								'taxon_id' => $taxonId, 
								'file_name' => basename($mVal),
								'original_name' => basename($mVal),
								'mime_type' => $mime, 
								'file_size' => -1, 
								'thumb_name' => null, 
								'sort_order' => $o++
							));					
	
						}


					}
				
				}
				finfo_close($finfo);

				foreach ((array)$coded_descriptions as $taxon => $val)
				{

					if (!isset($taxon_list[$taxon]))
						continue;

					foreach ((array)$val['categorical_states'] as $sKey => $sVal)
					{
						
						if (!isset($characters[$sKey]['id']))
							continue;

						foreach ((array)$sVal as $WHATEVER)
						{
							
							$this->models->MatrixTaxonState->save(
							array(
								'id' => null, 
								'project_id' => $this->getCurrentProjectId(), 
								'matrix_id' => $mId, 
								'characteristic_id' => $characters[$sKey]['id'],
								'state_id' => $state_list[$WHATEVER],
								'taxon_id' => $taxon_list[$taxon]
							));	

						}
						
					}
					
					foreach ((array)$val['quantitative_states'] as $sKey => $sVal)
					{
	
						if (!isset($characters[$sKey]['id']) || !isset($sVal['min']) || !isset($sVal['max']))
							continue;
							
						$cs = $this->models->CharacteristicState->_get(array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'characteristic_id' => $characters[$sKey]['id'], 
								'lower' => $sVal['min'],
								'upper' => $sVal['max']
							)
						));
						
					
						if ($cs) {

							$sId=$cs[0]['id'];

						} else {

							$this->models->CharacteristicState->save(
							array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(), 
								'characteristic_id' => $characters[$sKey]['id'], 
								'lower' => $sVal['min'],
								'upper' => $sVal['max']
							));
							
						
							
							$sId=$this->models->CharacteristicState->getNewId();

							$this->models->CharacteristicLabelState->save(
							array(
								'id' => null, 
								'project_id' => $this->getCurrentProjectId(), 
								'state_id' => $sId, 
								'language_id' => $this->getDefaultProjectLanguage(), 
								'label' =>  $sVal['min'].'-'.$sVal['max'].' '.$characters[$sKey]['unit']
							));
									
						}
						
						$this->models->MatrixTaxonState->save(
						array(
							'id' => null, 
							'project_id' => $this->getCurrentProjectId(), 
							'matrix_id' => $mId, 
							'characteristic_id' => $characters[$sKey]['id'],
							'state_id' =>$sId,
							'taxon_id' =>$taxon_list[$taxon]
						));	
						

					}

						
					
				}

				//$media_objects;
			
			
			}

	    } else {

			$this->addError('?');

		}

		$this->printPage();

	}

}
