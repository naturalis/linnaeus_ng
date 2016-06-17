<?php

    // A quick and very dirty script to posthumously add map_id to occurrences_taxa table
    // This is required to properly export data to kml/json

	$cfg = dirname(__FILE__) . '/../../configuration/admin/configuration.php';
	if (!file_exists($cfg)) die("Error: unable to locate $cfg.");
	include $cfg;
	$c = new configuration;
	$s = $c->getDatabaseSettings();
 	$my = mysqli_connect($s['host'], $s['user'], $s['password'], $s['database']);
	mysqli_set_charset($my, 'utf8');

    $projectId = 25;

    $q = '
        select t1.id, t1.item_id as taxon_id, t2.name as file_name
        from media_modules as t1
        left join media as t2 on t1.media_id = t2.id
        where t1.project_id = ' . $projectId . ' and t1.module_id = 17';
    $r = mysqli_query($my, $q);
    while ($row = mysqli_fetch_assoc($r)) {
        $up = '
            update media_modules
            set sort_order = ' . getSortOrder($row['taxon_id'], $row['file_name']) . '
            where id = ' . $row['id'];
        mysqli_query($my, $up) or die(mysqli_error($my));
        echo $up . '<br>';
    }

    function getSortOrder ($taxonId, $fileName) {
        global $my, $projectId;
        $q = '
            select sort_order
            from media_taxon
            where project_id = ' . $projectId . ' and
                taxon_id = ' . $taxonId . ' and
                file_name = "' . mysqli_real_escape_string($my, $fileName) . '"';
        $r = mysqli_query($my, $q);
        if ($row = mysqli_fetch_row($r)) {
            return $row[0];
        }
        return 99;
    }


?>