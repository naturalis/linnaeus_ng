<?php

include_once (__DIR__ . "/AbstractModel.php");

final class Table extends AbstractModel
{

    public $columns;

    /**
     * Table constructor.
     * @param bool $tableBaseName
     */
    public function __construct ($tableBaseName = false)
    {
        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

        if (! $tableBaseName) {
            die(_('FATAL: no table basename defined'));
        } else {
            $this->_tablePrefix = $this->databaseSettings['tablePrefix'];
            $this->tableName = $this->_tablePrefix . $tableBaseName;
        }

        $this->getTableColumnInfo();
    }

    /**
     * Destruction method
     *
     */
    public function __destruct ()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

    /**
     * @param bool $state
     */
    public function setTableExists($state)
	{
		$this->tableExists=$state;
	}

    /**
     * @return bool
     */
    public function getTableExists()
	{
		return $this->tableExists;
	}

    /**
     * @return string
     */
    public function getTablePrefix()
	{
		return $this->_tablePrefix;
	}

    /**
     * @param array $data
     * @return bool|string
     */
    public function save($data)
    {
        if ($this->hasId($data)) {
            return $this->update($data);
        } else {
            return $this->insert($data);
        }
    }

    public function insert ($data)
    {
        foreach ((array) $data as $key => $val) {
			if (!is_array($val) && !(substr($val,0,1)=='#')) {
                $data[$key] = $this->escapeString($val);
            }
        }

        $fields = null;
        $values = null;

        foreach ((array) $data as $key => $val)
		{
            if (is_array($val)) {
                continue;
            }

            if (empty($this->columns[$key])) {
                continue;
            }

            $d = $this->columns[$key];

			if ($key=='project_id')
			{
				$this->_projectId = $val;
			}

            if ($d && (!empty($val) || $val===0 || $val==='0') && $val!='null') {
                $fields .= "`".$key ."`, ";

				// # at beginning of value is signal to take the value literal
                if (substr($val,0,1)=='#')
				{
					$values .= substr($val,1) . ", ";
				} else if ($d['type'] == 'date' || $d['type'] == 'datetime' || $d['type'] == 'timestamp') {

					if ($this->isDateTimeFunction($val)) {
                        $values .= $val . ", ";
                    } else {
                        $values .= "'" . $val . "', ";
                    }

                } else if ($d['numeric']==1) {
                    $values .= $val . ", ";
                } else {
                    $values .= "'" . $val . "', ";
                }
            }
        }

        if (array_key_exists('created', $this->columns) && !array_key_exists('created', $data)) {
            $fields .= 'created,';
            $values .= 'CURRENT_TIMESTAMP,';
        }

        $query = "insert into " . $this->tableName . " (" . trim($fields, ', ') . ") values (" . trim($values, ', ') . ")";

        $this->retainDataBeforeQuery(null);
        $this->setLastQuery($query);

        if (!mysqli_query($this->databaseConnection, $query)) {
			$this->logQueryResult(false,$query,'ins');
            return mysqli_error($this->databaseConnection);
        } else {
			$this->setAffectedRows();
            $this->newId = mysqli_insert_id($this->databaseConnection);
			$this->retainDataAfterQuery($this->newId);
            return true;
        }
    }

