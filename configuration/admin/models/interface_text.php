<?php

include_once ('model.php');

class InterfaceText extends Model
{

    const tableBaseName = 'interface_texts';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct(self::tableBaseName);

		$this->doLog = false; // lots of duplicate entry errors; lazy me

    }

    /**
     * Destructor
     *
     * @access     public
     */
    public function __destruct ()
    {

        parent::__destruct();

    }

}

