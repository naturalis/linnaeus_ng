<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

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
