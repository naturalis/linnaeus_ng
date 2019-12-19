<?php
/**
 * Controller to setup and manage the taxonomic tree
 */

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

    /**
     * NsrTreeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    /**
     * Initalize the controller and the taxonomic tree
     */
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

    /**
     * Destructor
     */
    public function __destruct()
    {
        parent::__destruct();
    }


    /**
     * Index shows the tree
     */
    public function indexAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionRead() );

		$this->checkAuthorisation();

        $this->setPageName($this->translate('Index'));

		$this->_growTree();
    }

    /**
     * Shows the tree search
     */
    public function treeAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionRead() );

		$this->checkAuthorisation();

		if ( $this->rHasVal('node') ) {
            $this->smarty->assign('node', $this->rGetVal('node'));
        }

		$this->_growTree( 'tree' );
    }


    private function _growTree( $tpl=null )
    {
		$tree = $this->restoreTree();

		if ( empty($tree) || $this->rHasVar('tree-reset'))
		{
			$this->smarty->assign('nodes',json_encode($this->getTreeNode(array('node'=>false,'count'=>'species'))));
		} else {
			$this->smarty->assign('tree',json_encode($tree));
		}

		$this->printPage( $tpl );
    }


    /**
     * The ansynchronous action
     */
    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action')) {
            return;
        }

		$this->UserRights->setActionType( $this->UserRights->getActionRead() );
		if ( $this->getAuthorisationState()==false ) {
            return;
        }

		if ($this->rHasVal('action', 'get_tree_node'))
		{
			$return=json_encode($this->getTreeNode($this->rGetAll()));
        } else if ($this->rHasVal('action', 'store_tree')) {
	        $_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']=$this->rGetVal('tree');
			$return='saved';
        } else if ($this->rHasVal('action', 'restore_tree')) {
	        $return=json_encode($this->restoreTree());
        } else if ($this->rHasVal('action', 'get_parentage') && $this->rHasId()) {
	        $return=json_encode($this->getTaxonParentage($this->rGetId()));
        }

        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
    }


    /**
     * Returns the whole tree
     * @return mixed|null
     */
    private function restoreTree()
	{
	    if (!$this->_noTreeCaching && isset($_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree'])) {
	        return $_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree'];
        }

		return null;
	}

    /**
     * Returns a node of the tree
     *
     * @param null $p
     * @return array|void
     */
    private function getTreeNode($p=null )
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
			] );
			
			$val['child_count']=$childCount;

			$val['taxon']=$this->formatTaxon(array_merge($val, ['ranks' => $ranks, 'rankpos' => 'none']));
			$val['label']=(empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')');

			unset($val['is_hybrid']);
			unset($val['rank_id']);
			unset($val['base_rank_id']);

			if ($val['id']==$topNode)
			{
				$taxon=$val;
			}
			else
			{
				$val['has_children']=$this->models->NsrTreeModel->hasChildren(array(
					"project_id"=>$this->getCurrentProjectId(),
					"node"=>$val['id']
				));
				$progeny[]=$val;
			}

		}

		$hybrid=$this->_hybridMarkerHtml;
		$chimaera=$this->_hybridMarker_graftChimaera;
		$marker=$this->_hybridMarker;

		usort(
			$progeny,
			function($a,$b) use ($hybrid,$chimaera,$marker)
			{
				$aa=strtolower(str_replace([$hybrid,$chimaera,$marker,' '], '' , strip_tags($a['label'])));
				$bb=strtolower(str_replace([$hybrid,$chimaera,$marker,' '], '' , strip_tags($b['label'])));
				return ($aa==$bb ? 0 : ($aa>$bb ? 1 : -1));
			}
		);

		return [ 'node'=>$taxon, 'progeny'=>$progeny ];
	}

}
