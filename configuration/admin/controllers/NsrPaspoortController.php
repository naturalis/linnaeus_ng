<?php
/*
	TAB_VERSPREIDING::$this->getPresenceData($taxon)
	TAB_BEDREIGING_EN_BESCHERMING::EZ
	CTAB_LITERATURE
	CTAB_MEDIA
	CTAB_DNA_BARCODES
*/


include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

class NsrPaspoortController extends NsrController
{
    public $usedModels = array(
		'actors',
		'content_taxa',
        'pages_taxa',
        'pages_taxa_titles',
		'tab_order',
		'nsr_ids',
		'trash_can'
	);
    public $usedHelpers = array();
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_beheer.css',
        'media.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_beheer.js',
            'media.js'
        )
    );
    public $modelNameOverride='NsrTaxonModel';
    public $controllerPublicName = 'Taxon editor';
    public $includeLocalMenu = false;
	private $taxonId;
	private $obsoleteTabs=array();
	private $activeLanguage;

    /**
     * NsrPaspoortController constructor.
     */
    public function __construct()
    {
        parent::__construct();
		$this->initialize();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Paspoort action, shows actors, tabs (pages), concept
     */
    public function paspoortAction()
    {
		if (!$this->rHasId())
		{
			$this->redirect('index.php');
		}

		$this->UserRights->setItemId( $this->rGetId() );
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

		$this->setTaxonId($this->rGetId());

        $this->setPageName($this->translate('Edit taxon passport'));

		$this->smarty->assign( 'actors', $this->getActors() );
		$this->smarty->assign( 'tabs', $this->getPassportCategories() );
		$this->smarty->assign( 'concept', $this->getTaxonById($this->getTaxonId()) );
		$this->smarty->assign( 'obsolete_tabs', $this->obsoleteTabs );
        $this->smarty->assign( 'languages', $this->getProjectLanguages());
        $this->smarty->assign( 'activeLanguage', $this->getActiveLanguage());

		$this->UserRights->setActionType( $this->UserRights->getActionPublish() );
		$this->smarty->assign( 'can_publish', $this->getAuthorisationState() );

		$this->printPage();
	}

    /**
     * Paspoort Meta action, shows actors, tabs (pages), concept
     */
    public function paspoortMetaAction()
    {
		if (!$this->rHasId())
		{
			$this->redirect('index.php');
		}

		$this->UserRights->setItemId( $this->rGetId() );
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

		$this->setTaxonId($this->rGetId());

		if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->savePassportMeta($this->rGetAll());
		} else
		if ($this->rHasVal('action','delete') && $this->rHasVal('tab') && !$this->isFormResubmit())
		{
			$this->deletePassportMeta($this->rGetAll());
		}

        $this->setPageName($this->translate('Edit taxon passport metadata'));

		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('tabs',$this->getPassportCategories());
		$this->smarty->assign('concept',$this->getTaxonById($this->getTaxonId()));

		$this->printPage();
	}

    /**
     * ajaxInterface
     */
    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		if ( $this->getAuthorisationState()==false ) return;

		if ($this->rHasVal('action', 'save_passport'))
		{
			$p=$this->rGetAll();

			$this->UserRights->setActionType( $this->UserRights->getActionPublish() );
			if ( $this->getAuthorisationState()==false )
			{
				unset($p['publish']);
			}

			$return=$this->savePassport( $p );
        }

        $this->allowEditPageOverlay=false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
    }

    /**
     * initialize the controller with tabs
     */
    private function initialize()
    {
		// creating constants for the tab id's (id for page 'Schade en nut' becomes TAB_SCHADE_EN_NUT)
		foreach((array)$this->models->PagesTaxa->_get(array('id' => array('project_id' => $this->getCurrentProjectId()))) as $page)
		{
			$p=trim(strtoupper(str_replace(' ','_',$page['page'])));
			if (!defined('TAB_'.$p))
			{
				define('TAB_'.$p,$page['id']);
			}
		}
		if (!defined('CTAB_NAMES')) define('CTAB_NAMES','names');
		if (!defined('CTAB_CLASSIFICATION')) define('CTAB_CLASSIFICATION','classification');
		if (!defined('CTAB_TAXON_LIST')) define('CTAB_TAXON_LIST','list');
		if (!defined('CTAB_LITERATURE')) define('CTAB_LITERATURE','literature');
		if (!defined('CTAB_MEDIA')) define('CTAB_MEDIA','media');
		if (!defined('CTAB_DNA_BARCODES')) define('CTAB_DNA_BARCODES','dna barcodes');
		if (!defined('CTAB_NOMENCLATURE')) define('CTAB_NOMENCLATURE','Nomenclature');

        $activeLanguage =  ($this->rGetVal('activeLanguage')) ? $this->rGetVal('activeLanguage') : $this->getDefaultProjectLanguage();
        $this->setActiveLanguage($activeLanguage);
		$this->moduleSettings = new ModuleSettingsReaderController;

		 $this->setObsoleteTabs();
	}

    /**
     *  set Tabs no longer used
     */
    private function setObsoleteTabs()
	{
		foreach((array)json_decode($this->moduleSettings->getModuleSetting( ['setting'=>'obsolete_passport_tabs','module'=> 'species' ] ) ) as $key=>$val)
		{
			$tab='TAB_' . str_replace(' ','_',strtoupper($key));
			if ( defined( $tab ) ) $this->obsoleteTabs[constant($tab)]=['old'=>$key,'new'=>$val];
		}
	}

    /**
     *  set id of the Taxon
     */
	private function setTaxonId($id)
	{
		$this->TaxonId=$id;
	}

    /**
     *  get id of the Taxon
     */
	private function getTaxonId()
	{
		return isset($this->TaxonId) ? $this->TaxonId : false;
	}

    /**
     *  get the passport categories
     */
    private function getPassportCategories()
    {
		$categories=$this->getCategories();

		$content=$this->models->ContentTaxa->_get(["id"=>[
			"language_id"=>$this->getActiveLanguage(),
			"taxon_id"=>$this->getTaxonId(),
			"project_id"=>$this->getCurrentProjectId()
		],"fieldAsIndex"=>"page_id"]);

		foreach((array)$categories as $key=>$val)
		{
			$categories[$key]['content']=isset($content[$val['id']]) ? $content[$val['id']]['content'] : null;
			$categories[$key]['content_id']=isset($content[$val['id']]) ? $content[$val['id']]['id'] : null;
			$categories[$key]['publish']=isset($content[$val['id']]) ? $content[$val['id']]['publish'] : false;
			$categories[$key]['obsolete']=array_key_exists( $val['id'] , $this->obsoleteTabs );
		}

//		if ( defined('TAB_VERSPREIDING') ) $d=$this->getPassport(array('category'=>TAB_VERSPREIDING,'taxon'=>$this->getTaxonId()));

		foreach((array)$categories as $key=>$val)
		{

			if ($val['content_id'])
			{
				$rdf=$this->Rdf->getRdfValues($val['content_id']);

				foreach((array)$rdf as $rVal)
				{
					if ($rVal['predicate']=='hasPublisher')
					{
						$categories[$key]['rdf']['publisher'][]=$rVal['data'];
					}
					if ($rVal['predicate']=='hasReference')
					{
						$categories[$key]['rdf']['reference'][]=$rVal['data'];
					}
					if ($rVal['predicate']=='hasAuthor')
					{
						$categories[$key]['rdf']['author'][]=$rVal['data'];
					}
				}

				if (isset($categories[$key]['rdf']['publisher']))
				{
					$this->customSortArray($categories[$key]['rdf']['publisher'],array('key' => 'name'));
				}
				if (isset($categories[$key]['rdf']['reference']))
				{
					$this->customSortArray($categories[$key]['rdf']['reference'],array('key' => 'label'));
				}
				if (isset($categories[$key]['rdf']['author']))
				{
					$this->customSortArray($categories[$key]['rdf']['author'],array('key' => 'name'));
				}
			}
		}

		return $categories;

    }

    /**
     *  get the passport
     */
    private function getPassport($qry=null)
    {
		$taxon = isset($qry['taxon']) ? $qry['taxon'] : null;
		$category = isset($qry['category']) ? $qry['category'] : null;

		// the following values are all not used
		$isLower = isset($qry['isLower']) ? $qry['isLower'] : true;
		$limit=isset($qry['limit']) ? $qry['limit'] : null;
		$offset=isset($qry['offset']) ? $qry['offset'] : null;

		$content=null;
		$rdf=null;

        switch ($category)
		{
            case CTAB_CLASSIFICATION:
                //$content=$this->getTaxonClassification($taxon);
                $content=
					array(
						'classification'=>$this->getTaxonClassification($taxon),
						'taxonlist'=>$this->getTaxonNextLevel($taxon)
					);
                $page = 'CTAB_CLASSIFICATION';
                break;

            case CTAB_TAXON_LIST:
                $content=$this->getTaxonNextLevel($taxon);
                $page = 'CTAB_TAXON_LIST';
                break;

            case CTAB_LITERATURE:
                $content=$this->getTaxonLiterature($taxon);
                $page = 'CTAB_LITERATURE';
                break;

            case CTAB_DNA_BARCODES:
                $content=$this->getDNABarcodes($taxon);
                $page = 'CTAB_DNA_BARCODES';
                break;

            default:

                $ct = $this->models->ContentTaxa->_get( [ 'id' => [
						'taxon_id' => $taxon,
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getDefaultProjectLanguage(),
						'page_id' => $category,
					] ] );

				$content = isset($ct) ? $ct[0] : null;

                $page = @$this->models->PagesTaxa->_get( [ 'id' => [ 'project_id' => $this->getCurrentProjectId(), 'id' => $category ] ] )[0]['page'];
        }

		if (isset($content['id']))
		{
			$rdf=$this->Rdf->getRdfValues($content['id']);
		}

		$publish=$content['publish'];

		if (isset($content['content']))
		{
			$content=$content['content'];
		}

		return array('content'=>$content,'rdf'=>$rdf,'publish'=>$publish,'page'=>$page);
    }

    /**
     * Delete passport meta values
     *
     * @param $id
     */
    private function doDeletePassportMeta($id)
	{
		if (empty($id)) return;

		$this->Rdf->deleteRdfValue(array('subject_type'=>'passport','subject_id'=>$id,'predicate'=>'hasPublisher'));
		$this->Rdf->deleteRdfValue(array('subject_type'=>'passport','subject_id'=>$id,'predicate'=>'hasReference'));
		$this->Rdf->deleteRdfValue(array('subject_type'=>'passport','subject_id'=>$id,'predicate'=>'hasAuthor'));
	}

    /**
     * Save passport
     *
     * @param $qry
     * @return array $rec
     */
    private function savePassport($qry)
    {
        $taxon = isset($qry['taxon']) ? $qry['taxon'] : null;
        $page = isset($qry['page']) ? $qry['page'] : null;
        $content = isset($qry['content']) ? $qry['content'] : null;
        $publish = isset($qry['publish']) && $qry['publish'] == 1 ? '1' : '0';
        $langid = isset($qry['lang']) ? $qry['lang'] : $this->getDefaultProjectLanguage();

        $concept = $this->getConcept($taxon);
        $before = $this->getPassport(array('category' => $page, 'taxon' => $taxon));

        if (empty($content)) {
            $rec = $this->models->ContentTaxa->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $taxon,
                'language_id' => $langid,
                'page_id' => $page
            ));

            $this->logChange(array('before' => $before, 'note' => 'deleted passport tabpage  ' . $before['page'] . ' of ' . $concept['taxon']));
            return $rec;

        } else {

            $oldrec = $this->models->ContentTaxa->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $taxon,
                    'language_id' => $langid,
                    'page_id' => $page
                )
            ));

            $id = !empty($oldrec[0]['id']) ? $oldrec[0]['id'] : null;

            //
            // @todo: here is the content saved with only the default language
            //
            $rec = $this->models->ContentTaxa->save(array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $taxon,
                'language_id' => $langid,
                'page_id' => $page,
                'content' => $content,
                'publish' => $publish
            ));

            $after = $this->getPassport(array('category' => $page, 'taxon' => $taxon));

            $this->logChange(
                array(
                    'before' => $before,
                    'after' => $after,
                    'note' => ($id ? 'updated passport tabpage' : 'new passport tabpage') . ' ' . $after['page'] . ' of ' . $concept['taxon']
                )
            );

            return $rec;
        }

    }

    /**
     * Save passport meta information
     * @param $postMeta
     */
    private function savePassportMeta($postMeta)
	{
		$taxon = isset($postMeta['id']) ? $postMeta['id'] : null;
		$updatereach = isset($postMeta['update-reach']) ? $postMeta['update-reach'] : null;
		$actors = isset($postMeta['actor_id']) ? $postMeta['actor_id'] : null;
		$organisations = isset($postMeta['organisation_id']) ? $postMeta['organisation_id'] : null;
		$references = isset($postMeta['reference_id']) ? $postMeta['reference_id'] : null;

		if (empty($taxon)||empty($updatereach))
			return;

		/*
			<option value="all-text">alle huidige tabbladen met tekst</option>
			<option value="text-no-meta">huidige tabbladen met tekst zonder meta-gegevens</option>

			bestaande meta-gegevens van de geselecteerde tab(s) worden overschreven!
		*/

		$categories=$this->getPassportCategories();
		$shouldUpdate=array();

		foreach((array)$categories as $category)
		{
			if ($category['obsolete']==1 && strlen($category['content'])==0)
				continue;

			if ($updatereach=="all-text") {
				// alle huidige tabbladen met tekst
				if (strlen($category['content'])>0)
					$shouldUpdate[] = $category['content_id'];
			}
			else
			if ($updatereach=="text-no-meta") {
				// huidige tabbladen met tekst zonder meta-gegevens
				if (
					(strlen($category['content'])>0) &&
					(!isset($category['rdf']) || count((array)$category['rdf'])==0)
				)
				$shouldUpdate[] = $category['content_id'];
			}
			else
			if (is_numeric($updatereach) && $updatereach==$category['content_id']) {
				$shouldUpdate[] = $category['content_id'];
			}

		}

		foreach((array)$shouldUpdate as $category) {

			$this->doDeletePassportMeta($category);

			$rdf = array(
				'subject_type'=>'passport',
				'subject_id'=>$category,
			);

			foreach((array)$actors as $actor) {
				$rdf['predicate']='hasAuthor';
				$rdf['object_type']='actor';
				$rdf['object_id']=$actor;
				$this->Rdf->saveRdfValue($rdf);
			}

			foreach((array)$organisations as $organisation) {
				$rdf['predicate']='hasPublisher';
				$rdf['object_type']='actor';
				$rdf['object_id']=$organisation;
				$this->Rdf->saveRdfValue($rdf);
			}

			foreach((array)$references as $reference) {
				$rdf['predicate']='hasReference';
				$rdf['object_type']='reference';
				$rdf['object_id']=$reference;
				$this->Rdf->saveRdfValue($rdf);
			}

		}

	}

    /**
     * delete Passport Meta
     * @param $posted
     */
    private function deletePassportMeta($posted)
	{
		$tab=isset($posted['tab']) ? $posted['tab'] : null;

		if (empty($tab)) return;

		if ($tab=='*')
		{
			$categories=$this->getPassportCategories();
			foreach((array)$categories as $val)
			{
				$this->doDeletePassportMeta($val['content_id']);
			}
		}
		else
		{
			$this->doDeletePassportMeta($tab);
		}
	}

    /**
     * @return mixed
     */
    public function getActiveLanguage()
    {
        return $this->activeLanguage;
    }

    /**
     * @param mixed $activeLanguage
     */
    public function setActiveLanguage($activeLanguage)
    {
        $this->activeLanguage = $activeLanguage;
    }

}