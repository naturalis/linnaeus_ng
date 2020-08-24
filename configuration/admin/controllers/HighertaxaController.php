<?php /** @noinspection PhpMissingParentCallMagicInspection */

include_once ('Controller.php');

class HighertaxaController extends Controller
{

    public $controllerPublicName = 'Higher taxa';

    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    public function indexAction ()
    {
		$this->redirect('../species/index_ht.php');
    }

    public function editAction ()
    {
		$this->redirect('../species/edit.php?id='.$this->rGetId());
    }

}

