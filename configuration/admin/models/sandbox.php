<?php

/* DEBUG */

include_once ('model.php');

class Sandbox extends Model
{
    
    const tableBaseName = 'sandbox';



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

}

