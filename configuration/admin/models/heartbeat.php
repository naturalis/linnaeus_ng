<?php

include_once ('model.php');

class Heartbeat extends Model
{

    const tableBaseName = 'heartbeats';


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

    
    public function cleanUp($pId,$ms)
    {

        if (empty($pId) || empty($ms) || !is_numeric($ms)) return;

        // ppl probably get confused by milli and micro... 
        if (log10($ms)<5) $ms *= 1000; // it were milliseconds!
        if (log10($ms)<5) $ms *= 1000; // no, even worse! it were seconds!
        
		@$this->execute("delete from %table%  where project_id = ".$pId." and last_change <= TIMESTAMPADD(MICROSECOND ,-".($ms).",CURRENT_TIMESTAMP)");

    }
    
}

