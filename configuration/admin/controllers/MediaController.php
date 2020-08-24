<?php 
/**
 * Controller for uploading (media) files to resourcespace
 *
 * @todo: check mime type on upload
 * @todo: check max_file_uploads, memory_limit, post_max_size
 *
 */

include_once ('Controller.php');
include_once ('ModuleIdentifierController.php');
include_once ('ModuleSettingsReaderController.php');

class MediaController extends Controller
{

    private $_rsBaseUrl;
    private $_rsMasterKey;
    private $_rsUserKey;
    private $_rsCollectionId;
    private $_rsSearchApi;
    private $_rsUploadApi;
    private $_rsNewUserApi;
    private $_rsSearchUrl;
    private $_rsUploadUrl;
    private $_rsNewUserUrl;
    private $_rsUserName;
    private $_rsPassword;

    private $_uploaded;

    private $moduleId;
    private $itemId;
    private $languageId;
    private $itemsPerPage = 100;

    private $_mi;

    protected $_result;
    protected $_files;
    protected $mediaId;

    protected $_moduleSettingsReader;

    /**
     * Array containing the default parameters for the media upload server
     *
     * @var array rsSetupParameters
     */
    public static $rsSetupParameters = array(
        'rs_base_url' => array(
            'default' => 'https://resourcespace.naturalis.nl/plugins/',
            'info' => 'Base url to ResourceSpace server'
        ),
        'rs_new_user_api' => array(
            'default' => 'api_new_user_lng',
            'info' => 'Name of RS API to create new RS user'
        ),
        'rs_upload_api' => array(
            'default' => 'api_upload_lng',
            'info' => 'Name of RS API to upload to RS'
        ),
    	/*
        'rs_search_api' => array(
            'default' => 'api_search_lng',
            'info' => 'Name of RS API to search RS'
        ),
        */
        'rs_user_key' => array(
            'default' => '',
            'info' => 'RS API user key for current project (set dynamically when user is created)'
        ),
        'rs_collection_id' => array(
            'default' => '',
            'info' => 'RS collection ID for current project (set dynamically when user is created)'
        ),
        'rs_password' => array(
            'default' => '',
            'info' => 'RS password (set dynamically when user is created)'
        ),
        'rs_user_name' => array(
            'default' => '',
            'info' => 'RS user name (project name @ server name)'
        ),
    );

    public static $singleMediaFileControllers = array(
        'introduction',
        'key',
        'matrixkey',
        'free_module'
    );

    public static $metadataFields = array(
        'title',
        'location',
        'creator'//'photographer'
    );

