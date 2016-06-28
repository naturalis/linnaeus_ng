<?php

    // A quick and very dirty script to posthumously add map_id to occurrences_taxa table
    // This is required to properly export data to kml/json

	$cfg = dirname(__FILE__) . '/../configuration/admin/configuration.php';
	if (!file_exists($cfg)) die("Error: unable to locate $cfg.");
	include $cfg;
	$c = new configuration;
	$s = $c->getDatabaseSettings();
 	$my = mysqli_connect($s['host'], $s['user'], $s['password'], $s['database']);
	mysqli_set_charset($my, 'utf8');

    $projectId = 3;
    $srid = 4326;
    $stored = array();

	// Set map data to session as per original script
	$q = 'select * from l2_maps';
    $r = mysqli_query($my, $q);
    while ($d = mysqli_fetch_assoc($r)) {
        $maps[$d['name']]['label'] = $d['name'];
        $coordinates = json_decode($d['coordinates'], true);
        $maps[$d['name']]['coordinates'] = $coordinates;

        $lat1 = floatval($coordinates['topLeft']['lat']);
        $lat2 = floatval($coordinates['bottomRight']['lat']);
        $lon1 = floatval($coordinates['topLeft']['long']);
        $lon2 = floatval($coordinates['bottomRight']['long']);

        $sqW = $sqH = $d['name'] == 'World' ? 10 : 5;

        $widthInSquares = (($lon1 >= $lon2) ? $lon1 - $lon2 : 360 + $lon1 - $lon2) / $sqW;
        $heightInSquares = (($lat1 - $lat2) / $sqH);

        $maps[$d['name']]['square']['width'] = $sqW;
        $maps[$d['name']]['square']['height'] = $sqH;
        $maps[$d['name']]['widthInSquares'] = $widthInSquares;
        $maps[$d['name']]['heightInSquares'] = $heightInSquares;

    }

    $r = mysqli_query($my, "SHOW COLUMNS FROM `occurrences_taxa` LIKE 'map_id'");
    if (mysqli_num_rows($r) == 0) {
        mysqli_query($my, "ALTER TABLE `occurrences_taxa` ADD `map_id` INT NOT NULL");
        mysqli_query($my, "ALTER TABLE `occurrences_taxa` drop index project_id");
        mysqli_query($my, "ALTER TABLE `occurrences_taxa` add index project_id (project_id, taxon_id, nodes_hash,map_id)");
    }
    mysqli_query($my, "TRUNCATE TABLE `occurrences_taxa`");

	$q = 'select t1.*, t2.name as mapname
	      from l2_occurrences_taxa as t1
	      left join l2_maps as t2 on t1.map_id = t2.id
	      where t1.project_id = '. $projectId;
    $r = mysqli_query($my, $q);
    while ($d = mysqli_fetch_assoc($r)) {

        $mapname = $d['mapname'];

        // determining the position of the square in the map grid
        $row = floor(trim($d['square_number']) / $maps[$mapname]['widthInSquares']);
        $col = trim($d['square_number']) % $maps[$mapname]['widthInSquares'];
        if ($col == 0)
            $col = $maps[$mapname]['widthInSquares'];

        $topleftLon = $maps[$mapname]['coordinates']['topLeft']['long'];

        $n1Lat = $n2Lat = $maps[$mapname]['coordinates']['topLeft']['lat'] - ($row * $maps[$mapname]['square']['height']);
        $n1Lon = $topleftLon + (($col - 1) * $maps[$mapname]['square']['width']);
        $n1Lon = $n4Lon = ($n1Lon >= 180 ? -360 + $n1Lon : $n1Lon);

        $n2Lon = $topleftLon + ($col * $maps[$mapname]['square']['width']);
        $n2Lon = $n3Lon = ($n2Lon > 180 ? -360 + $n2Lon : $n2Lon);
        $n3Lat = $n4Lat = $maps[$mapname]['coordinates']['topLeft']['lat'] - (($row + 1) * $maps[$mapname]['square']['height']);

        $occurrence = array(
            'taxonId' => $d['taxon_id'],
            'mapId' => $d['map_id'],
            'square' => $d['square_number'],
            'row' => $row,
            'col' => $col,
            'typeId' => $d['type_id'],
            'nodes' => array(
                array(
                    $n1Lat,
                    $n1Lon
                ),
                array(
                    $n2Lat,
                    $n2Lon
                ),
                array(
                    $n3Lat,
                    $n3Lon
                ),
                array(
                    $n4Lat,
                    $n4Lon
                )
            ),
            'typeless' => empty($legend)
        );

        $k = $projectId . $d['taxon_id'] . $d['map_id'] . md5(json_encode($occurrence['nodes']));
        if (!in_array($k, $stored)) {
            $stored[] = $k;
            doSaveMapItem($occurrence);
        };

    }



    function doSaveMapItem ($occurrence)
    {
        global $projectId, $srid, $my;

        if ((!$occurrence['taxonId']) || (count((array) $occurrence['nodes']) <= 2) || (!$occurrence['typeId'])) {
            return;
        }

        // remove the last node if it is identical to the first, just in case
        if ($occurrence['nodes'][0] == $occurrence['nodes'][count((array) $occurrence['nodes']) - 1])
            array_pop($occurrence['nodes']);

            // create a string for mysql (which does require the first and last to be the same)
        $geoStr = array();
        foreach ((array) $occurrence['nodes'] as $sVal)
            $geoStr[] = $sVal[0] . ' ' . $sVal[1];
        $geoStr = implode(',', $geoStr) . ',' . $geoStr[0];

        $boundary = "GeomFromText('POLYGON((" . $geoStr . "))'," . $srid . ")";
        $boundary_nodes = json_encode($occurrence['nodes']);
        $nodes_hash = md5(json_encode($occurrence['nodes']));

        $q = "insert into occurrences_taxa (project_id, taxon_id, map_id, type_id,
            type, boundary, boundary_nodes, nodes_hash,created) values ($projectId, " . $occurrence['taxonId'] . ",".
            $occurrence['mapId'].",".$occurrence['typeId'].",'polygon',$boundary,'$boundary_nodes','$nodes_hash',now())";
/*
        $this->models->OccurrencesTaxa->save(
        array(
            'id' => null,
            'project_id' => $projectId,
            'taxon_id' => $occurrence['taxonId'],
            'type_id' => $occurrence['typeId'],
            'type' => 'polygon',
            'boundary' => "#GeomFromText('POLYGON((" . $geoStr . "))'," . $srid . ")",
            'boundary_nodes' => json_encode($occurrence['nodes']),
            'nodes_hash' => md5(json_encode($occurrence['nodes']))
        ));
*/

        mysqli_query($my, $q) or die(mysqli_error($my). $q);

    }

?>