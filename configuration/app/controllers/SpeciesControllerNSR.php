<?php
/*

automatische tabs
CTAB_NAMES
CTAB_MEDIA
CTAB_CLASSIFICATION
CTAB_TAXON_LIST
CTAB_LITERATURE
CTAB_DNA_BARCODES

TAB_VERSPREIDING: auto presence data, data TAB_VERSPREIDING (TAB_VOORKOMEN omgebracht bij import)

TAB_NAAMGEVING wordt omgeleid naar CTAB_NAMES
  CTAB_NAMES: auto naamgeving, classificatieboom, data TAB_NAAMGEVING

*/

include_once ('SpeciesController.php');
include_once ('RdfController.php');
include_once ('NSRFunctionsController.php');
include_once ('ModuleSettingsReaderController.php');

class SpeciesControllerNSR extends SpeciesController
{
	private $_resPicsPerPage=12;
	private $_nameTypeIds;

	public $usedModelsExtended = array(
		'actors',
		'name_types',
		'names_additions',
		'nsr_ids',
		'taxon_quick_parentage'
	);

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

        if ( empty($taxon) )
		{
			$content_404=json_decode($this->moduleSettings->getSetting( "404_content", '{"title":"Page not found","body":"The requested page could not be found."}' ));

			$this->smarty->assign( 'title', $this->translate( $content_404->title ) );
			$this->smarty->assign( 'body', $this->translate( $content_404->body ) );

	        $this->printPage( '../shared/404' );
		}
		else
		{

			$taxon['NsrId'] = $this->getNSRId(array('id'=>$this->rGetVal('id')));

			$sideBarLogos=array();

			$reqCatId=$this->rHasVal('cat') ? $this->rGetVal('cat') : null;

            $categories=$this->getCategories(array('taxon' => $taxon['id'],'base_rank' => $taxon['base_rank_id'],'requestedTab'=>$reqCatId));

			$names=$this->getNames( $taxon );

			$classification=$this->getTaxonClassification($taxon['id']);
			
			$classification=$this->getClassificationSpeciesCount(array('classification'=>$classification,'taxon'=>$taxon['id']));

			$children=$this->getTaxonChildren(array('taxon'=>$taxon['id'],'include_count'=>true));


			foreach((array)$categories['categories'] as $val)
			{
				if ( $val['id']==$reqCatId )
				{
					$requestedCategory=$val;
				}
			}
			
			if ( !empty($requestedCategory['external_reference']) )
			{
				$ref=$requestedCategory['external_reference'];

				if ( !empty($ref->full_url) && !empty($ref->link_embed) && ( $ref->link_embed=='link' || $ref->link_embed=='link_new' ) )
				{
					$this->redirect( $ref->full_url );
				} 
				else
				if ( !empty($ref->full_url) && !empty($ref->link_embed) && $ref->link_embed=='embed' )
				{
					$external_content=$ref;
					$external_content->content_raw=file_get_contents(  $ref->full_url  );
					$external_content->content_json_decoded=json_decode( $external_content->content_raw );
				}
				
				$content=$this->getTaxonContent(
					array(
						'taxon' => $taxon['id'],
						'category' => $reqCatId,
						'allowUnpublished' => $this->isLoggedInAdmin(),
						'isLower' =>  $taxon['lower_taxon']
					)
				);
		
			}
			else
			{
				// allow empty media page
				if ($categories['start']!=$reqCatId && $reqCatId=='media')
				{
					$categories['start']='media';
				}

				if ($categories['start']==CTAB_MEDIA)
				{
					$this->smarty->assign('search',$this->requestData);
					$this->smarty->assign('querystring',$this->reconstructQueryString());
					$this->smarty->assign('mediaOwn',$this->getTaxonMedia($this->requestData));
					$this->smarty->assign('mediaCollected',$this->getCollectedLowerTaxonMedia($this->requestData));
				}
				else
				{
					$content=$this->getTaxonContent(
						array(
							'taxon' => $taxon['id'],
							'category' => $categories['start'],
							'allowUnpublished' => $this->isLoggedInAdmin(),
							'isLower' =>  $taxon['lower_taxon']
						)
					);
				}

				if (defined('TAB_BEDREIGING_EN_BESCHERMING') && $categories['start']==TAB_BEDREIGING_EN_BESCHERMING)
				{
					$wetten=$this->getEzData($taxon['id']);
					$this->smarty->assign('wetten',$wetten);
				}
				else
				if (defined('TAB_VERSPREIDING') && $categories['start']==TAB_VERSPREIDING)
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
				else
				if (defined('TAB_NAAMGEVING') && ($categories['start']==CTAB_NAMES || $categories['start']==TAB_NAAMGEVING))
				{
					$content=$this->getTaxonContent(
						array(
							'taxon' => $taxon['id'],
							'category' =>  TAB_NAAMGEVING,
							'allowUnpublished' => $this->isLoggedInAdmin(),
							'isLower' =>  $taxon['lower_taxon']
						)
					);

				}

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

			$this->setPageName($taxon['label']);

			$this->smarty->assign('external_content',isset($external_content) ? $external_content : null);
			$this->smarty->assign('requested_category',isset($requestedCategory) ? $requestedCategory : null);
			$this->smarty->assign('content',isset($content['content']) ? $content['content'] : null);
            $this->smarty->assign('sideBarLogos',$sideBarLogos);
            $this->smarty->assign('showMediaUploadLink',$taxon['base_rank_id']>=SPECIES_RANK_ID);
            $this->smarty->assign('categories',$categories['categories']);
            $this->smarty->assign('activeCategory',$categories['start']);
			$this->smarty->assign('taxon',$taxon);
			$this->smarty->assign('classification',$classification);
			$this->smarty->assign('children',$children);
			$this->smarty->assign('names',$names);
			$this->smarty->assign('overviewImage',$this->getTaxonOverviewImage($taxon['id']));
            $this->smarty->assign('headerTitles',array('title'=>$taxon['label'].(isset($taxon['commonname']) ? ' ('.$taxon['commonname'].')' : '')));

	        $this->printPage('taxon');

        }

    }

