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
    private $_maxFileUploads;

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
    public function __construct ()
    {
        parent::__construct();
        $this->setConfigSettings();
        $this->setRsApiUrls();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function setRsBaseUrl ()
    {
        // To do: get from config
        //$this->_rsBaseUrl = 'http://134.213.149.111/resourcespace/plugins/';
        $this->_rsBaseUrl = 'http://localhost/resourcespace/plugins/';
    }

    private function setRsMasterKey ()
    {
        // To do: get from config
        //$this->_rsMasterKey = 'cmVpbHtlcCciJT9mcDk9ZCUpcHFyLGtjITUxZHZ4JnMkI29kITI2ZyctdyZ0LTU8IGNgZnAgcnZ1IGgwKmU8ZC0sIycpIg,,';
        $this->_rsMasterKey = 'LDpWw7lzzeM5Vbck0K5N2JBjn2nQiobY6Qxr195NPzqXxXNfeDIjqd96JuomeuIo1Gi8TsM7JjR8d7WgCvX-a8P3mz7WWHgfnZIyJ5KF1UAKAkV3VCjk0zt3Qyo3I4jM';
    }

    private function setRsUserKey ()
    {
        // To do: get from config

        // user: Diaspididae of the World 2.0 @ localhost
        // password: 7e8324c7
       // $this->_rsUserKey = 'V2hldmVwcHx0dGglfGckcX18NEJ_Z2FhMzMqNTVZNHl_dmxpe253cWkhci0lczU8I2Q0NnR4JHAnIm81dzM8YHcqLCUpc2w8d2AwYyF7dXMkLW5gIzk2ZyctJCx2dDs3IzkwNyYpIyUg';
        $this->_rsUserKey = 'mgnwUMXYDrxXQIWaA5V-ZzioDNExJhVwHQ-8scfwAawkfFAf8su4znTstVSIkMATeNA0pMhPQYok0C_if_k-sKNOL7uZ9o_VCesg6_Wi5j8Dbcs7yUud2tu05N4gGKMk';
    }

    private function setRsCollectionId ()
    {
        // To do: get from config
        //$this->_rsCollectionId = 3;
        $this->_rsCollectionId = 5;
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

    private function resetResult ()
    {
        $this->_uploaded = $this->_result = $this->_files = $this->errors = array();
    }

    private function addUploaded ($e)
    {
        $this->_uploaded[] = $e;
    }

    private function setMaxFileUploads ()
    {
        $this->_maxFileUploads =  ini_get('max_file_uploads');
    }

    private function setConfigSettings ()
    {
        $this->setMaxFileUploads();
        $this->setRsBaseUrl();
        $this->setRsMasterKey();
        $this->setRsUserKey();
        $this->setRsCollectionId();
        $this->setRsSearchApi();
        $this->setRsNewUserApi();
        $this->setRsUploadApi();
    }

    private function setRsApiUrls ()
    {
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

    public function selectAction ()
    {
        $this->resetResult();

        $this->smarty->assign('id', $this->rGetId());

        // global get module id?
        //$this->smarty->assign('module_id', $this->getCurrentModuleId());
        $this->smarty->assign('media', $this->getList());
        //print_r($this->media->getMediaList());

        $this->printPage();
    }

    public function uploadAction ()
    {
        $this->resetResult();

        //  $this->uploadHasFiles()

        if ($this->rHasVal('action', 'upload') && !$this->isFormResubmit()
            && $this->uploadHasFiles()) {

            $this->setFiles($_FILES['files']);

            foreach ($this->_files as $i => $file) {

                $this->upload(array(
                    'file' => array(
                        'name' => $file['tmp_name'],
                        'mimetype' => $file['type'],
                        'postname' => $file['name']
                    ),
                    'title' => 'test'
                ));

                // Store data
                if (empty($this->_result->error)) {

                    $this->addUploaded($file['name'] . ' (' . ceil($file['size']/1000) . ' KB)');

                    $media = $this->_result->resource;
                    $this->models->Media->save(array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'rs_id' => $media->ref,
                        'name' => $file['name'],
                        'mime_type' => $file['type'],
                        'file_size' => $file['size'],
                        'rs_original' => $media->files[0]->src,
                        'rs_resized_1' => isset($media->files[1]->src) ? $media->files[1]->src : null,
                        'rs_resized_1' => isset($media->files[2]->src) ? $media->files[2]->src : null,
                        'rs_thumb_small' => $media->thumbnails->small,
                        'rs_thumb_medium' => $media->thumbnails->normal,
                        'rs_thumb_large' => $media->thumbnails->large
                    ));


                } else {
                    $this->addError(_('Could not upload media') . ': ' . $this->_result->error);
                }
            }

            $this->smarty->assign('errors', $this->errors);
            $this->smarty->assign('uploaded', $this->_uploaded);

        }

        $this->smarty->assign('upload_max_filesize', ini_get('upload_max_filesize'));
        $this->smarty->assign('max_file_uploads', $this->_maxFileUploads);
        $this->smarty->assign('post_max_size', ini_get('post_max_size'));
        $this->smarty->assign('session_upload_progress_name', ini_get('session.upload_progress.name'));

        $this->printPage();
    }

    public function createUserAction ()
    {
        $this->createUser();

        die(print_r($this->_result));
        // Save $this->_result data to Linnaeus
    }

    private function uploadHasFiles ()
    {
        if (isset($_FILES['files']['error'][0]) && $_FILES['files']['error'][0] === 0) {
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

    private function getList ($p = false)
    {
        $search = isset($p['search']) ? $p['search'] : false; // empty to return everything
        $sort = isset($p['sort']) ? $p['sort'] : false; // asc (default)/desc

        $url = $this->_rsSearchUrl .
            ($search ? '&search=' . urlencode($search) . '*' : '') .
            ($sort ? '&sort=' . urlencode($sort) : '');
        $this->_result = $this->getCurlResult($url);

        return $this->parseList();
    }

    private function parseList ()
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
        $post['field8'] = 'No title';
        if (isset($p['title']) && empty($p['title'])) {
            $post['field8'] = $p['title'];
        }

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

}