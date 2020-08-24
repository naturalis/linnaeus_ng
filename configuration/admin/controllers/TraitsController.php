<?php /** @noinspection PhpMissingParentCallMagicInspection */

include_once ('Controller.php');

class TraitsController extends Controller
{

    public $_defaultMaxLengthStringValue=4000;
	public $_defaultMaxLengthIntegerValue=12;
	public $_defaultMaxLengthFloatValue=17;

	public $_columnHeaderSpecies='Species';
	public $_columnHeaderTaxonId='ID SRTregister';
	public $_columnHeaderReferences='References';
	public $_taxonIdResolveQuery;

	public $_sysColSpecies='#species';
	public $_sysColReferences='#references';
	public $_sysColNsrId='#nsr_id';

	public $_yesValues;
	public $_noValues;
	public $_dashValues;

	public $_inputFileFieldSeparator="\t";
	public $_inputFileFieldEncloser='"';
	public $_inputFileReferenceSeparators=";";

    public $usedModels = array(
        'traits',
    );

    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	public function getTraitsSettings()
	{
		//REFAC2015: all these should go to central settings
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


		$this->_yesValues= isset($s['yes values']['value']) ? $s['yes values']['value'] : 'yes';
		if (preg_match('/(\{)([^\{\}]*)(\})/',$this->_yesValues,$matches))
		{
			$this->_yesValues=explode("|",$matches[2]);
			array_walk($this->_yesValues, function(&$val) { $val=strtolower($val);});
		}

		$this->_noValues= isset($s['no values']['value']) ? $s['no values']['value'] : 'yes';
		if (preg_match('/(\{)([^\{\}]*)(\})/',$this->_noValues,$matches))
		{
			$this->_noValues=explode("|",$matches[2]);
			array_walk($this->_noValues, function(&$val) { $val=strtolower($val);});
		}

		$this->_dashValues= isset($s['dash values']['value']) ? $s['dash values']['value'] : '-';
		if (preg_match('/(\{)([^\{\}]*)(\})/',$this->_dashValues,$matches))
		{
			$this->_dashValues=explode("|",$matches[2]);
		}
		
	}

	public function getTraitgroup($id)
	{
		if (empty($id)) return;

		$r=$this->models->TraitsModel->getTraitgroup(array(
			'language_id'=>$this->getDefaultProjectLanguage(),
			'project_id'=>$this->getCurrentProjectId(),
			'group_id'=>$id
		));
		
		if (isset($r['name_tid'])) $r['names']=$this->getTextTranslations(array('text_id'=>$r['name_tid']));
		if (isset($r['description_tid'])) $r['descriptions']=$this->getTextTranslations(array('text_id'=>$r['description_tid']));
		if (isset($r['all_link_text_tid'])) $r['all_link_texts']=$this->getTextTranslations(array('text_id'=>$r['all_link_text_tid']));
		if (isset($r['id'])) $r['groups']=$this->getTraitgroups(array('parent'=>$r['id'],'level'=>0,'stop_level'=>0));
		if (isset($r['id'])) $r['traits']=$this->getTraitgroupTraits($r['id']);
		if (isset($r['parent_id'])) $r['parent']=$this->getTraitgroup($r['parent_id']);

		return $r;
	}

	public function getTraitgroups($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;
		$level=isset($p['level']) ? $p['level'] : 0;
		$stopLevel=isset($p['stop_level']) ? $p['stop_level'] : null;
		
		$g=$this->models->TraitsModel->getTraitgroups(array(
			'language_id'=>$this->getDefaultProjectLanguage(),
			'project_id'=>$this->getCurrentProjectId(),
			'parent_id'=>$parent
		));
		
		foreach((array)$g as $key=>$val)
		{
			$g[$key]['level']=$level;	
			//$g[$key]['taxa']=$this->getTaxongroupTaxa($val['id']);
			if (!is_null($stopLevel) && $stopLevel<=$level)
			{
				continue;
			}
			$g[$key]['children']=$this->getTraitgroups(array('parent'=>$val['id'],'level'=>$level+1,'stop_level'=>$stopLevel));
		}
		
		return $g;
	}

