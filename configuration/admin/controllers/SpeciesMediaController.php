<?php 

include_once ('Controller.php');
include_once ('MediaController.php');

class SpeciesMediaController extends Controller
{
    public $usedModels = array(
        'media_taxon',
        'media_descriptions_taxon',
        'media_meta'
    );

    public $usedHelpers = array(
        'file_upload_helper',
        //'image_thumber_helper',
        'hr_filesize_helper'
    );
    public $cssToLoad = array(
        'prettyPhoto/prettyPhoto.css',
        'taxon.css',
    );
    public $jsToLoad = array(
        'all' => array(
            'taxon.js',
            'prettyPhoto/jquery.prettyPhoto.js'
        )
    );
    public $controllerPublicName = 'Species module';
    public $includeLocalMenu = false;


    public $media;


	private $_mimeTypes=
		array(
			'jpg'=>'image/jpeg',
			'png'=>'image/png',
			'gif'=>'image/gif',
			'bmp'=>'image/bmp',
			'mp3'=>'audio/mpeg'
		);


    public function __construct ()
    {
        parent::__construct();

        $this->media = new MediaController();

    }

    public function __destruct()
    {
        parent::__destruct();
        $this->media = new MediaController();
    }



/*
	protected function getCurrentModuleId()
	{

		$activeModule = $this->moduleSession->getModuleSetting('activeModule');

	    return isset($activeModule['id']) ? $activeModule['id'] : false;

	}
*/



    public function mediaAction()
    {
        $this->checkAuthorisation();

        if (!$this->rHasId())
		{
			$this->redirect('index.php');
		}

		$activeLanguage=$this->rHasVar('language_id') ? $this->rGetVal('language_id') : $this->getDefaultProjectLanguage();

		if ($this->rHasVal('action','delete'))
		{
			$d=$this->deleteTaxonMedia($this->rGetId());
			@$this->addError($d['errors']);
			@$this->addWarning($d['warnings']);
			@$this->addMessage($d['messages']);
		}
		if ($this->rHasVal('action','save'))
		{
			$this->setOverviewImage($this->rGetId());
			$this->saveCaptions($this->rGetId());
			$this->addMessage('Saved');
		}
		if ($this->rHasVal('action','up') || $this->rHasVal('action','down'))
		{
			if ($this->moveImageInOrder($this->rGetId()))
				$this->addMessage('New media order saved');
		}

		$taxon=$this->getTaxonById( $this->rGetId() );
		$media=$this->getTaxonMedia(array('id'=>$this->rGetId(),'language_id'=>$activeLanguage));

		$this->setPageName(sprintf($this->translate('Media for "%s"'), $taxon['taxon']), $this->translate('Media'));

		$this->smarty->assign('media',$media);
		$this->smarty->assign('taxon',$taxon);
        $this->smarty->assign('soundPlayerPath',$this->generalSettings['soundPlayerPath']);
        $this->smarty->assign('soundPlayerName',$this->generalSettings['soundPlayerName']);
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage',$this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id',$activeLanguage);

