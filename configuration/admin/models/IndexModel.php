<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class IndexModel extends AbstractModel
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


    public function getTaxaLookupList ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $search = isset($params['search']) ? mysqli_real_escape_string($this->databaseConnection, $params['search']) : null;
        $path = isset($params['path']) ? $params['path'] :  null;

        if (is_null($projectId) || is_null($search) || is_null($path)) {
			return null;
		}

        $query = '
			select
				id,
				concat(
					author_first,
					(
						if(multiple_authors=1,
							\' et al.\',
							if(author_second!=\'\',concat(\' & \',author_second),\'\')
						)
					),
					\' (\',
					year(`year`),
					(
						if(isnull(suffix)!=1,
								suffix,
								\'\'
							)
					),
					\')\'
				) as label,
				lower(author_first) as _a1,
				lower(author_second) as _a2,
				`year`,
				"literature" as source,
				concat('.$path.',id) as url
			from %PRE%literature
			where
				(author_first like "%'.$search.'%" or
				author_second like "%'.$search.'%" or
				`year` like "%'.$search.'%")
				and project_id = '.$projectId.'
			order by _a1,_a2,`year`';

		return $this->freeQuery($query);
    }

}

?>