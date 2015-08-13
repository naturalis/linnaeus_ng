<?php

/*
	notes:

	augustus 2014
	'endemisch' veranderd naar 'inheems', en vervolgens verwijderd, op
	verzoek van roy kleukers en ed colijn. het veld is een overblijfsel
	van een uiteindelijk niet geïmplementeerde aanpasing door trezorix.
	(betreft invoerveld in taxon en taxon_new, plus de verwerking van de
	waarde in updateConcept() -> updateConceptIsIndigeous())
	

*/

include_once ('NsrController.php');

class NsrTaxonImagesController extends NsrController
{
	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'name_types',
		'nsr_ids',
		'media_taxon',
		'media_meta',
		'trash_can',
    );
    public $usedHelpers = array('csv_parser_helper');
    public $cacheFiles = array(
    );
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_beheer.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_beheer.js'
        )
    );
    public $controllerPublicName = 'Soortenregister beheer';
    public $includeLocalMenu = false;
	private $_nameTypeIds;
	private $conceptId=null;
	private $_resPicsPerPage=100;
	private $sys_label_NSR_ID='NSR ID';
	private $sys_label_file_name='file name';

    private 
		$_mime_types = array(          
			'png' => 'image/png', 
			'jpe' => 'image/jpeg', 
			'jpeg' => 'image/jpeg', 
			'jpg' => 'image/jpeg', 
			'gif' => 'image/gif', 
			'bmp' => 'image/bmp', 
			'ico' => 'image/vnd.microsoft.icon', 
			'tiff' => 'image/tiff', 
			'tif' => 'image/tiff', 
			'svg' => 'image/svg+xml', 
			'svgz' => 'image/svg+xml', 
		); 

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
		$this->availableMetaDataFields=$this->models->MediaMeta->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'distinct sys_label',
			'order'=>'sys_label'
		));

		array_unshift($this->availableMetaDataFields,array('sys_label'=>''));
		array_unshift($this->availableMetaDataFields,array('sys_label'=>$this->sys_label_NSR_ID));
		array_unshift($this->availableMetaDataFields,array('sys_label'=>$this->sys_label_file_name));
		
		$this->_taxon_main_image_base_url = $this->getSetting( "taxon_main_image_base_url", "http://images.naturalis.nl/comping/" );
		$this->smarty->assign( 'taxon_main_image_base_url',$this->_taxon_main_image_base_url );
	}

    public function imagesAction()
    {
		$this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect('taxon_new.php');
		
		if ($this->rHasId() && $this->rHasVal('image') && $this->rHasVal('action','delete'))
		{
			$this->setConceptId( $this->rGetId() );
			$this->disconnectTaxonMedia($this->rGetVal('image'));
			$this->setMessage('Afbeelding ontkoppeld.');
		} 
		
		$this->setConceptId( $this->rGetId() );
        $this->setPageName($this->translate('Taxon images'));
		$this->smarty->assign('concept',$this->getConcept($this->rGetVal('id')));
		$this->smarty->assign('images',$this->getTaxonMedia());

		$this->checkMessage();
		$this->printPage();
	}

    public function imageDataAction()
    {
		$this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect('taxon_new.php');

        $this->setPageName($this->translate('Meta-data'));
		
		if ($this->rHasId() && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->updateTaxonImageMetaData( $this->requestData );
			$this->updateTaxonImageTaxonId( $this->requestData );
			$this->updateTaxonImageOverviewState( $this->requestData );
			$this->addMessage( 'Meta-data opgeslagen.' );
		}

		$image=$this->getTaxonMedia(array('media_id'=>$this->rGetId()));
		$meta=$this->getTaxonMediaMetaDataFields();

		foreach((array)$image['data'][0]['meta'] as $val)
		{
			unset($meta[array_search($val['sys_label'],$meta)]);;
		}
		
		$this->smarty->assign('image',$image);
		$this->smarty->assign('meta_rest',$meta);
	
		$this->printPage();
	}

    public function imageMetaBulkAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Meta-data bulk'));
		
		$raw=null;
		$ignorefirst=false;
		$lines=null;
		$emptycols=null;
		$fields=null;
		$matches=null;
		$checks=null;
		$firstline=null;

		$ignorefirst=$this->rHasVal('ignorefirst','1');
		$this->setSessionVar('ignorefirst',$ignorefirst);

		if ($this->rHasVal('fields'))
		{
			$fields=$this->rGetVal('fields');
			$this->setSessionVar('fields',$fields);
		}
		
		if ($this->rHasVal('raw'))
		{
			$raw=$this->rGetVal('raw');
			$hash=md5($raw);
			if ($hash!=$this->getSessionVar('hash'))
			{
				$this->setSessionVar('match_ref',null);
				$this->setSessionVar('new_ref',null);
				$fields=null;
			}
			$this->setSessionVar('hash',$hash);
			
			$lines=$this->parseRawCsvData($raw);

			if (!$ignorefirst) $firstline=null;
			
			foreach($lines as $key=>$val)
			{
				if ($key==0)
				{
					foreach($val as $c=>$cell) $emptycols[$c]=true;
				}

				if ($ignorefirst && $key==0) 
				{
					$firstline=$val;
					continue;
				}

				foreach($val as $c=>$cell)
				{
					if (strlen(trim($cell))!=0)
					{
						$emptycols[$c]=false;
					}
				}
			}
		}
		
		if ($lines && $fields) 
		{
			$this->setSessionVar( 'lines', $lines );
			
			$this->setSessionVar( $this->sys_label_NSR_ID );
			$this->setSessionVar( $this->sys_label_file_name );

			$assignedMetaFields=0;

			foreach((array)$lines[0] as $c=>$cell)
			{
				if(isset($fields[$c]) && $fields[$c]==$this->sys_label_NSR_ID)
				{
					$this->setSessionVar( $this->sys_label_NSR_ID ,$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]==$this->sys_label_file_name)
				{
					$this->setSessionVar( $this->sys_label_file_name ,$c);
				}
				else
				{
					if (!empty($fields[$c]))
					{
						$assignedMetaFields++;
					}
				}
			}
			
			if ( is_null($this->getSessionVar( $this->sys_label_NSR_ID )) )
			{
				$this->addError( 'Must have a NSR ID-column.' );
			}

			if ( is_null($this->getSessionVar( $this->sys_label_file_name )) )
			{
				$this->addError( 'Must have a file name-column.' );
			}

			if ( $assignedMetaFields==0 )
			{
				$this->addError( 'You have assigned no meta-data-columns.' );
			}

			if
			( 
				!is_null($this->getSessionVar( $this->sys_label_NSR_ID )) && 
				!is_null($this->getSessionVar( $this->sys_label_file_name )) && 
				$assignedMetaFields>0
			)
			{
				$matches=$this->matchNsrIds(array('lines'=>$lines,'ignorefirst'=>$ignorefirst));
				$this->setSessionVar('matches',$matches);

				$checks=$this->fieldFormatChecks(array('lines'=>$lines,'fields'=>$fields,'ignorefirst'=>$ignorefirst));
				$this->setSessionVar('checks',$checks);
			}

		}
		
		$this->smarty->assign('col_NSR_ID',$this->getSessionVar( $this->sys_label_NSR_ID ));
		$this->smarty->assign('col_file_name',$this->getSessionVar( $this->sys_label_file_name ));
		$this->smarty->assign('checks',$checks);
		$this->smarty->assign('matches',$matches);
		$this->smarty->assign('fields',$fields);
		$this->smarty->assign('cols',$this->availableMetaDataFields);
		$this->smarty->assign('raw',$raw);
		$this->smarty->assign('ignorefirst',$ignorefirst);
		$this->smarty->assign('firstline',$firstline);
		$this->smarty->assign('lines',$lines);

		$this->printPage();
	}

    public function imageMetaBulkSaveAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Meta-data bulk'));


		$ignorefirst=$this->getSessionVar( 'ignorefirst' );
		$lines=$this->getSessionVar( 'lines' );

		$fields=$this->getSessionVar( 'fields' );
		$checks=$this->getSessionVar( 'checks' );
		$matches=$this->getSessionVar( 'matches' );

		$col_nsr_id=$this->getSessionVar( $this->sys_label_NSR_ID );
		$col_file_name=$this->getSessionVar( $this->sys_label_file_name );


			
		foreach((array)$lines as $key=>$line)
		{
			if ($ignorefirst && $key==0) continue;
			
			$filename=trim($line[$col_file_name]);
			
			if ( !is_null($matches['taxa'][$key]) && !empty($filename) )
			{
				$d=
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'taxon_id' => $matches['taxa'][$key]['taxon_id'], 
						'file_name' => $filename, 
						'original_name' => $filename,
						'mime_type' => @$this->_mime_types[pathinfo($filename, PATHINFO_EXTENSION)], 
						'sort_order' => 99
					);

				$mt=$this->models->MediaTaxon->save($d);

				if ($mt==1) 
				{
					
					$media_id=$this->models->MediaTaxon->getNewId();
					
					$concatfields=array();
					
					foreach((array)$line as $c=>$cell)
					{
						if ( isset($checks[$key][$c]) && $checks[$key][$c]['pass']==false) continue;

						if (isset($checks[$key][$c]))
						{
							$cell=
								sprintf('%s-%s-%s %02d:%02d:%02d',
									$checks[$key][$c]['data']['year'],
									$checks[$key][$c]['data']['month'],
									$checks[$key][$c]['data']['day'],
									$checks[$key][$c]['data']['hour'],
									$checks[$key][$c]['data']['minute'],
									$checks[$key][$c]['data']['second']
								);
						}

						
						if ( !empty($fields[$c]) && $c!=$col_file_name && $c!=$col_nsr_id)
						{
							if (!isset($concatfields[$fields[$c]]))
							{
								$concatfields[$fields[$c]]=$cell;
							}
							else
							{
								$concatfields[$fields[$c]]=$concatfields[$fields[$c]] . ( !empty($cell) ? ", ". $cell : "" );
							}
						}
						

					}
					
					foreach((array)$concatfields as $label=>$val)
					{
						$d=array(
							'project_id'=>$this->getCurrentProjectId(),
							'media_id'=>$media_id,
							'language_id'=>$this->getDefaultProjectLanguage(),
							'sys_label'=>$label,
							'meta_data'=>$val,
						);
			
						if( stripos($label,'datum') )
						{
							$d['meta_date']="#'".$val."'";
							$d['meta_number']=null;
							$d['meta_data']=null;
						}			
						else
						{
							$d['meta_date']=null;
							$d['meta_number']=null;
							$d['meta_data']=$val;
						}
						
						$this->models->MediaMeta->save( $d );
						
						q( $this->models->MediaMeta->q());
					}

					$this->addMessage( sprintf('Wrote %s.',$line[$col_file_name]) );
				}
				else
				{
					$this->addError( $mt );
				}

			}
			else
			{
				$this->addError( sprintf('Ignored line %s: couldn\' resolve NSR ID "%s".', $key+1, $line[$col_nsr_id]) );
			}

		}

		$this->printPage();
	}






	private function setConceptId($id)
	{
		$this->conceptId=$id;
	}

	private function getConceptId()
	{
		return isset($this->conceptId) ? $this->conceptId : false;
	}

    private function getTaxonMedia( $p=null )
    {
		$id=isset($p['id']) ? $p['id'] : $this->getConceptId();
		$media_id=isset($p['media_id']) ? $p['media_id'] : null;

		if ( empty($id) && empty($media_id) )
			return;

		$overview=isset($p['overview']) ? $p['overview'] : false;
		$distributionMaps=isset($p['distribution_maps']) ? $p['distribution_maps'] : false;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;
		$sort=!empty($p['sort']) ? $p['sort'] : '_m.overview_image desc,_meta4.meta_date desc';

		$data=$this->models->Taxon->freeQuery("		
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				_m.overview_image,
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
				and _z.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _z.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
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
				and _meta6.language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$this->getDefaultProjectLanguage()."

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
				".($id ? "and _m.taxon_id=". mysql_real_escape_string( $id ) : "")."
				and ifnull(_meta9.meta_data,0)!=".($distributionMaps?'0':'1')."
				".($overview ? "and _m.overview_image=1" : "")."
				".($media_id ? "and _m.id=". mysql_real_escape_string( $media_id ) : "")."

			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);

		$count=$this->models->MediaTaxon->freeQuery('select found_rows() as total');
		
		foreach((array)$data as $key=>$val)
		{
			$data[$key]['label']=
				trim(
					(isset($val['photographer']) ? $val['photographer'].', ' : '' ).
					(isset($val['meta_datum']) ? $val['meta_datum'].', ' : '' ).
					(isset($val['meta_geografie']) ? $val['meta_geografie'] : ''),
					', '
				);			

			$data[$key]['meta']=$this->models->Taxon->freeQuery("		
				select
					* 
				from
					%PRE%media_meta 
				where 
					project_id=".$this->getCurrentProjectId()."
					and media_id = ".$val['id']."
					and language_id=".$this->getDefaultProjectLanguage()."
			");

		}

		return array('count'=>$count[0]['total'],'data'=>$data,'perpage'=>$this->_resPicsPerPage);

    }

    private function getTaxonMediaMetaDataFields()
    {
		foreach((array)$this->models->Taxon->freeQuery("		
			select
				distinct sys_label
			from
				%PRE%media_meta
			where
				project_id=".$this->getCurrentProjectId()."
			order by
				sys_label
			") as $val)
			{
				$d[]=$val['sys_label'];
			}
		return $d;
    }

	private function disconnectTaxonMedia($image)
	{
		$id=$this->getConceptId();

		if (empty($id) || empty($image))
			return;

		$p=array(
			'project_id'=>$this->getCurrentProjectId(),
			'media_id'=>$image
		);
		$data=$this->models->MediaMeta->_get(array('id'=>$p));
		$this->models->MediaMeta->delete($p);
		$this->logNsrChange(array('before'=>$data,'note'=>'deleted media meta-data'));

		$p=array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$image,
			'taxon_id'=>$id
		);
		$data=$this->models->MediaTaxon->_get(array('id'=>$p));
		$this->models->MediaTaxon->delete($p);
		$this->logNsrChange(array('before'=>$data,'note'=>'disconnected media from taxon'));
	}

	private function updateTaxonImageMetaData($p)
	{
		$media_id=!empty($p['media_id']) ? $p['media_id'] : null;
		
		if (empty($media_id)) return;

		$image=$this->models->MediaTaxon->_get(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $media_id
		));

		foreach((array)$p['values'] as $key=>$val)
		{
			$ids=explode(",",$key);
			
			if ( empty($ids[0]) || empty($ids[1]) ) continue;

			$before=$this->models->MediaMeta->_get(array(
				'id' => $ids[0],
				'project_id' => $this->getCurrentProjectId()
			));

			if (empty($val))
			{
				$this->models->MediaMeta->delete(array(
					'id' => $ids[0],
					'project_id' => $this->getCurrentProjectId()
				));
				$after=null;
			}
			else
			{
				$this->models->MediaMeta->freeQuery("
					update 
					%PRE%media_meta 
					set ".$ids[1]." = '".$val."' 
					where 
					id = ".$ids[0]." 
					and project_id = ".$this->getCurrentProjectId()."
				");

				$after=$this->models->MediaMeta->_get(array(
					'id' => $ids[0],
					'project_id' => $this->getCurrentProjectId()
				));
			}
			
			$this->logNsrChange(
				array(
					'before'=>$before,
					'after'=>$after,
					'note'=>sprintf( (is_null($after) ? 'deleted' : 'changed'). ' metadata %s for image %s',$before['sys_label'],$image['file_name']) 
					)
				);
			
		}

		foreach((array)$p['new'] as $key=>$val)
		{
			if (empty($val)) continue;
			
			$d=array(
				'project_id'=>$this->getCurrentProjectId(),
				'media_id'=>$media_id,
				'language_id'=>$this->getDefaultProjectLanguage(),
				'sys_label'=>$key,
				'meta_data'=>$val,
			);
			
			if ($p['type'][$key]=='meta_date')
			{
				$d['meta_date']="#'".$val."'";
				$d['meta_number']=null;
				$d['meta_data']=null;
			}
			elseif ($p['type'][$key]=='meta_number')
			{
				$d['meta_date']=null;
				$d['meta_number']=$val;
				$d['meta_data']=null;
			}
			else
			{
				$d['meta_date']=null;
				$d['meta_number']=null;
				$d['meta_data']=$val;
			}

			$this->models->MediaMeta->save( $d );

			$after=$this->models->MediaMeta->_get(array(
				'id' => $this->models->MediaMeta->getNewId(),
				'project_id' => $this->getCurrentProjectId()
			));

			$this->logNsrChange(
				array(
					'after'=>$after,
					'note'=>sprintf( 'new metadata %s for image %s',$after['sys_label'],$image['file_name']) 
					)
				);
		}
	}

	private function updateTaxonImageTaxonId($p)
	{
		$media_id=!empty($p['media_id']) ? $p['media_id'] : null;
		$taxon_id=!empty($p['taxon_id']) ? $p['taxon_id'] : null;
		
		if ( empty($media_id) || empty($taxon_id) ) return;

		// logging = bureaucracy
		$before=$this->models->MediaTaxon->_get(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $media_id
		));
		
		$before['taxon']=$this->getTaxonById( $before['taxon_id'] );

		// actual work			
		$this->models->MediaTaxon->update(array(
			'taxon_id' => $taxon_id
		), array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $media_id
		));
		
		// logging = bureaucracy
		$after=$this->models->MediaTaxon->_get(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $media_id
		));

		$after['taxon']=$this->getTaxonById( $after['taxon_id'] );

		$this->logNsrChange(array('before'=>$before,'after'=>$after,'note'=> sprintf('changed taxon of image %s',$before['file_name'])));

	}

	private function updateTaxonImageOverviewState($p)
	{
		$media_id=!empty($p['media_id']) ? $p['media_id'] : null;
		$taxon_id=!empty($p['taxon_id']) ? $p['taxon_id'] : null;
		$overview_image=!empty($p['overview_image']) &&  $p['overview_image']=='on' ? 1 : 0;

		if ( empty($taxon_id) ) return;

		$before=$this->models->MediaTaxon->_get(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $media_id
		));

		if ( !empty($media_id) )
		{
			$this->models->MediaTaxon->update(
				array(
					'overview_image' => '0'
				), 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon_id,

					'id != ' => $media_id
				)
			);
			
			$this->models->MediaTaxon->update(
				array(
					'overview_image' => $overview_image
				), 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon_id,
					'id' => $media_id
				)
			);
		}
		else
		{
			$this->models->MediaTaxon->update(
				array(
					'overview_image' => '0'
				), 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon_id
				)
			);
		}

		$after=$this->models->MediaTaxon->_get(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $media_id
		));
		
		$before['taxon']=$after['taxon']=$this->getTaxonById( $before['taxon_id'] );
		
		
		$this->logNsrChange(array('before'=>$before,'after'=>$after,'note'=> sprintf('altered banner status of image %s',$before['file_name'])));

	}



	private function setMessage($m=null)
	{
		if (empty($m))
			unset($_SESSION['admin']['user']['species']['message']);
		else
			$_SESSION['admin']['user']['species']['message']=$m;
	}

	private function getMessage()
	{
		return @$_SESSION['admin']['user']['species']['message'];
	}

	private function checkMessage()
	{
		$m=$this->getMessage();
		if ($m) $this->addMessage($m);
		$this->setMessage();
	}

	private function setSessionVar($var,$val=null)
	{
		if (is_null($val))
		{
			unset($_SESSION['admin']['system']['nsr'][$var]);
		}
		else
		{
			$_SESSION['admin']['system']['nsr'][$var]=$val;
		}
	}

	private function getSessionVar($var)
	{
		return isset($_SESSION['admin']['system']['nsr'][$var]) ? $_SESSION['admin']['system']['nsr'][$var] : null;
	}

	private function parseRawCsvData($raw)
	{
		$this->helpers->CsvParserHelper->setFieldDelimiter("\t");
		$this->helpers->CsvParserHelper->setFieldMax(99);
		$this->helpers->CsvParserHelper->parseRawData($raw);
		$this->addError($this->helpers->CsvParserHelper->getErrors());

		if (!$this->getErrors())
		{
			return $this->helpers->CsvParserHelper->getResults();
		}
	}
	
	private function getTaxonByNsrId( $id )
	{
		$id=str_pad( $id, 12, '0',STR_PAD_LEFT);
		
		$taxa=$this->models->Taxon->freeQuery("
			select
				_ids.lng_id as taxon_id,
				_a.taxon
			from %PRE%taxa _a
			
			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%nsr_ids _ids
				on _a.id =_ids.lng_id 
				and _a.project_id = _ids.project_id
				and _ids.item_type = 'taxon'

			where
				_a.project_id =".$this->getCurrentProjectId()."
				and ifnull(_trash.is_deleted,0)=0
				and (
					nsr_id = '".$id."' or
					nsr_id = 'concept/".$id."' or
					nsr_id = 'tn.nlsr.concept/".$id."'
				)
		");

		return $taxa ? $taxa[0] : null;
	}
	
	private function matchNsrIds( $p )
	{
		$lines=isset($p['lines']) ? $p['lines'] : null;
		$ignorefirst=isset($p['ignorefirst']) ? $p['ignorefirst'] : false;

		$col_nsr_id=$this->getSessionVar( $this->sys_label_NSR_ID );
		$col_file_name=$this->getSessionVar( $this->sys_label_file_name );

		if (is_null($lines) || is_null($col_nsr_id) || is_null($col_file_name)) return null;

		$taxa=array();
		
		// go through all lines
		foreach((array)$lines as $key=>$line)
		{
			if ($ignorefirst && $key==0) continue;
			
			$date=null;
		
			// go through each cell of this reference	
			foreach((array)$line as $c=>$cell)
			{
				// resolve taxon
				if($c==$col_nsr_id)
				{
					$taxa[$key]=$this->getTaxonByNsrId( trim($cell) );
				}
				else
				if($c==$col_file_name)
				{
					$src=$this->_taxon_main_image_base_url . trim($cell);

					if (@getimagesize($src))
						$file_exists[$key]=array('exists'=>true,'url'=>$src);
					else
						$file_exists[$key]=array('exists'=>false,'url'=>$src);
				}
			}
		}
		
		return array(
			'taxa'=>$taxa,
			'files'=>$file_exists
		);

	}

	private function fieldFormatChecks( $p )
	{
		$lines=isset($p['lines']) ? $p['lines'] : null;
		$fields=isset($p['fields']) ? $p['fields'] : null;
		$ignorefirst=isset($p['ignorefirst']) ? $p['ignorefirst'] : false;

		if (is_null($lines)) return null;

		$checks=array();
		
		foreach((array)$lines as $key=>$line)
		{
			if ($ignorefirst && $key==0) continue;
			
			foreach((array)$line as $c=>$cell)
			{
				if( stripos($fields[$c],'datum') )
				{
					$d=date_parse($cell);
					$checks[$key][$c]=array('pass'=>$d['error_count']==0,'data'=>$d);
				}
			}
		}

		return $checks;
	}

	
}