    /**
     * Allowed mimeTypes
     *
     * @var array $mimeTypes
     */
    public static $mimeTypes = array(
        'image' => array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif'
        ),
        'audio' => array(
            'mp3' => array(
                'audio/mpeg',
                'audio/mp3'
            )
        ),
        'video' => array(
            'mp4' => 'video/mp4'
        ),
        'pdf' => array(
            'pdf' => 'application/pdf',
        ),
        'text' => array(
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'rtf' => 'text/rtf',
            'txt' => 'text/plain',
            'csv' => array(
                'text/comma-separated-values',
            	'text/csv',
            	'application/csv',
				'application/x-csv',
				'text/csv',
				'text/comma-separated-values',
				'text/x-comma-separated-values',
				'text/tab-separated-values',
				'text/plain',
				'application/vnd.ms-excel',
				'text/x-csv',
            )
        )
    );

    public $usedModels = array(
        'media',
        'media_metadata',
        'media_modules',
        'media_captions',
        'media_tags',
        'module_settings',
        'module_settings_values'
    );

    public $usedHelpers = array('hr_filesize_helper');

    public $cssToLoad = array(
        'media.css',
        'paginator.css',
        'inline_templates.css'
    );

    public $jsToLoad = array(
		'all' => array(
			'media.js',
		    'inline_templates.js'
		)
	);

    public $controllerPublicName = 'Media';
    public $modelNameOverride = 'MediaModel';

    /**
     * Constructor, calls parent's constructor
     * @access     public
     * @param null $p
     */
    public function __construct ($p = null)
    {
        parent::__construct($p);
        $this->initialize($p);
        $this->models->MediaModel->setMetadataFields($this::$metadataFields);
     }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize($p)
    {
        $this->moduleId = $this->rHasVal('module_id') ? $this->rGetVal('module_id') : -1;
        $this->itemId = $this->rHasVal('item_id') ? $this->rGetVal('item_id') : -1;
        $this->languageId = $this->rHasVar('language_id') ? $this->rGetVal('language_id') : $this->getDefaultProjectLanguage();
        $this->offset = $this->rHasVar('offset') ? $this->rGetVal('offset') : 0;

        $this->_mi = new ModuleIdentifierController();
        $this->_mi->setModuleId($this->moduleId);
        $this->_mi->setItemId($this->itemId);
        $this->_mi->setLanguageId($this->languageId);

		if (isset($p['module_settings_reader'])) {
			$this->_moduleSettingsReader = $p['module_settings_reader'];
		} else {
			$this->_moduleSettingsReader = new ModuleSettingsReaderController();
		}

        $this->setRsSettings();

        if (!isset($_SESSION['admin']['user']['media']['display'])) {
            $_SESSION['admin']['user']['media']['display'] = 'grid';
        }
    }

    public function setModuleId ($id)
    {
        $this->moduleId = isset($id) && is_numeric($id) ? $id : -1;
    }

    public function setItemId ($id)
    {
        $this->itemId = isset($id) && is_numeric($id) ? $id : -1;
    }

    public function setMediaId ($id)
    {
        $this->mediaId = isset($id) && is_numeric($id) ? $id : -1;
    }

    public function setLanguageId ($id)
    {
        $this->languageId = isset($id) && is_numeric($id) ? $id : $this->getDefaultProjectLanguage();
    }

    /**
     * Adds the succesfully uploaded file messages
     * @param $e
     */
    private function addUploaded ($e)
    {
        $this->_uploaded[] = $e;
    }

    /**
     * Set the ResourceSpace Settings
     */
    private function setRsSettings ()
    {
        $this->_rsBaseUrl = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_base_url',
            'module' => $this->controllerPublicName
        ));
        $this->_rsUserKey = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_user_key',
            'module' => $this->controllerPublicName
        ));
        $this->_rsCollectionId = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_collection_id',
            'module' => $this->controllerPublicName
        ));
        $this->_rsUploadApi = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_upload_api',
            'module' => $this->controllerPublicName
        ));
        $this->_rsNewUserApi = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_new_user_api',
            'module' => $this->controllerPublicName
        ));
        /*
        $this->_rsSearchApi = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_search_api',
            'module' => $this->controllerPublicName
        ));
        */
        $this->_rsUserName = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_user_name',
            'module' => $this->controllerPublicName
        ));
        $this->_rsPassword = $this->_moduleSettingsReader->getModuleSetting(array(
            'setting' =>'rs_password',
            'module' => $this->controllerPublicName
        ));

        if ($this->getAuthorisationState()) {
            $this->checkRsSettings();
        }

        // Search url: &search=[term]* to be appended
        /*
        $this->_rsSearchUrl = $this->_rsBaseUrl . $this->_rsSearchApi . '/?' .
            'key=' . $this->_rsUserKey . '&prettyfieldnames=true&collection=' .
            $this->_rsCollectionId;
       */
       // Upload url: &field8=[title] to be appended
        $this->_rsUploadUrl = $this->_rsBaseUrl . $this->_rsUploadApi . '/?' .
            'key=' . $this->_rsUserKey . '&collection=' . $this->_rsCollectionId;
        // New user url; newuser appended in createUser()
        $this->_rsNewUserUrl = $this->_rsBaseUrl . $this->_rsNewUserApi . '/?' .
            'key=' . $this->rGetVal('rs_master_key');
    }

    /**
     * Check the ResourceSpace Settings
     * @return bool
     */
    private function checkRsSettings ()
    {
        // As checking urls takes time, do this only once per session
        if (isset($_SESSION['admin']['user'][$this->getCurrentProjectId()]['media']['bootstrap_passed']) &&
            $_SESSION['admin']['user'][$this->getCurrentProjectId()]['media']['bootstrap_passed'] == 1) {
            return true;
        }

        // Die and redirect to setup if settings have not been set
        foreach ($this::$rsSetupParameters as $p => $v) {
            $s = $this->{'_' . lcfirst(implode('', array_map('ucfirst', explode('_', $p))))};
            if (!empty($this->getCurrentProjectId()) && empty($s) &&
                strpos($_SERVER['PHP_SELF'], 'setup_rs') === false) {
                die('FATAL: ' . $p . ' not set.
                    <a href="../media/setup_rs.php">Set up ResourceSpace</a> to continue.');
            }
        }

        // Check RS settings for all pages but RS setup
        if (strpos($_SERVER['PHP_SELF'], 'setup_rs') === false) {
            // Test base url
            $headers = get_headers($this->_rsBaseUrl);
            if (substr($headers[0], 9, 3) != 403) {
                die('FATAL: ResourceSpace base url is incorrect.
                    <a href="../module_settings/values.php?id=' . $this->getMediaModuleId() .
                    '">Correct settings</a> to continue.');
            }

            // Test plugin urls
            foreach (array($this->_rsUploadApi, $this->_rsNewUserApi) as $plugin) {
                $headers = get_headers($this->_rsBaseUrl . $plugin);
                if (substr($headers[0], 9, 3) != 301) {
                    die('FATAL: ResourceSpace plugin "' . $plugin . '" returns ' . $headers[0] . '.
                        <a href="../module_settings/values.php?id=' . $this->getMediaModuleId() .
                        '">Correct settings</a> to continue.');
                }
            }

            $_SESSION['admin']['user'][$this->getCurrentProjectId()]['media']['bootstrap_passed'] = 1;
        }
    }

    /**
     * Get the module id
     * @return bool
     */
    private function getMediaModuleId ()
    {
        $d = $this->getProjectModules();

        foreach ($d['modules'] as $m) {
            if ($m['controller'] == 'media') {
                return $m['module_id'];
            }
        }

        return false;
    }

    /**
     * Single setting value saver
     * @return bool|void
     */
    private function saveRsSetting ($setting, array $values)
    {
        $d = $this->models->ModuleSettings->getSingleColumn(array(
            'columns' => 'id',
			'id' => array(
                'module_id' => $this->getCurrentModuleId(),
                'setting' => $setting
             )
		));

        if (!empty($d)) {

            $id = $d[0];

        } else {

            $this->models->ModuleSettings->insert(array(
                'id' => !empty($d) ? $d[0] : null,
                'module_id' => $this->getCurrentModuleId(),
                'setting' => $setting,
                'info' => $values['info'],
                'default_value' => !empty($values['default']) ? $values['default'] : null
            ));

            $id = $this->models->ModuleSettings->getNewId();

        }

        if (!empty($values['default'])){

            $d = $this->models->ModuleSettingsValues->getSingleColumn(array(
                'columns' => 'id',
    			'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'setting_id' => $id
                 )
    		));

            $this->models->ModuleSettingsValues->save(array(
                'id' => !empty($d) ? $d[0] : null,
                'project_id' => $this->getCurrentProjectId(),
                'setting_id' => $id,
                'value' => $values['default']
            ));
        }
    }

    /**
     * Asynchronous action handler
     * @return bool|void
     */
    public function ajaxInterfaceAction ()
    {
        if ( !$this->getAuthorisationState() ) return;

        if ($this->rHasVal('action', 'upload_progress')) {
            $this->smarty->assign('returnText', $this->getUploadProgress('media'));
            $this->printPage();
        }
        if ($this->rHasVal('action', 'display_preference')) {
            $_SESSION['admin']['user']['media']['display'] = $this->rGetVal('type');
        }
        if ($this->rHasVal('action', 'type_to_find')) {
            $result = $this->getMediaFiles(array(
                'search' => $this->rGetVal('search'),
                'limit' => $this->itemsPerPage
            ));
            $this->smarty->assign('returnText', json_encode(array(
                'total' => $result['total'],
                'files' => $result['files']
            )));
            $this->printPage();
        }
        return false;
    }

    public function indexAction ()
    {
        $this->checkAuthorisation();

		$this->setPageName($this->translate('Index'));

		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
		$this->smarty->assign( 'CRUDstates', $this->getCRUDstates() );

		$p  = explode('/', $this->_rsBaseUrl);
		$this->smarty->assign('action', implode('/', array_slice($p, 0, 3)) . '/login.php');
		$this->smarty->assign('username', $this->_rsUserName);
		$this->smarty->assign('password', $this->_rsPassword);

        $this->printPage();
    }

    public function selectRsAction ()
    {
        $this->checkAuthorisation();
		$this->setPageName($this->translate('Browse Media on ResourceSpace server'));

        $this->smarty->assign('media', $this->getRsMediaList());
		$this->smarty->assign('from', 'select_rs');
        $this->printPage();
    }

    public function selectAction ()
    {
        $this->checkAuthorisation();
		$this->setPageName($this->translate('Browse media'));

        if ($this->moduleId != -1 && $this->itemId != -1) {

            $type = in_array($this->_mi->getModuleController(), $this::$singleMediaFileControllers) ? 'single' : 'multiple';

            $this->smarty->assign('module_name', $this->_mi->getModuleName());
            $this->smarty->assign('item_name', $this->_mi->getItemName());
            //$this->smarty->assign('back_url', $this->setBackUrl());
            $this->smarty->assign('back_url', $this->_mi->setMediaBackUrl());
            $this->smarty->assign('input_type', $type);
        }

        if ($this->rHasVal('action', 'delete')) {
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();
            $this->deleteMedia();
        }

        if ($this->rHasVal('action', 'attach')) {
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
            $this->attachMedia();
        }

        $this->smarty->assign('media', $this->getMediaFiles());
        $this->smarty->assign('module_id', $this->moduleId);
        $this->smarty->assign('item_id', $this->itemId);
		$this->smarty->assign('from', 'select');

        $this->printPage();
    }

    public function mediaOverlayAction ()
    {
        $this->checkAuthorisation();
		$this->setPageName($this->translate('Browse media'));
        $this->smarty->assign('media', $this->getMediaFiles());
        $this->printPage();
    }

    public function searchAction ()
    {
        $this->checkAuthorisation();
		$this->setPageName($this->translate('Search media'));

        foreach ($this::$metadataFields as $f) {
            $search['metadata'][$f] = $this->rGetVal($f);
        }
        $search['tags'] =
            array_unique(array_map('trim', explode(',', $this->rGetVal('tags'))));
        $search['file_name'] = $this->rGetVal('file_name');

        if ($this->rHasVal('action', 'search') && $this->arrayHasData($search)) {
            $result = $this->getMediaFiles(array('search' => $search));
            $this->smarty->assign('media', $result);
        }

		$this->smarty->assign('metadata', $search['metadata']);
		$this->smarty->assign('tags', $this->rGetVal('tags'));
		$this->smarty->assign('file_name', $search['file_name']);
		$this->smarty->assign('from', 'search');

		$this->printPage();
    }

    public function editAction ()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
        $this->checkAuthorisation();
		$this->setPageName($this->translate('Edit media'));
		$id = $this->rGetVal('id');

        // Save button has been pushed (language switch should not trigger save)
        if ($this->rHasVal('save', $this->translate('save'))) {

            $this->setFiles();

            if ($this->uploadHasFiles()) {

                $this->uploadFiles();
                $this->reattachMediaFile($id, $this->mediaId);

            }

            $this->saveMetadata(array('media_id' => $id));
            $this->saveTags(array('media_id' => $id));
			$this->addMessage($this->translate('Saved'));
        }

        $media = $this->getMediaFile($id);

		$this->smarty->assign('media_type', $this->getMediaType($media['mime_type']));
		$this->smarty->assign('thumbnail', $media['rs_thumb_medium']);
		$this->smarty->assign('source', $media['rs_original']);
		$this->smarty->assign('name', $media['name']);
		$this->smarty->assign('metadata', $media['metadata']);
		$this->smarty->assign('tags', implode(', ', $media['tags']));
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id', $this->languageId);
		$this->smarty->assign('links', $this->getMediaLinks($id));
		$this->smarty->assign('module_id', $this->moduleId);
		$this->smarty->assign('rs_id', $media['rs_id']);
		$this->smarty->assign('item_id', $this->itemId);
		$this->smarty->assign('back_url', $this->setBackUrl());

		$this->printPage();
    }

    private function reattachMediaFile ($oldId, $newId)
    {
        if (empty($oldId) || empty($newId)) {
            return false;
        }

        $oldMedia = $this->getMediaFile($oldId);
        $newMedia = $this->getMediaFile($newId);

        // Delete original file
        $this->models->Media->delete(array(
    		'project_id' => $this->getCurrentProjectId(),
    		'id' => $oldId
		));

        // Update new file
        $this->models->Media->update(
            array(
                'id' => $oldId,
                'title' => $oldMedia['title']
    		), array (
        		'project_id' => $this->getCurrentProjectId(),
        		'id' => $newId
    		)
        );

        // Replace internal links
        $this->replaceInternalMediaLinks(array(
            'old_media' => $oldMedia,
            'new_media' => $newMedia
        ));

        $this->logChange(array(
            'before' => $oldMedia,
            'note' => 'Reattach media',
            'after' => $newMedia
        ));
    }

    private function getMediaLinks ($mediaId)
    {
        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        $d = $this->models->MediaModules->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_id' => $mediaId
			),
            'order' => 'module_id'
		));

        if (!empty($d)) {
            $mi = new ModuleIdentifierController();

            foreach ($d as $row) {
                $mi->setModuleId($row['module_id']);

                $mi->setItemId($row['item_id']);

                $links[$mi->getModuleName()][] = '<a href="../' . $mi->getModuleController() .
                    '/' . $mi->getItemEditPage() . $row['item_id'] . '">' . $mi->getItemName() . '</a>';
            }

            return $links;
       }

       return false;
    }

    /**
     * Handle the upload action
     */
    public function uploadAction ()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
        $this->checkAuthorisation();
		$this->setPageName($this->translate('Upload media'));

        // Early check for files that are too large
        if (isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0 && empty($_FILES)) {
             $this->addError(_('File size') . ' ' . $_SERVER['CONTENT_LENGTH']  . ' ' .
             _('exceeds maximum file size') . ' ' . $this->setMaxFileSize());
        }

        if ($this->moduleId != -1 && $this->itemId != -1) {

            $type = in_array($this->_mi->getModuleController(), $this::$singleMediaFileControllers) ?
                'single' : 'multiple';

            $this->smarty->assign('module_name', $this->_mi->getModuleName());
            $this->smarty->assign('item_name', $this->_mi->getItemName());
            $this->smarty->assign('back_url', $this->setBackUrl());
            $this->smarty->assign('input_type', $type);
        }

        // Only upload if upload button has been pushed!
        if ($this->rHasVal('upload', $this->translate('upload')) &&
            !$this->isFormResubmit() && $this->uploadHasFiles()) {

            $this->setFiles();
            $this->uploadFiles();

            $this->smarty->assign('uploaded', $this->_uploaded);
        }

        $this->smarty->assign('errors', $this->errors);
        $this->smarty->assign('upload_max_filesize', ini_get('upload_max_filesize'));
        $this->smarty->assign('max_file_uploads', ini_get('max_file_uploads'));
        $this->smarty->assign('post_max_size', ini_get('post_max_size'));
        $this->smarty->assign('form_max_file_size', $this->setMaxFileSize());
        $this->smarty->assign('action', htmlentities($_SERVER['PHP_SELF']));
        $this->smarty->assign('session_upload_progress_name',
            ini_get('session.upload_progress.name'));
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id', $this->languageId);
		$this->smarty->assign('metadata', $this->setMetadataFields());
		$this->smarty->assign('module_id', $this->rGetVal('module_id'));
		$this->smarty->assign('item_id', $this->rGetVal('item_id'));

		$this->printPage();
    }

    /**
     * Handle the uploaded files
     *
     * @param bool $p
     * @return bool
     */
    protected function uploadFiles ($p = false)
    {
        if (empty($this->_files)) {
            return false;
        }

        $notes = array('success');

        foreach ($this->_files as $i => $file) {

            // Check mime type, file size, etc.
            if (!$this->uploadedFileIsValid($file)) {
                $this->logChange(array(
                    'note' => sprintf('File "%s" not uploaded, invalid',$file['name'])
                ));
                continue;
            }

            $result = $this->upload(array(
                'file' => array(
                    'name' => $file['tmp_name'],
                    'mimetype' => $file['type'],
                    'postname' => $file['name']
                ),
                'title' => $this->setFileTitle($file)
            ));

            // Store data
            if (is_object($result) && empty($result->error)) {

                // Store core data
                $media = $result->resource;

                $mediaRecord = array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'rs_id' => $media->ref,
                    'name' => $file['name'],
                    'title' => $media->field8,
                    'width' => is_int($media->files[0]->width) ? $media->files[0]->width : -1,
                    'height' => is_int($media->files[0]->height) ? $media->files[0]->height : -1,
                    'mime_type' => $file['type'],
                    'file_size' => $file['size'],
                    'rs_original' => $media->files[0]->src,
                    'rs_resized_1' => isset($media->files[1]->src) ? $media->files[1]->src : null,
                    'rs_resized_2' => isset($media->files[2]->src) ? $media->files[2]->src : null,
                    'rs_thumb_small' => $media->thumbnails->small,
                    'rs_thumb_medium' => $media->thumbnails->medium,
                    'rs_thumb_large' => $media->thumbnails->large
                );

                $this->models->Media->save($mediaRecord);

                $this->logChange(array(
                    'note' => sprintf('Uploaded "%s" to ResourceSpace',$file['name']),
                    'after' => $mediaRecord
                ));

                $this->setMediaId($this->models->Media->getNewId());

                // Store associated metadata
                $this->saveMetadata(array('media_id' => $this->mediaId));

                // Store tags
                $this->saveTags(array('media_id' => $this->mediaId));

                // If module_id and item_id have been set, save
                // contextual link. Overview is set only during
                // conversion in MediaConverterController
                if ($this->moduleId != -1 && $this->itemId != -1 && !isset($p['skip_attach'])) {
                     $this->models->MediaModules->insert(array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'media_id' => $this->mediaId,
                        'module_id' => $this->rHasVal('module_id') ? $this->rGetVal('module_id') : $this->moduleId,
                        'item_id' => $this->rHasVal('item_id') ? $this->rGetVal('item_id') : $this->itemId,
                        'overview_image' => isset($p['overview']) ? $p['overview'] : 0,
                        'sort_order' => isset($p['sort_order']) ? $p['sort_order'] : 0,
                     ));
                }

                $this->addUploaded('<span class="green">' . $file['name'] . '</span>
                    (' . ceil($file['size']/1024) . ' KB)
                    (<a target="_blank" href="edit.php?id=' . $this->mediaId . '">edit</a>)');

            } else {

                $error = isset($this->_result->error) ? $this->_result->error : $this->_result;

                $this->addError(_('Could not upload media') . ': ' . $error);

                $this->logChange(array(
                    'note' => sprintf('Could not upload "%s" to ResourceSpace: %s',$file['name'], $error)
                ));
            }
        }
    }

    private function setMaxFileSize ()
    {
        $a = $this->helpers->HrFilesizeHelper->returnBytes(ini_get('upload_max_filesize'));
        $b = $this->helpers->HrFilesizeHelper->returnBytes(ini_get('post_max_size'));
        return max(array($a, $b));
    }

    private function setFileTitle ($file)
    {
        if ($this->rHasVal('title')) {
            return $this->rGetVal('title');
        } else if (isset($file['title'])) {
            return $file['title'];
        }
        return '';
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

    private function setBackUrl ()
    {
        if ($this->rHasVal('back_url')) {
            return $this->rGetVal('back_url');
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
            // Append id if this is not present in the referrer
            if (strpos($url, 'id=') === false && $this->itemId != -1) {
                $url .= (strpos($url, '?') === false ? '?' : "&") . 'id=' . $this->itemId;
            }
            if (strpos($url, 'language_id=') === false && $this->languageId != -1) {
                $url .= (strpos($url, '?') === false ? '?' : "&") . 'language_id=' . $this->languageId;
            }
            return $url;
        }
        return false;
    }

    private function uploadedFileIsValid ($file)
    {
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

        // Check mime type
        if (!$this->getMediaType($file['type'])) {
            $this->addError(_('Mime type') . ' ' . $file['type'] . ' ' .
                _('not supported') . ': ' . $file['name']);
            return false;
        }

        // Check file name
        if (!$this->checkFileExtension($file)) {
            $this->addError(_('Extension') . ' ' . $this->getFileExtenstion($file['name']) .
                ' ' . _('not supported') . ': ' . $file['name']);
            return false;
        }

        return true;
    }

    private function deleteMedia ()
    {
        if (!$this->rHasVal('media_ids')) {
            return false;
        }

        $mediaIds = $this->rGetVal('media_ids');

        foreach ($mediaIds as $k => $v) {
            $media = $this->models->Media->_get(array('id' => $k, 'project_id' => $this->getCurrentProjectId()));
            $this->logChange(array(
                'before' => $media,
                'note' => sprintf('Media "%s" deleted',$media['name'])
            ));

            $this->models->Media->update(
    			array('deleted' => 1),
    			array('id' => $k, 'project_id' => $this->getCurrentProjectId())
    		);
        }

        $this->addMessage(sprintf(_('Deleted %s file(s).'), count($mediaIds)));
    }

    private function attachMedia ()
    {
        if (!$this->rHasVal('media_ids')) {
            return false;
        }

        // media_ids is single value if coming from radio form; transform to array
        $mediaIds = is_array($this->rGetVal('media_ids')) ? $this->rGetVal('media_ids') :
            array($this->rGetVal('media_ids') => 'on');

        foreach ($mediaIds as $k => $v) {
            $this->models->MediaModules->insert(array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'media_id' => $k,
                'module_id' => $this->moduleId,
                'item_id' => $this->itemId
            ));
            $media = $this->getMediaFile($k);
            $this->logChange(['note' => sprintf($this->translate('Attach medium "%s" to "%s"'),$media['name'], $this->_mi->getItemName())]);
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

            $mi = new ModuleIdentifierController();
            $mi->setModuleId($this->moduleId);
            $mi->setItemId($this->itemId);
            $mi->setLanguageId($this->languageId);

            $media = $this->getMediaFile($mediaId);
            $item = $mi->getItemName();

            $this->logChange(['note' => sprintf($this->translate("Detach medium '%s' from '%s'"), $media['name'], $item)]);

            return true;
        }

        return false;
    }


    public function setupRsAction ()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
        $this->checkAuthorisation();
        $this->setRsSettings();

        if ($this->rHasVal('action', 'create') && $this->rHasVal('rs_master_key')) {

            // Basic RS settings
            foreach ($this::$rsSetupParameters as $p => $v) {
                // Overwrite default if set in setup form (server!)
                if ($this->rHasVal($p)) {
                    $v = array('default' => $this->rGetVal($p));
                }
                $this->saveRsSetting($p, $v);
            }

            // Dynamic settings
            $this->setRsSettings();
            $this->createUser();

            // Test response; RS does not always return neat response...
            $error = is_string($this->_result) ?
                ucfirst($this->_result) : $this->_result->error;

            if (!empty($error)) {

                 $this->addError($error);

            } else {

                $this->saveRsSetting('rs_collection_id', array(
                    'default' => $this->_result->collection_id
                ));
                $this->saveRsSetting('rs_user_key', array(
                    'default' => $this->_result->authentification_key
                ));
                $this->saveRsSetting('rs_user_name', array(
                    'default' => $this->_result->username
                ));
                $this->saveRsSetting('rs_password', array(
                    'default' => $this->_result->password
                ));

                $d = array(
                    'rs_collection_id' => $this->_result->collection_id,
                    'rs_user_key' => $this->_result->authentification_key,
                    'rs_user_name' => $this->_result->username,
                    'rs_password' => $this->_result->password
                );

                $this->smarty->assign('result', $d);
            }
        }

        $baseUrl = !empty($this->_rsBaseUrl) ? $this->_rsBaseUrl :
            $this::$rsSetupParameters['rs_base_url']['default'];

        $this->smarty->assign('rsBaseUrl', $baseUrl);
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
        if (!(int)ini_get('session.upload_progress.enabled') || empty($formKey)) {
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

    public function getItemMediaFiles ()
    {
        // Module and item id must have been set using the setters
        if ($this->moduleId == -1 || $this->itemId == -1) {
            $this->addError('Module and or item id not set');
            return false;
        }

        $m = $this->models->MediaModel->getItemMediaFiles(array(
            'project_id' => $this->getCurrentProjectId(),
            'module_id' => $this->moduleId,
            'item_id' => $this->itemId
        ));

        if (!empty($m)) {
            foreach ($m as $k => $v) {
                $media[] = array_merge($v, $this->getMediaFile($v['id']));
            }
            return $media;
        }

        return array();
    }

    private function getMediaFiles ($p = false)
    {
        $search = isset($p['search']) ? $p['search'] : false; // empty to return everything
        $sort = isset($p['sort']) ? $p['sort'] : 'name';
        $limit = isset($p['limit']) ? $p['limit'] : false;

        // Search
        if (!empty($search)) {
            $media = $this->models->MediaModel->search(array(
                'search' => $search,
                'sort' => $sort,
                'project_id' => $this->getCurrentProjectId(),
                'limit' => $limit,
                'search_type' => 'or'
            ));

        // Return everything for item or general
        } else {
            $media = $this->models->MediaModel->getAllMediaFiles(array(
				'project_id' => $this->getCurrentProjectId(),
				'sort' => $sort
            ));
        }

        if ($this->moduleId != -1 && $this->itemId != 1) {
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
                $file['rs_id'] = $resource['rs_id'];
                $file['media_id'] = $resource['id'];
                $file['title'] = $resource['title'];
                $file['file_name'] = $resource['name'];
                $file['mime_type'] = $resource['mime_type'];
                $file['media_type'] = $this->getMediaType($resource['mime_type']);
                $file['height'] = $resource['height'];
                $file['width'] = $resource['width'];
                $file['source'] = $resource['rs_original'];
                $file['modified'] = $resource['last_change'];

                $file['thumbnails'] = array(
                    'small' => $resource['rs_thumb_small'],
                    'medium' => $resource['rs_thumb_medium'],
                    'large' => $resource['rs_thumb_large']
                );

                // Add flag if image is already attached to entity
                $file['attached'] = isset($attached) && is_array($attached) &&
                    in_array($resource['id'], $attached) ? 1 : 0;

                $files[] = $file;
            }
        }

        if (isset($files)) {
            $d = $this->getPaginationWithPager($files, $this->itemsPerPage);
            $list['files'] = $d['items'];
            unset($d['items']);
            $list = array_merge($list, $d);
        } else {
            $list['files'] = array();
        }

        return $list;
    }

    private function getMediaFile ($mediaId = false)
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
            $media['media_type'] = $this->getMediaType($media['mime_type']);
            $media['file_size_hr'] =
                $this->helpers->HrFilesizeHelper->convert($media['file_size']);

            foreach ($this::$metadataFields as $f) {
                $media['metadata'][$f] =
                    $this->getMetadataField(array(
            		    'media_id' => $mediaId,
                        'label' => $f
                    ));
            }

            $media['metadata'] = $this->getMetadata(array('media_id' => $mediaId));
            $media['tags'] = $this->getTags(array('media_id' => $mediaId));
            $media['caption'] = $this->getCaption(array('media_id' => $mediaId));

            return $media;
        }

        return false;
    }


    private function getMetadata ($p)
    {
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;
        $languageId = isset($p['language_id']) ? $p['language_id'] : $this->languageId;

        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        foreach ($this::$metadataFields as $f) {
            $md[$f] = $this->getMetadataField(array(
    		    'media_id' => $mediaId,
                'label' => $f,
                'language_id' => $languageId
            ));
        }

        return $md;
    }


    private function parseRsMediaList ()
    {
        if (!isset($this->_result) || empty($this->_result)) {
            return false;
        }

        $r = $this->_result;
        $list['total'] = $r->total;
        foreach ($r->resources as $i => $resource) {
            $file['rs_id'] = $resource->ref;
            $file['title'] = $resource->Title;
            $file['file_name'] = $resource->Original_filename;
            $file['height'] = $resource->files[0]->height;
            $file['width'] = $resource->files[0]->width;
            $file['extension'] = $resource->files[0]->extension;
            $file['source'] = $resource->files[0]->src;
            $file['modified'] = $resource->file_modified;
            $file['thumbnails'] = (array)$resource->thumbnails;

            // add flag if image is already attached to entity
            $file['attached'] = 0;
            $list['files'][$i] = $file;
        }
        return $list;
    }

    protected function upload ($p)
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
        $d = $this->getCurrentProjectData();

        $project = $d['sys_name'];
        if (strlen($project) > 25) {
            $d = explode(' ', $project);
            $project = $d[0] . ' ' . end($d);
            if (strlen($project) > 25) {
                $project = substr($project, 0, 22) . '...';
            }
        }

        $this->_result =
            $this->getCurlResult($this->_rsNewUserUrl . '&newuser=' .
            urlencode($project . ' @ ' . $_SERVER['SERVER_ADDR']));

        return $this->_result;
    }


   /**
    * Reformats $_FILES array into something more logical
    */
    private function setFiles ()
    {
        $filePost = $_FILES['files'];
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
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;
        $languageId = isset($p['language_id']) ? $p['language_id'] : $this->languageId;
        $metadata = isset($p['metadata']) ? $p['metadata'] : $this->setMetadataFields();

        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        foreach ($this::$metadataFields as $meta) {

            $val = $this->getMetadataField(array(
     		    'media_id' => $mediaId,
                'label' => $meta,
                'language_id' => $languageId
            ));

            // Data matches value in database; do nothing
            if ($val == $metadata[$meta]) {
                continue;
            // No data entered yet; insert
            } else if (!$val && $metadata[$meta] != '') {
                $this->models->MediaMetadata->insert(array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $mediaId,
                    'language_id' => $languageId,
                    'sys_label' => $meta,
                    'metadata' => $metadata[$meta]
                ));
            // Data has been updated
            } else if ($val != $metadata[$meta] && $metadata[$meta] != '') {
                $this->models->MediaMetadata->update(
        			array('metadata' =>  $metadata[$meta]),
        			array(
            			'project_id' => $this->getCurrentProjectId(),
                        'media_id' => $mediaId,
                        'language_id' => $languageId,
                        'sys_label' => $meta
        		    )
                );
            // Data has been deleted
            } else if ($val && $metadata[$meta] == '') {
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
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;
        $label = isset($p['label']) ? $p['label'] : false;
        $languageId = isset($p['language_id']) ? $p['language_id'] : $this->languageId;

        $metadata = $this->models->MediaMetadata->getSingleColumn(array(
            'columns' => 'metadata',
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_id' => $mediaId,
                'language_id' => $languageId,
                'sys_label' => $label
			)
		));
        return isset($metadata) ? $metadata[0] : false;
    }

    private function saveTags ($p)
    {
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;
        $languageId = isset($p['language_id']) ? $p['language_id'] : $this->languageId;
        $postedTags = isset($p['tags']) ? $p['tags'] :
            array_unique(array_map('trim', explode(',', $this->rGetVal('tags'))));

        if (!$mediaId || !is_numeric($mediaId)) {
            return false;
        }

        $tags = $this->getTags(array('media_id' => $mediaId));

        // Insert values present only in posted
        $insert = array_diff($postedTags, $tags);
        if (!empty($insert)) {
            foreach ($insert as $tag) {
                if ($tag != '') {
                    $this->models->MediaTags->insert(array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'media_id' => $mediaId,
                        'language_id' => $languageId,
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
                    'language_id' => $languageId,
                    'tag' => $tag
                 ));
            }
        }
    }


    private function getTags ($p)
    {
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;
        $languageId = isset($p['language_id']) ? $p['language_id'] : $this->languageId;

        if (!$mediaId || !is_numeric($mediaId)) {
            return array();
        }

        $tags = $this->models->MediaTags->_get(array(
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_id' => $mediaId,
                'language_id' => $languageId
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
        $languageId = isset($p['language_id']) ? $p['language_id'] : $this->languageId;

        if (!$mediaId || $this->moduleId == -1 || $this->itemId == -1) {
            return false;
        }

		$where = array(
			'project_id' => $this->getCurrentProjectId(),
			'language_id' => $languageId,
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

        // First reset all overviews
		$this->models->MediaModules->update(
            array('overview_image' => 0),
            array(
                'project_id' => $this->getCurrentProjectId(),
                'module_id' => $this->moduleId,
    		    'item_id' => $this->itemId
            )
		);

        // Then update
		$this->models->MediaModules->update(
            array('overview_image' => 1),
            array(
                'project_id' => $this->getCurrentProjectId(),
                'media_id' => $mediaId,
                'module_id' => $this->moduleId,
    		    'item_id' => $this->itemId
            )
		);
        $media = $this->getMediaFile($mediaId);

        $mi = new ModuleIdentifierController();
        $mi->setModuleId($this->moduleId);
        $mi->setItemId($this->itemId);
        $mi->setLanguageId($this->languageId);
        $item = $this->_mi->getItemName();

        $this->logChange(['note' => sprintf($this->translate("Set overview Image '%s' to '%s'"), $media['name'], $item)]);


    }

    private function getCaption ($p)
    {
        $mediaId = isset($p['media_id']) ? $p['media_id'] : false;
        $languageId = isset($p['language_id']) ? $p['language_id'] : $this->languageId;

        if (!$mediaId || !is_numeric($mediaId)) {
            return array();
        }

        $caption = $this->models->MediaCaptions->getSingleColumn(array(
            'columns' => 'caption',
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_modules_id' => $this->getMediaModulesId($mediaId),
                'language_id' => $languageId
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

    private function setMetadataFields ()
    {
        foreach ($this::$metadataFields as $f) {
            $d[$f] = $this->rHasVal($f) ? $this->rGetVal($f) : null;
        }
        return $d;
    }

    private function getFileExtenstion ($name)
    {
        return pathinfo($name, PATHINFO_EXTENSION);
    }

    private function checkFileExtension ($file)
    {
        $ext = strtolower($this->getFileExtenstion($file['name']));

        foreach ($this::$mimeTypes as $category => $types) {
            foreach ($types as $extension => $type) {
                if ($ext == $extension) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getMediaType ($mime)
    {
        foreach ($this::$mimeTypes as $category => $types) {
            foreach ($types as $extension => $type) {
                $type = is_array($type) ? $type : array($type);
                if (in_array($mime, $type)) {
                    return $category;
                }
            }
        }
        return false;
    }

    public function getMimeShowOrder ($mime)
    {
        foreach ($this::$mimeTypes as $category => $types) {
            foreach ($types as $extension => $type) {
                $type = is_array($type) ? $type : array($type);
                if (in_array($mime, $type)) {
                    return array_search($category, array_keys($this::$mimeTypes));
                }
            }
        }
        return 99;
    }

    private function replaceInternalMediaLinks ($p)
    {
        $oldMedia = isset($p['old_media']) ? $p['old_media'] : false;
        $newMedia = isset($p['new_media']) ? $p['new_media'] : false;

        if (!$oldMedia || !$newMedia) return false;

        $modules = array(
            'Taxon editor' => array(
                'column' => 'content',
                'table' => 'content_taxa'
            ),
            'Dichotomous key' => array(
                'column' => 'content',
                'table' => 'content_keysteps'
            ),
            'Introduction' => array(
                'column' => 'content',
                'table' => 'content_introduction'
            ),
            'Free modules' => array(
                'column' => 'content',
                'table' => 'content_free_modules'
            ),
            'Glossary' => array(
                'column' => 'definition',
                'table' => 'glossary'
            )
        );

        foreach ($modules as $name => $module) {

            $this->models->MediaModel->replaceInternalMediaLinks(array(
                'project_id' => $this->getCurrentProjectId(),
                'column' => $module['column'],
                'table' => $module['table'],
                'old_media' => $oldMedia,
                'new_media' => $newMedia
            ));
        }
    }
}
