<?php /** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class IntroductionModel extends AbstractModel
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
	
	public function getIntroductionPages( $p )
	{
		$project_id = isset($p['project_id']) ? $p['project_id'] : null;
		$language_id = isset($p['language_id']) ? $p['language_id'] : null;
		$got_content = isset($p['got_content']) ? $p['got_content'] : 1;
		$include_hidden = isset($p['include_hidden']) ? $p['include_hidden'] : false;

		if ( is_null($project_id) || is_null($language_id) ) return;

		$query="
			select
				_b.page_id as id,
				_b.topic,
				_b.content,
				_b.topic as label,
				_a.got_content
			from 
				%PRE%introduction_pages _a
				
			left join %PRE%content_introduction _b
				on _a.project_id = _b.project_id
				and _a.id = _b.page_id
				and _b.language_id = " . $language_id . "

			where 
				_a.project_id = " .$project_id . "
				and _a.got_content = " . ( $got_content ? '1' : '0' ) . "
				" . ( !$include_hidden ? 'and ifnull(_a.hide_from_index,0) != 1' : '' )  . "
			order by
				_a.show_order,_a.created
		";

		return $this->freeQuery($query);
		
	}



}
?>