<?php
	// Output is utf8
	header('Content-type: text/html; charset=UTF-8');

	// A few script settings...
	// $cfg = 'configuration/admin/configuration.php';
	// $dumpPath = 'database/empty-database.sql';
	// $mysqlPath = 'mysql';
	$cfg = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/configuration/admin/configuration.php';
	$path = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/database/';
	$dumpPath = $path . 'empty-database.sql';
	$emptyDatabaseFile = file_get_contents($dumpPath);
    $mysqlPath = '/Applications/MAMP/Library/bin/mysql';
    $includeBaseDataUpdate = false;

	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();

    // Includes modified version of Ayco Holleman's mysqldiff utility
	define('COLUMN_NAME', 'COLUMN_NAME');

	// User needs credentials to CREATE and DROP database
	$host0 = $s['host'];
	$user0 = $s['user'];
	$pw0 = $s['password'];

	// Both databases will run on the same server
	$host1 = $host0;
	$user1 = $user0;
	$pw1 = $pw0;

	$db0 = $s['database'];
	// Better use name that's unlikely to already exist...
	$db1 = 'linnaeus_ng_diff_test_HENKIEBOY';

	$showDetails = false;
	$ignoreComments = true;
	$ignoreCharset = true;

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
	$ignoredAttributes = array(
		COLUMN_NAME,
		'TABLE_NAME',
		'TABLE_SCHEMA',
		'TABLE_CATALOG',
		'ORDINAL_POSITION',
		'CHARACTER_MAXIMUM_LENGTH',
		'CHARACTER_OCTET_LENGTH',
		'DATA_TYPE',
		'NUMERIC_PRECISION'
	);
	if($ignoreComments) {
		$ignoredAttributes[] = 'COLUMN_COMMENT';
	}
	if($ignoreCharset) {
		$ignoredAttributes[] = 'CHARACTER_SET_NAME';
		$ignoredAttributes[] = 'COLLATION_NAME';
	}

	// Used to reorder queries
	$printOrder = array(
	    'Tables created',
        'Tables dropped',
	    'Columns added',
        'Columns updated',
	    'Indices updated'
	);
	// Keep track of database with dropped/recreated indices
    $droppedIndices = array();
    // Table prefix into account (only for source)
    $tablePrefix = $s['tablePrefix'];


	// Let's go
	echo 'This script compares the current Linnaeus NG database ' . $db0.
	   " to the latest version in git and lists the differences between the two.\n\n";


	// Bootstrap
	$conn0 = @mysql_connect($host0, $user0, $pw0);
	$conn1 = @mysql_connect($host1, $user1, $pw1);
	// Check connections
	if (!$conn0 || !$conn1) {
	    die("Cannot connect to source and/or target database server");
	}
	// Can we access the dump file?
    if (!file_exists($dumpPath)) {
	    die('Cannot open dump file for latest database at ' . $dumpPath);
	}
	// Dump test database if it still exists from an aborted previous session
	if (mysql_select_db($db1, $conn1)) {
        if (!mysql_query('DROP DATABASE `' . $db1 . '`', $conn1)) {
            die("Cannot drop test database, please check MySQL credentials");;
        }
	}
    // Does the user have CREATE credentials?
    if (!mysql_query('CREATE DATABASE `' . $db1 . '`', $conn1)) {
        die("Cannot create test database, please check MySQL credentials\n");
    }
    $cmd = "$mysqlPath -h $host1 -u $user1 --password='$pw1' $db1 < '$dumpPath'";
    // Can we import the dump via the command line?
    exec($cmd, $m, $s);
    if ($s !== 0) {
        die('Cannot import the dump file through exec(); check $mysqlPath variable');
    }



	// Disabe MySQL STRICT mode if this has been set
	$res = mysql_query("SELECT @@GLOBAL.sql_mode;", $conn0);
    $sqlMode = mysql_result($res, 0, 0);
    if ($sqlMode !== '') {
        echo "# First disable MySQL STRICT mode\nSET GLOBAL sql_mode = '';\n\n";
    }

    compareDatabases();

    // There are differences: list these plus base-data, which may contain updates
    if (!empty($queries)) {
        $queries = sortArrayByArray($queries, $printOrder);
        foreach ($queries as $type => $list) {
            echo "#$type: " . count($queries[$type]) . "\n";
            foreach ($list as $query) {
                echo $query;
            }
            echo "\n\n";
        }
        if ($includeBaseDataUpdate) {
            echo "#Base data update:\n" . file_get_contents($path . 'base-data.sql');
        }
    // Same
    } else {
        echo "Database is up-to-date!\n";
    }


    mysql_query('DROP DATABASE `' . $db1 . '`', $conn1);

    // Reset MySQL STRICT mode to original setting
    if ($sqlMode !== '') {
        echo "# Re-enable MySQL STRICT mode\nSET GLOBAL sql_mode = '$sqlMode';\n\n";
    }

	function getColumns($table, $db, $conn)
	{
		mysql_select_db('information_schema', $conn);
	    $table = mysql_real_escape_string($table, $conn);
		$db = mysql_real_escape_string($db, $conn);
		$sql = "SELECT * FROM COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='{$table}'";
		$res = mysql_query($sql, $conn);
		if (mysql_num_rows($res) === 0) {
			return false;
		}
		$columns = array();
		while (($row = mysql_fetch_assoc($res)) !== false) {
			$columns[] = $row;
		}
		return $columns;
	}


	function getTables($db, $conn, $tablePrefix = '')
	{
	    $sql = "SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA='$db'";
		$res = mysql_query($sql, $conn);
		$tables = array();
		while ( ($row = mysql_fetch_row($res)) !== FALSE ) {
			$tables[] = $tablePrefix . $row[0];
		}
		return $tables;
	}


	function databaseExists($db, $conn)
	{
		$db = mysql_real_escape_string($db, $conn);
		$sql = "SELECT 1 FROM SCHEMATA WHERE SCHEMA_NAME='$db'";
		return mysql_num_rows(mysql_query($sql, $conn)) === 1;
	}


	function displayOrphanColumns($columns0, $columns1, $dbName, $dbType)
	{
		global $queries;
		$orphans = array_udiff($columns0, $columns1, function ($col0, $col1) {
			return strcmp($col0[COLUMN_NAME], $col1[COLUMN_NAME]);
		});
		foreach ($orphans as $k => $orphan) {
			// Add column
            if (count($columns1) > count($columns0)) {
                $queries['Columns added'][] = printUpdateTable($orphan, 'create');
            // Drop column
            } else {
                $queries['Columns dropped'][] = 'ALTER TABLE `' . $orphans[$k]['TABLE_NAME'] .
                    '` DROP COLUMN `' . $orphans[$k]['COLUMN_NAME'] . "`;\n";
            }
		}
		return count($orphans);
	}

	// Display column definition differences between two columns.
	// Returns 0 if no differences were found, 1 otherwise.
	function displayColumnDifferences($col0, $col1)
	{
		global $showDetails, $ignoredAttributes, $queries, $droppedIndices;

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
		$attrs = array_diff($attrs, $ignoredAttributes);
		if (count($attrs) === 0) {
			return 0;
		}

		/*
		$format = '<p class="column-differences">Column <em>%s</em> has different definitions in source and target</p>';
		echo sprintf($format, $col0[COLUMN_NAME]);
		echo '<table class="column-differences"><thead><tr><th>Column Attribute</th><th>Source</th><th>Target</th></tr></thead><tbody>';
		$format = '<tr><td>%s</td><td>%s</td><td>%s</td></tr>';
		*/

		$attributeTypes = array('COLUMN_DEFAULT', 'IS_NULLABLE', 'NUMERIC_SCALE', 'COLUMN_TYPE', 'EXTRA');

		foreach ($attrs as $attr) {
		    // Index has changed
			if ($attr == 'COLUMN_KEY') {
			    if (!in_array($col1['TABLE_NAME'], $droppedIndices)) {
                    $queries['Indices updated'][] = printDeleteKeys($col1['TABLE_NAME']) .
                        printCreateKeys($col1['TABLE_NAME']);
                    $droppedIndices[] = $col1['TABLE_NAME'];
			    }
			// Column definition has changed
			} else if (in_array($attr, $attributeTypes)) {
                $q = printUpdateTable($col1);
                if (!isset($queries['Columns updated']) || !in_array($q, $queries['Columns updated'])) {
                    $queries['Columns updated'][] = $q;
                }
			} else {
			    echo "Cannot proceed; attribute $attr not parsed yet\n\n";
			    die(print_r($col1));
			}
		}
		return 1;

	}

	function printUpdateTable ($definition, $action = 'update') {
		global $tablePrefix;
		// Make life a little easier
		$table = $tablePrefix . $definition['TABLE_NAME'];
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
	           "ADD `$col` ") .
	        strtoupper($definition['DATA_TYPE']) .
            (!empty($definition['CHARACTER_MAXIMUM_LENGTH']) ?
                ' (' . $definition['CHARACTER_MAXIMUM_LENGTH'] . ')' :
                '') .
            (!empty($definition['NUMERIC_PRECISION']) ?
                ' (' . ($definition['NUMERIC_PRECISION'] + 1) . ')' :
                '') .
            ' ' .
            (strtolower($definition['IS_NULLABLE']) == 'no' ?
                ' NOT NULL' :
                '') .
            (strtolower($definition['EXTRA']) == 'auto_increment' ?
                ' AUTO_INCREMENT' :
                '')
	        ;
	    $default =  setDefault($definition['COLUMN_DEFAULT']);
        if (strtolower($definition['IS_NULLABLE']) == 'no' && $default == 'NULL') {
            return $output . ";\n";
        } else if (strtolower($definition['IS_NULLABLE']) == 'yes' && $default == 'NULL') {
            return $output . "DEFAULT NULL;\n";
        }
        return $output . ' DEFAULT ' . (strtolower($definition['COLUMN_DEFAULT']) == 'current_timestamp' ?
            "CURRENT_TIMESTAMP;\n" :
            setDefault($definition['COLUMN_DEFAULT']) . ";\n");
	}

	// Compare all columns in a pair of equally named tables
	// in the source and target database. As a side effect
	// this function finds out whether the target table exists
	// in the first place. Returns true if the target table
	// exists, false otherwise.
	function compareColumns($table)
	{
		global $conn0, $conn1, $db0, $db1, $tablePrefix;

		$targetColumns = getColumns(str_replace($tablePrefix, '', $table), $db1, $conn1);
		if ($targetColumns === false) {
			return false;
		}
		$sourceColumns = getColumns($table, $db0, $conn0);

		$diffCount = displayOrphanColumns($sourceColumns, $targetColumns, $db0, 'source');
		$diffCount += displayOrphanColumns($targetColumns, $sourceColumns, $db1, 'target');

		// Index target column definitions by name, so we can quickly
		// retrieve them while iterating over the source columns.
		$index = array();
		foreach($targetColumns as $col) {
			$index[$col[COLUMN_NAME]] = $col;
		}

		foreach($sourceColumns as $sourceColumn) {
			$name = $sourceColumn[COLUMN_NAME];
			if (!isset($index[$name])) {
				continue;
			}
			$targetColumn = $index[$name];
			$diffCount += displayColumnDifferences($sourceColumn, $targetColumn);
		}

		return true;
	}


	function compareTables()
	{
		global $conn0, $conn1, $db0, $db1, $tablePrefix, $queries;
		// Iterate over source tables
		$sourceTables = getTables($db0, $conn0);
		foreach($sourceTables as $table) {
			// Only present in source; drop table
			if (compareColumns($table) === false) {
                $queries['Tables dropped'][] = "DROP TABLE `$table`;\n";
			}
		}
		// Iterate over left-over target tables
		$targetTables = getTables($db1, $conn1, $tablePrefix);
		$orphansInTarget = array_diff($targetTables, $sourceTables);
		foreach($orphansInTarget as $table) {
		    $queries['Tables created'][] = printCreateTable($table);
		}
	}


	function compareDatabases()
	{
		global $conn0, $conn1, $db0, $db1;

		if ($conn0 === false) {
			echo '<p class="fatal">Could not connect to source database</p>';
			return;
		}

		if ($conn1 === false) {
			echo '<p class="fatal">Could not connect to target database</p>';
			return;
		}

		mysql_select_db('information_schema', $conn0);
		mysql_select_db('information_schema', $conn1);

		if($db0 === '') {
			echo '<p class="fatal">Please specify a source database</p>';
			return;
		}

		if($db1 === '') {
			echo '<p class="fatal">Please specify a target database</p>';
			return;
		}

		if (! databaseExists($db0, $conn0)) {
			echo '<p class="fatal">Source database does not exist: ' . $db0 . '</p>';
			return;
		}
		if (! databaseExists($db1, $conn1)) {
			echo '<p class="fatal">Target database does not exist: ' . $db1 . '</p>';
			return;
		}

		compareTables();
	}

	function printDeleteKeys ($table)
	{
		global $conn0, $db0, $tablePrefix;
		$table = $tablePrefix . $table;
		mysql_select_db($db0, $conn0);
		$sql = "SHOW INDEX FROM `$table`";
		$res = mysql_query($sql, $conn0);
		if (mysql_num_rows($res) === 0) {
			return false;
		}
		while (($row = mysql_fetch_assoc($res)) !== FALSE) {
			$keys[$row['Key_name']] = '';
		}
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

	function printCreateKeys ($table)
	{
		global $conn1, $db1, $tablePrefix;
		mysql_select_db($db1, $conn1);
		$sql = "SHOW INDEX FROM `$table`";
		$res = mysql_query($sql, $conn1);
		if (mysql_num_rows($res) === 0) {
			return false;
		}
		$output = '';
		while (($row = mysql_fetch_assoc($res)) !== FALSE) {
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
		foreach ($keys as $k => $v) {
		    if ($k == 'PRIMARY') {
		        // Skip PRIMARY
                // $output .= "ALTER TABLE `$table` ADD PRIMARY KEY (" . implode(', ', $v['column']) . ");\n";
		        continue;
		    } else {
                $output .= "ALTER TABLE `{$tablePrefix}{$table}` ADD " . (!empty($v['type']) ? $v['type'] . ' ' : '') .
                    "KEY `$k` (" . implode(', ', $v['column']) . ");\n";
		    }
		}
		return $output;
	}

	function setDefault ($s) {
	    if (is_null($s)) {
            return 'NULL';
	    } else if (is_numeric($s)) {
            return $s;
	    } else {
            return "'$s'";
	    }
	}

	function printCreateTable ($table) {
        global $dumpPath, $emptyDatabaseFile, $tablePrefix;
        $table = str_replace($tablePrefix, '', $table);
        $statements = array(
            'CREATE TABLE IF NOT EXISTS',
            'CREATE TABLE'
        );
        foreach ($statements as $create) {
            preg_match(setRegex("$create `$table`", ';'), $emptyDatabaseFile, $matches);
            if (isset($matches[0])) {
                return str_replace("`$table`", "`{$tablePrefix}{$table}`", $matches[0]) . "\n";
            }
        }
	}

	function setRegex ($startTag, $endTag) {
	    $delimiter = '/';
        return $delimiter . preg_quote($startTag, $delimiter) . '(.*?)' .
            preg_quote($endTag, $delimiter) . $delimiter . 's';
	}

    function sortArrayByArray ($array, $orderArray) {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists($key,$array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }

	//compareDatabases();
?>