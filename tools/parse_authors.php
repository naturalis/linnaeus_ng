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
   		'projectId' => 584,
   		'linesToCheck' => 10, // How many lines should be considered checked for the author?
        'includeHigherTaxa' => false, // Set to false to skip higher taxa
        'lineBreak' => '<br />', // Default in L2 imports
        'maxWordsInAuthorString' => 10, // Dismiss author if string contains more this number of words
        'yearCheck' => false, // Year must be present in author string; relevant when preceededBy and followedBy are false
    
        'species' => array(
            'preceededBy' => '[taxon]', // 'Text string', '[taxon]', false
            'followedBy' => false
        ),
        'higherTaxa' => array(
            'preceededBy' => false,
            'followedBy' => false
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

 	$d = mysqli_connect($s['host'],$s['user'],$s['password']) or die ('Cannot connect to '.$s['host']);
	mysqli_select_db($d, $s['database']) or die ('Cannot select database '.$s['database']);
	mysqli_set_charset($d, 'utf8');
	
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
	$result = mysqli_query($result, $query) or die(mysqli_error($result));
	$n = $k = 0;
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
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
				$message = $row['taxon'] . ': ' . $author . '<br>';
			} else {
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
		$content = strip_tags(str_replace($settings['lineBreak'], '@@@', $row['content']));
		$lines = explode('@@@', $content, $settings['linesToCheck'] + 1);
        for ($i = 0; $i < count($lines) - 1; $i++) {
	        $r[$i] = extractAuthor($lines[$i], $row);
	    }
	    return $r;
	}
	
	function extractAuthor ($line, $row) 
	{
	    global $settings;
	    $author = $line;
	    $preceededBy = $settings[$row['module']]['preceededBy'];
	    $followedBy = $settings[$row['module']]['followedBy'];
	    
	    if ($preceededBy) {
	        if ($preceededBy == '[taxon]') $preceededBy = $row['taxon'];
	        if (strpos($author, $preceededBy) !== false) {
	            $author = trim(substr($author, strlen($preceededBy), strlen($author)));
	        }
	    }
		if ($followedBy) {
	        if ($followedBy == '[taxon]') $followedBy = $row['taxon'];
	        if (strpos($author, $followedBy) !== false) {
	            $author = trim(substr($author, 0, strpos($author, $followedBy)));
	        }
	    }
	    if (!valideAuthor($line, $author, $preceededBy, $followedBy)) {
	        return array('author' => $line, 'error' => true);
	    }
        return array('author' => trim($author), 'error' => false);
    }
    
    function valideAuthor ($line, $author, $preceededBy, $followedBy)
    {
    	global $settings;
    	// Empty author string is not OK...
    	if (empty($author)) {
    		return false;
    	}
    	// There should be text before or after, but author matches the complete line; dismiss
    	if (($preceededBy || $followedBy) && $line == $author) {
    		return false;
    	}
    	// No text preceeds or follows; check for year
    	if ($settings['yearCheck'] && !preg_match('/(\s|,)(17|18|19|20)[0-9]{2}/', $author)) {
    		return false;
    	}
    	// String contains year but contains too many words
    	if (str_word_count($author) >= $settings['maxWordsInAuthorString']) {
    		return false;
    	}
    	return true;
    }
    
    function nullifyAuthors ()
    {
		global $settings, $s;
		$query = 'UPDATE `' . $s['tablePrefix'] . 'taxa` SET `author` = NULL WHERE `project_id` = ' . $settings['projectId'];
		$result = mysqli_query($result, $query) or die(mysqli_error($result));
    }
    
    function updateAuthor ($row, $author)
    {
		global $settings, $s, $n;
		$query = 'UPDATE `' . $s['tablePrefix'] . 'taxa` SET `author` = "' . mysqli_real_escape_string($author) .
			'" WHERE `id` = ' . $row['taxon_id'];
		$result = mysqli_query($result, $query) or die(mysqli_error($result));
		$n++;
    }
	
?>
</body>
</html>