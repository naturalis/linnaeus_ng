<?php

include_once ('Controller.php');
class IndexController extends Controller
{
    public $noResultCaching = true;
    public $usedModels = array(
        //'synonyms',
        'commonnames'
    );
    public $usedHelpers = array();
    public $cssToLoad = array(
        'index.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'main.js',
        )
    );

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ($p = null)
    {
        parent::__construct($p);
        $this->setHasNameTypes();
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

    /**
     * Index of the index module (ha); shows names
     *
     * @access    public
     */
    public function indexAction ()
    {
		$type=$this->rHasVar('type') ? $this->rGetVal('type') : 'lower';
		$language=$this->rHasVar('language') ? $this->rGetVal('language') : null;

		$alpha=$this->getAlphabet($type,$language);

		$letter=($this->rHasVar('letter') && $this->rGetVal('letter')!=''? $this->rGetVal('letter') : key($alpha['alphabet']));

		$d=$prev=$next=null;
		foreach((array)$alpha['alphabet'] as $key => $val)
		{
			$alphaNav['next']=$key;
			if ($d===true) break;
			if ($key==$letter)
			{
				$d=true;
				$alphaNav['prev']=$prev;
			}
			$prev=$key;
		}

		$alphaNav['next']=($alphaNav['next']==$letter?null:$alphaNav['next']);


        $this->setPageName($this->translate('Index: '.($type=='higher' ? 'Higher taxa' : ($type=='common' ? 'Common names' : 'Species and lower taxa'))));

		if ($type=='common')
		{
			$this->smarty->assign('nameLanguages',$this->getCommonLanguages());
		}

		$this->smarty->assign('alphaNav', $alphaNav);
		$this->smarty->assign('language',$language);
		$this->smarty->assign('type',$type);
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('letter','p')));
		$this->smarty->assign('alpha',$alpha);
		$this->smarty->assign('letter',$letter);
		$this->smarty->assign('list',$this->getList($type,$letter,$language));

		$this->smarty->assign('hasSpecies', $this->moduleSession->getModuleSetting('hasSpecies'));
		$this->smarty->assign('hasHigherTaxa', $this->moduleSession->getModuleSetting('hasHigherTaxa'));
		$this->smarty->assign('hasCommonNames', $this->moduleSession->getModuleSetting('hasCommonNames'));

		$this->printPage();
    }

    public function higherAction ()
    {
        $this->redirect('index.php?type=higher');
    }

    public function commonAction ()
    {
		$this->redirect('index.php?type=common');
    }

	private function reconstructQueryString($ignore=null)
	{
		$querystring=null;

		foreach((array)$this->rGetAll() as $key=>$val)
		{
			if (isset($ignore) && in_array($key,$ignore)) continue;

			if (is_array($val))
			{
				foreach((array)$val as $k2=>$v2)
				{
					$querystring.=$key.'['.$k2.']='.$v2.'&';
				}

			} else {
				$querystring.=$key.'='.$val.'&';
			}
		}

		return $querystring;
	}

    private function getAlphabet($type,$language=null)
    {
		if ($type=='common')
		{

		    $alpha = $this->models->IndexModel->getCommonNamesAlphabet(array(
                'projectId' => $this->getCurrentProjectId(),
		        'languageId' => $language
			));

		}
		else
		{
            $alpha = $this->models->IndexModel->getTaxaAlphabet(array(
                'projectId' => $this->getCurrentProjectId(),
		        'type' => $type
            ));

		}

		$result=array();
		$result['hasNonAlpha']=false;

		foreach((array)$alpha as $val)
		{
			$result['hasNonAlpha']=$result['hasNonAlpha'] || (ord($val['letter'])<97 || ord($val['letter'])>122);
			$result['alphabet'][$val['letter']]=true;
		}

		return $result;
    }

    private function getList($type,$letter,$language=null)
    {
		if ($type=='common')
		{

			$list = $this->models->IndexModel->getCommonNamesList(array(
                'projectId' => $this->getCurrentProjectId(),
			    'languageId' => $language,
			    'letter' => $letter,
            ));

		}
		else
		{

		    $list = $this->models->IndexModel->getTaxaList(array(
                'projectId' => $this->getCurrentProjectId(),
			    'type' => $type,
			    'letter' => $letter,
            ));

			foreach((array)$list as $key=>$val)
			{
				if ($val['source']!='synonym')
					$list[$key]['label']=$this->formatTaxon(array('taxon'=>array('taxon'=>$val['label'],'rank_id'=>$val['rank_id'],'parent_id'=>$val['parent_id']),'rankpos'=>'post'));
				if (!empty($val['ref_taxon']))
					$list[$key]['ref_taxon']=$this->formatTaxon(array('taxon'=>array('taxon'=>$val['ref_taxon'],'rank_id'=>$val['rank_id'],'parent_id'=>$val['parent_id']),'rankpos'=>'post'));
			}

		}
		//q($list);
		return $list;

    }

    private function getCommonLanguages()
    {

		return $this->models->IndexModel->getCommonLanguages(array(
            'projectId' => $this->getCurrentProjectId(),
		    'languageId' => $this->getCurrentLanguageId()
        ));
    }

    private function setHasNameTypes()
    {

        // Check if results have been stored in session; if so return
        if (!is_null($this->moduleSession->getModuleSetting('hasSpecies')) &&
            !is_null($this->moduleSession->getModuleSetting('hasHigherTaxa')) &&
            !is_null($this->moduleSession->getModuleSetting('hasCommonNames')))
        return;

		/*
			usually, taxa that have is_empty==1 (indicating they have no user-generated
			content) are ignored.
			however, if the HT-module has been explicitly activated, we *do* include
			"empty" taxa in the index, so the HT-entries index can link to the HT-module.
			this can be useful as taxa are never truely empty: the classification-tab
			is always generated, automatically, based on the taxonomy.

		*/

		$t = $this->models->IndexModel->setTaxaIndexTabs(array(
            'projectId' => $this->getCurrentProjectId(),
		    'hasHigherTaxa' => $this->doesCurrentProjectHaveModule(MODCODE_HIGHERTAXA)
		));

        $c = $this->models->Commonnames->_get(array(
            'where' => 'project_id = ' . $this->getCurrentProjectId(),
            'columns' => 'count(1)>0 as has_values'
        ));

        $this->moduleSession->setModuleSetting(array(
            'setting'=>'hasSpecies',
            'value'=>isset($t[1]['has_values']) ? $t[1]['has_values'] : 0
        ));
        $this->moduleSession->setModuleSetting(array(
            'setting'=>'hasHigherTaxa',
            'value'=>isset($t[0]['has_values']) ? $t[0]['has_values'] : 0
        ));
        $this->moduleSession->setModuleSetting(array(
            'setting'=>'hasCommonNames',
            'value'=>isset($c[0]['has_values']) ? $c[0]['has_values'] : 0
        ));
    }



}