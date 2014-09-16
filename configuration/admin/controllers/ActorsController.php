<?php

include_once ('Controller.php');
include_once ('RdfController.php');

class ActorsController extends Controller
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'literature2',
		'names',
		'actors',
		'presence_taxa',
		'content_taxon',
		'literature2_authors'
    );
   
    public $controllerPublicName = 'Actoren';

    public $cacheFiles = array(
    );
    
    public $cssToLoad = array(
		'lookup.css',
		'nsr_taxon_beheer.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'lookup.js',
				'nsr_taxon_beheer.js',
				'actors.js',
			)
		);
		
	private $teferenceId;

    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->Rdf = new RdfController;
    }



    public function indexAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Index'));
		$this->smarty->assign('individualAlphabet',$this->getIndividualAlphabet());
		$this->smarty->assign('companyAlphabet',$this->getCompanyAlphabet());
		$this->printPage();
	}

    public function ajaxInterfaceAction ()
    {
        if (!isset($this->requestData['action'])) return;
		$return=null;
		$return=$this->getActorLookupList($this->requestData);
        $this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage();
    }

    public function editAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Edit actor'));

		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			$this->setActorId($this->rGetId());
			$this->deleteActor();
			$this->setActorId(null);
			$template='_delete_result';
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setActorId($this->rGetId());
			$this->updateActor();
		} 
		else
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->smarty->assign('reference',$this->requestData);
			$this->saveActor();
		}

		if ($this->rHasId())
		{
			$this->setActorId($this->rGetId());
		} 

		if ($this->getActorId())
		{
			$this->smarty->assign('actor',$this->getActor());
			$this->smarty->assign('links',$this->getActorLinks());
		}

		$this->smarty->assign('companies',$this->getActors(array('is_company'=>true,'search'=>'*')));
		$this->printPage(isset($template) ? $template : null);
	}



	private function setActorId($id)
	{
		$this->actorId=$id;
	}

	private function getActorId()
	{
		return isset($this->actorId) ? $this->actorId : false;
	}

	private function saveActor()
	{
		$name=$this->rGetVal('name');
					
		if (empty($name))
		{
			$this->addError('Geen naam. Actor niet opgeslagen.');
			$this->setActorId(null);
			return;
		}

		$d=$this->models->Actors->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'name' => $name
		));
		
		if ($d)
		{
			$this->setActorId($this->models->Actors->getNewId());
			$this->addMessage('Nieuw actor aangemaakt.');
			$this->updateActor();
		}
		else 
		{
			$this->addError('Aanmaak nieuwe actor mislukt.');
		}
	}

	private function updateActor()
	{
		$f=array( 
			'name' => 'Naam',
			'name_alt' => 'Alternatieve naam',
			'gender' => 'Geslacht',
			'is_company' => '"Persoon of instelling"',
			'homepage' => 'Homepage',
			'employee_of_id' => '"Werkzaam bij"'
		);
		
		foreach($f as $field=>$label) 
		{
			if ($this->rHasVar($field))
			{
				if ($this->updateActorValue($field,$this->rGetVal($field)))
				{
					if ($this->models->Actors->getAffectedRows()!=0)
						$this->addMessage($label.' opgeslagen.');
				}
				else
				{
					$this->addError($label.' niet opgeslagen.');
				}
			}
		}
		
	}

	private function updateActorValue($name,$value)
	{
		return $this->models->Actors->update(
			array($name=>(empty($value) ? 'null' : trim($value))),
			array('id'=>$this->getActorId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function deleteActor()
	{
		$id=$this->getActorId();

		if (empty($id))
		{
			$this->addError("Geen ID.");
			return;
		}

        $this->models->Names->freeQuery(
			"update %PRE%names set expert_id = null where project_id = ".$this->getCurrentProjectId()." and expert_id = ".$id
		);
		$this->addMessage("Expert verwijderd van ".$this->models->Names->getAffectedRows()." namen.");

        $this->models->Names->freeQuery(
			"update %PRE%names set organisation_id = null where project_id = ".$this->getCurrentProjectId()." and organisation_id = ".$id
		);
		$this->addMessage("Organisatie verwijderd van ".$this->models->Names->getAffectedRows()." namen.");

        $this->models->PresenceTaxa->freeQuery(
			"update %PRE%presence_taxa set actor_id = null where project_id = ".$this->getCurrentProjectId()." and actor_id = ".$id
		);
		$this->addMessage("Expert verwijderd van ".$this->models->PresenceTaxa->getAffectedRows()." statussen.");

        $this->models->PresenceTaxa->freeQuery(
			"update %PRE%presence_taxa set actor_org_id = null where project_id = ".$this->getCurrentProjectId()." and actor_org_id = ".$id
		);
		$this->addMessage("Organisatie verwijderd van ".$this->models->PresenceTaxa->getAffectedRows()." statussen.");

		$this->models->Literature2Authors->freeQuery(
			"delete from %PRE%literature2_authors where project_id = ".$this->getCurrentProjectId()." and actor_id = ".$id
		);
		$this->addMessage("Auteur ontkoppeld van ".$this->models->Literature2Authors->getAffectedRows()." literatuurreferenties.");

		$this->models->Actors->freeQuery("delete from %PRE%rdf where _a.object_id=".$id." _a.object_type='actor'");
		$this->addMessage("Auteur verwijderd van ".$this->models->Actors->getAffectedRows()." paspoorten.");

		$this->models->Actors->freeQuery("delete from %PRE%actors where project_id = ".$this->getCurrentProjectId()." and id = ".$id." limit 1");	
		$this->addMessage("Actor verwijderd.");
	}

	private function getCompanyAlphabet()
	{
		$alpha=$this->models->Actors->freeQuery("
			select
				distinct if(ord(substr(lower(name),1,1))<97||ord(substr(lower(name),1,1))>122,'#',substr(lower(name),1,1)) as letter
			from			
				%PRE%actors
			where
				project_id = ".$this->getCurrentProjectId()."
				and is_company = 1
			order by letter
		");

		return $alpha;
	}

	private function getIndividualAlphabet()
	{
		$alpha=$this->models->Actors->freeQuery("
			select
				distinct if(ord(substr(lower(name),1,1))<97||ord(substr(lower(name),1,1))>122,'#',substr(lower(name),1,1)) as letter
			from			
				%PRE%actors
			where
				project_id = ".$this->getCurrentProjectId()."
				and is_company != 1
			order by letter
		");

		return $alpha;
	}

	private function getActor($id=null)
	{
		if (empty($id))
			$id=$this->getActorId();

		if (empty($id))
			return;

		$l=$this->models->Actors->freeQuery("
			select
				_a.*, _e.name as employer_name
				
			from %PRE%actors _a

			left join %PRE%actors _e
				on _a.employee_of_id = _e.id 
				and _a.project_id=_e.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.id = ".$id
		);	

		if ($l)
			return $l[0];

	}

    private function getActors($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $isCompany=isset($p['is_company']) ? $p['is_company'] : null;

        if (empty($search))
            return;
			
		if ($matchStartOnly && $search=='#')
		{
			$fetchNonAlpha=true;
		}
		else
		{
			$fetchNonAlpha=false;
		}

		$data=$this->models->Actors->freeQuery("
			select
				_a.*, _e.name as employer_name
				
			from %PRE%actors _a

			left join %PRE%actors _e
				on _a.employee_of_id = _e.id 
				and _a.project_id=_e.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.is_company = ".($isCompany ? '1' : '0' )."
				". ($search!='*' ? "	
					and (
						_a.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%' 
					)" : "")."

			order by _a.name
			");	

//						or _a.name_alt like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'

		return $data;
		
	}

    private function getActorLookupList($p)
    {
		$data=$this->getActors($p);

        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;

		return
			$this->makeLookupList(array(
				'data'=>$data,
				'module'=>'acrors',
				'url'=>'actor.php?id=%s',
				'encode'=>true,
				'isFullSet'=>count($data)<$maxResults
			));

    }
	
    private function getActorLinks($id=null)
    {
		if (empty($id))
			$id=$this->getActorId();

		if (empty($id))
			return;

        $names=$this->models->Names->freeQuery("
			select
				_a.taxon_id,
				_a.name,
				_b.nametype,
				_c.language,
				_d.label as language_label,
				_g.taxon

			from %PRE%names _a 

			left join %PRE%name_types _b
				on _a.type_id=_b.id 
				and _a.project_id=_b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%taxa _g
				on _a.taxon_id = _g.id 
				and _a.project_id=_g.project_id

		where
			_a.project_id = ".$this->getCurrentProjectId()."
			and (
				_a.expert_id=".$id." or 
				_a.organisation_id=".$id.
			")"
		);
		
		foreach((array)$names as $key=>$val)
		{
			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);
		}

		$presences=$this->models->PresenceTaxa->freeQuery(
			"select
				_a.taxon_id,
				_g.taxon,
				_a.presence_id,
				_b.label as presence_label,
				_a.reference_id
				
			from %PRE%presence_taxa _a

			left join %PRE%taxa _g
				on _a.taxon_id = _g.id 
				and _a.project_id=_g.project_id

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id 
				and _a.project_id=_b.project_id 
				and _b.language_id=".$this->getDefaultProjectLanguage()."

			where _a.project_id = ".$this->getCurrentProjectId()."
				and (
				_a.actor_id=".$id." or 
				_a.actor_org_id=".$id.
			")"
		);	
		
		$passports=$this->models->ContentTaxon->freeQuery("
			select
				_a.id,
				_a.subject_type,
				_a.predicate,
				_c.taxon,
				_d.title,
				_b.taxon_id
			from
				%PRE%rdf _a

			left join %PRE%content_taxa _b
				on _a.project_id = _b.project_id
				and _a.subject_id = _b.id
				
			left join %PRE%pages_taxa_titles _d
				on _a.project_id = _d.project_id
				and _b.page_id = _d.page_id
				and _d.language_id = ".$this->getDefaultProjectLanguage()."

			left join %PRE%taxa _c
				on _a.project_id = _b.project_id
				and _b.taxon_id = _c.id
			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.object_id=".$id."
				and _a.object_type='actor'
				and _a.subject_type='passport'
			order by taxon, title
		");
		
		return array(
			'names' => $names,
			'presences'=>$presences,
			'passports'=>$passports,
		);
	
	}
	
}
