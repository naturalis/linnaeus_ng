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

    public $usedHelpers = array('hr_filesize_helper');

    public static $metadataFields = array(
        'title',
        'location',
        'photographer'
    );

    public static $mimeTypes = array(
        'image' => array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif'
        ),
        'audio' => array(
            'mp3' => 'audio/mpeg'
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
            'csv' => 'text/comma-separated-values'
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
    }


    public function setModuleId ($id)
    {
        $this->moduleId = isset($id) && is_numeric($id) ? $id : -1;
    }

    public function setItemId ($id)
    {
        $this->itemId = isset($id) && is_numeric($id) ? $id : -1;
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
            $media['media_type'] = $this->getMediaType($resource['mime_type']);
            $media['file_size_hr'] =
                $this->helpers->HrFilesizeHelper->convert($media['file_size']);

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
                'language_id' => $this->getCurrentLanguageId()
			)
		));

        return isset($caption) ? $caption[0] : false;

    }

    private function getMediaType ($mime)
    {
        foreach ($this::$mimeTypes as $category => $types) {
            foreach ($types as $extension => $type) {
                if ($mime == $type) {
                    return $category;
                }
            }
        }
        return false;
    }


}
