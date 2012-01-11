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

class GlossaryController extends Controller
{

	private $_currentGlossatyId = false;

    public $usedModels = array(
		'glossary',
		'glossary_synonym',
		'glossary_media'
    );
   
    public $usedHelpers = array(
        'file_upload_helper','image_thumber_helper'
    );

    public $controllerPublicName = 'Glossary';


	public $cssToLoad = array(
		'glossary.css',
		'colorbox/colorbox.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array(
		'all' => array(
			'glossary.js',
			'colorbox/jquery.colorbox.js',
			'lookup.js',
			'int-link.js',
			'dialog/jquery.modaldialog.js'
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

    /**
     * Index of all glossary actions
     *
     * @access    public
     */
    public function indexAction()
    {

		$this->clearTempValues();

		//$d = $this->getFirstGlossaryTerm();
		
		//$this->redirect('edit.php?id='.$d['id']);
		$this->redirect('edit.php');
    
		/*
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));

		$this->clearTempValues();
		
		$gtc = $this->getGlossaryTermCount();

		$this->smarty->assign('totalCount', $gtc['total']);

        $this->printPage();
		
		*/
    
    }

    /**
     * Browse through all glossary terms
     *
     * @access    public
     */
    public function browseAction()
    {

       $this->checkAuthorisation();

		$this->setPageName(_('Browsing glossary'));
		
        if (!isset($_SESSION['admin']['project']['languages'])) {
		
			$this->addError(
				sprintf(
					_('No languages have been defined. You need to define at least one language. Go %shere%s to define project languages.'),
					'<a href="../projects/data.php">','</a>')
				);
		
		} else
        if (!isset($_SESSION['admin']['project']['default_language_id'])) {

			$this->addError(
				sprintf(
					_('No default language has been defined. Go %shere%s to set the default languages.'),
					'<a href="../projects/data.php">','</a>')
				);

		} else {

			if (!$this->rHasVal('letter') && isset($_SESSION['admin']['system']['glossary']['activeLetter']))
				$this->requestData['letter'] = $_SESSION['admin']['system']['glossary']['activeLetter'];
	
			if (!$this->rHasVal('activeLanguage') && isset($_SESSION['admin']['system']['glossary']['activeLanguage']))
				$this->requestData['activeLanguage'] = $_SESSION['admin']['system']['glossary']['activeLanguage'];
	
			$alpha = $this->getActualAlphabet($this->getActiveLanguage());

			if (!$this->rHasVal('letter') || !in_array($this->requestData['letter'],(array)$alpha))
				$this->requestData['letter'] = isset($alpha[0]) ? $alpha[0] : '-';

			if (!$this->rHasVal('activeLanguage'))
				$this->requestData['activeLanguage'] = $_SESSION['admin']['project']['default_language_id'];
				
			if ($this->rHasVal('letter')) {
	
				$gloss = $this->getGlossaryTerms(
					array(
						'term like' => $this->requestData['letter'].'%',
						'language_id' => $this->requestData['activeLanguage']
					),
					'term');
	
			}
	
			$pagination = $this->getPagination($gloss,$this->controllerSettings['termsPerPage']);

			$gloss = $pagination['items'];

			$this->smarty->assign('prevStart', $pagination['prevStart']);
		
			$this->smarty->assign('nextStart', $pagination['nextStart']);
		
			$this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);
	
			$this->smarty->assign('activeLanguage', $this->requestData['activeLanguage']);
	
			$this->smarty->assign('alpha', $alpha);
	
			if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);
	
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

		if ($this->rHasVal('letter')) {

			$gloss = $this->getFirstGlossaryTerm($this->requestData['letter']);

		} else
		if ($this->rHasId()) {

			$gloss = $this->getGlossaryTerm($this->requestData['id']);

		} else
		if (!$this->rHasVal('action','new')) {

			$gloss = $this->getFirstGlossaryTerm();

		}

		if (isset($gloss)) {

			$gloss['media'] = $this->getGlossaryMedia($gloss['id']);
		
			$_SESSION['admin']['system']['glossary']['activeLetter'] = strtolower(substr($gloss['term'],0,1));
			
			$_SESSION['admin']['system']['glossary']['activeLanguage'] = $gloss['language_id'];

    	    $this->setPageName(sprintf(_('Editing glossary term "%s"'),$gloss['term']));

			$navList = $this->getGlossaryTermsNavList();
		
		} else {

    	    $this->setPageName(_('New glossary term'));

		}

		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit()) {

			$_SESSION['admin']['system']['glossary']['activeLetter'] = strtolower(substr($this->requestData['term'],0,1));

			$_SESSION['admin']['system']['glossary']['activeLanguage'] = $this->requestData['language_id'];

			$this->deleteGlossaryTerm($this->requestData['id']);

			$d = $this->getFirstGlossaryTerm();

			$navList = $this->getGlossaryTermsNavList(true);
			
			$this->redirect('edit.php?id='.$d['id']);

			//$this->redirect('browse.php');

		}

		if ($this->rHasVal('activeLanguage')) {

			$activeLanguage = $this->requestData['activeLanguage'];

		} elseif (isset($gloss)) {

			$activeLanguage = $gloss['language_id'];

		} else {

			$activeLanguage = $_SESSION['admin']['project']['default_language_id'];

		}

		if ($this->rHasVal('term') && $this->rHasVal('definition') && !$this->rHasVal('action','browse') && !$this->isFormResubmit()) {

			$data = $this->requestData;

			$data['project_id'] = $this->getCurrentProjectId();

			$data['id'] =  $this->rHasId() ? $this->requestData['id'] : null;

            $data['definition'] = $this->cleanUpRichContent($data['definition']);

			if ($data['id']==null && $this->getGlossaryTerms(array('term' => $data['term'],'language_id' => $data['language_id']))) {

				$this->addError(_('Glossary term already exists.'));

				$gloss = $this->requestData;
				
				$activeLanguage = $this->requestData['language_id'];

			} else
			if ($this->models->Glossary->save($data)) {

				$navList = $this->getGlossaryTermsNavList(true);

				$id = $this->rHasId() ? $this->requestData['id'] : $this->models->Glossary->getNewId();

				$this->models->GlossarySynonym->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'glossary_id' => $id
					)
				);

				if ($this->rHasVal('synonyms')) {

					foreach((array)$this->requestData['synonyms'] as $key => $val) {

						$this->models->GlossarySynonym->save(
							array(
								'project_id' => $this->getCurrentProjectId(),
								'glossary_id' => $id,
								'language_id' => $this->requestData['language_id'],
								'synonym' => rawurldecode($val)
							)
						);

					}

				}
				
				$this->clearTempValues();

				if ($this->rHasVal('action','media')) {

					$this->redirect('media_upload.php?id='.$id);

				} else
				if ($this->rHasVal('action','preview')) {

					$this->redirect('preview.php?id='.$id);

				} else {

					$_SESSION['admin']['system']['glossary']['activeLetter'] = strtolower(substr($this->requestData['term'],0,1));
	
					$_SESSION['admin']['system']['glossary']['activeLanguage'] = $this->requestData['language_id'];
	
					$this->redirect('edit.php?id='.$id);

					//$this->redirect('browse.php');

				}

			} else {

				$this->addError(_('Could not save glossary term.'));

			}

		}

		$alpha = $this->getActualAlphabet($this->getActiveLanguage());

        if (isset($gloss)) $this->smarty->assign('gloss', $gloss);

        if ($_SESSION['admin']['project']['languages']) $this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);

		if (isset($navList)) $this->smarty->assign('navList', $navList);
		if (isset($gloss)) $this->smarty->assign('navCurrentId',$gloss['id']);

		$this->smarty->assign('includeHtmlEditor', true);

		$this->smarty->assign('activeLanguage', $activeLanguage);

		$this->smarty->assign('backUrl', $this->rHasId() ? 'browse.php' : 'index.php');

		$this->smarty->assign('alpha', $alpha);

        $this->printPage();

    }

    /**
     * Upload media for a glossary term
     *
     * @access    public
     */
    public function mediaUploadAction ()
    {

        $this->checkAuthorisation();

        if ($this->rHasId()) {
        // get existing glossary

            $gloss = $this->getGlossaryTerm($this->requestData['id']);
            
            if ($gloss['id']) {

				$this->setBreadcrumbIncludeReferer(
					array(
						'name' => _('Editing glossary term'), 
						'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php?id='.$gloss['id']
					)
				);

                $this->setPageName(sprintf(_('New media for "%s"'),$gloss['term']));

                if ($this->requestDataFiles && !$this->isFormResubmit()) {

                    $filesToSave =  $this->getUploadedMediaFiles();
					
					$firstInsert = false;
    
                    if ($filesToSave) {

                        foreach((array)$filesToSave as $key => $file) {

                            $thumb = false;

                            if (
                                $this->helpers->ImageThumberHelper->canResize($file['mime_type']) &&
                                $this->helpers->ImageThumberHelper->thumbnail($this->getProjectsMediaStorageDir().$file['name'])
                            ) {

                                $pi = pathinfo($file['name']);
                                $this->helpers->ImageThumberHelper->size_width(150);
                                
                                if ($this->helpers->ImageThumberHelper->save(
                                    $this->getProjectsThumbsStorageDir().$pi['filename'].'-thumb.'.$pi['extension']
                                )) {
                                
                                    $thumb = $pi['filename'].'-thumb.'.$pi['extension'];
                                
                                }

                            }

                            $mt = $this->models->GlossaryMedia->save(
                                array(
                                    'id' => null,
                                    'project_id' => $this->getCurrentProjectId(),
                                    'glossary_id' => $this->requestData['id'],
                                    'file_name' => $file['name'],
                                    'original_name' => $file['original_name'],
                                    'mime_type' => $file['mime_type'],
                                    'file_size' => $file['size'],
                                    'thumb_name' => $thumb ? $thumb : null,
                                )
                            );
							
							if (!$firstInsert) {
							
								$firstInsert = array('id'=>$this->models->GlossaryMedia->getNewId(),'name'=>$file['name']);

							}
                
                            if ($mt) {
                                 
                                $this->addMessage(sprintf(_('Saved: %s (%s)'),$file['original_name'],$file['media_name']));
    
                            } else {
    
                                $this->addError(_('Failed writing uploaded file to database.'),1);
    
                            }
                
                        }

						if (isset($_SESSION['admin']['system']['media']['newRef']) && $_SESSION['admin']['system']['media']['newRef'] == '<new>') {
		
							$_SESSION['admin']['system']['media']['newRef'] =
								'<span class="taxonContentMediaLink" onclick="taxonContentOpenMediaLink('.$firstInsert['id'].');">'.
									$firstInsert['name'].
								'</span>';
		
							$this->redirect('edit.php?id='.$gloss['id']);
		
						}

                    }

                }
    
            } else {

                $this->addError(_('Unknown glossary term.'));

            }

            $this->smarty->assign('id',$this->requestData['id']);

            $this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);

            $this->smarty->assign('iniSettings',
                array(
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size')
                )
            );

        } else {

            $this->addError(_('No glossary term specified.'));

        }        

        $this->printPage();
    
    }


    /**
     * Search through all glossary terms
     *
     * @access    public
     */
    public function searchAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName(_('Search for glossary terms'));
		
		if ($this->rHasVal('search')) {

			if (
				isset($_SESSION['admin']['system']['glossary']['search']) && 
				$_SESSION['admin']['system']['glossary']['search']['search'] == $this->requestData['search']) 
			{
			
				$terms = $_SESSION['admin']['system']['glossary']['search']['results'];
			
			} else {

				$synonyms = $this->models->GlossarySynonym->_get(
					array('id' =>
						'select distinct glossary_id
						from %table%
						where
							synonym like "%'.mysql_real_escape_string($this->requestData['search']).'%"
							and project_id = '.$this->getCurrentProjectId()
					)
				);
				
				$b = false;
				
				foreach((array)$synonyms as $key => $val) {

					$b .= $val['glossary_id'].',';

				}
				
				if ($b) $b = '('.rtrim($b,',').')';

				$terms = $this->models->Glossary->_get(
					array('id' =>
						'select *
						from %table%
						where
							(term like "%'.mysql_real_escape_string($this->requestData['search']).'%"
							or definition like "%'.mysql_real_escape_string($this->requestData['search']).'%" '.
							($b ? 'or id in '.$b.') ' : '').
							'and project_id = '.$this->getCurrentProjectId().'
						order by language_id,term'
					)
				);

				foreach((array)$terms as $key => $val) {

					$terms[$key]['language'] = $_SESSION['admin']['project']['languageList'][$val['language_id']]['language'];

				}

				$_SESSION['admin']['system']['glossary']['search']['search'] = $this->requestData['search'];
	
				$_SESSION['admin']['system']['glossary']['search']['results'] = $terms;

			}

		}

        // user requested a sort of the table
        if ($this->rHasVal('key')) {

            $sortBy = array(
                'key' => $this->requestData['key'], 
                'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'), 
                'case' => 'i'
            );

			$this->customSortArray($terms, $sortBy);

        } else {

            $sortBy = array(
                'key' => 'term', 
                'dir' => 'asc', 
                'case' => 'i'
            );
	
		}
        
		$this->smarty->assign('sortBy', $sortBy);

		if (isset($terms)) $this->smarty->assign('terms',$terms);

		if ($this->rHasVal('search')) $this->smarty->assign('search',$this->requestData['search']);

        $this->printPage();

	}


    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
        
        if ($this->requestData['action'] == 'delete_media') {
            
            $this->deleteMedia();

        } else
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        }
		
        $this->printPage();
    
    }


	public function matchTerms($text,$languageId)
	{

		if (empty($text) || empty($languageId)) return;

		$wordlist = $this->getWordList($languageId,true);

		$processed = $text;

		foreach((array)$wordlist as $key => $val) {
		
			$this->_currentGlossatyId = $val['id'];

			$expr = '|\b('.$val['word'].')\b|i';
		
			$processed = preg_replace_callback($expr,array($this,'embedGlossaryLink'),$processed);
		
		}

		return $processed;
	
	}

    public function previewAction ()
    {

		$term = $this->getGlossaryTerm($this->requestData['id']);

		$navList = $this->getGlossaryTermsNavList();

		$this->smarty->assign('backUrl','edit.php?id='.$this->requestData['id']);
		$this->smarty->assign('nextUrl','edit.php?id='.$navList[$this->requestData['id']]['next']['id']);

		if (isset($term)) $this->smarty->assign('term', $term);

		$this->printPreviewPage(
			'../../../../app/templates/templates/glossary/_term',
			'glossary.css'
		);
    
    }

	private function embedGlossaryLink($matches)
	{

		return '<span class="glossary-term-highlight" onclick="glossTextLink('.$this->_currentGlossatyId.')">'.$matches[0].'</span>';

	}

	private function getWordList($languageId,$force=false)
	{

		if ($force || !isset($_SESSION['admin']['system']['glossary'][$languageId]['wordlist'])) {

			$terms = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $languageId
					),
					'columns' => 'id,term as word,\'term\' as source'
				)
			);

			$synonyms = $this->models->GlossarySynonym->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $languageId
					),
					'columns' => 'glossary_id as id,synonym as word,\'synonym\' as source'
				)
			);


			$_SESSION['admin']['system']['glossary'][$languageId]['wordlist'] = array_merge($terms,$synonyms);

		}

		return $_SESSION['admin']['system']['glossary'][$languageId]['wordlist'];

	}


	private function deleteMedia($id=null)
	{
	
		$id = isset($id) ? $id : (isset($this->requestData['id']) ? $this->requestData['id'] : null);
		
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
		
		$_SESSION['admin']['system']['glossary']['alpha'] = $alpha;

		return $alpha;
	
	}


	private function deleteGlossaryTerm($id)
	{

		if (!isset($id)) return;

		$this->models->GlossarySynonym->delete(
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
	
		return $this->models->GlossarySynonym->_get(
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

		$l = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);

		if ($l) {
		
			$term = $l[0];
			
			$term['synonyms'] = $this->getGlossarySynonyms($id);
			$term['media'] = $this->getGlossaryMedia($id);

			return $term;

		} else {
		
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

		$l2 = $this->models->GlossarySynonym->_get(
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
			$this->makeLookupList(
				array_merge((array)$l1,(array)$l2),
				$this->controllerBaseName,
				'../glossary/edit.php?id=%s',
				true
			)
		); // for glossary lookup list
		
		return array_merge((array)$l1,(array)$l2); // for combined lookup list
		
	}

	private function getGlossaryTermsNavList($forceLookup=false) {
	
		if (empty($_SESSION['admin']['glossary']['navList']) || $forceLookup) {
		
			$d = $this->getGlossaryTerms(null);
			
			foreach((array)$d as $key => $val) {

				$res[$val['id']] = array(
					'prev' => array('id' => isset($d[$key-1]['id']) ? $d[$key-1]['id'] : null, 'title' => isset($d[$key-1]['term']) ? $d[$key-1]['term'] : null),
					'next' => array('id' => isset($d[$key+1]['id']) ? $d[$key+1]['id'] : null, 'title' => isset($d[$key+1]['term']) ? $d[$key+1]['term'] : null),
				);

			}
		
			$_SESSION['admin']['glossary']['navList'] = $res;
		
		}
		
		return $_SESSION['admin']['glossary']['navList'];

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

			return $this->requestData['activeLanguage'];

		} elseif(isset($_SESSION['admin']['project']['default_language_id'])) {

			return $_SESSION['admin']['project']['default_language_id'];

		} else {

			return false;
	
		}

	}

	private function clearTempValues()
	{
	
		unset($_SESSION['admin']['system']['glossary']['alpha']);
		unset($_SESSION['admin']['system']['glossary']['search']);
		unset($_SESSION['admin']['system']['glossary']['activeLetter']);
		unset($_SESSION['admin']['system']['glossary']['activeLanguage']);

	}

}