<?php

/*

	make sure that if there are multiple parameters, they are in alphabetical
	order (this is assumed while matching with the current URL in the app).
	so:

		$this->saveHotword(
			array(
				'project_id' => $val['project_id'],
				[...]
				'params' => 'id='. $val['id'].'&modId='.$val['module_id']
			)
		)

	good,

		$this->saveHotword(
			array(
				'project_id' => $val['project_id'],
				[...]
				'params' => 'modId='.$val['module_id'].'&id='. $val['id']
			)
		)

	bad.


*/

include_once ('Controller.php');

class HotwordController extends Controller
{

    public $usedModels = array(
		'hotwords',
		'content_introduction',
		'glossary',
		'glossary_synonyms',
		'content_keysteps',
		'literature',
		'content_free_modules',
		'commonnames'
    );

    public $controllerPublicName = 'Hotwords';

	public $jsToLoad = array('all' => array('hotwords.js'));

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

    /**
     * Index
     *
     * @access    public
     */
    public function indexAction()
    {

		$this->checkAuthorisation();

		$this->setPageName($this->translate('Hotwords'));

		if ($this->rHasVal('action','delete_all') && !$this->isFormResubmit()) {

			$this->clearHotwords();


		}

		$c = $this->models->Hotwords->_get(
			array('id'=>
				array('project_id'=>$this->getCurrentProjectId()),
				'columns'=>'count(*) as tot, controller',
				'group' => 'controller'
			)
		);

		$this->smarty->assign('controllers',$c);

        $this->printPage();

	}

    /**
     * Update
     *
     * @access    public
     */
    public function updateAction()
    {

		$this->checkAuthorisation();

		$this->setPageName($this->translate('Update hotwords'));

		if ($this->rHasVal('action','update') && !$this->isFormResubmit()) {

			$this->clearHotwords();

			$this->addMessage('Deleted old hotwords.');
			$this->addMessage('Added '.$this->updateIntroduction().' hotwords from Introduction.');
			$this->addMessage('Added '.$this->updateGlossary().' hotwords from Glossary.');
			$this->addMessage('Added '.$this->updateLiterature().' hotwords from Literature.');
			$this->addMessage('Added '.$this->updateSpecies().' hotwords from Species.');
			$this->addMessage('Added '.$this->updateCommonNames().' hotwords from Common names.');
			$this->addMessage('Added '.$this->updateKey().' hotwords from Dichotomous key.');
			$this->addMessage('Added '.$this->updateFreeModules().' hotwords from free modules.');

		}

		$h = $this->models->Hotwords->_get(
			array('id'=>
				array('project_id'=>$this->getCurrentProjectId()),
				'columns'=>'date_format(max(created),\'%d-%m-%Y %H:%i:%s\') as last_created'
			)
		);

		$this->smarty->assign('last_created',$h[0]['last_created']);

        $this->printPage();

	}

	/**
     * Delete
     *
     * @access    public
     */
    public function browseAction()
    {

		$this->checkAuthorisation();

		$this->setPageName($this->translate('Browse hotwords'));

		$id = array('project_id' => $this->getCurrentProjectId());

		if ($this->rHasVal('c')) {

			$id['controller'] = $this->requestData['c'];

			$this->smarty->assign('controller',$this->requestData['c']);

		}

		if ($this->rHasVal('id') && $this->rHasVal('action','delete')) {

			$this->models->Hotwords->delete(array_merge($id,array('id' => $this->requestData['id'])));


		} else
		if ($this->rHasVal('action','delete_module')) {

			$this->models->Hotwords->delete($id);


		}

		$h = $this->models->Hotwords->_get(array('id' => $id,'order' => 'hotword'));

		$pagination = $this->getPagination($h,20);

		$slice = $pagination['items'];

		$this->smarty->assign('prevStart', $pagination['prevStart']);

		$this->smarty->assign('nextStart', $pagination['nextStart']);

		$this->smarty->assign('num',count((array)$h));

		$this->smarty->assign('hotwords',$slice);

        $this->printPage();

	}




	private function clearHotwords()
	{

		$r = $this->models->Hotwords->delete(array('project_id' => $this->getCurrentProjectId()));

		//if ($r!=1) echo $r.'<br />';

	}

