<?php /** @noinspection PhpMissingParentCallMagicInspection */
/*

	export from excel NOT as csv, but as tab-delimited text
	
	what we expect:
		tab as field sep
		'Species',

	//REFAC2015: finish these
	// check functions
	v	check_boolean
	v	check_stringlist
	v	check_stringlistfree
	v	check_stringfree
	check_datelist
	check_datelistfree
	v	check_datefree
	check_datefreelimit
	check_intlist
	check_intlistfree
	check_intfree
	check_intfreelimit
	check_floatlist
	check_floatlistfree
	check_floatfree
	check_floatfreelimit
*/
	
include_once ('TraitsController.php');

class TraitsDataController extends TraitsController
{
    public $usedModels = array(
		'traits_settings',
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'text_translations',
		'traits_values',
		'traits_taxon_values',
		'traits_taxon_freevalues',
		'traits_taxon_references',
		'literature2'
    );
   
    public $controllerPublicName = 'Traits';
	public $controllerBaseName='traits';
    public $modelNameOverride='TraitsDataModel';

    public $cacheFiles = array();
    
    public $cssToLoad = array(
		'traits.css',
//		'taxon_groups.css'
	);

	public $jsToLoad=array(
        'all' => array('traits.js','jquery.mjs.nestedSortable.js')
	);

    public $usedHelpers = array(
        'session_messages',
		//'encoding'
    );
	
