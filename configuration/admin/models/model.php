<?php

include_once (dirname(__FILE__) . "/../BaseClass.php");

abstract class Model extends BaseClass
{

	private $noKeyViolationLogging = false;
    
    private $databaseSettings;
    public $databaseConnection;
    private $data;
    public $tableName;
    private $id;
    public $newId;
    private $retainBeforeAlter;
    private $retainedData;
    private $lastQuery;
	private $logger;
	private $_projectId=false;
	public $doLog = true;
	private $_affectedRows = 0;

    public function __construct ($tableBaseName = false)
    {
        
        parent::__construct();
        
        $this->connectToDatabase() or die(_('FATAL: cannot connect to database') . ' (' . mysql_error() . ')');
        
        if (!$tableBaseName) {
            
            die(_('FATAL: no table basename defined'));
        
        }
        else {
            
            $this->tableName = $this->databaseSettings['tablePrefix'] . $tableBaseName;
        
        }
        
        $this->getTableColumnInfo();
        
        $this->setRetainBeforeAlter(false);
    
    }

    public function __destruct ()
    {
        
        if ($this->databaseConnection) {
            
            $this->disconnectFromDatabase();
        
        }
        
        parent::__destruct();
    
    }
	
	public function setLogger($logger)
	{

		$this->logger = $logger;

	}


    public function escapeString ($d)
    {
        
        return is_string($d) ? mysql_real_escape_string($d) : $d;
    
    }



    public function setRetainBeforeAlter ($state = true)
    {
        
        $this->retainBeforeAlter = $state;
    
    }



    public function getRetainedData ()
    {
        
        return isset($this->retainedData) ? $this->retainedData : false;
    
    }



    public function save($data)
    {

        if ($this->hasId($data)) {
            
            return $this->update($data);
        
        } else {
            
            return $this->insert($data);
        
        }

		/*
        if (!$this->hasId($data)) return false;

        $this->_get();

        if (empty($this->data)) {
            
            return $this->insert($data);
        
        }
        else {
            
            return $this->update($data);
        
        }
		*/
    
    }



    public function insert ($data)
    {

        foreach ((array) $data as $key => $val) {

			if (!is_array($val) && !(substr($val,0,1)=='#')) $data[$key] = $this->escapeString($val);
        
        }
        
        $fields = null;
        
        $values = null;
        
        foreach ((array) $data as $key => $val) {
            
            if (is_array($val))
                continue;

            if (empty($this->columns[$key]))
                continue;
            
            $d = $this->columns[$key];

			if ($key=='project_id') {

				$this->_projectId = $val;
			
			}

            if ($d && (!empty($val) || $val===0 || $val==='0')) {

                $fields .= "`".$key ."`, ";

				// # at beginning of value is signal to take the value literal
                if (substr($val,0,1)=='#') {

					$values .= substr($val,1) . ", ";

				}
				elseif ($d['type'] == 'date' || $d['type'] == 'datetime' || $d['type'] == 'timestamp') {
                    
                    if ($this->isDateTimeFunction($val)) {
                        
                        $values .= $val . ", ";
                    
                    }
                    else {
                        
                        $values .= "'" . $val . "', ";
                    
                    }
                }
                elseif ($d['numeric'] == 1) {
                    
                    $values .= $val . ", ";
                
                }
                else {
                    
                    $values .= "'" . $val . "', ";
                
                }
            
            }
        
        }
        
        if (array_key_exists('created', $this->columns) && !array_key_exists('created', $data)) {
            
            $fields .= 'created,';
            
            $values .= 'CURRENT_TIMESTAMP,';
        
        }
        
        $query = "insert into " . $this->tableName . " (" . trim($fields, ', ') . ") values (" . trim($values, ', ') . ")";
        
        $this->setLastQuery($query);
        
        if (!mysql_query($query)) {
		
			$this->logQueryResult(false,$query,'ins');

            return mysql_error($this->databaseConnection);
        
        }
        else {

			$this->setAffectedRows();            

            $this->newId = mysql_insert_id($this->databaseConnection);
            
            return true;
        
        }
    
    }



