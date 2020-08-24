<?php 

include_once 'Controller.php';

class InternalLinksController extends Controller
{

	private $_lowerSpecies;

    public $usedModels = array(
		'content',
		'glossary',
		'literature',
		'projects_ranks',
		'pages_taxa',
		'pages_taxa_titles',
		'matrices',
		'matrices_names',
		'free_modules_projects',
		'content_free_modules',
		'occurrences_taxa',
		'content_introduction'
    );

    public $controllerPublicName = 'Internal Links';
    public $modelNameOverride = 'KeyModel';

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
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {

        parent::__destruct();

    }

    /**
     *
     */
    public function intLinksAction()
	{

		if ($this->rHasVal('language'))
			$this->smarty->assign('language',$this->rGetVal('language'));
		else
			$this->smarty->assign('language',$this->getDefaultProjectLanguage());

		$this->smarty->assign('internalLinks',$this->makeInternalLinksStructure());

        $this->printPage();

	}

    /**
     * @return null
     */
    private function intLinkGetGlossaryTerms()
	{

		$l = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'columns' => 'id,language_id,term',
					'order' => 'language_id,term',
					)
				);

		foreach((array)$l as $key => $val) {

			$r[$val['language_id']][] = array('id' => $val['id'],'label' => htmlspecialchars($val['term'],ENT_QUOTES));

		}

		return isset($r) ? $r : null;

	}

    /**
     * @return null
     */
    private function intLinkGetGlossaryAlpha()
	{

		$l = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
				),
				'columns' => 'distinct lower(substr(term,1,1)) as letter, language_id',
				'order' => 'letter'
			)
		);

		foreach((array)$l as $key => $val) {

			$r[$val['language_id']][] = array('id' => $val['letter'],'label' => $val['letter']);

		}

		return isset($r) ? $r : null;

	}

 	private function intLinkGetLiteratureReferences()
	{

		$l = $this->models->Literature->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'order' => 'label',
					'columns' => 'id,
					 				concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									),
									\' \',
									year(`year`),
									if(suffix!=\'\',suffix,\'\')
								) as label'
				)
			);

		foreach((array)$l as $key => $val) $l[$key]['label'] = htmlspecialchars($val['label'],ENT_QUOTES);

		return $l;

	}

	private function intLinkGetLiteratureAlpha()
	{

		$l = $this->models->Literature->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'distinct lower(substr(author_first,1,1)) as id',
				'order' => 'author_first'
			)
		);

		foreach((array)$l as $key => $val) {
            $l[$key]['label'] = $val['id'];
        }

		return $l;

	}

	private function intLinkGetSpecies($higher=false)
	{

		$pr = $this->models->ProjectsRanks->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'columns' => 'lower_taxon,id',
					'fieldAsIndex' => 'id'
				)
			);

		$l = $this->models->Taxa->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'order' => 'taxon_order',
					'columns' => 'id, taxon, rank_id,is_hybrid',
					'fieldAsIndex' => 'id'
				)
			);

		foreach((array)$l as $key => $val) {

			if (isset($pr[$val['rank_id']]) && $pr[$val['rank_id']]['lower_taxon']==($higher ? 0 : 1))

				$d[] = array(
					'id'=> $val['id'],
					'label' => htmlspecialchars($val['taxon'],ENT_QUOTES).($val['is_hybrid']==1 ? ' ' . $this->_hybridMarkerHtml : '')
				);

		}

		if (!$higher) $this->_lowerSpecies = $d;

		return isset($d) ? $d : null;

	}

	private function intLinkGetSpeciesCategories()
	{

		$tp = $this->models->PagesTaxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'id',
				'order' => 'show_order'
			)
		);

		foreach ((array) $tp as $key => $val) {

			$tpt = $this->models->PagesTaxaTitles->_get(
				array(
					'id'=>array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $val['id']
					),
					'columns'=>'language_id,title'
				)
			);

			foreach((array)$tpt as $tKey => $tVal) {

				$l[$tVal['language_id']][] = array('id' => $val['id'],'label' => $tVal['title']);
				$d[$tVal['language_id']] = $tVal['language_id'];

			}

		}

		if (isset($d)) {

			foreach((array)$d as $key => $val) {

				$l[$val][] = array('id' => 'media','label' => 'Media','untranslated' => 1);
				$l[$val][] = array('id' => 'classification','label' => 'Classification','untranslated' => 1);
				$l[$val][] = array('id' => 'literature','label' => 'Literature','untranslated' => 1);
				$l[$val][] = array('id' => 'names','label' => 'Synonyms','untranslated' => 1);

			}

		}

		return isset($l) ? $l : null;

	}

	private function intLinkGetMapSpecies()
	{

		if (!$this->_lowerSpecies) $this->intLinkGetSpecies();

		foreach((array)$this->_lowerSpecies as $key => $val) {

			$ot = $this->models->OccurrencesTaxa->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $val['id']
					),
					'columns' => 'count(*) as total'
				)
			);

			if ($ot[0]['total']>0) {

				$d[] = array(
					'id'=> $val['id'],
					'label' => $val['label']
				);

			}

		}

		return isset($d) ? $d : null;

	}

	private function intLinkGetMatricexCount()
	{

		$m = $this->models->Matrices->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'count(*) as total'
			)
		);

		return $m[0]['total'];

	}

 	private function intLinkGetMatrices()
	{

		$m = $this->models->Matrices->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		foreach((array)$m as $key => $val) {

			$mn = $this->models->MatricesNames->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $val['id']
					),
					'columns' => 'language_id,matrix_id,name'
				)
			);

			foreach((array)$mn as $mKey => $mVal) {

				$d[$mVal['language_id']][] = array('id' => $mVal['matrix_id'],'label' => $mVal['name']);

			}

		}

		return isset($d) ? $d : null;

	}

	private function intLinkGetContent()
	{

		$c = $this->models->Content->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'id,language_id,subject'
			)
		);

		foreach((array)$c as $key => $val) {

			$l[$val['language_id']][] = array('id' => $val['id'],'label' => $val['subject']);

		}

		return isset($l) ? $l : null;

	}

	private function intLinkGetFreeModules()
	{

		return $this->models->FreeModulesProjects->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
				),
				'columns' => 'id,module as label'
			)
		);

	}

	private function intLinkGetFreeModuleTopics($id)
	{

		$cfm = $this->models->ContentFreeModules->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'module_id' => $id,
				),
				'columns' => 'language_id,page_id, topic',
				'order' => 'topic'
			)
		);

		foreach((array)$cfm as $key => $val) {

			$l[$val['language_id']][] = array('id' => $val['page_id'],'label' => $val['topic']);

		}

		return isset($l) ? $l : null;

	}

	private function intLinkGetKeySteps()
	{

	    $l = $this->models->KeyModel->getInternalLinksKeysteps(array(
            'projectId' => $this->getCurrentProjectId(),
	        'languageId' => $this->getDefaultProjectLanguage()
	    ));

		$d=array();
		foreach((array)$l as $key => $val) {

			if (!isset($d[$val['keystep_id']])) {

				$d[$val['keystep_id']] = array(
					'id'=> $val['keystep_id'],
					'label' =>
						htmlspecialchars($val['title'].'. '.substr(strip_tags($val['choice_txt']),0,25),ENT_QUOTES).'...'
				);

			} else {

				$d[$val['keystep_id']]['label'] .= ' / '.htmlspecialchars(substr(strip_tags($val['choice_txt']),0,25),ENT_QUOTES).'...';

			}

		}

		unset($l);

		foreach((array)$d as $val) $l[] = $val;


		return isset($l) ? $l : null;

	}

	private function intLinkGetIntroduction()
	{

		$c = $this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'id,language_id,topic'
			)
		);

		foreach((array)$c as $key => $val) {

			$l[$val['language_id']][] = array('id' => $val['id'],'label' => $val['topic']);

		}

		return isset($l) ? $l : null;

	}


	private function makeInternalLinksStructure()
	{

		$modules = $this->models->ModulesProjects->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'columns' => 'module_id',
			'fieldAsIndex' => 'module_id'
		));


		$i = array();

		if (isset($modules[MODCODE_SPECIES]))
		{

			array_push($i,
				array(
					'label' => $this->translate('Species module index'),
					'controller' => 'species',
				)
			);

			$d=$this->intLinkGetSpecies();
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Species module detail'),
						'controller' => 'species',
						'url' => 'taxon.php',
						'params' => json_encode(
							array(
								array(
									'label' => $this->translate('Species:'),
									'param' => 'id',
									'language_independent' => true,
									'values' => $d
								),
								array(
									'label' => $this->translate('Category:'),
									'param' => 'cat',
									'values' => $this->intLinkGetSpeciesCategories()
								)
							)
						)
					)
				);

		}


		if (isset($modules[MODCODE_HIGHERTAXA]))
		{

			array_push($i,
				array(
					'label' => $this->translate('Higher taxa index'),
					'controller' => 'highertaxa',
				)
			);

			$d=$this->intLinkGetSpecies(true);
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Higher taxa detail'),
						'controller' => 'highertaxa',
						'url' => 'taxon.php',
						'params' => json_encode(
							array(
								array(
									'label' => $this->translate('Taxa:'),
									'param' => 'id',
									'language_independent' => true,
									'values' => $d
								),
								array(
									'label' => $this->translate('Category:'),
									'param' => 'cat',
									'values' => $this->intLinkGetSpeciesCategories()
								)
							)
						)
					)
				);

		}


		if (isset($modules[MODCODE_INTRODUCTION]))
		{

			$d=$this->intLinkGetIntroduction();
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Introduction'),
						'controller' => 'introduction',
						'params' =>
							json_encode(array(
								array(
									'label' => $this->translate('Page:'),
									'param' => 'id',
									'values' => $d
								)
							)
						)
					)
				);

		}


		if (isset($modules[MODCODE_GLOSSARY]))
		{

			$d=$this->intLinkGetGlossaryAlpha();
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Glossary alphabet'),
						'controller' => 'glossary',
						'params' =>
							json_encode(array(
								array(
									'label' => $this->translate('Letter:'),
									'param' => 'letter',
									'values' => $d
								)
							)
						)
					)
				);

			$d=$this->intLinkGetGlossaryTerms();
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Glossary term'),
						'controller' => 'glossary',
						'url' => 'term.php',
						'params' =>	json_encode(
							array(
								array(
									'label' => $this->translate('Term:'),
									'param' => 'id',
									'values' => $d
								)
							)
						)
					)
				);

		}


		if (isset($modules[MODCODE_LITERATURE]))
		{

			array_push($i,
				array(
					'label' => $this->translate('Literature index'),
					'controller' => 'literature',
				)
			);

			$d=$this->intLinkGetLiteratureAlpha();
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Literature alphabet'),
						'controller' => 'literature',
						'params' =>
							json_encode(array(
								array(
									'label' => $this->translate('Letter:'),
									'param' => 'letter',
									'language_independent' => true,
									'values' => $d
								)
							)
						)
					)
				);

			$d=$this->intLinkGetLiteratureReferences();
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Literature reference'),
						'controller' => 'literature',
						'url' => 'reference.php',
						'params' =>	json_encode(
							array(
								array(
									'label' => $this->translate('Reference:'),
									'param' => 'id',
									'language_independent' => true,
									'values' => $d
								)
							)
						)
					)
				);

		}


		if (isset($modules[MODCODE_KEY]))
		{

			$d=$this->intLinkGetKeySteps(true);
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Dichotomous key'),
						'controller' => 'key',
						'url' => 'index.php?r',
						'params' => json_encode(
							array(
								array(
									'label' => $this->translate('Step:'),
									'param' => 'step',
									'language_independent' => true,
									'values' => $d
								)
							)
						)
					)
				);

		}


		if (isset($modules[MODCODE_DISTRIBUTION]))
		{

			array_push($i,
				array(
					'label' => $this->translate('Distribution index'),
					'controller' => 'mapkey',
				)
			);

			$d=$this->intLinkGetMapSpecies(true);
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Distribution detail'),
						'controller' => 'mapkey',
						'url' => 'examine_species.php',
						'params' => json_encode(
							array(
								array(
									'label' => $this->translate('Species:'),
									'param' => 'id',
									'language_independent' => true,
									'values' => $d
								)
							)
						)
					)
				);

		}


		if (isset($modules[MODCODE_MATRIXKEY]))
		{

			$mc = $this->intLinkGetMatricexCount();

			if ($mc>1) {

				array_push($i,
					array(
						'label' => $this->translate('Matrix key index'),
						'controller' => 'matrixkey',
						'url' => 'matrices.php'
					)
				);


				array_push($i,
					array(
						'label' => $this->translate('Matrix keys'),
						'controller' => 'matrixkey',
						'params' => json_encode(
							array(
								array(
									'label' => $this->translate('Matrix name:'),
									'param' => 'mtrx',
									'values' => $this->intLinkGetMatrices()
								),
								array(
									'label' => $this->translate('Element:'),
									'param' => 'url',
									'language_independent' => true,
									'values' => array(
										array('id' => 'index.php', 'label' => 'Index', 'untranslated' => 1),
										array('id' => 'identify.php', 'label' => 'Identify', 'untranslated' => 1),
										array('id' => 'examine.php', 'label' => 'Examine', 'untranslated' => 1),
										array('id' => 'compare.php', 'label' => 'Compare', 'untranslated' => 1),
									)
								)
							)
						)
					)
				);

			} else
			if ($mc==1) {

				array_push($i,
					array(
						'label' => $this->translate('Matrix key'),
						'controller' => 'matrixkey',
						'params' => json_encode(
							array(
								array(
									'label' => $this->translate('Element:'),
									'param' => 'url',
									'language_independent' => true,
									'values' => array(
										array('id' => 'index.php', 'label' => 'Index', 'untranslated' => 1),
										array('id' => 'identify.php', 'label' => 'Identify', 'untranslated' => 1),
										array('id' => 'examine.php', 'label' => 'Examine', 'untranslated' => 1),
										array('id' => 'compare.php', 'label' => 'Compare', 'untranslated' => 1),
									)
								)
							)
						)
					)
				);

			}

		}


		$mods = $this->intLinkGetFreeModules();

		foreach((array)$mods as $key => $val) {

			array_push($i,
				array(
					'label' => $this->translate($val['label'].' index'),
					'controller' => 'module',
					'url' => 'index.php?modId='.$val['id']
				)
			);

			$d=$this->intLinkGetFreeModuleTopics($val['id']);
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate($val['label'].' topic'),
						'controller' => 'module',
						'url' => 'topic.php?modId='.$val['id'],
						'params' => json_encode(
							array(
								array(
									'label' => $this->translate('Topic:'),
									'param' => 'id',
									'values' => $d
								)
							)
						)
					)
				);

		}

		if (isset($modules[MODCODE_CONTENT]))
		{

			$d=$this->intLinkGetContent();
			if (count($d)>0)
				array_push($i,
					array(
						'label' => $this->translate('Content pages'),
						'controller' => 'linnaeus',
						'params' =>
							json_encode(array(
								array(
									'label' => $this->translate('Page:'),
									'param' => 'id',
									'values' => $d
								)
							)
						)
					)
				);

		}

		return $i;

	}


}
