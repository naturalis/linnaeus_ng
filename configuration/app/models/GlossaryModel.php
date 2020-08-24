<?php
/** @noinspection PhpMissingParentCallMagicInspection */
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

    public function getGlossarySynonyms($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$id = isset($params['id']) ? $params['id'] : null;


		if ( is_null($project_id) ||  is_null($language_id) ||  is_null($id) )
			return;

		$query = "
			select 
				_a.id,_a.synonym,_a.language_id,_b.label as language
			from 
				%PRE%glossary_synonyms _a
			
			left join %PRE%labels_languages _b
				on _a.project_id = _b.project_id
				and _b.language_id = " . $language_id . "
				and _a.language_id = _b.label_language_id

			where 
				_a.project_id = " . $project_id . "
				and _a.glossary_id = " . $id  . "

			order by _a.synonym
			";

		return $this->freeQuery($query);

	}

    public function getGlossaryMedia($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$id = isset($params['id']) ? $params['id'] : null;

		if ( is_null($project_id) ||  is_null($language_id) ||  is_null($id) )
			return;

		$query = "select 
				_a.file_name,
				_a.thumb_name,
				_a.original_name,
				_a.id,
				_a.fullname,
				_a.mime_type,
				substring(_a.mime_type,1,locate('/',_a.mime_type)-1) as mime,
				_b.caption,
				_b.caption as alt
			from 
				glossary_media _a
			
			left join glossary_media_captions _b
				on _b.project_id = _a.project_id
				and _b.language_id = " . $language_id . "
				and _b.media_id = _a.id
			
			where 
				_a.project_id = " . $project_id . " 
				and _a.glossary_id = " . $id . " 
			
			order by mime";

		return $this->freeQuery($query);

	}

}
