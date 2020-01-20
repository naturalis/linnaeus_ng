<?php
/*
 * This class creates a sitemap for Linnaeus. Currently there is no way
 * to link projects to absolute urls required for the sitemap.
 *
 * To fix this, this script needs to be told the full server name. This should be
 * set in configuration/admin/configuration.php:
 *
 *  public function getProjectsDomains ()
    {
        return [
            // 1 => 'https://example.linnaeus.naturalis.nl',
        ];
    }
 *
 */

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    class SitemapCreator
    {
        private $config;
        private $dbSettings;
	    private $mysqli;
	    private $tablePrefix;
	    private $outputDir;
	    private $domains;
	    private $files = [];
	    private $projectId;
	    private $domain;
	    private $modulePath;
	    private $moduleId;
	    private $modulePages;
	    private $mediaPages;
	    private $xmlWriter;
	    private $linksPerSitemap = 50000;

        public function __construct ()
        {
            $this->setConfig();
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

		public function run ()
        {
            $this->bootstrap();
            $this->setDomains();
            $this->deleteOldFiles();
            $this->writeFiles();
            $this->writeIndex();
            echo "Ready\n";
		}

		private function bootstrap ()
		{
		    // Test if domains are set for project ids
            if (!method_exists($this->config, 'getProjectsDomains') ||
                empty($this->config->getProjectsDomains())) {
                die('No relations between project ids and domain names defined in configuration/admin/configuration.php.' .
                "\nPlease enter required data to getProjectsDomains() method.\n");
            }
            // Test if output directory exists; if not try to create it
            if (!file_exists($this->outputDir) && !mkdir($this->outputDir) && !is_dir($this->outputDir)) {
                die('Cannot create directory ' . $this->outputDir . "\n");
            }
		    // Test output directory
		    if (!is_writable($this->outputDir)) {
                die($this->outputDir . " is not writable!\n");
		    }
		}

		private function deleteOldFiles ()
		{
		    array_map('unlink', glob($this->outputDir . 'sitemap*'));
		}

		private function setConfig ()
		{
		    require_once __DIR__ . '/../../configuration/admin/configuration.php';
            $this->config = new configuration();
            $this->dbSettings = $this->config->getDatabaseSettings();
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
            foreach ($this->domains as $this->projectId => $domain) {
                if ($this->projectIsPublic()) {
                    $this->setDomain($domain);
                    $this->writeIntroduction();
                    $this->writeKey();
                    $this->writeTaxa();
                }
            }
		}

        private function setDomain ($domain)
        {
            if (strpos($domain, 'https://') === false) {
                die('Protocol in domain name should start with https://! Currently is ' . $domain);
            }
            $this->domain = rtrim($domain, '/') . '/';
            $this->modulePath = $this->domain . 'linnaeus_ng/app/views/';
        }

		private function writeIntroduction ()
		{
            if ($this->moduleIsPublic('introduction')) {
                $this->getIntroductionPages();
                $this->writeModulePages('sitemap_introduction');
            }
		}

    	private function writeKey ()
		{
            if ($this->moduleIsPublic('key')) {
                $this->getKeyPages();
                $this->writeModulePages('sitemap_key');
            }
		}

        private function writeTaxa ()
        {
            if ($this->moduleIsPublic('nsr')) {
                $this->getTaxonPages();
                $this->writeModulePages('sitemap_taxon');
            }
        }

        private function writeIndex ()
        {
            $this->xmlWriter->startDocument('1.0', 'UTF-8');
            $this->xmlWriter->startElement('sitemapindex');
            $this->xmlWriter->startAttribute('xmlns');
            $this->xmlWriter->text('http://www.sitemaps.org/schemas/sitemap/0.9');
            $this->xmlWriter->endAttribute();
            foreach ($this->files as $projectId => $files) {
                foreach ($files as $file) {
                    $this->xmlWriter->startElement('sitemap');
                    $this->xmlWriter->writeElement('loc', $file);
                    $this->xmlWriter->writeElement('lastmod', date('Y-m-d'));
                    $this->xmlWriter->endElement();
                }
            }
            $this->xmlWriter->endElement();
            file_put_contents(
                $this->outputDir . 'sitemap_index.xml',
                $this->xmlWriter->flush(true),
                FILE_APPEND
            );
        }

		private function writeModulePages ($file)
		{

            $sets = array_chunk($this->modulePages, $this->linksPerSitemap);

		    foreach ($sets as $i => $pages) {

                $fileName = $file . '_' . $this->projectId . '_' . ($i + 1) . '.xml';

                $this->xmlWriter->startDocument('1.0', 'UTF-8');
                $this->xmlWriter->startElement('urlset');
                $this->xmlWriter->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
                $this->xmlWriter->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

                foreach ($pages as $id => $url) {

                    $this->xmlWriter->startElement('url');
                    $this->xmlWriter->writeElement('loc', $this->modulePath . $url);

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

                $this->files[$this->projectId][] = $this->domain . 'linnaeus_ng/' . $fileName;

            }
		}

        private function setDomains ()
        {
            $configDomains = $this->config->getProjectsDomains();

            $q = '
                select 
                    id
                from
                    ' . $this->tablePrefix . 'projects
                where
                    published = 1';
            $r = $this->mysqli->query($q);
            while ($row = $r->fetch_assoc()) {
                if (isset($configDomains[$row['id']])) {
                    $this->domains[$row['id']] = $configDomains[$row['id']];
                }
            }

            if (empty($this->domains)) {
                die('No relations between project ids and domain names found in configuration/admin/configuration.php.' .
                    "\nPlease enter required data to getProjectsDomains() method.\n");
            }
        }

        private function getIntroductionPages ()
		{
            $this->resetPages();
            $q = '
                select 
                    id
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
                select 
                    t1.id
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
                $this->modulePages[$row['id']] = 'key/index.php?step='. $row['id'] .
                    '&epi=' . $this->projectId;
            }
		}

		private function getTaxonPages ()
		{
		    $this->resetPages();
		    $q = '
                select 
                    id
		        from
                    ' . $this->tablePrefix . 'taxa
		        where
		            project_id = ' . $this->projectId;

		    $r = $this->mysqli->query($q);
            while ($row = $r->fetch_assoc()) {
                $this->modulePages[$row['id']] = 'species/nsr_taxon.php?id=' . $row['id'] .
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
                select 
                    t1.rs_original, 
                    t1.mime_type, 
                    t3.caption
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
            $q = '
                select 
                    id 
                from 
                    ' . $this->tablePrefix . 'modules 
                where 
                    controller = "' .
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

		public function setOutputDir ()
		{
            $this->outputDir = dirname(__FILE__) . '/../../www/';
		}

    	private function connectDb()
		{
			$this->tablePrefix = isset($this->dbSettings['tablePrefix']) ?
                $this->dbSettings['tablePrefix'] : '';
		    $this->mysqli = new mysqli(
		        $this->dbSettings['host'],
                $this->dbSettings['user'],
                $this->dbSettings['password'],
                $this->dbSettings['database']
            );

			if ($this->mysqli->connect_error) {
				throw new Exception($this->mysqli->connect_error . "\n");
			}

			$this->mysqli->query('SET NAMES ' . $this->dbSettings['characterSet']);
			$this->mysqli->query('SET CHARACTER SET ' . $this->dbSettings['characterSet']);
		}
    }


    $smc = new SitemapCreator();
    $smc->run();
