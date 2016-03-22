<?php

include_once ('Controller.php');
class ModuleIdentifierController extends Controller
{

    private $moduleId;
    private $itemId;
    private $projectModules;

    /*
    * Currently incomplete; extend this for modules requiring info!
    */public $usedModels = array(
        'taxa',
        'glossary',
        'content_introduction'
    );

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();

        $this->loadExternalModel('ModuleIdentifierModel');

        $this->setProjectModules();
    }

    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        parent::__destruct();
    }

	public function setModuleId ($id)
	{
	    $this->moduleId = $id;
	}

	public function setItemId ($id)
	{
	    $this->itemId = $id;
	}

    public function getModuleName ()
    {
        return $this->getModuleProperty('module');
    }

    public function getModuleController ()
    {
        return $this->getModuleProperty('controller');
    }

    public function getItemName ()
	{
        if (!$this->moduleId || !$this->itemId) {
            return false;
        }

        $controller = $this->getModuleProperty('controller');

        if ($controller) {
            switch ($controller) {
                case 'nsr':
                    $r = $this->models->Taxa->_get(array('id'=>array('id'=>$this->itemId)));
                    return $r[0]['taxon'];
                case 'glossary':
                    $r = $this->models->Glossary->_get(array('id'=>array('id'=>$this->itemId)));
                    return $r[0]['term'];
                case 'introduction':
                    $r = $this->models->ContentIntroduction->_get(array('id'=>array('id'=>$this->itemId)));
                    return $r[0]['topic'];
                case 'key':
                    $r = $this->models->ModuleIdentifierModel->getChoiceName(array(
                        'project_id' => $this->getCurrentProjectId(),
                        'choice_id' => $this->itemId
                    ));
                    return $this->translate('choice') . ' ' . $r['choice_number'] . ' ' .
                        $this->translate('of step') . ' ' . $r['keystep_number'];

                default:
                    return false;
            }
        }

        return false;
	}

	private function getModuleProperty ($prop)
    {
        foreach ($this->projectModules as $m) {
            if ($m['module_id'] == $this->moduleId) {
                return isset($m[$prop]) ? $m[$prop] : false;
            }
        }
        return false;
    }

	private function setProjectModules ()
	{
        $pm = $this->getProjectModules();
        $this->projectModules = $pm['modules'];
	}

}