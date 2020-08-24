<?php /** @noinspection PhpMissingParentCallMagicInspection */


include_once ('Controller.php');

class UtilitiesController extends Controller
{

    public $usedModels = array(
		'commonnames',
		'synonyms'
    );
    public $cssToLoad = array();
    public $controllerPublicName = 'Utilities';

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p=null)
    {

        parent::__construct($p);

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
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVar('action')) return;

		if ($this->rHasVar('action', 'translate'))
		{
			$this->smarty->assign('returnText',json_encode($this->javascriptTranslate($this->rGetVal('text'))));

        }
		else
		if ($this->rHasVar('action', 'set_session'))
		{
			$this->setSessionVar($this->rGetVal('var'), $this->rHasVar('val') ? $this->rGetVal('val') : null);
        }
		else
		if ($this->rHasVar('action', 'get_session'))
		{
			$this->getSessionVar($this->rGetVal('var'));
        }

		$this->allowEditPageOverlay = false;

        $this->printPage();

    }

	public function getNamesAction()
	{

		/*

		taxon	[taxon id]	[taxon name]	[taxon rank]
		commonname	[common name id]	[common name]	[language name]	[taxon id]	[taxon name]	[taxon rank]
		synonym	[synonym id]	[synonym]	[remark]	[taxon id]	[taxon name]	[taxon rank]

		*/

		if (!$this->rHasVal('id')) return null;

		$pId = $this->rGetId();

		$ranks = $this->models->ProjectRank->_get(
			array(
				'id' => array(
					'project_id' => $pId,
					'lower_taxon'=> 1
				),
				'columns' => 'id,rank_id',
				'fieldAsIndex' => 'id'
			)
		);

		foreach((array)$ranks as $key => $val) {

			$r = $this->models->Rank->_get(
				array(
					'id' => array('id' => $val['rank_id']),
					'columns' => 'rank'
				)
			);

			$ranks[$key]['rank'] = $r[0]['rank'];

		}

		$a = false;
		foreach((array)$ranks as $key => $val) {

			if (!$a) $a = $val['rank']=='Species' ? true : false;
			if ($a) $d[$key] = $val;

		}

		$ranks = $d;

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,taxon,rank_id',
				'fieldAsIndex' => 'id'
			)
		);

		$languages = $this->models->Language->_get(
			array(
				'id' => '*',
				'columns' => 'language,id',
				'fieldAsIndex' => 'id'
			)
		);


		$common = $this->models->Commonname->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,commonname,language_id,taxon_id'
			)
		);

		$synonyms = $this->models->Synonym->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,synonym,remark,taxon_id'
			)
		);

		$this->smarty->assign('ranks',$ranks);
		$this->smarty->assign('taxa',$taxa);
		$this->smarty->assign('lang',$languages);
		$this->smarty->assign('common',$common);
		$this->smarty->assign('synonyms',$synonyms);

		$this->printPage();

	}

	public function dynamicCssAction()
	{

		$cssVariables = array(
			'projectMedia' => $this->getProjectUrl('projectMedia'),
			'systemMedia' => $this->getProjectUrl('systemMedia')
		);

		foreach ($cssVariables as $k => $v) {
			$this->smarty->assign($k,$v);
		}

		header('Content-type:text/css');

		$this->printPage('dynamic-css');

	}

}
