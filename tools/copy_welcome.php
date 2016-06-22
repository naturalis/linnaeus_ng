<?php

	$cfg = dirname(__FILE__) . '/../configuration/admin/configuration.php';
	if (!file_exists($cfg)) die("Error: unable to locate $cfg.");
	include $cfg;
	$c = new configuration;
	$s = $c->getDatabaseSettings();
 	$my = mysqli_connect($s['host'], $s['user'], $s['password'], $s['database']);
	mysqli_set_charset($my, 'utf8');

	$q = 'select distinct project_id from content';
    $r = mysqli_query($my, $q);
    while ($d = mysqli_fetch_assoc($r)) {
        $projects[] = $d['project_id'];
    }

    foreach ($projects as $projectId) {
    	$q = 'select * from content where project_id = ' . $projectId;
        $r = mysqli_query($my, $q);

        mysqli_query($my, 'update introduction_pages set show_order = show_order + 2
            where project_id = '. $projectId);

        $i = 0;

        while ($row = mysqli_fetch_assoc($r)) {

            $q = 'insert into introduction_pages
                  (project_id, got_content, show_order, hide_from_index, created, last_change)
                  values
                  (' . $projectId . ',1,' . $i . ',0,now(),now())';
            mysqli_query($my, $q) or die($q);
            $pageId = mysqli_insert_id($my);

            $q = 'insert into content_introduction values
                  (null,' . $projectId . ',' . $pageId . ', ' . $row['language_id'] . ', "' .
                  mysqli_real_escape_string($my, $row['subject']) . '","' .
                  mysqli_real_escape_string($my, $row['content']) . '",now(),now())';
            mysqli_query($my, $q) or die($q);

            $i++;
        }
    }
?>