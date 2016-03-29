<?php

include_once ('MediaController.php');

class MediaConverterController extends MediaController
{

    public $modelNameOverride = 'MediaModel';

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

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize ()
    {
    }



}
