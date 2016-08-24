<?php
    /*
     * Default occurrences_taxa lacks map_id column!
     * Duplicate this table, add map_id column and run first part of script
     * See table definition at the bottom of this script
     */


	 // Export KML/geojson for this project
	$projectId = 11;
	// Specificy map_id to export specific map; should be corresponding id in l2_maps table!
	$mapId = null; // default null
    // Idem to exclude specific map; i.e. use for World map as this has lower resolution
	$excludeMapId = 28; // default null
    // Spatial Reference System Identifier; data should already be in the right format in MySQL
	$srid = 4326;
	 // Postgres table to collect 'raw' data from MySQL
	$tableIn = 'linnaeus';
	// Postgres table to which unified distribution data is written
	$tableOut = 'linnaeus_post';

    // Postgres database with Postgis enabled required
    // Imports into "linnaeus" table, postprocesses into "linnaeus_post" table
    // Table definition included at the bottom
    /*
        define('PG_DB_HOST', 'localhost');
    	define('PG_DB_USER', 'user');
    	define('PG_DB_PASSWORD', 'pass');
    	define('PG_DB_DATABASE', 'db');
     */
    require_once 'postgres_credentials.php';

	// KML settings
    $path = dirname(__FILE__) . '/' . 'geo';
    $color = '32FFFFFF';
    $colorMode = 'normal';
    $polyFill = 1; // vs 0
    $outline = 1;

	// Some magic so output is flushed to screen immediately
	@apache_setenv('no-gzip', 1);
	@ini_set('zlib.output_compression', 0);
	@ini_set('implicit_flush', 1);
	for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
	ob_implicit_flush(1);



	// Connect to Linnaeus
	$cfg = dirname(__FILE__) . '/../configuration/admin/configuration.php';
	if (!file_exists($cfg)) die("Error: unable to locate $cfg.");
	include $cfg;
	$c = new configuration;
	$s = $c->getDatabaseSettings();
 	$my = mysqli_connect($s['host'], $s['user'], $s['password'], $s['database']);
	mysqli_set_charset($my, 'utf8');

	// Connect to Postgres
	if (!isset($pg_connect)) {
		$pg = pg_connect("host=" . PG_DB_HOST . " dbname=" . PG_DB_DATABASE .
		  " user=" . PG_DB_USER . " password=" . PG_DB_PASSWORD);
	}



	// First fix data in occurrences_taxa; map_id was missing in the original table!
	echo 'Fixing occurrences_taxa by adding map_id column<br>Getting maps...<br>';
    mysqli_query($my, 'truncate table `occurrences_taxa_with_map_id`');
    $q = '
        select distinct t3.title as project, t1.project_id, t2.name as map, t1.map_id
        from l2_occurrences_taxa as t1
        left join l2_maps as t2 on t1.map_id = t2.id
	    left join projects as t3 on t1.project_id = t3.id
        order by t3.title, t2.name';
    $r = mysqli_query($my, $q);
    while ($row = mysqli_fetch_assoc($r)) {
        $projectMaps[] = $row;
    }

    // Has to run in batches, otherwise you'll get out of memory errors...
    foreach ($projectMaps as $d) {
        echo $d['project'] . ': processing ' . $d['map'] . '...<br>';
        $q = '
            select t1.*, t2.name, t2.coordinates, t2.rows, t2.cols
            from l2_occurrences_taxa as t1
            left join l2_maps as t2 on t1.map_id = t2.id
            where t1.project_id = ' . $d['project_id'] . ' and
            t1.map_id = ' . $d['map_id'];
        $r = mysqli_query($my, $q);
        while ($row = mysqli_fetch_assoc($r)) {

            $latLon = squareNumberToLatLon($row);

            $geoStr = array();
            foreach ($latLon as $sVal)
                $geoStr[] = $sVal[0] . ' ' . $sVal[1];
            $geoStr = implode(',', $geoStr) . ',' . $geoStr[0];

            $insert = '
                insert into `occurrences_taxa_with_map_id` values (' .
                $row['id'] . ', ' .
                $row['project_id'] . ', ' .
                $row['taxon_id'] . ', ' .
                $row['map_id'] . ', ' .
                $row['type_id'] . ', "polygon", null, null, null, ' .
                "GeomFromText('POLYGON((" . $geoStr . "))', $srid)" . ',"' .
                json_encode($latLon) . '", null, now(), now())';

            mysqli_query($my, $insert);
        }
    }


	echo "Some spring cleaning first...<br>";
	pg_query($pg, "truncate table $tableIn") or die(pg_last_error());
	pg_query($pg, "alter sequence {$tableIn}_id_seq restart") or die(pg_last_error());
	pg_query($pg, "update $tableIn set id = default") or die(pg_last_error());
	pg_query($pg, "truncate table $tableOut") or die(pg_last_error());
	pg_query($pg, "alter sequence {$tableOut}_id_seq restart") or die(pg_last_error());
	pg_query($pg, "update $tableOut set id = default") or die(pg_last_error());

    $q = '
        select
            t1.id,
            t1.taxon_id,
            t4.name as map,
            t2.taxon,
            t3.title as geo_type,
            AsWKT(boundary) as geo
        from
            occurrences_taxa_with_map_id as t1
        left join
            taxa as t2 on t1.taxon_id = t2.id
        left join
            geodata_types_titles as t3 on t1.type_id = t3.type_id
        left join
            l2_maps as t4 on t1.map_id = t4.id
        where
            t1.project_id = ' . $projectId .
        (!empty($mapId) ? ' and t1.map_id = ' . $mapId : '') .
        (!empty($excludeMapId) ? ' and t1.map_id != ' . $excludeMapId : '');

    $r = mysqli_query($my, $q);

    echo "Copying relevant data from MySQL to Postgres...<br>";
    while ($row = mysqli_fetch_assoc($r)) {

		$insert = '
            insert into ' . $tableIn . ' (
                id,
                taxon_id,
                taxon,
                geo_type,
                the_geom,
                map
            ) values (' .
                $row['id'] . ',
                ' . $row['taxon_id'] . ',
                \'' .  pg_escape_string($row['taxon']) . '\',
                \'' . pg_escape_string($row['geo_type']) . '\',
                ST_GeomFromText(\'' . $row['geo'] . '\', ' . $srid . '), \'' .
                $row['map'] . '\')';
		pg_query($pg, $insert) or die(pg_result_error() . $insert);
    }

	echo "Merging areas...<br>";
    $insert = "
        insert into $tableOut (
            select
                nextval('{$tableOut}_id_seq'),
                taxon_id,
                taxon,
                geo_type,
                ST_Multi(ST_Union(the_geom)) as the_geom
             from
                $tableIn
             group by
                taxon_id,
                taxon,
                geo_type
        )";
    pg_query($pg, $insert) or die(pg_last_error() . $insert);


    echo "Creating kml and geojson files...<br>";

    $xml = new XMLWriter();
	$xml->openMemory();
	$xml->setIndent(true);
	$xml->setIndentString("   ");

    $q = '
        select
            taxon_id,
            taxon,
            geo_type,
            ST_AsKML(2, the_geom) as the_geom_kml,
            ST_AsGeoJSON(the_geom) as the_geom_json
        from
            linnaeus_post';
    $r = pg_query($pg, $q);
    while ($row = pg_fetch_assoc($r)) {

        // KML
        $file = $path . '/' . $row['taxon'] . ' - ' . $row['geo_type'] . '.kml';

    	$xml->startDocument('1.0', 'UTF-8');
        $xml->startElementNS(
            null,
            'kml',
            'http://www.opengis.net/kml/2.2'
        );
        $xml->writeAttributeNS(
            'xmlns',
            'gx',
            null,
            'http://www.google.com/kml/ext/2.2'
        );
        $xml->writeAttributeNS(
            'xmlns',
            'kml',
            null,
            'http://www.opengis.net/kml/2.2'
        );
        $xml->writeAttributeNS(
            'xmlns',
            'atom',
            null,
            'http://www.w3.org/2005/Atom'
        );
        $xml->startElement('Document');
    	$xml->startElement('Placemark');
        $xml->writeElement('name', $row['taxon']);
        $xml->startElement('Style');
        $xml->startElement('Linestyle');
        $xml->writeElement('color', $color);
        $xml->endElement();
        $xml->startElement('PolyStyle');
        $xml->writeElement('color', $color);
        $xml->writeElement('colorMode', $colorMode);
        $xml->writeElement('fill', $polyFill);
        $xml->writeElement('outline', $outline);
        $xml->endElement();
        $xml->endElement();
        $xml->writeRaw($row['the_geom_kml']);
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();

        file_put_contents($file, $xml->flush(true));

        // GeoJSON
        $file = $path . '/' . $row['taxon'] . ' - ' . $row['geo_type'] . '.json';
        file_put_contents($file, $row['the_geom_json']);
    }


    function squareNumberToLatLon ($p) {

        $widthInSquares = $p['cols'];
        $heightInSquares = $p['rows'];
        $number = $p['square_number'];
        $coordinates = json_decode($p['coordinates'], true);

        $mapWidth = $coordinates['topLeft']['long'] >= $coordinates['bottomRight']['long'] ?
            $coordinates['topLeft']['long'] - $coordinates['bottomRight']['long'] :
            360 + $coordinates['topLeft']['long'] - $coordinates['bottomRight']['long'];
        $mapHeight = $coordinates['topLeft']['lat'] - $coordinates['bottomRight']['lat'];

        $squareWidth = $mapWidth / $widthInSquares;
        $squareHeight = $mapHeight / $heightInSquares;

        // determining the position of the square in the map grid
        $row = floor(($number - 1) / $widthInSquares);
        if ($row == 0) {
            $row = 1;
        }
        $col = $number % $widthInSquares;
        if ($col == 0) {
            $col = $widthInSquares;
        }

        $coordinates['topLeft']['long'] = $coordinates['topLeft']['long'] * -1;

        $n1Lat = $n2Lat = $coordinates['topLeft']['lat'] - ($row * $squareHeight);
        $n1Lon = $coordinates['topLeft']['long']  + (($col - 1) * $squareWidth);
        $n1Lon = $n4Lon = ($n1Lon >= 180 ? -360 + $n1Lon : $n1Lon);
        $n2Lon = $coordinates['topLeft']['long'] + ($col * $squareWidth);
        $n2Lon = $n3Lon = ($n2Lon > 180 ? -360 + $n2Lon : $n2Lon);
        $n3Lat = $n4Lat = $coordinates['topLeft']['lat'] - (($row + 1) * $squareHeight);

        return array(
            array(
                $n1Lon,
                $n1Lat
            ),
            array(
                $n2Lon,
                $n2Lat
            ),
            array(
                $n3Lon,
                $n3Lat
            ),
            array(
                $n4Lon,
                $n4Lat
            )
        );
    }


/*
 *
 *
 *
--
-- Table structure for table `occurrences_taxa_fixed`
--

CREATE TABLE `occurrences_taxa_fixed` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `type` enum('marker','polygon') NOT NULL,
  `boundary` polygon DEFAULT NULL,
  `boundary_nodes` text,
  `nodes_hash` varchar(64) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `occurrences_taxa_fixed`
--
ALTER TABLE `occurrences_taxa_fixed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`nodes_hash`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `occurrences_taxa_fixed`
--
ALTER TABLE `occurrences_taxa_fixed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 *
 *
 *
 *
 *
 *
 *
 *
 *
--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: linnaeus; Type: TABLE; Schema: public; Owner: ruud; Tablespace:
--

CREATE TABLE linnaeus (
    id integer NOT NULL,
    taxon_id integer,
    taxon character varying(255),
    geo_type character varying(255),
    the_geom geometry(Geometry,4326),
    map character varying(50)
);


ALTER TABLE public.linnaeus OWNER TO ruud;

--
-- Name: linnaeus_id_seq; Type: SEQUENCE; Schema: public; Owner: ruud
--

CREATE SEQUENCE linnaeus_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.linnaeus_id_seq OWNER TO ruud;

--
-- Name: linnaeus_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ruud
--

ALTER SEQUENCE linnaeus_id_seq OWNED BY linnaeus.id;


--
-- Name: linnaeus_taxon_id_seq; Type: SEQUENCE; Schema: public; Owner: ruud
--

CREATE SEQUENCE linnaeus_taxon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.linnaeus_taxon_id_seq OWNER TO ruud;

--
-- Name: linnaeus_taxon_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ruud
--

ALTER SEQUENCE linnaeus_taxon_id_seq OWNED BY linnaeus.taxon_id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: ruud
--

ALTER TABLE ONLY linnaeus ALTER COLUMN id SET DEFAULT nextval('linnaeus_id_seq'::regclass);


--
-- Name: linnaeus_pkey; Type: CONSTRAINT; Schema: public; Owner: ruud; Tablespace:
--

ALTER TABLE ONLY linnaeus
    ADD CONSTRAINT linnaeus_pkey PRIMARY KEY (id);


--
-- Name: taxon_id; Type: INDEX; Schema: public; Owner: ruud; Tablespace:
--

CREATE INDEX taxon_id ON linnaeus USING btree (taxon_id);


--
-- Name: the_geom; Type: INDEX; Schema: public; Owner: ruud; Tablespace:
--

CREATE INDEX the_geom ON linnaeus USING gist (the_geom);


--
-- PostgreSQL database dump complete
--




*/

?>