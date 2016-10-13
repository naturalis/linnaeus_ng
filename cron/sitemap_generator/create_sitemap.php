<?php
/*
 * This class creates a sitemap for Linnaeus. Currently there is no way
 * to link projects to absolute urls required for the sitemap. A provisional
 * solution is to initialize the class with an array like this:
 *
 * $class->setDomains(array(
 *     project_id_1 => 'absolute_url_1',
 *     project_id_2 => 'absolute_url_2',
 *     etc...
 * );
 *
 * where the absolute_url is the full domain:
 * http://project.linnaeus.naturalis.nl
 *
 */

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    class SitemapCreator
    {
		private $config;
	    private $mysqli;
	    private $tablePrefix;
	    private $outputDir;
	    private $domains;
	    private $files = array();
	    private $projects = array();
	    private $projectId;
	    private $domain;
	    private $moduleId;
	    private $modulePages;
	    private $mediaPages;
	    private $xmlWriter;

        public function __construct ()
        {
            $this->getConfig();
            $this->connectDb();
            $this->setOutputDir();
            $this->initXmlWriter();
        }

        public function __destruct ()
        {
            if ($this->xmlWriter) {
                unset($this->xmlWriter);
            }
        }

		public function run () {
            $this->bootstrap();
            $this->deleteOldFiles();
            $this->writeFiles();
		}

		private function bootstrap ()
		{
            // Domains array is a must!
            if (!is_array($this->domains) || empty($this->domains)) {
                die("Domains should be set in an array: please refer to docs for instruction!\n");
            }
            // Test if output directory exists; if not try to create it
            if (!file_exists($this->outputDir)) {
                if (!mkdir($this->outputDir)) {
                    die('Cannot create directory ' . $this->outputDir . "\n");
                }
            }
		    // Test output directory
		    if (!is_writable($this->outputDir)) {
                die($this->outputDir . " is not writable!\n");
		    }
		}

		private function deleteOldFiles ()
		{
		    array_map('unlink', glob($this->outputDir . '*'));
		}

		private function getConfig ()
		{
            require_once dirname(__FILE__) . '/../../configuration/admin/configuration.php';
            $this->config = new configuration();
		}

		private function initXmlWriter ()
		{
            $this->xmlWriter = new XMLWriter();
            $this->xmlWriter->openMemory();
            $this->xmlWriter->setIndent(true);
            $this->xmlWriter->setIndentString("   ");
		}

		private function writeFiles ()
		{
            foreach ($this->domains as $this->projectId => $this->domain) {
                if ($this->projectIsPublic()) {
                    $this->domain .= '/linnaeus_ng/app/views/';
                    $this->writeIntroduction();
                    $this->writeKey();
                    $this->writeTaxa();
                }
            }
		}

		private function writeIntroduction ()
		{
            if ($this->moduleIsPublic('introduction')) {
                $this->getIntroductionPages();
                $this->writeModulePages('introduction_' . $this->projectId . '.xml');
            }
		}

    	private function writeKey ()
		{
            if ($this->moduleIsPublic('key')) {
                $this->getKeyPages();
                $this->writeModulePages('key_' . $this->projectId . '.xml');
            }
		}

        private function writeTaxa ()
        {
            if ($this->moduleIsPublic('nsr')) {
                $this->getTaxonPages();
                $this->writeModulePages('taxon_' . $this->projectId . '.xml');
            }
        }

		private function writeModulePages ($fileName)
		{
        	$this->xmlWriter->startDocument('1.0', 'UTF-8');
        	$this->xmlWriter->startElement('urlset');
        	$this->xmlWriter->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        	$this->xmlWriter->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

        	foreach ($this->modulePages as $id => $url) {
        		$this->xmlWriter->startElement('url');
        		$this->xmlWriter->writeElement('loc', $this->domain . $url);

                if (isset($this->mediaPages[$id])) {
                    foreach ($this->mediaPages[$id] as $media) {
                        $this->xmlWriter->startElement('image:image');
                        $this->xmlWriter->writeElement('image:loc', $media['loc']);
                        if (!empty($media['caption'])) {
                            $this->xmlWriter->writeElement('image:caption', $media['caption']);
                        }
                        $this->xmlWriter->endElement();
                    }
        		}

        	    $this->xmlWriter->endElement();
        	}

        	$this->xmlWriter->endElement();
        	file_put_contents(
        	   $this->outputDir . $fileName,
        	   $this->xmlWriter->flush(true),
        	   FILE_APPEND
        	);

        	$this->files[$this->projectId][] = $this->outputDir . $fileName;
		}

		private function getIntroductionPages ()
		{
            $this->resetPages();
            $q = '
                select id
                from
                    ' . $this->tablePrefix . 'introduction_pages
                where
                    project_id = ' . $this->projectId;
            $r = $this->mysqli->query($q);
            while ($row = $r->fetch_assoc()) {
                $this->modulePages[$row['id']] = 'introduction/topic.php?id=' . $row['id'] .
                    '&epi=' . $this->projectId;
            }
		}

		/* Only fetch pages with a title! */
		private function getKeyPages ()
		{
            $this->resetPages();
		    $q = '
                select t1.id
                from
                    ' . $this->tablePrefix . 'keysteps as t1
                left join
                    ' . $this->tablePrefix . 'content_keysteps as t2 on t1.id = t2.keystep_id
                where
                    t2.title is not null
                    and t2.title != ""
                    and t2.title != t1.number
                    and t1.project_id = ' . $this->projectId;
		            $r = $this->mysqli->query($q);
            while ($row = $r->fetch_assoc()) {
                $this->modulePages[$row['id']] = 'species/nsr_taxon.php?id=' . $row['id'] .
                    '&epi=' . $this->projectId;
            }
		}

		private function getTaxonPages ()
		{
		    $this->resetPages();
		    $q = '
                select id
		        from
                    ' . $this->tablePrefix . 'taxa
		        where
		            is_empty = 0
                    and project_id = ' . $this->projectId . '
                    limit 50000';

		    $r = $this->mysqli->query($q);
            while ($row = $r->fetch_assoc()) {
                $this->modulePages[$row['id']] = 'key/index.php?choice=' . $row['id'] .
                    '&epi=' . $this->projectId;
            }

		    $this->setModuleId('nsr');
		    foreach ($this->modulePages as $id => $url) {
                $this->setTaxonMedia($id);
            }
		}

		private function setTaxonMedia ($id)
		{
            $q = '
                select t1.rs_original, t1.mime_type, t3.caption
                from
                    ' . $this->tablePrefix . 'media as t1
                left join
                    ' . $this->tablePrefix . 'media_modules as t2 on t1.id = t2.media_id
                left join
                    ' . $this->tablePrefix . 'media_captions as t3 on t2.id = t3.media_modules_id
                left join
                    ' . $this->tablePrefix . 'languages_projects as t4 on t1.project_id = t4.project_id
                    and t3.language_id = t4.language_id
                where
                    t2.module_id = ' . $this->moduleId . '
                    and t2.item_id = ' . $id . '
                    and t1.deleted = 0
                    and t4.def_language = 1
                    and t1.project_id = ' . $this->projectId;
		    $r = $this->mysqli->query($q);
            while ($row = $r->fetch_assoc()) {
                // Only images for now
                if (strpos($row['mime_type'], 'image') !== false) {
                    $this->mediaPages[$id][] = array(
                        'loc' => $row['rs_original'],
                        'caption' => $row['caption']
                    );
                }
            }
		}

		private function setModuleId ($controller)
		{
            $q = 'select id from ' . $this->tablePrefix . 'modules where controller = "' .
                $this->mysqli->real_escape_string($controller) . '"';
            $r = $this->mysqli->query($q);
            $row = $r->fetch_assoc();
            $this->moduleId = isset($row['id']) ? $row['id'] : 0;
		}

		private function projectIsPublic ()
		{
            $q = '
                select 1
                from
                    ' . $this->tablePrefix . 'projects
                where
                    id = ' . $this->projectId . '
                    and published = 1';
            $r = $this->mysqli->query($q);
            return $r->num_rows == 1;
		}

		private function moduleIsPublic ($controller)
		{
            $q = '
                select 1
                from
                    ' . $this->tablePrefix . 'modules_projects as t1
                left join
                    ' . $this->tablePrefix . 'modules as t2 on t1.module_id = t2.id
                where
                    t2.controller = "' . $controller .'"
                    and t1.project_id = ' . $this->projectId . '
                    and t1.active = "y"';
            $r = $this->mysqli->query($q);
            return $r->num_rows == 1;
		}

		private function resetPages ()
		{
            $this->modulePages = $this->mediaPages = array();
            $this->moduleId = false;
		}

		public function setOutputDir ($path = false)
		{
            $this->outputDir = dirname(__FILE__) . '/../../www/' . (!$path ? 'sitemap/' : $path . '/');
		}

		public function setDomains ($d = array())
		{
		    $this->domains = $d;
		}

    	private function connectDb()
		{
			$c = $this->config->getDatabaseSettings();
			$this->tablePrefix = $c['tablePrefix'];

		    $this->mysqli = new mysqli($c['host'], $c['user'], $c['password'], $c['database']);

			if ($this->mysqli->connect_error) {
				throw new Exception($this->mysqli->connect_error . "\n");
			}

			$this->mysqli->query('SET NAMES ' . $c['characterSet']);
			$this->mysqli->query('SET CHARACTER SET ' . $c['characterSet']);
		}
    }


    $smc = new SitemapCreator();
    $smc->setDomains(array(
        85 => 'http://insecten_van_europa.linnaeus.naturalis.nl',
        2 => 'http://de_interactieve_paddenstoelengids.linnaeus.naturalis.nl',
        101 => 'http://dagvlinders_van_europa.linnaeus.naturalis.nl',
        11 => 'http://turtles_of_the_world.linnaeus.naturalis.nl',
        6 => 'http://vogels_van_europa.linnaeus.naturalis.nl',
        7 => 'http://interactive_guide_to_caribbean_diving.linnaeus.naturalis.nl',
        8 => 'http://euphausiids_of_the_world_ocean.linnaeus.naturalis.nl',
        168 => 'http://sponges_of_the_north_east_atlantic_2.0.linnaeus.naturalis.nl',
/*        67 => 'http://reptielen_&_amfibieën.linnaeus.naturalis.nl',
        116 => 'http://marine_planarians_of_the_world.linnaeus.naturalis.nl',
        97 => 'http://zoetwatervissen_van_nederland.linnaeus.naturalis.nl',
        23 => 'http://zooplankton_of_the_south_atlantic_ocean.linnaeus.naturalis.nl',
        24 => 'http://reef_corals_of_the_indo-malayan_seas.linnaeus.naturalis.nl',
        25 => 'http://marine_lobsters_of_the_world.linnaeus.naturalis.nl',
        26 => 'http://north_australian_sea_cucumbers.linnaeus.naturalis.nl',
        27 => 'http://macrobenthos_of_the_north_sea_-_miscellaneous_worms.linnaeus.naturalis.nl',
        93 => 'http://macrobenthos_of_the_north_sea_-_polychaeta.linnaeus.naturalis.nl',
        103 => 'http://plant_resources_of_south-east_asia,_edible_fruits_and_nuts.linnaeus.naturalis.nl',
        34 => 'http://zooplankton_and_micronekton_of_the_north_sea.linnaeus.naturalis.nl',
        32 => 'http://crabs_of_japan.linnaeus.naturalis.nl',
        87 => 'http://otoliths_of_north_sea_fish_1.0.linnaeus.naturalis.nl',
        55 => 'http://agromyzidae_of_the_world.linnaeus.naturalis.nl',
        164 => 'http://eurasian_tortricidae_2.0.linnaeus.naturalis.nl',
        43 => 'http://flora_of_the_burren_and_southeast_connemara.linnaeus.naturalis.nl',
        94 => 'http://marine_planktonic_ostracods.linnaeus.naturalis.nl',
        77 => 'http://chironomidae_exuviae.linnaeus.naturalis.nl',
        210 => 'http://zooplankton_and_micronekton_of_the_north_sea_2.0.linnaeus.naturalis.nl',
        51 => 'http://interactive_flora_of_the_british_isles.linnaeus.naturalis.nl',
        162 => 'http://butterflies_of_europe.linnaeus.naturalis.nl',
        59 => 'http://marine_mammals.linnaeus.naturalis.nl',
        195 => 'http://braconidae_-_an_illustrated_key_to_all_subfamilies.linnaeus.naturalis.nl',
        117 => 'http://flora_malesiana_-_caesalpinioideae.linnaeus.naturalis.nl',
        76 => 'http://turbellaria_of_the_world.linnaeus.naturalis.nl',
        74 => 'http://sharks_of_the_world.linnaeus.naturalis.nl',
        142 => 'http://tree_seedlings_of_indonesia.linnaeus.naturalis.nl',
        124 => 'http://freshwater_oligochaeta_of_north-west_europe_1.0.linnaeus.naturalis.nl',
        81 => 'http://torymus.linnaeus.naturalis.nl',
        82 => 'http://pteromalus.linnaeus.naturalis.nl',
        129 => 'http://plant_resources_of_south-east_asia,_bamboos.linnaeus.naturalis.nl',
        96 => 'http://prosea_timber_trees.linnaeus.naturalis.nl',
        92 => 'http://blackflies_of_northern_europe_(diptera:_simuliidae).linnaeus.naturalis.nl',
        202 => 'http://useful_plants_-_an_introduction_to_economic_botany.linnaeus.naturalis.nl',
        128 => 'http://plant_resources_of_south-east_asia,_rattans.linnaeus.naturalis.nl',
        137 => 'http://interactive_guide_to_mushrooms_and_other_fungi.linnaeus.naturalis.nl',
        119 => 'http://decapodos_de_chile.linnaeus.naturalis.nl',
        115 => 'http://cultivation_and_farming_of_marine_plants.linnaeus.naturalis.nl',
        125 => 'http://de_interactieve_duikgids.linnaeus.naturalis.nl',
        192 => 'http://blackflies_of_northern_europe_(diptera:_simuliidae)_25.linnaeus.naturalis.nl',
        190 => 'http://chironomidae_larvae.linnaeus.naturalis.nl',
        204 => 'http://mush33.linnaeus.naturalis.nl',
        207 => 'http://birds_of_europe_3.1.linnaeus.naturalis.nl',
        135 => 'http://reptiles_and_amphibians_of_the_british_isles.linnaeus.naturalis.nl',
        203 => 'http://orchids_of_the_philippines_2.0.linnaeus.naturalis.nl',
        141 => 'http://fishes_of_the_northeastern_atlantic_and_mediterranean.linnaeus.naturalis.nl',
        143 => 'http://sponges_of_the_ne_atlantic.linnaeus.naturalis.nl',
        209 => 'http://belgische_flora_test_2014.linnaeus.naturalis.nl',
        145 => 'http://libellenlarven_van_nederland.linnaeus.naturalis.nl',
        148 => 'http://lemurs.linnaeus.naturalis.nl',
        147 => 'http://bird_remains_identification_system.linnaeus.naturalis.nl',
        150 => 'http://davalliaceae_1,_a_family_of_old_world_(sub-)tropical_ferns.linnaeus.naturalis.nl',
        152 => 'http://bats_of_the_indian_subcontinent.linnaeus.naturalis.nl',
        169 => 'http://pelagic_molluscs_2.0.linnaeus.naturalis.nl',
        155 => 'http://diaspididae_of_the_world_2.0.linnaeus.naturalis.nl',
        156 => 'http://tutorial_preparation_genitalia.linnaeus.naturalis.nl',
        157 => 'http://mormyridae_(osteoglossomorpha).linnaeus.naturalis.nl',
        159 => 'http://fish_eggs_and_larvae_from_amw.linnaeus.naturalis.nl',
        211 => 'http://european_limnofauna.linnaeus.naturalis.nl',
        165 => 'http://flora_malesiana_-_caesalpinioideae_2.0.linnaeus.naturalis.nl',
        167 => 'http://harmful_marine_dinoflagellates.linnaeus.naturalis.nl',
        171 => 'http://macrobenthos_of_the_north_sea_-_platyhelminthes.linnaeus.naturalis.nl',
        172 => 'http://macrobenthos_of_the_north_sea_-_sipuncula.linnaeus.naturalis.nl',
        173 => 'http://macrobenthos_of_the_north_sea_-_nemertina.linnaeus.naturalis.nl',
        177 => 'http://macrobenthos_of_the_north_sea_-_brachiopoda.linnaeus.naturalis.nl',
        176 => 'http://macrobenthos_of_the_north_sea_-_anthozoa.linnaeus.naturalis.nl',
        180 => 'http://macrobenthos_of_the_north_sea_-_crustacea.linnaeus.naturalis.nl',
        181 => 'http://macrobenthos_of_the_north_sea_-_tunicata.linnaeus.naturalis.nl',
        182 => 'http://macrobenthos_of_the_north_sea_-_pycnogonida.linnaeus.naturalis.nl',
        183 => 'http://macrobenthos_of_the_north_sea_-_mollusca.linnaeus.naturalis.nl',
        185 => 'http://macrobenthos_of_the_north_sea_-_echinodermata.linnaeus.naturalis.nl',
        186 => 'http://flora_arbórea_de_chile.linnaeus.naturalis.nl',
        193 => 'http://aetideidae_of_the_world_ocean.linnaeus.naturalis.nl'
        */
    ));
    $smc->run();