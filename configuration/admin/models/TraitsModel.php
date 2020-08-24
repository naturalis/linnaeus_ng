<?php

include_once (__DIR__ . "/AbstractModel.php");

final class TraitsModel extends AbstractModel
{

    public function __construct ()
    {

        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

     }

    public function __destruct ()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

	public function getTraitgroups($params)
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$parent_id=isset($params['parent_id']) ? $params['parent_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) )
			return;
		
		$query = "
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
				and _b.language_id=" . $language_id . "

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.id
				and _c.language_id=" . $language_id . "

			where
				_a.project_id=" . $project_id . "
				and _a.parent_id ".(is_null($parent_id) ? "is null" : "=".$parent_id)."
				order by _a.show_order, _a.sysname
		";
		
		return $this->freeQuery($query);
	}

	public function getTraitgroup($params)
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$group_id=isset($params['group_id']) ? $params['group_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id)  || is_null($group_id) )
			return;

		$query="
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
				and _b.language_id=" . $language_id . "

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.text_id
				and _c.language_id=" . $language_id . "

			where
				_a.project_id=" . $project_id . "
				and _a.id=" . $group_id . "
		";
		
		$r=$this->freeQuery($query);

		return isset($r[0]) ? $r[0] : null;
	}

	public function getTraitgroupTraits($params)
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$trait_group_id=isset($params['trait_group_id']) ? $params['trait_group_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) || is_null($trait_group_id) )
			return;

		$query="
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
				and _a.name_tid=_b.text_id
				and _b.language_id=" . $language_id . "

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.code_tid=_c.text_id
				and _c.language_id=" . $language_id . "

			left join 
				%PRE%text_translations _d
				on _a.project_id=_d.project_id
				and _a.description_tid=_d.text_id
				and _d.language_id=" . $language_id . "

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
				_a.project_id=" . $project_id."
				and _a.trait_group_id=" . $trait_group_id . "
			group by 
				_a.id, _b.translation, _c.translation, _d.translation
			order by
				_a.show_order,_a.sysname
		";

		return $this->freeQuery($query);

	}

    public function getTraitgroupTrait($params)
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$trait_id=isset($params['trait_id']) ? $params['trait_id'] : null;
		
		if ( is_null($project_id) || is_null($trait_id) )
			return;

		$query="
			select
				_a.*,
                _t.translation as description,
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

            left join 
                %PRE%text_translations _t
                on _a.description_tid = _t.text_id
                and _a.project_id = _t.project_id

			where
				_a.project_id=" . $project_id . "
				and _a.id=" . $trait_id . "
		";
		
		return $this->freeQuery($query);
				
	}
	
    public function getTraitgroupTraitValues($params)
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$trait_id=isset($params['trait_id']) ? $params['trait_id'] : null;
		
		if ( is_null($project_id) || is_null($trait_id) )
			return;

		$query="
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
				_a.project_id = " . $project_id . " 
				and _a.trait_id = " . $trait_id . " 
			order by 
				_a.show_order
		";
		
		return $this->freeQuery($query);
		
				
	}

}
