-- phpMyAdmin SQL Dump
-- version 2.11.9.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2012 at 10:31 PM
-- Server version: 5.1.48
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `linnaeus_ng`
--

-- --------------------------------------------------------

--
-- Table structure for table `dev_characteristics`
--

CREATE TABLE IF NOT EXISTS `dev_characteristics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `got_labels` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1177 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_characteristics_labels`
--

CREATE TABLE IF NOT EXISTS `dev_characteristics_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` text NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`characteristic_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1231 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_characteristics_labels_states`
--

CREATE TABLE IF NOT EXISTS `dev_characteristics_labels_states` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7385 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_characteristics_matrices`
--

CREATE TABLE IF NOT EXISTS `dev_characteristics_matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `show_order` smallint(6) NOT NULL DEFAULT '-1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`characteristic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1216 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_characteristics_states`
--

CREATE TABLE IF NOT EXISTS `dev_characteristics_states` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22653 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_choices_content_keysteps`
--

CREATE TABLE IF NOT EXISTS `dev_choices_content_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `choice_txt` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`choice_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=300175 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_choices_content_keysteps_undo`
--

CREATE TABLE IF NOT EXISTS `dev_choices_content_keysteps_undo` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_choices_keysteps`
--

CREATE TABLE IF NOT EXISTS `dev_choices_keysteps` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=342193 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_commonnames`
--

CREATE TABLE IF NOT EXISTS `dev_commonnames` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=326654 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_content`
--

CREATE TABLE IF NOT EXISTS `dev_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `subject` varchar(32) NOT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=402 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_content_free_modules`
--

CREATE TABLE IF NOT EXISTS `dev_content_free_modules` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1138 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_content_introduction`
--

CREATE TABLE IF NOT EXISTS `dev_content_introduction` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2975 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_content_keysteps`
--

CREATE TABLE IF NOT EXISTS `dev_content_keysteps` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115444 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_content_keysteps_undo`
--

CREATE TABLE IF NOT EXISTS `dev_content_keysteps_undo` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=150 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_content_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_content_taxa` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=170464 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_content_taxa_undo`
--

CREATE TABLE IF NOT EXISTS `dev_content_taxa_undo` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=251 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_diversity_index`
--

CREATE TABLE IF NOT EXISTS `dev_diversity_index` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=247 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_diversity_index_old`
--

CREATE TABLE IF NOT EXISTS `dev_diversity_index_old` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `type_id` int(10) NOT NULL,
  `boundary` geometrycollection NOT NULL,
  `boundary_nodes` text NOT NULL,
  `score` tinyint(4) NOT NULL,
  `encoded_json` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=179 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_free_modules_pages`
--

CREATE TABLE IF NOT EXISTS `dev_free_modules_pages` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1179 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_free_modules_projects`
--

CREATE TABLE IF NOT EXISTS `dev_free_modules_projects` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=168 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_free_modules_projects_users`
--

CREATE TABLE IF NOT EXISTS `dev_free_modules_projects_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `free_module_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`user_id`,`free_module_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=296 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_free_module_media`
--

CREATE TABLE IF NOT EXISTS `dev_free_module_media` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_geodata_types`
--

CREATE TABLE IF NOT EXISTS `dev_geodata_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `colour` varchar(6) DEFAULT NULL,
  `type` enum('marker','polygon','both') DEFAULT 'both',
  `show_order` smallint(2) NOT NULL DEFAULT '99',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1363 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_geodata_types_titles`
--

CREATE TABLE IF NOT EXISTS `dev_geodata_types_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`type_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1224 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_glossary`
--

CREATE TABLE IF NOT EXISTS `dev_glossary` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=113423 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_glossary_media`
--

CREATE TABLE IF NOT EXISTS `dev_glossary_media` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8264 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_glossary_media_captions`
--

CREATE TABLE IF NOT EXISTS `dev_glossary_media_captions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`media_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1082 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_glossary_synonyms`
--

CREATE TABLE IF NOT EXISTS `dev_glossary_synonyms` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=90295 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_heartbeats`
--

CREATE TABLE IF NOT EXISTS `dev_heartbeats` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=745 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_helptexts`
--

