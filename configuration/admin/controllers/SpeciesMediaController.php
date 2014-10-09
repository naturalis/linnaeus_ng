<?php

include_once ('Controller.php');
class SpeciesMediaController extends Controller
{
    public $usedModels = array(
        'media_taxon', 
        'media_descriptions_taxon', 
        'media_meta'
    );

    public $usedHelpers = array(
        'file_upload_helper', 
        'image_thumber_helper', 
        'hr_filesize_helper'
    );
    public $cacheFiles = array(
    );
    public $cssToLoad = array(
        'prettyPhoto/prettyPhoto.css', 
        'taxon.css', 
    );
    public $jsToLoad = array(
        'all' => array(
            'taxon.js', 
            'prettyPhoto/jquery.prettyPhoto.js', 
        )
    );
    public $controllerPublicName = 'Species module';
    public $includeLocalMenu = false;

    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }


    private function getTaxonMedia($id)
    {
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
				and _b.language_id = ".$this->getDefaultProjectLanguage()."

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.taxon_id = ".$id." 

			order by 
				media_type,
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

		$data=array();

        foreach ((array)$d as $val)
		{
			$data[$val['media_type']][]=$val;
        }

        return $data;
    }
	

    public function mediaAction()
    {
        $this->checkAuthorisation();
		
        if (!$this->rHasId())
		{
			$this->redirect('index.php');
		}

		$taxon=$this->getTaxonById();
		$media=$this->getTaxonMedia($this->rGetId());

		$this->setPageName(sprintf($this->translate('Media for "%s"'), $taxon['taxon']));
            
		$this->smarty->assign('media',$media);
		$this->smarty->assign('taxon',$taxon);
        $this->smarty->assign('soundPlayerPath', $this->generalSettings['soundPlayerPath']);
        $this->smarty->assign('soundPlayerName', $this->generalSettings['soundPlayerName']);
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());

        $this->printPage();
        
