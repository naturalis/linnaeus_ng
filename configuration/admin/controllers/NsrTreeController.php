<?php

/*

	use
	http://<host>/linnaeus_ng/admin/views/nsr/?tree-reset
	to forcibly reset tree

*/

include_once ('NsrController.php');

class NsrTreeController extends NsrController
{
	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'taxon_quick_parentage',
		'name_types'
    );
    public $usedHelpers = array();
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

    public $modelNameOverride='NsrTreeModel';
    public $controllerPublicName = 'Soortenregister beheer';
    public $includeLocalMenu = false;
	private $_nameTypeIds;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function indexAction()
    {
		$tree=$this->restoreTree();

		if (is_null($tree) || $this->rHasVar('tree-reset'))
		{
			$this->smarty->assign('nodes',json_encode($this->getTreeNode(array('node'=>false,'count'=>'species'))));
		}
		else
		{
			$this->smarty->assign('tree',json_encode($tree));
		}

		$this->printPage();
    }

    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action'))
            return;

		if ($this->rHasVal('action', 'get_tree_node'))
		{
			$return=json_encode($this->getTreeNode($this->GetAll()));
        }
		else
		if ($this->rHasVal('action', 'store_tree'))
		{	
	        $_SESSION['admin']['user']['species']['tree']=$this->rGetVal('tree');
			$return='saved';
        }
		else
		if ($this->rHasVal('action', 'restore_tree'))
		{
	        $return=json_encode($this->restoreTree());
        }
		else
		if ($this->rHasVal('action', 'get_parentage') && $this->rHasId())
		{
	        $return=json_encode($this->getTaxonParentage($this->rGetVal('id')));
        }

        
        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
    }

    private function initialize()
    {
		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
    }

	private function restoreTree()
	{
		return isset($_SESSION['admin']['user']['species']['tree']) ?  $_SESSION['admin']['user']['species']['tree'] : null;
	}

	private function getTreeNode($p)
	{
		$node=isset($p['node']) && $p['node']!==false ? $p['node'] : $this->treeGetTop();
		$count=isset($p['count']) && in_array($p['count'],array('none','taxon','species')) ? $p['count'] : 'none';

		if (is_null($node))
			return;

		$nameTypeIdPreferred=!empty($this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']) ? $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'] : -1;
		$nameTypeIdValid=!empty($this->_nameTypeIds[PREDICATE_VALID_NAME]['id']) ? $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] : -1;

		$taxa=
			$this->models->NsrTreeModel->getTreeNodeTaxa(array(
				"language_id"=>$this->getDefaultProjectLanguage(),
				"type_id_preferred"=>$nameTypeIdPreferred,
				"type_id_valid"=>$nameTypeIdValid,
				"project_id"=>$this->getCurrentProjectId(),
				"node_id"=>$node
			));

		$taxon=$progeny=array();

		foreach((array)$taxa as $key=>$val)
		{
			if ($count=='taxon') 
			{
				$d=$this->models->NsrTreeModel->getTaxonCount(array(
					"project_id"=>$this->getCurrentProjectId(),
					"node_id"=>$val['id']
				));
					
				$val['child_count']['taxon']=$d[0]['total'];
			}
			else
			if ($count=='species') 
			{	
				$d=$this->models->NsrTreeModel->getSpeciesCount(array(
					"project_id"=>$this->getCurrentProjectId(),
					"base_rank"=>$val['base_rank'],
					"node_id"=>$val['id']
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
				$d=$this->models->NsrTreeModel->getTaxonChildCount(array(
					"project_id"=>$this->getCurrentProjectId(),
					"parent_id"=>$val['id']
				));
					
				$val['has_children']=$d[0]['total']>0;
				$progeny[]=$val;
			}
		}

		usort(
			$progeny,
			function($a,$b)
			{
				return
					(strtolower($a['label'])==strtolower($b['label']) ? 
						0 : (strtolower($a['label'])>strtolower($b['label']) ? 1 : -1)); 
			}
		);

		return
			array(
				'node'=>$taxon,
				'progeny'=>$progeny
			);
		
	}

}
