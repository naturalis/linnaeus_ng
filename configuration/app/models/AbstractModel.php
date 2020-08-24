<?php 

include_once (__DIR__ . "/../BaseClass.php");
include_once (__DIR__ . "/../Db.php");

class AbstractModel extends BaseClass
{
    public $databaseConnection;
    public $tableName;
    public $newId;
	public $doLog = true;

	protected $noKeyViolationLogging = false;
    protected $databaseSettings;
    protected $data;
    protected $_tablePrefix;
    protected $id;
    protected $lastQuery;
	protected $logger;
	protected $_projectId=false;
	protected $_affectedRows = 0;
	protected $_currentWhereArray=null;
    protected $tableExists=true;

    protected $_dataBeforeQuery=true;
    protected $_dataAfterQuery=true;
	protected $error;

    public function __construct ($tableBaseName = false)
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	public function setLogger($logger)
	{
		$this->logger = $logger;
	}

    public function escapeString($d)
    {
        return is_string($d) ? mysqli_real_escape_string($this->databaseConnection, $d) : $d;
    }

    public function save($data)
    {
        if ($this->hasId($data))
		{
            return $this->update($data);
        }
		else
		{
            return $this->insert($data);
        }
    }

    public function getAffectedRows()
    {
        return $this->_affectedRows;
    }

    public function getLastQuery ()
    {
        return $this->lastQuery;
    }

	public function setNoKeyViolationLogging($state)
	{
		if (is_bool($state)) $this->noKeyViolationLogging = $state;
	}

    public function freeQuery($params)
    {
        if (is_array($params))
		{
            $query = isset($params['query']) ? $params['query'] : null;
            $fieldAsIndex = isset($params['fieldAsIndex']) ? $params['fieldAsIndex'] : false;
        }
		else
		{
            $query = isset($params) ? $params : null;
            $fieldAsIndex = false;
        }

        if (empty($query))
		{
            $this->log('Called freeQuery with an empty query',1);
            return;
        };

        $query = str_ireplace('%table%', $this->tableName, $query);
        $query = str_ireplace('%pre%', $this->_tablePrefix, $query);

        $set = mysqli_query($this->databaseConnection, $query);

		if (mysqli_error($this->databaseConnection))
		{
   			$this->setError( mysqli_error($this->databaseConnection) );
		}
		
        $this->logQueryResult($set,$query,'freeQuery');
        $this->setLastQuery($query);
		$this->setAffectedRows();

        unset($this->data);

        while($row=@mysqli_fetch_assoc($set))
		{
            if($fieldAsIndex!==false && isset($row[$fieldAsIndex]))
			{
                $this->data[$row[$fieldAsIndex]]=$row;
            }
			else
			{
                $this->data[]=$row;
            }
        }

        return isset($this->data) ? $this->data : null;

    }

	public function makeWhereString ($p=null,$alias=null)
	{
		if (!is_null($p))
			$this->setCurrentWhereArray($p);

		if (!is_array($this->getCurrentWhereArray()))
			return;

		$d=
			implode(' and ',
				array_map(
					function ($v, $k)
					{
						$d=is_numeric($v)?"%s":"'%s'";
						return sprintf(chr(21)."%s=$d", $k, $v);
					},
					$this->getCurrentWhereArray(),
					array_keys($this->getCurrentWhereArray()
					)
				)
			);

		return str_replace(chr(21),(is_null($alias)?null:$alias.'.'),$d);
	}

	public function getDataDelta()
	{
		$b=$b1=isset($this->_dataBeforeQuery) ? $this->_dataBeforeQuery : null;
		$a=$a1=isset($this->_dataAfterQuery) ? $this->_dataAfterQuery : null;

		if (isset($a1['created'])) unset($a1['created']);
		if (isset($a1['last_change'])) unset($a1['last_change']);
		if (isset($b1['created'])) unset($b1['created']);
		if (isset($b1['last_change'])) unset($b1['last_change']);

		return array(
			'before'=>$b,
			'after'=>$a,
			'changed'=>serialize($b1)!=serialize($a1)
		);
    }

    public function resetAffectedRows()
    {
		$this->_affectedRows = 0;
    }

	/* DEBUG */
    public function q()
    {
        return $this->getLastQuery();
    }

	protected function setCurrentWhereArray($p=null)
	{
		if (is_array($p))
			$this->_currentWhereArray=$p;
	}

	protected function getCurrentWhereArray()
	{
		return $this->_currentWhereArray;
	}

	protected function log($msg,$level=0)
	{
		if (!$this->doLog) return;

		if (method_exists($this->logger,'log'))
		{
			$this->logger->log(
				'('.($this->_projectId ? $this->_projectId : '?').') '.$msg.
			    ' ('.mysqli_errno($this->databaseConnection).': '.
			    mysqli_error($this->databaseConnection).')',
				$level,
				'Model:'.get_class($this)
			);
		}
	}

