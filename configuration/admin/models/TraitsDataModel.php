<?php

include_once (__DIR__ . "/AbstractModel.php");

final class TraitsDataModel extends AbstractModel
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

	public function getExistingTaxonValueCount($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$trait_group_id=isset($params['trait_group_id']) ? $params['trait_group_id'] : null;
		
		if ( is_null($project_id) || is_null($trait_group_id) )
			return;
		
		$query = "
			select
				_ttv.taxon_id, count(_ttv.id) as total
			from
				%PRE%traits_traits _tt
			
			left join %PRE%traits_values _tv
				on _tt.project_id=_tv.project_id
				and _tt.id=_tv.trait_id
			
			left join %PRE%traits_taxon_values _ttv
				on _tv.project_id=_ttv.project_id
				and _tv.id=_ttv.value_id
			
			where
				_tt.project_id=". $project_id."
				and _tt.trait_group_id=".$trait_group_id."
				and _ttv.taxon_id is not null
			group by
				_ttv.taxon_id 
		";
		
		return $this->freeQuery(array("query"=>$query,"fieldAsIndex"=>"taxon_id"));
	}

	public function getExistingTaxonFreeValueCount($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$trait_group_id=isset($params['trait_group_id']) ? $params['trait_group_id'] : null;
		
		if ( is_null($project_id) || is_null($trait_group_id) )
			return;
		
		$query = "
			select
				_ttf.taxon_id, count(_ttf.id) as total
			from
				%PRE%traits_traits _tt

			left join 
				%PRE%traits_taxon_freevalues _ttf
				on _tt.project_id=_ttf.project_id
				and _tt.id=_ttf.trait_id

			where
				_tt.project_id=". $project_id."
				and _tt.trait_group_id=".$trait_group_id."
				and _ttf.taxon_id is not null
			group by
				_ttf.taxon_id 
		";
		
		return $this->freeQuery(array("query"=>$query,"fieldAsIndex"=>"taxon_id"));
	}

	public function deleteTraitsTaxonValues($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$trait_id=isset($params['trait_id']) ? $params['trait_id'] : null;
		
		if ( is_null($project_id) || is_null($taxon_id) || is_null($trait_id) )
			return;
		
		$query = "
			delete from 
				%PRE%traits_taxon_values
			where
				project_id=" . $project_id . "
				and taxon_id=" . $taxon_id . "
				and value_id in (
					select id 
					from %PRE%traits_values 
					where
						project_id=" . $project_id . "
						and trait_id=" . $trait_id . "
					)
		";
		
		return $this->freeQuery($query);
	}



}
