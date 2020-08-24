<?php
	ini_set('max_execution_time',900);

	$d = @mysqli_connect('localhost','nsr','nsr') or die('cannot connect'));
	@mysqli_select_db($d, 'nsr_import') or die(mysqli_error($d));
	mysqli_query($d, 'SET NAMES utf8');
	mysqli_query($d, 'SET CHARACTER SET utf8');
/*

"4c5640cf-750c-4f15-9a95-1be7edef02ee","regnum"
"e2e25d70-d625-4ddb-aa47-e23adaace8b1","phylum"
"a49b7478-2239-4b01-97a9-36e592dc7b67","classis"
"9531366a-9755-4099-baf9-e0e9d8243988","subclassis"
"c61dbd3b-2bd7-416b-bf57-4ccb2ddb5a84","ordo"
"2ddf3454-a922-4949-9999-85bab260bde7","familia"
"f0ee48f5-d4fc-456d-8ca3-5ba418a6abad","genus"
"8b5a42d6-dee7-45b7-860b-bf19d89aaf63","subfamilia"
"39870865-7291-4133-9d34-1e2585500b35","superfamilia"
"4349df69-6c75-4632-acd7-1bf4c4ff8aa3","subordo"
"bed1b752-56e7-48d7-93f4-54252551e768","subgenus"

"b7808c0e-725c-4bf7-a2a5-e4d2de262e12","species"
"60a38a8c-cdcb-42a4-8d8a-b9b4d86e75d6","subspecies"
"d2e448f8-cbeb-4a1d-a9c1-e7f525f521ae","varietas"
"f484a72a-b3e8-47c6-be60-1955150b88d9","forma"
"09f76bd2-30d9-4109-bb12-c8111225b6c1","forma_specialis"
"5e7f7365-3622-4599-87ff-59388d0333ab","cultivar"

*/

	$ranks = array(
		"b7808c0e-725c-4bf7-a2a5-e4d2de262e12",
		"60a38a8c-cdcb-42a4-8d8a-b9b4d86e75d6",
		"d2e448f8-cbeb-4a1d-a9c1-e7f525f521ae",
		"f484a72a-b3e8-47c6-be60-1955150b88d9",
		"09f76bd2-30d9-4109-bb12-c8111225b6c1",
		"5e7f7365-3622-4599-87ff-59388d0333ab"
	);

	$qConcepts = 'select * from nsr_taxon_concepten where hasTaxonRank_resource in ("'.implode('","',$ranks).'") limit 100';

	$sql = mysqli_query($d, $qConcepts);

	while ($row = mysqli_fetch_assoc($d)) {

		echo $row[''];

	}



