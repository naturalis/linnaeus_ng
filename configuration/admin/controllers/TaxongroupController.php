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
		'taxongroups_taxa'
    );

    public $jsToLoad = array(
        'all' => array(
            'taxon_groups.js',
            'jquery.mjs.nestedSortable.js'
        )
    );

    public $cssToLoad = array(
		'taxon_groups.css'
	);
    public $usedHelpers = array(
        'session_messages'
    );
	
	public $modelNameOverride='TaxonGroupModel';

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
			$i=$this->saveGroupOrder($this->rGetId());
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
			$this->saveGroup($this->rGetAll());
			$this->helpers->SessionMessages->setMessage('Saved.');
			$this->redirect('taxongroups.php');
		}
		else
		if ($this->rHasVal('action','delete'))
		{
			$this->deleteGroup($this->rGetId());
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
			$this->saveTaxongroupTaxa($this->rGetId());
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

		$path = $this->moduleSession->getModuleSetting('path');

		if ($this->rHasId())
		{
			$group=$this->getGroup($this->rGetId());

			if ($this->rHasVal('back','1'))
			{
				$d=array();
				foreach($path as $row)
				{
					$d[]=$row;
					if ($row['id']==$this->rGetId())
						break;
				}
				$path = $d;
			}
			else
			{
				$path[$group['id']]=array('id'=>$group['id'],'sys_label'=>$group['sys_label']);
			}

			$this->smarty->assign('group',$group);
			$this->smarty->assign('path',$path);

			$this->moduleSession->setModuleSetting(array(
                'setting' => 'path',
                'value' => $path
            ));
		}
		else
		{
			$this->moduleSession->setModuleSetting(array('setting' => 'path'));
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

        if ($this->rGetVal('action') == 'save_taxon_to_group')
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

		$g = $this->models->TaxonGroupModel->getTaxonGroups(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'parentId' => $parent
		));

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
		$r = $this->models->TaxonGroupModel->getTaxonGroup(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'groupId' => $id
		));

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
			'parent_id'=>$parent_id,
			'sys_label'=>$sys_label
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
				'language_id'=>$language_id,
				'name'=>$name,
				'description'=>(isset($descriptions[$language_id]) ? $descriptions[$language_id] : null),
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
			'taxongroup_id'=>$id,
		));

		$this->models->TaxongroupsLabels->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>$id,
		));

		$this->models->Taxongroups->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$id,
		));

		$this->models->Taxongroups->update(
			array('parent_id'=>'null'),
			array('project_id'=>$this->getCurrentProjectId(),'parent_id'=>$id)
		);

		return true;
	}


	private function getAllCommonnames()
	{
		return $this->models->TaxonGroupModel->getAllCommonnames($this->getCurrentProjectId());
	}

	private function getTaxa($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;

		if (is_null($p))
		{
			$this->tmp=$this->getAllCommonnames();
		}

		$g = $this->models->TaxonGroupModel->getTaxa(array(
            'projectId' => $this->getCurrentProjectId(),
		    'parentId' => $parent
		));

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
			'taxongroup_id'=>$taxongroup_id,
			'taxon_id'=>$taxon_id
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
			'taxongroup_id'=>$group_id,
		));

		foreach((array)$taxa as $key=>$taxon)
		{
			 $this->saveTaxongroupTaxon(
			 	array(
					'taxongroup_id'=>$group_id,
					'taxon_id'=>$taxon,
					'show_order'=>($key+1)
				)
			);
		}

	}

	private function getTaxongroupTaxa($id)
	{
		if (empty($id))
			return;

		$d = $this->models->TaxonGroupModel->getTaxongroupTaxa(array(
            'projectId' => $this->getCurrentProjectId(),
    		'groupId' => $id
		));

		$c=$this->getAllCommonnames();

		foreach((array)$d as $key=>$val)
		{
			$d[$key]['commonname']=isset($c[$val['id']]['commonname']) ? $c[$val['id']]['commonname'] : null;
		}

		return $d;

	}

	private function getTaxongroupMemberships($id)
	{
		return $this->models->TaxonGroupModel->getTaxongroupMemberships(array(
            'projectId' => $this->getCurrentProjectId(),
    		'groupId' => $id
		));
	}


	private function cleanUp()
	{
		$d = $this->models->TaxonGroupModel->getOrphanedTaxonGroupTaxa($this->getCurrentProjectId());

		foreach((array)$d as $val)
		{
			$this->models->TaxongroupsTaxa->delete(
				array(
					"project_id"=>$this->getCurrentProjectId(),
					"id"=>$val['id']
				)
			);
		}

		$g = $this->models->TaxonGroupModel->getOrphanedTaxonGroups($this->getCurrentProjectId());

		foreach((array)$g as $val)
		{
			$this->models->Taxongroups->update(
				array('parent_id'=>'null'),
				array('project_id'=>$this->getCurrentProjectId(),'id'=>$val['id'])
			);

		}
	}


}