<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

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
						_b.taxon
			
					from %PRE%matrices_taxa _a
			
					left join %PRE%taxa _b
						on _a.project_id=_b.project_id
						and _a.taxon_id = _b.id

					right join %PRE%taxon_quick_parentage _sq
						on _a.taxon_id = _sq.taxon_id
						and _sq.project_id = " . $project_id . " 
			
					where 
						_a.project_id = ". $project_id ."
						and _a.matrix_id = ". $matrix_id."
						and (
								MATCH(_sq.parentage) AGAINST ('" . $top . "' in boolean mode)
								or _a.taxon_id=" . $top . " 
							)		
					order by
						_b.taxon
					");

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
					_b.taxon
		
				from %PRE%matrices_taxa _a
		
				left join %PRE%taxa _b
					on _a.project_id=_b.project_id
					and _a.taxon_id = _b.id
		
				where 
					_a.project_id = ". $project_id ."
					and _a.matrix_id = ". $matrix_id."
	
				order by
					_b.taxon
				");

		}

		return $taxa;
    }

    public function getAllTaxaAndMatrixPresence( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$branch_tops=isset($params['branch_tops']) ? $params['branch_tops'] : null;
		
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
						_c.keypath_endpoint,
						if(ifnull(_a.id,0)=0,0,1) as already_in_matrix
			
					from %PRE%taxa _b
		
					left join %PRE%matrices_taxa _a
						on _a.project_id=_b.project_id
						and _a.taxon_id = _b.id
						and _a.matrix_id = ". $matrix_id."
		
					left join %PRE%projects_ranks _c
						on _b.project_id=_c.project_id
						and _b.rank_id = _c.id

					right join %PRE%taxon_quick_parentage _sq
						on _b.id = _sq.taxon_id
						and _sq.project_id = " . $project_id . " 

					where 
						_b.project_id = ". $project_id ."
						and (
								MATCH(_sq.parentage) AGAINST ('" . $top . "' in boolean mode)
								or _b.id=" . $top . " 
							)			
					order by
						_b.taxon
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
					_c.keypath_endpoint,
					if(ifnull(_a.id,0)=0,0,1) as already_in_matrix
		
				from %PRE%taxa _b
	
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
					_b.taxon
			");

		}

		return $taxa;

    }

	public function getCharactersNotInGroups( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id=isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id) || is_null($matrix_id) )
			return;

		$query="
			select
				_a.characteristic_id, _a.show_order, _c.id as characteristic_chargroup_id 

			from %PRE%characteristics_matrices _a

			left join %PRE%characteristics_chargroups _c
				on _c.characteristic_id = _a.characteristic_id
				and _c.project_id = _a.project_id

			where
				_a.matrix_id =" . $matrix_id . "
				and _a.project_id =" . $project_id . "

			and _c.id is null
		";
		
		return $this->freeQuery(array('query'=>$query,'fieldAsIndex' => 'characteristic_id'));
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

}
