<?php

include_once ('SpeciesController.php');

class TreeController extends Controller
{
    public $usedModels = array(
		'name_types'
    );
	
    public $modelNameOverride = 'TreeModel';

	private $_idPreferredName=0;
	private $_idValidName=0;
		
    public function __construct()
    {
        parent::__construct();
		$this->initialise();
	}
	
	private function initialise()
	{
		$d=$this->models->NameTypes->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'nametype' => PREDICATE_PREFERRED_NAME,
				'language_id' => $this->getCurrentLanguageId()
			)
		));
		
		if ($d) $this->_idPreferredName=$d[0]['id'];
		
		$d=$this->models->NameTypes->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'nametype' => PREDICATE_VALID_NAME,
				'language_id' => LANGUAGE_ID_SCIENTIFIC
			)
		));
		
		if ($d) $this->_idValidName=$d[0]['id'];

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
		/*
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache'); 
		*/

		$tree=$this->restoreTree();

		if (is_null($tree))
		{
			$this->smarty->assign('nodes',json_encode($this->getTreeNode(array('node'=>false,'count'=>'species'))));
		}
		else
		{
			$this->smarty->assign('tree',json_encode($tree));
		}
	
		if ($this->rHasVar('expand'))
		{
			$this->smarty->assign('expand',(int)$this->rGetVal('expand'));
		}

		$this->printPage('tree');
	}
	
    public function ajaxInterfaceAction ()
    {
		$return='error';
        
		if ($this->rHasVal('action', 'get_tree_node'))
		{
			$return=json_encode($this->getTreeNode(array('node'=>$this->rGetVal('node'),'count'=>$this->rGetVal('count'))));
        }
		else
		if ($this->rHasVal('action', 'store_tree'))
		{	
			$this->moduleSession->getModuleSetting( array( 'setting'=>'tree','value'=>$this->rGetVal('tree') ) );
			$return='saved';
        }
		else
		if ($this->rHasVal('action', 'restore_tree'))
		{
	        $return=json_encode($this->restoreTree());
        }
        
        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
    }
	
	private function restoreTree()
	{
		return $this->moduleSession->getModuleSetting( 'tree' );
	}

	private function getTreeTop()
	{
		$p=$this->models->TreeModel->getTreeTop(array("project_id"=>$this->getCurrentProjectId()));

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

		if (isset($p['node']) && $p['node']!==false)
		{
			$node=$p['node'];
			$isTop=false;
		}
		else
		{
			$node=$this->getTreeTop();
			$isTop=true;
		}
		
		$count=isset($p['count']) && in_array($p['count'],array('none','taxon','species')) ? $p['count'] : 'none';

		if (is_null($node))
			return;

		$taxa=
			$this->models->TreeModel->getTreeBranch(array(
				'project_id'=>$this->getCurrentProjectId(),
				'language_id'=>$this->getCurrentLanguageId(),
				'type_id_preferred'=>$this->_idPreferredName,
				'type_id_valid'=>$this->_idValidName,
				'node'=>$node
			));

		$taxon=$progeny=array();

		foreach((array)$taxa as $key=>$val)
		{
			if ($count=='taxon') 
			{
				$val['child_count']['taxon']=$this->models->TreeModel->getBranchTaxonCount(array(
					"project_id"=>$this->getCurrentProjectId(),
					"node"=>$val['id']
				));
			}
			else
			if ($count=='species') 
			{
	
				$d=$this->models->TreeModel->getBranchSpeciesCount(array(
					"project_id"=>$this->getCurrentProjectId(),
					"base_rank_id"=>$val['base_rank_id'],
					"node"=>$val['id']
				));
	
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

			if ($val['base_rank_id']>=SPECIES_RANK_ID && $val['base_rank_id']!=NOTHOGENUS_RANK_ID )
			{
				if ($val['authorship']!='')
				{
					$val['taxon']=
						'<i>'.
						$this->addHybridMarker(array('name'=>str_replace($val['authorship'],'',$val['taxon']),'base_rank_id'=>$val['base_rank_id'])).
						'</i>'.' '.$val['authorship'];
				}
				else
				{
					$val['taxon']=$this->formatTaxon($val);
				}
			}
			else
			{
				$val['taxon']=$this->addHybridMarker(array('name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id']));
			}

			$val['label']=empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')';

			unset($val['parent_id']);
			unset($val['is_hybrid']);
			unset($val['rank_id']);
			unset($val['base_rank_id']);

			if ($val['id']==$node)
			{
				$taxon=$val;
			}
			else 
			{
				$val['has_children']=$this->models->TreeModel->hasChildren(array(
					"project_id"=>$this->getCurrentProjectId(),
					"node"=>$val['id']
				));

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
		
		$taxon['is_top']=$isTop;

		return
			array(
				'node'=>$taxon,
				'progeny'=>$progeny
			);
		
	}

}
