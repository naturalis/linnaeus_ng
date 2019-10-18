<?php
include_once (__DIR__ . "/AbstractModel.php");

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

    public function getReferences($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $publicationTypeId = isset($params['publicationTypeId']) ? $params['publicationTypeId'] : null;

        if (is_null($projectId)) {
			return null;
		}

        $query = "
            select
				_a.id,
				_a.language_id,
				_a.label,
				_a.alt_label,
				_a.alt_label_language_id,
				_a.date,
				_a.author,
				_a.publication_type,
				ifnull(_a.publishedin,ifnull(_h.label,null)) as publishedin,
				ifnull(_a.periodical,ifnull(_i.label,null)) as periodical,
				_a.pages,
				_a.volume,
				_a.external_link,
				if(_a.actor_id=-1,1,0) as unparsed /* abuse: -1 means unparsed entry */

			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i
				on _a.periodical_id = _i.id
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$projectId."
				".(!is_null($publicationTypeId) ?
					"and ".
					(is_array($publicationTypeId) ?
						"_a.publication_type_id in (" . implode(",",array_map('intval',$publicationTypeId)). ")" :
						"_a.publication_type_id = " .
					        mysqli_real_escape_string($this->databaseConnection, (int)$publicationTypeId) ) :
					"" )."";

        $data = $this->freeQuery($query);
		
		foreach((array)$data as $key=>$val)
		{
			$query="
				select
					_b.name
	
				from %PRE%literature2_authors _a
	
				left join %PRE%actors _b
					on _a.actor_id = _b.id 
					and _a.project_id=_b.project_id
	
				where
					_a.project_id = ".$projectId."
					and _a.literature2_id =".$val['id']."
				order by _a.sort_order,_b.name
			";
		
			$data[$key]['authors']=$this->freeQuery( $query );
		}
		
		return $data;
    }

    public function getTitleAlphabet($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        $query = "
            select
				lower(_a.label) as label
			from
				%PRE%literature2 _a
			where
				_a.project_id = " . $project_id;
				
		$d=$this->freeQuery( $query );
		$r=array();
		foreach((array)$d as $val)
		{
			$a=substr(trim(preg_replace('/[^A-Za-z0-9]/','',strip_tags($val['label']))),0,1);
			$r[$a]['letter']=$a;
		}
		sort($r);
		return $r;
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

        $baseQuery = "
            select
				_a.id,
				_a.taxon,
				_a.parent_id,
				_r.id as base_rank_id,
				_r.rank

			from %PRE%taxa _a

			right join [join]

			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where [where]
            
			[group]";

        $joins = [
            // Taxa directly linked to literature
            [
                'join' => "
                    %PRE%literature_taxa _t
				    on _a.project_id=_t.project_id
				    and _a.id=_t.taxon_id
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _t.literature_id= " . $literature_id,
                'group' => ''
            ],
            // Taxa linked to presence status
            [
                'join' => "
                   %PRE%presence_taxa _pt on 
                       _a.project_id=_pt.project_id 
                       and _a.id=_pt.taxon_id 
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _pt.reference_id= " . $literature_id,
                'group' => ''
            ],
            // Taxa linked to traits
            [
                'join' => "
                   %PRE%traits_taxon_references _ttr on 
                   _a.project_id=_ttr.project_id 
                   and _a.id=_ttr.taxon_id  
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _ttr.reference_id= " . $literature_id,
                'group' => ''
            ],
            // Taxa linked to names
            [
                'join' => "
                   %PRE%names _n on 
                   _a.project_id=_n.project_id 
                   and _a.id=_n.taxon_id  
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _n.reference_id= " . $literature_id,
                'group' => 'group by _n.taxon_id'
            ],
        ];

        $taxa = [];
        foreach ($joins as $d) {
            $query = str_replace(
                ['[join]', '[where]', '[group]'],
                [$d['join'], $d['where'], $d['group']],
                $baseQuery
            );
            $new = $this->freeQuery($query);
            if ($new) {
                $taxa = array_merge($taxa, $new);
            }
        }

        $query = "
            select 
                   t3.id, 
                   t3.taxon, 
                   t3.parent_id, 
                   t5.id as base_rank_id, 
                   t5.rank

            from rdf as t1
            
            left join content_taxa as t2 
                on t1.subject_id = t2.id 
                and t1.project_id=t2.project_id
        
            left join taxa as t3 
                on t2.taxon_id = t3.id 
                and t2.project_id=t3.project_id
        
            left join projects_ranks as t4 
                on t3.project_id=t4.project_id 
                and t3.rank_id=t4.id 
        
            left join ranks t5 
                on t4.rank_id=t5.id 
            
            where 
                t1.project_id = $project_id 
                and t1.object_type = 'reference' 
                and t1.object_id = $literature_id
            
            group by t2.taxon_id ";

        $new = $this->freeQuery($query);
        if ($new) {
            $taxa = array_merge($taxa, $new);
        }

        usort($taxa, function($a, $b) {
            $r = $a['taxon'] <=> $b['taxon'];
            return $r;
        });

        $done = [];
        foreach ($taxa as $i => $taxon) {
            if (in_array($taxon['id'], $done)) {
                unset($taxa[$i]);
                continue;
            }
            $done[] = $taxon['id'];
        }

        return $taxa;
    }

    public function getReferenceAuthors($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($literatureId)) {
			return null;
		}

        $query = "
            select
				_a.actor_id,
				_b.name

			from %PRE%literature2_authors _a

			left join %PRE%actors _b
				on _a.actor_id = _b.id
				and _a.project_id=_b.project_id

			where
				_a.project_id = ".$projectId."
				and _a.literature2_id =".$literatureId."

			order by _a.sort_order,_b.name";

        return $this->freeQuery($query);
    }
	
}
