<?php

include_once ('Controller.php');
include_once ('TaxonParentageController.php');

class TreeController extends Controller
{
	private $_lookupListMaxResults=99999;

    public $usedModels = array(
    );
    public $usedHelpers = array(
    );
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_tree.css'
    );
    public $jsToLoad = array(
        'all' => array(
			'jquery.mjs.nestedSortable.js',
			'taxon_tree.js'
        )
    );
    public $controllerPublicName = 'Species module';
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
		$this->TaxonParentageController = new TaxonParentageController;
		$this->checkParentage();
    }

	private function padId($id)
	{
		return $this->TaxonParentageController->padId($id);
	}

    public function indexAction()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Taxonomic tree'));

		$tree=$this->restoreTree();

		if (is_null($tree))
		{
			$this->smarty->assign('nodes',json_encode($this->getTreeNode(array('node'=>false,'count'=>'taxon'))));
		}
		else
		{
			$this->smarty->assign('tree',json_encode($tree));
		}

		$this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

        if ($this->rHasVar('action', 'get_lookup_list'))
		{
            $return=$this->getLookupList($this->GetAll());
        } else
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


        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
    }

	private function restoreTree()
	{
		return isset($_SESSION['admin']['user']['species']['tree']) ?  $_SESSION['admin']['user']['species']['tree'] : null;
	}

	private function unsetTree()
	{
		unset($_SESSION['admin']['user']['species']['tree']);
	}

	private function treeGetTop()
	{
		/*
			get the top taxon = no parent
			"_r.id < 10" added as there might be orphans, which are ususally low-level ranks
		*/
	    $p = $this->models->TreeModel->getTreeTop($this->getCurrentProjectId());

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
		$count=isset($p['count']) && in_array($p['count'],array('none','taxon','species')) ? $p['count'] : 'taxon';

		if (is_null($node))
			return;

		$taxa = $this->models->TreeModel->getTreeNode(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'node' => $node
		));

		$taxon=$progeny=array();

		foreach((array)$taxa as $key=>$val)
		{
			if ($count=='taxon')
			{
				$val['child_count'] = $this->models->TreeModel->countChildrenTaxon(array(
    				'projectId' => $this->getCurrentProjectId(),
    				'parentId' => $this->padId($val['id'])
				));
			}
			else if ($count=='species')
			{
				$val['child_count'] = $this->models->TreeModel->countChildrenSpecies(array(
    				'projectId' => $this->getCurrentProjectId(),
    				'parentId' => $this->padId($val['id']),
				    'rankId' => $val['base_rank']
				));
			}
			else
			{
				$val['child_count']=null;
			}

			if ($val['base_rank']>=SPECIES_RANK_ID)
			{
				$val['taxon']=$this->formatTaxon($val);
			}

			$val['label']=empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')';

			//unset($val['parent_id']);
			unset($val['is_hybrid']);
			unset($val['rank_id']);
			//unset($val['base_rank']);

			if ($val['id']==$node)
			{
				$taxon=$val;
			}
			else
			{
				$val['has_children'] = $this->models->TreeModel->hasChildren(array(
    				'projectId' => $this->getCurrentProjectId(),
    				'parentId' => $val['id']
				));
				$progeny[$val['id']]=$val;
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

	private function checkParentage()
	{

		if ($this->TaxonParentageController->getParentageTableRowCount()==0)
		{
			$this->TaxonParentageController->generateParentageAll();
			$this->unsetTree();
		}

	}


}