    /**
     * @param array $data
     * @param bool $where
     * @return bool|string
     */
    public function update ($data, $where = false)
    {
        foreach ((array) $data as $key => $val) {
			if (!is_array($val) && !(substr($val,0,1)=='#')) $data[$key] = $this->escapeString($val);
        }

        $query = "update " . $this->tableName . " set ";

        foreach ((array) $data as $key => $val) {
            if (!isset($this->columns[$key]))
                continue;

            $d = $this->columns[$key];
			if ($key=='project_id') {
				$this->_projectId = $val;
			}

			// # at beginning of value is signal to take the value literal
			if (substr($val,0,1)=='#') {
				$query .= " `" . $key . "` = " . substr($val,1) . ", ";
			} else if ($d && (!empty($val) || $val===0 || $val==='0')) {
                if (($d['numeric'] == 1) || ($val == 'now()')) {
                    $query .= " `" . $key . "` = " . $val . ", ";
                } else if ($d['type'] == 'date' || $d['type'] == 'datetime' || $d['type'] == 'timestamp') {
                    if ($this->isDateTimeFunction($val)) {
                        $query .= " `" . $key . "` = " . $val . ", ";
                    } else {
                        $query .= " `" . $key . "` = '" . $val . "', ";
                    }
                } else {
                    $query .= " `" . $key . "` = ".($val=='null' ? 'null' : "'" . $val . "'").", ";
                }
            }
        }

        // this might seem odd as all the last_change columns are defined with 'ON UPDATE CURRENT_TIMESTAMP'
        // occasionally, it is necessary to update only the last_change column, as with the heartbeats table
        if (array_key_exists('last_change', $this->columns) && array_key_exists('last_change', $data)) {
            $query .= 'last_change = CURRENT_TIMESTAMP,';
        }

        $query = rtrim($query, ', ');

        if (!$where) {
            $query .= " where id = " . $data['id'];
        } else if (is_array($where)) {
            $query .= " where id = id ";
            foreach ((array) $where as $col => $val)
			{
                if (strpos($col, ' ') === false) {
                    $operator = '=';
                } else {
                    $operator = trim(substr($col, strpos($col, ' ')));
                    $col = trim(substr($col, 0, strpos($col, ' ')));
                }

	            $d = $this->columns[$col];

                if ($d['numeric'] == 1) {
	                $query .= ' and `' . $col . "` " . $operator . " " . $this->escapeString($val);
				} else {
	                $query .= ' and `' . $col . "` " . $operator . " '" . $this->escapeString($val) . "'";
    			}
            }
        }

        $this->retainDataBeforeQuery($query);
        $this->setLastQuery($query);

        if (!mysqli_query($this->databaseConnection, $query)) {
			$this->logQueryResult(false,$query,'upd');
			$this->retainDataAfterQuery($query,true);
            return mysqli_error($this->databaseConnection);
        } else {
			$this->setAffectedRows();
			$this->retainDataAfterQuery($query);
            return true;
        }
    }

