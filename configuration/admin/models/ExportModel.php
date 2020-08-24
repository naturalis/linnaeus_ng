<?php /** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class ExportModel extends AbstractModel
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


    public function getTaxaNsr ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $numberOfRecords = isset($params['numberOfRecords']) ? $params['numberOfRecords'] : null;

        if (is_null($projectId) || is_null($languageId)) {
			return null;
		}

        $query = "
            select
				_t.taxon as name,
				_r.rank,
				_t.id,
				trim(LEADING '0' FROM replace(_rr.nsr_id,'tn.nlsr.concept/','')) as nsr_id,
				trim(LEADING '0' FROM replace(_pp.nsr_id,'tn.nlsr.concept/','')) as nsr_id_parent,
				concat('https://nederlandsesoorten.nl/nsr/concept/',replace(_rr.nsr_id,'tn.nlsr.concept/','')) as url,
				concat(_h.index_label,' ',_h.label) as status_status,
				_l2.label as status_reference_title,
				_e1.name as status_expert_name,
				_e2.name as status_organisation_name,
				_q.parentage as classification

			from
				%PRE%taxa _t

			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id=_f.project_id

			left join %PRE%ranks _r
				on _f.rank_id=_r.id

			left join %PRE%nsr_ids _rr
				on _t.id=_rr.lng_id
				and _rr.item_type='taxon'
				and _t.project_id=_rr.project_id

			left join %PRE%nsr_ids _pp
				on _t.parent_id=_pp.lng_id
				and _pp.item_type='taxon'
				and _t.project_id=_pp.project_id

			left join %PRE%presence_taxa _g
				on _t.id=_g.taxon_id
				and _t.project_id=_g.project_id

			left join %PRE%presence_labels _h
				on _g.presence_id = _h.presence_id
				and _g.project_id=_h.project_id
				and _h.language_id=".$languageId."

			left join %PRE%literature2 _l2
				on _g.reference_id = _l2.id
				and _g.project_id=_l2.project_id

			left join %PRE%actors _e1
				on _g.actor_id = _e1.id
				and _g.project_id=_e1.project_id

			left join %PRE%actors _e2
				on _g.actor_org_id = _e2.id
				and _g.project_id=_e2.project_id

			left join %PRE%taxon_quick_parentage _q
				on _t.id=_q.taxon_id
				and _t.project_id=_q.project_id

			left join %PRE%trash_can _trash
				on _t.project_id = _trash.project_id
				and _t.id =  _trash.lng_id
				and _trash.item_type='taxon'

			where _t.project_id = ".$projectId."
				and ifnull(_trash.is_deleted,0)=0

			".($numberOfRecords!='*'  ? "limit ".$numberOfRecords : "" );

        return $this->freeQuery($query);
    }


    public function getNamesNsr ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

        if (is_null($projectId) || is_null($taxonId)) {
			return null;
		}

        $query = "
            select
				_a.name as fullname,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.name_author,
				_a.authorship_year,
				_a.reference,
				_a.expert,
				_a.organisation,
				_b.nametype,
				_e.name as expert_name,
				_f.name as organisation_name,
				_g.label as reference_title,
				_g.author as reference_author,
				_g.date as reference_date,
				_lan.language

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id = _b.project_id

			left join %PRE%actors _e
				on _a.expert_id = _e.id
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.organisation_id = _f.id
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id
				and _a.project_id=_g.project_id

			left join %PRE%languages _lan
				on _a.language_id=_lan.id

			where
				_a.project_id = ".$projectId."
				and _a.taxon_id = ". $taxonId;

        return $this->freeQuery($query);
    }


    public function getTaxonMediaNsr ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $imageBaseUrl = isset($params['imageBaseUrl']) ? $params['imageBaseUrl'] : null;

        if (is_null($projectId) || is_null($taxonId) || is_null($languageId)) {
			return null;
		}

        $query = "
            select
				concat('".$imageBaseUrl."',_m.file_name) as url,
				_m.mime_type as mime_type,
				_c.meta_data as photographer_name,
				date_format(_meta1.meta_date,'%e %M %Y') as date_taken,
				_meta2.meta_data as short_description,
				_meta3.meta_data as geography,
				_meta5.meta_data as copyright,
				_meta7.meta_data as maker_adress

			from  %PRE%media_taxon _m

			left join %PRE%media_meta _c
				on _m.project_id=_c.project_id
				and _m.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'
				and _c.language_id=".$languageId."

			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'
				and _meta1.language_id=".$languageId."

			left join %PRE%media_meta _meta2
				on _m.id=_meta2.media_id
				and _m.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijving'
				and _meta2.language_id=".$languageId."

			left join %PRE%media_meta _meta3
				on _m.id=_meta3.media_id
				and _m.project_id=_meta3.project_id
				and _meta3.sys_label='beeldbankLokatie'
				and _meta3.language_id=".$languageId."

			left join %PRE%media_meta _meta5
				on _m.id=_meta5.media_id
				and _m.project_id=_meta5.project_id
				and _meta5.sys_label='beeldbankCopyright'
				and _meta5.language_id=".$languageId."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$languageId."

			where _m.project_id = ".$projectId."
				and _m.taxon_id = ".$taxonId;

        return $this->freeQuery($query);
    }


    public function getNameAndRankNsr ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

        if (is_null($projectId) || is_null($taxonId)) {
			return null;
		}

        $query = "
            select
				_t.id,
				ifnull(_names.uninomial,_t.taxon) as name,
				_r.rank

			from
				%PRE%taxa _t

			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id=_f.project_id

			left join %PRE%names _names
				on _t.project_id=_f.project_id
				and _t.id=_names.taxon_id
				and _names.type_id=".VALID_NAME_ID."

			left join %PRE%ranks _r
				on _f.rank_id=_r.id

			where _t.project_id = ".$projectId." and _t.id=".$taxonId;

        $t = $this->freeQuery($query);

        return $t[0];
    }



    public function getTaxonPagesNsr ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

        if (is_null($projectId) || is_null($taxonId)) {
			return null;
		}

        $query = "
            select
				_x2.title,_x1.content as text,_x3.language
			from
				%PRE%content_taxa _x1

			left join %PRE%pages_taxa_titles _x2
				on _x1.project_id=_x2.project_id
				and  _x1.page_id=_x2.page_id

			left join %PRE%languages _x3
				on _x1.language_id=_x3.id

			where
				_x1.project_id =".$projectId."
				and _x1.taxon_id = ".$taxonId;

        return $this->freeQuery($query);
    }





}

?>