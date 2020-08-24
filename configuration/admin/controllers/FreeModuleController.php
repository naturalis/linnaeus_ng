<?php /** @noinspection PhpMissingParentCallMagicInspection */

include_once ('Controller.php');
include_once ('MediaController.php');

class FreeModuleController extends Controller
{

    private $_mc;

    public $usedModels=
		array(
			'free_modules_projects',
			'free_modules_pages',
			'content_free_modules',
			'free_module_media'
		);

    public $usedHelpers = array(
        'file_upload_helper',
        //'image_thumber_helper',
        'csv_parser_helper'
    );

    public $controllerPublicName = 'Free Modules';

    public $cssToLoad = array(
		'../vendor/prettyPhoto/css/prettyPhoto.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array(
		'all' => array(
			'freemodule.js',
			'lookup.js',
			'int-link.js'
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
		$this->UserRights->setModuleType($this->UserRights->getModuleTypeCustom());
		$this->UserRights->setFreeModuleId($this->getFreeModuleId());
		$this->controllerPublicName = $this->getActiveModuleName();
		$this->setMediaController();
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

	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());
        $this->_mc->setLanguageId($this->getDefaultProjectLanguage());
	}

    /**
     * Module index
     *
     * @access    public
     */
    public function indexAction()
    {

        $this->checkAuthorisation();

		if ($this->getActiveModule()==null || $this->rHasVal('freeId'))
		{
			$this->setActiveModule($this->getFreeModule());
			$this->controllerPublicName = $this->getActiveModuleName();
		}

		$this->cleanUpEmptyPages();

		if (!$this->rHasVal('page'))
		{
			$this->redirect('edit.php?id='.$this->getFirstPageId());
		}
		else
		{
			$this->redirect('edit.php?id='.$this->rGetVal('page'));
		}
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

    public function createPageAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();
		$this->redirect('edit.php?id=' . $this->createPage());
	}

