<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class NsrTreeModel extends AbstractModel
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

	public function getTreeNodeTaxa( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$type_id_valid=isset($params['type_id_valid']) ? $params['type_id_valid'] : null;
		$type_id_preferred=isset($params['type_id_preferred']) ? $params['type_id_preferred'] : null;
		$node_id=isset($params['node_id']) ? $params['node_id'] : null;

		if( !isset( $project_id ) || !isset( $language_id ) || !isset( $type_id_valid ) || !isset( $type_id_preferred ) || !isset( $node_id ) )
		{
			return;
		}
		
		$query="
				select
					_a.id,
					_a.parent_id,
					_a.is_hybrid,
					_a.rank_id,
					_a.taxon,
					ifnull(_k.name,_l.commonname) as name,
					_m.authorship,
					_r.rank,
					_q.label as rank_label,
					_p.rank_id as base_rank

				from
					%PRE%taxa _a

				left join %PRE%trash_can _trash
					on _a.project_id = _trash.project_id
					and _a.id = _trash.lng_id
					and _trash.item_type='taxon'
	
				left join %PRE%projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id

				left join %PRE%labels_projects_ranks _q
					on _a.rank_id=_q.project_rank_id
					and _a.project_id = _q.project_id
					and _q.language_id=".$language_id."

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				left join %PRE%commonnames _l
					on _a.id=_l.taxon_id
					and _a.project_id=_l.project_id
					and _l.language_id=".$language_id."

				left join %PRE%names _k
					on _a.id=_k.taxon_id
					and _a.project_id=_k.project_id
					and _k.type_id=".$type_id_preferred."
					and _k.language_id=".$language_id."
	
				left join %PRE%names _m
					on _a.id=_m.taxon_id
					and _a.project_id=_m.project_id
					and _m.type_id=".$type_id_valid."
	
				where 
					_a.project_id = ".$project_id." 
					and ifnull(_trash.is_deleted,0)=0
					and (_a.id = ".$node_id." or _a.parent_id = ".$node_id.")

				order by
					label
			";

			return $this->freeQuery( $query );
		
	}

	public function getTaxonCount( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$node_id=isset($params['node_id']) ? $params['node_id'] : null;
		
		if( !isset( $project_id ) || !isset( $node_id ) )
		{
			return;
		}
		
		$query="
			select
				count(*) as total
			from
				%PRE%taxon_quick_parentage _sq

			left join %PRE%taxa _e
				on _sq.taxon_id = _e.id
				and _sq.project_id = _e.project_id

			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id = _trash.lng_id
				and _trash.item_type='taxon'

			where 
				_sq.project_id = ".$project_id." 
				and ifnull(_trash.is_deleted,0)=0
				and MATCH(_sq.parentage) AGAINST ('".$node_id."' in boolean mode)
			";
			
			return $this->freeQuery( $query );
			
	}

	public function getSpeciesCount( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$base_rank=isset($params['base_rank']) ? $params['base_rank'] : null;
		$node_id=isset($params['node_id']) ? $params['node_id'] : null;
		
		if( !isset( $project_id ) || !isset( $base_rank ) || !isset( $node_id ) )
		{
			return;
		}
		
		$query="
			select
				count(_sq.taxon_id) as total,
				_sq.taxon_id,
				_sp.presence_id,
				ifnull(_sr.established,'undefined') as established
			from 
				%PRE%taxon_quick_parentage _sq
			
			left join %PRE%presence_taxa _sp
				on _sq.project_id=_sp.project_id
				and _sq.taxon_id=_sp.taxon_id
			
			left join %PRE%presence _sr
				on _sp.project_id=_sr.project_id
				and _sp.presence_id=_sr.id
	
			left join %PRE%taxa _e
				on _sq.taxon_id = _e.id
				and _sq.project_id = _e.project_id
	
			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id = _trash.lng_id
				and _trash.item_type='taxon'
			
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _e.project_id = _f.project_id
			
			where
				_sq.project_id=".$project_id."
				and ifnull(_trash.is_deleted,0)=0
				and _sp.presence_id is not null
				and _f.rank_id".($base_rank>=SPECIES_RANK_ID ? ">=" : "=")." ".SPECIES_RANK_ID."
				and MATCH(_sq.parentage) AGAINST ('".$node_id."' in boolean mode)
			group by 
				_sr.established";
			
			return $this->freeQuery( array('query'=> $query, 'fieldAsIndex' => 'established') );
			
	}

	public function getTaxonChildCount( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$parent_id=isset($params['parent_id']) ? $params['parent_id'] : null;
		
		if( !isset( $project_id ) || !isset( $parent_id ) )
		{
			return;
		}
		
		$query="
			select
				count(*) as total
			from
				%PRE%taxa _a
	
			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'
	
			where 
				_a.project_id = ".$project_id." 
				and ifnull(_trash.is_deleted,0)=0
				and _a.parent_id = ".$parent_id;
			
			return $this->freeQuery( $query );
			
	}




}