    public function update ($data, $where = false)
    {
        
        foreach ((array) $data as $key => $val) {
            
			if (!is_array($val) && !(substr($val,0,1)=='#')) $data[$key] = $this->escapeString($val);
            //$data[$key] = $this->escapeString($val);
        
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

			}
			else
			if ($d && (!empty($val) || $val===0 || $val==='0')) {
			
                if ($d['numeric'] == 1) {
                    
                    $query .= " `" . $key . "` = " . $val . ", ";
                
                }
                elseif ($d['type'] == 'datetime') {
                    
                    $query .= " `" . $key . "` = " . $val . ", ";
                
                }
                else {

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
        
        }
        else if (is_array($where)) {
            
            $query .= " where id = id ";
            
            foreach ((array) $where as $col => $val) {
                
                if (strpos($col, ' ') === false) {
                    
                    $operator = '=';
                
                }
                else {
                    
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
        
        $this->retainAlteredData($query);
        
        $this->setLastQuery($query);
        
        if (!mysql_query($query)) {

			$this->logQueryResult(false,$query,'upd');
            
            return mysql_error($this->databaseConnection);
        
        }
        else {

			$this->setAffectedRows();            

            return true;
        
        }
    
    }



    public function delete ($id = false)
    {
        
        if (!$id)
            return;
        
        if (is_array($id)) {
            
            $query = 'delete from ' . $this->tableName . ' where 1=1 ';
            
            foreach ((array) $id as $col => $val) {
                
                if (strpos($col, ' ') === false) {
                    
                    $operator = '=';
                
                } else {
                    
                    $operator = trim(substr($col, strpos($col, ' ')));
                    
                    $col = trim(substr($col, 0, strpos($col, ' ')));
                
                }

				if ($col=='project_id') {
	
					$this->_projectId = $val;
				
				}

                $query .= " and `" . $col . "` " . $operator . " '" . $this->escapeString($val) . "'";
            
            }
        
        } elseif (is_numeric($id)) {
            
            $query = 'delete from ' . $this->tableName . ' where id = ' . ($id ? $id : $this->id) . ' limit 1';
        
        } elseif (is_string($id)) {
            
            $query = str_replace('%table%', $this->tableName, $id);
        
        } else {
            
            return;
        
        }

        $this->retainAlteredData($query);
        
        $this->setLastQuery($query);
        
        $result = mysql_query($query);
        
        if (!$result) {
		
			$this->logQueryResult(false,$query,'del');

            return mysql_error($this->databaseConnection);
        
        }
        else {

			$this->setAffectedRows();            

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
     * Returns the id of a newly inserted row
     *
     * @return     integer    new id
     * @access     public
     */
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

		$this->retainAlteredData($query);
		
		$this->setLastQuery($query);
		
		$result = mysql_query($query);

		if (!$result) {
		
			$this->logQueryResult(false,$query,'exec');
			
			return mysql_error($this->databaseConnection);
		
		}
		else {
		
			$this->setAffectedRows();            
			
			return true;
		
		}
    
    }

	public function setNoKeyViolationLogging($state)
	{

		if (is_bool($state)) $this->noKeyViolationLogging = $state;

	}


	/* DEBUG */
    public function q ()
    {
        
        return $this->getLastQuery();
    
    }


	private function log($msg,$level=0)
	{
	
		if (!$this->doLog) return;

		if (method_exists($this->logger,'log')) {

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
        
        $this->databaseConnection = mysql_connect($this->databaseSettings['host'], $this->databaseSettings['user'], $this->databaseSettings['password']);
        
        if (!$this->databaseConnection) {

			 $this->log('Failed to connect to database '.$this->databaseSettings['host'].' with user '.$this->databaseSettings['user'],2);

            return false;

		}
        
        mysql_select_db($this->databaseSettings['database'], $this->databaseConnection) or $this->log('Failed to select database '.$this->databaseSettings['database'],2);

        if ($this->databaseSettings['characterSet']) {
            
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

		if (!$r) return;
        
        $i = 0;
       
        while ($i < mysql_num_fields($r)) {
            
            $info = mysql_fetch_field($r, $i);
            
            if ($info) {

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



    private function hasId ($data)
    {
        
        foreach ((array) $data as $col => $val) {
            
            if ($col == 'id' && $val != null) {
                
                $this->id = $val;
                
                return true;
            
            }
        
        }
		
        return false;
    
    }



    private function retainAlteredData($query)
    {
        
        if (!$this->retainBeforeAlter) return;
        
        unset($this->retainedData);

        $query = strtolower($query);
        
        if (strpos($query, 'delete') === 0) {
            
            $q = str_replace('delete from', 'select * from', $query);
        
        }
        else if (strpos($query, 'update') === 0) {
            
			/* this will fail if there is a string with the substring " where " somewhere in the where-clause*/
            $d = preg_split('/ where /', $query);
            
            $q = 'select * from ' . $this->tableName . ' where ' . $d[count((array)$d)-1];
        
        }

        if (isset($q)) {

            $this->setLastQuery($q);
            
            $result = mysql_query($q);
            
            while ($r = @mysql_fetch_assoc($result)) {
                
                $this->retainedData[] = $r;

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

        $query = false;
        
        if (!$id && !$where) return;
	
        if (is_array($id)) {

            $query = 'select ' . (!$cols ? '*' : $cols) . ' from ' . $this->tableName . ' where 1=1 ';
            
            foreach ((array) $id as $col => $val) {
                
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

				} else {

					continue;

				}

				// operator ending with # signals to use val literally (for queries like: "mean = (23 + (sd * 2))"
                if (substr($operator,-1) == '#') {

                    $query .= " and " . $col . " " . substr($operator,0,-1) . " " . $val;
                
                } elseif ($val===null) {
                
                    $query .= " and " . $col . " " . $operator . " null ";
                
                } elseif ($operator == 'like') {

                    $query .= " and " . $col . " " . $operator . " '" . strtolower($val)."'";
                
                } elseif ($d['numeric'] == 1) {

                    $query .= " and " . $col . " " . $operator . " " . $this->escapeString(strtolower($val));
                
                } elseif ($d['type'] == 'datetime') {
                    
                    $query .= " and " . $col . " " . $operator . " '" . $this->escapeString(strtolower($val))."'";
                
                } elseif ($ignoreCase && is_string($val)) {
                    
                    $query .= " and lower(" . $col . ") " . $operator . " '" . $this->escapeString(strtolower($val)) . "'";
                
                } else {
                    
                    $query .= " and " . $col . " " . $operator . " '" . $this->escapeString($val) . "'";
                
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

			$query = str_replace('%table%', $this->tableName, $id);

            $set = mysql_query($query);
			
			$this->logQueryResult($set,$query,'set,full query');

            $this->setLastQuery(str_replace('%table%', $this->tableName, $id));

            while ($row = @mysql_fetch_assoc($set)) {
                
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

	private function logQueryResult($set,$query,$type,$severity=1) {

		if (!$set) {

			// 1062 = key violation
			if (mysql_errno()!=1062 || $this->noKeyViolationLogging!=true) $this->log('Failed query ('.$type.'): '.$query,$severity);
	
		}

	}



}
