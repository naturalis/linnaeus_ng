<?php

include_once ('Controller.php');

class InternalLinksController extends Controller
{
    
	private $_lowerSpecies;
	
    public $usedModels = array(
		'content',
		'glossary',
		'literature',
		'project_rank',
		'page_taxon',
		'page_taxon_title',
		'matrix',
		'matrix_name',
		'free_module_project',
		'content_free_module',
		'occurrence_taxon'
    );
    
    public $controllerPublicName = 'Internal Links';


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

	public function intLinksAction()
	{

		if ($this->rHasVal('language'))
			$this->smarty->assign('language',$this->requestData['language']);
		else
			$this->smarty->assign('language',$this->getDefaultProjectLanguage());

		$this->smarty->assign('internalLinks',$this->makeInternalLinksStructure());

        $this->printPage();

	}

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

		foreach((array)$l as $key => $val)  $l[$key]['label'] = $val['id'];

		return $l;

	}

	private function intLinkGetSpecies($higher=false)
	{

		$pr = $this->models->ProjectRank->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'columns' => 'lower_taxon,id',
					'fieldAsIndex' => 'id'
				)
			);

		$l = $this->models->Taxon->_get(
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
					'label' => htmlspecialchars($val['taxon'],ENT_QUOTES).($val['is_hybrid']==1 ? ' &#215;' : '')
				);
		
		}

		if (!$higher) $this->_lowerSpecies = $d;

		return isset($d) ? $d : null;

	}

	private function intLinkGetSpeciesCategories()
	{

		$tp = $this->models->PageTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'id',
				'order' => 'show_order'
			)
		);

		foreach ((array) $tp as $key => $val) {
	
			$tpt = $this->models->PageTaxonTitle->_get(
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

			$ot = $this->models->OccurrenceTaxon->_get(
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

		$m = $this->models->Matrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_names' => 1
				),
				'columns' => 'count(*) as total'
			)
		);
		
		return $m[0]['total'];

	}

 	private function intLinkGetMatrices()
	{
	
		$m = $this->models->Matrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_names' => 1
				)
			)
		);
		
		foreach((array)$m as $key => $val) {

			$mn = $this->models->MatrixName->_get(
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

		return $this->models->FreeModuleProject->_get(
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
	
		$cfm = $this->models->ContentFreeModule->_get(
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
		
		$l = $this->models->Project->freeQuery("
			SELECT _a.*, _b.title, _c.number, _d.choice_txt
				from %PRE%choices_keysteps _a
				left join %PRE%content_keysteps _b
					on _b.keystep_id = _a.keystep_id
					and _b.language_id = ".$this->getDefaultProjectLanguage()."
					and _a.project_id = _b.project_id
				left join %PRE%keysteps _c
					on _c.id = _a.keystep_id
					and _a.project_id = _c.project_id
				left join %PRE%choices_content_keysteps _d
					on _a.id = _d.choice_id
					and _a.project_id = _d.project_id
				where _a.project_id = " . $this->getCurrentProjectId() ."
				order by _a.keystep_id, title
			");

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


	private function makeInternalLinksStructure()
	{

		$i =
			array(
				array(
					'label' => $this->translate('Content pages'),
					'controller' => 'linnaeus',
					'params' =>
						json_encode(array(
							array(
								'label' => $this->translate('Page:'),
								'param' => 'id',
								'values' => $this->intLinkGetContent()
							)
						)
					)
				),
				array(
					'label' => $this->translate('Glossary alphabet'),
					'controller' => 'glossary',
					'params' =>
						json_encode(array(
							array(
								'label' => $this->translate('Letter:'),
								'param' => 'letter',
								'values' => $this->intLinkGetGlossaryAlpha()
							)
						)
					)
				),
				array(
					'label' => $this->translate('Glossary term'),
					'controller' => 'glossary',
					'url' => 'term.php',
					'params' =>	json_encode(
						array(
							array(
								'label' => $this->translate('Term:'),
								'param' => 'id',
								'values' => $this->intLinkGetGlossaryTerms()
							)
						)
					)
				),
				array(
					'label' => $this->translate('Literature index'),
					'controller' => 'literature',
				),
				array(
					'label' => $this->translate('Literature alphabet'),
					'controller' => 'literature',
					'params' =>
						json_encode(array(
							array(
								'label' => $this->translate('Letter:'),
								'param' => 'letter',
								'language_independent' => true,
								'values' => $this->intLinkGetLiteratureAlpha()
							)
						)
					)
				),
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
								'values' => $this->intLinkGetLiteratureReferences()
							)
						)
					)
				),
				array(
					'label' => $this->translate('Species module index'),
					'controller' => 'species',
				),
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
								'values' => $this->intLinkGetSpecies()
							),
							array(
								'label' => $this->translate('Category:'),
								'param' => 'cat',
								'values' => $this->intLinkGetSpeciesCategories()
							)
						)
					)
				),
				array(
					'label' => $this->translate('Higher taxa index'),
					'controller' => 'highertaxa',
				),
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
								'values' => $this->intLinkGetSpecies(true)
							),
							array(
								'label' => $this->translate('Category:'),
								'param' => 'cat',
								'values' => $this->intLinkGetSpeciesCategories()
							)
						)
					)
				),
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
								'values' => $this->intLinkGetKeySteps()
							)
						)
					)
				),
				array(
					'label' => $this->translate('Distribution index'),
					'controller' => 'mapkey',
				),
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
								'values' => $this->intLinkGetMapSpecies()
							)
						)
					)
				),
			);

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

		$mods = $this->intLinkGetFreeModules();
		
		foreach((array)$mods as $key => $val) {

			array_push($i,
				array(
					'label' => $this->translate($val['label'].' index'),
					'controller' => 'module',
					'url' => 'index.php?modId='.$val['id']
				)
			);

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
								'values' => $this->intLinkGetFreeModuleTopics($val['id'])
							)
						)
					)
				)
			);

		}

		return $i;

	}
	
	
}
