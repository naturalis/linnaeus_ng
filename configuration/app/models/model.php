<?php

include_once (dirname(__FILE__) . "/../BaseClass.php");

abstract class Model extends BaseClass
{
    public $databaseConnection;
    public $tableName;
    public $newId;
	public $doLog = true;

	private $noKeyViolationLogging = false;
    private $databaseSettings;
    private $data;
    private $_tablePrefix;
    private $id;
    private $lastQuery;
	private $logger;
	private $_projectId=false;
	private $_affectedRows = 0;
	private $_currentWhereArray=null;
    private $tableExists=true;

    private $_dataBeforeQuery=true;
    private $_dataAfterQuery=true;

    public function __construct ($tableBaseName = false)
    {
        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.$this->databaseSettings['database'].
        	' with user '.$this->databaseSettings['user'] . '. ' . mysql_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

        if (!$tableBaseName)
		{
            die(_('FATAL: no table basename defined'));
        }
        else
		{
            $this->_tablePrefix = $this->databaseSettings['tablePrefix'];
            $this->tableName = $this->_tablePrefix . $tableBaseName;
        }

        $this->getTableColumnInfo();
    }

    public function __destruct ()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

    public function setTableExists($state)
	{
		$this->tableExists=$state;
	}

    public function getTableExists()
	{
		return $this->tableExists;
	}

	public function setLogger($logger)
	{
		$this->logger = $logger;
	}

	public function getTablePrefix()
	{
		return $this->_tablePrefix;
	}

