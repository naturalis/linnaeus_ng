<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class IndexModel extends AbstractModel
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


    public function getCommonNamesAlphabet ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;

        $query = "
			select
				distinct lower(substr(commonname,1,1)) as letter
			from
				%PRE%commonnames
			where
				project_id = ".$projectId."
			".(!empty($languageId) ? " and language_id=".$languageId : "" )."
			order by letter";

        return $this->freeQuery($query);

    }


    public function getTaxaAlphabet ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$type = isset($params['type']) ? $params['type'] : false;

        $query = "
			select distinct unionized.letter, _f.lower_taxon from (
					select
						distinct lower(substr(taxon,1,1)) as letter,
						project_id,
						rank_id
					from
						%PRE%taxa
					where
						project_id = ".$projectId."

					union

					select
						distinct lower(substr(_a.synonym,1,1)) as letter,
						_a.project_id,
						_b.rank_id as rank_id
					from
						%PRE%synonyms _a

					right join %PRE%taxa _b
						on _a.project_id = _b.project_id
						and _a.taxon_id = _b.id

					where
						_a.project_id = ".$projectId."
				) as unionized

				left join %PRE%projects_ranks _f
					on unionized.rank_id=_f.id
					and unionized.project_id = _f.project_id

				where
					unionized.project_id = ".$projectId."
					and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
				order by letter";

        return $this->freeQuery($query);

    }


    public function getCommonNamesList ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$letter = isset($params['letter']) ?
            mysqli_real_escape_string($this->databaseConnection, $params['letter']) : false;

        $query = "
			select
				_a.taxon_id,_a.commonname,_a.transliteration, ifnull(_b.label,_c.language) as language

				from
					%PRE%commonnames _a

				left join
					%PRE%languages _c
					on _a.language_id = _c.id

				left join
					%PRE%labels_languages _b
					on _a.project_id = _b.project_id
					and _a.language_id = _b.label_language_id
					and _b.language_id = ".$projectId."

				where
					_a.project_id = ".$projectId."
					".(!empty($languageId) ? " and _a.language_id=".$languageId : "" )."
					".(!empty($letter) ? "and _a.commonname like '".$letter."%'" : null)."

				order by
					_a.commonname";

        return $this->freeQuery($query);

    }


    public function getTaxaList ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$type = isset($params['type']) ? $params['type'] : false;
		$letter = isset($params['letter']) ?
            mysqli_real_escape_string($this->databaseConnection, $params['letter']) : false;

        $query = "
			select unionized.*, _f.lower_taxon from (
					select
						project_id,
						id as taxon_id,
						taxon as label,
						null as author,
						null as ref_taxon,
						author as ref_author,
						rank_id,
						parent_id,
						is_empty,
						'taxon' as source
					from
						%PRE%taxa
					where
						project_id = ".$projectId."

					union

					select
						_a.project_id,
						_a.taxon_id,
						_a.synonym as label,
						_a.author,
						_b.taxon as ref_taxon,
						_b.author as ref_author,
						_b.rank_id as rank_id,
						_b.parent_id as parent_id,
						_b.is_empty as is_empty,
						'synonym' as source
					from
						%PRE%synonyms _a

					right join %PRE%taxa _b
						on _a.project_id = _b.project_id
						and _a.taxon_id = _b.id

					where
						_a.project_id = ".$projectId."
				) as unionized

				left join %PRE%projects_ranks _f
					on unionized.rank_id=_f.id
					and unionized.project_id = _f.project_id

				where
					unionized.project_id = ".$projectId."
					and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
					".(!empty($letter) ? "and label like '".$letter."%'" : null)."
				order by label";

        return $this->freeQuery($query);

    }


    public function getCommonLanguages ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;

        $query = "
			select
				distinct _a.language_id, ifnull(_b.label,_c.language) as language, _a.language_id as id

			from
				%PRE%commonnames _a

			left join
				%PRE%languages _c
				on _a.language_id = _c.id

			left join
				%PRE%labels_languages _b
				on _a.project_id = _b.project_id
				and _a.language_id = _b.label_language_id
				and _b.language_id = ".$languageId."

			where
				_a.project_id = ".$projectId;

        return $this->freeQuery($query);

    }


    public function setTaxaIndexTabs ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$hasHigherTaxa = isset($params['hasHigherTaxa']) ? $params['hasHigherTaxa'] : false;

        $query = "
            select count(_a.id)>0 as has_values, _c.lower_taxon
			from %PRE%taxa _a
			left join %PRE%projects_ranks _c
				on _a.rank_id = _c.id
				and _a.project_id = _c.project_id
			where _a.project_id = " . $projectId . "
			    ".(!$hasHigherTaxa ? "and _a.is_empty = 0" : "" )."
			group by _c.lower_taxon";

        return $this->freeQuery(array(
        	'query' => $query,
        	'fieldAsIndex' => 'lower_taxon'
        ));

	}



}
?>