die('nsr exporter dies');

	$fPath = 'C:\Users\mschermer\Desktop\nsr\nsr data export\\';

	$fLanguages		= $fPath.'Languages.rdf.xml';
	$fRanks			= $fPath.'Taxon ranks.rdf.xml';
	$fConcepts		= $fPath.'NSR taxon concepten.rdf.xml';
	$fNames			= $fPath.'NSR taxon namen.rdf.xml';
	$fHabitats		= $fPath.'Habitats.rdf.xml';
	$fPresence		= $fPath.'Presence statussen.rdf.xml';
	$fTaxonPresence	= $fPath.'NSR taxon statussen.rdf.xml';

	$qLanguages		= "insert into languages (Description_about,prefLabel_nl,iso_639_6) values ('%s','%s','%s')";
	$qRanks			= "insert into taxon_ranks (Description_about,Parent_about,Preflabel_sci,Preflabel_en,Preflabel_nl,mutationDate) values ('%s','%s','%s','%s','%s','%s')";
	$qConcepts		= "insert into nsr_taxon_concepten (Description_about,Preflabel_sci,hasTaxonRank_resource,broader,hasNsrId,mutationDate) values ('%s','%s','%s','%s','%s','%s')";
	$qNames			= "insert into nsr_taxon_namen (concept_id,language_id,Label,Label_type,hasLanguage_resource,isNameOf_resource,typeOfName) values (%s,%s,'%s','%s','%s','%s','%s')";
	$qHabitats		= "insert into habitats (Description_about,prefLabel_nl) values ('%s','%s')";
	$qPresence		= "insert into presence_statussen (Description_about,prefLabel_nl) values ('%s','%s')";
	$qTaxonPresence	= "insert into nsr_taxon_statussen (concept_id,status_id,status82_id,habitat_id,is_indigeneous,habitat_verbatim,concept_verbatim,Preflabel_sci) values (%s,%s,%s,%s,%s,'%s','%s','%s')";

	$regExpDescription = '/\<rdf:Description rdf:about="(https?\:\/\/)([a-z0-9-]+){0,1}((\.)([a-z0-9-]+))+\/([a-z0-9-]+\/)*([a-z0-9-]{36})"\>/i';
	$regExpTaxonPresence = '/\<rna:%s rdf:resource="(https?\:\/\/)([a-z0-9-]+){0,1}((\.)([a-z0-9-]+))+\/([a-z0-9-]+\/)*([a-z0-9-]{36})"(.*)\>/i';

	$ISO_639_6 = 
		array(
			'Nederlands' => 'nld',
			'Duits' => 'deu',
			'Frans' => 'fra',
			'Grieks' => 'ell',
			'Spaans' => 'spa',
			'Engels' => 'eng'
		);

	ini_set('max_execution_time',900);

	$d = mysqli_connect('localhost','nsr','nsr') or die('cannot connect');
	@mysqli_select_db($d, 'nsr_import') or die(mysqli_error($d));
	mysqli_query($d, 'SET NAMES utf8');
	mysqli_query($d, 'SET CHARACTER SET utf8');


	function fixLabel($label)
	{
		return str_replace(array(chr(9),'&amp;'),array('','&'),$label);
	}

	function crunchSimpleStructure($filename,$query,$table)
	{

		global $regExpDescription, $d;

		mysqli_query($d, "truncate ".$table);

		$file = fopen($filename, "r");

		$i = 0;
		$rec = false;
		$results = array();

		while (!feof($file)) {

			$line = trim(fgets($file));

			preg_match($regExpDescription,$line,$m);

			if (isset($m[7]) && !empty($m[7])) {
				$Start_line = $line;
				$Description_about = $m[7];
				$rec = true;
			}

			if ($line=='</rdf:Description>') {

				if (isset($prefLabel_nl)) {
					$q = sprintf($query, 
						mysqli_real_escape_string($d, $Description_about),
						mysqli_real_escape_string($d, $prefLabel_nl)
					);

					if (mysqli_query($d, $q)) {
						$i++;
						$results[$Description_about] = mysqli_insert_id($d);

					} else {
						echo 'Failed insert ('.mysqli_error($d).'<!--'.$q.'-->)<br />'.chr(10);
					}

				} else {

					echo 'No Dutch name for "'.(isset($prefLabel_en) ? $prefLabel_en : htmlentities($Description_about)).'" ('.$Description_about.') - record not saved<br />'.chr(10);

				}
				$rec = false;
				$Description_about = $prefLabel_nl = $prefLabel_en = null;
			}

			if ($rec) {
				if (stripos($line,'<skos:prefLabel xml:lang="nl">')!==false)
					$prefLabel_nl = strip_tags($line);
				if (stripos($line,'<skos:prefLabel xml:lang="en">')!==false)
					$prefLabel_en = strip_tags($line);
			}


		}
		fclose($file);

		return array($i,$results);

	}


	echo '<h2>parsing languages</h2>'.chr(10);

	mysqli_query($d, "truncate languages");

	$file = fopen($fLanguages, "r");

	$i = 0;
	$rec = false;
	$languages = array();
	while (!feof($file)) {

		$line = trim(fgets($file));

		preg_match($regExpDescription,$line,$m);

		if (isset($m[7]) && !empty($m[7])) {
			$Start_line = $line;
			$Description_about = $m[7];
			$rec = true;
		}

		if ($line=='</rdf:Description>') {

			if (isset($prefLabel_nl)) {
				$q = sprintf($qLanguages, 
					mysqli_real_escape_string($d, $Description_about),
					mysqli_real_escape_string($d, $prefLabel_nl),
					(isset($ISO_639_6[$prefLabel_nl]) ? $ISO_639_6[$prefLabel_nl] : null)
				);

				if (mysqli_query($d, $q)) {
					$i++;
					$languages[$Description_about] = mysqli_insert_id($d);

					if (!isset($ISO_639_6[$prefLabel_nl]))
						echo 'No ISO639-6 code avaialble for "'.$prefLabel_nl.'" (saved record anyway)';

				} else {
					echo 'Failed insert ('.mysqli_error($d).'<!--'.$q.'-->)<br />'.chr(10);
				}

			} else {

				echo 'No Dutch name for "'.($prefLabel_en ? $prefLabel_en : htmlentities($Description_about)).'" ('.$Description_about.') - record not saved<br />'.chr(10);

			}
			$rec = false;
			$Description_about = $prefLabel_nl = $prefLabel_en = null;
		}

		if ($rec) {
			if (stripos($line,'<skos:prefLabel xml:lang="nl">')!==false)
				$prefLabel_nl = strip_tags($line);
			if (stripos($line,'<skos:prefLabel xml:lang="en">')!==false)
				$prefLabel_en = strip_tags($line);
		}


	}
	fclose($file);

	echo '<p>wrote '.$i.' languages</p>'.chr(10);


	echo '<h2>parsing ranks</h2>'.chr(10);

	mysqli_query($d, "truncate taxon_ranks");

	$file = fopen($fRanks, "r");
	$r = array();
	$i = 0;
	$rec = false;
	while (!feof($file)) {

		$line = trim(fgets($file));

		preg_match($regExpDescription,$line,$m);

		if (isset($m[7]) && !empty($m[7])) {
			$Start_line = $line;
			$Description_about = $m[7];
			$rec = true;
		}

		if ($line=='</rdf:Description>') {
			if (isset($Preflabel_sci)) {
				$q = sprintf($qRanks, 
					mysqli_real_escape_string($d, $Description_about),
					isset($Parent_about) ? mysqli_real_escape_string($d, $Parent_about) :'',
					mysqli_real_escape_string($d, fixLabel($Preflabel_sci)),
					mysqli_real_escape_string($d, fixLabel($Preflabel_en)),
					mysqli_real_escape_string($d, fixLabel($Preflabel_nl)),
					mysqli_real_escape_string($d, $mutationDate)
				);


				if (mysqli_query($d, $q)) {
					$i++;
					$r[$Description_about]=$Preflabel_sci;
				} else {
					echo 'Failed insert ('.mysqli_error($d).'<!--'.$q.'-->)<br />'.chr(10);
				}
			} else {
				echo 'No scientific name for "'.$Preflabel_en.'" ('.$Description_about.') - record not saved<br />'.chr(10);
			}
			$rec = false;
			$Description_about = $Preflabel_sci = $hasTaxonRank_resource = $hasNsrId = $mutationDate = $Preflabel_nl = null;
		}

		if ($rec) {
			if (stripos($line,'<skos:prefLabel xml:lang="sci">')!==false)
				$Preflabel_sci = strip_tags($line);
			if (stripos($line,'<skos:prefLabel xml:lang="en">')!==false)
				$Preflabel_en = strip_tags($line);
			if (stripos($line,'<skos:prefLabel xml:lang="nl">')!==false)
				$Preflabel_nl = strip_tags($line);
			if (stripos($line,'<skos:broader rdf:resource="http://data.nederlandsesoorten.nl/')!==false)
				$Parent_about = str_replace(array('<skos:broader rdf:resource="http://data.nederlandsesoorten.nl/','" />'),'',$line);
			if (stripos($line,'<rnax:mutationDate>')!==false)
				$mutationDate = strip_tags($line);
		}


	}
	fclose($file);

	echo '<p>wrote '.$i.' ranks</p>'.chr(10);


	echo '<h2>parsing taxon concepts</h2>'.chr(10);

	mysqli_query($d, "truncate nsr_taxon_concepten");

	$file = fopen($fConcepts, "r");
	$i = 0;
	$c = array();
	$rec = false;
	$concepts = array();
	while (!feof($file)) {

		$line = trim(fgets($file));
		preg_match($regExpDescription,$line,$m);

		if (isset($m[7]) && !empty($m[7])) {
			$Start_line = $line;
			$Description_about = $m[7];
			$rec = true;
		}

		if ($line=='</rdf:Description>') {
			if (isset($Preflabel_sci)) {

				$q = sprintf($qConcepts, 
					mysqli_real_escape_string($d, $Description_about),
					mysqli_real_escape_string($d, fixLabel($Preflabel_sci)),
					mysqli_real_escape_string($d, $hasTaxonRank_resource),
					mysqli_real_escape_string($d, $broader),
					mysqli_real_escape_string($d, $hasNsrId),
					mysqli_real_escape_string($d, $mutationDate)
				);

				if (mysqli_query($d, $q)) {

					if (!isset($c[$hasTaxonRank_resource]))
						$c[$hasTaxonRank_resource]=1;
					else
						$c[$hasTaxonRank_resource]++;

					$concepts[$Description_about] = mysqli_insert_id($d);

					$i++;

				} else {

					echo 'Failed inserting ('.htmlentities($Start_line).')<br />'.chr(10);

				}

			} else {
				echo 'No scientific name for "'.$Preflabel_en.'" (record not saved)<br />'.chr(10);
			}
			$rec = false;
			$Description_about = $Preflabel_sci = $hasTaxonRank_resource = $hasNsrId = $mutationDate = $Preflabel_nl = $hasSource = $broader = null;
		}

		if ($rec) {
			// base data
			if (stripos($line,'<skos:prefLabel xml:lang="sci">')!==false)
				$Preflabel_sci = strip_tags($line);
			if (stripos($line,'<skos:prefLabel xml:lang="en">')!==false)
				$Preflabel_en = strip_tags($line);
			if (stripos($line,'<skos:prefLabel xml:lang="nl">')!==false)
				$Preflabel_nl = strip_tags($line);
			if (stripos($line,'<rna:hasTaxonRank rdf:resource="http://data.nederlandsesoorten.nl/')!==false)
				$hasTaxonRank_resource = str_replace(array('<rna:hasTaxonRank rdf:resource="http://data.nederlandsesoorten.nl/','" />'),'',$line);
			if (stripos($line,'<rna:hasNsrId>')!==false)
				$hasNsrId = strip_tags($line);
			if (stripos($line,'<rnax:mutationDate>')!==false)
				$mutationDate = strip_tags($line);
			if (stripos($line,'<rna:hasSource>')!==false)
				$hasSource = strip_tags($line);
			if (preg_match('/\<skos:broader rdf:resource="(https?\:\/\/)([a-z0-9-]+){0,1}((\.)([a-z0-9-]+))+\/([a-z0-9-]+\/)*([a-z0-9-]{36})"(.*)\>/i',$line,$m)) {
				$broader = $m[7];
			}

		}


	}
	fclose($file);

	echo '<p>wrote '.$i.' concepts</p>'.chr(10);


	echo '<h2>parsing taxon names & synonyms</h2>'.chr(10);

	mysqli_query($d, "truncate nsr_taxon_namen");

	$file = fopen($fNames, "r");
	$i = $skippedProbablyUseless = 0;
	$rec = false;
	while (!feof($file)) {

		$line = trim(fgets($file));
		preg_match($regExpDescription,$line,$m);

		if (isset($m[7]) && !empty($m[7])) {
			$Start_line = $line;
			$Description_about = $m[7];
			$rec = true;
		}

		if ($line=='</rdf:Description>') {
			if (isset($Label) && isset($hasLanguage_resource)) {

				$q = sprintf($qNames, 
					isset($concept_id) ? $concept_id : 'null',
					isset($language_id) ? $language_id : 'null',
					mysqli_real_escape_string($d, fixLabel($Label)),
					mysqli_real_escape_string($d, $Label_type),
					isset($hasLanguage_resource) ? mysqli_real_escape_string($d, $hasLanguage_resource) : 'null',
					isset($isNameOf_resource) ? $isNameOf_resource : mysqli_real_escape_string($d, $isNameOf_resource),
					isset($typeOfName) ? $typeOfName : mysqli_real_escape_string($d, $typeOfName)
				);

				if (mysqli_query($d, $q)) {

					$i++;

				} else {

					echo 'Failed inserting '.$Label.' ('.mysqli_error($d).'<!--'.$q.'-->)<br />'.chr(10);

					var_dump($q);

				}

			} else {

				if (!isset($Label))
					echo 'No name for "'.$Description_about.'" - record not saved<br />'.chr(10);
				else
					$skippedProbablyUseless++;

			}
			$rec = false;
			$concept_id = $language_id = $Label = $Label_type = $hasLanguage_resource = $typeOfName = $isNameOf_resource = null;
		}

		if ($rec) {

			if (preg_match('/\<rna:is(AlternativeName|Anamorf|Basionym|Homonym|InvalidName|MisspelledName|NomenDubium|PreferredName|Synonym|SynonymSL|ValidName)Of rdf:resource="(https?\:\/\/)([a-z0-9-]+){0,1}((\.)([a-z0-9-]+))+\/([a-z0-9-]+\/)*([a-z0-9-]{36})"(\s|\/)*\>/i',$line,$m)===1) {
				$typeOfName = $m[1];
				$isNameOf_resource = $m[8];
				$concept_id = isset($concepts[$isNameOf_resource]) ? $concepts[$isNameOf_resource] : null;
			} else
			if (preg_match('/^\<skos:(.+)Label xml:lang="(.+)"\>(.+)\<\/skos:(.+)Label\>$/',$line,$m)===1) {
				$Label_type = $m[1].'Label';
				$Label = trim($m[3]);
			} else
			if (preg_match('/\<rna:hasLanguage rdf:resource="(https?\:\/\/)([a-z0-9-]+){0,1}((\.)([a-z0-9-]+))+\/([a-z0-9-]+\/)*([a-z0-9-]{36})"(\s|\/)*\>/i',$line,$m)===1) {
				$hasLanguage_resource = $m[7];
				$language_id = isset($languages[$hasLanguage_resource]) ? $languages[$hasLanguage_resource] : null;
			}

		}


	}
	fclose($file);

	echo '<p>wrote '.$i.' names (skipped '.$skippedProbablyUseless.' without a name; probably entries for alphabet letters)</p>'.chr(10);	


	echo '<h2>parsing habitats</h2>'.chr(10);

	$structure = crunchSimpleStructure($fHabitats,$qHabitats,'habitats');

	$habitats = $structure[1];

	echo '<p>wrote '.$structure[0].' habitats</p>'.chr(10);


	echo '<h2>parsing presences</h2>'.chr(10);

	$structure = crunchSimpleStructure($fPresence,$qPresence,'presence_statussen');

	$precenses = $structure[1];

	echo '<p>wrote '.$structure[0].' presences</p>'.chr(10);


	echo '<h2>parsing taxon statuses</h2>'.chr(10);

	mysqli_query($d, "truncate nsr_taxon_statussen");

	$file = fopen($fTaxonPresence, "r");
	$i = $f = $noStatus = $noHabitat = $verbHabitat = $verbConcept = $justLabel = $skippedProbablyUseless = 0;
	$rec = false;

	while (!feof($file)) {

		$line = trim(fgets($file));
		preg_match($regExpDescription,$line,$m);

		if (isset($m[7]) && !empty($m[7])) {
			$Start_line = $line;
			$Description_about = $m[7];
			$rec = true;
		}

		if ($line=='</rdf:Description>') {

			if (isset($concept_id) || isset($concept_verbatim) || isset($prefLabel)) {

				$q = sprintf($qTaxonPresence, 
					isset($concept_id) ? $concept_id : 'null',
					isset($status_id) ? $status_id : 'null',
					isset($status82_id) ? $status82_id : 'null',
					isset($habitat_id) ? $habitat_id : 'null',
					isset($is_indigeneous) ? $is_indigeneous : 'null',
					isset($habitat_verbatim) ? mysqli_real_escape_string($d, $habitat_verbatim) : 'null',
					isset($concept_verbatim) ? mysqli_real_escape_string($d, $concept_verbatim) : 'null',
					isset($prefLabel) ? mysqli_real_escape_string($d, $prefLabel) : 'null'
				);

				if (mysqli_query($d, $q)) {

					if (!isset($status_id))
						$noStatus++;
					if (!isset($habitat_id))
						$noHabitat++;
					if (!isset($habitat_id) && isset($habitat_verbatim))
						$verbHabitat++;
					if (!isset($concept_id) && isset($concept_verbatim))
						$verbConcept++;
					if (!isset($concept_id) && !isset($concept_verbatim) && isset($prefLabel))
						$justLabel++;


					$i++;

				} else {

					echo 'Failed inserting '.$Description_about.' ('.mysqli_error($d).'<!--'.$q.'-->)<br />'.chr(10);

				}

			} else {
				//echo 'Could not resolve concept ID for "'.(isset($Label_nl) ? $Label_nl : (isset($Label_en) ? $Label_en : $Description_about)).'" (record not saved)<br />'.chr(10);
				$skippedProbablyUseless++;
			}
			$rec = false;
			$concept_id = $status_id = $status82_id = $habitat_id = $is_indigeneous = $prefLabel = $habitat_verbatim = $concept_verbatim = $prefLabel = $Label_en = $Label_nl = null;
		}


		if ($rec) {

			if (stripos($line,'<skos:prefLabel xml:lang="en">')!==false) {
				$Label_en = strip_tags($line);
			} else
			if (stripos($line,'<skos:prefLabel xml:lang="nl">')!==false) {
				$Label_nl = strip_tags($line);
			} else
			if (stripos($line,'<skos:prefLabel xml:lang="sci">')!==false) {
				$prefLabel = strip_tags($line);
			} else
			if (preg_match(sprintf($regExpTaxonPresence,'isTaxonStateOf'),$line,$m)===1) {
				$concept_id = isset($concepts[$m[7]]) ? $concepts[$m[7]] : null;
			} else
			if (stripos($line,'<rna:isTaxonStateOf>')!==false) {
				$concept_verbatim = strip_tags($line);
			} else
			if (preg_match(sprintf($regExpTaxonPresence,'hasPresenceStatus'),$line,$m)===1) {
				$status_id = isset($precenses[$m[7]]) ? $precenses[$m[7]] : null;
			} else
			if (preg_match(sprintf($regExpTaxonPresence,'hasPresenceStatus1982'),$line,$m)===1) {
				$status82_id = isset($precenses[$m[7]]) ? $precenses[$m[7]] : null;
			} else
			if (preg_match(sprintf($regExpTaxonPresence,'hasHabitat'),$line,$m)===1) {
				$habitat_id = isset($habitats[$m[7]]) ? $habitats[$m[7]] : null;
			} else
			if (stripos($line,'<rna:hasHabitat>')!==false) {
				$habitat_verbatim = strip_tags($line);
			} else
			if (stripos($line,'<rna:isIndigeneous>')!==false) {
				$is_indigeneous = strtolower(trim(strip_tags($line)))=='ja' ? '1' : '0';
			}

		}


	}
	fclose($file);

	echo '<p>wrote '.$i.' taxon statuses<br />'.
	'skipped '.$skippedProbablyUseless.' without a name; probably entries for alphabet letters<br />'.
	$verbConcept.' with a verbatim concept (unresolved), '.$justLabel.' with just a status label (<i>definitely</i> unresolved);<br/>'.
	$noStatus.' without status, '.$noHabitat.' without habitat, '.$verbHabitat.' with a unresolved verbatim habitat</p>'.chr(10);


	echo '<p><i>breakdown concepts per rank:</i><br />'.chr(10);


	foreach((array)$c as $key => $val) 
		echo $r[$key].': '.$val.'<br />'.chr(10);

	echo '</p>'.chr(10);


	echo 'to do:
		<ol>
			<li>add hasNsrId for all tables</li>
			<li>proces other files</li>
		</ol>';

