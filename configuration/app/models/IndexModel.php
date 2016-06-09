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

    public function getHasHigherLower( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		
		if ( is_null($project_id) ) return;

        $query = "
            select
				count(_a.id)>0 as has_values,
				_c.lower_taxon

			from
				%PRE%taxa _a

			left join %PRE%projects_ranks _c
				on _a.rank_id = _c.id
				and _a.project_id = _c.project_id

			where
				_a.project_id = " . $project_id . "

			group by
				_c.lower_taxon";

		$d=$this->freeQuery( [ 'query' => $query, 'fieldAsIndex' => 'lower_taxon' ] );

		return ['has_lower'=>$d[1]['has_values']==1,'has_higher'=>$d[0]['has_values']==1];
	}

    public function getHasNames( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$nametypes = isset($p['nametypes']) ? $p['nametypes'] : null;
		
		if ( is_null($project_id) || is_null($nametypes) ) return;

        $query = "
            select
				count(*) as total

			from %PRE%names _a
		
			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id
			
			where
				_a.project_id = ".$project_id."
				and _b.nametype in ('" . implode("','",$nametypes) ."')
		";
					
        $d=$this->freeQuery( $query );
		
		return [ 'has_names'=>$d[0]['total']>0 ];
    }

    public function getScientificNameList( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$type = isset($p['type']) ? $p['type'] : null;
		$display_language_id = isset($p['display_language_id']) ? $p['display_language_id'] : null;
		$nametypes = isset($p['nametypes']) ? $p['nametypes'] : null;
		$valid_name_id = isset($p['valid_name_id']) ? $p['valid_name_id'] : null;
		
		$letter = isset($p['letter']) ?
            mysqli_real_escape_string($this->databaseConnection, $p['letter']) : null;
		
		if ( is_null($project_id) || is_null($valid_name_id) || is_null($display_language_id) || is_null($type) || is_null($nametypes) )
			return;

        $query = "
            select
				_a.id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.authorship,
				_a.name_author,
				_a.authorship_year,
				_a.reference,
				_a.reference_id,
				_a.expert,
				_a.expert_id,
				_a.organisation,
				_a.organisation_id,
				_b.nametype,
				_a.language_id,
				ifnull(_q.label,_r.rank) as rank_label,
				_t.id as taxon_id,
				_t.taxon as ref_taxon,
				_valid.authorship as ref_taxon_authorship,
				_t.rank_id,
				_t.parent_id

			from %PRE%names _a
		
			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id=_t.project_id

			left join %PRE%names _valid
				on _t.id=_valid.taxon_id
				and _t.project_id=_valid.project_id
				and _valid.type_id= ". $valid_name_id ."
		
			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id = _f.project_id
			
			left join %PRE%ranks _r
				on _f.rank_id=_r.id
		
			left join %PRE%labels_projects_ranks _q
				on _f.id=_q.project_rank_id
				and _f.project_id = _q.project_id
				and _q.language_id=".$display_language_id."
			
			where
				_a.project_id = ".$project_id."
				and _b.nametype in ('" . implode("','",$nametypes) ."')
				and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
				".(!is_null($letter) ? "and _a.name like '".$letter."%'" : '' )."
			
			order by
				_a.name, _t.taxon
			";

        return $this->freeQuery( $query );
    }

    public function getTaxaListaaa( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$type = isset($p['type']) ? $p['type'] : null;
		$letter = isset($params['letter']) ?
            mysqli_real_escape_string($this->databaseConnection, $params['letter']) : null;
		
		if ( is_null($project_id) || is_null($type) ) return;

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
						project_id = ".$project_id."

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
						_a.project_id = ".$project_id."
				) as unionized

				left join %PRE%projects_ranks _f
					on unionized.rank_id=_f.id
					and unionized.project_id = _f.project_id

				where
					unionized.project_id = ".$project_id."
					and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
					".(!is_null($letter) ? "and label like '".$letter."%'" : '' )."
				order by label";


        return $this->freeQuery($query);

    }



}