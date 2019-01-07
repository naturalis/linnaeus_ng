SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


DELIMITER $$
DROP FUNCTION IF EXISTS `fnStripTags`$$
CREATE DEFINER=`linnaeus_user`@`localhost` FUNCTION `fnStripTags`( Dirty varchar(64000) ) RETURNS varchar(64000) CHARSET latin1
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

DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `controller` varchar(64) NOT NULL,
  `view` varchar(64) NOT NULL,
  `data_before` text,
  `data_after` text,
  `note` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`user`),
  FULLTEXT KEY `fulltext` (`controller`,`data_before`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `actors`;
CREATE TABLE IF NOT EXISTS `actors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_alt` varchar(255) DEFAULT NULL,
  `homepage` varchar(255) DEFAULT NULL,
  `gender` enum('m','f') DEFAULT NULL,
  `is_company` tinyint(1) NOT NULL DEFAULT '0',
  `employee_of_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `actors_addresses`;
CREATE TABLE IF NOT EXISTS `actors_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `address_label` varchar(255) NOT NULL,
  `address` varchar(2000) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `beelduitwisselaar_batches`;
CREATE TABLE `beelduitwisselaar_batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_identifier` varchar(32),
  `project_id` int(11) NOT NULL,
  `number_in_feed` int(5) NOT NULL DEFAULT '0',
  `number_downloaded` int(5) NOT NULL DEFAULT '0',
  `number_moved` int(5) NOT NULL DEFAULT '0',
  `number_saved` int(5) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `characteristics`;
CREATE TABLE IF NOT EXISTS `characteristics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `sys_name` varchar(255) not null,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `characteristics_chargroups`;
CREATE TABLE IF NOT EXISTS `characteristics_chargroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `chargroup_id` int(11) NOT NULL,
  `show_order` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `characteristics_labels`;
CREATE TABLE IF NOT EXISTS `characteristics_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` text NOT NULL,
  `additional` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`characteristic_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `characteristics_labels_states`;
CREATE TABLE IF NOT EXISTS `characteristics_labels_states` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `characteristics_matrices`;
CREATE TABLE IF NOT EXISTS `characteristics_matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `show_order` smallint(6) NOT NULL DEFAULT '-1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`characteristic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `characteristics_states`;
CREATE TABLE IF NOT EXISTS `characteristics_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_dimensions` varchar(16) DEFAULT NULL,
  `lower` float(23,3) DEFAULT NULL,
  `upper` float(23,3) DEFAULT NULL,
  `mean` float(23,3) DEFAULT NULL,
  `sd` float(23,3) DEFAULT NULL,
  `sys_name` varchar(255) not null,
  `show_order` tinyint(4) NOT NULL DEFAULT '-1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`characteristic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `chargroups`;
CREATE TABLE IF NOT EXISTS `chargroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `show_order` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `chargroups_labels`;
CREATE TABLE IF NOT EXISTS `chargroups_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `chargroup_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `choices_content_keysteps`;
CREATE TABLE IF NOT EXISTS `choices_content_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `choice_txt` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`choice_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`choice_txt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `choices_keysteps`;
CREATE TABLE IF NOT EXISTS `choices_keysteps` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `commonnames`;
CREATE TABLE IF NOT EXISTS `commonnames` (
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
  KEY `taxon_id` (`taxon_id`),
  KEY `project_id_2` (`project_id`,`taxon_id`,`language_id`),
  KEY `project_id_3` (`project_id`,`taxon_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`commonname`,`transliteration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `subject` varchar(32) NOT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`subject`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `content_free_modules`;
CREATE TABLE IF NOT EXISTS `content_free_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `project_id_2` (`project_id`,`module_id`,`page_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`topic`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `content_introduction`;
CREATE TABLE IF NOT EXISTS `content_introduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `project_id_2` (`project_id`,`page_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`topic`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `content_keysteps`;
CREATE TABLE IF NOT EXISTS `content_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `keystep_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(64) DEFAULT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`keystep_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `content_taxa`;
CREATE TABLE IF NOT EXISTS `content_taxa` (
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
  KEY `project_id_2` (`project_id`,`publish`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `diversity_index`;
CREATE TABLE IF NOT EXISTS `diversity_index` (
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

DROP TABLE IF EXISTS `diversity_index_old`;
CREATE TABLE IF NOT EXISTS `diversity_index_old` (
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

DROP TABLE IF EXISTS `dna_barcodes`;
CREATE TABLE IF NOT EXISTS `dna_barcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `taxon_literal` varchar(255) NOT NULL,
  `taxon_nsr_id` varchar(24) DEFAULT NULL,
  `barcode` varchar(32) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_literal` varchar(32) DEFAULT NULL,
  `specialist` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `external_ids`;
CREATE TABLE IF NOT EXISTS `external_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `external_id` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`),
  KEY `project_id_2` (`project_id`,`external_id`),
  KEY `project_id_3` (`project_id`,`taxon_id`,`external_id`),
  KEY `project_id_4` (`project_id`,`taxon_id`,`org_id`,`external_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `external_orgs`;
CREATE TABLE IF NOT EXISTS `external_orgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `organisation_url` varchar(255) DEFAULT NULL,
  `general_url` varchar(255) DEFAULT NULL,
  `service_url` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `free_modules_pages`;
CREATE TABLE IF NOT EXISTS `free_modules_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `got_content` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `show_order` mediumint(9) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `free_modules_projects`;
CREATE TABLE IF NOT EXISTS `free_modules_projects` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `free_module_media`;
CREATE TABLE IF NOT EXISTS `free_module_media` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `geodata_types`;
CREATE TABLE IF NOT EXISTS `geodata_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `colour` varchar(6) DEFAULT NULL,
  `type` enum('marker','polygon','both') DEFAULT 'both',
  `show_order` smallint(2) NOT NULL DEFAULT '99',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `geodata_types_titles`;
CREATE TABLE IF NOT EXISTS `geodata_types_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`type_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `glossary`;
CREATE TABLE IF NOT EXISTS `glossary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `term` varchar(255) NOT NULL,
  `definition` text NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  KEY `idx_glossary1` (`project_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`term`,`definition`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `glossary_media`;
CREATE TABLE IF NOT EXISTS `glossary_media` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `glossary_media_captions`;
CREATE TABLE IF NOT EXISTS `glossary_media_captions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`media_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `glossary_synonyms`;
CREATE TABLE IF NOT EXISTS `glossary_synonyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `glossary_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `synonym` varchar(255) NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`glossary_id`),
  KEY `idx_glossary_synonym1` (`project_id`,`glossary_id`),
  FULLTEXT KEY `fulltext` (`synonym`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `gui_menu_order`;
CREATE TABLE IF NOT EXISTS `gui_menu_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `ref_type` enum('char','group') NOT NULL,
  `show_order` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`matrix_id`),
  KEY `project_id_2` (`project_id`,`matrix_id`,`ref_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `habitats`;
CREATE TABLE IF NOT EXISTS `habitats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `sys_label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `habitat_labels`;
CREATE TABLE IF NOT EXISTS `habitat_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `habitat_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`,`habitat_id`,`language_id`),
  KEY `project_id` (`project_id`,`label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `hotwords`;
CREATE TABLE IF NOT EXISTS `hotwords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NULL,
  `hotword` varchar(255) NOT NULL,
  `controller` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_3` (`project_id`,`hotword`,`language_id`),
  KEY `project_id_2` (`project_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `hybrids`;
CREATE TABLE IF NOT EXISTS `hybrids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hybrid` varchar(128) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hybrid` (`hybrid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `interface_texts`;
CREATE TABLE IF NOT EXISTS `interface_texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `env` varchar(8) NOT NULL DEFAULT 'app',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text` (`text`(255),`env`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `interface_translations`;
CREATE TABLE IF NOT EXISTS `interface_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_text_id` int(11) NOT NULL,
  `language_id` tinyint(3) NOT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `introduction_media`;
CREATE TABLE IF NOT EXISTS `introduction_media` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `introduction_pages`;
CREATE TABLE IF NOT EXISTS `introduction_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `got_content` tinyint(1) NOT NULL DEFAULT '0',
  `show_order` int(11) DEFAULT NULL,
  `hide_from_index` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `keysteps`;
CREATE TABLE IF NOT EXISTS `keysteps` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `keysteps_taxa`;
CREATE TABLE IF NOT EXISTS `keysteps_taxa` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`project_id` int(11) NOT NULL,
	`keystep_id` int(11) NOT NULL,
	`taxon_id` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `project_id` (`project_id`,`keystep_id`,`taxon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `l2_diversity_index`;
CREATE TABLE IF NOT EXISTS `l2_diversity_index` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `l2_maps`;
CREATE TABLE IF NOT EXISTS `l2_maps` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `l2_occurrences_taxa`;
CREATE TABLE IF NOT EXISTS `l2_occurrences_taxa` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `l2_occurrences_taxa_combi`;
CREATE TABLE IF NOT EXISTS `l2_occurrences_taxa_combi` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `labels_languages`;
CREATE TABLE IF NOT EXISTS `labels_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label_language_id` int(11) NOT NULL,
  `label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `labels_projects_ranks`;
CREATE TABLE IF NOT EXISTS `labels_projects_ranks` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `labels_sections`;
CREATE TABLE IF NOT EXISTS `labels_sections` (
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

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(32) NOT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `iso2` varchar(2) DEFAULT NULL,
  `locale_win` varchar(32) DEFAULT NULL,
  `locale_lin` varchar(16) DEFAULT NULL,
  `direction` enum('ltr','rtl') DEFAULT 'ltr',
  `show_order` int(2) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `languages_projects`;
CREATE TABLE IF NOT EXISTS `languages_projects` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `literature`;
CREATE TABLE IF NOT EXISTS `literature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `author_first` varchar(64) NOT NULL,
  `author_second` varchar(64) DEFAULT NULL,
  `multiple_authors` tinyint(1) NOT NULL DEFAULT '0',
  `year` smallint(4) DEFAULT NULL,
  `suffix` varchar(3) DEFAULT NULL,
  `year_separator` varchar(8) DEFAULT NULL,
  `year_2` smallint(4) DEFAULT NULL,
  `suffix_2` varchar(3) DEFAULT NULL,
  `text` text NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  FULLTEXT KEY `fulltext` (`author_first`,`author_second`,`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `literature_taxa`;
CREATE TABLE IF NOT EXISTS `literature_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `literature_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`literature_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `literature2`;
CREATE TABLE IF NOT EXISTS `literature2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `label` varchar(1000) NOT NULL,
  `alt_label` varchar(1000) DEFAULT NULL,
  `alt_label_language_id` int(11) DEFAULT NULL,
  `date` varchar(32) DEFAULT NULL,
  `author` varchar(1000) DEFAULT NULL,
  `publication_type` varchar(24) DEFAULT NULL,
  `publication_type_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publishedin` varchar(500) DEFAULT NULL,
  `publishedin_id` int(11) DEFAULT NULL,
  `pages` varchar(100) DEFAULT NULL,
  `volume` varchar(32) DEFAULT NULL,
  `periodical` varchar(128) DEFAULT NULL,
  `periodical_id` int(11) DEFAULT NULL,
  `order_number` int(3) DEFAULT NULL,
  `external_link` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`),
  KEY `project_id` (`project_id`,`label`(250))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `literature2_authors`;
CREATE TABLE IF NOT EXISTS `literature2_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `literature2_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`literature2_id`,`actor_id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `literature2_publication_types`;
CREATE TABLE `literature2_publication_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `sys_label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`sys_label`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `literature2_publication_types_labels`;
CREATE TABLE `literature2_publication_types_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `publication_type_id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`publication_type_id`,`language_id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `matrices`;
CREATE TABLE IF NOT EXISTS `matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `sys_name` varchar(255) DEFAULT 'matrix',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `matrices_names`;
CREATE TABLE IF NOT EXISTS `matrices_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `matrices_taxa`;
CREATE TABLE IF NOT EXISTS `matrices_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`taxon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `matrices_taxa_states`;
CREATE TABLE IF NOT EXISTS `matrices_taxa_states` (
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
  KEY `project_id_6` (`project_id`,`matrix_id`,`ref_matrix_id`,`characteristic_id`,`state_id`),
  KEY `project_id_7` (`project_id`,`matrix_id`,`characteristic_id`,`state_id`),
  KEY `project_id_8` (`project_id`,`matrix_id`,`variation_id`),
  KEY `project_id_10` (`project_id`,`matrix_id`,`taxon_id`),
  KEY `project_id_9` (`project_id`,`matrix_id`,`ref_matrix_id`),
  KEY `state_id` (`state_id`),
  KEY `state_id_2` (`state_id`,`taxon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `matrices_variations`;
CREATE TABLE IF NOT EXISTS `matrices_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`variation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `rs_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `original_name` varchar(128) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `rs_original` varchar(255) NOT NULL,
  `rs_resized_1` varchar(255) DEFAULT NULL,
  `rs_resized_2` varchar(255) DEFAULT NULL,
  `rs_thumb_small` varchar(255) NOT NULL,
  `rs_thumb_medium` varchar(255) NOT NULL,
  `rs_thumb_large` varchar(255) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_1` (`project_id`, `deleted`, `name`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `media_captions`;
CREATE TABLE `media_captions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_modules_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `caption` varchar(500) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`media_modules_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `media_conversion_log`;
CREATE TABLE `media_conversion_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `media_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `old_file` varchar(255) NOT NULL,
  `new_file` varchar(255) NOT NULL,
  `error` varchar(255) DEFAULT NULL,
  `converted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `old_file` (`project_id`,`old_file`),
  KEY `item_id` (`project_id`,`module`,`item_id`),
  KEY `error` (`error`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `media_descriptions_taxon`;
CREATE TABLE IF NOT EXISTS `media_descriptions_taxon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`media_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `media_meta`;
CREATE TABLE IF NOT EXISTS `media_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `sys_label` varchar(64) NOT NULL,
  `meta_data` varchar(1024) DEFAULT NULL,
  `meta_date` datetime DEFAULT NULL,
  `meta_number` float(16,8) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`media_id`),
  KEY `project_id_2` (`project_id`,`sys_label`),
  KEY `project_id_3` (`project_id`,`media_id`,`sys_label`),
  KEY `project_id_4` (`project_id`,`media_id`,`language_id`,`sys_label`),
  KEY `project_id_5` (`project_id`,`sys_label`,`meta_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `media_metadata`;
CREATE TABLE `media_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `sys_label` varchar(64) NOT NULL,
  `metadata` varchar(1024) DEFAULT NULL,
  `metadata_date` datetime DEFAULT NULL,
  `metadata_number` float(16,8) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_4` (`project_id`,`media_id`,`language_id`,`sys_label`),
  KEY `project_id` (`project_id`,`media_id`),
  KEY `project_id_2` (`project_id`,`sys_label`),
  KEY `project_id_3` (`project_id`,`media_id`,`sys_label`),
  KEY `project_id_5` (`project_id`,`sys_label`,`metadata_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `media_modules`;
CREATE TABLE `media_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `overview_image` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`media_id`,`module_id`,`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `media_tags`;
CREATE TABLE `media_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`media_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `media_taxon`;
CREATE TABLE IF NOT EXISTS `media_taxon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `thumb_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(128) NOT NULL,
  `mime_type` varchar(32) NOT NULL,
  `file_size` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `overview_image` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_3` (`project_id`,`taxon_id`,`file_name`),
  KEY `taxon_id` (`taxon_id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`,`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `controller` varchar(32) NOT NULL,
  `show_order` int(2) NOT NULL,
  `show_in_menu` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_public_menu` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module` (`module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `modules_projects`;
CREATE TABLE IF NOT EXISTS `modules_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `show_order` tinyint(2) NOT NULL DEFAULT '0',
  `active` enum('y','n') NOT NULL DEFAULT 'y',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`module_id`),
  KEY `project_id_2` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `module_settings`;
CREATE TABLE IF NOT EXISTS `module_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `setting` varchar(64) NOT NULL,
  `info` varchar(1000) DEFAULT NULL,
  `default_value` varchar(512) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_settings_3` (`module_id`,`setting`),
  KEY `module_settings_2` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF  EXISTS `module_settings_values`;
CREATE TABLE IF NOT EXISTS `module_settings_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `setting_id` int(11) NOT NULL,
  `value` varchar(512) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_settings_3` (`project_id`,`setting_id`),
  KEY `module_settings_1` (`project_id`),
  KEY `module_settings_2` (`project_id`,`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `names`;
CREATE TABLE IF NOT EXISTS `names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `uninomial` varchar(64) DEFAULT NULL,
  `specific_epithet` varchar(64) DEFAULT NULL,
  `infra_specific_epithet` varchar(64) DEFAULT NULL,
  `authorship` varchar(128) DEFAULT NULL,
  `name_author` varchar(128) DEFAULT NULL,
  `authorship_year` varchar(16) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `expert` varchar(255) DEFAULT NULL,
  `expert_id` int(11) DEFAULT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `rank_id` int(11) NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`),
  KEY `project_id_2` (`project_id`,`taxon_id`,`language_id`),
  KEY `project_id_4` (`project_id`,`type_id`),
  KEY `project_id_3` (`project_id`,`uninomial`,`specific_epithet`),
  KEY `project_id_5` (`project_id`,`created`,`last_change`),
  KEY `project_id_6` (`project_id`,`taxon_id`,`type_id`),
  KEY `project_id_7` (`project_id`,`name`,`authorship`),
  KEY `project_id_8` (`project_id`,`uninomial`,`specific_epithet`,`infra_specific_epithet`),
  KEY `project_id_9` (`project_id`,`uninomial`),
  KEY `project_id_10` (`project_id`,`taxon_id`,`language_id`,`type_id`),
  KEY `name` (`name`,`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `name_types`;
CREATE TABLE IF NOT EXISTS `name_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `nametype` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`nametype`),
  KEY `id` (`id`,`project_id`),
  KEY `id_2` (`id`,`project_id`,`nametype`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `names_additions`;
CREATE TABLE `names_additions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `addition` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nbc_extras`;
CREATE TABLE IF NOT EXISTS `nbc_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation','matrix') NOT NULL DEFAULT 'taxon',
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`ref_id`,`ref_type`),
  KEY `project_id_2` (`project_id`,`ref_id`,`ref_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `nsr_ids`;
CREATE TABLE IF NOT EXISTS `nsr_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `rdf_id` varchar(128) DEFAULT NULL,
  `nsr_id` varchar(128) DEFAULT NULL,
  `lng_id` int(11) NOT NULL,
  `item_type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rdf_id` (`rdf_id`),
  KEY `nsr_id` (`nsr_id`),
  KEY `nsr_id_2` (`nsr_id`,`item_type`),
  KEY `item_type` (`item_type`),
  KEY `project_id` (`project_id`,`lng_id`,`item_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `occurrences_taxa`;
CREATE TABLE IF NOT EXISTS `occurrences_taxa` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `pages_taxa`;
CREATE TABLE IF NOT EXISTS `pages_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page` varchar(32) NOT NULL,
  `def_page` tinyint(1) NOT NULL DEFAULT '0',
  `external_reference` varchar(4000) DEFAULT NULL,
  `always_hide` tinyint(1) NOT NULL DEFAULT '0',
  `page_blocks` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `pages_taxa_titles`;
CREATE TABLE IF NOT EXISTS `pages_taxa_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `presence`;
CREATE TABLE IF NOT EXISTS `presence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `sys_label` varchar(255) NOT NULL,
  `established` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `presence_labels`;
CREATE TABLE IF NOT EXISTS `presence_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `presence_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `information` varchar(1024) DEFAULT NULL,
  `information_short` varchar(255) DEFAULT NULL,
  `information_title` varchar(64) DEFAULT NULL,
  `index_label` varchar(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`,`presence_id`,`language_id`),
  KEY `project_id` (`project_id`,`presence_id`,`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `presence_taxa`;
CREATE TABLE IF NOT EXISTS `presence_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `presence_id` int(11) DEFAULT NULL,
  `presence82_id` int(11) DEFAULT NULL,
  `habitat_id` int(11) DEFAULT NULL,
  `is_indigenous` tinyint(1) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `actor_org_id` int(11) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id1` (`project_id`,`taxon_id`),
  KEY `project_id2` (`project_id`,`taxon_id`,`presence_id`),
  KEY `project_id3` (`project_id`,`taxon_id`,`presence82_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_name` varchar(255) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `projects_ranks`;
CREATE TABLE IF NOT EXISTS `projects_ranks` (
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
  KEY `project_id_3` (`project_id`,`rank_id`,`parent_id`),
  KEY `project_id_4` (`project_id`),
  KEY `rank_id` (`rank_id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `projects_roles_users`;
CREATE TABLE IF NOT EXISTS `projects_roles_users` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ranks`;
CREATE TABLE IF NOT EXISTS `ranks` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `rdf`;
CREATE TABLE IF NOT EXISTS `rdf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `subject_type` varchar(32) NOT NULL,
  `predicate` varchar(32) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`subject_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hidden` tinyint(1) null default 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section` varchar(32) NOT NULL,
  `show_order` int(2) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `setting` varchar(64) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`setting`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `synonyms`;
CREATE TABLE IF NOT EXISTS `synonyms` (
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
  KEY `taxon_id` (`taxon_id`),
  FULLTEXT KEY `fulltext` (`synonym`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tab_order`;
CREATE TABLE IF NOT EXISTS `tab_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `show_order` int(2) NOT NULL DEFAULT '99',
  `start_order` int(2) DEFAULT NULL,
  `suppress` tinyint(1) NOT NULL DEFAULT '0',
  `show_when_empty` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `project_id` (`project_id`,`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxa`;
CREATE TABLE IF NOT EXISTS `taxa` (
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
  KEY `is_empty` (`is_empty`),
  KEY `taxon_order` (`taxon_order`),
  KEY `id` (`id`,`project_id`),
  FULLTEXT KEY `fulltext` (`taxon`)
) ENGINE=MyISAM AUTO_INCREMENT=1758 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxa_relations`;
CREATE TABLE IF NOT EXISTS `taxa_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation') NOT NULL DEFAULT 'taxon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxon_id` (`taxon_id`,`relation_id`,`ref_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxa_variations`;
CREATE TABLE IF NOT EXISTS `taxa_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxon_id` (`taxon_id`,`label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxon_quick_parentage`;
CREATE TABLE IF NOT EXISTS `taxon_quick_parentage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `parentage` varchar(4000) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id1` (`project_id`),
  KEY `project_id2` (`project_id`,`taxon_id`),
  FULLTEXT KEY `parentage_idx` (`parentage`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxon_trends`;
CREATE TABLE IF NOT EXISTS `taxon_trends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `source_id` int(11) DEFAULT NULL,
  `trend_label` varchar(64) NOT NULL,
  `trend` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxon_trend_years`;
CREATE TABLE IF NOT EXISTS `taxon_trend_years` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `source_id` int(11) DEFAULT NULL,
  `trend_year` int(4) NOT NULL,
  `trend` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxongroups`;
CREATE TABLE `taxongroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sys_label` varchar(64) NOT NULL,
  `show_order` int(2) NOT NULL DEFAULT '99',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`sys_label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxongroups_labels`;
CREATE TABLE `taxongroups_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxongroup_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxongroup_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `taxongroups_taxa`;
CREATE TABLE `taxongroups_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxongroup_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `show_order` int(3) NOT NULL DEFAULT '999',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxongroup_id`,`taxon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `text_translations`;
CREATE TABLE `text_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `text_id` int(11) DEFAULT NULL,
  `language_id` int(11) NOT NULL,
  `translation` varchar(4000) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_date_formats`;
CREATE TABLE `traits_date_formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sysname` varchar(64) NOT NULL,
  `format` varchar(64) NOT NULL,
  `format_hr` varchar(64) NOT NULL,
  `format_db` varchar(64) NOT NULL,
  `show_order` int(3) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`sysname`),
  UNIQUE KEY `format` (`format`),
  UNIQUE KEY `format_hr` (`format_hr`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_groups`;
CREATE TABLE `traits_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sysname` varchar(64) NOT NULL,
  `name_tid` int(11) DEFAULT NULL,
  `description_tid` int(11) DEFAULT NULL,
  `all_link_text_tid` int(11) DEFAULT NULL,
  `help_link_url` varchar(255) DEFAULT NULL,
  `show_show_all_link` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_search` tinyint(1) NOT NULL DEFAULT '1',
  `show_order` int(3) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`project_id`,`sysname`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_project_types`;
CREATE TABLE `traits_project_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_type` (`project_id`,`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_settings`;
CREATE TABLE `traits_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_group_id` int(11) NOT NULL,
  `setting` varchar(64) NOT NULL,
  `value` varchar(1000) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting` (`setting`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_taxon_freevalues`;
CREATE TABLE `traits_taxon_freevalues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `trait_id` int(11) NOT NULL,
  `boolean_value` tinyint(1) DEFAULT NULL,
  `string_value` varchar(4000) DEFAULT NULL,
  `numerical_value` float(12,5) DEFAULT NULL,
  `numerical_value_end` float(12,5) DEFAULT NULL,
  `date_value` date DEFAULT NULL,
  `date_value_end` date DEFAULT NULL,
  `comment` varchar(1000) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_taxon_references`;
CREATE TABLE `traits_taxon_references` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_group_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`trait_group_id`,`taxon_id`,`reference_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_taxon_values`;
CREATE TABLE `traits_taxon_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `comment` varchar(1000) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_traits`;
CREATE TABLE `traits_traits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_group_id` int(11) NOT NULL,
  `project_type_id` int(11) NOT NULL,
  `date_format_id` int(11) DEFAULT NULL,
  `sysname` varchar(64) NOT NULL,
  `name_tid` int(11) DEFAULT NULL,
  `code_tid` int(11) DEFAULT NULL,
  `description_tid` int(11) DEFAULT NULL,
  `max_length` float(12,5) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL,
  `can_select_multiple` tinyint(1) NOT NULL DEFAULT '1',
  `can_include_comment` tinyint(1) NOT NULL DEFAULT '0',
  `can_be_null` tinyint(1) NOT NULL DEFAULT '0',
  `can_have_range` tinyint(1) NOT NULL DEFAULT '0',
  `show_index_numbers` tinyint(1) NOT NULL DEFAULT '0',
  `show_order` int(3) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`project_id`,`trait_group_id`,`sysname`),
  KEY `project_id` (`project_id`,`id`,`trait_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_types`;
CREATE TABLE `traits_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sysname` varchar(64) NOT NULL,
  `name_tid` int(11) DEFAULT NULL,
  `description_tid` int(11) DEFAULT NULL,
  `verification_function_name` varchar(64) DEFAULT NULL,
  `allow_values` tinyint(1) DEFAULT '1',
  `allow_max_length` tinyint(1) DEFAULT '0',
  `allow_unit` tinyint(1) DEFAULT '0',
  `allow_fractures` tinyint(1) DEFAULT '0',
  `allow_select_multiple` tinyint(1) NOT NULL DEFAULT '0',
  `allow_ranges` tinyint(1) DEFAULT '0',
  `allow_smaller_larger_than` tinyint(1) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`sysname`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `traits_values`;
CREATE TABLE `traits_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_id` int(11) NOT NULL,
  `string_value` varchar(1000) DEFAULT NULL,
  `string_label_tid` int(11) DEFAULT NULL,
  `numerical_value` float(12,5) DEFAULT NULL,
  `numerical_value_end` float(12,5) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `is_lower_limit` tinyint(1) DEFAULT '0',
  `is_upper_limit` tinyint(1) DEFAULT '0',
  `lower_limit_label` varchar(16) DEFAULT NULL,
  `upper_limit_label` varchar(16) DEFAULT NULL,
  `show_order` int(3) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `trash_can`;
CREATE TABLE IF NOT EXISTS `trash_can` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `lng_id` int(11) NOT NULL,
  `item_type` varchar(32) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trash_can_1` (`project_id`,`lng_id`,`item_type`),
  KEY `trash_can_2` (`project_id`),
  KEY `trash_can_3` (`project_id`,`lng_id`,`item_type`),
  KEY `trash_can_4` (`project_id`,`lng_id`,`item_type`,`is_deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `trend_sources`;
CREATE TABLE IF NOT EXISTS `trend_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `source` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email_address` varchar(128) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `logins` int(11) NOT NULL DEFAULT '0',
  `last_password_change` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email_address`),
  UNIQUE KEY `username_2` (`username`),
  KEY `password` (`password`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `users_taxa`;
CREATE TABLE IF NOT EXISTS `users_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`user_id`),
  KEY `project_id_2` (`project_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `user_module_access`;
CREATE TABLE IF NOT EXISTS `user_module_access` (
	id int(11) not null primary key auto_increment,
	project_id int(11) not null,
	user_id int(11) not null,
	module_id int(11) not null,
	module_type enum('standard','custom') default 'standard' not null,
	can_read bool default 1 not null,
	can_write bool default 0 not null,
	can_publish bool default 1 not null,
	last_change timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	created datetime NOT NULL,
	UNIQUE KEY user_module_access_u1 (project_id,module_id,module_type,user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `user_item_access`;
CREATE TABLE IF NOT EXISTS `user_item_access` (
	id int(11) not null primary key auto_increment,
	project_id int(11) not null,
	user_id int(11) not null,
	item_id int(11) not null,
	item_type enum('taxon') not null,
	last_change timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	created datetime NOT NULL,
	UNIQUE KEY user_item_access_u1 (project_id,user_id,item_id,item_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `variations_labels`;
CREATE TABLE IF NOT EXISTS `variations_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `label_type` enum('alternative','prefix','postfix','') NOT NULL DEFAULT 'alternative',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `variation_relations`;
CREATE TABLE IF NOT EXISTS `variation_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation') NOT NULL DEFAULT 'taxon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`variation_id`,`relation_id`,`ref_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `actors_taxa`;
CREATE TABLE `actors_taxa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`actor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
