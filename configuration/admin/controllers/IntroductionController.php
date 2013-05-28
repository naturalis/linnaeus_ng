<?php

include_once ('Controller.php');

class IntroductionController extends Controller
{

    public $usedModels = array(
		'content_introduction',
		'introduction_page',
		'introduction_media'
    );
   
    public $usedHelpers = array(
        'file_upload_helper','image_thumber_helper'
    );

    public $controllerPublicName = 'Introduction';

	public $cssToLoad = array(
		'prettyPhoto/prettyPhoto.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css',
	);

	public $jsToLoad = array(
		'all' => array(
			'freemodule.js',
			'prettyPhoto/jquery.prettyPhoto.js',
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
		
		$this->cleanUpEmptyPages();

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

    /**
     * Contents (page overview)
     *
     * @access    public
     */
    public function contentsAction()
    {
    
        $this->checkAuthorisation();
    
        $this->setPageName($this->translate('Contents'));
    
        $pagination = $this->getPagination($this->getPageHeaders(),25);
    
        $this->smarty->assign('prevStart', $pagination['prevStart']);
    
        $this->smarty->assign('nextStart', $pagination['nextStart']);
    
        $this->smarty->assign('pages',$pagination['items']);
    
        $this->printPage();
    
    }
    
    
    /**
     * Module index
     *
     * @access    public
     */
    public function indexAction()
    {

        $this->checkAuthorisation();
		
		if (!$this->rHasVal('page')) {

			$this->redirect('edit.php?id='.$this->getFirstPageId());

		} else {

			$this->redirect('edit.php?id='.$this->requestData['page']);

		}

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

				$this->addError($this->translate('Could not create page.'));

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

		        $this->setPageName($this->translate('Creating new page'));

			} else {

		        $this->setPageName($this->translate('Editing page'));

			}

		}

		$navList = $this->getPageNavList(true);
	
		if (isset($navList)) $this->smarty->assign('navList', $navList);
			$this->smarty->assign('navCurrentId',isset($this->requestData['id']) ? $this->requestData['id'] : null);

		$this->smarty->assign('id', $this->rHasId() ? $this->requestData['id'] : $id);

		if (isset($page)) $this->smarty->assign('page', $page);

		$this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);
		
		$this->smarty->assign('activeLanguage', $this->getDefaultProjectLanguage());

		$this->smarty->assign('includeHtmlEditor', true);

