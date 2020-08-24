<?php

include_once ('Controller.php');
include_once ('MediaController.php');
include_once ('ModuleSettingsReaderController.php');

class IntroductionController extends Controller
{

    private $_mc;
	private $use_media;

    public $usedModels = array(
		'content_introduction',
		'introduction_media',
		'introduction_pages'
    );

    public $usedHelpers = array(
        'file_upload_helper',
		'image_thumber_helper',
		'session_module_settings'
    );

    public $controllerPublicName = 'Introduction';

	public $cssToLoad = array(
		'../vendor/prettyPhoto/css/prettyPhoto.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css',
	);

	public $jsToLoad = array(
		'all' => array(
            '../vendor/tinymce/tinymce.min.js',
			'freemodule.js',
			'lookup.js',
			'int-link.js'
		)
	);

    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
	}
		
    private function initialize()
    {
		$this->cleanUpEmptyPages();

		$this->moduleSettings=new ModuleSettingsReaderController;
		
		$this->use_media=$this->moduleSettings->getModuleSetting( [ 'setting'=>'no_media','subst'=>0 ] )!=1;
		
		if ( $this->use_media )
		{
			$this->setMediaController();
		}

		$this->smarty->assign( 'use_media', $this->use_media );
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());
        $this->_mc->setLanguageId($this->getDefaultProjectLanguage());
	}

    public function contentsAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Contents'));

        $pagination = $this->getPagination($this->getPageHeaders(),25);

        $this->smarty->assign( 'prevStart', $pagination['prevStart'] );
        $this->smarty->assign( 'nextStart', $pagination['nextStart'] );
        $this->smarty->assign( 'pages', $pagination['items'] );
		$this->smarty->assign( 'CRUDstates', $this->getCRUDstates() );

        $this->printPage();
    }

    public function indexAction()
    {
        $this->checkAuthorisation();

		if (!$this->rHasVal('page'))
		{
			$this->redirect('edit.php?id=' . $this->getFirstPageId());
		}
		else
		{
			$this->redirect('edit.php?id=' . $this->rGetVal('page'));
		}
    }

    public function editAction()
    {
		$this->checkAuthorisation();

		if (!$this->rHasId()) {
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

			$id = $this->createPage();
			$this->logChange(array('after'=>["id"=>$id],'note'=>'created new page'));

			// redirecting to protect against resubmits
			if ($id) {
				$this->redirect('edit.php?id=' . $id);
			} else {
				$this->addError($this->translate('Could not create page.'));
			}
		} else {
			if ($this->rHasVal('action','delete'))
			{
				$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
				$this->checkAuthorisation();
				$before=$this->getPage();
				$this->deletePage();

				$this->logChange(array('before'=>$before,'note'=>'deleted page '.$before['topic']));

				$this->redirect('index.php');
			} else if ($this->rHasVal('action','deleteImage')) {
				$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
				$this->checkAuthorisation();
				//$this->deleteMedia();
				$before=$this->getPage();
				$this->detachAllMedia();

				$this->logChange(array('before'=>$before,'after'=>$this->getPage(),'note'=>'deleted media from '.$before['topic']));
			} else if ($this->rHasVal('action','preview')) {
				$this->saveAllContent($this->rGetAll());
				$this->redirect('preview.php?id=' . $this->rGetId());
			}

			$page=$this->getPage();

			if ($page['got_content']==0)
			{
		        $this->setPageName($this->translate('Creating new page'));
			} else {
		        $this->setPageName($this->translate('Editing page'));
			}
		}

		if ( $this->use_media )
		{
			// Override image
			$page['image'] = $this->getPageImage();
		}

		$navList = $this->getPageNavList(true);

		if ( isset($navList) ) {
		    $this->smarty->assign('navList', $navList);
        }
		if ( isset($page) ) {
		    $this->smarty->assign('page', $page);
        }

		$this->smarty->assign( 'navCurrentId', $this->rHasId() ? $this->rGetId() : null );
		$this->smarty->assign( 'id', $this->rHasId() ? $this->rGetId() : $id );
		$this->smarty->assign( 'languages', $this->getProjectLanguages() );
		$this->smarty->assign( 'activeLanguage', $this->getDefaultProjectLanguage() );
		$this->smarty->assign( 'includeHtmlEditor', true );
		$this->smarty->assign( 'module_id', $this->getCurrentModuleId() );
		$this->smarty->assign( 'CRUDstates', $this->getCRUDstates() );

        $this->printPage();
    }

    public function previewAction()
    {
		$this->redirect(
			'../../../app/views/introduction/topic.php?p='.$this->getCurrentProjectId().
			'&id='.$this->rGetId().
			'&lan='.$this->getDefaultProjectLanguage()
		);
	}

    public function orderAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
        $this->checkAuthorisation();

		$this->setPageName($this->translate('Change page order'));

		if ($this->rHasVal('newOrder') && !$this->isFormResubmit())
		{
			foreach((array)$this->rGetVal('newOrder') as $key=>$val)
			{
				 $this->models->IntroductionPages->update(
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

		} else if ($this->rHasVal('sortAlpha') && !$this->isFormResubmit()) {
			$d = $this->getPageHeaders();

			$this->customSortArray($d, array(
				'key' => 'label',
				'dir' => 'asc',
				'case' => 'i'
			));

			foreach((array)$d as $key => $val) {
				 $this->models->IntroductionPages->update(
					array(
						'show_order' => $key + 1,
					),
					array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['id']
					)
				);
			}

			$this->addMessage($this->translate('Alphabetic order saved.'));
		}

		$this->smarty->assign('pages',$this->getPageHeaders());
        $this->printPage();
	}

    public function mediaUploadAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

		$this->setPageName($this->translate('New image'));

		$this->loadControllerConfig('Module');

		if ($this->requestDataFiles && !$this->isFormResubmit())
		{
			$filesToSave =  $this->getUploadedMediaFiles();

			$firstInsert = false;

			if ($filesToSave && $this->rHasId())
			{
                $results = array('updated' => [], 'saved' => [], 'failed' => []);

				foreach((array)$filesToSave as $key => $file)
				{

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
							'page_id' => $this->rGetId(),
							'file_name' => $file['name'],
							'original_name' => $file['original_name'],
							'mime_type' => $file['mime_type'],
							'file_size' => $file['size'],
							'thumb_name' => $thumb ? $thumb : null,
						)
					);

					if ($fmm) {
                        $results['success'][] = $file['original_name'];
						$this->addMessage(sprintf($this->translate('Saved: %s (%s)'),$file['original_name'],$file['media_name']));
					} else {
                        $results['failed'][] = $file['original_name'];
						$this->addError($this->translate('Failed writing uploaded file to database.'),1);
					}

				}
                $msg = "Media upload. ";
                foreach($results as $name => $files) {
                    if (count($files) > 0) {
                        $msg .= "  " . $name . ": " . implode(', ', $files);
                    }
                }
                $this->logChange(array('note' => $msg));
			}

		}

		$this->smarty->assign('id',$this->rGetId());
		$this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);
		$this->smarty->assign('iniSettings',
			array(
				'upload_max_filesize' => ini_get('upload_max_filesize'),
				'post_max_size' => ini_get('post_max_size')
			)
		);

		$this->printPage();

    }

    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action', 'save_content'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if ( $this->getAuthorisationState() )
			{
	            if ($this->saveContent($this->rGetAll()))
				{
					$this->smarty->assign('returnText', 'saved');
				}
			}
        }
		else if ($this->rHasVal('action', 'get_content'))
		{
			if ( $this->getAuthorisationState() )
			{
	            $this->ajaxActionGetContent();
			}
        }
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->rGetVal('search')))
		{
			if ( $this->getAuthorisationState() )
			{
	    	    $this->getLookupList($this->rGetVal('search'));
			}
        }

		$this->allowEditPageOverlay = false;
        $this->printPage();
    }

	private function saveAllContent( $p=null )
	{
		if (!isset($p['id']) || !isset($p['language-default']))
			return;

		$this->saveContent(
			array(
				'id' => $p['id'],
				'language' => $p['language-default'],
				'topic' => $p['topic-default'],
				'content' => $p['content-default']
			)
		);

		if (isset($p['language-other']))
		{
			$this->saveContent(
				array(
					'id' => $p['id'],
					'language' => $p['language-other'],
					'topic' => $p['topic-other'],
					'content' => $p['content-other']
				)
			);
		}

		return true;

	}

	private function saveContent( $p=null )
	{
		$id = isset($p['id']) ? $p['id'] : null;
		$language = isset($p['language']) ? $p['language'] : null;
		$topic = isset($p['topic']) ? $p['topic'] : null;
		$content = isset($p['content']) ? $p['content'] : null;
		$hide_from_index = isset($p['hide_from_index']) ? $p['hide_from_index']==1 : false;

		if (!isset($id) || !isset($language))
		{
			return;
		}
		else
		{
            if (!isset($topic) && !isset($content))
			{
                $this->models->ContentIntroduction->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $language,
						'page_id' => $id
					)
                );

				$this->setPageGotContent($id);
            }
			else
			{
                $before = $this->models->ContentIntroduction->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $language,
							'page_id' => $id
						)
					)
				);

                $this->models->ContentIntroduction->save(
					array(
						'id' => isset($before[0]['id']) ? $before[0]['id'] : null,
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $language,
						'page_id' => $id,
						'topic' => trim($topic),
						'content' => trim($content)
					)
				);

                $after = $this->models->ContentIntroduction->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(),
                            'language_id' => $language,
                            'page_id' => $id
                        )
                    )
                );
                
                $this->logChange(array(
                    'before'=>$before,
                    'after'=>$after,
                    'note'=>'saved introduction' . (!empty($before['topic']) ? ' ' . $before['topic'] : '')
                ));

				$this->setPageGotContent($id,true);

            }

			$this->models->IntroductionPages->save(
				array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
					'hide_from_index' => $hide_from_index ? '1' : 'null'
				)
			);

			$this->moduleSession->setModuleSetting( array('setting'=>'navList' ) );

			return true;

        }

	}

	private function ajaxActionGetContent()
	{
        if (!$this->rHasVal('id') || !$this->rHasVal('language'))
		{
            return;
        }
		else
		{
			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->rGetVal('language'),
						'page_id' => $this->rGetId(),
						)
				)
			);

            $this->smarty->assign('returnText', json_encode(array('topic' => $cfm[0]['topic'],'content' => $cfm[0]['content'])));
        }
	}

	private function getNextShowOrderValue()
	{
		$ip=$this->models->IntroductionPages->_get(
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
		$this->models->IntroductionPages->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'show_order' => $this->getNextShowOrderValue()
			)
		);

		return $this->models->IntroductionPages->getNewId();
	}

	private function setPageGotContent($id,$state=null)
	{
		if ($state==null)
		{
			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $id
					),
					'columns' => 'count(*) as total'
				)
			);

			$state=($cfm[0]['total']==0 ? false : true);
		}

		$this->models->IntroductionPages->update(
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
		$ip =  $this->models->IntroductionPages->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				),
				'order' => 'show_order,created'
			)
		);

		foreach((array)$ip as $key => $val)
		{
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
		$id = isset($id) ? $id : $this->rGetId();

		if (!isset($id)) return;

		$pfm = $this->models->IntroductionPages->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		if ($pfm)
		{
			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => isset($languageId) ? $languageId : $this->getDefaultProjectLanguage(),
						'page_id' => $this->rGetId(),
						)
				)
			);

			if ($cfm)
			{
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

			if ($fmm)
			{
				$pfm[0]['image']['file_name'] = $fmm[0]['file_name'];
				$pfm[0]['image']['thumb_name'] = $fmm[0]['thumb_name'];
			}

			return $pfm[0];

		}
		else
		{
			return null;
		}
	}

	private function getFirstPageId()
	{
		$ip=$this->models->IntroductionPages->_get(
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
		$this->models->IntroductionPages->delete(array(
			'project_id'=> $this->getCurrentProjectId(),
			'got_content'=> 0,
			'created #' => '< DATE_ADD(now(), INTERVAL -7 DAY)'
		));
	}

	private function deleteMedia($id=null)
	{
		$id = isset($id) ? $id : ($this->rHasId() ? $this->rGetId() : null);

		if ($id == null) return;

		$fmm = $this->models->IntroductionMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'page_id' => $id
				)
			)
		);

		$f=$this->getProjectsMediaStorageDir() . $fmm[0]['file_name'];
		$t=$this->getProjectsThumbsStorageDir() . $fmm[0]['thumb_name'];

		if (file_exists($f)) unlink($f);
		if (file_exists($t)) unlink($t);

		$this->models->IntroductionMedia->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'page_id' => $id
			)
		);
	}

	private function deletePage($id=null)
	{
		$id = isset($id) ? $id : ($this->rHasId() ? $this->rGetId() : null);

		if ($id == null) return;

		//$this->deleteMedia($id);
		$this->detachAllMedia();

		$this->models->ContentIntroduction->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'page_id' => $id
			)
		);

		$this->models->IntroductionPages->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);
	}

	private function getPageNavList($forceLookup=false)
	{
		if (null!==$this->moduleSession->getModuleSetting( 'navList' ) || $forceLookup)
		{

			$d = $this->getPageHeaders();

			foreach((array)$d as $key => $val)
			{
				$res[$val['id']] = array(
					'prev' => array('id' => isset($d[$key-1]['id']) ? $d[$key-1]['id'] : null),
					'next' => array('id' => isset($d[$key+1]['id']) ? $d[$key+1]['id'] : null),
				);
			}

			if (isset($res))
				$this->moduleSession->setModuleSetting( array('setting'=>'navList','value'=>$res ) );
			else
				$this->moduleSession->setModuleSetting( array('setting'=>'navList' ) );
		}

		return $this->moduleSession->getModuleSetting( 'navList' );

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
			$this->makeLookupList(array(
				'data'=>$cfm,
				'module'=>$this->controllerBaseName,
				'url'=>'../introduction/edit.php?id=%s',
				'sortData'=>true
			))
		);

	}


    private function detachAllMedia ()
    {
        if (empty($this->_mc)) {
            return false;
        }
        
        $media = $this->_mc->getItemMediaFiles();

        if (!empty($media)) {
            foreach ($media as $item) {
                $this->_mc->deleteItemMedia($item['id']);
            }
        }
    }

    private function getPageImage ()
    {
		$img = $this->_mc->getItemMediaFiles();

        if (!empty($img) && $img[0]['media_type'] == 'image') {
            return $img[0];
        }

        return null;
    }

}
