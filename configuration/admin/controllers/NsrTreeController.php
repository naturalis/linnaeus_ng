<?php

/*

	use
	http://<host>/linnaeus_ng/admin/views/nsr/?tree-reset
	to forcibly reset tree

*/

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

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
    public $controllerPublicName = 'Taxon editor';
    public $includeLocalMenu = false;
	private $_nameTypeIds;
	private $_noTreeCaching=false;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
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


		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->_tree_show_upper_taxon=$this->moduleSettings->getModuleSetting( 'tree_show_upper_taxon' );
		$this->smarty->assign( 'tree_show_upper_taxon',$this->_tree_show_upper_taxon, false );
    }

    public function __destruct()
    {
        parent::__destruct();
    }


    public function indexAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionRead() );

		$this->checkAuthorisation();

        $this->setPageName($this->translate('Index'));

		$this->_growTree();
    }

    public function treeAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionRead() );

		$this->checkAuthorisation();

		if ( $this->rHasVal('node') ) $this->smarty->assign('node',$this->rGetVal('node'));

		$this->_growTree( 'tree' );
    }


    private function _growTree( $tpl=null )
    {
		$tree=$this->restoreTree();

		if ( empty($tree) || $this->rHasVar('tree-reset'))
		{
			$this->smarty->assign('nodes',json_encode($this->getTreeNode(array('node'=>false,'count'=>'species'))));
		}
		else
		{
			$this->smarty->assign('tree',json_encode($tree));
		}

		$this->printPage( $tpl );
    }


    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action'))
            return;

		$this->UserRights->setActionType( $this->UserRights->getActionRead() );
		if ( $this->getAuthorisationState()==false )
			return;

		if ($this->rHasVal('action', 'get_tree_node'))
		{
			$return=json_encode($this->getTreeNode($this->rGetAll()));
        }
		else
		if ($this->rHasVal('action', 'store_tree'))
		{
	        $_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']=$this->rGetVal('tree');
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
	        $return=json_encode($this->getTaxonParentage($this->rGetId()));
        }


        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
    }


	private function restoreTree()
	{
		return
			!$this->_noTreeCaching && isset($_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']) ?
				$_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree'] : null;
	}

	private function getTreeNode( $p=null )
	{
		
		$topNode=isset($p['node']) && $p['node']!==false ? $p['node'] : $this->treeGetTop();

		if (is_null($topNode)) return;

		$nodeChildren=
			$this->models->NsrTreeModel->getTreeNodeTaxa( [
				'language_id' => $this->getDefaultProjectLanguage(),
				'type_id_preferred' => $this->getNameTypeId(PREDICATE_PREFERRED_NAME),
				'type_id_valid' => $this->getNameTypeId(PREDICATE_VALID_NAME),
				'project_id' => $this->getCurrentProjectId(),
				'node_id' => $topNode
			] );

        $ranks=$this->newGetProjectRanks();

		$taxon=$progeny=array();

		foreach((array)$nodeChildren as $key=>$val)
		{
			$childCount=$this->models->NsrTreeModel->getTaxonBranchTaxonCount( [
				'project_id' => $this->getCurrentProjectId(),
				'node_id' => $val['id'],
//				'min_rank' => SPECIES_RANK_ID,
//				'min_rank_style' => 'EQ'
			] );
			
			$val['child_count']=$childCount;

			/*
			$establishedSpeciesCount=$this->models->NsrTreeModel->getTaxonBranchEstablishedSpeciesCount( [
				'project_id' => $this->getCurrentProjectId(),
				'node_id' => $val['id']
			] );
			$val['child_count_established']=$establishedSpeciesCount;
			*/

			$val['taxon'] = $this->formatTaxon(array_merge($val, ['ranks' => $ranks, 'rankpos' => 'none']));
			$val['label']=empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')';

			//unset($val['parent_id']);
			unset($val['is_hybrid']);
			unset($val['rank_id']);
			unset($val['base_rank']);

			if ($val['id']==$topNode)
			{
				$taxon=$val;
			}
			else
			{
				$val['has_children']=$childCount>0;
				$progeny[]=$val;
			}

		}

		$x1=$this->_hybridMarkerHtml;
		$x2=$this->_hybridMarker_graftChimaera;

		usort(
			$progeny,
			function($a,$b) use ($x1,$x2)
			{
				$aa=strtolower(str_replace([$x1,$x2,' '], '' , strip_tags($a['label'])));
				$bb=strtolower(str_replace([$x1,$x2,' '], '' , strip_tags($b['label'])));
				return ($aa==$bb ? 0 : ($aa>$bb ? 1 : -1));
			}
		);

		return [ 'node'=>$taxon, 'progeny'=>$progeny ];

	}


}
