<?php

include_once ('model.php');

class MatrixName extends Model
{
    
    const tableBaseName = 'matrices_names';



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