    /**
     * Deletes the record in the table when called with id
     * @param bool $id
     * @return bool|string|void
     */
    public function delete ($id = false)
    {
        if (!$id)
            return;

        if (is_array($id))
		{

            $query = 'delete from ' . $this->tableName . ' where 1=1 ';

            foreach ((array) $id as $col => $val)
			{
                if (strpos($col,' ')===false) {
                    $operator = '=';
                } else {
                    $operator = trim(substr($col, strpos($col,' ')));
                    $col = trim(substr($col, 0, strpos($col,' ')));
                }

				if ($col=='project_id') {
					$this->_projectId = $val;
				}

				// operator ending with # signals to use val literally (for queries like: "mean = (23 + (sd * 2))"
                if (substr($operator,-1)=='#') {
                    $query .= " and `" . $col . "` " . substr($operator,0,-1) . " " . $val;
                } else {
	                $query .= " and `" . $col . "` " . $operator . " '" . $this->escapeString($val) . "'";
				}
            }
        } else if (is_numeric($id)) {
            $query = 'delete from ' . $this->tableName . ' where id = ' . ($id ? $id : $this->id) . ' limit 1';
        } else if (is_string($id)) {
            $query = str_replace('%table%', $this->tableName, $id);
        } else {
            return;
        }
        $this->retainDataBeforeQuery($query);
        $this->setLastQuery($query);
        $result = mysqli_query($this->databaseConnection, $query);

        if (!$result) {
			$this->logQueryResult(false,$query,'del');
			$this->retainDataAfterQuery($query,true);
            return mysqli_error($this->databaseConnection);
        } else {
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

    /**
     * Returns single column from result as non-associative array:
     * array(0 => array(param => x), 1 => array(param => y)) becomes
     * array(0 => x, 1 => y). This can be handy to quickly generate
     * lookup tables.
     *
     * @param $params
     * @return bool
     */
    public function getSingleColumn ($params) {
        $r = $this->_get($params);
        if ($r && !empty($r)) {
            foreach ($r as $k => $v) {
                $o[] = reset($v);
            }
            return isset($o) ? $o : false;
        }
        return false;
    }

    /**
     * Get the new Id
     * @return mixed
     */
    public function getNewId ()
    {
        return $this->newId;
    }

    /**
     * Get the number of affected rows
     * @return mixed
     */
    public function getAffectedRows()
    {
        return $this->_affectedRows;
    }

    /**
     * Get the Last Query
     * @return mixed
     */
    public function getLastQuery ()
    {
        return $this->lastQuery;
    }

    /**
     * @param $state
     */
    public function setNoKeyViolationLogging($state)
	{
		if (is_bool($state)) {
            $this->noKeyViolationLogging = $state;
        }
	}

    /**
     * Get the name of the Table handled by the model
     *
     * @return string
     */
    public function getTableName()
	{
		return $this->tableName;
	}

    /**
     * Get table column details
     */
    private function getTableColumnInfo ()
    {
		$query = 'select * from ' . $this->tableName . ' limit 1';
        $r = mysqli_query($this->databaseConnection, $query);
		$this->logQueryResult($r,$query,'table col info');

		if (!$r)
		{
			$this->setTableExists(false);
			return;
		}

        $i = 0;

        while ($i < mysqli_num_fields($r)) {
            $info = mysqli_fetch_field($r);

            if ($info) {
                $this->columns[$info->name] = array(
                    'numeric' => in_array($info->type, array(16,1,2,9,3,8,4,5,246)) ? 1 : 0,
                    'table' => $info->table,
                    'type' => $this->getDataType($info->type)
                );
                $i++;
            }
        }
    }

    /**
     * Check if the array has an 'id' field
     * @param $data
     * @return bool
     */
    private function hasId($data)
    {
        foreach ((array) $data as $col => $val)
		{
            if ($col == 'id' && $val != null) {
                $this->id = $val;
                return true;
            }
        }
        return false;
    }

    /**
     * Setting the values in a record
     * function can take as $id:
     * - a single $id to find the corresponding row
     * - an array of column/value-pairs (array('last_name' => 'turing' ))
     * standard operator is '=' but it is possible to tag another operator
     * after the column-value (array('last_name !=' => 'gates' ))
     * - a full query with %table% as tablename
     * - * for no where clause
     * $cols can hold a string that replaces the defualt * in 'select * from...'
     *
     * @param $params
     */
    private function set($params)
    {
		$id = isset($params['id']) ? $params['id'] : false;
		$cols = isset($params['columns']) ? $params['columns'] : false;
		$order = isset($params['order']) ? $params['order'] : false;
		$group = isset($params['group']) ? $params['group'] : false;
		$limit = isset($params['limit']) ? $params['limit'] : false;
		$ignoreCase = isset($params['ignoreCase']) ? $params['ignoreCase'] : true;
		$fieldAsIndex = isset($params['fieldAsIndex']) ? $params['fieldAsIndex'] : false;
		$where = isset($params['where']) ? $params['where'] : false;

		$this->setCurrentWhereArray($id);

        $query = "";

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
				} else if (strtolower(trim($col))=='%literal%') {
	            	$colLiteral = true;
				} else {
					continue;
				}

				if ($colLiteral) {
                    $query .= " and " . $val;
				} elseif (substr($operator,-1) == '#') {
                    // operator ending with # signals to use val literally (for queries like: "mean = (23 + (sd * 2))"
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

            $set = mysqli_query($this->databaseConnection, $query);
			$this->logQueryResult($set,$query,'set,normal');
            $this->setLastQuery($query);

            while ($row = @mysqli_fetch_assoc($set)) {
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

            $set = mysqli_query($this->databaseConnection, $query);
			$this->logQueryResult($set,$query,'set,*');

            while ($row = @mysqli_fetch_assoc($set)) {
				if ($fieldAsIndex!==false && isset($row[$fieldAsIndex])) {
	                $this->data[$row[$fieldAsIndex]] = $row;
				} else {
	                $this->data[] = $row;
            	}
            }

        } elseif (is_numeric($id)) {

            $query = 'select ' . (!$cols ? '*' : $cols) . ' from ' . $this->tableName . ' where id =' . $this->escapeString($id) . ' limit 1';
            $this->setLastQuery($query);
			$m = mysqli_query($this->databaseConnection, $query);
			$this->logQueryResult($m,$query,'set,id only');
            $this->data = @mysqli_fetch_assoc($m);

        } elseif ($where!==false) {

            $query = 'select ' . (!$cols ? '*' : $cols) . ' from ' . $this->tableName . ' where ' . $where;
            $query .= $group ? " group by " . $group : '';
            $query .= $order ? " order by " . $order : '';
            $query .= $limit ? " limit " . $limit : '';

            $this->setLastQuery($query);

            $set = mysqli_query($this->databaseConnection, $query) or
                $this->log('Failed query: '.$query,2);

            $this->setLastQuery($query);

            while ($row = @mysqli_fetch_assoc($set)) {

				if ($fieldAsIndex!==false && isset($row[$fieldAsIndex])) {
	                $this->data[$row[$fieldAsIndex]] = $row;
				} else {
	                $this->data[] = $row;
            	}

            }

        } elseif (! is_null($id)) {

			$query = str_ireplace('%table%', $this->tableName, $id);
            $set = mysqli_query($this->databaseConnection, $query);
			$this->logQueryResult($set,$query,'set,full query');
            $this->setLastQuery($query);

            while ($row = @mysqli_fetch_assoc($set)) {

				if ($fieldAsIndex!==false && isset($row[$fieldAsIndex])) {
	                $this->data[$row[$fieldAsIndex]] = $row;
				} else {
	                $this->data[] = $row;
            	}
            }
        } else {
			$this->log('Called _get with an empty query (poss. cause: "...\'id\' => \'null\' " instead of " => null ")',1);
		}
    }
}

