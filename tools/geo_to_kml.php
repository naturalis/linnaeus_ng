<?php
    // Postgres database with Postgis enabled required
    // Imports into "linnaeus" table, postprocesses into "linnaeus_post" table
    // Pg table definition included at the bottom
	$projectId = 3;
	$srid = 4326;
	$tableIn = 'linnaeus';
	$tableOut = 'linnaeus_post';

    define('PG_DB_HOST', 'localhost');
	define('PG_DB_USER', 'ruud');
	define('PG_DB_PASSWORD', '');
	define('PG_DB_DATABASE', 'ruud');
	if (!isset($pg_connect)) {
		$pg = pg_connect("host=" . PG_DB_HOST . " dbname=" . PG_DB_DATABASE .
		  " user=" . PG_DB_USER . " password=" . PG_DB_PASSWORD);
	}

	// KML
    $path = dirname(__FILE__) . '/' . 'geo';
    $color = '32FFFFFF';
    $colorMode = 'normal';
    $polyFill = 1; // vs 0
    $outline = 1;


	// Linnaeus
	$cfg = dirname(__FILE__) . '/../configuration/admin/configuration.php';
	if (!file_exists($cfg)) die("Error: unable to locate $cfg.");
	include $cfg;
	$c = new configuration;
	$s = $c->getDatabaseSettings();
 	$my = mysqli_connect($s['host'], $s['user'], $s['password'], $s['database']);
	mysqli_set_charset($my, 'utf8');


	echo "Some spring cleaning first...\n";
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
            t2.taxon,
            t3.title as geo_type,
            astext(t1.boundary) as geo
        from
            occurrences_taxa as t1
        left join
            taxa as t2 on t1.taxon_id = t2.id
        left join
            geodata_types_titles as t3 on t1.type_id = t3.type_id
        where
            t1.map_id > 1 and
            t1.project_id = ' . $projectId;
    $r = mysqli_query($my, $q);


    // Add ST_FlipCoordinates() if necessary
	echo "Copying relevant data from MySQL to Postgres...\n";
    while ($row = mysqli_fetch_assoc($r)) {
		$insert = '
            insert into ' . $tableIn . ' (
                id,
                taxon_id,
                taxon,
                geo_type,
                the_geom
            ) values (' .
                $row['id'] . ',
                ' . $row['taxon_id'] . ',
                \'' .  pg_escape_string($row['taxon']) . '\',
                \'' . pg_escape_string($row['geo_type']) . '\',
                ST_FlipCoordinates(ST_GeomFromText(\'' . $row['geo'] . '\', ' . $srid . '))
                )';
		pg_query($pg, $insert) or die(pg_last_error() . $insert);
    }

	echo "Merging areas...\n";
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


    echo "Creating kml and geojson files...\n";

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


/*
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
    the_geom geometry(Geometry,4326)
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