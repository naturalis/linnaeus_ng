<?php
/*

	export from excel NOT as csv, but as tab-delimited text
	
	what we expect:
		tab as field sep
		'Species',

*/

include_once ('TraitsController.php');

class TraitsDataController extends TraitsController
{
	private $_columnHeaderSpecies='Species';
	private $_columnHeaderTaxonId='ID SRTregister';
	private $_columnHeaderReferences='References';
	private $_taxonIdResolveQuery;
	private $_inputFileFieldSeparator="\t";
	private $_inputFileFieldEncloser='"';
	private $_inputFileReferenceSeparators=";";

	private $_sysColSpecies='#species';
	private $_sysColReferences='#references';
	private $_sysColNsrId='#nsr_id';

	private $_yesValues;
	private $_noValues;
	private $_dashValues;

    public $usedModels = array(
		'traits_settings',
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'text_translations',
		'traits_values',
		'traits_taxon_values',
		'traits_taxon_freevalues'
    );
   
    public $controllerPublicName = 'Kenmerken';

    public $cacheFiles = array();
    
    public $cssToLoad = array(
		'traits.css',
//		'taxon_groups.css'
	);

	public $jsToLoad=array(
        'all' => array('traits.js','jquery.mjs.nestedSortable.js')
	);

    public $usedHelpers = array(
        'session_messages'
    );

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
		$this->smarty->assign('sysColSpecies',$this->_sysColSpecies);
		$this->smarty->assign('sysColReferences',$this->_sysColReferences);
		$this->smarty->assign('sysColNsrId',$this->_sysColNsrId);
    }

    public function dataUploadAction()
    {
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
						'lines'=>null
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
		$this->checkAuthorisation();

		if ($this->rHasVal('action','clear'))
		{
			$this->setDataSession(null);
			$this->getSessionLines(null);
			$this->redirect('data_upload.php');
		} 
		
		$f=$this->getDataSession();
		$this->getSettings($f['traitgroup']);

		if ($this->rHasVal('action','save'))
		{
			$this->saveValues();
die();		
		}


		// we'll just keep it like this for now, ok?
		if (1==1||$f['status']=='raw')
		{
			$this->matchTraits();
			$this->matchSpecies();
			$this->matchValues();
			$this->setDataStatus('matched');
		}

        $this->setPageName($this->translate('Data upload'));

		$this->smarty->assign('data',$this->getDataSession());

		$this->printPage();
    }



	private function getSettings($group)
	{
        $s=$this->models->TraitsSettings->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'fieldAsIndex' => 'setting'
			));

		$this->_inputFileFieldSeparator=
			isset($s['input file field separator']['value']) ? $s['input file field separator']['value'] : $this->_inputFileFieldSeparator;
		$this->_inputFileFieldEncloser=
			isset($s['input file field encloser']['value']) ? $s['input file field encloser']['value'] : $this->_inputFileFieldEncloser;

		$this->_inputFileReferenceSeparators=
			isset($s['input file reference separator']['value']) ? $s['input file reference separator']['value'] : $this->_inputFileReferenceSeparators;
		if (preg_match('/(\{)([^\{\}]*)(\})/',$this->_inputFileReferenceSeparators,$matches))
		{
			$this->_inputFileReferenceSeparators=explode("|",$matches[2]);
			//array_walk($this->_inputFileReferenceSeparators, function(&$val) { $val=strtolower($val);});
		}

		$this->_columnHeaderSpecies=
			isset($s['column header species']['value']) ? $s['column header species']['value'] : $this->_columnHeaderSpecies;
		$this->_columnHeaderTaxonId=
			isset($s['column header taxon id']['value']) ? $s['column header taxon id']['value'] : $this->_columnHeaderTaxonId;
		$this->_columnHeaderReferences=
			isset($s['column header references']['value']) ? $s['column header references']['value'] : $this->_columnHeaderReferences;

		$this->_taxonIdResolveQuery=
			isset($s['taxon id query']['value']) ? $s['taxon id query']['value'] : null;


		$this->_yesValues= isset($s['yes values']['value']) ? $s['yes values']['value'] : array('yes');
		if (preg_match('/(\{)([^\{\}]*)(\})/',$this->_yesValues,$matches))
		{
			$this->_yesValues=explode("|",$matches[2]);
			array_walk($this->_yesValues, function(&$val) { $val=strtolower($val);});
		}

		$this->_noValues= isset($s['no values']['value']) ? $s['no values']['value'] : array('yes');
		if (preg_match('/(\{)([^\{\}]*)(\})/',$this->_noValues,$matches))
		{
			$this->_noValues=explode("|",$matches[2]);
			array_walk($this->_noValues, function(&$val) { $val=strtolower($val);});
		}

		$this->_dashValues= isset($s['dash values']['value']) ? $s['dash values']['value'] : array('-');
		if (preg_match('/(\{)([^\{\}]*)(\})/',$this->_dashValues,$matches))
		{
			$this->_dashValues=explode("|",$matches[2]);
		}
		
	}

	private function setDataSession($p)
	{
		$_SESSION['admin']['traits']['data']=$p;
	}

	private function getDataSession()
	{
		return isset($_SESSION['admin']['traits']['data']) ? $_SESSION['admin']['traits']['data'] : null;
	}

	private function setDataStatus($status)
	{
		$_SESSION['admin']['traits']['data']['status']=$status;
	}

	private function setSessionLines($lines)
	{
		$_SESSION['admin']['traits']['data']['lines']=$lines;
	}

	private function getSessionLines()
	{
		return
			isset($_SESSION['admin']['traits']['data']['lines']) ? 
				$_SESSION['admin']['traits']['data']['lines'] : 
				null;
	}

	private function parseSessionFile()
	{
		$file=$this->getDataSession();
		$raw=file($file['path'],FILE_IGNORE_NEW_LINES);
		$lines=array();

		foreach((array)$raw as $line)
		{
			$cell=explode($this->_inputFileFieldSeparator,$line);
			$buffer=array();

			for($i=count($cell)-1;$i>=0;$i--)
			{
				if (empty($cell[$i]) && empty($buffer)) continue;
				array_unshift($buffer,trim($cell[$i],$this->_inputFileFieldEncloser." "));
			}
			if (isset($buffer))
			{
				$lines[]=array('cells'=>$buffer);
			}
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

		/*
			[path] => C:\Windows\Temp\lng549A.tmp
			[name] => ___Vragenlijst BKoese TESTVERSIE.txt
			[status] => raw
			[traitgroup] => 1
			[lines]
		*/

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
				}
			}

			if (!empty($this->_taxonIdResolveQuery) && isset($line['trait']['sysname']) && $line['trait']['sysname']==$this->_sysColNsrId)
			{
				foreach($line['cells'] as $c=>$cell)
				{
					if ($c==0) continue;
					$t2=$this->models->Taxon->freeQuery(str_replace(array('%pid%','%tid%'),array($this->getCurrentProjectId(),$cell),$this->_taxonIdResolveQuery));
					if ($t2)
					{
						$t2=$this->getTaxonById($t2[0]['id']);
						$taxa[$c]['by_id']=$t2;
					}
				}
			}
		}

		foreach($taxa as $c=>$val)
		{
			$taxa[$c]=
				array(
					'by_name'=>$val['by_name'],
					'by_id'=>$val['by_id'],
					'match'=> isset($val['by_name']) && isset($val['by_id']) && $val['by_name']['id']==$val['by_id']['id'],
					'have_taxon'=>isset($val['by_name']) || isset($val['by_id']),
					'will_use'=> isset($val['by_id']) ? $val['by_id']['taxon'] : (isset($val['by_name']) ? $val['by_name']['taxon'] :  null ),
					'will_use_id'=> isset($val['by_id']) ? $val['by_id']['id'] : (isset($val['by_name']) ? $val['by_name']['id'] :  null ),
					'will_use_source'=> isset($val['by_id']) ? 'ID' : (isset($val['by_name']) ? 'name' :  null )
				);
		}
		
		$data['taxa']=$taxa;
		
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
				$trait=$this->getTraitgroupTrait($line['trait']['id']);

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
									'cell_0'=>$line['cells'][0]
								)
							);
					}
				}
				else
				{
					$this->addWarning(sprintf($this->translate('Check function "%s" does not exist'),$values['type_verification_function_name']));
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
		
		$data['references']=$refs;


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


	private function saveValues()
	{
		$data=$this->getDataSession();

		foreach((array)$data['lines'] as $key=>$line)
		{
			if (isset($line['trait']['id']) && $line['has_data'])
			{
				//echo $line['trait']['sysname'],'<br />';
				
				foreach((array)$line['cell_status'] as $c=>$cell)
				{
					$q=array();
					
					if ($cell['pass'])
					{
						$taxon_id=$data['taxa'][$c]['will_use_id'];
						
						if (empty($taxon_id))
						{
							$this->addError(sprintf($this->translate('No taxon ID for column %s'),$c));
							continue;
						}

						if (isset($cell['value_id']))
						{
							//echo 'have value id',$cell['value_id'],'<br />';
							$q=array(
								'project_id'=>$this->getCurrentProjectId(),
								'taxon_id'=>$taxon_id,
								'value_id'=>$cell['value_id'],
								//'comment'=>$line['cell_comments'][$c]
							);

						}
						else
						{
							if (!empty($line['cells'][$c]))
							{
								$trait=$this->getTraitgroupTrait($line['trait']['id']);
								$func=array($this,$trait['type_verification_function_name']);
								
								if (is_callable($func))
								{
									$r=call_user_func(
										$func,
										array(
											'value'=>$line['cells'][$c],
											'trait'=>$trait,
											'boolean_data'=>$line['boolean_data'],
											'cell_0'=>$line['cells'][0]
										)
									);

									$q=array(
										'project_id'=>$this->getCurrentProjectId(),
										'taxon_id'=>$taxon_id,
										'trait_id'=>$line['trait']['id'],
										//'comment'=>$line['cell_comments'][$c]
									);
																		
									if (strpos($trait['type_sysname'],'boolean')===0)
									{
									}
									else
									if (strpos($trait['type_sysname'],'string')===0)
									{
									}
									else
									if ((strpos($trait['type_sysname'],'int')===0)||(strpos($trait['type_sysname'],'float')===0))
									{
									}
									else
									if (strpos($trait['type_sysname'],'date')===0)
									{
									}



q();
									
/*
`taxon_id` int(11) NOT NULL,
`trait_id` int(11) NOT NULL,
`boolean_value` bool,
`string_value` varchar(1000),  
`numerical_value` FLOAT(12,5),
`date_value` date,
*/
									q($r);		
									
								}
								else
								{
									$this->addError(sprintf($this->translate('Missing check function "%s"'),$trait['type_verification_function_name']));
								}
							}
							
							//echo 'B:',$line['cells'][$c],'<br />';
						}
					}
				}
//			q($line);
			}
			
//				'project_id' => $this->getCurrentProjectId(),
			
		}

die();		
q($data,1);
/*
TraitsTaxonValues
TraitsTaxonFreevalues


drop TABLE if exists `traits_taxon_values`;
CREATE TABLE IF NOT EXISTS `traits_taxon_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `comment` varchar(1000), # ref `can_include_comment`
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

drop TABLE if exists `traits_taxon_freevalues`;
CREATE TABLE IF NOT EXISTS `traits_taxon_freevalues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `trait_id` int(11) NOT NULL,
  `boolean_value` bool,
  `string_value` varchar(1000),  
  `numerical_value` FLOAT(12,5),
  `date_value` date,
  `comment` varchar(1000), # ref `can_include_comment`
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


*/


	}



	// check functions
	/*
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

	private function __null_check($value,$trait)
	{
		if (empty($value))
		{
			if ($trait['can_be_null']==1)
			{
				return array('pass'=>true);
			}
			else
			{
				return array('pass'=>false,'error'=>$this->translate('value cannot be null'));
			}
		}		
		
	}

	private function __string_list_check($value,$trait)
	{
		
		foreach((array)$trait['values'] as $val)
		{
			if ($value==$val['string_value'])
			{
				return
					array(
						'pass'=>true,
						'value_id'=>$val['id']
					);
			}
			else
			if (strtolower($value)==strtolower($val['string_value']))
			{
				return 
					array(
						'pass'=>true,
						'warning'=>$this->translate('case mismatch'),
						'value_id'=>$val['id']
					);
			}
		}
	}
	
	private function __string_list_check_weak($value,$trait)
	{
		$potential_matches=array();
		foreach((array)$trait['values'] as $val)
		{
			if (strpos($val['string_value'],$value)===0)
			{
				$potential_matches[]=array('id'=>$val['id'],'value'=>$val['string_value']);
			}
			else
			if (preg_replace('/[^(\x20-\x7F)]*/','',$val['string_value'])==preg_replace('/[^(\x20-\x7F)]*/','',$value))
			{
				$potential_matches[]=array('id'=>$val['id'],'value'=>$val['string_value']);
			}
			
		}

		if (count($potential_matches)==1)
		{
			return 
				array(
					'pass'=>true,
					'warning'=>$this->translate('weak trait match'),
					'value_id'=>$potential_matches[0]['id']
					
				);
		} else
		if (count($potential_matches)>1)
		{
			return 
				array(
					'pass'=>true,
					'warning'=>$this->translate('weak trait matches'),
					'matches'=>$potential_matches
				);
		}
	}
	
	private function __free_string_length_check($value,$trait)
	{
		$max=!empty($trait['max_length']) ? $trait['max_length'] : $this->_defaultMaxLengthStringValue;
		
		if (strlen($value)>$max)
		{
			return
				array(
					'pass'=>false,
					'error'=>sprintf($this->translate('value too long (%s characters; max. %s)'),strlen($value),$max)
				);
		}
		else
		{
			return array('pass'=>true,'value'=>$value);
		}
	}
		


	private function check_boolean($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		
		$check=$this->__null_check($value,$trait);
		if (!empty($check)) return $check;

		$value=strtolower($value);

		$r=in_array($value,$this->_yesValues) || in_array($value,$this->_noValues);

		if ($r)
		{
			return array('pass'=>true,'value'=>in_array($value,$this->_yesValues));
		}
		else
		{
			return 
				array(
					'pass'=>false,
					'error'=>$this->translate('illegal value'),
					'allowed'=>array_merge($this->_yesValues,$this->_noValues)
				);
		}
	}
	
	private function check_stringlist($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		$boolean_data=isset($p['boolean_data']) ? $p['boolean_data'] : null;
		$cell_0=isset($p['cell_0']) ? $p['cell_0'] : null;

		$check=$this->__null_check($value,$trait);
		if (!empty($check)) return $check;

		if ($boolean_data)
		{
			$check=$this->__string_list_check($cell_0,$trait);
			if (empty($check))
				$check=$this->__string_list_check_weak($cell_0,$trait);
			
			if (!empty($check)) 
			{
				$value=strtolower($value);
				$check['true_value']=$cell_0;
				$check['bool_value']=(in_array($value,$this->_yesValues) ? true : (in_array($value,$this->_noValues) ? false : null));
				return $check;
			}
		}
		else
		{
			$check=$this->__string_list_check($value,$trait);
			if (!empty($check)) return $check;

			$check=$this->__string_list_check_weak($value,$trait);
			if (!empty($check)) return $check;

		}

		$allowed=array();
		foreach((array)$trait['values'] as $val)
		{
			$allowed[]=$val['string_value'];
		}
		

		return
			array(
				'pass'=>false,
				'error'=>$this->translate('illegal value'),
				'allowed'=>$allowed
			);
	}

	private function check_stringlistfree($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;

		$check=$this->__null_check($value,$trait);
		if (!empty($check)) return $check;
		
		if ($boolean_data)
		{
			$check=$this->__string_list_check($cell_0,$trait);
			if (!empty($check)) 
			{
				$value=strtolower($value);
				$check['true_value']=$cell_0;
				$check['bool_value']=(in_array($value,$this->_yesValues) ? true : (in_array($value,$this->_noValues) ? false : null));
				return $check;
			}
		}
		else
		{
			$check=$this->__string_list_check($value,$trait);
			if (!empty($check)) return $check;
		}

		$check=$this->__free_string_length_check($value,$trait);
		if (!empty($check)) return $check;
		
		return
			array(
				'pass'=>false,
				'error'=>$this->translate('uncaught error')
			);
	}

	private function check_stringfree($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;

		$check=$this->__null_check($value,$trait);
		if (!empty($check)) return $check;

		$check=$this->__free_string_length_check($value,$trait);
		if (!empty($check)) return $check;
		
		return
			array(
				'pass'=>false,
				'error'=>$this->translate('uncaught error')
			);
	}

	private function check_datefree($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;

		$check=$this->__null_check($value,$trait);
		if (!empty($check)) return $check;

		
		$value=str_replace(' ','',$value);

		$f=date_parse_from_format($trait['date_format_format'],$value);

		if ($f['error_count']==0)
		{
			return array('pass'=>true,'value'=>$value);
		}

		if ($trait['can_have_range']==1)
		{
			$dash=null;
			foreach((array)$this->_dashValues as $val)
			{
				if (strpos($value,$val)!==false)
				{
					$dash=$val;
					break;
				}
			}

			if (!is_null($dash))
			{
				$values=explode($dash,$value);
				if (count($values)!=2)
				{
					return array('pass'=>false,'error'=>$this->translate('illegal range'));
				}
				else
				{
					return array('pass'=>true,'value'=>$values);
				}
			}
		}

		return array('pass'=>false,'error'=>$this->translate('illegal value'));

	}



}



