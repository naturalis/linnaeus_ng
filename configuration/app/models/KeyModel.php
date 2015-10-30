<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

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


/*"
			select
				_ck.res_taxon_id,
				_ck.keystep_id,
				_a.taxon,
				_c.commonname

			from
				%PRE%choices_keysteps _ck

			left join %PRE%taxa _a
				on _ck.project_id=_a.project_id
				and _ck.res_taxon_id=_a.id

			left join %PRE%commonnames _c
				on _ck.project_id=_c.project_id
				and _c.id=
					(select
						id
					from
						%PRE%commonnames
					where
						project_id=_ck.project_id
						and taxon_id=_ck.res_taxon_id
						and language_id = ". $this->getCurrentLanguageId() ."
						limit 1
					)
			where
				_ck.project_id = ".$this->getCurrentProjectId()."
				and _ck.res_taxon_id is not null
			"
			*/

    public function getChoicesLeadingToATaxon ($projectId)
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



}
?>