        $this->printPage();
    }

    public function mediaUploadAction()
    {
        $this->checkAuthorisation();

        $this->includeLocalMenu = false;

        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Taxon list'),
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/branches.php'
        ));

		// referred from the taxon content editing page
        if ($this->rHasVal('add', 'hoc') && !isset($_SESSION['admin']['system']['media']['newRef'])) {

            $_SESSION['admin']['system']['media']['newRef'] = '<new>';

            $this->requestData['id']=$this->getActiveTaxonId();
        }

		// get existing taxon name
        if ($this->rHasId())
		{
            $taxon = $this->getTaxonById( $this->rGetId() );

            if ($taxon['id']) {

                $this->setPageName(sprintf($this->translate('New media for "%s"'), $taxon['taxon']), $this->translate('New media'));

                if ($this->requestDataFiles && !$this->isFormResubmit()) {

                    $filesToSave = $this->getUploadedMediaFiles();

                    $firstInsert = false;

                    if ($filesToSave) {

                        foreach ((array) $filesToSave as $key => $file) {

                            $thumb = false;

                            if ($this->helpers->ImageThumberHelper->canResize($file['mime_type']) && $this->helpers->ImageThumberHelper->thumbnail($this->getProjectsMediaStorageDir() . $file['name'])) {

                                $pi = pathinfo($file['name']);
                                $this->helpers->ImageThumberHelper->size_width(150);

                                if ($this->helpers->ImageThumberHelper->save($this->getProjectsThumbsStorageDir() . $pi['filename'] . '-thumb.' . $pi['extension'])) {

                                    $thumb = $pi['filename'] . '-thumb.' . $pi['extension'];
                                }
                            }

                            $mt = $this->models->MediaTaxon->save(
                            array(
                                'id' => null,
                                'project_id' => $this->getCurrentProjectId(),
                                'taxon_id' => $this->rGetId(),
                                'file_name' => $file['name'],
                                'original_name' => $file['original_name'],
                                'mime_type' => $file['mime_type'],
                                'file_size' => $file['size'],
                                'thumb_name' => $thumb ? $thumb : null,
                                'sort_order' => 99
                            ));

                            if (!$firstInsert) {

                                $firstInsert = array(
                                    'id' => $this->models->MediaTaxon->getNewId(),
                                    'name' => $file['name']
                                );
                            }

                            if ($mt) {

                                $this->addMessage(sprintf($this->translate('Saved: %s (%s)'), $file['original_name'], $file['media_name']));
                            }
                            else {

                                $this->addError($this->translate('Failed writing uploaded file to database.'), 1);
                            }
                        }

                        if (isset($_SESSION['admin']['system']['media']['newRef']) && $_SESSION['admin']['system']['media']['newRef'] == '<new>') {

                            $_SESSION['admin']['system']['media']['newRef'] = '<span class="inline-' . substr($file['mime_type'], 0, strpos($file['mime_type'], '/')) . '" onclick="showMedia(\'' .
                             addslashes($_SESSION['admin']['project']['urls']['project_media'] . $file['name']) . '\',\'' . addslashes($file['name']) . '\');">' . $firstInsert['name'] . '</span>';

                            $this->redirect('../species/taxon.php?id='.$this->getActiveTaxonId());
                        }
                    }
                }
            }
            else {

                $this->addError($this->translate('Unknown taxon.'));
            }

            $this->smarty->assign('id', $this->rGetId());

            $this->smarty->assign('allowedFormats', $this->controllerSettings['media']['allowedFormats']);

            $this->smarty->assign('iniSettings', array(
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size')
            ));
        }
        else {

            $this->addError($this->translate('No taxon specified.'));
        }

        $this->printPage();
    }

    public function remoteImgBatchAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Remote image batch upload'));

        if ($this->requestDataFiles && !$this->isFormResubmit())
		{
			$saved=$failed=0;
			$data=$this->acquireCsvLines($this->rGetVal('delimiter',","));
			$data=$this->matchLinesToTaxon($data);
			$result=$this->processImageLines(array('data'=>$data,'delete_remote'=>$this->rHasVal('del_existing','1')));

			$this->addMessage(sprintf('Saved %s image(s), failed %s image(s).',$result['saved'],$result['failed']));

			if ($failed)
				$this->addMessage('Failed pages are due to botched inserts.');
        }

        $this->printPage();
    }

    public function localImgBatchAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Local image batch upload'));
        if ($this->requestDataFiles)// && !$this->isFormResubmit())
		{
			$saved=$failed=0;
			$data=$this->acquireCsvLines($this->rGetVal('delimiter',","));
			$data=$this->matchLinesToTaxon($data);
			$result=$this->processImageLines(array('data'=>$data,'delete_local'=>$this->rHasVal('del_existing','1')));
			$this->addMessage(sprintf('Saved %s image(s), failed %s image(s).',$result['saved'],$result['failed']));
        }

        $this->printPage();
    }

    public function imageCaptionAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Image caption batch upload'));
        if ($this->requestDataFiles)// && !$this->isFormResubmit())
		{
			$saved=$failed=0;
			$data=$this->acquireCsvLines($this->rGetVal('delimiter',","));
			$data=$this->matchLinesToTaxon($data);
			$data=$this->matchLinesToMedia($data);

			foreach($data as $key=>$val)
			{
				if (!is_numeric($val[0])) continue;

				if (isset($val['media_id']))
				{
					$this->saveCaptions(array(
						'taxon_id'=>$val[0],
						'language_id'=>$this->rGetVal('language_id') ,
						'captions'=>array($val['media_id']=>$val[2])
					));

				}
				if (empty($val[2]))
				{
					$this->addMessage(sprintf('Removed caption for %s',$val[1]));
				}
				else
				{
					$this->addMessage(sprintf('Saved caption %s',$val[2]));
				}
			}

        }

		$this->smarty->assign('languages', $this->getProjectLanguages());

        $this->printPage();
    }

	public function tempNsr1Action()
	{
        $this->checkAuthorisation();

        $this->setPageName('NSR image re-something');

		if ($this->rHasVar('del') && $this->rHasVar('action','delete'))
		{
			$this->models->MediaMeta->freeQuery("
				delete from %table%
				where media_id = ".(int)$this->rGetVal('del')."
				and project_id = ".$this->getCurrentProjectId()
			);
			$this->models->MediaDescriptionsTaxon->freeQuery("
				delete from %table%
				where media_id = ".(int)$this->rGetVal('del')."
				and project_id = ".$this->getCurrentProjectId()."
				limit 1"
			);
			$this->models->MediaTaxon->freeQuery("
				delete from %table%
				where id = ".(int)$this->rGetVal('del')."
				and project_id = ".$this->getCurrentProjectId()."
				limit 1"
			);

			$this->addMessage('deleted');

		} else
		if ($this->rHasVar('id') && $this->rHasVar('image_id') && $this->rHasVar('new_taxon_id'))
		{
			$mdt = $this->models->MediaTaxon->freeQuery("
				update %table% set taxon_id = ".(int)$this->rGetVal('new_taxon_id')."
				where id = ".(int)$this->rGetVal('image_id')."
				and project_id = ".$this->getCurrentProjectId()."
				limit 1"
			);

			$this->addMessage('saved');

		} else
		if ($this->rHasVar('id'))
		{

			$mdt = $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'file_name like' => $this->rGetId().'%'
				)
			));

			if ($mdt)
			{
				$this->smarty->assign('image_id', $mdt[0]['id']);

				$current=$this->getTaxonById($mdt[0]['taxon_id']);
				$d=$this->models->Taxon->freeQuery("select * from  %PRE%nsr_ids where lng_id = ".$current['id']." and item_type = 'taxon'");
				if ($d)
					$current['nsr_id']=str_replace('tn.nlsr.concept/','',$d[0]['nsr_id']);
				else
					$current['nsr_id']='?';

				$this->smarty->assign('current', $current);

				if ($this->rHasVar('newid'))
				{
					$d=$this->models->Taxon->freeQuery(
						"select * from  %PRE%nsr_ids where nsr_id = 'tn.nlsr.concept/".
						str_pad($this->models->MediaTaxon->escapeString($this->rGetVal('newid')),12,'0',STR_PAD_LEFT)."' and item_type = 'taxon'"
					);

					if ($d)
					{
						$new=$this->getTaxonById($d[0]['lng_id']);
						if ($new)
						{
							$this->smarty->assign('new', $new);
						}
					}

				}


			}


		}

		$this->smarty->assign('newid', $this->rGetVal('newid'));
		$this->smarty->assign('id', $this->rGetId());

        $this->printPage();
	}



    private function getTaxonMedia($p)
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$language=isset($p['language_id']) ? $p['language_id'] : $this->getDefaultProjectLanguage();

        $d=$this->models->MediaTaxon->freeQuery("
			select
				_a.id,
				_a.taxon_id,
				_a.file_name,
				_a.thumb_name,
				_a.original_name,
				_a.mime_type,
				substring(_a.mime_type,1,locate('/',_a.mime_type)-1) as media_type,
				_a.file_size,
				_a.sort_order,
				_a.overview_image,
				_b.description
			from
				%PRE%media_taxon _a

			left join %PRE%media_descriptions_taxon _b
				on _a.id = _b.media_id
				and _b.project_id = _a.project_id
				and _b.language_id = ".$language."

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.taxon_id = ".$id."

			order by
				_a.sort_order,
				_a.file_name
		");

        foreach ((array)$d as $key=>$val)
		{
			$url=$_SESSION['admin']['project']['urls']['project_media'].$val['file_name'];

            if (file_exists($url))
			{
                $d[$key]['file_exists']=true;
				if ($val['media_type']=='image')
					$d[$key]['dimensions']=getimagesize($url);
            }
			else
			{
                $d[$key]['file_exists']=true;
			}

            $d[$key]['file_size_hr']=$this->helpers->HrFilesizeHelper->convert($val['file_size']);

        }

		/*
		$data=array();

        foreach ((array)$d as $val)
		{
			$data[$val['media_type']][]=$val;
        }
		*/

        return $d;
    }

    private function setOverviewImage($p)
    {
		$taxon=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$id=isset($p['overview-image']) ? $p['overview-image'] : null;

		if (empty($taxon))
			return;
		if (empty($id))
			return;

		$d=array(
            'project_id' => $this->getCurrentProjectId(),
            'taxon_id' => $taxon
        );
		$this->models->MediaTaxon->update(array('overview_image'=>0),$d);
		$d['id']=$id;
		$this->models->MediaTaxon->update(array('overview_image'=>1),$d);

    }

    private function saveCaptions($p)
    {
		$taxon=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$captions=isset($p['captions']) ? $p['captions'] : null;
		$language=isset($p['language_id']) ? $p['language_id'] : null;

		if (empty($taxon))
			return;

		if (empty($language))
			return;

		foreach((array)$captions as $id=>$caption)
		{
			$d=array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $language,
				'media_id' => $id
			);

			if (empty($caption))
			{
				$this->models->MediaDescriptionsTaxon->delete($d);
			}
			else
			{
                $m=$this->models->MediaDescriptionsTaxon->_get(array('id'=>$d));
				$d['id']=isset($m[0]['id']) ? $m[0]['id'] : null;
				$d['description']=strip_tags(trim($caption));
                $this->models->MediaDescriptionsTaxon->save($d);
			}
		}
    }

    private function moveImageInOrder($p)
    {
		$taxon=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$subject=isset($p['subject']) ? $p['subject'] : null;
		$direction=isset($p['action']) ? $p['action'] : null;

		if (empty($taxon))
			return;
		if (empty($subject))
			return;
		if (empty($direction) || ($direction!='up' && $direction!='down'))
			return;

        $media=$this->getTaxonMedia(array('id'=>$taxon));

		foreach((array)$media as $key=>$val)
		{
			$this->models->MediaTaxon->update(
				array('sort_order'=>$key),
				array('project_id'=>$this->getCurrentProjectId(),'id'=>$val['id'])
			);
		}

		$r=null;

		foreach((array)$media as $key=>$val)
		{
			if ($val['id']==$subject)
			{
				if ($key==0 && $direction=='up') continue;
				if ($key==(count($media)-1) && $direction=='down') continue;

                $this->models->MediaTaxon->update(array(
                    'sort_order'=>($key+($direction=='up'?-1:1))
                ), array(
                    'project_id' => $this->getCurrentProjectId(),
                    'id' => $val['id']
                ));

                $this->models->MediaTaxon->update(array(
                    'sort_order'=>($key+($direction=='up'?1:-1))
                ), array(
                    'project_id' => $this->getCurrentProjectId(),
                    'id' => $media[$key+($direction=='up'?-1:1)]['id']
                ));

				$r=true;

			}
		}

		return $r;

    }

    private function deleteTaxonMedia($p)
    {
		$taxon=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$subject=isset($p['subject']) ? $p['subject'] : null;

		if (empty($taxon))
			return;
		if (empty($subject))
			return;

		$mt = $this->models->MediaTaxon->_get(array(
			'id' => array(
				'id'=>$subject,
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$taxon
			)
		));

		$image=$_SESSION['admin']['project']['paths']['project_media'].$mt[0]['file_name'];
		$thumb=!empty($mt[0]['thumb_name']) ? $_SESSION['admin']['project']['paths']['project_thumbs'].$mt[0]['thumb_name'] : null;

		$messages=array();

		if (file_exists($image))
		{
			if (unlink($image))
			{
				$messages['messages'][]='Deleted file';
			}
			else
			{
				$messages['errors'][]='Could not delete file '.$image;
			}
		}
		else
		{
			$messages['warnings'][]=$image.' did not exist';
		}


		if (!empty($thumb))
		{
			if (file_exists($thumb))
			{
				if (unlink($thumb))
				{
					$messages['messages'][]='Deleted thumbnail';
				}
				else
				{
					$messages['errors'][]='Could not delete thumbnail '.$thumb;
				}
			}
			else
			{
				$messages['warnings'][]=$image.' did not exist';
			}
		}

		$d=$this->models->MediaDescriptionsTaxon->delete(array(
			'project_id' => $this->getCurrentProjectId(),
			'media_id' => $subject
		));

		if ($d)
		{
			$messages['messages'][]='Deleted caption';
		}
		else
		{
			$messages['errors'][]='Could not delete caption';
		}

		if ($d)
		{
			$d=$this->models->MediaTaxon->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'id'=>$subject,
				'taxon_id'=>$taxon

			));

			if ($d)
			{
				$messages['messages'][]='Deleted image';
			}
			else
			{
				$messages['errors'][]='Could not delete image';
			}

		}

		return $messages;
    }

	private function matchLinesToMedia($raw)
	{
		foreach ((array)$raw as $key=>$line)
		{
			if (empty($line[1])) continue;

			$mt=$this->models->MediaTaxon->_get(array("id"=>array("taxon_id"=>$line[0],"file_name"=>$line[1])));

			if (!$mt) continue;

			$raw[$key]['media_id']=$mt[0]['id'];
		}

		return $raw;
	}






	private function acquireCsvLines($delimiter=",")
	{

		set_time_limit(2400);

		$raw = array();

		if (($handle = fopen($this->requestDataFiles[0]["tmp_name"], "r")) !== FALSE) {
			$i = 0;
			while (($dummy = fgetcsv($handle,0,$delimiter)) !== FALSE)
			{
				foreach ((array) $dummy as $val)
				{
					$raw[$i][] = $val;
				}
				$i++;
			}
			fclose($handle);
		}

		return $raw;

	}

	private function matchLinesToTaxon($raw)
	{
		foreach ((array) $raw as $key => $line)
		{

			$d = implode('',$line);

			if (empty($d))
				continue;

			foreach((array)$line as $fKey => $fVal)
			{

				$fVal = trim($fVal,chr(239).chr(187).chr(191));  //BOM!

				if (empty($fVal))
					continue;

				if ($fKey==0)
				{
					$tIdOrName=$fVal;
					$tId = $this->resolveTaxonByIdOrname($tIdOrName);
					if (empty($tId))
					{
						$this->addError(sprintf('Could not resolve taxon "%s".',$tIdOrName));
						$raw[$key][0]='?';
					}
					else
					{
						$raw[$key][0]=$tId;
					}
				}

			}

		}

		return $raw;

	}

	private function deletePreviousMedia($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$deleteRemote=isset($p['delete_remote']) ? $p['delete_remote'] : false;
		$deleteLocal=isset($p['delete_local']) ? $p['delete_local'] : false;

		if (!isset($id))
			return;

		if ($deleteRemote)
		{
			$this->models->MediaTaxon->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id,
				'file_name like' => 'http://%'
			));

			$this->models->MediaTaxon->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id,
				'file_name like' => 'https://%'
			));

		}

		if ($deleteLocal)
		{
			$this->models->MediaTaxon->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id,
				'file_name not like' => 'http://%'
			));

			$this->models->MediaTaxon->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id,
				'file_name not like' => 'https://%'
			));

		}

	}

	private function processImageLines($p)
	{
		$data=isset($p['data']) ? $p['data'] : null;
		$deleteRemote=isset($p['delete_remote']) ? $p['delete_remote'] : false;
		$deleteLocal=isset($p['delete_local']) ? $p['delete_local'] : false;
		$handleDuplicates=isset($p['handle_duplicates']) ? $p['handle_duplicates'] : 'suppress_duplicates';

		$saved=$failed=0;

		if (!isset($data))
			return array('saved'=>$saved,'failed'=>$failed);

		$clearedTaxa = array();
		$counter=array();

		foreach ((array)$data as $key=>$line)
		{
			$d = implode('',$line);

			if (empty($d))
				continue;

			// name or id wasn't resolved
			if (!is_numeric($line[0]))
				continue;

			$tId=$line[0];

			if(($deleteRemote || $deleteLocal) && !isset($clearedTaxa[$tId]))
			{
				$this->deletePreviousMedia(array('id'=>$tId,'delete_remote'=>$deleteRemote,'delete_local'=>$deleteLocal));
				$clearedTaxa[$tId] = true;
			}

			$fVal=$line[1];
			$isOverview=(isset($line[2]) && ($line[2]=='1' || strtolower($line[2])=='y' || strtolower($line[2])=='yes'));

			$fVal = trim($fVal,chr(239).chr(187).chr(191));  //BOM!

			if (empty($fVal))
				continue;

			// potentially multiple images per column separated by ;
			$images=array_map('trim',explode(';',$fVal));

			foreach((array)$images as $iKey => $iVal)
			{
				if (empty($iVal)) continue;

				if ($handleDuplicates=='suppress_duplicates')
				{
					if ($this->doesImageExist(array('file_name'=>$iVal,'taxon'=>$tId)))
					{
						$this->addWarning($iVal." ignored: already exists for the same taxon.");
						continue;
					}
				} else
				if ($handleDuplicates=='suppress_global_duplicates')
				{
					if ($this->doesImageExist(array('file_name'=>$iVal)))
					{
						$this->addWarning($iVal." ignored: already exists for this or another taxon.");
						continue;
					}
				}

				$d=pathinfo($iVal);

				$mime=isset($this->_mimeTypes[strtolower($d['extension'])]) ? $this->_mimeTypes[strtolower($d['extension'])] : null;

				$counter[$tId]=isset($counter[$tId]) ? $counter[$tId]+1 : 0;
				$mt = $this->models->MediaTaxon->save(
				array(
					'id' => null,
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $tId,
					'file_name' => $iVal,
					'original_name' => $iVal,
					'mime_type' => $mime,
					'file_size' => 0,
					'thumb_name' => null,
					'sort_order' => $counter[$tId],
					'overview_image' => ($isOverview && $iKey==0 ? '1' : '0')
				));

				if ($mt)
					$saved++;
				else
					$failed++;

			}


		}

		return array('saved'=>$saved,'failed'=>$failed);

	}

	private function resolveTaxonByIdOrname($whatisit)
	{

		$tId=null;


		if (!empty($whatisit)) {

			if (is_numeric($whatisit)) {

				$t = $this->models->Taxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'id' => (int)$whatisit
						)
					));

				if ($t[0]['id']!=$whatisit)
					$tId = null;

			} else {

				$t = $this->models->Taxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon' => trim($whatisit)
						)
					));

				if (empty($t[0]['id']))
					$tId = null;
				else
					$tId = $t[0]['id'];

			}

		}

		return $tId;

	}

	private function doesImageExist($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$file_name=isset($p['file_name']) ? $p['file_name'] : null;
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;

		if (empty($id) && empty($file_name) && empty($taxon))
		{
			return;
		}

		$d=array('project_id' => $this->getCurrentProjectId());

		if (isset($id))
		{
			$d['taxon_id']=$id;
		}
		if (isset($file_name))
		{
			$d['file_name']=$file_name;
		}
		if (isset($taxon))
		{
			$d['taxon_id']=$taxon;
		}

		$mt=$this->models->MediaTaxon->_get(array("id"=>$d,"columns"=>"count(*) as total"));
		return $mt[0]['total']!=0;
	}

}

