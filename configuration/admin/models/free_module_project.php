<?php

include_once ('model.php');

class FreeModuleProject extends Model
{
    
    const tableBaseName = 'free_modules_projects';



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

