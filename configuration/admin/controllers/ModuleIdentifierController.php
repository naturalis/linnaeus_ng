<?php 
/**
 * Controller which supplies information about projects and modules to other controllers. Used by MediaController and ProjectsController
 */

include_once ('Controller.php');
class ModuleIdentifierController extends Controller
{

    private $moduleId;
    private $itemId;
    private $languageId;
    private $projectModules;
    private $projectFreeModules;

    /*
    * Currently incomplete; extend this for modules requiring info!
    */public $usedModels = array(
        'taxa',
        'glossary',
        'content_introduction',
        'content_free_modules'
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

	public function setLanguageId ($id)
	{
	    $this->languageId = $id;
	}

	public function getModuleName ()
    {
        return $this->getModuleProperty('module');
    }

    public function getModuleController ()
    {
        $c = $this->getModuleProperty('controller');

        // Return free module controller as free_module
        if (empty($c)) {
            foreach ($this->projectFreeModules as $m) {
                if ($m['id'] == $this->moduleId) {
                    return 'free_module';
                }
            }
            return false;
        }

        return $c;
    }

    public function getModuleIdByController ($controller)
    {
        foreach ($this->projectModules as $m) {
            if ($m['controller'] == $controller) {
                return $m['module_id'];
            }
        }
        return false;
    }

    public function getItemEditPage ()
    {
        if (!$this->moduleId || !$this->itemId) {
            return false;
        }

        $controller = $this->getModuleController();

        if ($controller) {
            switch ($controller) {
                case 'nsr':
                    return 'taxon.php?id=';
                case 'key':
                    return 'choice_edit.php?id=';
                case 'matrixkey':
                    return 'state.php?id=';
                default:
                    return 'edit.php?id=';
            }
        }
    }

    public function setMediaBackUrl ()
    {
        if (!$this->moduleId || !$this->itemId) {
            return false;
        }

        $controller = $this->getModuleController();

        if ($controller) {
            switch ($controller) {
                case 'nsr':
                    return '../' . $controller. '/media.php?id=' . $this->itemId . '&noautoexpand=1';
                case 'glossary':
                    return '../' . $controller. '/media.php?id=' . $this->itemId;
                case 'key':
                    return '../' . $controller. '/choice_edit.php?id=' . $this->itemId;
                case 'matrixkey':
                    return '../' . $controller. '/state.php?id=' . $this->itemId;
                default:
                    return '../' . $controller. '/edit.php?id=' . $this->itemId;
            }
        }
    }

    public function getItemName ()
	{
        if (!$this->moduleId || !$this->itemId) {
            return false;
        }

        $controller = $this->getModuleController();

        if ($controller) {
            switch ($controller) {
                case 'nsr':
                    $r = $this->models->Taxa->_get(array('id'=>array('id'=>$this->itemId)));
                    return $r[0]['taxon'];
                case 'glossary':
                    $r = $this->models->Glossary->_get(array('id'=>array('id'=>$this->itemId)));
                    return $r[0]['term'];
                case 'introduction':
                    $r = $this->models->ContentIntroduction
                        ->_get(array('id'=>array('page_id'=>$this->itemId)));
                    return $r[0]['topic'];
                case 'key':
                    $r = $this->models->ModuleIdentifierModel->getChoiceName(array(
                        'project_id' => $this->getCurrentProjectId(),
                        'choice_id' => $this->itemId
                    ));
                    return $this->translate('choice') . ' ' . $r['choice_number'] . ' ' .
                        $this->translate('of step') . ' ' . $r['keystep_number'];
                case 'matrixkey':
                    $r = $this->models->ModuleIdentifierModel->getStateName(array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->languageId,
                        'state_id' => $this->itemId
                    ));
                    return $this->translate('state') . ' ' . $r['state_label'] . ' ' .
                        $this->translate('of characteristic') . ' ' . $r['characteristic_label'];
                case 'free_module':
                    $r = $this->models->ContentFreeModules
                        ->_get(array('id'=>array('id'=>$this->itemId)));
                    return $r[0]['topic'];
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
        foreach ($this->projectFreeModules as $m) {
            if ($m['id'] == $this->moduleId) {
                return isset($m[$prop]) ? $m[$prop] : false;
            }
        }
        return false;
    }

	private function setProjectModules ()
	{
        $pm = $this->getProjectModules();
        $this->projectModules = is_array($pm['modules']) ? $pm['modules'] : array();
        $this->projectFreeModules = is_array($pm['freeModules']) ? $pm['freeModules'] : array();
	}

}