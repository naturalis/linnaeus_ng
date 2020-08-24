<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class MatrixKeyAppModel extends AbstractModel
{
	
	private $remainingCountClauses;

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

	public function getStateCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$states = isset($params['states']) ? $params['states'] : null;
		$taxa = isset($params['taxa']) ? $params['taxa'] : null;
					
		if ( is_null($project_id)  ||  is_null($matrix_id) )
			return;
			
		$selStates=isset($data['states']) ? preg_split('/,/',$data['states'],-1,PREG_SPLIT_NO_EMPTY) : array();

		$query="
			select case count(1) when 0 then -1 else 0 end as can_select, state_id
			from %PRE%matrices_taxa_states
			where project_id = ".$project_id." and matrix_id = ".$matrix_id.
			(count($taxa)!=0 ? " and taxon_id in (".implode(',',$taxa).") " : "" ).
			(count($selStates)!=0 ? " and state_id not in (".$states.") " : "")."
			group by state_id
				union all
			select distinct -1 as can_select, state_id
			from %PRE%matrices_taxa_states
			where project_id = ".$project_id." and matrix_id = ".$matrix_id." 
			".(count($taxa)!=0 ? " and taxon_id not in (".implode(',',$taxa).") " : "" ). "
			and state_id not in (
				select state_id from %PRE%matrices_taxa_states
				where project_id = ".$project_id." and matrix_id = ".$matrix_id.
				(count($taxa)!=0 ? " and taxon_id in (".implode(',',$taxa).") " : "" ).
				(count($selStates)!=0 ? " and state_id not in (".$states.") " : "").
			")
				union all
			select 1 as can_select, id as state_id
			from %PRE%characteristics_states
			where project_id = ".$project_id." 
			and id in (".(count($selStates)!=0 ?  $states : '-1' ).")"
		;

		return $this->freeQuery(array(
			'query' => $query,
			'fieldAsIndex' => 'state_id'
		));
	}

	public function getCharacteristicStates( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$characteristic_id = isset($params['characteristic_id']) ? $params['characteristic_id'] : null;
					
		if ( is_null($project_id) || is_null($language_id) || is_null($characteristic_id) )
			return;
			
		$query="
			select
				_a.id,
				_c.label,
				_a.file_name as img,
				'0' as select_state, 
				_a.show_order
			from %PRE%characteristics_states _a
			left join %PRE%characteristics_labels_states _c 
				on _a.id = _c.state_id 
				and _c.language_id = ".$language_id." 
				and _c.project_id = ".$project_id."
			where
				_a.characteristic_id = ".$characteristic_id." 
				and _a.project_id = ".$project_id."
			order by 
				_a.show_order,
				_c.label"
		;
		
		return $this->freeQuery( $query );
	}

	public function getChargroupCharacteristics( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$chargroup_id = isset($params['chargroup_id']) ? $params['chargroup_id'] : null;
					
		if ( is_null($project_id) || is_null($language_id) || is_null($chargroup_id) )
			return;
			
		$query="
			select
				_a.characteristic_id as id,
				'character' as type,
				_a.show_order as show_order,
				if(locate('|',_c.label)=0,_c.label,substring(_c.label,1,locate('|',_c.label)-1)) as label,
				if(locate('|',_c.label)=0,_c.label,substring(_c.label,locate('|',_c.label)+1)) as description					
			from %PRE%characteristics_chargroups _a
			left join %PRE%characteristics _b 
				on _a.characteristic_id = _b.id
			left join %PRE%characteristics_labels _c 
				on _a.characteristic_id = _c.characteristic_id 
				and _c.language_id = ".$language_id." 
				and _c.project_id = ".$project_id."
			where
				_a.chargroup_id = ".$chargroup_id. " 
				and _a.project_id = ".$project_id."
			order by 
				label"
		;
		
		return $this->freeQuery( $query );
	}

	public function getResults( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$states = isset($params['states']) ? $params['states'] : null;
		
		if ( is_null($project_id)  ||  is_null($language_id) )
			return;

		$selStateCount=count(isset($states) ? preg_split('/,/', $states,-1,PREG_SPLIT_NO_EMPTY) : array());
	
		if ($selStateCount==0)
		{
			$query="
				select 'taxon' as type, _a.taxon_id as id, 0 as total_states, 100 as score,_c.is_hybrid as is_hybrid, trim(_c.taxon) as sci_name, trim(_d.commonname) as label,_e.value as url_thumbnail
				from %PRE%matrices_taxa _a
				left join %PRE%taxa _c on _a.taxon_id = _c.id and _c.project_id = ".$project_id."
				left join %PRE%commonnames _d on _d.taxon_id = _a.taxon_id and _d.project_id = ".$project_id." and _d.language_id = ".$language_id." 
				left join %PRE%nbc_extras _e on _c.id = _e.ref_id and _e.ref_type='taxon' and _e.name='url_thumbnail' and _e.project_id = ".$project_id."
				where _a.project_id = ".$project_id."
				group by _a.taxon_id
				union all
				select 'variation' as type, _a.variation_id as id, 0 as total_states, 100 as score,0 as is_hybrid, trim(_d.taxon) as sci_name, trim(_c.label) as label, _e.value as url_thumbnail
				from  %PRE%matrices_variations _a
				left join %PRE%taxa_variations _c on _a.variation_id = _c.id and _c.project_id = ".$project_id."
				left join %PRE%taxa _d on _c.taxon_id = _d.id and _d.project_id = ".$project_id."
				left join %PRE%nbc_extras _e on _a.variation_id = _e.ref_id and _e.ref_type='variation' and _e.name='url_thumbnail' and _e.project_id = ".$project_id."
				where _a.project_id = ".$project_id."
				group by _a.variation_id
				order by label"
			;

		} 
		else
		{
			$query="
				select 'taxon' as type, _a.taxon_id as id, count(_b.state_id) as total_states,
				round((case when count(_b.state_id)>".$selStateCount." then ".$selStateCount." else count(_b.state_id) end/".$selStateCount.")*100,0) as score,
				_c.is_hybrid as is_hybrid, trim(_c.taxon) as sci_name, trim(_d.commonname) as label,_e.value as url_thumbnail
				from %PRE%matrices_taxa _a
				left join %PRE%matrices_taxa_states _b on _a.project_id = _b.project_id and _a.matrix_id = _b.matrix_id and _a.taxon_id = _b.taxon_id and (_b.state_id in (".$states.")) and _b.project_id = ".$project_id."
				left join %PRE%taxa _c on _a.taxon_id = _c.id  and _c.project_id = ".$project_id."
				left join %PRE%commonnames _d on _d.taxon_id = _a.taxon_id and _d.project_id = ".$project_id." and _d.language_id = ".$language_id." 
				left join %PRE%nbc_extras _e on _c.id = _e.ref_id and _e.ref_type='taxon' and _e.name='url_thumbnail' and _e.project_id = ".$project_id."
				where _a.project_id = ".$project_id."
				group by _a.taxon_id having score=100
				union all
				select 'variation' as type, _a.variation_id as id, count(_b.state_id) as total_states,
				round((case when count(_b.state_id)>".$selStateCount." then ".$selStateCount." else count(_b.state_id) end/".$selStateCount.")*100,0) as score,
				0 as is_hybrid, trim(_d.taxon) as sci_name, trim(_c.label) as label, _e.value as url_thumbnail
				from  %PRE%matrices_variations _a
				left join %PRE%matrices_taxa_states _b on _a.project_id = _b.project_id and _a.matrix_id = _b.matrix_id and _a.variation_id = _b.variation_id and (_b.state_id in (".$states.")) and _b.project_id = ".$project_id."
				left join %PRE%taxa_variations _c on _a.variation_id = _c.id and _c.project_id = ".$project_id."
				left join %PRE%taxa _d on _c.taxon_id = _d.id and _d.project_id = ".$project_id."
				left join %PRE%nbc_extras _e on _a.variation_id = _e.ref_id and _e.ref_type='variation' and _e.name='url_thumbnail' and _e.project_id = ".$project_id."
				where _a.project_id = ".$project_id."
				group by _a.variation_id having score=100
				order by score,label"
			;
		}
		
		return $this->freeQuery( $query );
					
	}

	public function getDetail( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		
		if ( is_null($project_id)  ||  is_null($taxon_id) )
			return;

		$res=array();
	
		$query="
			select t.id,trim(replace(t.taxon,'%VAR%','')) as name_sci, c.commonname as name_nl, 
				p.id as group_id, p.taxon as groupname_sci, pc.commonname as groupname_nl, 'taxon' as type 
			from %PRE%taxa t
			left join %PRE%commonnames c 
				on c.taxon_id = t.id 
				and c.project_id = ".$project_id."
			left join %PRE%taxa p 
				on t.parent_id = p.id 
				and p.project_id = ".$project_id."
			left join %PRE%commonnames pc 
				on pc.taxon_id = p.id 
				and pc.project_id = ".$project_id."
			where t.id = ".$taxon_id."
			and t.project_id = ".$project_id
		;

		$d=$this->freeQuery( $query );

		if (!$d) return;

		$res=$d[0];

		$query="
			select _b.title,_a.content, _c.page 
			from %PRE%content_taxa _a
			left join %PRE%pages_taxa_titles _b 
				on _a.page_id = _b.page_id 
				and _b.project_id = ".$project_id."
			left join %PRE%pages_taxa _c 
				on _a.page_id = _c.id 
				and _c.project_id = ".$project_id."
			where _a.taxon_id = ".$taxon_id."
			and _a.project_id = ".$project_id
		;

		$res['content']=$this->freeQuery( $query );

		$query="
			select _a.value as file_name,_b.value as copyright, '1' as overview_image 
			from %PRE%nbc_extras _a
			left join %PRE%nbc_extras _b 
				on _b.ref_type = 'taxon' 
				and _b.ref_id=_a.ref_id 
				and _b.name='photographer' 
				and _b.project_id = ".$project_id."
			where _a.ref_id = ".$taxon_id." 
				and _a.ref_type='taxon' 
				and _a.name='url_image'
			and _a.project_id = ".$project_id
		;

		$res['img_main']=$this->freeQuery( $query );


		$query="select file_name from %PRE%media_taxon where taxon_id = ".$taxon_id." and project_id = ".$project_id;

		$res['img_other']=$this->freeQuery( $query );


		$query="
			select 'taxon' as type, _b.id as id, _b.taxon as taxon,_c.commonname as label, _n.value as img 
			from %PRE%taxa_relations _a 
			left join %PRE%taxa _b 
				on _b.id = _a.relation_id 
				and _b.project_id = ".$project_id."
			left join %PRE%commonnames _c 
				on _c.taxon_id = _b.id 
				and _c.project_id = ".$project_id."
			left join %PRE%nbc_extras _n 
				on _b.id = _n.ref_id 
				and _n.ref_type='taxon' 
				and _n.name='url_thumbnail' 
				and _n.project_id = ".$project_id."
			where _a.ref_type='taxon' and _a.taxon_id = ".$taxon_id." 
			and _a.project_id = ".$project_id."
			union all
			select 'variation' as type, _e.id as id,  _f.taxon as taxon, _e.label as label, _n.value as img 
			from %PRE%taxa_relations _d 
			left join %PRE%taxa_variations _e 
				on _e.id = _d.relation_id 
				and _e.project_id = ".$project_id."
			left join %PRE%taxa _f 
				on _f.id = _d.taxon_id 
				and _f.project_id = ".$project_id."
			left join %PRE%nbc_extras _n 
				on _d.taxon_id = _n.ref_id 
				and _n.ref_type='taxon' 
				and _n.name='url_thumbnail' 
				and _n.project_id = ".$project_id."
			where _d.ref_type='variation'  
			and _d.taxon_id =".$taxon_id." 
			and _d.project_id = ".$project_id
		;
		$res['similar']=$this->freeQuery( $query );

		return $res;
					
	}

    public function getGuiMenuOrder( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) || is_null($matrix_id) )
			return;
		
		$query="
			select 
					_a.ref_id as id,'character' as type,_a.show_order as show_order,
					if(locate('|',_b.label)=0,_b.label,substring(_b.label,1,locate('|',_b.label)-1)) as label,
				if(locate('|',_b.label)=0,_b.label,substring(_b.label,locate('|',_b.label)+1)) as description
			from %PRE%gui_menu_order _a
			left join %PRE%characteristics_labels _b on _b.characteristic_id = _a.ref_id and _b.language_id = ".$language_id."
			where 
				_a.project_id = ".$project_id."
				and _a.matrix_id = ".$matrix_id."
				and _a.ref_type='char'
			union all
			select 
				_a.ref_id as id,'c_group' as type,_a.show_order as show_order, _c.label as label, null as description from %PRE%gui_menu_order _a
			left join %PRE%chargroups_labels _c on _c.chargroup_id = _a.ref_id and _c.language_id = ".$language_id."
			where
				_a.project_id = ".$project_id."
				and _a.matrix_id = ".$matrix_id."
				and _a.ref_type='group'
			order by show_order,label"
		;

		return $this->freeQuery( $query );
	}

}




















