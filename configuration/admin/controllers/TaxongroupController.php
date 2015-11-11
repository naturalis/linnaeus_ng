<?php


/*

CREATE TABLE IF NOT EXISTS `taxongroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sys_label` varchar(64) NOT NULL,
  `show_order` int(2) NOT NULL DEFAULT '99',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`sys_label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `taxongroups_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxongroup_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxongroup_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `taxongroups_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxongroup_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `show_order` int(3) NOT NULL DEFAULT '999',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxongroup_id`,`taxon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


*/

include_once ('Controller.php');
class TaxongroupController extends Controller
{

    public $usedModels = array(
		'taxongroups',
		'taxongroups_labels',
		'taxongroups_taxa',
		'commonname'
    );
    public $jsToLoad = array(
        'all' => array('taxon_groups.js','jquery.mjs.nestedSortable.js')
    );	

    public $cssToLoad = array(
		'taxon_groups.css'
	);
    public $usedHelpers = array(
        'session_messages'
    );
   
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
		$this->cleanUp();
	}
	


    public function taxongroupsAction ()
    {
		$this->checkAuthorisation();
		$this->addMessage($this->helpers->SessionMessages->getMessages());
		
		if ($this->rHasVal('action','saveorder'))
		{
			$i=$this->saveGroupOrder(rGetAll());
			if ($i>0) $this->addMessage('New order saved.');
		}
		
		$this->smarty->assign('groups',$this->getGroups());
		$this->printPage();
    }
	
    public function taxongroupAction ()
    {
		$this->checkAuthorisation();
		
		if ($this->rHasVal('action','save'))
		{
			$this->saveGroup(rGetAll());
			$this->helpers->SessionMessages->setMessage('Saved.');
			$this->redirect('taxongroups.php');
		}
		else 
		if ($this->rHasVal('action','delete'))
		{
			$this->deleteGroup(rGetAll());
			$this->helpers->SessionMessages->setMessage('Group deleted.');
			$this->redirect('taxongroups.php');
		}
		else 
		if (!$this->rHasId())
		{
			$this->smarty->assign('newgroup',true);
		} else
		if ($this->rHasId())
		{
			$this->smarty->assign('group',$this->getGroup($this->rGetId()));
			$this->smarty->assign('newgroup',false);
		}
		else
		{
			$this->smarty->assign('newgroup',true);
		}

		$this->smarty->assign('groups',$this->getGroups());
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->printPage();
    }
	
    public function taxongroupTaxaAction ()
    {
		$this->checkAuthorisation();
		if (!$this->rHasId()) $this->redirect('taxongroups.php');
		
		if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->saveTaxongroupTaxa(rGetAll());
			$this->addMessage('Saved');
		}

		$this->smarty->assign('group',$this->getGroup($this->rGetId()));
		$this->smarty->assign('taxongroupTaxa',$this->getTaxongroupTaxa($this->rGetId()));
		$this->smarty->assign('taxa',$this->getTaxa());
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->printPage();
    }

    public function taxongroupClickthroughAction ()
    {
		$this->checkAuthorisation();
		
		if ($this->rHasId())
		{
			$group=$this->getGroup($this->rGetId());

			if ($this->rHasVal('back','1'))
			{
				$d=array();
				foreach($_SESSION['admin']['user']['taxongroup']['path'] as $row)
				{
					$d[]=$row;
					if ($row['id']==$this->rGetId())
						break;
				}
				$_SESSION['admin']['user']['taxongroup']['path']=$d;
			}
			else
			{
				$_SESSION['admin']['user']['taxongroup']['path'][$group['id']]=array('id'=>$group['id'],'sys_label'=>$group['sys_label']);
			}

			$this->smarty->assign('group',$group);
			$this->smarty->assign('path',$_SESSION['admin']['user']['taxongroup']['path']);
		}
		else
		{
			unset($_SESSION['admin']['user']['taxongroup']['path']);
			$groups=$this->getGroups(array('stop_level'=>0));
			$this->smarty->assign('groups',$groups);
		}

		$this->printPage();
    }
	
    public function taxongroupOrphanedTaxaAction ()
    {
		$this->checkAuthorisation();
		$this->smarty->assign('taxa',$this->getTaxa());
		$this->smarty->assign('groups',$this->getGroups());
		$this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

        if ($this->requestData['action'] == 'save_taxon_to_group')
		{
			 $r=$this->saveTaxongroupTaxon(
			 	array(
					'taxongroup_id'=>$this->rGetVal('group'),
					'taxon_id'=>$this->rGetVal('taxon')
				)
			);
			$this->smarty->assign('returnText',$r);
		}

        $this->printPage('ajax_interface');
	}



	private function getGroups($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;
		$level=isset($p['level']) ? $p['level'] : 0;
		$stopLevel=isset($p['stop_level']) ? $p['stop_level'] : null;
		
		$g=$this->models->Taxongroups->freeQuery("
			select
				_a.*,
				_b.name,
				_b.description
			from
				%PRE%taxongroups _a
				
			left join 
				%PRE%taxongroups_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.taxongroup_id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.parent_id ".(is_null($parent) ? "is null" : "=".$parent)."
				order by _a.show_order
		");
		
		foreach((array)$g as $key=>$val)
		{
			$g[$key]['level']=$level;	
			$g[$key]['taxa']=$this->getTaxongroupTaxa($val['id']);	
			if (!is_null($stopLevel) && $stopLevel<=$level)
			{
				continue;
			}
			$g[$key]['children']=$this->getGroups(array('parent'=>$val['id'],'level'=>$level+1,'stop_level'=>$stopLevel));
		}
		
		return $g;
	}

	private function getGroup($id)
	{
		$d=$this->models->Taxongroups->freeQuery("
			select
				_a.id,
				_a.parent_id,
				_a.sys_label,
				_b.name,
				_b.description
			from
				%PRE%taxongroups _a
				
			left join 
				%PRE%taxongroups_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.taxongroup_id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$id."
		");

		$r=$d[0];

		$d=$this->models->TaxongroupsLabels->_get(array("id"=>array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>$id
		)));

		foreach((array)$d as $val)
		{
			$r['names'][$val['language_id']]=$val['name'];
			$r['descriptions'][$val['language_id']]=$val['description'];
		}
		
		$r['taxa']=$this->getTaxongroupTaxa($r['id']);
		$r['groups']=$this->getGroups(array('parent'=>$r['id'],'level'=>0,'stop_level'=>0));

		return $r;
	}
	
	private function saveGroup($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$parent_id=!empty($p['parent_id']) ? $p['parent_id'] : 'null';
		$sys_label=isset($p['sys_label']) ? $p['sys_label'] : null;
		$names=isset($p['names']) ? $p['names'] : null;
		$descriptions=isset($p['descriptions']) ? $p['descriptions'] : null;

		if (empty($sys_label))
			return false;

		$this->models->Taxongroups->save(array(
			'id'=>$id,
			'project_id'=>$this->getCurrentProjectId(),
			'parent_id'=>mysql_real_escape_string($parent_id),
			'sys_label'=>mysql_real_escape_string($sys_label)
		));
		
		if (empty($id)) $id=$this->models->Taxongroups->getNewId();
		
		if (!$id) return;

		$this->models->TaxongroupsLabels->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>$id,
		));
					
		foreach((array)$names as $language_id=>$name)
		{
			$this->models->TaxongroupsLabels->save(array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxongroup_id'=>$id,
				'language_id'=>mysql_real_escape_string($language_id),
				'name'=>mysql_real_escape_string($name),
				'description'=>(isset($descriptions[$language_id]) ? mysql_real_escape_string($descriptions[$language_id]) : null),
			));
		}
		
		return true;
	}

	private function saveGroupOrder($p)
	{
		$groups=isset($p['groups']) ? $p['groups'] : null;

		if (empty($groups))
			return false;
			
		$i=0;
		foreach((array)$groups as $key=>$group)
		{
			$this->models->Taxongroups->save(array(
				'id'=>$group,
				'project_id'=>$this->getCurrentProjectId(),
				'show_order'=>$key
			));
			
			$i+=$this->models->Taxongroups->getAffectedRows();
		}
		return $i;
	}

	private function deleteGroup($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;

		$this->models->TaxongroupsTaxa->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($id),
		));

		$this->models->TaxongroupsLabels->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($id),
		));
					
		$this->models->Taxongroups->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>mysql_real_escape_string($id),
		));

		$this->models->Taxongroups->update(
			array('parent_id'=>'null'),
			array('project_id'=>$this->getCurrentProjectId(),'parent_id'=>mysql_real_escape_string($id))
		);

		return true;
	}


	private function getAllCommonnames()
	{
		return $this->models->Commonname->freeQuery(array(
				"query" => "
					select
						taxon_id,commonname
					from %PRE%commonnames
					where
						project_id = ".$this->getCurrentProjectId()."
						and language_id=".LANGUAGE_ID_DUTCH."
						order by show_order desc",
				"fieldAsIndex"=>"taxon_id"
			));
	}

	private function getTaxa($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;

		if (is_null($p))
		{
			$this->tmp=$this->getAllCommonnames();
		}

		$g=$this->models->Taxon->freeQuery("
			select
				_t.id,
				_t.taxon,
				_t.parent_id,
				_t.is_hybrid,
				_q.rank
			from
				%PRE%taxa _t

			left join %PRE%projects_ranks _f
				on _t.project_id = _f.project_id
				and _t.rank_id=_f.id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id
				
			where
				_t.project_id=". $this->getCurrentProjectId()."
				and _t.parent_id ".(is_null($parent) ? "is null" : "=".$parent)."

			order by
					_t.taxon
		");

		foreach((array)$g as $key=>$val)
		{
			$g[$key]['commonname']=isset($this->tmp[$val['id']]['commonname']) ? $this->tmp[$val['id']]['commonname'] : null;
			$g[$key]['group_memberships']=$this->getTaxongroupMemberships($val['id']);
			$g[$key]['children']=$this->getTaxa(array('parent'=>$val['id']));
		}
				
		return $g;
	}

	private function saveTaxongroupTaxon($p)
	{
		$taxongroup_id=isset($p['taxongroup_id']) ? $p['taxongroup_id'] : null;
		$taxon_id=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$show_order=isset($p['show_order']) ? $p['show_order'] : null;
		
		if (empty($taxongroup_id) || empty($taxon_id))
			return false;
		
		$d=array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($taxongroup_id),
			'taxon_id'=>mysql_real_escape_string($taxon_id)
		);
		
		if (!is_null($show_order)) $d['show_order']=$show_order;

		$this->models->TaxongroupsTaxa->save($d);
		
		return true;
	}

	private function saveTaxongroupTaxa($p)
	{
		$group_id=isset($p['group_id']) ? $p['group_id'] : null;
		$taxa=isset($p['taxa']) ? $p['taxa'] : null;

		if (empty($group_id))
			return false;

		$this->models->TaxongroupsTaxa->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($group_id),
		));

		foreach((array)$taxa as $key=>$taxon)
		{
			 $this->saveTaxongroupTaxon(
			 	array(
					'taxongroup_id'=>mysql_real_escape_string($group_id),
					'taxon_id'=>mysql_real_escape_string($taxon),
					'show_order'=>($key+1)
				)
			);
		}

	}

	private function getTaxongroupTaxa($id)
	{
		if (empty($id))
			return;

		$d=$this->models->TaxongroupsTaxa->freeQuery("
			select
				_t.id,
				_t.taxon,
				_t.parent_id,
				_t.is_hybrid,
				_q.rank,			
				_a.show_order
			from
				%table% _a

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id = _t.project_id

			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id = _f.project_id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id
				
			where
				_a.project_id=".$this->getCurrentProjectId()."
				and _a.taxongroup_id = ".mysql_real_escape_string($id)."
			order by 
				_a.show_order
		");
		
		$c=$this->getAllCommonnames();
		
		foreach((array)$d as $key=>$val)
		{
			$d[$key]['commonname']=isset($c[$val['id']]['commonname']) ? $c[$val['id']]['commonname'] : null;
		}

		return $d;

	}
	
	private function getTaxongroupMemberships($id)
	{
		if (empty($id))
			return;

		$d=$this->models->TaxongroupsTaxa->freeQuery(array(
			"query"=>"
				select
					_a.id,_a.taxongroup_id,_g.sys_label
				from
					%table% _a
				left join %PRE%taxongroups _g
					on _a.project_id=_g.project_id
					and _a.taxongroup_id=_g.id
				where
					_a.project_id=".$this->getCurrentProjectId()."
					and _a.taxon_id = ".$id,
			"fieldAsIndex"=> "taxongroup_id"
		));

		return $d;
	}


	private function cleanUp()
	{
		$d=$this->models->TaxongroupsTaxa->freeQuery("
			select
				_a.id
			from
				%table% _a

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id = _t.project_id

			where
				_a.project_id=".$this->getCurrentProjectId()."
				and _t.id is null
		");

		foreach((array)$d as $val)
		{
			$this->models->TaxongroupsTaxa->delete(
				array(
					"project_id"=>$this->getCurrentProjectId(),
					"id"=>$val['id']
				)
			);
		}

		$g=$this->models->Taxongroups->freeQuery("
			select
				_a.*
			from
				%PRE%taxongroups _a
				
			left join 
				%PRE%taxongroups _b
				on _a.project_id=_b.project_id
				and _a.parent_id=_b.id

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.parent_id is not null
				and _b.id is null
		");

		foreach((array)$g as $val)
		{
			$this->models->Taxongroups->update(
				array('parent_id'=>'null'),
				array('project_id'=>$this->getCurrentProjectId(),'id'=>$val['id'])
			);

		}

		

			
	}


}