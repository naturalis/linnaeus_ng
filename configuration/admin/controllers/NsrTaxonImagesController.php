<?php /** @noinspection ALL */

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

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
    public $modelNameOverride='NsrTaxonImagesModel';
    public $controllerPublicName = 'Taxon editor';
    public $includeLocalMenu = false;
	private $_nameTypeIds;
	public $conceptId=null;
	private $_resPicsPerPage=100;
	private $sys_label_NSR_ID='NSR ID';
	private $sys_label_file_name='file name';

	private $availableMetaDataFields=array(
		'beeldbankAdresMaker',
		'beeldbankCopyright',
//		'beeldbankDatumAanmaak',
		'beeldbankDatumVervaardiging',
		'beeldbankFotograaf',
		'beeldbankLicentie',
		'beeldbankLokatie',
		'beeldbankOmschrijving',
		'beeldbankValidator',
//		'verspreidingsKaart',
//		'verspreidingsKaartBron',
//		'verspreidingsKaartTitel',
	);

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
		$this->smarty->assign('concept',$this->getConcept($this->rGetId()));
		$this->smarty->assign('images',$this->getTaxonMedia());

		$this->smarty->assign('module_id', $this->getCurrentModuleId());
		$this->smarty->assign('item_id', $this->rGetId());

		$this->checkMessage();
		$this->printPage();
	}

    public function imageDataAction()
    {
		$this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect('taxon_new.php');

        $this->setPageName($this->translate('Media metadata'));

		if ($this->rHasId() && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->updateTaxonImageMetaData($this->rGetAll());
			$this->updateTaxonImageTaxonId($this->rGetAll());
			$this->updateTaxonImageOverviewState($this->rGetAll());
			$this->addMessage( $this->translate('Metadata saved.') );
		}

		
		$meta_data_fields=
			array_unique(
				array_merge(
					// pre-defined metadata fields
					$this->availableMetaDataFields,
					// metadata fields actually in use
					$this->getTaxonMediaMetaDataFields()
				)
			);

		$image=$this->getTaxonMedia(array('media_id'=>$this->rGetId()));
		
		foreach((array)$image['data'][0]['meta'] as $val)
		{
			unset($meta_data_fields[array_search($val['sys_label'],$meta_data_fields)]);;
		}

		$this->smarty->assign('image',$image);
		$this->smarty->assign('meta_data_fields',$meta_data_fields);

		$this->printPage();
	}

    public function imageMetaBulkAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Media metadata bulk'));

		$raw=null;
		$ignorefirst=false;
		$no_image_exist_check=true;
		$lines=null;
		$emptycols=null;
		$fields=null;
		$matches=null;
		$checks=null;
		$firstline=null;

		$ignorefirst=$this->rHasVal('ignorefirst','1');
		$this->setSessionVar('ignorefirst',$ignorefirst);

		$no_image_exist_check=$this->rHasVal('no_image_exist_check','1');
		$this->setSessionVar('no_image_exist_check',$no_image_exist_check);

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
				$this->addError( 'You have assigned no metadata columns.' );
			}

			if
			(
				!is_null($this->getSessionVar( $this->sys_label_NSR_ID )) &&
				!is_null($this->getSessionVar( $this->sys_label_file_name )) &&
				$assignedMetaFields>0
			)
			{
				$matches=$this->matchNsrIdsAndCheckImgExistence(array('lines'=>$lines,'ignorefirst'=>$ignorefirst,'no_image_exist_check'=>$no_image_exist_check));
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
		$this->smarty->assign('cols',array_merge([$this->sys_label_file_name,$this->sys_label_NSR_ID,''], $this->availableMetaDataFields));
		$this->smarty->assign('raw',$raw);
		$this->smarty->assign('ignorefirst',$ignorefirst);
		$this->smarty->assign('no_image_exist_check',$no_image_exist_check);
		$this->smarty->assign('firstline',$firstline);
		$this->smarty->assign('lines',$lines);

		$this->printPage();
	}

    public function imageMetaBulkSaveAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Media metadata bulk'));

		if ( !$this->isFormResubmit() )
		{
			$this->doSaveImageMetaBulk();
		}

		$this->printPage();
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

		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->taxon_main_image_base_url=$this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_main','module'=>'species','subst'=>'http://images.naturalis.nl/original/') );
		$this->smarty->assign( 'taxon_main_image_base_url', $this->taxon_main_image_base_url );
		$this->smarty->assign( 'taxon_thumb_image_base_url', $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_thumb','module'=>'species','subst'=>'http://images.naturalis.nl/160x100/') ) );
	}

    private function getTaxonMedia( $p=null )
    {
		$id=isset($p['id']) ? $p['id'] : $this->getConceptId();
		$media_id=isset($p['media_id']) ? $p['media_id'] : null;

		if ( empty($id) && empty($media_id) )
			return;

		$overview=isset($p['overview']) ? $p['overview'] : false;
		$distributionMaps=isset($p['distribution_maps']) ? $p['distribution_maps'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;
		$sort=!empty($p['sort']) ? $p['sort'] : '_m.overview_image desc,_meta4.meta_date desc,_meta1.meta_date desc';

		$d=$this->models->NsrTaxonImagesModel->getTaxonMedia(array(
			"distribution_maps"=>$distributionMaps,
			"type_id_preferred"=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
			"type_id_valid"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"language_id"=>$this->getDefaultProjectLanguage(),
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$id,
			"overview"=>$overview,
			"media_id"=>$media_id,
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset
		));

		$data=$d['data'];
		$count=$d['count'];

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['label']=
				trim(
					(isset($val['photographer']) ? $val['photographer'].', ' : '' ).
					(isset($val['meta_datum']) ? $val['meta_datum'].', ' : '' ).
					(isset($val['meta_geografie']) ? $val['meta_geografie'] : ''),
					', '
				);

			$data[$key]['meta']=$this->models->MediaMeta->_get(array("id"=>
				array(
					"project_id"=>$this->getCurrentProjectId(),
					"media_id"=>$val['id'],
					"language_id"=>$this->getDefaultProjectLanguage()
				)
			));
		}

		return array('count'=>$count,'data'=>$data,'perpage'=>$this->_resPicsPerPage);

    }

    private function getTaxonMediaMetaDataFields()
    {
		$fields=$this->models->MediaMeta->_get(array(
			"id"=>array("project_id"=>$this->getCurrentProjectId()),
			"columns"=>"distinct sys_label",
			"order"=>"sys_label"
		));

		$d=[];

		foreach((array)$fields as $val)
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
		$this->logChange(array('before'=>$data,'note'=>'deleted media metadata'));

		$p=array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$image,
			'taxon_id'=>$id
		);
		$data=$this->models->MediaTaxon->_get(array('id'=>$p));
		$this->models->MediaTaxon->delete($p);
		$this->logChange(array('before'=>$data,'note'=>'detached media from taxon'));
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
				$this->models->MediaMeta->update(
					array($ids[1]=>$val),
					array('id'=>$ids[0],'project_id'=>$this->getCurrentProjectId())
				);
				
				$after=$this->models->MediaMeta->_get(array(
					'id' => $ids[0],
					'project_id' => $this->getCurrentProjectId()
				));
			}

			$this->logChange(
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

			$this->logChange(
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

		$this->logChange(array('before'=>$before,'after'=>$after,'note'=> sprintf('changed taxon of image %s',$before['file_name'])));

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


		$this->logChange(array('before'=>$before,'after'=>$after,'note'=> sprintf('altered banner status of image %s',$before['file_name'])));

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
		//$id=str_pad( $id, 12, '0',STR_PAD_LEFT);

		$taxa=$this->models->NsrTaxonImagesModel->getTaxonByNsrId(array(
			"project_id"=>$this->getCurrentProjectId(),
			"nsr_id"=>$id
		));

		return $taxa ? $taxa[0] : null;
	}

	private function matchNsrIdsAndCheckImgExistence( $p )
	{
		$lines=isset($p['lines']) ? $p['lines'] : null;
		$ignorefirst=isset($p['ignorefirst']) ? $p['ignorefirst'] : false;
		$no_image_exist_check=isset($p['no_image_exist_check']) ? $p['no_image_exist_check'] : true;

		$col_nsr_id=$this->getSessionVar( $this->sys_label_NSR_ID );
		$col_file_name=$this->getSessionVar( $this->sys_label_file_name );

		if (is_null($lines) || is_null($col_nsr_id) || is_null($col_file_name)) return null;

		$taxa=[];
		$files=[];

		// go through all lines
		foreach((array)$lines as $key=>$line)
		{
			if ($ignorefirst && $key==0) continue;

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

					$src=$this->taxon_main_image_base_url . trim($cell);
					
					$files[$key]=array('file_name'=>trim($cell),'url'=>$src);
					
					if ($no_image_exist_check===false)
					{
						if (@getimagesize($src))
						{
							$files[$key]['exists']=true;
						}
						else
						{
							$files[$key]['exists']=false;
						}
					}
				}
			}

			$files[$key]['exists_in_db']=false;

			if ( isset($taxa[$key]['taxon_id']) && isset($files[$key]['file_name']) )
			{
				$exist=$this->models->MediaTaxon->_get([ 'id' => [
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxa[$key]['taxon_id'],
					'file_name' => $files[$key]['file_name']
				], 'columns' => 'id', 'limit' => 1 ]);

				if ($exist)
				{
					$files[$key]['exists_in_db']=true;
					$files[$key]['db_id']=$exist[0]['id'];
				}
			}
		}

		return array(
			'taxa'=>$taxa,
			'files'=>$files
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

	private function doSaveImageMetaBulk()
	{

	/*
	   [files] => Array
	        (
	            [0] => Array
	                (
	                    [file_name] => 64805.jpg
	                    [url] => http://images.naturalis.nl/w800/64805.jpg
	                    [exists] => 1
	                    [exists_in_db] => 1
	                )

		implement [exists_in_db] => 1
		image exists, save/replace metadata

	*/		
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
				$media_id=null;

				if ($matches['files'][$key]['exists_in_db']==1)
				{
					$media_id=$matches['files'][$key]['db_id'];
				}
				else
				{
					$d=
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'taxon_id' => $matches['taxa'][$key]['taxon_id'],
							'file_name' => $filename,
							'original_name' => $filename,
							'mime_type' => @$this->_mime_types[pathinfo($filename, PATHINFO_EXTENSION)],
							'sort_order' => 99,
							'file_size' => '0'
						);

					$mt=$this->models->MediaTaxon->save($d);

					if ($mt==1)
					{
						$media_id=$this->models->MediaTaxon->getNewId();
					}
				}

				if (!is_null($media_id))
				{
					$fieldssaved=0;

					$concatfields=array();

					foreach((array)$line as $c=>$cell)
					{
						if ( isset($checks[$key][$c]) && $checks[$key][$c]['pass']==false) continue;

						if ( isset($checks[$key][$c]) )
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

					$concatfields['beeldbankDatumAanmaak']=date("Y-m-d H:i:s");

					$allmeta=array();

					foreach((array)$concatfields as $label=>$val)
					{
						$md=array(
							'project_id'=>$this->getCurrentProjectId(),
							'media_id'=>$media_id,
							'language_id'=>$this->getDefaultProjectLanguage(),
							'sys_label'=>$label
						);

						$this->models->MediaMeta->delete( $md );

						$md['meta_data']=$val;

						if( stripos($label,'datum') )
						{
							$md['meta_date']="#'".$val."'";
							$md['meta_number']=null;
							$md['meta_data']=null;
						}
						else
						{
							$md['meta_date']=null;
							$md['meta_number']=null;
							$md['meta_data']=$val;
						}

						if ($this->models->MediaMeta->save( $md ))
						{
							$fieldssaved++;
							$allmeta[]=$md;
						}
						else
						{
							$this->addError( sprintf('Couldn\'t save %s=%s for %s.', $label, $val, $filename) );
						}

					}

					$this->addMessage( sprintf('Wrote "%s" with %s metadata fields.',$filename,$fieldssaved) );

					$d['meta-data']=$allmeta;
					$this->logChange(array('after'=>$d,'note'=> sprintf('wrote "%s" (bulk upload).',$filename)));

				}
				else
				{
					$this->addError( $mt );
				}

			}
			else
			{
				$this->addError( sprintf('Ignored line %s: couldn\'t resolve NSR ID "%s".', $key+1, $line[$col_nsr_id]) );
			}

		}
	}

}

