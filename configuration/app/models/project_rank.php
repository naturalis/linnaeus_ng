<?php

include_once ('model.php');

class ProjectRank extends Model
{
    
    const tableBaseName = 'projects_ranks';



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