    protected function setAffectedRows()
    {
		$this->_affectedRows = mysqli_affected_rows($this->databaseConnection);
    }

    protected function isDateTimeFunction ($val)
    {
        try {
            $date = new DateTime($val);
            return false;
        }
        catch (Exception $e) {
            return true;
        }
    }

    protected function setLastQuery ($query)
    {
        $this->lastQuery = $query;
    }

    protected function connectToDatabase ()
    {
		$config = new configuration;
		$settings = $config->getDatabaseSettings();

        Db::createInstance('lngApp', $settings);
        $this->databaseConnection = Db::getInstance('lngApp');

        if (!$this->databaseConnection) {
            die('Error ' . mysqli_connect_errno() . ': failed to connect to database ' .
                $settings['database'] . ' with user ' . $settings['user']);
        }
		return true;
    }

    protected function disconnectFromDatabase ()
    {
        @mysqli_close($this->databaseConnection);
    }

	protected function reEngineerQuery($query)
	{
		$q=null;

        // inserts return an id, not a query
		if (is_int($query))
		{
            $q = 'select * from ' . $this->tableName . ' where id = ' .$query;
        }
		else
        if (stripos($query,'delete')===0)
		{
            $q = str_ireplace('delete from', 'select * from', $query);
        }
        else
		if (stripos($query,'update')===0)
		{
			/* this will fail if there is a string with the substring " where " somewhere in the where-clause*/
            $d = preg_split('/ where /', $query);
            $q = 'select * from ' . $this->tableName . ' where ' . $d[count((array)$d)-1];
        }
		return $q;
	}

    protected function retainDataBeforeQuery($query)
    {
        unset($this->_dataBeforeQuery);

		if (empty($query))
			return;

		$q=$this->reEngineerQuery($query);

        if (isset($q))
		{
            $result = mysqli_query($this->databaseConnection, $q);
            while ($r = @mysqli_fetch_assoc($result))
			{
                $this->_dataBeforeQuery[] = $r;
            }
        }
    }

    protected function retainDataAfterQuery($query,$failed=false)
    {
        unset($this->_dataAfterQuery);

		if ($failed)
			return;

		$q=$this->reEngineerQuery($query);

        if (isset($q))
		{
            $result = mysqli_query($this->databaseConnection, $q);
            while ($r = @mysqli_fetch_assoc($result))
			{
                $this->_dataAfterQuery[] = $r;
            }
        }
    }

	protected function logQueryResult($set,$query,$type,$severity=1)
	{
		if (!$set)
		{
			// 1062 = key violation
			if (mysqli_errno($this->databaseConnection)!=1062 ||
                $this->noKeyViolationLogging!=true) {
                $this->log('Failed query ('.$type.'): '.$query,$severity);
			}
		}
	}

    public function cleanUp($pId,$ms)
    {

        if (empty($pId) || empty($ms) || !is_numeric($ms)) return;

        // ppl probably get confused by milli and micro...
        if (log10($ms)<5) $ms *= 1000; // it were milliseconds!
        if (log10($ms)<5) $ms *= 1000; // no, even worse! it were seconds!

		@$this->execute("delete from %table%  where project_id = ".$pId." and last_change <= TIMESTAMPADD(MICROSECOND ,-".($ms).",CURRENT_TIMESTAMP)");

    }

    protected function getDataType ($n)
    {
        $mysql_data_type_hash = array(
            1=>'tinyint',
            2=>'smallint',
            3=>'int',
            4=>'float',
            5=>'double',
            7=>'timestamp',
            8=>'bigint',
            9=>'mediumint',
            10=>'date',
            11=>'time',
            12=>'datetime',
            13=>'year',
            16=>'bit',
            //252 is currently mapped to all text and blob types (MySQL 5.0.51a)
            253=>'varchar',
            254=>'char',
            246=>'decimal'
        );
        return array_key_exists($n, $mysql_data_type_hash) ? $mysql_data_type_hash[$n] : null;
    }

    public function setLocale ($locale)
    {
		if (!$locale) {
		    return false;
		}

        $this->freeQuery("SET lc_time_names = '". mysqli_real_escape_string($this->databaseConnection, $locale)."'");
    }

    public function getLanguagesUsed ($projectId = null)
    {
        if (is_null($projectId)) {
			return null;
		}

        $query = "
            select count(id) as `count`, language_id
			from %PRE%names
			where project_id=".$projectId."
			group by language_id
			order by `count` asc";

        return $this->freeQuery($query);

    }

    public function arrayHasData ($p = array())
    {
        foreach ($p as $k => $v) {
            if (is_array($v)) {
                $this->arrayHasData($v);
            }
            if ($v != '') {
                return true;
            }
        }
        return false;
    }
	
	public function generateTaxonParentageId( $id )
	{
		return Controller::generateTaxonParentageId( $id );
	}

	public function setError( $error )
	{
		$this->error=$error;
	}

	public function getError()
	{
		return $this->error;
	}

}
