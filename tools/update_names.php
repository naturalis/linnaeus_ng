<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Update Names</title>
</head>
<body style="font: 12px Verdana; width: 800px;">

<?php
	$cfg = '/var/www/linnaeusng/configuration/admin/configuration.php';
	$cfg = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/configuration/admin/configuration.php';

	// Get external settings
	if (!file_exists($cfg)) die("Unable to locate $cfg. This script should be in the root of a linnaeus NG-installation");
	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();

 	$d = mysqli_connect($s['host'],$s['user'],$s['password'], $s['database']);
	mysqli_set_charset($d, 'utf8');
	mysqli_query($d, 'SET sql_mode = ""');

	$nameTypes = array(
        'isValidNameOf',
        'isSynonymOf',
        'isSynonymSLOf',
        'isBasionymOf',
        'isHomonymOf',
        'isAlternativeNameOf',
        'isPreferredNameOf',
        'isMisspelledNameOf',
        'isInvalidNameOf'
	);

	$q = 'select id from languages where language="scientific" group by language';
	$r = mysqli_query($d, $q) or die($q . mysqli_error($d));
	$row = mysqli_fetch_row($r);
	$scientificId = $row[0];

	echo '<h3>Update Names</h3>';

	if (!isset($_GET['go'])) {
		echo '<p>This script copies data from the tables Taxa, Synonyms and Commonnames to Names.</p>';
	} else {
	    echo '<p>Updating global Linnaeus settings...<br>';
	    updateSettings();

	    echo 'Updating name types...<br>';
	    $q = 'SELECT `id` FROM `projects`';
	    $r = mysqli_query($d, $q);
	    while ($row = mysqli_fetch_assoc($r)) {
            $projectIds[] = $row['id'];
	    }
        setNameTypes($projectIds);

	    echo 'Clearing previously inserted names...<br>';
        clearNames($projectIds);

        echo 'Inserting taxa...<br>';
        insertTaxa($projectIds);

        echo 'Updating (infra)species names...<br>';
        updateTaxa();

        echo 'Inserting synonyms...<br>';
        insertSynonyms($projectIds);

        echo 'Inserting common names...<br>';
        insertCommonNames();
	}
	echo '</p>';
	if (!isset($_GET['go'])) {
		echo '<a href="?go" style="margin-top: 30px; font-weight: bold; text-decoration: none;">Do it!</a></p>';
	} else {
		echo "<p>Ready!";
	}


	function getYear ($s) {
        preg_match('/\b\d{4}([a-z]|\b)/', $s, $m);
        return isset($m[0]) && strpos($s, $m[0]) < 25 ? $m[0] : false;
	}

	function updateSettings () {
	    global $d;
        $queries = array(
            "INSERT IGNORE INTO modules (id,module,description,controller,icon,show_order,show_in_menu,show_in_public_menu,created,last_change)
                VALUES (15,'Beheer Soortenregister','Beheer Soortenregister','nsr','index.png',0,1,0,NOW(),NOW());",
            "INSERT IGNORE INTO rights (controller,view,view_description,created) VALUES ('nsr','*','full access',NOW());",
            "INSERT IGNORE INTO languages (language,iso3,direction,created) VALUES ('Scientific','sci','ltr',NOW());"
        );
        foreach ($queries as $q) {
            mysqli_query($d, $q) or die($q . mysqli_error($d));
        }
	}

	function setNameTypes ($projectIds) {
	    global $d, $nameTypes;
        foreach ($projectIds as $id) {
            foreach ($nameTypes as $type) {
                $q = "INSERT IGNORE INTO `name_types` VALUES (NULL,$id,'$type',NOW(),NOW());";
                mysqli_query($d, $q) or die($q . mysqli_error($d));
            }
        }
	}

	function clearNames ($projectIds) {
	    global $d;
	    mysqli_query($d, 'truncate table names') or die($q . mysqli_error($d));
	    /*
	     *
	    $queries = array(
            "delete from names where
            	taxon_id in (select id from taxa)
            	and type_id =
            		(select id from name_types where project_id=[id] and nametype ='isValidNameOf');",
            "delete from names where
            	taxon_id in (select taxon_id from commonnames)
            	and type_id =
            		(select id from name_types where project_id=[id] and nametype ='isPreferredNameOf');",
            "delete from names where
            	taxon_id in (select taxon_id from synonyms)
            	and type_id =
            		(select id from name_types where project_id=[id] and nametype ='isSynonymOf');"
        );
        foreach ($projectIds as $id) {
    	    foreach ($queries as $q) {
    	        $q = str_replace('[id]', $id, $q);
                mysqli_query($d, $q) or die($q . mysqli_error($d));
            }
        }
        */
	}

	function insertTaxa ($projectIds) {
	    global $d, $scientificId;
	    $q = "insert into names (project_id,taxon_id,language_id,type_id,name,authorship,created)
        	select
        		[id],
        		_a.id,
            	$scientificId,
        		_b.id,
        		_a.taxon,
        		_a.author,
        		now()
        	from
        		taxa _a

        	left join name_types _b
        		on _a.project_id=_b.project_id
        		and _b.nametype = 'isValidNameOf'

        	where
        		_a.project_id=[id]
        		and _a.taxon is not null";

	    foreach ($projectIds as $id) {
	        $q = str_replace('[id]', $id, $q);
	        mysqli_query($d, $q) or die($q . mysqli_error($d));
        }
	}

	function updateTaxa () {
	    global $d;
	    $abbreviations = array();
	    // First get genera and lower for all projects; species = 74
	    $q = 'select t1.id, t2.abbreviation from projects_ranks as t1
            left join ranks as t2 on t2.id = t1.rank_id
            where t1.rank_id >= 63';
	    mysqli_query($d, $q) or die($q . mysqli_error($d));
	    $r = mysqli_query($d, $q) or die($q . mysqli_error($d));
	    while ($row = mysqli_fetch_assoc($r)) {
            $rankIds[] = $row['id'];
            if (!empty($row['abbreviation'])) {
                $abbreviations[] = $row['abbreviation'];
            }
	    }
	    $q = 'select id, taxon from taxa where rank_id in (' . implode(',', $rankIds) . ') order by project_id';
	    $r = mysqli_query($d, $q) or die($q . mysqli_error($d));
		while ($row = mysqli_fetch_assoc($r)) {
            $parts = explode(' ', $row['taxon']);
            $q2 = 'update `names` set uninomial = "' . mysqli_real_escape_string($d, $parts[0]) . '"';
            if (isset($parts[1])) {
                $q2 .=  ', specific_epithet = "' . mysqli_real_escape_string($d, $parts[1]) . '"';
            }
            $infra = 'null';
		    if (isset($parts[2]) && !in_array($parts[2], $abbreviations)) {
		        $infra = '"' . mysqli_real_escape_string($d, $parts[2]) . '"';
            } else if (isset($parts[2]) && in_array($parts[2], $abbreviations) && isset($parts[3])) {
                $infra = '"' . mysqli_real_escape_string($d, $parts[3]) . '"';
            }
            $q2 .=  ', infra_specific_epithet = ' . $infra . ' where taxon_id = '. $row['id'] . ' and type_id = 1';
		    mysqli_query($d, $q2) or die($q2 . mysqli_error($d));
		}
	}

	function insertSynonyms ($projectIds) {
	    global $d, $scientificId;
	    $q = "insert into names (project_id,taxon_id,language_id,type_id,name,authorship,created)
        	select
        		[id],
        		_a.taxon_id,
        		$scientificId,
        		_b.id,
        		_a.synonym,
        		_a.author ,
        		now()
        	from
        		synonyms _a

        	left join name_types _b
        		on _a.project_id=_b.project_id
        		and _b.nametype = 'isSynonymOf'

        	where
        		_a.project_id=[id]
        		and _a.synonym is not null";

	    foreach ($projectIds as $id) {
	        $q = str_replace('[id]', $id, $q);
            mysqli_query($d, $q) or die($q . mysqli_error($d));
        }
	}

	function insertCommonNames () {
	    global $d;
	    $q = "SELECT * FROM `commonnames` ORDER BY project_id, taxon_id, language_id, show_order";
	    $r = mysqli_query($d, $q) or die($q . mysqli_error($d));
	    $oldTest = false;
	    while ($row = mysqli_fetch_assoc($r)) {
            $test = $row['taxon_id'] . $row['language_id'];
            $typeId = $test == $oldTest ?
                getNameTypeId($row['project_id'], 'isAlternativeNameOf') :
                getNameTypeId($row['project_id'], 'isPreferredNameOf');
            $q2 = 'insert into names (project_id,taxon_id,language_id,type_id,name,created) values (' .
                $row['project_id'] . ', ' . $row['taxon_id'] . ', ' . $row['language_id'] . ', ' .
                $typeId . ', "' .  mysqli_real_escape_string($d, $row['commonname']) . '", NOW());';
//echo "$q2<br>";
            mysqli_query($d, $q2) or die($q2 . mysqli_error($d));
            $oldTest = $test;
	    }
	}

	function getNameTypeId ($projectId, $nameType) {
	    global $d;
	    if (!isset($_SESSION[$nameType][$projectId])) {
            $q = "SELECT id FROM name_types WHERE project_id = $projectId AND
                nametype = '" . mysqli_real_escape_string($d, $nameType) . "';";
	        $r = mysqli_query($d, $q) or die($q . mysqli_error($d));
            $row = mysqli_fetch_assoc($r);
            $_SESSION[$nameType][$projectId] = $row['id'];
        }
        return $_SESSION[$nameType][$projectId];

	}


?>
</body>
</html>