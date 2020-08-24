<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class TraitsTraitsModel extends AbstractModel
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

	public function getDatatypes($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) )
			return;

		$query="
			select
				_a.*,
				_d.translation as name,
				_e.translation as description,
				_b.id as project_type_id
			from
				%PRE%traits_types _a

			left join 
				%PRE%text_translations _d
				on _a.name_tid=_d.text_id
				and _d.language_id=" . $language_id . "
				and _d.project_id is null

			left join 
				%PRE%text_translations _e
				on _a.description_tid=_e.text_id
				and _e.language_id=" . $language_id . "
				and _e.project_id is null
				
			left join 
				%PRE%traits_project_types _b
				on _a.id=_b.type_id
				and _b.project_id=" . $project_id . "
			order by _a.id
		";

		return $this->freeQuery($query);
	}

	public function getProjectDatatypes($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) )
			return;
		
		$query = "
			select
				_a.*,
				_d.translation as name,
				_e.translation as description,
				_b.id as project_type_id
			from
				%PRE%traits_types _a

			left join 
				%PRE%text_translations _d
				on _a.name_tid=_d.id
				and _d.language_id=" . $language_id . "
				and _d.project_id is null

			left join 
				%PRE%text_translations _e
				on _a.description_tid=_e.id
				and _e.language_id=" . $language_id . "
				and _e.project_id is null
				
			left join 
				%PRE%traits_project_types _b
				on _a.id=_b.type_id
				and _b.project_id=" . $project_id. "
			
			where
				_b.id is not null
		";
		
		return $this->freeQuery($query);
	}

	public function getProjectDatatype($params)
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$type_id=isset($params['type_id']) ? $params['type_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id)  || is_null($type_id) )
			return;
		
		$query = "
			select
				_a.*,
				_d.translation as name,
				_e.translation as description,
				_b.id as project_type_id
			from
				%PRE%traits_types _a

			left join 
				%PRE%text_translations _d
				on _a.name_tid=_d.id
				and _d.language_id=" . $language_id . "
				and _d.project_id is null

			left join 
				%PRE%text_translations _e
				on _a.description_tid=_e.id
				and _e.language_id=" . $language_id . "
				and _e.project_id is null
				
			left join 
				%PRE%traits_project_types _b
				on _a.id=_b.type_id
				and _b.project_id=" . $project_id. "
			where _b.id =".$type_id."
		";
		
		$t=$this->freeQuery($query);
		return isset($t[0]) ? $t[0] : null;
	}

}
