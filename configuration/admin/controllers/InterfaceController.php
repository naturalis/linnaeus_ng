<?php


include_once ('Controller.php');
class InterfaceController extends Controller
{
    private $_idDutch = 24;
    private $_idEnglish = 26;
    public $usedModels = array();
    public $controllerPublicName = 'Interface texts';
    public $usedHelpers = array();
    public $cssToLoad = array();
    public $jsToLoad = array(
        'all' => array(
            'interface.js'
        )
    );



    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
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

	public function indexAction ()
    {
        $this->setPageName($this->translate('Interface translations'));
        
        $this->printList();
    }



    public function untransAction ()
    {

        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Interface translations'));
        
        $this->printList(true);
    }



    private function printList ($untranslatedOnly = false)
    {

        if ($this->rHasVal('action', 'delete') && $this->rHasVal('id')) {
            
            $this->models->InterfaceTranslation->delete(array(
                'interface_text_id' => $this->requestData['id']
            ));
            
            $this->models->InterfaceText->delete(array(
                'id' => $this->requestData['id']
            ));
        }
        

        $env = $this->rhasVal('env') ? $this->requestData['env'] : $this->getAppname();
        $lan = $this->rhasVal('lan') ? $this->requestData['lan'] : $this->_idDutch;
        
        $texts = $this->getAllTexts(array(
            'lan' => $lan, 
            'env' => $env, 
            'untranslatedOnly' => $untranslatedOnly
        ));
        
        $pagination = $this->getPagination($texts, 25);

        $this->smarty->assign('prevStart', $pagination['prevStart']);
        $this->smarty->assign('currStart', $pagination['currStart']);
        $this->smarty->assign('nextStart', $pagination['nextStart']);
        $this->smarty->assign('texts', $pagination['items']);
        
        $this->smarty->assign('env', $env);
        $this->smarty->assign('lan', $lan);
        
        $this->smarty->assign('isOriginalLanguage', $lan == $this->_idEnglish);
        
        $this->smarty->assign('envs', array(
            $this->getAppName(), 
            $this->generalSettings['appNameFrontEnd']
        ));
        
        if ($this->rHasVal('immediateEdit', '1'))
            $this->smarty->assign('immediateEdit', true);
            
            //        
        


        $this->printPage('index');
    }



    private function getAllTexts ($p = null)
    {
        $env = isset($p['env']) ? $p['env'] : null;
        $lan = isset($p['lan']) ? $p['lan'] : null;
        $untranslatedOnly = isset($p['untranslatedOnly']) ? $p['untranslatedOnly'] : null;
        
        $d = array(
            'project_id' => $this->getCurrentProjectId()
        );
        
        if (in_array($env, array(
            'app', 
            'admin'
        )))
            $d['env'] = $env;
        
        $i = $this->models->InterfaceText->_get(array(
            'id' => $d, 
            'columns' => 'id,text,env', 
            'order' => 'lower(text)'
        ));
        
        if ($untranslatedOnly)
            $d = array();
        
        if (!is_null($lan)) {
            
            foreach ((array) $i as $key => $val) {
                
                $it = $this->models->InterfaceTranslation->_get(
                array(
                    'id' => array(
                        'interface_text_id' => $val['id'], 
                        'language_id' => $lan
                    ), 
                    'columns' => 'translation'
                ));
                
                $i[$key]['translation'] = empty($it[0]) ? null : $it[0]['translation'];
                $i[$key]['translation_language_id'] = $lan;
                
                if ($untranslatedOnly) {
                    if (empty($i[$key]['translation']))
                        $d[] = $i[$key];
                }
            }
        }
        
        if ($untranslatedOnly)
            $i = $d;
        
        return $i;
    }



    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;
        
        if ($this->requestData['action'] == 'save_translation') {
            
            $this->saveTranslation();
        }
        
        $this->printPage();
    }



    private function saveTranslation ()
    {
        $id = empty($this->requestData['param']['id']) ? null : $this->requestData['param']['id'];
        $lan = empty($this->requestData['param']['lan']) ? null : $this->requestData['param']['lan'];
        $newVal = empty($this->requestData['param']['newVal']) ? null : $this->requestData['param']['newVal'];
        
        if (is_null($id) || is_null($lan)) {
            $this->smarty->assign('returnText', 'not saved<br/>(' . (is_null($id) ? 'no id' : 'no lang. id') . ')');
            return;
        }
        
        $this->models->InterfaceTranslation->delete(array(
            'interface_text_id' => $id, 
            'language_id' => $lan
        ));
        
        if (!is_null($newVal)) {
            
            $this->models->InterfaceTranslation->save(array(
                'interface_text_id' => $id, 
                'language_id' => $lan, 
                'translation' => $newVal
            ));
        }
        
        $this->smarty->assign('returnText', 'saved');
    }
}