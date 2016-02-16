<?php

/*
 * media.js should be loaded in "parent" controller!
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

    private $_curlResult;

    public $usedModels = array();

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

    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        parent::__destruct();
    }

    private function setRsBaseUrl ()
    {
        // To do: get from config
        $this->_rsBaseUrl = 'http://localhost/resourcespace/plugins/';
    }

    private function setRsMasterKey ()
    {
        // To do: get from config
        $this->_rsMasterKey = 'LDpWw7lzzeM5Vbck0K5N2JBjn2nQiobY6Qxr195NPzqXxXNfeDIjqd96JuomeuIo1Gi8TsM7JjR8d7WgCvX-a8P3mz7WWHgfnZIyJ5KF1UAKAkV3VCjk0zt3Qyo3I4jM';
    }

    private function setRsUserKey ()
    {
        // To do: get from config
        $this->_rsUserKey = 'mgnwUMXYDrxXQIWaA5V-ZzioDNExJhVwHQ-8scfwAawkfFAf8su4znTstVSIkMATeNA0pMhPQYok0C_if_k-sKNOL7uZ9o_VCesg6_Wi5j8Dbcs7yUud2tu05N4gGKMk';
    }

    private function setRsCollectionId ()
    {
        // To do: get from config
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

    private function setConfigSettings ()
    {
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

    }

    public function getMediaList ($p = false)
    {
        $search = isset($p['search']) ? $p['search'] : false; // empty to return everything
        $sort = isset($p['sort']) ? $p['sort'] : false; // asc (default)/desc

        $url = $this->_rsSearchUrl .
            ($search ? '&search=' . urlencode($search) . '*' : '') .
            ($sort ? '&sort=' . urlencode($sort) : '');
        $this->_curlResult = $this->getCurlResult($url);
        return $this->parseMediaList();
    }

    private function parseMediaList ()
    {
        if (!isset($this->_curlResult) || empty($this->_curlResult)) {
            return false;
        }
        //print_r($this->_curlResult);

        $r = $this->_curlResult;
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
            $image['attached'] = 1;

            $list['images'][$i] = $image;
        }
        return $list;
    }


    public function uploadMedia ($p)
    {
        $file = is_array($p) && isset($p['file']) ? $p['file'] : $p;
        if (empty($file) || !file_exists(realpath($file))) {
            // Todo: die
            return 'error';
        }
        $post['userfile'] = new CURLFile($file);
        if (isset($p['title']) && empty($p['title'])) {
            $post['field8'] = $p['title'];
        }
        $this->_curlResult = $this->getCurlResult(array(
            'url' => $this->_rsUploadUrl,
            'post' => $post
        ));
        // Part of data should be stored in Linnaeus!!

        // Check for error
        return $this->_curlResult;
    }

    public function createUser ()
    {
        $project = $this->getCurrentProjectData();
        $newUser = $project['title'] . ' @ ' . $_SERVER['SERVER_NAME'];
        $this->_curlResult =
            $this->getCurlResult($this->_rsNewUserUrl . '&newuser=' . urlencode($newUser));

        // Data should be stored in Linnaeus!!

        // Check for error
        return $this->_curlResult;
    }


}