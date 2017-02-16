<?php
	$cfg = dirname(__FILE__) . '/../configuration/admin/configuration.php';

	// Get external settings
	if (!file_exists($cfg)) die("Unable to locate $cfg. This script should be in the root of a linnaeus NG-installation");
	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();

 	$d = mysqli_connect($s['host'], $s['user'], $s['password'], $s['database']);
	mysqli_set_charset($d, 'utf8');
	mysqli_query($d, 'SET sql_mode = ""');
	$errors = array();

    echo "Clearing Literature2 tables...\n";
    clearLit2();

    $q = 'SELECT `id`, `title` FROM `projects` ORDER BY `title`';
    $r = mysqli_query($d, $q);
    while ($row = mysqli_fetch_assoc($r)) {
        $projects[$row['id']] = $row['title'];
    }

    foreach ($projects as $projectId => $projectTitle) {

        echo "Getting language for project $projectTitle...\n";
        $q = "SELECT `id`, `language_id` FROM `languages_projects` WHERE `project_id` = $projectId AND `def_language` = 1";
        $r = mysqli_query($d, $q);
        while ($row = mysqli_fetch_assoc($r)) {
            $languages[$row['id']] = $row['language_id'];
        }

    	echo "Copying literature...\n";
        $q = "SELECT * FROM `literature` WHERE `project_id` = $projectId";
        $r = mysqli_query($d, $q);
        while ($row = mysqli_fetch_assoc($r)) {
            $actors = array();
            // Remove paragraph but leave other html markup
            $full = str_replace(array('<p>', '</p>'), '', $row['text']);
            // Save authors as actors
           	$actors[] = $row['author_first'];
            if (!empty($row['author_second']) && !in_array($row['author_second'], $actors)) {
                $actors[] = $row['author_second'];
            }
            // Year is set properly
            if (!empty($row['year'])) {
                $date = $row['year'] . (!empty($row['suffix']) ? $row['suffix'] : '');
                if (!empty($row['year_2'])) {
                    $date .= '-' . $row['year_2'] . (!empty($row['suffix_2']) ? $row['suffix_2'] : '');
                }
            // If not we have to extract it from the full reference
            } else {
                $date = getYear($full);
            }
            // If date is set properly we can split the reference in author and label
            if ($date && strpos($full, $date) !== false) {
                list($author, $label) = explode($date, $full);
                $author = rtrim($author, ' ,');
                $label = ltrim($label, ' .,');
            // Give up and store everything in author column
            // These (more or less) failed inserts can be identified by actor_id = -1
            } else {
                $date = $label = null;
                $author = $full;
                $errors[$projectId][] = $full;
            }

            // language_id may be missing in languages_projects; if so default to English
            $language_id = isset($languages[$row['project_id']]) ? $languages[$row['project_id']] : 26;

            $q = 'INSERT INTO `literature2` (`id`, `project_id`, `language_id`, `actor_id`, `label`,
                      `date`, `author`, `created`)
                  VALUES (' .
                     $row['id'] . ', ' .
                     $row['project_id'] . ', ' .
                     $language_id . ', ' .
                     (is_null($label) ? -1 : 'NULL') . ',
                     "' . mysqli_real_escape_string($d, $label). '",
                     "' . mysqli_real_escape_string($d, $date). '",
                     "' . mysqli_real_escape_string($d, $author). '",
                     CURRENT_TIMESTAMP' .
                  ')';
            mysqli_query($d, $q) or die($q . mysqli_error($d));

            foreach ($actors as $i => $actor) {
                $q2 = 'SELECT `id` FROM `actors` WHERE `name` = "' . mysqli_real_escape_string($d, $actor) . '"';
                $r2 = mysqli_query($d, $q2);
                if (mysqli_num_rows($r2) > 0) {
                    $row2 = mysqli_fetch_assoc($r2);
                    $actor_id = $row2['id'];
                } else {
                    $q3 = 'INSERT INTO `actors` (`project_id`, `name`, `created`) VALUES (' .
                        $row['project_id'] . ', "' .
                        mysqli_real_escape_string($d, $actor) . '",
                        CURRENT_TIMESTAMP
                    )';
                    mysqli_query($d, $q3) or die($q3. mysqli_error($d));
                    $actor_id = mysqli_insert_id($d);
                }
                $q4 = 'INSERT INTO `literature2_authors`
                    (`project_id`, `literature2_id`, `actor_id`, `sort_order`) VALUES (' .
                        $row['project_id'] . ', ' .
                        $row['id'] . ', ' .
                        $actor_id . ', ' .
                        ($i + 1) .
                    ')';
                mysqli_query($d, $q4) or die($q4 . mysqli_error($d));
            }
        }
        echo "\n";
    }



	if (!empty($errors)) {
        echo "\nThe following references could not be parsed properly. The complete text has " .
            "been stored in the author field. These references can be identified by actor_id = -1:\n\n";
        foreach ($errors as $projectId => $d) {
            echo $projects[$projectId] . "\n";
            foreach ($d as $error) {
                echo "$error\n";
            }
            echo "\n\n";
        }
        echo "Ready!\n\n\n";
	}


	function getYear ($s) {
        preg_match('/\b\d{4}([a-z]|\b)/', $s, $m);
        return isset($m[0]) && strpos($s, $m[0]) < 25 ? $m[0] : false;
	}

	function clearLit2 () {
	    global $d;
        $queries = array(
            'DROP TABLE IF EXISTS `actors`;',
            'CREATE TABLE `actors` (
              `id` int(11) NOT NULL,
              `project_id` int(11) NOT NULL,
              `name` varchar(255) NOT NULL,
              `name_alt` varchar(255) DEFAULT NULL,
              `homepage` varchar(255) DEFAULT NULL,
              `gender` enum(\'m\',\'f\') DEFAULT NULL,
              `is_company` tinyint(1) NOT NULL DEFAULT 0,
              `employee_of_id` int(11) DEFAULT NULL,
              `created` datetime NOT NULL,
              `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
            'DROP TABLE IF EXISTS `literature2`;',
            'CREATE TABLE `literature2` (
              `id` int(11) NOT NULL,
              `project_id` int(11) NOT NULL,
              `language_id` int(11) DEFAULT NULL,
              `label` varchar(1000) NOT NULL,
              `alt_label` varchar(255) DEFAULT NULL,
              `alt_label_language_id` int(11) DEFAULT NULL,
              `date` varchar(32) DEFAULT NULL,
              `author` varchar(1000) DEFAULT NULL,
              `publication_type` varchar(24) DEFAULT NULL,
              `publication_type_id` int(11) DEFAULT NULL,
              `actor_id` int(11) DEFAULT NULL,
              `publisher` varchar(255) DEFAULT NULL,
              `publishedin` varchar(255) DEFAULT NULL,
              `publishedin_id` int(11) DEFAULT NULL,
              `pages` varchar(32) DEFAULT NULL,
              `volume` varchar(32) DEFAULT NULL,
              `periodical` varchar(128) DEFAULT NULL,
              `periodical_id` int(11) DEFAULT NULL,
              `order_number` int(3) DEFAULT NULL,
              `external_link` varchar(255) DEFAULT NULL,
              `created` datetime NOT NULL,
              `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
            'DROP TABLE IF EXISTS `literature2_authors`;',
            'CREATE TABLE `literature2_authors` (
              `id` int(11) NOT NULL,
              `project_id` int(11) NOT NULL,
              `literature2_id` int(11) NOT NULL,
              `actor_id` int(11) NOT NULL,
              `sort_order` int(11) NOT NULL DEFAULT 0
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
            'DROP TABLE IF EXISTS `literature2_publication_types`;',
            'CREATE TABLE `literature2_publication_types` (
              `id` int(11) NOT NULL,
              `project_id` int(11) NOT NULL,
              `sys_label` varchar(255) NOT NULL,
              `created` datetime NOT NULL,
              `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
            'DROP TABLE IF EXISTS `literature2_publication_types_labels`;',
            'CREATE TABLE `literature2_publication_types_labels` (
              `id` int(11) NOT NULL,
              `project_id` int(11) NOT NULL,
              `publication_type_id` varchar(255) NOT NULL,
              `language_id` int(11) DEFAULT NULL,
              `label` varchar(255) NOT NULL,
              `created` datetime NOT NULL,
              `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;',
            'ALTER TABLE `actors`
              ADD PRIMARY KEY (`id`),
              ADD KEY `id` (`id`,`project_id`);',
            'ALTER TABLE `literature2`
              ADD PRIMARY KEY (`id`),
              ADD KEY `id` (`id`,`project_id`),
              ADD KEY `project_id` (`project_id`,`label`(250)) USING BTREE;',
            'ALTER TABLE `literature2_authors`
              ADD PRIMARY KEY (`id`),
              ADD UNIQUE KEY `project_id_2` (`project_id`,`literature2_id`,`actor_id`),
              ADD KEY `project_id` (`project_id`);',
            'ALTER TABLE `literature2_publication_types`
              ADD PRIMARY KEY (`id`),
              ADD UNIQUE KEY `project_id` (`project_id`,`sys_label`),
              ADD KEY `id` (`id`,`project_id`);',
            'ALTER TABLE `literature2_publication_types_labels`
              ADD PRIMARY KEY (`id`),
              ADD UNIQUE KEY `project_id` (`project_id`,`publication_type_id`,`language_id`),
              ADD KEY `id` (`id`,`project_id`);',
            'ALTER TABLE `actors`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;',
            'ALTER TABLE `literature2`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;',
            'ALTER TABLE `literature2_authors`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;',
            'ALTER TABLE `literature2_publication_types`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'
        );
        foreach ($queries as $q) {
            mysqli_query($d, $q) or die($q . mysqli_error($d));
        }
	}






?>