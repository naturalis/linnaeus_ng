<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

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

    public function __destruct()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

    public function getTaxon( $params )
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] :  null;
        $taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
        $name = isset($params['name']) ? $params['name'] : null;
        $type_id_valid_name = isset($params['type_id_valid_name']) ? $params['type_id_valid_name'] : null;

		if ( is_null($project_id) || ( is_null($taxon_id) && is_null($name) ) ) return;

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
				_p.rank_id as base_rank,
				_p.rank_id as base_rank_id,
				_p.lower_taxon as lower_taxon,
			" . (!is_null($type_id_valid_name) ? "trim(replace(concat(ifnull(_n.uninomial,''),' ',ifnull(_n.specific_epithet,''),' ',ifnull(_n.infra_specific_epithet,'')),'  ',' ')) as nomen, " : "") ."
				_r.rank as rank

			from
				%PRE%taxa _a

			left join %PRE%projects_ranks _p
				on _a.project_id= _p.project_id
				and _a.rank_id = _p.id

			left join %PRE%ranks _r
				on _p.rank_id = _r.id
				
			" . (!is_null($type_id_valid_name) ? "

			left join %PRE%names _n
				on _a.id = _n.taxon_id
				and _a.project_id = _n.project_id
				and _n.type_id = " . $type_id_valid_name 
			: "" ) . "

			where
				_a.project_id = " . $project_id . "
				and " . ( is_null($taxon_id) ? "_a.taxon = '" . $this->escapeString($name) ."'" : "_a.id = " . $taxon_id ) . "

			limit 1
			";

		$d=$this->freeQuery( $query );

		return $d ? $d[0] : null;
	}

    public function getProjectRanks($params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] :  null;
        $language_id = isset($params['language_id']) ? $params['language_id'] : null;

		if ( is_null($project_id) || is_null($language_id) )
			return null;

        $query = "
            select
				_p.id,
				_p.project_id,
				_p.rank_id,
				_p.parent_id,
				_p.lower_taxon,
				_p.keypath_endpoint,
				_r.rank,
				_r.abbreviation,
				_r.can_hybrid,
				_pr.id as ideal_parent_id,
				replace(ifnull(_q.label,_r.rank),'_',' ') as label
			from
				%PRE%projects_ranks _p

			left join %PRE%ranks _r
				on _p.rank_id = _r.id

			left join %PRE%projects_ranks _pr
				on _p.project_id= _pr.project_id
				and _r.ideal_parent_id = _pr.rank_id

			left join %PRE%labels_projects_ranks _q
				on _p.id=_q.project_rank_id
				and _p.project_id = _q.project_id
				and _q.language_id=" . $language_id . "

			where
				_p.project_id = " . $project_id . "
				order by _p.rank_id";

        return
			$this->freeQuery(array(
				"query"=>$query,
				"fieldAsIndex"=>"id"
			));
	}

	public function getActors($projectId)
	{
 		if (!isset($projectId))
			return null;

        $query = "
            select
				_e.id,
				_e.name as label,
				_e.name_alt,
				_e.homepage,
				_e.gender,
				_e.is_company,
				_e.employee_of_id,
				_f.name as company_of_name,
				_f.name_alt as company_of_name_alt,
				_f.homepage as company_of_homepage

			from %PRE%actors _e

			left join %PRE%actors _f
				on _e.employee_of_id = _f.id
				and _e.project_id=_f.project_id

			where
				_e.project_id = ".$projectId."

			order by
				_e.is_company, _e.name";

        return $this->freeQuery($query);
	}

    public function getTaxonParentage($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

		if (is_null($projectId) || is_null($taxonId)) {
			return null;
		}

        $query = "
            select
				parentage
			from
				%PRE%taxon_quick_parentage
			where
				project_id = ".$projectId."
				and taxon_id = ".$taxonId;

        $d = $this->freeQuery($query);

        return isset($d[0]) ? explode(' ',$d[0]['parentage']) : null;
	}

    public function treeGetTop($projectId)
    {
		if ( is_null($projectId) ) return;

        $query = "
           select
				_a.id,
				_a.taxon,
				_r.rank
			from
				%PRE%taxa _a

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where
				_a.project_id = ".$projectId."
				and ifnull(_trash.is_deleted,0)=0
				and _a.parent_id is null
			";

		// avoiding low-hanging orphans (if any...
		$rankClause = "and _r.id < 10";

		$top=$this->freeQuery( $query . $rankClause );

		// ...apparently not)
		if ( count($top)==0 )
		{
			$top=$this->freeQuery( $query );
		}

        return $top;
	}

	public function deleteTaxonParentage($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

        if (is_null($projectId)) {
			return null;
		}

		$query = "delete from %PRE%taxon_quick_parentage where project_id = " . $projectId;

		if (!is_null($taxonId)) {
            $query .= " and taxon_id = " . $taxonId;
		}

        $this->freeQuery($query);
	}

	public function checkNsrCode($params)
	{
	    $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $nsrCode = isset($params['nsrCode']) ? $params['nsrCode'] : null;

        if (is_null($projectId) || is_null($nsrCode)) {
			return null;
		}

		$query = "
            select count(*) as total
			from %PRE%nsr_ids
			where
				project_id = ".$projectId."
				and nsr_id = '".$nsrCode."'";

        return $this->freeQuery($query);

	}

	public function checkRdfId($params)
	{
	    $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $rdfId = isset($params['rdfId']) ? $params['rdfId'] : null;

        if (is_null($projectId) || is_null($rdfId)) {
			return null;
		}

		$query = "
            select count(*) as total
			from %PRE%nsr_ids
			where
				project_id = ".$projectId."
				and rdf_id = '".$rdfId."'";

        return $this->freeQuery($query);

	}

	public function getSetting($params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $module_id = isset($params['module_id']) ? $params['module_id'] : null;
        $setting = isset($params['setting']) ? $params['setting'] : null;

        if (is_null($project_id) || is_null($module_id) || is_null($setting)) return;

		$query = "
			select
				_b.id as setting_id,
				_a.id as value_id,
				_a.value as value,
				_b.default_value as default_value

			from
				%PRE%module_settings_values _a

			left join
				%PRE%module_settings _b
				on _b.id=_a.setting_id

			where
				_a.project_id = " . $project_id . "
				and _b.setting = '" . $setting ."'
				and _b.module_id = " . $module_id;

        $d=$this->freeQuery($query);

		return $d ? $d[0]['value'] : null;
	}




	// Used in IndexController but maybe useful elsewhere too? Moved to model as it requires db connection
	public function makeRegExpCompatSearchString ($s)
	{

		$s = trim($s);

		// if string enclosed by " take it literally
		if (preg_match('/^"(.+)"$/',$s)) {
		    return '(' . mysqli_real_escape_string($this->databaseConnection, substr($s,1,-2)).')';
		}

		$s = preg_replace('/(\s+)/',' ',$s);

		if (strpos($s,' ')===0) {
		    return mysqli_real_escape_string($this->databaseConnection, $s);
		}

		$s = str_replace(' ','|',$s);

		return '('.mysqli_real_escape_string($this->databaseConnection, $s).')';

	}

	public function getGeneralSettingValue( $params )
	{
	    $project_id = isset($params['project_id']) ? $params['project_id'] : null;
	    $setting = isset($params['setting']) ? $params['setting'] : null;
	    $use_default = isset($params['use_default']) ? $params['use_default'] : false;
	    
	    if ( is_null($project_id) || is_null($setting) ) return;
	    
	    $query = "
			select
				_a.value as value
	        
			from
				%PRE%module_settings_values _a
	        
			left join
				%PRE%module_settings _b
				on _b.id=_a.setting_id
	        
			where
				_a.project_id = " . $project_id . "
				and _b.setting = '" . $setting . "'
				and _b.module_id = -1";
	    
	    $d=$this->freeQuery($query);
	    
	    // Fallback to default setting
	    if (empty($d) && $use_default) {
	        
	        $query = "
                select default_value as value
                from %PRE%module_settings 
                where setting = '" . $setting . "' and module_id = -1";
	        
	        $d = $this->freeQuery($query);
	        
	    }
	    
	    return $d ? $d[0]['value'] : null;
	}
	
	public function getCronNextRun ()
	{
        $d = $this->freeQuery('select last_cron_reset from last_cron_reset');
        return isset($d) ? $d[0]['last_cron_reset'] : false;
	}

}

