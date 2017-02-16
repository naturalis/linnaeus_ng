<?php

include_once ('NsrController.php');
include_once ('RdfController.php');

class ActorsController extends NsrController
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'literature2',
		'names',
		'actors',
        'rdf',
        'nsr_ids',
		'presence_taxa',
		'content_taxa',
		'literature2_authors'
    );

    public $controllerPublicName = 'Actors';

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

	private $actorBefore;

    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    private function initialise()
    {
		$this->Rdf = new RdfController;
    }

    public function __destruct ()
    {
        parent::__destruct();
    }


    public function indexAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Index'));

		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->smarty->assign( 'can_create', $this->getAuthorisationState() );

		$this->smarty->assign('individualAlphabet',$this->getIndividualAlphabet());
		$this->smarty->assign('companyAlphabet',$this->getCompanyAlphabet());
		$this->printPage();
	}

    public function editAction()
	{
		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();

			$this->setActorId($this->rGetId());
			$this->setActorBefore();
			$this->deleteActor();
			$this->setActorId(null);
			$this->logChange(array('before'=>$this->getActorBefore(),'note'=>'deleted actor '.$this->getActorBefore('name')));
			$template='_delete_result';
		}
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$before=$this->getActor();
			$this->setActorId($this->rGetId());
			$this->setActorBefore();
			$this->updateActor();
			$this->logChange(array('before'=>$this->getActorBefore(),'after'=>$this->getActor(),'note'=>'updated actor '.$this->getActorBefore('name')));
		}
		else
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

			$this->smarty->assign('reference',$this->rGetAll());
			$this->saveActor();
		}

		if ($this->rHasId())
		{
			$this->setActorId($this->rGetId());
		}

		if ($this->getActorId())
		{
			$this->setPageName($this->translate('Edit actor'));
			$actor=$this->getActor();
			$this->smarty->assign('actor',$actor);
			$this->smarty->assign('links',$this->getActorLinks( $actor ));
		}
		else
		{
			$this->setPageName($this->translate('New actor'));
		}

		$this->smarty->assign( 'companies', $this->getAllActors(array('is_company'=>true,'search'=>'*')));
		$this->smarty->assign( 'CRUDstates', $this->getCRUDstates() );

		$this->printPage(isset($template) ? $template : null);
	}



    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVar('action')) return;

		if ( !$this->getAuthorisationState() )
			return;

		$return=null;
		$return=$this->getActorLookupList($this->rGetAll());
        $this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage();
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
			$this->addError('Name missing. Actor not saved.');
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
			$this->createNsrIds(array('id'=>$this->getActorId(),'type'=> 'actor', 'subtype'=> ( $this->rHasVal('is_company','1') ? 'organization' : 'person' )));
			$this->addMessage('New actor created.');
			$this->updateActor();
			$this->logChange(array('after'=>$this->getActor(),'note'=>'new actor '.$name));
		}
		else
		{
			$this->addError('Could not create new actor.');
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
						$this->addMessage($label.' saved.');
				}
				else
				{
					$this->addError($label.' not saved.');
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
			$this->addError("No ID.");
			return;
		}

		/*
		 * Free queries should be update/delete statements! Rewritten
		 *
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


		$this->models->Actors->freeQuery("delete from %PRE%rdf where object_id=".$id." and object_type='actor'");
		$this->addMessage("Auteur verwijderd van ".$this->models->Actors->getAffectedRows()." tabbladen.");

		$this->models->Actors->freeQuery("delete from %PRE%nsr_ids where project_id = ".$this->getCurrentProjectId()." and lng_id=".$id." and item_type='actor'");
		$this->addMessage("Actor NSR ID verwijderd.");

		$this->models->Actors->freeQuery("delete from %PRE%actors where project_id = ".$this->getCurrentProjectId()." and id = ".$id." limit 1");
		$this->addMessage("Actor verwijderd.");
		*/

		$this->models->Names->update(
			array('expert' => null),
			array('expert_id'=>$id,'project_id'=>$this->getCurrentProjectId())
		);
		$this->addMessage("Expert deleted from ".$this->models->Names->getAffectedRows()." names.");

		$this->models->Names->update(
		    array('organisation_id' => null),
		    array('organisation_id'=>$id,'project_id'=>$this->getCurrentProjectId())
		);
		$this->addMessage("Organisation deleted from ".$this->models->Names->getAffectedRows()." names.");

        $this->models->PresenceTaxa->update(
		    array('actor_id' => null),
		    array('actor_id'=>$id,'project_id'=>$this->getCurrentProjectId())
		);
		$this->addMessage("Expert deleted from ".$this->models->PresenceTaxa->getAffectedRows()." statuses.");

		$this->models->PresenceTaxa->update(
		    array('actor_org_id' => null),
		    array('actor_org_id'=>$id,'project_id'=>$this->getCurrentProjectId())
		);
		$this->addMessage("Organisation deleted from ".$this->models->PresenceTaxa->getAffectedRows()." statuses.");

		$this->models->Literature2Authors->delete(
		    array('actor_id'=>$id,'project_id'=>$this->getCurrentProjectId())
		);
		$this->addMessage("Actor detached from ".$this->models->Literature2Authors->getAffectedRows()." literature references.");

		$this->models->Rdf->delete(
		    array('object_id'=>$id,'object_type'=>'actor')
		);
		$this->addMessage("Actor deleted from ".$this->models->Rdf->getAffectedRows()." tabs.");

		$this->models->NsrIds->delete(
		    array('lng_id'=>$id,'project_id'=>$this->getCurrentProjectId(),'item_type'=>'actor')
		);
		$this->addMessage("Actor NSR ID deleted.");

		// Left this one because of the limit 1
		$this->models->Actors->freeQuery("delete from %PRE%actors where project_id = ".$this->getCurrentProjectId()." and id = ".$id." limit 1");
		$this->addMessage("Actor deleted.");

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
		return $this->models->ActorsModel->getCompanyAlphabet($this->getCurrentProjectId());
	}

	private function getActor($id=null)
	{
		if (empty($id))
			$id=$this->getActorId();

		if (empty($id))
			return;

		$l = $this->models->ActorsModel->getIndividualAlphabet(array(
            'projectId' => $this->getCurrentProjectId(),
    		'actorId' => $id
		));

		if ($l)
		{

			$actor=$l[0];

			// catching up...
			if ( empty($actor['nsr_id']) )
			{
				$d=$this->createNsrIds(array('id'=>$actor['id'],'type'=> 'actor', 'subtype'=> ( $l[0]['is_company']==1 ? 'organization' : 'person' )));
				$actor['nsr_id']=$d['nsr_id'];
			}

			return $actor;
		}

	}

    private function getAllActors( $p )
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $isCompany=isset($p['is_company']) ? $p['is_company'] : null;

        if (empty($search))
            return;

		return $this->models->ActorsModel->getAllActors(array(
            'projectId' => $this->getCurrentProjectId(),
    		'search' => $search,
    		'isCompany' => $isCompany,
    		'matchStartOnly' => $matchStartOnly
		));

	}

    private function getActorLookupList( $p )
    {
		$data=$this->getAllActors($p);

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

    private function getActorLinks( $p )
    {
        $id = isset($p['id']) ? $p['id'] : null;
        $name = isset($p['name']) ? $p['name'] : null;
        $name_alt = isset($p['name_alt']) ? $p['name_alt'] : null;

		if ( empty($id) && empty($name) && empty($name_alt) )
		{
			$id=$this->getActorId();
		}

		if ( empty($id) )
			return;

		// NAMES
		$names = $this->models->ActorsModel->getActorNames(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'expertId' => $id
		));

		foreach((array)$names as $key=>$val)
		{
			$names[$key]['name']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['name'],'base_rank_id'=>$val['base_rank_id'] ) );

			if ($val['nametype']==PREDICATE_VALID_NAME)
			{
				$names[$key]['taxon']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
			}

			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);
		}

		// PRESENCE
		$presences = $this->models->ActorsModel->getActorPresences(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'expertId' => $id
		));

		foreach((array)$presences as $key=>$val)
		{
			$presences[$key]['taxon']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
		}

		// PASSPORTS
		$passports = $this->models->ActorsModel->getActorPassports(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'expertId' => $id
		));

		foreach((array)$passports as $key=>$val)
		{
			$passports[$key]['taxon']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
		}

		// LITERATURE
		$literature = $this->models->ActorsModel->getActorLiterature(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'expertId' => $id,
		    'name' => $name,
		    'nameAlt' => $name_alt
		));

		return
			array(
				'names' => $names,
				'presences'=>$presences,
				'passports'=>$passports,
				'literature'=>$literature,
			);
	}

	private function setActorBefore()
	{
		$this->actorBefore=$this->getActor();
	}

	private function getActorBefore( $f=null )
	{
		if ( $f && isset($this->actorBefore[$f]) )
		{
			return $this->actorBefore[$f];
		}
		else
		{
			return $this->actorBefore;
		}
	}



}