	public function getTraitgroupTraits($group)
	{
		if (empty($group)) return;

		$r=$this->models->TraitsModel->getTraitgroupTraits(array(
			'language_id'=>$this->getDefaultProjectLanguage(),
			'project_id'=>$this->getCurrentProjectId(),
			'trait_group_id'=>$group
		));

		return $r;
	}

	public function getTraitgroupTrait($p)
	{
		$trait=isset($p['trait']) ? $p['trait'] : null;

		if (empty($trait)) return;

		$r=$this->models->TraitsModel->getTraitgroupTrait(array(
			'project_id'=>$this->getCurrentProjectId(),
			'trait_id'=>$trait
		));

		$r = isset($r[0]) ? $r[0] : null;
	
		if (!empty($r))
		{
			if (strpos($r['type_sysname'],'float')===false)
			{
				$r['max_length']=round($r['max_length'],0,PHP_ROUND_HALF_DOWN);
			}

			$r['values']=$this->getTraitgroupTraitValues( $p );
			
			$r['values']=$this->getTraitgroupTraitValuesTaxonCount( $r['values'] );
			
			if (substr($r['type_sysname'],-4)=='free')
			{
				$r=$this->getTraitgroupTraitFreeValueTaxonCount( $r );
			}

			$r['language_labels']=
				array(
					'name'=>$this->getTextTranslations(array('text_id'=>$r['name_tid'])),
					'code'=>$this->getTextTranslations(array('text_id'=>$r['code_tid'])),
					'description'=>$this->getTextTranslations(array('text_id'=>$r['description_tid']))
				);
		}

		return $r;
	}

	public function getTraitgroupTraitValues($p)
	{
		$trait=isset($p['trait']) ? $p['trait'] : null;

		if (empty($trait)) return;

		$r=$this->models->TraitsModel->getTraitgroupTraitValues(array(
			'project_id'=>$this->getCurrentProjectId(),
			'trait_id'=>$trait
		));
		
		foreach((array)$r as $key=>$val)
		{
			if ($val['allow_fractures']!='1' && (!empty($val['numerical_value']) || !empty($val['numerical_value_end'])))
			{
				if (!empty($val['numerical_value']))
					$r[$key]['numerical_value']=round($val['numerical_value'],0,PHP_ROUND_HALF_DOWN);
				if (!empty($val['numerical_value_end']))
					$r[$key]['numerical_value_end']=round($val['numerical_value_end'],0,PHP_ROUND_HALF_DOWN);
			} else
			if (!empty($val['date']) || !empty($val['date_end'])  && !empty($val['date_format_format']))
			{
				if (!empty($val['date']))
					$r[$key]['date']=$this->formatDbDate($val['date'],$val['date_format_format']);
				if (!empty($val['date_end']))
					$r[$key]['date_end']=$this->formatDbDate($val['date_end'],$val['date_format_format']);
			}

			$r[$key]['language_labels']= $this->getTextTranslations(array('text_id'=>$val['string_label_tid']));
		}

		return $r;

	}
	
	public function getTextTranslations($p)
	{
		$text_id=isset($p['text_id']) ? $p['text_id'] : null;
		$language_id=isset($p['language']) ? $p['language'] : null;
		
		if (empty($text_id)) return;
		
		$base=array('project_id'=>$this->getCurrentProjectId(),'text_id'=>$text_id);
		if (!empty($language_id)) $base+=array('language_id'=>$language_id);

		$d=$this->models->TextTranslations->_get(array('id'=>$base));

		$r=array();
		foreach((array)$d as $key=>$val)
		{
			$r[$val['language_id']]=$val['translation'];
		}
		return $r;
	}

	public function _null_check($value,$trait)
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

	public function __string_list_check($value,$trait)
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
	
	public function __string_list_check_weak($value,$trait)
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
	
	public function __free_string_length_check($value,$trait)
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

