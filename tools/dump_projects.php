<?php

	// Output is utf8
	header('Content-type: text/html; charset=UTF-8');
	// Disable timeout, as this may take a while...
	ini_set('max_execution_time', 3600);
	
	// A few script settings...
	$cfg = 'configuration/admin/configuration.php';
	$dumpPath = 'dumps/'; // add trailing slash!
//	$cfg = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/configuration/admin/configuration.php';
//	$dumpPath = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/dumps/'; // add trailing slash!

	// Get external settings
	if (!file_exists($cfg)) die("Unable to locate $cfg. This script should be in the root of a linnaeus NG-installation");
	if (!is_writable($dumpPath)) die('Cannot write to ' . $dumpPath);
	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();
	
	// Add a new insert statement after this many characters
	$maxLengthQuery = 50000;
	// Tables in which project id is labeled other than project_id (table name => column name)
	$exceptions = array(
		$s['tablePrefix'] . 'projects' => 'id'
	);
	// Fields that are dumped without quotes
	$numericTypes = array(
		'tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double'
	);
	// Fields that are dumped as text and converted back to geometries on import
	$geoTypes = array(
		'point', 'polygon', 'multipolygon', 'geometrycollection', 'geometry'
	);
	

	// Let's go
	echo 'This script dumps all projects in the '.$s['database'].' database to individual .sql files.' .
	     'These files can be used to restore individual projects from a backup.';

 	$d = mysqli_connect($s['host'],$s['user'],$s['password']) or die ('Cannot connect to '.$s['host']);
	mysqli_select_db($d, $s['database']) or die ('Cannot select database '.$s['database']);
	mysqli_set_charset($d, 'utf8');
	
	$projects = getProjects($s);
	$tables = getTables($s);
	foreach ($projects as $projectId => $projectName) {
		// Create project-specific dump files
		$dumpFile = $dumpPath . str_replace(' ', '_', strtolower($projectName)) . '.sql';
		$fp = fopen($dumpFile, 'wb');

		foreach ($tables as $table) {
			$columns = getColumns($s, $table);
			if (array_key_exists('project_id', $columns) || array_key_exists($table, $exceptions)) {
				$result = mysqli_query($d, constructQuery($table, $columns, $exceptions, $projectId)) or die(mysqli_error($result));
				$nrRows = mysqli_num_rows($d);
				if ($nrRows > 0) {
					$i = 0;
					$line = "--\n-- Dumping data for table `$table`\n--\n\n" . startDump($table, $columns);
					fwrite($fp, $line);
					$length = strlen($line);

					while ($row = mysqli_fetch_array($d, MYSQLI_ASSOC)) {
						$i++; 
						$line = '(';
						foreach ($row as $column => $value) {
							$line .= cleanValue($columns, $column, $value) . ', '; 
						}
						$line = substr($line, 0, -2) . ')';
						$length += strlen($line);
						if ($i < $nrRows && $length < $maxLengthQuery) {
							$line .=  ",\n";
						} else {
							$line .=  ";\n\n";
							// Renew insert statement after maximum length
							if ($i != $nrRows && $length >= $maxLengthQuery) {
								$line .= startDump($table, $columns);
								$length = 0;
							}
						}
						fwrite($fp, $line);
					}
				}
			}
			
		}
		fclose($fp);

		// Create delete files
		$dumpFile = $dumpPath . 'delete_' . str_replace(' ', '_', strtolower($projectName)) . '.sql';
		$fp = fopen($dumpFile, 'wb');
		
		foreach ($tables as $table) {
			$columns = getColumns($s, $table);
			if (array_key_exists('project_id', $columns)) {
				fwrite($fp, "DELETE FROM `$table` WHERE `project_id` = $projectId;\n");;
			} else if (array_key_exists($table, $exceptions)) {
				fwrite($fp, "DELETE FROM `$table` WHERE `" . $exceptions[$table] . "` = $projectId;\n");
			}
		}
		fclose($fp);
	}
	mysqli_close();

		
	function getProjects ($s)
	{
        global $d;

		$result = mysqli_query($d, 'SELECT `id`, `title` FROM `' . $s['tablePrefix'] . 'projects`');
		while ($row = mysqli_fetch_array($d, MYSQLI_NUM)) {
			$projects[$row[0]] = $row[1];
		}
		return $projects;
	}
	
	function getTables ($s)
	{
        global $d;

		$result = mysqli_query($d, 'SHOW TABLES');
		while ($row = mysqli_fetch_array($d, MYSQLI_NUM)) {
			$tables[] = $row[0];
		}
		return $tables;
	}

	function getColumns ($s, $table)
	{
        global $d;

		$result = mysqli_query($d, "SHOW COLUMNS FROM `$table`");
		while ($row = mysqli_fetch_array($d, MYSQLI_NUM)) {
			$columns[$row[0]] = setMysqlType($row[1]);
		}
		return $columns;
	}
	
	function constructQuery ($table, $columns, $exceptions, $projectId)
	{
		global $geoTypes;
		$query = 'SELECT ';
		foreach ($columns as $column => $type) {
			if (in_array($type, $geoTypes)) {
				$query .= "AsText(`$column`) AS $column, ";
			} else {
				$query .= "`$column`, ";
			}
		}
		$query = substr($query, 0, -2) . " FROM `$table` WHERE ";
		if (array_key_exists('project_id', $columns)) {
			$query .= '`project_id` = ' . $projectId;
		} else {
			$query .= '`' . $exceptions[$table] . '` = ' . $projectId;
		}
		return $query;
	}
	
	function startDump ($table, $columns) 
	{
		$line = "INSERT INTO `$table` (";
		foreach ($columns as $column => $type) {
			$line .= '`' . $column . '`, ';
		}
		return substr($line, 0, -2) . ") VALUES\n";
	}
	
	function cleanValue ($columns, $column, $value)
	{
		global $numericTypes, $geoTypes;
		$type = $columns[$column];
		if (is_null($value)) return 'NULL';
		if (in_array($type, $numericTypes)) return $value;
		if (in_array($type, $geoTypes)) return "GeomFromText('$value')";
		return "'" . str_replace("'", "''", $value) . "'";
	}
	
	function setMysqlType ($type)
	{
		$pos = strpos($type, '(');
		if ($pos !== false) {
			$type = substr($type, 0, $pos);
		}
		return strtolower($type);
	}
	
?>
