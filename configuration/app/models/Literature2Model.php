<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class Literature2Model extends AbstractModel
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

	public function getReference( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$literature2_id = isset($params['literature2_id']) ? $params['literature2_id'] : null;

		if ( is_null($project_id) ||  is_null($language_id) ||  is_null($literature2_id) )
			return;

		$query = "
			select
				_a.id,
				_a.label,
				_a.date,
				_a.author,
				ifnull(ifnull(_l2ptl.label,_l2pt.sys_label),_a.publication_type) as publication_type,
				_a.order_number,
				_a.citation,
				_a.source,
				_a.publisher,
				_a.publishedin,
				_a.publishedin_id,
				_c.label as publishedin_name,
				_a.pages,
				_a.volume,
				_a.periodical,
				_a.periodical_id,
				_d.label as periodical_name,
				_a.language_id,
				_e.label as language_name,
				_a.external_link

			from %PRE%literature2 _a

			left join %PRE%literature2 _c
				on _a.publishedin_id = _c.id 
				and _a.project_id=_c.project_id
				
			left join %PRE%literature2 _d
				on _a.periodical_id = _d.id 
				and _a.project_id=_d.project_id
				
			left join %PRE%labels_languages _e
				on _a.language_id = _e.language_id 
				and _a.project_id=_e.project_id
				and _e.label_language_id = ".$language_id."

			left join %PRE%literature2_publication_types _l2pt
				on _a.publication_type_id = _l2pt.id 
				and _a.project_id=_l2pt.project_id
			
			left join %PRE%literature2_publication_types_labels _l2ptl
				on _a.publication_type_id = _l2ptl.publication_type_id 
				and _a.project_id=_l2ptl.project_id
				and _l2ptl.language_id = ".$language_id."
				
			where _a.project_id = ".$project_id."
			and _a.id =".$literature2_id
		;	

		$d=$this->freeQuery( $query );
		$data=$d[0];
		
		$query="
			select
				_b.name

			from %PRE%literature2_authors _a

			left join %PRE%actors _b
				on _a.actor_id = _b.id 
				and _a.project_id=_b.project_id

			where
				_a.project_id = ".$project_id."
				and _a.literature2_id =".$literature2_id."
			order by _a.sort_order,_b.name
		";
		
		$data['authors']=$this->freeQuery( $query );

		return $data;
	}


    // Used in original LiteratureController
	public function getLookupList ($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$search = isset($params['search']) ? $params['search'] : null;
		$matchStartOnly = isset($params['match_start']) ? $params['match_start']=='1' : false;
		$getAll = isset($params['get_all']) ? $params['get_all']=='1' : false;

		if (is_null($project_id) || is_null($search))
		{
			return;
		}

		$match = $matchStartOnly ?
            mysqli_real_escape_string($this->databaseConnection, $search).'%' :
		    '%'.mysqli_real_escape_string($this->databaseConnection, $search).'%';

		$query = '
    		select
            id,
            concat(
            	author_first,
            	(
            		if(multiple_authors=1,
            			\' et al.\',
            			if(author_second!=\'\',concat(\' & \',author_second),\'\')
            		)
            	),
            	\', \',
            	if(isnull(`year`)!=1,`year`,\'\'),
            	if(isnull(suffix)!=1,suffix,\'\'),
            	if(isnull(year_2)!=1,
            		concat(
            			if(year_separator!=\'-\',
            				concat(
            					\' \',
            					year_separator,
            					\' \'
            				),
            				year_separator
            			),
            			year_2,
            			if(isnull(suffix_2)!=1,
            				suffix_2,
            				\'\')
            			)
            			,\'\'
            		)
            ) as label,
            lower(author_first) as _a1,
            lower(author_second) as _a2,
            `year`
            from %PRE%literature
            where project_id = '.
            	$project_id .
            	(!$getAll ? ' and (author_first like "'.$match.'" or author_second like "'.$match.'" or `year` like "'.$match.'")' : null ).'
            order by _a1,_a2,`year`';

		return $this->freeQuery($query);
    }


    public function getTitleAlphabet($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        $query = "
            select
				distinct
                    if(
                        ord(substr(lower(_a.label),1,1))<97||ord(substr(lower(_a.label),1,1))>122,
                        '#',
                        substr(lower(_a.label),1,1)
                    )
                as letter
			from
				%PRE%literature2 _a
			where
				_a.project_id = " . $project_id . "
			order by letter";

        return $this->freeQuery( $query );
    }


    public function getAuthorAlphabet($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        $query = "
            select distinct * from (
				select
					distinct if(ord(substr(lower(_a.author),1,1))<97||ord(substr(lower(_a.author),1,1))>122,'#',substr(lower(_a.author),1,1)) as letter
				from
					%PRE%literature2 _a
				where
					_a.project_id = ".$project_id."
			union
				select
					distinct if(ord(substr(lower(_f.name),1,1))<97||ord(substr(lower(_f.name),1,1))>122,'#',substr(lower(_f.name),1,1)) as letter

				from
					%PRE%literature2 _a

				left join %PRE%actors _f
					on _a.actor_id = _f.id
					and _a.project_id=_f.project_id

				where
					_a.project_id = ".$project_id."
			) as unification
			order by letter";

        return $this->freeQuery( $query );
    }


    public function getReferencedTaxa( $params )
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $literature_id = isset($params['literature_id']) ? $params['literature_id'] : null;

        if ( is_null($project_id) || is_null($literature_id) ) return;

        $query = "
            select

				_a.id,
				_a.taxon,
				_r.id as base_rank_id,
				_r.rank

			from %PRE%taxa _a

			right join %PRE%literature_taxa _t
				on _a.project_id=_t.project_id
				and _a.id=_t.taxon_id

			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where
				_a.project_id = ".$project_id."
				and _t.literature_id= " . $literature_id . " 

			order by
				_a.taxon
			";

        return $this->freeQuery($query);
    }

}
?>