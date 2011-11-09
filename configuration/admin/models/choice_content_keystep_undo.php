<?php

include_once ('model.php');

class ChoiceContentKeystepUndo extends Model
{

    const tableBaseName = 'choices_content_keysteps_undo';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct(self::tableBaseName);

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


    /**
     * Returns name of class/model
     *
     * @access     public
     */
    public function getClassName ()
    {
        
        return get_class();
    
    }

}

