<?php

include_once ('Controller.php');

class ModuleController extends Controller
{

    public $usedModels = array(
		'free_module_project',
		'free_module_project_user',
		'free_module_page',
		'content_free_module',
		'free_module_media'
    );
   
    public $usedHelpers = array(
        'file_upload_helper','image_thumber_helper'
    );

    public $controllerPublicName = 'Free Modules';

	public $cssToLoad = array(
		'colorbox/colorbox.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array(
		'all' => array(
			'freemodule.js',
			'colorbox/jquery.colorbox.js',
			'lookup.js',
			'int-link.js',
			'dialog/jquery.modaldialog.js'
		)
	);


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();

		if (isset($_SESSION['admin']['user']['freeModules']['activeModule']))
	    	$this->controllerPublicName = $_SESSION['admin']['user']['freeModules']['activeModule']['module'];

    }

    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }

	public function checkAuthorisation()
	{

		if ($this->rHasVal('freeId')) {

			if (!$this->isUserAuthorizedForFreeModule())
				$this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);

		} else {

			if (!$_SESSION['admin']['user']['freeModules']['activeModule'])
				$this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);

		}
	
	}


    /**
     * Module index
     *
     * @access    public
     */
    public function indexAction()
    {

        $this->checkAuthorisation();
		
		if (!isset($_SESSION['admin']['user']['freeModules']['activeModule']) || $this->rHasVal('freeId')) {

			$_SESSION['admin']['user']['freeModules']['activeModule'] = $this->getFreeModule();
			
			$this->controllerPublicName = $_SESSION['admin']['user']['freeModules']['activeModule']['module'];

		}

		$this->cleanUpEmptyPages();

		if (!$this->rHasVal('page'))
			$this->redirect('edit.php?id='.$this->getFirstPageId());
		else		
			$this->redirect('edit.php?id='.$this->requestData['page']);

		/*
    	$this->controllerPublicName = $_SESSION['admin']['user']['freeModules']['activeModule']['module'];

		$this->smarty->assign('module',$_SESSION['admin']['user']['freeModules']['activeModule']);

        $this->printPage();
		*/

    }

    /**
     * Create new page or edit existing
     *
     * @access    public
     */
    public function editAction()
    {

		$this->checkAuthorisation();

		if (!$this->rHasId()) {

			$id = $this->createPage();

			// redirecting to protect against resubmits
			if ($id) {

				$this->redirect('edit.php?id='.$id);

			} else {

				$this->addError(_('Could not create page.'));

			}

		} else {

			if ($this->rHasVal('action','delete')) {

				$this->deletePage();
				
				$this->redirect('index.php');

			} else
			if ($this->rHasVal('action','deleteImage')) {

				$this->deleteMedia();

			} else
			if ($this->rHasVal('action','preview')) {

				$this->redirect('preview.php?id='.$this->requestData['id']);

			} 

			$page = $this->getPage();

			if ($page['got_content']==0) {

		        $this->setPageName(_('Creating new page'));

			} else {

		        $this->setPageName(_('Editing page'));

			}

		}

		$navList = $this->getModulePageNavList(true);
	
		if (isset($navList)) $this->smarty->assign('navList', $navList);
			$this->smarty->assign('navCurrentId',isset($this->requestData['id']) ? $this->requestData['id'] : null);

		$this->smarty->assign('id', $this->rHasId() ? $this->requestData['id'] : $id);

		if (isset($page)) $this->smarty->assign('page', $page);

		$this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);
		
		$this->smarty->assign('activeLanguage', $_SESSION['admin']['project']['default_language_id']);

		$this->smarty->assign('includeHtmlEditor', true);

        $this->printPage();
    
    }


    public function previewAction()
    {

		$this->redirect('../../../app/views/module/topic.php?p='.$this->getCurrentProjectId().'&modId='.$this->getCurrentModuleId().'&id='.$this->requestData['id']);

    }

    public function browseAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName(_('Browsing pages'));

		$alpha = $this->getActualAlphabet();

		if (!$this->rHasVal('letter') && isset($alpha))
			$this->requestData['letter'] = current($alpha);

		if ($this->rHasVal('letter')) {

			$refs = $_SESSION['admin']['system']['freeModule']['alphaIndex'][$this->requestData['letter']];

		}

		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);

		if (isset($alpha)) $this->smarty->assign('alpha',$alpha);

		if (isset($refs)) $this->smarty->assign('refs',$refs);

        $this->printPage();

	}


    /**
     * Upload media for a glossary term
     *
     * @access    public
     */
    public function mediaUploadAction ()
    {

		$this->checkAuthorisation();
		
		$this->setPageName(_('New image'));
		
		if ($this->requestDataFiles && !$this->isFormResubmit()) {
		
			$filesToSave =  $this->getUploadedMediaFiles();
			
			$firstInsert = false;
			
			if ($filesToSave && $this->rHasId()) {
			
				foreach((array)$filesToSave as $key => $file) {
				
					$thumb = false;
					
					if (
					$this->helpers->ImageThumberHelper->canResize($file['mime_type']) &&
					$this->helpers->ImageThumberHelper->thumbnail($this->getProjectsMediaStorageDir().$file['name'])
					) {
					
						$pi = pathinfo($file['name']);
						$this->helpers->ImageThumberHelper->size_width(150);
						
						if ($this->helpers->ImageThumberHelper->save(
						$this->getProjectsThumbsStorageDir().$pi['filename'].'-thumb.'.$pi['extension']
						)) {
						
							$thumb = $pi['filename'].'-thumb.'.$pi['extension'];
						
						}
					
					}
					
					$fmm = $this->models->FreeModuleMedia->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'page_id' => $this->requestData['id'],
							'file_name' => $file['name'],
							'original_name' => $file['original_name'],
							'mime_type' => $file['mime_type'],
							'file_size' => $file['size'],
							'thumb_name' => $thumb ? $thumb : null,
						)
					);
					
					if ($fmm) {
					
						$this->addMessage(sprintf(_('Saved: %s (%s)'),$file['original_name'],$file['media_name']));
					
					} else {
					
						$this->addError(_('Failed writing uploaded file to database.'),1);
					
					}
				
				}

			}
		
		}

		$this->smarty->assign('id',$this->requestData['id']);
		
		$this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);
		
		$this->smarty->assign('iniSettings',
			array(
				'upload_max_filesize' => ini_get('upload_max_filesize'),
				'post_max_size' => ini_get('post_max_size')
			)
		);

		$this->printPage();
    
    }


	/**
	* General interface for all AJAX-calls
	*
	* @access     public
	*/
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
        
        if ($this->requestData['action'] == 'save_content') {
            
            $this->ajaxActionSaveContent();
        
        } else
        if ($this->requestData['action'] == 'get_content') {
            
            $this->ajaxActionGetContent();
        
        }
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        }
		
        $this->printPage();
    
    }

	private function ajaxActionSaveContent()
	{

       if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        
        } else {
            
            if (!$this->rHasVal('topic') && !$this->rHasVal('content')) {
                
                $this->models->ContentFreeModule->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(),
						'module_id' => $this->getCurrentModuleId(),
                        'language_id' => $this->requestData['language'], 
                        'page_id' => $this->requestData['id']
                    )
                );
				
				$this->setPageGotContent($this->requestData['id']);
            
            } else {
                
                $cfm = $this->models->ContentFreeModule->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'module_id' => $this->getCurrentModuleId(),
							'language_id' => $this->requestData['language'], 
	                        'page_id' => $this->requestData['id']
						)
					)
				);
                
                $this->models->ContentFreeModule->save(
					array(
						'id' => isset($cfm[0]['id']) ? $cfm[0]['id'] : null, 
						'project_id' => $this->getCurrentProjectId(), 
						'module_id' => $this->getCurrentModuleId(),
						'language_id' => $this->requestData['language'], 
						'page_id' => $this->requestData['id'],
						'topic' => trim($this->requestData['topic']),
						'content' => trim($this->requestData['content'])
					)
				);

				$this->setPageGotContent($this->requestData['id'],true);

            }
			
			unset($_SESSION['admin']['system']['freeModule'][$this->getCurrentModuleId()]['navList']);

            $this->smarty->assign('returnText', 'saved');
        
        }

	}

	private function getPageContent($id,$languageId)
	{

        if (!$id || !$languageId) {
            
            return;
        
        } else {

			$cfm = $this->models->ContentFreeModule->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'module_id' => $this->getCurrentModuleId(),
						'language_id' => $languageId, 
						'page_id' => $id,
						)
				)
			);
                
            return $cfm[0];

        }

	}

	private function ajaxActionGetContent()
	{

        if (!$this->rHasVal('id') || !$this->rHasVal('language')) {
            
            return;
        
        } else {

			$page = $this->getPageContent($this->requestData['id'],$this->requestData['language']);
                
            $this->smarty->assign('returnText', json_encode(array('topic' => $page['topic'],'content' => $page['content'])));
        
        }

	}

	private function getCurrentModuleId()
	{

		return isset($_SESSION['admin']['user']['freeModules']['activeModule']['id']) ?
				$_SESSION['admin']['user']['freeModules']['activeModule']['id'] :
				false;

	}

	private function isUserAuthorizedForFreeModule($id=null)
	{
	
		$id = isset($id) ? $id : $this->requestData['freeId'];
		
		if (!isset($id)) return false;
	
		$fmpu = $this->models->FreeModuleProjectUser->_get(
			array(
				'id' => array(
					'free_module_id' => $id,
					'project_id' => $this->getCurrentProjectId(),
					'user_id' => $this->getCurrentUserId()
				)
			)
		);

		return (count((array)$fmpu)>0);

	}


	private function getFreeModule($id=null)
	{

		$id = 
			isset($id) ? 
				$id : 
				isset($this->requestData['freeId']) ?
					$this->requestData['freeId'] :
					$this->getCurrentModuleId();

		$fmp = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		if ($fmp) return $fmp[0];
	
	}

	private function createPage()
	{
	
		if (!$this->getCurrentModuleId()) return;

		$this->models->FreeModulePage->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId()
			)
		);

		return $this->models->FreeModulePage->getNewId();

	}

	private function setPageGotContent($id,$state=null)
	{

		if (!$this->getCurrentModuleId()) return;

		if ($state==null) {

			$cfm = $this->models->ContentFreeModule->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'page_id' => $id
					),
					'columns' => 'count(*) as total'
				)
			);

			$state = ($cfm[0]['total']==0 ? false : true);

		}

		$this->models->FreeModulePage->update(
			array(
				'got_content' => ($state==false ? '0' : '1'),
			),
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId()
			)
		);

	}

	private function getPages()
	{

		$fmp = $this->models->FreeModulePage->_get(
			array(
				'id' => array(
					'module_id' => $this->getCurrentModuleId(),
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				)
			)
		);
		
		foreach((array)$fmp as $key => $val) {

			$d = $this->getPageContent($val['id'],$_SESSION['admin']['project']['default_language_id']);

			$fmp[$key]['topic'] = $d['topic'];

		}
		
		$this->customSortArray($fmp,array('key' => 'topic'));
		
		return $fmp;

	}

	private function getPage($id=null)
	{

		$id = isset($id) ? $id : $this->requestData['id'];

		if (!isset($id)) return;

		$pfm = $this->models->FreeModulePage->_get(
			array(
				'id' => array(
					'id' => $id,
					'module_id' => $this->getCurrentModuleId(),
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);
	
		if ($pfm) {

			$fmm = $this->models->FreeModuleMedia->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $id
					)
				)
			);
			
			if ($fmm) {

				$pfm[0]['image']['file_name'] = $fmm[0]['file_name'];

				$pfm[0]['image']['thumb_name'] = $fmm[0]['thumb_name'];

			}

			return $pfm[0];
		
		} else {

			return null;

		}

	}


	private function getFirstPageId()
	{

		$cfm = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'module_id' => $this->getCurrentModuleId(),
					'language_id' => $_SESSION['admin']['project']['default_language_id']
					),
				'order' => 'topic',
				'columns' => 'page_id',
				'ignoreCase' => true,
				'limit' => 1
			)
		);

		return isset($cfm[0]) ? $cfm[0]['page_id'] : null;
		
	}

	private function cleanUpEmptyPages()
	{

		if (!$this->getCurrentModuleId()) return;

		// delete all pages with no content that are over 7 days old
		$this->models->FreeModulePage->delete('delete from %table% 
			where module_id = '.$this->getCurrentModuleId().'
			and project_id =  '.$this->getCurrentProjectId().'
			and got_content = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)');

	}

	private function getActualAlphabet()
	{

		unset($_SESSION['admin']['system']['freeModule']['alphaIndex']);

		$cfm = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'module_id' => $this->getCurrentModuleId(),
					'language_id' => $_SESSION['admin']['project']['default_language_id'], 
				),
				'columns' => 'page_id, topic, lower(substr(topic,1,1)) as letter',
				'order' => 'letter'
			)
		);
		
		$alpha = null;
		
		foreach((array)$cfm as $key => $val) {

			$alpha[$val['letter']] = $val['letter'];

			$_SESSION['admin']['system']['freeModule']['alphaIndex'][$val['letter']][] = array('id' => $val['page_id'],'topic' => $val['topic']);

		}

		return $alpha;
	
	}

	private function deleteMedia($id=null)
	{
	
		$id = isset($id) ? $id : (isset($this->requestData['id']) ? $this->requestData['id'] : null);
		
		if ($id == null) return;

		$fmm = $this->models->FreeModuleMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'page_id' => $id
				)
			)
		);
			
		if (file_exists($_SESSION['admin']['project']['paths']['project_media'].$fmm[0]['file_name'])) {

			if (unlink($_SESSION['admin']['project']['paths']['project_media'].$fmm[0]['file_name'])) {
			
				if ($fmm[0]['thumb_name'] && file_exists($_SESSION['admin']['project']['paths']['project_thumbs'].$fmm[0]['thumb_name'])) {
				
					unlink($_SESSION['admin']['project']['paths']['project_thumbs'].$fmm[0]['thumb_name']);

				}
			}

		}

		$this->models->FreeModuleMedia->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'page_id' => $id
			)
		);

	}	

	private function deletePage($id=null)
	{
	
		$id = isset($id) ? $id : (isset($this->requestData['id']) ? $this->requestData['id'] : null);
		
		if ($id == null || !$this->getCurrentModuleId()) return;

		$this->deleteMedia($id);

		$this->models->ContentFreeModule->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId(),
				'page_id' => $id
			)
		);

		$this->models->FreeModulePage->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId()
			)
		);

	}	

	private function getModulePageNavList($forceLookup=true)
	{
	
		if (empty($_SESSION['admin']['system']['freeModule'][$this->getCurrentModuleId()]['navList']) || $forceLookup) {
		
			$d = $this->getPages();

			foreach((array)$d as $key => $val) {

				$res[$val['id']] = array(
					'prev' => array(
						'id' => isset($d[$key-1]['id']) ? $d[$key-1]['id'] : null,
						'title' => isset($d[$key-1]['topic']) ? $d[$key-1]['topic'] : null
					),
					'next' => array(
						'id' => isset($d[$key+1]['id']) ? $d[$key+1]['id'] : null,
						'title' => isset($d[$key+1]['topic']) ? $d[$key+1]['topic'] : null
					),
				);

			}

			$_SESSION['admin']['system']['freeModule'][$this->getCurrentModuleId()]['navList'] = isset($res) ? $res : null;
		
		}
		
		return $_SESSION['admin']['system']['freeModule'][$this->getCurrentModuleId()]['navList'];

	}

	private function getLookupList($search)
	{

		if (empty($search)) return;

		$cfm = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'module_id' => $this->getCurrentModuleId(),
					'topic like' => '%'.$search.'%'
					),
				'order' => 'topic',
				'columns' => 'distinct page_id as id, topic as label',
			)
		);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$cfm,
				$this->controllerBaseName,
				'../module/edit.php?id=%s',
				true
			)
		);
		
	}

}