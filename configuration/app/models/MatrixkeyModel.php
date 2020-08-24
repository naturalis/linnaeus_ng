<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class MatrixKeyModel extends AbstractModel
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

    public function getMatrix( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id)  ||  is_null($language_id) )
			return;

		$query="
			select
					_a.id,
					_a.default,
					_b.name
				from
					%PRE%matrices _a

				left join %PRE%matrices_names _b
					on _a.project_id = _b.project_id
					and _a.id = _b.matrix_id
					and _b.language_id = " . $language_id ."

				where
					_a.project_id = " .  $project_id ."
					" . ( isset($matrix_id) && $matrix_id!='*' ? "and _a.id = " . $matrix_id : "" ) . "
				order by
					_a.default desc
		";

		$d=$this->freeQuery( array( "query"=>$query, "fieldAsIndex"=>"id") );

		return ( isset($matrix_id) && $matrix_id!='*' && isset($d[$matrix_id]) ? $d[$matrix_id] : ( isset($d) ? $d  : null ) );
	}

    public function getCharacterStates( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$state_id = isset($params['state_id']) ? $params['state_id'] : null;
		$characteristic_id = isset($params['characteristic_id']) ? $params['characteristic_id'] : null;

		if ( is_null($project_id)  ||  is_null($language_id) )
			return;

		$query="
			select
				_a.id,
				_a.characteristic_id,
				_a.file_name,
				_a.file_dimensions,
				_a.lower,
				_a.upper,
				_a.mean,
				_a.sd,
				_a.show_order,
				_b.type,
				_c.label,
				_c.text

			from %PRE%characteristics_states _a

			left join %PRE%characteristics _b
				on _a.characteristic_id = _b.id
				and _a.project_id=_b.project_id

			left join %PRE%characteristics_labels_states _c
				on _a.id = _c.state_id
				and _a.project_id=_c.project_id
				and _c.language_id=".$language_id."

			where
				_a.project_id=".$project_id."
				".( empty($state_id) ? "" : "and _a.id=" . $state_id )."
				".( empty($characteristic_id) ? "" : "and _a.characteristic_id=" . $characteristic_id )."

			order by
				_a.show_order
			"
		;

		$d=$this->freeQuery( $query );

        foreach ((array) $d as $key => $val)
		{
            $d[$key]['img_dimensions']=explode(':',$val['file_dimensions']);
		}

        return (isset($state_id) && $state_id!='*' && isset($d[0]) ? $d[0] : $d);
    }

    public function getVariationsInMatrix( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id) || is_null($language_id) || is_null($matrix_id) )
			return;

		$query="
			select
				_b.id,
				_b.taxon_id,
				_b.label,
				_c.language_id,
				_c.label,
				_c.label_type,
				'variation' as type

			from %PRE%matrices_variations _a

			right join %PRE%taxa_variations _b
				on _a.project_id=_b.project_id
				and _a.variation_id = _b.id

			left join %PRE%variations_labels _c
				on _a.project_id=_c.project_id
				and _a.variation_id = _c.variation_id
				and _c.language_id = ". $language_id ."

			where
				_a.project_id = ". $project_id ."
				and _a.matrix_id = ". $matrix_id."
			order by
				_c.label"
			;

		return $this->freeQuery( array( "query"=>$query, "fieldAsIndex"=>"variation_id") );

    }

    public function getMatricesInMatrix( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id) || is_null($language_id) || is_null($matrix_id) )
			return;

		$query="
			select
				distinct _a.ref_matrix_id as id,
				_b.name as label,
				'matrix' as type
			from
				%PRE%matrices_taxa_states _a

			left join %PRE%matrices_names _b
				on _a.project_id = _b.project_id
				and _a.ref_matrix_id = _b.matrix_id
				and _b.language_id = " . $language_id . "

			where
				_a.project_id = " . $project_id . "
				and _a.matrix_id = " . $matrix_id . "
				and _a.ref_matrix_id is not null
			";

		return $this->freeQuery( $query );
    }

    public function getEntityStates( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$type = isset($params['type']) ? $params['type'] : null;
		$id = isset($params['id']) ? $params['id'] : null;

		if ( is_null($project_id) || is_null($language_id) || is_null($matrix_id) || is_null($type)  || is_null($id) )
			return;

		$query="
			select
				_c.label as characteristic,
				_b.type,
				_b.type,
				_a.project_id,
				_a.state_id,
				_d.characteristic_id,
				_d.file_name,
				_d.lower,
				_d.upper,
				_d.mean,
				_d.sd,
				_e.label,
				ifnull(_g.label,_gg.label) as group_label

			from %PRE%matrices_taxa_states _a

			left join %PRE%characteristics_states _d
				on _a.project_id=_d.project_id
				and _a.state_id=_d.id

			left join %PRE%characteristics _b
				on _a.project_id=_b.project_id
				and _a.characteristic_id=_b.id

			left join %PRE%characteristics_labels _c
				on _a.project_id=_c.project_id
				and _a.characteristic_id=_c.characteristic_id
				and _c.language_id=".$language_id."

			left join %PRE%characteristics_labels_states _e
				on _a.state_id = _e.state_id
				and _a.project_id=_e.project_id
				and _e.language_id=".$language_id."

			left join %PRE%characteristics_chargroups _f
				on _a.project_id=_f.project_id
				and _a.characteristic_id=_f.characteristic_id

			left join %PRE%chargroups _gg
				on _f.project_id=_gg.project_id
				and _f.chargroup_id=_gg.id

			left join %PRE%chargroups_labels _g
				on _f.project_id=_g.project_id
				and _f.chargroup_id=_g.chargroup_id
				and _g.language_id=".$language_id."

			where
				_a.project_id = ".$project_id."
				and _a.matrix_id = ".$matrix_id."
				and _a.".($type=="variation" ? "variation_id" : ($type=="matrix" ? "ref_matrix_id" : "taxon_id"))."=".$id
		;

		$m=$this->freeQuery( $query );

		$res=array();
		foreach((array)$m as $val)
		{
			$d=explode('|',$val['characteristic']);
			$res[$val['characteristic_id']]['characteristic']=$d[0];
			//$res[$val['characteristic_id']]['explanation']=$d[1];
			$res[$val['characteristic_id']]['type'] = $val['type'];
			$res[$val['characteristic_id']]['group_label'] = $val['group_label'];
			$res[$val['characteristic_id']]['states'][$val['state_id']] = array(
				'characteristic_id'=>$val['characteristic_id'],
				'id'=>$val['state_id'],
				'file_name'=>$val['file_name'],
				'lower'=>$val['lower'],
				'upper'=>$val['upper'],
				'mean'=>$val['mean'],
				'sd'=>$val['sd'],
				'label'=>$val['label']
			);
		}

		return $res;
    }

	public function getFacetMenu( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id) || is_null($language_id) || is_null($matrix_id) )
			return;

		$query="
			select
				id,
				label,
				type,
				show_order_main,
				show_order_sub
			 from (

				select
					_a.id,
					ifnull(_c.label,_cdef.label) as label,
					'char' as type,
					_gmo.show_order as show_order_main,
					_b.show_order as show_order_sub

				from
					%PRE%characteristics _a

				right join %PRE%characteristics_matrices _b
					on _a.id = _b.characteristic_id
					and _a.project_id = _b.project_id
					and _b.matrix_id = " . $matrix_id . "

				right join %PRE%characteristics_labels _c
					on _a.project_id = _c.project_id
					and _a.id = _c.characteristic_id
					and _c.language_id = ". $language_id."

				right join %PRE%characteristics_labels _cdef
					on _a.project_id = _cdef.project_id
					and _a.id = _cdef.characteristic_id
					and _cdef.language_id = ". $language_id."

				left join %PRE%characteristics_chargroups _d
					on _a.project_id = _d.project_id
					and _a.id = _d.characteristic_id

				left join %PRE%chargroups _e
					on _d.project_id = _e.project_id
					and _d.chargroup_id = _e.id
					and _e.matrix_id = " . $matrix_id . "

				left join %PRE%gui_menu_order _gmo
					on _a.project_id = _gmo.project_id
					and _gmo.matrix_id = " . $matrix_id . "
					and _gmo.ref_id = _a.id
					and _gmo.ref_type='char'

				where
					_a.project_id = " . $project_id . "
					and _d.id is null

				union

				select
					_a.id,
					ifnull(_c.label,_a.label) as label,
					'group' as type,
					_gmo.show_order as show_order_main,
					_a.show_order as show_order_sub

				from
					%PRE%chargroups _a

				left join %PRE%chargroups_labels _c
					on _a.project_id = _c.project_id
					and _a.id = _c.chargroup_id
					and _c.language_id = ". $language_id."

				left join %PRE%gui_menu_order _gmo
					on _a.project_id = _gmo.project_id
					and _gmo.matrix_id = " . $matrix_id . "
					and _gmo.ref_id = _a.id
					and _gmo.ref_type='group'

				where
					_a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id ."

			) as unionized

			order by show_order_main, show_order_sub, label
		";

		return $this->freeQuery( $query );

	}

    public function getCharacter( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$characteristic_id = isset($params['characteristic_id']) ? $params['characteristic_id'] : null;

		if ( is_null($project_id) || is_null($language_id) || is_null($characteristic_id) )
			return;

		$query="
			select
				_a.id,
				_a.type,
				_c.label,
				if (_a.type='media'||_a.type='text','c','f') as prefix

			from
				%PRE%characteristics _a

			left join %PRE%characteristics_labels _c
				on _a.project_id=_c.project_id
				and _a.id=_c.characteristic_id
				and _c.language_id=".$language_id."

			where
				_a.project_id = " . $project_id . "
				and _a.id = " . $characteristic_id . "
		";

		$d=$this->freeQuery( $query );

		return isset($d[0]) ? $d[0] : null;
    }


	/*
		DOES NOT WORK YET - WORK IN PROGRESS

		states within the same charachters expand the result set,
		selected states across characters restrict the result set
		example: (red OR black) AND round

		REFAC2015: finish!

	*/
    public function getScoresLiberal( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$state_ids = isset($params['state_ids']) ? $params['state_ids'] : null;
		$character_ids = isset($params['character_ids']) ? $params['character_ids'] : null;
		$incUnknowns = isset($params['incUnknowns']) ? $params['incUnknowns'] : null;
		$stateCount = isset($params['stateCount']) ? $params['stateCount'] : null;
		$matrixVariationExists = isset($params['matrixVariationExists']) ? $params['matrixVariationExists'] : false;

		if ( is_null($project_id) || is_null($matrix_id)  || is_null($language_id) )
			return;


        $n = $stateCount + ($incUnknowns ? 1 : 0);
        $s = implode(',', $state_ids);
        $c = implode(',', $character_ids);

		$query="
        	select 'taxon' as type, _a.taxon_id as id, _b.state_id, _b.characteristic_id,
       				_c.is_hybrid as h, trim(_c.taxon) as l
        		from %PRE%matrices_taxa _a
        		left join %PRE%matrices_taxa_states _b
        			on _a.project_id = _b.project_id
        			and _a.matrix_id = _b.matrix_id
        			and _a.taxon_id = _b.taxon_id
        			and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
		        left join %PRE%taxa _c
			        on _a.taxon_id = _c.id
		        where _a.project_id = " . $project_id . "
			        and _a.matrix_id = " . $matrix_id . "
        		group by _a.taxon_id
        	union all
        	select 'matrix' as type, _a.id as id, _b.state_id, _b.characteristic_id,
			        0 as h, trim(_c.name) as l
        		from  %PRE%matrices _a
        		join %PRE%matrices_taxa_states _b
        			on _a.project_id = _b.project_id
        			and _a.id = _b.ref_matrix_id
        			and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
		        left join %PRE%matrices_names _c
			        on _b.ref_matrix_id = _c.id
        			and _c.language_id = " . $language_id . "
        		where _a.project_id = " . $project_id . "
        			and _b.matrix_id = " . $matrix_id . "
        		group by id" . ($matrixVariationExists ? "
			union all
			select 'variation' as type, _a.variation_id as id, _b.state_id, _b.characteristic_id,
				0 as h, trim(_c.label) as l
				from  %PRE%matrices_variations _a
				left join %PRE%matrices_taxa_states _b
					on _a.project_id = _b.project_id
					and _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
					and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
				left join %PRE%taxa_variations _c
					on _a.variation_id = _c.id
				where _a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id . "
				group by _a.variation_id" : "")."
			order by characteristic_id"
        ;

		return $this->freeQuery( $query );
    }

    public function getScoresRestrictive( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$state_ids = isset($params['state_ids']) ? $params['state_ids'] : null;
		$character_ids = isset($params['character_ids']) ? $params['character_ids'] : null;
		$incUnknowns = isset($params['incUnknowns']) ? $params['incUnknowns'] : null;
		$stateCount = isset($params['stateCount']) ? $params['stateCount'] : null;
		$matrixVariationExists = isset($params['matrixVariationExists']) ? $params['matrixVariationExists'] : false;

		if ( is_null($project_id) || is_null($matrix_id)  || is_null($language_id) )
			return;

        $n = $stateCount;
        $s = implode(',', $state_ids);

		// query to get all taxa, matrices and variations, including their matching percentage
		$query="
        	select
				'taxon' as type,
				_a.taxon_id as id,
				count(_b.state_id) as matching_states,
				round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as score,
				trim(_c.taxon) as label

			from
				%PRE%matrices_taxa _a

			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _a.matrix_id = _b.matrix_id
				and _a.taxon_id = _b.taxon_id
				and _b.state_id in (" . $s . ")

			left join %PRE%taxa _c
				on _a.project_id = _c.project_id
				and _a.taxon_id = _c.id

			where
				_a.project_id = " . $project_id . "
				and _a.matrix_id = " . $matrix_id . "

			group by _a.taxon_id

        	union all

        	select
				'matrix' as type,
				_a.id as id,
				count(_b.state_id) as matching_states,
				round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as score,
				trim(_c.name) as label

			from
				%PRE%matrices _a

			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _b.matrix_id = " . $matrix_id . "
				and _a.id = _b.ref_matrix_id
				and _b.state_id in (" . $s . ")

			left join %PRE%matrices_names _c
				on _a.project_id = _c.project_id
				and _a.id = _c.matrix_id
				and _c.language_id = " . $language_id . "

			where
				_a.project_id = " . $project_id . "
				and _a.id != " . $matrix_id . "

			group by id" .

			( $matrixVariationExists ? "

				union all

				select
					'variation' as type,
					_a.variation_id as id,
					count(_b.state_id) as matching_states,
					round((if(count(_b.state_id)>" . $n . "," . $n . ", count(_b.state_id))/" . $n . ")*100,0) as score,
					trim(_d.taxon) as label

				from
					%PRE%matrices_variations _a

				left join %PRE%matrices_taxa_states _b
					on _a.project_id = _b.project_id
					and _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
					and _b.state_id in (" . $s . ")

				left join %PRE%taxa_variations _c
					on _a.project_id = _c.project_id
					and _a.variation_id = _c.id

				left join %PRE%taxa _d
					on _a.project_id = _d.project_id
					and _c.taxon_id = _d.id

				where
					_a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id . "

				group by
					_a.variation_id" :  ""
			)
		;

		$results=$this->freeQuery( $query );

		/*
			"unknowns" are taxa for which *no* state has been defined within a certain character.
			note that this is different froam having a *different* state within that character. if
			there is a character "colour", and taxon A has the state "green", taxon B has the
			state "brown" and taxon C has no state for colour, then selecting "brown" with 'Treat
			unknowns as matches' set to false will yield A:0%, B:100%, C:0%. selecting "brown"
			with 'Treat unknowns as matches' set to true will yield A:0%, B:100%, C:100%.
			it can be seen as a 'rather safe than sorry' setting.
		*/
		if ($incUnknowns)
		{

			$c = implode(',', $character_ids);

			$unknowns=array('taxon'=>array(),'matrix'=>array(),'variation'=>array());

			foreach((array)$c as $character)
			{
				$q = "
					select
						'taxon' as type,
						_a.taxon_id as id,
						trim(_c.taxon) as label
					from
						%PRE%matrices_taxa _a

					left join %PRE%taxa _c
						on _a.project_id = _c.project_id
						and _a.taxon_id = _c.id

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.taxon_id = _b.taxon_id
						and _b.characteristic_id =".$character."


					where
						_a.project_id = " . $project_id . "
						and _a.matrix_id = " . $matrix_id . "

					group by
						_a.taxon_id

					having
						count(_b.id)=0

				union all

					select
						'matrix' as type,
						_a.id as id,
						trim(_c.name) as label
					from
						%PRE%matrices _a

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _b.matrix_id = " . $matrix_id . "
						and _a.id = _b.ref_matrix_id
						and _b.characteristic_id =".$character."

					left join %PRE%matrices_names _c
						on _a.project_id = _c.project_id
						and _a.id = _c.matrix_id
						and _c.language_id = " . $language_id . "

					where
						_a.project_id = " . $project_id . "
						and _a.id != " . $matrix_id . "

					group by
						_a.id
					having
						count(_b.id)=0

				".( $matrixVariationExists ? "

				union all

					select
						'variation' as type,
						_a.variation_id as id,
						trim(_d.taxon) as label

					from
						%PRE%matrices_variations _a

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.variation_id = _b.variation_id
						and _b.characteristic_id =".$character."

					left join %PRE%taxa_variations _c
						on _a.project_id = _c.project_id
						and _a.variation_id = _c.id

					left join %PRE%taxa _d
						on _a.project_id = _d.project_id
						and _c.taxon_id = _d.id

					where

						_a.project_id = " . $project_id . "
						and _a.matrix_id = " . $matrix_id . "

					group by
						_a.variation_id

					having
						count(_b.id)=0
				" : "" );

		        $rrr=$this->freeQuery( $q );

				foreach((array)$rrr as $r)
				{
					switch($r['type'])
					{
						case 'taxon':
							$unknowns['taxon'][$r['id']]=$r;
							isset($unknowns['taxon'][$r['id']]['matching_states']) ?
								$unknowns['taxon'][$r['id']]['matching_states']++ :
								$unknowns['taxon'][$r['id']]['matching_states']=1;

							$unknowns['taxon'][$r['id']]['score'] =
								round(($unknowns['taxon'][$r['id']]['matching_states']/$n)*100);
							break;

						case 'matrix':
							$unknowns['matrix'][$r['id']]=$r;
							isset($unknowns['matrix'][$r['id']]['matching_states']) ?
								$unknowns['matrix'][$r['id']]['matching_states']++ :
								$unknowns['matrix'][$r['id']]['matching_states']=1;

							$unknowns['matrix'][$r['id']]['score'] =
								round(($unknowns['matrix'][$r['id']]['matching_states']/$n)*100);
							break;

						case 'variation':
							$unknowns['variation'][$r['id']]=$r;
							isset($unknowns['variation'][$r['id']]['matching_states']) ?
								$unknowns['variation'][$r['id']]['matching_states']++ :
								$unknowns['variation'][$r['id']]['matching_states']=1;

							$unknowns['variation'][$r['id']]['score'] =
								round(($unknowns['variation'][$r['id']]['matching_states']/$n)*100);
							break;
					}
				}
			}

			foreach((array)$results as $key => $val)
			{
				if (isset($unknowns[$val['type']][$val['id']]))
				{
					$temp=$unknowns[$val['type']][$val['id']];
					$results[$key]['matching_states']+=$temp['matching_states'];
					$results[$key]['score']=round(($results[$key]['matching_states']/$n)*100);
					unset($unknowns[$val['type']][$val['id']]);
				}
			}

			foreach((array)$unknowns as $type)
			{
				foreach((array)$type as $key => $val)
				{
					array_push($results,$val);
				}
			}
		}

        return $results;
    }

    public function getRemainingStateCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id) || is_null($matrix_id) )
			return;

   		$c=$this->getRemainingCountClauses();

		$dT = $c['dT'];
		$fsT = $c['fsT'];
		$dV = $c['dV'];
		$fsV = $c['fsV'];
		$dM = $c['dM'];
		$fsM = $c['fsM'];

		$s = array();

        /*
        find the number of taxon/state-connections that exist, grouped by state, but only for taxa that
        have the already selected states, unless no states have been selected at all, in which case we just
        return them all
        */

        $query = "
        	select sum(tot) as tot, state_id, characteristic_id
        	from (
        		select count(distinct _a.taxon_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
	        		from %PRE%matrices_taxa_states _a
	        		" . (!empty($dT) ? $dT : "") . "
					" . (!empty($fsT) ?  $fsT : ""). "
					where _a.project_id = " . $project_id . "
						and _a.matrix_id = " . $matrix_id . "
						and _a.taxon_id not in
							(select taxon_id from %PRE%taxa_variations where project_id = " . $project_id . ")
					group by _a.state_id
				union all
				select count(distinct _a.variation_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
					from %PRE%matrices_taxa_states _a
					" . (!empty($dV) ? $dV : "") . "
					" . (!empty($fsV) ? $fsV : "") . "
					where _a.project_id = " . $project_id . "
						and _a.matrix_id = " . $matrix_id . "
					group by _a.state_id
				union all
				select count(distinct _a.ref_matrix_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
					from %PRE%matrices_taxa_states _a
					" . (!empty($dM) ? $dM : "") . "
					" . (!empty($fsM) ? $fsM : "") . "
					where _a.project_id = " . $project_id . "
						and _a.matrix_id = " . $matrix_id . "
					group by _a.state_id


			) as q1
			group by q1.state_id
			";

		return $this->freeQuery( $query );
    }

    public function getCharacterCounts( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id) || is_null($matrix_id) )
			return;

   		$c=$this->getRemainingCountClauses();

		$dT = $c['dT'];
		$fsT = $c['fsT'];
		$dV = $c['dV'];
		$fsV = $c['fsV'];
		$dM = $c['dM'];
		$fsM = $c['fsM'];

        $query = "
        	select
				sum(taxon_count) as taxon_count,
				sum(distinct_state_count) as distinct_state_count,
				characteristic_id
        	from (
        		select
					_a.characteristic_id as characteristic_id,
					count(distinct _a.taxon_id) as taxon_count,
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dT) ? $dT : "") . "
					" . (!empty($fsT) ?  $fsT : ""). "
				where
					_a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id . "
					and _a.taxon_id not in
						(select taxon_id from %PRE%taxa_variations where project_id = " . $project_id . ")
				group by
					_a.characteristic_id

				union all

				select
					_a.characteristic_id as characteristic_id,
					count(distinct _a.variation_id) as taxon_count,
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dV) ? $dV : "") . "
					" . (!empty($fsV) ? $fsV : "") . "
				where
					_a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id . "
				group by
					_a.characteristic_id

				union all

				select
					_a.characteristic_id as characteristic_id,
					count(distinct _a.ref_matrix_id) as taxon_count,
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dM) ? $dM : "") . "
					" . (!empty($fsM) ? $fsM : "") . "
				where
					_a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id . "
				group by
					_a.characteristic_id

			) as q1
			group by q1.characteristic_id
			";

        return $this->freeQuery( $query );
    }

    public function getSearchResults( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$search = isset($params['search']) ? $params['search'] : null;

		if ( is_null($project_id) || is_null($language_id) || is_null($matrix_id) || is_null($search) )
			return;


		$search=$this->escapeString(strtolower($search));

		// n.b. don't change to 'union all'
		$query="
			select * from (
				select
					'variation' as type,
					_a.variation_id as id,
					trim(_c.label) as label,
					_c.taxon_id as taxon_id,
					_d.taxon as taxon,
					_e.commonname as commonname

				from
					%PRE%matrices_variations _a

				left join %PRE%taxa_variations _c
					on _a.variation_id = _c.id
					and _c.project_id = " . $project_id . "

				left join %PRE%taxa _d
					on _c.taxon_id = _d.id
					and _d.project_id = " . $project_id . "

                left join %PRE%commonnames _e
                    on _e.taxon_id = _d.id
                    and _e.language_id = $language_id
                    and _e.project_id = $project_id

				left join %PRE%matrices_taxa_states _b
					on _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
					and _b.taxon_id = _d.id
                    and _b.project_id = " . $project_id . "

				where _a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id . "
					and (
					    _c.label like '%". $search ."%' or 
					    _e.commonname like '%". $search ."%' or 
					    _d.taxon like '%". $search ."%'
					)

				union

				select
					'taxon' as type,
					_a.taxon_id as id,
					trim(_c.taxon) as label,
					_a.taxon_id as taxon_id,
					_c.taxon as taxon,
					_d.commonname as commonname

				from
					%PRE%matrices_taxa _a

				left join %PRE%matrices_taxa_states _b
					on _a.matrix_id = _b.matrix_id
					and _a.taxon_id = _b.taxon_id
					and _b.project_id = " . $project_id . "

				left join %PRE%taxa _c
					on _a.taxon_id = _c.id
					and _c.project_id = " . $project_id . "

				left join %PRE%commonnames _d
					on _a.taxon_id = _d.taxon_id
					and _d.language_id = ".$language_id ."
					and _d.project_id = " . $project_id . "

				where _a.project_id = " . $project_id . "
					and _a.matrix_id = " . $matrix_id . "
					and (lower(_c.taxon) like '%". $search ."%' or lower(_d.commonname) like '%". $search ."%')
			) as unionized
			order by label
			";

        return $this->freeQuery( $query );
    }

    public function setRemainingCountClauses( $clause )
    {
		$this->remainingCountClauses=$clause;
	}

    private function getRemainingCountClauses()
    {
		return $this->remainingCountClauses;
	}

    public function getAllMatrices( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;

		if ( is_null($project_id) || is_null($language_id) )
			return;

		$query="
			select
				_a.id,
				_b.name as label
			from
				%PRE%matrices _a

			left join %PRE%matrices_names _b
				on _a.project_id = _b.project_id
				and _a.id = _b.matrix_id
				and _b.language_id = " . $language_id . "

			where
				_a.project_id = " . $project_id . "
			order by
				_a.default desc, _b.name asc
			";

		return $this->freeQuery( $query );
    }

    public function getTaxaInMatrix( $p )
    {
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$matrix_id = isset($p['matrix_id']) ? $p['matrix_id'] : null;
		$language_id = isset($p['language_id']) ? $p['language_id'] : null;
		$preferred_nametype_id = isset($p['preferred_nametype_id']) ? $p['preferred_nametype_id'] : null;

		$limit = isset($p['limit']) ? $p['limit'] : null;

		if ( is_null($project_id) || is_null($matrix_id) || is_null($language_id) || is_null($preferred_nametype_id) )
			return;

		$query="
			select
				_a.id,
				_a.id as taxon_id,
				_a.taxon,
				_a.author,
				_a.parent_id,
				_a.rank_id,
				_a.taxon_order,
				_a.is_hybrid,
				_a.list_level,
				_a.is_empty,
				'taxon' as type,
				_c.name as commonname

			from
				%PRE%taxa _a

			left join
				%PRE%matrices_taxa  _b
					on _a.project_id = _b.project_id
					and _a.id = _b.taxon_id

			left join
				%PRE%names _c
					on _a.project_id = _c.project_id
					and _a.id = _c.taxon_id
					and _c.type_id= " . $preferred_nametype_id . "
					and _c.language_id = " . $language_id . "

			where
				_a.project_id = " . $project_id ."
				and _b.matrix_id = ". $matrix_id ."
			" . ( isset($limit) ? "limit " . $limit : "" ) . "
		";

		return $this->freeQuery( $query );
	}

}
