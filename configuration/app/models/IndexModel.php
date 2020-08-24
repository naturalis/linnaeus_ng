<?php 
include_once (__DIR__ . "/AbstractModel.php");

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


    public function getTaxaAlphabet( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$type = isset($p['type']) ? $p['type'] : null;
		$nametypes = isset($p['nametypes']) ? $p['nametypes'] : null;

		if ( is_null($project_id) || is_null($type) || is_null($nametypes) )
			return;

        $query = "
            select

				distinct lower(substr(_a.name,1,1)) as letter

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id=_t.project_id

			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id = _f.project_id

            left join %PRE%trash_can _tr
                on _tr.lng_id = _t.id
                and _t.project_id = _tr.project_id
                and _tr.item_type = 'taxon'

			where
				_a.project_id = ".$project_id."
				and _b.nametype in ('" . implode("','",$nametypes) ."')
				and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
				and _tr.is_deleted is null

			order by
				letter
			";

        return $this->freeQuery( $query );
    }

    public function getCommonNamesAlphabet( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$language_id = isset($p['language_id']) ? $p['language_id'] : null;
		$nametypes = isset($p['nametypes']) ? $p['nametypes'] : null;

		if ( is_null($project_id) || is_null($nametypes) ) return;

        $query = "
            select
				distinct lower(substr(_a.name,1,1)) as letter

			from
				%PRE%names _a

			left join
				%PRE%name_types _b
					on _a.type_id=_b.id
					and _a.project_id=_b.project_id
			where
				_a.project_id = ".$project_id."
				and _b.nametype in ('" . implode("','",$nametypes) ."')
				" . ( !is_null($language_id) ? "and _a.language_id = ".$language_id : "" ). "

			order by
				letter
		";

		return $this->freeQuery( $query );
    }

    public function getCommonNames( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$label_language_id = isset($p['label_language_id']) ? $p['label_language_id'] : null;
		$language_id = isset($p['language_id']) ? $p['language_id'] : null;
		$nametypes = isset($p['nametypes']) ? $p['nametypes'] : null;
		$letter = isset($p['letter']) ? $p['letter'] : null;

		if ( is_null($project_id) || is_null($label_language_id) || is_null($nametypes) ) return;

        $query = "
            select
				_a.taxon_id,
				_a.type_id,
				_a.name,
				_b.nametype,
				ifnull(_d.label,_c.language) as language

			from
				%PRE%names _a

			left join
				%PRE%name_types _b
					on _a.type_id=_b.id
					and _a.project_id=_b.project_id

			left join
				%PRE%languages _c
					on _a.language_id = _c.id

			left join
				%PRE%labels_languages _d
					on _a.project_id = _d.project_id
					and _a.language_id = _d.label_language_id
					and _d.language_id = ".$label_language_id."

			where
				_a.project_id = ".$project_id."
				and _b.nametype in ('" . implode("','",$nametypes) ."')
				".(!empty($letter) ? "and _a.name like '".$letter."%'" : null)."
				".(!empty($language_id) ? "and _a.language_id = ".$language_id : null)."
		";

		return $this->freeQuery( $query );

    }

    public function getCommonNameLanguages( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$label_language_id = isset($p['label_language_id']) ? $p['label_language_id'] : null;
		$nametypes = isset($p['nametypes']) ? $p['nametypes'] : null;

		if ( is_null($project_id) || is_null($label_language_id)  || is_null($nametypes) ) return;

        $query = "
            select
				distinct
					ifnull(_d.label,_c.language) as language,
					_a.language_id

			from
				%PRE%names _a

			left join
				%PRE%name_types _b
					on _a.type_id=_b.id
					and _a.project_id=_b.project_id

			left join
				%PRE%languages _c
					on _a.language_id = _c.id

			left join
				%PRE%labels_languages _d
					on _a.project_id = _d.project_id
					and _a.language_id = _d.label_language_id
					and _d.language_id = ".$label_language_id."

			where
				_a.project_id = ".$project_id."
				and _b.nametype in ('" . implode("','",$nametypes) ."')
		";

		return $this->freeQuery( $query );
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

        if (!empty($d) && isset($d[0]) && isset($d[1])) {
            return ['has_lower'=>$d[1]['has_values']==1,'has_higher'=>$d[0]['has_values']==1];
        }
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

        if (!empty($d) && isset($d[0])) {
            return [ 'has_names'=>$d[0]['total']>0 ];
        }
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

			left join %PRE%trash_can _tr
				on _tr.lng_id = _t.id
				and _t.project_id = _tr.project_id
				and _tr.item_type = 'taxon'

			where
				_a.project_id = ".$project_id."
				and _b.nametype in ('" . implode("','",$nametypes) ."')
				and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
				".(!is_null($letter) ? "and _a.name like '".$letter."%'" : '' )."
				and _tr.is_deleted is null

			order by
				_a.name, _t.taxon
			";

        return $this->freeQuery( $query );
    }

}