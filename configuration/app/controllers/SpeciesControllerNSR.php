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

class SpeciesControllerNSR extends SpeciesController
{
	private $_resPicsPerPage=12;

    public function __construct()
    {
        parent::__construct();
		$this->initialise();
	}

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->models->Taxon->freeQuery("SET lc_time_names = 'nl_NL'");
		$this->Rdf = new RdfController;
    }

    public function indexAction()
    {
        if (!$this->rHasVal('id'))
			$id = $this->getFirstTaxonIdNsr();
        else
			$id = $this->requestData['id'];

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
        if ($this->rHasId())
		{
            $taxon = $this->getTaxonById($this->rGetVal('id'));
			$taxon['NsrId'] = $this->getNSRId(array('id'=>$this->rGetVal('id')));
		}
		else 
		{
			$this->redirect('nsr_index.php');
		}

        if (!empty($taxon))
		{
			
			$sideBarLogos=array();

			$reqCat=$this->rHasVal('cat') ? $this->rGetVal('cat') : null;

            $categories=$this->getCategories(array('taxon' => $taxon['id'],'base_rank' => $taxon['base_rank_id'],'requestedTab'=>$reqCat));

			$names=$this->getNames($taxon);
			
			$classification=$this->getTaxonClassification($taxon['id']);
			$classification=$this->getClassificationSpeciesCount(array('classification'=>$classification,'taxon'=>$taxon['id']));
			$children=$this->getTaxonChildren(array('taxon'=>$taxon['id'],'include_count'=>true));

			if ($categories['start']==CTAB_MEDIA)
			{
				$this->smarty->assign('search',$this->requestData);	
				$this->smarty->assign('querystring',$this->reconstructQueryString());


//q($this->getTaxonMedia($this->requestData),1);


//				if($taxon['base_rank_id']>=SPECIES_RANK_ID)
				{
					$this->smarty->assign('mediaOwn',$this->getTaxonMedia($this->requestData));	
					//$this->smarty->assign('mediaType','taxon');
				}
//				else
				{
					$this->smarty->assign('mediaCollected',$this->getCollectedHigherTaxonMedia($this->requestData));	
					//$this->smarty->assign('mediaType','collected');
				}
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

			/*
			if ($categories['start']!=CTAB_MEDIA && $categories['start']!=CTAB_DNA_BARCODES)
			{
				$content['content'] = $this->matchGlossaryTerms($content['content']);
				$content['content'] = $this->matchHotwords($content['content']);
			}
			*/

			$this->setPageName($taxon['label']);

			if (isset($content))
			{
				$name=$url=null;	
				foreach((array)$content['rdf'] as $val)
				{
					if ($val['predicate']=='hasPublisher')
					{
						$name=isset($val['data']['name']) ? $val['data']['name'] : null;
						$url=isset($val['data']['homepage']) ? $val['data']['homepage'] : null;
					}
				}

				array_push(
					$sideBarLogos,
					array(
						'organisation'=>$name,
						'logo'=>$this->getOrganisationLogoUrl($name),
						'url'=>$url
					)
				);
						
				
				$this->smarty->assign('content',$content['content']);
				$this->smarty->assign('rdf',$content['rdf']);
			}
			
            $this->smarty->assign('sideBarLogos',$sideBarLogos);
            $this->smarty->assign('showMediaUploadLink',$taxon['base_rank_id']>=SPECIES_RANK_ID);
            $this->smarty->assign('categories',$categories['categories']);
            $this->smarty->assign('activeCategory',$categories['start']);
			$this->smarty->assign('taxon',$taxon);
			$this->smarty->assign('classification',$classification);
			$this->smarty->assign('children',$children);
			$this->smarty->assign('names',$names);
			$this->smarty->assign('overviewImage', $this->getTaxonOverviewImage($taxon['id']));
            $this->smarty->assign('headerTitles', array('title'=>$taxon['label'].(isset($taxon['commonname']) ? ' ('.$taxon['commonname'].')' : '')));

        } else {
            
            $this->addError($this->translate('No or unknown taxon ID specified.'));
        }
        
        $this->printPage('taxon');

    }

    public function nameAction()
    {
        if ($this->rHasId())
		{
			$d=$this->getName(array('nameId'=>$this->requestData['id']));
			$name=$d[0];
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
	
	private function getFirstTaxonIdNsr()
	{

		$data=$this->models->Taxon->freeQuery("
			select
				_a.id,
				_a.taxon
			
			from %PRE%taxa _a

			where
				_a.project_id =".$this->getCurrentProjectId()."
			order by _a.taxon
			limit 1"
		);
		
		return $data[0]['id'];

	}

    private function getCategories($p=null)
    {
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$baseRank = isset($p['base_rank']) ? $p['base_rank'] : null;
		$requestedTab = isset($p['requestedTab']) ? $p['requestedTab'] : null;

		$categories=$this->models->PageTaxon->freeQuery("
			select
				_a.id,
				ifnull(_b.title,_a.page) as title,
				concat('TAB_',replace(upper(_a.page),' ','_')) as tabname,
				_a.show_order,
				".(isset($taxon) ? "if(length(_c.content)>0 && _c.publish=1,0,1) as is_empty, " : "")."
				_a.def_page

			from 
				%PRE%pages_taxa _a
				
			left join %PRE%pages_taxa_titles _b
				on _a.project_id=_b.project_id
				and _a.id=_b.page_id
				and _b.language_id = ". $this->getCurrentLanguageId() ."
				
			".(isset($taxon) ? "
				left join %PRE%content_taxa _c
					on _a.project_id=_c.project_id
					and _a.id=_c.page_id
					and _c.taxon_id =".$taxon."
					and _c.language_id = ". $this->getCurrentLanguageId() ."
				
				" : "")."

			where 
				_a.project_id=".$this->getCurrentProjectId()."

			order by 
				_a.show_order
		");

		if (!$categories) $categories=array();

		if (isset($taxon))
		{

			if (defined('TAB_VERSPREIDING'))
			{
				$d=$this->getTaxonContent(array('category'=>TAB_VERSPREIDING,'taxon'=>$taxon));
	
				if (!is_null($this->getPresenceData($taxon)) || !is_null($d['content']))
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

			foreach((array)$categories as $key=>$val)
			{
				if (defined('TAB_NAAMGEVING') && $val['id']==TAB_NAAMGEVING)
					$categories[$key]['is_empty']=true;
					
				if (defined('TAB_BEDREIGING_EN_BESCHERMING') && $val['id']==TAB_BEDREIGING_EN_BESCHERMING)
					$dummy=$key;
			}
			
			// TAB_BEDREIGING_EN_BESCHERMING check at EZ
			if (isset($dummy) && isset($categories[$dummy]['is_empty']) && $categories[$dummy]['is_empty']==1)
			{
				$ezData=$this->getEzData($taxon);
				$categories[$dummy]['is_empty']=empty($ezData);
			}

			if (!$this->_suppressTab_LITERATURE)
			{
				array_push($categories,
					array(
						'id' => CTAB_LITERATURE, 
						'title' => $this->translate('Literature'), 
						'is_empty' => !$this->hasTaxonLiterature($taxon),
						'tabname' => 'CTAB_LITERATURE'
					)
				);
			}

			if (!$this->_suppressTab_MEDIA)
			{
				/*
					species & lower should always show the media tab, even
					if there is no media, to be able to show the upload link
				*/
				if (isset($baseRank) && $baseRank>=SPECIES_RANK_ID)
				{
					$isEmpty=false;
				}
				else
				{

					$d=$this->getTaxonMedia(array('id'=>$taxon,'limit'=>1));
					if ($d['count']>0)
					{
						$isEmpty=0;
					}
					else
					{
						$d=$this->getCollectedHigherTaxonMedia(array('id'=>$taxon));
						$isEmpty=(count((array)$d['data'])==0);
					}
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
						'is_empty' => !$this->hasTaxonBarcodes($taxon),
						'tabname' => 'CTAB_DNA_BARCODES'
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
				$firstNonEmpty=$val['id'];

			if (isset($requestedTab) && $val['id']==$requestedTab && empty($val['is_empty'])) {
				$start=$val['id'];
			} else
			if (is_null($start) && !empty($order[$val['tabname']]['start_order']) && empty($val['is_empty'])) {
				$start=$val['id'];
			}
		}
		
		$this->customSortArray($categories,array('key' => 'show_order'));

		if (is_null($start)) $start=$firstNonEmpty;

		return array('start'=>$start,'categories'=>$categories);

    }

	private function getNames($p)
	{	
		$id=isset($p['id']) ? $p['id'] : null;
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;

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
						and _d.label_language_id=".$this->getDefaultLanguageId()."

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
		$scientific_name=null;
		$nomen=null;

		foreach((array)$names as $key=>$val)
		{
			if ($val['nametype']==PREDICATE_PREFERRED_NAME && $val['language_id']==$this->getDefaultLanguageId())
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
				
				if ($base_rank_id>=GENUS_RANK_ID)
				{
					$nomen='<i>'.trim($nomen).'</i>';
					$names[$key]['name']=trim($nomen.' '.$val['authorship']);
				}
				else
				{
					$scientific_name=trim($val['name']);
				}
			}
		}

		return
			array(
				'scientific_name'=>$scientific_name,
				'nomen'=>$nomen,
				'nomen_no_tags'=>trim(strip_tags($nomen)),
				'preffered_name'=>$prefferedname,
				'list'=>$names
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
		$d=(array)$this->getTaxonMedia(array('id'=>$id,'sort'=>'_meta4.meta_date desc','limit'=>1));
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

		$data=$this->models->Taxon->freeQuery("		
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				file_name as image,
				file_name as thumb,
				_k.taxon,
				_z.name as common_name,
				_j.name,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,
				".($distributionMaps?
					"_map1.meta_data as meta_map_source,
					 _map2.meta_data as meta_map_description,": "")."
				date_format(_meta1.meta_date,'%e %M %Y') as meta_datum,
				_meta2.meta_data as meta_short_desc,
				_meta3.meta_data as meta_geografie,
				date_format(_meta4.meta_date,'%e %M %Y') as meta_datum_plaatsing,
				_meta5.meta_data as meta_copyrights,
				_meta6.meta_data as meta_validator,
				_meta7.meta_data as meta_adres_maker,
				_meta8.meta_data as photographer
			
			from  %PRE%media_taxon _m
			
			left join %PRE%media_meta _c
				on _m.project_id=_c.project_id
				and _m.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'
			
			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id
				
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%names _z
				on _m.taxon_id=_z.taxon_id
				and _m.project_id=_z.project_id
				and _z.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _z.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."
				

			left join %PRE%media_meta _map1
				on _m.id=_map1.media_id
				and _m.project_id=_map1.project_id
				and _map1.sys_label='verspreidingsKaartBron'

			left join %PRE%media_meta _map2
				on _m.id=_map2.media_id
				and _m.project_id=_map2.project_id
				and _map2.sys_label='verspreidingsKaartTitel'

			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'

			left join %PRE%media_meta _meta2
				on _m.id=_meta2.media_id
				and _m.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijving'
			
			left join %PRE%media_meta _meta3
				on _m.id=_meta3.media_id
				and _m.project_id=_meta3.project_id
				and _meta3.sys_label='beeldbankLokatie'
			
			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'
			
			left join %PRE%media_meta _meta5
				on _m.id=_meta5.media_id
				and _m.project_id=_meta5.project_id
				and _meta5.sys_label='beeldbankCopyright'

			left join %PRE%media_meta _meta6
				on _m.id=_meta6.media_id
				and _m.project_id=_meta6.project_id
				and _meta6.sys_label='beeldbankValidator'
				and _meta6.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta8
				on _m.id=_meta8.media_id
				and _m.project_id=_meta8.project_id
				and _meta8.sys_label='beeldbankFotograaf'
			
			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'
			
			where
				_m.project_id=".$this->getCurrentProjectId()."
				and _m.taxon_id=".$id."
				and ifnull(_meta9.meta_data,0)!=".($distributionMaps?'0':'1')."
				".($overview ? "and _m.overview_image=1" : "")."


			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);
		
		$count=$this->models->MediaTaxon->freeQuery('select found_rows() as total');
		
		foreach((array)$data as $key=>$val)
		{

			$metaData=array(
				'' => '<span class="pic-meta-label">'.(!empty($val['common_name']) ? $val['common_name'].' (<i>'.$val['nomen'].'</i>)' : '<i>'.$val['nomen'].'</i>').'</span>',
				'Fotograaf' => $val['photographer'],
				'Datum' => $val['meta_datum'],
				'Locatie' => $val['meta_geografie'],
				'Validator' => $val['meta_validator'],
				'Geplaatst op' => $val['meta_datum_plaatsing'],
				'Copyright' => $val['meta_copyrights'],
				'Contactadres fotograaf' => $val['meta_adres_maker'],
				'Omschrijving' => $val['meta_short_desc'],
				
			);

			$data[$key]['photographer']=$val['photographer'];
			$data[$key]['label']=
				trim(
					(isset($val['photographer']) ? $val['photographer'].', ' : '' ).
					(isset($val['meta_datum']) ? $val['meta_datum'].', ' : '' ).
					(isset($val['meta_geografie']) ? $val['meta_geografie'] : ''),
					', '
				);
				
				
			$data[$key]['meta_data']=$this->helpers->Functions->nuclearImplode('</span>: ','<br /><span class="pic-meta-label">',$metaData,true);
			
		}

		return array('count'=>$count[0]['total'],'data'=>$data,'perpage'=>$this->_resPicsPerPage);

    }

    private function getCollectedHigherTaxonMedia($p)
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;

		if (empty($id))
			return;
	
		$data=$this->models->Taxon->freeQuery("		
			select
				SQL_CALC_FOUND_ROWS
				_q.taxon_id,
				_m.file_name as image,
				_m.file_name as thumb,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as taxon,
				_z.name,
				date_format(_meta1.meta_date,'%e %M %Y') as meta_datum,
				_meta2.meta_data as meta_short_desc,
				_meta3.meta_data as meta_geografie,
				date_format(_meta4.meta_date,'%e %M %Y') as meta_datum_plaatsing,
				_meta5.meta_data as meta_copyrights,
				_meta6.meta_data as meta_validator,
				_meta7.meta_data as meta_adres_maker,
				_meta8.meta_data as photographer
			
			from
				%PRE%taxon_quick_parentage _q
			
			right join %PRE%media_taxon _m
				on _q.taxon_id=_m.taxon_id
				and _q.project_id=_m.project_id
				and _m.id = (
					select 
						_m.id
					from
						%PRE%media_taxon _m

					left join %PRE%media_meta _meta4
						on _m.id=_meta4.media_id
						and _m.project_id=_meta4.project_id
						and _meta4.sys_label='beeldbankDatumAanmaak'

					left join %PRE%media_meta _meta9
						on _m.id=_meta9.media_id
						and _m.project_id=_meta9.project_id
						and _meta9.sys_label='verspreidingsKaart'
						
					where 
						_m.taxon_id = _q.taxon_id 
						and ifnull(_meta9.meta_data,0)!=1
						and _m.project_id=".$this->getCurrentProjectId()." 
					order by
						_meta4.meta_date desc
					limit 1
				)

			left join %PRE%taxa _k
				on _q.taxon_id=_k.id
				and _q.project_id=_k.project_id
				
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%names _z
				on _q.taxon_id=_z.taxon_id
				and _q.project_id=_z.project_id
				and _z.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _z.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'

			left join %PRE%media_meta _meta2
				on _m.id=_meta2.media_id
				and _m.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijving'
			
			left join %PRE%media_meta _meta3
				on _m.id=_meta3.media_id
				and _m.project_id=_meta3.project_id
				and _meta3.sys_label='beeldbankLokatie'
			
			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'
			
			left join %PRE%media_meta _meta5
				on _m.id=_meta5.media_id
				and _m.project_id=_meta5.project_id
				and _meta5.sys_label='beeldbankCopyright'

			left join %PRE%media_meta _meta6
				on _m.id=_meta6.media_id
				and _m.project_id=_meta6.project_id
				and _meta6.sys_label='beeldbankValidator'
				and _meta6.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta8
				on _m.id=_meta8.media_id
				and _m.project_id=_meta8.project_id
				and _meta8.sys_label='beeldbankFotograaf'
			
			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'
		
			where
				_q.project_id=".$this->getCurrentProjectId()."
				and (MATCH(_q.parentage) AGAINST ('".$id."' in boolean mode))

			order by taxon
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);
//				and _f.rank_id >= ".SPECIES_RANK_ID."


		foreach((array)$data as $key=>$val)
		{
			$metaData=array(
				'' => (!empty($val['common_name']) ? $val['common_name'].' (<i>'.$val['nomen'].'</i>)' : '<i>'.$val['nomen'].'</i>'),
				'Fotograaf' => $val['photographer'],
				'Datum' => $val['meta_datum'],
				'Locatie' => $val['meta_geografie'],
				'Validator' => $val['meta_validator'],
				'Geplaatst op' => $val['meta_datum_plaatsing'],
				'Copyright' => $val['meta_copyrights'],
				'Contactadres fotograaf' => $val['meta_adres_maker'],
				'Omschrijving' => $val['meta_short_desc'],
				
			);

			$data[$key]['photographer']=$val['photographer'];
			$data[$key]['label']=
				trim(
					(isset($val['photographer']) ? $val['photographer'].', ' : '' ).
					(isset($val['meta_datum']) ? $val['meta_datum'].', ' : '' ).
					(isset($val['meta_geografie']) ? $val['meta_geografie'] : ''),
					', '
				);
			$data[$key]['meta_data']=$this->helpers->Functions->nuclearImplode(': ','<br />',$metaData,true);
			
		}

		$count=$this->models->Taxon->freeQuery('select found_rows() as total');
		
		/*
		$totalCount=$this->models->Taxon->freeQuery("		
			select
				count(*) as total
			
			from
				%PRE%taxon_quick_parentage _q
			
			right join %PRE%media_taxon _m
				on _q.taxon_id=_m.taxon_id
				and _q.project_id=_m.project_id

			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			left join %PRE%taxa _k
				on _q.taxon_id=_k.id
				and _q.project_id=_k.project_id
				
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			where
				_q.project_id=".$this->getCurrentProjectId()."
				and ifnull(_meta9.meta_data,0)!=1
				and (MATCH(_q.parentage) AGAINST ('".$id."' in boolean mode))
			"
		);
//				and _f.rank_id >= ".SPECIES_RANK_ID."
		*/

		$species=$this->models->Taxon->freeQuery("		
			select
				count(distinct _m.taxon_id) as total
			
			from
				%PRE%taxon_quick_parentage _q
			
			right join %PRE%media_taxon _m
				on _q.taxon_id=_m.taxon_id
				and _q.project_id=_m.project_id

			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			left join %PRE%taxa _k
				on _q.taxon_id=_k.id
				and _q.project_id=_k.project_id
				
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			where
				_q.project_id=".$this->getCurrentProjectId()."
				and ifnull(_meta9.meta_data,0)!=1
				and (MATCH(_q.parentage) AGAINST ('".$id."' in boolean mode))
			"
		);
//				and _f.rank_id >= ".SPECIES_RANK_ID."
		
		$data= 
			array(
				'count'=>$count[0]['total'], // number of images, one per taxon in this branch
//				'totalCount'=>$totalCount[0]['total'], // all images in this branch
				'species'=>$species[0]['total'], // number taxa in this branch
				'data'=>$data,
				'perpage'=>$this->_resPicsPerPage
			);

		return $data;
	}

	private function _getTaxonClassification($id)
	{
		$taxon=$this->models->Taxon->freeQuery("
			select
				_a.id,
				_a.taxon,
				_a.parent_id,
				trim(if(_m.authorship is null,_m.name,replace(_m.name,_m.authorship,''))) as name,
				_m.uninomial,
				_m.specific_epithet,
				_m.infra_specific_epithet,
				_m.authorship,
				_f.rank_id,
				_f.lower_taxon,
				_g.label as rank,
				_q.label as rank_label,
				_k.name as common_name
			
			from %PRE%taxa _a

			left join %PRE%names _m
				on _a.id=_m.taxon_id
				and _a.project_id=_m.project_id
				and _m.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _m.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%labels_projects_ranks _g
				on _a.rank_id=_g.project_rank_id
				and _a.project_id = _g.project_id
				and _g.language_id=". LANGUAGE_ID_SCIENTIFIC ."

			left join %PRE%labels_projects_ranks _q
				on _a.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$this->getCurrentLanguageId()."

			where
				_a.project_id =".$this->getCurrentProjectId()."
				and _a.id=".$id."
			"
		);

		array_unshift($this->tmp,$taxon[0]);
	
		if (!empty($taxon[0]['parent_id'])) {
			$this->_getTaxonClassification($taxon[0]['parent_id']);
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
		
		$data=$this->models->Taxon->freeQuery(array(
			'query'=>"
				select
					count(_sq.taxon_id) as total,
					_sq.taxon_id,
					_sp.presence_id,
					ifnull(_sr.established,'undefined') as established
				from 
					%PRE%taxon_quick_parentage _sq
				
				left join %PRE%presence_taxa _sp
					on _sq.project_id=_sp.project_id
					and _sq.taxon_id=_sp.taxon_id
				
				left join %PRE%presence _sr
					on _sp.project_id=_sr.project_id
					and _sp.presence_id=_sr.id

				left join %PRE%taxa _e
					on _sq.taxon_id = _e.id
					and _sq.project_id = _e.project_id
				
				left join %PRE%projects_ranks _f
					on _e.rank_id=_f.id
					and _e.project_id = _f.project_id
				
				where
					_sq.project_id=".$this->getCurrentProjectId()."
					and MATCH(_sq.parentage) AGAINST ('".$id."' in boolean mode)
					and _sp.presence_id is not null
					and _f.rank_id".($rank>=SPECIES_RANK_ID ? ">=" : "=")." ".SPECIES_RANK_ID."
					
				group by _sr.established",
			'fieldAsIndex'=>'established'
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

		$data=$this->models->Taxon->freeQuery("
			select
				_a.id,
				_a.taxon,
				if (
					length(
						trim(
							concat(
								if(_k.uninomial is null,'',concat(_k.uninomial,' ')),
								if(_k.specific_epithet is null,'',concat(_k.specific_epithet,' ')),
								if(_k.infra_specific_epithet is null,'',concat(_k.infra_specific_epithet,' '))
							)
						)
					)=0,
					_k.name,
					trim(
						concat(
							if(_k.uninomial is null,'',concat(_k.uninomial,' ')),
							if(_k.specific_epithet is null,'',concat(_k.specific_epithet,' ')),
							if(_k.infra_specific_epithet is null,'',concat(_k.infra_specific_epithet,' '))
						)
					)
				) as name,
				_f.rank_id,
				_g.label as rank_label
			
			from %PRE%taxa _a

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _k.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%labels_projects_ranks _g
				on _a.rank_id=_g.project_rank_id
				and _a.project_id = _g.project_id
				and _g.language_id=". LANGUAGE_ID_DUTCH."

			where
				_a.project_id =".$this->getCurrentProjectId()."
				and _a.parent_id = ".$id."
			order by _a.taxon
		");

		//q($this->models->MediaTaxon->q(),1);
		//q($data,1);

		if ($include_count) {
		
			foreach((array)$data as $key=>$val)
			{
				$data[$key]['species_count']=$this->getSpeciesCount(array('id'=>$val['id'],'rank'=>$val['rank_id']));
			}		

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

		return $this->models->Names->freeQuery("
			select
				_a.taxon_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
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
				_g.date as reference_date
			from %PRE%names _a

			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id
				
			left join %PRE%languages _c 
				on _a.language_id=_c.id
				
			left join %PRE%labels_languages _d 
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id 
				and _d.label_language_id=".$languageId."

			left join %PRE%actors _e
				on _a.expert_id = _e.id 
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.organisation_id = _f.id 
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id 
				and _a.project_id=_g.project_id
	
			where _a.project_id = ".$this->getCurrentProjectId().
			(!empty($nameId) ? " and _a.id=".$nameId : "").				
			(!empty($taxonId) ? " and _a.taxon_id=".$taxonId : "").				
			(!empty($predicateType) ? " and _b.nametype=".$predicateType : "")
		);
		
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
		$data=$this->models->PresenceTaxa->freeQuery(
			"select
				ifnull(_a.is_indigenous,0) as is_indigenous,
				_a.presence_id,
				_a.presence82_id,
				_a.reference_id,
				_b.label as presence_label,
				_b.information as presence_information,
				_b.information_title as presence_information_title,
				_b.index_label as presence_index_label,
				_c.label as presence82_label,
				_d.label as habitat_label,
				_e.name as expert_name,
				_f.name as organisation_name,
				_g.label as reference_label,
				_g.author as reference_author,
				_g.date as reference_date
				
			from %PRE%presence_taxa _a

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id 
				and _a.project_id=_b.project_id 
				and _b.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%presence_labels _c
				on _a.presence82_id = _c.presence_id 
				and _a.project_id=_c.project_id 
				and _c.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%habitat_labels _d
				on _a.habitat_id = _d.habitat_id 
				and _a.project_id=_d.project_id 
				and _d.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%actors _e
				on _a.actor_id = _e.id 
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.actor_org_id = _f.id 
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id 
				and _a.project_id=_g.project_id

			where _a.project_id = ".$this->getCurrentProjectId()."
				and _a.taxon_id =".$id
		);	
		
		$data[0]['presence_information_one_line']=str_replace(array("\n","\r","\r\n"),'<br />',$data[0]['presence_information']);
		
		return $data[0];
	}

	private function getTrendData($id)
	{
		$byYear=$this->models->TaxonTrendYears->freeQuery("
			select 
				_a.trend_year,
				_a.trend,
				_b.source
			from %PRE%taxon_trend_years _a
			
			left join %PRE%trend_sources _b
				on _a.project_id=_b.project_id
				and _a.source_id=_b.id
			
			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.taxon_id = ".$id." 
			order by _a.trend_year
		");


		$byTrend=$this->models->TaxonTrends->freeQuery("
			select 
				_a.trend_label,
				_a.trend,
				_b.source
			from %PRE%taxon_trends _a
			
			left join %PRE%trend_sources _b
				on _a.project_id=_b.project_id
				and _a.source_id=_b.id
			
			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.taxon_id = ".$id." 
			order by _a.trend_label
		");		

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

        switch ($category)
		{
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
            
            case CTAB_DNA_BARCODES:
                $content=$this->getDNABarcodes($taxon);
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
                
                $ct = $this->models->ContentTaxon->_get(array(
                    'id' => $d, 
                ));

				$content = isset($ct) ? $ct[0] : null;

        }

		if (isset($content['id']))
			$rdf=$this->Rdf->getRdfValues($content['id']);

		if (isset($content['content']))
			$content=$content['content'];

		return array('content'=>$content,'rdf'=>$rdf);
    }

    private function hasTaxonLiterature($id)
    {
        $d=$this->models->LiteratureTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            ),
			'columns' => 'count(*) as total'
        ));
        
        return $d[0]['total']>1;
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
			foreach($exts as $ext)
			{
				array_push($d,
					$a.'.'.$ext,
					strtolower($a).'.'.$ext,
					$a.'_logo.'.$ext,
					strtolower($a).'_logo.'.$ext,
					$b.'.'.$ext,
					strtolower($b).'.'.$ext,
					$b.'-logo.'.$ext,
					strtolower($b).'-logo.'.$ext
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

		$t=$this->models->ExternalOrgs->freeQuery("
			select
				name,
				organisation_url,
				general_url,
				service_url,
				external_id
			from %PRE%external_orgs _a

			right join %PRE%external_ids _b
				on _a.project_id=_b.project_id
				and _a.id=_b.org_id
				and _b.taxon_id=".$id."

			where 
				_a.project_id = ".$this->getCurrentProjectId()." 
				and lower(_a.name) = '". mysql_real_escape_string($org) ."'
		");
		
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

		$t=$this->models->ExternalOrgs->freeQuery("
			select
				name,
				organisation_url,
				general_url,
				service_url
			from %PRE%external_orgs
			where 
				project_id = ".$this->getCurrentProjectId()." 
				and lower(name) = '". mysql_real_escape_string($org) ."'
		");

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
			$data=json_decode(file_get_contents(sprintf($org['service_url'],$this->getNSRId(array('id'=>$id)))));

			if (isset($data))
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
			$data=json_decode(file_get_contents(sprintf($org['service_url'],$this->getNSRId(array('id'=>$id)))));

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
		return $this->getTaxonMedia(array('id'=>$id,'distribution_maps'=>true,'sort'=>'meta_datum_plaatsing','limit'=>1));
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

}
