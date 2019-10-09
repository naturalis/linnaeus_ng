<?php
include_once (__DIR__ . "/AbstractModel.php");

final class VersatileExportModel extends AbstractModel
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

	public function getPresenceStatuses( $params )
	{

		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $project_id ) )
		{
			return;
		}

		$query="
			select
				_b.index_label,
				_b.label,
				_a.established
			from
				%PRE%presence _a
			left join %PRE%presence_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.presence_id
				and _b.language_id=".LANGUAGE_ID_DUTCH."
			where
				_a.project_id = ". $project_id ." 
					and _b.index_label!=''
			order by
				_b.index_label
			";
			
		return $this->freeQuery( $query );

	}

	public function getRanks( $params )
	{

		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $project_id ) )
		{
			return;
		}

		$query="
			select
				_a.*,
				ifnull(_c.label,_a.rank) as label
			from
				%PRE%projects_ranks _b
				
			right join %PRE%ranks _a
				on _a.id=_b.rank_id
				
			left join %PRE%labels_projects_ranks _c
				on _b.project_id=_c.project_id 
				and _b.id=_c.project_rank_id
				and _c.language_id = " . LANGUAGE_ID_DUTCH . "

			where 
				_b.project_id = ". $project_id ." 

			order by
				_a.id
			";

		return $this->freeQuery( array("query"=>$query,"fieldAsIndex"=>"id" ));
	}

	public function doMainQuery( $params )
	{
		$query=isset($params['query']) ? $params['query'] : null;
		return $this->freeQuery( $query );
	}

	public function findAncestor( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if( !isset( $project_id ) || !isset( $taxon_id ) )
		{
			return;
		}

		$query="
			select
				_t.taxon,_t.id, _t.parent_id, _f.rank_id
			from
				taxa  _t
			left join projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id=_f.project_id
			where 
				_t.project_id=".$project_id."
				and _t.id = ".$taxon_id
		;

		$d=$this->freeQuery( $query );

		return isset($d[0] ) ? $d[0] : null;
	}

	public function doSynonymsQuery( $params )
	{
		$query=isset($params['query']) ? $params['query'] : null;
		return $this->freeQuery( $query );
	}

    public function getTraitValueFromMatrix ($p)
    {
        $taxonId = $p['taxon_id'] ?? null;
        $traitId = $p['trait_id'] ?? null;
        $groupId = $p['group_id'] ?? null;
        $projectId = $p['project_id'] ?? null;
        $languageId = $p['language_id'] ?? null;

        $query = '
            select 
                trait_value 
            from 
                 %PRE%traits_matrix
            where
                taxon_id = ' . $taxonId . ' and 
                trait_id = ' . $traitId . ' and 
                trait_group_id = ' . $groupId . ' and 
                project_id = ' . $projectId . ' and 
                language_id = ' . $languageId;

        $res = $this->freeQuery($query);

        return $res[0]['trait_value'] ?? null;
    }

    public function getTaxaTraitsFreeValues ($p)
    {
        $projectId = $p['project_id'] ?? null;

        $q = '
            select 
                taxon_id, group_concat(distinct trait_id separator ",") as trait_ids
            from 
                 %PRE%traits_taxon_freevalues
            where 
                project_id = ' . $projectId . '
            group by 
                taxon_id';

        return $this->freeQuery($q);
    }

    public function getTaxaTraitsFixedValues ($p)
    {
        $projectId = $p['project_id'] ?? null;

        $q = '
            select 
                t1.taxon_id, group_concat(distinct t2.trait_id separator ",") as trait_ids 
            from 
                 %PRE%traits_taxon_values as t1
            left join 
                %PRE%traits_values as t2 on t1.value_id = t2.id
            where 
                t2.trait_id is not null and 
                t1.project_id = ' . $projectId . '
            group by 
                t1.taxon_id';

        return $this->freeQuery($q);
    }

    public function emptyTraitsMatrix ()
    {
        return $this->freeQuery('truncate table %PRE%traits_matrix');
    }

    public function saveTaxonTraitValue ($p)
    {
        $projectId = $p['project_id'] ?? null;
        $languageId = $p['language_id'] ?? null;
        $taxonId = $p['taxon_id'] ?? null;
        $groupId = $p['trait_group_id'] ?? null;
        $groupName = $p['trait_group_name'] ?? null;
        $traitId = $p['trait_id'] ?? null;
        $traitName = $this->escapeString($p['trait_name']) ?? null;
        $traitValue = $this->escapeString($p['trait_value']) ?? null;

        $q = "
            insert ignore into
                %PRE%traits_matrix
            (
                language_id, 
                project_id, 
                taxon_id, 
                trait_group_id, 
                trait_group_name, 
                trait_id,
                trait_name,
                trait_value
            ) 
            values 
            (
                $projectId,
                $languageId,
                $taxonId,
                $groupId,
                '$groupName',
                $traitId,
                '$traitName',
                '$traitValue'
            )";

        return $this->freeQuery($q);
    }



    public function saveTaxonTraitValues ($p)
    {
        $projectId = $p['project_id'] ?? null;
        $languageId = $p['language_id'] ?? null;
        $taxonId = $p['taxon_id'] ?? null;

        $q = "
            insert ignore into
                %PRE%traits_matrix
            (
                language_id, 
                project_id, 
                taxon_id, 
                trait_group_id, 
                trait_group_name, 
                trait_id,
                trait_name,
                trait_value
            ) 
            values ";

        foreach ($p['traits'] as $d) {

            $groupId = $d['trait_group_id'] ?? null;
            $groupName = $d['trait_group_name'] ?? null;
            $traitId = $d['trait_id'] ?? null;
            $traitName = $this->escapeString($d['trait_name']) ?? null;
            $traitValue = $this->escapeString($d['trait_value']) ?? null;

            $q .= "
                 (
                    $projectId,
                    $languageId,
                    $taxonId,
                    $groupId,
                    '$groupName',
                    $traitId,
                    '$traitName',
                    '$traitValue'
                ),";
        }

        return $this->freeQuery(rtrim($q, ','));
    }

    public function getTaxonTraitsFixedValues ($params)
    {
        $project_id=isset($params['project_id']) ? $params['project_id'] : null;
        $language_id=isset($params['language_id']) ? $params['language_id'] : null;
        $taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
        $trait_id=isset($params['trait_id']) ? $params['trait_id'] : null;

        if ( is_null($project_id) || is_null($language_id) || is_null($taxon_id) )
            return;

        $query = "
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
                and _b.trait_id=" . $trait_id;

         return $this->freeQuery($query);
    }


    public function getTaxonTraitsFreeValues ($params)
    {
        $project_id=isset($params['project_id']) ? $params['project_id'] : null;
        $language_id=isset($params['language_id']) ? $params['language_id'] : null;
        $taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
        $trait_id=isset($params['trait_id']) ? $params['trait_id'] : null;

        if ( is_null($project_id) || is_null($language_id) || is_null($taxon_id) )
            return;

        $query = "
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
                and _a.trait_id=".$trait_id;

        return $this->freeQuery($query);
    }













}
