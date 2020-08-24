<?php /** @noinspection PhpMissingParentCallMagicInspection */
/**
 * Batch converts all local media to the ResourceSpace infrastructure
 */

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
    private $internalMediaLinks = array();
    private $uploadInternalMedia = array();

    private $_currentModule;
    private $_currentModuleId;
    private $_currentItemId;
    private $_currentMediaId;
    private $_originalMediaId;
    private $_currentFileName;
    private $_originalFileName;
    private $_rsFile;
    private $_overview;
    private $_sortOrder;
    private $_lastItem;

    private $_maxFileSize;
    private $_filePath;
    private $_error;
    private $_cli = false;
    private $_br = '<br>';

    public $usedModels = array(
        'media',
        'media_metadata',
        'media_modules',
        'media_captions',
        'media_tags',
        'media_conversion_log',
        'media_descriptions_taxon',
        'glossary_media_captions',
        'modules',
        'modules_projects'
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

    /**
     * Destructor
     *
     * @access     public
     */
    public function __destruct ()
    {
        parent::__destruct();
    }

    /**
     * Initializes Controller
     */
    private function initialize ()
    {
        $this->setProjectModules();
        $this->setTaxonEditorModule();
        $this->setFilePath();
        $this->setMaxFileSize();
    }

    public function initAction ()
    {
		$this->setPageName($this->translate('Convert media'));
        $this->smarty->assign('totals', $this->setProjectMedia());
        //$this->fixConversionError();
        $this->printPage();
    }

    private function fixConversionError ()
    {
        if (empty($this->_lastItem)) return;

        $originalMedia = $this->models->MediaModel->getOldStyleTaxonMedia(array(
            'project_id' => $this->getCurrentProjectId(),
            'taxon_id' => $this->_lastItem['item_id']
        ));

        $convertedMedia = $this->models->MediaModel->getItemMediaFiles(array(
            'project_id' => $this->getCurrentProjectId(),
            'item_id' => $this->_lastItem['item_id'],
            'module_id' => $this->_lastItem['module_id'],
        ));

        $delete = array_udiff($convertedMedia, $originalMedia, function ($a, $b) {
            return strcmp($a['file_name'], $b['file_name']);
        });

        if (!empty($delete)) {

            foreach ($delete as $file) {
                $this->models->MediaModules->delete(array(
            		'project_id' => $this->getCurrentProjectId(),
            		'media_id' => $file['id'],
                    'item_id' => $this->_lastItem['item_id'],
                    'module_id' => $this->_lastItem['module_id']
        		));
            }

            $this->addMessage(count($delete) . ' incorrectly linked media files were detached.');
        }
    }

    /**
     * Shows the progress of the conversion
     *
     * @param bool $cli
     */
    public function printConversionProgressAction ($cli = false)
    {
        if ($cli) $this->setCli();

        $this->setProjectMedia();

        foreach ($this->media['modules'] as $name => $module) {

            $this->setCurrentModule(array(
                'name' => $name,
                'id' => $module['id']
            ));

            if (isset($module['media']) && !empty($module['media'])) {

                echo $this->_cli ? "\n$name\n" : "<p><strong>$name</strong><br>";

                foreach ($module['media'] as $row) {

                    $this->resetProperties();
                    $this->setCurrentItem($row);

                    // Check if file has already been converted
                    if ($this->fileHasBeenConverted()) {

                        // Item has already been attached to specific item in a previous conversion
                        if ($this->itemHasBeenAttached()) {
                            echo "Already attached: $this->_currentFileName $this->_br";
                        } else {
                            // File has been converted but not yet attached to item
                            $this->attachFile();
                            echo "Attached: $this->_currentFileName $this->_br";
                        }

                    // Upload file
                    } else {
                        $this->uploadFile();

                        // Oops
                        if (!empty($this->_error)) {
                            echo 'ERROR: ' . $this->_error .  $this->_br;

                        // Success!
                        } else {
                            echo "Uploaded: $this->_currentFileName $this->_br";
                        }
                    }
                }

            }

        }

        echo $this->_cli ? "\n\nConverting internal media links\n" :
            '</p><p><b>Converting internal media links</b><br>';

        $this->convertInternalMediaLinks();
    }

    /* Keystep media should be copied to keystep table and deleted from media_modules */
    private function fixKeystepMedia ()
    {
        return $this->models->MediaModel->fixKeystepMedia($this->getCurrentProjectId());
    }

    private function setInternalMediaLinks ()
    {
        $modules = array(
            'Taxon editor' => array(
                'column' => 'content',
                'table' => 'content_taxa'
            ),
            'Dichotomous key - steps' => array(
                'column' => 'content',
                'table' => 'content_keysteps'
            ),
            'Dichotomous key - choices' => array(
                'column' => 'choice_txt',
                'table' => 'choices_content_keysteps'
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

            $data = $this->getInternalMediaLinks(array(
                'column' => $module['column'],
                'table' => $module['table']
            ));

            $this->internalMediaLinks[$name] = array(
                'column' => $module['column'],
                'table' => $module['table'],
                'data' => $data
            );
        }

    }

    private function convertInternalMediaLinks ()
    {
        $this->setInternalMediaLinks();

        foreach ($this->internalMediaLinks as $name => $module) {

            echo 'Updating links in ' . $name . '...' . $this->_br;

            $this->updateInternalMediaLinks(array(
                'column' => $module['column'],
                'table' => $module['table'],
                'data' => $module['data']
            ));
        }
    }

    private function updateInternalMediaLinks ($p)
    {
        $column = isset($p['column']) ? $p['column'] : false;
        $table = isset($p['table']) ? $p['table'] : false;
        $data = isset($p['data']) ? $p['data'] : false;

        if (!$column || !$table || empty($data)) return false;

        $regExp = '/(..\/..\/..\/shared\/media\/project\/' .
            str_pad($this->getCurrentProjectId(), 4, "0", STR_PAD_LEFT) .
            '\/)(([^,]+?)\.(jpg|mp3|mp4))/i';

        foreach ($data as $row) {

            $this->resetProperties();

            $newContent = $row['content'];
            preg_match_all($regExp, $row['content'], $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {

                $newFile = $this->getRsFile($match[2]);
                $this->_error = false;

                // File exists; replace path
                if (!empty($newFile)) {
                    $newContent = str_replace($match[0], $newFile, $newContent);

                // File still has to be uploaded (should be exceptional!)
                } else {

                    $this->setCurrentFileName($match[2]);
                    $this->setFiles();

                    if (empty($this->_error)) {

                        // Reset module and item id, otherwise media is attached to the
                        // last item parsed!
                        $this->uploadFiles(array('skip_attach' => true));

                        if (!empty($this->_result->resource->files[0]->src)) {

                            $newContent = str_replace($match[0],
                                $this->_result->resource->files[0]->src, $newContent);

                            echo "Uploaded: $this->_currentFileName $this->_br";

                        } else {

                            echo "ERROR: could not upload $this->_currentFileName (" .
                                $this->_result->error . ')' . $this->_br;

                        }

                    } else {

                        echo "ERROR: could not upload $this->_currentFileName (" .
                            $this->_error . ')' . $newFile . $this->_br;

                    }
                }
            }

            $this->models->MediaModel->updateInternalMediaLinks(array(
                'id' => $row['id'],
                'content' => $newContent,
                'table' => $table,
                'column' => $column
            ));

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

    private function setCli ()
    {
        $this->_cli = true;
        $this->_br = "\n";
    }

    private function setProjectMedia ()
    {
        // Determine if process was interrupted
        $this->totals['converted'] =
            $this->models->MediaModel->getConvertedMediaCount(
                array('project_id' => $this->getCurrentProjectId()
            ));

        // Matrix
        $moduleId = $this->getModuleId('matrixkey');
        if ($moduleId) {
            $media = $this->models->MediaModel->getConverterMatrixMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Multi-entry key'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Multi-entry key'] = count($media);
            $this->setLastItem($media, $moduleId);
        }

        // Key: keysteps and choices
        $moduleId = $this->getModuleId('key');

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
                $this->setLastItem($media, $moduleId);
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
                $this->setLastItem($media, $moduleId);
            }
        }

        // Glossary
        $moduleId = $this->getModuleId('glossary');
        if ($moduleId) {
            $media = $this->models->MediaModel->getConverterGlossaryMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Glossary'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Glossary'] = count($media);
            $this->setLastItem($media, $moduleId);
        }

        // Introduction
        $moduleId = $this->getModuleId('introduction');
        if ($moduleId) {
            $media = $this->models->MediaModel->getConverterIntroductionMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Introduction'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Introduction'] = count($media);
            $this->setLastItem($media, $moduleId);
        }

        // Taxa
        $moduleId = $this->getModuleId('nsr');
        if ($moduleId) {
            $media = $this->models->MediaModel->getConverterTaxonMedia(
                array('project_id' => $this->getCurrentProjectId()
            ));
            $this->media['modules']['Taxon editor'] = array(
                'id' => $moduleId,
                'media' => $media
            );
            $this->totals['modules']['Taxon editor'] = count($media);
            $this->setLastItem($media, $moduleId);
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
                    $this->setLastItem($media, $moduleId);
                }
            }
        }

        $this->totals['total'] = array_sum($this->totals['modules']);

        return $this->totals;
    }

    private function setLastItem ($media, $moduleId)
    {
        if (!empty($media)) {
            $this->_lastItem = end($media);
            $this->_lastItem['module_id'] = $moduleId;
        }
    }

    private function getInternalMediaLinks ($p)
    {
        $column = isset($p['column']) ? $p['column'] : false;
        $table = isset($p['table']) ? $p['table'] : false;

        if (!$column || !$table) return false;

        return $this->models->MediaModel->getInternalMediaLinks(array(
            'project_id' => $this->getCurrentProjectId(),
            'table' => $table,
            'column' => $column
        ));

    }

    private function getRsFile ($file)
    {
        $d = $this->models->Media->_get(array(
            'columns' => 'rs_original',
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'name' => $file
             )
        ));

        return isset($d[0]) ? $d[0]['rs_original'] : null;
    }


    private function setProjectModules ()
    {
        $d = $this->getProjectModules();

        // Set simplified array
        foreach ($d['modules'] as $m) {
            $this->projectModules['modules'][$m['module_id']] = $m['controller'];
        }
        if (isset($d['freeModules'])) {
            foreach ($d['freeModules'] as $m) {
                $this->projectModules['freeModules'][$m['id']] = $m['module'];
            }
        }

    }

    private function setTaxonEditorModule ()
    {
        if ($this->getModuleId('species') && !$this->getModuleId('nsr')) {
            $d = $this->models->Modules->_get(array(
                'columns' => 'id',
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'controller' => 'nsr'
                 )
            ));

            $this->models->ModulesProjects->insert(array(
                'module_id' => $d[0]['id'],
                'project_id' => $this->getCurrentProjectId(),
                'show_order' => 0,
                'active' => 'y'
            ));

            $this->setProjectModules();
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
        // @todo: what is happening here?
        if (!isset($row['file_name']) || !isset($row['item_id'])) {
            $this->_currentFileName = $this->_currentItemId = false;
        }
        $this->_currentFileName = $row['file_name'];
        $this->_originalFileName = $row['original_name'];
        $this->_currentItemId = $row['item_id'];
        $this->_originalMediaId = isset($row['media_id']) ? $row['media_id'] : false;
        $this->_overview = isset($row['overview_image']) ? $row['overview_image'] : 0;
        $this->_sortOrder = isset($row['sort_order']) ? $row['sort_order'] : 0;
    }

    private function setCurrentFileName ($name)
    {
        $this->_currentFileName = $name;
    }

    private function resetProperties ()
    {
        $this->_currentMediaId = false;
        $this->_files = false;
        $this->_result = false;
        $this->originalMediaId = false;
        $this->mediaId = false;
        $this->rsFile = false;
        $this->_originalFileName = false;
        $this->_error = false;
        $this->_overview = $this->_sortOrder = 0;
    }

    private function fileHasBeenConverted ()
    {
        $d = $this->models->MediaModel->getMediaId(array(
            'project_id' => $this->getCurrentProjectId(),
            'old_file' => $this->_currentFileName
        ));

        if (!empty($d)) {
            $this->_currentMediaId = $d['media_id'];
            $this->_rsFile = $d['new_file'];
        }

        return !empty($d);
    }

    private function itemHasBeenAttached ()
    {
        $d = $this->models->MediaModel->getMediaConversionId(array(
            'project_id' => $this->getCurrentProjectId(),
            'module' => $this->_currentModule,
            'item_id' => $this->_currentItemId,
            'media_id' => $this->_currentMediaId
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
                'item_id' => $this->_currentItemId,
                'overview_image' => $this->_overview,
                'sort_order' => $this->_sortOrder
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

            $this->uploadFiles(array(
                'overview' => $this->_overview,
                'sort_order' => $this->_sortOrder
            ));

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

        $this->setItemId($this->_currentItemId);
        $this->setModuleId($this->_currentModuleId);

        // Captions for Taxa item
        if ($this->_currentModule == 'Taxon editor') {
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
            $this->_error = "Local file $file cannot be found";

        // It's too large!
        } else if (filesize($file) > $this->_maxFileSize) {
            $error = 1;
            $this->_error = "Size of $file exceeds maximum file size";

        // It's OK!
        } else {
            $type = mime_content_type($file);
            $tmp_name = $file;
            $size = filesize($file);
            $error = 0;
        }

        $this->_files = array(
            array(
                'name' => $this->_currentFileName,
                'type' => $type,
                'tmp_name' => $tmp_name,
                'error' => $error,
                'size' => $size,
                'title' => !empty($this->_originalFileName) ? $this->_originalFileName : ''
            )
        );
    }

}
