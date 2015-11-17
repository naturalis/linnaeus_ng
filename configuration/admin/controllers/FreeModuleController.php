<?php

include_once ('Controller.php');

class FreeModuleController extends Controller
{

    public $usedModels = array(
		'free_module_project',
		'free_module_project_user',
		'free_module_page',
		'content_free_module',
		'free_module_media'
    );

    public $usedHelpers = array(
        'file_upload_helper',
        'image_thumber_helper',
        'csv_parser_helper'
    );

    public $controllerPublicName = 'Free Modules';

    public $cssToLoad = array(
		'prettyPhoto/prettyPhoto.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
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

		$this->controllerPublicName = $this->getActiveModuleName();

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

		// check if user is logged in, otherwise redirect to login page
		if (!$this->isUserLoggedIn()) return false;

		// check if there is an active project, otherwise redirect to choose project page
		if (!$this->getCurrentProjectId() && !$allowNoProjectId) return false;

		if ($this->isCurrentUserSysAdmin()) return true;

		if ($this->rHasVal('freeId')) {

			if (!$this->isUserAuthorizedForFreeModule())
				$this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);

		} else {

			if (is_null($this->getActiveModule()))
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

		if ($this->getActiveModule()==null || $this->rHasVal('freeId')) {

			$this->setActiveModule($this->getFreeModule());

			$this->controllerPublicName = $this->getActiveModuleName();

		}

		$this->cleanUpEmptyPages();

		if (!$this->rHasVal('page'))
			$this->redirect('edit.php?id='.$this->getFirstPageId());
		else
			$this->redirect('edit.php?id='.$this->requestData['page']);


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

        $pagination = $this->getPagination($this->getPages(),25);

        $this->smarty->assign('prevStart', $pagination['prevStart']);

        $this->smarty->assign('nextStart', $pagination['nextStart']);

        $this->smarty->assign('pages',$pagination['items']);

        $this->printPage();

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

		$navList = $this->getModulePageNavList(true);

		if (isset($navList)) $this->smarty->assign('navList', $navList);
			$this->smarty->assign('navCurrentId',isset($this->requestData['id']) ? $this->requestData['id'] : null);

		$this->smarty->assign('id', $this->rHasId() ? $this->requestData['id'] : $id);

		if (isset($page)) $this->smarty->assign('page', $page);

		$this->smarty->assign('languages', $this->getProjectLanguages());

		$this->smarty->assign('activeLanguage', $this->getDefaultProjectLanguage());

		$this->smarty->assign('includeHtmlEditor', true);

        $this->printPage();

    }


    public function previewAction()
    {

		$this->redirect(
			'../../../app/views/module/topic.php?p='.$this->getCurrentProjectId().
			'&modId='.$this->getCurrentModuleId().
			'&id='.$this->requestData['id'].
			'&lan='.$this->getDefaultProjectLanguage()
		);

    }

    public function browseAction()
    {

        $this->checkAuthorisation();

		$this->setPageName($this->translate('Browsing pages'));

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
     * Upload media for a page
     *
     * @access    public
     */
    public function mediaUploadAction ()
    {

		$this->checkAuthorisation();

		$this->setPageName($this->translate('New image'));

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
     * Module manage
     *
     * @access    public
     */
    public function manageAction()
    {

        $this->checkAuthorisation();

		$this->setPageName($this->translate('Management'));

		if ($this->rHasVal('submit')) {

			$d = array(
					'id' => $this->getCurrentModuleId(),
					'project_id' => $this->getCurrentProjectId(),
					'show_alpha' => $this->requestData['show_alpha'],
				);

			$m = trim($this->requestData['module']);

			if (!empty($m)) $d['module'] = $m;

			$this->models->FreeModuleProject->save($d);

			$module = $this->getFreeModule();

			$this->setActiveModule($module);

			$this->addMessage('Settings saved');

		} else {

			$module = $this->getFreeModule();

		}

		$this->smarty->assign('module',$module);

        $this->printPage();

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

		if ($this->rHasVal('dir') && $this->rHasId() && !$this->isFormResubmit()) {



			$d = $this->getPages();

			$lowerNext = $prev = false;

			foreach((array)$d as $key => $val) {

				$newOrder = $key;

				if ($val['id'] == $this->requestData['id']) {

					if (($this->requestData['dir']=='up') && $prev) {

						$newOrder = $key - 1;

						 $this->models->FreeModulePage->update(
							array(
								'show_order' => 'show_order + 1',
							),
							array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $prev
							)
						);

					} else
					if ($this->requestData['dir']=='down') {

						$newOrder = $key + 1;

					} else {

						$newOrder = $key;

					}

				}

				if ($lowerNext) {

					$newOrder = $key - 1;

					$lowerNext = false;

				}

				 $this->models->FreeModulePage->update(
				 	array(
						'show_order' => $newOrder,
					),
					array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['id']
					)
				);

				if ($val['id'] == $this->requestData['id'] && $this->requestData['dir']=='down') {

					$lowerNext = true;

				}

				$prev = $val['id'];

			}

		}

		$this->smarty->assign('pages',$this->getPages());

        $this->printPage();

	}


