<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class NsrActivityLogModel extends AbstractModel
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

	public function getActivityLog( $params )
	{
		$timezone=isset($params['timezone']) ? $params['timezone'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$search=isset($params['search']) ? $params['search'] : null;
		$limit=isset($params['limit']) ? $params['limit'] : null;
		$offset=isset($params['offset']) ? $params['offset'] : null;
		
		if( !isset( $timezone ) || !isset( $project_id ) )
		{
			return;
		}
		
		$query="
			select
				SQL_CALC_FOUND_ROWS	
				_a.id,
				_a.user_id,
				_a.user,
				_a.controller,
				_a.view,
				_a.data_before,
				_a.data_after,
				_a.note,
				DATE_FORMAT(CONVERT_TZ(_a.created,'UTC','".$timezone."'),'%d %b %Y, %T') as last_change_hr,
				_u.id as user_user_id,
				_u.username as user_username,
				_u.first_name as user_first_name,
				_u.last_name as user_last_name,
				_u.email_address as user_email_address,
				_u.active as user_active,
				CONCAT(
					FLOOR(HOUR(TIMEDIFF(now(), _a.created)) / 24), 'd ',
					MOD(HOUR(TIMEDIFF(now(), _a.created)), 24), 'h ',
					MINUTE(TIMEDIFF(now(), _a.created)), 'm ',
					SECOND(TIMEDIFF(now(), _a.created)), 's'
				) as time_past_hr

			from %PRE%activity_log _a
			
			left join %PRE%users _u
				on _a.user_id=_u.id

			where _a.project_id =".$project_id."
			". (!is_null($search) ? " 
				and (
						_a.user like '%". $this->escapeString($search) ."%' or
						_a.data_before like '%". $this->escapeString($search) ."%' or
						_a.data_after like '%". $this->escapeString($search) ."%' or
						_a.note like '%". $this->escapeString($search) ."%' or
						DATE_FORMAT(_a.created,'%d %b %Y, %T') like '%". $this->escapeString($search) ."%' or 
						concat(_u.first_name,' ',_u.last_name) like '%". $this->escapeString($search) ."%' or 
						_u.email_address like '%". $this->escapeString($search) ."%'
					) " : 
				"") ."
			order by 
				_a.created desc, _a.id desc
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "");
			
			return $this->freeQuery( $query );
			
	}


}