	private function saveHotword($p)
	{

		$p['hotword'] = trim($p['hotword']);

		if (is_numeric($p['hotword']) || empty($p['hotword'])) return;

		return @$this->models->Hotwords->save(
			array(
				'id' => null,
				'project_id' => $p['project_id'],
				'language_id' => $p['language_id'],
				'hotword' => trim($p['hotword']),
				'controller' => $p['controller'],
				'view' => $p['view'],
				'params' => isset($p['params']) ? $p['params'] : null
			)
		);

	}

	private function updateIntroduction()
	{

		$res = 0;

		$d = $this->models->ContentIntroduction->_get(array('id' =>array('project_id' => $this->getCurrentProjectId())));

		foreach((array)$d as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => $val['language_id'],
					'hotword' => $val['topic'],
					'controller' => 'introduction',
					'view' => 'topic',
					'params' => 'id='.$val['page_id']
				)
			)===true) $res++;

		}

		return $res;

	}

	private function updateGlossary()
	{

		$res = 0;

		$d = $this->models->Glossary->_get(array('id'=>array('project_id' => $this->getCurrentProjectId())));

		foreach((array)$d as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => $val['language_id'],
					'hotword' => $val['term'],
					'controller' => 'glossary',
					'view' => 'term',
					'params' => 'id='.$val['id']
				)
			)===true) $res++;

		}

		$d = $this->models->GlossarySynonyms->_get(array('id'=>array('project_id' => $this->getCurrentProjectId())));

		foreach((array)$d as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => $val['language_id'],
					'hotword' => $val['synonym'],
					'controller' => 'glossary',
					'view' => 'term',
					'params' => 'id='.$val['glossary_id']
				)
			)===true) $res++;

		}

		return $res;

	}

	private function updateLiterature()
	{

		$res = 0;

		$d = $this->models->Literature->_get(
			array('id' =>
				array('project_id' => $this->getCurrentProjectId()),
				'columns' => '
					id,
					project_id,
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						),
						\', \',
						year(`year`),
						if(suffix!=\'\',suffix,\'\')
					) as author_format_a,
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						),
						\' (\',
						year(`year`),
						if(suffix!=\'\',suffix,\'\'),
						\')\'
					) as author_format_b'
			)
		);

		foreach((array)$d as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => 0,
					'hotword' => $val['author_format_a'],
					'controller' => 'literature',
					'view' => 'reference',
					'params' => 'id='.$val['id']
				)
			)===true) $res++;

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => 0,
					'hotword' => $val['author_format_b'],
					'controller' => 'literature',
					'view' => 'reference',
					'params' => 'id='.$val['id']
				)
			)===true) $res++;

		}

		return $res;

	}

	private function updateSpecies()
	{

		$res = 0;

		$d = $this->models->Taxa->_get(array('id' =>array('project_id' => $this->getCurrentProjectId(),'is_empty'=>0)));

		foreach((array)$d as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => 0,
					'hotword' => $val['taxon'],
					'controller' => 'species',
					'view' => 'taxon',
					'params' => 'id='.$val['id']
				)
			)===true) $res++;

		}

		return $res;

	}

	private function updateCommonNames()
	{
		$res = 0;

		$c = $this->models->Commonnames->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));

		foreach((array)$c as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => 0,
					'hotword' => $val['commonname'],
					'controller' => 'species',
					'view' => 'taxon',
					'params' => 'id='.$val['taxon_id']
				)
			)===true) $res++;

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => 0,
					'hotword' => $val['transliteration'],
					'controller' => 'species',
					'view' => 'taxon',
					'params' => 'id='.$val['taxon_id']
				)
			)===true) $res++;

		}

		return $res;

	}

	private function updateKey()
	{

		$res = 0;

		$d = $this->models->ContentKeysteps->_get(array('id' =>array('project_id' => $this->getCurrentProjectId())));

		foreach((array)$d as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => $val['language_id'],
					'hotword' => $val['title'],
					'controller' => 'key',
					'view' => 'index',
					'params' => 'step='.$val['keystep_id']
				)
			)===true) $res++;

		}

		return $res;

	}

	private function updateFreeModules()
	{

		$res = 0;

		$d = $this->models->ContentFreeModules->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));

		foreach((array)$d as $key => $val) {

			if ($this->saveHotword(
				array(
					'project_id' => $val['project_id'],
					'language_id' => $val['language_id'],
					'hotword' => $val['topic'],
					'controller' => 'module',
					'view' => 'topic',
					'params' => 'id='. $val['id'].'&modId='.$val['module_id']
				)
			)===true) $res++;

		}

		return $res;

	}

}