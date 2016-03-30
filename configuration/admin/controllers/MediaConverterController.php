<?php

/*
 * This controller is supposed to be used from the command line
 *
 */


include_once ('MediaController.php');

class MediaConverterController extends MediaController
{

    private $matrixMedia;
    private $keystepsMedia;
    private $keychoicesMedia;
    private $freeModuleMedia;
    private $glossaryMedia;
    private $introductionMedia;
    private $taxonMedia;

    public $convertedMedia;
    public $totals = array();

    private $projectModules;

    private $_currentModule;
    private $_currentItemId;
    private $_currentFileName;

    public $usedModels = array(
        'media_conversion_log'
    );


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
    }

    public function initAction ()
    {
        $this->smarty->assign('totals', $this->setTotals());
        $this->printPage();
    }

    public function printConversionProgressAction ()
    {
        $this->setTotals();

        if (!empty($this->matrixMedia)) {

        }


    }





    private function setTotals ()
    {
        // Determine if process was interrupted
        $converted = $this->models->MediaConversionLog->_get(array(
            'columns' => 'count(*) as total',
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        $this->totals['converted'] = $converted[0]['total'];

        $this->matrixMedia = $this->models->MediaModel
            ->getConverterMatrixMedia(array('project_id' => $this->getCurrentProjectId()));
        $this->totals['modules']['Matrix key'] = count($this->matrixMedia);

        $this->keystepsMedia = $this->models->MediaModel
            ->getConverterKeystepsMedia(array('project_id' => $this->getCurrentProjectId()));
        $this->totals['totals']['Dichotomous key: key steps'] = count($this->keystepsMedia);

        $this->keychoicesMedia = $this->models->MediaModel
            ->getConverterKeychoicesMedia(array('project_id' => $this->getCurrentProjectId()));
        $this->totals['modules']['Dichotomous key: key choices'] = count($this->keychoicesMedia);

        $this->freeModuleMedia = $this->models->MediaModel
            ->getConverterFreeModuleMedia(array('project_id' => $this->getCurrentProjectId()));
        $this->totals['modules']['Free module'] = count($this->freeModuleMedia);

        $this->glossaryMedia = $this->models->MediaModel
            ->getConverterGlossaryMedia(array('project_id' => $this->getCurrentProjectId()));
        $this->totals['modules']['Glossary'] = count($this->glossaryMedia);

        $this->introductionMedia = $this->models->MediaModel
            ->getConverterIntroductionMedia(array('project_id' => $this->getCurrentProjectId()));
        $this->totals['modules']['Introduction'] = count($this->introductionMedia);

        $this->taxonMedia = $this->models->MediaModel
            ->getConverterTaxonMedia(array('project_id' => $this->getCurrentProjectId()));
        $this->totals['modules']['Taxon'] = count($this->taxonMedia);

        $this->totals['total'] = array_sum($this->totals['modules']);

        return $this->totals;
    }

    // Check to see if file has been converted and attached to module
    private function itemHasBeenConverted ()
    {
        $d = $this->models->MediaConversionLog->getSingleColumn(array(
            'column' => 'media_id',
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'module' => $this->_currentModule,
                'item_id' => $$this->_currentItemId,
             )
        ));

        return !empty($d) ? $d[0] : false;
    }

    // Check to see if file has been converted
    private function fileHasBeenConverted ()
    {
        $d = $this->models->MediaConversionLog->_get(array(
            'column' => 'media_id',
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'old_file' => $this->_currentFileName
             )
        ));

        return !empty($d) ? $d[0] : false;
    }


    private function setProjectModules ()
    {
        $d = $this->getProjectModules();

        // Set simplified array
        foreach ($d['modules'] as $m) {
            $this->projectModules['modules'][$m['module_id']] = $m['module'];
        }
        foreach ($d['freeModules'] as $m) {
            $this->projectModules['freeModules'][$m['id']] = $m['module'];
        }
    }

    private function setCurrentModule ($module)
    {
        $this->_currentModule = $module;
    }

    private function setCurrentModuleId ($module = false)
    {
        if (!$module) {
            $module = $this->_currentModule;
        }

        return array_search($module, $this->projectModules['modules']);
    }
}
