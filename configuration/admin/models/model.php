<?php

    include_once(dirname(__FILE__)."/../BaseClass.php");

    abstract class Model extends BaseClass {

        private $databaseSettings;
        public $databaseConnection;
        private $data;
        public $tableName;
        private $id;
        public $newId;

        public function __construct($tableBaseName = false) {

            parent::__construct();

            $this->connectToDatabase() or die(_('FATAL: cannot connect to database').' ('.mysql_error().')');

            if (!$tableBaseName) {

                die(_('FATAL: no table basename defined'));

            } else {

                $this->tableName = $this->databaseSettings['tablePrefix'] . $tableBaseName;

            }

            $this->getTableColumnInfo();

        }

        public function __destruct() {

            if ($this->databaseConnection) {

                $this->disconnectFromDatabase();

            }

            parent::__destruct();
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

        public function escapeString($d) {

            return mysql_real_escape_string($d);

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

                    if ($d['numeric']==1) {

                        $values .= $val.", ";

                    } elseif ($d['type']=='datetime') {
                    
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

            //q($query);

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

                    //echo $query.'<br />';

                }
                
            }

            //echo $query;die();

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

                    //echo $query.'<br />';

                }

                $result = mysql_query($query);


            } elseif ($id+0 == $id) {

                $query = "delete from ".$this->tableName." where id = ".($id ? $id : $this->id)." limit 1";
    
                //echo($query);
    
                $result = mysql_query($query);

            } else {

                return;
        
            }

            if (!$result) {

                return mysql_error($this->databaseConnection);

            } else {

                return true;

            }

        }

        private function set($id = false, $cols = false, $order = false, $ignore_case = true ) {

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
                    
                    if ($ignore_case) {

                        $query .= ' and lower('.$col.") ".$operator." '". $this->escapeString(strtolower($val))."'";

                    } else {

                        $query .= ' and '.$col." ".$operator." '". $this->escapeString($val)."'";

                    }

                }

                $query .= $order ? " order by ".$order : '';

                //echo $query.'<br />';//die();

                $set = mysql_query($query);

                while ($row = mysql_fetch_assoc($set)) {

                    $this->data[] = $row;

                }

            } elseif (is_numeric($id)) {

                $query =
                    'select '.( !$cols ? '*' : $cols).
                    ' from '.$this->tableName.
                    ' where id ='.$this->escapeString($id).' limit 1'.
                    ($query .= $order ? ' '.$order : '');

                $this->data = mysql_fetch_assoc(mysql_query($query));

            } else {

                $set = mysql_query(str_replace('%table%',$this->tableName,$id));

                while ($row = mysql_fetch_assoc($set)) {

                    $this->data[] = $row;

                }

            }

        }

        public function get($id = false, $cols = false, $order = false, $ignore_case = true ) {
        
            unset($this->data);

            $this->set($id ? $id : $this->id, $cols, $order, $ignore_case);

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

        /*
        private function connectToDatabasePDO() {

            $this->databaseConnection = false;

            $this->databaseSettings = $this->config->getDatabaseSettings();

            try
            {
                $this->databaseConnection = 
                    new PDO(
                        'mysql:host='.$this->databaseSettings['host'].';dbname='.$this->databaseSettings['database'], 
                        $this->databaseSettings['user'],
                        $this->databaseSettings['password']
                    );

                if (!empty($this->databaseSettings['characterSet'])) {
                
                    $this->databaseConnection->exec('SET NAMES '.$this->databaseSettings['characterSet']);
                    $this->databaseConnection->exec('SET CHARACTER SET '.$this->databaseSettings['characterSet']);

                }
            
                return true;
            }
            catch(PDOException $e)
            {
                die(_('FATAL: ').$e->getMessage());
            }

        }
        */
        
    }


?>