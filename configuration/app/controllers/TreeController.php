<?php

include_once ('SpeciesController.php');
include_once ('ModuleSettingsReaderController.php');

class TreeController extends Controller
{
    public $usedModels = array(
		'name_types'
    );

    public $jsToLoad = array(
        'all' => array(
            'main.js',
        ),
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

		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->_tree_taxon_count_style = $this->moduleSettings->getModuleSetting( [ 'setting'=>'tree_taxon_count_style','module'=>'species', 'subst'=>'species_established' ] );
		$this->_tree_initital_expand_levels = $this->moduleSettings->getModuleSetting( [ 'setting'=>'tree_initital_expand_levels','module'=>'species' ] );
		$this->setRobotsDirective( ["index","nofollow"] );
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
	    $published = $this->isProjectModulePublished('nsr');
	    
	    if ($published) {
	        
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
    
    		$this->smarty->assign('tree_taxon_count_style',$this->_tree_taxon_count_style);
    		$this->smarty->assign('tree_top',$this->getTreeTop());
    		$this->smarty->assign('initial_expansion',$this->_tree_initital_expand_levels);
 		
	    }
		
	    $this->smarty->assign('controllerMenuOverride', true);
	    $this->smarty->assign('isPublished', $published);
		
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

	public function jsonTreeAction()
	{
		$node=$this->rHasVal('node') ? $this->rGetVal('node') : $this->getTreeTop();
		$this->max_rank_id=$this->rHasVal('rank') ? $this->rGetVal('rank') : 36;
		$top=$this->getTaxonById( $node );
		$top['label']=$this->formatTaxon($top);
		$c=array('name'=>$top['taxon']=='Leven' ? 'Life' : $top['taxon'],'children'=>$this->aBranch( $node ));
		echo json_encode($c);
	}

	private function restoreTree()
	{
		return $this->moduleSession->getModuleSetting( 'tree' );
	}

	private function getTreeTop()
	{
        // LINNA-1400: do not set rank id of top in stone, but check ranks used in project
        // Skip first entry assuming this is a region
        $projectRanksIds = array_column($this->getProjectRanks(), 'rank_id');
        $rankId = isset($projectRanksIds[1]) && $projectRanksIds[1] > 10 ? $projectRanksIds[1] : 10;

        $p=$this->models->TreeModel->getTreeTop([
            "project_id"=>$this->getCurrentProjectId(),
            'top_rank_id' => $rankId
        ]);

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
			$this->addError('Found multiple high-order taxa without a parent. Unable to determine which is the top of the taxonomic tree.');
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

		$ranks = $this->getProjectRanks();

		foreach((array)$taxa as $key=>$val)
		{
			if ($count=='taxon')
			{
				if ( $this->_tree_taxon_count_style=='species_only' || $this->_tree_taxon_count_style=='species_established')
				{
					$val['child_count']['taxon']=$this->models->TreeModel->getBranchTaxonCount(array(
						"project_id"=>$this->getCurrentProjectId(),
						"node"=>$val['id']
					));
				}
				else
				{
					$val['child_count']['taxon']=null;
				}
			}
			else
			if ($count=='species' && $this->_tree_taxon_count_style!='none')
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

			$val['taxon']=$this->formatTaxon(array_merge($val, ['ranks' => $ranks, 'rankpos' => 'none']));
			$val['label']=(empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')');

			unset($val['parent_id']);
			unset($val['is_hybrid']);
			unset($val['rank_id']);
			unset($val['base_rank_id']);

			$val['parentage']=explode(' ',$val['parentage']);

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

		$x1=$this->_hybridMarkerHtml;
		$x2=$this->_hybridMarker_graftChimaera;
		$x3=$this->_hybridMarker;

		usort(
			$progeny,
			function($a,$b) use ($x1,$x2,$x3)
			{
				$aa=strtolower(str_replace([$x1,$x2,$x3,' '], '' , strip_tags($a['label'])));
				$bb=strtolower(str_replace([$x1,$x2,$x3,' '], '' , strip_tags($b['label'])));
				return ($aa==$bb ? 0 : ($aa>$bb ? 1 : -1));
			}
		);

		$taxon['is_top']=$isTop;

		return
			array(
				'node'=>$taxon,
				'progeny'=>$progeny
			);

	}

	private function aBranch( $node, $level=0 )
	{
		/*

		116298 | Animalia
		116299 | Plantae
		116300 | Fungi

		*/

		$children=
			$this->models->Taxa->freeQuery("
				select
					_a.id,
					concat(ifnull(ifnull(_k.name,_l.commonname),_a.taxon),' [',_q.label,']') as dutch_name,
					concat(_r.rank,' ',_a.taxon) as name,
					_p.rank_id,
					" . $level . " as level,
					case
					 when instr(_qp.parentage,'116298') || _a.id='116298' != 0 then 'Animalia'
					 when instr(_qp.parentage,'116299') || _a.id='116299' != 0 then 'Plantae'
					 when instr(_qp.parentage,'116300') || _a.id='116300' != 0 then 'Fungi'
					 else 'Unknown'
					end as kingdom
				from
					%PRE%taxa _a

				left join %PRE%taxon_quick_parentage _qp
					on _a.id = _qp.taxon_id
					and _a.project_id = _qp.project_id

				left join %PRE%trash_can _trash
					on _a.project_id = _trash.project_id
					and _a.id = _trash.lng_id
					and _trash.item_type='taxon'

				left join %PRE%projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id

				left join %PRE%labels_projects_ranks _q
					on _a.rank_id=_q.project_rank_id
					and _a.project_id = _q.project_id
					and _q.language_id=".$this->getCurrentLanguageId()."

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				left join %PRE%commonnames _l
					on _a.id=_l.taxon_id
					and _a.project_id=_l.project_id
					and _l.language_id=".$this->getCurrentLanguageId()."

				left join %PRE%names _k
					on _a.id=_k.taxon_id
					and _a.project_id=_k.project_id
					and _k.type_id=".$this->_idPreferredName."
					and _k.language_id=".$this->getCurrentLanguageId()."

				left join %PRE%names _m
					on _a.id=_m.taxon_id
					and _a.project_id=_m.project_id
					and _m.type_id=".$this->_idValidName."

				where
					_a.project_id = ".$this->getCurrentProjectId()."
					and ifnull(_trash.is_deleted,0)=0
					and _a.parent_id = ".$node."

				order by
					label
			");

		foreach((array)$children as $key=>$val)
		{
			if ( $val['rank_id'] <= $this->max_rank_id )
			{
				$children[$key]['children']=$this->aBranch( $val['id'], $level+1 );
				$children[$key]['size']=count($children[$key]['children']);
			}
		}


		return $children;

	}



}
