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
			$return=json_encode($this->getTreeNode(array('node'=>$this->requestData['node'])));
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
					
				$val['child_count']=$d[0]['total'];
				$progeny[]=$val;
			}
		}
		
		usort($progeny,function($a,$b) { return (strtolower($a['label'])==strtolower($b['label']) ? 0 : (strtolower($a['label'])>strtolower($b['label']) ? 1 : -1)); });

		return
			array(
				'node'=>$taxon,
				'progeny'=>$progeny
			);

		
	}



}
