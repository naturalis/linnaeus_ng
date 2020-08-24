<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

class MatrixKeyModel extends AbstractModel
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

    public function getTaxaInMatrix( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$branch_tops=isset($params['branch_tops']) ? $params['branch_tops'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$type_id_preferred=isset($params['type_id_preferred']) ? $params['type_id_preferred'] : null;
		$order_by=isset($params['order_by']) ? $params['order_by'] : '_b.taxon';

		if ( is_null($project_id) || is_null($matrix_id) )
			return;

		if ( isset($branch_tops) )
		{
			$taxa=array();

			foreach((array)$branch_tops as $top)
			{

				$d=$this->freeQuery( "
					select
						_b.id,
						_b.taxon,
						_b.parent_id,
						_c.rank_id as base_rank_id,
						_names.name,
						_parent.taxon as parent_taxon,
						_names_parent.name as parent_name
			
					from
						%PRE%matrices_taxa _a
			
					left join
						%PRE%taxa _b
							on _a.project_id=_b.project_id
							and _a.taxon_id = _b.id

					left join
						%PRE%names _names
							on _b.id = _names.taxon_id
							and _b.project_id = _names.project_id
							and _names.type_id = ".$type_id_preferred."
							and _names.language_id=".$language_id."

					left join
						%PRE%projects_ranks _c
							on _b.project_id=_c.project_id
							and _b.rank_id = _c.id

					right join
						%PRE%taxon_quick_parentage _sq
							on _a.taxon_id = _sq.taxon_id
							and _sq.project_id = " . $project_id . " 

					left join
						%PRE%taxa _parent
							on _b.project_id=_parent.project_id
							and _b.parent_id = _parent.id

					left join
						%PRE%names _names_parent
							on _parent.id = _names_parent.taxon_id
							and _b.project_id = _names_parent.project_id
							and _names_parent.type_id = ".$type_id_preferred."
							and _names_parent.language_id=".$language_id."
			
					where 
						_a.project_id = ". $project_id ."
						and _a.matrix_id = ". $matrix_id."
						and (
								MATCH(_sq.parentage) AGAINST ('" . $this->generateTaxonParentageId( $top ) . "' in boolean mode)
								or _a.taxon_id=" . $top . " 
							)		

					order by
						".$order_by
					);

					if ($d) $taxa=array_merge($taxa,$d);

			}
			
			$taxon=array();

			foreach ($taxa as $key => $row)
			{
				$taxon[$key] = $row['taxon'];
			}

			array_multisort( $taxon, SORT_ASC , $taxa );

		}
		else
		{
			$taxa=$this->freeQuery( "
				select
					_b.id,
					_b.taxon,
					_b.parent_id,
					_c.rank_id as base_rank_id,
					_names.name,
					_parent.taxon as parent_taxon,
					_names_parent.name as parent_name
		
				from
					%PRE%matrices_taxa _a
		
				left join
					%PRE%taxa _b
						on _a.project_id=_b.project_id
						and _a.taxon_id = _b.id

				left join
					%PRE%names _names
						on _b.id = _names.taxon_id
						and _b.project_id = _names.project_id
						and _names.type_id = ".$type_id_preferred."
						and _names.language_id=".$language_id."

				left join
					%PRE%projects_ranks _c
						on _b.project_id=_c.project_id
						and _b.rank_id = _c.id		

				left join
					%PRE%taxa _parent
						on _b.project_id=_parent.project_id
						and _b.parent_id = _parent.id

				left join
					%PRE%names _names_parent
						on _parent.id = _names_parent.taxon_id
						and _b.project_id = _names_parent.project_id
						and _names_parent.type_id = ".$type_id_preferred."
						and _names_parent.language_id=".$language_id."

				where 
					_a.project_id = ". $project_id ."
					and _a.matrix_id = ". $matrix_id."
	
				order by
					".$order_by
				);

		}

		return $taxa;
    }

    public function getAllTaxaAndMatrixPresence( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$branch_tops=isset($params['branch_tops']) ? $params['branch_tops'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$type_id_preferred=isset($params['type_id_preferred']) ? $params['type_id_preferred'] : null;

		if ( is_null($project_id) || is_null($matrix_id) )
			return;

		if ( isset($branch_tops) )
		{
			$taxa=array();

			foreach((array)$branch_tops as $top)
			{

				$taxa+=$this->freeQuery( "
					select
						_b.id,
						_b.taxon,
						_b.parent_id,
						_c.keypath_endpoint,
						_c.lower_taxon,
						_c.rank_id as base_rank_id,
						if(ifnull(_a.id,0)=0,0,1) as already_in_matrix,
						_names.name
			
					from
						%PRE%taxa _b
		
					left join
						%PRE%matrices_taxa _a
							on _a.project_id=_b.project_id
							and _a.taxon_id = _b.id
							and _a.matrix_id = ". $matrix_id."
		
					left join
						%PRE%names _names
							on _b.id = _names.taxon_id
							and _b.project_id = _names.project_id
							and _names.type_id = ".$type_id_preferred."
							and _names.language_id=".$language_id."

					left join
						%PRE%projects_ranks _c
							on _b.project_id=_c.project_id
							and _b.rank_id = _c.id

					right join
						%PRE%taxon_quick_parentage _sq
							on _b.id = _sq.taxon_id
							and _sq.project_id = " . $project_id . " 

					where 
						_b.project_id = ". $project_id ."
						and (
								MATCH(_sq.parentage) AGAINST ('" . $this->generateTaxonParentageId( $top ) . "' in boolean mode)
								or _b.id=" . $top . " 
							)			
					order by
						_c.rank_id asc, _b.taxon asc
					" );

			}

			foreach ($taxa as $key => $row)
			{
				$taxon[$key] = $row['taxon'];
			}

			array_multisort( $taxon, SORT_ASC , $taxa );

		}
		else
		{
			$taxa=$this->freeQuery( "
				select
				
					_b.id,
					_b.taxon,
					_b.parent_id,
					_c.lower_taxon,
					_c.keypath_endpoint,
					_c.rank_id as base_rank_id,
					if(ifnull(_a.id,0)=0,0,1) as already_in_matrix,
					_names.name
		
				from %PRE%taxa _b

				left join
					%PRE%names _names
						on _b.id = _names.taxon_id
						and _b.project_id = _names.project_id
						and _names.type_id = ".$type_id_preferred."
						and _names.language_id=".$language_id."

	
				left join %PRE%matrices_taxa _a
					on _a.project_id=_b.project_id
					and _a.taxon_id = _b.id
					and _a.matrix_id = ". $matrix_id."
	
				left join %PRE%projects_ranks _c
					on _b.project_id=_c.project_id
					and _b.rank_id = _c.id
	
				where 
					_b.project_id = ". $project_id ."

				order by
					_c.rank_id asc, _b.taxon asc
			");
		
		}
		
		return $taxa;

    }

	public function getCharactersNotInGroups( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id=isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;

		if ( is_null($project_id) || is_null($matrix_id) || is_null($language_id) )
			return;

		$query="
			select
				_b.id, 
				_b.type,
				_b.sys_name,
				ifnull(_c.label,_b.sys_name) as label,
				_a.show_order

			from %PRE%characteristics_matrices _a

			left join %PRE%characteristics_chargroups _d
				on _d.characteristic_id = _a.characteristic_id
				and _d.project_id = _a.project_id

			left join %PRE%characteristics _b
				on _a.project_id=_b.project_id
				and _a.characteristic_id=_b.id

			left join %PRE%characteristics_labels _c
				on _b.project_id=_c.project_id
				and _b.id=_c.characteristic_id
				and _c.language_id=" . $language_id . "

			where
				_a.matrix_id =" . $matrix_id . "
				and _a.project_id =" . $project_id . "
				and _d.id is null
		";
		
		$d=$this->freeQuery($query);

		if ($d)
		{
			foreach((array)$d as $key=>$val)
			{
				if (strpos($val['label'],'|')!==false)
				{
					$t=explode('|',$val['label']);
					$d[$key]['short_label']=$t[0];
				}
				else
				{
					$d[$key]['short_label']=$val['label'];
				}
			}
		}	

		return $d;	
		
	}

	public function deleteObsoleteCharacters( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

		$query='delete from %PRE%characteristics
			where project_id =  ' . $project_id . '
			and got_labels = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)';
		
		return $this->freeQuery($query);
	}

    public function getCharactersInMatrix( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$id = isset($params['id']) ? $params['id'] : null;

		if ( is_null($project_id) || is_null($matrix_id) || is_null($language_id) )
			return;

		$query ="
			select
				_b.id,
				_b.type,
				_b.sys_name,
				ifnull(_c.label,_b.sys_name) as label,
				_a.show_order

			from %PRE%characteristics_matrices _a
			
			right join %PRE%characteristics _b
				on _a.project_id=_b.project_id
				and _a.characteristic_id=_b.id

			left join %PRE%characteristics_labels _c
				on _b.project_id=_c.project_id
				and _b.id=_c.characteristic_id
				and _c.language_id=" . $language_id . "

			where 
				_a.project_id = ". $project_id ."
				and _a.matrix_id = ". $matrix_id."
				" . ( !is_null($id)  ? "and _b.id = " . $id : "" ) ."

			order by
				_a.show_order

			";

		$d=$this->freeQuery( $query );
		
		if ($d)
		{
			foreach((array)$d as $key=>$val)
			{
				$query ="
					select
						*
					from
						%PRE%characteristics_labels
					where
						project_id = ". $project_id ."
						and characteristic_id = " . $val['id'] . "
					";

				$d[$key]['labels']=$this->freeQuery( ["query"=>$query,"fieldAsIndex"=>"language_id"] );

				if (strpos($val['label'],'|')!==false)
				{
					$boom=explode('|',$val['label']);
					$d[$key]['short_label']=$boom[0];
				}
				else
				{
					$d[$key]['short_label']=$val['label'];
				}
			}
			
			return  !is_null($id) ? $d[0] : $d;
		}
    }

    public function getStates( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$character_id = isset($params['character_id']) ? $params['character_id'] : null;
		$id = isset($params['id']) ? $params['id'] : null;

		if ( is_null($project_id) || is_null($language_id) )
			return;

		$query ="
			select
				_a.id,
				_a.sys_name,
				_a.characteristic_id,
				_a.file_name,
				_a.file_dimensions,
				_a.lower,
				_a.upper,
				_a.mean,
				_a.sd,
				ifnull(_b.label,_a.sys_name) as label,
				_b.text

			from %PRE%characteristics_states _a

			left join %PRE%characteristics_labels_states _b
				on _a.project_id=_b.project_id
				and _a.id=_b.state_id
				and _b.language_id=" . $language_id . "

			where 
				_a.project_id = ". $project_id ."
				" . ( !is_null($character_id)  ? "and _a.character_id = " . $character_id : "" ) ."
				" . ( !is_null($id)  ? "and _a.id = " . $id : "" ) ."
			order by
				show_order
			";

		$d=$this->freeQuery( $query );
		
		if ($d)
		{
			foreach((array)$d as $key=>$val)
			{
				$query ="
					select
						text,
						label,
						language_id
					from
						%PRE%characteristics_labels_states
					where
						project_id = ". $project_id ."
						and state_id = " . $val['id'] . "
					";

				$t=$this->freeQuery( ["query"=>$query,"fieldAsIndex"=>"language_id"] );
				$d[$key]['labels']=array_map(function($val){ unset($val['text'],$val['language_id']); return $val;},(array)$t);
				$d[$key]['texts']=array_map(function($val){ unset($val['label'],$val['language_id']); return $val;},(array)$t);


			}
			
			return  !is_null($id) ? $d[0] : $d;
		}
    }

    public function getCharacters( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$id = isset($params['id']) ? $params['id'] : null;
		$not_id = isset($params['not_id']) ? $params['not_id'] : null;

		if ( is_null($project_id) || is_null($language_id) )
			return;

		$query ="
			select

				_b.id,
				_b.type,
				_b.sys_name,
				ifnull(_c.label,_b.sys_name) as label

			from
				%PRE%characteristics _b

			left join
				%PRE%characteristics_labels _c
					on _b.project_id=_c.project_id
					and _b.id=_c.characteristic_id
					and _c.language_id=" . $language_id . "

			where 
				_b.project_id = ". $project_id ."
				" . ( !is_null($id) ? "and _b.id = " . $id : "" ) ."
				" . ( !is_null($not_id) ? "and _b.id not in (" . implode(",",$not_id)  .")" : "" ) ."
			";

		$d=$this->freeQuery( $query );

		if ($d)
		{
			foreach((array)$d as $key=>$val)
			{
				$query ="
					select
						*
					from
						%PRE%characteristics_labels
					where
						project_id = ". $project_id ."
						and characteristic_id = " . $val['id'] . "
					";

				$d[$key]['labels']=$this->freeQuery( ["query"=>$query,"fieldAsIndex"=>"language_id"] );

				if (strpos($val['label'],'|')!==false)
				{
					$boom=explode('|',$val['label']);
					$d[$key]['short_label']=$boom[0];
				}
				else
				{
					$d[$key]['short_label']=$val['label'];
				}
			}
			
			return  !is_null($id) ? $d[0] : $d;
		}
    }


    public function getTaxaRelations( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id=isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$type_id_preferred=isset($params['type_id_preferred']) ? $params['type_id_preferred'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if ( is_null($project_id) || is_null($matrix_id) || is_null($language_id) || is_null($type_id_preferred) || is_null($taxon_id) )
			return;

		$taxa=$this->freeQuery( "
			select
			
				_b.id,
				_b.taxon,
				_b.parent_id,
				_c.lower_taxon,
				_c.keypath_endpoint,
				_c.rank_id as base_rank_id,
				if(ifnull(_a.id,0)=0,0,1) as already_in_matrix,
				_names.name
	
			from %PRE%taxa _b

			left join
				%PRE%names _names
					on _b.id = _names.taxon_id
					and _b.project_id = _names.project_id
					and _names.type_id = ".$type_id_preferred."
					and _names.language_id=".$language_id."

			left join
				%PRE%matrices_taxa _a
					on _a.project_id=_b.project_id
					and _a.taxon_id = _b.id
					and _a.matrix_id = ". $matrix_id."

			left join
				%PRE%projects_ranks _c
					on _b.project_id=_c.project_id
					and _b.rank_id = _c.id

			right join
				%PRE%taxa_relations _r
					on _b.project_id=_r.project_id
					and _b.id = _r.relation_id
					and _r.ref_type = 'taxon'

			where 
				_b.project_id = ". $project_id ."
				and _r.taxon_id = " . $taxon_id . "

			order by
				_c.rank_id asc, _b.taxon asc
				");

			return $taxa;

		
    }

}