	private $_referenceList=array();

    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->moduleSession->setModule( array('environment'=>'admin','controller'=>$this->controllerBaseName) );
		$this->smarty->assign('sysColSpecies',$this->_sysColSpecies);
		$this->smarty->assign('sysColReferences',$this->_sysColReferences);
		$this->smarty->assign('sysColNsrId',$this->_sysColNsrId);
    }
	
	public function translateDelimiter( $label )
	{
		if ($label=="comma") return ",";
		if ($label=="semi-colon") return ";";
		if ($label=="tab") return "\t";
		return $this->_inputFileFieldSeparator;
	}

    public function dataUploadAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Data upload'));

        if (isset($this->requestDataFiles[0]))
		{
            $tmp = tempnam(sys_get_temp_dir(),'lng');
            
            if (copy($this->requestDataFiles[0]['tmp_name'], $tmp))
			{
                $this->setDataSession(
					array(
						'path'=>$tmp,
						'name'=>$this->requestDataFiles[0]['name'],
						'status'=>'raw',
						'traitgroup'=>$this->rGetVal('traitgroup'),
						'lines'=>null,
						'field_separator'=>$this->translateDelimiter($this->rGetVal('delimiter'))
					)
				);
            }
            else
			{
				$this->setDataSession(null);
            }
        }

		if ($this->getDataSession())
		{
			$this->setSessionLines($this->parseSessionFile());
			$this->redirect('data_raw.php');
		}
	
		$this->smarty->assign('groups',$this->getTraitgroups());
		$this->printPage();
    }
	
    public function dataRawAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

		if ($this->rHasVal('action','ref_codes'))
		{
			$this->handleUploadedRefCodes();
		}
		else
		if ($this->rHasVal('action','clear_ref_codes'))
		{
			$this->setReferenceListSession( null );
		}
		else
		if ($this->rHasVal('action','clear'))
		{
			$this->setDataSession( null );
			$this->getSessionLines( null );
			$this->setReferenceListSession( null );
			$this->redirect('data_upload.php');
		} else		
		if ($this->rHasVal('action','rotate'))
		{
			$this->setSessionLines($this->parseSessionFile(!$this->getIsRotated()));
			$this->setIsRotated(!$this->getIsRotated());
			$this->redirect('data_raw.php?');
		} 

		$f=$this->getDataSession();

		if ( isset($f['traitgroup']) )
		{
			$this->getTraitsSettings();
			$this->matchTraits();
			$this->matchSpecies();
			$this->matchValues();
			$this->matchReferences();
		}

        $this->setPageName($this->translate('Data matched'));
		
		$data=$this->getDataSession();

		if ($data['any_existing_values'])
		{
			$this->addWarning( $this->translate( 'For some or all taxa, there already are values for this trait group in the database. These will be overwritten by your new data.' ) );
		}

		$this->smarty->assign( 'data', $data );
		$this->smarty->assign( 'reflist', $this->getReferenceListSession() );
		$this->smarty->assign( 'yes_values', $this->_yesValues );
		$this->smarty->assign( 'no_values', $this->_noValues );

		$this->printPage();
    }

    public function dataSaveAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

		$f=$this->getDataSession();
		$this->getTraitsSettings();

		if ( $this->rHasVal('action','save') )
		{
			$this->setJoinrows( $this->rGetVal('joinrows') );
			$this->saveValues();

			/*
			// reset
			$this->setSessionLines( null );
			$this->setDataSession( null );
			$this->setIsRotated( null );
			$this->setJoinrows( null );
			$this->setReferenceListSession( null );
			*/
		}

		if ($this->hasErrors())
		{
			array_unshift($this->errors,$this->translate('(these records were not saved)'));
			array_unshift($this->errors,sprintf('<b>%s</b>',$this->translate('Errors during saving')));
		}

		if ($this->hasWarnings())
		{
			array_unshift($this->warnings,$this->translate('(these records were saved)'));
			array_unshift($this->warnings,sprintf('<b>%s</b>',$this->translate('Warnings during saving')));
		}

        $this->setPageName( $this->translate('Data saved') );
		$this->printPage();
    }

	private function setJoinrows( $data )
	{
		$this->moduleSession->setModuleSetting( array('setting'=>'joinrows' ) );
		if (!is_null($data))
		{
			//$data=array();
			foreach((array)$data as $val)
			{
				$d=explode(",",trim($val,'[]'));
				$data[$d[0]]=$d[1];
			}
			$this->moduleSession->setModuleSetting( array('setting'=>'joinrows','value'=>$data ) );
		}		
	}

	private function getJoinrows()
	{
		return null!==$this->moduleSession->getModuleSetting( 'joinrows' ) ? 
			$this->moduleSession->getModuleSetting( 'joinrows' ) : 
			false;
	}

	private function setIsRotated( $state )
	{
		$this->moduleSession->setModuleSetting( array('setting'=>'rotated','value'=>$state ) );
	}

	private function getIsRotated()
	{
		return null!==$this->moduleSession->getModuleSetting( 'rotated' ) ? 
			$this->moduleSession->getModuleSetting( 'rotated' ) :
			false;
	}

	private function setDataSession( $data )
	{
		$this->moduleSession->setModuleSetting( array('setting'=>'data','value'=>$data ) );
	}

	private function getDataSession()
	{
		return $this->moduleSession->getModuleSetting( 'data' );
	}

	private function setSessionLines( $lines )
	{
		$data=$this->getDataSession();

		if (is_null($lines))
			unset($data['lines']);
		else
			$data['lines']=$lines;

		$this->setDataSession($data);
	}

	private function getSessionLines()
	{
		$data=$this->getDataSession()['lines'];
	}

	private function setReferenceListSession( $p )
	{
		$this->moduleSession->setModuleSetting( array('setting'=>'reference_list','value'=>$p ) );
	}

	private function getReferenceListSession()
	{
		return $this->moduleSession->getModuleSetting( 'reference_list' );
	}

	private function array_rotate( $array )
	{
		$new_array = array();
		foreach ($array as $el)
		{
			foreach($el as $i => $value)
			{
				if (!isset($new_array[$i])) $new_array[$i] = array();
				$new_array[$i][] = $value;
			}
		}
		return $new_array;
	}

	private function parseSessionFile( $rotate=false )
	{
		$file=$this->getDataSession();

		//		$raw=file($file['path'],FILE_IGNORE_NEW_LINES);
		/*
			apparently, excelsheets use line feeds (Lf, chr(10)) for line breaks within cells. for
			file(), these Lf's are indistinguishable from proper CrLf's, and mess up the resulting
			array. the three lines below filter out the lone Lf's before splitting the file's contents
			on CrLf's.
		*/
		$tmp=file_get_contents($file['path']);

		// replacing non-break space with space (&#160; / \xA0)
		$tmp=str_replace( chr(160), ' ', $tmp );
	
		// Ruud 28-03-18: Encoding class is no longer loaded as a helper, 
		// it's autoloaded through composer instead
		//$tmp=$this->helpers->Encoding->toUTF8( $tmp );
		$tmp = \ForceUTF8\Encoding::toUTF8($tmp);
		
		$tmp=str_replace(chr(10),' ',str_replace(chr(13).chr(10),chr(11),$tmp));
		$raw=explode(chr(11),$tmp);

		$lines=array();

		foreach((array)$raw as $key=>$line)
		{
			if ($key==0)
			{
				$line = ltrim($line,chr(239).chr(187).chr(191));  //BOM!
			}
				
			//$cell=explode($this->_inputFileFieldSeparator,$line);
			$cell=explode($file['field_separator'],$line);

			$buffer=array();

			for($i=count($cell)-1;$i>=0;$i--)
			{
				$c=trim(preg_replace(array('/(\p{Z})+/'),' ',$cell[$i]));
				if (empty($c) && empty($buffer)) continue;
				array_unshift($buffer,trim($c,$this->_inputFileFieldEncloser." "));
			}
			if (isset($buffer))
			{
				if ($rotate)
				{
					$lines[]=$buffer;
				}
				else
				{
					$lines[]=array('cells'=>$buffer);
				}
			}
		}
		
		if ($rotate)
		{
			$lines=$this->array_rotate($lines);
			
			$b=array();
			foreach((array)$lines as $key=>$val)
			{
				$b[$key]['cells']=$val;
			}
			$lines=$b;
		}
		
		return $lines;
	}

	private function matchTraits()
	{
		$data=$this->getDataSession();
		$traits=$this->getTraitgroupTraits($data['traitgroup']);

		foreach((array)$traits as $t=>$trait)
		{
			$traits[$t]['values']=$this->getTraitgroupTraitValues(array('trait'=>$trait['id']));
		}

		// tagging each line with basic columns & re-arrange
		foreach((array)$data['lines'] as $key=>$line)
		{
			if (isset($line['cells'][0]) && $line['cells'][0]==$this->_columnHeaderSpecies)
			{
				$lines[$key]['trait']['sysname']=$this->_sysColSpecies;
			}
			else
			if (isset($line['cells'][0]) && $line['cells'][0]==$this->_columnHeaderTaxonId)
			{
				$lines[$key]['trait']['sysname']=$this->_sysColNsrId;
			}
			else
			if (isset($line['cells'][0]) && $line['cells'][0]==$this->_columnHeaderReferences)
			{
				$lines[$key]['trait']['sysname']=$this->_sysColReferences;
			}
			
			$cells=array();
			$boolean_data=true;
			foreach((array)$line['cells'] as $c=>$cell)
			{
				$cells[]=$cell;
				// cell 0 contains the trait name
				if ($c>0)
				{
					if (
						!in_array(strtolower($cell),$this->_yesValues) && 
						!in_array(strtolower($cell),$this->_noValues) &&
						!empty($cell)
					) $boolean_data=false;
				}
			}
			
			$lines[$key]['has_data']=count($cells)>1;
			$lines[$key]['boolean_data']=$boolean_data;
			$lines[$key]['cells']=$cells;
		}

		// matching cell 0 to trait names
		foreach((array)$lines as $key=>$line)
		{
			foreach((array)$traits as $t=>$trait)
			{
				if (!isset($line['cells'][0])) continue;

				if ($line['cells'][0]==$trait['sysname'])
				{
					$lines[$key]['trait']=
						array(
							'id'=>$trait['id'],
							'sysname'=>$trait['sysname'],
							'type_sysname'=>$trait['type_sysname'],
							'match'=>'full',
							'values'=>$trait['values'],
							'can_be_null'=>$trait['can_be_null'],
							'can_select_multiple'=>$trait['can_select_multiple'],
							'can_have_range'=>$trait['can_have_range']
						);
				} 
				else
				if (strpos($line['cells'][0],$trait['sysname'])===0)
				{
					$lines[$key]['trait']=
						array(
							'id'=>$trait['id'],
							'sysname'=>$trait['sysname'],
							'type_sysname'=>$trait['type_sysname'],
							'match'=>'start',
							'values'=>$trait['values'],
							'can_be_null'=>$trait['can_be_null'],
							'can_select_multiple'=>$trait['can_select_multiple'],
							'can_have_range'=>$trait['can_have_range']
						);
				}
			}
		}
		
		// tagging each line with its trait, based on preceding trait names
		$prevTrait=null;
		foreach((array)$lines as $key=>$line)
		{
			/*
				we're not reassessing the row that matched the trait name based on values
				because we are assuming that ppl know what they're doing; we *are* making
				sure that the following lines have correctly matching values
			*/
			if (isset($line['trait']))
			{
				$prevTrait=$line['trait'];
			}
			else
			if (isset($prevTrait) && is_array($prevTrait) && substr($prevTrait['sysname'],0,1)!='#')
			{
				/*
				// check if data of this line matches the values of previous trait
				if ($line['has_data'])
				{
					$r=true;
					$value=$line['cells'][0];

					// if the trait is a boolean, check if the cells all have legal boolean values
					if (isset($prevTrait['type_sysname']) && $prevTrait['type_sysname']=='boolean')
					{
						foreach((array)$line['cells'] as $c=>$cell)
						{
							if ($c==0) continue;
							if ($r) $r=(in_array($cell,$this->_yesValues) || in_array($cell,$this->_noValues)) || ($prevTrait['can_be_null'] && empty($cell));
						}
					}
					else
					// if the trait is not a boolean, and does have values
					if (isset($prevTrait['values']))
					{
						if ($line['boolean_data'])
						{
							$r=in_array($value,$prevTrait['values']) || ($prevTrait['can_be_null'] && empty($value));
						} 
						else
						{
							foreach((array)$line['cells'] as $c=>$cell)
							{
								if ($c==0) continue;
								if ($prevTrait['can_be_null'] && empty($cell))
								{
									$r=true;
									continue;
								}
								$match=false;
								foreach((array)$prevTrait['values'] as $v)
								{
									

									if (strpos($prevTrait['type_sysname'],'string'===0) && $cell==$v['string_value']) $match=true;
									if (strpos($prevTrait['type_sysname'],'string'===0) && $cell==$v['string_value']) $match=true;
									if (strpos($prevTrait['type_sysname'],'string'===0) && $cell==$v['string_value']) $match=true;
								}
								$r=$match;
							}
						}
					}					
				}

				if ($r)
				{
					$lines[$key]['trait']=$prevTrait;
				}
				*/
				$lines[$key]['trait']=$prevTrait;
			}
		}

		$this->setSessionLines($lines);
	}

	private function matchSpecies()
	{
		$data=$this->getDataSession();

		$taxa=array();

		foreach((array)$data['lines'] as $line)
		{
			if (isset($line['trait']['sysname']) && $line['trait']['sysname']==$this->_sysColSpecies)
			{
				foreach($line['cells'] as $c=>$cell)
				{
					if ($c==0) continue;

					$t1=$this->getTaxonByName($cell);
					$taxa[$c]['by_name']=$t1;
					$taxa[$c]['name']=$cell;
				}
			}
				

			if (isset($line['trait']['sysname']) && $line['trait']['sysname']==$this->_sysColNsrId)
			{
				foreach($line['cells'] as $c=>$cell)
				{
					if ($c==0) continue;
					
					$taxa[$c]['code']=$cell;
					
					if (!empty($this->_taxonIdResolveQuery))
					{
					    $t2=$this->models->Taxa->freeQuery(
							str_replace(
								array('%pid%','%tid%'),
								array($this->getCurrentProjectId(),$cell),$this->_taxonIdResolveQuery)
							);
	
						if ($t2)
						{
							$t2=$this->getTaxonById($t2[0]['id']);
							$taxa[$c]['by_id']=$t2;
						}	
					}
				}
			}
		}

		$existingTaxonValues=
			$this->models->TraitsDataModel->getExistingTaxonValueCount(array(
				'project_id'=>$this->getCurrentProjectId(),
				'trait_group_id'=>$data['traitgroup']
			));

		$existingTaxonFreeValues=
			$this->models->TraitsDataModel->getExistingTaxonFreeValueCount(array(
				'project_id'=>$this->getCurrentProjectId(),
				'trait_group_id'=>$data['traitgroup']
			));
			
		$any_existing_values=false;

		foreach($taxa as $c=>$val)
		{
			$will_use_id=isset($val['by_id']) ? $val['by_id']['id'] : (isset($val['by_name']) ? $val['by_name']['id'] :  null );
			
			$has_existing_values=false;
			
			if ( !empty($will_use_id) )
			{
				$has_existing_values=
					(
						(isset($existingTaxonValues[$will_use_id]['total']) ? $existingTaxonValues[$will_use_id]['total'] : 0 ) +
						(isset($existingTaxonFreeValues[$will_use_id]['total']) ? $existingTaxonFreeValues[$will_use_id]['total'] : 0 )
					) > 0;
					
				if ($has_existing_values) $any_existing_values=true;
			}

			$taxa[$c]=
				array(
					'verbatim_name'=>isset($val['name']) ? $val['name'] : null,
					'verbatim_code'=>isset($val['code']) ? $val['code'] : null,
					'by_name'=>isset($val['by_name']) ? $val['by_name'] : null,
					'by_id'=>isset($val['by_id']) ? $val['by_id'] : null,
					'match'=> isset($val['by_name']) && isset($val['by_id']) && $val['by_name']['id']==$val['by_id']['id'],
					'have_taxon'=>isset($val['by_name']) || isset($val['by_id']),
					'will_use'=> isset($val['by_id']) ? $val['by_id']['taxon'] : (isset($val['by_name']) ? $val['by_name']['taxon'] :  null ),
					'will_use_id'=> $will_use_id,
					'will_use_source'=> isset($val['by_id']) ? 'ID' : (isset($val['by_name']) ? 'name' :  null ),
					'has_existing_values'=> $has_existing_values
				);
		}

		$data['taxa']=$taxa;
		$data['any_existing_values']=$any_existing_values;
		
		$this->setDataSession($data);
	}

	private function matchValues()
	{
		$data=$this->getDataSession();

		// matching and adding status
		foreach((array)$data['lines'] as $key=>$line)
		{
			$cell_status=array();
			
			if (isset($line['trait']['id']))
			{
				$trait=$this->getTraitgroupTrait(array('trait'=>$line['trait']['id']));

				$func=array($this,$trait['type_verification_function_name']);
				
				if (is_callable($func))
				{
					foreach((array)$line['cells'] as $c=>$cell)
					{
						if ($c==0) continue;
						$cell_status[$c]=
							call_user_func(
								$func,
								array(
									'value'=>$cell,
									'trait'=>$trait,
									'boolean_data'=>$line['boolean_data'],
									'actual_value'=>$line['cells'][0]
								)
							);
					}
				}
				else
				{
					$this->addWarning(sprintf($this->translate('Check function "%s" does not exist'),$trait['type_verification_function_name']));
				}
				
			}

			$data['lines'][$key]['cell_status']=$cell_status;
		}


		// atomizing reference-references
		foreach((array)$data['lines'] as $key=>$line)
		{
			if (isset($line['trait']['sysname']) && $line['trait']['sysname']==$this->_sysColReferences)
			{
				$refs=array();
				foreach((array)$line['cells'] as $c=>$cell)
				{
					if ($c==0) continue;
					$cell=str_replace($this->_inputFileReferenceSeparators,$this->_inputFileReferenceSeparators[0],$cell);
					$refs[$c]=explode($this->_inputFileReferenceSeparators[0],$cell);
					array_walk($refs[$c], function(&$val) { $val=trim($val);});
				}
			}
		}
		
		if (isset($refs)) $data['references']=$refs;


		// detecting illegal multiple selects
		foreach((array)$data['lines'] as $key=>$line)
		{
			if (isset($line['trait']['id']) && $line['trait']['can_select_multiple']==false)
			{
				foreach((array)$line['cells'] as $c=>$cell)
				{
					if ($c==0) continue;
			
					if (!$line['boolean_data'] || ($line['boolean_data'] && in_array(strtolower($cell),$this->_yesValues)))
					{
						if (!isset($nonMulti[$line['trait']['id']]['count'][$c]))
							$nonMulti[$line['trait']['id']]['count'][$c]=1;
						else
							$nonMulti[$line['trait']['id']]['count'][$c]++;
					}
				}
			}
		}

		// flagging illegal multiple selects
		foreach((array)$data['lines'] as $key=>$line)
		{
			if (isset($line['trait']['id']) && isset($nonMulti[$line['trait']['id']]))
			{
				foreach((array)$line['cells'] as $c=>$cell)
				{
					if (
						isset($nonMulti[$line['trait']['id']]['count'][$c]) && 
						in_array(strtolower($cell),$this->_yesValues) && 
						$nonMulti[$line['trait']['id']]['count'][$c]>1)
					{
						$data['lines'][$key]['cell_status'][$c]=array('pass'=>false,'error'=>'multiple value select not allowed');
					}
				}
			}
		}
				
		$this->setSessionLines($data['lines']);
	}

	private function matchReferences()
	{
		$data=$this->getDataSession();
		$reflist=$this->getReferenceListSession();
		
		$done=array();
		$references=array();

		foreach((array)$data['lines'] as $line)
		{
			if (isset($line['trait']['sysname']) && $line['trait']['sysname']==$this->_sysColReferences)
			{
				foreach($line['cells'] as $c=>$cell)
				{
					if ($c==0) continue;

					// from 1234; 6578, 8765 -> 1234 6578 8765 -> array(1234,6578,8765)
					//$d=explode(' ',preg_replace(array('/\D/','/(\s)+/'),array(' ',' '),$cell));
					$d=explode(' ',preg_replace(array('/(,|;)/','/(\s)+/'),array(' ',' '),$cell));

					$references[$c]['raw']=$cell;
					$references[$c]['atomized']=$d;

					foreach((array)$d as $val)
					{
						if (empty($val)) continue;
						
						$resolved=false;
						
						foreach((array)$reflist as $r=>$ref)
						{
							if ( isset($ref['ref_0']) )
							{
								if ( $val==$ref[1] )
								{
									$references[$c]['valid'][$val]=array('id'=>$ref['ref_0']['id'],'label'=>$ref['ref_0']['label']);
									$resolved=true;
									$done[$val]=$ref['ref_0']['label'];
								}
							} 
							else
							if ( isset($ref['ref_1']) )
							{
								if ( $val==$ref[0] )
								{
									$references[$c]['valid'][$val]=array('id'=>$ref['ref_1']['id'],'label'=>$ref['ref_1']['label']);
									$resolved=true;
									$done[$val]=$ref['ref_1']['label'];
								}
							}
						}
						
						if ( !$resolved )
						{
							//$this->addError(sprintf($this->translate('Unknown reference # %s'),$val));
							$references[$c]['invalid'][]=$val;
						}
					}
				}
			}
		}
		
		foreach($done as $key=>$val)
		{
			$this->addMessage(sprintf($this->translate('Resolved reference # %s to "%s"'),$key,$val));
		}

		$data['references']=$references;
		
		$this->setDataSession($data);
	}

	private function getLiterature2ById( $iets )
	{
		$r=$this->models->Literature2->_get(array(
			'id'=>
				array(
					'project_id'=>$this->getCurrentProjectId(),
					'id'=>$iets
				)
		));

		if ( $r )
		{
			return $r[0];
		}
	}

	private function handleUploadedRefCodes()
	{
		$d='';
		
		if ($this->requestDataFiles)
		{
			$d.=trim(file_get_contents($this->requestDataFiles[0]["tmp_name"])).chr(10);
		}
	
		if ($this->rHasVal( 'lines' ))
		{
			$d.=trim($this->rGetVal( 'lines' )).chr(10);
		}
		
		$d = (explode( chr(10), $d ));

		array_walk( $d, function( &$a ) { $a=explode(' ',preg_replace('/(\s+|,|;)/',' ',$a),2); } );
		array_walk( $d, function( &$a ) { if ( isset($a[0]) ) $a[0]=trim($a[0]); if ( isset($a[1]) ) $a[1]=trim($a[1]); } );

		foreach((array)$d as $key=>$val)
		{
			if ( empty($val[0]) || empty($val[1]) )
				continue;
			
			if ( is_numeric($val[0]) )
			{
				$d[$key]['ref_0']=$this->getLiterature2ById( $val[0] );
			}

			if ( is_numeric($val[1]) )
			{
				$d[$key]['ref_1']=$this->getLiterature2ById( $val[1] );
			}
		}

		$this->setReferenceListSession( $d );

	}

	private function saveValues()
	{
		$data=$this->getDataSession();
		$join=$this->getJoinrows();

		$saveVal=array();
		$saveFree=array();
		$delFree=array();
		$joined=array();
		$failedtaxa=array();

		foreach((array)$data['lines'] as $key=>$line)
		{
			if (isset($joined[$key]) && $joined[$key]==true) continue;
			
			if (isset($line['trait']['id']) && $line['has_data'])
			{
				
				//echo $line['trait']['sysname'],'<br />';
				
				foreach((array)$line['cell_status'] as $c=>$cell)
				{
					$q=array();

					if ($cell['pass'] && ((!isset($cell['bool_value'])) || (isset($cell['bool_value']) && $cell['bool_value']==true)))
					{
						$taxon_id=$data['taxa'][$c]['will_use_id'];
						
						if (empty($taxon_id))
						{
							$failedtaxa[$c]=array('name'=>$data['taxa'][$c]['verbatim_name'],'code'=>$data['taxa'][$c]['verbatim_code']);
							continue;
						}

						if (isset($cell['value_id']))
						{
							//echo 'have value id',$cell['value_id'],'<br />';
							$q=array(
								'project_id'=>$this->getCurrentProjectId(),
								'taxon_id'=>$taxon_id,
								'value_id'=>$cell['value_id'],
								'trait_id'=>$line['trait']['id'],
								'__column'=>$c
								//'comment'=>$line['cell_comments'][$c]
							);

							$saveVal[]=$q;
						}
						else
						{
							if (!empty($line['cells'][$c]))
							{
								$trait=$this->getTraitgroupTrait(array('trait'=>$line['trait']['id']));
								$func=array($this,$trait['type_verification_function_name']);
								$value=$line['cells'][$c];
								
								// joining cells for ranges
								if(isset($join[$key]) && $trait['can_have_range'])
								{
									if (isset($data['lines'][$join[$key]]['cell_status'][$c]) && $data['lines'][$join[$key]]['cell_status'][$c]['pass']==true)
									{
										$value2=$data['lines'][$join[$key]]['cell_status'][$c]['value'];
										
										if ($value2<$value)
										{
											$this->addError(
												sprintf(
													$this->translate('Invalid range: second value smaller than first (column %s, lines %s & %s: %s<%s)'),
													$c,$key,$join[$key],$value,$value2,$c)
												);
										}
										else
										if ($value2==$value)
										{
											$this->addWarning(
												sprintf(
													$this->translate('Second value same as first: not a range (column %s, lines %s & %s: %s==%s)'),
													$c,$key,$join[$key],$value,$value2)
												);
										}
										else
										{
											$value=
												$value.
												(isset($this->_dashValues[0]) ? $this->_dashValues[0] : '-').
												$data['lines'][$join[$key]]['cell_status'][$c]['value'];
										}
									}
									else
									{
										$this->addError($this->translate('Nothing to join to (illegal or non-existant second value)'));
									}
									$joined[$join[$key]]=true;
								}
									
																	
								if (is_callable($func))
								{
									$r=call_user_func(
										$func,
										array(
											'value'=>$value,
											'trait'=>$trait,
											'boolean_data'=>$line['boolean_data'],
											'actual_value'=>$line['cells'][0]
										)
									);									
									
									if (!$r['pass'])
									{
										$this->addError(sprintf($this->translate('Value didn\'t pass check function (%s at line %s, column %s)'),$value,$key,$c));
										continue;
									}
									
									$q=array(
										'project_id'=>$this->getCurrentProjectId(),
										'taxon_id'=>$taxon_id,
										'trait_id'=>$line['trait']['id'],
										//'comment'=>$line['cell_comments'][$c]
									);
																		
									if (strpos($trait['type_sysname'],'boolean')===0)
									{
										$q+=array('boolean_value'=>$r['value']);
									}
									else
									if (strpos($trait['type_sysname'],'string')===0)
									{
										$q+=array('string_value'=>$r['value']);
									}
									else
									if ((strpos($trait['type_sysname'],'int')===0)||(strpos($trait['type_sysname'],'float')===0))
									{
										if (is_array($r['value']))
										{
											$q+=array('numerical_value'=>$r['value'][0],'numerical_value_end'=>$r['value'][1]);
										}
										else
										{
											$q+=array('numerical_value'=>$r['value']);
										}
									}
									else
									if (strpos($trait['type_sysname'],'date')===0)
									{
										if (is_array($r['value']))
										{
											$q+=array(
												'date_value'=>$this->makeInsertableDate($r['value'][0],$trait['date_format_format']),
												'date_value_end'=>$this->makeInsertableDate($r['value'][1],$trait['date_format_format'])
											);
										}
										else
										{
											$q+=array('date_value'=>$this->makeInsertableDate($r['value'],$trait['date_format_format']));
										}
									}
									
									$q+=array('__column'=>$c);
									$saveFree[]=$q;
								}
								else
								{
									$this->addError(sprintf($this->translate('Missing check function "%s"'),$trait['type_verification_function_name']));
								}
							}
						}
					}
				}
			}
		}
	
		foreach((array)$failedtaxa as $val)
		{
			$this->addError(sprintf($this->translate('Unresolvable taxon: %s (%s)'),$val['name'],$val['code']));
		}


		$saved=$failed=0;
		$deletedtraits=array();
		$savedtaxabycolumn=array(); // for knowing which references to save

		foreach($saveVal as $val)
		{
			if (isset($val['trait_id']) && !isset($deletedtraits[$val['taxon_id']][$val['trait_id']]))
			{
				$this->models->TraitsDataModel->deleteTraitsTaxonValues(array(
					'project_id'=>$val['project_id'],
					'taxon_id'=>$val['taxon_id'],
					'trait_id'=>$val['trait_id']
				));
				$deletedtraits[$val['taxon_id']][$val['trait_id']]=true;
			}

			$c=$val['__column'];
			unset($val['__column']);
			unset($val['trait_id']);

			if ($this->models->TraitsTaxonValues->save($val))
			{
				$saved++;
				$savedtaxabycolumn[$c]=$val['taxon_id'];

				$after=$this->models->TraitsTaxonValues->_get( [ 'id' => $this->models->TraitsTaxonValues->getNewId() ] );
				$taxon=$this->getTaxonById($val['taxon_id']);
				$this->logChange( [ 'after'=>$after,'note'=> 'bulk add taxon trait values for ' . $taxon['nomen'] ] );
			}
			else
			{
				$failed++;
			}
		}

		$deletedtraits=array();
		foreach($saveFree as $val)
		{
			if (isset($val['trait_id']) && !isset($deletedtraits[$val['taxon_id']][$val['trait_id']]))
			{
				$this->models->TraitsTaxonFreevalues->delete(array(
					'project_id'=>$val['project_id'],
					'taxon_id'=>$val['taxon_id'],
					'trait_id'=>$val['trait_id']
				));
				$deletedtraits[$val['taxon_id']][$val['trait_id']]=true;
			}

			$c=$val['__column'];
			unset($val['__column']);

			if ($this->models->TraitsTaxonFreevalues->save($val))
			{
				$saved++;
				$savedtaxabycolumn[$c]=$val['taxon_id'];
				
				$after=$this->models->TraitsTaxonFreevalues->_get( [ 'id' => $this->models->TraitsTaxonFreevalues->getNewId() ] );
				$taxon=$this->getTaxonById($val['taxon_id']);
				$this->logChange( [ 'after'=>$after,'note'=> 'bulk add taxon trait free values for ' . $taxon['nomen'] ] );
			}
			else
			{
				$failed++;
			}			
		}


		// saving references
		foreach((array)$savedtaxabycolumn as $column=>$taxon_id)
		{
			$this->models->TraitsTaxonReferences->delete(array(
				'project_id'=>$this->getCurrentProjectId(),
				'trait_group_id'=>$data['traitgroup'],
				'taxon_id'=>$taxon_id
			));			

			if ( isset($data['references'][$column]['valid']) )
			{
				foreach((array)$data['references'][$column]['valid'] as $ref)
				{
					if ( isset($ref['id']) )
					{
						$this->models->TraitsTaxonReferences->save(array(
							'project_id'=>$this->getCurrentProjectId(),
							'trait_group_id'=>$data['traitgroup'],
							'taxon_id'=>$taxon_id,
							'reference_id'=>$ref['id'],
						));
						
					}
				}

				$taxon=$this->getTaxonById($taxon_id);
				$after=$this->models->TraitsTaxonReferences->_get( [ 'id' => [
					'project_id'=>$this->getCurrentProjectId(),
					'trait_group_id'=>$data['traitgroup'],
					'taxon_id'=>$taxon_id
				] ] );
				$this->logChange( [ 'after'=>$after,'note'=> 'bulk add taxon trait references for ' . $taxon['nomen'] ] );
			}
		}
		
		$this->addMessage(sprintf('<b>%s</b>',$this->translate('Summary')));
		$this->addMessage(sprintf($this->translate('Saved %s trait values, failed %s.'),$saved,$failed));

	}

}
