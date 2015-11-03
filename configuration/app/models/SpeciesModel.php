<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class SpeciesModel extends AbstractModel
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



    public function getFirstTaxonId ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : false;

        $query = "
			select _a.id
			from %PRE%taxa _a
			left join %PRE%projects_ranks _b on _a.rank_id=_b.id
			left join %PRE%ranks _c on _b.rank_id=_c.id
			where _a.project_id = '.$projectId.'
			and _b.lower_taxon = '.($taxonType == 'higher' ? 0 : 1).'
			order by _a.taxon_order, _a.taxon
			limit 1";

        $t = $this->freeQuery($query);

		return isset($t) ? $t[0]['id'] : null;
    }

    public function getBrowseOrder ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : false;

        $query = "
			select _a.id,_a.taxon
			from %PRE%taxa _a
			left join %PRE%projects_ranks _b on _a.rank_id=_b.id
			where _a.project_id = '.$projectId.'
			and _b.lower_taxon = '.($taxonType == 'higher' ? 0 : 1).'
			order by _a.taxon_order, _a.taxon";

        return $this->freeQuery($query);
    }



    public function getTaxa ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : false;
        $getAll = isset($params['getAll']) ? $params['getAll'] : false;
        $listMax = isset($params['listMax']) ? $params['listMax'] : false;
        $regExp = isset($params['regExp']) ? $params['regExp'] : false;

        $query = "
			select
				SQL_CALC_FOUND_ROWS
				_a.id, _a.taxon, _a.rank_id, _a.parent_id, _a.is_hybrid
			from
				%PRE%taxa _a
			left join %PRE%projects_ranks _b
				on _a.rank_id=_b.id
			where
				_a.project_id = ".$projectId."
				and _b.lower_taxon = ".($taxonType == 'higher' ? 0 : 1)."
				".($getAll ? "" : "and _a.taxon REGEXP '".$regExp."'")."
			order by taxon
			".(!empty($listMax) ? "limit ".$listMax : "");

        $taxa = $this->freeQuery($query);

		$count = $this->freeQuery('select found_rows() as total');
		$total = $count[0]['total'];

		// Non-associative array, so list() can be used to quickly assign variables in controller
		return array($taxa, $total);
    }


    public function getTaxonParentage ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

        $query = "
			select
				parentage
			from
				%PRE%taxon_quick_parentage
			where
				project_id = ".$projectId."
				and taxon_id = ".$taxonId;

        $d = $this->freeQuery($query);

		return !empty($d) ? explode(' ',$d[0]['parentage']) : null;
    }


}

?>