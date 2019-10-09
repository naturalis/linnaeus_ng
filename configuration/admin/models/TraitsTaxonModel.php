<?php
include_once (__DIR__ . "/AbstractModel.php");

final class TraitsTaxonModel extends AbstractModel
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

	public function getLiterature2Authors($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$literature2_id=isset($params['literature2_id']) ? $params['literature2_id'] : null;
		
		if ( is_null($project_id) || is_null($literature2_id) )
			return;
		
		$query = "
			select
				_a.actor_id, _b.name
			
			from %PRE%literature2_authors _a
			
			left join %PRE%actors _b
				on _a.actor_id = _b.id 
				and _a.project_id=_b.project_id
			
			where
				_a.project_id = " . $project_id . "
				and _a.literature2_id =" . $literature2_id . "
			order by _b.name
			";
		
		return $this->freeQuery($query);
	}

	public function getLiterature2($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$trait_group_id=isset($params['trait_group_id']) ? $params['trait_group_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		
		if ( is_null($project_id) || is_null($trait_group_id) || is_null($taxon_id) )
			return;
		
		$query = "
			select
				_ttr.id,
				_a.id as literature_id,
				_a.label
			
			from %PRE%literature2 _a
			
			right join %PRE%traits_taxon_references _ttr
				on _a.id = _ttr.reference_id 
				and _a.project_id=_ttr.project_id
				and _ttr.trait_group_id=" . $trait_group_id . "
			
			where
				_a.project_id=" . $project_id . " 
				and _ttr.taxon_id=" . $taxon_id;
		
		return $this->freeQuery($query);
	}

	public function getTraitsTaxonValues($params)
	{
	    $project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$trait_id=isset($params['trait_id']) ? $params['trait_id'] : null;
		$group_id=isset($params['group_id']) ? $params['group_id'] : null;

		if ( is_null($project_id) || is_null($language_id) || is_null($taxon_id) )
			return;

		$query = "
			select * from (
			
				select
					_a.id,
					_b.id as value_id,
					_b.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_t2.translation as trait_code,
					_t3.translation as trait_description,
					_g.sysname as trait_type_sysname,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN 
							if(length(ifnull(_t4.translation,''))=0,_b.string_value,_t4.translation)
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN
							_b.numerical_value
						WHEN locate('date',_g.sysname)=1 THEN
							_b.date
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN _b.numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN _b.date_end
						ELSE null
					END) AS value_end,

					_b.date as _date_value,
					_b.date_end as _date_value_end,
					_e.format as _date_format,
					
					_c.show_order as _show_order_1,
					_b.show_order as _show_order_2,
					'fixed' as type,
					_c.trait_group_id
			
				from
					%PRE%traits_taxon_values _a
				
				left join %PRE%traits_values _b
					on _a.project_id=_b.project_id
					and _a.value_id=_b.id
			
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _b.trait_id=_c.id
			
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language_id."
			
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language_id."
			
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language_id."
			
				left join %PRE%text_translations _t4
					on _b.project_id=_t4.project_id
					and _b.string_label_tid=_t4.text_id
					and _t4.language_id=".$language_id."
			
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
			
				left join  %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
							
				where
					_a.project_id=".$project_id."
					and _a.taxon_id=".$taxon_id."
					and _b.trait_id is not null
			
				union
			
				select
					_a.id,
					null as value_id,
					_a.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_t2.translation as trait_code,
					_t3.translation as trait_description,
					_g.sysname as trait_type_sysname,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN string_value
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value
						WHEN locate('date',_g.sysname)=1 THEN date_value
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN date_value_end
						ELSE null
					END) AS value_end,

					date_value as _date_value,
					date_value_end as _date_value_end,
					_e.format as _date_format,
			
					_c.show_order as _show_order_1,
					null as _show_order_2,
					'free' as type,
					_c.trait_group_id
					
				from
					%PRE%traits_taxon_freevalues _a
				
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _a.trait_id=_c.id
				
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language_id."
				
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language_id."
				
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language_id."
				
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
				
				left join %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
				
				where
					_a.project_id=".$project_id."
					and _a.taxon_id=".$taxon_id."
			
			) as unionized
			
			where 1
		
			".( $group_id ? "and trait_group_id =".$group_id : "" )."
			".( $trait_id ? "and trait_id =".$trait_id : "" )."

			order by _show_order_1,_show_order_2
		";
		
		return $this->freeQuery($query);
	}

	public function getTaxaTraitValues($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$trait_id=isset($params['trait_id']) ? $params['trait_id'] : null;

		if ( is_null($project_id) || is_null($language_id) )
			return;
		
		$query = "
			select unionized.*, _tx.taxon from (
			
				select
					_b.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_c.trait_group_id,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN 
							if(length(ifnull(_t4.translation,''))=0,_b.string_value,_t4.translation)
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN
							_b.numerical_value
						WHEN locate('date',_g.sysname)=1 THEN
							_b.date
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN _b.numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN _b.date_end
						ELSE null
					END) AS value_end,

					_b.date as _date_value,
					_b.date_end as _date_value_end,
					_e.format as _date_format,

					'fixed' as type,
					_a.taxon_id

				from
					%PRE%traits_taxon_values _a
				
				left join %PRE%traits_values _b
					on _a.project_id=_b.project_id
					and _a.value_id=_b.id
			
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _b.trait_id=_c.id
			
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language_id."
			
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language_id."
			
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language_id."
			
				left join %PRE%text_translations _t4
					on _b.project_id=_t4.project_id
					and _b.string_label_tid=_t4.text_id
					and _t4.language_id=".$language_id."
			
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
			
				left join  %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
							
				where
					_a.project_id=".$project_id."
					and _b.trait_id is not null
			
				union
			
				select
					_a.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_c.trait_group_id,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN string_value
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value
						WHEN locate('date',_g.sysname)=1 THEN date_value
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN date_value_end
						ELSE null
					END) AS value_end,

					date_value as _date_value,
					date_value_end as _date_value_end,
					_e.format as _date_format,

					'free' as type,
					_a.taxon_id
					
				from
					%PRE%traits_taxon_freevalues _a
				
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _a.trait_id=_c.id
				
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language_id."
				
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language_id."
				
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language_id."
				
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
				
				left join %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
				
				where
					_a.project_id=".$project_id."
			
			) as unionized
			left join taxa _tx
				on unionized.taxon_id = _tx.id
			where
				trait_id =".$trait_id ."
			order
				by _tx.taxon
		";
		
		return $this->freeQuery($query);
	}

}
