<?php

	class DatabaseTableCompare {

		private $constFile='/var/www/linnaeusng/configuration/admin/constants.php';
		private $cfgFile='/var/www/linnaeusng/configuration/admin/configuration.php';
		private $emptyDbFile='/var/www/linnaeusng/database/empty_database.sql';
		private $outputFile='%s_modify_%s.sql';
		private $errorFile;
		private $generalOutputFile='latest-modify.sql';
		private $generalErrorFile='latest-errors.txt';

		private $start;

		private $dbCfg;
		private $dbUserOverride = array();
		private $doNotCreateTempDatabase=false;

		private $dbHost;
		private $dbUser;
		private $dbPassword;

		private $conn0;
		private $conn1;
		private $dbDb0;
		private $dbDb0tablePrefix;
		private $dbDb1='____TEMP_lng_test_database';

		private $queries=[];
		private $errors=[];
		private $preflightQueries=[];
		private $postflightQueries=[];

		// When comparing column differences we don't consider the following
		// attributes, because column name and table name will always be the
		// same (that's why we compare the columns in the first place), while
		// the table schema will always be different (remember the purpose of
		// this utility, which is to compare databases). As for the columns'
		// ordinal position, if that's the only difference, it only means
		// that ANOTHER column was inserted or removed from one of the tables,
		// thus decrementing the ordinal position of all columns that came
		// after it. As for the CHARACTER_MAXIMUM_LENGTH, DATA_TYPE and
		// DATA_TYPE attributes, these are neatly packed together in the
		// COLUMN_TYPE attribute. As for the CHARACTER_OCTET_LENGTH attribute,
		// that's not something that you can specify explicitly when creating
		// a table. As for table catalog, who cares?
		private $ignoredAttributes=[
			'COLUMN_NAME',
			'TABLE_NAME',
			'TABLE_SCHEMA',
			'TABLE_CATALOG',
			'ORDINAL_POSITION',
			'CHARACTER_MAXIMUM_LENGTH',
			'CHARACTER_OCTET_LENGTH',
			'DATA_TYPE',
			'NUMERIC_PRECISION',
			'COLUMN_COMMENT',
			'CHARACTER_SET_NAME',
			'COLLATION_NAME',
		];

		// Used to reorder queries
		private $printOrder=[
			'Tables created',
			'Tables dropped',
			'Columns added',
			'Columns updated',
			'Indices updated'
		];

		// Keep track of database with dropped/recreated indices
		private $droppedIndices=[];

		public function setConstFile( $p )
		{
			$this->constFile=$p;
		}

		public function setCfgFile( $p )
		{
			$this->cfgFile=$p;
		}

		public function setEmptyDbFile( $p )
		{
			$this->emptyDbFile=$p;
		}

		public function setOutputFile( $p )
		{
			$this->outputFile=$p;
		}

		public function setGeneralOutputFile( $p )
		{
			$this->generalOutputFile=$p;
		}

		public function setGeneralErrorFile( $p )
		{
			$this->generalErrorFile=$p;
		}

		public function setDbUserOverride( $p )
		{
			$this->dbUserOverride=$p;
		}

		public function setDoNotCreateTempDatabase( $p )
		{
			$this->doNotCreateTempDatabase=$p;
		}

		public function setPreflightQueries($p)
		{
            $this->preflightQueries = $p;
		}

		public function addPreflightQuery($p)
		{
            $this->preflightQueries[] = $p;
		}

		public function setPostflightQueries($p)
		{
            $this->postflightQueries = $p;
		}

		public function addPostflightQuery($p)
		{
            $this->postflightQueries[] = $p;
		}

		public function run()
		{
			$this->checkFiles();
			$this->initialize();
			$this->printParameters();
			$this->connectDatabase();
			$this->checkLastChangeColumn();
			$this->initTestDatabase();
			$this->createTestTables();
			$this->compareTables();
			$this->dropTestDatabase();
			$this->writeQueries();
			$this->writeErrors();
			$this->copyOutputFilesToGeneral();
			$this->finish();
		}

		private function checkFiles()
		{
			echo "checking config files\n";

			$stat=[];
			foreach ([$this->constFile,$this->cfgFile,$this->emptyDbFile] as $file)
			{
				if ( !file_exists($file) )
				{
					$stat[]=sprintf("file does not exist: %s",$file);
				}
			}

			if ( count($stat)!=0 )
			{
				echo "initialization error(s):\n";
				echo implode("\n",$stat), "\n";
				die( 'abnormal program termination' );
			}
		}

		private function initialize()
		{
			echo "initializing\n";

			include_once( $this->constFile );
			include_once( $this->cfgFile );

			$c = new configuration;
			$this->dbCfg = $c->getDatabaseSettings(); // host, user, password

			$this->dbDb0=$this->dbCfg['database'];
			$this->dbDb0tablePrefix=$this->dbCfg['tablePrefix'];

			$this->dbHost=isset($this->dbUserOverride['host']) ? $this->dbUserOverride['host'] : $this->dbCfg['host'];
			$this->dbUser=isset($this->dbUserOverride['user']) ? $this->dbUserOverride['user'] : $this->dbCfg['user'];
			$this->dbPassword=array_key_exists('password',$this->dbUserOverride) ? $this->dbUserOverride['password'] : $this->dbCfg['password'];

			$this->start=new DateTime();
			$this->outputFile = sprintf( $this->outputFile , $this->dbDb0, $this->start->format('Y-m-d_H-i-s') );
			$this->errorFile = $this->outputFile . "-errors.txt";
		
			@unlink($this->generalOutputFile);
			@unlink($this->generalErrorFile);

		}



		private function connectDatabase()
		{
			echo "connecting to database\n";

			$this->conn0=@mysqli_connect( $this->dbHost, $this->dbUser, $this->dbPassword) or die( sprintf( "abnormal program termination: could not connect to mysql (%s@%s)\n",$this->dbUser, $this->dbHost) );
			$this->conn1=@mysqli_connect( $this->dbHost, $this->dbUser, $this->dbPassword) or die( sprintf( "abnormal program termination: could not connect to mysql (%s@%s)\n",$this->dbUser, $this->dbHost) );

			mysqli_select_db( $this->conn0, $this->dbDb0 ) or die( sprintf( "abnormal program termination: could not select database '%s'\n", $this->dbDb0 ) );

			$sqlMode=mysqli_fetch_object( mysqli_query( $this->conn0, "SELECT @@GLOBAL.sql_mode as mode;" ) );

			if ( $sqlMode->mode !== '')
			{
				//die( "abnormal program termination: disable MySQL STRICT mode (SET GLOBAL sql_mode = '')\n" );
				mysqli_query($this->conn0, "SET sql_mode = '';");
			}

		}

		private function checkLastChangeColumn () {
            mysqli_select_db( $this->conn0, 'information_schema' );
		    $tables = $this->getTables($this->dbDb0, $this->conn0);
		    foreach ($tables as $table) {
		        $columns = $this->getColumns($table, $this->dbDb0, $this->conn0);
		        foreach ($columns as $column) {
		            $name = $column['COLUMN_NAME'];
                    if (($name == 'last_change' || $name == 'last_update') &&
                        $column['COLUMN_DEFAULT'] == '0000-00-00 00:00:00') {
                        $this->addPreflightQuery("ALTER TABLE `$table` CHANGE `$name` `$name` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
                     }
		        }
		    }
		}

		private function printParameters()
		{
			$buffer[]=sprintf( "comparing database '%s' with '%s'", $this->dbDb0 , $this->emptyDbFile );
			$buffer[]=sprintf( "database user: %s", $this->dbUser.'@'.$this->dbHost );
			$buffer[]=sprintf( "automatically create test database: %s", $this->doNotCreateTempDatabase ? sprintf( "n (requires accessible test database '%s' to be present)",  $this->dbDb1 ) : 'y' );

			$buffer[]=sprintf( 'output file: %s', $this->outputFile );
			$buffer[]=sprintf( 'error file: %s', $this->errorFile );
			echo implode( "\n", $buffer ) , "\n";
		}

		private function dropTestDatabase()
		{
			if ( $this->doNotCreateTempDatabase )
			{
				echo "please drop test database manually\n";
				return;
			}

			echo "dropping test database\n";

			if ( !mysqli_query( $this->conn1, 'DROP DATABASE `' . $this->dbDb1 . '`' ) )
			{
				die( sprintf( 'abnormal program termination: failed to drop old test database %s',  $this->dbDb1 ) );
			}
		}

		private function initTestDatabase()
		{
			if ( $this->doNotCreateTempDatabase )
			{
				echo "using existing test database\n";
				mysqli_select_db( $this->conn1, $this->dbDb1 ) or die( sprintf( "abnormal program termination: could not select database '%s'\n", $this->dbDb1 ) );
				return;
			}

			echo "setting up test database\n";

			if ( mysqli_select_db( $this->conn1, $this->dbDb1 ) )
			{
				$this->dropTestDatabase();
			}

			if ( !mysqli_query( $this->conn1, 'CREATE DATABASE `' . $this->dbDb1 . '`' ) )
			{
				die( sprintf( "abnormal program termination: could not create test database '%s'\n",  $this->dbDb1 ) );
			}

			mysqli_select_db( $this->conn1, $this->dbDb1 ) or die( sprintf( "abnormal program termination: could not select database '%s'\n", $this->dbDb1 ) );
		}

		private function createTestTables()
		{
			
			$raw = file_get_contents( $this->emptyDbFile );
			$delimiter = ";";

			if (stripos($raw,"DELIMITER ")!==false)
			{
				$parts = preg_split('/DELIMITER(.*)/i',"/* auto dummy */\n" . $raw,0,PREG_SPLIT_DELIM_CAPTURE);
				
				foreach($parts as $key=>$part)
				{
					if ($key%2==1)
					{
						$delimiter = trim($part);
						mysqli_query( $this->conn1, "delimiter " .$delimiter );
					}
					else
					{
						foreach ( explode( $delimiter, $part ) as $stmnt )
						{
							if (strlen(trim($stmnt))==0) continue;

							if ( mysqli_query( $this->conn1, $stmnt ) !=1 )
							{
								print_r( mysqli_error( $this->conn1 ) );
								$this->errors[]=$stmnt . ( substr(trim($stmnt),-1)!=$delimiter ? $delimiter : "" );
							}			
						}
					}
				}
			}
			else
			{
				foreach ( explode( ";", $raw ) as $stmnt )
				{
					if (strlen(trim($stmnt))==0) continue;

					if ( mysqli_query( $this->conn1, $stmnt ) !=1 )
					{
						print_r( mysqli_error( $this->conn1 ) );
						$this->errors[]=$stmnt . ( substr(trim($stmnt),-1)!=$delimiter ? $delimiter : "" );
					}
				}
			}			
		}

		private function compareTables()
		{
			echo "comparing tables\n";

			mysqli_select_db( $this->conn0, 'information_schema' );
			mysqli_select_db( $this->conn1, 'information_schema' );

			$sourceTables = $this->getTables( $this->dbDb0, $this->conn0 );
			foreach($sourceTables as $table)
			{
				// Only present in source; drop table
				if ( $this->compareColumns( $table  ) === false)
				{
					$this->queries['Tables dropped'][] = "DROP TABLE `$table`;\n";
				}
			}

			// Iterate over left-over target tables
			$targetTables = $this->getTables( $this->dbDb1, $this->conn1 );
			$orphansInTarget = array_diff($targetTables, $sourceTables);

			foreach($orphansInTarget as $table)
			{
				$this->queries['Tables created'][] = $this->printCreateTable($table);
			}
		}

		private function getTables( $db, $conn )
		{
			$tables = array();

			if ( $result=mysqli_query( $conn, "SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA='$db'" ) )
			{
				while( $obj=$result->fetch_object() )
				{
					$tables[] = $this->dbDb0tablePrefix . $obj->TABLE_NAME;
				}
			}
			return $tables;
		}

		// Compare all columns in a pair of equally named tables
		// in the source and target database. As a side effect
		// this function finds out whether the target table exists
		// in the first place. Returns true if the target table
		// exists, false otherwise.
		private function compareColumns( $table )
		{
			$targetColumns = $this->getColumns( str_replace($this->dbDb0tablePrefix, '', $table), $this->dbDb1, $this->conn1);

			if ($targetColumns === false)
			{
				return false;
			}
			$sourceColumns = $this->getColumns($table, $this->dbDb0, $this->conn0);

			$diffCount = $this->displayOrphanColumns($sourceColumns, $targetColumns, $this->dbDb0, 'source');
			$diffCount += $this->displayOrphanColumns($targetColumns, $sourceColumns, $this->dbDb1, 'target');

			// Index target column definitions by name, so we can quickly
			// retrieve them while iterating over the source columns.
			$index = array();
			foreach($targetColumns as $col) {
				$index[$col['COLUMN_NAME']] = $col;
			}

			foreach($sourceColumns as $sourceColumn) {
				$name = $sourceColumn['COLUMN_NAME'];
				if (!isset($index[$name])) {
					continue;
				}
				$targetColumn = $index[$name];
				$diffCount += $this->displayColumnDifferences($sourceColumn, $targetColumn);
			}

			return true;
		}

		private function getColumns($table, $db, $conn)
		{
			mysqli_select_db( $conn, 'information_schema' );
			$columns = array();

			if ( $result=mysqli_query( $conn, "SELECT * FROM COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='{$table}'" ) )
			{
				while( $row=$result->fetch_array(MYSQLI_ASSOC) )
				{
					$columns[] = $row;
				}
			}

			return count((array)$columns)==0 ? false : $columns;
		}

		private function displayOrphanColumns($columns0, $columns1, $dbName, $dbType)
		{
			$orphans = array_udiff($columns0, $columns1, function ($col0, $col1)
			{
				return strcmp($col0['COLUMN_NAME'], $col1['COLUMN_NAME']);
			});

			foreach ($orphans as $k => $orphan)
			{
				// Add column
				if ($orphan['TABLE_SCHEMA'] != $this->dbDb0)
				{
					$this->queries['Columns added'][] = $this->printUpdateTable($orphan, 'create');
				}
				// Drop column
				else
				{
					$this->queries['Columns dropped'][] = 'ALTER TABLE `' . $orphans[$k]['TABLE_NAME'] .
						'` DROP COLUMN `' . $orphans[$k]['COLUMN_NAME'] . "`;\n";
				}
			}
			return count($orphans);
		}

		private function printUpdateTable($definition, $action = 'update')
		{
			$table = $this->dbDb0tablePrefix . $definition['TABLE_NAME'];
			$col = $definition['COLUMN_NAME'];

			// Exception for updating existing table with NOT NULL DATETIME column
			if ($action != 'update' && strtolower($definition['DATA_TYPE']) == 'datetime' &&
				$definition['IS_NULLABLE'] == 'NO') {
				return "ALTER TABLE `$table` ADD `created` DATETIME NOT NULL DEFAULT '" .
					date('Y-m-d H:i:s') . "';\n";
			}

			$output = "ALTER TABLE `$table` " .
			   ($action == 'update' ?
				   "CHANGE `$col` `$col` " :
				   "ADD `$col` ");

			$output .= $definition['COLUMN_TYPE'];

			/*
			// ENUM exception
			if (strtolower($definition['DATA_TYPE']) == 'enum') {
				$output .= $definition['COLUMN_TYPE'];
			} else {
				$output .= (strtoupper($definition['DATA_TYPE']) .
					(!empty($definition['CHARACTER_MAXIMUM_LENGTH']) ?
						' (' . $definition['CHARACTER_MAXIMUM_LENGTH'] . ')' :
						'') .
					(!empty($definition['NUMERIC_PRECISION']) ?
						' (' . ($definition['NUMERIC_PRECISION'] + 1) . ')' :
						''));
			}
			*/
			$output .= ' ' .
				(strtolower($definition['IS_NULLABLE']) == 'no' ?
					' NOT NULL' :
					'') .
				(strtolower($definition['EXTRA']) == 'auto_increment' ?
					' AUTO_INCREMENT' :
					'');
			$default =  $this->setDefault($definition['COLUMN_DEFAULT']);
			if (strtolower($definition['IS_NULLABLE']) == 'no' && $default == 'NULL') {
				return $output . ";\n";
			} else if (strtolower($definition['IS_NULLABLE']) == 'yes' && $default == 'NULL') {
				return $output . "DEFAULT NULL;\n";
			}
			return $output . ' DEFAULT ' . (strtolower($definition['COLUMN_DEFAULT']) == 'current_timestamp' ?
				"CURRENT_TIMESTAMP;\n" :
				$this->setDefault($definition['COLUMN_DEFAULT']) . ";\n");
		}

		private function setDefault($s)
		{
			if (is_null($s)) {
				return 'NULL';
			} else if (is_numeric($s)) {
				return $s;
			} else {
				return "'$s'";
			}
		}

		// Display column definition differences between two columns.
		// Returns 0 if no differences were found, 1 otherwise.
		private function displayColumnDifferences($col0, $col1)
		{
			// There a problem with [EXTRA] => 'on update CURRENT_TIMESTAMP' in combination with
			// [COLUMN_DEFAULT] => 'CURRENT_TIMESTAMP'. This is not set properly with the
			// resulting ALTER TABLE statement. Workaround is to manually reset this in the
			// comparison database...
			if (strtolower($col0['COLUMN_DEFAULT']) == 'current_timestamp' &&
				strtolower($col1['COLUMN_DEFAULT']) == 'current_timestamp' &&
				empty($col0['EXTRA']) &&
				strpos(strtolower($col1['EXTRA']), 'current_timestamp') !== false) {
				unset($col0['EXTRA'], $col1['EXTRA']);
			}

			$attrs = array_keys(array_diff_assoc($col0, $col1));
			$attrs = array_diff($attrs, $this->ignoredAttributes);
			if (count($attrs) === 0) {
				return 0;
			}

			$attributeTypes = array('COLUMN_DEFAULT', 'IS_NULLABLE', 'NUMERIC_SCALE', 'COLUMN_TYPE', 'EXTRA');

			foreach ($attrs as $attr) {
				// Index has changed
				if ($attr == 'COLUMN_KEY') {
					if (!in_array($col1['TABLE_NAME'], $this->droppedIndices)) {
						$this->queries['Indices updated'][] = $this->printDeleteKeys($col1['TABLE_NAME']) .
							$this->printCreateKeys($col1['TABLE_NAME']);
						$this->droppedIndices[] = $col1['TABLE_NAME'];
					}
				// Column definition has changed
				} else if (in_array($attr, $attributeTypes)) {
					$q = $this->printUpdateTable($col1);
					if (!isset($this->queries['Columns updated']) || !in_array($q, $this->queries['Columns updated'])) {
						$this->queries['Columns updated'][] = $q;
					}
				} else {
					echo "Cannot proceed; attribute $attr not parsed yet\n\n";
					die(print_r($col1));
				}
			}
			return 1;

		}

		private function printDeleteKeys( $table )
		{
			$table = $this->dbDb0tablePrefix . $table;
			mysqli_select_db( $this->conn0, $this->dbDb0 );

			if ( $result=mysqli_query( $this->conn0,  "SHOW INDEX FROM `$table`" ) )
			{
				while( $row=$result->fetch_array(MYSQLI_ASSOC) )
				{
					$keys[$row['Key_name']] = '';
				}

				if ( !isset($keys) ) return false;

				$output = '';
				//echo $table; print_r($keys);
				foreach ($keys as $k => $v) {
					// Skip PRIMARY
					if (strtolower($k) !== 'primary') {
						$output .= "ALTER TABLE `$table` DROP INDEX `$k`;\n";
					}
				}
				return $output;
			}
		}

		private function printCreateKeys( $table )
		{
			global $conn1, $db1, $tablePrefix;

			mysqli_select_db( $this->conn1, $this->dbDb1 );

			if ( $result=mysqli_query( $this->conn1,  "SHOW INDEX FROM `$table`" ) )
			{
				while( $row=$result->fetch_array(MYSQLI_ASSOC) )
				{
					$keys[$row['Key_name']]['column'][] = '`' . $row['Column_name'] . '`' .
					  (!empty($row['Sub_part']) ? ' (' . $row['Sub_part'] . ')' : '');
					$type = '';
					if ($row['Key_name'] == 'PRIMARY') {
						$type = 'PRIMARY';
						continue;
					} else if ($row['Index_type'] == 'FULLTEXT') {
						$type = $row['Index_type'];
					}
					$keys[$row['Key_name']]['type'] = $type;
				}

				if ( !isset($keys) ) return false;

				$output = '';

				foreach ($keys as $k => $v) {
					if ($k == 'PRIMARY') {
						// Skip PRIMARY
						// $output .= "ALTER TABLE `$table` ADD PRIMARY KEY (" . implode(', ', $v['column']) . ");\n";
						continue;
					} else {
						$output .= "ALTER TABLE `{$this->dbDb0tablePrefix}{$table}` ADD " . (!empty($v['type']) ? $v['type'] . ' ' : '') .
							"KEY `$k` (" . implode(', ', $v['column']) . ");\n";
					}
				}
				return $output;

			}

		}

		private function setRegex( $startTag, $endTag )
		{
			$delimiter = '/';
			return $delimiter . preg_quote($startTag, $delimiter) . '(.*?)' .
				preg_quote($endTag, $delimiter) . $delimiter . 's';
		}

		private function printCreateTable($table)
		{
			$table = str_replace($this->dbDb0tablePrefix, '', $table);
			$emptyDatabaseStatements = file_get_contents($this->emptyDbFile);

			foreach (['CREATE TABLE IF NOT EXISTS','CREATE TABLE'] as $create)
			{
				preg_match($this->setRegex("$create `$table`", ';'), $emptyDatabaseStatements, $matches);

				if (isset($matches[0]))
				{
					return str_replace("`$table`", "`{$this->dbDb0tablePrefix}{$table}`", $matches[0]) . "\n";
				}
			}
		}

		private function writeQueries()
		{
			function sortArrayByArray ($array, $orderArray)
			{
				$ordered = array();
				foreach ($orderArray as $key)
				{
					if (array_key_exists($key,$array))
					{
						$ordered[$key] = $array[$key];
						unset($array[$key]);
					}
				}
				return $ordered + $array;
			}

			// There are differences: list these plus base-data, which may contain updates
			if ( !empty($this->queries) )
			{
				$this->queries = sortArrayByArray($this->queries, $this->printOrder);

				$fp=fopen( $this->outputFile, 'w' );

				if ( $fp )
				{
					echo sprintf( "writing queries to '%s'\n", $this->outputFile );

					if (!empty($this->preflightQueries)) {
						fwrite( $fp, "#Pre-flight queries: " . count($this->preflightQueries) . "\n" );
					    foreach ($this->preflightQueries as $query) {
                            fwrite( $fp, $query ."\n" );
                        }
						fwrite( $fp, "\n\n" );
					}

					foreach ($this->queries as $type => $list)
					{
						fwrite( $fp, "#$type: " . count($this->queries[$type]) . "\n" );
						foreach ($list as $query)
						{
							fwrite( $fp, $query ."\n" );
						}
						fwrite( $fp, "\n\n" );
					}

					if (!empty($this->postflightQueries)) {
						fwrite( $fp, "#Post-flight queries: " . count($this->postflightQueries) . "\n" );
					    foreach ($this->postflightQueries as $query) {
                            fwrite( $fp, $query ."\n" );
                        }
						fwrite( $fp, "\n\n" );
					}

					fclose( $fp );
				}
				else
				{
					die( sprintf( "abnormal program termnination: could not open '%s' for writing", $this->outputFile ) );
				}
			}
			else
			{
				echo "databases are the same (no queries written)\n";
			}
		}

		private function writeErrors()
		{
			if ( !empty($this->errors) )
			{
				$fp=fopen( $this->errorFile, 'w' );

				if ( $fp )
				{
					echo sprintf( "writing loading errors to '%s'\n(these are probably stored procedures; be sure to check these by hand)\n", $this->errorFile );
					foreach ($this->errors as $error)
					{
						fwrite( $fp, $error ."\n" );
					}
					fclose( $fp );
				}
				else
				{
					die( sprintf( "abnormal program termnination: could not open '%s' for writing", $this->errorFile ) );
				}
			}
		}

		private function copyOutputFilesToGeneral()
		{
			if ( !empty($this->queries) )
			{
				echo sprintf( "copying to general output file '%s'", $this->generalOutputFile ) ;
				copy($this->outputFile,$this->generalOutputFile);
			}
			if ( !empty($this->errors) ) 
			{
				echo sprintf( "copying to general error file '%s'", $this->generalErrorFile ) ;
				copy($this->errorFile,$this->generalErrorFile);
			}
		}

		private function finish()
		{
			mysqli_close( $this->conn0 );
			mysqli_close( $this->conn1 );
			echo "done\n\n";
		}

	}



	$compare = new DatabaseTableCompare;

	/* Linnaeus server settings */

	/*
	// example configuration statments:
	//$compare->setConstFile( 'C:\www\linnaeus_ng\configuration\admin\constants.php' );
	//$compare->setCfgFile( 'C:\www\linnaeus_ng\configuration\admin\configuration.php' );
	//$compare->setEmptyDbFile( 'C:\www\linnaeus_ng\database\empty_database.sql' );
	$compare->setOutputFile( '/home/maarten.schermer/testdata/%s-modify-%s.sql' );
	//$compare->setDbUserOverride( ['user'=>'root','password'=>'secret','host'=>'localhost' ] );
	$compare->setDoNotCreateTempDatabase( true );
	$compare->setPreflightQueries(array(
        'ALTER TABLE `literature2` DROP INDEX `project_id`;'
	));
	$compare->setPostflightQueries(array(
        'ALTER TABLE `literature2` ADD KEY `project_id` (`project_id`, `label`(250));'
	));

	*/

	$compare->setConstFile(dirname(__FILE__) . '/../configuration/admin/constants.php');
	$compare->setCfgFile(dirname(__FILE__) . '/../configuration/admin/configuration.php');
	$compare->setEmptyDbFile(dirname(__FILE__) . '/../database/empty_database.sql');
	$compare->setOutputFile(dirname(__FILE__) . '/output/%s-modify-%s.sql');

	$compare->setGeneralOutputFile(dirname(__FILE__) . '/output/latest-modify.sql');
	$compare->setGeneralErrorFile(dirname(__FILE__) . '/output/latest-errors.txt');

	$compare->setDoNotCreateTempDatabase(true);
	$compare->setPreflightQueries(array(
        //'ALTER TABLE `literature2` DROP INDEX `project_id`;'
        "SET sql_mode = '';"
	));
	$compare->setPostflightQueries(array(
        //'ALTER TABLE `literature2` ADD KEY `project_id` (`project_id`, `label`(250));'
	));
	$compare->run();



