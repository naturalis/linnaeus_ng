<?php

class MySQL2SQLite 
{

	public $sqlTable;
	public $sqlKeys=array();
	public $sqlDropTable;
	public $sqlDropKeys=array();
	public $sqlReindexKeys=array();

	private $lines=array();
	private $dbSql_Proc=array();
	private $dbSql_Data=array();
	private $dbSql_Sche=array();
	private $dbSqlite=array();
	private $removeUniqueConstraints=false;

	//regex replacements for datatype conversion
	private $regex = array(
		'f' => array("@ COMMENT .*@",'@ UNSIGNED @i','@ on update [^,]*@','@ (small|tiny|medium|big|)int\([0-9]*\) @'),
		't' => array(',',' ','',' integer ')
	);
	
	//replacing escaped text in data
	private $escs = array(
		'f' => array("@\\\'@",'@\\\"@','@\\\n@','@\\\r@'),
		't' => array("''",'"',"\n","\r")
	);

	public function convert($t)
	{
		
		if (is_array($t))
			$this->lines = $t;
		else
			$this->lines = explode(chr(10),$t);
		
		$this->doConvert();

		return $this->dbSqlite;
	
	}

	public function getSqlTable()
	{
		return $this->sqlTable;
	}

	public function getsqlKeys()
	{
		return $this->sqlKeys;
	}
		
	public function getSqlDropTable()
	{
		return $this->sqlDropTable;
	}

	public function getSqlDropKeys()
	{
		return $this->sqlDropKeys;
	}

	public function getSqlReindexKeys()
	{
		return $this->sqlReindexKeys;
	}

	public function setRemoveUniqueConstraints($state)
	{
		/*
			the SQLite implementation on PhoneGap (WebDB) seems to handle 
			overlapping unique constraints differently than MySQL does. a 
			table with columns:
				a int null
				b int null
				value varchar not null
			and unique indexes on (a,value) as well as (b,value) has no 
			problems with inserting (1,null,'foo') and (2,null,'foo') on 
			MySQL, but seems to trigger a constraint violation on the second 
			insert, which apparently violates the second index. it seems
			MySQL ignores null values in this case, whereas WebDB sees
			them as an actual value. requires more testing, but of no
			importance as long as we're just exporting read-only databases
			to PhoneGap.
			
			setting $this->removeUniqueConstraints to true removes all
			UNIQUE's from the output, but retains the indexes (for lookup).
		
		*/
		if (is_bool($state)) $this->removeUniqueConstraints=$state;
	}
	
	private function reInit()
	{
		$this->sqlTable=null;
		$this->sqlKeys=array();
		$this->sqlDropTable=null;
		$this->sqlDropKeys=array();
		$this->sqlReindexKeys=array();

	}	

	private function doConvert()
	{
		
		$skipping = false;
		
		foreach((array)$this->lines as $line) {
		  list($key,) = explode(' ', trim($line));
		  switch (strtoupper($key)){
			case 'SET':
			case '/*!50003':
			case 'call':
				if($skipping){
					$this->dbSql_Proc[] = $line;
				}  		
			break;
			case 'DELIMITER':
				$this->dbSql_Proc[] = $this->cnvEscapes($line);
				$skipping = ($skipping == false)?true:false;
			break;
			case 'INSERT':
				if($skipping == false){
					$this->dbSql_Data[] = $this->cnvEscapes($line);
				}else{
					$this->dbSql_Proc[] = $line;
				}	
			break;
			default:
				if($skipping == false){
					$this->dbSql_Sche[] = $line;
				}else{
					$this->dbSql_Proc[] = $line;
				}	
			break;
		  }	
		}
		
		$schema = implode(chr(10),$this->dbSql_Sche);

		preg_match_all("/CREATE TABLE `([^\`]+?)`([^;]+);/isU", $schema, $m);

		$dbSchema = array_combine($m[1], $m[0]);
		
		foreach($dbSchema as $table => $schema){
			$this->dbSqlite[] = $this->mkSqlite3($table, $schema);
		}

		$this->dbSqlite = array_merge($this->dbSqlite,$this->dbSql_Data);
		
	}  
  
  
	private function mkSqlite3($table, $struct)
	{

		$keys = array();
		$split = explode("\n", $struct);

		$carry = array();
		foreach($split as $k => $line) {

			$line = preg_replace($this->regex['f'], $this->regex['t'], $line);
			switch(true){
				case (stripos(trim($line), 'PRIMARY KEY') === 0):
					//unset($split[$k]);
					break;
				case (stripos($line, 'enum(') !== false):
					preg_match("@enum\(([^)]*)\)@", $line, $m);
					$a = explode(',', str_replace("'", "",$m[1]));
					$g = 0;
					foreach ($a as $t){
						if(strlen($t) > $g) $g = strlen($t); 
					}
					$g = ceil( $g / 10) * 10;
					if($g > 255) $g = 255;
					
					$carry[] = preg_replace("@ enum\(.*\)@i", " varchar($g)", $line); 
					break;
				case (stripos($line, 'KEY ') !== false):
					$line = str_replace(' FULLTEXT','', $line);
					if(substr($line,-1) == ',')
					$line = substr($line, 0, -1);
					
					if ($this->removeUniqueConstraints) {
						$keys[] = 'CREATE ' . preg_replace("/(UNIQUE )?(KEY) `([^\"]+?)`(.*)/","INDEX `idx_{$table}_".(count((array)$keys))."` ON `{$table}`$4",trim($line)) . ';';
					} else
						$keys[] = 'CREATE ' . preg_replace("/(KEY) `([^\"]+?)`(.*)/","INDEX `idx_{$table}_".(count((array)$keys))."` ON `{$table}`$3",trim($line)) . ';';


					break;
				default:
					$carry[] = $line;
			}
		}
		
		// test and correct tailing lines commas
		if(substr($carry[(count($carry) - 2)],-1) == ',')
			$carry[(count($carry) -2)] = substr($carry[(count($carry) - 2)], 0, -1);

		$rv = implode("\n",$carry);
		
		$rv = preg_replace(array('/(ENGINE=)([^\s]*)/','/(AUTO_INCREMENT=)([^\s]*)/','/(DEFAULT CHARSET)([^\s;]*)/'),'',$rv);
		
		$f = array(' auto_increment',' CURRENT_TIMESTAMP');
		$t = array(' primary key autoincrement'," '0000-00-00 00:00:00'");
		$rv = str_ireplace($f, $t, $rv);		
		
		$this->reInit();

		$this->sqlTable = preg_replace(array('/\n/','/(\s+)/'),' ',$this->cnvEscapes($rv));
		$this->sqlKeys = $keys;
		
		$this->sqlDropTable = 'DROP TABLE IF EXISTS `'.$table.'`;';
		
		foreach((array)$this->sqlKeys as $val) {
			$key = preg_replace(array('/(CREATE )(UNIQUE )?(INDEX )/','/( ON )(.|\n)*/'),'',$val);
			$this->sqlDropKeys[] = 'DROP INDEX IF EXISTS '.$key.';';
			$this->sqlReindexKeys[] = 'REINDEX '.$key.';';
		}

	}
	
	private function cnvEscapes($str){
		return preg_replace($this->escs['f'], $this->escs['t'], $str);
	}

}