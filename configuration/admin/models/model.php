<?php

    include_once(dirname(__FILE__)."/../BaseClass.php");

    abstract class Model extends BaseClass {

        private $databaseSettings;
        public $databaseConnection;
        private $data;
        public $tableName;
        private $id;
        public $newId;
		private $retainBeforeAlter;
		private $retainedData;
		private $lastQuery;

        public function __construct($tableBaseName = false) {

            parent::__construct();

            $this->connectToDatabase() or die(_('FATAL: cannot connect to database').' ('.mysql_error().')');

            if (!$tableBaseName) {

                die(_('FATAL: no table basename defined'));

            } else {

                $this->tableName = $this->databaseSettings['tablePrefix'] . $tableBaseName;

            }

            $this->getTableColumnInfo();
			
			$this->setRetainBeforeAlter(false);

        }

        public function __destruct() {

            if ($this->databaseConnection) {

                $this->disconnectFromDatabase();

            }

            parent::__destruct();

        }

        public function escapeString($d) {

            return is_string($d) ? mysql_real_escape_string($d) : $d;

        }

		public function setRetainBeforeAlter($state = true) {

			$this->retainBeforeAlter = $state;

		}

		public function getRetainedData() {

			return isset($this->retainedData) ? $this->retainedData : false;

		}

        public function save($data) {
            
            if (!$this->hasId($data)) return false;

            $this->get();

            if (empty($this->data)) {

                return $this->insert($data);

            } else {

                return $this->update($data);

            }

        }

        public function insert($data) {

            foreach((array)$data as $key => $val) {

                $data[$key] = $this->escapeString($val);

            }
            
            $fields = null;

            $values = null;

            foreach((array)$data as $key => $val) {

                if (empty($this->columns[$key])) continue;

                $d = $this->columns[$key];

                if ($d && !empty($val)) {
                
                    $fields .= $key.", ";

                    if ($d['type']=='date' || $d['type']=='datetime' || $d['type']=='timestamp') {

						if ($this->isDateTimeFunction($val)) {

	                        $values .= $val.", ";
						
						} else {

	                        $values .= "'".$val."', ";
						
						}
					} elseif ($d['numeric']==1) {

                        $values .= $val.", ";

                    } else {

                        $values .= "'".$val."', ";

                    }

                }
            
            }

            if (array_key_exists('created',$this->columns) && !array_key_exists('created',$data)) {
            
                $fields .= 'created,';

                $values .= 'CURRENT_TIMESTAMP,';

            }

            $query =
                "insert into ".$this->tableName." (".trim($fields,', ').") values (".trim($values,', ').")";

			$this->setLastQuery($query);

            if (!mysql_query($query)) {

                return mysql_error($this->databaseConnection);

            } else {

                $this->newId = mysql_insert_id($this->databaseConnection);

                return true;

            }

        }

        public function update($data, $where = false) {

            foreach((array)$data as $key => $val) {

                $data[$key] = $this->escapeString($val);

            }

            $query = "update ".$this->tableName." set ";

            foreach((array)$data as $key => $val) {

                if (!isset($this->columns[$key])) continue;

                $d = $this->columns[$key];

                if ($d && isset($val)) {

                    if ($d['numeric']==1) {

                        $query .= " ".$key." = ".$val.", ";

                    } elseif ($d['type']=='datetime') {
                    
                        $query .= " ".$key." = ".$val.", ";

                    } else {

                        $query .= " ".$key." = '".$val."', ";

                    }

                }
            
            }

			// this might seen odd as all the last_change columns are defined with 'ON UPDATE CURRENT_TIMESTAMP' 
			// occasionally, it is necessary to update only the last_change column, as with the heartbeats table
            if (array_key_exists('last_change',$this->columns) && array_key_exists('last_change',$data)) {
            
                $query .= 'last_change = CURRENT_TIMESTAMP,';

            }

            $query = rtrim($query,', ');

            if (!$where) {

                $query .= " where id = ".$data['id'];

            } else 
            if (is_array($where)) {

                $query .= " where id = id ";

                foreach((array)$where as $col => $val) {

                    if (strpos($col,' ')===false) {

                        $operator = '=';

                    } else {

                        $operator = trim(substr($col,strpos($col,' ')));

                        $col = trim(substr($col,0,strpos($col,' ')));

                    }

                    $query .= ' and '.$col." ".$operator." '". $this->escapeString($val)."'";

                }
                
            }

			$this->retainAlteredData($query);

			$this->setLastQuery($query);

            if (!mysql_query($query)) {

                return mysql_error($this->databaseConnection);

            } else {

                return true;

            }

        }

        public function delete($id = false) {

            if (!$id) return;

            if (is_array($id)) {

                $query = 'delete from '.$this->tableName.' where 1=1 ';

                foreach((array)$id as $col => $val) {

                    if (strpos($col,' ')===false) {

                        $operator = '=';

                    } else {

                        $operator = trim(substr($col,strpos($col,' ')));

                        $col = trim(substr($col,0,strpos($col,' ')));

                    }

                    $query .= ' and '.$col." ".$operator." '". $this->escapeString($val)."'";

                }

			} elseif (is_string($id)) {

				$query = str_replace('%table%',$this->tableName,$id);

			} elseif ($id+0 == $id) {

                $query = 'delete from '.$this->tableName.' where id = '.($id ? $id : $this->id).' limit 1';

            } else {
			
				return;

			}

			$this->retainAlteredData($query);

			$this->setLastQuery($query);
			
			$result = mysql_query($query);

            if (!$result) {

                return mysql_error($this->databaseConnection);

            } else {

                return true;

            }

        }

        public function get($id = false, $cols = false, $order = false, $groupby = false, $ignore_case = true ) {
        
            unset($this->data);

            $this->set($id ? $id : $this->id, $cols, $order, $groupby, $ignore_case);

            return isset($this->data) ? $this->data : null;

        }

        /**
        * Returns the id of a newly inserted row
        *
        * @return     integer    new id
        * @access     public
        */
        public function getNewId() {

            return $this->newId;

        }

		public function getLastQuery() {

			return $this->lastQuery;

		}

		public function q() {

			return $this->getLastQuery();

		}


		private function isDateTimeFunction($val) {

			try {

				$date = new DateTime($val);
				
				return false;

			} catch (Exception $e) {

				return true;

			}

		}
		
		private function setLastQuery($query) {

			$this->lastQuery = $query;

		}
		
        private function connectToDatabase() {

            $this->databaseSettings = $this->config->getDatabaseSettings();

            $this->databaseConnection =
                mysql_connect(
                    $this->databaseSettings['host'],
                    $this->databaseSettings['user'],
                    $this->databaseSettings['password']
                );

            if (!$this->databaseConnection) return false;

            mysql_select_db($this->databaseSettings['database'],$this->databaseConnection);// or return false;
            
            if ($this->databaseSettings['characterSet']) {
            
                mysql_query('SET NAMES '.$this->databaseSettings['characterSet'],$this->databaseConnection);
                mysql_query('SET CHARACTER SET '.$this->databaseSettings['characterSet'],$this->databaseConnection);

            }

            return true;

        }
        
        private function disconnectFromDatabase() {

            @mysql_close($this->databaseConnection);

        }

        private function getTableColumnInfo() {

            $r = mysql_query('select * from '.$this->tableName.' limit 1');

            $i = 0;

            while ($i < mysql_num_fields($r)) {

                $info = mysql_fetch_field($r, $i);

                if ($info) {
                
                    $this->columns[$info->name] =
                    array(
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

        private function hasId($data) {

            foreach((array)$data as $col => $val) {

                if ($col=='id') {

                    $this->id = $val;

                    return true;

                }

            }
            
            return false;

        }

		private function retainAlteredData($query) {

			if (!$this->retainBeforeAlter) return;
			
			unset($this->retainedData);
			
			$query = strtolower($query);

			if (strpos($query,'delete')===0) {
			
				$q = str_replace('delete from','select * from',$query);
			
			} else
			if (strpos($query,'update')===0) {
			
				$d = preg_split('/ where /',$query);

				$q = 'select * from '.$this->tableName.' where '.$d[1];
			
			}

			if (isset($q)) {

				$this->setLastQuery($q);

				$result = mysql_query($q);
				
				while ($r = mysql_fetch_assoc($result)) {

					$this->retainedData[] = $r;

				}

			}

		}

        private function set($id = false, $cols = false, $order = false, $groupby = false, $ignore_case = true ) {

            /*

                function can take as $id:
                    - a single $id to find the corresponding row
                    - an array of column/value-pairs (array('last_name' => 'turing' ))
                      standard operator is '=' but it is possible to tag another operator 
                      after the column-value (array('last_name !=' => 'gates' ))
                    - a full query with %table% as tablename
                $cols can hold a string that replaces the defualt * in 'select * from...'

            */
            
            $query = false;

            if (!$id) return;

            if (is_array($id)) {
            
                $query = 'select '.( !$cols ? '*' : $cols).' from '.$this->tableName.' where 1=1 ';

                foreach((array)$id as $col => $val) {

                    if (strpos($col,' ')===false) {

                        $operator = '=';

                    } else {

                        $operator = trim(substr($col,strpos($col,' ')));

                        $col = trim(substr($col,0,strpos($col,' ')));

                    }
                    
                    if ($ignore_case && is_string($val)) {

                        $query .= ' and lower('.$col.") ".$operator." '". $this->escapeString(strtolower($val))."'";

                    } else {

                        $query .= ' and '.$col." ".$operator." '". $this->escapeString($val)."'";

                    }

                }

                $query .= $groupby ? " group by ".$groupby : '';

                $query .= $order ? " order by ".$order : '';

				$this->setLastQuery($query);

                $set = mysql_query($query);

				$this->setLastQuery($query);

                while ($row = mysql_fetch_assoc($set)) {

                    $this->data[] = $row;

                }

            } elseif (is_numeric($id)) {

                $query =
                    'select '.( !$cols ? '*' : $cols).
                    ' from '.$this->tableName.
                    ' where id ='.$this->escapeString($id).' limit 1';

				$this->setLastQuery($query);

                $this->data = mysql_fetch_assoc(mysql_query($query));

            } else {

				$this->setLastQuery($query);

                $set = mysql_query(str_replace('%table%',$this->tableName,$id));

                while ($row = mysql_fetch_assoc($set)) {

                    $this->data[] = $row;

                }

            }

        }
      
    }


?>