/*


CREATE TABLE IF NOT EXISTS habitats (				// corresponds to "Habitats.rdf.xml"
  id int(11) NOT NULL AUTO_INCREMENT,
  Description_about varchar(42) NOT NULL,			// unique identifier, stripped of the html tag and the URI
  prefLabel_nl varchar(16) NOT NULL,				// preferred label in Dutch
  PRIMARY KEY (id),
  UNIQUE KEY Description_about (Description_about,prefLabel_nl)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS languages (				// corresponds to "Languages.rdf.xml"
  id int(11) NOT NULL AUTO_INCREMENT,
  Description_about varchar(36) NOT NULL,			// unique identifier, stripped of the html tag and the URI
  prefLabel_nl varchar(16) NOT NULL,				// preferred label in Dutch
  iso_639_6 varchar(6) DEFAULT NULL,				// ISO code, resolved through array at the top of this file
  PRIMARY KEY (id),
  UNIQUE KEY Description_about (Description_about,prefLabel_nl)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS nsr_taxon_concepten (	// corresponds to "NSR taxon concepten.rdf.xml"
  id int(11) NOT NULL AUTO_INCREMENT,
  Description_about varchar(42) NOT NULL,			// unique identifier, stripped of the html tag and the URI
  Preflabel_sci varchar(255) NOT NULL,				// preferred scientific name
  hasTaxonRank_resource varchar(255) NOT NULL,		// identifier pointing to the rank in taxon_ranks
  broader varchar(42) DEFAULT NULL,					// identifier pointing to the parent (nsr_taxon_concepten.Description_about)
  hasNsrId varchar(255) DEFAULT NULL,				// NSR-id, corresponds to the last bit of the URL on the site
  mutationDate varchar(24) NOT NULL,				// mutation date, taken as string from xml-file
  PRIMARY KEY (id),
  UNIQUE KEY Description_about (Description_about),
  KEY broader (broader)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS nsr_taxon_namen (		// corresponds to "NSR taxon namen.rdf.xml"
  id int(11) NOT NULL AUTO_INCREMENT,
  concept_id int(11) DEFAULT NULL,					// id of the corresponding record in "nsr_taxon_concepten"; can be null, if unresolvable
  language_id int(11) DEFAULT NULL,					// id of the corresponding record in "languages"
  Label varchar(128) NOT NULL,						// the actual name or synonym
  Label_type varchar(16) NOT NULL,					// "prefLabel" or "altLabel" (preferred or alternative)
  hasLanguage_resource varchar(35) DEFAULT NULL,	// full resource ID of the language (backup to `language_id`)
  isNameOf_resource varchar(36) DEFAULT NULL,		// full resource ID of the concept (backup to `concept_id`; but can be just as null)
  typeOfName varchar(16) NOT NULL,					// ValidName,Synonym,Basionym,Homonym,PreferredName,AlternativeName,MisspelledName,InvalidName,SynonymSL,null
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS nsr_taxon_statussen (	// corresponbds to "NSR taxon statussen.rdf.xml"
  id int(11) NOT NULL AUTO_INCREMENT,
  concept_id int(11) DEFAULT NULL,					// id of the corresponding record in "nsr_taxon_concepten"; can be null, if unresolvable (<rna:isTaxonStateOf />)
  concept_verbatim varchar(64) DEFAULT NULL,		// verbatim concept (<rna:isTaxonStateOf>Name</rna:isTaxonStateOf>); can ALSO be null - how TERRIFIC!
  Preflabel_sci varchar(64) DEFAULT NULL,			// verbatim label (when all else fails); "Epinotia maculana (Fabricius, 1775) (presence)"
  status_id int(11) DEFAULT NULL,					// id of the corresponding record in "presence_statussen"; can be null, if unresolvable
  status82_id int(11) DEFAULT NULL,					// id of the corresponding record in "presence_statussen"; can be null, if unresolvable
  habitat_id int(11) DEFAULT NULL,					// id of the corresponding record in "habitats"; can be null, if unresolvable
  habitat_verbatim varchar(36) DEFAULT NULL,		// verbatim habitat (<rna:hasHabitat>marien zoet</rna:hasHabitat>); can also be null (ofcourse)
  is_indigeneous tinyint(1) DEFAULT NULL,			// 1, 0 or null
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS presence_statussen (		// corresponbds to "NSR taxon statussen.rdf.xml"
  id int(11) NOT NULL AUTO_INCREMENT,
  Description_about varchar(42) NOT NULL,			// unique key
  prefLabel_nl varchar(64) NOT NULL,				// label (verdwenen, verschenen, etc)
  PRIMARY KEY (id),
  UNIQUE KEY Description_about (Description_about,prefLabel_nl)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS taxon_ranks (			// corresponds to "Taxon ranks.rdf.xml"
  id int(11) NOT NULL AUTO_INCREMENT,
  Description_about varchar(36) NOT NULL,			// unique identifier, stripped of the html tag and the URI
  Parent_about varchar(36) DEFAULT NULL,			// parent ID (hierarchy)
  Preflabel_sci varchar(255) NOT NULL,				// scientific label
  Preflabel_en varchar(255) DEFAULT NULL,			// english label
  Preflabel_nl varchar(255) DEFAULT NULL,			// dutch label
  mutationDate varchar(24) DEFAULT NULL,			// mutation date, taken as string from xml-file (fuck knows why)
  PRIMARY KEY (id),
  UNIQUE KEY Description_about (Description_about)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



*/

