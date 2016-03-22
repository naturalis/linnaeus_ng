<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

class ModuleIdentifierModel extends AbstractModel
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


	public function getChoiceName ($params)
    {
        $projectId = isset($params['project_id']) ? $params['project_id'] : null;
        $choiceId = isset($params['choice_id']) ? $params['choice_id'] : null;

        if (is_null($choiceId) || is_null($projectId)) {
			return null;
		}

		$query = "
			select
                t1.`show_order` as choice_number, t2.`number` as keystep_number
            from
                `choices_keysteps` as t1
            left join
                `keysteps` as t2 on t1.`keystep_id` = t2.`id`
            where
                t1.`project_id` = $projectId and
                t1.`id` = $choiceId";

        $r = $this->freeQuery($query);
		return $r[0];
	}





}

?>