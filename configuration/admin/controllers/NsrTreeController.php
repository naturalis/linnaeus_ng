<?php

include_once ('Controller.php');
class NsrTreeController extends Controller
{
	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'name_types'
    );
    public $usedHelpers = array(
    );
    public $cacheFiles = array(
    );
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_tree.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_tree.js'
        )
    );
    public $controllerPublicName = 'Soortenregister beheer';
    public $includeLocalMenu = false;
	private $_nameTypeIds;

    public function __construct ()
    {
        parent::__construct();
        $this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize ()
    {
		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
    }

    public function indexAction()
    {
		$tree=$this->restoreTree();

		if (is_null($tree))
			$this->smarty->assign('nodes',json_encode($this->getTreeNode(array('node'=>false,'count'=>'species'))));
		else
			$this->smarty->assign('tree',json_encode($tree));

		$this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

        if ($this->requestData['action'] == 'get_lookup_list')
		{
            $return=$this->getLookupList($this->requestData);
        } else
		if ($this->rHasVal('action', 'get_tree_node'))
		{
			$return=json_encode($this->getTreeNode($this->requestData));
        }
		else
		if ($this->rHasVal('action', 'store_tree'))
		{	
	        $_SESSION['admin']['user']['species']['tree']=$this->requestData['tree'];
			$return='saved';
        }
		else
		if ($this->rHasVal('action', 'restore_tree'))
		{
	        $return=json_encode($this->restoreTree());
        }

        
        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage();
    }

    private function getLookupList($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly=isset($p['match_start']) ? $p['match_start']==1 : false;
        $getAll=isset($p['get_all']) ? $p['get_all']==1 : false;
        $concise=isset($p['concise']) ? $p['concise']==1 : false;
        $formatted=isset($p['formatted']) ? $p['formatted']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;

        if (empty($search) && !$getAll)
            return;

        $taxa = $this->models->Taxon->freeQuery("
			select * from
			(
			". ($taxaOnly ? "" : "

				select
					_a.taxon_id as id,
					_a.name as label,
					_b.rank_id,
					_c.rank_id as base_rank_id,
					_b.taxon as taxon,
					'names' as source,
					_d.rank
				from
					%PRE%names _a

				left join
					%PRE%taxa _b
						on _a.project_id=_b.project_id
						and _a.taxon_id=_b.id

				left join
					%PRE%projects_ranks _c
						on _b.project_id=_c.project_id
						and _b.rank_id=_c.id

				left join
					%PRE%ranks _d
					on _c.rank_id=_d.id


				where
					_a.project_id =  ".$this->getCurrentProjectId()."
					and _a.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
					and _a.type_id != ".
						(
							isset($this->_nameTypeIds[PREDICATE_VALID_NAME]['id']) ?
								$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] : -1
						)."
				union

			")."

			select
				_b.id,
				_b.taxon as label,
				_b.rank_id,
				_d.rank_id as base_rank_id,
				_b.taxon as taxon,
				'taxa' as source,
				_e.rank
			from
				%PRE%taxa _b

			left join
				%PRE%projects_ranks _d
					on _b.project_id=_d.project_id
					and _b.rank_id=_d.id

			left join
				%PRE%ranks _e
				on _d.rank_id=_e.id

			where
				_b.project_id = ".$this->getCurrentProjectId()."
				and _b.taxon like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'

			) as unification
			".($rankAbove ? "where base_rank_id < ".$rankAbove : "")."
			order by label, base_rank_id
			limit ".$maxResults
		);

        foreach ((array) $taxa as $key => $val)
		{
			if ($val['source']=='taxa')
			{
				if ($formatted)
					$taxa[$key]['label']=$this->formatTaxon($val);
			}
			else
			{
				if ($formatted)
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$this->formatTaxon($val).')';
				else
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$val['taxon'].')';
			}

			$taxa[$key]['label']=$taxa[$key]['label'].' ['.$val['rank'].']';

			unset($taxa[$key]['taxon']);
			unset($taxa[$key]['source']);
		}

		return
			$this->makeLookupList(array(
				'data'=>$taxa,
				'module'=>'species',
				'url'=>'../species/taxon.php?id=%s',
				'encode'=>true,
				'isFullSet'=>count($taxa)<$maxResults
			));

    }

	private function restoreTree()
	{
		return isset($_SESSION['admin']['user']['species']['tree']) ?  $_SESSION['admin']['user']['species']['tree'] : null;
	}

	private function treeGetTop()
	{
		/*
			get the top taxon = no parent
			"_r.id < 10" added as there might be orphans, which are ususally low-level ranks 
		*/
		$p=$this->models->Taxon->freeQuery("
			select
				_a.id,
				_a.taxon,
				_r.rank
			from
				%PRE%taxa _a
					
			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where 
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.parent_id is null
				and _r.id < 10

		");

		if ($p && count((array)$p)==1)
		{
			$p=$p[0]['id'];
		} 
		else
		{
			$p=null;
		}

		if (count((array)$p)>1)
		{
			$this->addError('Detected multiple high-order taxa without a parent. Unable to determine which is the top of the tree.');
		}

		return $p;
	}
	
	private function getTreeNode($p)
	{
		
		$node=isset($p['node']) && $p['node']!==false ? $p['node'] : $this->treeGetTop();
		$count=isset($p['count']) && in_array($p['count'],array('none','taxon','species')) ? $p['count'] : 'none';

		if (is_null($node))
			return;

		$taxa=
			$this->models->Taxon->freeQuery("
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

				left join %PRE%projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id

				left join %PRE%labels_projects_ranks _q
					on _a.rank_id=_q.project_rank_id
					and _a.project_id = _q.project_id
					and _q.language_id=".$this->getDefaultProjectLanguage()."

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				left join %PRE%commonnames _l
					on _a.id=_l.taxon_id
					and _a.project_id=_l.project_id
					and _l.language_id=".$this->getDefaultProjectLanguage()."

				left join %PRE%names _k
					on _a.id=_k.taxon_id
					and _a.project_id=_k.project_id
					and _k.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
					and _k.language_id=".$this->getDefaultProjectLanguage()."
	
				left join %PRE%names _m
					on _a.id=_m.taxon_id
					and _a.project_id=_m.project_id
					and _m.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
	
				where 
					_a.project_id = ".$this->getCurrentProjectId()." 
					and (_a.id = ".$node." or _a.parent_id = ".$node.")

				order by
					label
			");
			
		$taxon=$progeny=array();

		foreach((array)$taxa as $key=>$val)
		{
			if ($count=='taxon') 
			{
				$d=$this->models->Taxon->freeQuery("
					select
						count(*) as total
					from
						%PRE%taxon_quick_parentage
					where 
						project_id = ".$this->getCurrentProjectId()." 
						and MATCH(parentage) AGAINST ('".$val['id']."' in boolean mode)
					");
					
				$val['child_count']['taxon']=$d[0]['total'];
			}
			else
			if ($count=='species') 
			{
	
				$d=$this->models->Taxon->freeQuery(array(
					'query'=> "
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
						
						left join %PRE%projects_ranks _f
							on _e.rank_id=_f.id
							and _e.project_id = _f.project_id
						
						where
							_sq.project_id=".$this->getCurrentProjectId()."
							and _sp.presence_id is not null
							and _f.rank_id".($val['base_rank']>=SPECIES_RANK_ID ? ">=" : "=")." ".SPECIES_RANK_ID."

							and MATCH(_sq.parentage) AGAINST ('".$val['id']."' in boolean mode)
							
						group by _sr.established",
					'fieldAsIndex' =>"established"
					)
				);
	
				$val['child_count']=
					array(
						'total'=>
							(int)
								(isset($d['undefined']['total'])?$d['undefined']['total']:0)+
								(isset($d[0]['total'])?$d[0]['total']:0)+
								(isset($d[1]['total'])?$d[1]['total']:0),
						'established'=>
								(int)(isset($d[1]['total'])?$d[1]['total']:0),
						'not_established'=>
								(int)(isset($d[0]['total'])?$d[0]['total']:0)
					);
			} else
			{
				$val['child_count']=null;
			}
			
			if ($val['base_rank']>=SPECIES_RANK_ID)
			{
				if ($val['authorship']!='')
				{
					$val['taxon']=
						'<i>'.
						str_replace($val['authorship'],'',$val['taxon']).
						'</i>'.' '.$val['authorship'];
				}
				else
				{
					$val['taxon']=$this->formatTaxon($val);
				}
			}

			$val['label']=empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')';

			//unset($val['parent_id']);
			unset($val['is_hybrid']);
			unset($val['rank_id']);
			unset($val['base_rank']);

			if ($val['id']==$node)
			{
				$taxon=$val;
			}
			else 
			{
				$d=$this->models->Taxon->freeQuery("
					select
						count(*) as total
					from
						%PRE%taxa
					where 
						project_id = ".$this->getCurrentProjectId()." 
						and parent_id = ".$val['id']
					);
					
				$val['has_children']=$d[0]['total']>0;
				$progeny[]=$val;
			}
		}

		usort(
			$progeny,
			function($a,$b)
			{
				return (strtolower($a['label'])==strtolower($b['label']) ? 0 : (strtolower($a['label'])>strtolower($b['label']) ? 1 : -1)); 
			}
		);

		return
			array(
				'node'=>$taxon,
				'progeny'=>$progeny
			);
		
	}


}
