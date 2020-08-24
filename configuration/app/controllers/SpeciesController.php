<?php /** @noinspection PhpMissingParentCallMagicInspection */

include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');
include_once ('MediaController.php');

class SpeciesController extends Controller
{
	private $_lookupListMaxResults=1000;
	private $_includeOverviewImageInMedia=true;
	private $_defaultSpeciesTab;
	private $_mc;
	private $taxon_id;

	protected $_model;

    public $usedModels = array(
        'commonnames',
        'content_taxa',
		'dna_barcodes',
        'labels_languages',
        'literature',
        'literature_taxa',
        'media_descriptions_taxon',
        'media_taxon',
        'media_meta',
        'pages_taxa',
        'pages_taxa_titles',
        'synonyms',
        'tab_order',
        'taxa_relations'
    );
    public $controllerPublicName = 'Species module';
    public $controllerBaseName = 'species';
    public $cssToLoad = array(
        'species.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'main.js',
            'lookup.js',
            'dialog/jquery.modaldialog.js'
        ),
        'IE' => array()
    );

	/* init */
    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
	}


    public function __destruct ()
    {
        parent::__destruct();
    }


    private function initialise ()
    {
        $this->setModel();
        $this->setMediaController();

        // creating constants for the tab id's (id for page 'Schade en nut' becomes TAB_SCHADE_EN_NUT)
		foreach((array)$this->models->PagesTaxa->_get(array('id' => array('project_id' => $this->getCurrentProjectId()))) as $page)
		{
			$p=trim(strtoupper(str_replace(' ','_',$page['page'])));
			if (!defined('TAB_'.$p))
			{
				define('TAB_'.$p,$page['id']);
			}
		}

		// fixed tabs
		if (!defined('CTAB_NAMES')) define('CTAB_NAMES','names');
		if (!defined('CTAB_CLASSIFICATION')) define('CTAB_CLASSIFICATION','classification');
		if (!defined('CTAB_TAXON_LIST')) define('CTAB_TAXON_LIST','list');
		if (!defined('CTAB_LITERATURE')) define('CTAB_LITERATURE','literature');
		if (!defined('CTAB_MEDIA')) define('CTAB_MEDIA','media');
		if (!defined('CTAB_DNA_BARCODES')) define('CTAB_DNA_BARCODES','dna barcodes');
		if (!defined('CTAB_NOMENCLATURE')) define('CTAB_NOMENCLATURE','Nomenclature');
		if (!defined('CTAB_DICH_KEY_LINKS')) define('CTAB_DICH_KEY_LINKS','key_links');

		$this->moduleSettings=new ModuleSettingsReaderController;

		$this->_suppressTab_NAMES=$this->moduleSettings->getModuleSetting( array( 'setting'=>'species_suppress_autotab_names','subst'=>0) )==1;
		$this->_suppressTab_CLASSIFICATION=$this->moduleSettings->getModuleSetting( array( 'setting'=>'species_suppress_autotab_classification','subst'=>0) )==1;
		$this->_suppressTab_LITERATURE=$this->moduleSettings->getModuleSetting( array( 'setting'=>'species_suppress_autotab_literature','subst'=>0) )==1;
		$this->_suppressTab_MEDIA=$this->moduleSettings->getModuleSetting( array( 'setting'=>'species_suppress_autotab_media','subst'=>0) )==1;
		$this->_suppressTab_DNA_BARCODES=$this->moduleSettings->getModuleSetting( array( 'setting'=>'species_suppress_autotab_dna_barcodes','subst'=>0) )==1;
        $this->_lookupListMaxResults=$this->moduleSettings->getModuleSetting( array( 'setting'=>'lookup_list_species_max_results','subst'=>$this->_lookupListMaxResults) );
        $this->_includeOverviewImageInMedia=$this->moduleSettings->getModuleSetting( array( 'setting'=>'include_overview_in_media','subst'=>true) );
		$this->_defaultSpeciesTab=$this->moduleSettings->getModuleSetting( array( 'setting'=>'species_default_tab','subst'=>CTAB_CLASSIFICATION) );
		$this->_inclHigherTaxaRankPrefix=$this->moduleSettings->getModuleSetting( array( 'setting'=>'higher_taxa_rank_prefix','subst'=>0) )==1;
        $this->_base_url_images_main=$this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_main','module'=>'species') );
        $this->_base_url_images_thumb=$this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_thumb','module'=>'species') );
        $this->_base_url_images_overview=$this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_overview','module'=>'species') );
        
		$this->smarty->assign('inclHigherTaxaRankPrefix',$this->_inclHigherTaxaRankPrefix);
    }

	public function setTaxonId( $id )
	{
		$this->taxon_id = (int)$id;
	}

	public function getTaxonId()
	{
		return $this->taxon_id;
	}

    /* Dynamically set proper model name (species/highertaxa) */
    private function setModel ()
    {
       $this->_model = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getControllerBaseName())))) . 'Model';
    }


	/* public set/get */

    public function setTaxonType ($type)
    {
		//public as it needs to be callable from the view
        //$_SESSION['app'][$this->spid()]['species']['type'] = ($type == 'higher') ? 'higher' : 'lower';

        $this->moduleSession->setModuleSetting(array(
            'setting' => 'type',
            'value' => ($type == 'higher') ? 'higher' : 'lower'
        ));
    }


	/* public actions */

    public function indexAction ()
    {

        $this->setPageName($this->translate('Species module index'));

        $this->setTaxonType('lower');

        $this->setControllerBaseName();

        $this->_indexAction();
    }


    public function higherSpeciesIndexAction ()
    {
        $this->setPageName($this->translate('Higher taxa index'));

        $this->setTaxonType('higher');

        $this->setControllerBaseName();

        $this->_indexAction();
    }


    public function taxonAction ()
    {
        if ($this->rHasId()) {

            // get taxon
            $taxon = $this->getTaxonById($this->rGetId());
			$taxon['label']=$this->formatTaxon($taxon);
		}
        if (!empty($taxon))
		{

            $this->setTaxonType($taxon['lower_taxon'] == 1 ? 'lower' : 'higher');

            $this->setControllerBaseName();

            if (isset($this->requestData['lan']))
                $this->setCurrentLanguageId($this->requestData['lan']);

            // get categories
            $categories = $this->getCategories(
				array(
					'taxon' => $taxon['id'],
					'allowUnpublished' => $this->isLoggedInAdmin()
				)
            );

            // determine the page_id the page will open in
			$requestedCat=$this->rHasVal('cat') ? $this->rGetVal('cat') : null;

            $activeCategory =
            	!empty($requestedCat) ?
	            	(isset($categories['emptinessList'][$requestedCat]) && $categories['emptinessList'][$requestedCat]==0 ?
	            		$requestedCat :
	            		$categories['defaultCategory']
	            	) :
           		$categories['defaultCategory'];


			$activeCategory = !empty($requestedCat) ? $requestedCat : $categories['defaultCategory'];


            // setting the css classnames
			// we need to move this to a template
            foreach ((array) $categories['categories'] as $key => $val)
			{
                $c = array(
                    'category'
                );
                if ($val['id'] == $activeCategory) {
                    $c[] = 'category-active';

					$activeCategory_label=isset($val['page']) ? $val['page'] : $val['title'];

                }
                if ($key == 0) {
                    $c[] = 'category-first';
                }
                else if ($key == count($categories['categories']) - 1) {
                    $c[] = 'category-last';
                }
                if ($val['is_empty'] == 1 && $val['id'] != $activeCategory) {
                    $c[] = 'category-no-content';
                }
                $categories['categories'][$key]['className'] = implode(' ', $c);
            }

            if ($taxon['lower_taxon']==1)
			{
                $this->setPageName(sprintf($this->translate('Species module: "%s" (%s)'), $taxon['label'], $this->getCategoryName($activeCategory)));
                $this->setLastViewedTaxonIdForTheBenefitOfTheMapkey($taxon['id']);
            }
            else
			{
                $this->setPageName(sprintf($this->translate('Higher taxa: "%s" (%s)'), $taxon['label'], $this->getCategoryName($activeCategory)));
            }

            if (isset($taxon))
			{
                //$this->smarty->assign('overviewImage', $this->getTaxonOverviewImage($taxon['id']));
                //$this->smarty->assign('overviewSound', $this->getTaxonOverviewSound($taxon['id']));
                //$this->smarty->assign('overviewImage', $this->getTaxonOverviewImage());

			    $this->smarty->assign('overview', $this->getTaxonOverviewImage());

                $this->smarty->assign('taxon',$taxon);

				$d=$this->getTaxonContent(
					array(
						'taxon' => $taxon['id'],
						'category' => $activeCategory,
						'allowUnpublished' => $this->isLoggedInAdmin(),
						'isLower' =>  $taxon['lower_taxon']
					)
				);

				$content=$d['content'];

				if ($activeCategory!=CTAB_MEDIA) {
					$content = $this->matchHotwords($content);
				}

                $this->smarty->assign('content', $content);
                $this->smarty->assign('contentCount', $this->getContentCount($taxon['id']));
                $this->smarty->assign('adjacentItems', $this->getAdjacentTaxa($taxon['id']));

            }

            $this->smarty->assign('categories', $categories['categories']);
            $this->smarty->assign('activeCategory', $activeCategory);
            $this->smarty->assign('categorySysList', $categories['categorySysList']);
            $this->smarty->assign('headerTitles', array('title' => $taxon['label'].(isset($taxon['commonname']) ? ' ('.$taxon['commonname'].')' : '')));

            //$this->setLastViewedTaxonIdForTheBenefitOfTheMapkey($taxon['id']);
        }
        else {

            $this->addError($this->translate('No or unknown taxon ID specified.'));

            $this->setLastViewedTaxonIdForTheBenefitOfTheMapkey(null);
        }

        $this->printPage('taxon');
    }


    public function ajaxInterfaceAction ()
    {
		$return='error';

        if ($this->rHasVal('action', 'get_lookup_list') && !empty($this->rGetVal('search'))) {

            $return=$this->getLookupList($this->requestData);

        }
        else
		if ($this->rHasVal('action', 'get_media_info') && !empty($this->requestData['id'])) {

			$return=json_encode($this->getTaxonMedia(array('id'=>intval($this->requestData['id']))));

        }
		else
		if ($this->rHasVal('action', 'get_parentage') && $this->rHasId())
		{
	        $return=json_encode($this->getTaxonParentage(intval($this->rGetVal('id'))));
        }

        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage();
    }


	//sec overview of taxon without header, menu's etc (created for dierenzoeker, fetched through ajax)
    public function taxonOverviewAction ()
    {
		$related=$this->getRelatedEntities(array('tId' => $this->rGetVal('id')));
		foreach((array)$related as $key => $val)
		{
			$d = $this->getTaxonById($val['relation_id']);
            $related[$key]['label'] = $d['commonname'];

            $media=$this->getTaxonMedia( [ 'taxon'=>$val['relation_id'],'forceOld'=>true ] );

            foreach((array)$media as $file)
            {
                if ($file['overview_image']==1)
                {
                    $related[$key]['url_image'] = $file['file_name'];
                    $related[$key]['url_thumbnail'] = $file['thumb_name'];
                }
            }
		}

    	$children=$this->models->Taxa->_get(array('id'=>array('project_id' => $this->getCurrentProjectId(),'parent_id' => $this->rGetVal('id'))));

        $children=$this->models->SpeciesModel->getTaxonChildrenNsr( [
            'projectId' => $this->getCurrentProjectId(),
            'taxonId' => $this->rGetVal('id'),
            'languageId' => $this->getCurrentLanguageId(),
            'predicateValidNameId' => $this->getNameTypeId(PREDICATE_VALID_NAME),
            'predicatePreferredNameId' => $this->getNameTypeId(PREDICATE_PREFERRED_NAME)
        ]);

		foreach((array)$children as $key => $val)
		{

            $media=(array)$this->getTaxonMedia( [ 'taxon'=>$val['id'],'forceOld'=>true ] );

            foreach($media as $file)
            {
                if ($file['overview_image']==1)
                {
                    $children[$key]['url_image'] = $file['file_name'];
                    $children[$key]['url_thumbnail'] = $file['thumb_name'];
                }
            }

		}

		if ($children) usort($children,function($a,$b) {return ($a['commonname']>$b['commonname']?1:-1);});

		$taxon = $this->getTaxonById($this->rGetVal('id'));
		$taxon['label']=$this->formatTaxon($taxon);
		//$taxon['taxon']=trim(str_replace('%VAR%','',$taxon['taxon']));
        $taxon['taxon']=preg_replace('/\%VAR(\d)*\%/','',trim($taxon['taxon']));
		$parent = $this->getTaxonById($taxon['parent_id']);
		$parent['label']=$this->formatTaxon($parent);
		$categories = $this->getCategories(array('taxon' => $this->requestData['id']));
		$content = $this->models->ContentTaxa->_get(array(
			'id' =>  array(
				'taxon_id' => $this->rGetVal('id'),
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->getCurrentLanguageId()
			),
			'columns'=>'page_id,content',
			'fieldAsIndex'=>'page_id'
			)
		);
		
		$media=(array)$this->getTaxonMedia( [ 'taxon'=>$this->rGetVal('id'),'forceOld'=>true ] );

		$contentparent = $this->models->ContentTaxa->_get(array(
			'id' =>  array(
				'taxon_id' => $parent['id'],
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->getCurrentLanguageId()
			),
			'columns'=>'count(*) as total'
			)
		);

		$parent['hasContent']=$contentparent[0]['total']>0;

		$this->smarty->assign('taxon',$taxon);
		$this->smarty->assign('parent',$parent);
		$this->smarty->assign('categories', $categories);
		$this->smarty->assign('content',$content);
		$this->smarty->assign('children', $children);
		$this->smarty->assign('related', $related);
		$this->smarty->assign('media', $media);
		$this->smarty->assign('back',$this->rGetVal('back'));
        $this->smarty->assign('base_url_images_thumb',$this->_base_url_images_thumb);
        $this->smarty->assign('base_url_images_main',$this->_base_url_images_main);
        $this->smarty->assign('base_url_images_overview',$this->_base_url_images_overview);
		$this->printPage();
    }







	/* private set/get */

    private function getTaxonType()
    {
        return !is_null($this->moduleSession->getModuleSetting('type')) ?
            $this->moduleSession->getModuleSetting('type') : 'lower';
    }


    private function setControllerBaseName()
    {
        if ($this->getTaxonType() == 'higher')
            $this->controllerBaseName = 'highertaxa';
        else
            $this->controllerBaseName = 'species';
    }

    private function setLastViewedTaxonIdForTheBenefitOfTheMapkey($id)
    {
        if (!is_null($id)) {

            $this->moduleSession->setModuleSetting(array(
                'setting' => 'lastTaxon',
                'value' => $id
            ));

            $this->moduleSession->setModuleSetting(array(
                'setting' => 'state',
                'module' => 'mapkey',
                'projectId' => $this->spid()
            ));

            //unset($_SESSION['app']['user']['mapkey']['state']);
        }
        else {

            $this->moduleSession->setModuleSetting(array(
                'setting' => 'lastTaxon'
            ));

            //unset($_SESSION['app'][$this->spid()]['species']['lastTaxon']);
        }
    }


    private function getlastVisitedCategory($taxon,$category)
    {

		if (is_null($this->moduleSession->getModuleSetting('last_visited'))) {
			return false;
		}

		$lastVisited = $this->moduleSession->getModuleSetting('last_visited');

        if (isset($lastVisited[$taxon][$category])) {
            return $lastVisited[$taxon][$category];
        }

        $storedTaxon = key($lastVisited);
        if ($storedTaxon != $taxon) {

            unset($_SESSION['app'][$this->spid()]['species']['last_visited'][$storedTaxon]);
        }

        return false;
    }


    private function setlastVisitedCategory($taxon,$category,$d)
    {
        $this->moduleSession->setModuleSetting(array(
            'setting' => 'last_visited',
            'value' => array(
                $taxon => array(
                    $category => $d
                )
            )
        ));

        //$_SESSION['app'][$this->spid()]['species']['last_visited'][$taxon][$category] = $d;
    }


    public function getFirstTaxonId()
    {
        return $this->models->{$this->_model}->getFirstTaxonId(array(
            'projectId' => $this->getCurrentProjectId(),
            'taxonType' => $this->getTaxonType()
        ));
    }


	private function getAdjacentTaxa($id)
    {

        $browseOrder = $this->moduleSession->getModuleSetting('browse_order');
/*
        if (!isset($_SESSION['app'][$this->spid()]['species']['browse_order'][$this->getTaxonType()])) {

			$_SESSION['app'][$this->spid()]['species']['browse_order'][$this->getTaxonType()] =
				$this->models->{$this->_model}->getBrowseOrder(array(
                    'projectId' => $this->getCurrentProjectId(),
                    'taxonType' => $this->getTaxonType()
				));
		}
*/
        if (!isset($browseOrder[$this->getTaxonType()])) {

            $browseOrder[$this->getTaxonType()] = $this->models->{$this->_model}->getBrowseOrder(array(
                'projectId' => $this->getCurrentProjectId(),
                'taxonType' => $this->getTaxonType()
		    ));

            $this->moduleSession->setModuleSetting(array(
                'setting' => 'browse_order',
                'value' => array(
                    $this->getTaxonType() => $browseOrder[$this->getTaxonType()]
                )
            ));

		}
		$taxa = $browseOrder[$this->getTaxonType()];

		$prev=$next=false;

        $keys = array_keys($taxa);
        foreach ($keys as $index => $key)
        {
            $val = $taxa[$key];

			if ($val['id']==$id) {

                $next = array_key_exists($index+1, $keys) ? $taxa[$keys[$index+1]] : null;

				return array(
					'prev' => $prev!==false ? array( 'id' => $prev['id'], 'label' => $prev['taxon'] ) : null,
					'next' => $next!==false ? array( 'id' => $next['id'], 'label' => $next['taxon'] ) : null
				);
			}

			$prev = $val;

		}

        return null;
    }


    public function getTaxonNextLevel($id)
    {
		$t = $this->models->{$this->_model}->getTaxonChildrenNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'predicateValidNameId' => $this->getNameTypeId(PREDICATE_VALID_NAME),
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id
		));

        return $t;
    }


    private function getCategories($p=null)
    {
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$allowUnpublished = isset($p['allowUnpublished']) ? $p['allowUnpublished'] : false;
		$showAlways = isset($p['showAlways']) ? $p['showAlways'] : null;

		$stdCats=array();

		// get the defined categories (just the page definitions, no content yet)
		$tp = $this->models->PagesTaxa->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'fieldAsIndex' => 'id'
		));

		foreach ((array) $tp as $key => $val)
		{
		    if (is_null($this->moduleSession->getModuleSetting('defaultCategory'))) {
                $this->moduleSession->setModuleSetting(array(
                    'setting' => 'defaultCategory',
                    'value' => $val['id']
                ));
			}

			$tp[$key]['title'] = $this->getCategoryName($val['id']);
			$tp[$key]['tabname'] = 'TAB_'.str_replace(' ','_',strtoupper($val['page']));

			if ($val['def_page'] == 1) {
                $this->moduleSession->setModuleSetting(array(
                    'setting' => 'defaultCategory',
                    'value' => $val['id']
                ));
			}
		}


        if ($taxon)
		{
            $defCat = $this->_defaultSpeciesTab;

			if ($this->_suppressTab_NAMES==false)
			{

				$n = $this->getTaxonNames($taxon);

				$stdCats[] = array(
					'id' => CTAB_NAMES,
					'title' => $this->translate('Names'),
					'is_empty' => (count((array) $n['synonyms']) == 0 && count((array) $n['common']) == 0 ? 1 : 0),
					'tabname' => 'CTAB_NAMES'
				);

			}

            $d = array();

            foreach ((array) $tp as $key => $val)
			{
                $val['is_empty'] = 1;

                $ct = $this->models->ContentTaxa->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->getCurrentLanguageId(),
                        'taxon_id' => $taxon,
                        'page_id' => $val['id']
                    ),
                    'columns' => 'page_id,publish,content'
                ));

                if (($ct[0]['publish'] == '1' || $allowUnpublished) && strlen($ct[0]['content']) > 0) {
                    $val['is_empty'] = 0;
                }

                $d[$key] = $val;

                if ($ct[0]['page_id'] == $this->moduleSession->getModuleSetting('defaultCategory')) {
                    $defCat = $this->moduleSession->getModuleSetting('defaultCategory');
                }
			}

			foreach ((array) $d as $key => $val) {
				$categoryList[$key] = $val['title'];
				$categorySysList[$key] = $val['page'];
			}

			if ($this->_suppressTab_CLASSIFICATION==false)
			{

				$stdCats[] = array(
					'id' => CTAB_CLASSIFICATION,
					'title' => $this->translate('Classification'),
					'is_empty' => 0,
					'tabname' => 'CTAB_CLASSIFICATION'
				);

			}

			if ($this->doesCurrentProjectHaveModule(MODCODE_LITERATURE) && $this->_suppressTab_LITERATURE==false)
			{

				$l = $this->getTaxonLiterature($taxon);

				$stdCats[] = array(
					'id' => CTAB_LITERATURE,
					'title' => $this->translate('Literature'),
					'is_empty' => (count((array) $l) > 0 ? 0 : 1),
					'tabname' => 'CTAB_LITERATURE'
				);
			}

			if ($this->_suppressTab_MEDIA==false)
			{

				$m = $this->getTaxonMediaCount($taxon);

				$stdCats[] = array(
					'id' => CTAB_MEDIA,
					'title' => $this->translate('Media'),
					'is_empty' => $m>0?0:1,
					'tabname' => 'CTAB_MEDIA'
				);

			}

			if ($this->models->DnaBarcodes->getTableExists() && $this->_suppressTab_DNA_BARCODES==false)
			{
				$dna = $this->models->DnaBarcodes->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $taxon
					),
					'columns' => 'count(*) as tot'
				));

				$stdCats[] = array(
					'id' => CTAB_DNA_BARCODES,
					'title' => $this->translate('DNA barcodes'),
					'is_empty' => $dna[0]['tot']==0 ? 1 : 0,
					'tabname' => 'CTAB_DNA_BARCODES'
				);
			}

            $tp=array_merge($d, $stdCats);

            foreach ((array) $tp as $val)
                $emptinessList[$val['id']] = $val['is_empty'];

        }


		if ($this->models->TabOrder->getTableExists())
		{

			$tab=$this->models->TabOrder->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns'=>'tabname,show_order,start_order',
				'fieldAsIndex'=>'tabname',
				'order'=>'start_order'
			));

			$start=null;
			if (!empty($tab)) {
    			foreach((array)$tp as $key=>$val) {
    				$tp[$key]['show_order']=isset($tab[$val['tabname']]) ? $tab[$val['tabname']]['show_order'] : 99;
    				if (is_null($start) && !empty($tab[$val['tabname']]['start_order']) && $emptinessList[$val['id']]==0) {
    					$start=$val['id'];
    				}
    			}
    			$defCat=!is_null($start) ? $start : $defCat	;

    			$this->customSortArray($tp,array('key' => array('show_order')));
			}
		}

		return array(
            'categories' => $tp,
			'defaultCategory' => isset($defCat) ? $defCat : $this->moduleSession->getModuleSetting('defaultCategory'),
			'categoryList' => isset($categoryList) ? $categoryList : null,
			'categorySysList' => isset($categorySysList) ? $categorySysList : null,
			'emptinessList' => isset($emptinessList) ? $emptinessList : null
		);

    }


    private function getCategoryName($id)
    {
		if (is_numeric($id)) {

			$tpt = $this->models->PagesTaxaTitles->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'page_id' => $id
				),
				'columns' => 'title'
			));

			if (empty($tpt[0]['title'])) {

				$tpt = $this->models->PagesTaxa->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $id
					),
					'columns' => 'page as title'
				));
			}

			return $tpt[0]['title'];
		}
		else {

			return ucwords($id);
		}

    }


    private function getTaxonContent($p=null)
    {

		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$category = isset($p['category']) ? $p['category'] : null;
		$allowUnpublished = isset($p['allowUnpublished']) ? $p['allowUnpublished'] : false;
		$incOverviewImage = isset($p['incOverviewImage']) ? $p['incOverviewImage'] : $this->_includeOverviewImageInMedia;
		$isLower = isset($p['isLower']) ? $p['isLower'] : true;

		$content=null;

        switch ($category) {

            case CTAB_MEDIA:
                $content=$this->getTaxonMedia(array('taxon'=>$taxon,'incOverviewImage'=>$incOverviewImage));
                break;

            case CTAB_CLASSIFICATION:
                //$content=$this->getTaxonClassification($taxon);
                $content=
					array(
						'classification'=>$this->getTaxonClassification($taxon),
						'taxonlist'=>$this->getTaxonNextLevel($taxon)
					);
                break;

            case CTAB_TAXON_LIST:
                $content=$this->getTaxonNextLevel($taxon);
                break;

            case CTAB_LITERATURE:
                $content=$this->getTaxonLiterature($taxon);
                break;

            case CTAB_NAMES:
                $content=$this->getTaxonNames($taxon);
                break;

            default:
                $d = array(
                    'taxon_id' => $taxon,
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->getCurrentLanguageId(),
                    'page_id' => $category
                );

                if (!$allowUnpublished)
                    $d['publish'] = '1';

                $ct = $this->models->ContentTaxa->_get(array(
                    'id' => $d,
                ));

				$content = isset($ct) ? $ct[0] : null;

                $content=$content['content'];
        }

		return array('content'=>$content);
    }


	protected function getTaxonMedia ($p)
	{
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$id = isset($p['id']) ? $p['id'] : null;
		$forceOld = isset($p['forceOld']) ? $p['forceOld'] : false;

        /*
            forceOld should be replaced with a "use/don't use RS"-setting
        */

		$inclOverviewImage = isset($p['inclOverviewImage']) ? $p['inclOverviewImage'] : false;

		if (!$forceOld)
		{
			if ($mt = $this->getlastVisitedCategory($taxon, CTAB_MEDIA))
			{
				return $mt;
			}

			$this->_mc->setItemId(isset($taxon) ? $taxon : $id);
			$mt = $this->_mc->getItemMediaFiles();
			$this->_mc->reformatOutput($mt, $inclOverviewImage);

			$this->setlastVisitedCategory($taxon, CTAB_MEDIA, $mt);
		}
		else
		{
			
			$mt = $this->models->MediaTaxon->_get( [ 'id' => [ 'project_id' => $this->getCurrentProjectId(), 'taxon_id' => $taxon ] ] );

            foreach ((array)$mt as $key => $value)
            {
                $mt[$key]['meta_data']=$this->models->MediaMeta->_get( [
                    "id"=>[
                            "project_id"=>$this->getCurrentProjectId(),
                            "language_id"=>$this->getCurrentLanguageId(),
                            'media_id'=>$value["id"]
                    ],
                    "columns"=>"sys_label, meta_data, meta_date, meta_number",
                    "fieldAsIndex"=>"sys_label"
                ] );
            }
		}



		return $mt;
	}

    private function getTaxonLiterature($tId)
    {
        if ($refs = $this->getlastVisitedCategory($tId, CTAB_LITERATURE))
            return $refs;

        $lt = $this->models->LiteratureTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $tId
            )
        ));

        $refs = array();

        foreach ((array) $lt as $key => $val) {

            $l = $this->models->Literature->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'id' => $val['literature_id']
                ),
                'columns' => '*, year(`year`) as `year`,
									concat(
										author_first,
										(
											if(multiple_authors=1,
												\' et al.\',
												if(author_second!=\'\',concat(\' & \',author_second),\'\')
											)
										)
									) as author_full,
									concat(
										if(isnull(`year`)!=1,`year`,\'\'),
										if(isnull(suffix)!=1,suffix,\'\'),
										if(isnull(year_2)!=1,
											concat(
												if(year_separator!=\'-\',
													concat(
														\' \',
														year_separator,
														\' \'
													),
													year_separator
												),
												year_2,
												if(isnull(suffix_2)!=1,
													suffix_2,
													\'\')
												)
												,\'\'
											)
									) as year_full'
            ));

            $refs[] = $l[0];
        }

        $sortBy = array(
            'key' => 'author_first',
            'dir' => 'asc',
            'case' => 'i'
        );

        $this->customSortArray($refs, $sortBy);

        $this->setlastVisitedCategory($tId, CTAB_LITERATURE, $refs);

        return $refs;
    }


    private function getTaxonNames($tId)
    {
        if ($names = $this->getlastVisitedCategory($tId, CTAB_NAMES))
            return $names;

        $names = array(
            'synonyms' => $this->getTaxonSynonyms($tId),
            'common' => $this->getTaxonCommonNames($tId)
        );

        $this->setlastVisitedCategory($tId, CTAB_NAMES, $names);

        return $names;
    }


    private function getTaxonSynonyms($tId)
    {
        $s = $this->models->Synonyms->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $tId
            )
        ));

        if (isset($s)) {

            foreach ((array) $s as $key => $val) {

                if ($val['lit_ref_id'])
                    $s[$key]['reference'] = $this->getReference($val['lit_ref_id']);
            }
        }

        return $s;
    }


    private function getTaxonCommonNames($tId, $languageId = null)
    {
        $d = array(
            'project_id' => $this->getCurrentProjectId(),
            'taxon_id' => $tId
        );

        if (isset($languageId))
            $d['language_id'] = $languageId;

        $c = $this->models->Commonnames->_get(array(
            'id' => $d,
            'columns' => 'language_id,commonname,transliteration',
            'order' => 'show_order,commonname'
        ));

        if (isset($c)) {

            foreach ((array) $c as $key => $val) {

               $ll = $this->models->LabelsLanguages->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'label_language_id' => $val['language_id'],
                        'language_id' => $this->getCurrentLanguageId()
                    ),
                    'columns' => 'label'
                ));

                if ($ll) {

                    $c[$key]['language_name'] = $ll[0]['label'];
                }
                else {

                    $l = $this->models->Languages->_get(
                    array(
                        'id' => array(
                            'id' => $val['language_id']
                        ),
                        'columns' => 'language'
                    ));

                    $c[$key]['language_name'] = $l[0]['language'];
                }
            }
        }

        return $c;
    }


    private function getReference($id)
    {
        $l = $this->models->Literature->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $id
            ),
            'columns' => '*, year(`year`) as `year`,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full'
        ));

        return $l[0];
    }


    private function getContentCount($id)
    {
        return array(
            CTAB_NAMES => $this->getTaxonSynonymCount($id) + $this->getTaxonCommonnameCount($id),
            CTAB_MEDIA => $this->getTaxonMediaCount($id),
            CTAB_LITERATURE => $this->getTaxonLiteratureCount($id)
        );
    }


    private function getTaxonSynonymCount($id)
    {
        $s = $this->models->Synonyms->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $id
            ),
            'columns' => 'count(*) as total'
        ));

        return isset($s) ? $s[0]['total'] : 0;
    }


    private function getTaxonCommonnameCount($id)
    {
        $c = $this->models->Commonnames->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $id
            ),
            'columns' => 'count(*) as total'
        ));

        return isset($c) ? $c[0]['total'] : 0;
    }

    private function getTaxonMediaCount($id)
    {
        return $this->_mc->getItemMediaFileCount();
    }


    private function getTaxonLiteratureCount($id)
    {
        $lt = $this->models->LiteratureTaxa->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $id
            ),
            'columns' => 'count(*) as total'
        ));

        return isset($lt) ? $lt[0]['total'] : 0;
    }

    /*
	private function getTaxonOverviewImage($id)
    {
        $mt = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'taxon_id' => $id,
                'overview_image' => 1,
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        if ($mt) {

            $mdt = $this->models->MediaDescriptionsTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->getCurrentLanguageId(),
                    'media_id' => $mt[0]['id']
                ),
                'columns' => 'description'
            ));

            return array('image' => $mt[0]['file_name'],'label' => $mdt[0]['description']);
		} else
            return null;
    }
    */

	protected function getTaxonOverviewImage()
    {
        return $this->_mc->getOverView();
    }


	private function getTaxonOverviewSound($id)
    {
        $mt = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'taxon_id' => $id,
                'overview_image' => 1,
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        if ($mt) {

            $mdt = $this->models->MediaDescriptionsTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->getCurrentLanguageId(),
                    'media_id' => $mt[0]['id']
                ),
                'columns' => 'description'
            ));

            return array('image' => $mt[0]['file_name'],'label' => $mdt[0]['description']);
		} else
            return null;
    }

    public function getLookupList($p)
    {

        $search = isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start'] == '1' : false;
        $getAll = isset($p['get_all']) ? $p['get_all'] == '1' : false;
		$listMax = isset($p['list_max']) ? ($p['list_max']=='0' ? null : (int)$p['list_max']) : $this->_lookupListMaxResults;

        if (empty($search) && !$getAll)
            return;

		$lower=true;

		if (isset($p['vars']))
		{
			foreach((array)$p['vars'] as $key=>$val)
			{
				if ($val['name']=='lower')
				{
					$lower=($val['value']==1);
				}
			}
		}

        list($taxa, $total) = $this->models->{$this->_model}->getTaxa(array(
            'projectId' => $this->getCurrentProjectId(),
            'taxonType' => $lower ? 'lower' : 'higher', //$this->getTaxonType(),
            'getAll' => $getAll,
            'listMax' => $listMax,
            'regExp' => ($matchStartOnly?'^':'').preg_quote($search, '/'),
            'predicateValidNameId' => $this->getNameTypeId(PREDICATE_VALID_NAME)
        ));

		$ranks=$this->getProjectRanks();

        foreach ((array) $taxa as $key => $val)
		{
			$taxa[$key]['label'] = $this->formatTaxon(array('taxon'=>$val,'rankpos'=>'post','ranks'=>$ranks));
			if (!empty($val['authorship'])) {
			    $taxa[$key]['label'] .= ' ' . $val['authorship'];
			}
			unset($taxa[$key]['taxon']);
		}

		/*
        $this->customSortArray($taxa, array(
            'key' => 'label',
            'dir' => 'asc',
            'case' => 'i'
		));
		*/
		
		//'module'=>($this->getTaxonType() == 'higher' ? 'highertaxa' : 'species'),
		//'url'=>'../' . ($this->getTaxonType() == 'higher' ? 'highertaxa' : 'species') . '/taxon.php?id=%s',

		return $this->makeLookupList(
			array(
				'data'=>$taxa,
				'module'=>($lower ? 'species' : 'highertaxa'),
				'url'=>'../' . ($lower ? 'species' : 'highertaxa') . '/taxon.php?id=%s',
				'total'=>$total
			)
		);

	}

    private function getRelatedEntities($p)
    {
        $tId = isset($p['tId']) ? $p['tId'] : null;
        $vId = isset($p['vId']) ? $p['vId'] : null;
        $includeSelf = isset($p['includeSelf']) ? $p['includeSelf'] : false;

        $rel = null;

        if ($tId) {

            $rel = $this->models->TaxaRelations->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $tId
                )
            ));

            if ($includeSelf && isset($tId))
                array_unshift($rel, array(
                    'id' => $tId,
                    'relation_id' => $tId,
                    'ref_type' => 'taxon'
                ));

            foreach ((array) $rel as $key => $val) {

                if ($val['ref_type'] == 'taxon') {
                    $rel[$key]['label'] = $this->formatTaxon($this->getTaxonById($val['relation_id']));
                }
                else {
                    $d = $this->getVariation($val['relation_id']);
                    $rel[$key]['label'] = $d['label'];
                    $rel[$key]['taxon_id'] = $d['taxon_id'];
                }
            }
        }
        else if ($vId) {

            $rel = $this->models->VariationRelations->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'variation_id' => $vId
                )
            ));

            if ($includeSelf && isset($vId))
                array_unshift($rel, array(
                    'id' => $vId,
                    'relation_id' => $vId,
                    'ref_type' => 'variation'
                ));


            foreach ((array) $rel as $key => $val) {

                if ($val['ref_type'] == 'taxon') {
                    $rel[$key]['label'] = $this->formatTaxon($d = $this->getTaxonById($val['relation_id']));
                }
                else {
                    $d = $this->getVariation($val['relation_id']);
                    $rel[$key]['label'] = $d['label'];
                }
            }
        }

        return $rel;
    }

	/* private other */

    private function _indexAction ()
    {

        if (!$this->rHasId()) {

            $id = $this->getFirstTaxonId();

            //die('id ' . $id);

        }
        else {

            $id = $this->requestData['id'];
        }

        $this->setStoreHistory(false);

        $this->redirect('taxon.php?id=' . $id);
    }


	public function findByNameAction()
	{

		//RewriteEngine on
		//RewriteRule ^p/([\d]+)/([^/\.]+)/?$ /linnaeus_ng/app/views/species/find_by_name.php?epi=$1&name=$2

		$base = $this->baseUrl.$this->appName.'/views/'.$this->controllerBaseName.'/';

		$name = trim($this->requestData['name']);

		if (empty($name))
			$this->redirect($base.'index.php');

		// try literal
		$t = $this->models->Taxa->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon' => $name
			),
			'columns' => 'id'
		));

		if ($t)
			$this->redirect($base.'taxon.php?epi='.$this->requestData['epi'].'&id='.$t[0]['id']);

	}

	private function getTaxonParentage($id)
	{
		if (is_null($id))
			return;

		return $this->models->{$this->_model}->getTaxonParentage(array(
            'projectId' => $this->getCurrentProjectId(),
		    'taxonId' => $id
		));

	}

	private function getCommonname($tId)
	{
		$c = $this->models->Commonnames->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $tId,
				'language_id' => $this->getCurrentLanguageId()
			)
		));

		return $c[0]['commonname'];

	}

	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId('species'));
        $this->_mc->setItemId($this->rGetId());
        $this->_mc->setLanguageId($this->getCurrentLanguageId());
	}



}