/*

	prefLabel	sci		(accepted) scientific name
	altLabel	sci		synonym
	hiddenLabel	sci		alt spellings for search purposes (only two occurrences, "Hoefijzerneus" and "Roofdier"!)
	prefLabel	nl		dutch name
	altLabel	nl		alt dutch name
	hiddenLabel	nl		alt spellings for search purposes 
	prefLabel	en		english name
	altLabel	en		alt dutch name
	prefLabel	ger		german name
	prefLabel	fre		french name
	prefLabel	spa		spanish name
	altLabel	spa		alt spanish name

    name: ok (will be a combination of preferred and alternative common names)
    language code: ok (NSR uses ISO 639-3, so will have to convert)
    geography: not present in NSR data
    period: not present in NSR data
    taxon: ok
    reference: they all list the same source, "NSR" (alternatively, there's a literature reference, like "Heukels' Flora van Nederland [23e druk]")


	// common name export for OpenBio, July 2013 (NBCBIO-18):

	select
		_a.Label as Name,
		_b.iso_639_6,
		_c.Preflabel_sci as Taxon,
		'NSR' as Source,
		_a.typeOfName
	from
		nsr_taxon_namen _a
	left join languages _b
		on _b.id = _a.language_id
	left join nsr_taxon_concepten _c
		on _a.concept_id = _c.id
	left join taxon_ranks _d 
		on _c.hasTaxonRank_resource = _d.Description_about
	where 
		_a.typeOfName in ('Valid','Alternative','Preferred')
		and _a.language_id != 6
		and _a.Label_type != 'hiddenLabel'
		and _d.Preflabel_sci in ('species','varietas','subspecies','cultivar','forma','forma_specialis')
	order by 
		FIELD(_d.Preflabel_sci,'regnum','phylum','classis','subclassis','ordo','familia','genus','species','subspecies','varietas','cultivar','forma','forma_specialis'), 
		Taxon
	;


    isAlternativeNameOf
    isAnamorfOf
    isBasionymOf
    isHomonymOf
    isInvalidNameOf
    isMisspelledNameOf
    isNomenDubiumOf
    isPreferredNameOf
    isSynonymOf
    isSynonymSLOf
    isValidNameOf


*/
/*

parsing languages
No Dutch name for "Languages" (089aa4b0-b942-47eb-a0cb-5e25feb050e5) - record not saved
No ISO639-6 code avaialble for "Wetenschappelijk" (saved record anyway)

wrote 7 languages
parsing ranks
No scientific name for "Taxon ranks" (5305dbb5-083d-4366-90fe-e28d4d4e04c4) - record not saved

wrote 17 ranks
parsing taxon concepts
No scientific name for "NSR taxon concepten" (record not saved)

wrote 56565 concepts
parsing taxon names & synonyms

wrote 87481 names (skipped 165 without a name; probably entries for alphabet letters)
parsing habitats
No Dutch name for "Habitats" (3cc96896-f26f-4ff1-acdc-d88010b5dcce) - record not saved

wrote 6 habitats
parsing presences
No Dutch name for "4e6cf09e-5395-4d71-a7e1-e8cfa7e903c4" (4e6cf09e-5395-4d71-a7e1-e8cfa7e903c4) - record not saved

wrote 23 presences
parsing taxon statuses

wrote 41065 taxon statuses
skipped 29 without a name; probably entries for alphabet letters
16 with a verbatim concept (unresolved), 322 with just a status label (definitely unresolved);
7 without status, 17784 without habitat, 343 with a unresolved verbatim habitat

breakdown concepts per rank:
regnum: 3
phylum: 40
classis: 85
ordo: 465
familia: 2244
genus: 12555
species: 39531
varietas: 725
subspecies: 749
cultivar: 6
forma: 140
forma_specialis: 21
subclassis: 1


*/

?>
