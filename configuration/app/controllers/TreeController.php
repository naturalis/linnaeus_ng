<?php

include_once ('SpeciesController.php');

class TreeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
	}

    public function __destruct()
    {
        parent::__destruct();
    }

    public function indexAction()
    {
        $this->redirect('tree.php');
    }

	public function treeAction()
	{
		$this->printPage('tree');
	}
	
    public function ajaxInterfaceAction ()
    {
		$return='error';
        
		if ($this->rHasVal('action', 'get_tree_node'))
		{
			$return=json_encode($this->getTreeNode(array('node'=>$this->requestData['node'],'count'=>$this->requestData['count'])));
        }
		else
		if ($this->rHasVal('action', 'store_tree'))
		{	
	        $_SESSION['app'][$this->spid()]['species']['tree']=$this->requestData['tree'];
			$return='saved';
        }
		else
		if ($this->rHasVal('action', 'restore_tree'))
		{
	        $return=
				json_encode(
					isset($_SESSION['app'][$this->spid()]['species']['tree']) ? 
						$_SESSION['app'][$this->spid()]['species']['tree'] : 
						null
				);
        }
        
        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
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
					_k.name,
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
					and _q.language_id=".$this->getCurrentLanguageId()."

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				left join %PRE%names _k
					on _a.id=_k.taxon_id
					and _a.project_id=_k.project_id
					and _k.type_id=
					(
						select 
							id 
						from 
							%PRE%name_types 
						where 
							project_id = ".$this->getCurrentProjectId()." 
							and nametype='".PREDICATE_PREFERRED_NAME."'
					)
					and _k.language_id=".$this->getCurrentLanguageId()."
	
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
				$val['taxon']=$this->formatTaxon($val);
			}

			$val['label']=empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')';

			unset($val['parent_id']);
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
