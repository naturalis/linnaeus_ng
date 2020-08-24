<?php /** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class HotwordsModel extends AbstractModel
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


    public function getLiteratureHotwords( $params )
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;

        if ( is_null($project_id) ) return;

        $query = "
            select
				_a.id,
				concat(trim(_a.author),', ',trim(_a.date)) as author1,
				concat(trim(_a.author),' (',trim(_a.date),')') as author2,
				trim(_a.date) as `date`

			from %PRE%literature2 _a

			left join %PRE%actors _b
				on _a.actor_id = _b.id
				and _a.project_id=_b.project_id

			where
				_a.project_id = ".$project_id."
			";
			
		$d=$this->freeQuery($query);
		$result=array();
		
		foreach((array)$d as $val)
		{
			if ( !empty($val['author1']) )
				$result[md5($val['id'].$val['author1'])]=[ 'id'=>$val['id'],'author'=>$val['author1'] ];

			if ( !empty($val['author2']) )
				$result[md5($val['id'].$val['author2'])]=[ 'id'=>$val['id'],'author'=>$val['author2'] ];

			$l=$this->getReferenceAuthors( [ 'projectId'=>$project_id, 'literatureId'=>$val['id'] ] );

			$dummy=null;
			foreach((array)$l as $key2=>$val2)
			{
				$dummy .= ($key2>0 ? ($key2==count((array)$l)-1 ? " & " : ", " ) : "" ) . $val2['name'];
			}

			if ( !empty($val['dummy']) )
			{
				$result[md5($dummy . ' (' . $val['date']. ')')]=[ 'id'=>$val['id'],'author'=>$dummy . ' (' . $val['date']. ')' ];
				$result[md5($dummy . ', ' . $val['date'])]=[ 'id'=>$val['id'],'author'=>$dummy . ', ' . $val['date'] ];
			}
		}
		
		return $result;
    }

    public function getReferenceAuthors($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($literatureId)) {
			return null;
		}

        $query = "
            select
				_b.name

			from %PRE%literature2_authors _a

			left join %PRE%actors _b
				on _a.actor_id = _b.id
				and _a.project_id=_b.project_id

			where
				_a.project_id = ".$projectId."
				and _a.literature2_id =".$literatureId."

			order by _a.sort_order,_b.name";

        return $this->freeQuery($query);
    }


}