        $this->printPage();
    
    }


    /**
     * Create new page or edit existing
     *
     * @access    public
     */
    public function previewAction()
    {

		$this->redirect(
			'../../../app/views/introduction/topic.php?p='.$this->getCurrentProjectId().
			'&id='.$this->requestData['id'].
			'&lan='.$this->getDefaultProjectLanguage()
		);

	}

    /**
     * Change page display order
     *
     * @access    public
     */
    public function orderAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName($this->translate('Change page order'));
		
		if ($this->rHasVal('newOrder') && !$this->isFormResubmit()) {
		
			foreach((array)$this->requestData['newOrder'] as $key => $val) {
			
				 $this->models->IntroductionPage->update(
					array(
						'show_order' => $key,
					),
					array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val
					)
				);

			}
			
			$this->addMessage($this->translate('New order saved.'));

		}

		$this->smarty->assign('pages',$this->getPageHeaders());

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
		
		$this->setPageName($this->translate('New image'));
		
		$this->loadControllerConfig('Module');
		
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
					
					$fmm = $this->models->IntroductionMedia->save(
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
					
						$this->addMessage(sprintf($this->translate('Saved: %s (%s)'),$file['original_name'],$file['media_name']));
					
					} else {
					
						$this->addError($this->translate('Failed writing uploaded file to database.'),1);
					
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
		
		$this->allowEditPageOverlay = false;
		
        $this->printPage();
    
    }

	private function ajaxActionSaveContent()
	{

       if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        
        } else {
            
            if (!$this->rHasVal('topic') && !$this->rHasVal('content')) {
                
                $this->models->ContentIntroduction->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->requestData['language'], 
						'page_id' => $this->requestData['id']
					)
                );
				
				$this->setPageGotContent($this->requestData['id']);
            
            } else {

                $cfm = $this->models->ContentIntroduction->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $this->requestData['language'], 
							'page_id' => $this->requestData['id']
						)
					)
				);
                
                $this->models->ContentIntroduction->save(
					array(
						'id' => isset($cfm[0]['id']) ? $cfm[0]['id'] : null, 
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->requestData['language'], 
						'page_id' => $this->requestData['id'],
						'topic' => trim($this->requestData['topic']),
						'content' => trim($this->requestData['content'])
					)
				);

				$this->setPageGotContent($this->requestData['id'],true);

            }
			
			unset($_SESSION['admin']['system']['introduction']['navList']);

            $this->smarty->assign('returnText', 'saved');
        
        }

	}

	private function ajaxActionGetContent()
	{

        if (!$this->rHasVal('id') || !$this->rHasVal('language')) {
            
            return;
        
        } else {

			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'page_id' => $this->requestData['id'],
						)
				)
			);
                
            $this->smarty->assign('returnText', json_encode(array('topic' => $cfm[0]['topic'],'content' => $cfm[0]['content'])));
        
        }

	}
	
	private function getNextShowOrderValue()
	{
	
		$ip =  $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				),
				'columns' => 'max(ifnull(show_order,0))+1 as next'
			)
		);

		return $ip[0]['next'];
	
	}

	private function createPage()
	{
	
		$this->models->IntroductionPage->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'show_order' => $this->getNextShowOrderValue()
			)
		);

		return $this->models->IntroductionPage->getNewId();

	}

	private function setPageGotContent($id,$state=null)
	{

		if ($state==null) {

			$cfm = $this->models->ContentIntroduction->_get(
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

		$this->models->IntroductionPage->update(
			array(
				'got_content' => ($state==false ? '0' : '1'),
			),
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId(),
			)
		);

	}

	private function getPageHeaders()
	{

		$ip =  $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				),
				'order' => 'show_order,created'
			)
		);


		foreach((array)$ip as $key => $val) {

			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getDefaultProjectLanguage(), 
						'page_id' => $val['id']
					),
					'columns' => 'topic',
				)
			);
			
			$ip[$key]['topic'] = $ip[$key]['label'] = $cfm[0]['topic'];

		}
		
		return $ip;

	}

	private function getPage($id=null,$languageId=null)
	{

		$id = isset($id) ? $id : $this->requestData['id'];
		
		if (!isset($id)) return;

		$pfm = $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		if ($pfm) {

			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => isset($languageId) ? $languageId : $this->getDefaultProjectLanguage(), 
						'page_id' => $this->requestData['id'],
						)
				)
			);
				
			if ($cfm) {

				$pfm[0]['content'] = $cfm[0]['content'];

				$pfm[0]['topic'] = $cfm[0]['topic'];

			}

			$fmm = $this->models->IntroductionMedia->_get(
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

		$ip =  $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				),
				'order' => 'show_order,created',
				'columns' => 'id',
				'limit' => 1
			)
		);


		return isset($ip[0]['id']) ? $ip[0]['id'] : null;
		
	}

	private function cleanUpEmptyPages()
	{

		// delete all pages with no content that are over 7 days old
		$this->models->IntroductionPage->delete('delete from %table% 
			where project_id =  '.$this->getCurrentProjectId().'
			and got_content = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)');

	}

	private function deleteMedia($id=null)
	{
	
		$id = isset($id) ? $id : (isset($this->requestData['id']) ? $this->requestData['id'] : null);
		
		if ($id == null) return;

		$fmm = $this->models->IntroductionMedia->_get(
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

		$this->models->IntroductionMedia->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'page_id' => $id
			)
		);

	}	

	private function deletePage($id=null)
	{
	
		$id = isset($id) ? $id : (isset($this->requestData['id']) ? $this->requestData['id'] : null);
		
		if ($id == null) return;

		$this->deleteMedia($id);

		$this->models->ContentIntroduction->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'page_id' => $id
			)
		);

		$this->models->IntroductionPage->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

	}	

	private function getPageNavList($forceLookup=false)
	{

		if (empty($_SESSION['admin']['system']['introduction']['navList']) || $forceLookup) {
		
			$d = $this->getPageHeaders();

			foreach((array)$d as $key => $val) {

				$res[$val['id']] = array(
					'prev' => array('id' => isset($d[$key-1]['id']) ? $d[$key-1]['id'] : null),
					'next' => array('id' => isset($d[$key+1]['id']) ? $d[$key+1]['id'] : null),
				);

			}

			if (isset($res))
				$_SESSION['admin']['system']['introduction']['navList'] = $res;
			else
				$_SESSION['admin']['system']['introduction']['navList'] = null;
		
		}
		
		return $_SESSION['admin']['system']['introduction']['navList'];

	}

	private function getLookupList($search)
	{

		if (empty($search)) return;

		$cfm = $this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' =>  $this->getDefaultProjectLanguage(),
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
				'../introduction/edit.php?id=%s',
				true
			)
		);
		
	}

}