<?php

/*
 * To do
 *
 * - check mime type on upload
 * - check max_file_uploads, memory_limit, post_max_size
 *
 *
 */

include_once ('Controller.php');
include_once ('ModuleIdentifierController.php');

class MediaController extends Controller
{

    private $_rsBaseUrl;
    private $_rsMasterKey;
    private $_rsUserKey;
    private $_rsCollectionId;
    private $_rsSearchApi;
    private $_rsUploadApi;
    private $_rsSearchUrl;
    private $_rsUploadUrl;
    private $_rsNewUserUrl;

    private $_result;
    private $_files;
    private $_uploaded;

    private $moduleId;
    private $itemId;
    private $languageId;

    public static $metadataFields = array('title', 'location', 'photographer');

    public $usedModels = array(
        'media',
        'media_metadata',
        'media_modules',
        'media_captions',
        'media_tags'
    );

    public $usedHelpers = array('hr_filesize_helper');

    public $cssToLoad = array('media.css');

    public $jsToLoad = array();

    public $controllerPublicName = 'Media';
    public $modelNameOverride = 'MediaModel';

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
        $this->initialize();
     }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize ()
    {
        $this->moduleId = $this->rHasVal('module_id') ? $this->rGetVal('module_id') : -1;
        $this->itemId = $this->rHasVal('item_id') ? $this->rGetVal('item_id') : -1;
        $this->languageId = $this->rHasVar('language_id') ?
            $this->rGetVal('language_id') : $this->getDefaultProjectLanguage();

        $this->setRsSettings();
    }

    public function setModuleId ($id)
    {
        $this->moduleId = isset($id) && is_numeric($id) ? $id : -1;
    }

    public function setItemId ($id)
    {
        $this->itemId = isset($id) && is_numeric($id) ? $id : -1;
    }

    public function setLanguageId ($id)
    {
        $this->languageId = isset($id) && is_numeric($id) ?
            $id : $this->getDefaultProjectLanguage();
    }

    private function setRsBaseUrl ()
    {
        // To do: get from config
        $this->_rsBaseUrl = 'https://rs.naturalis.nl/plugins/';
        //$this->_rsBaseUrl = 'http://localhost/resourcespace/plugins/';
    }

    private function setRsMasterKey ()
    {
        // To do: get from config
        $this->_rsMasterKey = 'cmVpbHtldSVyLDs0KjEyZCd4ciwncGlhITY1ZHErJi0hJGw0cjlmZnMtJCRydzVkdjFnZHR7LXAhJD9gIzhmNXcucSIiJg,,';
        //$this->_rsMasterKey = 'LDpWw7lzzeM5Vbck0K5N2JBjn2nQiobY6Qxr195NPzqXxXNfeDIjqd96JuomeuIo1Gi8TsM7JjR8d7WgCvX-a8P3mz7WWHgfnZIyJ5KF1UAKAkV3VCjk0zt3Qyo3I4jM';
    }

    private function setRsUserKey ()
    {
        // To do: get from config

        // user: Diaspididae of the World 2.0 @ localhost
        // password: 96b5f659
        $this->_rsUserKey = 'V2hldmVwcHx0dGglfGckcX18NEJ_Z2FhMzMqNTVZNHl_dmxpe253cWl9IS0jIzxmIjJmMCwvcXAoJjw1IGIyNCYrcSEmd2wzIThgYSEhd3BzIGkycjliMSUqLCwgcGtnKjRlNyIsJy0o';
        //$this->_rsUserKey = 'mgnwUMXYDrxXQIWaA5V-ZzioDNExJhVwHQ-8scfwAawkfFAf8su4znTstVSIkMATeNA0pMhPQYok0C_if_k-sKNOL7uZ9o_VCesg6_Wi5j8Dbcs7yUud2tu05N4gGKMk';
    }

    private function setRsCollectionId ()
    {
        // To do: get from config
        $this->_rsCollectionId = 2;
        //$this->_rsCollectionId = 5;
    }

    private function setRsSearchApi ()
    {
        // To do: get from config
        $this->_rsSearchApi = 'api_search_lng';
    }

    private function setRsNewUserApi ()
    {
        // To do: get from config
        $this->_rsNewUserApi =  'api_new_user_lng';
    }

    private function setRsUploadApi ()
    {
        // To do: get from config
        $this->_rsUploadApi =  'api_upload_lng';
    }

    private function resetMediaController ()
    {
        $this->_uploaded = $this->_result = $this->_files =
            $this->errors = $this->_rsIds = array();
    }

    private function addUploaded ($e)
    {
        $this->_uploaded[] = $e;
    }

    private function setRsSettings ()
    {
        $this->setRsBaseUrl();
        $this->setRsMasterKey();
        $this->setRsUserKey();
        $this->setRsCollectionId();
        $this->setRsSearchApi();
        $this->setRsNewUserApi();
        $this->setRsUploadApi();

        // Search url: &search=[term]* to be appended
        $this->_rsSearchUrl = $this->_rsBaseUrl . $this->_rsSearchApi . '/?' .
            'key=' . $this->_rsUserKey . '&prettyfieldnames=true&collection=' .
            $this->_rsCollectionId;
       // Upload url: &field8=[title] to be appended
        $this->_rsUploadUrl = $this->_rsBaseUrl . $this->_rsUploadApi . '/?' .
            'key=' . $this->_rsUserKey . '&collection=' . $this->_rsCollectionId;
        // New user url; newuser appended in createUser()
        $this->_rsNewUserUrl = $this->_rsBaseUrl . $this->_rsNewUserApi . '/?' .
            'key=' . $this->_rsMasterKey;
    }

    public function ajaxInterfaceAction ()
    {
        if ($this->rHasVal('action', 'upload_progress')) {
            $this->smarty->assign('returnText', $this->getUploadProgress('media'));
        }
        return false;
    }

    public function indexAction ()
    {
        $this->checkAuthorisation();
        $this->printPage();
    }

    public function selectRsAction ()
    {
        $this->checkAuthorisation();
        $this->resetMediaController();

        // global get module id?
        //$this->smarty->assign('module_id', $this->getCurrentModuleId());
        $this->smarty->assign('media', $this->getRsMediaList());
        //print_r($this->media->getMediaList());

        $this->printPage();
    }

    public function selectAction ()
    {
        $this->checkAuthorisation();
        $this->resetMediaController();
        $this->setItemTemplate();

        if ($this->rHasVal('action', 'delete')) {
            $this->deleteMedia();
        }

        if ($this->rHasVal('action', 'attach')) {
            $this->attachMedia();
        }

        $this->smarty->assign('media', $this->getMediaList());
        $this->smarty->assign('module_id', $this->rGetVal('module_id'));
        $this->smarty->assign('item_id', $this->rGetVal('item_id'));

        $this->printPage();
    }

    public function searchAction ()
    {
        $this->checkAuthorisation();
        $this->resetMediaController();

        foreach ($this::$metadataFields as $f) {
            $search['metadata'][$f] = $this->rGetVal($f);
        }
        $search['tags'] =
            array_unique(array_map('trim', explode(',', $this->rGetVal('tags'))));
        $search['file_name'] = $this->rGetVal('file_name');

        if ($this->rHasVal('action', 'search') && $this->arrayHasData($search)) {
            $result = $this->getMediaList(array('search' => $search));
            $this->smarty->assign('media', $result);
        }

		$this->smarty->assign('metadata', $search['metadata']);
		$this->smarty->assign('tags', $this->rGetVal('tags'));
		$this->smarty->assign('file_name', $search['file_name']);

		$this->printPage();
    }

    public function editAction ()
    {
        $this->checkAuthorisation();
        $this->resetMediaController();

        $id = $this->rGetVal('id');

        // Save button has been pushed (language switch should not trigger save)
        if ($this->rHasVal('save', $this->translate('save'))) {
            $this->saveMetadata($id);
            $this->saveTags($id);
        }

        $media = $this->getMedia($id);

		$this->smarty->assign('source', $media['rs_original']);
		$this->smarty->assign('name', $media['name']);
		$this->smarty->assign('metadata', $this->setMetadataFields($media['metadata']));
		$this->smarty->assign('tags', implode(', ', $media['tags']));
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id', $this->languageId);
		$this->smarty->assign('module_id', $this->moduleId);
		$this->smarty->assign('rs_id', $media['rs_id']);
		$this->smarty->assign('item_id', $this->itemId);

		$this->printPage();
    }

    public function uploadAction ()
    {
        $this->checkAuthorisation();
        $this->resetMediaController();
        $this->setItemTemplate();

        // Only upload if upload button has been pushed!
        if ($this->rHasVal('upload', $this->translate('upload')) && !$this->isFormResubmit()
            && $this->uploadHasFiles()) {

            $this->setFiles($_FILES['files']);
            foreach ($this->_files as $i => $file) {

                // Check mime type, file size, etc.
                if (!$this->uploadedFileIsValid($file)) {
                    continue;
                }

                $this->upload(array(
                    'file' => array(
                        'name' => $file['tmp_name'],
                        'mimetype' => $file['type'],
                        'postname' => $file['name']
                    ),
                    'title' => $this->rHasVal('title') ? $this->rGetVal('title') : ''
                ));

                // Store data
                if (empty($this->_result->error)) {

                    $this->addUploaded($file['name'] . ' (' . ceil($file['size']/1024) . ' KB)');

                    // Store core data
                    $media = $this->_result->resource;
                    $this->models->Media->save(array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'rs_id' => $media->ref,
                        'name' => $file['name'],
                        'title' => $media->field8,
                        'width' => $media->files[0]->width,
                        'height' => $media->files[0]->height,
                        'mime_type' => $file['type'],
                        'file_size' => $file['size'],
                        'rs_original' => $media->files[0]->src,
                        'rs_resized_1' => isset($media->files[1]->src) ? $media->files[1]->src : null,
                        'rs_resized_1' => isset($media->files[2]->src) ? $media->files[2]->src : null,
                        'rs_thumb_small' => $media->thumbnails->small,
                        'rs_thumb_medium' => $media->thumbnails->medium,
                        'rs_thumb_large' => $media->thumbnails->large
                    ));

                    // Store associated metadata
                    $mediaId = $this->models->Media->getNewId();
                    $this->saveMetadata($mediaId);

                    // Store tags
                    $this->saveTags($mediaId);

                    // If module_id and item_id have been set, save
                    // contextual link
                    if ($this->moduleId && $this->itemId) {
                         $this->models->MediaModules->insert(array(
                            'id' => null,
                            'project_id' => $this->getCurrentProjectId(),
                            'media_id' => $mediaId,
                            'module_id' => $this->rGetVal('module_id'),
                            'item_id' => $this->rGetVal('item_id')
                         ));
                    }
                } else {
                    $this->addError(_('Could not upload media') . ': ' . $this->_result->error);
                }
            }

            $this->smarty->assign('errors', $this->errors);
            $this->smarty->assign('uploaded', $this->_uploaded);

        }

        $this->smarty->assign('upload_max_filesize', ini_get('upload_max_filesize'));
        $this->smarty->assign('max_file_uploads', ini_get('max_file_uploads'));
        $this->smarty->assign('post_max_size', ini_get('post_max_size'));
        $this->smarty->assign('action', htmlentities($_SERVER['PHP_SELF']));
        $this->smarty->assign('session_upload_progress_name', ini_get('session.upload_progress.name'));
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id', $this->languageId);
		$this->smarty->assign('metadata', $this->setMetadataFields());
		$this->smarty->assign('module_id', $this->rGetVal('module_id'));
		$this->smarty->assign('item_id', $this->rGetVal('item_id'));

		$this->printPage();
    }

    private function setItemTemplate ()
    {
        // Verify module_id and item_id if set
        if ($this->rHasVal('module_id') && $this->rHasVal('item_id')) {

            $mi = new ModuleIdentifierController();
            $mi->setModuleId($this->moduleId);
            $mi->setItemId($this->itemId);

            $this->smarty->assign('module_name', $mi->getModuleName());
            $this->smarty->assign('item_name', $mi->getItemName());
            $this->smarty->assign('back_url',
                $this->rHasVal('back_url') ? $this->rGetVal('back_url') : $_SERVER['HTTP_REFERER']);
        }
    }

    public function setSortOrder ($p)
    {
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;
        $order = isset($p['order']) && is_numeric($p['order']) ? $p['order'] : false;

        if (!$mediaId || !$order || $this->moduleId == -1 || $this->itemId == -1) {
            return false;
        }

        $this->models->MediaModules->update(
			array('sort_order' => $order),
			array(
                'project_id' => $this->getCurrentProjectId(),
                'media_id' => $mediaId,
                'module_id' => $this->moduleId,
                'item_id' => $this->itemId
			)
        );

    }

    private function uploadedFileIsValid ($file)
    {
        // Check mime type

        // Check errors in file
        switch ($file['error']) {
            case 1:
            case 2:
                $this->addError(_('File too large') . ': ' . $file['name']);
                return false;
            case 3:
                $this->addError(_('File upload incomplete') . ': ' . $file['name']);
                return false;
        }
        return true;
    }

    private function deleteMedia ()
    {
        if (empty($this->rGetVal('media_ids'))) {
            return false;
        }

        $mediaIds = $this->rGetVal('media_ids');

        foreach ($mediaIds as $k => $v) {
            $this->models->Media->update(
    			array('deleted' => 1),
    			array('id' => $k, 'project_id' => $this->getCurrentProjectId())
    		);
        }

        $this->addMessage(sprintf(_('Deleted %s file(s).'), count($mediaIds)));
    }

    private function attachMedia ()
    {
        if (empty($this->rGetVal('media_ids'))) {
            return false;
        }

        $mediaIds = $this->rGetVal('media_ids');

        foreach ($mediaIds as $k => $v) {
            $this->models->MediaModules->insert(array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'media_id' => $k,
                'module_id' => $this->moduleId,
                'item_id' => $this->itemId
            ));
        }

        $this->addMessage(sprintf(_('Attached %s file(s).'), count($mediaIds)));

    }

    public function deleteItemMedia ($mediaId = false)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        $where = array(
            'project_id' => $this->getCurrentProjectId(),
            'media_id' => $mediaId,
            'module_id' => $this->moduleId,
            'item_id' => $this->itemId
        );

        $mm = $this->models->MediaModules->_get(array('id' => $where));
		$mmId = isset($mm[0]['id']) ? $mm[0]['id'] : false;

        if ($mmId) {
            // Delete caption
            $mm = $this->models->MediaCaptions->delete(array('media_modules_id' => $mmId));

            // Delete link in media_modules
            $mm = $this->models->MediaModules->delete($where);

            return true;
        }

        return false;
    }


    public function createUserAction ()
    {
        $this->checkAuthorisation();

        if ($this->rHasVal('action', 'create')) {
            $this->createUser();

            if (!empty($this->_result->error)) {

                $this->smarty->assign('result', $this->_result->error);

            } else {
                // Save $this->_result data to Linnaeus!
               $this->smarty->assign('result', $this->_result);

            }


        }

        $this->printPage();
    }

    private function uploadHasFiles ()
    {
        if (isset($_FILES['files']['name'][0]) && !empty($_FILES['files']['name'][0])) {
            return true;
        }
        return false;
    }

    private function getUploadProgress ($formKey)
    {
        if (!intval(ini_get('session.upload_progress.enabled')) || empty($formKey)) {
            return false;
        }

        $key = ini_get('session.upload_progress.prefix') . $formKey;

        if (!empty($_SESSION[$key])) {

            $current = $_SESSION[$key]["bytes_processed"];
            $total = $_SESSION[$key]["content_length"];
            return $current < $total ? ceil($current / $total * 100) : 100;

        }
        return 100;
    }

    private function getRsMediaList ($p = false)
    {
        $search = isset($p['search']) ? $p['search'] : false; // empty to return everything
        $sort = isset($p['sort']) ? $p['sort'] : false; // asc (default)/desc

        $url = $this->_rsSearchUrl .
            ($search ? '&search=' . urlencode($search) . '*' : '') .
            ($sort ? '&sort=' . urlencode($sort) : '');
        $this->_result = $this->getCurlResult($url);

        return $this->parseRsMediaList();
    }

    public function getItemMedia ()
    {
        // Module and item id must have been set using the setters
        if ($this->moduleId == -1 || $this->itemId == -1) {
            $this->addError('Module and or item id not set');
            return false;
        }

        $m = $this->models->MediaModel->getItemMedia(array(
            'project_id' => $this->getCurrentProjectId(),
            'module_id' => $this->moduleId,
            'item_id' => $this->itemId
        ));

        if (!empty($m)) {
            foreach ($m as $k => $v) {
                $media[] = array_merge($v, $this->getMedia($v['id']));
            }
            return $media;
        }

        return array();
    }

    private function getMediaList ($p = false)
    {
        $search = isset($p['search']) ? $p['search'] : false; // empty to return everything
        $sort = isset($p['sort']) ? $p['sort'] : false; // asc (default)/desc

        // Search
        if (!empty($search)) {
            $media = $this->models->MediaModel->search(array(
                'search' => $search,
                'sort' => $sort,
                'project_id' => $this->getCurrentProjectId()
            ));
        // Return everything for item or general
        } else {
            $media = $this->models->Media->_get(array(
    			'id' => array(
    				'project_id' => $this->getCurrentProjectId(),
    			    'deleted' => 0
    			),
                'order' => 'name'
    		));
        }

        if (!empty($this->moduleId) && !empty($this->itemId)) {
            $attached = $this->models->MediaModules->getSingleColumn(array(
                'columns' => 'media_id',
    			'id' => array(
    				'project_id' => $this->getCurrentProjectId(),
    				'module_id' => $this->moduleId,
    				'item_id' => $this->itemId,
    			)
    		));
        }

        $list['total'] = count($media);

        if ($list['total'] > 0) {

            foreach ($media as $i => $resource) {
                $image['rs_id'] = $resource['rs_id'];
                $image['media_id'] = $resource['id'];
                $image['title'] = $resource['title'];
                $image['file_name'] = $resource['name'];
                $image['height'] = $resource['height'];
                $image['width'] = $resource['width'];
                $image['source'] = $resource['rs_original'];
                $image['modified'] = $resource['last_change'];
                $image['thumbnails'] = array(
                    'small' => $resource['rs_thumb_small'],
                    'medium' => $resource['rs_thumb_medium'],
                    'large' => $resource['rs_thumb_large']
                );

                $image['alt_files'] = '';
                foreach (array('rs_resized_1', 'rs_resized_2') as $alt) {
                    if (!empty($resource[$alt])) {
                        $image['alt_files'][] = $resource[$alt];
                    }
                }

                // add flag if image is already attached to entity
                $image['attached'] = isset($attached) && is_array($attached) &&
                    in_array($resource['id'], $attached) ? 1 : 0;

                $list['images'][$i] = $image;
            }
        }

        return $list;
    }

    private function getMedia ($mediaId = false)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        $d = $this->models->Media->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'id' => $mediaId
			)
		));

        if (!empty($d)) {
            $media = $d[0];

            // A few LNG additions
            $media['media_type'] = substr($media['mime_type'], 0, strpos($media['mime_type'], '/'));
            $media['file_size_hr'] =
                $this->helpers->HrFilesizeHelper->convert($media['file_size']);

            foreach ($this::$metadataFields as $f) {
                $media['metadata'][$f] =
                    $this->getMetadataField(array(
            		    'media_id' => $mediaId,
                        'label' => $f
                    ));
            }

            $media['tags'] = $this->getTags($mediaId);
            $media['caption'] = $this->getCaption($mediaId);

            return $media;
        }

        return false;
    }


    private function parseRsMediaList ()
    {
        if (!isset($this->_result) || empty($this->_result)) {
            return false;
        }

        $r = $this->_result;
        $list['total'] = $r->total;
        foreach ($r->resources as $i => $resource) {
            $files = $resource->files;
            $image['rs_id'] = $resource->ref;
            $image['title'] = $resource->Title;
            $image['file_name'] = $resource->Original_filename;
            $image['height'] = $resource->files[0]->height;
            $image['width'] = $resource->files[0]->width;
            $image['extension'] = $resource->files[0]->extension;
            $image['source'] = $resource->files[0]->src;
            $image['modified'] = $resource->file_modified;
            $image['thumbnails'] = (array)$resource->thumbnails;

            $image['alt_files'] = '';
            $nrAltFiles = count($resource->files) - 1;
            if ($nrAltFiles > 0) {
                for ($j = 1; $nrAltFiles + 1; $j++) {
                    $altFiles[] = (array)$resource->files[$j];
                }
                $image['alt_files'] = $altFiles;
            }

            // add flag if image is already attached to entity
            $image['attached'] = 0;
            $list['images'][$i] = $image;
        }
        return $list;
    }


    private function upload ($p)
    {
        // File should contain path, mime type and name
        $file = isset($p['file']) ? $p['file'] : false;
        if (!isset($file['name']) || !isset($file['mimetype']) || !isset($file['postname'])) {
           $this->addError(_('No file provided for upload'));
            return false;
        }
        // File should exist
        if (!file_exists(realpath($file['name']))) {
            $this->addError(_('File does not exist') . ': ' . $file['name']);
            return false;
        }

        $post['userfile'] = new CURLFile(
            $file['name'],
            $file['mimetype'],
            $file['postname']
        );

        // Set title
        $post['field8'] = isset($p['title']) && !empty($p['title']) ?
            $p['title'] : $this->translate('No title');

        // Store RS response in $this->_result
        $this->_result = $this->getCurlResult(array(
            'url' => $this->_rsUploadUrl,
            'post' => $post
        ));

        return $this->_result;
    }

    private function createUser ()
    {
        $project = $this->getCurrentProjectData();
        $newUser = $project['title'] . ' @ ' . $_SERVER['SERVER_NAME'];
        $this->_result =
            $this->getCurlResult($this->_rsNewUserUrl . '&newuser=' . urlencode($newUser));
        return $this->_result;
    }


    /*
    * Reformat $_FILES array into something more logical
    */
    private function setFiles (&$filePost)
    {
        $this->_files = array();
        $multiple = is_array($filePost['name']);

        $fileCount = $multiple ? count($filePost['name']) : 1;
        $fileKeys = array_keys($filePost);

        for ($i = 0; $i < $fileCount; $i++) {
            foreach ($fileKeys as $key) {
                $this->_files[$i][$key] = $multiple ? $filePost[$key][$i] : $filePost[$key];
            }
        }

        return $this->_files;
    }

    private function saveMetadata ($mediaId = false)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        foreach ($this::$metadataFields as $meta) {

            $val = $this->getMetadataField(array(
     		    'media_id' => $mediaId,
                'label' => $meta
            ));

            // Data matches value in database; do nothing
            if ($val == $this->rGetVal($meta)) {
                continue;
            // No data entered yet; insert
            } else if (!$val && $this->rHasVal($meta)) {
                $this->models->MediaMetadata->insert(array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $mediaId,
                    'language_id' => $this->languageId,
                    'sys_label' => $meta,
                    'metadata' => $this->rGetVal($meta)
                ));
            // Data has been updated
            } else if ($val != $this->rGetVal($meta) && $this->rHasVal($meta)) {
                $this->models->MediaMetadata->update(
        			array('metadata' => $this->rGetVal($meta)),
        			array(
            			'project_id' => $this->getCurrentProjectId(),
                        'media_id' => $mediaId,
                        'language_id' => $this->languageId,
                        'sys_label' => $meta
        		    )
                );
            // Data has been deleted
            } else if ($val && !$this->rHasVal($meta)) {
                $this->models->MediaMetadata->delete(array(
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $mediaId,
                    'language_id' => $this->languageId,
                    'sys_label' => $meta
                ));
            }
        }
    }

    private function getMetadataField ($p)
    {
        // No $p check, only used internally
        $metadata = $this->models->MediaMetadata->getSingleColumn(array(
            'columns' => 'metadata',
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_id' => $p['media_id'],
                'language_id' => $this->languageId,
                'sys_label' => $p['label']
			)
		));
        return isset($metadata) ? $metadata[0] : false;
    }

    private function saveTags ($mediaId = false)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        $tags = $this->getTags($mediaId);
        $postedTags =
            array_unique(array_map('trim', explode(',', $this->rGetVal('tags'))));

        // Insert values present only in posted
        $insert = array_diff($postedTags, $tags);
        if (!empty($insert)) {
            foreach ($insert as $tag) {
                if ($tag != '') {
                    $this->models->MediaTags->insert(array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'media_id' => $mediaId,
                        'language_id' => $this->languageId,
                        'tag' => $tag
                     ));
                }
            }
        }
        // Delete values present only in database
        $delete = array_diff($tags, $postedTags);
        if (!empty($delete)) {
            foreach ($delete as $tag) {
                $this->models->MediaTags->delete(array(
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $mediaId,
                    'language_id' => $this->languageId,
                    'tag' => $tag
                 ));
            }
        }
    }


    private function getTags ($mediaId = false)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return array();
        }

        $tags = $this->models->MediaTags->_get(array(
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_id' => $mediaId,
                'language_id' => $this->languageId
			),
            'order' => 'tag'
		));
        if (!empty($tags)) {
            foreach ($tags as $d) {
                $r[] = $d['tag'];
            }
            return $r;
        }
        return array();
    }

    public function saveCaption ($p)
    {
        $caption = isset($p['caption']) ? $p['caption'] : false;
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;

        if (!$mediaId || $this->moduleId == -1 || $this->itemId == -1) {
            return false;
        }

		$where = array(
			'project_id' => $this->getCurrentProjectId(),
			'language_id' => $this->languageId,
			'media_modules_id' => $this->getMediaModulesId($mediaId)
		);

		if ($caption == '') {

			$this->models->MediaCaptions->delete($where);

		} else {

            $m = $this->models->MediaCaptions->_get(array('id' => $where));
			$where['id'] = isset($m[0]['id']) ? $m[0]['id'] : null;
			$where['caption'] = htmlentities(trim($caption));
            $this->models->MediaCaptions->save($where);

		}
    }

    public function setOverviewImage ($mediaId = false)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

		$this->models->MediaModules->update(
            array('overview_image' => 1),
            array(
                'project_id' => $this->getCurrentProjectId(),
                'media_id' => $mediaId,
                'module_id' => $this->moduleId,
    		    'item_id' => $this->itemId
            )
		);

    }

    private function getCaption ($mediaId = false)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return array();
        }

        $caption = $this->models->MediaCaptions->getSingleColumn(array(
            'columns' => 'caption',
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_modules_id' => $this->getMediaModulesId($mediaId),
                'language_id' => $this->languageId
			)
		));

        return isset($caption) ? $caption[0] : false;

    }

    private function getMediaModulesId ($mediaId)
    {
        $mm = $this->models->MediaModules->getSingleColumn(array(
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_id' => $mediaId,
			    'module_id' => $this->moduleId,
                'item_id' => $this->itemId
			)
		));

        return isset($mm) ? $mm[0] : false;
    }

    /* Input is associative array */
    private function setMetadataFields ($v = array())
    {
        foreach ($this::$metadataFields as $f) {
            $d[$f] = isset($v[$f]) ? $v[$f] : null;
        }
        return $d;
    }


}