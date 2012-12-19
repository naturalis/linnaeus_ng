<?php
    $projectId = 588;
    
    // Modify project settings
    $settings = array(
        'includeHigherTaxa' => true,
        'authorOnFirstLine' => true,
        'lineBreak' => '<br />',
    
        'species' => array(
            'preceededBy' => '[taxon]', // text string, [taxon], false
            'followedBy' => false, // text string, [taxon], false
        ),
        'higherTaxa' => array(
            'preceededBy' => '[taxon]', // text string, [taxon], false
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
	
	// Let's go
	echo 'This script tries to extract taxon authors from the description field. ' .
	     'Authors that cannot be successfully extracted are listed after the process has completed.';

 	$d = mysql_connect($s['host'],$s['user'],$s['password']) or die ('Cannot connect to '.$s['host']);
	mysql_select_db($s['database'],$d) or die ('Cannot select database '.$s['database']);
	mysql_set_charset('utf8', $d);
	

    $query = 'SELECT t1.`taxon_id`, t1.`content`, t2.`taxon` FROM `' . $s['tablePrefix'] . 'content_taxa` t1
        LEFT JOIN `' . $s['tablePrefix'] . 'taxa` AS t2 ON t1.`taxon_id` = t2.`id`
        LEFT JOIN `' . $s['tablePrefix'] . 'projects_ranks` AS t3 ON t2.`rank_id` = t3.`id`
        WHERE t1.`project_id` = ' . $projectId;	
	if (!$settings['includeHigherTaxa'])  {
	    $query .= ' AND t3.`lower_taxon` = 1';
	}
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    // Get an array with results, size dependent of number of lines to scan for author
	    $authorResults = getAuthor($row);
	    // Result must be on first line; don't need to loop for errors
	    if ($settings['authorOnFirstLine']) {
	        // ok
	        if (!$authorResults[0]['error']) {
	            echo $row['taxon'] . ' -- author: ' . $authorResults[0]['author'] . '<br>';
	        // fail
	        } else {
	            echo '<span style="color:red">' . $row['taxon'] . ' could not locate author in ' . 
	                $authorResults[0]['author'] . '</span><br>';
	        }
	    // Check array for results first; throw error if no result
	    } else {
    	    $author = false;
    	    foreach ($authorResults as $authorResult) {
    	        if (!$authorResult['error']) {
    	            $author = $authorResult['author'];
    	            break;
    	        }
    	    }
    	    // ok
    	    if ($author) {
    	        echo $row['taxon'] . ' -- author: ' . $author . '<br>';
    	    // fail   
    	    } else {
	            echo '<span style="color:red">' . $row['taxon'] . ' could not locate author</span><br>';
    	    }
	    }
	}
	
	function getAuthor ($row, $settings) 
	{
	    // If the author is on the first line, cut off rest before continuing
	    if ($settings['authorOnFirstLine']) {
	        $lines[] = strip_tags(substr($row['content'], 0, strpos($row['content'], $settings['lineBreak'])));
	    } else {
    	    // First replace line break with custom line break
    	    $content = strip_tags(str_replace($settings['lineBreak'], '@@@', $row['content']));
    	    // Maximum number of lines to check is 10
    	    $lines = explode('@@@', $content, 6);
	    }
        for ($i = 0; $i < count($lines) - 1; $i++) {
	        $authorResults[$i] = extractAuthor($lines[$i]);
	    }
	    return $authorResults;
	}
	
	function extractAuthor ($line, $taxon, $settings) 
	{
	    $preceededBy = $settings['preceededBy'];
	    $followedBy = $settings['$followedBy'];
	    $newLine = $line;
	    
	    if ($preceededBy) {
	        if ($preceededBy == '[taxon]') $preceededBy = $taxon;
	        if (strpos($line, $preceededBy) !== false) {
	            $newLine = substr($newLine, strpos($newLine, $preceededBy, strlen($newLine)));
	        }
	    }
		if ($followedBy) {
	        if ($followedBy == '[taxon]') $followedBy = $taxon;
	        if (strpos($newLine, $followedBy) !== false) {
	            $newLine = substr($newLine, 0, strpos($newLine, $followedBy));
	        }
	    }
	    
	    // line is not modified while it should have been, return original string and error
	    if (($preceededBy || $followedBy) && $line == $newLine) {
	        return array(
	            'author' => $line,
	            'error' => true
	        );
	    }
	    // else return what should now be the author
        return array(
            'author' => $newLine,
            'error' => false
        );
    }
	
?>