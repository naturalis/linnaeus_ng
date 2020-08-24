<?php

include_once (__DIR__ . "/AbstractModel.php");

final class KeyModel extends AbstractModel
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

    public function setChoiceKeystepTable ($projectId)
    {
		if (!$projectId) {
		    return false;
		}

        $query = "
			select
				res_keystep_id, keystep_id
			from
				%PRE%choices_keysteps
			where
				project_id = ".$projectId."
				and res_keystep_id is not null";

        return $this->freeQuery($query);

    }

    public function getChoicesLeadingToATaxon ($params)
    {
		$project_id = isset($params['projectId']) ? $params['projectId'] : null;
		$language_id = isset($params['languageId']) ? $params['languageId'] : null;
		$nametype_id_preferredname = isset($params['nametype_id_preferredname']) ? $params['nametype_id_preferredname'] : null;

		if ( is_null($project_id) ||  is_null($language_id) ||  is_null($nametype_id_preferredname) )
			return;

        $query = "
			select
				_ck.res_taxon_id,
				_ck.keystep_id,
				_a.taxon,
				_k.name as commonname

			from
				%PRE%choices_keysteps _ck

			left join %PRE%taxa _a
				on _ck.project_id=_a.project_id
				and _ck.res_taxon_id=_a.id

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id = ".$nametype_id_preferredname."
				and _k.language_id=".$language_id."

			where
				_ck.project_id = ".$project_id."
				and _ck.res_taxon_id is not null";

        return $this->freeQuery($query);

    }

    public function getKeystepList( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;

		if ( is_null($project_id) ||  is_null($language_id) )
			return;

        $query = "
			select
				_a.id,
				_a.number,
				_b.title as label
			from
				%PRE%keysteps _a

			left join %PRE%content_keysteps _b
				on _a.id=_b.keystep_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$language_id."

			where
				_a.project_id = ".$project_id."

			order by
				_a.number, _b.title
			";

        return $this->freeQuery( $query );

    }


}
