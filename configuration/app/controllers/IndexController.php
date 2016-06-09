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
	
	private $_commonNameTypes=[PREDICATE_PREFERRED_NAME,PREDICATE_ALTERNATIVE_NAME];

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p = null)
    {
        parent::__construct($p);
        $this->setHasNameTypes();
    }

    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct()
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

		$list=$this->getIndexList( [ 'type'=>$type, 'letter'=>$letter, 'language'=>$language ] );


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
			$this->smarty->assign('nameLanguages',$this->getCommonNameLanguages());
		}

		$this->smarty->assign('alphaNav', $alphaNav);
		$this->smarty->assign('language',$language);
		$this->smarty->assign('type',$type);
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('letter','p')));
		$this->smarty->assign('alpha',$alpha);
		$this->smarty->assign('letter',$letter);
		$this->smarty->assign('list',$list);
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

    private function getIndexList( $p )
    {
		$type = isset($p['type']) ? $p['type'] : null;
		$letter = isset($p['letter']) ? $p['letter'] : null;
		$language = isset($p['language']) ? $p['language'] : null;
       	$ranks = $this->getProjectRanks();
		
		if ($type=='common')
		{
			$list = $this->models->IndexModel->getCommonNames(array(
				'project_id' => $this->getCurrentProjectId(),
				'label_language_id' => $this->getCurrentLanguageId(),
				'language_id' => $language,
				'nametypes' => $this->_commonNameTypes,
				'letter' => $letter
			));
			
			foreach((array)$list as $key=>$val)
			{
				$list[$key]['taxon']=
					$this->formatTaxon(
						array(
							'taxon'=>array('taxon'=>$val['taxon'],'rank_id'=>$val['rank_id'],'parent_id'=>$val['parent_id']),
							'rankpos'=>'post',
							'ranks'=>$ranks
					));
			}


		}
		else
		{
		    $list = $this->models->IndexModel->getScientificNameList(array(
                'project_id' => $this->getCurrentProjectId(),
				'nametypes'=> [PREDICATE_VALID_NAME,PREDICATE_SYNONYM,PREDICATE_SYNONYM_SL,PREDICATE_HOMONYM,PREDICATE_MISSPELLED_NAME,PREDICATE_INVALID_NAME],
			    'type' => $type,
			    'letter' => $letter,
			    'display_language_id' => $this->getCurrentLanguageId(),
			    'valid_name_id' => $this->getNameTypeId(PREDICATE_VALID_NAME)				
            ));

			foreach((array)$list as $key=>$val)
			{
				if ($val['nametype']==PREDICATE_VALID_NAME)
					$list[$key]['label']=
						$this->formatTaxon(
							array(
								'taxon'=>array('taxon'=>$val['name'],'rank_id'=>$val['rank_id'],'parent_id'=>$val['parent_id']),
								'rankpos'=>'post',
								'ranks'=>$ranks
						));

				if (!empty($val['ref_taxon']))
					$list[$key]['ref_taxon']=
						$this->formatTaxon(
							array(
								'taxon'=>array('taxon'=>$val['ref_taxon'],'rank_id'=>$val['rank_id'],'parent_id'=>$val['parent_id']),
								'rankpos'=>'post',
								'ranks'=>$ranks
						));
			}

		}

		return $list;

    }

    private function getCommonNameLanguages()
    {
		return $this->models->IndexModel->getCommonNameLanguages(array(
			'project_id' => $this->getCurrentProjectId(),
			'label_language_id' => $this->getCurrentLanguageId(),
			'nametypes' => $this->_commonNameTypes
        ));
    }

    private function setHasNameTypes()
    {
        // Check if results have been stored in session; if so return
        if (!is_null($this->moduleSession->getModuleSetting('hasSpecies')) &&
            !is_null($this->moduleSession->getModuleSetting('hasHigherTaxa')) &&
            !is_null($this->moduleSession->getModuleSetting('hasCommonNames')))
		return;

		$t=$this->models->IndexModel->getHasHigherLower( ['project_id' => $this->getCurrentProjectId() ] );

        $this->moduleSession->setModuleSetting( ['setting'=>'hasSpecies', 'value'=>$t['has_lower'] ]);
        $this->moduleSession->setModuleSetting( ['setting'=>'hasHigherTaxa', 'value'=>$t['has_higher'] ]);

		$t=$this->models->IndexModel->getHasNames( [
			'project_id' => $this->getCurrentProjectId(),
			'nametypes' => $this->_commonNameTypes
		] );

        $this->moduleSession->setModuleSetting( ['setting'=>'hasCommonNames', 'value'=>$t['has_names'] ]);
    }

}




















