<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

class WebservicesModel extends AbstractModel
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

    public function initDb( $params )
	{
		$db_lc_time_names = isset($params['db_lc_time_names']) ? $params['db_lc_time_names'] : null;
		
		if ( is_null($db_lc_time_names) )
			return;
		
		$query="SET lc_time_names = '".$this->getSetting('db_lc_time_names','nl_NL')."'";

		$this->freeQuery( $query );
	}

    public function getNames( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$from_date = isset($params['from_date']) ? $params['from_date'] : null;
		$rowcount = isset($params['rowcount']) ? $params['rowcount'] : null;
		$offset = isset($params['offset']) ? $params['offset'] : null;
		$url = isset($params['url']) ? $params['url'] : null;
		
		if ( is_null($project_id) || is_null($from_date) )
			return;
		
		$query="
			select
				SQL_CALC_FOUND_ROWS
				_a.id as name_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.authorship,
				_c.language,
				_c.iso3 as language_iso3,
				if(_a.last_change='0000-00-00 00:00:00',_a.created,_a.last_change) as last_change,
				_b.nametype,
				_a.taxon_id, 
				_d.taxon, 
				_f.default_label as rank,
				concat('".$url."',_a.taxon_id) as url,
				_h.id as taxon_valid_name_id
			from %PRE%names _a
			
			left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
			left join %PRE%languages _c on _a.language_id=_c.id
			left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id
			left join %PRE%projects_ranks _e on _d.rank_id=_e.id and _a.project_id=_e.project_id
			left join %PRE%ranks _f on _e.rank_id=_f.id
			
			left join %PRE%name_types _g on _a.project_id=_g.project_id and _g.nametype='isValidNameOf'
			left join %PRE%names _h on _h.taxon_id=_a.taxon_id and _h.type_id=_g.id and _a.project_id=_h.project_id
	
			left join %PRE%nsr_ids _i on _a.project_id=_i.project_id and _a.taxon_id=_i.lng_id and _i.item_type='taxon'
	
			where _a.project_id=".$project_id."
			and (
				(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$from_date."','%Y%m%d')) ||
				(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$from_date."','%Y%m%d'))
			)
			and _e.rank_id >= ".SPECIES_RANK_ID."
			and _d.taxon is not null".
			(!is_null($rowcount) ? ' limit '.$rowcount : '').
			(!is_null($rowcount) && !is_null($offset) ? ' offset '.$offset : '')
		;

		$data=$this->freeQuery( $query );
		//SQL_CALC_FOUND_ROWS
		$count=$this->freeQuery( "select found_rows() as total" );
				
		return array('data'=>$data,'count'=>$count[0]['total']);
	}

    public function getNameCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$from_date = isset($params['from_date']) ? $params['from_date'] : null;
		
		if ( is_null($project_id) || is_null($from_date) )
			return;
		
		$query="
			select
				count(_a.id) as total
			from
				%PRE%names _a
			left join %PRE%taxa _d
				on _a.taxon_id=_d.id 
				and _a.project_id=_d.project_id
			left join %PRE%projects_ranks _e
				on _d.rank_id=_e.id 
				and _a.project_id=_d.project_id
			where
				_a.project_id=".$project_id."
				and (
					(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$from_date."','%Y%m%d')) ||
					(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$from_date."','%Y%m%d'))
				)
				and _e.rank_id >= ".SPECIES_RANK_ID."
				and _d.taxon is not null";;
		
		return $this->freeQuery( $query );
	}

    public function resolveName( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$name = isset($params['name']) ? $params['name'] : null;
		
		if ( is_null($project_id) || is_null($name) )
			return;
		
		$query="
			select
				_a.taxon_id as id, _a.name
			from %PRE%names _a
			left join %PRE%name_types _b 
				on _a.type_id=_b.id and _a.project_id=_b.project_id
			where
				_a.project_id = ".$project_id."
				and trim(REPLACE(_a.name,_a.authorship,''))='". $this->escapeString($name) ."'
				and _b.nametype = 'isValidNameOf'"
		;

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;
	}

    public function getTaxonSummary( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if ( is_null($project_id) || is_null($taxon_id) )
			return;
		
		$query="
			select
				_a.content, _b.page
			from %PRE%content_taxa _a, %PRE%pages_taxa _b
			where _a.project_id=".$project_id."
			and _a. =".$taxon_id."
			and _a.language_id =".LANGUAGE_ID_DUTCH."
			and _b.project_id=".$project_id."
			and _a.page_id=_b.id
			and _b.page='Summary_dutch'
			";

		$d=$this->freeQuery( $query );
		return isset($d[0]) && isset($d[0]['content']) ? strip_tags($d[0]['content']) : null;

	}

    public function getTaxonNames( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		
		if ( is_null($project_id) || is_null($taxon_id) )
			return;
		
		$query="
			select
				_a.id as name_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.authorship,
				_c.language,
				_c.iso3 as language_iso3,
				_b.nametype
			from %PRE%names _a
			
			left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
			left join %PRE%languages _c on _a.language_id=_c.id
			left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id

			where _a.project_id=".$project_id."
			and _a.taxon_id =".$taxon_id
		;

		return $this->freeQuery( $query );
	}

    public function getTaxonMedia( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$image_base_url = isset($params['image_base_url']) ? $params['image_base_url'] : null;
		
		if ( is_null($project_id) || is_null($taxon_id) )
			return;
		
		$query="
			select
				_a.id as media_id,
				concat('".$image_base_url."',_a.file_name) as url,
				_b.meta_data as copyright,
				_c.meta_data as caption,
				_d.meta_data as creator,
				date_format(_e.meta_date,'%e %M %Y') as date_created

			from %PRE%media_taxon _a

			left join %PRE%media_meta _b
				on _a.id=_b.media_id and _a.project_id=_b.project_id and _b.sys_label = 'beeldbankCopyright'
			left join %PRE%media_meta _c
				on _a.id=_c.media_id and _a.project_id=_c.project_id and _c.sys_label = 'beeldbankCaption'
			left join %PRE%media_meta _d
				on _a.id=_d.media_id and _a.project_id=_d.project_id and _d.sys_label = 'beeldbankFotograaf'
			left join %PRE%media_meta _e
				on _a.id=_e.media_id and _a.project_id=_e.project_id and _e.sys_label = 'beeldbankDatumVervaardiging'

			where
				_a.project_id = ".$project_id."
				and _a.taxon_id = ".$taxon_id."
			order by
				_e.meta_date desc
			";

		return $this->freeQuery( $query );
	}

    public function getTaxonPage( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$page_id = isset($params['page_id']) ? $params['page_id'] : null;

		if ( is_null($project_id) || is_null($taxon_id) || is_null($page_id) )
			return;
		
		$query="
			select
				_b.id,
				ifnull(_c.title,_a.page) as title,
				_b.content

			from
				%PRE%pages_taxa _a
				
			left join %PRE%content_taxa _b
				on _a.id=_b.page_id
				and _a.project_id=_b.project_id
				and _b.language_id =".LANGUAGE_ID_DUTCH."
				and _b.taxon_id =".$taxon_id."
				
			left join %PRE%pages_taxa_titles _c
				on _a.id=_c.page_id
				and _a.project_id=_c.project_id
				and _c.language_id =".LANGUAGE_ID_DUTCH."

			where
				_a.project_id=".$project_id."
				and _a.id=" . $page_id;

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;

	}


}
