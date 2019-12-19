<?php

/*

	call:
		$this->matchTerms($text,$language_id);

	glossary anatomy:

	- the glossary consists of terms.
	- a term consists of the term and its definition.
	- the term can consist of one or more words.
	- term and defintion are defined in one language.
	- terms and defintions have no translations into the other languages, as they are intended to complement the text in a
	  specific language. this means that the glossaries for each language might differ in size and content.
	- each term can have one or more synonyms.
	- a synonym consists of a single term (one or more words) in the same language as the term it is a synonym of.



*/

include_once ('Controller.php');
include_once ('MediaController.php');
include_once ('ModuleSettingsReaderController.php');

class GlossaryController extends Controller
{

	private $_currentGlossaryId = false;

    public $usedModels = array(
		'glossary',
		'glossary_synonyms',
		'glossary_media',
		'glossary_media_captions'
    );

    public $usedHelpers = array(
        'file_upload_helper',
        'image_thumber_helper'
    );

    public $controllerPublicName = 'Glossary';

    public $cssToLoad = array(
		'glossary.css',
		'../vendor/prettyPhoto/css/prettyPhoto.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array(
		'all' => array(
			'glossary.js',
			'lookup.js',
			'int-link.js'
		)
	);

    private $_mc;
    private $termsPerPage=20;

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

    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        parent::__destruct();
    }


    private function initialize()
    {

		$this->moduleSettings=new ModuleSettingsReaderController;
		
		$this->use_media=$this->moduleSettings->getModuleSetting( [ 'setting'=>'no_media','subst'=>0 ] )!=1;
		
		if ( $this->use_media )
		{
			$this->setMediaController();
		}

		$this->smarty->assign( 'use_media', $this->use_media );
	}


	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());
        $this->_mc->setLanguageId($this->getActiveLanguage());
	}



    /**
     * Index of all glossary actions
     *
     * @access    public
     */
    public function indexAction()
    {
		$this->clearTempValues();

		$d = $this->getFirstGlossaryTerm();

		$this->redirect('edit.php?id='.$d['id']);
    }

    /**
     * Browse through all glossary terms
     *
     * @access    public
     */
    public function contentsAction()
    {

        $this->checkAuthorisation();

		$this->setPageName($this->translate('Contents'));

        if (is_null($this->getProjectLanguages()))
		{
			$this->addError(
				sprintf(
					$this->translate('No languages have been defined. You need to define at least one language. Go %shere%s to define project languages.'),
					'<a href="../projects/data.php">','</a>')
				);

		} 
		else
        if (is_null($this->getDefaultProjectLanguage()))
		{
			$this->addError(
				sprintf(
					$this->translate('No default language has been defined. Go %shere%s to set the default languages.'),
					'<a href="../projects/data.php">','</a>')
				);

		} 
		else 
		{
			if (!$this->rHasVal('letter') && !is_null($this->moduleSession->getModuleSetting('activeLetter')))
				$this->requestData['letter'] = $this->moduleSession->getModuleSetting('activeLetter');

			if (!$this->rHasVal('activeLanguage') && !is_null($this->moduleSession->getModuleSetting('activeLanguage')))
				$this->requestData['activeLanguage'] = $this->moduleSession->getModuleSetting('activeLanguage');

			$alpha = $this->getActualAlphabet($this->getActiveLanguage());

			if (!$this->rHasVal('letter') || !in_array($this->rGetVal('letter'),(array)$alpha))
				$this->requestData['letter'] = isset($alpha[0]) ? $alpha[0] : '-';

			if (!$this->rHasVal('activeLanguage'))
				$this->requestData['activeLanguage'] = $this->getDefaultProjectLanguage();

			if ($this->rHasVal('letter'))
			{
				$gloss = $this->getGlossaryTerms(
					array(
						'term like' => $this->rGetVal('letter').'%',
						'language_id' => $this->rGetVal('activeLanguage')
					),
					'term');
			}

			$pagination = $this->getPagination($gloss,$this->termsPerPage);

			$gloss = $pagination['items'];

			$this->smarty->assign('prevStart', $pagination['prevStart']);
			$this->smarty->assign('nextStart', $pagination['nextStart']);
			$this->smarty->assign('languages', $this->getProjectLanguages());
			$this->smarty->assign('activeLanguage', $this->rGetVal('activeLanguage'));
			$this->smarty->assign('alpha', $alpha);

			if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->rGetVal('letter'));

			if (isset($gloss)) $this->smarty->assign('gloss',$gloss);

		}

		$this->printPage();

	}

    /**
     * Create new or edit existing glossary term
     *
     * @access    public
     */
    public function editAction()
    {
        $this->checkAuthorisation();

		if ($this->rHasVal('letter'))
		{
			$gloss = $this->getFirstGlossaryTerm($this->rGetVal('letter'));
		}
		else
		if ($this->rHasId())
		{
			$gloss = $this->getGlossaryTerm($this->rGetId());
		}
		else
		if (!$this->rHasVal('action','new'))
		{
			$gloss = $this->getFirstGlossaryTerm();
		}

		if (isset($gloss['term']))
		{
			$gloss['media'] = $this->getGlossaryMedia($gloss['id']);
			$this->moduleSession->setModuleSetting(array('setting'=>'activeLetter','value'=>strtolower(substr($gloss['term'],0,1))));
			$this->moduleSession->setModuleSetting(array('setting'=>'activeLanguage','value'=>$gloss['language_id']));
    	    $this->setPageName(sprintf($this->translate('Editing glossary term "%s"'),$gloss['term']), $this->translate('Editing glossary term'));
			$navList = $this->getGlossaryTermsNavList();
		} else {
    	    $this->setPageName($this->translate('New glossary term'));
		}

		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
	        $this->checkAuthorisation();

			$this->moduleSession->setModuleSetting(array('setting'=>'activeLetter','value'=>strtolower(substr($this->rGetVal('term'),0,1))));
			$this->moduleSession->setModuleSetting( array('setting'=>'activeLanguage','value'=> $this->rGetVal('language_id')));

			$b=$this->getGlossaryTerm($this->rGetId());
			$this->deleteGlossaryTerm($this->rGetId());

			$this->logChange(array('before'=>$b,'note'=>sprintf('deleted glossary term "%s"',$b['term'])));

			$d = $this->getFirstGlossaryTerm();
			$navList = $this->getGlossaryTermsNavList(true);
			$this->redirect('edit.php?id='.$d['id']);
		}

		if ($this->rHasVal('activeLanguage')) {
			$activeLanguage = $this->rGetVal('activeLanguage');
		} elseif (isset($gloss)) {
			$activeLanguage = $gloss['language_id'];
		} else {
			$activeLanguage = $this->getDefaultProjectLanguage();
		}

		if ($this->rHasVal('term') && $this->rHasVal('definition') && !$this->rHasVal('action','browse') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
	        $this->checkAuthorisation();

			$data = $this->rGetAll();

			$data['project_id'] = $this->getCurrentProjectId();
			$data['id'] =  $this->rHasId() ? $this->rGetId() : null;
            $data['definition'] = $this->cleanUpRichContent($data['definition']);

			$b=$this->rHasId() ? $this->getGlossaryTerm($this->rGetId()) : null;

			if ($data['id']==null && $this->getGlossaryTerms(array('term' => $data['term'],'language_id' => $data['language_id']))) {
				$this->addError($this->translate('Glossary term already exists.'));
				$gloss = $this->rGetAll();
				$activeLanguage = $this->rGetVal('language_id');
			} else if ($this->models->Glossary->save($data)) {
				$navList = $this->getGlossaryTermsNavList(true);
				$id = $this->rHasId() ? $this->rGetId() : $this->models->Glossary->getNewId();

				$this->models->GlossarySynonyms->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'glossary_id' => $id
					)
				);

				if ($this->rHasVal('synonyms'))
				{
					foreach((array)$this->rGetVal('synonyms') as $key => $val)
					{
						$this->models->GlossarySynonyms->save(
							array(
								'project_id' => $this->getCurrentProjectId(),
								'glossary_id' => $id,
								'language_id' => $this->rGetVal('language_id'),
								'synonym' => rawurldecode($val)
							)
						);
					}
				}

				$a=$this->getGlossaryTerm($id);

				if (is_null($b)) {
                    $this->logChange(array('after' => $a, 'note' => sprintf('created glossary term "%s"', $a['term'])));
                } else {
                    $this->logChange(array('before' => $b, 'after' => $a, 'note' => sprintf('updated glossary term "%s"', $a['term'])));
                }

				$this->clearTempValues();

				if ($this->rHasVal('action','media')) {
					$this->redirect('media.php?id='.$id);
				} else if ($this->rHasVal('action','preview')) {
					$this->redirect('preview.php?id='.$id);
				} else {
				    $this->moduleSession->setModuleSetting( array('setting'=>'activeLetter','value'=> strtolower(substr($this->rGetVal('term'),0,1))));
				    $this->moduleSession->setModuleSetting( array('setting'=>'activeLanguage','value'=> $this->rGetVal('language_id')));
					$this->redirect('edit.php?id='.$id);
				}
			}
			else
			{
				$this->addError($this->translate('Could not save glossary term.'));
			}
		}

		$alpha = $this->getActualAlphabet($this->getActiveLanguage());

        if (isset($gloss)) $this->smarty->assign('gloss', $gloss);

        if ($this->getProjectLanguages()) $this->smarty->assign('languages', $this->getProjectLanguages());

		if (isset($navList)) $this->smarty->assign('navList', $navList);
		if (isset($gloss)) $this->smarty->assign('navCurrentId',$gloss['id']);

		$this->smarty->assign('includeHtmlEditor', true);
		$this->smarty->assign('activeLanguage', $activeLanguage);
		$this->smarty->assign('backUrl', $this->rHasId() ? 'browse.php' : 'index.php');
		$this->smarty->assign('alpha', $alpha);

        $this->printPage();

    }

    public function mediaAction ()
    {
        $this->checkAuthorisation();

		if (!$this->rHasId())
		{
		    $this->redirect('index.php');
		}

		$gloss = $this->getGlossaryTerm($this->rGetId());

		$this->setBreadcrumbIncludeReferer(
			array(
				'name' => $this->translate('Editing glossary term'),
				'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php?id='.$gloss['id']
			)
		);

		$this->setPageName(sprintf($this->translate('Media for "%s"'),$gloss['term']), $this->translate('Media'));

		if ($this->rHasVal('action','delete'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();

			$r = $this->detachMedia();
			$this->addMessage($r);
		}

		if ($this->rHasVal('action','save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$this->setOverviewImage();
			$this->saveCaptions();
			$this->addMessage('Saved');
		}

		if ($this->rHasVal('action','up') || $this->rHasVal('action','down'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

		    if ($this->moveImageInOrder())
			{
				$this->addMessage('New media order saved');
			}
		}

		$this->smarty->assign('media', $this->_mc->getItemMediaFiles());
		$this->smarty->assign('name', $gloss['term']);
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id', $this->getActiveLanguage());
		$this->smarty->assign('module_id', $this->getCurrentModuleId());
		$this->smarty->assign('item_id', $this->rGetId());

        $this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','delete_media'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			if (!$this->getAuthorisationState() ) return;
            $this->deleteMedia();
        }
		elseif ($this->rHasVal('action','save_media_desc'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if (!$this->getAuthorisationState() ) return;
            $this->ajaxActionSaveMediaDescription();
        }
		elseif ($this->rHasVal('action','get_media_desc'))
		{
			if (!$this->getAuthorisationState() ) return;
            $this->ajaxActionGetMediaDescription();
        }
		elseif ($this->rHasVal('action','get_media_descs'))
		{
			if (!$this->getAuthorisationState() ) return;
            $this->ajaxActionGetMediaDescriptions();
        }
		elseif ($this->rHasVal('action','get_lookup_list') && !empty($this->rGetVal('search')))
		{
			if (!$this->getAuthorisationState() ) return;
            $this->getLookupList($this->rGetVal('search'));
        }
		elseif ($this->rHasVal('action','save_synonym') && $this->rHasId() && $this->rHasVal('synonym'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if (!$this->getAuthorisationState() ) return;
			$this->saveSynonym($this->rGetId(),$this->rGetVal('language_id'),$this->rGetVal('synonym'));
        }
		elseif ($this->rHasVal('action','delete_synonym') && $this->rHasId() && $this->rHasVal('synonym'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			if (!$this->getAuthorisationState() ) return;
			$this->deleteSynonymByName($this->rGetId(),$this->rGetVal('language_id'),$this->rGetVal('synonym'));
        }

        $this->printPage();

    }

	public function matchTerms($text,$languageId)
	{

		if (empty($text) || empty($languageId)) return;

		$wordlist = $this->getWordList($languageId,true);

		$processed = $text;

		foreach((array)$wordlist as $key => $val) {

			$this->_currentGlossaryId = $val['id'];

			$expr = '|\b('.$val['word'].')\b|i';

			$processed = preg_replace_callback($expr,array($this,'embedGlossaryLink'),$processed);

		}

		return $processed;

	}

	public function getLookupList($search)
	{

		if (empty($search)) return;

		$l1 = $this->models->Glossary->_get(
			array(
				'id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'term like' => '%'.$search.'%'
					),
				'columns' => 'id,term as label,"glossary" as source'
			)
		);

		$l2 = $this->models->GlossarySynonyms->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'synonym like' => '%'.$search.'%'
					),
				'columns' => 'glossary_id as id,synonym as label,"glossary synonym" as source'
			)
		);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>array_merge((array)$l1,(array)$l2),
				'module'=>$this->controllerBaseName,
				'url'=>'../glossary/edit.php?id=%s',
				'sortData'=>true
			))
		); // for glossary lookup list

		return array_merge((array)$l1,(array)$l2); // for combined lookup list

	}

	private function embedGlossaryLink($matches)
	{
        return trim($matches[0]) == '' ? $matches[0] : '<span class="glossary-term-highlight" onmouseover="glossTextOver(' . $this->_currentGlossaryId . ',this)">' . $matches[0] . '</span>';
	}

	private function getWordList($languageId,$force=false)
	{

	    $sLanguageId = $this->moduleSession->getModuleSetting($languageId);

		if ($force || !isset($sLanguageId['wordlist'])) {

			$terms = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $languageId
					),
					'columns' => 'id,term as word,\'term\' as source'
				)
			);

			$synonyms = $this->models->GlossarySynonyms->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $languageId
					),
					'columns' => 'glossary_id as id,synonym as word,\'synonym\' as source'
				)
			);

			$wordlist = array_merge($terms,$synonyms);
			$this->moduleSession->setModuleSetting(array('setting'=>$languageId,'value'=> array('wordlist' => $wordlist)));

		}

		return $wordlist;

	}

	private function deleteMedia($id=null)
	{

		$id = isset($id) ? $id : ($this->rGetId() ? $this->rGetId() : null);

		if ($id == null) return;

		$gm = $this->models->GlossaryMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);

		if (file_exists($_SESSION['admin']['project']['paths']['project_media'].$gm[0]['file_name'])) {

			if (unlink($_SESSION['admin']['project']['paths']['project_media'].$gm[0]['file_name'])) {

				if ($gm[0]['thumb_name'] && file_exists($_SESSION['admin']['project']['paths']['project_thumbs'].$gm[0]['thumb_name'])) {

					unlink($_SESSION['admin']['project']['paths']['project_thumbs'].$gm[0]['thumb_name']);

				}
			}

		}

		$this->models->GlossaryMediaCaptions->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'media_id' => $id
			)
		);

		$this->models->GlossaryMedia->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $id
			)
		);

		$this->smarty->assign('returnText','<ok>');

	}

	private function getGlossaryMedia($id)
	{

		return $this->models->GlossaryMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'glossary_id' => $id
				)
			)
		);

	}

	private function getActualAlphabet($languageId)
	{

		$l = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'language_id'=>$languageId
				),
				'columns' => 'distinct lower(substr(term,1,1)) as letter',
				'order' => 'letter'
			)
		);

		$alpha = null;

		foreach((array)$l as $key => $val) {

			$alpha[] = $val['letter'];

		}

		$this->moduleSession->setModuleSetting(array('setting'=>'alpha','value'=> $alpha));

		return $alpha;

	}

	private function deleteGlossaryTerm($id)
	{

		if (!isset($id)) return;

		$this->models->GlossarySynonyms->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'glossary_id' => $id
			)
		);

		$gm = $this->models->GlossaryMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'glossary_id' => $id
				),
				'columns' => 'id'
			)
		);

		foreach((array)$gm as $key => $val) {

			$this->deleteMedia($val['id']);

		}

		$this->models->Glossary->delete(
			array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $id
			)
		);

	}

	private function getGlossarySynonyms($id)
	{
		return $this->models->GlossarySynonyms->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'glossary_id' => $id
				)
			)
		);

	}

	private function getFirstGlossaryTerm($letter=null)
	{
		$d = array('project_id' => $this->getCurrentProjectId());

		if (isset($letter))  $d['term like'] = $letter.'%';

		$g = $this->models->Glossary->_get(
				array(
					'id' => $d,
					'order' => 'term',
					'limit' => 1
				)
			);

		if ($g) {

			$term = $g[0];

			$term['synonyms'] = $this->getGlossarySynonyms($term['id']);
			$term['media'] = $this->getGlossaryMedia($term['id']);

			return $term;

		} else {

			return false;

		}

	}

	private function getGlossaryTerm($id)
	{
		$l = $this->models->Glossary->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
		));

		if ($l)
		{
			$term = $l[0];
			$term['synonyms'] = $this->getGlossarySynonyms($id);
			$term['media'] = $this->getGlossaryMedia($id);
			return $term;
		}
		else
		{
			return false;
		}
	}

	private function getGlossaryTerms($search,$order=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if (!empty($search['term'])) $d['term'] = $search['term'];
		if (!empty($search['term like'])) $d['term like'] = $search['term like'];
		if (!empty($search['definition'])) $d['definition'] = $search['definition'];
		if (!empty($search['language_id'])) $d['language_id'] =  $search['language_id'];

		$l = $this->models->Glossary->_get(
				array(
					'id' => $d,
					'order' => !empty($order) ? $order : 'term'
				)
			);

		foreach((array)$l as $key => $val) {

			$l[$key]['synonyms'] = $this->getGlossarySynonyms($val['id']);
			$l[$key]['media'] = $this->getGlossaryMedia($val['id']);

		}

		return $l;

	}

	private function getGlossaryTermsNavList($forceLookup=false)
	{

		if (!is_null($this->moduleSession->getModuleSetting('navList')) || $forceLookup) {

			$d = $this->getGlossaryTerms(null);

			foreach((array)$d as $key => $val) {

				$res[$val['id']] = array(
					'prev' => array('id' => isset($d[$key-1]['id']) ? $d[$key-1]['id'] : null, 'title' => isset($d[$key-1]['term']) ? $d[$key-1]['term'] : null),
					'next' => array('id' => isset($d[$key+1]['id']) ? $d[$key+1]['id'] : null, 'title' => isset($d[$key+1]['term']) ? $d[$key+1]['term'] : null),
				);

			}

			$this->moduleSession->setModuleSetting(array('setting'=>'navList','value'=> $res));

		}

		return $this->moduleSession->getModuleSetting('navList');

	}

	private function getGlossaryTermCount()
	{

		$l = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'columns' => 'count(*) as total,language_id',
					'group' => 'language_id',
					'fieldAsIndex' => 'language_id'
				)
			);

		$t = 0;

		foreach((array)$l as $key => $val) {

			$t += $val['total'];

		}

		return array('count' => $l, 'total' => $t);

	}

	private function getActiveLanguage()
	{

		if ($this->rHasVal('activeLanguage')) {

			return $this->rGetVal('activeLanguage');

		} else if ($this->rHasVal('language_id')) {

			return $this->rGetVal('language_id');

		} else if(!is_null($this->getDefaultProjectLanguage())) {

			return $this->getDefaultProjectLanguage();

		} else {

			return false;

		}

	}

	private function clearTempValues()
	{

	    $this->moduleSession->setModuleSetting(array('setting'=>'alpha'));
	    $this->moduleSession->setModuleSetting(array('setting'=>'search'));
	    $this->moduleSession->setModuleSetting(array('setting'=>'activeLetter'));
	    $this->moduleSession->setModuleSetting(array('setting'=>'activeLanguage'));

	}

    private function ajaxActionSaveMediaDescription()
    {

        if (!$this->rHasId() || !$this->rHasVal('language')) {

            return;

        } else {

            if (!$this->rHasVal('description')) {

                $this->models->GlossaryMediaCaptions->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->rGetVal('language'),
                        'media_id' => $this->rGetId()
                    ));

            } else {

                $mdt = $this->models->GlossaryMediaCaptions->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->rGetVal('language'),
							'media_id' => $this->rGetId()
						)
                    )
                );

				$d = $this->filterContent(trim($this->rGetVal('description')));

                $this->models->GlossaryMediaCaptions->save(
                    array(
                        'id' => isset($mdt[0]['id']) ? $mdt[0]['id'] : null,
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->rGetVal('language'),
                        'media_id' => $this->rGetId(),
                        'caption' => $d['content']
                    )
                );

            }

            $this->smarty->assign('returnText', '<ok>');

        }

    }

    private function ajaxActionGetMediaDescription()
    {

        if (!$this->rHasId() || !$this->rHasVal('language')) {

            return;

        } else {

            $mdt = $this->models->GlossaryMediaCaptions->_get(
				array(
					'id' =>  array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->rGetVal('language'),
						'media_id' => $this->rGetId()
					)
				)
			);

            $this->smarty->assign('returnText', $mdt[0]['caption']);

        }

    }

    private function ajaxActionGetMediaDescriptions()
    {

        if (!$this->rHasVal('language')) {

            return;

        } else {

            $mt = $this->models->GlossaryMedia->_get(
				array(
					'id' =>  array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $this->rGetId()
					),
					'columns' => 'id'
				)
			);

            foreach((array)$mt as $key => $val) {

                $mdt = $this->models->GlossaryMediaCaptions->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->rGetVal('language'),
							'media_id' => $val['id']
						),
						'columns' => 'caption'
					)
				);

                $mt[$key]['description'] = $mdt ? $mdt[0]['caption'] : null;
            }

            $this->smarty->assign('returnText', json_encode($mt));

        }

    }

    private function filterContent($content)
    {

		return array('content' => $content, 'modified' => false);

		/*

        if (!$this->controllerSettings['filterContent'])
            return $content;

        $modified = $content;

        if ($this->controllerSettings['filterContent']['html']['doFilter']) {

            $modified = strip_tags($modified,$this->controllerSettings['filterContent']['html']['allowedTags']);

        }

        return array('content' => $modified, 'modified' => $content!=$modified);
		*/

    }

	private function saveSynonym($id,$languageId,$synonym)
	{

		$this->models->GlossarySynonyms->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'glossary_id' => $id,
				'language_id' => !empty($languageId) ? $languageId : $this->getDefaultProjectLanguage(),
				'synonym' => $synonym
			)
		);

	}


	private function deleteSynonymByName($id,$languageId,$synonym)
	{

		$this->models->GlossarySynonyms->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'glossary_id' => $id,
				'language_id' => !empty($languageId) ? $languageId : $this->getDefaultProjectLanguage(),
				'synonym' => $synonym
			)
		);

	}

    private function moveImageInOrder()
    {
		$mediaId = $this->rHasVal('subject') ? $this->rGetVal('subject') : false;
		$direction = $this->rHasVal('action') ? $this->rGetVal('action') : false;

		if (!$mediaId || !$direction || ($direction!='up' && $direction!='down')) {
			return;
		}

		$media =  $this->_mc->getItemMediaFiles();

		foreach ($media as $key => $val) {

		    $this->_mc->setSortOrder(array(
                'media_id' => $val['id'],
                'order' => $key
		    ));

		}

		$r = null;

		foreach ($media as $key => $val) {

		    if ($val['id'] == $mediaId) {

				if ($key == 0 && $direction == 'up') continue;
				if ($key == (count($media)-1) && $direction == 'down') continue;

    		    $this->_mc->setSortOrder(array(
                    'media_id' => $val['id'],
                    'order' => ($key+($direction=='up'?-1:1))
    		    ));

    		    $this->_mc->setSortOrder(array(
                    'media_id' => $media[$key+($direction=='up'?-1:1)]['id'],
                    'order' => ($key+($direction=='up'?1:-1))
    		    ));

				$r = true;

			}
		}

		return $r;

    }

    private function detachMedia ()
    {
    	$mediaId = $this->rHasVal('subject') ? $this->rGetVal('subject') : false;

		if (!$mediaId) {
			return;
		}

		return $this->deleteItemMedia($mediaId);
    }

    private function saveCaptions ()
    {
		$captions = $this->rHasVal('captions') ? $this->rGetVal('captions') : array();

		foreach((array)$captions as $mediaId => $caption) {

		    $this->_mc->saveCaption(array(
		        'media_id' => $mediaId,
                'caption' => $caption
		    ));

		}
    }

    private function setOverviewImage ()
    {
        $mediaId = $this->rHasVal('overview-image') ?
            $this->rGetVal('overview-image') : -1;

        $this->_mc->setOverviewImage($mediaId);

    }

    private function deleteItemMedia ($mediaId)
    {
        $r = $this->_mc->deleteItemMedia($mediaId);

        return $r ?  $this->translate('Detached file') :
            $this->translate('Could not detach file');
    }


}