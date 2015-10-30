<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class ControllerModel extends AbstractModel
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

    public function getTaxonById ($params)
    {
		if (!$params) {
		    return false;
		}

		$trashCanExists = isset($params['trashCanExists']) ? $params['trashCanExists'] : false;
		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

        $query = "
			select
				_a.id,
				_a.taxon,
				_a.author,
				_a.parent_id,
				_a.rank_id,
				_a.taxon_order,
				_a.is_hybrid,
				_a.list_level,
				_a.is_empty,
				_b.lower_taxon,
				_c.commonname,
				_b.rank_id as base_rank_id
			from %PRE%taxa _a

		".($trashCanExists ? "
			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id =  _trash.lng_id
				and _trash.item_type='taxon'
			" : "")."

			left join %PRE%projects_ranks _b
				on _a.project_id=_b.project_id
				and _a.rank_id=_b.id

			left join %PRE%commonnames _c
				on _a.project_id=_c.project_id
				and _c.id=
					(select
						id
					from
						%PRE%commonnames
					where
						project_id = " . $projectId . "
						and taxon_id=" . $taxonId . "
						and language_id = ". $languageId . "
						limit 1
					)
			where
				_a.id=". $taxonId ."
				and _a.project_id=".$projectId."
				".($trashCanExists ? " and ifnull(_trash.is_deleted,0)=0" : "");

        return $this->freeQuery($query);
    }


    public function getPreferredName ($params)
    {
		if (!$params) {
		    return false;
		}

		$predicatePreferredName = isset($params['predicatePreferredName']) ? $params['predicatePreferredName'] : false;
		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

        $query = "
            select * from %PRE%names _a
            left join %PRE%name_types _b
                on _a.type_id=_b.id
                and _a.project_id=_b.project_id
                and _b.nametype = '".$predicatePreferredName."'
            where _a.project_id =".$projectId."
                and _a.taxon_id =".$taxonId."
                and language_id =".$languageId."
            limit 1";

        return $this->freeQuery($query);
    }


    public function getTaxa ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;

        $query = "
			select
				_a.id,
				_a.taxon,
				_a.parent_id,
				_a.rank_id,
				_a.taxon_order,
				_a.is_hybrid,
				_a.list_level,
				_a.is_empty,
				_a.author
			from %PRE%taxa _a
			where
				_a.project_id = ".$projectId;

        return $this->freeQuery($query);
    }


    public function getTaxonCommonNames ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

        $query = "
			select language_id, commonname, transliteration
			from %PRE%commonnames
			where project_id = ".$projectId."
			and taxon_id=".$taxonId;

        return $this->freeQuery($query);
    }


    public function getLookupList ($params)
    {
		if (!$params) {
		    return false;
		}

		$search = isset($params['search']) ? mysqli_real_escape_string($this->databaseConnection, $params['search']) : false;
		$matchStart = isset($params['matchStart']) ? $params['matchStart'] : false;
		$projectId = isset($params['projectId']) ? $params['projectId'] : false;

        $query = "
			select * from
			(
				select
					id,taxon as label,'species' as source, concat('../species/taxon.php?id=',id) as url, rank_id
				from
					%PRE%taxa
				where
					project_id = ".$projectId ."
					".($search=='*' ? "" : "and taxon like '".(!$matchStart ? '%' : ''). $search."%'" )."

			union

				select
					id,concat(
						author_first,
						if(multiple_authors=1,
							' et al.',
							if(author_second!='',concat(' & ',author_second),'')
						),
						', ',
						year,
						ifnull(suffix,'')
					) as label,'literature' as source, concat('../literature/reference.php?id=',id) as url, null as rank_id
				from %PRE%literature
				where
					project_id = ".$projectId ."
					".($search=='*' ? "" : "
						and (
							author_first like '".(!$matchStart ? '%' : ''). $search."%' or
							author_second like '".(!$matchStart ? '%' : ''). $search."%' or
							year like '".(!$matchStart ? '%' : ''). $search."%'
						)
					")."

			union

				select
					id,topic as label,'introduction' as source, concat('../introduction/topic.php?id=',id) as url, null as rank_id
				from
					%PRE%content_introduction
				where
					project_id = ".$projectId ."
					".($search=='*' ? "" : "and topic like '".(!$matchStart ? '%' : ''). $search."%'" )."


			) as unification
			order by label
			limit 100";


        /* Copied from LinnaeusController. Not updated to fit new model structure! Replace parameters and mysql_* if necessary.

			union

				select
					id,term as label,'glossary' as source, concat('../glossary/term.php?id=',id) as url
				from
					%PRE%glossary
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and term like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

			union

				select
					taxon_id as id,commonname as label,'species' as source, concat('../species/taxon.php?cat=names&id=',taxon_id) as url
				from
					%PRE%commonnames
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and commonname like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

			union

				select
					taxon_id as id,synonym as label,'species' as source, concat('../species/taxon.php?cat=names&id=',taxon_id) as url
				from
					%PRE%synonyms
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and synonym like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

			union

				select
					glossary_id as id,synonym as label,'glossary' as source, concat('../glossary/term.php?id=',glossary_id) as url
				from
					%PRE%glossary_synonyms
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and synonym like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

        */

        return $this->freeQuery($query);
    }



}

?>