    /**
     * Import texts
     *
     * @access    public
     */
    public function importAction ()
    {

		$this->checkAuthorisation();

		$this->setPageName($this->translate('CSV data import'));

		if ($this->requestDataFiles) { //  && !$this->isFormResubmit()) {

            $tmp = tempnam(sys_get_temp_dir(), 'lng');

			switch ($this->requestData["delimiter"]) {
				case 'comma' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(',');
					break;
				case 'semi-colon' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(';');
					break;
				case 'tab' :
					$this->helpers->CsvParserHelper->setFieldDelimiter("\t");
					break;
			}

			$this->helpers->CsvParserHelper->parseFile($this->requestDataFiles[0]['tmp_name']);

			$this->addError($this->helpers->CsvParserHelper->getErrors());

			if (!$this->getErrors()) {

			    $import = array(
					'data' => $this->helpers->CsvParserHelper->getResults(),
					'has_titles' => isset($this->requestData["has_titles"]) ? $this->requestData["has_titles"]=='1' : false,
					'language' => $this->requestData["language"]
				);

			    $this->moduleSession->setModuleSetting(array(
                    'setting' => 'import',
                    'value' => $import
			    ));

				$this->smarty->assign('has_titles',$import['has_titles']);
				$this->smarty->assign('data',$import['data']);

            }

		} else
		if ($this->rHasVal('action','import')) {

		    $import = $this->moduleSession->getModuleSetting('import');

			foreach((array)$import['data'] as $key => $val) {

				if ($import['has_titles'] && $key==0)
					continue;

				$p = array(
					'id' => $this->createPage(),
					'topic' => $val[0],
					'content' => $val[1],
					'language' => $import['language']
				);

				if ($this->saveContent($p))
					$this->addMessage(sprintf($this->translate('Saved page "%s".'),htmlentities($val[0])));
				else
					$this->addMessage(sprintf($this->translate('Couldn\'t save page "%s".'),$val[0]));

			}

			$this->moduleSession->setModuleSetting(array('setting' => 'import'));
			$this->smarty->assign('imported',true);

		}

		if (isset($this->requestData["reset"]))
			$this->moduleSession->setModuleSetting(array('setting' => 'import'));

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



            $this->ajaxSaveContent();

        } else
        if ($this->requestData['action'] == 'get_content') {

            $this->ajaxActionGetContent();

        }
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        }

        $this->printPage();

    }

	private function setActiveModule($data)
	{

		$this->moduleSession->setModuleSetting(array(
            'setting' => 'activeModule',
            'value' => $data
        ));

	}

	private function getActiveModule()
	{

	    return $this->moduleSession->getModuleSetting('activeModule');

	}

	private function getActiveModuleName()
	{

	    $activeModule = $this->moduleSession->getModuleSetting('activeModule');
		return isset($activeModule['module']) ? $activeModule['module'] : null;

	}

	private function ajaxSaveContent()
	{

		if (!$this->rHasId() || !$this->rHasVal('language'))
			return;

		if (
			$this->saveContent(
				array(
					'id' => $this->requestData['id'],
					'language' => $this->requestData['language'],
					'topic' => isset($this->requestData['topic']) ? $this->requestData['topic'] : null,
					'content' => isset($this->requestData['content']) ? $this->requestData['content'] : null,
				)
			)
		) $this->smarty->assign('returnText', 'saved');

	}


	private function saveContent($p)
	{

		$id = isset($p['id']) ? $p['id'] : null;
		$language = isset($p['language']) ? $p['language'] : $this->getDefaultProjectLanguage();
		$topic = isset($p['topic']) ? trim($p['topic']) : null;
		$content = isset($p['content']) ? trim($p['content']) : null;

		if (empty($id))
			return false;

		if (empty($topic) && empty($content)) {

			$this->models->ContentFreeModule->delete(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'module_id' => $this->getCurrentModuleId(),
					'language_id' => $language,
					'page_id' => $id
				)
			);

			$this->setPageGotContent($id);

		} else {

			$cfm = $this->models->ContentFreeModule->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'module_id' => $this->getCurrentModuleId(),
						'language_id' => $language,
						'page_id' => $id
					)
				)
			);

			$this->models->ContentFreeModule->save(
				array(
					'id' => isset($cfm[0]['id']) ? $cfm[0]['id'] : null,
					'project_id' => $this->getCurrentProjectId(),
					'module_id' => $this->getCurrentModuleId(),
					'language_id' => $language,
					'page_id' => $id,
					'topic' => $topic,
					'content' => $content
				)
			);

			$this->setPageGotContent($id,true);

			$tmp = $this->moduleSession->getModuleSetting($this->getCurrentModuleId());
            unset($tmp['navList']);
            $this->moduleSession->setModuleSetting(array(
                'setting' => $this->getCurrentModuleId(),
                'value' => $tmp
            ));

        }

		return true;

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
				),
				'order' => 'show_order'
			)
		);

		foreach((array)$fmp as $key => $val) {

			$d = $this->getPageContent($val['id'],$this->getDefaultProjectLanguage());

			$fmp[$key]['topic'] = $d['topic'];

		}

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
					'language_id' => $this->getDefaultProjectLanguage()
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
					'language_id' => $this->getDefaultProjectLanguage(),
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
			$this->makeLookupList(array(
				'data'=>$cfm,
				'module'=>$this->controllerBaseName,
				'url'=>'../module/edit.php?id=%s',
				'sortData'=>true
			))
		);

	}

}