	public function check_boolean($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		
		$check=$this->_null_check($value,$trait);
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
	
	public function check_stringlist($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		$boolean_data=isset($p['boolean_data']) ? $p['boolean_data'] : null;
		$actual_value=isset($p['actual_value']) ? $p['actual_value'] : null;

		$check=$this->_null_check($value,$trait);
		if (!empty($check)) return $check;

		if ($boolean_data)
		{
			$check=$this->__string_list_check($actual_value,$trait);
			if (empty($check))
				$check=$this->__string_list_check_weak($actual_value,$trait);
			
			if (!empty($check)) 
			{
				$value=strtolower($value);
				$check['true_value']=$actual_value;
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

	public function check_stringlistfree($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		$actual_value=isset($p['actual_value']) ? $p['actual_value'] : null;

		$check=$this->_null_check($value,$trait);
		if (!empty($check)) return $check;
		
		if ($boolean_data)
		{
			$check=$this->__string_list_check($actual_value,$trait);
			if (!empty($check)) 
			{
				$value=strtolower($value);
				$check['true_value']=$actual_value;
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

	public function check_stringfree($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;

		$check=$this->_null_check($value,$trait);
		if (!empty($check)) return $check;

		$check=$this->__free_string_length_check($value,$trait);
		if (!empty($check)) return $check;
		
		return
			array(
				'pass'=>false,
				'error'=>$this->translate('uncaught error')
			);
	}

	public function check_datefree($p)
	{
		$value=isset($p['value']) ? $p['value'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;

		$check=$this->_null_check($value,$trait);
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

    public function check_floatfree($p)
    {
        $value=isset($p['value']) ? $p['value'] : null;
        $trait=isset($p['trait']) ? $p['trait'] : null;

        $check=$this->_null_check($value,$trait);
        if (!empty($check)) return $check;

        $value=str_replace(' ','',$value);

        if (is_numeric($value))
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
                    if (is_numeric($values[0]) && is_numeric($values[1])) {
                        return array('pass'=>true,'value'=>$values);
                    }

                }
            }
        }

        return array('pass'=>false,'error'=>$this->translate('illegal value'));

    }

    public function formatDbDate($date,$format)
	{
	    if ($format=="Y")
		{
			$d=date_parse($date);
			if ($d['month']==0) $d['month']=1;
			if ($d['day']==0) $d['day']=1;
			$date=$d['year']."-".$d['month']."-".$d['day'];
		}

		return is_null($date) ? null : ltrim(date_format(date_create($date),$format),'0');
	}

	public function makeInsertableDate($date,$format)
	{
		$r=date_parse_from_format($format,$date);
		
		if ($r['error_count']==0)
		{
			return
				(!empty($r['year']) ? $r['year'] : '0000')."-".
				(!empty($r['month']) ? sprintf('%02s',$r['month']) : '00')."-".
				(!empty($r['day']) ? sprintf('%02s',$r['day']) : '00')." ".
				(!empty($r['hour']) ? sprintf('%02s',$r['hour']) : '00').":".
				(!empty($r['minute']) ? sprintf('%02s',$r['minute']) : '00').":".
				(!empty($r['second']) ? sprintf('%02s',$r['second']) : '00')
			;
		}
	}

	private function getTraitgroupTraitValuesTaxonCount( $values )
	{
		if ( empty($values) )
			return $values;

		$r=$this->models->TraitsTaxonValues->_get(
			array(
				'columns'=>'count(distinct taxon_id) as taxon_count,count(taxon_id) as total_count',
				'id'=>array('project_id'=>$this->getCurrentProjectId()),
				'group'=>'value_id',
				'fieldAsIndex'=>'value_id'
			)
		);

		foreach( $values as $key=>$val )
		{
			$values[$key]['usage_taxon_count']=@$r[$val['id']]['taxon_count'];
			$values[$key]['usage_total_count']=@$r[$val['id']]['total_count'];
		}
		
		return $values;
	}

	private function getTraitgroupTraitFreeValueTaxonCount( $trait )
	{
		if ( empty($trait) )
			return $trait;
			
		$r=$this->models->TraitsTaxonFreevalues->_get(array(
			'columns'=>'count(distinct taxon_id) as taxon_count,count(taxon_id) as total_count',
			'id'=>array('project_id'=>$this->getCurrentProjectId(),'trait_id'=>$trait['id'])
		));
		
		$trait['freevalue_taxon_count']=$r[0]['taxon_count'];
		$trait['freevalue_total_count']=$r[0]['total_count'];

		return $trait;
	}


}



