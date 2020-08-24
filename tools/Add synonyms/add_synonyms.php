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

 	$d = mysqli_connect($s['host'],$s['user'],$s['password']) or die ('Cannot connect to '.$s['host']);
	mysqli_select_db($d, $s['database']) or die ('Cannot select database '.$s['database']);
	mysqli_set_charset($d, 'utf8');
	
	// Delete old records if present
	$delete = 'DELETE FROM `' . $s['tablePrefix'] . 'synonyms` WHERE `project_id` = ' . $settings['projectId'];
	mysqli_query($d, $delete) or die(mysqli_error($d));
	
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
		global $settings, $s, $d;
		$query = 'SELECT `id` FROM `' . $s['tablePrefix'] . 'taxa` ' .
			' WHERE `taxon` = "' . mysqli_real_escape_string($d, $name) . 
			'" AND `project_id` = ' . $settings['projectId'];
		$result = mysqli_query($d, $query) or die(mysqli_error($d));
		$row = mysqli_fetch_row($d, $result);
		return $row[0];
    }
    
    function insertSynonym ($taxonId, $synonym)
    {
		global $settings, $s, $d;
		$query = 'INSERT INTO `' . $s['tablePrefix'] . 'synonyms` ' .
		    '(`project_id`, `taxon_id`, `synonym`, `created`) VALUES (' .
			mysqli_real_escape_string($d, settings['projectId']) . ', ' .
			mysqli_real_escape_string($d, taxonId) . ', "' .
			mysqli_real_escape_string($d, synonym) . '", NOW())';
		$result = mysqli_query($d, $query) or die(mysqli_error($d));
    }
    


?>
</body>
</html>
