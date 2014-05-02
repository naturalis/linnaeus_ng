<?php

include_once ('Controller.php');
class IndexController extends Controller
{
    public $noResultCaching = true;
    public $usedModels = array(
        'synonym', 
        'commonname'
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
        $this->setIndexTabs();
		$this->smarty->assign('hasNameTypes', $_SESSION['app'][$this->spid()]['indexModule']);
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
		$type=$this->rHasVar('type') ? $this->requestData['type'] : 'lower';
		$language=$this->rHasVar('language') ? $this->requestData['language'] : null;

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

		foreach((array)$this->requestData as $key=>$val)
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

			$alpha=$this->models->Taxon->freeQuery("
				select 
					distinct lower(substr(commonname,1,1)) as letter
				from
					%PRE%commonnames
				where
					project_id = ".$this->getCurrentProjectId()."
				".(!empty($language) ? " and language_id=".$language : "" )."
				order by letter
			
			");

		} 
		else 
		{

			$alpha=$this->models->Taxon->freeQuery("
				select distinct unionized.letter, _f.lower_taxon from (
					select 
						distinct lower(substr(taxon,1,1)) as letter,
						project_id,
						rank_id
					from
						%PRE%taxa
					where
						project_id = ".$this->getCurrentProjectId()."
		
					union
		
					select 
						distinct lower(substr(_a.synonym,1,1)) as letter,
						_a.project_id,
						_b.rank_id as rank_id
					from
						%PRE%synonyms _a
		
					right join %PRE%taxa _b
						on _a.project_id = _b.project_id
						and _a.taxon_id = _b.id
		
					where
						_a.project_id = ".$this->getCurrentProjectId()."
				) as unionized
	
				left join %PRE%projects_ranks _f
					on unionized.rank_id=_f.id
					and unionized.project_id = _f.project_id
	
				where
					unionized.project_id = ".$this->getCurrentProjectId()."
					and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
				order by letter
			
			");
			
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

			$list=$this->models->Taxon->freeQuery("
				select 
					_a.taxon_id,_a.commonname,_a.transliteration, concat(ifnull(_b.label,_c.language),':',_a.language_id) as language

				from
					%PRE%commonnames _a

				left join
					%PRE%languages_projects _d
					on _a.language_id = _d.id

				left join
					%PRE%languages _c
					on _d.language_id = _c.id
	
				left join
					%PRE%labels_languages _b
					on _a.project_id = _b.project_id
					and _a.language_id = _b.language_id
					and _b.label_language_id = ".$this->getCurrentLanguageId()."
					
				where
					_a.project_id = ".$this->getCurrentProjectId()."
					".(!empty($language) ? " and _a.language_id=".$language : "" )."
					".(!empty($letter) ? "and _a.commonname like '".mysql_real_escape_string($letter)."%'" : null)."

				order by
					_a.commonname
			");
			
		} 
		else 
		{
			$list=$this->models->Taxon->freeQuery("
				select unionized.*, _f.lower_taxon from (
					select 
						project_id,
						id as taxon_id,
						taxon as label, 
						null as author,
						null as ref_taxon,
						author as ref_author,
						rank_id,
						parent_id,
						is_empty,
						'taxon' as source
					from
						%PRE%taxa
					where
						project_id = ".$this->getCurrentProjectId()."
		
					union
		
					select 
						_a.project_id,
						_a.taxon_id, 
						_a.synonym as label, 
						_a.author,
						_b.taxon as ref_taxon,
						_b.author as ref_author,
						_b.rank_id as rank_id, 
						_b.parent_id as parent_id, 
						_b.is_empty as is_empty, 
						'synonym' as source
					from
						%PRE%synonyms _a
		
					right join %PRE%taxa _b
						on _a.project_id = _b.project_id
						and _a.taxon_id = _b.id
		
					where
						_a.project_id = ".$this->getCurrentProjectId()."
				) as unionized
	
				left join %PRE%projects_ranks _f
					on unionized.rank_id=_f.id
					and unionized.project_id = _f.project_id
	
				where
					unionized.project_id = ".$this->getCurrentProjectId()."
					and _f.lower_taxon = ".($type=='higher' ? 0 : 1)."
					".(!empty($letter) ? "and label like '".mysql_real_escape_string($letter)."%'" : null)."
				order by label
			");
			
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
		$list=$this->models->Taxon->freeQuery("
			select 
				distinct _a.language_id, ifnull(_b.label,_c.language) as language, _a.language_id as id

			from
				%PRE%commonnames _a

			left join
				%PRE%languages _c
				on _a.language_id = _c.id

			left join
				%PRE%labels_languages _b
				on _a.project_id = _b.project_id
				and _a.language_id = _b.language_id
				and _b.label_language_id = ".$this->getCurrentLanguageId()."

			where
				_a.project_id = ".$this->getCurrentProjectId()."
		");

		return $list;
    }

    private function setIndexTabs()
    {

        // Check if results have been stored in session; if so return
        if (isset($_SESSION['app'][$this->spid()]['indexModule']['hasSpecies']) &&
			isset($_SESSION['app'][$this->spid()]['indexModule']['hasHigherTaxa']) &&
			isset($_SESSION['app'][$this->spid()]['indexModule']['hasCommonNames']))
		return;

		/*
			usually, taxa that have is_empty==1 (indicating they have no user-generated
			content) are ignored.
			however, if the HT-module has been explicitly activated, we *do* include
			"empty" taxa in the index, so the HT-entries index can link to the HT-module.
			this can be useful as taxa are never truely empty: the classification-tab
			is always generated, automatically, based on the taxonomy.
		
		*/
		$t = $this->models->Taxon->freeQuery(
			array(
				'query'=>
					"select count(_a.id)>0 as has_values, _c.lower_taxon
					from %PRE%taxa _a
					left join %PRE%projects_ranks _c
						on _a.rank_id = _c.id
						and _a.project_id = _c.project_id 
					where _a.project_id = " . $this->getCurrentProjectId() . "
					".(!$this->doesCurrentProjectHaveModule(MODCODE_HIGHERTAXA) ? "and _a.is_empty = 0" : "" )."
					group by _c.lower_taxon",
				'fieldAsIndex'=>'lower_taxon'
			)
		);

        $c = $this->models->Commonname->_get(array(
            'where' => 'project_id = ' . $this->getCurrentProjectId(), 
            'columns' => 'count(1)>0 as has_values'
        ));
		
		$_SESSION['app'][$this->spid()]['indexModule']['hasSpecies'] = isset($t[1]['has_values']) ? $t[1]['has_values'] : 0;
		$_SESSION['app'][$this->spid()]['indexModule']['hasHigherTaxa'] = isset($t[0]['has_values']) ? $t[0]['has_values'] : 0;
        $_SESSION['app'][$this->spid()]['indexModule']['hasCommonNames'] = isset($c[0]['has_values']) ? $c[0]['has_values'] : 0;

    }



}