    public function nameAction()
    {
        if ($this->rHasId())
		{
			$name=$this->getName(array('nameId'=>$this->rGetId()));
			$name['nametype']=sprintf($this->Rdf->translatePredicate($name['nametype']),$name['language_label']);
			$this->smarty->assign('name',$name);
			$this->smarty->assign('taxon',$this->getTaxonById($name['taxon_id']));

		}
        $this->printPage();
    }

	public function getTaxonClassification($id)
	{
		$this->tmp = array();

		$this->_getTaxonClassification($id);

		return $this->tmp;
	}

    public function ajaxInterfaceAction ()
    {
		$return=null;

		if ($this->rHasVal('action', 'get_media_batch') && $this->rHasId())
		{
			$return=json_encode($this->getTaxonMedia(array('id'=>$this->rGetId(),'page'=>$this->rGetVal('page'))));
        } else
		if ($this->rHasVal('action', 'get_collected_batch') && $this->rHasId())
		{
			$return=json_encode($this->getCollectedLowerTaxonMedia(array('id'=>$this->rGetId(),'page'=>$this->rGetVal('page'))));
        }

        $this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage('ajax_interface');
    }

	private function getFirstTaxonIdNsr()
	{
		return $this->models->{$this->_model}->getFirstTaxonIdNsr($this->getCurrentProjectId());
	}
	
	private function parseExternalReference( $p )
	{

		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$reference = isset($p['reference']) ? $p['reference'] : null;

		if ( is_null($taxon) || is_null($reference) ) return;
		
		if ( isset($reference->substitute) )
		{
			foreach((array)$reference->substitute as $key=>$val)
			{
				if ( isset($taxon[$val]) )
				{
					$sval=$taxon[$val];

					if ( isset($reference->substitute_encode) && $reference->substitute_encode!='none' && is_callable( $reference->substitute_encode ) )
					{
						$sval=call_user_func($reference->substitute_encode, $sval );
					}

					$reference->url = str_replace( $key, $sval, $reference->url );
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
					
					if ( !empty($sval) )
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
				if ( isset($taxon[$val]) )
				{
					$sval=$taxon[$val];
				}
				
				if ( !empty($sval) ) 
				{
					if ( isset($reference->parameter_encode) && $reference->parameter_encode!='none' && is_callable( $reference->parameter_encode ) )
					{
						$sval=call_user_func($reference->parameter_encode, $sval );
					}

					$reference->url = str_replace( $key, $sval, $reference->url );

					$query_string .= $key .'=' . rawurlencode( $sval ) . '&';
				}
			}
		}

		$parts=parse_url( $reference->url );

		$full_url=$reference->url . ( !empty($parts['query']) ? '&' : '?' ) . rtrim( $query_string, '&' );
		
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
				'is_empty'=>$is_empty
			);

	}
	
