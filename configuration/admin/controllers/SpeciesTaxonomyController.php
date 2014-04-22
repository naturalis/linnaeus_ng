<?php

include_once ('Controller.php');
include_once ('RdfController.php');
class SpeciesTaxonomyController extends Controller
{
    private $_useNBCExtras = false;
	private $_lookupListMaxResults=50;
    public $usedModels = array(
		'names',
		'name_types'
    );
    public $usedHelpers = array(
    );
    public $cacheFiles = array(
    );
    public $cssToLoad = array(
        'taxon.css', 
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

					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and _a.taxon_id=".$id."
					order by 
						sort_criterium desc
						",
				'fieldAsIndex' => 'id'
			)
		);

		$prefferedname=null;

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


			if ($val['language_id']==LANGUAGE_ID_SCIENTIFIC && $val['nametype']==PREDICATE_VALID_NAME)
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

	private function getName($type)
	{	
		$id=$this->getTaxonId();

        $name=$this->models->Names->freeQuery(
			array(
				'query' => "
					select
						*
					from %PRE%names _a 
					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and _a.taxon_id = ".$id."
						and _a.type_id = ".$this->_nameTypeIds[$type]['id']
			)
		);
		
		return $name[0];

	}
	

	private function constructTaxonConceptName($p)
	{
		return trim(
			str_replace('  ',' ',
				$p['uninomial'].' '.
				$p['specific_epithet'].' '.
				$p['infra_specific_epithet'].' '.
				$p['authorship']
			)
		);
	}

	private function updateName($p)
	{
		$id=$this->getTaxonId();
		
		if (is_null($id))
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
				'taxon_id'=>$this->getTaxonId(),
				'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
				'id'=>$p['id']
			)
		);

		return $res;
	}

	private function updateTaxon($p)
	{
		$id=$this->getTaxonId();
		
		if (is_null($id))
			return;

		$res=$this->models->Taxon->update(
			array('taxon'=>$this->constructTaxonConceptName($p),),
			array('project_id' => $this->getCurrentProjectId(),'id' => $id)
		);

		return $res;
	}



    public function taxonomyAction ()
    {
        $this->checkAuthorisation();

        if ($this->rHasVal('action','save') && $this->rHasVal('id') && !$this->isFormResubmit())
		{
			
			$current=$this->getName(PREDICATE_VALID_NAME);
			$parts=$this->rGetVal('valid_name');
			$parts['id']=$current['id'];
			
			if ($this->rHasVar('new_parent_id'))
			{
				$parts['parent_id']=$this->rGetVal('new_parent_id');
			}

			/*
				need checks:
				
				- does parent_id exist?
				- is parent legal:
					- can it have children of the proposed rank?
					- do the names match?
				- count spaces
			*/

			$res=$this->updateName($parts);
			
			if ($res)
			{	
				$res=$this->updateTaxon($parts);
					
				if ($res)
				{
					$this->addMessage('Name saved.');
				}
				else
				{
					$this->addError('Could not save name.');
					$res=$this->updateName($current);
				}
			}
			else 
			{
				$this->addError('Could not save name.');
			}

			$this->setTaxonConcept();	
		}

		$this->addError('Please note: this function is still under development. As yet, no checks are performed on name or parent - data is saved <i>as is</i>, including errors.');

		$concept=$this->getTaxonConcept();

		$this->setPageName(sprintf($this->translate('Editing "%s"'), $this->formatTaxon($concept)));

		$this->smarty->assign('ranks',$this->newGetProjectRanks());
		$this->smarty->assign('concept',$concept);
		$this->smarty->assign('parent',$this->getTaxonById($concept['parent_id']));
		$this->smarty->assign('names',$this->getNames());

		$this->printPage();


        
        
    }




}
