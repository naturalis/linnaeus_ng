<?php

include_once ('Controller.php');

class MediaController extends Controller
{

    private $moduleId;
    private $itemId;
    private $languageId;

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
    public $modelNameOverride = 'MediaModel';

    public static $metadataFields = array(
        'title',
        'location',
        'photographer'
    );

    public static $mimeTypes = array(
        'image' => array(
            'image/png' => 'png',
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg',
            'image/gif' => 'gif',
        ),
        'video' => array(
            'video/mp4' => 'mp4'
        ),
        'audio' => array(
            'audio/mpeg' => 'mp3',
            'audio/mp3' => 'mp3'
        ),
        'pdf' => array(
            'application/pdf' => 'pdf',
        ),
        'text' => array(
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.oasis.opendocument.text' => 'odt',
            'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
            'text/rtf' => 'rtf',
            'text/plain' => 'txt',
            'text/comma-separated-values' => 'csv',
        )
    );


    public function __construct($p=null)
    {
        parent::__construct($p);
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
        $this->moduleId = $this->rHasVal('module_id') ? $this->rGetVal('module_id') : -1;
        $this->itemId = $this->rHasVal('id') ? $this->rGetVal('id') : -1;
        $this->languageId = $this->getCurrentLanguageId();
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
        $this->languageId = isset($id) && is_numeric($id) ? $id : -1;
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

    public function getItemMediaFileCount ()
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

        return !empty($m) ? count($m) : 0;
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

            foreach ($this::$metadataFields as $f) {
                $media['metadata'][$f] =
                    $this->getMetadataField(array(
            		    'media_id' => $mediaId,
                        'label' => $f
                    ));
            }

            //$media['tags'] = $this->getTags($mediaId);
            $media['caption'] = $this->getCaption($mediaId);

            return $media;
        }

        return false;
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


    private function getMetadataField ($p)
    {
        // No $p check, only used internally
        $metadata = $this->models->MediaMetadata->getSingleColumn(array(
            'columns' => 'metadata',
            'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			    'media_id' => $p['media_id'],
                'language_id' => $this->getCurrentLanguageId(),
                'sys_label' => $p['label']
			)
		));
        return isset($metadata) ? $metadata[0] : false;
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

    public function getMediaType ($mime)
    {
        foreach ($this::$mimeTypes as $category => $types) {
            foreach ($types as $type => $extension) {
                if ($mime == $type) {
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
                if ($mime == $type) {
                    return array_search($category, array_keys($this::$mimeTypes));
                }
            }
        }
        return 99;
    }

    public function reformatOutput (&$d, $displayOverview = true)
    {
        foreach ($d as $i => $m) {

            $d[$i]['mime_show_order'] = $this->getMimeShowOrder($m['mime_type']);
            $d[$i]['full_path'] = $m['rs_original'];
            $d[$i]['description'] = $m['caption'];
            $d[$i]['file_name'] = $d[$i]['original_name'] = $m['name'];
            $d[$i]['mime'] = $d[$i]['category'] = $m['media_type'];

            if ($d[$i]['overview_image'] == 1 && !$displayOverview) {
                unset($d[$i]);
            }
        }

        return $d;
    }

    public function getOverview ()
    {
        return $this->models->MediaModel->getOverview(array(
            'project_id' => $this->getCurrentProjectId(),
            'module_id' =>$this->moduleId,
            'item_id' => $this->itemId
        ));
    }

}