    private function getCategories($p=null)
    {
		$taxon_id = isset($p['taxon']) ? $p['taxon'] : null;
		$baseRank = isset($p['base_rank']) ? $p['base_rank'] : null;
		$requestedTab = isset($p['requestedTab']) ? $p['requestedTab'] : null;

		$categories = $this->models->{$this->_model}->getCategoriesNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'taxonId' => $taxon_id,
    		'languageId' => $this->getCurrentLanguageId(),
    		'hasRedirectTo' => isset($this->models->PagesTaxa->columns['redirect_to']),
    		'hasCheckQuery' => isset($this->models->PagesTaxa->columns['check_query']),
    		'hasAlwaysHide' => isset($this->models->PagesTaxa->columns['always_hide']),
    		'hasExternalReference' => isset($this->models->PagesTaxa->columns['external_reference'])
		));

		if ( isset($taxon_id) )
		{
			$taxon=$this->getTaxonById( $taxon_id );
			
			foreach((array)$categories as $key=>$val)
			{
				if (defined('TAB_NAAMGEVING') && $val['id']==TAB_NAAMGEVING)
				{
					$categories[$key]['is_empty']=true;
				}

				if (defined('TAB_BEDREIGING_EN_BESCHERMING') && $val['id']==TAB_BEDREIGING_EN_BESCHERMING)
				{
					$categories[$key]['is_empty']=true;
					$dummy=$key;
				}

				if ( isset($val['external_reference']) )
				{
					$ref=json_decode( $val['external_reference'] );
					$d=$this->parseExternalReference( array('taxon'=>$taxon,'reference'=>$ref) );
					$ref->full_url=$d['full_url'];
					$categories[$key]['external_reference']=$ref;
					$categories[$key]['is_empty']=$d['is_empty'];
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

			if (!$this->_suppressTab_NAMES)
			{
				array_push($categories,
					array(
						'id' => CTAB_NAMES,
						'title' => $this->translate('Naamgeving'),
						'is_empty' => false,
						'tabname' => 'CTAB_NAMES'
					)
				);
			}

			if (!$this->_suppressTab_LITERATURE)
			{
				array_push($categories,
					array(
						'id' => CTAB_LITERATURE,
						'title' => $this->translate('Literature'),
						'is_empty' => !$this->hasTaxonLiterature($taxon_id),
						'tabname' => 'CTAB_LITERATURE'
					)
				);
			}

			if (!$this->_suppressTab_MEDIA)
			{
				$d=$this->getTaxonMedia(array('id'=>$taxon_id,'limit'=>1));
				if ($d['count']>0)
				{
					$isEmpty=0;
				}
				else
				{
					$d=$this->getCollectedLowerTaxonMedia(array('id'=>$taxon_id));
					$isEmpty=(count((array)$d['data'])==0);
				}

				array_push($categories,
					array(
						'id' => CTAB_MEDIA,
						'title' => $this->translate('Media'),
						'is_empty' => $isEmpty,
						'tabname' => 'CTAB_MEDIA'
					)
				);
			}

			if (!$this->_suppressTab_DNA_BARCODES)
			{
				array_push($categories,
					array(
						'id' => CTAB_DNA_BARCODES,
						'title' => $this->translate('DNA barcodes'),
						'is_empty' => !$this->hasTaxonBarcodes($taxon_id),
						'tabname' => 'CTAB_DNA_BARCODES'
					)
				);
			}
			
			
			//if (!$this->_suppressTab_DNA_DICH_KEY_LINKS)
			{
				array_push($categories,
					array(
						'id' => CTAB_DICH_KEY_LINKS,
						'title' => $this->translate('Key links'),
						'is_empty' => !$this->hasTaxonKeyLinks($taxon_id),
						'tabname' => 'CTAB_DICH_KEY_LINKS'
					)
				);
			}
			
			
			

		}

		$order=$this->models->TabOrder->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'columns'=>'tabname,show_order,start_order',
			'fieldAsIndex'=>'tabname',
			'order'=>'start_order'
		));

		$start=null;
		$firstNonEmpty=null;

		foreach((array)$categories as $key=>$val)
		{
			$categories[$key]['show_order']=isset($order[$val['tabname']]) ? $order[$val['tabname']]['show_order'] : 99;

			if (is_null($firstNonEmpty) && empty($val['is_empty']))
			{
				$firstNonEmpty=$val['id'];
			}

			if (isset($requestedTab) && $val['id']==$requestedTab && empty($val['is_empty']))
			{
				$start=$val['id'];
			}
			else
			if (!isset($requestedTab) && !empty($order[$val['tabname']]['start_order']) &&  empty($val['is_empty']) &&
				(
					is_null($start) ||
					$order[$val['tabname']]['start_order']<$start
				))
			{
				$start=$val['id'];
			}
		}

		$this->customSortArray($categories,array('key' => 'show_order'));

		if (is_null($start)) $start=$firstNonEmpty;

		if ($requestedTab=='external') $start=$requestedTab;

		return array('start'=>$start,'categories'=>$categories);
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
					$nomen='<i>'.$this->addHybridMarker( array('name'=>trim($nomen),'base_rank_id'=>$base_rank_id) ).'</i>';
					$names[$key]['name']=trim($nomen.' '.$val['authorship']);
				}

				$scientific_name=$this->addHybridMarker( array('name'=>trim($val['name']),'base_rank_id'=>$base_rank_id) );
				
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
				'hybrid_marker'=>$this->addHybridMarker( array('base_rank_id'=>$base_rank_id) ),
				'list'=>$names,
				'language_has_preferredname'=>$language_has_preferredname
			);
	}

	private function getActor($id)
	{
		$data=$this->models->Actors->_get(array(
			'id' => array(
				'project_id'=>$this->getCurrentProjectId(),
				'id'=>$id
			)
		));
		return $data[0];
	}

    private function getTaxonOverviewImage($id)
	{
		$d=$this->getTaxonMedia(array('id'=>$id,'sort'=>'_meta4.meta_date desc','limit'=>1,'overview'=>true));

		if ( empty($d['data']) )
		{
			$d=(array)$this->getTaxonMedia(array('id'=>$id,'sort'=>'_meta4.meta_date desc','limit'=>1));
		}

		return !empty($d['data']) ? array_shift($d['data']) : null;
	}

    private function getTaxonMedia($p)
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
			$data[$key]['taxon']=$this->addHybridMarker( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
			$data[$key]['name']=$this->addHybridMarker( array( 'name'=>$val['name'],'base_rank_id'=>$val['base_rank_id'] ) );
			$data[$key]['nomen']=$this->addHybridMarker( array( 'name'=>$val['nomen'],'base_rank_id'=>$val['base_rank_id'] ) );
		}

		return
			array(
				'count'=>$total,
				'data'=>$this->NSRFunctions->formatPictureResults($data),
				'perpage'=>$this->_resPicsPerPage
			);

    }

    private function getCollectedLowerTaxonMedia($p)
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

	private function _getTaxonClassification($id)
	{
		$taxon = $this->models->{$this->_model}->getTaxonNsr(array(
            'projectId' => $this->getCurrentProjectId(),
    		'predicatePreferredNameId' => $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id
		));

		$taxon['name']=$this->addHybridMarker(array('name'=>$taxon['name'],'base_rank_id'=>$taxon['rank_id']));
		$taxon['taxon']=$this->addHybridMarker(array('name'=>$taxon['taxon'],'base_rank_id'=>$taxon['rank_id']));
		$taxon['uninomial']=$this->addHybridMarker(array('uninomial'=>$taxon['uninomial'],'base_rank_id'=>$taxon['rank_id']));
		$taxon['specific_epithet']=$this->addHybridMarker(array('specific_epithet'=>$taxon['specific_epithet'],'base_rank_id'=>$taxon['rank_id']));

		array_unshift($this->tmp,$taxon);

		if (!empty($taxon['parent_id'])) {
			$this->_getTaxonClassification($taxon['parent_id']);
		}

	}

	private function getSpeciesCount($p)
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

	private function getTaxonChildren($p)
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
			$data[$key]['name']=$this->addHybridMarker(array('name'=>$val['name'],'base_rank_id'=>$val['rank_id']));
			$data[$key]['taxon']=$this->addHybridMarker(array('name'=>$val['taxon'],'base_rank_id'=>$val['rank_id']));
			$data[$key]['uninomial']=$this->addHybridMarker(array('uninomial'=>$val['uninomial'],'base_rank_id'=>$val['rank_id']));
			$data[$key]['specific_epithet']=$this->addHybridMarker(array('specific_epithet'=>$val['specific_epithet'],'base_rank_id'=>$val['rank_id']));
	
			if ($include_count)
				$data[$key]['species_count']=$this->getSpeciesCount(array('id'=>$val['id'],'rank'=>$val['rank_id']));
		}

		return $data;
	}

	private function getClassificationSpeciesCount($p)
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

	private function getName($p)
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

		$d['name']=$this->addHybridMarker( array('name'=>$d['name'],'base_rank_id'=>$d['base_rank_id']) );

		$d['addition']=$this->getNameAddition(array('name_id'=>$d['id']));

		if ($d['nametype']==PREDICATE_ALTERNATIVE_NAME)
		{
			$d['alt_alt_nametype_label']=sprintf($this->Rdf->translatePredicate(PREDICATE_PREFERRED_NAME),$d['language_label']);
			$d['language_has_preferredname']=$this->doesLanguageHavePreferredName( $d );
		}

		return $d;
	}

	private function getDNABarcodes($id)
	{
		return $this->models->DnaBarcodes->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id
				),
				'columns' => 'taxon_literal,barcode,location,date_literal,specialist',
				'order' => 'date desc'
			));
	}

	private function getPresenceData($id)
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

		$content=$rdf=null;

		$isPublished=null;

        switch ($category)
		{
            case CTAB_CLASSIFICATION:
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
                $content=
					array(
						'literature'=>$this->getTaxonLiterature($taxon),
						'inherited_literature'=>$this->getInheritedTaxonLiterature($taxon)
					);
                break;

            case CTAB_DNA_BARCODES:
                $content=$this->getDNABarcodes($taxon);
                break;

            case CTAB_DICH_KEY_LINKS:
                $content=$this->getTaxonKeyLinks( $taxon );
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
				$isPublished = isset($content['publish']) ? $content['publish'] : null;

        }

		if (isset($content['id']))
			$rdf=$this->Rdf->getRdfValues($content['id']);

		if (isset($content['content']))
			$content=$content['content'];

		return array('content'=>$content,'rdf'=>$rdf,'isPublished'=>$isPublished);
    }

    private function hasTaxonLiterature($id)
    {
		
		/*
        $d=$this->models->LiteratureTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $id
            ),
			'columns' => 'count(*) as total'
        ));

        return $d[0]['total']>0;
		*/
		$d=$this->getTaxonLiterature( $id );
		
		if (count((array)$d)==0)
		{
			$d=$this->getInheritedTaxonLiterature( $id );
			return count((array)$d)>0;
		}
		else
		{
			return count((array)$d)>0;
		}
		
    }

    private function hasTaxonBarcodes($id)
    {
		$d=$this->models->DnaBarcodes->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id
			),
			'columns' => 'count(*) as total',
			'order'=> 'date'
		));

        return $d[0]['total']>0;
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
		return $this->getTaxonMedia(array('id'=>$id,'distribution_maps'=>true,'sort'=>'meta_datum_plaatsing'));
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

    public function getTaxonById( $id )
    {
        $taxon=parent::getTaxonById( $id );
		return $taxon;
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

    private function hasTaxonKeyLinks( $taxon_id )
    {
		$d=$this->getTaxonKeyLinks( $taxon_id );
        return count((array)$d)>0;
    }


}