return;

        if ($this->rHasId())
		{

            if ($this->rHasVal('mId') && $this->rHasVal('move') && !$this->isFormResubmit())
			{
                $this->changeMediaSortOrder($this->requestData['id'], $this->requestData['mId'], $this->requestData['move']);
            }
            
           
            
            foreach ((array) $this->controllerSettings['media']['allowedFormats'] as $key => $val) {
                
                $d[$val['mime']] = $val['media_type'];
            }
            
            if (isset($r))
                $this->smarty->assign('media', $r);
            
            $this->smarty->assign('languages', $this->getProjectLanguages());
            
            $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
            
            $this->smarty->assign('allowedFormats', $this->controllerSettings['media']['allowedFormats']);
        }
        else {
            
            $this->addError($this->translate('No taxon specified.'));
        }
        
        if (isset($taxon)) {
            $this->smarty->assign('taxon', $taxon);
//	        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
		}
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
            $taxon = $this->getTaxonById();
            
            if ($taxon['id']) {
                
                $this->setPageName(sprintf($this->translate('New media for "%s"'), $taxon['taxon']));
                
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
                                'taxon_id' => $this->requestData['id'], 
                                'file_name' => $file['name'], 
                                'original_name' => $file['original_name'], 
                                'mime_type' => $file['mime_type'], 
                                'file_size' => $file['size'], 
                                'thumb_name' => $thumb ? $thumb : null, 
                                'sort_order' => $this->getNextMediaSortOrder($this->requestData['id'])
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
            
            $this->smarty->assign('id', $this->requestData['id']);
            
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



	private function acquireCsvLines()
	{

		set_time_limit(2400);

		$raw = array();

		if (($handle = fopen($this->requestDataFiles[0]["tmp_name"], "r")) !== FALSE) {
			$i = 0;
			while (($dummy = fgetcsv($handle)) !== FALSE)
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
		
		$saved=$failed=0;

		if (!isset($data))
			return array('saved'=>$saved,'failed'=>$failed);

		$clearedTaxa = array();

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

			foreach((array)$line as $fKey => $fVal)
			{
				$fVal = trim($fVal,chr(239).chr(187).chr(191));  //BOM!
				
				if (empty($fVal))
					continue;

				// potentially multiple images per column separated by ;						
				$images=array_map('trim',explode(';',$fVal));

				foreach((array)$images as $iKey => $iVal)
				{
					if (empty($iVal)) continue;
					
					$mimes=array('jpg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','bmp'=>'image/bmp');
					$d=pathinfo($iVal);
					$mime=isset($mimes[strtolower($d['extension'])]) ? $mimes[strtolower($d['extension'])] : '?';

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
						'sort_order' => $this->getNextMediaSortOrder($tId)
					));
						
					if ($mt)
						$saved++;
					else
						$failed++;
				
				}
				
			}				
			
		}
		
		return array('saved'=>$saved,'failed'=>$failed);

	}

    public function remoteImgBatchAction()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Remote image batch upload'));
        
        if ($this->requestDataFiles && !$this->isFormResubmit())
		{
			$saved=$failed=0;

			$data=$this->acquireCsvLines();
			$data=$this->matchLinesToTaxon($data);
			$result=$this->processImageLines(array('data'=>$tId,'delete_remote'=>$this->rHasVal('del_existing','1')));

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
        
        if ($this->requestDataFiles && !$this->isFormResubmit())
		{
			$saved=$failed=0;
			$data=$this->acquireCsvLines();
			$data=$this->matchLinesToTaxon($data);
			$result=$this->processImageLines(array('data'=>$tId,'delete_local'=>$this->rHasVal('del_existing','1')));
			$this->addMessage(sprintf('Saved %s image(s), failed %s image(s).',$result['saved'],$result['failed']));
        }
       
        $this->printPage();
    }

    public function ajaxInterfaceAction()
    {
		if ($this->requestData['action'] == 'get_media_desc') {
            
            $this->ajaxActionGetMediaDescription();
        }
        else if ($this->requestData['action'] == 'get_media_descs') {
            
            $this->ajaxActionGetMediaDescriptions();
        }
        else if ($this->requestData['action'] == 'delete_media') {
            
            $this->deleteTaxonMedia();
        }
        else if ($this->requestData['action'] == 'set_overview') {
            
			$r = $this->setOverviewImageState($this->requestData['taxon_id'], $this->requestData['id'], $this->requestData['state']);
            $this->smarty->assign('returnText', $r ? '<ok>' : 'error' );
        }
        
        $this->printPage();
    }

    private function ajaxActionSaveMediaDescription()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            if (!$this->rHasVal('description')) {
                
                $this->models->MediaDescriptionsTaxon->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'media_id' => $this->requestData['id']
                ));
            }
            else {
                
                $mdt = $this->models->MediaDescriptionsTaxon->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $this->requestData['id']
                    )
                ));
                
                $d = $this->filterContent(trim($this->requestData['description']));
                
                $this->models->MediaDescriptionsTaxon->save(
                array(
                    'id' => isset($mdt[0]['id']) ? $mdt[0]['id'] : null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'media_id' => $this->requestData['id'], 
                    'description' => $d['content']
                ));
            }
            
            $this->smarty->assign('returnText', '<ok>');
        }
    }

    private function ajaxActionGetMediaDescription()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $mdt = $this->models->MediaDescriptionsTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'media_id' => $this->requestData['id']
                )
            ));
            
            $this->smarty->assign('returnText', $mdt[0]['description']);
        }
    }

    private function ajaxActionGetMediaDescriptions()
    {
        if (!$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $mt = $this->models->MediaTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id']
                ), 
                'columns' => 'id'
            ));
            
            foreach ((array) $mt as $key => $val) {
                
                $mdt = $this->models->MediaDescriptionsTaxon->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $val['id']
                    ), 
                    'columns' => 'description'
                ));
                
                $mt[$key]['description'] = $mdt ? $mdt[0]['description'] : null;
            }
            
            $this->smarty->assign('returnText', json_encode($mt));
        }
    }

