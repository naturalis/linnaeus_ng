-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 17, 2013 at 07:25 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `linnaeus_ng`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fnStripTags`( Dirty varchar(4000) ) RETURNS varchar(4000) CHARSET utf8
    DETERMINISTIC
BEGIN
  DECLARE iStart, iEnd, iLength int;
  WHILE Locate( '<', Dirty ) > 0 And Locate( '>', Dirty, Locate( '<', Dirty )) > 0 DO
    BEGIN
      SET iStart = Locate( '<', Dirty ), iEnd = Locate( '>', Dirty, Locate('<', Dirty ));
      SET iLength = ( iEnd - iStart) + 1;
      IF iLength > 0 THEN
        BEGIN
          SET Dirty = Insert( Dirty, iStart, iLength, '');
        END;
      END IF;
    END;
  END WHILE;
  RETURN Dirty;
END$$

DELIMITER ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3002 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_characteristics_chargroups`
--

CREATE TABLE IF NOT EXISTS `dev_characteristics_chargroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `chargroup_id` int(11) NOT NULL,
  `show_order` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2979 ;

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
  `additional` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`characteristic_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3003 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28020 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3002 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_characteristics_states`
--

CREATE TABLE IF NOT EXISTS `dev_characteristics_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_dimensions` varchar(16) DEFAULT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28027 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_chargroups`
--

CREATE TABLE IF NOT EXISTS `dev_chargroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `show_order` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=665 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_chargroups_labels`
--