    public function escapeString($d)
    {
        return is_string($d) ? mysql_real_escape_string($d) : $d;
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

    public function insert ($data)
    {
        foreach ((array) $data as $key => $val)
		{
			if (!is_array($val) && !(substr($val,0,1)=='#')) $data[$key] = $this->escapeString($val);
        }

        $fields = null;
        $values = null;

        foreach ((array) $data as $key => $val)
		{
            if (is_array($val))
                continue;

            if (empty($this->columns[$key]))
                continue;

            $d = $this->columns[$key];

			if ($key=='project_id')
			{
				$this->_projectId = $val;
			}

            if ($d && (!empty($val) || $val===0 || $val==='0') && $val!='null')
			{
                $fields .= "`".$key ."`, ";

				// # at beginning of value is signal to take the value literal
                if (substr($val,0,1)=='#')
				{
					$values .= substr($val,1) . ", ";
				}
				else
				if ($d['type'] == 'date' || $d['type'] == 'datetime' || $d['type'] == 'timestamp')
				{
                    if ($this->isDateTimeFunction($val)) {
                        $values .= $val . ", ";
                    }
                    else
					{
                        $values .= "'" . $val . "', ";
                    }
                }
                else
				if ($d['numeric']==1)
				{
                    $values .= $val . ", ";
                }
                else
				{
                    $values .= "'" . $val . "', ";
                }
            }
        }

        if (array_key_exists('created', $this->columns) && !array_key_exists('created', $data))
		{
            $fields .= 'created,';
            $values .= 'CURRENT_TIMESTAMP,';
        }

        $query = "insert into " . $this->tableName . " (" . trim($fields, ', ') . ") values (" . trim($values, ', ') . ")";

        $this->retainDataBeforeQuery(null);
        $this->setLastQuery($query);

        if (!mysql_query($query))
		{
			$this->logQueryResult(false,$query,'ins');
            return mysql_error($this->databaseConnection);
        }
        else
		{
			$this->setAffectedRows();
            $this->newId = mysql_insert_id($this->databaseConnection);
			$this->retainDataAfterQuery($this->newId);
            return true;
        }
    }

    public function update ($data, $where = false)
    {
        foreach ((array) $data as $key => $val)
		{
			if (!is_array($val) && !(substr($val,0,1)=='#')) $data[$key] = $this->escapeString($val);
        }

        $query = "update " . $this->tableName . " set ";

        foreach ((array) $data as $key => $val)
		{
            if (!isset($this->columns[$key]))
                continue;

            $d = $this->columns[$key];
			if ($key=='project_id')
			{
				$this->_projectId = $val;
			}

			// # at beginning of value is signal to take the value literal
			if (substr($val,0,1)=='#')
			{
				$query .= " `" . $key . "` = " . substr($val,1) . ", ";
			}
			else
			if ($d && (!empty($val) || $val===0 || $val==='0'))
			{
                if ($d['numeric'] == 1)
				{
                    $query .= " `" . $key . "` = " . $val . ", ";
                }
                else
				if ($d['type'] == 'datetime')
				{
                    $query .= " `" . $key . "` = " . $val . ", ";
                }
                else
				{
                    $query .= " `" . $key . "` = ".($val=='null' ? 'null' : "'" . $val . "'").", ";
                }
            }
        }

        // this might seem odd as all the last_change columns are defined with 'ON UPDATE CURRENT_TIMESTAMP'
        // occasionally, it is necessary to update only the last_change column, as with the heartbeats table
        if (array_key_exists('last_change', $this->columns) && array_key_exists('last_change', $data))
		{
            $query .= 'last_change = CURRENT_TIMESTAMP,';
        }

        $query = rtrim($query, ', ');

        if (!$where)
		{
            $query .= " where id = " . $data['id'];
        }
        else
		if (is_array($where))
		{
            $query .= " where id = id ";
            foreach ((array) $where as $col => $val)
			{
                if (strpos($col, ' ') === false)
				{
                    $operator = '=';
                }
                else
				{
                    $operator = trim(substr($col, strpos($col, ' ')));
                    $col = trim(substr($col, 0, strpos($col, ' ')));
                }

	            $d = $this->columns[$col];

                if ($d['numeric'] == 1)
				{
	                $query .= ' and `' . $col . "` " . $operator . " " . $this->escapeString($val);
				}
				else
				{
	                $query .= ' and `' . $col . "` " . $operator . " '" . $this->escapeString($val) . "'";
    			}
            }
        }

        $this->retainDataBeforeQuery($query);
        $this->setLastQuery($query);

        if (!mysql_query($query))
		{
			$this->logQueryResult(false,$query,'upd');
			$this->retainDataAfterQuery($query,true);
            return mysql_error($this->databaseConnection);
        }
        else
		{
			$this->setAffectedRows();
			$this->retainDataAfterQuery($query);
            return true;
        }
    }

    public function delete ($id = false)
    {
        if (!$id)
            return;

        if (is_array($id))
		{

            $query = 'delete from ' . $this->tableName . ' where 1=1 ';

            foreach ((array) $id as $col => $val)
			{
                if (strpos($col,' ')===false)
				{
                    $operator = '=';
                }
				else
				{
                    $operator = trim(substr($col, strpos($col,' ')));
                    $col = trim(substr($col, 0, strpos($col,' ')));
                }

				if ($col=='project_id')
				{
					$this->_projectId = $val;
				}

				// operator ending with # signals to use val literally (for queries like: "mean = (23 + (sd * 2))"
                if (substr($operator,-1)=='#')
				{
                    $query .= " and `" . $col . "` " . substr($operator,0,-1) . " " . $val;
                }
				else
				{
	                $query .= " and `" . $col . "` " . $operator . " '" . $this->escapeString($val) . "'";
				}
            }
        } else
		if (is_numeric($id))
		{
            $query = 'delete from ' . $this->tableName . ' where id = ' . ($id ? $id : $this->id) . ' limit 1';
        }
		else
		if (is_string($id))
		{
            $query = str_replace('%table%', $this->tableName, $id);
        }
		else
		{
            return;
        }

        $this->retainDataBeforeQuery($query);
        $this->setLastQuery($query);
        $result = mysql_query($query);

        if (!$result)
		{
			$this->logQueryResult(false,$query,'del');
			$this->retainDataAfterQuery($query,true);
            return mysql_error($this->databaseConnection);
        }
        else
		{
			$this->setAffectedRows();
			$this->retainDataAfterQuery($query);
            return true;
        }
    }

    public function _get($params=false)
    {
		if (!$params) return false;

		$id = isset($params['id']) ? $params['id'] : false;
		$columns = isset($params['columns']) ? $params['columns'] : false;
		$order = isset($params['order']) ? $params['order'] : false;
		$group = isset($params['group']) ? $params['group'] : false;
		$limit = isset($params['limit']) ? $params['limit'] : false;
		$ignoreCase = isset($params['ignoreCase']) ? $params['ignoreCase'] : true;
		$fieldAsIndex = isset($params['fieldAsIndex']) ? $params['fieldAsIndex'] : false;
		$where = isset($params['where']) ? $params['where'] : false;

		$this->setCurrentWhereArray($id);

        unset($this->data);

        $this->set(
			array(
				'id' => ($id ? $id : $this->id),
				'columns' => $columns,
				'order' => $order,
				'group' => $group,
				'ignoreCase' => $ignoreCase,
				'fieldAsIndex' => $fieldAsIndex,
				'limit' => $limit,
				'where' => $where
			)
		);

        return isset($this->data) ? $this->data : null;

    }

    public function getNewId ()
    {
        return $this->newId;
    }

    public function getAffectedRows()
    {
        return $this->_affectedRows;
    }

    public function getLastQuery ()
    {
        return $this->lastQuery;
    }

    public function execute($query)
    {
		$query = str_replace('%table%', $this->tableName, $query);
		$this->retainDataBeforeQuery($query);
		$this->setLastQuery($query);

		$result = mysql_query($query);

		if (!$result)
		{
			$this->logQueryResult(false,$query,'exec');
			$this->retainDataAfterQuery($query,true);
			return mysql_error($this->databaseConnection);
		}
		else
		{
			$this->setAffectedRows();
			$this->retainDataAfterQuery($query);
			return true;
		}
    }

	public function setNoKeyViolationLogging($state)
	{
		if (is_bool($state)) $this->noKeyViolationLogging = $state;
	}


	public function getTableName()
	{
		return $this->tableName;
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

        $set = mysql_query($query);

        $this->logQueryResult($set,$query,'freeQuery');
        $this->setLastQuery($query);
		$this->setAffectedRows();

        unset($this->data);

        while($row=@mysql_fetch_assoc($set))
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


	/* DEBUG */
    public function q()
    {
        return $this->getLastQuery();
    }

	private function setCurrentWhereArray($p=null)
	{
		if (is_array($p))
			$this->_currentWhereArray=$p;
	}

	private function getCurrentWhereArray()
	{
		return $this->_currentWhereArray;
	}

	private function log($msg,$level=0)
	{
		if (!$this->doLog) return;

		if (method_exists($this->logger,'log'))
		{
			$this->logger->log(
				'('.($this->_projectId ? $this->_projectId : '?').') '.$msg.' ('.mysql_errno().': '.mysql_error().')',
				$level,
				'Model:'.get_class($this)
			);
		}
	}

    private function setAffectedRows()
    {
		$this->_affectedRows = mysql_affected_rows($this->databaseConnection);
    }

    private function isDateTimeFunction ($val)
    {
        try {
            $date = new DateTime($val);
            return false;
        }
        catch (Exception $e) {
            return true;
        }
    }

    private function setLastQuery ($query)
    {
        $this->lastQuery = $query;
    }

    private function connectToDatabase ()
    {
        $this->databaseSettings = $this->config->getDatabaseSettings();
        $this->databaseConnection = @mysql_connect($this->databaseSettings['host'], $this->databaseSettings['user'], $this->databaseSettings['password']);
        if (!$this->databaseConnection)
		{
			$this->log('Failed to connect to database '.$this->databaseSettings['host'].' with user '.$this->databaseSettings['user'],2);
			return false;
		}

        mysql_select_db($this->databaseSettings['database'], $this->databaseConnection) or $this->log('Failed to select database '.$this->databaseSettings['database'],2);

        if ($this->databaseSettings['characterSet'])
		{
            mysql_query('SET NAMES ' . $this->databaseSettings['characterSet'], $this->databaseConnection);
            mysql_query('SET CHARACTER SET ' . $this->databaseSettings['characterSet'], $this->databaseConnection);
        }
        return true;
    }

    private function disconnectFromDatabase ()
    {
        @mysql_close($this->databaseConnection);
    }

    private function getTableColumnInfo ()
    {
		$query = 'select * from ' . $this->tableName . ' limit 1';
        $r = mysql_query($query);
		$this->logQueryResult($r,$query,'table col info');

		if (!$r)
		{
			$this->setTableExists(false);
			return;
		}

        $i = 0;

        while ($i < mysql_num_fields($r))
		{
            $info = mysql_fetch_field($r, $i);
            if ($info)
			{
                $this->columns[$info->name] = array(
                    'blob' => $info->blob,
                    'max_length' => $info->max_length,
                    'multiple_key' => $info->multiple_key,
                    'name' => $info->name,
                    'not_null' => $info->not_null,
                    'numeric' => $info->numeric,
                    'primary_key' => $info->primary_key,
                    'table' => $info->table,
                    'type' => $info->type,
                    'unique_key' => $info->unique_key,
                    'unsigned' => $info->unsigned,
                    'zerofill' => $info->zerofill
                );
                $i++;
            }
        }
    }

    private function hasId($data)
    {
        foreach ((array) $data as $col => $val)
		{
            if ($col == 'id' && $val != null)
			{
                $this->id = $val;
                return true;
            }
        }
        return false;
    }

	private function reEngineerQuery($query)
	{
		$q=null;

        // inserts return an id, not a query
		if (is_integer($query))
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

    private function retainDataBeforeQuery($query)
    {
        unset($this->_dataBeforeQuery);

		if (empty($query))
			return;

		$q=$this->reEngineerQuery($query);

        if (isset($q))
		{
            $result = mysql_query($q);
            while ($r = @mysql_fetch_assoc($result))
			{
                $this->_dataBeforeQuery[] = $r;
            }
        }
    }

    private function retainDataAfterQuery($query,$failed=false)
    {
        unset($this->_dataAfterQuery);

		if ($failed)
			return;

		$q=$this->reEngineerQuery($query);

        if (isset($q))
		{
            $result = mysql_query($q);
            while ($r = @mysql_fetch_assoc($result))
			{
                $this->_dataAfterQuery[] = $r;
            }
        }
    }

    private function set($params)
    {
        /*

			function can take as $id:
				- a single $id to find the corresponding row
				- an array of column/value-pairs (array('last_name' => 'turing' ))
				  standard operator is '=' but it is possible to tag another operator
				  after the column-value (array('last_name !=' => 'gates' ))
				- a full query with %table% as tablename
				- * for no where clause
			$cols can hold a string that replaces the defualt * in 'select * from...'

		*/

		$id = isset($params['id']) ? $params['id'] : false;
		$cols = isset($params['columns']) ? $params['columns'] : false;
		$order = isset($params['order']) ? $params['order'] : false;
		$group = isset($params['group']) ? $params['group'] : false;
		$limit = isset($params['limit']) ? $params['limit'] : false;
		$ignoreCase = isset($params['ignoreCase']) ? $params['ignoreCase'] : true;
		$fieldAsIndex = isset($params['fieldAsIndex']) ? $params['fieldAsIndex'] : false;
		$where = isset($params['where']) ? $params['where'] : false;

		$this->setCurrentWhereArray($id);

        $query = false;

		if ($fieldAsIndex!=false && $cols!=false && $cols!='*' && stripos(','.$cols.',',','.$fieldAsIndex.',')===false)
			$cols .= ','.$fieldAsIndex;

        if (!$id && !$where) return;

        if (is_array($id)) {

            $query = 'select ' . (!$cols ? '*' : $cols) . ' from ' . $this->tableName . ' where 1=1 ';

            foreach ((array) $id as $col => $val) {

				$colLiteral	= false;

                if (strpos($col, ' ') === false) {

                    $operator = '=';

                } else {

                    $operator = trim(substr($col, strpos($col, ' ')));

                    $col = trim(substr($col, 0, strpos($col, ' ')));

                }

				if ($col=='project_id') {

					$this->_projectId = $val;

				}

				if (isset($this->columns[$col])) {

	            	$d = $this->columns[$col];

				} else
				if (strtolower(trim($col))=='%literal%') {

	            	$colLiteral = true;

				}
				else {

					continue;

				}

				if ($colLiteral) {

                    $query .= " and " . $val;

				} else
				// operator ending with # signals to use val literally (for queries like: "mean = (23 + (sd * 2))"
                if (substr($operator,-1) == '#') {

                    $query .= " and `" . $col . "` " . substr($operator,0,-1) . " " . $val;

                } elseif ($val===null) {

                    $query .= " and `" . $col . "` " . $operator . " null ";

                } elseif ($operator == 'like') {

                    $query .= " and `" . $col . "` " . $operator . " '" . mb_strtolower($val,'UTF-8')."'";

                } elseif ($d['numeric'] == 1) {

                    $query .= " and `" . $col . "` " . $operator . " " . $this->escapeString(mb_strtolower($val,'UTF-8'));

                } elseif ($d['type'] == 'datetime') {

                    $query .= " and `" . $col . "` " . $operator . " '" . $this->escapeString(mb_strtolower($val,'UTF-8'))."'";

                } elseif ($ignoreCase && is_string($val)) {

                    $query .= " and lower(`" . $col . "`) " . $operator . " '" . $this->escapeString(mb_strtolower($val,'UTF-8')) . "'";

                } else {

                    $query .= " and `" . $col . "` " . $operator . " '" . $this->escapeString($val) . "'";

                }

            }

            $query .= $group ? " group by " . $group : '';

            $query .= $order ? " order by " . $order : '';

            $query .= $limit ? " limit " . $limit : '';

            $this->setLastQuery($query);

            $set = mysql_query($query);

			$this->logQueryResult($set,$query,'set,normal');

            $this->setLastQuery($query);

            while ($row = @mysql_fetch_assoc($set)) {

				if ($fieldAsIndex!==false && isset($row[$fieldAsIndex])) {

	                $this->data[$row[$fieldAsIndex]] = $row;

				} else {

	                $this->data[] = $row;

            	}

            }

        } elseif ($id=='*') {

            $query = 'select ' . (!$cols ? '*' : $cols) . ' from ' . $this->tableName;

            $query .= $group ? " group by " . $group : '';

            $query .= $order ? " order by " . $order : '';

            $query .= $limit ? " limit " . $limit : '';

            $this->setLastQuery($query);

            $set = mysql_query($query);

			$this->logQueryResult($set,$query,'set,*');

            while ($row = @mysql_fetch_assoc($set)) {

				if ($fieldAsIndex!==false && isset($row[$fieldAsIndex])) {

	                $this->data[$row[$fieldAsIndex]] = $row;

				} else {

	                $this->data[] = $row;

            	}

            }

        } elseif (is_numeric($id)) {

            $query = 'select ' . (!$cols ? '*' : $cols) . ' from ' . $this->tableName . ' where id =' . $this->escapeString($id) . ' limit 1';

            $this->setLastQuery($query);

			$m = mysql_query($query);

			$this->logQueryResult($m,$query,'set,id only');

            $this->data = @mysql_fetch_assoc($m);

        } elseif ($where!==false) {

            $query = 'select ' . (!$cols ? '*' : $cols) . ' from ' . $this->tableName . ' where ' . $where;

            $query .= $group ? " group by " . $group : '';

            $query .= $order ? " order by " . $order : '';

            $query .= $limit ? " limit " . $limit : '';

            $this->setLastQuery($query);

            $set = mysql_query($query) or $this->log('Failed query: '.$query,2);

            $this->setLastQuery($query);

            while ($row = @mysql_fetch_assoc($set)) {

				if ($fieldAsIndex!==false && isset($row[$fieldAsIndex])) {

	                $this->data[$row[$fieldAsIndex]] = $row;

				} else {

	                $this->data[] = $row;

            	}

            }

        } elseif (!is_null($id)) {

			$query = str_ireplace('%table%', $this->tableName, $id);

            $set = mysql_query($query);

			$this->logQueryResult($set,$query,'set,full query');

            $this->setLastQuery($query);

            while ($row = @mysql_fetch_assoc($set)) {

				if ($fieldAsIndex!==false && isset($row[$fieldAsIndex]))
				{
	                $this->data[$row[$fieldAsIndex]] = $row;
				}
				else
				{
	                $this->data[] = $row;
            	}
            }
        }
		else
		{
			$this->log('Called _get with an empty query (poss. cause: "...\'id\' => \'null\' " instead of " => null ")',1);
		}
    }

	private function logQueryResult($set,$query,$type,$severity=1)
	{
		if (!$set)
		{
			// 1062 = key violation
			if (mysql_errno()!=1062 || $this->noKeyViolationLogging!=true) $this->log('Failed query ('.$type.'): '.$query,$severity);
		}
	}
}