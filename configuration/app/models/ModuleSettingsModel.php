<?php 
include_once (__DIR__ . "/AbstractModel.php");

final class ModuleSettingsModel extends AbstractModel
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


	public function setModules ()
    {
        return $this->freeQuery("
		    select
				_a.id,
				_a.module,
				_a.controller,
				count(_b.id) as num_of_settings
			from
				%PRE%modules _a

			left join %PRE%module_settings _b
				on _a.id=_b.module_id

			group by
				_a.controller

			order by
				_a.module"
        );
	}


	public function setModuleSettingValues ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $moduleId = isset($params['moduleId']) ? $params['moduleId'] : null;

        if (is_null($moduleId) || is_null($projectId)) {
			return null;
		}

		$query = "
			select
				_b.id as setting_id,
				_a.id as value_id,
				_a.value as value,
				_b.default_value as default_value

			from
				%PRE%module_settings_values _a

			left join
				%PRE%module_settings _b
				on _b.id=_a.setting_id

			where
				_a.project_id = " . $projectId . "
				and _b.module_id = " . $moduleId;

        return $this->freeQuery($query);
	}


	public function setModuleReaderSettingValues ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $moduleId = isset($params['moduleId']) ? $params['moduleId'] : null;
        $setting = isset($params['setting']) ? $params['setting'] : null;

        if (is_null($moduleId) || is_null($projectId)) {
			return null;
		}

		$query = "
			select
				_a.value as value,
				_b.setting,
				_b.default_value as default_value

			from
				%PRE%module_settings _b

			left join
				%PRE%module_settings_values _a
				on _b.id=_a.setting_id
				and _a.project_id = " . $projectId . "

			where
				_b.module_id = " . $moduleId;

		 if (!is_null($setting)) {
			$query .= "	and _b.setting = '" . $setting . "'";
         }

        return $this->freeQuery($query);
	}



}

?>