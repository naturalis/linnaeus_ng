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
    private $_rsIds;

    private $moduleId;
    private $termId;

    public static $metadataFields = array('title', 'location', 'photographer');

    public $usedModels = array(
        'media',
        'media_metadata',
        'media_modules',
        'media_captions',
        'media_tags'
    );

    public $cssToLoad = array();

    public $jsToLoad = array();

    public $controllerPublicName = 'Media';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ($moduleId = false, $itemId = false)
    {
        parent::__construct();
        $this->setExternalIds($moduleId, $itemId);
        $this->setRsSettings();
     }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function setExternalIds ($moduleId, $itemId) {
        $this->moduleId = $moduleId;
        $this->itemId = $itemId;
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

        if ($this->rHasVal('action', 'delete')) {
            $this->deleteMedia();
        }

        //die(print_r($this->getMediaList()));

        // global get module id?
        //$this->smarty->assign('module_id', $this->getCurrentModuleId());
        $this->smarty->assign('media', $this->getMediaList());

        $this->printPage();
    }

    public function editAction ()
    {
        $this->checkAuthorisation();
        $this->resetMediaController();

        $id = $this->rGetVal('id');
        $activeLanguage = $this->rHasVar('language_id') ?
            $this->rGetVal('language_id') : $this->getDefaultProjectLanguage();

        // Save button has been pushed (language switch should not trigger save)
        if ($this->rHasVal('save', $this->translate('save'))) {
            $this->saveMetadata(array(
                'media_id' => $id,
                'language_id' => $activeLanguage
            ));
             $this->saveTags(array(
                'media_id' => $id,
                'language_id' => $activeLanguage
            ));
        }

        $media = $this->getMedia(array(
            'id' => $id,
            'language_id' => $activeLanguage
        ));
        //print_r($media); print_r($_POST);

		$this->smarty->assign('source', $media['rs_original']);
		$this->smarty->assign('name', $media['name']);
		$this->smarty->assign('metadata', $this->setMetadataFields($media['metadata']));
		$this->smarty->assign('tags', implode(', ', $media['tags']));
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id', $activeLanguage);
		$this->smarty->assign('module_id', $this->moduleId);
		$this->smarty->assign('rs_id', $media['rs_id']);
		$this->smarty->assign('item_id', $this->itemId);

		$this->printPage();
    }

    public function uploadAction ()
    {
        $this->checkAuthorisation();
        $this->resetMediaController();
        $activeLanguage = $this->rHasVar('language_id') ?
            $this->rGetVal('language_id') : $this->getDefaultProjectLanguage();

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
                    $this->saveMetadata(array(
                        'media_id' => $mediaId,
                        'language_id' => $activeLanguage
                    ));

                    // Store tags
                     $this->saveTags(array(
                        'media_id' => $mediaId,
                        'language_id' => $activeLanguage
                    ));

                    // If module_id and item_id have been set, save
                    // contextual link
                    if ($this->rHasVal('module_id') && $this->rHasVal('item_id')) {
                        $this->models->MediaMetadata->save(array(
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
		$this->smarty->assign('language_id', $activeLanguage);
		$this->smarty->assign('module_id', $this->moduleId);
		$this->smarty->assign('metadata', $this->setMetadataFields());
		$this->smarty->assign('item_id', $this->itemId);

		$this->printPage();
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
        $this->setPostedRsIds();
        if ($this->_rsIds) {
            foreach ($this->_rsIds as $id) {
                $this->models->Media->update(
        			array('deleted' => 1),
        			array('rs_id' => $id, 'project_id' => $this->getCurrentProjectId())
        		);
            }
            $this->addMessage(sprintf(_('Deleted %s files.'), count($this->_rsIds)));
        }
    }


    public function createUserAction ()
    {
        $this->createUser();

        die(print_r($this->_result));
        // Save $this->_result data to Linnaeus
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

    private function getMediaList ($p = false)
    {
        $search = isset($p['search']) ? $p['search'] : false; // empty to return everything
        $sort = isset($p['sort']) ? $p['sort'] : false; // asc (default)/desc

        $media = $this->models->Media->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'deleted' => 0
			)
		));

        $attached = array();
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
                $image['attached'] = in_array($resource['id'], $attached) ? 1 : 0;

                $list['images'][$i] = $image;
            }
        }

        return $list;
    }

    private function getMedia ($p)
    {
        $id = isset($p['id']) && !empty($p['id']) ? $p['id'] : false;
        $languageId = isset($p['language_id']) && !empty($p['language_id']) ?
            $p['language_id'] : false;

        if (!$id || !$languageId) {
            return false;
        }

        $d = $this->models->Media->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'id' => $id
			)
		));

        if (!empty($d)) {
            $media = $d[0];

            foreach ($this::$metadataFields as $f) {
                $media['metadata'][$f] = '';
            }
            $media['tags'] = array();

            $metadata = $this->models->MediaMetadata->_get(array(
    			'id' => array(
    				'project_id' => $this->getCurrentProjectId(),
    				'language_id' => $languageId,
    			    'media_id' => $id
    			)
    		));

            if (!empty($metadata)) {
                foreach ($metadata as $d) {
                    $media['metadata'][$d['sys_label']] = $d['metadata'];
                }
            }

            $tags = $this->models->MediaTags->_get(array(
    			'id' => array(
    				'project_id' => $this->getCurrentProjectId(),
    				'language_id' => $languageId,
    			    'media_id' => $id
    			)
    		));

            if (!empty($tags)) {
                foreach ($tags as $d) {
                    $media['tags'][] = $d['tag'];
                }
            }

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

    private function saveMetadata ($p)
    {
        $mediaId = isset($p['media_id']) && !empty($p['media_id']) ?
            $p['media_id'] : false;
        $languageId = isset($p['language_id']) && !empty($p['language_id']) ?
            $p['language_id'] : false;

        if (!$mediaId || !$languageId) {
            return false;
        }

        foreach ($this::$metadataFields as $meta) {

            $val = $this->getMetadataField(array(
                'project_id' => $this->getCurrentProjectId(),
    		    'media_id' => $mediaId,
                'language_id' => $languageId,
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
                    'language_id' => $languageId,
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
                        'language_id' => $languageId,
                        'sys_label' => $meta
        		    )
                );
            // Data has been deleted
            } else if ($val && !$this->rHasVal($meta)) {
                $this->models->MediaMetadata->delete(array(
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $mediaId,
                    'language_id' => $languageId,
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
				'project_id' => $p['project_id'],
			    'media_id' => $p['media_id'],
                'language_id' => $p['language_id'],
                'sys_label' => $p['label']
			)
		));
        return isset($metadata) ? $metadata[0] : false;
    }

    private function saveTags ($p)
    {
        $mediaId = isset($p['media_id']) && !empty($p['media_id']) ?
            $p['media_id'] : false;
        $languageId = isset($p['language_id']) && !empty($p['language_id']) ?
            $p['language_id'] : false;

        if (!$mediaId || !$languageId) {
            return false;
        }

        $tags = $this->getTags(array(
            'project_id' => $this->getCurrentProjectId(),
		    'media_id' => $mediaId,
            'language_id' => $languageId
        ));
        $postedTags =
            array_unique(array_map('trim', explode(',', $this->rGetVal('tags'))));

        // Insert values present only in posted
        $insert = array_diff($postedTags, $tags);
        if (!empty($insert)) {
            foreach ($insert as $tag) {
                $this->models->MediaTags->insert(array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $mediaId,
                    'language_id' => $languageId,
                    'tag' => $tag
                 ));
            }
        }
        // Delete values present only in database
        $delete = array_diff($tags, $postedTags);
        if (!empty($delete)) {
            foreach ($delete as $tag) {
                $this->models->MediaTags->delete(array(
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $mediaId,
                    'language_id' => $languageId,
                    'tag' => $tag
                 ));
            }
        }
    }

    private function getTags ($p)
    {
        // No $p check, only used internally
        $tags = $this->models->MediaTags->_get(array(
            'id' => array(
				'project_id' => $p['project_id'],
			    'media_id' => $p['media_id'],
                'language_id' => $p['language_id']
			)
		));
        if (!empty($tags)) {
            foreach ($tags as $d) {
                $r[] = $d['tag'];
            }
            return $r;
        }
        return array();
    }

    /* Input is associative array */
    private function setMetadataFields ($v = array())
    {
        foreach ($this::$metadataFields as $f) {
            $d[$f] = isset($v[$f]) ? $v[$f] : null;
        }
        return $d;
    }


    private function setPostedRsIds ()
    {
        foreach ($this->requestData as $k => $v) {
            if (strpos($k, 'rs_id_') === 0 && $v == 'on') {
                $this->_rsIds[] = substr($k, 6);
             }
        }
    }
}