CREATE TABLE IF NOT EXISTS `dev_helptexts` (
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

-- --------------------------------------------------------

--
-- Table structure for table `dev_hotwords`
--

CREATE TABLE IF NOT EXISTS `dev_hotwords` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32514 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_hybrids`
--

CREATE TABLE IF NOT EXISTS `dev_hybrids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hybrid` varchar(128) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hybrid` (`hybrid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_introduction_media`
--

CREATE TABLE IF NOT EXISTS `dev_introduction_media` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1176 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_introduction_pages`
--

CREATE TABLE IF NOT EXISTS `dev_introduction_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `got_content` tinyint(1) NOT NULL DEFAULT '0',
  `show_order` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3012 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_keysteps`
--

CREATE TABLE IF NOT EXISTS `dev_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `number` int(3) NOT NULL DEFAULT '0',
  `is_start` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idxKeystepsNumber` (`project_id`,`number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133854 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_keytrees`
--

CREATE TABLE IF NOT EXISTS `dev_keytrees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `chunk` int(3) NOT NULL DEFAULT '0',
  `keytree` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`chunk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_l2_diversity_index`
--

CREATE TABLE IF NOT EXISTS `dev_l2_diversity_index` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33139 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_l2_maps`
--

CREATE TABLE IF NOT EXISTS `dev_l2_maps` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=382 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_l2_occurrences_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_l2_occurrences_taxa` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9644083 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_l2_occurrences_taxa_combi`
--

CREATE TABLE IF NOT EXISTS `dev_l2_occurrences_taxa_combi` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23502 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_labels_languages`
--

CREATE TABLE IF NOT EXISTS `dev_labels_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label_language_id` int(11) NOT NULL,
  `label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_labels_projects_ranks`
--

CREATE TABLE IF NOT EXISTS `dev_labels_projects_ranks` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4308 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_labels_sections`
--

CREATE TABLE IF NOT EXISTS `dev_labels_sections` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=108 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_languages`
--

CREATE TABLE IF NOT EXISTS `dev_languages` (
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

-- --------------------------------------------------------

--
-- Table structure for table `dev_languages_projects`
--

CREATE TABLE IF NOT EXISTS `dev_languages_projects` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=589 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_literature`
--

CREATE TABLE IF NOT EXISTS `dev_literature` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111829 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_literature_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_literature_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `literature_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`literature_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=437001 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_matrices`
--

CREATE TABLE IF NOT EXISTS `dev_matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `got_names` tinyint(1) DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=408 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_matrices_names`
--

CREATE TABLE IF NOT EXISTS `dev_matrices_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=375 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_matrices_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_matrices_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`taxon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=90376 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_matrices_taxa_states`
--

CREATE TABLE IF NOT EXISTS `dev_matrices_taxa_states` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=652223 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_media_descriptions_taxon`
--

CREATE TABLE IF NOT EXISTS `dev_media_descriptions_taxon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`media_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=241127 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_media_taxon`
--

CREATE TABLE IF NOT EXISTS `dev_media_taxon` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=276224 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_modules`
--

CREATE TABLE IF NOT EXISTS `dev_modules` (
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

-- --------------------------------------------------------

--
-- Table structure for table `dev_modules_projects`
--

CREATE TABLE IF NOT EXISTS `dev_modules_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `show_order` tinyint(2) NOT NULL DEFAULT '0',
  `active` enum('y','n') NOT NULL DEFAULT 'y',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`module_id`),
  KEY `project_id_2` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3607 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_modules_projects_users`
--

CREATE TABLE IF NOT EXISTS `dev_modules_projects_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`module_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2551 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_occurrences_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_occurrences_taxa` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40399962 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_pages_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_pages_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page` varchar(32) NOT NULL,
  `show_order` int(11) DEFAULT NULL,
  `def_page` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=695 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_pages_taxa_titles`
--

CREATE TABLE IF NOT EXISTS `dev_pages_taxa_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=482 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_projects`
--

CREATE TABLE IF NOT EXISTS `dev_projects` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=572 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_projects_ranks`
--

CREATE TABLE IF NOT EXISTS `dev_projects_ranks` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4351 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_projects_roles_users`
--

CREATE TABLE IF NOT EXISTS `dev_projects_roles_users` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=712 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_ranks`
--

CREATE TABLE IF NOT EXISTS `dev_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` varchar(128) NOT NULL,
  `additional` varchar(64) DEFAULT NULL,
  `default_label` varchar(32) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `in_col` tinyint(1) DEFAULT '0',
  `can_hybrid` tinyint(1) DEFAULT '0',
  `ideal_parent_id` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rank` (`rank`,`additional`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_rights`
--

CREATE TABLE IF NOT EXISTS `dev_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `view_description` varchar(64) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `controller` (`controller`,`view`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=88 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_rights_roles`
--

CREATE TABLE IF NOT EXISTS `dev_rights_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `right_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `right_id_2` (`right_id`,`role_id`),
  KEY `right_id` (`right_id`,`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_roles`
--

CREATE TABLE IF NOT EXISTS `dev_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `abbrev` varchar(10) DEFAULT NULL,
  `assignable` enum('y','n') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`),
  KEY `role_2` (`role`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_sections`
--

CREATE TABLE IF NOT EXISTS `dev_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section` varchar(32) NOT NULL,
  `show_order` int(2) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1094 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_settings`
--

CREATE TABLE IF NOT EXISTS `dev_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `setting` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=114 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_synonyms`
--

CREATE TABLE IF NOT EXISTS `dev_synonyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `synonym` varchar(128) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `lit_ref_id` int(11) DEFAULT NULL,
  `show_order` int(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`),
  KEY `taxon_id` (`taxon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36537 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon` varchar(255) NOT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=363390 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_timezones`
--

CREATE TABLE IF NOT EXISTS `dev_timezones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timezone` varchar(9) NOT NULL,
  `locations` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timezone` (`timezone`,`locations`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_translate_me`
--

CREATE TABLE IF NOT EXISTS `dev_translate_me` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) NOT NULL,
  `content` varchar(255) NOT NULL,
  `env` varchar(8) NOT NULL DEFAULT 'front',
  `done` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content` (`content`,`env`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=286 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_users`
--

CREATE TABLE IF NOT EXISTS `dev_users` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_users_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_users_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`user_id`),
  KEY `project_id_2` (`project_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42520 ;
