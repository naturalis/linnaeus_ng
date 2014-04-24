<?php

include_once ('Controller.php');
include_once ('RdfController.php');
class SpeciesTaxonomyController extends Controller
{
    private $_useNBCExtras = false;
	private $_lookupListMaxResults=50;
    public $usedModels = array(
		'names',
		'name_types',
		'actors',
		'language',
		'literature2'
    );
    public $usedHelpers = array(
    );
    public $cacheFiles = array(
    );
    public $cssToLoad = array(
        'taxon.css',
		'taxonomy.css' 
    );
    public $jsToLoad = array(
        'all' => array(
            'taxon.js', 
            'lookup.js',
			'taxon_extra.js'
        )
    );
    public $controllerPublicName = 'Species module';
    public $includeLocalMenu = true;
	
	private $_taxonId=null;
	private $_taxonConcept=null;
	private $_nameTypeIds=array();


    public function __construct()
    {
        parent::__construct();
		$this->initialize();
    }


    public function __destruct()
    {
        parent::__destruct();
    }
	
    private function initialize()
    {
		$this->Rdf = new RdfController;

		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));

		$this->setTaxonId($this->rGetVal('id'));
		$this->setTaxonConcept();
	}

    public function setTaxonId($id)
    {
        $this->_taxonId=$id;
    }

    public function getTaxonId()
    {
        return $this->_taxonId;
    }

    public function setTaxonConcept()
    {
        $this->_taxonConcept=$this->getTaxonById($this->getTaxonId());
    }

    public function getTaxonConcept($id=null)
    {
        return $this->_taxonConcept;
    }

	private function getActor()
	{
		return null;
	}

    private function getRank($id)
    {
		$rank=$this->models->ProjectRank->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id'=> $id
				)
			));

		$d=$this->models->LabelProjectRank->_get(
			array(
				'id' => array(
					'project_id' => $pId, 
					'project_rank_id' => $rank['id'], 
					'language_id' => $this->getDefaultProjectLanguage()
				), 
				'columns' => 'label'
			));
			
		$rank['label']=$d[0]['label'];
					
        return $rank;
    }



	private function getNames()
	{
		$p=$this->getTaxonConcept();
		$id=$p['id'];
		$base_rank=isset($p['base_rank']) ? $p['base_rank'] : null;

        $names=$this->models->Names->freeQuery(
			array(
				'query' => "
					select
						_a.id,
						_a.name,
						_a.uninomial,
						_a.specific_epithet,
						_a.infra_specific_epithet,
						_a.authorship,
						_a.name_author,
						_a.authorship_year,
						_a.reference,
						_a.reference_id,
						_a.expert,
						_a.expert_id,
						_a.organisation,
						_a.organisation_id,
						_b.nametype,
						_a.language_id,
						_c.language,
						_d.label as language_label,

				_e.name as expert_name,
				_f.name as organisation_name,
				_g.label as reference_label,
				_g.author as reference_author,
				_g.date as reference_date,
				
						case
							when _b.nametype = '".PREDICATE_PREFERRED_NAME."' then 10
							when _b.nametype = '".PREDICATE_ALTERNATIVE_NAME."' then 9
							when _b.nametype = '".PREDICATE_VALID_NAME."' then 8
							when _b.nametype = '".PREDICATE_SYNONYM."' then 7
							when _b.nametype = '".PREDICATE_SYNONYM_SL."' then 6

							when _b.nametype = '".PREDICATE_HOMONYM."' then 5
							when _b.nametype = '".PREDICATE_MISSPELLED_NAME."' then 4
							when _b.nametype = '".PREDICATE_INVALID_NAME."' then 3
							else 0
						end as sort_criterium

					from %PRE%names _a 

					left join %PRE%name_types _b
						on _a.type_id=_b.id 
						and _a.project_id=_b.project_id

					left join %PRE%languages _c
						on _a.language_id=_c.id

					left join %PRE%labels_languages _d
						on _a.language_id=_d.language_id
						and _d.label_language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%actors _e
				on _a.expert_id = _e.id 
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.organisation_id = _f.id 
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id 
				and _a.project_id=_g.project_id

					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and _a.taxon_id=".$id."
					order by 
						sort_criterium desc
						",
				'fieldAsIndex' => 'id'
			)
		);

		$prefferedname=$scientific_name=$nomen=$prefferedname=$valid_name=null;

		foreach((array)$names as $key=>$val)
		{
			if ($val['nametype']==PREDICATE_PREFERRED_NAME && $val['language_id']==$this->getDefaultProjectLanguage())
			{
				$prefferedname=$val['name'];
			}

			if (!empty($val['expert_id']))
				$names[$key]['expert']=$this->getActor($val['expert_id']);

			if (!empty($val['organisation_id']))
				$names[$key]['organisation']=$this->getActor($val['organisation_id']);

			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);


//			if ($val['language_id']==LANGUAGE_ID_SCIENTIFIC && $val['nametype']==PREDICATE_VALID_NAME)
			if ($val['nametype']==PREDICATE_VALID_NAME)
			{
				$nomen=trim($val['uninomial']).' '.trim($val['specific_epithet']).' '.trim($val['infra_specific_epithet']);
				
				if (strlen(trim($nomen))==0)
					$nomen=trim(str_replace($val['authorship'],'',$val['name']));
				
				if ($base_rank>=GENUS_RANK_ID)
				{
					$nomen='<i>'.trim($nomen).'</i>';
					$names[$key]['name']=trim($scientific_name=$nomen.' '.$val['authorship']);
				}
				else
				{
					$scientific_name=trim($val['name']);
				}
				$valid_name=$val;
			}
		}

		return
			array(
				'scientific_name'=>$scientific_name,
				'nomen'=>$nomen,
				'nomen_no_tags'=>trim(strip_tags($nomen)),
				'preffered_name'=>$prefferedname,
				'valid_name'=>$valid_name,
				'list'=>$names
			);
	}

	private function getName($p)
	{	
	
		$type=isset($p['type'])	? $p['type'] : null;
		$id=isset($p['id'])	? $p['id'] : null;
	
		$taxon_id=$this->getTaxonId();
		
		$d=array(
			'project_id' => $this->getCurrentProjectId(),
			'taxon_id' => $taxon_id,
		);
		
		if (isset($type))
			$d['type_id'] = $this->_nameTypeIds[$type]['id'];

		if (isset($id))
			$d['id'] = $id;

        $name=$this->models->Names->_get(array('id' => $d));

		return $name[0];
	}

	private function getNameTypes()
	{
        $types=$this->models->NameTypes->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId()
			)
		));
		
		return $types;
	}

	private function getActors()
	{
        $actors=$this->models->Actors->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'order'=>'name'
		));
		
		return $actors;
	}

	private function getLanguages()
	{
		$d=$this->getDefaultProjectLanguage();

        $languages=$this->models->Language->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,language,if(id='.$d.',-1,ifnull(show_order,9999)) as show_order',
			'order'=>'show_order,language'
		));

		return $languages;
	}

	private function getReferences()
	{
        $actors=$this->models->Literature2->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'order'=>'label'
		));
		
		return $actors;
	}

	private function constructTaxonConceptName($p)
	{
		return trim(
			str_replace('  ',' ',
				@$p['uninomial'].' '.
				@$p['specific_epithet'].' '.
				@$p['infra_specific_epithet'].' '.
				@$p['authorship']
			)
		);
	}

	private function createName($p)
	{
		$id=$this->getTaxonId();
		
		if (is_null($id))
			return;
				
		$res=$this->models->Names->save(
			array(
				'id'=>isset($p['id']) ? $p['id'] : null,
				'name'=>!empty($p['name']) ? $p['name'] : 'null',
				'uninomial'=>!empty($p['uninomial']) ? $p['uninomial'] : 'null',
				'specific_epithet'=>!empty($p['specific_epithet']) ? $p['specific_epithet'] : 'null',
				'infra_specific_epithet'=>!empty($p['infra_specific_epithet']) ? $p['infra_specific_epithet'] : 'null',
				'authorship'=>!empty($p['authorship']) ? $p['authorship'] : 'null',
				'name_author'=>!empty($p['name_author']) ? $p['name_author'] : 'null',
				'authorship_year'=>!empty($p['authorship_year']) ? $p['authorship_year'] : 'null',
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$id,
				'type_id'=>$p['type_id'],
				'language_id'=>!empty($p['language_id']) ? $p['language_id'] : 'null',
				'expert_id'=>!empty($p['expert_id']) ? $p['expert_id'] : 'null',
				'organisation_id'=>!empty($p['organisation_id']) ? $p['organisation_id'] : 'null',
				'reference_id'=>!empty($p['reference_id']) ? $p['reference_id'] : 'null',
			)
		);

		return $res;
	}

	private function deleteName($p)
	{
		$id=$this->getTaxonId();
		
		if (is_null($id) || is_null($p['id']))
			return;
				
		$res=$this->models->Names->delete(
			array(
				'id'=>$p['id'],
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$id
			)
		);

		return $res;
	}

	private function updateValidName($p)
	{
		$id=$this->getTaxonId();
		
		if (is_null($id))
			return;

		if (
			empty($p['uninomial']) && 
			empty($p['specific_epithet']) && 
			empty($p['infra_specific_epithet']) && 
			empty($p['authorship']) && 
			empty($p['name_author']) && 
			empty($p['authorship_year'])
		)
		return;

		$res=$this->models->Names->update(
			array(
				'name'=>$this->constructTaxonConceptName($p),
				'uninomial'=>$p['uninomial'],
				'specific_epithet'=>$p['specific_epithet'],
				'infra_specific_epithet'=>$p['infra_specific_epithet'],
				'authorship'=>$p['authorship'],
				'name_author'=>$p['name_author'],
				'authorship_year'=>$p['authorship_year'],
			),
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$id,
				'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
				'id'=>$p['id']
			)
		);

		return $res;
	}

	private function createTaxon($p)
	{
		$res=$this->models->Taxon->insert(
			array(
				'taxon'=>$this->constructTaxonConceptName($p),
				'parent_id' => $p['parent_id'],
				'project_id' => $this->getCurrentProjectId()
			)
		);
		
		if ($res)
			return $this->models->Taxon->getNewId();
	}
	
	private function updateTaxon($p)
	{
		$id=$this->getTaxonId();
		
		if (is_null($id))
			return;
			
		$taxon=$this->constructTaxonConceptName($p);
		if ($taxon)
		{
			$p['taxon']=$taxon;
		}

		$res=$this->models->Taxon->update(
			$p,
			array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $id
			)
		);

		return $res;
	}

    public function taxonomyAction ()
    {
        $this->checkAuthorisation();

        if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{

			$p=$this->rGetVal('valid_name');
			$p['rank_id']=$this->rGetVal('rank_id');
			if ($this->rHasVar('new_parent_id'))
			{
				$p['parent_id']=$this->rGetVal('new_parent_id');
			}
			
			// update
			if ($this->rHasVal('id')) 
			{
				$this->updateTaxon($p);

				$currentValidName=$this->getName(array('type'=>PREDICATE_VALID_NAME));

				$p['id']=$currentValidName['id'];
	
				$res=$this->updateValidName($p);
				
				if ($res)
				{	
					$this->addMessage('Valid name updated.');
					$res=$this->updateTaxon($p);
						
					if ($res)
					{
						$this->addMessage('Taxon concept updated.');
					}
					else
					{
						$this->addError('Could not update taxon concept.');
						$res=$this->updateValidName($current);
					}
				}
				else 
				{
					$this->addError('Could not update valid name.');
				}
				
			}
			// new
			else 
			{
				if ($this->rHasVar('new_parent_id'))
				{
					$res=$this->createTaxon($p);
	
					if ($res)
					{	
						$this->addMessage('Created taxon concept.');
						
						$this->setTaxonId($res);

						$p['type_id']=$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'];
						$p['language_id']=LANGUAGE_ID_SCIENTIFIC;
						$p['name']=$this->constructTaxonConceptName($p);
				
						$res=$this->createName($p);
							
						if ($res)
						{
							$this->addMessage('Created valid name.');
						}
						else
						{
							$this->addError('Could not create valid name.');
							
						}
					}
					else 
					{
						$this->addError('Could not create taxon concept.');
					}

				}
				else
				{
					$this->addError('Cannot save taxon without parent.');
				}
	
			}
	
			$this->setTaxonConcept();	

		}

		$this->addError('Please note: this function is still under development. As yet, no checks are performed on name or parent - data is saved <i>as is</i>, including errors.');
		
		if (!is_null($this->getTaxonId()))
		{
			$concept=$this->getTaxonConcept();
			$names=$this->getNames();

			$this->setPageName(sprintf($this->translate('Editing "%s"'), $this->formatTaxon($concept)));
			$this->smarty->assign('parent',$this->getTaxonById($concept['parent_id']));
			$this->smarty->assign('ranks',$this->newGetProjectRanks());
			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('names',$names);
		}
		else 
		{
			$this->setPageName(sprintf($this->translate('New taxon concept')));
			$this->smarty->assign('ranks',$this->newGetProjectRanks());
		}

		$this->printPage();
       
    }

    public function namesAction ()
    {
        $this->checkAuthorisation();

		$concept=$this->getTaxonConcept();
		$names=$this->getNames();

		$this->setPageName(sprintf($this->translate('Names for "%s"'), $this->formatTaxon($concept)));
		$this->smarty->assign('concept',$this->getTaxonConcept());
		$this->smarty->assign('names',$names);
		$this->smarty->assign('types',$this->getNameTypes());
		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('languages',$this->getLanguages());


		$this->printPage();
       
    }

    public function namesEditAction ()
    {
        $this->checkAuthorisation();
		
        if ($this->rHasVal('action','save')  && !$this->isFormResubmit())
		{
			$p=$this->requestData;

			if ($this->rHasVar('name_id'))
			{
				$p['id']=$this->rGetVal('name_id');
				unset($p['name_id']);
			}

			$res=$this->createName($p);

			if ($res)
			{	
				$this->redirect('names.php?id='.$this->getTaxonId());
			}
			else 
			{
				$this->addError('Could not create name.');
			}

		}
		else
        if ($this->rHasVal('action','delete')  && $this->rHasVar('name_id') && !$this->isFormResubmit())
		{
			$p['id']=$this->rGetVal('name_id');
			$res=$this->deleteName($p);

			if ($res)
			{	
				$this->redirect('names.php?id='.$this->getTaxonId());
			}
			else 
			{
				$this->addError('Could not delete name.');
			}
		}

		$concept=$this->getTaxonConcept();

		$this->setPageName(sprintf($this->translate('Names for "%s"'), $this->formatTaxon($concept)));

		$this->smarty->assign('concept',$this->getTaxonConcept());
		if ($this->rHasVal('name_id'))
			$this->smarty->assign('name',$this->getName(array('id'=>$this->rGetVal('name_id'))));
		$this->smarty->assign('types',$this->getNameTypes());
		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('languages',$this->getLanguages());
		$this->smarty->assign('references',$this->getReferences());


		$this->printPage();
       
    }




}