    public function editAction()
    {
		$this->checkAuthorisation();

		if ($this->rHasVal('action','delete'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();

			$this->deletePage();
			$this->redirect('index.php');
		}
		else
		if ($this->rHasVal('action','deleteImage'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			//$this->deleteMedia();
			$this->detachAllMedia();

		}
		else
		if ($this->rHasVal('action','preview'))
		{
			$this->checkAuthorisation();

			$this->redirect('preview.php?id='.$this->rGetId());
		}
		
		// LINNA-1358
		$id = $this->rHasId() ? $this->rGetId() : null;
		if (is_null($id)) {
		    $this->createPageAction();
		}

		$page = $this->getPage();

		if ($page['got_content']==0)
		{
			$this->setPageName($this->translate('New page'));
		}
		else
		{
			$this->setPageName($this->translate('Editing page'));
		}

        // Override image
        $page['image'] = $this->getPageImage();

        $navList = $this->getModulePageNavList(true);

		if (isset($navList)) $this->smarty->assign('navList', $navList);
		if (isset($page)) $this->smarty->assign('page', $page);

		$this->smarty->assign('navCurrentId', $this->rGetId() ? $this->rGetId() : null);
		$this->smarty->assign('id', $id);
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('activeLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('includeHtmlEditor', true);
		$this->smarty->assign('module_id', $this->getCurrentModuleId());

        $this->printPage();
    }


    public function previewAction()
    {
		$this->redirect(
			'../../../app/views/module/topic.php?p='.$this->getCurrentProjectId().
			'&modId='.$this->getCurrentModuleId().
			'&id='.$this->rGetId().
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

		if ($this->rHasVal('letter'))
		{
		    $alphaIndex = $this->moduleSession->getModuleSetting('alphaIndex');
		    $refs = $alphaIndex[$this->rGetVal('letter')];
		    $this->smarty->assign('letter', $alphaIndex[$this->rGetVal('letter')]);
		}

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

		if ($this->requestDataFiles && !$this->isFormResubmit())
		{
			$filesToSave =  $this->getUploadedMediaFiles();
			$firstInsert = false;

			if ($filesToSave && $this->rHasId())
			{
			    $results = array('success' => [], 'failed' => []);

				foreach((array)$filesToSave as $key => $file)
				{

					$thumb = false;

					if ($this->helpers->ImageThumberHelper->canResize($file['mime_type']) &&
						$this->helpers->ImageThumberHelper->thumbnail($this->getProjectsMediaStorageDir().$file['name']))
					{
						$pi=pathinfo($file['name']);
						$this->helpers->ImageThumberHelper->size_width(150);

						if ($this->helpers->ImageThumberHelper->save($this->getProjectsThumbsStorageDir().$pi['filename'].'-thumb.'.$pi['extension']))
						{
							$thumb = $pi['filename'].'-thumb.'.$pi['extension'];
						}
					}

					$fmm = $this->models->FreeModuleMedia->save(
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
					    $msg = sprintf($this->translate('Saved: %s (%s)'),$file['original_name'],$file['media_name']);
					    $results['success'][] = $file['original_name'];

						$this->addMessage($msg);
					} else {
                        $results['failed'][] = $file['original_name'];

						$this->addError($this->translate('Failed writing uploaded file to database.'),1);
					}
				}
				$result_msg = "Uploaded media. ";
				foreach($results as $group => $files) {
				    if (count($files) > 0) {
				        $result_msg .= $group . ": " . implode(', ', $files);
                    }
                }

                $this->logChange(array('note' => $result_msg));
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


    /**
     * Module manage
     *
     * @access    public
     */
    public function manageAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();

		$this->setPageName($this->translate('Management'));

		if ($this->rHasVal('submit'))
		{
			$d =
				array(
					'id' => $this->getCurrentModuleId(),
					'project_id' => $this->getCurrentProjectId(),
					'show_alpha' => $this->rGetVal('show_alpha'),
				);

			$m = trim($this->rGetVal('module'));

			if (!empty($m)) $d['module'] = $m;

			$this->models->FreeModulesProjects->save($d);
			$module = $this->getFreeModule();
			$this->setActiveModule($module);
			$this->addMessage('Settings saved');
		}
		else
		{
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
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();

		$this->setPageName($this->translate('Change page order'));

		if ($this->rHasVal('dir') && $this->rHasId() && !$this->isFormResubmit())
		{
			$d = $this->getPages();
			$lowerNext = $prev = false;

			foreach((array)$d as $key => $val)
			{
				$newOrder = $key;

				if ($val['id'] == $this->rGetId())
				{
					if ($this->rHasVar('dir', 'up') && $prev)
					{
						$newOrder = $key - 1;

						 $this->models->FreeModulesPages->update(
							array(
								'show_order' => 'show_order + 1',
							),
							array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $prev
							)
						);

					} else
					if ($this->rHasVar('dir', 'down'))
					{
						$newOrder = $key + 1;
					}
					else
					{
						$newOrder = $key;
					}
				}

				if ($lowerNext)
				{
					$newOrder = $key - 1;
					$lowerNext = false;
				}

				 $this->models->FreeModulesPages->update(
				 	array(
						'show_order' => $newOrder,
					),
					array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['id']
					)
				);

				if ($val['id'] == $this->rGetId() && $this->rHasVar('dir', 'down'))
				{
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
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
		$this->checkAuthorisation();

		$this->setPageName($this->translate('CSV data import'));

		if ($this->requestDataFiles) { //  && !$this->isFormResubmit()) {

            $tmp = tempnam(sys_get_temp_dir(), 'lng');

			switch ($this->rGetVal('delimiter')) {
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
					'has_titles' => $this->rHasVar('has_titles') ? $this->rGetVar('has_titles') == '1' : false,
					'language' => $this->rGetVar('language')
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

		if ($this->rHasVar('reset'))
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

        if ($this->rHasVal('action', 'save_content'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if ( !$this->getAuthorisationState() ) return;
            $this->ajaxSaveContent();
        }
		elseif ($this->rHasVal('action', 'get_content'))
		{
			if ( !$this->getAuthorisationState() ) return;
            $this->ajaxActionGetContent();
        }
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->rGetVal('search')))
		{
			if ( !$this->getAuthorisationState() ) return;
            $this->getLookupList($this->rGetVal('search'));
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
					'id' => $this->rGetId(),
					'language' => $this->rGetVal('language'),
					'topic' => $this->rGetVal('topic'),
					'content' => $this->rGetVal('content'),
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

			$this->models->ContentFreeModules->delete(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'module_id' => $this->getCurrentModuleId(),
					'language_id' => $language,
					'page_id' => $id
				)
			);

			$this->setPageGotContent($id);

		} else {

			$cfm = $this->models->ContentFreeModules->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'module_id' => $this->getCurrentModuleId(),
						'language_id' => $language,
						'page_id' => $id
					)
				)
			);

			$this->models->ContentFreeModules->save(
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

			$cfm = $this->models->ContentFreeModules->_get(
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

			$page = $this->getPageContent($this->rGetId(),$this->rGetVal('language'));

            $this->smarty->assign('returnText', json_encode(array('topic' => $page['topic'],'content' => $page['content'])));

        }

	}

	protected function getCurrentModuleId()
	{

		$activeModule = $this->moduleSession->getModuleSetting('activeModule');

	    return isset($activeModule['id']) ? $activeModule['id'] : false;

	}
	
	private function isUserAuthorizedForFreeModule($id=null)
	{

		$id = isset($id) ? $id : $this->rGetVal('freeId');

		if (!isset($id)) return false;

		$fmpu = $this->models->FreeModulesProjectsUsers->_get(
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


	private function getFreeModule ($id=null)
	{

		$id = isset($id) ? $id : $this->getFreeModuleId();

		$fmp = $this->models->FreeModulesProjects->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

        if ($fmp) return $fmp[0];

	}
	
	private function getFreeModuleId () 
	{
	    return !is_null($this->rGetVal('freeId')) ?
	       $this->rGetVal('freeId') :
	       $this->getCurrentModuleId();
	}

	private function createPage()
	{

	    if (!$this->getCurrentModuleId()) return;

		$this->models->FreeModulesPages->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId()
			)
		);

		return $this->models->FreeModulesPages->getNewId();

	}

	private function setPageGotContent($id,$state=null)
	{

		if (!$this->getCurrentModuleId()) return;

		if ($state==null) {

			$cfm = $this->models->ContentFreeModules->_get(
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

		$this->models->FreeModulesPages->update(
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

		$fmp = $this->models->FreeModulesPages->_get(
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

		$id = isset($id) ? $id : $this->rGetId();

		if (!isset($id)) return;

		$pfm = $this->models->FreeModulesPages->_get(
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

		$cfm = $this->models->ContentFreeModules->_get(
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
		$this->models->FreeModulesPages->delete('delete from %table%
			where module_id = '.$this->getCurrentModuleId().'
			and project_id =  '.$this->getCurrentProjectId().'
			and got_content = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)');

	}

	private function getActualAlphabet()
	{

		$this->moduleSession->setModuleSetting(array('setting' => 'alphaIndexs'));

		$cfm = $this->models->ContentFreeModules->_get(
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

		$alpha = $alphaIndex = null;

		foreach((array)$cfm as $key => $val) {

			$alpha[$val['letter']] = $val['letter'];

			$alphaIndex[$val['letter']][] = array('id' => $val['page_id'],'topic' => $val['topic']);

			//$_SESSION['admin']['system']['freeModule']['alphaIndex'][$val['letter']][] = array('id' => $val['page_id'],'topic' => $val['topic']);

		}

		$this->moduleSession->setModuleSetting(array(
            'setting' => 'alphaIndex',
            'value' => $alphaIndex
        ));

		return $alpha;

	}

	private function deleteMedia($id=null)
	{

		$id = isset($id) ? $id : ($this->rGetId() ? $this->rGetId() : null);

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

		$id = isset($id) ? $id : ($this->rGetId() ? $this->rGetId() : null);

		if ($id == null || !$this->getCurrentModuleId()) return;

		//$this->deleteMedia($id);
		$this->detachAllMedia();

		$this->models->ContentFreeModules->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId(),
				'page_id' => $id
			)
		);

		$this->models->FreeModulesPages->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId()
			)
		);

	}

	private function getModulePageNavList($forceLookup=true)
	{

	    $moduleId = $this->moduleSession->getModuleSetting($this->getCurrentModuleId());

		if (empty($moduleId['navList']) || $forceLookup) {

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

			$moduleId['navList'] = isset($res) ? $res : null;

			$this->moduleSession->setModuleSetting(array(
                'setting' => $this->getCurrentModuleId(),
                'value' => $moduleId
            ));

		}

		return $moduleId['navList'];

	}

	private function getLookupList($search)
	{

		if (empty($search)) return;

		$cfm = $this->models->ContentFreeModules->_get(
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

        if (!empty($img) && $img[0]['media_type'] ==  'image') {
            return $img[0];
        }

        return null;
    }


}
