<?php

/*
 * TODO: remove limit 6000 from MediaModel getConverterTaxonMedia()!!!!!
 *
 */


include_once ('MediaController.php');

class MediaConverterController extends MediaController
{

    private $projectModules;
    private $totals = array();
    private $media = array();

    private $_currentModule;
    private $_currentModuleId;
    private $_currentItemId;
    private $_currentMediaId;
    private $_originalMediaId;
    private $_currentFileName;
    private $_originalFileName;
    private $_rsFile;

    private $_maxFileSize;
    private $_filePath;
    private $_error;

    public $usedModels = array(
        'media',
        'media_metadata',
        'media_modules',
        'media_captions',
        'media_tags',
        'media_conversion_log',
        'media_descriptions_taxon',
        'glossary_media_captions'
    );

    public $usedHelpers = array('hr_filesize_helper');



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
        $this->setProjectModules();
        $this->setFilePath();
        $this->setMaxFileSize();
    }

    public function initAction ()
    {
        $this->smarty->assign('totals', $this->setProjectMedia());
        $this->printPage();
    }

    public function printConversionProgressAction ()
    {
        $this->setProjectMedia();

        foreach ($this->media['modules'] as $name => $module) {

            $this->setCurrentModule(array(
                'name' => $name,
                'id' => $module['id']
            ));
            echo "<p><strong>$name</strong><br>";

            foreach ($module['media'] as $row) {

                $this->resetProperties();
                $this->setCurrentItem($row);

                // File name is present; check if already attached
                if ($this->fileHasBeenConverted()) {

                    // Item has already been converted
                    if ($this->itemHasBeenConverted()) {
                        echo "Already converted: $this->_currentFileName<br>";

                    // File has been converted but not yet attached to item
                    } else {
                        $this->attachFile();
                        echo "Attached: $this->_currentFileName<br>";
                    }

                // Upload file
                } else {

                    $this->uploadFile();

                    // Oops
                    if (!empty($this->_error)) {
                        echo '<span class="error">ERROR</span>: ' . $this->_error . '<br>';

                    // Success!
                    } else {
                        echo "Uploaded: $this->_currentFileName<br>";
                    }
                }
            }

            echo '</p>';
        }
    }



    private function setMaxFileSize ()
    {
        $h = new HrFilesizeHelper();

        $upload = $h->returnBytes(ini_get('upload_max_filesize'));
        $post = $h->returnBytes(ini_get('post_max_size'));

        $this->_maxFileSize = $upload <= $post ? $upload : $post;
    }

    private function setFilePath ()
    {
        $this->_filePath = $this->getProjectsMediaStorageDir();
    }

    private function setProjectMedia ()
    {
        // Determine if process was interrupted
        $converted = $this->models->MediaConversionLog->_get(array(
            'columns' => 'count(*) as total',
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        $this->totals['converted'] = $converted[0]['total'];

        // Matrix
        $moduleId = $this->getModuleId('Multi-entry key');
        if ($moduleId) {
           $media = $this->models->MediaModel->getConverterMatrixMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Multi-entry key'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Multi-entry key'] = count($media);
        }

        // Key: keysteps and choices
        $moduleId = $this->getModuleId('Single-access key');
        if ($moduleId) {
            // Steps
            $media = $this->models->MediaModel->getConverterKeystepsMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            if (!empty($media)) {
                $this->media['modules']['Dichotomous key steps'] = array(
                    'id' => $moduleId,
                    'media' => $media
                );
                $this->totals['modules']['Dichotomous key steps'] = count($media);
            }
            // Choices
            $media = $this->models->MediaModel->getConverterKeychoicesMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            if (!empty($media)) {
                $this->media['modules']['Dichotomous key choices'] = array(
                    'id' => $moduleId,
                    'media' => $media
                );
                $this->totals['modules']['Dichotomous key choices'] = count($media);
            }
        }

        // Glossary
        $moduleId = $this->getModuleId('Glossary');
        if ($moduleId) {
            $media = $this->models->MediaModel->getConverterGlossaryMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Glossary'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Glossary'] = count($media);
        }

        // Introduction
        $moduleId = $this->getModuleId('Introduction');
        if ($moduleId) {
            $media = $this->models->MediaModel->getConverterIntroductionMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Introduction'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Introduction'] = count($media);
        }

        // Taxa
        $moduleId = $this->getModuleId('Beheer Soortenregister');
        if ($moduleId) {
            $media = $this->models->MediaModel->getConverterTaxonMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Taxa'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Taxa'] = count($media);
        }

        // Free module(s)
        if (isset($this->projectModules['freeModules'])) {
            foreach ($this->projectModules['freeModules'] as $id => $module) {
                $media = $this->models->MediaModel->getConverterFreeModuleMedia(
                    array(
                        'project_id' => $this->getCurrentProjectId(),
                        'module_id'=> $id
                    )
                );
                if (!empty($media)) {
                    $this->media['modules'][$module] = array(
                        'id' => $id,
                        'media' => $media
                    );
                    $this->totals['modules'][$module] = count($media);
                }
            }
        }

        $this->totals['total'] = array_sum($this->totals['modules']);

        return $this->totals;
    }


    private function setProjectModules ()
    {
        $d = $this->getProjectModules();

        // Set simplified array
        foreach ($d['modules'] as $m) {
            $this->projectModules['modules'][$m['module_id']] = $m['module'];
        }
        if (isset($d['freeModules'])) {
            foreach ($d['freeModules'] as $m) {
                $this->projectModules['freeModules'][$m['id']] = $m['module'];
            }
        }
    }

    private function setCurrentModule ($p)
    {
        $this->_currentModule = $p['name'];
        $this->_currentModuleId = $p['id'];
    }

    private function getModuleId ($m)
    {
        return array_search($m, $this->projectModules['modules']);
    }

    private function setCurrentItem ($row)
    {
        if (!isset($row['file_name']) || !isset($row['item_id'])) {
            $this->_currentFileName = $this->_currentItemId = false;
        }
        $this->_currentFileName = $row['file_name'];
        $this->_originalFileName = $row['original_name'];
        $this->_currentItemId = $row['item_id'];
        $this->_originalMediaId = isset($row['media_id']) ? $row['media_id'] : false;
    }

    private function resetProperties ()
    {
        $this->_currentMediaId = $this->_files = $this->_result =
            $this->originalMediaId = $this->mediaId = $this->rsFile =
            $this->_originalFileName = $this->_error = false;
    }

    private function fileHasBeenConverted ()
    {
        $d = $this->models->MediaConversionLog->_get(array(
            'columns' => 'media_id, new_file',
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'old_file' => $this->_currentFileName
             )
        ));

        $this->_currentMediaId = !empty($d) ? $d[0]['media_id'] : false;
        $this->_rsFile = $d[0]['new_file'];

        return $this->_currentMediaId !== false;
    }

    private function itemHasBeenConverted ()
    {
        $d = $this->models->MediaConversionLog->getSingleColumn(array(
            'columns' => 'media_id',
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'module' => $this->_currentModule,
                'item_id' => $this->_currentItemId,
             )
        ));

        return !empty($d);
    }

    private function attachFile ()
    {
        if ($this->_currentMediaId && $this->_currentItemId) {
            $this->models->MediaModules->insert(array(
                'module_id' => $this->_currentModuleId,
                'media_id' => $this->_currentMediaId,
                'project_id' => $this->getCurrentProjectId(),
                'item_id' => $this->_currentItemId
            ));
        }
        $this->saveCaptions();
        $this->logAction();
    }

    private function uploadFile ()
    {
        $this->setFiles();

        if (empty($this->_error)) {

            $this->setItemId($this->_currentItemId);
            $this->setModuleId($this->_currentModuleId);

            $this->uploadFiles();

            // Upload errors are stored in $this->errors
            if (!empty($this->errors)) {
                $this->_error = implode('; ', $this->errors);
                $this->errors = array();
            } else {
                $this->saveCaptions();
            }
        }

        $this->logAction();
    }

    private function saveCaptions ()
    {
        if (!$this->_originalMediaId)  {
            return false;
        }

        // Captions for Taxa item
        if ($this->_currentModule == 'Taxa') {
            $captions = $this->models->MediaDescriptionsTaxon->_get(array(
                'columns' => 'description as caption, language_id',
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $this->_originalMediaId
                 )
            ));
        }

        // Captions for Glossary item
        if ($this->_currentModule == 'Glossary') {
            $captions = $this->models->GlossaryMediaCaptions->_get(array(
                'columns' => 'caption, language_id',
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'media_id' => $this->_originalMediaId
                 )
            ));
        }

        if (isset($captions) && !empty($captions)) {

            foreach ($captions as $row) {
                $this->saveCaption(array(
                    'caption' => $row['caption'],
                    'media_id' => !empty($this->mediaId) ?
                        $this->mediaId : $this->_currentMediaId,
                    'language_id' => $row['language_id']
                ));
             }
        }
    }

    private function logAction ()
    {
        // File has been uploaded to RS
        if (!empty($this->_result) && empty($this->_result->error)) {
            $mediaId = $this->mediaId;
            $newFile = $this->_result->resource->files[0]->src;

        // File has been attached
        } else if (!empty($this->_currentMediaId) && !empty($this->_rsFile)) {
            $mediaId = $this->_currentMediaId;
            $newFile = $this->_rsFile;

        // Something went horribly wrong
        } else {
            $mediaId = -1;
            $newFile = 'failed';
        }

        if (isset($mediaId)) {
            $this->models->MediaConversionLog->insert(array(
                'project_id' => $this->getCurrentProjectId(),
                'module' => $this->_currentModule,
                'media_id' => $mediaId,
                'item_id' => $this->_currentItemId,
                'old_file' => $this->_currentFileName,
                'new_file' => $newFile,
                'error' => $this->_error
            ));
        }
    }

    private function setFiles ()
    {
        $file = $this->_filePath . $this->_currentFileName;
        $type = $tmp_name = $size = null;

        // It's not there!
        if (!file_exists($file)) {
            $error = 4;
            $this->_error = "Local file $this->_currentFileName cannot be found";

        // It's too large!
        } else if (filesize($file) > $this->_maxFileSize) {
            $error = 1;
            $this->_error = "Size of $this->_currentFileName exceeds maximum file size";

        // It's OK!
        } else {
            $type = mime_content_type($file);
            $tmp_name = $file;
            $size = filesize($file);
            $error = 0;
        }

        $this->_files = array(
            array(
                'name' => !empty($this->_originalFileName) ?
                    $this->_originalFileName : $this->_currentFileName,
                'type' => $type,
                'tmp_name' => $tmp_name,
                'error' => $error,
                'size' => $size
            )
        );
    }

}