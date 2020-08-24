<?php 
include_once (__DIR__ . "/AbstractModel.php");

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


    public function getStateName ($params)
    {
        $projectId = isset($params['project_id']) ? $params['project_id'] : null;
        $languageId = isset($params['language_id']) ? $params['language_id'] : null;
        $stateId = isset($params['state_id']) ? $params['state_id'] : null;

        if (is_null($stateId) || is_null($projectId) || is_null($languageId)) {
			return null;
		}

		$query = "
			select
                t2.`label` as state_label,
                t3.`label` as characteristic_label
            from
                `characteristics_states` as t1
            left join
                `characteristics_labels_states` as t2 on t1.`id` = t2.`state_id`
            left join
                `characteristics_labels` as t3 on t1.`characteristic_id` = t3.`characteristic_id`
            where
                t1.`id` = $stateId and
                t1.`project_id` = $projectId and
                t2.`language_id` = $languageId";

		$r = $this->freeQuery($query);

		return $r[0];
	}



}

?>