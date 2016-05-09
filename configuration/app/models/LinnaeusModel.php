<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class LinnaeusModel extends AbstractModel
{

    public function __construct ()
    {

        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

     }

    public function __destruct ()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

	public function getIntroductionTopicId( $params )
    {

		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$topic = isset($params['topic']) ? $params['topic'] : null;

		if ( is_null($project_id) || is_null($topic) ) return;

		$query = "
			select

				*

			from %PRE%introduction_pages _a

			right join %PRE%content_introduction _c
				on _a.id = _c.page_id
				and _a.project_id=_c.project_id
				and _c.topic = '" .  mysqli_real_escape_string($this->databaseConnection, $topic) . "' 
				
			where
				_a.project_id = ".$project_id . "
			limit 1
		";	
		
		$d=$this->freeQuery( $query );
		
		return $d ? $d[0]['id'] : null;
	}

	public function getFirstIntroductionTopicId( $params )
    {

		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) ) return;

		$query = "
			select
				*
			from
				%PRE%introduction_pages _a

			where
				_a.project_id = ".$project_id ."
				and _a.got_content = 1
			order by
				_a.show_order
			limit 1
		";
		
		$d=$this->freeQuery( $query );
		
		return $d ? $d[0]['id'] : null;
	}

}