/*
    private function getTaxonMedia($id)
    {
        $d = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            ), 
            'columns' => 'id,taxon_id,file_name,thumb_name,original_name,mime_type,file_size,sort_order,overview_image,substring(mime_type,1,locate(\'/\',mime_type)-1) as mime', 
            'order' => 'mime,sort_order,file_name'
        ));
        
        foreach ((array) $this->controllerSettings['media']['allowedFormats'] as $val)
            $mimes[$val['mime']] = $val;
        
        foreach ((array) $d as $key => $val) {
            
            if ($val['mime_type']) $d[$key]['media_type'] = $mimes[$val['mime_type']];
            if (file_exists($_SESSION['admin']['project']['urls']['project_media'] . $val['file_name'])) {
                $d[$key]['dimensions'] = getimagesize($_SESSION['admin']['project']['urls']['project_media'] . $val['file_name']);
            }
            $d[$key]['hr_file_size'] = $this->helpers->HrFilesizeHelper->convert($val['file_size']);
        }
        
        return $d;
    }
	
*/

    private function setOverviewImageState($taxon, $id, $state)
    {
        $mt = $this->models->MediaTaxon->update(array(
            'overview_image' => 0
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $taxon
        ));

        if ($state==1) {

            return $this->models->MediaTaxon->update(array(
                'overview_image' => 1
            ), array(
                'id' => $id, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $taxon
            ));
        }
		
		return true;		
		
    }

    private function getNextMediaSortOrder($taxon)
    {
        $d = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $taxon
            ), 
            'columns' => '(max(sort_order) + 1) as next'
        ));
        
        return $d[0]['next'];
    }

    private function reOrderMediaSortOrder($taxon)
    {
        $tm = $this->getTaxonMedia($taxon);
        
        $prevMime = null;
        
        foreach ((array) $tm as $val) {
            
            if ($prevMime != $val['mime'])
                $i = 0;
            
            $this->models->MediaTaxon->update(array(
                'sort_order' => $i++
            ), array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $val['id']
            ));
            
            $prevMime = $val['mime'];
        }
    }

    private function changeMediaSortOrder($taxon, $id, $dir)
    {
        $this->reOrderMediaSortOrder($taxon);
        
        $tm = $this->getTaxonMedia($taxon);
        
        foreach ((array) $tm as $key => $val) {
            
            if ($val['id'] == $id && $dir == 'down' && $key != (count((array) $tm) - 1)) {
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order'] + 1
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                ));
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $tm[$key + 1]['id']
                ));
            }
            else if ($val['id'] == $id && $dir == 'up' && $key != 0) {
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order'] - 1
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                ));
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $tm[$key - 1]['id']
                ));
            }
        }
    }

    private function deleteTaxonMedia($id=false,$output=true)
    {
        if ($id === false) {
            
            $id = $this->requestData['id'];
        }
        
        if (empty($id)) {
            
            return;
        }
        else {
            
            $mt = $this->models->MediaTaxon->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                )
            ));
            
            $delRecords = true;
            
            if (file_exists($_SESSION['admin']['project']['paths']['project_media'] . $mt[0]['file_name'])) {
                
                $delRecords = unlink($_SESSION['admin']['project']['paths']['project_media'] . $mt[0]['file_name']);
            }
            
            if ($delRecords) {
                
                if ($mt[0]['thumb_name'] && file_exists($_SESSION['admin']['project']['paths']['project_thumbs'] . $mt[0]['thumb_name'])) {
                    unlink($_SESSION['admin']['project']['paths']['project_thumbs'] . $mt[0]['thumb_name']);
                }
                
                $this->models->MediaDescriptionsTaxon->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'media_id' => $id
                ));
                

                $this->models->MediaTaxon->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                ));
                
                if ($output)
                    $this->smarty->assign('returnText', '<ok>');
            }
            else {
                
                if ($output)
                    $this->addError(sprintf($this->translate('Could not delete file: %s'), $mt[0]['file_name']));
            }
        }
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
					'file_name like' => $this->rGetVal('id').'%'
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
						str_pad(mysql_real_escape_string($this->rGetVal('newid')),12,'0',STR_PAD_LEFT)."' and item_type = 'taxon'"
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
		$this->smarty->assign('id', $this->rGetVal('id'));
        
        $this->printPage();
	}
	

}























