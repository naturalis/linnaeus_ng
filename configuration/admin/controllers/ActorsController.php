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
		'literature2_authors',
        'actors_taxa'
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

    /**
     * ActorsController constructor.
     */
    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    /**
     * Initialises with the RsdController
     */
    private function initialise()
    {
		$this->Rdf = new RdfController;
    }

    /**
     * Destructor
     */
    public function __destruct ()
    {
        parent::__destruct();
    }


    /**
     * Shows the actors
     */
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

    /**
     * Edit actor
     */
    public function editAction()
	{
	    $this->checkAuthorisation();
	    
	    if ($this->rHasId() && $this->rHasVal('action','delete')) {

			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();

			$this->setActorId($this->rGetId());
			$this->setActorBefore();
			$this->deleteActor();
			$this->setActorId(null);
			$this->logChange(array('before'=>$this->getActorBefore(),'note'=>'deleted actor '.$this->getActorBefore('name')));
			$template='_delete_result';

		} else if ($this->rHasId() && $this->rHasVal('action','save')) {

			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$this->setActorId($this->rGetId());
			$this->setActorBefore();
			$this->updateActor();
			$this->logChange(array('before'=>$this->getActorBefore(),'after'=>$this->getActor(),'note'=>'updated actor '.$this->getActorBefore('name')));

		} else if (!$this->rHasId() && $this->rHasVal('action','save')) {

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
			$this->smarty->assign('links',$this->getActorLinks($actor));
		}
		else
		{
			$this->setPageName($this->translate('New actor'));
		}

		$this->smarty->assign( 'companies', $this->getAllActors(array('is_company'=>true,'search'=>'*')));
		$this->smarty->assign( 'CRUDstates', $this->getCRUDstates() );

		$this->printPage(isset($template) ? $template : null);
	}


    /**
     * ajax Interface
     */
    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVar('action')) return;

		if ( !$this->getAuthorisationState() ) return;

		$return=$this->getActorLookupList($this->rGetAll());

        $this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage();
    }


    /**
     * Setter
     * @param $id
     */
    private function setActorId($id)
	{
		$this->actorId=$id;
	}

    /**
     * Getter
     * @return id or false
     */
	private function getActorId()
	{
        return isset($this->actorId) ? $this->actorId : false;
	}

    /**
     * save Actor
     */
    private function saveActor()
	{
		$name=$this->rGetVal('name');

		if (empty($name))
		{
			$this->addError('Name missing. Actor not saved.');
			$this->setActorId(null);
			return;
		}

		$newActor = $this->models->Actors->save(array(
			'project_id' => $this->getCurrentProjectId(),
			'name' => $name
		));

		if ($newActor) {
			$this->setActorId($this->models->Actors->getNewId());
			$this->createNsrIds(array('id'=>$this->getActorId(),'type'=> 'actor', 'subtype'=> ( $this->rHasVal('is_company','1') ? 'organization' : 'person' )));
			$this->addMessage('New actor created.');
			$this->updateActor();
			$this->logChange(array('after'=>$this->getActor(),'note'=>'new actor '.$name));
		} else {
			$this->addError('Could not create new actor.');
		}
	}

    /**
     * Update Actor
     */
    private function updateActor()
	{
		$fields = array(
		    'name' => $this->translate('Name'),
		    'name_alt' => $this->translate('Alternative naam'),
		    'gender' => $this->translate('Gender'),
		    'is_company' => '"' . $this->translate('Person or organisation') . '"',
            'homepage' => $this->translate('Home page'),
            'logo_url' => $this->translate('Logo url'),
		    'employee_of_id' => '"' . $this->translate('Employee of') . '"'
		);

		foreach($fields as $field => $label) {

			if ($this->rHasVar($field)) {
				if ($this->updateActorValue($field,$this->rGetVal($field))) {
					if ($this->models->Actors->getAffectedRows()!=0)
						$this->addMessage($label.' saved.');
				} else {
					$this->addError($label.' not saved.');
				}
			}

		}
		
		foreach ((array)$this->rGetVal('new_taxa') as $taxon_id) {
		    $this->models->ActorsModel->saveTaxonActor(array(
	            "project_id" => $this->getCurrentProjectId(),
	            "taxon_id" => $taxon_id,
	            "actor_id" => $this->getActorId()
	        ));
	    }
		    
		
	}

    /**
     * Update a field in a Actor model
     * @param $name
     * @param $value
     * @return mixed
     */
    private function updateActorValue($name, $value)
	{
		return $this->models->Actors->update(
			array($name=>(empty($value) ? 'null' : trim($value))),
			array('id'=>$this->getActorId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

    /**
     * Delete Actor
     */
    private function deleteActor()
	{
		$id=$this->getActorId();

		if (empty($id))
		{
			$this->addError("No ID.");
			return;
		}

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
		
		$this->models->ActorsTaxa->delete(
		    array('actor_id'=>$id,'project_id'=>$this->getCurrentProjectId())
		);
		$this->addMessage("Actor detached from ".$this->models->ActorsTaxa->getAffectedRows()." taxa.");
		
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

    /**
     * Returns the alphabet of actors in a project
     * @return mixed
     */
    private function getCompanyAlphabet()
	{
		$alpha = $this->models->Actors->freeQuery(sprintf('select
				distinct if(ord(substr(lower(name),1,1))<97||ord(substr(lower(name),1,1))>122,"#",substr(lower(name),1,1)) as letter
			from %%PRE%%actors
			where
				project_id = %d 
				and is_company = 1
			order by letter', $this->getCurrentProjectId()));

		return $alpha;
	}

    /**
     * Returns the alphabet of actors in a project
     * @return mixed
     */
	private function getIndividualAlphabet()
	{
		return $this->models->ActorsModel->getCompanyAlphabet($this->getCurrentProjectId());
	}

    /**
     * Get the Actor by id
     * @param null $id
     */
    private function getActor($id=null)
	{
		if (empty($id)) {
            $id = $this->getActorId();
        }

        // no Id, no actor
		if (empty($id)) return;

		$alphabet = $this->models->ActorsModel->getIndividualAlphabet(array(
            'projectId' => $this->getCurrentProjectId(),
    		'actorId' => $id
		));

		if ($alphabet)
		{
			$actor=$alphabet[0];

			// catching up...
			if ( empty($actor['nsr_id']) )
			{
				$nsrIds = $this->createNsrIds(array('id'=>$actor['id'],'type'=> 'actor', 'subtype'=> ( $alphabet[0]['is_company']==1 ? 'organization' : 'person' )));
				$actor['nsr_id'] = $nsrIds['nsr_id'];
			}
			
			$actor['taxa'] = $this->getActorTaxa($actor['id']);
			return $actor;
		}

	}

    /**
     * Get all actors by search
     * @param $qry
     */
    private function getAllActors($qry)
    {
        $search=isset($qry['search']) ? $qry['search'] : null;
        $matchStartOnly = isset($qry['match_start']) ? $qry['match_start']==1 : false;
        $isCompany=isset($qry['is_company']) ? $qry['is_company'] : null;

        if (empty($search))
            return;

		return $this->models->ActorsModel->getAllActors(array(
            'projectId' => $this->getCurrentProjectId(),
    		'search' => $search,
    		'isCompany' => $isCompany,
    		'matchStartOnly' => $matchStartOnly
		));

	}

    /**
     * Get a lookup list of actors
     * @param $qry
     * @return array|string
     */
    private function getActorLookupList($qry )
    {
		$data=$this->getAllActors($qry);

        $maxResults=isset($qry['max_results']) && (int)$qry['max_results']>0 ? (int)$qry['max_results'] : $this->_lookupListMaxResults;

		return
			$this->makeLookupList(array(
				'data'=>$data,
				'module'=>'acrors',
				'url'=>'actor.php?id=%s',
				'encode'=>true,
				'isFullSet'=>count($data)<$maxResults
			));

    }
    
    private function getActorTaxa ($id)
    {
        return $this->models->ActorsModel->getActorTaxa([
            'project_id' => $this->getCurrentProjectId(),
            'actor_id' => $id,
        ]);
    }

    /**
     * Get Actor links
     * @param $qry
     * @return array|void
     */
    private function getActorLinks($qry )
    {
        $id = isset($qry['id']) ? $qry['id'] : null;
        $name = isset($qry['name']) ? $qry['name'] : null;
        $name_alt = isset($qry['name_alt']) ? $qry['name_alt'] : null;

		if ( empty($id) && empty($name) && empty($name_alt) )
		{
			$id=$this->getActorId();
		}

		if ( empty($id) ) return;

		// NAMES
		$names = $this->models->ActorsModel->getActorNames(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'expertId' => $id
		));

		foreach((array)$names as $key=>$val) {
			$names[$key]['name']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['name'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );

			if ($val['nametype']==PREDICATE_VALID_NAME) {
				$names[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );
			}

			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);
		}

		// PRESENCE
		$presences = $this->models->ActorsModel->getActorPresences(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'expertId' => $id
		));

		foreach((array)$presences as $key=>$val) {
			$presences[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );
		}

		// PASSPORTS
		$passports = $this->models->ActorsModel->getActorPassports(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'expertId' => $id
		));

		foreach((array)$passports as $key=>$val) {
			$passports[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );
		}

		// LITERATURE
		$literature = $this->models->ActorsModel->getActorLiterature(array(
    		'projectId' => $this->getCurrentProjectId(),
    		'expertId' => $id,
		    'name' => $name,
		    'nameAlt' => $name_alt
		));
		
		$result = array(
			'names' => $names,
			'presences'=>$presences,
			'passports'=>$passports,
			'literature'=>$literature,
		    'taxa' => $this->getActorTaxa($id)
		);

		return $result;
	}

    /**
     * set last Actor
     */
    private function setActorBefore()
	{
		$this->actorBefore = $this->getActor();
	}

    /**
     * get the Actor before this actor
     */
	private function getActorBefore($key=null )
	{
		if ( $key && isset($this->actorBefore[$key]) ) {
			return $this->actorBefore[$key];
		} else {
			return $this->actorBefore;
		}
	}

}

