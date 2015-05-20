<?php

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
    }


	public function getSettings($group)
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


	public function getTraitgroup($id)
	{
		if (empty($id)) return;

		$d=$this->models->TraitsGroups->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as description
			from
				%PRE%traits_groups _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.text_id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.text_id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$id."
		");
		
		$r=$d[0];

		if (isset($r['name_tid'])) $r['names']=$this->getTextTranslations(array('text_id'=>$r['name_tid']));
		if (isset($r['description_tid'])) $r['descriptions']=$this->getTextTranslations(array('text_id'=>$r['description_tid']));
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
		
		$g=$this->models->TraitsGroups->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as description
			from
				%PRE%traits_groups _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.parent_id ".(is_null($parent) ? "is null" : "=".$parent)."
				order by _a.show_order, _a.sysname
		");
		
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
		
		$r=$this->models->TraitsTraits->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as code,
				_d.translation as description,
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_g.sysname as type_sysname,
				_g.allow_values as type_allow_values,
				_g.allow_select_multiple as type_allow_select_multiple,
				_g.allow_max_length as type_allow_max_length,
				_g.allow_unit as type_allow_unit,
				count(_v.id) as value_count

			from
				%PRE%traits_traits _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.code_tid=_c.id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _d
				on _a.project_id=_d.project_id
				and _a.description_tid=_d.id
				and _d.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%traits_date_formats _e
				on _a.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _a.project_id=_f.project_id
				and _a.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id
				
			left join
				%PRE%traits_values _v
				on _a.project_id=_v.project_id
				and _a.id=_v.trait_id

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.trait_group_id=".$group."
			group by _a.id
			order by _a.show_order,_a.sysname
		");

		return $r;
	}


	private function getTraitgroupTraitValuesTaxonCount( $values )
	{
		if ( empty($values) )
			return $values;
		
		$r=$this->models->TraitsValues->freeQuery(
			array(
				"query"=>"
					select
						count(distinct taxon_id) as taxon_count,
						count(taxon_id) as total_count,
						value_id
					from
						%PRE%traits_taxon_values
					where 
						project_id = " . $this->getCurrentProjectId() . "
					group by
						value_id
					",
				"fieldAsIndex"=>"value_id"
			)
		);
		
		foreach( $values as $key=>$val )
		{
			$values[$key]['usage_taxon_count']=$r[$val['id']]['taxon_count'];
			$values[$key]['usage_total_count']=$r[$val['id']]['total_count'];
		}
		
		return $values;
	}

	private function getTraitgroupTraitFreeValueTaxonCount( $trait )
	{
		if ( empty($trait) )
			return $trait;
			
		$r=$this->models->TraitsValues->freeQuery("
			select
				count(distinct taxon_id) as taxon_count,
				count(taxon_id) as total_count
			from
				%PRE%traits_taxon_freevalues
			where 
				project_id = " . $this->getCurrentProjectId() . "
				and trait_id = ". $trait['id']
		);
		
		$trait['freevalue_taxon_count']=$r[0]['taxon_count'];
		$trait['freevalue_total_count']=$r[0]['total_count'];

		return $trait;
	}





	public function getTraitgroupTrait($p)
	{
		$trait=isset($p['trait']) ? $p['trait'] : null;

		if (empty($trait)) return;
		
		$r=$this->models->TraitsTraits->freeQuery("
			select
				_a.*,
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_g.sysname as type_sysname,
				_g.verification_function_name as type_verification_function_name
			from
				%PRE%traits_traits _a

			left join 
				%PRE%traits_date_formats _e
				on _a.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _a.project_id=_f.project_id
				and _a.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$trait."
		");

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

		$r=$this->models->TraitsValues->freeQuery("
			select
				_a.id,
				_a.trait_id,
				_a.string_value,
				_a.string_label_tid,
				_a.numerical_value,
				_a.numerical_value_end,
				_a.date,
				_a.date_end,
				_a.is_lower_limit,
				_a.is_upper_limit,
				_a.lower_limit_label,
				_a.upper_limit_label,						
				_g.allow_fractures,
				_e.format as date_format_format

			from 
				%PRE%traits_values _a
				
			left join 
				%PRE%traits_traits _b
				on _a.project_id=_b.project_id
				and _a.trait_id=_b.id

			left join 
				%PRE%traits_date_formats _e
				on _b.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _b.project_id=_f.project_id
				and _b.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.trait_id = ".$trait." 
			order by 
				_a.show_order
		
		");
		
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



	public function __null_check($value,$trait)
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
	
	public function check_stringlist($p)
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

	public function check_stringlistfree($p)
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

	public function check_stringfree($p)
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

	public function check_datefree($p)
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



	public function formatDbDate($date,$format)
	{
		return is_null($date) ? null : date_format(date_create($date),$format);
	}


	public function makeInsertableDate($date,$format)
	{
		$r=date_parse_from_format($format,$date);
		
		if ($r['error_count']==0)
		{
			return
				(!empty($r['year']) ? $r['year'] : '0000')."-".
				(!empty($r['month']) ? sprintf('%02s',$r['month']) : '01')."-".
				(!empty($r['day']) ? sprintf('%02s',$r['day']) : '01')." ".
				(!empty($r['hour']) ? sprintf('%02s',$r['hour']) : '00').":".
				(!empty($r['minute']) ? sprintf('%02s',$r['minute']) : '00').":".
				(!empty($r['second']) ? sprintf('%02s',$r['second']) : '00')
			;
		}
	}

}



