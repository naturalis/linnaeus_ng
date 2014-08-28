<?php

include_once ('Controller.php');
include_once ('RdfController.php');

class Literature2Controller extends Controller
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'literature2',
		'literature_taxon',
		'names',
		'actors',
		'presence_taxa'
    );
   
    public $controllerPublicName = 'Literatuur (v2)';

    public $cacheFiles = array(
    );
    
    public $cssToLoad = array(
		'lookup.css',
		'nsr_taxon_beheer.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'nsr_taxon_beheer.js',
				'literature2.js',
				'lookup.js',
			)
		);

	private $referenceId=null;

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
		$this->smarty->assign('authorAlphabet',$this->getAuthorAlphabet());
		$this->smarty->assign('titleAlphabet',$this->getTitleAlphabet());
		$this->printPage();
	}

    public function ajaxInterfaceAction ()
    {
        if (!isset($this->requestData['action'])) return;
		$return=null;
		$return=$this->getReferenceLookupList($this->requestData);
        $this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage();
    }

    public function editAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Edit reference'));

		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			$this->setReferenceId($this->rGetId());
			$this->deleteReference();
			$this->setReferenceId(null);
			$template='_delete_result';
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setReferenceId($this->rGetId());
			$this->updateReference();
			
		} 
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->smarty->assign('reference',$this->requestData);
			$this->saveReference();
		}
		if ($this->rHasId())
		{
			$this->setReferenceId($this->rGetId());
		} 

		if ($this->getReferenceId())
		{
			$this->smarty->assign('reference',$this->getReference());
			$this->smarty->assign('links',$this->getReferenceLinks());
		}

		$this->smarty->assign('languages',$this->getLanguages());
		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('publicationTypes',$this->getPublicationTypes());
		$this->printPage(isset($template) ? $template : null);
	}



	private function setReferenceId($id)
	{
		$this->referenceId=$id;
	}

	private function getReferenceId()
	{
		return isset($this->referenceId) ? $this->referenceId : false;
	}

	private function saveReference()
	{
		$label=$this->rGetVal('label');
					
		if (empty($label))
		{
			$this->addError('Geen titel. Referentie niet opgeslagen.');
			$this->setReferenceId(null);
			return;
		}

		$d=$this->models->Literature2->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'label' => $label
		));
		
		if ($d)
		{
			$this->setReferenceId($this->models->Literature2->getNewId());
			$this->addMessage('Nieuw referentie aangemaakt.');
			$this->updateReference();
		}
		else 
		{
			$this->addError('Aanmaak nieuwe referentie mislukt.');
		}
	}


	private function updateReference()
	{
		$f=array( 
			'language_id' => 'Taal',
			'label' => 'Naam',
			'date' => 'Datum',
			'author' => 'Auteur (verbatim)',
			'actor_id' => 'Auteur',
			'publication_type' => 'Publicatietype',
			'citation' => 'Citatie',
			'source' => 'Bron',
			'publishedin' => 'Publicatie (verbatim)',
			'publishedin_id' => 'Publicatie',
			'periodical' => 'Periodiek (verbatim)',
			'periodical_id' => 'Periodiek',
			'pages' => 'Pagina(s)',
			'volume' => 'Volume',
			'external_link' => 'Link',
		);
		
		foreach($f as $field=>$label) 
		{
			if ($this->rHasVar($field))
			{
				if ($this->updateReferenceValue($field,$this->rGetVal($field)))
				{
					if ($this->models->Literature2->getAffectedRows()!=0)
						$this->addMessage($label.' opgeslagen.');
				}
				else
				{
					$this->addError($label.' niet opgeslagen.');
				}
			}
		}
		
	}

	private function updateReferenceValue($name,$value)
	{
		return $this->models->Literature2->update(
			array($name=>(empty($value) ? 'null' : trim($value))),
			array('id'=>$this->getReferenceId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function deleteReference()
	{
		$id=$this->getReferenceId();

		if (empty($id))
		{
			$this->addError("Geen ID.");
			return;
		}

        $this->models->Names->freeQuery(
			"update %PRE%names set reference_id = null where project_id = ".$this->getCurrentProjectId()." and reference_id = ".$id
		);
		$this->addMessage("Referentie verwijderd van ".$this->models->Names->getAffectedRows()." namen.");

        $this->models->PresenceTaxa->freeQuery(
			"update %PRE%presence_taxa set reference_id = null where project_id = ".$this->getCurrentProjectId()." and reference_id = ".$id
		);
		$this->addMessage("Referentie verwijderd van ".$this->models->PresenceTaxa->getAffectedRows()." statussen.");

		$this->models->Literature2->freeQuery("delete from %PRE%literature2 where project_id = ".$this->getCurrentProjectId()." and id = ".$id." limit 1");	
		$this->addMessage("Referentie verwijderd.");

	}


	private function getTitleAlphabet()
	{
		$alpha=$this->models->Literature2->freeQuery("
			select
				distinct if(ord(substr(lower(_a.label),1,1))<97||ord(substr(lower(_a.label),1,1))>122,'#',substr(lower(_a.label),1,1)) as letter
			from			
				%PRE%literature2 _a
			where
				_a.project_id = ".$this->getCurrentProjectId()."
			");

		return $alpha;
	}

	private function getAuthorAlphabet()
	{
		$alpha=$this->models->Literature2->freeQuery("
			select distinct * from (
				select
					distinct if(ord(substr(lower(_a.author),1,1))<97||ord(substr(lower(_a.author),1,1))>122,'#',substr(lower(_a.author),1,1)) as letter
				from			
					%PRE%literature2 _a
				where
					_a.project_id = ".$this->getCurrentProjectId()."
			union
				select
					distinct if(ord(substr(lower(_f.name),1,1))<97||ord(substr(lower(_f.name),1,1))>122,'#',substr(lower(_f.name),1,1)) as letter

				from			
					%PRE%literature2 _a

				left join %PRE%actors _f
					on _a.actor_id = _f.id 
					and _a.project_id=_f.project_id		

				where
					_a.project_id = ".$this->getCurrentProjectId()."
			) as unification
			order by letter
		");

		return $alpha;
	}

	private function getReference($id=null)
	{
		if (empty($id))
			$id=$this->getReferenceId();

		if (empty($id))
			return;

		$l=$this->models->Literature2->freeQuery(
			"select
				_a.*,
				ifnull(_a.author,ifnull(_e.name,'-')) as author_or_verbatim,
				_h.label as publishedin_label,
				_i.label as periodical_label

			from %PRE%literature2 _a

			left join %PRE%actors _e
				on _a.actor_id = _e.id 
				and _a.project_id=_e.project_id

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.id = ".$id
		);
		
		if ($l)
			return $l[0];

	}

    private function getReferences($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $searchTitle=isset($p['search_title']) ? $p['search_title'] : null;
        $searchAuthor=isset($p['search_author']) ? $p['search_author'] : null;

        if (empty($search) && empty($searchTitle) && empty($searchAuthor))
            return;
			
		if (empty($search) && $matchStartOnly && ($searchTitle=='#' || $searchAuthor=='#'))
		{
			$fetchNonAlpha=true;
		}
		else
		{
			$fetchNonAlpha=false;
		}

		$data=$this->models->Literature2->freeQuery(
			"select
				_a.id,
				_a.language_id,
				_a.label,
				_a.date,
				ifnull(_a.author,ifnull(_e.name,'-')) as author,
				_a.publication_type,
				_a.citation,
				_a.source,
				ifnull(_a.publishedin,ifnull(_h.label,null)) as publishedin,
				ifnull(_a.periodical,ifnull(_i.label,null)) as periodical,
				_a.pages,
				_a.volume,
				_a.external_link
				
			from %PRE%literature2 _a

			left join %PRE%actors _e
				on _a.actor_id = _e.id 
				and _a.project_id=_e.project_id

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 

			". (!empty($search) && $search!='*' ? "	
					and (
						_a.label like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%' or
						_a.author like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%' or
						_e.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
					)" : "")."

			".
				( !empty($searchTitle) ?
					( $fetchNonAlpha ?
						"and ord(substr(lower(_a.label),1,1))<97||ord(substr(lower(_a.label),1,1))>122"  : 
						"and (_a.label like '".($matchStartOnly ? '':'%').mysql_real_escape_string($searchTitle)."%')"
					) : 
				"")."

			". ( !empty($searchAuthor) ?
					( $fetchNonAlpha ?
						"and (
							(ord(substr(lower(_a.author),1,1))<97||ord(substr(lower(_a.author),1,1))>122) ||
							(ord(substr(lower(_e.name),1,1))<97||ord(substr(lower(_e.name),1,1))>122)
						)" : 
						"and (
							_a.author like '".($matchStartOnly ? '':'%').mysql_real_escape_string($searchAuthor)."%' or
							_e.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($searchAuthor)."%'
						)"
					) :
				 "")."

			order by ".
				(!empty($search) || !empty($searchTitle) ? "_a.label, _a.author " : "" ).
				(!empty($searchAuthor) ? "_a.author, _a.label " : "" )
			);	
			
		return $data;
		
	}

    private function getReferenceLinks($id=null)
    {
		if (empty($id))
			$id=$this->getReferenceId();

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
			and _a.reference_id=".$id
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
				and _a.reference_id=".$id
		);	
		
		return array(
			'names' => $names,
			'presences'=>$presences,
		);
	
	}





    private function getReferenceLookupList($p)
    {
		$data=$this->getReferences($p);

        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;

		return
			$this->makeLookupList(
				$data,
				'reference',
				'reference.php?id=%s',
				false,
				true,
				count($data)<$maxResults
			);

    }

	private function getActors()
	{
		return $this->models->Actors->freeQuery(
			"select
				_e.id,
				_e.name as label,
				_e.name_alt,
				_e.homepage,
				_e.gender,
				_e.is_company,
				_e.employee_of_id,
				_f.name as company_of_name,
				_f.name_alt as company_of_name_alt,
				_f.homepage as company_of_homepage

			from %PRE%actors _e

			left join %PRE%actors _f
				on _e.employee_of_id = _f.id 
				and _e.project_id=_f.project_id

			where
				_e.project_id = ".$this->getCurrentProjectId()."

			order by
				_e.is_company, _e.name
		");	
	}
		
	private function getLanguages()
	{
        $used=$this->models->Names->freeQuery("
				select count(id) as `count`, language_id
				from %PRE%names
				where project_id=".$this->getCurrentProjectId()."
				group by language_id
				order by `count` asc
		");
		
		$stuff=null;
		foreach((array)$used as $key => $val)
		{
			$stuff .= "when _c.id = ".$val['language_id']." then ".($key+1)."\n";
		}
		
		if (!empty($stuff))
		{
			$stuff = ", case ".$stuff." else 0 end as sort_criterium\n";
		}

        $languages=$this->models->Language->freeQuery("
			select
				_c.id,
				_c.language,
				ifnull(_d.label,_c.language) as label
				".$stuff."
			from %PRE%languages _c

			left join %PRE%labels_languages _d
				on _c.id=_d.language_id
				and _d.project_id = ".$this->getCurrentProjectId()."
				and _d.label_language_id=".$this->getDefaultProjectLanguage()."
				order by ".(!empty($stuff) ? "sort_criterium desc, " : "")."label asc
			");

		return $languages;
	}

    private function getPublicationTypes()
    {
		return
			$this->models->Literature2->freeQuery("
				select 
					distinct publication_type 
				from 
					%PRE%literature2 
				where 
					publication_type is not null 
				order by 
					publication_type
			");
	}



	
}
