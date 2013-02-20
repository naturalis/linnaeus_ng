ALTER DATABASE CHARACTER SET = utf8;


-- MySQL dump 10.13  Distrib 5.1.48, for Win32 (ia32)
--
-- Host: localhost    Database: linnaeus_ng
-- ------------------------------------------------------
-- Server version	5.1.48-community

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dev_characteristics`
--

DROP TABLE IF EXISTS `dev_characteristics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_characteristics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `got_labels` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_characteristics`
--

LOCK TABLES `dev_characteristics` WRITE;
/*!40000 ALTER TABLE `dev_characteristics` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_characteristics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_characteristics_labels`
--

DROP TABLE IF EXISTS `dev_characteristics_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_characteristics_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` text NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`characteristic_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_characteristics_labels`
--

LOCK TABLES `dev_characteristics_labels` WRITE;
/*!40000 ALTER TABLE `dev_characteristics_labels` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_characteristics_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_characteristics_labels_states`
--

DROP TABLE IF EXISTS `dev_characteristics_labels_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_characteristics_labels_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` text NOT NULL,
  `text` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`state_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_characteristics_labels_states`
--

LOCK TABLES `dev_characteristics_labels_states` WRITE;
/*!40000 ALTER TABLE `dev_characteristics_labels_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_characteristics_labels_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_characteristics_matrices`
--

DROP TABLE IF EXISTS `dev_characteristics_matrices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_characteristics_matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `show_order` smallint(6) NOT NULL DEFAULT '-1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`characteristic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_characteristics_matrices`
--

LOCK TABLES `dev_characteristics_matrices` WRITE;
/*!40000 ALTER TABLE `dev_characteristics_matrices` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_characteristics_matrices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_characteristics_states`
--

DROP TABLE IF EXISTS `dev_characteristics_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_characteristics_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `lower` float(23,3) DEFAULT NULL,
  `upper` float(23,3) DEFAULT NULL,
  `mean` float(23,3) DEFAULT NULL,
  `sd` float(23,3) DEFAULT NULL,
  `got_labels` tinyint(1) DEFAULT '0',
  `show_order` tinyint(4) NOT NULL DEFAULT '-1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`characteristic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_characteristics_states`
--

LOCK TABLES `dev_characteristics_states` WRITE;
/*!40000 ALTER TABLE `dev_characteristics_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_characteristics_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_choices_content_keysteps`
--

DROP TABLE IF EXISTS `dev_choices_content_keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_choices_content_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `choice_txt` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`choice_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_choices_content_keysteps`
--

LOCK TABLES `dev_choices_content_keysteps` WRITE;
/*!40000 ALTER TABLE `dev_choices_content_keysteps` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_choices_content_keysteps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_choices_content_keysteps_undo`
--

DROP TABLE IF EXISTS `dev_choices_content_keysteps_undo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_choices_content_keysteps_undo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `choice_content_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `choice_txt` text,
  `choice_content_created` datetime NOT NULL,
  `choice_last_change` datetime NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `choice_content_id` (`choice_content_id`,`project_id`,`choice_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_choices_content_keysteps_undo`
--

LOCK TABLES `dev_choices_content_keysteps_undo` WRITE;
/*!40000 ALTER TABLE `dev_choices_content_keysteps_undo` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_choices_content_keysteps_undo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_choices_keysteps`
--

DROP TABLE IF EXISTS `dev_choices_keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_choices_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `keystep_id` int(11) DEFAULT NULL,
  `show_order` int(2) DEFAULT NULL,
  `choice_img` varchar(255) DEFAULT NULL,
  `choice_image_params` varchar(255) DEFAULT NULL,
  `res_keystep_id` int(11) DEFAULT NULL,
  `res_taxon_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`keystep_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_choices_keysteps`
--

LOCK TABLES `dev_choices_keysteps` WRITE;
/*!40000 ALTER TABLE `dev_choices_keysteps` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_choices_keysteps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_commonnames`
--

DROP TABLE IF EXISTS `dev_commonnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_commonnames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `commonname` varchar(64) DEFAULT NULL,
  `transliteration` varchar(64) DEFAULT NULL,
  `show_order` int(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`),
  KEY `taxon_id` (`taxon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_commonnames`
--

LOCK TABLES `dev_commonnames` WRITE;
/*!40000 ALTER TABLE `dev_commonnames` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_commonnames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_content`
--

DROP TABLE IF EXISTS `dev_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `subject` varchar(32) NOT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_content`
--

LOCK TABLES `dev_content` WRITE;
/*!40000 ALTER TABLE `dev_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_content_free_modules`
--

DROP TABLE IF EXISTS `dev_content_free_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_content_free_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `project_id_2` (`project_id`,`module_id`,`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_content_free_modules`
--

LOCK TABLES `dev_content_free_modules` WRITE;
/*!40000 ALTER TABLE `dev_content_free_modules` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_content_free_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_content_introduction`
--

DROP TABLE IF EXISTS `dev_content_introduction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_content_introduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `project_id_2` (`project_id`,`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_content_introduction`
--

LOCK TABLES `dev_content_introduction` WRITE;
/*!40000 ALTER TABLE `dev_content_introduction` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_content_introduction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_content_keysteps`
--

DROP TABLE IF EXISTS `dev_content_keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_content_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `keystep_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(64) DEFAULT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`keystep_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_content_keysteps`
--

LOCK TABLES `dev_content_keysteps` WRITE;
/*!40000 ALTER TABLE `dev_content_keysteps` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_content_keysteps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_content_keysteps_undo`
--

DROP TABLE IF EXISTS `dev_content_keysteps_undo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_content_keysteps_undo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keystep_content_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `keystep_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(64) DEFAULT NULL,
  `content` text,
  `keystep_content_created` datetime NOT NULL,
  `keystep_content_last_change` datetime NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `keystep_content_id` (`keystep_content_id`,`project_id`,`keystep_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_content_keysteps_undo`
--

LOCK TABLES `dev_content_keysteps_undo` WRITE;
/*!40000 ALTER TABLE `dev_content_keysteps_undo` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_content_keysteps_undo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_content_taxa`
--

DROP TABLE IF EXISTS `dev_content_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_content_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `content` longtext,
  `title` varchar(64) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`language_id`,`page_id`),
  KEY `project_id_2` (`project_id`,`publish`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_content_taxa`
--

LOCK TABLES `dev_content_taxa` WRITE;
/*!40000 ALTER TABLE `dev_content_taxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_content_taxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_content_taxa_undo`
--

DROP TABLE IF EXISTS `dev_content_taxa_undo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_content_taxa_undo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_taxa_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `content` longtext,
  `title` varchar(64) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '0',
  `content_taxa_created` datetime NOT NULL,
  `content_last_change` datetime NOT NULL,
  `save_type` enum('auto','manual') DEFAULT NULL,
  `save_label` varchar(64) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `content_taxa_id` (`content_taxa_id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_content_taxa_undo`
--

LOCK TABLES `dev_content_taxa_undo` WRITE;
/*!40000 ALTER TABLE `dev_content_taxa_undo` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_content_taxa_undo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_diversity_index`
--

DROP TABLE IF EXISTS `dev_diversity_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_diversity_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `type_id` int(10) NOT NULL,
  `boundary` geometrycollection NOT NULL,
  `boundary_nodes` text NOT NULL,
  `score` tinyint(4) NOT NULL,
  `encoded_json` text NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_diversity_index`
--

LOCK TABLES `dev_diversity_index` WRITE;
/*!40000 ALTER TABLE `dev_diversity_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_diversity_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_diversity_index_old`
--

DROP TABLE IF EXISTS `dev_diversity_index_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_diversity_index_old` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `type_id` int(10) NOT NULL,
  `boundary` geometrycollection NOT NULL,
  `boundary_nodes` text NOT NULL,
  `score` tinyint(4) NOT NULL,
  `encoded_json` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_diversity_index_old`
--

LOCK TABLES `dev_diversity_index_old` WRITE;
/*!40000 ALTER TABLE `dev_diversity_index_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_diversity_index_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_free_module_media`
--

DROP TABLE IF EXISTS `dev_free_module_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_free_module_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `thumb_name` varchar(128) DEFAULT NULL,
  `original_name` varchar(128) NOT NULL,
  `mime_type` varchar(32) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_free_module_media`
--

LOCK TABLES `dev_free_module_media` WRITE;
/*!40000 ALTER TABLE `dev_free_module_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_free_module_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_free_modules_pages`
--

DROP TABLE IF EXISTS `dev_free_modules_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_free_modules_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `got_content` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `show_order` mediumint(9) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_free_modules_pages`
--

LOCK TABLES `dev_free_modules_pages` WRITE;
/*!40000 ALTER TABLE `dev_free_modules_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_free_modules_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_free_modules_projects`
--

DROP TABLE IF EXISTS `dev_free_modules_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_free_modules_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module` varchar(32) NOT NULL,
  `description` text,
  `show_order` tinyint(2) NOT NULL DEFAULT '0',
  `show_alpha` tinyint(1) NOT NULL DEFAULT '0',
  `active` enum('y','n') NOT NULL DEFAULT 'y',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_free_modules_projects`
--

LOCK TABLES `dev_free_modules_projects` WRITE;
/*!40000 ALTER TABLE `dev_free_modules_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_free_modules_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_free_modules_projects_users`
--

DROP TABLE IF EXISTS `dev_free_modules_projects_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_free_modules_projects_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `free_module_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`user_id`,`free_module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_free_modules_projects_users`
--

LOCK TABLES `dev_free_modules_projects_users` WRITE;
/*!40000 ALTER TABLE `dev_free_modules_projects_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_free_modules_projects_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_geodata_types`
--

DROP TABLE IF EXISTS `dev_geodata_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_geodata_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `colour` varchar(6) DEFAULT NULL,
  `type` enum('marker','polygon','both') DEFAULT 'both',
  `show_order` smallint(2) NOT NULL DEFAULT '99',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_geodata_types`
--

LOCK TABLES `dev_geodata_types` WRITE;
/*!40000 ALTER TABLE `dev_geodata_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_geodata_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_geodata_types_titles`
--

DROP TABLE IF EXISTS `dev_geodata_types_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_geodata_types_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`type_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_geodata_types_titles`
--

LOCK TABLES `dev_geodata_types_titles` WRITE;
/*!40000 ALTER TABLE `dev_geodata_types_titles` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_geodata_types_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_glossary`
--

DROP TABLE IF EXISTS `dev_glossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_glossary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `term` varchar(255) NOT NULL,
  `definition` text NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `idx_glossary1` (`project_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_glossary`
--

LOCK TABLES `dev_glossary` WRITE;
/*!40000 ALTER TABLE `dev_glossary` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_glossary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_glossary_media`
--

DROP TABLE IF EXISTS `dev_glossary_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_glossary_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `glossary_id` int(11) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `thumb_name` varchar(128) DEFAULT NULL,
  `original_name` varchar(128) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `mime_type` varchar(32) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`glossary_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_glossary_media`
--

LOCK TABLES `dev_glossary_media` WRITE;
/*!40000 ALTER TABLE `dev_glossary_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_glossary_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_glossary_media_captions`
--

DROP TABLE IF EXISTS `dev_glossary_media_captions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_glossary_media_captions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`media_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_glossary_media_captions`
--

LOCK TABLES `dev_glossary_media_captions` WRITE;
/*!40000 ALTER TABLE `dev_glossary_media_captions` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_glossary_media_captions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_glossary_synonyms`
--

DROP TABLE IF EXISTS `dev_glossary_synonyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_glossary_synonyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `glossary_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `synonym` varchar(255) NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`glossary_id`),
  KEY `idx_glossary_synonym1` (`project_id`,`glossary_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_glossary_synonyms`
--

LOCK TABLES `dev_glossary_synonyms` WRITE;
/*!40000 ALTER TABLE `dev_glossary_synonyms` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_glossary_synonyms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_heartbeats`
--

DROP TABLE IF EXISTS `dev_heartbeats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_heartbeats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app` varchar(32) NOT NULL,
  `ctrllr` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  `params_hash` varchar(32) NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`user_id`,`app`,`ctrllr`,`view`,`params_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dev_helptexts`
--

CREATE TABLE `dev_helptexts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `subject` varchar(64) NOT NULL,
  `helptext` text NOT NULL,
  `show_order` int(3) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `dev_helptexts`
--

INSERT INTO `dev_helptexts` (`id`, `controller`, `view`, `subject`, `helptext`, `show_order`, `created`, `last_change`) VALUES
(1, 'users', 'login', 'Logging in', 'To log in, fill in your Linnaeus NG-username and password, and press the button labeled "Login".', 0, '2010-08-26 10:51:15', '2010-08-26 08:51:15'),
(2, 'users', 'login', 'Problems logging in?', 'If you cannot login, please <a href="mailto:helpdesk@linnaeus.eti.uva.nl">contact the helpdesk</a>.', 1, '2010-08-26 10:51:15', '2010-08-26 08:51:15'),
(3, 'users', 'edit', 'Role', 'The ''role'' indicates the role this user will have in the current project. Hover your mouse over the role''s names to see a short description.', 0, '2010-08-26 10:51:15', '2010-08-26 08:51:15'),
(4, 'users', 'edit', 'Active', '''Active'' indicates whether a user is actively working on the current project. When set to ''n'', the user can no longer log in or work on the project. It allows you to temporarily disable users without deleting them outright.<br />Users that have the role of ''Lead expert'' cannot change role, or be made in-active, as they are the lead manager of a project.', 1, '2010-08-26 10:51:15', '2010-08-26 08:51:15');

--
-- Table structure for table `dev_hotwords`
--

DROP TABLE IF EXISTS `dev_hotwords`;
CREATE TABLE `dev_hotwords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `hotword` varchar(255) NOT NULL,
  `controller` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_3` (`project_id`,`hotword`,`language_id`),
  KEY `project_id_2` (`project_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `dev_hybrids`
--

DROP TABLE IF EXISTS `dev_hybrids`;
CREATE TABLE `dev_hybrids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hybrid` varchar(128) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hybrid` (`hybrid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `dev_hybrids`
--

INSERT INTO `dev_hybrids` (`id`, `hybrid`, `created`, `last_change`) VALUES
(1, 'x Genus', '2010-10-14 12:02:03', '2010-10-14 10:02:03'),
(2, 'x Genus species', '2010-10-14 12:02:03', '2010-10-14 10:02:03'),
(3, 'Genus x species', '2010-10-14 12:02:03', '2010-10-14 10:02:03'),
(4, 'Genus species x Genus species', '2010-10-14 12:02:03', '2010-10-14 10:02:03');



--
-- Table structure for table `dev_interface_texts`
--

CREATE TABLE `dev_interface_texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_bin NOT NULL,
  `env` varchar(8) CHARACTER SET utf8 NOT NULL DEFAULT 'front',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text` (`text`(255),`env`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `dev_interface_texts`
--

INSERT INTO `dev_interface_texts` (`id`, `text`, `env`, `created`) VALUES
(24, 0x496e74726f64756374696f6e, 'app', '2012-12-11 08:52:17'),
(25, 0x476c6f7373617279, 'app', '2012-12-11 08:52:17'),
(26, 0x4c697465726174757265, 'app', '2012-12-11 08:52:17'),
(27, 0x53706563696573, 'app', '2012-12-11 08:52:17'),
(28, 0x4869676865722074617861, 'app', '2012-12-11 08:52:17'),
(29, 0x446963686f746f6d6f7573206b6579, 'app', '2012-12-11 08:52:17'),
(30, 0x4d6174726978206b6579, 'app', '2012-12-11 08:52:17'),
(31, 0x446973747269627574696f6e, 'app', '2012-12-11 08:52:17'),
(32, 0x4164646974696f6e616c207465787473, 'app', '2012-12-11 08:52:17'),
(33, 0x496e646578, 'app', '2012-12-11 08:52:17'),
(34, 0x536561726368, 'app', '2012-12-11 08:52:17'),
(35, 0x70726f6a65637473, 'app', '2012-12-11 08:52:17'),
(36, 0x6c6f67696e, 'app', '2012-12-11 08:52:17'),
(37, 0x68656c70, 'app', '2012-12-11 08:52:17'),
(38, 0x6e6f742079657420617661696c61626c65, 'app', '2012-12-11 08:52:17'),
(39, 0x636f6e74616374, 'app', '2012-12-11 08:52:17'),
(41, 0x57656c636f6d65, 'app', '2012-12-11 08:52:18'),
(42, 0x436f6e7472696275746f7273, 'app', '2012-12-11 08:52:18'),
(43, 0x41626f757420455449, 'app', '2012-12-11 08:52:18'),
(44, 0x5365617263682e2e2e, 'app', '2012-12-11 08:52:18'),
(45, 0x4c6f6164696e67206170706c69636174696f6e, 'app', '2012-12-11 08:52:18'),
(46, 0x436f6e74656e7473, 'app', '2012-12-11 08:52:18'),
(47, 0x4261636b, 'app', '2012-12-11 08:52:18'),
(48, 0x4261636b20746f20, 'app', '2012-12-11 08:52:24'),
(49, 0x50726576696f7573, 'app', '2012-12-11 08:52:38'),
(50, 0x4e657874, 'app', '2012-12-11 08:52:38'),
(52, 0x496e6465783a205370656369657320616e64206c6f7765722074617861, 'app', '2012-12-11 08:52:44'),
(53, 0x5370656369657320616e64206c6f7765722074617861, 'app', '2012-12-11 08:52:44'),
(54, 0x436f6d6d6f6e206e616d6573, 'app', '2012-12-11 08:52:44'),
(55, 0x496e6465783a204869676865722074617861, 'app', '2012-12-11 08:52:47'),
(56, 0x496e6465783a20436f6d6d6f6e206e616d6573, 'app', '2012-12-11 08:52:48'),
(57, 0x4c616e67756167653a, 'app', '2012-12-11 08:52:48'),
(58, 0x53686f7720616c6c, 'app', '2012-12-11 08:52:48'),
(59, 0x476c6f73736172793a2022257322, 'app', '2012-12-11 08:52:52'),
(60, 0x53796e6f6e796d, 'app', '2012-12-11 08:52:52'),
(61, 0x666f72, 'app', '2012-12-11 08:52:52'),
(62, 0x4c6974657261747572653a2022257322, 'app', '2012-12-11 08:52:57'),
(63, 0x53706563696573206d6f64756c6520696e646578, 'app', '2012-12-11 08:53:02'),
(64, 0x4d65646961, 'app', '2012-12-11 08:53:02'),
(65, 0x436c617373696669636174696f6e, 'app', '2012-12-11 08:53:02'),
(66, 0x4e616d6573, 'app', '2012-12-11 08:53:02'),
(67, 0x53706563696573206d6f64756c653a20222573222028257329, 'app', '2012-12-11 08:53:02'),
(68, 0x486967686572207461786120696e646578, 'app', '2012-12-11 08:53:16'),
(69, 0x48696768657220746178613a20222573222028257329, 'app', '2012-12-11 08:53:16'),
(70, 0x446963686f746f6d6f7573206b65793a20737465702025733a2022257322, 'app', '2012-12-11 08:53:24'),
(71, 0x53746570, 'app', '2012-12-11 08:53:24'),
(72, 0x52656d61696e696e67, 'app', '2012-12-11 08:53:24'),
(73, 0x4578636c75646564, 'app', '2012-12-11 08:53:24'),
(74, 0x257320706f737369626c652025732072656d61696e696e673a, 'app', '2012-12-11 08:53:24'),
(75, 0x2573202573206578636c756465643a, 'app', '2012-12-11 08:53:24'),
(76, 0x4e6f2063686f69636573206d61646520796574, 'app', '2012-12-11 08:53:24'),
(77, 0x4669727374, 'app', '2012-12-11 08:53:24'),
(78, 0x4465636973696f6e2070617468, 'app', '2012-12-11 08:53:24'),
(79, 0x52657475726e20746f2066697273742073746570, 'app', '2012-12-11 08:53:34'),
(80, 0x52657475726e20746f2073746570, 'app', '2012-12-11 08:53:34'),
(81, 0x4d617472697820222573223a206964656e74696679, 'app', '2012-12-11 08:53:39'),
(82, 0x4964656e74696679, 'app', '2012-12-11 08:53:39'),
(83, 0x4578616d696e65, 'app', '2012-12-11 08:53:39'),
(84, 0x436f6d70617265, 'app', '2012-12-11 08:53:39'),
(85, 0x4d61747269783a, 'app', '2012-12-11 08:53:39'),
(86, 0x43686172616374657273, 'app', '2012-12-11 08:53:39'),
(87, 0x536f7274, 'app', '2012-12-11 08:53:39'),
(88, 0x537461746573, 'app', '2012-12-11 08:53:39'),
(89, 0x416464, 'app', '2012-12-11 08:53:39'),
(90, 0x44656c657465, 'app', '2012-12-11 08:53:39'),
(91, 0x436c65617220616c6c, 'app', '2012-12-11 08:53:39'),
(92, 0x536561726368202667743b2667743b, 'app', '2012-12-11 08:53:39'),
(93, 0x53656c656374656420636f6d62696e6174696f6e206f662063686172616374657273, 'app', '2012-12-11 08:53:39'),
(94, 0x547265617420756e6b6e6f776e73206173206d617463686573, 'app', '2012-12-11 08:53:39'),
(95, 0x526573756c74206f66207468697320636f6d62696e6174696f6e206f662063686172616374657273, 'app', '2012-12-11 08:53:39'),
(96, 0x4d617472697820222573223a206578616d696e65, 'app', '2012-12-11 08:53:41'),
(97, 0x53656c6563742061207461786f6e, 'app', '2012-12-11 08:53:41'),
(98, 0x53656c6563742061207461786f6e2066726f6d20746865206c69737420746f2076696577206368617261637465727320616e642063686172616374657220737461746573206f662074686973207461786f6e2e, 'app', '2012-12-11 08:53:41'),
(99, 0x546865736520617265207573656420666f7220746865206964656e74696669636174696f6e2070726f6365737320756e646572204964656e746966792e, 'app', '2012-12-11 08:53:41'),
(100, 0x54797065, 'app', '2012-12-11 08:53:41'),
(101, 0x436861726163746572, 'app', '2012-12-11 08:53:41'),
(102, 0x5374617465, 'app', '2012-12-11 08:53:41'),
(103, 0x4d617472697820222573223a20636f6d70617265, 'app', '2012-12-11 08:53:42'),
(104, 0x53656c6563742074776f20746178612066726f6d20746865206c6973747320616e6420636c69636b20436f6d7061726520746f20636f6d7061726520746865206368617261637465727320616e64206368617261637465722073746174657320666f7220626f746820746178612e2054686520726573756c74732073686f772074686520646966666572656e63657320616e642073696d696c6172697469657320666f7220626f746820746178612e, 'app', '2012-12-11 08:53:42'),
(105, 0x556e69717565206368617261637465722073746174657320666f722025733a, 'app', '2012-12-11 08:53:42'),
(106, 0x53686172656420636861726163746572207374617465733a, 'app', '2012-12-11 08:53:42'),
(107, 0x556e697175652073746174657320696e, 'app', '2012-12-11 08:53:42'),
(108, 0x5374617465732070726573656e7420696e20626f74683a, 'app', '2012-12-11 08:53:42'),
(109, 0x5374617465732070726573656e7420696e206e6569746865723a, 'app', '2012-12-11 08:53:42'),
(110, 0x4e756d626572206f6620617661696c61626c65207374617465733a, 'app', '2012-12-11 08:53:42'),
(111, 0x5461786f6e6f6d69632064697374616e63653a, 'app', '2012-12-11 08:53:42'),
(148, 0x53776974636820746f20616e6f74686572206d6174726978, 'app', '2012-12-11 08:56:57'),
(149, 0x446973706c6179696e672022257322, 'app', '2012-12-11 09:07:15'),
(150, 0x44697665727369747920696e646578, 'app', '2012-12-11 09:07:15'),
(151, 0x476f20746f2074686973207461786f6e, 'app', '2012-12-11 09:07:15'),
(152, 0x53656c656374206120646966666572656e74206d6170, 'app', '2012-12-11 09:07:15'),
(153, 0x43686f6f73652061206d6170, 'app', '2012-12-11 09:07:15'),
(154, 0x436f6d706172696e672074617861, 'app', '2012-12-11 09:07:18'),
(155, 0x446973706c617973206f7665726c6170206265747765656e2074776f20746178612e, 'app', '2012-12-11 09:07:18'),
(156, 0x436c656172206d6170, 'app', '2012-12-11 09:07:19'),
(157, 0x53656c65637420746865206172656120796f752077616e7420746f2073656172636820627920636c69636b696e67207468652072656c6576616e7420737175617265732e, 'app', '2012-12-11 09:07:19'),
(158, 0x5768656e2066696e69736865642c20636c69636b2027536561726368272e, 'app', '2012-12-11 09:07:19'),
(159, 0x7265636f726473, 'app', '2012-12-11 09:07:21'),
(160, 0x53656172636820726573756c7473, 'app', '2012-12-11 09:11:43'),
(161, 0x436f6d706172696e672074617861202225732220616e642022257322, 'app', '2012-12-11 09:13:23'),
(162, 0x53696d706c652064697373696d696c617269747920636f656666696369656e74, 'app', '2012-12-11 09:13:34'),
(195, 0x576f6f7264656e6c696a7374, 'app', '2012-12-11 10:38:42'),
(196, 0x536f6f7274, 'app', '2012-12-11 10:38:42'),
(198, 0x28636f6d6d6f6e206e616d65206f6620257329, 'app', '2012-12-11 10:39:07'),
(199, 0x53706563696573206e616d6573, 'app', '2012-12-11 10:39:10'),
(200, 0x53706563696573206465736372697074696f6e73, 'app', '2012-12-11 10:39:10'),
(201, 0x537065636965732073796e6f6e796d73, 'app', '2012-12-11 10:39:10'),
(202, 0x5370656369657320636f6d6d6f6e206e616d6573, 'app', '2012-12-11 10:39:10'),
(203, 0x53706563696573206d65646961, 'app', '2012-12-11 10:39:10'),
(215, 0x476c6f7373617279207465726d73, 'app', '2012-12-11 13:21:55'),
(216, 0x476c6f73736172792073796e6f6e796d73, 'app', '2012-12-11 13:21:55'),
(217, 0x476c6f7373617279206d65646961, 'app', '2012-12-11 13:21:55'),
(218, 0x4c69746572617279207265666572656e636573, 'app', '2012-12-11 13:21:55'),
(264, 0x28656e6c6172676520696d61676529, 'app', '2012-12-11 14:47:19'),
(286, 0x436c69636b20746f20656e6c61726765, 'app', '2012-12-12 10:47:31'),
(287, 0x43686f696365, 'app', '2012-12-12 10:48:18'),
(325, 0x496e74726f647563746965, 'app', '2012-12-12 16:07:27'),
(326, 0x486f676572652074617861, 'app', '2012-12-12 16:07:27'),
(327, 0x446963686f746f6d6520736c657574656c, 'app', '2012-12-12 16:07:27'),
(328, 0x4d6174726978736c657574656c, 'app', '2012-12-12 16:07:27'),
(329, 0x566572737072656964696e67, 'app', '2012-12-12 16:07:27'),
(330, 0x4f76657220455449, 'app', '2012-12-12 16:07:43'),
(345, 0x556e6b6e6f776e206f72206e6f2070726f6a6563742049442e, 'app', '2012-12-13 12:26:25'),
(346, 0x4261636b20746f204c696e6e61657573204e4720726f6f74, 'app', '2012-12-13 12:26:25'),
(374, 0x446963686f746f6d6f7573206b6579207374657073, 'app', '2012-12-19 09:24:00'),
(375, 0x446963686f746f6d6f7573206b65792063686f69636573, 'app', '2012-12-19 09:24:00'),
(376, 0x4d6174726978206b6579206d61747269636573, 'app', '2012-12-19 09:24:00'),
(377, 0x4d6174726978206b65792063686172616374657273, 'app', '2012-12-19 09:24:00'),
(378, 0x4d6174726978206b657920737461746573, 'app', '2012-12-19 09:24:00'),
(379, 0x4e6176696761746f72, 'app', '2012-12-19 09:24:00'),
(380, 0x67656f67726170686963616c2064617461, 'app', '2012-12-19 09:24:01'),
(381, 0x596f75722073656172636820666f7220222573222070726f647563656420257320726573756c74732e, 'app', '2012-12-19 09:24:01'),
(382, 0x457870616e6420616c6c, 'app', '2012-12-19 09:24:01'),
(383, 0x616e64, 'app', '2012-12-19 09:24:01'),
(384, 0x5461786f6e3a, 'app', '2012-12-19 09:24:01'),
(385, 0x696e, 'app', '2012-12-19 09:24:01'),
(386, 0x4974206973206e6f7420706f737369626c6520746f206a756d70206469726563746c7920746f20612073706563696669632073746570206f722063686f696365206f662074686520646963686f746f6d6f7573206b6579, 'app', '2012-12-19 09:24:01'),
(387, 0x2573537461727420746865206b65792066726f6d2074686520737461727425732e, 'app', '2012-12-19 09:24:01'),
(388, 0x4261636b20746f2073656172636820726573756c7473, 'app', '2012-12-19 09:24:06'),
(389, 0x5b73796e2e5d, 'app', '2012-12-19 09:25:44'),
(391, 0x73796e2e20, 'app', '2012-12-19 12:13:31'),
(392, 0x73796e796e6f6e796d, 'app', '2012-12-19 12:16:48'),
(393, 0x6f66, 'app', '2012-12-19 12:17:58'),
(394, 0x76616e, 'app', '2012-12-19 12:30:45'),
(403, 0x5265666572656e63656420696e2074686520666f6c6c6f77696e6720746178613a, 'app', '2012-12-19 14:24:19'),
(404, 0x54686520696d6167652066696c6520666f7220746865206d61702022257322206973206d697373696e672e, 'app', '2012-12-19 14:24:42'),
(426, 0x53796e6f6e796d73, 'app', '2012-12-19 15:50:32'),
(445, 0x73796e6f6e796d, 'app', '2012-12-20 11:13:57'),
(446, 0x5461786f6e206c697374, 'app', '2012-12-20 13:44:04'),
(447, 0x4e6f207461786f6e204944207370656369666965642e, 'app', '2012-12-20 13:45:43'),
(448, 0x636f6e7461696e732074686520666f6c6c6f77696e672074617861, 'app', '2012-12-20 14:08:19'),
(450, 0x556e6b6e6f776e2070726f6a656374206f7220696e76616c69642070726f6a6563742049442e, 'app', '2013-01-03 08:33:09');



--
-- Database: `linnaeus_ng`
--

-- --------------------------------------------------------

--
-- Table structure for table `dev_interface_translations`
--

DROP TABLE IF EXISTS `dev_interface_translations`;
CREATE TABLE `dev_interface_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_text_id` int(11) NOT NULL,
  `language_id` tinyint(3) NOT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

--
-- Dumping data for table `dev_interface_translations`
--

INSERT INTO `dev_interface_translations` (`id`, `interface_text_id`, `language_id`, `translation`) VALUES
(3, 75, 24, '%s %s uitgesloten:'),
(4, 74, 24, '%s mogelijk %s resterend:'),
(5, 43, 24, 'Over ETI'),
(6, 89, 24, 'Voeg toe'),
(7, 47, 24, 'Terug'),
(8, 48, 24, 'Terug naar'),
(9, 101, 24, 'Kenmerk'),
(10, 86, 24, 'Kenmerken'),
(11, 153, 24, 'Kies een kaart'),
(12, 91, 24, 'Maak alles leeg'),
(13, 156, 24, 'Maak de kaart leeg'),
(14, 54, 24, 'Gewone namen'),
(15, 84, 24, 'Vergelijk'),
(16, 39, 24, 'contact'),
(17, 46, 24, 'Inhoud'),
(18, 42, 24, 'Bijdragen van | Medewerkers'),
(19, 78, 24, 'Beslispad'),
(20, 90, 24, 'Verwijder'),
(21, 29, 24, 'Dichotome sleutel'),
(22, 155, 24, 'Toont overlap van twee taxa.'),
(23, 31, 24, 'Verspreiding'),
(24, 150, 24, 'Diversiteitindex'),
(25, 83, 24, 'Onderzoek'),
(26, 73, 24, 'Uitgezonderd'),
(27, 77, 24, 'Eerst'),
(28, 61, 24, 'voor'),
(29, 25, 24, 'Woordenlijst'),
(30, 151, 24, 'Ga naar dit taxon'),
(31, 37, 24, 'help'),
(32, 28, 24, 'Hogere taxa'),
(33, 82, 24, 'Identificeer'),
(34, 33, 24, 'Index'),
(35, 24, 24, 'Introductie'),
(36, 57, 24, 'Taal:'),
(37, 26, 24, 'Literatuur'),
(38, 45, 24, 'Programma wordt geladen'),
(39, 36, 24, 'login'),
(40, 30, 24, 'Matrixsleutel'),
(41, 148, 24, 'Selecteer een andere matrix'),
(42, 50, 24, 'Volgende'),
(43, 76, 24, 'Nog geen keuze(s) gemaakt'),
(44, 38, 24, 'nog niet beschikbaar'),
(45, 110, 24, 'Aantal beschikbare toestanden:'),
(46, 49, 24, 'Vorige'),
(47, 35, 24, 'projecten'),
(48, 159, 24, 'records'),
(49, 72, 24, 'Resterend'),
(50, 95, 24, 'Resultaat van deze combinatie kenmerken'),
(51, 79, 24, 'Terug naar stap 1 | Terug naar eerste keuze'),
(52, 80, 24, 'Terug naar stap | Terug naar keuze'),
(53, 34, 24, 'Zoek'),
(54, 92, 24, 'Zoek &gt;&gt;'),
(55, 160, 24, 'Zoekresultaten'),
(56, 44, 24, 'Zoek...'),
(57, 152, 24, 'Selecteer een andere kaart'),
(58, 97, 24, 'Selecteer een taxon'),
(59, 98, 24, 'Selecteer uit de lijst een taxon om de kenmerken en toestanden ervan te zien.'),
(60, 99, 24, 'Deze worden bij het selectieproces van de Snelzoeker gebruikt.'),
(61, 157, 24, 'Selecteer het zoekgebied door de relevante vierkanten aan te klikken.'),
(62, 104, 24, 'Selecteer twee taxa uit de lijst en klik Vergelijk om de kenmerken en toestanden ervan te vergelijken. De uitkomst toont de verschillen en overeenkomsten tussen beide taxa.'),
(63, 93, 24, 'Geselecteerde combinatie van kenmerken'),
(64, 106, 24, 'Gedeelde toestanden:'),
(65, 58, 24, 'Toon alle'),
(66, 87, 24, 'Sorteer'),
(67, 27, 24, 'Soort'),
(68, 53, 24, 'Soorten en lagere taxa'),
(69, 102, 24, 'Toestand'),
(70, 88, 24, 'Toestanden'),
(71, 108, 24, 'Toestanden in beide aanwezig:'),
(72, 109, 24, 'Toestanden in geen van beide aanwezig:'),
(73, 71, 24, 'Stap'),
(74, 148, 24, 'Verander van matrix'),
(75, 60, 24, 'Synoniem'),
(76, 111, 24, 'Taxonomische afstand:'),
(77, 94, 24, 'Behandel onbekenden als overeenkomend'),
(78, 100, 24, 'Type'),
(79, 105, 24, 'Unieke toestand voor %s:'),
(80, 107, 24, 'Unieke toestand in'),
(81, 41, 24, 'Welkom'),
(82, 158, 24, 'Wanneer gereed, klik ''Zoek''.'),
(94, 147, 24, 'terug'),
(84, 23, 24, 'Terug naar Linnaeus NG ''root'''),
(85, 125, 24, 'Kenmerken'),
(86, 115, 24, 'Bijdragen van | Medewerkers'),
(95, 142, 24, 'verwijder'),
(88, 15, 24, 'login'),
(89, 2, 24, 'projecten'),
(90, 134, 24, 'Toestanden'),
(91, 114, 24, 'Welkom'),
(92, 177, 24, 'taal toevoegen'),
(97, 113, 24, 'Inhoud'),
(102, 208, 24, '< vorige'),
(99, 182, 24, 'Taal'),
(103, 129, 24, '& andere matrices'),
(104, 393, 24, 'van'),
(105, 445, 24, 'synoniem');

--
-- Table structure for table `dev_introduction_media`
--

DROP TABLE IF EXISTS `dev_introduction_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_introduction_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `thumb_name` varchar(128) DEFAULT NULL,
  `original_name` varchar(128) NOT NULL,
  `mime_type` varchar(32) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_introduction_media`
--

LOCK TABLES `dev_introduction_media` WRITE;
/*!40000 ALTER TABLE `dev_introduction_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_introduction_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_introduction_pages`
--

DROP TABLE IF EXISTS `dev_introduction_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_introduction_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `got_content` tinyint(1) NOT NULL DEFAULT '0',
  `show_order` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_introduction_pages`
--

LOCK TABLES `dev_introduction_pages` WRITE;
/*!40000 ALTER TABLE `dev_introduction_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_introduction_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_keysteps`
--

DROP TABLE IF EXISTS `dev_keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `number` int(3) NOT NULL DEFAULT '0',
  `is_start` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idxKeystepsNumber` (`project_id`,`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_keysteps`
--

LOCK TABLES `dev_keysteps` WRITE;
/*!40000 ALTER TABLE `dev_keysteps` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_keysteps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_keytrees`
--

DROP TABLE IF EXISTS `dev_keytrees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_keytrees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `chunk` int(3) NOT NULL DEFAULT '0',
  `keytree` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`chunk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_keytrees`
--

LOCK TABLES `dev_keytrees` WRITE;
/*!40000 ALTER TABLE `dev_keytrees` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_keytrees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_l2_diversity_index`
--

DROP TABLE IF EXISTS `dev_l2_diversity_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_l2_diversity_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `square_number` int(11) NOT NULL,
  `diversity_count` mediumint(9) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`map_id`,`type_id`,`square_number`),
  KEY `project_id_2` (`project_id`,`map_id`),
  KEY `project_id_3` (`project_id`,`map_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_l2_diversity_index`
--

LOCK TABLES `dev_l2_diversity_index` WRITE;
/*!40000 ALTER TABLE `dev_l2_diversity_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_l2_diversity_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_l2_maps`
--

DROP TABLE IF EXISTS `dev_l2_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_l2_maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `image` varchar(64) DEFAULT NULL,
  `coordinates` varchar(255) NOT NULL,
  `rows` smallint(6) NOT NULL,
  `cols` smallint(6) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_l2_maps`
--

LOCK TABLES `dev_l2_maps` WRITE;
/*!40000 ALTER TABLE `dev_l2_maps` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_l2_maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_l2_occurrences_taxa`
--

DROP TABLE IF EXISTS `dev_l2_occurrences_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_l2_occurrences_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `square_number` mediumint(9) NOT NULL,
  `coordinates` text NOT NULL,
  `legend` varchar(64) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_3` (`project_id`,`taxon_id`,`map_id`,`type_id`,`square_number`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`taxon_id`,`map_id`),
  KEY `project_id_4` (`project_id`,`taxon_id`),
  KEY `taxon_id` (`taxon_id`),
  KEY `project_id_5` (`project_id`,`taxon_id`,`map_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_l2_occurrences_taxa`
--

LOCK TABLES `dev_l2_occurrences_taxa` WRITE;
/*!40000 ALTER TABLE `dev_l2_occurrences_taxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_l2_occurrences_taxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_l2_occurrences_taxa_combi`
--

DROP TABLE IF EXISTS `dev_l2_occurrences_taxa_combi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_l2_occurrences_taxa_combi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `square_numbers` text NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`map_id`,`type_id`),
  KEY `project_id_2` (`project_id`,`taxon_id`,`map_id`),
  KEY `project_id_3` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_l2_occurrences_taxa_combi`
--

LOCK TABLES `dev_l2_occurrences_taxa_combi` WRITE;
/*!40000 ALTER TABLE `dev_l2_occurrences_taxa_combi` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_l2_occurrences_taxa_combi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_labels_languages`
--

DROP TABLE IF EXISTS `dev_labels_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_labels_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label_language_id` int(11) NOT NULL,
  `label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_labels_languages`
--

LOCK TABLES `dev_labels_languages` WRITE;
/*!40000 ALTER TABLE `dev_labels_languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_labels_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_labels_projects_ranks`
--

DROP TABLE IF EXISTS `dev_labels_projects_ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_labels_projects_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `project_rank_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `project_id_2` (`project_id`,`project_rank_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_labels_projects_ranks`
--

LOCK TABLES `dev_labels_projects_ranks` WRITE;
/*!40000 ALTER TABLE `dev_labels_projects_ranks` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_labels_projects_ranks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_labels_sections`
--

DROP TABLE IF EXISTS `dev_labels_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_labels_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `project_id_2` (`project_id`,`section_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_labels_sections`
--

LOCK TABLES `dev_labels_sections` WRITE;
/*!40000 ALTER TABLE `dev_labels_sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_labels_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_languages`
--

DROP TABLE IF EXISTS `dev_languages`;
CREATE TABLE `dev_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(32) NOT NULL,
  `iso3` varchar(3) NOT NULL,
  `iso2` varchar(2) DEFAULT NULL,
  `locale_win` varchar(32) DEFAULT NULL,
  `locale_lin` varchar(16) DEFAULT NULL,
  `direction` enum('ltr','rtl') DEFAULT 'ltr',
  `show_order` int(2) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`),
  UNIQUE KEY `iso3` (`iso3`),
  UNIQUE KEY `iso2` (`iso2`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123 ;

--
-- Dumping data for table `dev_languages`
--

INSERT INTO `dev_languages` (`id`, `language`, `iso3`, `iso2`, `locale_win`, `locale_lin`, `direction`, `show_order`, `created`) VALUES
(1, 'Abkhaz', 'abk', 'ab', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:07'),
(2, 'Afrikaans', 'afr', 'af', 'Afrikaans', 'af_ZA', 'ltr', NULL, '2010-09-06 13:44:07'),
(3, 'Albanian', 'alb', NULL, 'Albanian', 'sq_AL', 'ltr', NULL, '2010-09-06 13:44:07'),
(4, 'Amharic', 'amh', 'am', NULL, 'am_ET', 'ltr', NULL, '2010-09-06 13:44:07'),
(5, 'Arabic', 'ara', 'ar', 'Arabic_Egypt', 'ar_EG', 'rtl', NULL, '2010-09-06 13:44:07'),
(6, 'Assyrian/Syriac', 'syr', NULL, 'Syriac', NULL, 'rtl', NULL, '2010-09-06 13:44:07'),
(7, 'Armenian', 'arm', NULL, 'Armenian', NULL, 'ltr', NULL, '2010-09-06 13:44:07'),
(8, 'Assamese', 'asm', 'as', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:07'),
(9, 'Aymara', 'aym', 'ay', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:07'),
(10, 'Azeri', 'aze', 'az', 'Azeri_Cyrillic', NULL, 'rtl', NULL, '2010-09-06 13:44:07'),
(11, 'Basque', 'baq', NULL, 'Basque', 'eu_ES', 'ltr', NULL, '2010-09-06 13:44:07'),
(12, 'Belarusian', 'bel', 'be', 'Belarusian', 'be_BY', 'ltr', NULL, '2010-09-06 13:44:07'),
(13, 'Bengali', 'ben', 'bn', 'Bengali_India', 'bn_IN', 'ltr', NULL, '2010-09-06 13:44:07'),
(14, 'Bislama', 'bis', 'bi', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:07'),
(15, 'Bosnian', 'bos', 'bs', 'Bosnian_Latin', 'bs_BA', 'ltr', NULL, '2010-09-06 13:44:07'),
(16, 'Bulgarian', 'bul', 'bg', 'Bulgarian', 'bg_BG', 'ltr', NULL, '2010-09-06 13:44:07'),
(17, 'Burmese', 'bur', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:07'),
(18, 'Catalan', 'cat', 'ca', 'Catalan', 'ca_ES', 'ltr', NULL, '2010-09-06 13:44:07'),
(19, 'Chinese', 'chi', NULL, 'Chinese_PRC', 'zh_CN', 'ltr', NULL, '2010-09-06 13:44:07'),
(20, 'Croatian', 'hrv', 'hr', 'Croatian', NULL, 'ltr', NULL, '2010-09-06 13:44:07'),
(21, 'Czech', 'cze', NULL, 'Czech', 'cs_CZ', 'ltr', NULL, '2010-09-06 13:44:07'),
(22, 'Danish', 'dan', 'da', 'Danish', 'da_DK', 'ltr', NULL, '2010-09-06 13:44:07'),
(23, 'Dhivehi', 'div', 'dv', 'Divehi', NULL, 'rtl', NULL, '2010-09-06 13:44:07'),
(24, 'Dutch', 'dut', 'nl', 'Dutch', 'nl_NL', 'ltr', 2, '2010-09-06 13:44:08'),
(25, 'Dzongkha', 'dzo', 'dz', NULL, 'dz_BT', 'ltr', NULL, '2010-09-06 13:44:08'),
(26, 'English', 'eng', 'en', 'English', 'en_GB', 'ltr', 1, '2010-09-06 13:44:08'),
(27, 'Estonian', 'est', 'et', 'Estonian', 'et_EE', 'ltr', NULL, '2010-09-06 13:44:08'),
(28, 'Fijian', 'fij', 'fj', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(29, 'Filipino', 'fil', NULL, NULL, 'fil_PH', 'ltr', NULL, '2010-09-06 13:44:08'),
(30, 'Finnish', 'fin', 'fi', 'Finnish', 'fi_FI', 'ltr', NULL, '2010-09-06 13:44:08'),
(31, 'French', 'fre', 'fr', 'French_Standard', 'fr_FR', 'ltr', 5, '2010-09-06 13:44:08'),
(32, 'Frisian', 'frs', NULL, NULL, 'fy_NL', 'ltr', NULL, '2010-09-06 13:44:08'),
(33, 'Gagauz', 'gag', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(34, 'Galician', 'glg', 'gl', 'Galician', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(35, 'Georgian', 'geo', 'ka', 'Georgian', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(36, 'German', 'ger', 'de', 'German_Standard', 'de_DE', 'ltr', 4, '2010-09-06 13:44:08'),
(37, 'Greek', 'gre', 'el', 'Greek', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(38, 'Gujarati', 'guj', 'gu', 'Gujarati', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(39, 'Haitian Creole', 'hat', 'ht', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(40, 'Hebrew', 'heb', 'he', 'Hebrew', NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(41, 'Hindi', 'hin', 'hi', 'Hindi', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(42, 'Hiri Motu', 'hmo', 'ho', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(43, 'Hungarian', 'hun', 'hu', 'Hungarian', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(44, 'Icelandic', 'ice', 'is', 'Icelandic', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(45, 'Indonesian', 'ind', 'id', 'Indonesian', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(46, 'Inuinnaqtun', 'ikt', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(47, 'Inuktitut', 'iku', 'iu', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(48, 'Irish', 'gle', 'ga', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(49, 'Italian', 'ita', 'it', 'Italian_Standard', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(50, 'Japanese', 'jpn', 'ja', 'Japanese', 'ja_JP', 'ltr', NULL, '2010-09-06 13:44:08'),
(51, 'Kannada', 'kan', 'kn', 'Kannada', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(52, 'Kashmiri', 'kas', 'ks', 'Kazakh', NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(53, 'Kazakh', 'kaz', 'kk', NULL, NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(54, 'Khmer', 'khm', 'km', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(55, 'Korean', 'kor', 'ko', 'Korean', 'ko_KR', 'ltr', NULL, '2010-09-06 13:44:08'),
(56, 'Kurdish', 'kur', 'ku', 'Kyrgyz', NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(57, 'Kyrgyz', 'kir', 'ky', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(58, 'Lao', 'lao', 'lo', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(59, 'Latin', 'lat', 'la', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(60, 'Latvian', 'lav', 'lv', 'Latvian', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(61, 'Lithuanian', 'lit', 'lt', 'Lithuanian', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(62, 'Luxembourgish', 'ltz', 'lb', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(63, 'Macedonian', 'mac', 'mk', 'Macedonian', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(64, 'Malagasy', 'mlg', 'mg', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(65, 'Malay', 'may', 'ms', 'Malay_Malaysia', NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(66, 'Malayalam', 'mal', 'ml', 'Malayalam', NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(67, 'Maltese', 'mlt', 'mt', 'Maltese', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(68, 'Manx Gaelic', 'glv', 'gv', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(69, 'Ma-ori', 'mao', 'mi', 'Maori', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(70, 'Marathi', 'mar', 'mr', 'Marathi', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(71, 'Mayan', 'myn', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(72, 'Moldovan', 'rum', 'ro', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(73, 'Mongolian', 'mon', 'mn', 'Mongolian', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(74, 'Ndebele', 'nde', 'nd', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(75, 'Nepali', 'nep', 'ne', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(76, 'Northern Sotho', 'nso', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(77, 'Norwegian', 'nor', 'no', 'Norwegian_Bokmal', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(78, 'Occitan', 'oci', 'oc', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(79, 'Oriya', 'ori', 'or', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(80, 'Ossetian', 'oss', 'os', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(81, 'Papiamento', 'pap', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(82, 'Pashto', 'pus', 'ps', NULL, NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(83, 'Persian', 'per', 'fa', NULL, NULL, 'rtl', NULL, '2010-09-06 13:44:08'),
(84, 'Polish', 'pol', 'pl', 'Polish', NULL, 'ltr', NULL, '2010-09-06 13:44:08'),
(85, 'Portuguese', 'por', 'pt', 'Portuguese_Standard', NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(86, 'Punjabi', 'pan', 'pa', 'Punjabi', NULL, 'rtl', NULL, '2010-09-06 13:44:09'),
(87, 'Quechua', 'que', 'qu', 'Quechua_Bolivia', NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(88, 'Rhaeto-Romansh', 'roh', 'rm', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(89, 'Russian', 'rus', 'ru', 'Russian', 'ru_RU', 'ltr', NULL, '2010-09-06 13:44:09'),
(90, 'Sanskrit', 'san', 'sa', 'Sanskrit', 'sa_IN', 'ltr', NULL, '2010-09-06 13:44:09'),
(91, 'Serbian', 'srp', 'sr', 'Serbian_Cyrillic', 'sr_ME', 'ltr', NULL, '2010-09-06 13:44:09'),
(92, 'Shona', 'sna', 'sn', NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(93, 'Sindhi', 'snd', 'sd', NULL, 'sd_IN', 'rtl', NULL, '2010-09-06 13:44:09'),
(94, 'Sinhala', 'sin', 'si', NULL, 'si_LK', 'ltr', NULL, '2010-09-06 13:44:09'),
(95, 'Slovak', 'slo', 'sk', 'Slovak', 'sk_SK', 'ltr', NULL, '2010-09-06 13:44:09'),
(96, 'Slovene', 'slv', 'sl', 'Slovenian', 'sl_SI', 'ltr', NULL, '2010-09-06 13:44:09'),
(97, 'Somali', 'som', 'so', NULL, 'so_SO', 'rtl', NULL, '2010-09-06 13:44:09'),
(98, 'Sotho', 'sot', 'st', NULL, 'st_ZA', 'ltr', NULL, '2010-09-06 13:44:09'),
(99, 'Spanish', 'spa', 'es', NULL, 'es_ES', 'ltr', 3, '2010-09-06 13:44:09'),
(100, 'Sranan Tongo', 'srn', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(101, 'Swahili', 'swa', 'sw', 'Swahili', NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(102, 'Swati', 'ssw', 'ss', NULL, 'ss_ZA', 'ltr', NULL, '2010-09-06 13:44:09'),
(103, 'Swedish', 'swe', 'sv', 'Swedish', 'sv_SE', 'ltr', NULL, '2010-09-06 13:44:09'),
(104, 'Tajik', 'tgk', 'tg', NULL, 'tg_TJ', 'ltr', NULL, '2010-09-06 13:44:09'),
(105, 'Tamil', 'tam', 'ta', 'Tamil', 'ta_IN', 'ltr', NULL, '2010-09-06 13:44:09'),
(106, 'Telugu', 'tel', 'te', 'Telugu', 'te_IN', 'ltr', NULL, '2010-09-06 13:44:09'),
(107, 'Tetum', 'tet', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(108, 'Thai', 'tha', 'th', 'Thai', 'th_TH', 'ltr', NULL, '2010-09-06 13:44:09'),
(109, 'Tok Pisin', 'tpi', NULL, NULL, NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(110, 'Tsonga', 'tog', NULL, NULL, 'ts_ZA', 'ltr', NULL, '2010-09-06 13:44:09'),
(111, 'Tswana', 'tsn', 'tn', NULL, 'tn_ZA', 'ltr', NULL, '2010-09-06 13:44:09'),
(112, 'Turkish', 'tur', 'tr', 'Turkish', NULL, 'ltr', NULL, '2010-09-06 13:44:09'),
(113, 'Turkmen', 'tuk', 'tk', NULL, 'tk_TM', 'rtl', NULL, '2010-09-06 13:44:09'),
(114, 'Ukrainian', 'ukr', 'uk', 'Ukrainian', 'uk_UA', 'ltr', NULL, '2010-09-06 13:44:09'),
(115, 'Urdu', 'urd', 'ur', 'Urdu', 'ur_PK', 'rtl', NULL, '2010-09-06 13:44:09'),
(116, 'Uzbek', 'uzb', 'uz', 'Uzbek_Cyrillic', 'uz_UZ', 'ltr', NULL, '2010-09-06 13:44:09'),
(117, 'Venda', 'ven', 'cy', NULL, 've_ZA', 'ltr', NULL, '2010-09-06 13:44:09'),
(118, 'Vietnamese', 'vie', 'vi', 'Vietnamese', 'vi_VN', 'ltr', NULL, '2010-09-06 13:44:09'),
(119, 'Welsh', 'wel', NULL, 'Welsh', 'cy_GB', 'ltr', NULL, '2010-09-06 13:44:09'),
(120, 'Xhosa', 'xho', 'xh', 'Xhosa', 'xh_ZA', 'ltr', NULL, '2010-09-06 13:44:09'),
(121, 'Yiddish', 'yid', 'yi', NULL, 'yi_US', 'rtl', NULL, '2010-09-06 13:44:09'),
(122, 'Zulu', 'zul', 'zu', 'Zulu', 'zu_ZA', 'ltr', NULL, '2010-09-06 13:44:09');

--
-- Table structure for table `dev_languages_projects`
--

DROP TABLE IF EXISTS `dev_languages_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_languages_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `def_language` tinyint(1) NOT NULL DEFAULT '0',
  `active` enum('y','n') NOT NULL DEFAULT 'n',
  `tranlation_status` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `language_id_2` (`language_id`,`project_id`),
  KEY `language_id` (`language_id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_languages_projects`
--

LOCK TABLES `dev_languages_projects` WRITE;
/*!40000 ALTER TABLE `dev_languages_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_languages_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_literature`
--

DROP TABLE IF EXISTS `dev_literature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_literature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `author_first` varchar(64) NOT NULL,
  `author_second` varchar(64) DEFAULT NULL,
  `multiple_authors` tinyint(1) NOT NULL DEFAULT '0',
  `year` date NOT NULL,
  `suffix` varchar(3) DEFAULT NULL,
  `text` text NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_literature`
--

LOCK TABLES `dev_literature` WRITE;
/*!40000 ALTER TABLE `dev_literature` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_literature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_literature_taxa`
--

DROP TABLE IF EXISTS `dev_literature_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_literature_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `literature_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`literature_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_literature_taxa`
--

LOCK TABLES `dev_literature_taxa` WRITE;
/*!40000 ALTER TABLE `dev_literature_taxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_literature_taxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_matrices`
--

DROP TABLE IF EXISTS `dev_matrices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `got_names` tinyint(1) DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_matrices`
--

LOCK TABLES `dev_matrices` WRITE;
/*!40000 ALTER TABLE `dev_matrices` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_matrices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_matrices_names`
--

DROP TABLE IF EXISTS `dev_matrices_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_matrices_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_matrices_names`
--

LOCK TABLES `dev_matrices_names` WRITE;
/*!40000 ALTER TABLE `dev_matrices_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_matrices_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_matrices_taxa`
--

DROP TABLE IF EXISTS `dev_matrices_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_matrices_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`taxon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_matrices_taxa`
--

LOCK TABLES `dev_matrices_taxa` WRITE;
/*!40000 ALTER TABLE `dev_matrices_taxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_matrices_taxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_matrices_taxa_states`
--

DROP TABLE IF EXISTS `dev_matrices_taxa_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_matrices_taxa_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `taxon_id` int(11) DEFAULT NULL,
  `ref_matrix_id` int(11) DEFAULT NULL,
  `characteristic_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`taxon_id`,`characteristic_id`,`state_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_matrices_taxa_states`
--

LOCK TABLES `dev_matrices_taxa_states` WRITE;
/*!40000 ALTER TABLE `dev_matrices_taxa_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_matrices_taxa_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_media_descriptions_taxon`
--

DROP TABLE IF EXISTS `dev_media_descriptions_taxon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_media_descriptions_taxon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`media_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_media_descriptions_taxon`
--

LOCK TABLES `dev_media_descriptions_taxon` WRITE;
/*!40000 ALTER TABLE `dev_media_descriptions_taxon` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_media_descriptions_taxon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_media_taxon`
--

DROP TABLE IF EXISTS `dev_media_taxon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_media_taxon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `thumb_name` varchar(128) DEFAULT NULL,
  `original_name` varchar(128) NOT NULL,
  `mime_type` varchar(32) NOT NULL,
  `file_size` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `overview_image` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `taxon_id` (`taxon_id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_media_taxon`
--

LOCK TABLES `dev_media_taxon` WRITE;
/*!40000 ALTER TABLE `dev_media_taxon` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_media_taxon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_modules`
--

DROP TABLE IF EXISTS `dev_modules`;
CREATE TABLE `dev_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `controller` varchar(32) NOT NULL,
  `icon` varchar(32) DEFAULT NULL,
  `show_order` int(2) NOT NULL,
  `show_in_menu` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_public_menu` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `dev_modules`
--

INSERT INTO `dev_modules` (`id`, `module`, `description`, `controller`, `icon`, `show_order`, `show_in_menu`, `show_in_public_menu`, `created`, `last_change`) VALUES
(1, 'Introduction', 'Comprehensive project introduction', 'introduction', 'introduction.png', 0, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(2, 'Glossary', 'Project glossary', 'glossary', 'glossary.png', 1, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(3, 'Literature', 'Literary references', 'literature', 'literature.png', 2, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(4, 'Species', 'Detailed pages for taxa', 'species', 'species.png', 4, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(5, 'Higher taxa', 'Detailed pages for higher taxa', 'highertaxa', 'highertaxa.png', 5, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(6, 'Dichotomous key', 'Dichotomic key based on pictures and text', 'key', 'dichotomouskey.png', 6, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(7, 'Matrix key', 'Key based on attributes', 'matrixkey', 'matrixkey.png', 7, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(8, 'Distribution', 'Key based on species distribution', 'mapkey', 'mapkey.png', 8, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(10, 'Additional texts', 'Welcome, About ETI, etc', 'content', NULL, 9, 0, 0, '2011-10-27 14:48:04', '2011-10-27 12:48:07'),
(11, 'Index', 'Index module', 'index', 'index.png', 3, 1, 1, '2011-10-27 16:27:21', '2011-10-27 14:27:24'),
(12, 'Search', 'Search and replace within all modules.', 'utilities', NULL, 10, 0, 0, '2011-11-17 12:31:32', '2011-11-17 11:31:35');


--
-- Table structure for table `dev_modules_projects`
--

DROP TABLE IF EXISTS `dev_modules_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_modules_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `show_order` tinyint(2) NOT NULL DEFAULT '0',
  `active` enum('y','n') NOT NULL DEFAULT 'y',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`module_id`),
  KEY `project_id_2` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `dev_modules_projects_users`
--

DROP TABLE IF EXISTS `dev_modules_projects_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_modules_projects_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`module_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_modules_projects_users`
--

LOCK TABLES `dev_modules_projects_users` WRITE;
/*!40000 ALTER TABLE `dev_modules_projects_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_modules_projects_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_occurrences_taxa`
--

DROP TABLE IF EXISTS `dev_occurrences_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_occurrences_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `type` enum('marker','polygon') NOT NULL,
  `coordinate` point DEFAULT NULL,
  `latitude` double(13,10) DEFAULT NULL,
  `longitude` double(13,10) DEFAULT NULL,
  `boundary` polygon DEFAULT NULL,
  `boundary_nodes` text,
  `nodes_hash` varchar(64) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`nodes_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_occurrences_taxa`
--

LOCK TABLES `dev_occurrences_taxa` WRITE;
/*!40000 ALTER TABLE `dev_occurrences_taxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_occurrences_taxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_pages_taxa`
--

DROP TABLE IF EXISTS `dev_pages_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_pages_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page` varchar(32) NOT NULL,
  `show_order` int(11) DEFAULT NULL,
  `def_page` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_pages_taxa`
--

LOCK TABLES `dev_pages_taxa` WRITE;
/*!40000 ALTER TABLE `dev_pages_taxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_pages_taxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_pages_taxa_titles`
--

DROP TABLE IF EXISTS `dev_pages_taxa_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_pages_taxa_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_pages_taxa_titles`
--

LOCK TABLES `dev_pages_taxa_titles` WRITE;
/*!40000 ALTER TABLE `dev_pages_taxa_titles` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_pages_taxa_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_projects`
--

DROP TABLE IF EXISTS `dev_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_name` varchar(64) NOT NULL,
  `sys_description` text NOT NULL,
  `title` varchar(64) NOT NULL,
  `css_url` varchar(255) DEFAULT NULL,
  `includes_hybrids` tinyint(1) NOT NULL DEFAULT '0',
  `keywords` text,
  `description` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_name` (`sys_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_projects`
--

LOCK TABLES `dev_projects` WRITE;
/*!40000 ALTER TABLE `dev_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_projects_ranks`
--

DROP TABLE IF EXISTS `dev_projects_ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_projects_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `lower_taxon` tinyint(1) DEFAULT '0',
  `keypath_endpoint` tinyint(1) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`rank_id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_3` (`project_id`,`rank_id`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_projects_ranks`
--

LOCK TABLES `dev_projects_ranks` WRITE;
/*!40000 ALTER TABLE `dev_projects_ranks` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_projects_ranks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_projects_roles_users`
--

DROP TABLE IF EXISTS `dev_projects_roles_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_projects_roles_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `last_project_select` datetime DEFAULT NULL,
  `project_selects` int(11) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`role_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_projects_roles_users`
--

LOCK TABLES `dev_projects_roles_users` WRITE;
/*!40000 ALTER TABLE `dev_projects_roles_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_projects_roles_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dev_ranks`
--

DROP TABLE IF EXISTS `dev_ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` varchar(128) NOT NULL,
  `additional` varchar(64) DEFAULT NULL,
  `default_label` varchar(32) DEFAULT NULL,
  `abbreviation` varchar(15) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `in_col` tinyint(1) DEFAULT '0',
  `can_hybrid` tinyint(1) DEFAULT '0',
  `ideal_parent_id` tinyint(3) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rank` (`rank`,`additional`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_ranks`
--

INSERT INTO `dev_ranks` (`id`, `rank`, `additional`, `default_label`, `abbreviation`, `parent_id`, `in_col`, `can_hybrid`, `ideal_parent_id`, `created`, `last_change`) VALUES
(1, 'Domain or Empire', NULL, 'Empire', NULL, NULL, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(2, 'Kingdom', NULL, 'Kingdom', NULL, 1, 1, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:38'),
(3, 'Subkingdom', NULL, 'Subkingdom', NULL, 2, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(4, 'Branch', NULL, 'Branch', NULL, 3, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(5, 'Infrakingdom', NULL, 'Infrakingdom', NULL, 4, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(6, 'Superphylum', 'or Superdivision in botany', 'Superphylum ', NULL, 5, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(7, 'Phylum', 'or Division in botany', 'Phylum', NULL, 6, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(8, 'Subphylum', 'or Subdivision in botany', 'Subphylum', NULL, 7, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(9, 'Infraphylum', 'or Infradivision in botany', 'Infraphylum', NULL, 8, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(10, 'Microphylum', NULL, 'Microphylum', NULL, 9, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(11, 'Supercohort', 'botany', 'Supercohort', NULL, 10, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(12, 'Cohort', 'botany', 'Cohort', NULL, 11, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(13, 'Subcohort', 'botany', 'Subcohort', NULL, 12, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(14, 'Infracohort', 'botany', 'Infracohort', NULL, 13, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(15, 'Superclass', NULL, 'Superclass', NULL, 14, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(16, 'Class', NULL, 'Class', NULL, 15, 1, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:38'),
(17, 'Subclass', NULL, 'Subclass', NULL, 16, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(18, 'Infraclass', NULL, 'Infraclass', NULL, 17, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(19, 'Parvclass', NULL, 'Parvclass', NULL, 18, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 11:25:37'),
(20, 'Superdivision', 'zoology', 'Superdivision', NULL, 19, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(21, 'Division', 'zoology', 'Division', NULL, 20, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(22, 'Subdivision', 'zoology', 'Subdivision', NULL, 21, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(23, 'Infradivision', 'zoology', 'Infradivision', NULL, 22, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(24, 'Superlegion', 'zoology', 'Superlegion', NULL, 23, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(25, 'Legion', 'zoology', 'Legion', NULL, 24, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(26, 'Sublegion', 'zoology', 'Sublegion', NULL, 25, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(27, 'Infralegion', 'zoology', 'Infralegion', NULL, 26, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(28, 'Supercohort', 'zoology', 'Supercohort', NULL, 27, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(29, 'Cohort', 'zoology', 'Cohort', NULL, 28, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(30, 'Subcohort', 'zoology', 'Subcohort', NULL, 29, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(31, 'Infracohort', 'zoology', 'Infracohort', NULL, 30, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(32, 'Gigaorder', 'zoology', 'Gigaorder', NULL, 31, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(33, 'Magnorder or Megaorder', 'zoology', 'Megaorder', NULL, 32, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(34, 'Grandorder or Capaxorder', 'zoology', 'Grandorder', NULL, 33, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(35, 'Mirorder or Hyperorder', 'zoology', 'Hyperorder', NULL, 34, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(36, 'Superorder', NULL, 'Superorder', NULL, 35, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(37, 'Series', 'for fishes', 'Series', NULL, 36, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(38, 'Order', NULL, 'Order', NULL, 37, 1, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(39, 'Parvorder', 'position in some  classifications', 'Parvorder', NULL, 38, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(40, 'Nanorder', 'zoological', 'Nanorder', NULL, 39, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(41, 'Hypoorder', 'zoological', 'Hypoorder', NULL, 40, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(42, 'Minorder', 'zoological', 'Minorder', NULL, 41, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(43, 'Suborder', NULL, 'Suborder', NULL, 42, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(44, 'Infraorder', NULL, 'Infraorder', NULL, 43, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(45, 'Parvorder', '(usual position) or Microorder (zoology)', 'Parvorder', NULL, 44, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(46, 'Section', 'zoology', 'Section ', NULL, 45, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(47, 'Subsection', 'zoology', 'Subsection', NULL, 46, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(48, 'Gigafamily', 'zoology', 'Gigafamily', NULL, 47, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(49, 'Megafamily', 'zoology', 'Megafamily', NULL, 48, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(50, 'Grandfamily', 'zoology', 'Grandfamily', NULL, 49, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(51, 'Hyperfamily', 'zoology', 'Hyperfamily ', NULL, 50, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(52, 'Superfamily', NULL, 'Superfamily', NULL, 51, 1, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(53, 'Epifamily', 'zoology', 'Epifamily ', NULL, 52, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(54, 'Series', 'for Lepidoptera', 'Series ', NULL, 53, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(55, 'Group', 'for Lepidoptera', 'Group', NULL, 54, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(56, 'Family', NULL, 'Family', NULL, 55, 1, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(57, 'Subfamily', NULL, 'Subfamily', NULL, 56, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(58, 'Infrafamily', NULL, 'Infrafamily', NULL, 57, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(59, 'Supertribe', NULL, 'Supertribe', NULL, 58, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(60, 'Tribe', NULL, 'Tribe', NULL, 59, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(61, 'Subtribe', NULL, 'Subtribe', NULL, 60, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(62, 'Infratribe', NULL, 'Infratribe', NULL, 61, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(63, 'Genus', NULL, 'Genus', NULL, 62, 1, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(64, 'Subgenus', NULL, 'Subgenus', NULL, 63, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(65, 'Infragenus', NULL, 'Infragenus', NULL, 64, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(66, 'Section', NULL, 'Section', NULL, 65, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(67, 'Subsection', 'botany', 'Subsection', NULL, 66, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(68, 'Series', 'botany', 'Series', NULL, 67, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(69, 'Subseries', 'botany', 'Subseries', NULL, 68, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(70, 'Superspecies or Species-group', NULL, 'Species Group', NULL, 69, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(71, 'Species Subgroup', NULL, 'Species Subgroup', NULL, 70, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(72, 'Species Complex', NULL, 'Species Complex', NULL, 71, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(73, 'Species Aggregate', NULL, 'Species Aggregate', NULL, 72, 0, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(74, 'Species', NULL, 'Species', NULL, 73, 1, 1, 63, '2010-10-14 13:25:38', '2012-12-04 08:17:39'),
(75, 'Infraspecies', NULL, 'Infraspecies', NULL, 74, 0, 1, 74, '2010-10-14 13:25:38', '2012-12-04 08:20:14'),
(76, 'Subspecific Aggregate', NULL, 'Subspecific Aggregate', NULL, 75, 0, 1, 74, '2010-10-14 13:25:38', '2012-12-04 08:20:14'),
(77, 'Subspecies', 'or Forma Specialis for fungi, or Variety for bacteria', 'Subspecies', 'subsp.', 76, 0, 1, 74, '2010-10-14 13:25:38', '2012-12-06 14:10:08'),
(78, 'Varietas/Variety or Form/Morph', 'zoology', 'Variety', 'var.', 77, 0, 1, NULL, '2010-10-14 13:25:38', '2012-12-06 14:10:08'),
(79, 'Subvariety', 'botany', 'Subvariety', 'subvar.', 78, 0, 1, NULL, '2010-10-14 13:25:38', '2012-12-06 14:10:08'),
(80, 'Subsubvariety', NULL, 'Subsubvariety', 'subsubvar.', 79, 0, 1, NULL, '2010-10-14 13:25:38', '2012-12-06 14:10:08'),
(81, 'Forma/Form', 'botany', 'Form', 'f.', 80, 0, 1, NULL, '2010-10-14 13:25:38', '2012-12-06 14:10:08'),
(82, 'Subform', 'botany', 'Subform', 'subf.', 81, 0, 1, NULL, '2010-10-14 13:25:38', '2012-12-06 14:10:08'),
(83, 'Subsubform', NULL, 'Subsubform', 'subsubf.', 82, 0, 1, NULL, '2010-10-14 13:25:38', '2012-12-06 14:10:08'),
(84, 'Candidate', NULL, 'Candidate', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(85, 'Cultivar', NULL, 'Cultivar', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(86, 'Cultivar-group', NULL, 'Cultivar-group', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(87, 'Denomination Class', NULL, 'Denomination Class', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(88, 'Graft-chimaera', NULL, 'Graft-chimaera', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(89, 'Grex', NULL, 'Grex', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(90, 'Patho-variety', NULL, 'Patho-variety', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(91, 'Special form', NULL, 'Special form', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(92, 'Bio-variety', NULL, 'Bio-variety', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38');

--
-- Table structure for table `dev_rights`
--

DROP TABLE IF EXISTS `dev_rights`;
CREATE TABLE `dev_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `view_description` varchar(64) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `controller` (`controller`,`view`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dev_rights`
--

INSERT INTO `dev_rights` (`id`, `controller`, `view`, `view_description`, `created`) VALUES
(1, 'users', '*', 'full access', '2010-11-03 14:59:36'),
(2, 'users', 'index', 'index', '2010-11-03 14:59:36'),
(3, 'users', 'choose_project', 'choose a project to work on (MANDATORY)', '2010-11-03 14:59:36'),
(4, 'users', 'create', 'create new users', '2010-11-03 14:59:36'),
(5, 'users', 'edit', 'edit existing users', '2010-11-03 14:59:36'),
(7, 'users', 'view', 'view a user in current project', '2010-11-03 14:59:36'),
(8, 'projects', '*', 'full access', '2010-11-03 15:00:18'),
(9, 'projects', 'index', 'index', '2010-11-03 15:00:18'),
(10, 'projects', 'data', 'edit basic project data', '2010-11-03 15:00:18'),
(11, 'projects', 'modules', 'view and (un)publish modules, add and remove custom modules', '2010-11-03 15:00:18'),
(12, 'projects', 'collaborators', 'manage collaborators'' access to modules', '2010-11-03 15:00:18'),
(13, 'species', '*', 'full access', '2010-11-03 15:00:23'),
(14, 'species', 'index', 'index', '2010-11-03 15:00:23'),
(15, 'species', 'edit', 'edit basic taxon data', '2010-11-03 15:00:23'),
(16, 'species', 'list', 'view list of all taxa in the species module', '2010-11-03 15:00:23'),
(17, 'species', 'page', 'define species content categories', '2010-11-03 15:00:23'),
(18, 'species', 'col', 'import data from the catalogue of life', '2010-11-03 15:00:23'),
(19, 'species', 'collaborators', 'assign taxa to collaborators', '2010-11-03 15:00:23'),
(20, 'species', 'file', 'import a taxon tree from file', '2010-11-03 15:00:23'),
(21, 'species', 'media', 'see media files for a taxon', '2010-11-03 15:00:23'),
(22, 'species', 'media_upload', 'add media files for a taxon', '2010-11-03 15:00:23'),
(23, 'species', 'ranklabels', 'label taxonomic ranks', '2010-11-03 15:00:23'),
(24, 'species', 'ranks', 'define taxonomic ranks', '2010-11-03 15:00:23'),
(25, 'species', 'sections', 'define default sections with taxon content', '2010-11-03 15:00:23'),
(26, 'species', 'taxon', 'edit content for a species', '2010-11-03 15:00:23'),
(27, 'key', '*', 'full access', '2010-11-03 15:00:48'),
(28, 'key', 'index', 'index', '2010-12-27 11:23:27'),
(29, 'key', 'step_edit', 'editing keysteps', '2010-12-27 11:23:27'),
(30, 'key', 'step_show', 'reviewing keysteps and choices', '2010-12-27 11:23:27'),
(31, 'key', 'choice_edit', 'editing choices', '2010-12-27 11:23:27'),
(32, 'key', 'dead_ends', 'list of dead ends', '2010-12-27 11:23:27'),
(33, 'key', 'section', 'list of key sections', '2010-12-27 11:23:27'),
(34, 'key', 'map', 'key map', '2010-12-27 11:23:27'),
(35, 'key', 'orphans', 'list of orphans', '2010-12-27 11:23:27'),
(36, 'key', 'process', 'create list of remaining taxa', '2010-12-27 11:23:27'),
(37, 'key', 'rank', 'define rank of taxa available in key', '2010-12-27 11:23:57'),
(38, 'literature', '*', 'full access', '2010-12-27 11:32:11'),
(39, 'literature', 'browse', 'browse literary references', '2011-01-03 10:58:43'),
(40, 'literature', 'edit', 'edit literary references', '2011-01-03 10:58:43'),
(41, 'literature', 'search', 'search literary references', '2011-01-03 10:58:43'),
(42, 'glossary', '*', 'full access', '2011-01-03 10:59:34'),
(43, 'highertaxa', '*', 'full access', '2011-01-04 11:53:00'),
(44, 'matrixkey', '*', 'full access', '2011-01-14 12:51:24'),
(45, 'mapkey', '*', 'full access', '2011-01-31 11:25:02'),
(46, 'content', '*', 'full access', '2011-02-08 13:34:08'),
(47, 'literature', 'index', 'index', '2011-02-14 14:10:57'),
(48, 'glossary', 'index', 'index', '2011-02-14 14:11:32'),
(49, 'glossary', 'browse', 'browse glossary', '2011-02-14 14:11:32'),
(50, 'glossary', 'edit', 'edit and add glossary items', '2011-02-14 14:11:32'),
(51, 'glossary', 'media_upload', 'ulpload media for glossary item', '2011-02-14 14:11:32'),
(52, 'glossary', 'search', 'search glossary', '2011-02-14 14:11:32'),
(53, 'highertaxa', 'index', 'index', '2011-02-14 14:12:07'),
(54, 'matrixkey', 'index', 'index', '2011-02-14 14:14:58'),
(55, 'matrixkey', 'char', 'add or edit characteristic', '2011-02-14 14:14:58'),
(56, 'matrixkey', 'edit', 'edit a matrix', '2011-02-14 14:14:58'),
(57, 'matrixkey', 'links', 'add or edit a link', '2011-02-14 14:14:58'),
(58, 'matrixkey', 'matrices', 'view matrices', '2011-02-14 14:14:58'),
(59, 'matrixkey', 'matrix', 'create a new matrix', '2011-02-14 14:14:58'),
(60, 'matrixkey', 'state', 'add or edit states', '2011-02-14 14:14:58'),
(61, 'matrixkey', 'taxa', 'add or remove taxa', '2011-02-14 14:14:58'),
(62, 'content', 'index', 'index', '2011-02-21 09:53:07'),
(63, 'content', 'content', 'edit content (such as the introduction)', '2011-02-21 09:53:07'),
(64, 'module', '*', 'full access', '2011-02-21 09:54:09'),
(65, 'module', 'index', 'index', '2011-02-21 09:54:09'),
(66, 'module', 'browse', 'browse', '2011-02-21 09:54:09'),
(67, 'module', 'edit', 'edit', '2011-02-21 09:54:09'),
(68, 'module', 'media_upload', 'media_upload', '2011-02-21 09:54:09'),
(69, 'mapkey', 'index', 'index', '2011-04-04 10:54:05'),
(70, 'mapkey', 'data_types', 'define types of geodata, plus their colour', '2011-04-04 10:54:05'),
(71, 'mapkey', 'choose_species', 'select a species to add geodata for', '2011-04-04 10:54:11'),
(72, 'mapkey', 'draw_species', 'add geodata on the map for a species', '2011-04-04 10:54:11'),
(73, 'mapkey', 'file', 'add bulk geodata through upload', '2011-04-04 10:54:14'),
(74, 'mapkey', 'download_csv', 'download sample geodata file', '2011-04-04 10:54:14'),
(75, 'mapkey', 'species_select', 'select a species to view geodata for', '2011-04-04 10:54:19'),
(76, 'mapkey', 'species', 'select data for a species to view', '2011-04-04 10:54:19'),
(77, 'mapkey', 'species_show', 'view data for a species', '2011-04-04 10:54:19'),
(78, 'import', '*', 'data import from linnaues 2', '2011-04-04 11:09:01'),
(79, 'introduction', '*', 'full access', '2011-10-27 16:29:25'),
(80, 'index', '*', 'full access', '2011-10-27 16:29:25'),
(81, 'utilities', '*', 'full access', '2011-11-17 12:34:01'),
(82, 'users', 'add_user', 'Dialog for adding users to a project', '2012-03-08 11:35:47'),
(83, 'users', 'add_user_module', 'Dialog for assigning users to a project module', '2012-03-08 11:35:47'),
(84, 'users', 'remove_user', 'Dialog for removing users from a project', '2012-03-08 11:35:47'),
(85, 'users', 'remove_user_module', 'Dialog for unassigning a project module from a users', '2012-03-08 11:35:47'),
(86, 'users', 'all', 'Browse all collaborators', '2012-03-08 11:37:27'),
(87, 'users', 'delete', 'Delete users', '2012-03-08 12:30:03');


--
-- Table structure for table `dev_rights_roles`
--

DROP TABLE IF EXISTS `dev_rights_roles`;
CREATE TABLE `dev_rights_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `right_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `right_id_2` (`right_id`,`role_id`),
  KEY `right_id` (`right_id`,`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dev_rights_roles`
--

INSERT INTO `dev_rights_roles` (`id`, `right_id`, `role_id`, `created`) VALUES
(1, 1, 1, '2012-11-23 12:55:40'),
(2, 2, 1, '2012-11-23 12:55:40'),
(3, 3, 1, '2012-11-23 12:55:40'),
(4, 4, 1, '2012-11-23 12:55:40'),
(5, 5, 1, '2012-11-23 12:55:40'),
(6, 6, 1, '2012-11-23 12:55:40'),
(7, 7, 1, '2012-11-23 12:55:40'),
(8, 8, 1, '2012-11-23 12:55:40'),
(9, 9, 1, '2012-11-23 12:55:40'),
(10, 10, 1, '2012-11-23 12:55:40'),
(11, 11, 1, '2012-11-23 12:55:40'),
(12, 12, 1, '2012-11-23 12:55:40'),
(13, 13, 1, '2012-11-23 12:55:40'),
(14, 14, 1, '2012-11-23 12:55:40'),
(15, 15, 1, '2012-11-23 12:55:40'),
(16, 16, 1, '2012-11-23 12:55:40'),
(17, 17, 1, '2012-11-23 12:55:40'),
(18, 18, 1, '2012-11-23 12:55:40'),
(19, 19, 1, '2012-11-23 12:55:40'),
(20, 20, 1, '2012-11-23 12:55:40'),
(21, 21, 1, '2012-11-23 12:55:40'),
(22, 22, 1, '2012-11-23 12:55:40'),
(23, 23, 1, '2012-11-23 12:55:40'),
(24, 24, 1, '2012-11-23 12:55:40'),
(25, 25, 1, '2012-11-23 12:55:40'),
(26, 26, 1, '2012-11-23 12:55:40'),
(27, 27, 1, '2012-11-23 12:55:40'),
(28, 28, 1, '2012-11-23 12:55:40'),
(29, 29, 1, '2012-11-23 12:55:40'),
(30, 30, 1, '2012-11-23 12:55:40'),
(31, 31, 1, '2012-11-23 12:55:40'),
(32, 32, 1, '2012-11-23 12:55:40'),
(33, 33, 1, '2012-11-23 12:55:40'),
(34, 34, 1, '2012-11-23 12:55:40'),
(35, 35, 1, '2012-11-23 12:55:40'),
(36, 36, 1, '2012-11-23 12:55:40'),
(37, 37, 1, '2012-11-23 12:55:40'),
(38, 38, 1, '2012-11-23 12:55:40'),
(39, 39, 1, '2012-11-23 12:55:40'),
(40, 40, 1, '2012-11-23 12:55:40'),
(41, 41, 1, '2012-11-23 12:55:40'),
(42, 42, 1, '2012-11-23 12:55:40'),
(43, 43, 1, '2012-11-23 12:55:40'),
(44, 44, 1, '2012-11-23 12:55:40'),
(45, 45, 1, '2012-11-23 12:55:40'),
(46, 46, 1, '2012-11-23 12:55:40'),
(47, 47, 1, '2012-11-23 12:55:40'),
(48, 48, 1, '2012-11-23 12:55:40'),
(49, 49, 1, '2012-11-23 12:55:40'),
(50, 50, 1, '2012-11-23 12:55:40'),
(51, 51, 1, '2012-11-23 12:55:40'),
(52, 52, 1, '2012-11-23 12:55:40'),
(53, 53, 1, '2012-11-23 12:55:40'),
(54, 54, 1, '2012-11-23 12:55:40'),
(55, 55, 1, '2012-11-23 12:55:40'),
(56, 56, 1, '2012-11-23 12:55:40'),
(57, 57, 1, '2012-11-23 12:55:40'),
(58, 58, 1, '2012-11-23 12:55:40'),
(59, 59, 1, '2012-11-23 12:55:40'),
(60, 60, 1, '2012-11-23 12:55:40'),
(61, 61, 1, '2012-11-23 12:55:40'),
(62, 62, 1, '2012-11-23 12:55:40'),
(63, 63, 1, '2012-11-23 12:55:40'),
(64, 64, 1, '2012-11-23 12:55:40'),
(65, 65, 1, '2012-11-23 12:55:40'),
(66, 66, 1, '2012-11-23 12:55:40'),
(67, 67, 1, '2012-11-23 12:55:40'),
(68, 68, 1, '2012-11-23 12:55:40'),
(69, 69, 1, '2012-11-23 12:55:40'),
(70, 70, 1, '2012-11-23 12:55:40'),
(71, 71, 1, '2012-11-23 12:55:40'),
(72, 72, 1, '2012-11-23 12:55:40'),
(73, 73, 1, '2012-11-23 12:55:40'),
(74, 74, 1, '2012-11-23 12:55:40'),
(75, 75, 1, '2012-11-23 12:55:40'),
(76, 76, 1, '2012-11-23 12:55:40'),
(77, 77, 1, '2012-11-23 12:55:40'),
(78, 78, 1, '2012-11-23 12:55:40'),
(79, 79, 1, '2012-11-23 12:55:40'),
(80, 80, 1, '2012-11-23 12:55:40'),
(81, 81, 1, '2012-11-23 12:55:40'),
(82, 82, 1, '2012-11-23 12:55:40'),
(83, 83, 1, '2012-11-23 12:55:40'),
(84, 84, 1, '2012-11-23 12:55:40'),
(85, 85, 1, '2012-11-23 12:55:40'),
(86, 86, 1, '2012-11-23 12:55:40'),
(87, 87, 1, '2012-11-23 12:55:40'),
(88, 1, 2, '2012-11-23 12:56:25'),
(89, 2, 2, '2012-11-23 12:56:25'),
(90, 3, 2, '2012-11-23 12:56:25'),
(91, 4, 2, '2012-11-23 12:56:25'),
(92, 5, 2, '2012-11-23 12:56:25'),
(93, 6, 2, '2012-11-23 12:56:25'),
(94, 7, 2, '2012-11-23 12:56:25'),
(95, 8, 2, '2012-11-23 12:56:25'),
(96, 9, 2, '2012-11-23 12:56:25'),
(97, 10, 2, '2012-11-23 12:56:25'),
(98, 11, 2, '2012-11-23 12:56:25'),
(99, 12, 2, '2012-11-23 12:56:25'),
(100, 13, 2, '2012-11-23 12:56:25'),
(101, 14, 2, '2012-11-23 12:56:25'),
(102, 15, 2, '2012-11-23 12:56:25'),
(103, 16, 2, '2012-11-23 12:56:25'),
(104, 17, 2, '2012-11-23 12:56:25'),
(105, 18, 2, '2012-11-23 12:56:25'),
(106, 19, 2, '2012-11-23 12:56:25'),
(107, 20, 2, '2012-11-23 12:56:25'),
(108, 21, 2, '2012-11-23 12:56:25'),
(109, 22, 2, '2012-11-23 12:56:25'),
(110, 23, 2, '2012-11-23 12:56:25'),
(111, 24, 2, '2012-11-23 12:56:25'),
(112, 25, 2, '2012-11-23 12:56:25'),
(113, 26, 2, '2012-11-23 12:56:25'),
(114, 27, 2, '2012-11-23 12:56:25'),
(115, 28, 2, '2012-11-23 12:56:25'),
(116, 29, 2, '2012-11-23 12:56:25'),
(117, 30, 2, '2012-11-23 12:56:25'),
(118, 31, 2, '2012-11-23 12:56:25'),
(119, 32, 2, '2012-11-23 12:56:25'),
(120, 33, 2, '2012-11-23 12:56:25'),
(121, 34, 2, '2012-11-23 12:56:25'),
(122, 35, 2, '2012-11-23 12:56:25'),
(123, 36, 2, '2012-11-23 12:56:25'),
(124, 37, 2, '2012-11-23 12:56:25'),
(125, 38, 2, '2012-11-23 12:56:25'),
(126, 39, 2, '2012-11-23 12:56:25'),
(127, 40, 2, '2012-11-23 12:56:25'),
(128, 41, 2, '2012-11-23 12:56:25'),
(129, 42, 2, '2012-11-23 12:56:25'),
(130, 43, 2, '2012-11-23 12:56:25'),
(131, 44, 2, '2012-11-23 12:56:25'),
(132, 45, 2, '2012-11-23 12:56:25'),
(133, 46, 2, '2012-11-23 12:56:25'),
(134, 47, 2, '2012-11-23 12:56:25'),
(135, 48, 2, '2012-11-23 12:56:25'),
(136, 49, 2, '2012-11-23 12:56:25'),
(137, 50, 2, '2012-11-23 12:56:25'),
(138, 51, 2, '2012-11-23 12:56:25'),
(139, 52, 2, '2012-11-23 12:56:25'),
(140, 53, 2, '2012-11-23 12:56:25'),
(141, 54, 2, '2012-11-23 12:56:25'),
(142, 55, 2, '2012-11-23 12:56:25'),
(143, 56, 2, '2012-11-23 12:56:25'),
(144, 57, 2, '2012-11-23 12:56:25'),
(145, 58, 2, '2012-11-23 12:56:25'),
(146, 59, 2, '2012-11-23 12:56:25'),
(147, 60, 2, '2012-11-23 12:56:25'),
(148, 61, 2, '2012-11-23 12:56:25'),
(149, 62, 2, '2012-11-23 12:56:25'),
(150, 63, 2, '2012-11-23 12:56:25'),
(151, 64, 2, '2012-11-23 12:56:25'),
(152, 65, 2, '2012-11-23 12:56:25'),
(153, 66, 2, '2012-11-23 12:56:25'),
(154, 67, 2, '2012-11-23 12:56:25'),
(155, 68, 2, '2012-11-23 12:56:25'),
(156, 69, 2, '2012-11-23 12:56:25'),
(157, 70, 2, '2012-11-23 12:56:25'),
(158, 71, 2, '2012-11-23 12:56:25'),
(159, 72, 2, '2012-11-23 12:56:25'),
(160, 73, 2, '2012-11-23 12:56:25'),
(161, 74, 2, '2012-11-23 12:56:25'),
(162, 75, 2, '2012-11-23 12:56:25'),
(163, 76, 2, '2012-11-23 12:56:25'),
(164, 77, 2, '2012-11-23 12:56:25'),
(364, 39, 5, '2012-11-23 13:07:41'),
(166, 79, 2, '2012-11-23 12:56:25'),
(167, 80, 2, '2012-11-23 12:56:25'),
(168, 81, 2, '2012-11-23 12:56:25'),
(169, 82, 2, '2012-11-23 12:56:25'),
(170, 83, 2, '2012-11-23 12:56:25'),
(171, 84, 2, '2012-11-23 12:56:25'),
(172, 85, 2, '2012-11-23 12:56:25'),
(173, 86, 2, '2012-11-23 12:56:25'),
(174, 87, 2, '2012-11-23 12:56:25'),
(176, 2, 3, '2012-11-23 12:56:51'),
(177, 3, 3, '2012-11-23 12:56:51'),
(180, 6, 3, '2012-11-23 12:56:51'),
(181, 7, 3, '2012-11-23 12:56:51'),
(183, 9, 3, '2012-11-23 12:56:51'),
(188, 14, 3, '2012-11-23 12:56:51'),
(189, 15, 3, '2012-11-23 12:56:51'),
(190, 16, 3, '2012-11-23 12:56:51'),
(192, 18, 3, '2012-11-23 12:56:51'),
(376, 22, 5, '2012-11-23 13:24:21'),
(194, 20, 3, '2012-11-23 12:56:51'),
(195, 21, 3, '2012-11-23 12:56:51'),
(196, 22, 3, '2012-11-23 12:56:51'),
(197, 23, 3, '2012-11-23 12:56:51'),
(200, 26, 3, '2012-11-23 12:56:51'),
(359, 34, 5, '2012-11-23 13:06:29'),
(202, 28, 3, '2012-11-23 12:56:51'),
(203, 29, 3, '2012-11-23 12:56:51'),
(204, 30, 3, '2012-11-23 12:56:51'),
(205, 31, 3, '2012-11-23 12:56:51'),
(206, 32, 3, '2012-11-23 12:56:51'),
(207, 33, 3, '2012-11-23 12:56:51'),
(208, 34, 3, '2012-11-23 12:56:51'),
(209, 35, 3, '2012-11-23 12:56:51'),
(210, 36, 3, '2012-11-23 12:56:51'),
(211, 37, 3, '2012-11-23 12:56:51'),
(213, 39, 3, '2012-11-23 12:56:51'),
(214, 40, 3, '2012-11-23 12:56:51'),
(215, 41, 3, '2012-11-23 12:56:51'),
(352, 49, 5, '2012-11-23 12:59:04'),
(360, 33, 5, '2012-11-23 13:06:55'),
(354, 48, 5, '2012-11-23 12:59:37'),
(221, 47, 3, '2012-11-23 12:56:51'),
(222, 48, 3, '2012-11-23 12:56:51'),
(223, 49, 3, '2012-11-23 12:56:51'),
(224, 50, 3, '2012-11-23 12:56:51'),
(225, 51, 3, '2012-11-23 12:56:51'),
(226, 52, 3, '2012-11-23 12:56:51'),
(227, 53, 3, '2012-11-23 12:56:51'),
(228, 54, 3, '2012-11-23 12:56:51'),
(229, 55, 3, '2012-11-23 12:56:51'),
(230, 56, 3, '2012-11-23 12:56:51'),
(231, 57, 3, '2012-11-23 12:56:51'),
(232, 58, 3, '2012-11-23 12:56:51'),
(234, 60, 3, '2012-11-23 12:56:51'),
(235, 61, 3, '2012-11-23 12:56:51'),
(236, 62, 3, '2012-11-23 12:56:51'),
(350, 63, 4, '2012-11-23 12:58:25'),
(370, 65, 5, '2012-11-23 13:20:30'),
(239, 65, 3, '2012-11-23 12:56:51'),
(240, 66, 3, '2012-11-23 12:56:51'),
(241, 67, 3, '2012-11-23 12:56:51'),
(242, 68, 3, '2012-11-23 12:56:51'),
(243, 69, 3, '2012-11-23 12:56:51'),
(245, 71, 3, '2012-11-23 12:56:51'),
(246, 72, 3, '2012-11-23 12:56:51'),
(247, 73, 3, '2012-11-23 12:56:51'),
(248, 74, 3, '2012-11-23 12:56:51'),
(249, 75, 3, '2012-11-23 12:56:51'),
(250, 76, 3, '2012-11-23 12:56:51'),
(251, 77, 3, '2012-11-23 12:56:51'),
(362, 30, 5, '2012-11-23 13:07:16'),
(253, 79, 3, '2012-11-23 12:56:51'),
(254, 80, 3, '2012-11-23 12:56:51'),
(255, 81, 3, '2012-11-23 12:56:51'),
(378, 7, 5, '2012-11-23 13:31:27'),
(260, 86, 3, '2012-11-23 12:56:51'),
(263, 2, 4, '2012-11-23 12:57:25'),
(264, 3, 4, '2012-11-23 12:57:25'),
(267, 6, 4, '2012-11-23 12:57:25'),
(268, 7, 4, '2012-11-23 12:57:25'),
(270, 9, 4, '2012-11-23 12:57:25'),
(372, 9, 5, '2012-11-23 13:21:34'),
(275, 14, 4, '2012-11-23 12:57:25'),
(276, 15, 4, '2012-11-23 12:57:25'),
(277, 16, 4, '2012-11-23 12:57:25'),
(373, 14, 5, '2012-11-23 13:22:52'),
(375, 21, 5, '2012-11-23 13:24:18'),
(374, 16, 5, '2012-11-23 13:24:10'),
(282, 21, 4, '2012-11-23 12:57:25'),
(283, 22, 4, '2012-11-23 12:57:25'),
(284, 23, 4, '2012-11-23 12:57:25'),
(287, 26, 4, '2012-11-23 12:57:25'),
(358, 28, 5, '2012-11-23 13:06:16'),
(289, 28, 4, '2012-11-23 12:57:25'),
(290, 29, 4, '2012-11-23 12:57:25'),
(291, 30, 4, '2012-11-23 12:57:25'),
(292, 31, 4, '2012-11-23 12:57:25'),
(293, 32, 4, '2012-11-23 12:57:25'),
(294, 33, 4, '2012-11-23 12:57:25'),
(295, 34, 4, '2012-11-23 12:57:25'),
(296, 35, 4, '2012-11-23 12:57:25'),
(297, 36, 4, '2012-11-23 12:57:25'),
(298, 37, 4, '2012-11-23 12:57:25'),
(366, 77, 5, '2012-11-23 13:09:03'),
(300, 39, 4, '2012-11-23 12:57:25'),
(301, 40, 4, '2012-11-23 12:57:25'),
(302, 41, 4, '2012-11-23 12:57:25'),
(353, 51, 5, '2012-11-23 12:59:06'),
(357, 80, 5, '2012-11-23 13:01:11'),
(368, 58, 5, '2012-11-23 13:11:42'),
(351, 62, 5, '2012-11-23 12:58:38'),
(308, 47, 4, '2012-11-23 12:57:25'),
(309, 48, 4, '2012-11-23 12:57:25'),
(310, 49, 4, '2012-11-23 12:57:25'),
(311, 50, 4, '2012-11-23 12:57:25'),
(312, 51, 4, '2012-11-23 12:57:25'),
(313, 52, 4, '2012-11-23 12:57:25'),
(314, 53, 4, '2012-11-23 12:57:25'),
(315, 54, 4, '2012-11-23 12:57:25'),
(316, 55, 4, '2012-11-23 12:57:25'),
(367, 54, 5, '2012-11-23 13:11:29'),
(318, 57, 4, '2012-11-23 12:57:25'),
(319, 58, 4, '2012-11-23 12:57:25'),
(321, 60, 4, '2012-11-23 12:57:25'),
(371, 66, 5, '2012-11-23 13:20:49'),
(323, 62, 4, '2012-11-23 12:57:25'),
(349, 63, 3, '2012-11-23 12:58:25'),
(369, 68, 5, '2012-11-23 13:20:16'),
(326, 65, 4, '2012-11-23 12:57:25'),
(327, 66, 4, '2012-11-23 12:57:25'),
(328, 67, 4, '2012-11-23 12:57:25'),
(329, 68, 4, '2012-11-23 12:57:25'),
(330, 69, 4, '2012-11-23 12:57:25'),
(332, 71, 4, '2012-11-23 12:57:25'),
(333, 72, 4, '2012-11-23 12:57:25'),
(336, 75, 4, '2012-11-23 12:57:25'),
(337, 76, 4, '2012-11-23 12:57:25'),
(338, 77, 4, '2012-11-23 12:57:25'),
(361, 36, 5, '2012-11-23 13:07:01'),
(340, 79, 4, '2012-11-23 12:57:25'),
(341, 80, 4, '2012-11-23 12:57:25'),
(342, 81, 4, '2012-11-23 12:57:25'),
(377, 3, 5, '2012-11-23 13:31:12'),
(379, 81, 5, '2012-11-23 13:55:52'),
(347, 86, 4, '2012-11-23 12:57:25'),
(355, 52, 5, '2012-11-23 12:59:44'),
(356, 53, 5, '2012-11-23 12:59:52'),
(365, 41, 5, '2012-11-23 13:07:43');

--
-- Table structure for table `dev_roles`
--

DROP TABLE IF EXISTS `dev_roles`;
CREATE TABLE `dev_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `abbrev` varchar(10) DEFAULT NULL,
  `assignable` enum('y','n') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`),
  KEY `role_2` (`role`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dev_roles`
--

INSERT INTO `dev_roles` (`id`, `role`, `description`, `abbrev`, `assignable`, `created`) VALUES
(1, 'System administrator', 'ETI admin; creates new projects and lead experts', 'sysadmin', 'n', '2010-08-26 08:46:38'),
(2, 'Lead expert', 'General manager of a project', 'lead ex', 'y', '2010-08-26 08:46:38'),
(3, 'Expert', 'Content manager of a project', 'expert', 'y', '2010-08-26 08:46:38'),
(4, 'Editor', 'Edits specific parts of a project', 'editor', 'y', '2010-08-26 08:46:38'),
(5, 'Contributor', 'Contributes to a project but cannot edit', 'contrib', 'y', '2010-08-26 08:46:39');

--
-- Table structure for table `dev_sections`
--

DROP TABLE IF EXISTS `dev_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section` varchar(32) NOT NULL,
  `show_order` int(2) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `dev_settings`
--

DROP TABLE IF EXISTS `dev_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `setting` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dev_synonyms`
--

DROP TABLE IF EXISTS `dev_synonyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_synonyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `synonym` varchar(128) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `lit_ref_id` int(11) DEFAULT NULL,
  `show_order` int(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`),
  KEY `taxon_id` (`taxon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `dev_taxa`
--

DROP TABLE IF EXISTS `dev_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `rank_id` int(11) NOT NULL,
  `taxon_order` int(11) DEFAULT NULL,
  `is_hybrid` tinyint(1) DEFAULT '0',
  `list_level` int(5) DEFAULT '0',
  `is_empty` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`parent_id`),
  KEY `project_id_3` (`project_id`,`parent_id`,`rank_id`),
  KEY `is_empty` (`is_empty`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dev_timezones`
--

DROP TABLE IF EXISTS `dev_timezones`;
CREATE TABLE `dev_timezones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timezone` varchar(9) NOT NULL,
  `locations` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timezone` (`timezone`,`locations`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dev_timezones`
--

INSERT INTO `dev_timezones` (`id`, `timezone`, `locations`, `created`) VALUES
(1, 'GMT-12:00', 'International Date Line West', '2010-09-21 14:47:44'),
(2, 'GMT-11:00', 'Midway Island, Samoa', '2010-09-21 14:47:44'),
(3, 'GMT-10:00', 'Hawaii', '2010-09-21 14:47:44'),
(4, 'GMT-09:00', 'Alaska', '2010-09-21 14:47:44'),
(5, 'GMT-08:00', 'Pacific Time U& Canada); Tijuana', '2010-09-21 14:47:44'),
(6, 'GMT-07:00', 'Arizona', '2010-09-21 14:47:44'),
(7, 'GMT-07:00', 'Mountain Time U& Canada)', '2010-09-21 14:47:44'),
(8, 'GMT-07:00', 'Chihuahua, La Paz, Mazatlan', '2010-09-21 14:47:44'),
(9, 'GMT-06:00', 'Central America', '2010-09-21 14:47:44'),
(10, 'GMT-06:00', 'Saskatchewan', '2010-09-21 14:47:44'),
(11, 'GMT-06:00', 'Guadalajara, Mexico City, Monterrey', '2010-09-21 14:47:44'),
(12, 'GMT-06:00', 'Central Time U& Canada)', '2010-09-21 14:47:44'),
(13, 'GMT-05:00', 'Indiana East)', '2010-09-21 14:47:44'),
(14, 'GMT-05:00', 'Bogota, Lima, Quito', '2010-09-21 14:47:44'),
(15, 'GMT-05:00', 'Eastern Time U & Canada)', '2010-09-21 14:47:44'),
(16, 'GMT-04:00', 'Caracas, La Paz', '2010-09-21 14:47:44'),
(17, 'GMT-04:00', 'Santiago', '2010-09-21 14:47:44'),
(18, 'GMT-04:00', 'Atlantic Time Canada)', '2010-09-21 14:47:44'),
(19, 'GMT-03:30', 'Newfoundland', '2010-09-21 14:47:44'),
(20, 'GMT-03:00', 'Buenos Aires, Georgetown', '2010-09-21 14:47:44'),
(21, 'GMT-03:00', 'Greenland', '2010-09-21 14:47:44'),
(22, 'GMT-03:00', 'Brasilia', '2010-09-21 14:47:44'),
(23, 'GMT-02:00', 'Mid-Atlantic', '2010-09-21 14:47:44'),
(24, 'GMT-01:00', 'Cape Verde Is.', '2010-09-21 14:47:44'),
(25, 'GMT-01:00', 'Azores', '2010-09-21 14:47:44'),
(26, 'GMT', 'Casablanca, Monrovia', '2010-09-21 14:47:44'),
(27, 'GMT+01:00', 'West Central Africa', '2010-09-21 14:47:44'),
(28, 'GMT+01:00', 'Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', '2010-09-21 14:47:44'),
(29, 'GMT+01:00', 'Brussels, Copenhagen, Madrid, Paris', '2010-09-21 14:47:44'),
(30, 'GMT+01:00', 'Sarajevo, Skopje, Warsaw, Zagreb', '2010-09-21 14:47:44'),
(31, 'GMT+01:00', 'Belgrade, Bratislava, Budapest, Ljubljana, Prague', '2010-09-21 14:47:44'),
(32, 'GMT+02:00', 'Harare, Pretoria', '2010-09-21 14:47:44'),
(33, 'GMT+02:00', 'Jerusalem', '2010-09-21 14:47:44'),
(34, 'GMT+02:00', 'Athens, Istanbul, Minsk', '2010-09-21 14:47:44'),
(35, 'GMT+02:00', 'Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius', '2010-09-21 14:47:44'),
(36, 'GMT+02:00', 'Cairo', '2010-09-21 14:47:44'),
(37, 'GMT+02:00', 'Bucharest', '2010-09-21 14:47:44'),
(38, 'GMT+03:00', 'Nairobi', '2010-09-21 14:47:44'),
(39, 'GMT+03:00', 'Kuwait, Riyadh', '2010-09-21 14:47:44'),
(40, 'GMT+03:00', 'Moscow, St. Petersburg, Volgograd', '2010-09-21 14:47:44'),
(41, 'GMT+03:00', 'Baghdad', '2010-09-21 14:47:44'),
(42, 'GMT+03:30', 'Tehran', '2010-09-21 14:47:44'),
(43, 'GMT+04:00', 'Abu Dhabi, Muscat', '2010-09-21 14:47:44'),
(44, 'GMT+04:00', 'Baku, Tbilisi, Yerevan', '2010-09-21 14:47:44'),
(45, 'GMT+04:30', 'Kabul', '2010-09-21 14:47:44'),
(46, 'GMT+05:00', 'Islamabad, Karachi, Tashkent', '2010-09-21 14:47:44'),
(47, 'GMT+05:00', 'Ekaterinburg', '2010-09-21 14:47:44'),
(48, 'GMT+05:30', 'Chennai, Kolkata, Mumbai, New Delhi', '2010-09-21 14:47:44'),
(49, 'GMT+05:45', 'Kathmandu', '2010-09-21 14:47:44'),
(50, 'GMT+06:00', 'Sri Jayawardenepura', '2010-09-21 14:47:44'),
(51, 'GMT+06:00', 'Astana, Dhaka', '2010-09-21 14:47:44'),
(52, 'GMT+06:00', 'Almaty, Novosibirsk', '2010-09-21 14:47:44'),
(53, 'GMT+06:30', 'Rangoon', '2010-09-21 14:47:44'),
(54, 'GMT+07:00', 'Bangkok, Hanoi, Jakarta', '2010-09-21 14:47:44'),
(55, 'GMT+07:00', 'Krasnoyarsk', '2010-09-21 14:47:45'),
(56, 'GMT+08:00', 'Perth', '2010-09-21 14:47:45'),
(57, 'GMT+08:00', 'Taipei', '2010-09-21 14:47:45'),
(58, 'GMT+08:00', 'Kuala Lumpur, Singapore', '2010-09-21 14:47:45'),
(59, 'GMT+08:00', 'Beijing, Chongqing, Hong Kong, Urumqi', '2010-09-21 14:47:45'),
(60, 'GMT+08:00', 'Irkutsk, Ulaan Bataar', '2010-09-21 14:47:45'),
(61, 'GMT+09:00', 'Osaka, Sapporo, Tokyo', '2010-09-21 14:47:45'),
(62, 'GMT+09:00', 'Seoul', '2010-09-21 14:47:45'),
(63, 'GMT+09:00', 'Yakutsk', '2010-09-21 14:47:45'),
(64, 'GMT+09:30', 'Darwin', '2010-09-21 14:47:45'),
(65, 'GMT+09:30', 'Adelaide', '2010-09-21 14:47:45'),
(66, 'GMT+10:00', 'Guam, Port Moresby', '2010-09-21 14:47:45'),
(67, 'GMT+10:00', 'Brisbane', '2010-09-21 14:47:45'),
(68, 'GMT+10:00', 'Vladivostok', '2010-09-21 14:47:45'),
(69, 'GMT+10:00', 'Hobart', '2010-09-21 14:47:45'),
(70, 'GMT+10:00', 'Canberra, Melbourne, Sydney', '2010-09-21 14:47:45'),
(71, 'GMT+11:00', 'Magadan, Solomon Is., New Caledonia', '2010-09-21 14:47:45'),
(72, 'GMT+12:00', 'Fiji, Kamchatka, Marshall Is.', '2010-09-21 14:47:45'),
(73, 'GMT+12:00', 'Auckland, Wellington', '2010-09-21 14:47:45'),
(74, 'GMT+13:00', 'Nuku''alofa', '2010-09-21 14:47:45');


--
-- Table structure for table `dev_users`
--

DROP TABLE IF EXISTS `dev_users`;
CREATE TABLE `dev_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email_address` varchar(54) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `superuser` tinyint(1) NOT NULL DEFAULT '0',
  `timezone_id` int(11) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `logins` int(11) NOT NULL DEFAULT '0',
  `password_changed` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email_address`),
  UNIQUE KEY `username_2` (`username`),
  KEY `password` (`password`),
  KEY `superuser` (`superuser`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dev_users`
--

INSERT INTO `dev_users` (`id`, `username`, `password`, `first_name`, `last_name`, `email_address`, `active`, `superuser`, `timezone_id`, `photo_path`, `email_notifications`, `last_login`, `logins`, `password_changed`, `created_by`, `last_change`, `created`) VALUES
(15, 'sysadmin', '48a365b4ce1e322a55ae9017f3daf0c0', 'System', 'Administrator', 'sysadmin@eti.uva.nl', 1, 1, 1, NULL, 0, '2013-01-03 09:52:08', 1013, NULL, 0, '2013-01-03 08:52:08', '2010-09-06 10:26:21'),
(48, 'contributor', 'f5d1278e8109edd94e1e4197e04873b9', 'Contributor', '-', 'vvv@vvv.nl', 1, 0, 1, NULL, 0, '2012-11-30 12:42:09', 1, NULL, 15, '2012-11-30 11:42:09', '2012-11-23 10:33:20'),
(47, 'editor', 'f5d1278e8109edd94e1e4197e04873b9', 'Editor', '-', 'cccc@ccc.com', 1, 0, 3, NULL, 0, NULL, 0, NULL, 15, '2012-11-23 09:23:25', '2012-11-23 10:23:25'),
(46, 'expert', 'f5d1278e8109edd94e1e4197e04873b9', 'Expert', 'User', 'a@bc.com', 1, 0, 1, NULL, 1, '2012-11-23 14:57:06', 6, NULL, 15, '2012-11-23 13:57:06', '2012-11-23 10:20:49'),
(45, 'lead', 'f5d1278e8109edd94e1e4197e04873b9', 'Lead', 'User', 'xxx@xxx.xxx', 1, 0, 1, NULL, 1, '2012-11-23 14:48:58', 4, NULL, 15, '2012-11-23 13:48:58', '2012-11-23 10:07:10');


--
-- Table structure for table `dev_users_taxa`
--

DROP TABLE IF EXISTS `dev_users_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dev_users_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`user_id`),
  KEY `project_id_2` (`project_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dev_users_taxa`
--

LOCK TABLES `dev_users_taxa` WRITE;
/*!40000 ALTER TABLE `dev_users_taxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `dev_users_taxa` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-11-07 15:13:28

