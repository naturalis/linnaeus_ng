<?php
include_once (__DIR__ . "/AbstractModel.php");

final class GlossaryModel extends AbstractModel
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

    public function getSynonyms ($params)
    {
		if (!$params) {
		    return false;
		}

		$search = isset($params['search']) ? $params['search'] : false;
		$projectId = isset($params['projectId']) ? $params['projectId'] : false;

        $query = 'select distinct glossary_id from %PRE%glossary_synonyms
			 where synonym like "%' . mysqli_real_escape_string($this->databaseConnection, $search) . '%"
			 and project_id = ' . $projectId;

        return $this->freeQuery($query);
    }

    public function getTerms ($params)
    {
		if (!$params) {
		    return false;
		}

		$search = isset($params['search']) ? $params['search'] : false;
		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$synonymsIds = isset($params['synonymsIds']) ? $params['synonymsIds'] : false;

		$b = false;
		foreach((array)$synonymsIds as $key => $val) {
			$b .= $val['glossary_id'].',';
		}
		if ($b) $b = '('.rtrim($b,',').')';

        $query = 'select * from  %PRE%glossary where
			(term like "%' . mysqli_real_escape_string($this->databaseConnection, $search) . '%"
			or definition like "%' . mysqli_real_escape_string($this->databaseConnection, $search) . '%" '.
			($b ? 'or id in '. $b .') ' : '').
			'and project_id = ' . $projectId . '
		    order by language_id,term';

        return $this->freeQuery($query);
    }

}

?>