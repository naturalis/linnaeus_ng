<?php

/*

p 			int(11)			YES 		NULL	
i_int 		int(11)			YES 		NULL	
v_varchar 	varchar(255)	YES 		NULL	
t_text 		text			YES 		NULL	
created 	timestamp		NO 		CURRENT_TIMESTAMP	on update CURRENT_TIMESTAMP
	

*/

include_once ('model.php');

class Dump extends Model
{

    const tableBaseName = 'dump';


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

    public function trunc()
    {
		
		mysql_query('truncate '.$this->tableName);

    }

}

