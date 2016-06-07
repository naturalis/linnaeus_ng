<?php
/*

what about
			if (defined('TAB_VERSPREIDING'))
			{

			// TAB_BEDREIGING_EN_BESCHERMING check at EZ
			// this should be changed to a generalized method, using 'redirect_to'
			// REFAC2015

USE INHERITED LIT yes/no

TAB_VERSPREIDING: auto presence data, data TAB_VERSPREIDING (TAB_VOORKOMEN omgebracht bij import)

*/

include_once ('SpeciesController.php');
include_once ('RdfController.php');
include_once ('NSRFunctionsController.php');
include_once ('ModuleSettingsReaderController.php');

class SpeciesControllerNSR extends SpeciesController
{
	private $_resPicsPerPage=12;
	private $_nameTypeIds;
	private $show_nsr_specific_stuff=false;

	public $usedModelsExtended = array(
		'actors',
		'name_types',
		'names_additions',
		'nsr_ids',
		'names',
		'taxon_quick_parentage'
	);

	private $cTabs=[
		'CTAB_NAMES'=>['id'=>-1,'title'=>'Naamgeving'],
		'CTAB_MEDIA'=>['id'=>-2,'title'=>'Media'],	
		'CTAB_CLASSIFICATION'=>['id'=>-3,'title'=>'Classification'],
		'CTAB_TAXON_LIST'=>['id'=>-4,'title'=>'Child taxa list'],	// $this->getTaxonNextLevel($taxon); (children?)
		'CTAB_LITERATURE'=>['id'=>-5,'title'=>'Literature'],
		'CTAB_DNA_BARCODES'=>['id'=>-6,'title'=>'DNA barcodes'],
		'CTAB_DICH_KEY_LINKS'=>['id'=>-7,'title'=>'Key links'],
//		'CTAB_NOMENCLATURE'=>['id'=>-8,'title'=>'Nomenclature'],
	];

    public function __construct()
    {
        // Add specific models for this extended class to $usedModels
        $this->extendUsedModels();

        parent::__construct();
		$this->initialise();
	}

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->NSRFunctions=new NSRFunctionsController;
		$this->moduleSettings=new ModuleSettingsReaderController;

		$this->_taxon_base_url_images_main = $this->moduleSettings->getSetting( "taxon_base_url_images_main", "http://images.naturalis.nl/original/" );
		$this->_taxon_base_url_images_thumb = $this->moduleSettings->getSetting( "taxon_base_url_images_thumb", "http://images.naturalis.nl/160x100/" );
		$this->_taxon_base_url_images_overview = $this->moduleSettings->getSetting( "taxon_base_url_images_overview", "http://images.naturalis.nl/510x272/" );
		$this->_taxon_fetch_ez_data = $this->moduleSettings->getSetting( "taxon_fetch_ez_data", false );

		$this->smarty->assign( 'taxon_base_url_images_main',$this->_taxon_base_url_images_main );
		$this->smarty->assign( 'taxon_base_url_images_thumb',$this->_taxon_base_url_images_thumb );
		$this->smarty->assign( 'taxon_base_url_images_overview',$this->_taxon_base_url_images_overview );

		// not actually implemented to do anything yet
		$this->smarty->assign( 'tree_show_upper_taxon', $this->moduleSettings->getGeneralSetting( "tree_show_upper_taxon", 0 ) );

		$this->show_nsr_specific_stuff=$this->moduleSettings->getGeneralSetting( 'show_nsr_specific_stuff' , 0)==1;

