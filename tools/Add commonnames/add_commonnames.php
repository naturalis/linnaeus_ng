<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Add common names</title>
</head>
<body style="font: 12px Verdana; width: 800px;">

<?php
    // Modify project settings
    $settings = array(
        'text_file' => 'dagvlinder_commonnames.txt',
   		'projectId' => 244,
		'delete_existing' => true,
		'default_language_id' => 24 // dutch:24, english: 26, french: 31, german: 36, spanish: 99 (table dev_languages)
    );
    
	// A few script settings...
//	$cfg = 'configuration/admin/configuration.php';
	$cfg = 'C:/Program Files/wamp/www/linnaeus_ng/configuration/admin/configuration.php';

	// Get external settings
	if (!file_exists($cfg)) die("Unable to locate $cfg. This script should be in the root of a Linnaeus NG-installation");
	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();
	
	echo '<h3>Add common names</h3><p style="margin-bottom: 30px;">This script adds common names. The common names should be stored in a tab-delimited text file,
        in the format<br />
			<code>valid name - tab - synonym - tab - language code</code><br />
		Language codes are optional and should correspond to codes in the dev_languages table (Dutch: 24, English: 26, French: 31, German: 36, Spanish: 99). If no code is supplied,
		languagecode = '.$settings['default_language_id'].' is assumed. Make sure the file is saved with Unix line breaks (can be done in BBEdit).</p><p>';

 	$d = mysql_connect($s['host'],$s['user'],$s['password']) or die ('Cannot connect to '.$s['host']);
	mysql_select_db($s['database'],$d) or die ('Cannot select database '.$s['database']);
	mysql_set_charset('utf8', $d);
	
	if ($settings['delete_existing']) {
		// Delete old records if present
		$delete = 'DELETE FROM `' . $s['tablePrefix'] . 'commonnames` WHERE `project_id` = ' . $settings['projectId'];
		mysql_query($delete) or die(mysql_error());
	}
	
	$file = dirname(__FILE__) . '/' . $settings['text_file'];
	if (!file_exists($file)) die('Unable to read ' .$settings['text_file']);
	$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	foreach ($lines as $line){
        $dummy = explode("\t", $line);
		$taxon = getTaxonId($dummy[0]);
		if (empty($taxon))
			echo 'ERROR: could not resolve name: "'.$dummy[0].'"</br>';
		else
			insertCommonname ($taxon, $dummy[1], isset($dummy[2]) ? $dummy[2] : $settings['default_language_id']);
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
    
    function insertCommonname ($taxonId, $commonname, $languageId)
    {
		global $settings, $s;
		$query = 'INSERT INTO `' . $s['tablePrefix'] . 'commonnames` ' .
		    '(`project_id`, `taxon_id`, `language_id`,  `commonname`, `created`) VALUES (' .
			mysql_real_escape_string($settings['projectId']) . ', ' .
			mysql_real_escape_string($taxonId) . ', ' .
			mysql_real_escape_string($languageId) . ', "' .
			mysql_real_escape_string($commonname) . '", NOW())';

		$result = mysql_query($query) or die(mysql_error());
		if($result===true)
			echo 'inserted common name "'.$commonname.'"</br>';
		else
			echo 'ERROR: could not insert commonname '.$commonname.'</br>';
    }
    


?>
</body>
</html>