CREATE TABLE IF NOT EXISTS `dev_chargroups_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `chargroup_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=663 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31164 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

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
  KEY `project_id` (`project_id`,`keystep_id`),
  KEY `project_id_2` (`project_id`,`res_taxon_id`),
  KEY `project_id_3` (`project_id`,`res_keystep_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31166 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45757 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=167 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9108 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=672 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15472 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102894 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_dump`
--

CREATE TABLE IF NOT EXISTS `dev_dump` (
  `p` int(11) DEFAULT NULL,
  `i_int` int(11) DEFAULT NULL,
  `v_varchar` varchar(255) DEFAULT NULL,
  `t_text` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9119 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=108 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40988 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5663 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3670 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13916 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_gui_menu_order`
--

CREATE TABLE IF NOT EXISTS `dev_gui_menu_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `ref_type` enum('char','group') NOT NULL,
  `show_order` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`matrix_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=308 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1426 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43996 ;

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
-- Table structure for table `dev_interface_texts`
--

CREATE TABLE IF NOT EXISTS `dev_interface_texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `env` varchar(8) NOT NULL DEFAULT 'app',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text` (`text`(255),`env`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1182 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_interface_translations`
--

CREATE TABLE IF NOT EXISTS `dev_interface_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_text_id` int(11) NOT NULL,
  `language_id` tinyint(3) NOT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=144 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=673 ;

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
  KEY `idxKeystepsNumber` (`project_id`,`number`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`is_start`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15476 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5195 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

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
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_3` (`project_id`,`taxon_id`,`map_id`,`type_id`,`square_number`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`taxon_id`,`map_id`),
  KEY `project_id_4` (`project_id`,`taxon_id`),
  KEY `taxon_id` (`taxon_id`),
  KEY `project_id_5` (`project_id`,`taxon_id`,`map_id`,`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2038855 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=690 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2588 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=232 ;

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
  `year` year(4) NOT NULL,
  `suffix` varchar(3) DEFAULT NULL,
  `text` text NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6066 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12517 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=223 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=204 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13342 ;

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
  `variation_id` int(11) DEFAULT NULL,
  `characteristic_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`taxon_id`,`characteristic_id`,`state_id`),
  UNIQUE KEY `project_id_5` (`project_id`,`matrix_id`,`variation_id`,`characteristic_id`,`state_id`),
  KEY `project_id_2` (`project_id`,`matrix_id`,`taxon_id`,`state_id`),
  KEY `project_id_3` (`project_id`,`matrix_id`,`variation_id`,`state_id`),
  KEY `project_id_4` (`project_id`,`matrix_id`,`ref_matrix_id`,`state_id`),
  KEY `project_id_6` (`project_id`,`matrix_id`,`ref_matrix_id`,`characteristic_id`,`state_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=432460 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_matrices_variations`
--

CREATE TABLE IF NOT EXISTS `dev_matrices_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`variation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8690 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74332 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_media_taxon`
--

CREATE TABLE IF NOT EXISTS `dev_media_taxon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93363 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4396 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1215 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_nbc_extras`
--

CREATE TABLE IF NOT EXISTS `dev_nbc_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation') NOT NULL DEFAULT 'taxon',
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34011 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1924480 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=357 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=185 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_projects`
--

CREATE TABLE IF NOT EXISTS `dev_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_name` varchar(64) NOT NULL,
  `sys_description` text NOT NULL,
  `short_name` varchar(32) DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `css_url` varchar(255) DEFAULT NULL,
  `includes_hybrids` tinyint(1) NOT NULL DEFAULT '0',
  `keywords` text,
  `description` text,
  `group` varchar(64) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_name` (`sys_name`),
  UNIQUE KEY `short_name` (`short_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11632 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=231 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_ranks`
--

CREATE TABLE IF NOT EXISTS `dev_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` varchar(128) NOT NULL,
  `additional` varchar(64) DEFAULT NULL,
  `default_label` varchar(32) DEFAULT NULL,
  `abbreviation` varchar(15) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `in_col` tinyint(1) DEFAULT '0',
  `can_hybrid` tinyint(1) DEFAULT '0',
  `ideal_parent_id` tinyint(3) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rank` (`rank`,`additional`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=92 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=958 ;

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
  UNIQUE KEY `project_id_2` (`project_id`,`setting`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=779 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_synonyms`
--

CREATE TABLE IF NOT EXISTS `dev_synonyms` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43755 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_taxa`
--

CREATE TABLE IF NOT EXISTS `dev_taxa` (
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
  UNIQUE KEY `project_id_4` (`project_id`,`taxon`,`rank_id`,`parent_id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`parent_id`),
  KEY `project_id_3` (`project_id`,`parent_id`,`rank_id`),
  KEY `is_empty` (`is_empty`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=97509 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_taxa_relations`
--

CREATE TABLE IF NOT EXISTS `dev_taxa_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation') NOT NULL DEFAULT 'taxon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxon_id` (`taxon_id`,`relation_id`,`ref_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14772 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_taxa_variations`
--

CREATE TABLE IF NOT EXISTS `dev_taxa_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxon_id` (`taxon_id`,`label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5625 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=747 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_variations_labels`
--

CREATE TABLE IF NOT EXISTS `dev_variations_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `label_type` enum('alternative','prefix','postfix','') NOT NULL DEFAULT 'alternative',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7936 ;

-- --------------------------------------------------------

--
-- Table structure for table `dev_variation_relations`
--

CREATE TABLE IF NOT EXISTS `dev_variation_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation') NOT NULL DEFAULT 'taxon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`variation_id`,`relation_id`,`ref_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21951 ;



INSERT INTO `dev_helptexts` (`id`, `controller`, `view`, `subject`, `helptext`, `show_order`, `created`, `last_change`) VALUES
(1, 'users', 'login', 'Logging in', 'To log in, fill in your Linnaeus NG-username and password, and press the button labeled "Login".', 0, '2010-08-26 10:51:15', '2010-08-26 06:51:15'),
(2, 'users', 'login', 'Problems logging in?', 'If you cannot login, please <a href="mailto:helpdesk@linnaeus.eti.uva.nl">contact the helpdesk</a>.', 1, '2010-08-26 10:51:15', '2010-08-26 06:51:15'),
(3, 'users', 'edit', 'Role', 'The ''role'' indicates the role this user will have in the current project. Hover your mouse over the role''s names to see a short description.', 0, '2010-08-26 10:51:15', '2010-08-26 06:51:15'),
(4, 'users', 'edit', 'Active', '''Active'' indicates whether a user is actively working on the current project. When set to ''n'', the user can no longer log in or work on the project. It allows you to temporarily disable users without deleting them outright.<br />Users that have the role of ''Lead expert'' cannot change role, or be made in-active, as they are the lead manager of a project.', 1, '2010-08-26 10:51:15', '2010-08-26 06:51:15');

INSERT INTO `dev_hybrids` (`id`, `hybrid`, `created`, `last_change`) VALUES
(1, 'x Genus', '2010-10-14 12:02:03', '2010-10-14 10:02:03'),
(2, 'x Genus species', '2010-10-14 12:02:03', '2010-10-14 10:02:03'),
(3, 'Genus x species', '2010-10-14 12:02:03', '2010-10-14 10:02:03'),
(4, 'Genus species x Genus species', '2010-10-14 12:02:03', '2010-10-14 10:02:03');

INSERT INTO `dev_interface_texts` (`id`, `text`, `env`, `created`) VALUES
(1, 'Select a project to work on', 'admin', '2012-12-11 08:51:58'),
(2, 'Projects', 'admin', '2012-12-11 08:51:58'),
(3, 'Welcome back, %s.', 'admin', '2012-12-11 08:51:58'),
(4, 'Logged in as', 'admin', '2012-12-11 08:51:58'),
(5, 'Log out', 'admin', '2012-12-11 08:51:58'),
(6, 'Select a project to work on:', 'admin', '2012-12-11 08:51:58'),
(7, 'System administration tasks:', 'admin', '2012-12-11 08:51:58'),
(8, 'Create a project', 'admin', '2012-12-11 08:51:58'),
(9, 'Delete a project', 'admin', '2012-12-11 08:51:58'),
(10, 'Import Linnaeus 2 data', 'admin', '2012-12-11 08:51:58'),
(11, 'Collaborator overview', 'admin', '2012-12-11 08:51:58'),
(12, 'Rights matrix', 'admin', '2012-12-11 08:51:58'),
(13, 'Interface', 'admin', '2012-12-11 08:51:58'),
(14, 'Logout', 'admin', '2012-12-11 08:52:14'),
(15, 'Login', 'admin', '2012-12-11 08:52:14'),
(16, 'Log in to administer your Linnaeus project', 'admin', '2012-12-11 08:52:15'),
(17, 'Your username:', 'admin', '2012-12-11 08:52:15'),
(18, 'our password:', 'admin', '2012-12-11 08:52:15'),
(19, 'Remember me', 'admin', '2012-12-11 08:52:15'),
(20, 'Unable to log in? What is the problem you are experiencing?', 'admin', '2012-12-11 08:52:15'),
(21, 'I forgot my password and/or username: %sreset my password%s.', 'admin', '2012-12-11 08:52:15'),
(22, 'My password doesn''t work or my account may have been compromised: please %scontact the helpdesk%s.', 'admin', '2012-12-11 08:52:15'),
(23, 'Back to Linnaeus NG root', 'admin', '2012-12-11 08:52:15'),
(24, 'Introduction', 'app', '2012-12-11 08:52:17'),
(25, 'Glossary', 'app', '2012-12-11 08:52:17'),
(26, 'Literature', 'app', '2012-12-11 08:52:17'),
(27, 'Species', 'app', '2012-12-11 08:52:17'),
(28, 'Higher taxa', 'app', '2012-12-11 08:52:17'),
(29, 'Dichotomous key', 'app', '2012-12-11 08:52:17'),
(30, 'Matrix key', 'app', '2012-12-11 08:52:17'),
(31, 'Distribution', 'app', '2012-12-11 08:52:17'),
(32, 'Additional texts', 'app', '2012-12-11 08:52:17'),
(33, 'Index', 'app', '2012-12-11 08:52:17'),
(34, 'Search', 'app', '2012-12-11 08:52:17'),
(35, 'projects', 'app', '2012-12-11 08:52:17'),
(36, 'login', 'app', '2012-12-11 08:52:17'),
(37, 'help', 'app', '2012-12-11 08:52:17'),
(38, 'not yet available', 'app', '2012-12-11 08:52:17'),
(39, 'contact', 'app', '2012-12-11 08:52:17'),
(41, 'Welcome', 'app', '2012-12-11 08:52:18'),
(42, 'Contributors', 'app', '2012-12-11 08:52:18'),
(43, 'About ETI', 'app', '2012-12-11 08:52:18'),
(44, 'Search...', 'app', '2012-12-11 08:52:18'),
(45, 'Loading application', 'app', '2012-12-11 08:52:18'),
(46, 'Contents', 'app', '2012-12-11 08:52:18'),
(47, 'Back', 'app', '2012-12-11 08:52:18'),
(48, 'Back to ', 'app', '2012-12-11 08:52:24'),
(49, 'Previous', 'app', '2012-12-11 08:52:38'),
(50, 'Next', 'app', '2012-12-11 08:52:38'),
(52, 'Index: Species and lower taxa', 'app', '2012-12-11 08:52:44'),
(53, 'Species and lower taxa', 'app', '2012-12-11 08:52:44'),
(54, 'Common names', 'app', '2012-12-11 08:52:44'),
(55, 'Index: Higher taxa', 'app', '2012-12-11 08:52:47'),
(56, 'Index: Common names', 'app', '2012-12-11 08:52:48'),
(57, 'Language:', 'app', '2012-12-11 08:52:48'),
(58, 'Show all', 'app', '2012-12-11 08:52:48'),
(59, 'Glossary: "%s"', 'app', '2012-12-11 08:52:52'),
(60, 'Synonym', 'app', '2012-12-11 08:52:52'),
(61, 'for', 'app', '2012-12-11 08:52:52'),
(62, 'Literature: "%s"', 'app', '2012-12-11 08:52:57'),
(63, 'Species module index', 'app', '2012-12-11 08:53:02'),
(64, 'Media', 'app', '2012-12-11 08:53:02'),
(65, 'Classification', 'app', '2012-12-11 08:53:02'),
(66, 'Names', 'app', '2012-12-11 08:53:02'),
(67, 'Species module: "%s" (%s)', 'app', '2012-12-11 08:53:02'),
(68, 'Higher taxa index', 'app', '2012-12-11 08:53:16'),
(69, 'Higher taxa: "%s" (%s)', 'app', '2012-12-11 08:53:16'),
(70, 'Dichotomous key: step %s: "%s"', 'app', '2012-12-11 08:53:24'),
(71, 'Step', 'app', '2012-12-11 08:53:24'),
(72, 'Remaining', 'app', '2012-12-11 08:53:24'),
(73, 'Excluded', 'app', '2012-12-11 08:53:24'),
(74, '%s possible %s remaining:', 'app', '2012-12-11 08:53:24'),
(75, '%s %s excluded:', 'app', '2012-12-11 08:53:24'),
(76, 'No choices made yet', 'app', '2012-12-11 08:53:24'),
(77, 'First', 'app', '2012-12-11 08:53:24'),
(78, 'Decision path', 'app', '2012-12-11 08:53:24'),
(79, 'Return to first step', 'app', '2012-12-11 08:53:34'),
(80, 'Return to step', 'app', '2012-12-11 08:53:34'),
(81, 'Matrix "%s": identify', 'app', '2012-12-11 08:53:39'),
(82, 'Identify', 'app', '2012-12-11 08:53:39'),
(83, 'Examine', 'app', '2012-12-11 08:53:39'),
(84, 'Compare', 'app', '2012-12-11 08:53:39'),
(85, 'Matrix:', 'app', '2012-12-11 08:53:39'),
(86, 'Characters', 'app', '2012-12-11 08:53:39'),
(87, 'Sort', 'app', '2012-12-11 08:53:39'),
(88, 'States', 'app', '2012-12-11 08:53:39'),
(89, 'Add', 'app', '2012-12-11 08:53:39'),
(90, 'Delete', 'app', '2012-12-11 08:53:39'),
(91, 'Clear all', 'app', '2012-12-11 08:53:39'),
(92, 'Search &gt;&gt;', 'app', '2012-12-11 08:53:39'),
(93, 'Selected combination of characters', 'app', '2012-12-11 08:53:39'),
(94, 'Treat unknowns as matches', 'app', '2012-12-11 08:53:39'),
(95, 'Result of this combination of characters', 'app', '2012-12-11 08:53:39'),
(96, 'Matrix "%s": examine', 'app', '2012-12-11 08:53:41'),
(97, 'Select a taxon', 'app', '2012-12-11 08:53:41'),
(98, 'Select a taxon from the list to view characters and character states of this taxon.', 'app', '2012-12-11 08:53:41'),
(99, 'These are used for the identification process under Identify.', 'app', '2012-12-11 08:53:41'),
(100, 'Type', 'app', '2012-12-11 08:53:41'),
(101, 'Character', 'app', '2012-12-11 08:53:41'),
(102, 'State', 'app', '2012-12-11 08:53:41'),
(103, 'Matrix "%s": compare', 'app', '2012-12-11 08:53:42'),
(104, 'Select two taxa from the lists and click Compare to compare the characters and character states for both taxa. The results show the differences and similarities for both taxa.', 'app', '2012-12-11 08:53:42'),
(105, 'Unique character states for %s:', 'app', '2012-12-11 08:53:42'),
(106, 'Shared character states:', 'app', '2012-12-11 08:53:42'),
(107, 'Unique states in', 'app', '2012-12-11 08:53:42'),
(108, 'States present in both:', 'app', '2012-12-11 08:53:42'),
(109, 'States present in neither:', 'app', '2012-12-11 08:53:42'),
(110, 'Number of available states:', 'app', '2012-12-11 08:53:42'),
(111, 'Taxonomic distance:', 'app', '2012-12-11 08:53:42'),
(112, 'Project overview', 'admin', '2012-12-11 08:56:42'),
(113, 'Content', 'admin', '2012-12-11 08:56:42'),
(114, 'Welcome', 'admin', '2012-12-11 08:56:42'),
(115, 'Contributors', 'admin', '2012-12-11 08:56:42'),
(116, 'Type to find:', 'admin', '2012-12-11 08:56:42'),
(117, 'Management tasks:', 'admin', '2012-12-11 08:56:42'),
(118, 'Hotwords', 'admin', '2012-12-11 08:56:42'),
(119, 'User administration', 'admin', '2012-12-11 08:56:42'),
(120, 'Project administration', 'admin', '2012-12-11 08:56:42'),
(121, 'Switch projects', 'admin', '2012-12-11 08:56:42'),
(122, 'Editing matrix "%s"', 'admin', '2012-12-11 08:56:43'),
(123, 'preview', 'admin', '2012-12-11 08:56:44'),
(124, 'select another matrix', 'admin', '2012-12-11 08:56:44'),
(125, 'characters', 'admin', '2012-12-11 08:56:44'),
(126, 'sort characters', 'admin', '2012-12-11 08:56:44'),
(127, 'taxa', 'admin', '2012-12-11 08:56:44'),
(128, 'display current links per taxon', 'admin', '2012-12-11 08:56:44'),
(129, '& other matrices', 'admin', '2012-12-11 08:56:44'),
(130, 'add new', 'admin', '2012-12-11 08:56:44'),
(131, 'edit/delete selected', 'admin', '2012-12-11 08:56:44'),
(132, 'add new taxon', 'admin', '2012-12-11 08:56:44'),
(133, 'remove selected taxon', 'admin', '2012-12-11 08:56:44'),
(134, 'states', 'admin', '2012-12-11 08:56:44'),
(135, 'sort states', 'admin', '2012-12-11 08:56:44'),
(136, 'links', 'admin', '2012-12-11 08:56:44'),
(137, 'delete selected', 'admin', '2012-12-11 08:56:44'),
(138, 'Matrices', 'admin', '2012-12-11 08:56:46'),
(139, 'Below is a list of matrices that are currently defined. In order to edit a matrix'' name, click "edit name". In order to edit the actual matrix, click "edit matrix".', 'admin', '2012-12-11 08:56:46'),
(140, 'edit matrix', 'admin', '2012-12-11 08:56:46'),
(141, 'edit name', 'admin', '2012-12-11 08:56:46'),
(142, 'delete', 'admin', '2012-12-11 08:56:46'),
(143, 'create a new matrix', 'admin', '2012-12-11 08:56:46'),
(144, 'New matrix', 'admin', '2012-12-11 08:56:47'),
(145, 'Matrix name:', 'admin', '2012-12-11 08:56:47'),
(146, 'save', 'admin', '2012-12-11 08:56:47'),
(147, 'back', 'admin', '2012-12-11 08:56:47'),
(148, 'Switch to another matrix', 'app', '2012-12-11 08:56:57'),
(149, 'Displaying "%s"', 'app', '2012-12-11 09:07:15'),
(150, 'Diversity index', 'app', '2012-12-11 09:07:15'),
(151, 'Go to this taxon', 'app', '2012-12-11 09:07:15'),
(152, 'Select a different map', 'app', '2012-12-11 09:07:15'),
(153, 'Choose a map', 'app', '2012-12-11 09:07:15'),
(154, 'Comparing taxa', 'app', '2012-12-11 09:07:18'),
(155, 'Displays overlap between two taxa.', 'app', '2012-12-11 09:07:18'),
(156, 'Clear map', 'app', '2012-12-11 09:07:19'),
(157, 'Select the area you want to search by clicking the relevant squares.', 'app', '2012-12-11 09:07:19'),
(158, 'When finished, click ''Search''.', 'app', '2012-12-11 09:07:19'),
(159, 'records', 'app', '2012-12-11 09:07:21'),
(160, 'Search results', 'app', '2012-12-11 09:11:43'),
(161, 'Comparing taxa "%s" and "%s"', 'app', '2012-12-11 09:13:23'),
(162, 'Simple dissimilarity coefficient', 'app', '2012-12-11 09:13:34'),
(163, '(current)', 'admin', '2012-12-11 09:15:44'),
(164, 'Index', 'admin', '2012-12-11 10:38:26'),
(165, 'Project settings', 'admin', '2012-12-11 10:38:26'),
(166, 'Project modules', 'admin', '2012-12-11 10:38:26'),
(167, 'Assign collaborators to modules', 'admin', '2012-12-11 10:38:26'),
(168, 'Get info', 'admin', '2012-12-11 10:38:26'),
(169, 'Export', 'admin', '2012-12-11 10:38:26'),
(170, 'Internal project name:', 'admin', '2012-12-11 10:38:28'),
(171, 'Internal project description:', 'admin', '2012-12-11 10:38:28'),
(172, 'Project ID:', 'admin', '2012-12-11 10:38:28'),
(173, 'Project title:', 'admin', '2012-12-11 10:38:28'),
(174, 'Description (for html meta-tag):', 'admin', '2012-12-11 10:38:28'),
(175, 'Keywords (for html meta-tag; separate with spaces):', 'admin', '2012-12-11 10:38:28'),
(176, 'Project languages:', 'admin', '2012-12-11 10:38:28'),
(177, 'add language', 'admin', '2012-12-11 10:38:28'),
(178, 'This project includes hybrid taxa:', 'admin', '2012-12-11 10:38:28'),
(179, 'yes', 'admin', '2012-12-11 10:38:28'),
(180, 'no', 'admin', '2012-12-11 10:38:28'),
(181, 'Publish project:', 'admin', '2012-12-11 10:38:28'),
(182, 'Language', 'admin', '2012-12-11 10:38:28'),
(183, 'Default', 'admin', '2012-12-11 10:38:28'),
(184, 'Translation', 'admin', '2012-12-11 10:38:28'),
(185, 'Status', 'admin', '2012-12-11 10:38:28'),
(186, 'current', 'admin', '2012-12-11 10:38:28'),
(187, 'to be translated', 'admin', '2012-12-11 10:38:29'),
(188, 'translated', 'admin', '2012-12-11 10:38:29'),
(189, 'published', 'admin', '2012-12-11 10:38:29'),
(190, 'unpublish', 'admin', '2012-12-11 10:38:29'),
(191, 'make default', 'admin', '2012-12-11 10:38:31'),
(192, 'unpublished', 'admin', '2012-12-11 10:38:31'),
(193, 'publish', 'admin', '2012-12-11 10:38:32'),
(214, 'Each project can have one dichotomous key. That key consists of a theoretically unlimited number of steps. Each step consists of a number, a title and a description, plus a maximum of four choices. Each choice consists of a title and a text and/or an image. Also, each choice has a target: the connection to the next element within the key. The target can either be another step, or a taxon.', 'admin', '2012-12-11 16:07:23'),
(195, 'Woordenlijst', 'app', '2012-12-11 10:38:42'),
(196, 'Soort', 'app', '2012-12-11 10:38:42'),
(198, '(common name of %s)', 'app', '2012-12-11 10:39:07'),
(199, 'Species names', 'app', '2012-12-11 10:39:10'),
(200, 'Species descriptions', 'app', '2012-12-11 10:39:10'),
(201, 'Species synonyms', 'app', '2012-12-11 10:39:10'),
(202, 'Species common names', 'app', '2012-12-11 10:39:10'),
(203, 'Species media', 'app', '2012-12-11 10:39:10'),
(204, 'Select modules to export', 'admin', '2012-12-11 10:39:29'),
(205, 'identifier (and English translation)', 'admin', '2012-12-11 10:39:30'),
(206, 'translation in %s', 'admin', '2012-12-11 10:39:30'),
(207, 'Interface translations', 'admin', '2012-12-11 12:09:47'),
(208, '< previous', 'admin', '2012-12-11 12:09:48'),
(209, 'next >', 'admin', '2012-12-11 12:09:48'),
(210, 'Contents', 'admin', '2012-12-11 12:09:48'),
(211, 'Management', 'admin', '2012-12-11 12:09:48'),
(212, 'Show all texts', 'admin', '2012-12-11 12:10:11'),
(213, 'Show untranslated texts', 'admin', '2012-12-11 12:10:21'),
(215, 'You can edit the key from the startpoint, following its structure as the users will see it. Additionally, you can create sections of your key that are not yet connected to the main key. In that way, several people can work on different parts of the key at the same time. Once finished, a section can be hooked up to the main key by simply choosing the sections starting step as the target of a choice already part of the main key.', 'admin', '2012-12-11 16:07:23'),
(216, 'While navigating through your key or key sections, a keypath is maintained at the top of the screen, just beneath the navigational breadcrumb trail. You can navigate within your key by clicking elements in the keypath. As the keypath can become quite large, only the last few elements are show. To see the complete keypath, click the %s symbol at its very beginning.', 'admin', '2012-12-11 16:07:23'),
(217, 'Edit key (from startpoint)', 'admin', '2012-12-11 16:07:23'),
(218, 'Edit key sections', 'admin', '2012-12-11 16:07:23'),
(219, 'Key map', 'admin', '2012-12-11 16:07:23'),
(220, 'Compute taxon division', 'admin', '2012-12-11 16:07:23'),
(221, 'Renumber steps', 'admin', '2012-12-11 16:07:23'),
(222, 'Taxa not part of the key', 'admin', '2012-12-11 16:07:23'),
(223, 'Key validation', 'admin', '2012-12-11 16:07:23'),
(224, 'Define ranks that can appear in key', 'admin', '2012-12-11 16:07:23'),
(225, 'Set key type', 'admin', '2012-12-11 16:07:23'),
(226, 'Store key tree (for runtime performance purposes)', 'admin', '2012-12-11 16:07:23'),
(227, 'Show key step %s', 'admin', '2012-12-12 08:31:52'),
(228, 'undefined', 'admin', '2012-12-12 08:31:52'),
(229, 'show entire path', 'admin', '2012-12-12 08:31:53'),
(230, 'Keypath', 'admin', '2012-12-12 08:31:53'),
(231, 'Full keypath', 'admin', '2012-12-12 08:31:53'),
(232, 'close', 'admin', '2012-12-12 08:31:53'),
(233, 'Step', 'admin', '2012-12-12 08:31:53'),
(234, 'edit', 'admin', '2012-12-12 08:31:53'),
(235, 'Choices', 'admin', '2012-12-12 08:31:53'),
(236, 'choice title', 'admin', '2012-12-12 08:31:53'),
(237, 'choice leads to', 'admin', '2012-12-12 08:31:53'),
(238, 'change order', 'admin', '2012-12-12 08:31:53'),
(675, 'Below are all taxa in your project that are part of the higher taxa. All lower taxa can be found in the %sspecies module%s.', 'admin', '2013-01-03 14:19:33'),
(240, 'add new choice', 'admin', '2012-12-12 08:31:53'),
(241, 'Remaining taxa', 'admin', '2012-12-12 08:31:53'),
(242, 'Excluded taxa', 'admin', '2012-12-12 08:31:53'),
(243, 'Edit choice "%s" for step %s', 'admin', '2012-12-12 08:31:55'),
(244, 'Editing choice', 'admin', '2012-12-12 08:31:56'),
(245, 'Text:', 'admin', '2012-12-12 08:31:56'),
(246, 'Image:', 'admin', '2012-12-12 08:31:56'),
(247, 'Target:', 'admin', '2012-12-12 08:31:56'),
(248, 'new step', 'admin', '2012-12-12 08:31:56'),
(249, '(none)', 'admin', '2012-12-12 08:31:56'),
(250, 'or', 'admin', '2012-12-12 08:31:56'),
(251, 'taxon', 'admin', '2012-12-12 08:31:56'),
(252, 'undo last save', 'admin', '2012-12-12 08:31:56'),
(253, 'Enter the title, text, an optional image and the target of this choice. Title and text are saved automatically after you have entered the text in the appropriate input.', 'admin', '2012-12-12 08:31:56'),
(254, 'To change the step-number from the automatically generated one, enter a new number and click ''save''. Please note that the numbers have to be unique in your key.', 'admin', '2012-12-12 08:31:56'),
(255, 'the language', 'admin', '2012-12-12 08:32:23'),
(256, 'Are you sure you want to delete %s "%s"?', 'admin', '2012-12-12 08:32:23'),
(257, 'Deletion will be irreversible.', 'admin', '2012-12-12 08:32:23'),
(258, 'Final confirmation:', 'admin', '2012-12-12 08:32:24'),
(259, 'saved', 'admin', '2012-12-12 08:36:27'),
(260, 'Image saved.', 'admin', '2012-12-12 08:46:42'),
(261, 'delete image', 'admin', '2012-12-12 08:46:46'),
(262, 'show', 'admin', '2012-12-12 08:56:16'),
(263, 'image', 'admin', '2012-12-12 09:03:55'),
(264, 'move', 'admin', '2012-12-12 09:04:40'),
(265, 'keystep', 'admin', '2012-12-12 09:11:48'),
(266, 'Edit step %s', 'admin', '2012-12-12 09:15:06'),
(267, 'Editing keystep', 'admin', '2012-12-12 09:15:07'),
(268, 'Number:', 'admin', '2012-12-12 09:15:07'),
(269, 'Title:', 'admin', '2012-12-12 09:15:07'),
(270, 'Enter the title and text of this step in your key in the various languages within your project. Title and text are saved automatically after you have entered the text in the appropriate input.', 'admin', '2012-12-12 09:15:07'),
(271, 'Are you sure you want to delete this image?', 'admin', '2012-12-12 09:20:28'),
(272, 'Beware: you are changing the target of this choice.\nThis can radically alter the workings of your key.\nDo you wish to continue?', 'admin', '2012-12-12 09:27:16'),
(273, '(new step)', 'admin', '2012-12-12 09:27:20'),
(274, '(none defined)', 'admin', '2012-12-12 09:27:22'),
(275, 'Step number is required. The saved number for this step is %s. The lowest unused number is %s.', 'admin', '2012-12-12 09:28:18'),
(276, 'Introductie', 'app', '2012-12-12 16:05:55'),
(277, 'Hogere taxa', 'app', '2012-12-12 16:05:55'),
(278, 'Taxon list', 'admin', '2012-12-14 09:29:31'),
(279, 'Editing "%s"', 'admin', '2012-12-14 09:29:31'),
(280, 'save and preview', 'admin', '2012-12-14 09:29:32'),
(281, 'undo (auto)save', 'admin', '2012-12-14 09:29:32'),
(282, 'delete taxon', 'admin', '2012-12-14 09:29:32'),
(283, 'name and parent', 'admin', '2012-12-14 09:29:32'),
(284, 'media', 'admin', '2012-12-14 09:29:32'),
(285, 'literature', 'admin', '2012-12-14 09:29:32'),
(286, 'synonyms', 'admin', '2012-12-14 09:29:32'),
(287, 'common names', 'admin', '2012-12-14 09:29:32'),
(288, '(This page has not been published in this language. Click %shere%s to publish.)', 'admin', '2012-12-14 09:29:32'),
(289, 'Below are all taxa in your project that are part of the species module. All higher taxa can be found in the %shigher taxa module%s.', 'admin', '2012-12-14 09:29:59'),
(290, 'To edit a name, rank or parent, click the taxon''s name. To edit a taxon''s pages, click the percentage-indicator for that taxon in the ''content'' column. To edit media files, synoyms or common names, click the cell in the corresponding column.', 'admin', '2012-12-14 09:29:59'),
(291, 'You can change the order of presentation of taxa on the same level - such as two genera - by moving taxa up- or downward by clicking the arrows.', 'admin', '2012-12-14 09:29:59'),
(292, 'Rank', 'admin', '2012-12-14 09:29:59'),
(293, 'images, videos, soundfiles', 'admin', '2012-12-14 09:29:59'),
(294, 'Is being edited by:', 'admin', '2012-12-14 09:29:59'),
(295, 'media files', 'admin', '2012-12-14 09:29:59'),
(296, 'files', 'admin', '2012-12-14 09:29:59'),
(297, 'names', 'admin', '2012-12-14 09:29:59'),
(298, 'move branch downward in the tree', 'admin', '2012-12-14 09:29:59'),
(299, 'Add a new taxon', 'admin', '2012-12-14 09:29:59'),
(300, 'Species module overview', 'admin', '2012-12-14 09:30:02'),
(301, 'Editing taxa:', 'admin', '2012-12-14 09:30:02'),
(302, 'Import taxon tree from file', 'admin', '2012-12-14 09:30:02'),
(303, 'Import taxon tree from Catalogue Of Life (experimental)', 'admin', '2012-12-14 09:30:02'),
(304, 'Orphans (taxa outside of the main taxon tree)', 'admin', '2012-12-14 09:30:02'),
(305, 'Define taxonomic ranks', 'admin', '2012-12-14 09:30:02'),
(306, 'Label taxonomic ranks', 'admin', '2012-12-14 09:30:02'),
(307, 'Define categories', 'admin', '2012-12-14 09:30:02'),
(308, 'Define sections', 'admin', '2012-12-14 09:30:02'),
(309, 'Assign taxa to collaborators', 'admin', '2012-12-14 09:30:02'),
(310, 'New taxon', 'admin', '2012-12-14 09:30:04'),
(311, 'No parent', 'admin', '2012-12-14 09:41:35'),
(312, 'Parent taxon: ', 'admin', '2012-12-14 09:42:14'),
(313, 'Rank:', 'admin', '2012-12-14 09:42:14'),
(314, 'Taxon name:', 'admin', '2012-12-14 09:42:14'),
(315, 'Author:', 'admin', '2012-12-14 09:42:14'),
(316, 'save and create another', 'admin', '2012-12-14 09:42:14'),
(317, 'save and go to main taxon page', 'admin', '2012-12-14 09:42:14'),
(318, 'That taxon cannot have child taxa.', 'admin', '2012-12-14 09:43:35'),
(319, 'Taxonomic ranks', 'admin', '2012-12-14 09:46:42'),
(320, 'Click the arrow next to a rank to add that rank to the selection used in this project. Currently selected ranks are shown on the right. To remove a rank from the selection, double click it in the list on the right. The uppermost rank, %s, is mandatory and cannot be deleted.', 'admin', '2012-12-14 09:46:42'),
(321, 'Select all the ranks used in Catalogue Of Life, marked in blue in the list below', 'admin', '2012-12-14 09:46:42'),
(322, 'After you have made the appropriate selection, click the save-button. \r\nOnce you have saved the selection, you can ', 'admin', '2012-12-14 09:46:42'),
(323, 'change the ranks'' names and provide translations', 'admin', '2012-12-14 09:46:42'),
(324, 'In addition, you can specify where the distinction between the modules "higher taxa" and "species" will be. "Higher taxa" are described concisely, whereas the "species" module allows for a comprehensive description for each taxon, including different categories, images, videos and sounds. Despite its name, the "species module" does not restrict comprehensive descriptions to the rank of species; rather, you yourself can specify what ranks are described in such a way. The red line in the list of selected ranks below symbolises the distinction. All ranks above the line fall under "higher taxa", those below it under the "species module". You can move the line by clicking the &uarr; and &darr; arrows. The setting is saved when you click', 'admin', '2012-12-14 09:46:42'),
(325, 'save selected ranks', 'admin', '2012-12-14 09:46:42'),
(326, 'Be advised that this "border" is different from the one that defines taxa of what ranks can be the end-point of your keys. That distinction is defined in the "dichotomous key" module. However, that distinction must be on the same level as the one you define here, or below it. It can never be higer up in the rank hierarchy.', 'admin', '2012-12-14 09:46:42'),
(327, 'Please be advised:', 'admin', '2012-12-14 09:46:42'),
(328, 'deleting previously defined ranks to which taxa already have been assigned will leave those taxa without rank.', 'admin', '2012-12-14 09:46:42'),
(329, 'Ranks:', 'admin', '2012-12-14 09:46:42'),
(330, 'Selected ranks', 'admin', '2012-12-14 09:46:42'),
(331, '(double click to delete)', 'admin', '2012-12-14 09:46:42'),
(332, 'Ranks saved.', 'admin', '2012-12-14 09:46:53'),
(333, 'name', 'admin', '2012-12-14 10:44:23'),
(334, 'main page', 'admin', '2012-12-14 10:44:25'),
(335, 'Orphaned taxa', 'admin', '2012-12-14 12:14:46'),
(336, 'There are currently no orphaned taxa in your database.', 'admin', '2012-12-14 12:14:46'),
(337, '"%s" saved.', 'admin', '2012-12-14 12:18:34'),
(338, '(This page has been published in this language. Click %shere%s to unpublish.)', 'admin', '2012-12-14 12:20:01'),
(339, 'System administration', 'admin', '2012-12-14 12:37:53'),
(340, 'Select the project you wish to delete.', 'admin', '2012-12-14 12:37:53'),
(341, 'select', 'admin', '2012-12-14 12:37:53'),
(342, 'Linnaeus 2 import', 'admin', '2012-12-14 12:38:11'),
(343, 'Choose file', 'admin', '2012-12-14 12:38:11'),
(344, 'Creating project', 'admin', '2012-12-14 12:40:49'),
(345, 'Could not create %s', 'admin', '2012-12-14 12:40:50'),
(652, 'Collaborator data', 'admin', '2013-01-03 12:43:35'),
(653, 'Username:', 'admin', '2013-01-03 12:43:35'),
(654, 'Password:', 'admin', '2013-01-03 12:43:35'),
(348, 'Import', 'admin', '2012-12-14 12:41:14'),
(658, 'E-mail address:', 'admin', '2013-01-03 12:43:35'),
(659, 'Timezone:', 'admin', '2013-01-03 12:43:35'),
(660, 'Send e-mail notifications:', 'admin', '2013-01-03 12:43:35'),
(657, 'Last name:', 'admin', '2013-01-03 12:43:35'),
(655, 'Password (repeat):', 'admin', '2013-01-03 12:43:35'),
(656, 'First name:', 'admin', '2013-01-03 12:43:35'),
(643, 'Literature and glossary for "%s"', 'admin', '2013-01-02 10:33:23'),
(644, 'Additional content for "%s"', 'admin', '2013-01-02 10:35:13'),
(645, 'Keys for "Nieuwe Flora van Nederland"', 'admin', '2013-01-02 10:39:35'),
(646, 'new taxon', 'admin', '2013-01-03 10:45:19'),
(647, 'New higher taxon', 'admin', '2013-01-03 10:51:21'),
(648, 'All users', 'admin', '2013-01-03 12:40:20'),
(649, 'view', 'admin', '2013-01-03 12:40:20'),
(650, 'remove', 'admin', '2013-01-03 12:40:20'),
(651, 'Create new collaborator', 'admin', '2013-01-03 12:43:35'),
(632, 'No variations have been defined for this taxon.', 'admin', '2012-12-27 09:01:04'),
(633, 'author:', 'admin', '2012-12-27 09:01:40'),
(634, 'Related taxa and variations for "%s"', 'admin', '2012-12-27 10:45:13'),
(635, 'NBC extras', 'admin', '2012-12-27 12:49:49'),
(636, 'Additional NBC data for "%s"', 'admin', '2012-12-27 13:06:21'),
(637, 'Delete', 'admin', '2012-12-27 14:23:45'),
(638, 'Matrixsleutel', 'app', '2012-12-27 14:23:58'),
(639, 'Character', 'admin', '2012-12-27 14:34:39'),
(640, 'Species and ranks for "%s"', 'admin', '2013-01-02 10:29:19'),
(641, 'Save', 'admin', '2013-01-02 10:29:31'),
(642, 'Additional species data for "%s"', 'admin', '2013-01-02 10:32:07'),
(625, 'Map data for "%s"', 'admin', '2012-12-21 12:22:03'),
(626, 'DELETION WILL BE IRREVERSIBLE.', 'admin', '2012-12-21 14:21:37'),
(627, 'the variation', 'admin', '2012-12-21 14:23:32'),
(628, 'Are you sure you want to delete the variation "%s"?', 'admin', '2012-12-21 14:33:00'),
(629, 'related', 'admin', '2012-12-21 15:25:30'),
(630, 'Unknown or no project ID.', 'app', '2012-12-21 15:28:50'),
(631, 'Back to Linnaeus NG root', 'app', '2012-12-21 15:28:50'),
(375, 'file', 'admin', '2012-12-14 13:20:47'),
(376, 'Warning: "%s" does not exist.', 'admin', '2012-12-14 14:43:46'),
(377, 'Taxon name already in database.', 'admin', '2012-12-14 14:47:30'),
(378, 'Import data', 'admin', '2012-12-17 08:28:27'),
(379, 'Data import options', 'admin', '2012-12-17 08:28:31'),
(380, 'Import NBC Dierendeterminatie', 'admin', '2012-12-17 08:29:26'),
(381, 'NBC Dierendeterminatie Import', 'admin', '2012-12-17 08:43:54'),
(382, 'Parsed data example', 'admin', '2012-12-17 12:41:37'),
(672, 'Common names', 'admin', '2013-01-03 13:33:21'),
(673, 'Move', 'admin', '2013-01-03 13:33:21'),
(385, 'Created project', 'admin', '2012-12-17 13:05:31'),
(386, 'Select the standard modules you wish to use in your project:', 'admin', '2012-12-17 13:51:17'),
(387, 'Besides these standard modules, you can add up to 5 extra content modules to your project:', 'admin', '2012-12-17 13:51:17'),
(388, 'Enter new module''s name:', 'admin', '2012-12-17 13:51:17'),
(389, 'add module', 'admin', '2012-12-17 13:51:17'),
(390, 'Module', 'admin', '2012-12-17 13:51:17'),
(391, 'Actions', 'admin', '2012-12-17 13:51:17'),
(392, 'part of the project', 'admin', '2012-12-17 13:51:17'),
(393, 'not part of the project', 'admin', '2012-12-17 13:51:18'),
(394, 'add', 'admin', '2012-12-17 13:51:18'),
(395, 'Matrix', 'admin', '2012-12-17 13:51:36'),
(396, 'New character', 'admin', '2012-12-17 13:51:36'),
(397, 'New charcteristic for matrix "%s"', 'admin', '2012-12-17 13:51:36'),
(398, 'Add the name and type of the charcteristic you want to add. The following types of charcteristics are available:', 'admin', '2012-12-17 13:51:36'),
(399, 'text', 'admin', '2012-12-17 13:51:36'),
(400, 'a textual description.', 'admin', '2012-12-17 13:51:36'),
(401, 'an image, video or soundfile.', 'admin', '2012-12-17 13:51:36'),
(402, 'range', 'admin', '2012-12-17 13:51:36'),
(403, 'a value range, defined by a lowest and a highest value.', 'admin', '2012-12-17 13:51:36'),
(404, 'distribution', 'admin', '2012-12-17 13:51:36'),
(405, 'a value distribution, defined by a mean and values for one and two standard deviations.', 'admin', '2012-12-17 13:51:36'),
(406, 'Characteristic name:', 'admin', '2012-12-17 13:51:36'),
(407, 'Character type:', 'admin', '2012-12-17 13:51:36'),
(671, 'Synonyms', 'admin', '2013-01-03 13:33:21'),
(670, 'Literature', 'admin', '2013-01-03 13:33:21'),
(669, 'Media', 'admin', '2013-01-03 13:33:21'),
(668, 'Taxon', 'admin', '2013-01-03 13:33:21'),
(412, 'Description', 'admin', '2012-12-17 16:10:50'),
(413, 'Detailed Description', 'admin', '2012-12-17 16:10:50'),
(414, 'Ecology', 'admin', '2012-12-17 16:10:50'),
(415, 'Conservation', 'admin', '2012-12-17 16:10:50'),
(416, 'Relevance', 'admin', '2012-12-17 16:10:50'),
(417, 'Reproductive', 'admin', '2012-12-17 16:10:50'),
(418, 'Each taxon page consists of one or more categories, with a maximum of %s. The first category, ''%s'', is mandatory.', 'admin', '2012-12-17 16:11:00'),
(419, 'Below, you can specify the correct label of each category in the language or languages defined in your project. On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.', 'admin', '2012-12-17 16:11:00'),
(420, 'Text you enter is automatically saved when you leave the input field.', 'admin', '2012-12-17 16:11:00'),
(421, 'Category', 'admin', '2012-12-17 16:11:00'),
(422, 'Add a new category:', 'admin', '2012-12-17 16:11:00'),
(674, 'The name "%s" already exists.', 'admin', '2013-01-03 13:37:56'),
(667, 'No common names have been defined for this taxon.', 'admin', '2013-01-03 13:33:09'),
(666, 'Additional data for "Chironomidae exuviae"', 'admin', '2013-01-03 13:32:28'),
(665, 'Keys for "Chironomidae exuviae"', 'admin', '2013-01-03 13:31:29'),
(664, 'Username already exists.', 'admin', '2013-01-03 12:43:37'),
(663, 'Select the modules that will be assigned to this collaborator', 'admin', '2013-01-03 12:43:35'),
(430, 'Saving matrix data', 'admin', '2012-12-18 13:23:24'),
(662, 'Active:', 'admin', '2013-01-03 12:43:35'),
(432, 'Storing ranks, species and variations', 'admin', '2012-12-18 13:40:41'),
(433, 'You have to define at least one language in your project before you can add any taxa.', 'admin', '2012-12-18 13:40:56'),
(434, 'Define languages now.', 'admin', '2012-12-18 13:40:56'),
(661, 'Role in current project:', 'admin', '2013-01-03 12:43:35'),
(436, 'Common names for "%s"', 'admin', '2012-12-18 13:45:38'),
(437, 'common name', 'admin', '2012-12-18 13:45:38'),
(438, 'transliteration', 'admin', '2012-12-18 13:45:38'),
(439, 'move up', 'admin', '2012-12-18 13:45:38'),
(440, 'down', 'admin', '2012-12-18 13:45:38'),
(441, 'Add a new common name:', 'admin', '2012-12-18 13:45:38'),
(442, 'common name:', 'admin', '2012-12-18 13:45:38'),
(443, 'transliteration:', 'admin', '2012-12-18 13:45:38'),
(444, 'language:', 'admin', '2012-12-18 13:45:38'),
(445, 'After you have added a new common name, you will be allowed to provide the name of its language in the various interface languages that your project uses.', 'admin', '2012-12-18 13:45:38'),
(446, 'Project collaborator overview', 'admin', '2012-12-18 13:46:38'),
(447, 'days', 'admin', '2012-12-18 13:46:38'),
(448, 'first name', 'admin', '2012-12-18 13:46:38'),
(449, 'last name', 'admin', '2012-12-18 13:46:38'),
(450, 'username', 'admin', '2012-12-18 13:46:38'),
(451, 'e-mail', 'admin', '2012-12-18 13:46:38'),
(452, 'role', 'admin', '2012-12-18 13:46:38'),
(453, 'last access', 'admin', '2012-12-18 13:46:38'),
(454, 'Project collaborators', 'admin', '2012-12-18 13:46:38'),
(455, 'All collaborators', 'admin', '2012-12-18 13:46:38'),
(456, 'Create collaborator', 'admin', '2012-12-18 13:46:38'),
(457, 'add new for "%s"', 'admin', '2012-12-18 14:17:14'),
(458, 'Sort states of characteristic "%s".', 'admin', '2012-12-18 14:22:49'),
(459, 'move down', 'admin', '2012-12-18 14:22:49'),
(460, 'Editing character "%s"', 'admin', '2012-12-18 14:24:46'),
(461, 'New state for "%s"', 'admin', '2012-12-18 14:24:52'),
(462, 'Editing a state of the type "%s" for the character "%s" of matrix "%s".', 'admin', '2012-12-18 14:24:52'),
(463, 'Name:', 'admin', '2012-12-18 14:24:52'),
(464, 'Choose a file to upload:', 'admin', '2012-12-18 14:24:52'),
(465, 'Allowed formats:', 'admin', '2012-12-18 14:24:52'),
(466, '%s', 'admin', '2012-12-18 14:24:52'),
(467, 'max.', 'admin', '2012-12-18 14:24:52'),
(468, 'per file', 'admin', '2012-12-18 14:24:52'),
(469, 'save and return to matrix', 'admin', '2012-12-18 14:24:52'),
(470, 'save and add another state for &quot;%s&quot;', 'admin', '2012-12-18 14:24:53'),
(471, 'Editing state for "%s"', 'admin', '2012-12-18 14:26:46'),
(472, 'A media file is required.', 'admin', '2012-12-18 14:26:46'),
(473, 'characteristic', 'admin', '2012-12-18 14:43:02'),
(474, 'State "%s" saved.', 'admin', '2012-12-18 14:43:43'),
(475, 'Current image:', 'admin', '2012-12-18 14:45:49'),
(476, 'Lower limit (inclusive):', 'admin', '2012-12-18 14:53:09'),
(477, 'Upper limit (inclusive):', 'admin', '2012-12-18 14:53:09'),
(478, 'Are you sure?', 'admin', '2012-12-18 14:59:54'),
(479, 'edit character groups', 'admin', '2012-12-18 15:28:36'),
(480, 'Search and replace', 'admin', '2012-12-18 15:31:56'),
(481, 'Find', 'admin', '2012-12-18 15:31:56'),
(482, 'Search for:', 'admin', '2012-12-18 15:31:56'),
(483, 'Enclose multiple words with double quotes (") to search for the literal string.', 'admin', '2012-12-18 15:31:56'),
(484, 'In modules:', 'admin', '2012-12-18 15:31:56'),
(485, 'Species', 'admin', '2012-12-18 15:31:56'),
(486, 'Matrix key', 'admin', '2012-12-18 15:31:56'),
(487, 'Replace', 'admin', '2012-12-18 15:31:56'),
(488, 'Replace with:', 'admin', '2012-12-18 15:31:56'),
(489, 'Do not enclose multiple words with double quotes, unless you want them as part of the actual replacement string.', 'admin', '2012-12-18 15:31:56'),
(490, 'Replace options:', 'admin', '2012-12-18 15:31:56'),
(491, 'Confirm per match', 'admin', '2012-12-18 15:31:56'),
(492, 'Replace all without confirmation', 'admin', '2012-12-18 15:31:56'),
(493, 'search', 'admin', '2012-12-18 15:31:56'),
(494, 'Taxon-state links', 'admin', '2012-12-19 08:38:12'),
(495, 'Viewing taxon-state links in the matrix "%s"', 'admin', '2012-12-19 08:38:13'),
(496, 'view matrix', 'admin', '2012-12-19 08:38:13'),
(497, 'Choose a taxon:', 'admin', '2012-12-19 08:38:13'),
(499, 'State', 'admin', '2012-12-19 08:38:13'),
(500, 'No links found.', 'admin', '2012-12-19 08:38:13'),
(501, 'Adding taxa', 'admin', '2012-12-19 08:38:16'),
(502, 'save and add another taxon', 'admin', '2012-12-19 08:38:16'),
(503, 'Variation:', 'admin', '2012-12-19 09:44:54'),
(504, 'Editing character "%s" for matrix "%s"', 'admin', '2012-12-19 13:25:37'),
(505, 'Taxon added.', 'admin', '2012-12-19 13:51:43'),
(506, 'Taxon to add:', 'admin', '2012-12-19 15:23:13'),
(507, 'Variation to add:', 'admin', '2012-12-19 15:23:13'),
(508, 'variations', 'admin', '2012-12-19 15:58:27'),
(509, 'Synonyms for "%s"', 'admin', '2012-12-19 16:04:00'),
(510, 'No synonyms have been defined for this taxon.', 'admin', '2012-12-19 16:04:01'),
(511, 'Add a new synonym:', 'admin', '2012-12-19 16:04:01'),
(512, 'synonym:', 'admin', '2012-12-19 16:04:01'),
(513, 'Vartiations for "%s"', 'admin', '2012-12-19 16:04:46'),
(514, 'synonym', 'admin', '2012-12-19 16:10:15'),
(515, 'author', 'admin', '2012-12-19 16:10:15'),
(516, 'variation', 'admin', '2012-12-19 16:11:17'),
(517, 'Add a new variation:', 'admin', '2012-12-19 16:12:45'),
(518, 'Variations for "%s"', 'admin', '2012-12-19 16:14:54'),
(519, 'Editing %s "%s"', 'admin', '2012-12-20 08:38:34'),
(520, 'Synonyms for %s "%s"', 'admin', '2012-12-20 08:38:38'),
(521, 'Glossary terms', 'app', '2012-12-20 09:20:43'),
(522, 'Glossary synonyms', 'app', '2012-12-20 09:20:43'),
(523, 'Glossary media', 'app', '2012-12-20 09:20:43'),
(524, 'Literary references', 'app', '2012-12-20 09:20:43'),
(525, 'enter search term', 'app', '2012-12-20 09:22:26'),
(526, 'The module "%s" is not part of your project.', 'admin', '2012-12-20 09:25:28'),
(527, '[syn.]', 'app', '2012-12-20 09:47:32'),
(528, 'You can assign parts of the taxon tree to specific collaborator. If assigned, collaborators can only edit the assigned taxon, and all taxa beneath it in the taxon tree. If a collaborator has no taxa assigned to him, he can edit no taxa.', 'admin', '2012-12-20 09:52:15'),
(529, 'You can assign multiple taxa to the same collaborator. However, if you assign different taxa that appear in the same branch of the taxon tree, the taxa highest up the same branch takes precedent.', 'admin', '2012-12-20 09:52:15'),
(530, 'Assign taxon', 'admin', '2012-12-20 09:52:15'),
(531, 'to user', 'admin', '2012-12-20 09:52:15'),
(532, 'Current assignments:', 'admin', '2012-12-20 09:52:15'),
(533, 'Collaborator', 'admin', '2012-12-20 09:52:15'),
(534, 'Taxonomic ranks: labels', 'admin', '2012-12-20 09:52:27'),
(535, 'Below, you can specify the correct label of each rank in the language or languages defined in your project.', 'admin', '2012-12-20 09:52:27'),
(536, 'On the left hand side, the labels in the default language are displayed; on the right hand side, the labels in the other languages. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.', 'admin', '2012-12-20 09:52:27'),
(538, 'Taxa list', 'app', '2012-12-20 10:49:40'),
(539, 'Only species and below can contain spaces in their names.', 'admin', '2012-12-20 12:22:47'),
(540, 'The name you specified contains invalid characters.', 'admin', '2012-12-20 12:22:47'),
(541, 'The number of spaces in the name does not seem to match the selected rank.', 'admin', '2012-12-20 12:23:58'),
(542, 'The number of spaces in the name does not match the selected rank.', 'admin', '2012-12-20 12:44:59'),
(543, 'A %s should be linked to %s. This relationship is not enforced, so you can link to %s, but this may result in problems with the classification.', 'admin', '2012-12-20 12:44:59'),
(544, '"%s" cannot be selected as a parent for "%s".', 'admin', '2012-12-20 12:44:59'),
(545, 'Markers are inserted automatically.', 'admin', '2012-12-20 12:51:07'),
(546, 'save anyway', 'admin', '2012-12-20 12:55:09'),
(547, 'The selected parent taxon can not have children.', 'admin', '2012-12-20 13:28:28'),
(548, 'No taxon ID specified.', 'app', '2012-12-20 13:46:41'),
(554, 'Index: species', 'admin', '2012-12-20 15:03:35'),
(555, 'Higher Taxa', 'admin', '2012-12-20 15:03:35'),
(556, 'Click to browse:', 'admin', '2012-12-20 15:03:35'),
(621, 'identifier', 'admin', '2012-12-21 09:10:00'),
(622, '(as the original tags are in %s, they do not require translating)', 'admin', '2012-12-21 09:14:35'),
(623, 'As the original tags are in %s, they do not require translating, but if you do specify a translation, it will overrule the original tag.', 'admin', '2012-12-21 09:16:45'),
(624, 'Delete tag and all its translations!', 'admin', '2012-12-21 09:26:32'),
(676, 'Please note that you can only delete taxa that have no children, in order to maintain a correct taxon structure in the species module.', 'admin', '2013-01-03 14:19:33'),
(677, 'Password strength:', 'admin', '2013-01-03 14:38:17'),
(678, '(leave blank to leave unchanged)', 'admin', '2013-01-03 14:38:18'),
(679, 'status', 'admin', '2013-01-03 14:38:26'),
(680, 'Add collaborator', 'admin', '2013-01-03 14:38:46'),
(681, 'Add user', 'admin', '2013-01-03 14:38:46'),
(682, 'to project', 'admin', '2013-01-03 14:38:46'),
(683, 'in the role of', 'admin', '2013-01-03 14:38:46'),
(684, 'cancel', 'admin', '2013-01-03 14:38:46'),
(685, 'Login failed.', 'admin', '2013-01-03 14:42:38'),
(686, 'Taxon is already being edited by another editor.', 'admin', '2013-01-03 14:43:31'),
(687, 'Editing literature "%s (%s)"', 'admin', '2013-01-03 15:23:15'),
(688, 'Create new', 'admin', '2013-01-03 15:23:23'),
(689, 'Number of authors:', 'admin', '2013-01-03 15:23:23'),
(690, 'one', 'admin', '2013-01-03 15:23:23'),
(691, 'two', 'admin', '2013-01-03 15:23:23'),
(692, 'more', 'admin', '2013-01-03 15:23:23'),
(693, 'et al.', 'admin', '2013-01-03 15:23:23'),
(694, 'Year &amp; suffix (optional):', 'admin', '2013-01-03 15:23:23'),
(695, 'Reference:', 'admin', '2013-01-03 15:23:23'),
(696, 'Taxa this reference pertains to:', 'admin', '2013-01-03 15:23:23'),
(697, 'Authors:', 'admin', '2013-01-03 15:23:41'),
(698, 'That name already exists, albeit with a different parent.', 'admin', '2013-01-08 09:52:46'),
(699, 'Rank cannot be hybrid.', 'admin', '2013-01-08 10:22:26'),
(700, 'Hybrid:', 'admin', '2013-01-08 11:58:35'),
(701, 'no hybrid', 'admin', '2013-01-08 12:00:45'),
(702, 'interspecific hybrid', 'admin', '2013-01-08 12:00:45'),
(703, 'intergeneric hybrid', 'admin', '2013-01-08 12:00:45'),
(704, 'Password too short; should be between %s and %s characters.', 'admin', '2013-01-08 15:03:45'),
(705, 'Below are your username and password for access to the Linnaeus NG administration:\nUsername: %s\nPassword: %s\n\nYou can access Linnaeus NG at:\n[[url]]', 'admin', '2013-01-08 15:04:02'),
(706, '<html>Below are your username and password for access to the Linnaeus NG administration:<br />\nUsername: %s<br />\nPassword: %s<br />\n<br />\nYou can access Linnaeus NG at:<br />\n<a href="[[url]]">[[url]]</a>', 'admin', '2013-01-08 15:04:02'),
(707, 'No matrices have been defined.', 'app', '2013-01-09 13:58:44'),
(708, 'Synonyms', 'app', '2013-01-10 08:40:22'),
(709, 'Taxon list', 'app', '2013-01-10 08:47:03'),
(710, 'Back to', 'admin', '2013-01-10 16:19:39'),
(711, 'Linnaeus NG root', 'admin', '2013-01-10 16:19:39'),
(712, 'habitat', 'app', '2013-01-11 09:03:52'),
(713, 'Text is required.', 'admin', '2013-01-11 12:55:43'),
(714, 'Mean:', 'admin', '2013-01-11 12:58:31'),
(715, 'Standard deviation:', 'admin', '2013-01-11 12:58:31'),
(716, 'Using matrix "%s", function "%s"', 'app', '2013-01-11 13:03:18'),
(717, 'switch to ', 'app', '2013-01-11 13:03:18'),
(718, 'sort', 'app', '2013-01-11 13:03:18'),
(719, 'add', 'app', '2013-01-11 13:03:18'),
(720, 'delete', 'app', '2013-01-11 13:03:18'),
(721, 'clear all', 'app', '2013-01-11 13:03:18'),
(722, 'treat unknowns as matches:', 'app', '2013-01-11 13:03:18'),
(723, 'Linnaeus NG root', 'app', '2013-01-11 13:34:37'),
(724, 'Alphabet', 'app', '2013-01-22 08:31:57'),
(725, 'Separation coefficient', 'app', '2013-01-22 08:31:57'),
(726, 'Character type', 'app', '2013-01-22 08:31:57'),
(727, 'Number of states', 'app', '2013-01-22 08:31:57'),
(728, 'Entry order', 'app', '2013-01-22 08:31:57'),
(729, 'Value:', 'app', '2013-01-22 08:31:57'),
(730, 'ok', 'app', '2013-01-22 08:31:57'),
(731, 'cancel', 'app', '2013-01-22 08:31:57'),
(732, 'Number of allowed standard deviations:', 'app', '2013-01-22 08:31:57'),
(733, 'lower: ', 'app', '2013-01-22 08:38:25'),
(734, 'upper: ', 'app', '2013-01-22 08:38:25'),
(735, 'Enter a value', 'app', '2013-01-22 08:38:32'),
(736, 'Enter the required value for "%s":', 'app', '2013-01-22 08:38:32'),
(737, 'mean: ', 'app', '2013-01-22 08:44:00'),
(738, 'sd: ', 'app', '2013-01-22 08:44:00'),
(739, 'Enter the required values for "%s":', 'app', '2013-01-22 08:44:01'),
(740, 'Click %shere%s to specify a value; you can also double-click "%s" to do so.', 'app', '2013-01-22 10:58:16'),
(741, 'Please enter a value', 'app', '2013-01-22 11:20:02'),
(742, 'mean', 'app', '2013-01-22 11:40:26'),
(743, 'sd', 'app', '2013-01-22 11:40:26'),
(744, 'Next to', 'app', '2013-01-23 12:34:11'),
(745, 'It''s a text!', 'app', '2013-01-23 13:37:13'),
(746, 'It''s a plaatje?', 'app', '2013-01-23 13:37:13'),
(747, 'It''s a range...', 'app', '2013-01-23 13:37:13'),
(748, 'It''s a distribution!@#$', 'app', '2013-01-23 13:37:13'),
(749, 'Whatever', 'app', '2013-01-23 13:37:13'),
(750, 'Sort characters', 'admin', '2013-01-24 13:24:31'),
(751, 'Previous to', 'app', '2013-01-24 14:14:22'),
(752, 'synonym', 'app', '2013-01-24 14:14:29'),
(753, 'of', 'app', '2013-01-24 14:14:29'),
(754, 'Keys for "Linnaeus II"', 'admin', '2013-01-24 14:17:42'),
(755, 'Additional data for "Linnaeus II"', 'admin', '2013-01-24 14:17:48'),
(756, 'checklist', 'app', '2013-01-24 14:23:16'),
(757, 'habitat: "%s"', 'app', '2013-01-24 14:47:20'),
(758, 'checklist: "%s"', 'app', '2013-01-24 14:47:22'),
(759, ': "%s"', 'app', '2013-01-24 15:13:35'),
(760, '3: "%s"', 'app', '2013-01-24 15:18:43'),
(761, 'topic', 'app', '2013-01-24 15:20:11'),
(762, '4: "%s"', 'app', '2013-01-24 15:23:22'),
(763, '4:3', 'app', '2013-01-24 15:28:20'),
(764, '3:3', 'app', '2013-01-24 15:28:20'),
(765, '4:4', 'app', '2013-01-24 15:28:23'),
(766, '3:4', 'app', '2013-01-24 15:28:23'),
(767, 'gelijkende soorten', 'app', '2013-01-30 13:19:23'),
(768, 'Media for "%s"', 'admin', '2013-02-01 11:13:16'),
(769, 'upload media', 'admin', '2013-02-01 11:13:16'),
(770, 'Images', 'admin', '2013-02-01 11:13:16'),
(771, 'Overview image', 'admin', '2013-02-01 11:13:16'),
(772, 'Videos', 'admin', '2013-02-01 11:13:16'),
(773, 'Sound', 'admin', '2013-02-01 11:13:16'),
(774, 'New media for "%s"', 'admin', '2013-02-01 11:13:18'),
(775, 'upload', 'admin', '2013-02-01 11:13:18'),
(776, 'See current media for this taxon', 'admin', '2013-02-01 11:13:18'),
(777, 'Allowed MIME-types', 'admin', '2013-02-01 11:13:18'),
(778, 'Files of the following MIME-types are allowed:', 'admin', '2013-02-01 11:13:18'),
(779, 'see below for information on uploading archives', 'admin', '2013-02-01 11:13:18'),
(780, 'Overwriting and identical file names', 'admin', '2013-02-01 11:13:18'),
(781, 'All uploaded files are assigned unique file names, so there is no danger of accidentally overwriting an existing file. The original file names are retained in the project database and shown in the media management screens.', 'admin', '2013-02-01 11:13:18'),
(782, 'Uploading multiple files at once', 'admin', '2013-02-01 11:13:18'),
(783, 'In the current HTML-specification there are no cross-broswer possibilities for the uploading of multiple files at once without resorting to Flash or Java. Despite this limitation, you can upload several images at once by adding them to a ZIP-archive and uploading that file. The application will unpack the ZIP-file and store the separate files contained within. To the files within a ZIP-file the same limitations with regards to format and size apply as to files that are uploaded normally.', 'admin', '2013-02-01 11:13:18'),
(784, 'Group:', 'admin', '2013-02-01 12:25:08'),
(785, 'Project info', 'admin', '2013-02-01 12:27:48'),
(786, '%s taxa, with:', 'admin', '2013-02-01 12:27:48'),
(787, '%s media files', 'admin', '2013-02-01 12:27:48'),
(788, '%s common names', 'admin', '2013-02-01 12:27:48'),
(789, '%s synonyms', 'admin', '2013-02-01 12:27:48'),
(790, '%s pages', 'admin', '2013-02-01 12:27:48'),
(791, '%s glossary entries', 'admin', '2013-02-01 12:27:48'),
(792, '%s literature references', 'admin', '2013-02-01 12:27:48'),
(793, ' additional info modules with a total of %s pages', 'admin', '2013-02-01 12:27:48'),
(794, '%s steps in the dichtomous key', 'admin', '2013-02-01 12:27:48'),
(795, '%s matrix key(s)', 'admin', '2013-02-01 12:27:48'),
(796, '%s map items', 'admin', '2013-02-01 12:27:48'),
(797, '%s variations', 'admin', '2013-02-01 12:30:31'),
(798, '(van %s) objecten in huidige selectie', 'app', '2013-02-01 12:39:46'),
(799, '(deselecteer)', 'app', '2013-02-01 16:17:27'),
(800, 'details', 'app', '2013-02-05 10:39:53'),
(801, 'wis geselecteerde eigenschappen', 'app', '2013-02-05 10:55:34'),
(802, 'wis geselecteerde kenmerken', 'app', '2013-02-06 15:34:35'),
(803, 'terug', 'app', '2013-02-07 11:59:45'),
(804, 'Gelijkende soorten van', 'app', '2013-02-07 12:03:25'),
(805, 'Gelijkende soorten van %s', 'app', '2013-02-07 12:03:49'),
(806, 'Zoekresultaten voor %s', 'app', '2013-02-07 14:32:16'),
(807, 'Gebaseerd op', 'app', '2013-02-08 13:00:57'),
(808, 'meer info', 'app', '2013-02-08 13:00:57'),
(809, 'Editing page', 'admin', '2013-02-08 14:53:24'),
(810, 'Change page order', 'admin', '2013-02-08 14:53:24'),
(811, 'Topic:', 'admin', '2013-02-08 14:53:24'),
(812, 'current image for this page:', 'admin', '2013-02-08 14:53:24'),
(813, '(click to delete image)', 'admin', '2013-02-08 14:53:24'),
(814, 'Editing glossary term "%s"', 'admin', '2013-02-08 15:00:42'),
(815, 'Term:', 'admin', '2013-02-08 15:00:43'),
(816, 'Definition:', 'admin', '2013-02-08 15:00:43'),
(817, 'Synonyms:', 'admin', '2013-02-08 15:00:43'),
(818, '(double-click a synonym to remove it from the list)', 'admin', '2013-02-08 15:00:43'),
(819, 'Edit media files', 'admin', '2013-02-08 15:00:43'),
(820, 'Update hotwords', 'admin', '2013-02-08 15:02:02'),
(821, 'Browse all hotwords', 'admin', '2013-02-08 15:02:02'),
(822, 'Browse hotwords', 'admin', '2013-02-08 15:02:04'),
(823, 'All hotwords', 'admin', '2013-02-08 15:02:04'),
(824, 'Update hotwords table', 'admin', '2013-02-08 15:02:14'),
(825, 'Common Names', 'admin', '2013-02-08 15:02:44'),
(826, 'contains the following taxa', 'app', '2013-02-08 15:05:22'),
(827, 'step', 'app', '2013-02-08 15:10:40'),
(828, 'Browse hotwords in ', 'admin', '2013-02-08 15:14:46'),
(829, 'Sort characters by:', 'app', '2013-02-11 10:32:27'),
(830, 'common name of %s', 'app', '2013-02-12 13:12:37'),
(831, 'Zoek op naam', 'app', '2013-02-12 16:00:39'),
(832, 'zoek', 'app', '2013-02-12 16:00:39'),
(833, 'Zoek op kenmerken', 'app', '2013-02-12 16:00:39'),
(834, 'alle kenmerken tonen', 'app', '2013-02-12 16:05:56'),
(835, 'alle kenmerken verbergen', 'app', '2013-02-12 16:13:53'),
(836, 'onderscheidende kenmerken', 'app', '2013-02-12 16:14:42'),
(837, 'sluiten', 'app', '2013-02-12 16:19:14'),
(838, 'Meer resultaten laden', 'app', '2013-02-13 13:59:44'),
(839, 'meer resultaten laden', 'app', '2013-02-13 13:59:51'),
(840, 'Kies een waarde tussen %s en %s%s.', 'app', '2013-02-13 14:55:30'),
(841, 'male', 'app', '2013-02-14 13:57:42'),
(842, 'female', 'app', '2013-02-14 13:57:42'),
(843, 'distinctieve kenmerken', 'app', '2013-02-15 08:29:14'),
(844, 'the page', 'admin', '2013-02-19 11:48:47'),
(845, 'Keys for "Orchids of New Guinea. Vol. III."', 'admin', '2013-02-19 12:26:08'),
(846, 'Additional data for "Orchids of New Guinea. Vol. III."', 'admin', '2013-02-19 12:26:28'),
(847, 'Keys for "-test project «Narwhal» (2013-02-19T15:17:49+01:00)"', 'admin', '2013-02-19 14:19:36'),
(848, 'Additional data for "-test project «Narwhal» (2013-02-19T15:17:49+01:00)"', 'admin', '2013-02-19 14:19:44'),
(849, 'Keys for "-test project «Shark» (2013-02-19T15:22:33+01:00)"', 'admin', '2013-02-19 14:25:27'),
(850, 'Additional data for "-test project «Shark» (2013-02-19T15:22:33+01:00)"', 'admin', '2013-02-19 14:25:50'),
(851, 'Keys for "Marine Mammals"', 'admin', '2013-02-19 15:47:08');
INSERT INTO `dev_interface_texts` (`id`, `text`, `env`, `created`) VALUES
(852, 'Additional data for "Marine Mammals"', 'admin', '2013-02-19 15:49:15'),
(853, 'Keys for "Orchids of New Guinea. Vol. VI"', 'admin', '2013-02-19 15:52:05'),
(854, 'Additional data for "Orchids of New Guinea. Vol. VI"', 'admin', '2013-02-19 16:00:19'),
(855, 'Delete an orpahned project', 'admin', '2013-02-20 08:21:46'),
(856, 'Delete an orphaned project', 'admin', '2013-02-20 08:22:11'),
(857, 'Delete oprhaned project', 'admin', '2013-02-20 08:27:57'),
(858, 'Delete orphaned project', 'admin', '2013-02-20 08:40:26'),
(859, 'Delete orphaned data', 'admin', '2013-02-20 08:42:23'),
(860, 'Merge other project', 'admin', '2013-02-20 12:07:32'),
(861, 'Merge project', 'admin', '2013-02-20 12:09:10'),
(862, 'Besides these standard modules, you can add up to  extra content modules to your project:', 'admin', '2013-02-20 12:09:10'),
(863, 'Keys for "Orchids of New Guinea Vol II"', 'admin', '2013-02-20 12:20:23'),
(864, 'Additional data for "Orchids of New Guinea Vol II"', 'admin', '2013-02-20 12:20:33'),
(865, 'Keys for "Orchids of New Guinea. Vol. IV"', 'admin', '2013-02-20 12:22:28'),
(866, 'Additional data for "Orchids of New Guinea. Vol. IV"', 'admin', '2013-02-20 12:23:10'),
(867, 'Keys for "Orchids of New Guinea. Vol. V"', 'admin', '2013-02-20 12:24:08'),
(868, 'Additional data for "Orchids of New Guinea. Vol. V"', 'admin', '2013-02-20 12:24:19'),
(869, 'Select the project you wish to merge into the current project, "%s".', 'admin', '2013-02-20 12:26:14'),
(870, 'Back', 'admin', '2013-02-20 12:30:54'),
(871, 'You are about to merge the project "%s" into "%s".', 'admin', '2013-02-20 12:34:37'),
(872, 'Do you wish to continue?', 'admin', '2013-02-20 12:35:07'),
(873, 'merge', 'admin', '2013-02-20 12:38:15'),
(874, 'add an image to this page', 'admin', '2013-02-20 13:06:36'),
(875, 'Delete all hotwords', 'admin', '2013-02-21 08:28:45'),
(876, 'Hotwords in %s module', 'admin', '2013-02-21 08:32:54'),
(877, 'Referenced in the following taxa:', 'app', '2013-02-21 09:22:45'),
(878, 'Additional data for "Nieuwe Flora van Nederland"', 'admin', '2013-02-21 10:05:45'),
(879, 'Dichotome sleutel', 'app', '2013-02-21 10:12:18'),
(880, 'Verspreiding', 'app', '2013-02-21 10:12:18'),
(881, 'New glossary term', 'admin', '2013-02-21 12:34:44'),
(882, 'Browsing glossary', 'admin', '2013-02-21 12:35:32'),
(883, 'topic', 'admin', '2013-02-25 09:13:10'),
(884, 'The image file for the map "%s" is missing.', 'app', '2013-02-25 09:15:59'),
(885, 'Type locality', 'admin', '2013-02-25 10:38:49'),
(886, 'Choose a species', 'admin', '2013-02-25 10:38:49'),
(887, '(no data)', 'admin', '2013-02-25 10:38:51'),
(888, 'edit data', 'admin', '2013-02-25 10:38:51'),
(889, 'Choose a species', 'app', '2013-02-25 10:45:32'),
(890, 'Click a species to examine', 'app', '2013-02-25 10:45:32'),
(891, 'species comparison', 'app', '2013-02-25 10:45:32'),
(892, ' or ', 'app', '2013-02-25 10:45:32'),
(893, 'map search', 'app', '2013-02-25 10:45:32'),
(894, 'Taxon', 'app', '2013-02-25 10:45:32'),
(895, 'Number of geo entries', 'app', '2013-02-25 10:45:32'),
(896, 'previous', 'app', '2013-02-25 10:45:32'),
(897, 'next', 'app', '2013-02-25 10:45:32'),
(898, 'Select the desired option for each of the taxa listed below and press ''save''.', 'admin', '2013-02-25 11:11:37'),
(899, 'Attach to parent:', 'admin', '2013-02-25 11:11:37'),
(900, 'Do nothing', 'admin', '2013-02-25 11:11:37'),
(901, 'ok', 'admin', '2013-02-27 09:46:25'),
(902, 'beide', 'app', '2013-02-28 10:30:23'),
(903, 'man', 'app', '2013-02-28 10:30:23'),
(904, 'vrouw', 'app', '2013-02-28 10:30:23'),
(905, 'Import state labels for NBC Dierendeterminatie', 'admin', '2013-02-28 10:45:57'),
(906, 'Parsed data', 'admin', '2013-02-28 11:07:01'),
(907, 'Saving labels', 'admin', '2013-02-28 11:50:20'),
(908, 'Could not resolve state "%" (%s).', 'admin', '2013-02-28 12:27:04'),
(909, 'Could not resolve state "%s" (%s).', 'admin', '2013-02-28 12:28:20'),
(910, 'Could not resolve state "%s" for %s.', 'admin', '2013-02-28 12:58:05'),
(911, 'Skipped label for state "%s" for %s (no translation).', 'admin', '2013-02-28 12:58:27'),
(912, 'Skipped state "%s" for %s (no translation).', 'admin', '2013-02-28 13:06:24'),
(913, 'Updated image for "%s" to %s.', 'admin', '2013-02-28 13:06:24'),
(914, 'Done.', 'admin', '2013-02-28 13:06:24'),
(915, 'kb', 'admin', '2013-02-28 13:59:50'),
(916, 'delete this image', 'admin', '2013-02-28 13:59:50'),
(917, 'save description', 'admin', '2013-02-28 13:59:50'),
(918, 'move image downward', 'admin', '2013-02-28 13:59:50'),
(919, 'move image upward', 'admin', '2013-02-28 13:59:50'),
(920, 'Saved: %s (%s)', 'admin', '2013-02-28 14:00:20'),
(921, 'Boktorren determineren', 'app', '2013-02-28 15:19:54'),
(922, ' van ', 'app', '2013-02-28 15:29:11'),
(923, 'van %s', 'app', '2013-03-06 13:03:53'),
(924, 'Merge other project into current', 'admin', '2013-03-07 11:56:03'),
(925, 'Export project data to XML-file.', 'admin', '2013-03-07 12:24:08'),
(926, 'export', 'admin', '2013-03-07 12:24:08'),
(927, 'Images and other media files should be copied by hand, and are referenced in the export file by filename only. They can be found in the server folder:<br />', 'admin', '2013-03-07 12:24:08'),
(928, 'Creating new page', 'admin', '2013-03-07 12:54:00'),
(929, 'No species have been defined.', 'admin', '2013-03-08 08:09:59'),
(930, 'No taxa have been defined.', 'admin', '2013-03-08 08:10:03'),
(931, 'Insert internal link', 'admin', '2013-03-08 08:14:36'),
(932, 'Content pages', 'admin', '2013-03-08 08:14:36'),
(933, 'Page:', 'admin', '2013-03-08 08:14:36'),
(934, 'Glossary alphabet', 'admin', '2013-03-08 08:14:36'),
(935, 'Letter:', 'admin', '2013-03-08 08:14:36'),
(936, 'Glossary term', 'admin', '2013-03-08 08:14:36'),
(937, 'Literature index', 'admin', '2013-03-08 08:14:37'),
(938, 'Literature alphabet', 'admin', '2013-03-08 08:14:37'),
(939, 'Literature reference', 'admin', '2013-03-08 08:14:37'),
(940, 'Species module index', 'admin', '2013-03-08 08:14:37'),
(941, 'Species module detail', 'admin', '2013-03-08 08:14:37'),
(942, 'Species:', 'admin', '2013-03-08 08:14:37'),
(943, 'Category:', 'admin', '2013-03-08 08:14:37'),
(944, 'Higher taxa index', 'admin', '2013-03-08 08:14:37'),
(945, 'Higher taxa detail', 'admin', '2013-03-08 08:14:37'),
(946, 'Taxa:', 'admin', '2013-03-08 08:14:37'),
(947, 'Dichotomous key', 'admin', '2013-03-08 08:14:37'),
(948, 'Distribution index', 'admin', '2013-03-08 08:14:37'),
(949, 'Distribution detail', 'admin', '2013-03-08 08:14:37'),
(950, 'Element:', 'admin', '2013-03-08 08:14:37'),
(951, 'Test index', 'admin', '2013-03-08 08:14:37'),
(952, 'Test topic', 'admin', '2013-03-08 08:14:37'),
(953, 'Vnurk index', 'admin', '2013-03-08 08:14:37'),
(954, 'Vnurk topic', 'admin', '2013-03-08 08:14:37'),
(955, 'Insert a link to:', 'admin', '2013-03-08 08:14:37'),
(956, 'Module:', 'admin', '2013-03-08 08:14:37'),
(957, 'insert link', 'admin', '2013-03-08 08:14:37'),
(958, 'New image', 'admin', '2013-03-08 08:14:48'),
(959, 'Edit data for "%s"', 'admin', '2013-03-08 08:22:26'),
(960, 'copy', 'admin', '2013-03-08 08:22:27'),
(961, 'reset', 'admin', '2013-03-08 08:22:27'),
(962, 'clear', 'admin', '2013-03-08 08:22:27'),
(963, 'Data types', 'admin', '2013-03-08 08:22:31'),
(964, 'Set runtime map type', 'admin', '2013-03-08 08:22:31'),
(965, 'Store compacted data for Linnaeus 2 maps (for runtime performance purposes)', 'admin', '2013-03-08 08:22:31'),
(966, 'Below, you can define up to ten types of geographically organised data. Once defined, you can specify locations on the map for each species, for every data type.', 'admin', '2013-03-08 08:22:33'),
(967, 'Add a new data type:', 'admin', '2013-03-08 08:22:33'),
(968, 'Create new project', 'admin', '2013-03-08 08:53:51'),
(969, 'Enter the project''s name, description and version below, and click ''save'' to create the project.', 'admin', '2013-03-08 08:53:51'),
(970, 'Project name:', 'admin', '2013-03-08 08:53:51'),
(971, 'Project version:', 'admin', '2013-03-08 08:53:51'),
(972, 'Project description:', 'admin', '2013-03-08 08:53:51'),
(973, '(for reference only)', 'admin', '2013-03-08 08:53:51'),
(974, 'Default project languages:', 'admin', '2013-03-08 08:53:51'),
(975, '(you can change this later)', 'admin', '2013-03-08 08:53:51'),
(976, 'As system administrator, you will automatically be made system administrator of the new project. In that capacity, you will be able to create users, add modules and execute other administrative tasks for the newly created project.', 'admin', '2013-03-08 08:53:51'),
(977, 'Project ''%s'' saved.', 'admin', '2013-03-08 08:54:00'),
(978, '%sAdministrate the new project.%s', 'admin', '2013-03-08 08:54:00'),
(979, 'previous', 'admin', '2013-03-08 08:56:45'),
(980, 'next', 'admin', '2013-03-08 08:56:45'),
(981, '(no terms have been defined)', 'admin', '2013-03-08 09:14:53'),
(982, 'New reference', 'admin', '2013-03-08 09:15:23'),
(983, 'Browsing literature', 'admin', '2013-03-08 09:15:27'),
(984, '(no references have been defined)', 'admin', '2013-03-08 09:15:27'),
(985, '(subsection)', 'admin', '2013-03-08 09:20:11'),
(986, 'Keypath (subsection)', 'admin', '2013-03-08 09:20:11'),
(987, 'Full subsection keypath:', 'admin', '2013-03-08 09:20:11'),
(988, 'Below is a graphic representation of your key. Click a node to see the steps that follow from it. Click and drag to move the entire tree.', 'admin', '2013-03-08 09:20:25'),
(989, 'Click to see step "%s"', 'admin', '2013-03-08 09:20:27'),
(990, 'Key sections', 'admin', '2013-03-08 09:20:41'),
(991, '"Key sections" are parts of the dichotomous key that are not connected to the entire key. Put differently, they are steps that are not the starting step of your key, nor the target of any choice in another step. By creating sections, different collaborators can work on specific parts of the key, which are later hooked up to the main key.', 'admin', '2013-03-08 09:20:42'),
(992, 'Available sections (click to edit):', 'admin', '2013-03-08 09:20:42'),
(993, 'Start a new subsection', 'admin', '2013-03-08 09:20:42'),
(994, 'delete all images', 'admin', '2013-03-08 09:20:44'),
(995, '(show all)', 'admin', '2013-03-08 09:20:44'),
(996, 'You need to process and store your key tree to see the list of possible outcomes.', 'admin', '2013-03-08 09:20:44'),
(997, 'Could not create page.', 'admin', '2013-03-08 09:32:35'),
(998, 'Data for "%s"', 'admin', '2013-03-08 11:37:20'),
(999, 'Selection type:', 'admin', '2013-03-08 11:37:20'),
(1000, 'Coordinates:', 'admin', '2013-03-08 11:37:20'),
(1001, 'Select the type of data you are drawing on the map:', 'admin', '2013-03-08 11:37:20'),
(1002, '(%sadd or change datatypes.%s)', 'admin', '2013-03-08 11:37:20'),
(1003, 'To enable setting markers (points on the map), click the button below.', 'admin', '2013-03-08 11:37:20'),
(1004, 'Then click on the appropriate spot on the map to place a marker. To remove a marker, right-click on it.', 'admin', '2013-03-08 11:37:20'),
(1005, 'To enable drawing polygons, click the button below.', 'admin', '2013-03-08 11:37:20'),
(1006, 'Then draw the polygon by clicking the appropriate spots on the map. When finished drawing, click the button again. To remove a polygon, right-click on it.', 'admin', '2013-03-08 11:37:20'),
(1007, 'When you are done, click ''save'' to store all occurrences.', 'admin', '2013-03-08 11:37:20'),
(1008, 'data type', 'admin', '2013-03-08 11:38:29'),
(1009, 'Linnaeus 2 maps', 'admin', '2013-03-08 12:22:02'),
(1010, '"%s"', 'admin', '2013-03-08 12:22:21'),
(1011, 'Switch to another map:', 'admin', '2013-03-08 12:22:21'),
(1012, 'editable map', 'admin', '2013-03-08 12:22:21'),
(1013, 'copy data', 'admin', '2013-03-08 12:22:23'),
(1014, 'Set the type of map that will appear in the runtime interface:', 'admin', '2013-03-08 12:22:28'),
(1015, 'E', 'app', '2013-03-08 12:22:42'),
(1016, 'N', 'app', '2013-03-08 12:22:42'),
(1017, 'clear map', 'admin', '2013-03-08 14:15:14'),
(1018, 'Store compacted Linnaeus 2 data', 'admin', '2013-03-08 14:49:55'),
(1019, 'Click the button below to have the system store the Linnaeus 2 map data in a more compact form.', 'admin', '2013-03-08 14:49:55'),
(1020, 'Please note that, depending on the size of your data, this might take a few minutes.', 'admin', '2013-03-08 14:49:55'),
(1021, 'store compacted data', 'admin', '2013-03-08 14:49:55'),
(1022, 'Compacted data saved', 'admin', '2013-03-08 14:50:00'),
(1023, 'W', 'app', '2013-03-08 14:51:20'),
(1024, 'S', 'app', '2013-03-08 14:51:20'),
(1025, 'Copy occurrences from "%s"', 'admin', '2013-03-08 14:58:47'),
(1026, 'Choose the species you want to copy the map data of "%s" to:', 'admin', '2013-03-08 14:58:47'),
(1027, 'Editing glossary term', 'admin', '2013-03-11 09:16:44'),
(1028, 'Deleting taxon "%s"', 'admin', '2013-03-11 11:10:17'),
(1029, 'You are about to delete the taxon "%s", which has child taxa connected to it. Please specify what should happen to the connected child taxa. There are three possibilities:', 'admin', '2013-03-11 11:10:17'),
(1030, 'Orphans:', 'admin', '2013-03-11 11:10:17'),
(1031, 'turn them into "orphans". Orphans are taxa that are unconnected to the main taxon tree. You will need to individually reattach them later.', 'admin', '2013-03-11 11:10:17'),
(1032, 'Delete:', 'admin', '2013-03-11 11:10:17'),
(1033, 'delete them as well. Effectively this will delete the entire branch from taxon "%s" and down.', 'admin', '2013-03-11 11:10:17'),
(1034, 'Attach:', 'admin', '2013-03-11 11:10:17'),
(1035, 'attach them as child to the parent of "%s", which is the %s "%s". There will be no change in the rank of the reattached taxa.', 'admin', '2013-03-11 11:10:17'),
(1036, 'Orphan', 'admin', '2013-03-11 11:10:17'),
(1037, 'Attach to', 'admin', '2013-03-11 11:10:17'),
(1038, 'move branch upward in the tree', 'admin', '2013-03-11 12:56:58'),
(1039, '(orphan)', 'admin', '2013-03-11 15:56:30'),
(1040, 'No taxa have been assigned to you.', 'admin', '2013-03-11 16:00:27'),
(1041, 'Nederlands Soortenregister', 'app', '2013-03-13 15:32:05'),
(1042, 'Change a project ID', 'admin', '2013-03-13 15:49:42'),
(1043, 'Select the project of which you wish to change the ID.', 'admin', '2013-03-13 15:53:58'),
(1044, 'Select the project of which you wish to change the ID and enter the new ID.', 'admin', '2013-03-13 15:55:07'),
(1045, 'A project with ID %s already exists (%s).', 'admin', '2013-03-13 16:04:09'),
(1046, 'No parent selected (you can still save).', 'admin', '2013-03-14 11:05:46'),
(1047, 'Dichotomous key steps', 'app', '2013-03-19 13:39:28'),
(1048, 'Dichotomous key choices', 'app', '2013-03-19 13:39:28'),
(1049, 'Matrix key matrices', 'app', '2013-03-19 13:39:28'),
(1050, 'Matrix key characters', 'app', '2013-03-19 13:39:28'),
(1051, 'Matrix key states', 'app', '2013-03-19 13:39:28'),
(1052, 'Navigator', 'app', '2013-03-19 13:39:28'),
(1053, 'geographical data', 'app', '2013-03-19 13:39:28'),
(1054, 'Your search for "%s" produced %s results.', 'app', '2013-03-19 13:39:28'),
(1055, 'Expand all', 'app', '2013-03-19 13:39:28'),
(1056, 'and', 'app', '2013-03-19 13:39:28'),
(1057, 'Taxon:', 'app', '2013-03-19 13:39:28'),
(1058, 'in', 'app', '2013-03-19 13:39:30'),
(1059, 'It is not possible to jump directly to a specific step or choice of the dichotomous key', 'app', '2013-03-19 13:39:30'),
(1060, '%sStart the key from the start%s.', 'app', '2013-03-19 13:39:30'),
(1061, 'choice', 'app', '2013-03-19 13:39:30'),
(1062, 'search results', 'app', '2013-03-19 15:22:14'),
(1063, 'Store key tree', 'admin', '2013-03-19 15:50:36'),
(1064, 'Click the button below to have the system store a tree-structured representation of the key, required for runtime purposes.', 'admin', '2013-03-19 15:50:36'),
(1065, 'Please note that, depending on the size of your key, this might take a few minutes.', 'admin', '2013-03-19 15:50:36'),
(1066, 'store key tree', 'admin', '2013-03-19 15:50:36'),
(1067, 'Key tree saved', 'admin', '2013-03-19 15:50:38'),
(1068, 'Literatuur', 'app', '2013-03-20 11:03:49'),
(1069, 'Short name for URL:', 'admin', '2013-03-20 11:30:45'),
(1070, 'Unknown project or invalid project ID.', 'app', '2013-03-20 11:39:16'),
(1071, 'Below is a list of steps without any choices. To edit, click the name the step.', 'admin', '2013-03-20 15:57:06'),
(1072, 'Below is a list of steps with only one choice. To edit, click the name the step.', 'admin', '2013-03-20 15:57:06'),
(1073, 'Below is a list of unconnected choices, i.e. those that do not lead to another step or a taxon. To edit, click the name of either the step or the choice.', 'admin', '2013-03-20 15:57:06'),
(1074, 'choice', 'admin', '2013-03-20 15:57:06'),
(1075, 'Taxon:', 'admin', '2013-03-21 08:36:42'),
(1076, 'step', 'admin', '2013-03-21 09:11:40'),
(1077, 'Taxon ranks in key', 'admin', '2013-03-21 09:29:49'),
(1078, 'Below, you can define taxa of what rank or ranks will be part of your dichotomous key.', 'admin', '2013-03-21 09:29:50'),
(1079, 'The taxa that are of a rank below the red line in the list below are available in your key. To change the selection, move the red line up or down by clicking the &uarr; and &darr; arrows. To include all ranks, move the line to the top of the list, above the first rank. As at least one rank is required to be included, the line cannot be moved below the lowest rank. When you are satisfied with your selection, click the save-button.', 'admin', '2013-03-21 09:29:50'),
(1080, 'Please note that changing this setting will not detach any taxa that have already been attached to an end-point of your key. Taxa that have a rank that is no longer part of the selection below will remain connected to the key, until you manually detach them.', 'admin', '2013-03-21 09:29:50'),
(1081, 'Saved.', 'admin', '2013-03-21 09:29:54'),
(1082, 'Below is a list of taxa that are not yet part of your key:', 'admin', '2013-03-21 11:33:01'),
(1083, 'No key sections are available.', 'admin', '2013-03-21 12:41:58'),
(1084, 'Gebaseerd op:', 'app', '2013-03-22 08:34:26'),
(1085, 'Betekenis iconen:', 'app', '2013-03-22 11:06:19'),
(1086, 'upload and parse', 'admin', '2013-03-26 09:25:31'),
(1087, 'CSV data import', 'admin', '2013-03-26 12:27:01'),
(1088, 'import', 'admin', '2013-03-26 14:32:08'),
(1089, 'Saved page "%s".', 'admin', '2013-03-26 15:10:34'),
(1090, 'Diergroepteksten: "%s"', 'app', '2013-03-26 15:19:32'),
(1091, 'Found no states for "%s"', 'admin', '2013-03-27 12:11:57'),
(1092, 'Click to edit taxon "%s"', 'admin', '2013-03-27 13:11:46'),
(1093, 'Matrix key index', 'admin', '2013-03-27 13:37:57'),
(1094, 'Matrix keys', 'admin', '2013-03-27 13:37:57'),
(1095, 'Introduction', 'admin', '2013-03-27 13:42:22'),
(1096, 'Glossary', 'admin', '2013-03-27 13:42:22'),
(1097, 'Navigator', 'admin', '2013-03-27 13:42:22'),
(1098, 'Step:', 'admin', '2013-03-27 13:45:14'),
(1099, 'Classification', 'admin', '2013-03-27 14:15:42'),
(1100, 'Could not resolve similar id "%s"', 'admin', '2013-03-27 15:54:41'),
(1101, 'Project "%" not found in the database.', 'admin', '2013-03-28 15:28:04'),
(1102, 'Import halted.', 'admin', '2013-03-28 15:28:04'),
(1103, 'Skipped image for "%s" (not specified).', 'admin', '2013-03-28 15:33:03'),
(1104, 'Could not resolve character "%".', 'admin', '2013-03-28 15:33:03'),
(1105, '(not in any group)', 'admin', '2013-04-02 10:02:13'),
(1106, 'not in any group:', 'admin', '2013-04-02 10:05:43'),
(1107, 'not in any group', 'admin', '2013-04-02 11:31:04'),
(1108, 'A group named "%s" already exists.', 'admin', '2013-04-02 13:51:34'),
(1109, 'delete group', 'admin', '2013-04-02 13:59:21'),
(1110, 'Save and finish import', 'admin', '2013-04-03 07:26:10'),
(1111, 'language', 'admin', '2013-04-03 12:23:29'),
(1112, 'reacquire state image dimensions', 'admin', '2013-04-04 09:46:19'),
(1113, 'Updated states for "%s".', 'admin', '2013-04-04 09:56:22'),
(1114, 'Settings', 'admin', '2013-04-04 13:58:05'),
(1115, 'A setting with the name "%s" alreasy exists.', 'admin', '2013-04-04 14:29:29'),
(1116, 'A setting with the name "%s" already exists.', 'admin', '2013-04-04 14:32:06'),
(1117, 'A value is required for "%s".', 'admin', '2013-04-04 14:32:10'),
(1118, 'Project data', 'admin', '2013-04-05 07:14:06'),
(1119, 'eds', 'admin', '2013-04-05 08:10:16'),
(1120, 'Edit project collaborator', 'admin', '2013-04-09 09:33:15'),
(1121, '(has never worked on project)', 'admin', '2013-04-09 09:33:15'),
(1122, 'Project role:', 'admin', '2013-04-09 09:33:15'),
(1123, 'Active', 'admin', '2013-04-09 09:33:15'),
(1124, 'Select the modules that will be assigned to this collaborator:', 'admin', '2013-04-09 09:33:15'),
(1125, 'Taxon file upload', 'admin', '2013-04-10 09:56:17'),
(1126, 'CSV field delimiter:', 'admin', '2013-04-10 09:56:17'),
(1127, '(comma)', 'admin', '2013-04-10 09:56:17'),
(1128, '(semi-colon)', 'admin', '2013-04-10 09:56:17'),
(1129, 'tab stop', 'admin', '2013-04-10 09:56:17'),
(1130, 'To load a list of taxa from file, click the ''browse''-button above, select the file to load from your computer and click ''upload''.\r\nThe contents of the file will be displayed so you can review them before they are saved to your project''s database.', 'admin', '2013-04-10 09:56:17'),
(1131, 'The file must meet the following conditions:', 'admin', '2013-04-10 09:56:17'),
(1132, 'The format needs to be CSV.', 'admin', '2013-04-10 09:56:17'),
(1133, 'The field delimiter must be a comma, semi-colon or tab stop, and can be selected above.', 'admin', '2013-04-10 09:56:17'),
(1134, 'The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.', 'admin', '2013-04-10 09:56:17'),
(1135, 'There should be one taxon per line. No header line should be present.', 'admin', '2013-04-10 09:56:17'),
(1136, 'Each taxon consists of the following fields:', 'admin', '2013-04-10 09:56:17'),
(1137, 'Taxon name', 'admin', '2013-04-10 09:56:17'),
(1138, 'Taxon rank', 'admin', '2013-04-10 09:56:17'),
(1139, 'in that order. The first two are mandatory. ', 'admin', '2013-04-10 09:56:17'),
(1140, 'Ranks should match the list of ranks you have selected for your project.', 'admin', '2013-04-10 09:56:17'),
(1141, 'These currently are:', 'admin', '2013-04-10 09:56:17'),
(1142, 'Taxa with a rank that does not appear in this list will not be loaded.', 'admin', '2013-04-10 09:56:17'),
(1143, 'Hybrids are only possible for the following ranks:', 'admin', '2013-04-10 09:56:17'),
(1144, 'Parent-child relations are assumed top-down, one branch at a time. For instance, loading:', 'admin', '2013-04-10 09:56:17'),
(1145, 'in this order will correctly maintain the relations between Genus, Species and Infraspecies.', 'admin', '2013-04-10 09:56:17'),
(1146, 'Download a sample CSV-file', 'admin', '2013-04-10 09:56:17'),
(1147, 'Import taxon content from file', 'admin', '2013-04-10 09:57:22'),
(1148, 'To load taxa content from file, click the ''browse''-button above, select the file to load from your computer and click ''upload''.', 'admin', '2013-04-10 10:03:22'),
(1149, 'Each line consists of the following fields:', 'admin', '2013-04-10 10:03:22'),
(1150, 'Taxon ID (currently there is no automated lookup - sorry)', 'admin', '2013-04-10 10:03:22'),
(1151, 'Language ID', 'admin', '2013-04-10 10:03:22'),
(1152, 'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, and will overwrite any existant data for that combination of taxon and category.', 'admin', '2013-04-10 10:03:22'),
(1153, 'The first two fields are mandatory, all fields are expected in the order displayed above.', 'admin', '2013-04-10 10:04:25'),
(1154, 'The first line should contain the field headers:', 'admin', '2013-04-10 10:05:09'),
(1155, 'Taxon ID: optional, program explicitly expects the first column to be the taxon ID.', 'admin', '2013-04-10 10:06:33'),
(1156, 'Language ID: optional, program explicitly expects the first column to be the taxon ID.', 'admin', '2013-04-10 10:06:33'),
(1157, 'The content columns should have the system names of the corresponding categories in your project. Currently, these are:', 'admin', '2013-04-10 10:06:33'),
(1158, 'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existant data for that combination of taxon and category will be overwritten.', 'admin', '2013-04-10 10:12:29'),
(1159, 'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existent data for that combination of taxon and category will be overwritten without warning.', 'admin', '2013-04-10 10:14:53'),
(1160, 'The content column headers should contain the system names of the corresponding categories in your project. Currently, these are:', 'admin', '2013-04-10 10:14:53'),
(1161, 'Unknown rank', 'admin', '2013-04-10 10:20:42'),
(1162, 'Uppermost taxon is not a %s, and has a rank that has no immediate parent.', 'admin', '2013-04-10 10:20:43'),
(1163, 'The field delimiter must be a comma.', 'admin', '2013-04-10 11:27:31'),
(1164, 'saved (could not save certain HTML-tags)', 'admin', '2013-04-10 12:45:59'),
(1165, 'Species module: "%s"', 'app', '2013-04-10 13:06:00'),
(1166, 'Higher taxa: "%s"', 'app', '2013-04-10 13:11:13'),
(1167, 'Keys for "Euphausiids of the World Ocean"', 'admin', '2013-04-11 14:10:28'),
(1168, 'Additional data for "Euphausiids of the World Ocean"', 'admin', '2013-04-11 14:11:42'),
(1169, 'Set runtime key type', 'admin', '2013-04-11 14:20:02'),
(1170, 'Set the type of key that will appear in the runtime interface:', 'admin', '2013-04-11 14:20:02'),
(1171, 'Distribution', 'admin', '2013-04-11 14:20:06'),
(1172, 'Insufficient data.', 'admin', '2013-04-11 14:26:55'),
(1173, 'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existant data for that combination of taxon and category will be overwritten without warning.', 'admin', '2013-04-12 14:39:40'),
(1174, 'Keys for "Dagvlinders van Europa"', 'admin', '2013-04-16 07:12:44'),
(1175, 'Additional data for "Dagvlinders van Europa"', 'admin', '2013-04-16 07:13:21'),
(1176, 'Keys for "-test project «Oryx» (2013-04-16T09:45:24+02:00)"', 'admin', '2013-04-16 07:46:04'),
(1177, 'Keys for "-test project «Lobster» (2013-04-16T10:03:38+02:00)"', 'admin', '2013-04-16 08:04:37'),
(1178, 'Keys for "-test project «Eagle» (2013-04-16T10:07:51+02:00)"', 'admin', '2013-04-16 08:08:36'),
(1179, 'Choose a matrix to use', 'app', '2013-04-16 10:28:52'),
(1180, 'Keys for "-test project «Wasp» (2013-04-16T14:13:00+02:00)"', 'admin', '2013-04-16 12:13:54'),
(1181, 'Additional data for "-test project «Wasp» (2013-04-16T14:13:00+02:00)"', 'admin', '2013-04-16 12:15:07');

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
(130, 208, 24, '< vorige'),
(99, 182, 24, 'Taal'),
(132, 129, 24, '& andere matrices'),
(131, 466, 24, '\n\n'),
(118, 337, 24, '\n'),
(109, 391, 24, 'Acties'),
(110, 394, 24, 'toevoegen'),
(111, 422, 24, 'Voeg een nieuwe categorie toe:'),
(117, 544, 24, '\n'),
(135, 331, 24, '(dubbelklik om te verwijderen)\n'),
(134, 163, 24, '(huidig)\n'),
(133, 622, 24, '\n'),
(129, 288, 24, '\n'),
(136, 273, 24, '(nieuwe stap)\n'),
(143, 842, 24, 'vrouw'),
(142, 841, 24, 'man');

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

INSERT INTO `dev_modules` (`id`, `module`, `description`, `controller`, `icon`, `show_order`, `show_in_menu`, `show_in_public_menu`, `created`, `last_change`) VALUES
(1, 'Introduction', 'Comprehensive project introduction', 'introduction', 'introduction.png', 0, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(2, 'Glossary', 'Project glossary', 'glossary', 'glossary.png', 2, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(3, 'Literature', 'Literary references', 'literature', 'literature.png', 3, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(4, 'Species', 'Detailed pages for taxa', 'species', 'species.png', 4, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(5, 'Higher taxa', 'Detailed pages for higher taxa', 'highertaxa', 'highertaxa.png', 5, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(6, 'Dichotomous key', 'Dichotomic key based on pictures and text', 'key', 'dichotomouskey.png', 6, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(7, 'Matrix key', 'Key based on attributes', 'matrixkey', 'matrixkey.png', 7, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(8, 'Distribution', 'Key based on species distribution', 'mapkey', 'mapkey.png', 8, 1, 1, '2010-08-30 18:18:24', '2010-08-30 16:18:24'),
(10, 'Additional texts', 'Welcome, About ETI, etc', 'content', NULL, 9, 0, 0, '2011-10-27 14:48:04', '2011-10-27 12:48:07'),
(11, 'Index', 'Index module', 'index', 'index.png', 1, 1, 1, '2011-10-27 16:27:21', '2011-10-27 14:27:24'),
(12, 'Search', 'Search and replace within all modules.', 'utilities', NULL, 10, 0, 0, '2011-11-17 12:31:32', '2011-11-17 11:31:35');

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
(90, 'Patho-variety', NULL, 'Patho-variety', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(91, 'Special form', NULL, 'Special form', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38'),
(92, 'Bio-variety', NULL, 'Bio-variety', NULL, -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 11:25:38');

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

INSERT INTO `dev_rights_roles` (`id`, `right_id`, `role_id`, `created`) VALUES
(1, 1, 1, '2010-11-03 15:03:39'),
(2, 8, 1, '2010-11-03 15:03:39'),
(3, 13, 1, '2010-11-03 15:03:39'),
(4, 27, 1, '2010-11-03 15:03:39'),
(79, 3, 2, '2012-03-08 12:21:32'),
(6, 8, 2, '2010-11-03 15:03:43'),
(7, 13, 2, '2010-11-03 15:03:43'),
(8, 27, 2, '2010-11-03 15:03:43'),
(9, 3, 3, '2010-11-03 15:03:46'),
(10, 6, 3, '2010-11-03 15:03:46'),
(11, 7, 3, '2010-11-03 15:03:46'),
(12, 13, 3, '2010-11-03 15:03:46'),
(13, 27, 3, '2010-11-03 15:03:46'),
(14, 3, 4, '2010-11-03 15:03:50'),
(15, 15, 4, '2010-11-03 15:03:50'),
(16, 16, 4, '2010-11-03 15:03:50'),
(17, 3, 5, '2010-11-03 15:03:53'),
(18, 38, 1, '2010-12-27 11:33:10'),
(19, 38, 2, '2010-12-27 11:33:14'),
(20, 38, 3, '2010-12-27 11:33:17'),
(21, 42, 1, '2011-01-03 11:00:32'),
(22, 42, 2, '2011-01-03 11:00:36'),
(23, 42, 3, '2011-01-03 11:00:39'),
(24, 43, 1, '2011-01-04 11:53:22'),
(25, 43, 2, '2011-01-04 11:53:32'),
(26, 43, 3, '2011-01-04 11:53:40'),
(27, 44, 1, '2011-01-14 12:51:59'),
(28, 44, 2, '2011-01-14 12:52:03'),
(29, 44, 3, '2011-01-14 12:52:07'),
(30, 45, 1, '2011-01-31 11:25:32'),
(31, 45, 2, '2011-01-31 11:25:37'),
(32, 45, 3, '2011-01-31 11:25:40'),
(33, 46, 1, '2011-02-08 13:34:19'),
(34, 46, 2, '2011-02-08 13:34:28'),
(35, 46, 3, '2011-02-08 13:34:36'),
(36, 14, 4, '2011-02-14 16:26:14'),
(37, 64, 1, '2011-02-21 09:56:06'),
(38, 64, 2, '2011-02-21 09:56:10'),
(39, 64, 3, '2011-02-21 09:56:23'),
(40, 14, 5, '2011-03-14 15:27:29'),
(41, 15, 5, '2011-03-14 15:27:29'),
(42, 16, 5, '2011-03-14 15:27:29'),
(43, 21, 5, '2011-03-14 15:27:29'),
(44, 22, 5, '2011-03-14 15:27:29'),
(45, 26, 5, '2011-03-14 15:27:29'),
(46, 38, 5, '2011-03-14 15:27:29'),
(47, 42, 5, '2011-03-14 15:27:29'),
(48, 43, 5, '2011-03-14 15:27:29'),
(50, 65, 5, '2011-03-14 15:27:29'),
(51, 66, 5, '2011-03-14 15:27:29'),
(52, 67, 5, '2011-03-14 15:27:29'),
(53, 68, 5, '2011-03-14 15:27:29'),
(54, 79, 1, '2011-10-27 16:29:57'),
(55, 80, 1, '2011-10-27 16:29:57'),
(56, 79, 2, '2011-10-27 16:30:07'),
(57, 80, 2, '2011-10-27 16:30:07'),
(58, 79, 3, '2011-10-27 16:30:20'),
(59, 80, 3, '2011-10-27 16:30:20'),
(60, 79, 4, '2011-10-27 16:30:27'),
(61, 80, 4, '2011-10-27 16:30:27'),
(62, 79, 5, '2011-10-27 16:30:35'),
(63, 80, 5, '2011-10-27 16:30:35'),
(64, 81, 1, '2011-11-17 12:34:48'),
(65, 81, 2, '2011-11-17 12:34:48'),
(66, 81, 3, '2011-11-30 11:29:44'),
(67, 81, 4, '2011-11-30 11:29:44'),
(70, 81, 5, '2011-12-19 12:09:13'),
(74, 2, 2, '2012-03-08 12:20:48'),
(73, 86, 2, '2012-03-08 12:19:39'),
(75, 82, 2, '2012-03-08 12:21:05'),
(76, 83, 2, '2012-03-08 12:21:08'),
(77, 84, 2, '2012-03-08 12:21:12'),
(78, 85, 2, '2012-03-08 12:21:14'),
(80, 4, 2, '2012-03-08 12:21:37'),
(81, 5, 2, '2012-03-08 12:21:40'),
(82, 7, 2, '2012-03-08 12:21:43'),
(83, 7, 4, '2012-03-08 12:21:48'),
(84, 7, 5, '2012-03-08 12:21:50'),
(85, 5, 3, '2012-03-08 12:21:53'),
(86, 5, 4, '2012-03-08 12:21:56'),
(88, 5, 5, '2012-03-08 12:22:02'),
(90, 87, 2, '2012-03-08 12:30:13'),
(91, 78, 1, '2012-03-28 11:32:56');

INSERT INTO `dev_roles` (`id`, `role`, `description`, `abbrev`, `assignable`, `created`) VALUES
(1, 'System administrator', 'ETI admin; creates new projects and lead experts', 'sysadmin', 'n', '2010-08-26 08:46:38'),
(2, 'Lead expert', 'General manager of a project', 'lead ex', 'n', '2010-08-26 08:46:38'),
(3, 'Expert', 'Content manager of a project', 'expert', 'y', '2010-08-26 08:46:38'),
(4, 'Editor', 'Edits specific parts of a project', 'editor', 'y', '2010-08-26 08:46:38'),
(5, 'Contributor', 'Contributes to a project but cannot edit', 'contrib', 'y', '2010-08-26 08:46:39');

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

INSERT INTO `dev_users` (`id`, `username`, `password`, `first_name`, `last_name`, `email_address`, `active`, `superuser`, `timezone_id`, `photo_path`, `email_notifications`, `last_login`, `logins`, `password_changed`, `created_by`, `last_change`, `created`) VALUES
(1, 'mdschermer', '9151666f3c2109c0bd36d2bc5527bdc7', 'Maarten', 'Schermer', 'maarten.schermer@xs4all.nl', 1, 0, 28, NULL, 0, '2012-02-24 10:35:54', 75, NULL, 0, '2012-02-24 09:35:54', '2010-08-27 14:35:58'),
(3, 'raltenburg!', 'df5ea29924d39c3be8785734f13169c6', 'Ruud', 'Altenburg', 'ruud@eti.uva.nl', 1, 0, NULL, NULL, 0, NULL, 0, NULL, 0, '2010-08-27 12:18:30', '2010-08-26 12:04:01'),
(4, 'waddink', '1a36591bceec49c832079e270d7e8b73', 'Wouter', 'Addink', 'waddink@eti.uva.nl', 1, 0, NULL, NULL, 0, NULL, 0, NULL, 0, '2010-08-26 14:07:07', '2010-08-26 16:07:07'),
(15, 'sysadmin', '48a365b4ce1e322a55ae9017f3daf0c0', 'System', 'Administrator', 'sysadmin@eti.uva.nl', 1, 1, 1, NULL, 0, '2013-04-16 13:51:52', 1316, NULL, 0, '2013-04-16 11:51:52', '2010-09-06 10:26:21'),
(14, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Lead', 'Expert', 'expert@eti.uva.nl', 1, 0, 1, NULL, 0, '2011-12-09 13:06:45', 8, NULL, 0, '2011-12-09 12:06:45', '2010-09-06 09:34:51'),
(33, 'huub', '439fbc4a62c54db6dc4a449e23f4f0a9', 'Huub', 'Veldhuijzen van Zanten', 'hveldhuijzen@eti.uva.nl', 1, 0, 1, NULL, 1, '2010-11-18 09:53:10', 1, NULL, 0, '2010-11-18 08:53:10', '2010-11-18 09:52:27'),
(32, 'gideon', 'a623c702154bcdb66dfce443a02c06e0', 'Gideon', 'Gijswijt', 'ggyswyt@eti.uva.nl', 1, 0, 28, NULL, 1, '2010-12-14 11:23:44', 19, NULL, 0, '2010-12-14 10:23:44', '2010-11-18 09:45:07'),
(36, 'henk', '6a7259238ba5989e49f0ea5f75dd4cd0', 'henk', 'henk', 'ha@enk.com', 1, 0, 1, NULL, 1, '2013-01-03 15:43:19', 19, NULL, 0, '2013-01-03 14:43:19', '2011-02-14 14:37:17');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



