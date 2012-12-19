<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Update authors</title>
</head>
<body style="font: 12px Verdana; width: 800px;">

<?php
    // Modify project settings
    $settings = array(
   		'projectId' => 597,
        'authorOnFirstLine' => true,
   		'linesToCheck' => 10, // relevant only if author is not on the first line
        'includeHigherTaxa' => true, // skip higher taxa
        'lineBreak' => '<br />', // default in L2 imports
    
        'species' => array(
            'preceededBy' => 'Author:', // text string, [taxon], false
            'followedBy' => false, // text string, [taxon], false
        ),
        'higherTaxa' => array(
            'preceededBy' => 'Author:', // text string, [taxon], false
            'followedBy' => false, // text string, [taxon], false
        )
    );
    
	// A few script settings...
//	$cfg = 'configuration/admin/configuration.php';
	$cfg = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/configuration/admin/configuration.php';

	// Get external settings
	if (!file_exists($cfg)) die("Unable to locate $cfg. This script should be in the root of a linnaeus NG-installation");
	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();
	
	echo '<h3>Update authors</h3><p style="margin-bottom: 30px;">This script tries to extract taxon authors 
		 from the description field. Set the project-specific settings in the settings array before continuing. 
	     Authors that cannot be successfully extracted are listed in red. Please review the list and click 
	     the "Update database" click to copy the authors to the database.</p><p>';

 	$d = mysql_connect($s['host'],$s['user'],$s['password']) or die ('Cannot connect to '.$s['host']);
	mysql_select_db($s['database'],$d) or die ('Cannot select database '.$s['database']);
	mysql_set_charset('utf8', $d);
	
	// Nullify authors before commencing
	if (isset($_GET['update'])) nullifyAuthors();

    $query = 'SELECT t1.`taxon_id`, t1.`content`, t2.`taxon`, IF(t3.`lower_taxon` = 0, "higherTaxa", "species") AS `module`
        FROM `' . $s['tablePrefix'] . 'content_taxa` t1
        LEFT JOIN `' . $s['tablePrefix'] . 'taxa` AS t2 ON t1.`taxon_id` = t2.`id`
        LEFT JOIN `' . $s['tablePrefix'] . 'projects_ranks` AS t3 ON t2.`rank_id` = t3.`id`
        WHERE t1.`project_id` = ' . $settings['projectId'];	
	if (!$settings['includeHigherTaxa'])  {
	    $query .= ' AND t3.`lower_taxon` = 1';
	}
	$result = mysql_query($query) or die(mysql_error());
	$n = $k = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    // get an array with results, size dependent of number of lines to scan for author
	    $authorResults = getAuthor($row);
		$author = false;
		foreach ($authorResults as $authorResult) {
			if (!$authorResult['error']) {
				$author = $authorResult['author'];
				break;
			}
		}
	    // Update
	    if (isset($_GET['update'])) {
	    	$author ? updateAuthor($row, $author) : $k++;
		// Display only
		} else {
			if ($author) {
				$message = $row['taxon'] . ' -- author: ' . $author . '<br>';
			} else {
			
//	die(var_dump($authorResults));		
				$message = '<span style="color: red; font-weight: bold;">' . $row['taxon'] . ': ';
				$message .= count($authorResults) == 1 ? 'no author in "' . 
					trim($authorResults[0]['author']) . '"' : 'could not locate author';
				$message .= '</span><br>';
			}
			echo $message;
	    }
	}
	echo '</p>';
	if (!isset($_GET['update'])) {
		echo '<a href="?update" style="margin-top: 30px; font-weight: bold; color: #000; text-decoration: none;">&gt; Update authors</a></p>';
	} else {
		echo "<p>Ready! Copied $n authors to database, skipped $k.";
	}
	
	function getAuthor ($row) 
	{
	    global $settings;
	    // If the author is on the first line, cut off the rest before continuing
	    if ($settings['authorOnFirstLine']) {
	        return array(extractAuthor(
	        	strip_tags(substr($row['content'], 0, strpos($row['content'], $settings['lineBreak']))),
	        	$row['taxon'], 
	        	$settings[$row['module']]['preceededBy'],
	        	$settings[$row['module']]['followedBy']
	        ));
	    }
		// First replace line break with custom line break, then parse limited number of lines
		$content = strip_tags(str_replace($settings['lineBreak'], '@@@', $row['content']));
		$lines = explode('@@@', $content, $settings['linesToCheck'] + 1);
        for ($i = 0; $i < count($lines) - 1; $i++) {
	        $r[$i] = extractAuthor(
	        	$lines[$i], 
	        	$row['taxon'], 
	        	$settings[$row['module']]['preceededBy'],
	        	$settings[$row['module']]['followedBy']
	        );
	    }
	    return $r;
	}
	
	function extractAuthor ($line, $taxon, $preceededBy, $followedBy) 
	{
	    $author = $line;
	    if ($preceededBy) {
	        if ($preceededBy == '[taxon]') $preceededBy = $taxon;
	        if (strpos($author, $preceededBy) !== false) {
	            $author = trim(substr($author, strlen($preceededBy), strlen($author)));
	        }
	    }
		if ($followedBy) {
	        if ($followedBy == '[taxon]') $followedBy = $taxon;
	        if (strpos($author, $followedBy) !== false) {
	            $author = trim(substr($author, 0, strpos($author, $followedBy)));
	        }
	    }
	    if (!valideAuthor($line, $author, $preceededBy, $followedBy)) {
	        return array(
	            'author' => $line,
	            'error' => true
	        );
	    }
        return array(
            'author' => trim($author),
            'error' => false
        );
    }
    
    function valideAuthor ($line, $author, $preceededBy, $followedBy)
    {
    	// There should be text before or after, but the author matches the complete line: error
    	if (($preceededBy || $followedBy) && $line == $author) return false;
    	// Author consists of more than 10 words (this traps situations where no text preceeds or follows): error
    	if (str_word_count($author) > 10) return false;
    	return true;
    }
    
    function nullifyAuthors ()
    {
		global $settings, $s;
		$query = 'UPDATE `' . $s['tablePrefix'] . 'taxa` SET `author` = NULL WHERE `project_id` = ' . $settings['projectId'];
		$result = mysql_query($query) or die(mysql_error());
    }
    
    function updateAuthor ($row, $author)
    {
		global $settings, $s, $n;
		$query = 'UPDATE `' . $s['tablePrefix'] . 'taxa` SET `author` = "' . mysql_real_escape_string($author) .
			'" WHERE `id` = ' . $row['taxon_id'];
		$result = mysql_query($query) or die(mysql_error());
		$n++;
    }
	
?>
</body>
</html>