		$this->Rdf = new RdfController;
		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
    }

    public function indexAction()
    {
        if ( $this->rHasVal('id') )
		{
			$id = $this->rGetId();
        }
        else
		{
			$id = $this->getFirstTaxonIdNsr();
		}

        $this->setStoreHistory(false);

		if (isset($id))
		{
			$this->redirect('nsr_taxon.php?id=' . $id);
		}
		else
		{
			$this->smarty->assign('message','Geen taxon ID gevonden.');
			$this->printPage('../shared/generic-error');
		}
    }
	
    public function taxonAction()
    {
		$taxon = $this->getTaxonById($this->rGetId());

        if ( !empty($taxon) )
		{
			$categories=$this->getTaxonCategories( [ 'taxon' => $taxon['id'],'base_rank' => $taxon['base_rank_id'],'requestedTab'=>$this->rGetVal('cat') ] );

			$content=$this->getTaxonContent( [
				'taxon' => $taxon['id'],
				'category' => $categories['start'],
				'allowUnpublished' => $this->isLoggedInAdmin(),
				'isLower' =>  $taxon['lower_taxon']
			] );

			$external_content=$this->getExternalContent( $categories['start'] );
			
			if ( isset($external_content) && $external_content->must_redirect==true)
			{
				$this->redirect( $external_content->full_url );
			}
			
			if ( $this->show_nsr_specific_stuff )
			{
				$taxon['NsrId'] = $this->getNSRId(array('id'=>$this->rGetVal('id')));
				$overview = $this->getTaxonOverviewImageNsr($taxon['id']);
				$classification=$this->getTaxonClassification($taxon['id']);
				$classification=$this->getClassificationSpeciesCount(array('classification'=>$classification,'taxon'=>$taxon['id']));
				$children=$this->getTaxonChildren(array('taxon'=>$taxon['id'],'include_count'=>true));
				$names=$this->getNames(array('id'=>$taxon['id']));

				if (defined('TAB_BEDREIGING_EN_BESCHERMING') && $categories['start']['tabname']==TAB_BEDREIGING_EN_BESCHERMING)
				{
					$wetten=$this->getEzData($taxon['id']);
					$this->smarty->assign('wetten',$wetten);
				}
				else
				if (defined('TAB_VERSPREIDING') && $categories['start']['tabname']==TAB_VERSPREIDING)
				{

					$distributionMaps=$this->getDistributionMaps($taxon['id']);
					$this->smarty->assign('distributionMaps',$distributionMaps);

					$presenceData=$this->getPresenceData($taxon['id']);
					$this->smarty->assign('presenceData',$presenceData);

					$trendData=$this->getTrendData($taxon['id']);
					$this->smarty->assign('trendData',$trendData);

					$statusRodeLijst=$this->getEzStatusRodeLijst($taxon['id']);
					$this->smarty->assign('statusRodeLijst',$statusRodeLijst);

					$atlasData=$this->getVerspreidingsatlasData($taxon['id']);
					if (!empty($atlasData['logo']))
					{
						array_push(
							$sideBarLogos,
							array(
								'organisation'=>$atlasData['organisation'],
								'logo'=>$atlasData['logo'],
								'url'=>$atlasData['organisation_url']
							)
						);
					}
					$this->smarty->assign('atlasData',$atlasData);

				}

				if ($categories['start']['tabname']==CTAB_MEDIA)
				{
					$this->smarty->assign('search',$this->requestData);
					$this->smarty->assign('querystring',$this->reconstructQueryString());
					$this->smarty->assign('mediaOwn',$this->getTaxonMediaNsr($this->requestData));
					$this->smarty->assign('mediaCollected',$this->getCollectedLowerTaxonMediaNsr($this->requestData));
				}

				$sideBarLogos=array();
				// REFAC2015 --> needs to be moved to function
				if (isset($content['rdf']))
				{
					$name=$url=null;
					foreach((array)$content['rdf'] as $key=>$val)
					{
						if ($val['predicate']=='hasPublisher')
						{
							$name=isset($val['data']['name']) ? $val['data']['name'] : null;
							$url=isset($val['data']['homepage']) ? $val['data']['homepage'] : null;

							array_push(
								$sideBarLogos,
								array(
									'organisation'=>$name,
									'logo'=>$this->getOrganisationLogoUrl($name),
									'url'=>$url
								)
							);
						}
						if ($val['predicate']=='hasReference')
						{
							$content['rdf'][$key]['data']['authors']=$this->getReferenceAuthors($val['data']['id']);
							$content['rdf'][$key]['data']['periodical_ref']=$this->getReference($val['data']['periodical_id']);
							$content['rdf'][$key]['data']['publishedin_ref']=$this->getReference($val['data']['publishedin_id']);
						}
					}

					$this->smarty->assign('rdf',$content['rdf']);
				}
			}
			else
			{
				$overview = $this->getTaxonOverviewImage();
			}

			$this->setPageName($taxon['label']);

			$this->smarty->assign('external_content',isset($external_content) ? $external_content : null);
			$this->smarty->assign('requested_category',$categories['start']);
			$this->smarty->assign('content',isset($content['content']) ? $content['content'] : null);
			$this->smarty->assign('sideBarLogos',isset($sideBarLogos) ? $sideBarLogos : null);
			$this->smarty->assign('showMediaUploadLink',$taxon['base_rank_id']>=SPECIES_RANK_ID);
			$this->smarty->assign('categories',$categories['categories']);
			$this->smarty->assign('activeCategory',$categories['start']);
			$this->smarty->assign('taxon',$taxon);

			$this->smarty->assign('classification',isset($content['classification']) ? $content['classification'] : null);
			$this->smarty->assign('children',isset($content['children']) ? $content['children'] : null);
			$this->smarty->assign('names',isset($names) ? $names : null);

			$this->smarty->assign('overviewImage', $overview);
			$this->smarty->assign('headerTitles',array('title'=>$taxon['label'].(isset($taxon['commonname']) ? ' ('.$taxon['commonname'].')' : '')));
			$this->smarty->assign('is_nsr', $this->show_nsr_specific_stuff);
			
			$this->printPage('taxon');
		}
		else
		{

			$content_404=json_decode($this->moduleSettings->getSetting( "404_content", '{"title":"Page not found","body":"The requested page could not be found."}' ));

			$this->smarty->assign( 'title', $this->translate( $content_404->title ) );
			$this->smarty->assign( 'body', $this->translate( $content_404->body ) );

	        $this->printPage( '../shared/404' );
		}	
	}

    public function nameAction()
    {
        if ($this->rHasId())
		{
			$name=$this->getName(array('nameId'=>$this->rGetId()));
			$name['nametype']=sprintf($this->Rdf->translatePredicate($name['nametype']),$name['language_label']);

			$taxon=$this->getTaxonById($name['taxon_id']);
			$taxon['taxon']=$this->addHybridMarkerAndInfixes( array('name'=>$taxon['taxon'],'base_rank_id'=>$taxon['base_rank_id']) );
			
			$this->smarty->assign('name',$name);
			$this->smarty->assign('taxon',$taxon);

		}
        $this->printPage();
    }

	public function getTaxonClassification($id)
	{
		$this->tmp = array();

		$this->_getTaxonClassification($id);

		return $this->tmp;
	}

    public function getTaxonById( $id )
    {
        $taxon=parent::getTaxonById( $id );
		return $taxon;
    }

    public function ajaxInterfaceAction ()
    {
		$return=null;

		if ($this->rHasVal('action', 'get_media_batch') && $this->rHasId())
		{
			$return=json_encode($this->getTaxonMediaNsr(array('id'=>$this->rGetId(),'page'=>$this->rGetVal('page'))));
        } else
		if ($this->rHasVal('action', 'get_collected_batch') && $this->rHasId())
		{
			$return=json_encode($this->getCollectedLowerTaxonMediaNsr(array('id'=>$this->rGetId(),'page'=>$this->rGetVal('page'))));
        }

        $this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage('ajax_interface');
    }

	private function getFirstTaxonIdNsr()
	{
		return $this->models->{$this->_model}->getFirstTaxonIdNsr($this->getCurrentProjectId());
	}

	private function resolveSubstField( $p )
	{
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$val = isset($p['val']) ? $p['val'] : null;
		$valid_name = isset($p['valid_name']) ? $p['valid_name'] : null;

		if (is_null($taxon) || is_null($val) ) return;

		/*
		['field'=>'taxon','label'=>'scientific name'],
		['field'=>'name:nomen','label'=>'nomen (scientific name w/o author)'],
		['field'=>'name:uninomial','label'=>'uninomial (valid name 1st part)'],
		['field'=>'name:specific_epithet','label'=>'specific epithet (valid name 2nd part)'],
		['field'=>'name:infra_specific_epithet','label'=>'infra specific epithet (valid name 3rd part)'],
		['field'=>'id','label'=>'taxon ID'],
		['field'=>'project_id','label'=>'project ID'],
		['field'=>'language_id','label'=>'language ID'],
		['field'=>'nsr_id','label'=>'NSR ID']
		+
		trait:...
		*/

		$sval=null;

		if ( $val=='project_id' )
		{
			$sval=$this->getCurrentProjectId();
		}
		else
		if ( $val=='language_id' )
		{
			$sval=$this->getCurrentLanguageId();
		}
		else
		if( strpos($val,'name:')===0 )
		{
			$val=substr($val,strlen('name:'));

			if ($val=='nomen')
			{
				$sval=trim(str_replace($valid_name['authorship'],'',$valid_name['name']));
			}
			else
			if ( isset($valid_name[$val]) )
			{
				$sval=$valid_name[$val];
			}
		}
		else
		if( strpos($val,'trait:')!==false )
		{
			// 	val={trait:[traits_traits.trait_group_id]:[traits_traits.id]}
			$trait=explode(':',$val);
			$sval=$this->models->{$this->_model}->getTaxonTraitValue( array(
				"project_id" => $this->getCurrentProjectId(),
				"taxon_id" => $taxon['id'],
				"trait_group_id" => $trait[1],
				"trait_id" => $trait[2]
			));
		}
		else
		if ( isset($taxon[$val]) )
		{
			$sval=$taxon[$val];
		}

		return $sval;
	}

	private function parseExternalReference( $p )
	{

		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$reference = isset($p['reference']) ? $p['reference'] : null;

		if ( is_null($taxon) || is_null($reference) ) return;

		$valid_name=null;
		foreach((array)$reference->substitute+(array)$reference->parameters as $val)
		{
			if (strpos($val,'name:')===0)
			{
				$valid_name=@$this->models->Names->_get(["id"=>[
						"project_id"=>$this->getCurrentProjectId(),
						"taxon_id"=>$taxon['id'],
						"type_id"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']]
					])[0];
				break;
			}
		}

		if ( isset($reference->substitute) )
		{
			foreach((array)$reference->substitute as $key=>$val)
			{
				$sval=$this->resolveSubstField(['taxon'=>$taxon,'val'=>$val,'valid_name'=>$valid_name]);

				if ( isset($sval) )
				{
					if ( isset($reference->substitute_encode) && $reference->substitute_encode!='none' && is_callable( $reference->substitute_encode ) )
					{
						$sval=call_user_func($reference->substitute_encode, $sval );
					}

					$reference->url = str_replace( $key, $sval, $reference->url );
				}
				else
				{
					$reference->url = str_replace( $key, "" , $reference->url );
				}
			}
		}

		$query_string=null;

		if ( isset($reference->parameters) )
		{
			foreach((array)$reference->parameters as $key=>$val)
			{
				$sval=$this->resolveSubstField(['taxon'=>$taxon,'val'=>$val,'valid_name'=>$valid_name]);

				if ( !empty($sval) )
				{
					if ( isset($reference->parameter_encode) && $reference->parameter_encode!='none' && is_callable( $reference->parameter_encode ) )
					{
						$sval=call_user_func($reference->parameter_encode, $sval );
					}

					$query_string .= $key .'=' . rawurlencode( $sval ) . '&';
				}
			}
		}

		$parts=parse_url( $reference->url );

		$full_url=$reference->url . ( !empty($query_string) ? ( !empty($parts['query']) ? '&' : '?' ) . rtrim( $query_string, '&' ) : "" );

		$is_empty=null;

		if ( isset($reference->check_type) )
		{
			if ( $reference->check_type=='none' )
			{
				$is_empty=false;
			}
			else
			if ( $reference->check_type=='query' && !empty($reference->query) )
			{
				$query = str_replace( array('%pid%','%tid%'), array($this->getCurrentProjectId(), $taxon['id']), $reference->query );
				$is_empty=$this->models->{$this->_model}->runCheckQuery( $query );
			}
		}

		return
			array(
				'full_url'=>$full_url,
				'full_url_valid'=>filter_var($full_url, FILTER_VALIDATE_URL),
				'is_empty'=>$is_empty
			);

	}

    private function getCategories($p=null)
    {
		$taxon_id = isset($p['taxon']) ? $p['taxon'] : null;

		$categories = $this->models->{$this->_model}->getCategories(array(
            'project_id' => $this->getCurrentProjectId(),
    		'taxon_id' => $taxon_id,
    		'language_id' => $this->getCurrentLanguageId()
		));

		$standard_categories=$this->cTabs;

		array_walk($standard_categories,function(&$a,$b)
		{
			$a=['tabname'=>$b,'id'=>$a['id'],'page'=>$a['title'],'type'=>'auto','always_hide'=>false,'is_empty'=>false];
		});
		
		$all_categories=array_merge($categories,$standard_categories);

        $lp=$this->getProjectLanguages();

		$order=$this->models->TabOrder->_get([
			'id'=>['project_id' => $this->getCurrentProjectId()],
			'order'=>'show_order',
			'fieldAsIndex'=>'page_id'	
		]);
		
        foreach((array)$all_categories as $key=>$page)
		{
            foreach((array)$lp as $k=>$language)
			{
                $tpt = $this->models->PagesTaxaTitles->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'page_id' => $page['id'],
                        'language_id' => $language['language_id']
                    )
                ));
				
                $all_categories[$key]['page_titles'][$language['language_id']] = $tpt[0]['title'];
                $all_categories[$key]['show_order']=isset($order[$page['id']]) ? $order[$page['id']]['show_order'] : 99;
                $all_categories[$key]['suppress']=isset($order[$page['id']]) ? $order[$page['id']]['suppress']==1 : false;
                $all_categories[$key]['start_order']=isset($order[$page['id']]) ? $order[$page['id']]['start_order'] : null;
                $all_categories[$key]['show_when_empty']=isset($order[$page['id']]) ? $order[$page['id']]['show_when_empty']==1 : false;
				if ( empty($page['type']) ) $all_categories[$key]['type']= isset($page['external_reference']) ? 'external' : 'configured';
            }
			
			$all_categories[$key]['label']=
				isset($all_categories[$key]['page_titles'][$this->getCurrentLanguageId()]) ?
					$all_categories[$key]['page_titles'][$this->getCurrentLanguageId()] : $all_categories[$key]['page'];
        }

		usort($all_categories,function($a,$b)
		{
			if ($a['show_order']>$b['show_order'] )
				return 1;
			if ($a['show_order']<$b['show_order'] )
				return -1;
			if ($a['page']>$b['page'] )
				return 1;
			if ($a['page']<$b['page'] )
				return -1;
			return 0;
		});

		return $all_categories;

	}

    private function getTaxonCategories($p=null)
    {
		$taxon_id = isset($p['taxon']) ? $p['taxon'] : null;
		$baseRank = isset($p['base_rank']) ? $p['base_rank'] : null;
		$requestedTab = isset($p['requestedTab']) ? $p['requestedTab'] : null;

		// get all available categories (tabs)
		$categories=$this->getCategories($p);

		$taxon_categories=array();
		$start_category=null;
		$taxon=null;
		$cat=null;
		
		if (is_numeric($requestedTab))
		{
			$cat=$requestedTab;
		}

		// weed out the ones we don't want to display
		foreach((array)$categories as $key=>$val)
		{
			if ( $val['always_hide'] ) continue;
			if ( $val['suppress'] ) continue;
			
			if ($val['type']=='auto')
			{
				$val['is_empty']=$this->isAutoTabEmpty( ['tab'=>$val['tabname'], 'taxon_id'=>$taxon_id ] );
			}
			
			if ( $val['is_empty'] && !$val['show_when_empty'] ) continue;

			// parametrize external reference URLs
			if ( isset($val['external_reference']) )
			{
				$taxon=is_null($taxon) ? $this->getTaxonById( $taxon_id ) : $taxon;
				$ref=json_decode( $val['external_reference'] );
				$d=$this->parseExternalReference( array('taxon'=>$taxon,'reference'=>$ref) );
				$ref->full_url=$d['full_url'];
				$ref->full_url_valid=$d['full_url_valid'];
				$val['external_reference']=$ref;
				$val['is_empty']=$d['is_empty'];
				$val['type']='external';
			}

			$val['show_overview_image']=false;

			$taxon_categories[]=$val;

			// is cat has been provided as TAB_CATNAME rather than an ID, resolve
			if (!is_int($requestedTab) && $requestedTab==$val['tabname'])
			{
				$cat=$val['id'];
			}
		}
			
		// have the first non-automatic/external tab display the overview image
		foreach((array)$taxon_categories as $key=>$val)
		{
			if ( $val['type']=='configured' )
			{
				$taxon_categories[$key]['show_overview_image']=true;
				break;
			}
		}
		
		// determine with which category to open
		foreach((array)$taxon_categories as $key=>$val)
		{
			if ( is_null($start_category) )
			{
				$start_category=$val;
				if ( is_null($start_category['start_order']) ) $start_category['start_order']=99;
			}

			if ( !is_null($cat) && $val['id']==$cat )
			{
				$start_category=$val;
			}
			else
			if ( is_null($cat) && !is_null($val['start_order']) && $val['start_order'] < $start_category['start_order'] )
			{
				$start_category=$val;
			}
		}

//		q($start_category);
//		q($taxon_categories,1);
		
		return [ 'start'=>$start_category, 'categories'=>$taxon_categories ];
	
		



		// remnants...		
		if ( isset($taxon_id) )
		{
			foreach((array)$categories as $key=>$val)
			{
				if (defined('TAB_BEDREIGING_EN_BESCHERMING') && $val['id']==TAB_BEDREIGING_EN_BESCHERMING)
				{
					$categories[$key]['is_empty']=true;
					$dummy=$key;
				}
			}

			if (defined('TAB_VERSPREIDING'))
			{
				$d=$this->getTaxonContent(array('category'=>TAB_VERSPREIDING,'taxon'=>$taxon_id));
				$p=$this->getPresenceData($taxon_id);

				if (!empty($p['presence_information_one_line']) || !empty($d['content']))
				{
					foreach((array)$categories as $key=>$val)
					{
						if ($val['id']==TAB_VERSPREIDING) {
							$categories[$key]['is_empty']=false;
							break;
						}
					}
				}
			}

			// TAB_BEDREIGING_EN_BESCHERMING check at EZ
			// this should be changed to a generalized method, using 'redirect_to'
			// REFAC2015
			if (isset($dummy) && isset($categories[$dummy]['is_empty']) && $categories[$dummy]['is_empty']==1 && $this->_taxon_fetch_ez_data)
			{
				$ezData=$this->getEzData($taxon_id);
				$categories[$dummy]['is_empty']=empty($ezData);
			}

		}
    }

	private function getNames( $p )
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;

		$names = $this->models->{$this->_model}->getNamesNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultLanguageId(),
    		'taxonId' => $id
		));

		$preferredname=null;
		$scientific_name=null;
		$nomen=null;
		$prevs=array();

		$language_has_preferredname=array();

		$synonymStartIndex=-1;
		$synonymCount=0;
		$i=0;

		foreach((array)$names as $key=>$val)
		{
			$prevs[]=$key;

			if ($val['nametype']==PREDICATE_SYNONYM)
			{
				if ($synonymStartIndex==-1) $synonymStartIndex=$i;
				$synonymCount++;
			}

			if ($val['nametype']==PREDICATE_PREFERRED_NAME && $val['language_id']==$this->getDefaultLanguageId())
			{
				$preferredname=$val['name'];
			}

			if ($val['language_id']==LANGUAGE_ID_SCIENTIFIC && $val['nametype']==PREDICATE_VALID_NAME)
			{
				$nomen=trim($val['uninomial']).' '.trim($val['specific_epithet']).' '.trim($val['infra_specific_epithet']);

				if (strlen(trim($nomen))==0)
					$nomen=trim(str_replace($val['authorship'],'',$val['name']));

				if ($base_rank_id>=GENUS_RANK_ID)
				{
					$nomen='<i>'.$this->addHybridMarkerAndInfixes( array('name'=>trim($nomen),'base_rank_id'=>$base_rank_id) ).'</i>';
					$names[$key]['name']=trim($nomen.' '.$val['authorship']);
				}

				$scientific_name=$this->addHybridMarkerAndInfixes( array('name'=>trim($val['name']),'base_rank_id'=>$base_rank_id) );

			}

			$names[$key]['addition']=$this->getNameAddition(array('name_id'=>$val['id']));

			if (!empty($val['expert_id']))
			{
				$names[$key]['expert']=$this->getActor($val['expert_id']);
			}

			if (!empty($val['organisation_id']))
			{
				$names[$key]['organisation']=$this->getActor($val['organisation_id']);
			}

			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);


			/*
				$language_has_preferredname will be used to determine whether there is
				a preferred name in each language. if not, the alternative name(s) will
				be "promoted": 	it/they get the label of the preferred name, rather than
				that of an alternative name. put ifferently, it makes no sense to display
				alternative names if there's no preferred one.
				so this:
					Nederlandse naam 	Das
					Alternatieve Nederlandse naam 	Dasseke
					Alternatieve Nederlandse naam 	Bobbelbeest
					Alternatieve Nederlandse naam 	Zwartwitje
					Alternatieve Nederlandse naam 	Namaak-beer

				will turn into this:
					Nederlandse naam 	Das
					Nederlandse naam 	Dasseke
					Nederlandse naam 	Bobbelbeest
					Nederlandse naam 	Zwartwitje
					Nederlandse naam 	Namaak-beer

				if 'Das' is changed from preferred to alternative name.
			*/
			if ($val['nametype']==PREDICATE_PREFERRED_NAME)
			{
				$language_has_preferredname[$val['language_id']]=true;
			}

			if ($val['nametype']==PREDICATE_ALTERNATIVE_NAME)
			{
				$names[$key]['alt_alt_nametype_label']=sprintf($this->Rdf->translatePredicate(PREDICATE_PREFERRED_NAME),$val['language_label']);
			}

			$i++;
		}

		// sorting the synonyms by year
		if ($synonymStartIndex>-1)
		{
			$synonyms=array_splice($names,$synonymStartIndex,$synonymCount,array());
			usort($synonyms,function($a,$b){
				$aa=!empty($a['authorship_year']) ? intval($a['authorship_year']) : intval(preg_replace('/\D/',"",$a['name']));
				$bb=!empty($b['authorship_year']) ? intval($b['authorship_year']) : intval(preg_replace('/\D/',"",$b['name']));
				return ( $aa > $bb ? 1 : ( $aa < $bb ? -1 : 0 ) );
			});
			array_splice($names,$synonymStartIndex,0,$synonyms);
		}

		return
			array(
				'scientific_name'=>$scientific_name,
				'nomen'=>$nomen,
				'nomen_no_tags'=>trim(strip_tags($nomen)),
				'preffered_name'=>$preferredname,
				'hybrid_marker'=>$this->addHybridMarkerAndInfixes( array('base_rank_id'=>$base_rank_id) ),
				'list'=>$names,
				'language_has_preferredname'=>$language_has_preferredname
			);
	}

	private function getActor( $id )
	{
		$data=$this->models->Actors->_get(array(
			'id' => array(
				'project_id'=>$this->getCurrentProjectId(),
				'id'=>$id
			)
		));
		return $data[0];
	}

    private function getTaxonOverviewImageNsr( $id )
	{
		$d=$this->getTaxonMediaNsr(array('id'=>$id,'sort'=>'_meta4.meta_date desc','limit'=>1,'overview'=>true));

		if ( empty($d['data']) )
		{
			$d=(array)$this->getTaxonMediaNsr(array('id'=>$id,'sort'=>'_meta4.meta_date desc','limit'=>1));
		}

		return !empty($d['data']) ? array_shift($d['data']) : null;
	}

    private function getTaxonMediaNsr( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return;

		$overview=isset($p['overview']) ? $p['overview'] : false;
		$distributionMaps=isset($p['distribution_maps']) ? $p['distribution_maps'] : false;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;
		$sort=!empty($p['sort']) ? $p['sort'] : '_meta4.meta_date desc';

		list($data, $total) = $this->models->{$this->_model}->getTaxonMediaNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id,
		    'overview' => $overview,
    		'distributionMaps' => $distributionMaps,
    		'limit' => $limit,
    		'offset' => $offset,
    		'sort' => $sort,
    		'predicatePreferredNameId' => $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
    		'predicateValidNameId' => $this->_nameTypeIds[PREDICATE_VALID_NAME]['id']
		));

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['taxon']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
			$data[$key]['name']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['name'],'base_rank_id'=>$val['base_rank_id'] ) );
			$data[$key]['nomen']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['nomen'],'base_rank_id'=>$val['base_rank_id'] ) );
		}

		return
			array(
				'count'=>$total,
				'data'=>$this->NSRFunctions->formatPictureResults($data),
				'perpage'=>$this->_resPicsPerPage
			);

    }

    private function getCollectedLowerTaxonMediaNsr( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;

		if (empty($id))
			return;

		list($data, $total) = $this->models->{$this->_model}->getCollectedLowerTaxonMediaNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id,
    		'limit' => $limit,
    		'offset' => $offset,
    		'predicatePreferredNameId' => $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
    		'predicateValidNameId' => $this->_nameTypeIds[PREDICATE_VALID_NAME]['id']
		));

		return
			array(
				'count'=>$total, // number of images, one per taxon in this branch
//				'totalCount'=>$totalCount[0]['total'], // all images in this branch
				'species' => $this->models->{$this->_model}->taxonMediaCountNsr(array(
                    'projectId' => $this->getCurrentProjectId(),
				    'taxonId' => $id
				)),
				'data'=>$this->NSRFunctions->formatPictureResults($data),
				'perpage'=>$this->_resPicsPerPage
			);
	}

    private function getTaxonMediaCountNsr( $id )
    {
        $mt = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $id
            ),
            'columns' => 'count(*) as total'
        ));

        return isset($mt) ? $mt[0]['total'] : 0;
    }

	private function _getTaxonClassification( $id )
	{
		$taxon = $this->models->{$this->_model}->getTaxonNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'predicatePreferredNameId' => $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id
		));

		$taxon['name']=$this->addHybridMarkerAndInfixes(array('name'=>$taxon['name'],'base_rank_id'=>$taxon['rank_id']));
		$taxon['taxon']=$this->addHybridMarkerAndInfixes(array('name'=>$taxon['taxon'],'base_rank_id'=>$taxon['rank_id']));
		$taxon['uninomial']=$this->addHybridMarkerAndInfixes(array('uninomial'=>$taxon['uninomial'],'base_rank_id'=>$taxon['rank_id']));
		$taxon['specific_epithet']=$this->addHybridMarkerAndInfixes(array('specific_epithet'=>$taxon['specific_epithet'],'base_rank_id'=>$taxon['rank_id']));

		array_unshift($this->tmp,$taxon);

		if (!empty($taxon['parent_id'])) {
			$this->_getTaxonClassification($taxon['parent_id']);
		}

	}

	private function getSpeciesCount( $p )
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$rank=isset($p['rank']) ? $p['rank'] : null;

		if (is_null($id))
			return;

		/*
			'undefined' are the taxa that DO HAVE a presence_id (so they must be
			species or below) but have a presence that has a null-value
			for indigenous
		*/

		$data = $this->models->{$this->_model}->getSpeciesCountNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'taxonId' => $id,
    		'rankId' => $rank,
    		'speciesRankId' => SPECIES_RANK_ID
		));

		$d=
			array(
				'total'=>
					(int)
						(isset($data['undefined']['total'])?$data['undefined']['total']:0)+
						(isset($data[0]['total'])?$data[0]['total']:0)+
						(isset($data[1]['total'])?$data[1]['total']:0),
				'established'=>
						(int)(isset($data[1]['total'])?$data[1]['total']:0),
				'not_established'=>
						(int)(isset($data[0]['total'])?$data[0]['total']:0)
			);

		return $d;
	}

	private function getTaxonChildren( $p )
	{
		$include_count=isset($p['include_count']) ? $p['include_count'] : false;
		$id=isset($p['taxon']) ? $p['taxon'] : null;

		$data = $this->models->{$this->_model}->getTaxonChildrenNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'predicateValidNameId' => $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id
		));

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['name']=$this->addHybridMarkerAndInfixes(array('name'=>$val['name'],'base_rank_id'=>$val['rank_id']));
			$data[$key]['taxon']=$this->addHybridMarkerAndInfixes(array('name'=>$val['taxon'],'base_rank_id'=>$val['rank_id']));
			$data[$key]['uninomial']=$this->addHybridMarkerAndInfixes(array('uninomial'=>$val['uninomial'],'base_rank_id'=>$val['rank_id']));
			$data[$key]['specific_epithet']=$this->addHybridMarkerAndInfixes(array('specific_epithet'=>$val['specific_epithet'],'base_rank_id'=>$val['rank_id']));

			if ($include_count)
				$data[$key]['species_count']=$this->getSpeciesCount(array('id'=>$val['id'],'rank'=>$val['rank_id']));
		}

		return $data;
	}

	private function getClassificationSpeciesCount( $p )
	{
		$classification=isset($p['classification']) ? $p['classification'] : null;
		$current_taxon=isset($p['taxon']) ? $p['taxon'] : null;

		if (is_null($classification))
			return;

		/*
			get the key of the taxon above the one being displayed (unless no current
			taxon has been specified, in which case we will calculate number of
			children for all members of the classification)
		*/
		$prev=null;

		if (!is_null($current_taxon)) {

			foreach((array)$classification as $key=>$val)
			{
				if ($val['id']==$current_taxon)
					break;
				$prev=$key;
			}

		}

		foreach((array)$classification as $key=>$val)
		{
			if (is_null($prev) || $key==$prev || $val['id']==$current_taxon)
				$classification[$key]['species_count']=$this->getSpeciesCount(array('id'=>$val['id'],'rank'=>$val['rank_id']));
		}

		return $classification;
	}

	private function getName( $p )
	{
		$nameId=isset($p['nameId']) ? $p['nameId'] : null;
		$taxonId=isset($p['taxonId']) ? $p['taxonId'] : null;
		$languageId=isset($p['languageId']) ? $p['languageId'] : $this->getCurrentLanguageId();
		$predicateType=isset($p['predicateType']) ? $p['predicateType'] : null;

		if (empty($nameId) && (empty($taxonId) || empty($languageId) || empty($predicateType))) return;

		$d = $this->models->{$this->_model}->getNameNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'predicateType' => $predicateType,
    		'languageId' => $languageId,
    		'taxonId' => $taxonId,
    		'nameId' => $nameId
		));

		$d['name']=$this->addHybridMarkerAndInfixes( array('name'=>$d['name'],'base_rank_id'=>$d['base_rank_id']) );

		$d['addition']=$this->getNameAddition(array('name_id'=>$d['id']));

		if ($d['nametype']==PREDICATE_ALTERNATIVE_NAME)
		{
			$d['alt_alt_nametype_label']=sprintf($this->Rdf->translatePredicate(PREDICATE_PREFERRED_NAME),$d['language_label']);
			$d['language_has_preferredname']=$this->doesLanguageHavePreferredName( $d );
		}

		return $d;
	}

	private function getDNABarcodes( $taxon_id )
	{
		return $this->models->DnaBarcodes->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon_id
				),
				'columns' => 'taxon_literal,barcode,location,date_literal,specialist',
				'order' => 'date desc'
			));
	}

	private function getPresenceData( $id )
	{
		$data = $this->models->{$this->_model}->getPresenceDataNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id
		));

		$data[0]['presence_information_one_line']=str_replace(array("\n","\r","\r\n"),'<br />',$data[0]['presence_information']);

		return $data[0];
	}

	private function getTrendData($id)
	{
	    $byYear = $this->models->{$this->_model}->getTrendDataByYear(array(
            'projectId' => $this->getCurrentProjectId(),
    	    'taxonId' => $id
	    ));

		$byTrend = $this->models->{$this->_model}->getTrendDataByTrend(array(
            'projectId' => $this->getCurrentProjectId(),
    	    'taxonId' => $id
	    ));

		$sources=array();

		foreach(array_merge((array)$byYear,(array)$byTrend) as $val)
			$sources[$val['source']]=$val['source'];

		sort($sources);

		return array(
			'byYear'=>$byYear,
			'byTrend'=>$byTrend,
			'sources'=>$sources
		);
	}

    private function getTaxonContent($p=null)
    {
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$category = isset($p['category']) ? $p['category'] : null;
		$allowUnpublished = isset($p['allowUnpublished']) ? $p['allowUnpublished'] : false;
		$isLower = isset($p['isLower']) ? $p['isLower'] : true;
		$limit=isset($p['limit']) ? $p['limit'] : null;
		$offset=isset($p['offset']) ? $p['offset'] : null;
		$inclOverviewImage = isset($p['inclOverviewImage']) ? $p['inclOverviewImage'] : false;

		$content=$rdf=null;

		$isPublished=null;

        switch ($category['tabname'])
		{
            case 'CTAB_MEDIA':
                $content = $this->getTaxonMedia(array(
                    'taxon'=>$taxon,
                    'inclOverviewImage'=>$inclOverviewImage
                ));
                break;

		    case 'CTAB_CLASSIFICATION':
                $content=
					array(
						'classification'=>$this->getTaxonClassification($taxon),
						'taxonlist'=>$this->getTaxonNextLevel($taxon)
					);
                break;

            case 'CTAB_TAXON_LIST':
                $content=$this->getTaxonNextLevel($taxon);
                break;

            case 'CTAB_LITERATURE':
                $content=
					array(
						'literature'=>$this->getTaxonLiterature($taxon),
						'inherited_literature'=>$this->getInheritedTaxonLiterature($taxon)
					);
                break;

            case 'CTAB_DNA_BARCODES':
                $content=$this->getDNABarcodes($taxon);
                break;

            case 'CTAB_DICH_KEY_LINKS':
                $content=$this->getTaxonKeyLinks( $taxon );
                break;

            case 'CTAB_NAMES':
                $content=$this->getNames( ['id' => $taxon ] );
                break;

            default:

                $d = array(
                    'taxon_id' => $taxon,
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->getCurrentLanguageId(),
                    'page_id' => $category['id']
                );

                if (!$allowUnpublished)
                    $d['publish'] = '1';

                $ct = $this->models->ContentTaxa->_get(array(
                    'id' => $d,
                ));

				$content = isset($ct) ? $ct[0] : null;
				$isPublished = isset($content['publish']) ? $content['publish'] : null;

        }

		if (isset($content['id']))
			$rdf=$this->Rdf->getRdfValues($content['id']);

		if (isset($content['content']))
			$content=$content['content'];

		return array('content'=>$content,'rdf'=>$rdf,'isPublished'=>$isPublished);
    }

	private function getExternalContent( $cat )
	{
		if ( $cat['type']!='external' ) return;

		$ref=$cat['external_reference'];
		$external_content=$ref;
		$external_content=(array)$external_content;
		$external_content['must_redirect']=false;
		$external_content=(object)$external_content;

		if ( !empty($ref->full_url) && !empty($ref->link_embed) && ( $ref->link_embed=='link' || $ref->link_embed=='link_new' ) )
		{
			if ( $ref->full_url_valid )
			{
				//$this->redirect( $ref->full_url );
				$external_content->must_redirect=true;
			}
			else
			{
				//$this->addMessage( sprintf( $this->translate('Invalid URL: %s'), $ref->full_url ) );
			}
		}
		else
		if ( !empty($ref->full_url) && !empty($ref->link_embed) && $ref->link_embed=='embed' )
		{
			if ( $ref->full_url_valid )
			{
				$external_content->content_raw=@file_get_contents(  $ref->full_url  );
				$external_content->content_json_decoded=@json_decode( $external_content->content_raw );
			}
			else
			{
				//$this->addMessage( sprintf( $this->translate('Invalid URL: %s'), $ref->full_url ) );
			}
		}
		q($external_content);
		return $external_content;

	}

	private function reconstructQueryString()
	{
		$querystring=null;
		foreach((array)$this->requestData as $key=>$val) {
			if ($key=='page') continue;
			$querystring.=$key.'='.$val.'&';
		}
		return $querystring;
	}

    private function getNSRId($p)
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$item_type=isset($p['item_type']) ? $p['item_type'] : 'taxon';
		$rdf_nsr=isset($p['rdf_nsr']) ? $p['rdf_nsr'] : 'nsr';
		$strip=isset($p['strip']) ? $p['strip'] : true;

		if (empty($id))
			return;

		$t=$this->models->NsrIds->_get(
			array('id'=>
				array(
					'project_id' => $this->getCurrentProjectId(),
					'lng_id' => $id,
					'item_type' => $item_type
					)
				)
			);


		if (!$t) return;

		if ($strip) {
			return ltrim(str_replace(($item_type=='rdf' ?'http://data.nederlandsesoorten.nl/' : 'tn.nlsr.concept/'),'',($rdf_nsr=='rdf' ? $t[0]['rdf_id'] : $t[0]['nsr_id'])),' 0');
		} else {
			return $rdf_nsr=='rdf' ? $t[0]['rdf_id'] : $t[0]['nsr_id'];
		}
    }

	private function getOrganisationLogoUrl($name)
	{
		if (empty($name)) return;

		$exts=array('png','jpg','PNG','JPG','gif','GIF');
		$d=array();

		foreach($exts as $ext)
		{
			array_push($d,$name.'.'.$ext,strtolower($name).'.'.$ext,$name.'-logo.'.$ext,strtolower($name).'-logo.'.$ext);
		}

		if (strpos($name,' ')!==false)
		{
			$a=str_replace(' ','_',$name);
			$b=str_replace(' ','-',$name);
			$c=substr($name,0,strpos($name,' '));
			foreach($exts as $ext)
			{
				array_push($d,
					$a.'.'.$ext,
					strtolower($a).'.'.$ext,
					$a.'-logo.'.$ext,
					strtolower($a).'-logo.'.$ext,

					$b.'.'.$ext,
					strtolower($b).'.'.$ext,
					$b.'-logo.'.$ext,
					strtolower($b).'-logo.'.$ext,

					$c.'.'.$ext,
					strtolower($c).'.'.$ext,
					$c.'-logo.'.$ext,
					strtolower($c).'-logo.'.$ext
				);
			}
		}

		$logo=null;

		foreach((array)$d as $val)
		{
			if (file_exists($this->getProjectUrl('projectMedia').$val))
			{
				$logo=$this->getProjectUrl('projectMedia').$val;
				break;
			}
		}

		return $logo;
	}

    private function getExternalId($p)
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$org=isset($p['org']) ? $p['org'] : null;

		if (empty($id)||empty($org))
			return;

		$t = $this->models->{$this->_model}->getExternalIdNsr(array(
            'projectId' => $this->getCurrentProjectId(),
            'taxonId' => $id,
    		'organisation' => $org
		));

		if ($t)
		{
			$name=$t[0]['name'];

			return array(
				'organisation' => $name,
				'logo'=> $this->getOrganisationLogoUrl($name),
				'organisation_url' => $t[0]['organisation_url'],
				'general_url' => sprintf($t[0]['general_url'],$t[0]['external_id']),
				'service_url' => sprintf($t[0]['service_url'],$t[0]['external_id']),
				'id' => $t[0]['external_id']
			);
		}

    }

    private function getExternalOrg($org)
    {
		if (empty($org))
			return;

		$t = $this->models->{$this->_model}->getExternalOrgNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'organisation' => $org
		));

		if ($t)
		{
			$name=$t[0]['name'];

			return array(
				'organisation' => $name,
				'logo'=> $this->getOrganisationLogoUrl($name),
				'organisation_url' => $t[0]['organisation_url'],
				'general_url' => $t[0]['general_url'],
				'service_url' => $t[0]['service_url'],
			);
		}

    }

	private function getEzData($id)
	{
		$checked=$this->getSessionVar(array('ez-data-checked',$id));

		if ($checked!==true)
		{
			$org=$this->getExternalOrg('Ministerie EZ');
			//$data=json_decode(file_get_contents(sprintf($org['service_url'],$this->getNSRId(array('id'=>$id)))));

			// REFAC2015 - move the timeout values to config
			$url=str_replace(' ','%20',sprintf($org['service_url'],$this->getNSRId(array('id'=>$id))));
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$result=curl_exec($ch);
			curl_close($ch);
			$data=json_decode($result);

			if (!empty($data))
			{
				$wetten=array();

				foreach($data as $key=>$val)
				{
					$wetten[$val->wetenschappelijke_naam]['wetten'][$val->wet][]=
						array(
							'categorie'=>$val->categorie,
							'publicatie'=>strip_tags($val->publicatie)
						);
					$wetten[$val->wetenschappelijke_naam]['url']=sprintf($org['general_url'],$val->soort_id);
				}

				$this->setSessionVar(array('ez-data',$id),$wetten);
			}
			else
			{
				$wetten=null;
			}

			$this->setSessionVar(array('ez-data-checked',$id),true);

			return $wetten;
		}
		else
		{
			return $this->getSessionVar(array('ez-data',$id));
		}

	}

	private function getEzStatusRodeLijst($id)
	{
		$checked=$this->getSessionVar(array('ez-data-rl-checked',$id));

		if ($checked!==true)
		{
			$org=$this->getExternalOrg('Ministerie EZ: Rode Lijst');
			//$data=json_decode(file_get_contents(sprintf($org['service_url'],$this->getNSRId(array('id'=>$id)))));

			// REFAC2015 - move the timeout values to config
			$url=str_replace(' ','%20',sprintf($org['service_url'],$this->getNSRId(array('id'=>$id))));
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$result=curl_exec($ch);
			curl_close($ch);
			$data=json_decode($result);

			if (isset($data[0]->subcategorie))
			{
				$data = array('status'=>$data[0]->subcategorie,'url'=>sprintf($org['general_url'],$data[0]->soort_id));
				$this->setSessionVar(array('ez-data-rl',$id),$data);
			}
			else
			{
				$data=null;
			}

			$this->setSessionVar(array('ez-data-rl-checked',$id),true);

			return $data;
		}
		else
		{
			return $this->getSessionVar(array('ez-data-rl',$id));
		}

	}

    private function getDistributionMaps($id)
	{
		return $this->getTaxonMediaNsr(array('id'=>$id,'distribution_maps'=>true,'sort'=>'meta_datum_plaatsing'));
	}

	private function getVerspreidingsatlasData($id)
	{

		$data=$this->getSessionVar(array('atlas-data-checked',$id));

		if (is_null($data))
		{

			$data=$this->getSessionVar(array('atlas-data',$id));

			if (is_null($data))
			{

				$data=$this->getExternalId(array('id'=>$id,'org'=>'Verspreidingsatlas'));

				if ($data)
				{
					$dummy=file_get_contents($data['service_url']);

					if ($dummy)
					{
						$xml = simplexml_load_string($dummy);

						if ($xml)
						{
							$data['content'] = (string)$xml->tab->content;
							$data['author'] = (string)$xml->tab->author;
							$data['pubdate'] = (string)$xml->tab->pubdate;
							$data['copyright'] = (string)$xml->tab->copyright;
							$data['sourcedocument'] = (string)$xml->tab->sourcedocument;
							$data['distributionmap'] = (string)$xml->tab->distributionmap;

						}
					}

				}

				$this->setSessionVar(array('atlas-data',$id),$data);
			}

			$this->setSessionVar(array('atlas-data-checked',$id),true);

		}
		else
		{
			$data=$this->getSessionVar(array('atlas-data',$id));
		}

		return $data;

	}

	private function getReferenceAuthors($id)
	{
		return $this->models->{$this->_model}->getReferenceAuthorsNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'literatureId' => $id
		));
	}

	private function getReference($id)
	{
		$l = $this->models->{$this->_model}->getReferenceNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'literatureId' => $id
		));

        if (!empty($l)) {

            $l['authors'] = $this->getReferenceAuthors($id);

        }

        return $l;

	}

	private function getNameAddition( $p )
	{
		$name_id=isset($p['name_id']) ? $p['name_id'] : null;

		if (is_null($name_id)) return;

		return $this->models->NamesAdditions->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'name_id'=>$name_id
			),
			'columns'=>'id,language_id,addition',
			'fieldAsIndex'=>'language_id'
		));
	}

	private function doesLanguageHavePreferredName( $p )
	{
		$taxon_id=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$language_id=isset($p['language_id']) ? $p['language_id'] : null;

		if ( is_null($taxon_id) || is_null($language_id) ) return;

		return $this->models->{$this->_model}->doesLanguageHavePreferredNameNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'taxonId' => $taxon_id,
    		'languageId' => $language_id
		));

	}

    private function getTaxonLiterature( $taxon_id )
    {
		return $this->models->{$this->_model}->getTaxonReferences(array(
            'project_id' => $this->getCurrentProjectId(),
    		'taxon_id' => $taxon_id,
		));
    }

    private function getInheritedTaxonLiterature( $taxon_id )
    {
		$p=$this->models->TaxonQuickParentage->_get(array("id"=>
			array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $taxon_id,
				)
		));

		$res=array();

		if ($p)
		{
			$p=explode(' ',$p[0]['parentage']);

			foreach($p as $val)
			{
				$d=$this->models->{$this->_model}->getTaxonReferences(array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $val,
				));
				if ($d)
				{
					foreach((array)$d as $dkey=>$dval)
					{
						$d[$dkey]['referencing_taxon']=$this->getTaxonById( $val );
					}
					$res=array_merge($res,$d);
				}
			}
		}
		return $res;
    }

    private function getTaxonKeyLinks( $taxon_id )
    {
		return $this->models->{$this->_model}->getTaxonKeyLinks(array(
            'project_id' => $this->getCurrentProjectId(),
    		'taxon_id' => $taxon_id,
    		'language_id' => $this->getCurrentLanguageId(),
		));
    }
	
	private function isAutoTabEmpty( $p )
	{
		$taxon_id=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$tab=isset($p['tab']) ? $p['tab'] : null;
		
		if ( is_null($taxon_id) ) return false;

		switch ( $tab )
		{
			case 'CTAB_NAMES':
				return count((array)$this->getNames( [ 'id'=>$taxon_id ] ))<=0;
				break;
			case 'CTAB_MEDIA':
				if ( $this->show_nsr_specific_stuff )
				{
					$a=$this->getTaxonMediaNsr(array('id'=>$taxon_id,'limit'=>1));
					$b=$this->getCollectedLowerTaxonMediaNsr(array('id'=>$taxon_id));
					return count((array)$a)+count((array)$b)<=0;
				}
				else
				{
					return count((array)$this->getTaxonMedia(array('id'=>$taxon_id)))<=0;
				}
				break;
			case 'CTAB_CLASSIFICATION':
				return false;
				break;
			case 'CTAB_TAXON_LIST':
				return count((array)$this->getTaxonNextLevel($taxon_id))<=0;
				break;
			case 'CTAB_LITERATURE':
				$a=$this->getTaxonLiterature($taxon_id);
				$b=$this->getInheritedTaxonLiterature($taxon_id);
				return count((array)$a)+count((array)$b)<=0;
				break;
			case 'CTAB_DNA_BARCODES':
				return count((array)$this->getDNABarcodes($taxon_id))<=0;
				break;
			case 'CTAB_DICH_KEY_LINKS':
				return count((array)$this->getTaxonKeyLinks($taxon_id))<=0;
				break;
			default:
				return false;
		}
	}

}
