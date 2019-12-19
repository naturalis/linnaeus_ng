<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Add synonyms</title>
</head>
<body style="font: 12px Verdana; width: 800px;">

<?php
    // Modify project settings
    $settings = array(
        'text_file' => 'orchids_2.txt',
   		'projectId' => 107,
    );
    
	// A few script settings...
//	$cfg = 'configuration/admin/configuration.php';
	$cfg = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/configuration/admin/configuration.php';

	// Get external settings
	if (!file_exists($cfg)) die("Unable to locate $cfg. This script should be in the root of a linnaeus NG-installation");
	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();
	
	echo '<h3>Add synonyms</h3><p style="margin-bottom: 30px;">This script adds synonyms that got lost when a project
        had to be upgraded to Linnaeus II 2.6 before import. The synonyms should be stored in a tab-delimited text file,
        in the format valid name - tab - synonym. Make sure the file is saved with Unix line breaks (can be done in BBEdit).</p><p>';

 	$d = mysql_connect($s['host'],$s['user'],$s['password']) or die ('Cannot connect to '.$s['host']);
	mysql_select_db($s['database'],$d) or die ('Cannot select database '.$s['database']);
	mysql_set_charset('utf8', $d);
	
	// Delete old records if present
	$delete = 'DELETE FROM `' . $s['tablePrefix'] . 'synonyms` WHERE `project_id` = ' . $settings['projectId'];
	mysql_query($delete) or die(mysql_error());
	
	$file = dirname(__FILE__) . '/' . $settings['text_file'];
	if (!file_exists($file)) die('Unable to read ' .$settings['text_file']);
	$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	foreach ($lines as $line){
        list($name, $synonym) = explode("\t", $line);
        insertSynonym (getTaxonId($name), $synonym);
    }
    
    echo 'Done!</p>';
    
    function getTaxonId ($name)
    {
		global $settings, $s;
		$query = 'SELECT `id` FROM `' . $s['tablePrefix'] . 'taxa` ' .
			' WHERE `taxon` = "' . mysql_real_escape_string($name) . 
			'" AND `project_id` = ' . $settings['projectId'];
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_row($result);
		return $row[0];
    }
    
    function insertSynonym ($taxonId, $synonym)
    {
		global $settings, $s;
		$query = 'INSERT INTO `' . $s['tablePrefix'] . 'synonyms` ' .
		    '(`project_id`, `taxon_id`, `synonym`, `created`) VALUES (' .
			mysql_real_escape_string($settings['projectId']) . ', ' .
			mysql_real_escape_string($taxonId) . ', "' .
			mysql_real_escape_string($synonym) . '", NOW())';
		$result = mysql_query($query) or die(mysql_error());
    }
    


?>
</body>
</html>