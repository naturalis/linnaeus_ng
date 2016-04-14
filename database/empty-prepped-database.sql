/*
	full database
	basic data
	no projects
	no users

	
insert into projects
  (id,sys_name,sys_description,title,short_name,css_url,includes_hybrids,keywords,description,`group`,published,created,last_change)
values
  (1,'Project internal name','Project description','Project visible title',null,NULL,0,NULL,NULL,NULL,0,now(),now())
;

insert into users
	(id,username,password,first_name,last_name,email_address,active,last_login,logins,last_password_change,created_by,last_change,created) 
values
	(1,'sysadmin','48a365b4ce1e322a55ae9017f3daf0c0','sys','admin','sy@admin.com',1,null,0,null,-1,now(),now())
;

insert into projects_roles_users
	(id,project_id,role_id,user_id,active,last_project_select,project_selects,created)
values
	(1,1,1,1,1,null,0,now())
;

*/

drop table beelduitwisselaar_batches;
drop table choices_content_keysteps_undo;
drop table content_keysteps_undo;
drop table content_taxa_undo;
drop table diversity_index;
drop table diversity_index_old;
drop table dna_barcodes;
drop table dump;
drop table geodata_types;
drop table geodata_types_titles;
drop table heartbeats;
drop table helptexts;
drop table hybrids;
drop table l2_diversity_index;
drop table l2_maps;
drop table l2_occurrences_taxa;
drop table l2_occurrences_taxa_combi;
drop table literature;
drop table names_temp;
/* drop table nbc_extras; */
drop table occurrences_taxa;
drop table settings;
drop table taxon_trend_years;
drop table taxon_trends;
drop table users_taxa;

		
--
-- Table structure for table `users_taxa`
--
DROP TABLE IF EXISTS `users_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_taxa` (
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
		
		
*/

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
-- Table structure for table `activity_log`
--
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
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
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`user`),
  FULLTEXT KEY `fulltext` (`controller`,`data_before`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `actors`
--
DROP TABLE IF EXISTS `actors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_alt` varchar(255) DEFAULT NULL,
  `homepage` varchar(255) DEFAULT NULL,
  `gender` enum('m','f') DEFAULT NULL,
  `is_company` tinyint(1) NOT NULL DEFAULT '0',
  `employee_of_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `actors_addresses`
--
DROP TABLE IF EXISTS `actors_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actors_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `address_label` varchar(255) NOT NULL,
  `address` varchar(2000) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characteristics`
--
DROP TABLE IF EXISTS `characteristics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characteristics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `got_labels` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characteristics_chargroups`
--
DROP TABLE IF EXISTS `characteristics_chargroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characteristics_chargroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `chargroup_id` int(11) NOT NULL,
  `show_order` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characteristics_labels`
--
DROP TABLE IF EXISTS `characteristics_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characteristics_labels` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characteristics_labels_states`
--
DROP TABLE IF EXISTS `characteristics_labels_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characteristics_labels_states` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characteristics_matrices`
--
DROP TABLE IF EXISTS `characteristics_matrices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characteristics_matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `characteristic_id` int(11) NOT NULL,
  `show_order` smallint(6) NOT NULL DEFAULT '-1',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`characteristic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characteristics_states`
--
DROP TABLE IF EXISTS `characteristics_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characteristics_states` (
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
  `show_order` int(6) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`characteristic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargroups`
--
DROP TABLE IF EXISTS `chargroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chargroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `show_order` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargroups_labels`
--
DROP TABLE IF EXISTS `chargroups_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chargroups_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `chargroup_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `choices_content_keysteps`
--
DROP TABLE IF EXISTS `choices_content_keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `choices_content_keysteps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `choice_txt` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`choice_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`choice_txt`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `choices_keysteps`
--
DROP TABLE IF EXISTS `choices_keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `choices_keysteps` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commonnames`
--
DROP TABLE IF EXISTS `commonnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commonnames` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content`
--
DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `subject` varchar(32) NOT NULL,
  `content` text,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`subject`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_free_modules`
--
DROP TABLE IF EXISTS `content_free_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_free_modules` (
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
  KEY `project_id_2` (`project_id`,`module_id`,`page_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`topic`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_introduction`
--
DROP TABLE IF EXISTS `content_introduction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_introduction` (
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
  KEY `project_id_2` (`project_id`,`page_id`,`language_id`),
  FULLTEXT KEY `fulltext` (`topic`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_keysteps`
--
DROP TABLE IF EXISTS `content_keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_keysteps` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_taxa`
--
DROP TABLE IF EXISTS `content_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_taxa` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `external_ids`
--
DROP TABLE IF EXISTS `external_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_ids` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `external_orgs`
--
DROP TABLE IF EXISTS `external_orgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_orgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `organisation_url` varchar(255) DEFAULT NULL,
  `general_url` varchar(255) DEFAULT NULL,
  `service_url` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `free_module_media`
--
DROP TABLE IF EXISTS `free_module_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `free_module_media` (
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
-- Table structure for table `free_modules_pages`
--
DROP TABLE IF EXISTS `free_modules_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `free_modules_pages` (
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
-- Table structure for table `free_modules_projects`
--
DROP TABLE IF EXISTS `free_modules_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `free_modules_projects` (
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
-- Table structure for table `glossary`
--
DROP TABLE IF EXISTS `glossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossary` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `glossary_media`
--
DROP TABLE IF EXISTS `glossary_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossary_media` (
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
-- Table structure for table `glossary_media_captions`
--
DROP TABLE IF EXISTS `glossary_media_captions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossary_media_captions` (
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
-- Table structure for table `glossary_synonyms`
--
DROP TABLE IF EXISTS `glossary_synonyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossary_synonyms` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gui_menu_order`
--
DROP TABLE IF EXISTS `gui_menu_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gui_menu_order` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `habitat_labels`
--
DROP TABLE IF EXISTS `habitat_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `habitat_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `habitat_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`,`habitat_id`,`language_id`),
  KEY `project_id` (`project_id`,`label`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `habitats`
--
DROP TABLE IF EXISTS `habitats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `habitats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `sys_label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `hotwords`
--
DROP TABLE IF EXISTS `hotwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotwords` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `interface_texts`
--
DROP TABLE IF EXISTS `interface_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interface_texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `env` varchar(8) NOT NULL DEFAULT 'app',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text` (`text`(255),`env`)
) ENGINE=MyISAM AUTO_INCREMENT=1721 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `interface_translations`
--
DROP TABLE IF EXISTS `interface_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interface_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_text_id` int(11) NOT NULL,
  `language_id` tinyint(3) NOT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=147 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `introduction_media`
--
DROP TABLE IF EXISTS `introduction_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `introduction_media` (
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
-- Table structure for table `introduction_pages`
--
DROP TABLE IF EXISTS `introduction_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `introduction_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `got_content` tinyint(1) NOT NULL DEFAULT '0',
  `show_order` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keysteps`
--
DROP TABLE IF EXISTS `keysteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keysteps` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keytrees`
--
DROP TABLE IF EXISTS `keytrees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keytrees` (
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
-- Table structure for table `labels_languages`
--
DROP TABLE IF EXISTS `labels_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labels_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label_language_id` int(11) NOT NULL,
  `label` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labels_projects_ranks`
--
DROP TABLE IF EXISTS `labels_projects_ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labels_projects_ranks` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labels_sections`
--
DROP TABLE IF EXISTS `labels_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labels_sections` (
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
-- Table structure for table `languages`
--
DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
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
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `languages_projects`
--
DROP TABLE IF EXISTS `languages_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages_projects` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `literature2`
--
DROP TABLE IF EXISTS `literature2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `literature2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `alt_label` varchar(255) DEFAULT NULL,
  `alt_label_language_id` int(11) DEFAULT NULL,
  `date` varchar(32) DEFAULT NULL,
  `author` varchar(1000) DEFAULT NULL,
  `publication_type` varchar(24) DEFAULT NULL,
  `publication_type_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `citation` varchar(1000) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publishedin` varchar(255) DEFAULT NULL,
  `publishedin_id` int(11) DEFAULT NULL,
  `pages` varchar(32) DEFAULT NULL,
  `volume` varchar(32) DEFAULT NULL,
  `periodical` varchar(128) DEFAULT NULL,
  `periodical_id` int(11) DEFAULT NULL,
  `order_number` int(3) DEFAULT NULL,
  `external_link` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`),
  KEY `project_id` (`project_id`,`label`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `literature2_authors`
--
DROP TABLE IF EXISTS `literature2_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `literature2_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `literature2_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`literature2_id`,`actor_id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `literature2_publication_types`
--
DROP TABLE IF EXISTS `literature2_publication_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `literature2_publication_types_labels`
--
DROP TABLE IF EXISTS `literature2_publication_types_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `literature2_publication_types_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `publication_type_id` varchar(255) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`publication_type_id`,`language_id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `literature_taxa`
--
DROP TABLE IF EXISTS `literature_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `literature_taxa` (
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
-- Table structure for table `matrices`
--
DROP TABLE IF EXISTS `matrices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `got_names` tinyint(1) DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrices_names`
--
DROP TABLE IF EXISTS `matrices_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrices_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrices_taxa`
--
DROP TABLE IF EXISTS `matrices_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrices_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`taxon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrices_taxa_states`
--
DROP TABLE IF EXISTS `matrices_taxa_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrices_taxa_states` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrices_variations`
--
DROP TABLE IF EXISTS `matrices_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrices_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`matrix_id`,`variation_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media`
--
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `project_id` (`project_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_captions`
--
DROP TABLE IF EXISTS `media_captions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_descriptions_taxon`
--
DROP TABLE IF EXISTS `media_descriptions_taxon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_descriptions_taxon` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_meta`
--
DROP TABLE IF EXISTS `media_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_meta` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_metadata`
--
DROP TABLE IF EXISTS `media_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_modules`
--
DROP TABLE IF EXISTS `media_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_tags`
--
DROP TABLE IF EXISTS `media_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_taxon`
--
DROP TABLE IF EXISTS `media_taxon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_taxon` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_settings`
--
DROP TABLE IF EXISTS `module_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_settings` (
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
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_settings_values`
--
DROP TABLE IF EXISTS `module_settings_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_settings_values` (
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
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modules`
--
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
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
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modules_projects`
--
DROP TABLE IF EXISTS `modules_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `show_order` tinyint(2) NOT NULL DEFAULT '0',
  `active` enum('y','n') NOT NULL DEFAULT 'y',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`module_id`),
  KEY `project_id_2` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `name_types`
--
DROP TABLE IF EXISTS `name_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `name_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `nametype` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`nametype`),
  KEY `id` (`id`,`project_id`),
  KEY `id_2` (`id`,`project_id`,`nametype`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `names`
--
DROP TABLE IF EXISTS `names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `uninomial` varchar(64) DEFAULT NULL,
  `specific_epithet` varchar(64) DEFAULT NULL,
  `infra_specific_epithet` varchar(64) DEFAULT NULL,
  `authorship` varchar(64) DEFAULT NULL,
  `name_author` varchar(64) DEFAULT NULL,
  `authorship_year` varchar(16) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `expert` varchar(255) DEFAULT NULL,
  `expert_id` int(11) DEFAULT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `rank_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `names_additions`
--
DROP TABLE IF EXISTS `names_additions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `names_additions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `addition` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nsr_ids`
--
DROP TABLE IF EXISTS `nsr_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nsr_ids` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `occurrences_taxa`
--
DROP TABLE IF EXISTS `occurrences_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `occurrences_taxa` (
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
-- Table structure for table `pages_taxa`
--
DROP TABLE IF EXISTS `pages_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages_taxa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page` varchar(32) NOT NULL,
  `show_order` int(11) DEFAULT NULL,
  `def_page` tinyint(1) NOT NULL DEFAULT '0',
  `external_reference` varchar(4000) DEFAULT NULL,
  `always_hide` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pages_taxa_titles`
--
DROP TABLE IF EXISTS `pages_taxa_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages_taxa_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`page_id`,`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presence`
--
DROP TABLE IF EXISTS `presence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `sys_label` varchar(255) NOT NULL,
  `established` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presence_labels`
--
DROP TABLE IF EXISTS `presence_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presence_labels` (
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
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`project_id`,`presence_id`,`language_id`),
  KEY `project_id` (`project_id`,`presence_id`,`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presence_taxa`
--
DROP TABLE IF EXISTS `presence_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presence_taxa` (
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
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`),
  KEY `project_id1` (`project_id`,`taxon_id`),
  KEY `project_id2` (`project_id`,`taxon_id`,`presence_id`),
  KEY `project_id3` (`project_id`,`taxon_id`,`presence82_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--
DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_ranks`
--
DROP TABLE IF EXISTS `projects_ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_ranks` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_roles_users`
--
DROP TABLE IF EXISTS `projects_roles_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_roles_users` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ranks`
--
DROP TABLE IF EXISTS `ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranks` (
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
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdf`
--
DROP TABLE IF EXISTS `rdf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rdf` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`),
  KEY `role_2` (`role`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sections`
--
DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section` varchar(32) NOT NULL,
  `show_order` int(2) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`page_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `setting` varchar(64) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`setting`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `synonyms`
--
DROP TABLE IF EXISTS `synonyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `synonyms` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tab_order`
--
DROP TABLE IF EXISTS `tab_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `tabname` varchar(64) NOT NULL,
  `show_order` int(2) NOT NULL DEFAULT '99',
  `start_order` int(2) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxa`
--
DROP TABLE IF EXISTS `taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxa` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1758 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxa_relations`
--
DROP TABLE IF EXISTS `taxa_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxa_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation') NOT NULL DEFAULT 'taxon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxon_id` (`taxon_id`,`relation_id`,`ref_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxa_variations`
--
DROP TABLE IF EXISTS `taxa_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxa_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxon_id` (`taxon_id`,`label`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxon_quick_parentage`
--
DROP TABLE IF EXISTS `taxon_quick_parentage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxon_quick_parentage` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxongroups`
--
DROP TABLE IF EXISTS `taxongroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxongroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sys_label` varchar(64) NOT NULL,
  `show_order` int(2) NOT NULL DEFAULT '99',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`sys_label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxongroups_labels`
--
DROP TABLE IF EXISTS `taxongroups_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxongroups_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxongroup_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxongroup_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxongroups_taxa`
--
DROP TABLE IF EXISTS `taxongroups_taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `text_translations`
--
DROP TABLE IF EXISTS `text_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `text_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `text_id` int(11) DEFAULT NULL,
  `language_id` int(11) NOT NULL,
  `translation` varchar(4000) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_date_formats`
--
DROP TABLE IF EXISTS `traits_date_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traits_date_formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sysname` varchar(64) NOT NULL,
  `format` varchar(64) NOT NULL,
  `format_hr` varchar(64) NOT NULL,
  `format_db` varchar(64) NOT NULL,
  `show_order` int(3) DEFAULT '0',
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`sysname`),
  UNIQUE KEY `format` (`format`),
  UNIQUE KEY `format_hr` (`format_hr`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_groups`
--
DROP TABLE IF EXISTS `traits_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`project_id`,`sysname`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_project_types`
--
DROP TABLE IF EXISTS `traits_project_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traits_project_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_type` (`project_id`,`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_settings`
--
DROP TABLE IF EXISTS `traits_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traits_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_group_id` int(11) NOT NULL,
  `setting` varchar(64) NOT NULL,
  `value` varchar(1000) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting` (`setting`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_taxon_freevalues`
--
DROP TABLE IF EXISTS `traits_taxon_freevalues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_taxon_references`
--
DROP TABLE IF EXISTS `traits_taxon_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traits_taxon_references` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_group_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`trait_group_id`,`taxon_id`,`reference_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_taxon_values`
--
DROP TABLE IF EXISTS `traits_taxon_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traits_taxon_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `comment` varchar(1000) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`taxon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_traits`
--
DROP TABLE IF EXISTS `traits_traits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`project_id`,`trait_group_id`,`sysname`),
  KEY `project_id` (`project_id`,`id`,`trait_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_types`
--
DROP TABLE IF EXISTS `traits_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`sysname`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traits_values`
--
DROP TABLE IF EXISTS `traits_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trash_can`
--
DROP TABLE IF EXISTS `trash_can`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trash_can` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `user_item_access`
--
DROP TABLE IF EXISTS `user_item_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_item_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` enum('taxon') NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_item_access_u1` (`project_id`,`user_id`,`item_id`,`item_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_module_access`
--
DROP TABLE IF EXISTS `user_module_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_module_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `module_type` enum('standard','custom') NOT NULL DEFAULT 'standard',
  `can_read` tinyint(1) NOT NULL DEFAULT '1',
  `can_write` tinyint(1) NOT NULL DEFAULT '0',
  `can_publish` tinyint(1) NOT NULL DEFAULT '1',
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_module_access_u1` (`project_id`,`module_id`,`module_type`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email_address` varchar(54) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `variation_relations`
--
DROP TABLE IF EXISTS `variation_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variation_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation') NOT NULL DEFAULT 'taxon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`variation_id`,`relation_id`,`ref_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `variations_labels`
--
DROP TABLE IF EXISTS `variations_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variations_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `label_type` enum('alternative','prefix','postfix','') NOT NULL DEFAULT 'alternative',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- Table structure for table `nbc_extras`
--
DROP TABLE IF EXISTS `nbc_extras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nbc_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `ref_type` enum('taxon','variation','matrix') NOT NULL DEFAULT 'taxon',
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`ref_id`,`ref_type`),
  KEY `project_id_2` (`project_id`,`ref_id`,`ref_type`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




--
-- Procedure `country_hos`
-- Function `fnStripTags`
--
DELIMITER $$
DROP PROCEDURE IF EXISTS `country_hos`$$
CREATE DEFINER=`linnaeus_user`@`localhost` PROCEDURE `country_hos`(IN con CHAR(20))
BEGIN
SELECT Name, HeadOfState FROM Country
WHERE Continent = con;
END$$

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





--
-- Dumping data for table `habitat_labels`
--
LOCK TABLES `habitat_labels` WRITE;
/*!40000 ALTER TABLE `habitat_labels` DISABLE KEYS */;
INSERT INTO `habitat_labels` VALUES (1,1,1,24,'land','2014-05-07 10:40:54','0000-00-00 00:00:00'),(2,1,2,24,'land marien','2014-05-07 10:40:54','0000-00-00 00:00:00'),(3,1,3,24,'land zoet','2014-05-07 10:40:54','0000-00-00 00:00:00'),(4,1,4,24,'marien','2014-05-07 10:40:54','0000-00-00 00:00:00'),(5,1,5,24,'zoet','2014-05-07 10:40:54','0000-00-00 00:00:00'),(7,1,8,24,'brak','2015-02-17 10:17:17','2015-02-17 10:17:17'),(8,1,7,24,'marien zoet','2015-02-17 10:18:20','2015-02-17 10:18:20'),(9,1,9,24,'brak land','2015-05-13 09:32:38','2015-05-13 09:32:38'),(10,1,10,24,'brak marien','2015-05-13 09:32:38','2015-05-13 09:32:38'),(11,1,11,24,'brak zoet','2015-05-13 09:32:38','2015-05-13 09:32:38'),(12,1,12,24,'brak marien zoet','2015-05-13 09:32:38','2015-05-13 09:32:38');
/*!40000 ALTER TABLE `habitat_labels` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `habitats`
--
LOCK TABLES `habitats` WRITE;
/*!40000 ALTER TABLE `habitats` DISABLE KEYS */;
INSERT INTO `habitats` VALUES (1,1,'land','2014-05-07 10:40:54','0000-00-00 00:00:00'),(2,1,'land marien','2014-05-07 10:40:54','0000-00-00 00:00:00'),(3,1,'land zoet','2014-05-07 10:40:54','0000-00-00 00:00:00'),(4,1,'marien','2014-05-07 10:40:54','0000-00-00 00:00:00'),(5,1,'zoet','2014-05-07 10:40:54','0000-00-00 00:00:00'),(7,1,'marien zoet','2015-02-05 10:54:05','2015-02-05 10:54:05'),(8,1,'brak','2015-02-17 10:17:11','2015-02-17 10:17:11'),(9,1,'brak land','2015-05-13 09:08:20','2015-05-13 09:08:20'),(10,1,'brak marien','2015-05-13 09:08:20','2015-05-13 09:08:20'),(11,1,'brak zoet','2015-05-13 09:08:20','2015-05-13 09:08:20'),(12,1,'brak marien zoet','2015-05-13 09:08:20','2015-05-13 09:08:20');
/*!40000 ALTER TABLE `habitats` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `interface_texts`
--
LOCK TABLES `interface_texts` WRITE;
/*!40000 ALTER TABLE `interface_texts` DISABLE KEYS */;
INSERT INTO `interface_texts` VALUES (1,'Select a project to work on','admin','2012-12-11 07:51:58'),(2,'Projects','admin','2012-12-11 07:51:58'),(3,'Welcome back, %s.','admin','2012-12-11 07:51:58'),(4,'Logged in as','admin','2012-12-11 07:51:58'),(5,'Log out','admin','2012-12-11 07:51:58'),(6,'Select a project to work on:','admin','2012-12-11 07:51:58'),(7,'System administration tasks:','admin','2012-12-11 07:51:58'),(8,'Create a project','admin','2012-12-11 07:51:58'),(9,'Delete a project','admin','2012-12-11 07:51:58'),(10,'Import Linnaeus 2 data','admin','2012-12-11 07:51:58'),(11,'Collaborator overview','admin','2012-12-11 07:51:58'),(12,'Rights matrix','admin','2012-12-11 07:51:58'),(13,'Interface','admin','2012-12-11 07:51:58'),(14,'Logout','admin','2012-12-11 07:52:14'),(15,'Login','admin','2012-12-11 07:52:14'),(16,'Log in to administer your Linnaeus project','admin','2012-12-11 07:52:15'),(17,'Your username:','admin','2012-12-11 07:52:15'),(18,'our password:','admin','2012-12-11 07:52:15'),(19,'Remember me','admin','2012-12-11 07:52:15'),(20,'Unable to log in? What is the problem you are experiencing?','admin','2012-12-11 07:52:15'),(21,'I forgot my password and/or username: %sreset my password%s.','admin','2012-12-11 07:52:15'),(22,'My password doesn\'t work or my account may have been compromised: please %scontact the helpdesk%s.','admin','2012-12-11 07:52:15'),(23,'Back to Linnaeus NG root','admin','2012-12-11 07:52:15'),(24,'Introduction','app','2012-12-11 07:52:17'),(25,'Glossary','app','2012-12-11 07:52:17'),(26,'Literature','app','2012-12-11 07:52:17'),(27,'Species','app','2012-12-11 07:52:17'),(28,'Higher taxa','app','2012-12-11 07:52:17'),(29,'Dichotomous key','app','2012-12-11 07:52:17'),(30,'Matrix key','app','2012-12-11 07:52:17'),(31,'Distribution','app','2012-12-11 07:52:17'),(32,'Additional texts','app','2012-12-11 07:52:17'),(33,'Index','app','2012-12-11 07:52:17'),(34,'Search','app','2012-12-11 07:52:17'),(35,'projects','app','2012-12-11 07:52:17'),(36,'login','app','2012-12-11 07:52:17'),(37,'help','app','2012-12-11 07:52:17'),(38,'not yet available','app','2012-12-11 07:52:17'),(39,'contact','app','2012-12-11 07:52:17'),(41,'Welcome','app','2012-12-11 07:52:18'),(42,'Contributors','app','2012-12-11 07:52:18'),(43,'About ETI','app','2012-12-11 07:52:18'),(44,'Search...','app','2012-12-11 07:52:18'),(45,'Loading application','app','2012-12-11 07:52:18'),(46,'Contents','app','2012-12-11 07:52:18'),(47,'Back','app','2012-12-11 07:52:18'),(48,'Back to ','app','2012-12-11 07:52:24'),(49,'Previous','app','2012-12-11 07:52:38'),(50,'Next','app','2012-12-11 07:52:38'),(52,'Index: Species and lower taxa','app','2012-12-11 07:52:44'),(53,'Species and lower taxa','app','2012-12-11 07:52:44'),(54,'Common names','app','2012-12-11 07:52:44'),(55,'Index: Higher taxa','app','2012-12-11 07:52:47'),(56,'Index: Common names','app','2012-12-11 07:52:48'),(57,'Language:','app','2012-12-11 07:52:48'),(58,'Show all','app','2012-12-11 07:52:48'),(59,'Glossary: \"%s\"','app','2012-12-11 07:52:52'),(60,'Synonym','app','2012-12-11 07:52:52'),(61,'for','app','2012-12-11 07:52:52'),(62,'Literature: \"%s\"','app','2012-12-11 07:52:57'),(63,'Species module index','app','2012-12-11 07:53:02'),(64,'Media','app','2012-12-11 07:53:02'),(65,'Classification','app','2012-12-11 07:53:02'),(66,'Names','app','2012-12-11 07:53:02'),(67,'Species module: \"%s\" (%s)','app','2012-12-11 07:53:02'),(68,'Higher taxa index','app','2012-12-11 07:53:16'),(69,'Higher taxa: \"%s\" (%s)','app','2012-12-11 07:53:16'),(70,'Dichotomous key: step %s: \"%s\"','app','2012-12-11 07:53:24'),(71,'Step','app','2012-12-11 07:53:24'),(72,'Remaining','app','2012-12-11 07:53:24'),(73,'Excluded','app','2012-12-11 07:53:24'),(74,'%s possible %s remaining:','app','2012-12-11 07:53:24'),(75,'%s %s excluded:','app','2012-12-11 07:53:24'),(76,'No choices made yet','app','2012-12-11 07:53:24'),(77,'First','app','2012-12-11 07:53:24'),(78,'Decision path','app','2012-12-11 07:53:24'),(79,'Return to first step','app','2012-12-11 07:53:34'),(80,'Return to step','app','2012-12-11 07:53:34'),(81,'Matrix \"%s\": identify','app','2012-12-11 07:53:39'),(82,'Identify','app','2012-12-11 07:53:39'),(83,'Examine','app','2012-12-11 07:53:39'),(84,'Compare','app','2012-12-11 07:53:39'),(85,'Matrix:','app','2012-12-11 07:53:39'),(86,'Characters','app','2012-12-11 07:53:39'),(87,'Sort','app','2012-12-11 07:53:39'),(88,'States','app','2012-12-11 07:53:39'),(89,'Add','app','2012-12-11 07:53:39'),(90,'Delete','app','2012-12-11 07:53:39'),(91,'Clear all','app','2012-12-11 07:53:39'),(92,'Search &gt;&gt;','app','2012-12-11 07:53:39'),(93,'Selected combination of characters','app','2012-12-11 07:53:39'),(94,'Treat unknowns as matches','app','2012-12-11 07:53:39'),(95,'Result of this combination of characters','app','2012-12-11 07:53:39'),(96,'Matrix \"%s\": examine','app','2012-12-11 07:53:41'),(97,'Select a taxon','app','2012-12-11 07:53:41'),(98,'Select a taxon from the list to view characters and character states of this taxon.','app','2012-12-11 07:53:41'),(99,'These are used for the identification process under Identify.','app','2012-12-11 07:53:41'),(100,'Type','app','2012-12-11 07:53:41'),(101,'Character','app','2012-12-11 07:53:41'),(102,'State','app','2012-12-11 07:53:41'),(103,'Matrix \"%s\": compare','app','2012-12-11 07:53:42'),(104,'Select two taxa from the lists and click Compare to compare the characters and character states for both taxa. The results show the differences and similarities for both taxa.','app','2012-12-11 07:53:42'),(105,'Unique character states for %s:','app','2012-12-11 07:53:42'),(106,'Shared character states:','app','2012-12-11 07:53:42'),(107,'Unique states in','app','2012-12-11 07:53:42'),(108,'States present in both:','app','2012-12-11 07:53:42'),(109,'States present in neither:','app','2012-12-11 07:53:42'),(110,'Number of available states:','app','2012-12-11 07:53:42'),(111,'Taxonomic distance:','app','2012-12-11 07:53:42'),(112,'Project overview','admin','2012-12-11 07:56:42'),(113,'Content','admin','2012-12-11 07:56:42'),(114,'Welcome','admin','2012-12-11 07:56:42'),(115,'Contributors','admin','2012-12-11 07:56:42'),(116,'Type to find:','admin','2012-12-11 07:56:42'),(117,'Management tasks:','admin','2012-12-11 07:56:42'),(118,'Hotwords','admin','2012-12-11 07:56:42'),(119,'User administration','admin','2012-12-11 07:56:42'),(120,'Project administration','admin','2012-12-11 07:56:42'),(121,'Switch projects','admin','2012-12-11 07:56:42'),(122,'Editing matrix \"%s\"','admin','2012-12-11 07:56:43'),(123,'preview','admin','2012-12-11 07:56:44'),(124,'select another matrix','admin','2012-12-11 07:56:44'),(125,'characters','admin','2012-12-11 07:56:44'),(126,'sort characters','admin','2012-12-11 07:56:44'),(127,'taxa','admin','2012-12-11 07:56:44'),(128,'display current links per taxon','admin','2012-12-11 07:56:44'),(129,'& other matrices','admin','2012-12-11 07:56:44'),(130,'add new','admin','2012-12-11 07:56:44'),(131,'edit/delete selected','admin','2012-12-11 07:56:44'),(132,'add new taxon','admin','2012-12-11 07:56:44'),(133,'remove selected taxon','admin','2012-12-11 07:56:44'),(134,'states','admin','2012-12-11 07:56:44'),(135,'sort states','admin','2012-12-11 07:56:44'),(136,'links','admin','2012-12-11 07:56:44'),(137,'delete selected','admin','2012-12-11 07:56:44'),(138,'Matrices','admin','2012-12-11 07:56:46'),(139,'Below is a list of matrices that are currently defined. In order to edit a matrix\' name, click \"edit name\". In order to edit the actual matrix, click \"edit matrix\".','admin','2012-12-11 07:56:46'),(140,'edit matrix','admin','2012-12-11 07:56:46'),(141,'edit name','admin','2012-12-11 07:56:46'),(142,'delete','admin','2012-12-11 07:56:46'),(143,'create a new matrix','admin','2012-12-11 07:56:46'),(144,'New matrix','admin','2012-12-11 07:56:47'),(145,'Matrix name:','admin','2012-12-11 07:56:47'),(146,'save','admin','2012-12-11 07:56:47'),(147,'back','admin','2012-12-11 07:56:47'),(148,'Switch to another matrix','app','2012-12-11 07:56:57'),(149,'Displaying \"%s\"','app','2012-12-11 08:07:15'),(150,'Diversity index','app','2012-12-11 08:07:15'),(151,'Go to this taxon','app','2012-12-11 08:07:15'),(152,'Select a different map','app','2012-12-11 08:07:15'),(153,'Choose a map','app','2012-12-11 08:07:15'),(154,'Comparing taxa','app','2012-12-11 08:07:18'),(155,'Displays overlap between two taxa.','app','2012-12-11 08:07:18'),(156,'Clear map','app','2012-12-11 08:07:19'),(157,'Select the area you want to search by clicking the relevant squares.','app','2012-12-11 08:07:19'),(158,'When finished, click \'Search\'.','app','2012-12-11 08:07:19'),(159,'records','app','2012-12-11 08:07:21'),(160,'Search results','app','2012-12-11 08:11:43'),(161,'Comparing taxa \"%s\" and \"%s\"','app','2012-12-11 08:13:23'),(162,'Simple dissimilarity coefficient','app','2012-12-11 08:13:34'),(163,'(current)','admin','2012-12-11 08:15:44'),(164,'Index','admin','2012-12-11 09:38:26'),(165,'Project settings','admin','2012-12-11 09:38:26'),(166,'Project modules','admin','2012-12-11 09:38:26'),(167,'Assign collaborators to modules','admin','2012-12-11 09:38:26'),(168,'Get info','admin','2012-12-11 09:38:26'),(169,'Export','admin','2012-12-11 09:38:26'),(170,'Internal project name:','admin','2012-12-11 09:38:28'),(171,'Internal project description:','admin','2012-12-11 09:38:28'),(172,'Project ID:','admin','2012-12-11 09:38:28'),(173,'Project title:','admin','2012-12-11 09:38:28'),(174,'Description (for html meta-tag):','admin','2012-12-11 09:38:28'),(175,'Keywords (for html meta-tag; separate with spaces):','admin','2012-12-11 09:38:28'),(176,'Project languages:','admin','2012-12-11 09:38:28'),(177,'add language','admin','2012-12-11 09:38:28'),(178,'This project includes hybrid taxa:','admin','2012-12-11 09:38:28'),(179,'yes','admin','2012-12-11 09:38:28'),(180,'no','admin','2012-12-11 09:38:28'),(181,'Publish project:','admin','2012-12-11 09:38:28'),(182,'Language','admin','2012-12-11 09:38:28'),(183,'Default','admin','2012-12-11 09:38:28'),(184,'Translation','admin','2012-12-11 09:38:28'),(185,'Status','admin','2012-12-11 09:38:28'),(186,'current','admin','2012-12-11 09:38:28'),(187,'to be translated','admin','2012-12-11 09:38:29'),(188,'translated','admin','2012-12-11 09:38:29'),(189,'published','admin','2012-12-11 09:38:29'),(190,'unpublish','admin','2012-12-11 09:38:29'),(191,'make default','admin','2012-12-11 09:38:31'),(192,'unpublished','admin','2012-12-11 09:38:31'),(193,'publish','admin','2012-12-11 09:38:32'),(214,'Each project can have one dichotomous key. That key consists of a theoretically unlimited number of steps. Each step consists of a number, a title and a description, plus a maximum of four choices. Each choice consists of a title and a text and/or an image. Also, each choice has a target: the connection to the next element within the key. The target can either be another step, or a taxon.','admin','2012-12-11 15:07:23'),(195,'Woordenlijst','app','2012-12-11 09:38:42'),(196,'Soort','app','2012-12-11 09:38:42'),(198,'(common name of %s)','app','2012-12-11 09:39:07'),(199,'Species names','app','2012-12-11 09:39:10'),(200,'Species descriptions','app','2012-12-11 09:39:10'),(201,'Species synonyms','app','2012-12-11 09:39:10'),(202,'Species common names','app','2012-12-11 09:39:10'),(203,'Species media','app','2012-12-11 09:39:10'),(204,'Select modules to export','admin','2012-12-11 09:39:29'),(205,'identifier (and English translation)','admin','2012-12-11 09:39:30'),(206,'translation in %s','admin','2012-12-11 09:39:30'),(207,'Interface translations','admin','2012-12-11 11:09:47'),(208,'< previous','admin','2012-12-11 11:09:48'),(209,'next >','admin','2012-12-11 11:09:48'),(210,'Contents','admin','2012-12-11 11:09:48'),(211,'Management','admin','2012-12-11 11:09:48'),(212,'Show all texts','admin','2012-12-11 11:10:11'),(213,'Show untranslated texts','admin','2012-12-11 11:10:21'),(215,'You can edit the key from the startpoint, following its structure as the users will see it. Additionally, you can create sections of your key that are not yet connected to the main key. In that way, several people can work on different parts of the key at the same time. Once finished, a section can be hooked up to the main key by simply choosing the sections starting step as the target of a choice already part of the main key.','admin','2012-12-11 15:07:23'),(216,'While navigating through your key or key sections, a keypath is maintained at the top of the screen, just beneath the navigational breadcrumb trail. You can navigate within your key by clicking elements in the keypath. As the keypath can become quite large, only the last few elements are show. To see the complete keypath, click the %s symbol at its very beginning.','admin','2012-12-11 15:07:23'),(217,'Edit key (from startpoint)','admin','2012-12-11 15:07:23'),(218,'Edit key sections','admin','2012-12-11 15:07:23'),(219,'Key map','admin','2012-12-11 15:07:23'),(220,'Compute taxon division','admin','2012-12-11 15:07:23'),(221,'Renumber steps','admin','2012-12-11 15:07:23'),(222,'Taxa not part of the key','admin','2012-12-11 15:07:23'),(223,'Key validation','admin','2012-12-11 15:07:23'),(224,'Define ranks that can appear in key','admin','2012-12-11 15:07:23'),(225,'Set key type','admin','2012-12-11 15:07:23'),(226,'Store key tree (for runtime performance purposes)','admin','2012-12-11 15:07:23'),(227,'Show key step %s','admin','2012-12-12 07:31:52'),(228,'undefined','admin','2012-12-12 07:31:52'),(229,'show entire path','admin','2012-12-12 07:31:53'),(230,'Keypath','admin','2012-12-12 07:31:53'),(231,'Full keypath','admin','2012-12-12 07:31:53'),(232,'close','admin','2012-12-12 07:31:53'),(233,'Step','admin','2012-12-12 07:31:53'),(234,'edit','admin','2012-12-12 07:31:53'),(235,'Choices','admin','2012-12-12 07:31:53'),(236,'choice title','admin','2012-12-12 07:31:53'),(237,'choice leads to','admin','2012-12-12 07:31:53'),(238,'change order','admin','2012-12-12 07:31:53'),(675,'Below are all taxa in your project that are part of the higher taxa. All lower taxa can be found in the %sspecies module%s.','admin','2013-01-03 13:19:33'),(240,'add new choice','admin','2012-12-12 07:31:53'),(241,'Remaining taxa','admin','2012-12-12 07:31:53'),(242,'Excluded taxa','admin','2012-12-12 07:31:53'),(243,'Edit choice \"%s\" for step %s','admin','2012-12-12 07:31:55'),(244,'Editing choice','admin','2012-12-12 07:31:56'),(245,'Text:','admin','2012-12-12 07:31:56'),(246,'Image:','admin','2012-12-12 07:31:56'),(247,'Target:','admin','2012-12-12 07:31:56'),(248,'new step','admin','2012-12-12 07:31:56'),(249,'(none)','admin','2012-12-12 07:31:56'),(250,'or','admin','2012-12-12 07:31:56'),(251,'taxon','admin','2012-12-12 07:31:56'),(252,'undo last save','admin','2012-12-12 07:31:56'),(253,'Enter the title, text, an optional image and the target of this choice. Title and text are saved automatically after you have entered the text in the appropriate input.','admin','2012-12-12 07:31:56'),(254,'To change the step-number from the automatically generated one, enter a new number and click \'save\'. Please note that the numbers have to be unique in your key.','admin','2012-12-12 07:31:56'),(255,'the language','admin','2012-12-12 07:32:23'),(256,'Are you sure you want to delete %s \"%s\"?','admin','2012-12-12 07:32:23'),(257,'Deletion will be irreversible.','admin','2012-12-12 07:32:23'),(258,'Final confirmation:','admin','2012-12-12 07:32:24'),(259,'saved','admin','2012-12-12 07:36:27'),(260,'Image saved.','admin','2012-12-12 07:46:42'),(261,'delete image','admin','2012-12-12 07:46:46'),(262,'show','admin','2012-12-12 07:56:16'),(263,'image','admin','2012-12-12 08:03:55'),(264,'move','admin','2012-12-12 08:04:40'),(265,'keystep','admin','2012-12-12 08:11:48'),(266,'Edit step %s','admin','2012-12-12 08:15:06'),(267,'Editing keystep','admin','2012-12-12 08:15:07'),(268,'Number:','admin','2012-12-12 08:15:07'),(269,'Title:','admin','2012-12-12 08:15:07'),(270,'Enter the title and text of this step in your key in the various languages within your project. Title and text are saved automatically after you have entered the text in the appropriate input.','admin','2012-12-12 08:15:07'),(271,'Are you sure you want to delete this image?','admin','2012-12-12 08:20:28'),(272,'Beware: you are changing the target of this choice.\nThis can radically alter the workings of your key.\nDo you wish to continue?','admin','2012-12-12 08:27:16'),(273,'(new step)','admin','2012-12-12 08:27:20'),(274,'(none defined)','admin','2012-12-12 08:27:22'),(275,'Step number is required. The saved number for this step is %s. The lowest unused number is %s.','admin','2012-12-12 08:28:18'),(276,'Introductie','app','2012-12-12 15:05:55'),(277,'Hogere taxa','app','2012-12-12 15:05:55'),(278,'Taxon list','admin','2012-12-14 08:29:31'),(279,'Editing \"%s\"','admin','2012-12-14 08:29:31'),(280,'save and preview','admin','2012-12-14 08:29:32'),(281,'undo (auto)save','admin','2012-12-14 08:29:32'),(282,'delete taxon','admin','2012-12-14 08:29:32'),(283,'name and parent','admin','2012-12-14 08:29:32'),(284,'media','admin','2012-12-14 08:29:32'),(285,'literature','admin','2012-12-14 08:29:32'),(286,'synonyms','admin','2012-12-14 08:29:32'),(287,'common names','admin','2012-12-14 08:29:32'),(288,'(This page has not been published in this language. Click %shere%s to publish.)','admin','2012-12-14 08:29:32'),(289,'Below are all taxa in your project that are part of the species module. All higher taxa can be found in the %shigher taxa module%s.','admin','2012-12-14 08:29:59'),(290,'To edit a name, rank or parent, click the taxon\'s name. To edit a taxon\'s pages, click the percentage-indicator for that taxon in the \'content\' column. To edit media files, synoyms or common names, click the cell in the corresponding column.','admin','2012-12-14 08:29:59'),(291,'You can change the order of presentation of taxa on the same level - such as two genera - by moving taxa up- or downward by clicking the arrows.','admin','2012-12-14 08:29:59'),(292,'Rank','admin','2012-12-14 08:29:59'),(293,'images, videos, soundfiles','admin','2012-12-14 08:29:59'),(294,'Is being edited by:','admin','2012-12-14 08:29:59'),(295,'media files','admin','2012-12-14 08:29:59'),(296,'files','admin','2012-12-14 08:29:59'),(297,'Names','admin','2012-12-14 08:29:59'),(298,'move branch downward in the tree','admin','2012-12-14 08:29:59'),(299,'Add a new taxon','admin','2012-12-14 08:29:59'),(300,'Species module overview','admin','2012-12-14 08:30:02'),(301,'Editing taxa:','admin','2012-12-14 08:30:02'),(302,'Import taxon tree from file','admin','2012-12-14 08:30:02'),(303,'Import taxon tree from Catalogue Of Life (experimental)','admin','2012-12-14 08:30:02'),(304,'Orphans (taxa outside of the main taxon tree)','admin','2012-12-14 08:30:02'),(305,'Define taxonomic ranks','admin','2012-12-14 08:30:02'),(306,'Label taxonomic ranks','admin','2012-12-14 08:30:02'),(307,'Define categories','admin','2012-12-14 08:30:02'),(308,'Define sections','admin','2012-12-14 08:30:02'),(309,'Assign taxa to collaborators','admin','2012-12-14 08:30:02'),(310,'New taxon','admin','2012-12-14 08:30:04'),(311,'No parent','admin','2012-12-14 08:41:35'),(312,'Parent taxon: ','admin','2012-12-14 08:42:14'),(313,'Rank:','admin','2012-12-14 08:42:14'),(314,'Taxon name:','admin','2012-12-14 08:42:14'),(315,'Author:','admin','2012-12-14 08:42:14'),(316,'save and create another','admin','2012-12-14 08:42:14'),(317,'save and go to main taxon page','admin','2012-12-14 08:42:14'),(318,'That taxon cannot have child taxa.','admin','2012-12-14 08:43:35'),(319,'Taxonomic ranks','admin','2012-12-14 08:46:42'),(320,'Click the arrow next to a rank to add that rank to the selection used in this project. Currently selected ranks are shown on the right. To remove a rank from the selection, double click it in the list on the right. The uppermost rank, %s, is mandatory and cannot be deleted.','admin','2012-12-14 08:46:42'),(321,'Select all the ranks used in Catalogue Of Life, marked in blue in the list below','admin','2012-12-14 08:46:42'),(322,'After you have made the appropriate selection, click the save-button. \r\nOnce you have saved the selection, you can ','admin','2012-12-14 08:46:42'),(323,'change the ranks\' names and provide translations','admin','2012-12-14 08:46:42'),(324,'In addition, you can specify where the distinction between the modules \"higher taxa\" and \"species\" will be. \"Higher taxa\" are described concisely, whereas the \"species\" module allows for a comprehensive description for each taxon, including different categories, images, videos and sounds. Despite its name, the \"species module\" does not restrict comprehensive descriptions to the rank of species; rather, you yourself can specify what ranks are described in such a way. The red line in the list of selected ranks below symbolises the distinction. All ranks above the line fall under \"higher taxa\", those below it under the \"species module\". You can move the line by clicking the &uarr; and &darr; arrows. The setting is saved when you click','admin','2012-12-14 08:46:42'),(325,'save selected ranks','admin','2012-12-14 08:46:42'),(326,'Be advised that this \"border\" is different from the one that defines taxa of what ranks can be the end-point of your keys. That distinction is defined in the \"dichotomous key\" module. However, that distinction must be on the same level as the one you define here, or below it. It can never be higer up in the rank hierarchy.','admin','2012-12-14 08:46:42'),(327,'Please be advised:','admin','2012-12-14 08:46:42'),(328,'deleting previously defined ranks to which taxa already have been assigned will leave those taxa without rank.','admin','2012-12-14 08:46:42'),(329,'Ranks:','admin','2012-12-14 08:46:42'),(330,'Selected ranks','admin','2012-12-14 08:46:42'),(331,'(double click to delete)','admin','2012-12-14 08:46:42'),(332,'Ranks saved.','admin','2012-12-14 08:46:53'),(333,'name','admin','2012-12-14 09:44:23'),(334,'main page','admin','2012-12-14 09:44:25'),(335,'Orphaned taxa','admin','2012-12-14 11:14:46'),(336,'There are currently no orphaned taxa in your database.','admin','2012-12-14 11:14:46'),(337,'\"%s\" saved.','admin','2012-12-14 11:18:34'),(338,'(This page has been published in this language. Click %shere%s to unpublish.)','admin','2012-12-14 11:20:01'),(339,'System administration','admin','2012-12-14 11:37:53'),(340,'Select the project you wish to delete.','admin','2012-12-14 11:37:53'),(341,'select','admin','2012-12-14 11:37:53'),(342,'Linnaeus 2 import','admin','2012-12-14 11:38:11'),(343,'Choose file','admin','2012-12-14 11:38:11'),(344,'Creating project','admin','2012-12-14 11:40:49'),(345,'Could not create %s','admin','2012-12-14 11:40:50'),(652,'Collaborator data','admin','2013-01-03 11:43:35'),(653,'Username:','admin','2013-01-03 11:43:35'),(654,'Password:','admin','2013-01-03 11:43:35'),(348,'Import','admin','2012-12-14 11:41:14'),(658,'E-mail address:','admin','2013-01-03 11:43:35'),(659,'Timezone:','admin','2013-01-03 11:43:35'),(660,'Send e-mail notifications:','admin','2013-01-03 11:43:35'),(657,'Last name:','admin','2013-01-03 11:43:35'),(655,'Password (repeat):','admin','2013-01-03 11:43:35'),(656,'First name:','admin','2013-01-03 11:43:35'),(643,'Literature and glossary for \"%s\"','admin','2013-01-02 09:33:23'),(644,'Additional content for \"%s\"','admin','2013-01-02 09:35:13'),(645,'Keys for \"Nieuwe Flora van Nederland\"','admin','2013-01-02 09:39:35'),(646,'new taxon','admin','2013-01-03 09:45:19'),(647,'New higher taxon','admin','2013-01-03 09:51:21'),(648,'All users','admin','2013-01-03 11:40:20'),(649,'view','admin','2013-01-03 11:40:20'),(650,'remove','admin','2013-01-03 11:40:20'),(651,'Create new collaborator','admin','2013-01-03 11:43:35'),(632,'No variations have been defined for this taxon.','admin','2012-12-27 08:01:04'),(633,'author:','admin','2012-12-27 08:01:40'),(634,'Related taxa and variations for \"%s\"','admin','2012-12-27 09:45:13'),(635,'NBC extras','admin','2012-12-27 11:49:49'),(636,'Additional NBC data for \"%s\"','admin','2012-12-27 12:06:21'),(637,'Delete','admin','2012-12-27 13:23:45'),(638,'Matrixsleutel','app','2012-12-27 13:23:58'),(639,'Character','admin','2012-12-27 13:34:39'),(640,'Species and ranks for \"%s\"','admin','2013-01-02 09:29:19'),(641,'Save','admin','2013-01-02 09:29:31'),(642,'Additional species data for \"%s\"','admin','2013-01-02 09:32:07'),(625,'Map data for \"%s\"','admin','2012-12-21 11:22:03'),(626,'DELETION WILL BE IRREVERSIBLE.','admin','2012-12-21 13:21:37'),(627,'the variation','admin','2012-12-21 13:23:32'),(628,'Are you sure you want to delete the variation \"%s\"?','admin','2012-12-21 13:33:00'),(629,'related','admin','2012-12-21 14:25:30'),(630,'Unknown or no project ID.','app','2012-12-21 14:28:50'),(631,'Back to Linnaeus NG root','app','2012-12-21 14:28:50'),(375,'file','admin','2012-12-14 12:20:47'),(376,'Warning: \"%s\" does not exist.','admin','2012-12-14 13:43:46'),(377,'Taxon name already in database.','admin','2012-12-14 13:47:30'),(378,'Import data','admin','2012-12-17 07:28:27'),(379,'Data import options','admin','2012-12-17 07:28:31'),(380,'Import NBC Dierendeterminatie','admin','2012-12-17 07:29:26'),(381,'NBC Dierendeterminatie Import','admin','2012-12-17 07:43:54'),(382,'Parsed data example','admin','2012-12-17 11:41:37'),(672,'Common names','admin','2013-01-03 12:33:21'),(673,'Move','admin','2013-01-03 12:33:21'),(385,'Created project','admin','2012-12-17 12:05:31'),(386,'Select the standard modules you wish to use in your project:','admin','2012-12-17 12:51:17'),(387,'Besides these standard modules, you can add up to 5 extra content modules to your project:','admin','2012-12-17 12:51:17'),(388,'Enter new module\'s name:','admin','2012-12-17 12:51:17'),(389,'add module','admin','2012-12-17 12:51:17'),(390,'Module','admin','2012-12-17 12:51:17'),(391,'Actions','admin','2012-12-17 12:51:17'),(392,'part of the project','admin','2012-12-17 12:51:17'),(393,'not part of the project','admin','2012-12-17 12:51:18'),(394,'add','admin','2012-12-17 12:51:18'),(395,'Matrix','admin','2012-12-17 12:51:36'),(396,'New character','admin','2012-12-17 12:51:36'),(397,'New charcteristic for matrix \"%s\"','admin','2012-12-17 12:51:36'),(398,'Add the name and type of the charcteristic you want to add. The following types of charcteristics are available:','admin','2012-12-17 12:51:36'),(399,'text','admin','2012-12-17 12:51:36'),(400,'a textual description.','admin','2012-12-17 12:51:36'),(401,'an image, video or soundfile.','admin','2012-12-17 12:51:36'),(402,'range','admin','2012-12-17 12:51:36'),(403,'a value range, defined by a lowest and a highest value.','admin','2012-12-17 12:51:36'),(404,'distribution','admin','2012-12-17 12:51:36'),(405,'a value distribution, defined by a mean and values for one and two standard deviations.','admin','2012-12-17 12:51:36'),(406,'Characteristic name:','admin','2012-12-17 12:51:36'),(407,'Character type:','admin','2012-12-17 12:51:36'),(671,'Synonyms','admin','2013-01-03 12:33:21'),(670,'Literature','admin','2013-01-03 12:33:21'),(669,'Media','admin','2013-01-03 12:33:21'),(668,'Taxon','admin','2013-01-03 12:33:21'),(412,'Description','admin','2012-12-17 15:10:50'),(413,'Detailed Description','admin','2012-12-17 15:10:50'),(414,'Ecology','admin','2012-12-17 15:10:50'),(415,'Conservation','admin','2012-12-17 15:10:50'),(416,'Relevance','admin','2012-12-17 15:10:50'),(417,'Reproductive','admin','2012-12-17 15:10:50'),(418,'Each taxon page consists of one or more categories, with a maximum of %s. The first category, \'%s\', is mandatory.','admin','2012-12-17 15:11:00'),(419,'Below, you can specify the correct label of each category in the language or languages defined in your project. On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.','admin','2012-12-17 15:11:00'),(420,'Text you enter is automatically saved when you leave the input field.','admin','2012-12-17 15:11:00'),(421,'Category','admin','2012-12-17 15:11:00'),(422,'Add a new category:','admin','2012-12-17 15:11:00'),(674,'The name \"%s\" already exists.','admin','2013-01-03 12:37:56'),(667,'No common names have been defined for this taxon.','admin','2013-01-03 12:33:09'),(666,'Additional data for \"Chironomidae exuviae\"','admin','2013-01-03 12:32:28'),(665,'Keys for \"Chironomidae exuviae\"','admin','2013-01-03 12:31:29'),(664,'Username already exists.','admin','2013-01-03 11:43:37'),(663,'Select the modules that will be assigned to this collaborator','admin','2013-01-03 11:43:35'),(430,'Saving matrix data','admin','2012-12-18 12:23:24'),(662,'Active:','admin','2013-01-03 11:43:35'),(432,'Storing ranks, species and variations','admin','2012-12-18 12:40:41'),(433,'You have to define at least one language in your project before you can add any taxa.','admin','2012-12-18 12:40:56'),(434,'Define languages now.','admin','2012-12-18 12:40:56'),(661,'Role in current project:','admin','2013-01-03 11:43:35'),(436,'Common names for \"%s\"','admin','2012-12-18 12:45:38'),(437,'common name','admin','2012-12-18 12:45:38'),(438,'transliteration','admin','2012-12-18 12:45:38'),(439,'move up','admin','2012-12-18 12:45:38'),(440,'down','admin','2012-12-18 12:45:38'),(441,'Add a new common name:','admin','2012-12-18 12:45:38'),(442,'common name:','admin','2012-12-18 12:45:38'),(443,'transliteration:','admin','2012-12-18 12:45:38'),(444,'language:','admin','2012-12-18 12:45:38'),(445,'After you have added a new common name, you will be allowed to provide the name of its language in the various interface languages that your project uses.','admin','2012-12-18 12:45:38'),(446,'Project collaborator overview','admin','2012-12-18 12:46:38'),(447,'days','admin','2012-12-18 12:46:38'),(448,'first name','admin','2012-12-18 12:46:38'),(449,'last name','admin','2012-12-18 12:46:38'),(450,'username','admin','2012-12-18 12:46:38'),(451,'e-mail','admin','2012-12-18 12:46:38'),(452,'role','admin','2012-12-18 12:46:38'),(453,'last access','admin','2012-12-18 12:46:38'),(454,'Project collaborators','admin','2012-12-18 12:46:38'),(455,'All collaborators','admin','2012-12-18 12:46:38'),(456,'Create collaborator','admin','2012-12-18 12:46:38'),(457,'add new for \"%s\"','admin','2012-12-18 13:17:14'),(458,'Sort states of characteristic \"%s\".','admin','2012-12-18 13:22:49'),(459,'move down','admin','2012-12-18 13:22:49'),(460,'Editing character \"%s\"','admin','2012-12-18 13:24:46'),(461,'New state for \"%s\"','admin','2012-12-18 13:24:52'),(462,'Editing a state of the type \"%s\" for the character \"%s\" of matrix \"%s\".','admin','2012-12-18 13:24:52'),(463,'Name:','admin','2012-12-18 13:24:52'),(464,'Choose a file to upload:','admin','2012-12-18 13:24:52'),(465,'Allowed formats:','admin','2012-12-18 13:24:52'),(466,'%s','admin','2012-12-18 13:24:52'),(467,'max.','admin','2012-12-18 13:24:52'),(468,'per file','admin','2012-12-18 13:24:52'),(469,'save and return to matrix','admin','2012-12-18 13:24:52'),(470,'save and add another state for &quot;%s&quot;','admin','2012-12-18 13:24:53'),(471,'Editing state for \"%s\"','admin','2012-12-18 13:26:46'),(472,'A media file is required.','admin','2012-12-18 13:26:46'),(473,'characteristic','admin','2012-12-18 13:43:02'),(474,'State \"%s\" saved.','admin','2012-12-18 13:43:43'),(475,'Current image:','admin','2012-12-18 13:45:49'),(476,'Lower limit (inclusive):','admin','2012-12-18 13:53:09'),(477,'Upper limit (inclusive):','admin','2012-12-18 13:53:09'),(478,'Are you sure?','admin','2012-12-18 13:59:54'),(479,'edit character groups','admin','2012-12-18 14:28:36'),(480,'Search and replace','admin','2012-12-18 14:31:56'),(481,'Find','admin','2012-12-18 14:31:56'),(482,'Search for:','admin','2012-12-18 14:31:56'),(483,'Enclose multiple words with double quotes (\") to search for the literal string.','admin','2012-12-18 14:31:56'),(484,'In modules:','admin','2012-12-18 14:31:56'),(485,'Species','admin','2012-12-18 14:31:56'),(486,'Matrix key','admin','2012-12-18 14:31:56'),(487,'Replace','admin','2012-12-18 14:31:56'),(488,'Replace with:','admin','2012-12-18 14:31:56'),(489,'Do not enclose multiple words with double quotes, unless you want them as part of the actual replacement string.','admin','2012-12-18 14:31:56'),(490,'Replace options:','admin','2012-12-18 14:31:56'),(491,'Confirm per match','admin','2012-12-18 14:31:56'),(492,'Replace all without confirmation','admin','2012-12-18 14:31:56'),(493,'search','admin','2012-12-18 14:31:56'),(494,'Taxon-state links','admin','2012-12-19 07:38:12'),(495,'Viewing taxon-state links in the matrix \"%s\"','admin','2012-12-19 07:38:13'),(496,'view matrix','admin','2012-12-19 07:38:13'),(497,'Choose a taxon:','admin','2012-12-19 07:38:13'),(499,'State','admin','2012-12-19 07:38:13'),(500,'No links found.','admin','2012-12-19 07:38:13'),(501,'Adding taxa','admin','2012-12-19 07:38:16'),(502,'save and add another taxon','admin','2012-12-19 07:38:16'),(503,'Variation:','admin','2012-12-19 08:44:54'),(504,'Editing character \"%s\" for matrix \"%s\"','admin','2012-12-19 12:25:37'),(505,'Taxon added.','admin','2012-12-19 12:51:43'),(506,'Taxon to add:','admin','2012-12-19 14:23:13'),(507,'Variation to add:','admin','2012-12-19 14:23:13'),(508,'variations','admin','2012-12-19 14:58:27'),(509,'Synonyms for \"%s\"','admin','2012-12-19 15:04:00'),(510,'No synonyms have been defined for this taxon.','admin','2012-12-19 15:04:01'),(511,'Add a new synonym:','admin','2012-12-19 15:04:01'),(512,'synonym:','admin','2012-12-19 15:04:01'),(513,'Vartiations for \"%s\"','admin','2012-12-19 15:04:46'),(514,'synonym','admin','2012-12-19 15:10:15'),(515,'author','admin','2012-12-19 15:10:15'),(516,'variation','admin','2012-12-19 15:11:17'),(517,'Add a new variation:','admin','2012-12-19 15:12:45'),(518,'Variations for \"%s\"','admin','2012-12-19 15:14:54'),(519,'Editing %s \"%s\"','admin','2012-12-20 07:38:34'),(520,'Synonyms for %s \"%s\"','admin','2012-12-20 07:38:38'),(521,'Glossary terms','app','2012-12-20 08:20:43'),(522,'Glossary synonyms','app','2012-12-20 08:20:43'),(523,'Glossary media','app','2012-12-20 08:20:43'),(524,'Literary references','app','2012-12-20 08:20:43'),(525,'enter search term','app','2012-12-20 08:22:26'),(526,'The module \"%s\" is not part of your project.','admin','2012-12-20 08:25:28'),(527,'[syn.]','app','2012-12-20 08:47:32'),(528,'You can assign parts of the taxon tree to specific collaborator. If assigned, collaborators can only edit the assigned taxon, and all taxa beneath it in the taxon tree. If a collaborator has no taxa assigned to him, he can edit no taxa.','admin','2012-12-20 08:52:15'),(529,'You can assign multiple taxa to the same collaborator. However, if you assign different taxa that appear in the same branch of the taxon tree, the taxa highest up the same branch takes precedent.','admin','2012-12-20 08:52:15'),(530,'Assign taxon','admin','2012-12-20 08:52:15'),(531,'to user','admin','2012-12-20 08:52:15'),(532,'Current assignments:','admin','2012-12-20 08:52:15'),(533,'Collaborator','admin','2012-12-20 08:52:15'),(534,'Taxonomic ranks: labels','admin','2012-12-20 08:52:27'),(535,'Below, you can specify the correct label of each rank in the language or languages defined in your project.','admin','2012-12-20 08:52:27'),(536,'On the left hand side, the labels in the default language are displayed; on the right hand side, the labels in the other languages. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.','admin','2012-12-20 08:52:27'),(538,'Taxa list','app','2012-12-20 09:49:40'),(539,'Only species and below can contain spaces in their names.','admin','2012-12-20 11:22:47'),(540,'The name you specified contains invalid characters.','admin','2012-12-20 11:22:47'),(541,'The number of spaces in the name does not seem to match the selected rank.','admin','2012-12-20 11:23:58'),(542,'The number of spaces in the name does not match the selected rank.','admin','2012-12-20 11:44:59'),(543,'A %s should be linked to %s. This relationship is not enforced, so you can link to %s, but this may result in problems with the classification.','admin','2012-12-20 11:44:59'),(544,'\"%s\" cannot be selected as a parent for \"%s\".','admin','2012-12-20 11:44:59'),(545,'Markers are inserted automatically.','admin','2012-12-20 11:51:07'),(546,'save anyway','admin','2012-12-20 11:55:09'),(547,'The selected parent taxon can not have children.','admin','2012-12-20 12:28:28'),(548,'No taxon ID specified.','app','2012-12-20 12:46:41'),(554,'Index: species','admin','2012-12-20 14:03:35'),(555,'Higher Taxa','admin','2012-12-20 14:03:35'),(556,'Click to browse:','admin','2012-12-20 14:03:35'),(621,'identifier','admin','2012-12-21 08:10:00'),(622,'(as the original tags are in %s, they do not require translating)','admin','2012-12-21 08:14:35'),(623,'As the original tags are in %s, they do not require translating, but if you do specify a translation, it will overrule the original tag.','admin','2012-12-21 08:16:45'),(624,'Delete tag and all its translations!','admin','2012-12-21 08:26:32'),(676,'Please note that you can only delete taxa that have no children, in order to maintain a correct taxon structure in the species module.','admin','2013-01-03 13:19:33'),(677,'Password strength:','admin','2013-01-03 13:38:17'),(678,'(leave blank to leave unchanged)','admin','2013-01-03 13:38:18'),(679,'status','admin','2013-01-03 13:38:26'),(680,'Add collaborator','admin','2013-01-03 13:38:46'),(681,'Add user','admin','2013-01-03 13:38:46'),(682,'to project','admin','2013-01-03 13:38:46'),(683,'in the role of','admin','2013-01-03 13:38:46'),(684,'cancel','admin','2013-01-03 13:38:46'),(685,'Login failed.','admin','2013-01-03 13:42:38'),(686,'Taxon is already being edited by another editor.','admin','2013-01-03 13:43:31'),(687,'Editing literature \"%s (%s)\"','admin','2013-01-03 14:23:15'),(688,'Create new','admin','2013-01-03 14:23:23'),(689,'Number of authors:','admin','2013-01-03 14:23:23'),(690,'one','admin','2013-01-03 14:23:23'),(691,'two','admin','2013-01-03 14:23:23'),(692,'more','admin','2013-01-03 14:23:23'),(693,'et al.','admin','2013-01-03 14:23:23'),(694,'Year &amp; suffix (optional):','admin','2013-01-03 14:23:23'),(695,'Reference:','admin','2013-01-03 14:23:23'),(696,'Taxa this reference pertains to:','admin','2013-01-03 14:23:23'),(697,'Authors:','admin','2013-01-03 14:23:41'),(698,'That name already exists, albeit with a different parent.','admin','2013-01-08 08:52:46'),(699,'Rank cannot be hybrid.','admin','2013-01-08 09:22:26'),(700,'Hybrid:','admin','2013-01-08 10:58:35'),(701,'no hybrid','admin','2013-01-08 11:00:45'),(702,'interspecific hybrid','admin','2013-01-08 11:00:45'),(703,'intergeneric hybrid','admin','2013-01-08 11:00:45'),(704,'Password too short; should be between %s and %s characters.','admin','2013-01-08 14:03:45'),(705,'Below are your username and password for access to the Linnaeus NG administration:\nUsername: %s\nPassword: %s\n\nYou can access Linnaeus NG at:\n[[url]]','admin','2013-01-08 14:04:02'),(706,'<html>Below are your username and password for access to the Linnaeus NG administration:<br />\nUsername: %s<br />\nPassword: %s<br />\n<br />\nYou can access Linnaeus NG at:<br />\n<a href=\"[[url]]\">[[url]]</a>','admin','2013-01-08 14:04:02'),(707,'No matrices have been defined.','app','2013-01-09 12:58:44'),(708,'Synonyms','app','2013-01-10 07:40:22'),(709,'Taxon list','app','2013-01-10 07:47:03'),(710,'Back to','admin','2013-01-10 15:19:39'),(711,'Linnaeus NG root','admin','2013-01-10 15:19:39'),(712,'habitat','app','2013-01-11 08:03:52'),(713,'Text is required.','admin','2013-01-11 11:55:43'),(714,'Mean:','admin','2013-01-11 11:58:31'),(715,'Standard deviation:','admin','2013-01-11 11:58:31'),(716,'Using matrix \"%s\", function \"%s\"','app','2013-01-11 12:03:18'),(717,'switch to ','app','2013-01-11 12:03:18'),(718,'sort','app','2013-01-11 12:03:18'),(719,'add','app','2013-01-11 12:03:18'),(720,'delete','app','2013-01-11 12:03:18'),(721,'clear all','app','2013-01-11 12:03:18'),(722,'treat unknowns as matches:','app','2013-01-11 12:03:18'),(723,'Linnaeus NG root','app','2013-01-11 12:34:37'),(724,'Alphabet','app','2013-01-22 07:31:57'),(725,'Separation coefficient','app','2013-01-22 07:31:57'),(726,'Character type','app','2013-01-22 07:31:57'),(727,'Number of states','app','2013-01-22 07:31:57'),(728,'Entry order','app','2013-01-22 07:31:57'),(729,'Value:','app','2013-01-22 07:31:57'),(730,'ok','app','2013-01-22 07:31:57'),(731,'cancel','app','2013-01-22 07:31:57'),(732,'Number of allowed standard deviations:','app','2013-01-22 07:31:57'),(733,'lower: ','app','2013-01-22 07:38:25'),(734,'upper: ','app','2013-01-22 07:38:25'),(735,'Enter a value','app','2013-01-22 07:38:32'),(736,'Enter the required value for \"%s\":','app','2013-01-22 07:38:32'),(737,'mean: ','app','2013-01-22 07:44:00'),(738,'sd: ','app','2013-01-22 07:44:00'),(739,'Enter the required values for \"%s\":','app','2013-01-22 07:44:01'),(740,'Click %shere%s to specify a value; you can also double-click \"%s\" to do so.','app','2013-01-22 09:58:16'),(741,'Please enter a value','app','2013-01-22 10:20:02'),(742,'mean','app','2013-01-22 10:40:26'),(743,'sd','app','2013-01-22 10:40:26'),(744,'Next to','app','2013-01-23 11:34:11'),(745,'It\'s a text!','app','2013-01-23 12:37:13'),(746,'It\'s a plaatje?','app','2013-01-23 12:37:13'),(747,'It\'s a range...','app','2013-01-23 12:37:13'),(748,'It\'s a distribution!@#$','app','2013-01-23 12:37:13'),(749,'Whatever','app','2013-01-23 12:37:13'),(750,'Sort characters','admin','2013-01-24 12:24:31'),(751,'Previous to','app','2013-01-24 13:14:22'),(752,'synonym','app','2013-01-24 13:14:29'),(753,'of','app','2013-01-24 13:14:29'),(754,'Keys for \"Linnaeus II\"','admin','2013-01-24 13:17:42'),(755,'Additional data for \"Linnaeus II\"','admin','2013-01-24 13:17:48'),(756,'checklist','app','2013-01-24 13:23:16'),(757,'habitat: \"%s\"','app','2013-01-24 13:47:20'),(758,'checklist: \"%s\"','app','2013-01-24 13:47:22'),(759,': \"%s\"','app','2013-01-24 14:13:35'),(760,'3: \"%s\"','app','2013-01-24 14:18:43'),(761,'topic','app','2013-01-24 14:20:11'),(762,'4: \"%s\"','app','2013-01-24 14:23:22'),(763,'4:3','app','2013-01-24 14:28:20'),(764,'3:3','app','2013-01-24 14:28:20'),(765,'4:4','app','2013-01-24 14:28:23'),(766,'3:4','app','2013-01-24 14:28:23'),(767,'gelijkende soorten','app','2013-01-30 12:19:23'),(768,'Media for \"%s\"','admin','2013-02-01 10:13:16'),(769,'upload media','admin','2013-02-01 10:13:16'),(770,'Images','admin','2013-02-01 10:13:16'),(771,'Overview image','admin','2013-02-01 10:13:16'),(772,'Videos','admin','2013-02-01 10:13:16'),(773,'Sound','admin','2013-02-01 10:13:16'),(774,'New media for \"%s\"','admin','2013-02-01 10:13:18'),(775,'upload','admin','2013-02-01 10:13:18'),(776,'See current media for this taxon','admin','2013-02-01 10:13:18'),(777,'Allowed MIME-types','admin','2013-02-01 10:13:18'),(778,'Files of the following MIME-types are allowed:','admin','2013-02-01 10:13:18'),(779,'see below for information on uploading archives','admin','2013-02-01 10:13:18'),(780,'Overwriting and identical file names','admin','2013-02-01 10:13:18'),(781,'All uploaded files are assigned unique file names, so there is no danger of accidentally overwriting an existing file. The original file names are retained in the project database and shown in the media management screens.','admin','2013-02-01 10:13:18'),(782,'Uploading multiple files at once','admin','2013-02-01 10:13:18'),(783,'In the current HTML-specification there are no cross-broswer possibilities for the uploading of multiple files at once without resorting to Flash or Java. Despite this limitation, you can upload several images at once by adding them to a ZIP-archive and uploading that file. The application will unpack the ZIP-file and store the separate files contained within. To the files within a ZIP-file the same limitations with regards to format and size apply as to files that are uploaded normally.','admin','2013-02-01 10:13:18'),(784,'Group:','admin','2013-02-01 11:25:08'),(785,'Project info','admin','2013-02-01 11:27:48'),(786,'%s taxa, with:','admin','2013-02-01 11:27:48'),(787,'%s media files','admin','2013-02-01 11:27:48'),(788,'%s common names','admin','2013-02-01 11:27:48'),(789,'%s synonyms','admin','2013-02-01 11:27:48'),(790,'%s pages','admin','2013-02-01 11:27:48'),(791,'%s glossary entries','admin','2013-02-01 11:27:48'),(792,'%s literature references','admin','2013-02-01 11:27:48'),(793,' additional info modules with a total of %s pages','admin','2013-02-01 11:27:48'),(794,'%s steps in the dichtomous key','admin','2013-02-01 11:27:48'),(795,'%s matrix key(s)','admin','2013-02-01 11:27:48'),(796,'%s map items','admin','2013-02-01 11:27:48'),(797,'%s variations','admin','2013-02-01 11:30:31'),(798,'(van %s) objecten in huidige selectie','app','2013-02-01 11:39:46'),(799,'(deselecteer)','app','2013-02-01 15:17:27'),(800,'details','app','2013-02-05 09:39:53'),(801,'wis geselecteerde eigenschappen','app','2013-02-05 09:55:34'),(802,'wis geselecteerde kenmerken','app','2013-02-06 14:34:35'),(803,'terug','app','2013-02-07 10:59:45'),(804,'Gelijkende soorten van','app','2013-02-07 11:03:25'),(805,'Gelijkende soorten van %s','app','2013-02-07 11:03:49'),(806,'Zoekresultaten voor %s','app','2013-02-07 13:32:16'),(807,'Gebaseerd op','app','2013-02-08 12:00:57'),(808,'meer info','app','2013-02-08 12:00:57'),(809,'Editing page','admin','2013-02-08 13:53:24'),(810,'Change page order','admin','2013-02-08 13:53:24'),(811,'Topic:','admin','2013-02-08 13:53:24'),(812,'current image for this page:','admin','2013-02-08 13:53:24'),(813,'(click to delete image)','admin','2013-02-08 13:53:24'),(814,'Editing glossary term \"%s\"','admin','2013-02-08 14:00:42'),(815,'Term:','admin','2013-02-08 14:00:43'),(816,'Definition:','admin','2013-02-08 14:00:43'),(817,'Synonyms:','admin','2013-02-08 14:00:43'),(818,'(double-click a synonym to remove it from the list)','admin','2013-02-08 14:00:43'),(819,'Edit media files','admin','2013-02-08 14:00:43'),(820,'Update hotwords','admin','2013-02-08 14:02:02'),(821,'Browse all hotwords','admin','2013-02-08 14:02:02'),(822,'Browse hotwords','admin','2013-02-08 14:02:04'),(823,'All hotwords','admin','2013-02-08 14:02:04'),(824,'Update hotwords table','admin','2013-02-08 14:02:14'),(825,'Common Names','admin','2013-02-08 14:02:44'),(826,'contains the following taxa','app','2013-02-08 14:05:22'),(827,'step','app','2013-02-08 14:10:40'),(828,'Browse hotwords in ','admin','2013-02-08 14:14:46'),(829,'Sort characters by:','app','2013-02-11 09:32:27'),(830,'common name of %s','app','2013-02-12 12:12:37'),(831,'Zoek op naam','app','2013-02-12 15:00:39'),(832,'zoek','app','2013-02-12 15:00:39'),(833,'Zoek op kenmerken','app','2013-02-12 15:00:39'),(834,'alle kenmerken tonen','app','2013-02-12 15:05:56'),(835,'alle kenmerken verbergen','app','2013-02-12 15:13:53'),(836,'onderscheidende kenmerken','app','2013-02-12 15:14:42'),(837,'sluiten','app','2013-02-12 15:19:14'),(838,'Meer resultaten laden','app','2013-02-13 12:59:44'),(839,'meer resultaten laden','app','2013-02-13 12:59:51'),(840,'Kies een waarde tussen %s en %s%s.','app','2013-02-13 13:55:30'),(841,'male','app','2013-02-14 12:57:42'),(842,'female','app','2013-02-14 12:57:42'),(843,'distinctieve kenmerken','app','2013-02-15 07:29:14'),(844,'the page','admin','2013-02-19 10:48:47'),(845,'Keys for \"Orchids of New Guinea. Vol. III.\"','admin','2013-02-19 11:26:08'),(846,'Additional data for \"Orchids of New Guinea. Vol. III.\"','admin','2013-02-19 11:26:28'),(847,'Keys for \"-test project «Narwhal» (2013-02-19T15:17:49+01:00)\"','admin','2013-02-19 13:19:36'),(848,'Additional data for \"-test project «Narwhal» (2013-02-19T15:17:49+01:00)\"','admin','2013-02-19 13:19:44'),(849,'Keys for \"-test project «Shark» (2013-02-19T15:22:33+01:00)\"','admin','2013-02-19 13:25:27'),(850,'Additional data for \"-test project «Shark» (2013-02-19T15:22:33+01:00)\"','admin','2013-02-19 13:25:50'),(851,'Keys for \"Marine Mammals\"','admin','2013-02-19 14:47:08'),(852,'Additional data for \"Marine Mammals\"','admin','2013-02-19 14:49:15'),(853,'Keys for \"Orchids of New Guinea. Vol. VI\"','admin','2013-02-19 14:52:05'),(854,'Additional data for \"Orchids of New Guinea. Vol. VI\"','admin','2013-02-19 15:00:19'),(855,'Delete an orpahned project','admin','2013-02-20 07:21:46'),(856,'Delete an orphaned project','admin','2013-02-20 07:22:11'),(857,'Delete oprhaned project','admin','2013-02-20 07:27:57'),(858,'Delete orphaned project','admin','2013-02-20 07:40:26'),(859,'Delete orphaned data','admin','2013-02-20 07:42:23'),(860,'Merge other project','admin','2013-02-20 11:07:32'),(861,'Merge project','admin','2013-02-20 11:09:10'),(862,'Besides these standard modules, you can add up to  extra content modules to your project:','admin','2013-02-20 11:09:10'),(863,'Keys for \"Orchids of New Guinea Vol II\"','admin','2013-02-20 11:20:23'),(864,'Additional data for \"Orchids of New Guinea Vol II\"','admin','2013-02-20 11:20:33'),(865,'Keys for \"Orchids of New Guinea. Vol. IV\"','admin','2013-02-20 11:22:28'),(866,'Additional data for \"Orchids of New Guinea. Vol. IV\"','admin','2013-02-20 11:23:10'),(867,'Keys for \"Orchids of New Guinea. Vol. V\"','admin','2013-02-20 11:24:08'),(868,'Additional data for \"Orchids of New Guinea. Vol. V\"','admin','2013-02-20 11:24:19'),(869,'Select the project you wish to merge into the current project, \"%s\".','admin','2013-02-20 11:26:14'),(870,'Back','admin','2013-02-20 11:30:54'),(871,'You are about to merge the project \"%s\" into \"%s\".','admin','2013-02-20 11:34:37'),(872,'Do you wish to continue?','admin','2013-02-20 11:35:07'),(873,'merge','admin','2013-02-20 11:38:15'),(874,'add an image to this page','admin','2013-02-20 12:06:36'),(875,'Delete all hotwords','admin','2013-02-21 07:28:45'),(876,'Hotwords in %s module','admin','2013-02-21 07:32:54'),(877,'Referenced in the following taxa:','app','2013-02-21 08:22:45'),(878,'Additional data for \"Nieuwe Flora van Nederland\"','admin','2013-02-21 09:05:45'),(879,'Dichotome sleutel','app','2013-02-21 09:12:18'),(880,'Verspreiding','app','2013-02-21 09:12:18'),(881,'New glossary term','admin','2013-02-21 11:34:44'),(882,'Browsing glossary','admin','2013-02-21 11:35:32'),(883,'topic','admin','2013-02-25 08:13:10'),(884,'The image file for the map \"%s\" is missing.','app','2013-02-25 08:15:59'),(885,'Type locality','admin','2013-02-25 09:38:49'),(886,'Choose a species','admin','2013-02-25 09:38:49'),(887,'(no data)','admin','2013-02-25 09:38:51'),(888,'edit data','admin','2013-02-25 09:38:51'),(889,'Choose a species','app','2013-02-25 09:45:32'),(890,'Click a species to examine','app','2013-02-25 09:45:32'),(891,'species comparison','app','2013-02-25 09:45:32'),(892,' or ','app','2013-02-25 09:45:32'),(893,'map search','app','2013-02-25 09:45:32'),(894,'Taxon','app','2013-02-25 09:45:32'),(895,'Number of geo entries','app','2013-02-25 09:45:32'),(896,'previous','app','2013-02-25 09:45:32'),(897,'next','app','2013-02-25 09:45:32'),(898,'Select the desired option for each of the taxa listed below and press \'save\'.','admin','2013-02-25 10:11:37'),(899,'Attach to parent:','admin','2013-02-25 10:11:37'),(900,'Do nothing','admin','2013-02-25 10:11:37'),(901,'ok','admin','2013-02-27 08:46:25'),(902,'beide','app','2013-02-28 09:30:23'),(903,'man','app','2013-02-28 09:30:23'),(904,'vrouw','app','2013-02-28 09:30:23'),(905,'Import state labels for NBC Dierendeterminatie','admin','2013-02-28 09:45:57'),(906,'Parsed data','admin','2013-02-28 10:07:01'),(907,'Saving labels','admin','2013-02-28 10:50:20'),(908,'Could not resolve state \"%\" (%s).','admin','2013-02-28 11:27:04'),(909,'Could not resolve state \"%s\" (%s).','admin','2013-02-28 11:28:20'),(910,'Could not resolve state \"%s\" for %s.','admin','2013-02-28 11:58:05'),(911,'Skipped label for state \"%s\" for %s (no translation).','admin','2013-02-28 11:58:27'),(912,'Skipped state \"%s\" for %s (no translation).','admin','2013-02-28 12:06:24'),(913,'Updated image for \"%s\" to %s.','admin','2013-02-28 12:06:24'),(914,'Done.','admin','2013-02-28 12:06:24'),(915,'kb','admin','2013-02-28 12:59:50'),(916,'delete this image','admin','2013-02-28 12:59:50'),(917,'save description','admin','2013-02-28 12:59:50'),(918,'move image downward','admin','2013-02-28 12:59:50'),(919,'move image upward','admin','2013-02-28 12:59:50'),(920,'Saved: %s (%s)','admin','2013-02-28 13:00:20'),(921,'Boktorren determineren','app','2013-02-28 14:19:54'),(922,' van ','app','2013-02-28 14:29:11'),(923,'van %s','app','2013-03-06 12:03:53'),(924,'Merge other project into current','admin','2013-03-07 10:56:03'),(925,'Export project data to XML-file.','admin','2013-03-07 11:24:08'),(926,'export','admin','2013-03-07 11:24:08'),(927,'Images and other media files should be copied by hand, and are referenced in the export file by filename only. They can be found in the server folder:<br />','admin','2013-03-07 11:24:08'),(928,'Creating new page','admin','2013-03-07 11:54:00'),(929,'No species have been defined.','admin','2013-03-08 07:09:59'),(930,'No taxa have been defined.','admin','2013-03-08 07:10:03'),(931,'Insert internal link','admin','2013-03-08 07:14:36'),(932,'Content pages','admin','2013-03-08 07:14:36'),(933,'Page:','admin','2013-03-08 07:14:36'),(934,'Glossary alphabet','admin','2013-03-08 07:14:36'),(935,'Letter:','admin','2013-03-08 07:14:36'),(936,'Glossary term','admin','2013-03-08 07:14:36'),(937,'Literature index','admin','2013-03-08 07:14:37'),(938,'Literature alphabet','admin','2013-03-08 07:14:37'),(939,'Literature reference','admin','2013-03-08 07:14:37'),(940,'Species module index','admin','2013-03-08 07:14:37'),(941,'Species module detail','admin','2013-03-08 07:14:37'),(942,'Species:','admin','2013-03-08 07:14:37'),(943,'Category:','admin','2013-03-08 07:14:37'),(944,'Higher taxa index','admin','2013-03-08 07:14:37'),(945,'Higher taxa detail','admin','2013-03-08 07:14:37'),(946,'Taxa:','admin','2013-03-08 07:14:37'),(947,'Dichotomous key','admin','2013-03-08 07:14:37'),(948,'Distribution index','admin','2013-03-08 07:14:37'),(949,'Distribution detail','admin','2013-03-08 07:14:37'),(950,'Element:','admin','2013-03-08 07:14:37'),(951,'Test index','admin','2013-03-08 07:14:37'),(952,'Test topic','admin','2013-03-08 07:14:37'),(953,'Vnurk index','admin','2013-03-08 07:14:37'),(954,'Vnurk topic','admin','2013-03-08 07:14:37'),(955,'Insert a link to:','admin','2013-03-08 07:14:37'),(956,'Module:','admin','2013-03-08 07:14:37'),(957,'insert link','admin','2013-03-08 07:14:37'),(958,'New image','admin','2013-03-08 07:14:48'),(959,'Edit data for \"%s\"','admin','2013-03-08 07:22:26'),(960,'copy','admin','2013-03-08 07:22:27'),(961,'reset','admin','2013-03-08 07:22:27'),(962,'clear','admin','2013-03-08 07:22:27'),(963,'Data types','admin','2013-03-08 07:22:31'),(964,'Set runtime map type','admin','2013-03-08 07:22:31'),(965,'Store compacted data for Linnaeus 2 maps (for runtime performance purposes)','admin','2013-03-08 07:22:31'),(966,'Below, you can define up to ten types of geographically organised data. Once defined, you can specify locations on the map for each species, for every data type.','admin','2013-03-08 07:22:33'),(967,'Add a new data type:','admin','2013-03-08 07:22:33'),(968,'Create new project','admin','2013-03-08 07:53:51'),(969,'Enter the project\'s name, description and version below, and click \'save\' to create the project.','admin','2013-03-08 07:53:51'),(970,'Project name:','admin','2013-03-08 07:53:51'),(971,'Project version:','admin','2013-03-08 07:53:51'),(972,'Project description:','admin','2013-03-08 07:53:51'),(973,'(for reference only)','admin','2013-03-08 07:53:51'),(974,'Default project languages:','admin','2013-03-08 07:53:51'),(975,'(you can change this later)','admin','2013-03-08 07:53:51'),(976,'As system administrator, you will automatically be made system administrator of the new project. In that capacity, you will be able to create users, add modules and execute other administrative tasks for the newly created project.','admin','2013-03-08 07:53:51'),(977,'Project \'%s\' saved.','admin','2013-03-08 07:54:00'),(978,'%sAdministrate the new project.%s','admin','2013-03-08 07:54:00'),(979,'previous','admin','2013-03-08 07:56:45'),(980,'next','admin','2013-03-08 07:56:45'),(981,'(no terms have been defined)','admin','2013-03-08 08:14:53'),(982,'New reference','admin','2013-03-08 08:15:23'),(983,'Browsing literature','admin','2013-03-08 08:15:27'),(984,'(no references have been defined)','admin','2013-03-08 08:15:27'),(985,'(subsection)','admin','2013-03-08 08:20:11'),(986,'Keypath (subsection)','admin','2013-03-08 08:20:11'),(987,'Full subsection keypath:','admin','2013-03-08 08:20:11'),(988,'Below is a graphic representation of your key. Click a node to see the steps that follow from it. Click and drag to move the entire tree.','admin','2013-03-08 08:20:25'),(989,'Click to see step \"%s\"','admin','2013-03-08 08:20:27'),(990,'Key sections','admin','2013-03-08 08:20:41'),(991,'\"Key sections\" are parts of the dichotomous key that are not connected to the entire key. Put differently, they are steps that are not the starting step of your key, nor the target of any choice in another step. By creating sections, different collaborators can work on specific parts of the key, which are later hooked up to the main key.','admin','2013-03-08 08:20:42'),(992,'Available sections (click to edit):','admin','2013-03-08 08:20:42'),(993,'Start a new subsection','admin','2013-03-08 08:20:42'),(994,'delete all images','admin','2013-03-08 08:20:44'),(995,'(show all)','admin','2013-03-08 08:20:44'),(996,'You need to process and store your key tree to see the list of possible outcomes.','admin','2013-03-08 08:20:44'),(997,'Could not create page.','admin','2013-03-08 08:32:35'),(998,'Data for \"%s\"','admin','2013-03-08 10:37:20'),(999,'Selection type:','admin','2013-03-08 10:37:20'),(1000,'Coordinates:','admin','2013-03-08 10:37:20'),(1001,'Select the type of data you are drawing on the map:','admin','2013-03-08 10:37:20'),(1002,'(%sadd or change datatypes.%s)','admin','2013-03-08 10:37:20'),(1003,'To enable setting markers (points on the map), click the button below.','admin','2013-03-08 10:37:20'),(1004,'Then click on the appropriate spot on the map to place a marker. To remove a marker, right-click on it.','admin','2013-03-08 10:37:20'),(1005,'To enable drawing polygons, click the button below.','admin','2013-03-08 10:37:20'),(1006,'Then draw the polygon by clicking the appropriate spots on the map. When finished drawing, click the button again. To remove a polygon, right-click on it.','admin','2013-03-08 10:37:20'),(1007,'When you are done, click \'save\' to store all occurrences.','admin','2013-03-08 10:37:20'),(1008,'data type','admin','2013-03-08 10:38:29'),(1009,'Linnaeus 2 maps','admin','2013-03-08 11:22:02'),(1010,'\"%s\"','admin','2013-03-08 11:22:21'),(1011,'Switch to another map:','admin','2013-03-08 11:22:21'),(1012,'editable map','admin','2013-03-08 11:22:21'),(1013,'copy data','admin','2013-03-08 11:22:23'),(1014,'Set the type of map that will appear in the runtime interface:','admin','2013-03-08 11:22:28'),(1015,'E','app','2013-03-08 11:22:42'),(1016,'N','app','2013-03-08 11:22:42'),(1017,'clear map','admin','2013-03-08 13:15:14'),(1018,'Store compacted Linnaeus 2 data','admin','2013-03-08 13:49:55'),(1019,'Click the button below to have the system store the Linnaeus 2 map data in a more compact form.','admin','2013-03-08 13:49:55'),(1020,'Please note that, depending on the size of your data, this might take a few minutes.','admin','2013-03-08 13:49:55'),(1021,'store compacted data','admin','2013-03-08 13:49:55'),(1022,'Compacted data saved','admin','2013-03-08 13:50:00'),(1023,'W','app','2013-03-08 13:51:20'),(1024,'S','app','2013-03-08 13:51:20'),(1025,'Copy occurrences from \"%s\"','admin','2013-03-08 13:58:47'),(1026,'Choose the species you want to copy the map data of \"%s\" to:','admin','2013-03-08 13:58:47'),(1027,'Editing glossary term','admin','2013-03-11 08:16:44'),(1028,'Deleting taxon \"%s\"','admin','2013-03-11 10:10:17'),(1029,'You are about to delete the taxon \"%s\", which has child taxa connected to it. Please specify what should happen to the connected child taxa. There are three possibilities:','admin','2013-03-11 10:10:17'),(1030,'Orphans:','admin','2013-03-11 10:10:17'),(1031,'turn them into \"orphans\". Orphans are taxa that are unconnected to the main taxon tree. You will need to individually reattach them later.','admin','2013-03-11 10:10:17'),(1032,'Delete:','admin','2013-03-11 10:10:17'),(1033,'delete them as well. Effectively this will delete the entire branch from taxon \"%s\" and down.','admin','2013-03-11 10:10:17'),(1034,'Attach:','admin','2013-03-11 10:10:17'),(1035,'attach them as child to the parent of \"%s\", which is the %s \"%s\". There will be no change in the rank of the reattached taxa.','admin','2013-03-11 10:10:17'),(1036,'Orphan','admin','2013-03-11 10:10:17'),(1037,'Attach to','admin','2013-03-11 10:10:17'),(1038,'move branch upward in the tree','admin','2013-03-11 11:56:58'),(1039,'(orphan)','admin','2013-03-11 14:56:30'),(1040,'No taxa have been assigned to you.','admin','2013-03-11 15:00:27'),(1041,'Nederlands Soortenregister','app','2013-03-13 14:32:05'),(1042,'Change a project ID','admin','2013-03-13 14:49:42'),(1043,'Select the project of which you wish to change the ID.','admin','2013-03-13 14:53:58'),(1044,'Select the project of which you wish to change the ID and enter the new ID.','admin','2013-03-13 14:55:07'),(1045,'A project with ID %s already exists (%s).','admin','2013-03-13 15:04:09'),(1046,'No parent selected (you can still save).','admin','2013-03-14 10:05:46'),(1047,'Dichotomous key steps','app','2013-03-19 12:39:28'),(1048,'Dichotomous key choices','app','2013-03-19 12:39:28'),(1049,'Matrix key matrices','app','2013-03-19 12:39:28'),(1050,'Matrix key characters','app','2013-03-19 12:39:28'),(1051,'Matrix key states','app','2013-03-19 12:39:28'),(1052,'Navigator','app','2013-03-19 12:39:28'),(1053,'geographical data','app','2013-03-19 12:39:28'),(1054,'Your search for \"%s\" produced %s results.','app','2013-03-19 12:39:28'),(1055,'Expand all','app','2013-03-19 12:39:28'),(1056,'and','app','2013-03-19 12:39:28'),(1057,'Taxon:','app','2013-03-19 12:39:28'),(1058,'in','app','2013-03-19 12:39:30'),(1059,'It is not possible to jump directly to a specific step or choice of the dichotomous key','app','2013-03-19 12:39:30'),(1060,'%sStart the key from the start%s.','app','2013-03-19 12:39:30'),(1061,'choice','app','2013-03-19 12:39:30'),(1062,'search results','app','2013-03-19 14:22:14'),(1063,'Store key tree','admin','2013-03-19 14:50:36'),(1064,'Click the button below to have the system store a tree-structured representation of the key, required for runtime purposes.','admin','2013-03-19 14:50:36'),(1065,'Please note that, depending on the size of your key, this might take a few minutes.','admin','2013-03-19 14:50:36'),(1066,'store key tree','admin','2013-03-19 14:50:36'),(1067,'Key tree saved','admin','2013-03-19 14:50:38'),(1068,'Literatuur','app','2013-03-20 10:03:49'),(1069,'Short name for URL:','admin','2013-03-20 10:30:45'),(1070,'Unknown project or invalid project ID.','app','2013-03-20 10:39:16'),(1071,'Below is a list of steps without any choices. To edit, click the name the step.','admin','2013-03-20 14:57:06'),(1072,'Below is a list of steps with only one choice. To edit, click the name the step.','admin','2013-03-20 14:57:06'),(1073,'Below is a list of unconnected choices, i.e. those that do not lead to another step or a taxon. To edit, click the name of either the step or the choice.','admin','2013-03-20 14:57:06'),(1074,'choice','admin','2013-03-20 14:57:06'),(1075,'Taxon:','admin','2013-03-21 07:36:42'),(1076,'step','admin','2013-03-21 08:11:40'),(1077,'Taxon ranks in key','admin','2013-03-21 08:29:49'),(1078,'Below, you can define taxa of what rank or ranks will be part of your dichotomous key.','admin','2013-03-21 08:29:50'),(1079,'The taxa that are of a rank below the red line in the list below are available in your key. To change the selection, move the red line up or down by clicking the &uarr; and &darr; arrows. To include all ranks, move the line to the top of the list, above the first rank. As at least one rank is required to be included, the line cannot be moved below the lowest rank. When you are satisfied with your selection, click the save-button.','admin','2013-03-21 08:29:50'),(1080,'Please note that changing this setting will not detach any taxa that have already been attached to an end-point of your key. Taxa that have a rank that is no longer part of the selection below will remain connected to the key, until you manually detach them.','admin','2013-03-21 08:29:50'),(1081,'Saved.','admin','2013-03-21 08:29:54'),(1082,'Below is a list of taxa that are not yet part of your key:','admin','2013-03-21 10:33:01'),(1083,'No key sections are available.','admin','2013-03-21 11:41:58'),(1084,'Gebaseerd op:','app','2013-03-22 07:34:26'),(1085,'Betekenis iconen:','app','2013-03-22 10:06:19'),(1086,'upload and parse','admin','2013-03-26 08:25:31'),(1087,'CSV data import','admin','2013-03-26 11:27:01'),(1088,'import','admin','2013-03-26 13:32:08'),(1089,'Saved page \"%s\".','admin','2013-03-26 14:10:34'),(1090,'Diergroepteksten: \"%s\"','app','2013-03-26 14:19:32'),(1091,'Found no states for \"%s\"','admin','2013-03-27 11:11:57'),(1092,'Click to edit taxon \"%s\"','admin','2013-03-27 12:11:46'),(1093,'Matrix key index','admin','2013-03-27 12:37:57'),(1094,'Matrix keys','admin','2013-03-27 12:37:57'),(1095,'Introduction','admin','2013-03-27 12:42:22'),(1096,'Glossary','admin','2013-03-27 12:42:22'),(1097,'Navigator','admin','2013-03-27 12:42:22'),(1098,'Step:','admin','2013-03-27 12:45:14'),(1099,'Classification','admin','2013-03-27 13:15:42'),(1100,'Could not resolve similar id \"%s\"','admin','2013-03-27 14:54:41'),(1101,'Project \"%\" not found in the database.','admin','2013-03-28 14:28:04'),(1102,'Import halted.','admin','2013-03-28 14:28:04'),(1103,'Skipped image for \"%s\" (not specified).','admin','2013-03-28 14:33:03'),(1104,'Could not resolve character \"%\".','admin','2013-03-28 14:33:03'),(1105,'(not in any group)','admin','2013-04-02 08:02:13'),(1106,'not in any group:','admin','2013-04-02 08:05:43'),(1107,'not in any group','admin','2013-04-02 09:31:04'),(1108,'A group named \"%s\" already exists.','admin','2013-04-02 11:51:34'),(1109,'delete group','admin','2013-04-02 11:59:21'),(1110,'Save and finish import','admin','2013-04-03 05:26:10'),(1111,'language','admin','2013-04-03 10:23:29'),(1112,'reacquire state image dimensions','admin','2013-04-04 07:46:19'),(1113,'Updated states for \"%s\".','admin','2013-04-04 07:56:22'),(1114,'Settings','admin','2013-04-04 11:58:05'),(1115,'A setting with the name \"%s\" alreasy exists.','admin','2013-04-04 12:29:29'),(1116,'A setting with the name \"%s\" already exists.','admin','2013-04-04 12:32:06'),(1117,'A value is required for \"%s\".','admin','2013-04-04 12:32:10'),(1118,'Project data','admin','2013-04-05 05:14:06'),(1119,'eds','admin','2013-04-05 06:10:16'),(1120,'Edit project collaborator','admin','2013-04-09 07:33:15'),(1121,'(has never worked on project)','admin','2013-04-09 07:33:15'),(1122,'Project role:','admin','2013-04-09 07:33:15'),(1123,'Active','admin','2013-04-09 07:33:15'),(1124,'Select the modules that will be assigned to this collaborator:','admin','2013-04-09 07:33:15'),(1125,'Taxon file upload','admin','2013-04-10 07:56:17'),(1126,'CSV field delimiter:','admin','2013-04-10 07:56:17'),(1127,'(comma)','admin','2013-04-10 07:56:17'),(1128,'(semi-colon)','admin','2013-04-10 07:56:17'),(1129,'tab stop','admin','2013-04-10 07:56:17'),(1130,'To load a list of taxa from file, click the \'browse\'-button above, select the file to load from your computer and click \'upload\'.\r\nThe contents of the file will be displayed so you can review them before they are saved to your project\'s database.','admin','2013-04-10 07:56:17'),(1131,'The file must meet the following conditions:','admin','2013-04-10 07:56:17'),(1132,'The format needs to be CSV.','admin','2013-04-10 07:56:17'),(1133,'The field delimiter must be a comma, semi-colon or tab stop, and can be selected above.','admin','2013-04-10 07:56:17'),(1134,'The fields in the CSV-file *may* be enclosed by \" (double-quotes), but this is not mandatory.','admin','2013-04-10 07:56:17'),(1135,'There should be one taxon per line. No header line should be present.','admin','2013-04-10 07:56:17'),(1136,'Each taxon consists of the following fields:','admin','2013-04-10 07:56:17'),(1137,'Taxon name','admin','2013-04-10 07:56:17'),(1138,'Taxon rank','admin','2013-04-10 07:56:17'),(1139,'in that order. The first two are mandatory. ','admin','2013-04-10 07:56:17'),(1140,'Ranks should match the list of ranks you have selected for your project.','admin','2013-04-10 07:56:17'),(1141,'These currently are:','admin','2013-04-10 07:56:17'),(1142,'Taxa with a rank that does not appear in this list will not be loaded.','admin','2013-04-10 07:56:17'),(1143,'Hybrids are only possible for the following ranks:','admin','2013-04-10 07:56:17'),(1144,'Parent-child relations are assumed top-down, one branch at a time. For instance, loading:','admin','2013-04-10 07:56:17'),(1145,'in this order will correctly maintain the relations between Genus, Species and Infraspecies.','admin','2013-04-10 07:56:17'),(1146,'Download a sample CSV-file','admin','2013-04-10 07:56:17'),(1147,'Import taxon content from file','admin','2013-04-10 07:57:22'),(1148,'To load taxa content from file, click the \'browse\'-button above, select the file to load from your computer and click \'upload\'.','admin','2013-04-10 08:03:22'),(1149,'Each line consists of the following fields:','admin','2013-04-10 08:03:22'),(1150,'Taxon ID (currently there is no automated lookup - sorry)','admin','2013-04-10 08:03:22'),(1151,'Language ID','admin','2013-04-10 08:03:22'),(1152,'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, and will overwrite any existant data for that combination of taxon and category.','admin','2013-04-10 08:03:22'),(1153,'The first two fields are mandatory, all fields are expected in the order displayed above.','admin','2013-04-10 08:04:25'),(1154,'The first line should contain the field headers:','admin','2013-04-10 08:05:09'),(1155,'Taxon ID: optional, program explicitly expects the first column to be the taxon ID.','admin','2013-04-10 08:06:33'),(1156,'Language ID: optional, program explicitly expects the first column to be the taxon ID.','admin','2013-04-10 08:06:33'),(1157,'The content columns should have the system names of the corresponding categories in your project. Currently, these are:','admin','2013-04-10 08:06:33'),(1158,'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existant data for that combination of taxon and category will be overwritten.','admin','2013-04-10 08:12:29'),(1159,'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existent data for that combination of taxon and category will be overwritten without warning.','admin','2013-04-10 08:14:53'),(1160,'The content column headers should contain the system names of the corresponding categories in your project. Currently, these are:','admin','2013-04-10 08:14:53'),(1161,'Unknown rank','admin','2013-04-10 08:20:42'),(1162,'Uppermost taxon is not a %s, and has a rank that has no immediate parent.','admin','2013-04-10 08:20:43'),(1163,'The field delimiter must be a comma.','admin','2013-04-10 09:27:31'),(1164,'saved (could not save certain HTML-tags)','admin','2013-04-10 10:45:59'),(1165,'Species module: \"%s\"','app','2013-04-10 11:06:00'),(1166,'Higher taxa: \"%s\"','app','2013-04-10 11:11:13'),(1167,'Keys for \"Euphausiids of the World Ocean\"','admin','2013-04-11 12:10:28'),(1168,'Additional data for \"Euphausiids of the World Ocean\"','admin','2013-04-11 12:11:42'),(1169,'Set runtime key type','admin','2013-04-11 12:20:02'),(1170,'Set the type of key that will appear in the runtime interface:','admin','2013-04-11 12:20:02'),(1171,'Distribution','admin','2013-04-11 12:20:06'),(1172,'Insufficient data.','admin','2013-04-11 12:26:55'),(1173,'One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existant data for that combination of taxon and category will be overwritten without warning.','admin','2013-04-12 12:39:40'),(1174,'Keys for \"Dagvlinders van Europa\"','admin','2013-04-16 05:12:44'),(1175,'Additional data for \"Dagvlinders van Europa\"','admin','2013-04-16 05:13:21'),(1176,'Keys for \"-test project «Oryx» (2013-04-16T09:45:24+02:00)\"','admin','2013-04-16 05:46:04'),(1177,'Keys for \"-test project «Lobster» (2013-04-16T10:03:38+02:00)\"','admin','2013-04-16 06:04:37'),(1178,'Keys for \"-test project «Eagle» (2013-04-16T10:07:51+02:00)\"','admin','2013-04-16 06:08:36'),(1179,'Choose a matrix to use','app','2013-04-16 08:28:52'),(1180,'Keys for \"-test project «Wasp» (2013-04-16T14:13:00+02:00)\"','admin','2013-04-16 10:13:54'),(1181,'Additional data for \"-test project «Wasp» (2013-04-16T14:13:00+02:00)\"','admin','2013-04-16 10:15:07'),(1182,'A description is required.','admin','2013-09-24 08:25:51'),(1183,'Mass upload images','admin','2013-09-24 08:26:14'),(1184,'Clear cache','admin','2013-09-24 08:26:14'),(1185,'Generic export','admin','2013-09-24 08:26:14'),(1186,'Export multi-entry key for Linnaeus Mobile','admin','2013-09-24 08:26:14'),(1187,'Manage basic project information','admin','2013-09-24 08:26:20'),(1188,'Manage project modules','admin','2013-09-24 08:26:20'),(1189,'Manage system settings','admin','2013-09-24 08:26:20'),(1190,'Entity count for current project','admin','2013-09-24 08:26:20'),(1191,'Merge other project into current project','admin','2013-09-24 08:26:20'),(1192,'Import remote images from file','admin','2013-09-25 07:40:08'),(1193,'List all synonyms','admin','2013-09-25 07:40:08'),(1194,'List all common names','admin','2013-09-25 07:40:08'),(1195,'Literature v2','app','2013-10-08 13:59:25'),(1196,'Your search for \"%s\" produced no results.','app','2013-10-09 08:45:16'),(1197,'Extensive search','admin','2013-10-09 09:16:47'),(1198,'Literature v2','admin','2013-10-09 09:16:47'),(1199,'Species names','admin','2013-10-09 09:17:31'),(1200,'Species descriptions','admin','2013-10-09 09:17:31'),(1201,'Species synonyms','admin','2013-10-09 09:17:31'),(1202,'Species common names','admin','2013-10-09 09:17:31'),(1203,'Species media','admin','2013-10-09 09:17:31'),(1204,'Collapse all','app','2013-10-09 10:07:54'),(1205,'Informatie op Nederlands Soortenregister','app','2013-10-09 10:18:11'),(1206,'opnieuw beginnen','app','2013-10-09 10:18:29'),(1207,'No taxon ID specified.','admin','2013-10-10 07:58:09'),(1208,'Higher taxa list','admin','2013-10-10 07:58:30'),(1209,'Data import','admin','2013-10-10 11:18:33'),(1210,'Switch to higher taxa list','admin','2013-10-10 14:40:38'),(1211,'Is being edited by','admin','2013-10-10 14:40:38'),(1212,'Are you sure you want to permanently sort the taxa alphabetically per taxonomic level?','admin','2013-10-10 14:54:51'),(1213,'Clear project cache','admin','2013-10-11 10:44:55'),(1214,'Taxon names','app','2013-10-11 12:02:07'),(1215,'Welcome, %s.','admin','2013-10-23 08:00:27'),(1216,'Datatypes','app','2013-10-23 09:57:28'),(1217,'Steps','app','2013-10-23 09:57:28'),(1218,'Choices','app','2013-10-23 09:57:28'),(1219,'Items','app','2013-10-23 09:57:28'),(1220,'Vernaculars','app','2013-10-23 11:42:50'),(1221,'Import taxon tree from Catalogue Of Life','admin','2013-11-06 13:03:24'),(1222,'DNA barcodes','app','2013-11-06 13:39:48'),(1223,'No or unknown taxon ID specified.','app','2014-01-08 10:21:32'),(1224,'No or illegal taxon ID specified.','app','2014-01-22 12:58:25'),(1225,'Nomenclature','app','2014-01-23 11:20:57'),(1226,'No or illegal project ID specified.','app','2014-01-24 15:28:43'),(1229,'Article','app','2014-02-10 10:28:42'),(1230,'Book','app','2014-02-10 10:28:42'),(1231,'Report','app','2014-02-10 10:28:42'),(1232,'BookPart','app','2014-02-10 10:28:42'),(1233,'SerialWork','app','2014-02-10 10:28:42'),(1234,'manuscript','app','2014-02-10 10:29:42'),(1235,'PressRelease','app','2014-02-10 10:29:42'),(1236,'Periodical','app','2014-02-10 10:29:42'),(1237,'Chapter','app','2014-02-10 10:29:42'),(1312,'User can only be deleted by system admin.','admin','2014-12-16 08:45:49'),(1239,'pagina:','app','2014-02-28 10:48:06'),(1240,'Naamgeving','app','2014-03-05 10:20:07'),(1241,'geen','app','2014-03-05 15:44:27'),(1242,'soorten in totaal','app','2014-03-10 15:39:43'),(1243,'inheems','app','2014-03-10 15:39:43'),(1244,'Status voorkomen:','app','2014-03-11 08:55:29'),(1245,'Niets gevonden.','app','2014-03-14 08:31:18'),(1246,'gevestigd','app','2014-03-18 15:08:48'),(1247,'Complete export for Linnaeus Mobile','admin','2014-03-25 16:07:50'),(1248,'save new order','admin','2014-03-25 16:11:57'),(1249,'taxonomy','admin','2014-03-26 09:26:35'),(1250,'names','admin','2014-03-26 09:26:35'),(1251,'pages','admin','2014-03-26 09:26:35'),(1252,'Aantal soorten in Nederland','app','2014-03-28 13:03:45'),(1253,'Aantal soorten in Nederland met status voorkomen 1, 1a, 2, 2a of 2b','app','2014-03-28 13:05:08'),(1254,'Soorten met foto\'s','app','2014-03-28 13:06:57'),(1255,'Aantal soorten in het Soortenregister met één of meer foto\'s','app','2014-03-28 13:06:57'),(1256,'Totaal aantal soortfoto\'s in het Soortenregister','app','2014-03-28 13:07:37'),(1257,'Foto\'s','app','2014-03-28 13:09:37'),(1258,'Geaccepteerde soortnamen','app','2014-03-28 13:09:37'),(1259,'Nederlandse namen','app','2014-03-28 13:09:56'),(1260,'	Engelse namen','app','2014-03-28 13:09:56'),(1261,'Engelse namen','app','2014-03-28 13:10:01'),(1262,'Literatuurbronnen','app','2014-03-28 13:11:30'),(1263,'Specialisten','app','2014-03-28 13:23:27'),(1264,'Aantal soorten in Nederland met status voorkomen 1, 1a, 2, 2a of 2b.','app','2014-03-28 13:30:17'),(1265,'No ranks have been defined.','admin','2014-04-04 07:48:01'),(1266,'Content not found.','admin','2014-04-04 07:52:45'),(1267,'Bekijk alle gegevens','app','2014-04-08 09:26:33'),(1268,'Meer recente afbeeldingen','app','2014-04-08 09:26:33'),(1269,'Locatie','app','2014-04-08 09:26:33'),(1270,'Fotograaf','app','2014-04-08 09:26:33'),(1271,'Validator','app','2014-04-08 09:26:33'),(1272,'Datum plaatsing','app','2014-04-08 09:26:33'),(1273,'Generate parentage table','admin','2014-04-10 12:05:56'),(1274,'Find species:','admin','2014-04-17 13:31:59'),(1275,'New taxon concept','admin','2014-04-23 09:35:01'),(1276,'Names for \"%s\"','admin','2014-04-23 12:58:16'),(1277,'Create new reference','admin','2014-04-24 09:29:05'),(1278,'Browse references','admin','2014-04-24 09:29:05'),(1279,'Search references','admin','2014-04-24 09:29:05'),(1280,'Edit literature','admin','2014-04-24 12:57:54'),(1281,'Edit reference','admin','2014-04-24 13:01:17'),(1282,'Find taxa:','admin','2014-04-25 08:45:05'),(1283,'Verspreidingskaarten','app','2014-04-29 08:17:53'),(1284,'Trendgrafieken','app','2014-04-29 09:10:56'),(1285,'1','app','2014-10-12 11:27:59'),(1286,'Edit taxon concept','admin','2014-11-11 12:30:20'),(1287,'Activity log','admin','2014-11-11 12:30:27'),(1288,'Bewerk wetenschappelijke naam','admin','2014-11-11 16:18:31'),(1289,'Bewerk name','admin','2014-11-12 09:04:14'),(1290,'Close','admin','2014-11-12 09:36:04'),(1291,'Reset password','admin','2014-11-12 10:21:54'),(1292,'To reset your password, enter you e-mail address and press \"reset password\":','admin','2014-11-12 10:21:55'),(1293,'Your e-mailaddress:','admin','2014-11-12 10:21:55'),(1294,'Back to login','admin','2014-11-12 10:21:55'),(1295,'Taxa gemarkeerd als verwijderd','admin','2014-11-13 07:43:55'),(1296,'Project collaborator data','admin','2014-11-13 09:35:20'),(1297,'E-mail notifications:','admin','2014-11-13 09:35:20'),(1298,'remove from project','admin','2014-11-13 09:35:20'),(1299,'Last login:','admin','2014-11-13 09:35:20'),(1300,'Module assignment in current project, \"%s\":','admin','2014-11-13 09:35:20'),(1301,'assign','admin','2014-11-13 09:35:20'),(1302,'This user is also collaborating in the following projects:','admin','2014-11-13 09:35:20'),(1303,'Edit actor','admin','2014-11-13 09:56:18'),(1304,'pagina:','admin','2014-11-13 13:49:58'),(1305,'Import NBC multi-entry key','admin','2014-11-13 15:05:45'),(1306,'Import labels for NBC multi-entry key','admin','2014-11-13 15:05:45'),(1307,'You are not authorized to do that.','admin','2014-11-15 16:25:18'),(1308,'To gain access to the page you were attempting to view, please contact one of the lead experts of your project:','admin','2014-11-15 16:25:18'),(1309,'Edit taxon passport','admin','2014-11-20 10:28:18'),(1310,'Taxon images','admin','2014-12-02 09:47:28'),(1311,'Literatuur (v2)','app','2014-12-03 12:27:46'),(1313,'Remove collaborator','admin','2014-12-16 08:46:03'),(1314,'Remove user','admin','2014-12-16 08:46:04'),(1315,'with the role of','admin','2014-12-16 08:46:04'),(1316,'from project','admin','2014-12-16 08:46:04'),(1317,'E-mail address already exists.','admin','2014-12-16 08:47:01'),(1318,'Assign collaborator to module','admin','2014-12-16 08:47:31'),(1319,'Assign user','admin','2014-12-16 08:47:31'),(1320,'to the module','admin','2014-12-16 08:47:31'),(1321,'in the project','admin','2014-12-16 08:47:31'),(1322,'User data saved','admin','2014-12-16 09:38:50'),(1323,'Omschrijving','app','2015-01-12 11:20:22'),(1324,'Datum','app','2015-01-12 11:20:22'),(1325,'Geplaatst op','app','2015-01-12 11:20:22'),(1326,'Copyright','app','2015-01-12 11:20:22'),(1327,'Contactadres fotograaf','app','2015-01-12 11:20:22'),(1328,'Stand van zaken','app','2015-01-12 11:21:03'),(1329,'Het soortenregister bevat','app','2015-01-12 11:21:03'),(1330,'Import links to remote images from CSV-file','admin','2015-01-12 14:59:53'),(1331,'Import links to local images from CSV-file','admin','2015-01-12 14:59:53'),(1332,'Import image captions from CSV-file','admin','2015-01-12 14:59:53'),(1333,'Taxon groups','admin','2015-01-12 14:59:53'),(1334,'%s soorten in totaal / %s gevestigde soorten','app','2015-01-12 17:11:49'),(1335,'Indextabel bijwerken','admin','2015-01-12 18:55:59'),(1336,'NBC multi-entry key import','admin','2015-01-15 10:26:26'),(1337,'Import finished','admin','2015-03-03 15:51:26'),(1338,'Could not resolve character \"%s\".','admin','2015-03-03 15:51:50'),(1339,'Updated image for \"%s\" to \'%s\'.','admin','2015-03-03 15:51:50'),(1340,'Could not resolve state \"%s\" for \"%s\".','admin','2015-03-03 15:51:50'),(1341,'Re-evaluating character types (using setting \"need some\").','admin','2015-03-03 15:51:50'),(1342,'Set character type for \"%s\" to %s.','admin','2015-03-03 15:51:50'),(1343,'To gain access to the page you were attempting to view, please contact the lead expert of your project:','admin','2015-03-03 15:52:49'),(1344,'Assign collaborator to modules','admin','2015-03-05 13:16:29'),(1345,'Assign collaborators to work on modules:','admin','2015-03-05 13:16:29'),(1346,'Assign collaborators to work on free modules:','admin','2015-03-05 13:16:29'),(1347,'remove as collaborator','admin','2015-03-05 13:16:30'),(1348,'edit user','admin','2015-03-05 13:16:30'),(1349,'add as collaborator','admin','2015-03-05 13:16:30'),(1350,'add all collaborators','admin','2015-03-05 13:16:30'),(1351,'no free modules have been defined','admin','2015-03-05 13:16:30'),(1352,'go %shere%s to define modules','admin','2015-03-05 13:16:30'),(1353,'Uitgebreid zoeken naar soorten','app','2015-03-13 10:36:47'),(1354,'Soortgroep','app','2015-03-13 10:36:47'),(1355,'Status voorkomen','app','2015-03-13 10:36:47'),(1356,'klik voor help over dit onderdeel','app','2015-03-13 10:36:47'),(1357,'gevestigde soorten','app','2015-03-13 10:36:47'),(1358,'niet gevestigde soorten','app','2015-03-13 10:36:47'),(1359,'Multimedia','app','2015-03-13 10:36:47'),(1360,'met foto(\'s)','app','2015-03-13 10:36:47'),(1361,'met verspreidingskaart','app','2015-03-13 10:36:47'),(1362,'met trendgrafiek','app','2015-03-13 10:36:47'),(1363,'DNA barcoding','app','2015-03-13 10:36:47'),(1364,'met een of meer exemplaren verzameld','app','2015-03-13 10:36:47'),(1365,'minder dan drie exemplaren verzameld','app','2015-03-13 10:36:47'),(1366,'Geselecteerde kenmerken','app','2015-03-13 10:36:47'),(1367,'alles verwijderen','app','2015-03-13 10:36:47'),(1368,'Resultaten sorteren op:','app','2015-03-13 10:36:47'),(1369,'Wetenschappelijk naam','app','2015-03-13 10:36:47'),(1370,'Nederlandse naam','app','2015-03-13 10:36:47'),(1371,'Status voorkomen=\"','app','2015-03-13 10:36:55'),(1372,'voor','app','2015-03-13 10:36:57'),(1373,'Exoten','app','2015-03-13 10:37:32'),(1374,'Edit trait groups','admin','2015-03-13 10:42:05'),(1375,'Upload a data sheet','admin','2015-03-13 10:42:05'),(1376,'Select project data types','admin','2015-03-13 10:42:05'),(1377,'Trait groups','admin','2015-03-13 10:42:07'),(1378,'Trait group','admin','2015-03-13 10:42:39'),(1379,'Trait group traits','admin','2015-03-13 10:43:38'),(1380,'%s: traits','admin','2015-03-13 10:43:39'),(1381,'%s: new trait','admin','2015-03-13 10:43:40'),(1382,'data type:','admin','2015-03-13 10:43:40'),(1383,'date format:','admin','2015-03-13 10:43:40'),(1384,'system name','admin','2015-03-13 10:43:40'),(1385,'code','admin','2015-03-13 10:43:40'),(1386,'description','admin','2015-03-13 10:43:40'),(1387,'max. length','admin','2015-03-13 10:43:40'),(1388,'unit','admin','2015-03-13 10:43:40'),(1389,'Project data types','admin','2015-03-13 10:43:54'),(1390,'Traits settings','admin','2015-03-13 10:44:24'),(1391,'Data upload','admin','2015-03-13 10:45:08'),(1392,'%s: %s','admin','2015-03-13 10:49:41'),(1393,'Trait values','admin','2015-03-13 10:52:17'),(1394,'values','admin','2015-03-13 10:52:17'),(1395,'current values (%s):','admin','2015-03-13 10:52:18'),(1396,'cannot add a empty value','admin','2015-03-13 10:52:33'),(1397,'%s values saved','admin','2015-03-13 10:52:34'),(1398,'is gelijk aan','app','2015-03-13 10:58:36'),(1399,'is ongelijk aan','app','2015-03-13 10:58:36'),(1400,'na','app','2015-03-13 10:58:36'),(1401,'na of gelijk aan','app','2015-03-13 10:58:36'),(1402,'voor of gelijk aan','app','2015-03-13 10:58:36'),(1403,'ligt tussen','app','2015-03-13 10:58:36'),(1404,'ligt niet tussen','app','2015-03-13 10:58:36'),(1405,'value cannot be null','admin','2015-03-13 11:08:59'),(1406,'illegal value','admin','2015-03-13 11:08:59'),(1407,'weak trait match','admin','2015-03-13 11:09:01'),(1408,'Data matched','admin','2015-03-13 11:09:08'),(1409,'Second value same as first: not a range (column %s, lines %s & %s: %s==%s)','admin','2015-03-13 11:19:03'),(1410,'Unresolvable taxon: %s (%s)','admin','2015-03-13 11:19:05'),(1411,'Saved %s values, failed %s.','admin','2015-03-13 11:19:10'),(1412,'Data saved','admin','2015-03-13 11:19:10'),(1413,'met foto\'s; ','app','2015-03-13 13:31:41'),(1414,'Exotenpaspoort','app','2015-03-13 14:42:00'),(1415,'Exotenpaspoorten','app','2015-03-13 14:43:00'),(1416,'met nog DNA-exemplaren te verzamelen; ','app','2015-03-16 14:56:26'),(1417,'met DNA-exemplaren verzameld; ','app','2015-03-16 14:56:48'),(1418,'Status voorkomen=','app','2015-03-27 12:19:35'),(1419,'Met foto\'s; ','app','2015-03-27 12:20:33'),(1420,'Bulk upload (matching)','admin','2015-03-27 13:12:58'),(1421,'Taxon trait data','admin','2015-03-27 13:13:46'),(1422,'Met verspreidingskaart(en); ','app','2015-03-27 14:58:05'),(1423,'Zonder trendgrafiek; ','app','2015-03-27 15:03:00'),(1424,'Met DNA-exemplaren verzameld; ','app','2015-03-27 15:05:09'),(1425,'Unknown reference id %s','admin','2015-03-31 11:23:33'),(1426,'Met nog DNA-exemplaren te verzamelen; ','app','2015-04-07 08:03:57'),(1427,'Bulk upload (further matching)','admin','2015-04-14 19:17:20'),(1428,'Bulk upload (saving)','admin','2015-04-14 19:20:50'),(1429,'Saved author \"%s\"','admin','2015-04-14 19:20:50'),(1430,'Saved reference \"%s\"','admin','2015-04-14 19:20:50'),(1431,'redirects to:','admin','2015-04-15 12:08:51'),(1432,'NSR ID resolver','admin','2015-05-13 10:05:11'),(1433,'Multi-purpose export','admin','2015-05-15 07:22:59'),(1434,'Met trendgrafiek; ','app','2015-05-24 19:49:40'),(1435,'For some or all taxa, there already are values for this trait group in the database. These will be overwritten by your new data.','admin','2015-06-02 13:20:20'),(1436,'unresolved reference','admin','2015-06-02 13:20:22'),(1437,'upload a file with reference # / literature ID data','admin','2015-06-02 13:20:22'),(1438,'Resolved reference # %s to \"%s\"','admin','2015-06-03 08:07:22'),(1439,'clear last upload','admin','2015-06-03 08:07:24'),(1440,'Summary','admin','2015-06-03 08:27:13'),(1441,'Saved %s trait values, failed %s.','admin','2015-06-03 08:27:13'),(1442,'(these records were not saved)','admin','2015-06-03 08:27:13'),(1443,'Errors during saving','admin','2015-06-03 08:27:13'),(1444,'(these records were saved)','admin','2015-06-03 08:27:13'),(1445,'Warnings during saving','admin','2015-06-03 08:27:13'),(1446,'Licentie','app','2015-06-11 15:23:57'),(1447,'Meta-data','admin','2015-06-11 15:25:12'),(1448,'Bewerk naam','admin','2015-06-11 18:18:20'),(1449,'Meer afbeeldingen','app','2015-06-15 14:54:32'),(1450,'Naam concept direct aanpassen','admin','2015-06-16 09:37:56'),(1451,'Geldige naam direct aanpassen','admin','2015-06-16 12:54:32'),(1452,'Browse images','admin','2015-06-16 14:54:26'),(1453,'Alle kenmerken en referenties verwijderen?','admin','2015-06-25 13:12:11'),(1454,'Missing function \"ExportController::exportNsr\"','admin','2015-07-14 07:35:03'),(1455,'Export NSR taxa with content and names','admin','2015-08-10 07:15:46'),(1456,'Export taxon data to XML','admin','2015-08-10 07:15:46'),(1457,'Meta-data bulk','admin','2015-08-14 11:15:40'),(1458,'Module settings','admin','2015-09-02 12:53:26'),(1459,'Overzicht van de Nederlandse biodiversiteit','app','2015-09-09 09:31:40'),(1460,'Indeling','app','2015-09-09 09:31:40'),(1461,'Voorkomen','app','2015-09-09 09:31:40'),(1462,'Status','app','2015-09-09 09:31:40'),(1463,'Habitat','app','2015-09-09 09:31:40'),(1464,'Referentie','app','2015-09-09 09:31:40'),(1465,'Expert','app','2015-09-09 09:31:40'),(1466,'Waarnemingen','app','2015-09-09 09:31:40'),(1467,'Uitgebreid zoeken','app','2015-09-09 09:31:40'),(1468,'Foto\'s zoeken','app','2015-09-09 09:31:40'),(1469,'Taxonomische boom','app','2015-09-09 09:31:40'),(1470,'naar boven','app','2015-09-09 09:31:40'),(1471,'Toolbox','app','2015-09-09 09:31:42'),(1472,'Exporteer (als CSV)','app','2015-09-09 09:31:42'),(1473,'Afdrukken','app','2015-09-09 09:31:42'),(1474,'alles in-/uitklappen','app','2015-09-09 09:31:42'),(1475,'Met dit zoekscherm maak je uiteenlopende selecties (onder)soorten. Verruim je selectie door meer dan 1 waarde binnen een kenmerk te selecteren (bijv. soorten met Status voorkomen 1a &lt;b&gt;of&lt;/b&gt; 1b). Vernauw je selectie door een waarde binnen een ander kenmerk te selecteren (bijv. soorten met Status voorkomen 1a &lt;b&gt;en&lt;/b&gt; met foto\\\'s). Druk op > om een kenmerkwaarde te selecteren.','app','2015-09-09 09:31:42'),(1476,'hulp bij zoeken','app','2015-09-09 09:31:42'),(1477,'maak een keuze','app','2015-09-09 09:31:42'),(1478,'Foto(\'s)','app','2015-09-09 09:31:42'),(1479,'zonder foto\'s','app','2015-09-09 09:31:42'),(1480,'Verspreidingskaart(en)','app','2015-09-09 09:31:42'),(1481,'met verspreidingskaart(en)','app','2015-09-09 09:31:42'),(1482,'zonder verspreidingskaarten','app','2015-09-09 09:31:42'),(1483,'Trendgrafiek','app','2015-09-09 09:31:42'),(1484,'zonder trendgrafiek','app','2015-09-09 09:31:42'),(1485,'van','app','2015-09-09 09:31:42'),(1486,'Soorten/taxa met afbeelding(en):','app','2015-09-09 09:31:43'),(1487,'Foto','app','2015-09-09 09:31:43'),(1488,'Top 5 fotografen','app','2015-09-09 09:31:51'),(1489,'Fotograaf (foto’s/soorten)','app','2015-09-09 09:31:51'),(1490,'Bekijk volledige lijst','app','2015-09-09 09:31:51'),(1491,'Top 5 validatoren','app','2015-09-09 09:31:51'),(1492,'Validator (foto’s/soorten)','app','2015-09-09 09:31:51'),(1493,'Soortnaam','app','2015-09-09 09:31:51'),(1494,'Naar deze soort','app','2015-09-09 09:31:51'),(1495,'Zoekresultaten','app','2015-09-09 09:32:01'),(1496,'Gezocht op','app','2015-09-09 09:32:01'),(1497,'Relevantie','app','2015-09-09 09:32:01'),(1498,'Wetenschappelijke naam','app','2015-09-09 09:32:01'),(1499,'Bron','app','2015-09-09 09:32:12'),(1500,'Auteur(s)','app','2015-09-09 09:32:12'),(1501,'Publicatie','app','2015-09-09 09:32:12'),(1502,'Totaal aantal afbeeldingen:','app','2015-09-09 09:32:13'),(1503,'Beschermingsstatus','app','2015-09-09 09:32:35'),(1504,'Bron:','app','2015-09-09 09:32:35'),(1505,'soortgegevens','app','2015-09-09 09:32:35'),(1506,'Afbeeldingen bij soort/taxon','app','2015-09-09 09:33:30'),(1507,'Soorten/taxa met afbeelding(en)','app','2015-09-09 09:33:30'),(1508,'Naam','app','2015-09-09 09:33:44'),(1509,'Is','app','2015-09-09 09:33:44'),(1510,'Registratienummer','app','2015-09-09 09:33:54'),(1511,'Verzameldatum, plaats','app','2015-09-09 09:33:54'),(1512,'Verzamelaar','app','2015-09-09 09:33:54'),(1513,'Organisatie','app','2015-09-09 09:36:31'),(1514,'Status rode lijst','app','2015-09-09 09:37:23'),(1515,'Recente afbeeldingen','app','2015-09-09 09:48:58'),(1516,'Jaar','app','2015-09-09 09:54:29'),(1517,'Titel','app','2015-09-09 09:54:29'),(1518,'Periodiek','app','2015-09-09 09:54:29'),(1519,'Volume','app','2015-09-09 09:54:29'),(1520,'Pagina\'s','app','2015-09-09 09:54:29'),(1521,'Bedreiging en bescherming','app','2015-09-09 09:56:04'),(1522,'Overzicht fotografen','app','2015-09-09 10:14:44'),(1523,'Gepubliceerd in','app','2015-09-09 10:50:45'),(1524,'Resultaten','app','2015-09-09 11:12:24'),(1525,'wetenschappelijke naam','app','2015-09-09 11:12:24'),(1526,'status voorkomen','app','2015-09-09 11:12:24'),(1527,'link naar soortenregister','app','2015-09-09 11:12:24'),(1528,'Verspreidingskaart','app','2015-09-09 11:15:33'),(1529,'Meer over deze soort in de BLWG Verspreidingsatlas','app','2015-09-09 11:15:33'),(1530,'Uitgever','app','2015-09-09 11:16:52'),(1531,'Link','app','2015-09-09 14:19:35'),(1532,'Publicaties','app','2015-09-09 17:32:41'),(1533,'Overzicht validatoren','app','2015-09-09 23:50:32'),(1534,'Referentie niet gevonden.','app','2015-09-15 22:32:39'),(1535,'soort (of onderliggend taxon)','app','2015-09-16 14:44:58'),(1536,'soorten (en onderliggende taxa)','app','2015-09-16 14:44:58'),(1537,'Trend','app','2015-09-16 15:59:37'),(1538,'Netwerk Ecologische Monitoring','app','2015-09-16 15:59:37'),(1539,'Kenmerken','app','2015-09-17 05:42:47'),(1540,'Artikel','admin','2015-09-21 08:21:49'),(1541,'Boek','admin','2015-09-21 08:21:49'),(1542,'Boek (deel)','admin','2015-09-21 08:21:49'),(1543,'Database','admin','2015-09-21 08:21:49'),(1544,'Hoofdstuk','admin','2015-09-21 08:21:49'),(1545,'Literatuur','admin','2015-09-21 08:21:49'),(1546,'Manuscript','admin','2015-09-21 08:21:49'),(1547,'Persbericht','admin','2015-09-21 08:21:49'),(1548,'Persoonlijke mededeling','admin','2015-09-21 08:21:49'),(1549,'Rapport','admin','2015-09-21 08:21:49'),(1550,'Serie','admin','2015-09-21 08:21:49'),(1551,'Tijdschrift','admin','2015-09-21 08:21:49'),(1552,'Website','admin','2015-09-21 08:21:49'),(1553,'Verslag','admin','2015-09-21 08:21:49'),(1554,'Zonder foto\'s; ','app','2015-10-03 15:04:05'),(1555,'Zonder verspreidingskaart(en); ','app','2015-10-03 15:04:56'),(1556,'Module settings: %s ','admin','2015-10-07 14:47:11'),(1557,'new setting %s saved.','admin','2015-10-07 14:47:20'),(1558,'value %s saved.','admin','2015-10-07 14:48:56'),(1559,'Publicatievormen','admin','2015-10-14 07:56:29'),(1560,'Shortname for URL (\"slug\"):','admin','2015-10-20 11:33:04'),(1561,'Opmerking','app','2015-10-26 03:08:50'),(1562,'Index per publicatievorm','admin','2015-12-08 14:57:48'),(1563,'Alternative forms:','admin','2016-02-10 08:22:13'),(1564,'add form','admin','2016-02-10 08:22:13'),(1565,'(alternative forms are also linked to this lemma by the hotwords-function)','admin','2016-02-10 08:22:13'),(1566,'Edit multimedia','admin','2016-02-10 08:22:13'),(1567,'Invalid e-mail address.','admin','2016-02-24 14:12:46'),(1568,'Passwords not the same.','admin','2016-02-24 14:14:57'),(1569,'case mismatch','admin','2016-02-25 14:10:38'),(1570,'Management tasks','admin','2016-04-06 15:26:50'),(1571,'attributes','admin','2016-04-06 15:32:50'),(1572,'always hidden','admin','2016-04-06 15:32:50'),(1573,'always hide','admin','2016-04-06 15:32:55'),(1574,'external reference','admin','2016-04-06 15:32:55'),(1575,'General settings','admin','2016-04-11 11:26:32'),(1576,'value deleted.','admin','2016-04-11 11:35:04'),(1577,'setting deleted.','admin','2016-04-11 11:35:09'),(1578,'Redactie: bewerk inhoud','app','2016-04-11 13:23:18'),(1579,'After you have made the appropriate selection, click the save-button.  Once you have saved the selection, you can ','admin','2016-04-11 13:32:34'),(1580,'In addition, you can specify where the distinction between the modules \"higher taxa\" and \"species\" will be. You can move the line by clicking the &uarr; and &darr; arrows. The setting is saved when you click','admin','2016-04-11 13:32:34'),(1581,'Be advised that this division is different from the one that defines which taxa can be the end-point of your keys. That distinction is defined in the \"dichotomous key\" module. However, that distinction must be on the same level as the one you define here, or below it. It can never be higer up in the rank hierarchy.','admin','2016-04-11 13:32:34'),(1582,'RS master key','admin','2016-04-12 08:03:16'),(1583,'create','admin','2016-04-12 08:03:16'),(1584,'Collaborators','admin','2016-04-12 08:04:22'),(1585,'create new user','admin','2016-04-12 08:04:22'),(1586,'Can publish:','admin','2016-04-12 08:06:35'),(1587,'Modules:','admin','2016-04-12 08:06:35'),(1588,'(leave empty for all)','admin','2016-04-12 08:06:38'),(1589,'First name should be between %s and %s characters.','admin','2016-04-12 08:09:04'),(1590,'Last name should be between %s and %s characters.','admin','2016-04-12 08:09:04'),(1591,'Username should be between %s and %s characters.','admin','2016-04-12 08:09:04'),(1592,'Data not saved.','admin','2016-04-12 08:09:04'),(1593,'Updated rights.','admin','2016-04-12 08:09:04'),(1594,'Updated taxa.','admin','2016-04-12 08:09:04'),(1595,'Data saved.','admin','2016-04-12 08:14:14'),(1596,'Create a new matrix:','admin','2016-04-12 08:31:53'),(1597,'Internal name','admin','2016-04-12 08:31:53'),(1598,'Edit matrix names:','admin','2016-04-12 08:37:32'),(1599,'Available matrices','admin','2016-04-12 08:37:37'),(1600,'Import Nexus file','admin','2016-04-12 08:37:37'),(1601,'Import SDD file','admin','2016-04-12 08:37:37'),(1602,'Below is a list of matrices that are currently defined. In order to edit a matrix\' name, click \"edit name\". In order to edit the actual matrix, click its name.','admin','2016-04-12 08:37:39'),(1603,'set as default','admin','2016-04-12 08:37:39'),(1604,'acquire state image dimensions','admin','2016-04-12 08:37:39'),(1605,'Get and save state image dimensions (for newly imported matrices)','admin','2016-04-12 08:37:44'),(1606,'attach media','admin','2016-04-12 09:21:59'),(1607,'title','admin','2016-04-12 09:22:01'),(1608,'location','admin','2016-04-12 09:22:01'),(1609,'photographer','admin','2016-04-12 09:22:01'),(1610,'tags','admin','2016-04-12 09:22:01'),(1611,'enter multiple tags separated by comma\'s','admin','2016-04-12 09:22:01'),(1612,'Create a new matrix','admin','2016-04-12 09:32:28'),(1613,'Upload','admin','2016-04-12 12:38:05'),(1614,'Clean up empty steps, orphaned choices and ghostly targets','admin','2016-04-12 12:38:38'),(1615,'Insert new step','admin','2016-04-12 12:38:41'),(1616,'Decision path (subsection)','admin','2016-04-12 12:38:41'),(1617,'Steps leading to this one:','admin','2016-04-12 12:39:37'),(1618,'do not show remaining and excluded taxa (enhances performance of this page; has no effect on the front-end).','admin','2016-04-12 12:39:37'),(1619,'Decision path','admin','2016-04-12 12:39:50'),(1620,'Are you sure you want to delete this choice?','admin','2016-04-12 12:40:55'),(1621,'value updated to %s.','admin','2016-04-13 07:41:05'),(1622,'nieuw concept','admin','2016-04-13 07:50:01'),(1623,'taxonomie','admin','2016-04-13 07:50:01'),(1624,'rang:','admin','2016-04-13 07:50:01'),(1625,'rang','admin','2016-04-13 07:50:01'),(1626,'ouder:','admin','2016-04-13 07:50:01'),(1627,'ouder','admin','2016-04-13 07:50:01'),(1628,'geldige wetenschappelijke naam','admin','2016-04-13 07:50:01'),(1629,'genus of uninomial:','admin','2016-04-13 07:50:01'),(1630,'genus','admin','2016-04-13 07:50:01'),(1631,'soort:','admin','2016-04-13 07:50:01'),(1632,'soort','admin','2016-04-13 07:50:01'),(1633,'derde naamdeel:','admin','2016-04-13 07:50:01'),(1634,'(ondersoort, forma, varietas, etc.)','admin','2016-04-13 07:50:01'),(1635,'vul de volledige waarde voor \'auteurschap\' in, inclusief komma, jaartal en haakjes; het programma leidt de waarden voor auteur en jaar automatisch af.','admin','2016-04-13 07:50:01'),(1636,'auteurschap:','admin','2016-04-13 07:50:01'),(1637,'auteurschap','admin','2016-04-13 07:50:01'),(1638,'auteur(s):','admin','2016-04-13 07:50:01'),(1639,'auteur','admin','2016-04-13 07:50:01'),(1640,'jaar:','admin','2016-04-13 07:50:01'),(1641,'jaar','admin','2016-04-13 07:50:01'),(1642,'expert:','admin','2016-04-13 07:50:01'),(1643,'organisatie:','admin','2016-04-13 07:50:01'),(1644,'publicatie:','admin','2016-04-13 07:50:01'),(1645,'Publicatie','admin','2016-04-13 07:50:01'),(1646,'concept','admin','2016-04-13 07:50:01'),(1647,'conceptnaam wordt automatische samengesteld op basis van de geldige wetenschappelijke naam.','admin','2016-04-13 07:50:01'),(1648,'naam:','admin','2016-04-13 07:50:01'),(1649,'nsr id:','admin','2016-04-13 07:50:01'),(1650,'(wordt automatisch gegenereerd)','admin','2016-04-13 07:50:01'),(1651,'n.v.t.','admin','2016-04-13 07:50:01'),(1652,'status voorkomen kan alleen worden ingevuld voor soorten en lager.','admin','2016-04-13 07:50:01'),(1653,'voorkomen','admin','2016-04-13 07:50:01'),(1654,'status:','admin','2016-04-13 07:50:01'),(1655,'habitat:','admin','2016-04-13 07:50:01'),(1656,'opslaan','admin','2016-04-13 07:50:01'),(1657,'terug','admin','2016-04-13 07:50:01'),(1658,'Nederlandse naam','admin','2016-04-13 07:50:01'),(1659,'top','admin','2016-04-13 09:09:42'),(1660,'rangen','admin','2016-04-13 09:09:42'),(1661,'statussen','admin','2016-04-13 09:09:42'),(1662,'(alle)','admin','2016-04-13 09:09:42'),(1663,'A total of','admin','2016-04-13 10:01:39'),(1664,'media files has been uploaded for this project','admin','2016-04-13 10:01:39'),(1665,'You must first','admin','2016-04-13 10:01:39'),(1666,'upload images','admin','2016-04-13 10:01:39'),(1667,'Browse Media','admin','2016-04-13 10:02:29'),(1668,'file name','admin','2016-04-13 10:02:34'),(1669,'Nothing found (yet)!','admin','2016-04-13 10:02:34'),(1670,'Search Media','admin','2016-04-13 10:02:59'),(1671,'Browse media on ResourceSpace server','admin','2016-04-13 10:03:14'),(1672,'media item(s) is stored for this project','admin','2016-04-13 10:03:14'),(1673,'Browse Media on ResourceSpace server','admin','2016-04-13 10:03:37'),(1674,'Nexus file upload','admin','2016-04-13 10:06:58'),(1675,'Nexus import','admin','2016-04-13 10:16:41'),(1676,'Specify file','admin','2016-04-13 10:18:23'),(1677,'SDD Import','admin','2016-04-13 10:20:08'),(1678,'Editing matrix \"(sort characters)','admin','2016-04-13 10:21:06'),(1679,'Insert a step before step %s','admin','2016-04-13 10:25:09'),(1680,'Insert a new start step before step','admin','2016-04-13 10:25:09'),(1681,'insert','admin','2016-04-13 10:25:09'),(1682,'Insert a step between','admin','2016-04-13 10:30:07'),(1683,'and step','admin','2016-04-13 10:30:07'),(1684,'Insert a step between step %s and %s','admin','2016-04-13 10:31:43'),(1685,'Edit trait group','admin','2016-04-13 11:18:30'),(1686,'New trait group','admin','2016-04-13 11:19:20'),(1687,'New actor','admin','2016-04-13 11:21:49'),(1688,'conceptkaart:','admin','2016-04-13 11:36:09'),(1689,'namen','admin','2016-04-13 11:36:09'),(1690,'niet-wetenschappelijke naam toevoegen','admin','2016-04-13 11:36:09'),(1691,'toevoegen van geldige naam, synoniem, etc.','admin','2016-04-13 11:36:09'),(1692,'wetenschappelijke naam toevoegen','admin','2016-04-13 11:36:09'),(1693,'ondersoort toevoegen aan \"%s\"','admin','2016-04-13 11:36:09'),(1694,'paspoort','admin','2016-04-13 11:36:09'),(1695,'afbeeldingen (NSR-only)','admin','2016-04-13 11:36:09'),(1696,'taxon bekijken in front-end (nieuw venster)','admin','2016-04-13 11:36:09'),(1697,'taxon markeren als verwijderd','admin','2016-04-13 11:36:09'),(1698,'naam taxon concept direct aanpassen','admin','2016-04-13 11:36:09'),(1699,'kenmerken toevoegen:','admin','2016-04-13 11:36:09'),(1700,'Upload media','admin','2016-04-13 11:39:21'),(1701,'(enclose multiple words with double quotes (\") to search for the literal string)','admin','2016-04-13 11:50:29'),(1702,'in modules:','admin','2016-04-13 11:50:29'),(1703,'Actoren','admin','2016-04-13 11:50:29'),(1704,'Beheer Soortenregister','admin','2016-04-13 11:50:29'),(1705,'Kenmerken','admin','2016-04-13 11:50:29'),(1706,'User management','admin','2016-04-13 11:50:29'),(1707,'Project management','admin','2016-04-13 11:50:29'),(1708,'Literatuur (v2)','admin','2016-04-13 11:50:29'),(1709,'descriptions','admin','2016-04-13 12:20:19'),(1710,'navigator','admin','2016-04-13 12:20:19'),(1711,'steps','admin','2016-04-13 12:20:19'),(1712,'choices','admin','2016-04-13 12:20:19'),(1713,'endpoints','admin','2016-04-13 12:20:19'),(1714,'references','admin','2016-04-13 12:20:19'),(1715,'items','admin','2016-04-13 12:20:19'),(1716,'Results','admin','2016-04-13 12:24:05'),(1717,'Import options','admin','2016-04-13 12:55:16'),(1718,'Taxonomic tree','admin','2016-04-13 13:51:51'),(1719,'External ID resolver','admin','2016-04-13 15:17:38'),(1720,'onderliggend taxon toevoegen aan \"%s\"','admin','2016-04-13 15:17:56');
/*!40000 ALTER TABLE `interface_texts` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `interface_translations`
--
LOCK TABLES `interface_translations` WRITE;
/*!40000 ALTER TABLE `interface_translations` DISABLE KEYS */;
INSERT INTO `interface_translations` VALUES (3,75,24,'%s %s uitgesloten:'),(4,74,24,'%s mogelijk %s resterend:'),(5,43,24,'Over ETI'),(6,89,24,'Voeg toe'),(7,47,24,'Terug'),(8,48,24,'Terug naar'),(9,101,24,'Kenmerk'),(10,86,24,'Kenmerken'),(11,153,24,'Kies een kaart'),(12,91,24,'Maak alles leeg'),(13,156,24,'Maak de kaart leeg'),(14,54,24,'Gewone namen'),(15,84,24,'Vergelijk'),(16,39,24,'contact'),(17,46,24,'Inhoud'),(18,42,24,'Bijdragen van | Medewerkers'),(19,78,24,'Beslispad'),(20,90,24,'Verwijder'),(21,29,24,'Dichotome sleutel'),(22,155,24,'Toont overlap van twee taxa.'),(23,31,24,'Verspreiding'),(24,150,24,'Diversiteitindex'),(25,83,24,'Onderzoek'),(26,73,24,'Uitgezonderd'),(27,77,24,'Eerst'),(28,61,24,'voor'),(29,25,24,'Woordenlijst'),(30,151,24,'Ga naar dit taxon'),(31,37,24,'help'),(32,28,24,'Hogere taxa'),(33,82,24,'Identificeer'),(34,33,24,'Index'),(35,24,24,'Introductie'),(36,57,24,'Taal:'),(37,26,24,'Literatuur'),(38,45,24,'Programma wordt geladen'),(39,36,24,'login'),(40,30,24,'Matrixsleutel'),(41,148,24,'Selecteer een andere matrix'),(42,50,24,'Volgende'),(43,76,24,'Nog geen keuze(s) gemaakt'),(44,38,24,'nog niet beschikbaar'),(45,110,24,'Aantal beschikbare toestanden:'),(46,49,24,'Vorige'),(47,35,24,'projecten'),(48,159,24,'records'),(49,72,24,'Resterend'),(50,95,24,'Resultaat van deze combinatie kenmerken'),(51,79,24,'Terug naar stap 1 | Terug naar eerste keuze'),(52,80,24,'Terug naar stap | Terug naar keuze'),(53,34,24,'Zoek'),(54,92,24,'Zoek &gt;&gt;'),(55,160,24,'Zoekresultaten'),(56,44,24,'Zoek...'),(57,152,24,'Selecteer een andere kaart'),(58,97,24,'Selecteer een taxon'),(59,98,24,'Selecteer uit de lijst een taxon om de kenmerken en toestanden ervan te zien.'),(60,99,24,'Deze worden bij het selectieproces van de Snelzoeker gebruikt.'),(61,157,24,'Selecteer het zoekgebied door de relevante vierkanten aan te klikken.'),(62,104,24,'Selecteer twee taxa uit de lijst en klik Vergelijk om de kenmerken en toestanden ervan te vergelijken. De uitkomst toont de verschillen en overeenkomsten tussen beide taxa.'),(63,93,24,'Geselecteerde combinatie van kenmerken'),(64,106,24,'Gedeelde toestanden:'),(65,58,24,'Toon alle'),(66,87,24,'Sorteer'),(67,27,24,'Soort'),(68,53,24,'Soorten en lagere taxa'),(69,102,24,'Toestand'),(70,88,24,'Toestanden'),(71,108,24,'Toestanden in beide aanwezig:'),(72,109,24,'Toestanden in geen van beide aanwezig:'),(73,71,24,'Stap'),(74,148,24,'Verander van matrix'),(75,60,24,'Synoniem'),(76,111,24,'Taxonomische afstand:'),(77,94,24,'Behandel onbekenden als overeenkomend'),(78,100,24,'Type'),(79,105,24,'Unieke toestand voor %s:'),(80,107,24,'Unieke toestand in'),(81,41,24,'Welkom'),(82,158,24,'Wanneer gereed, klik \'Zoek\'.'),(94,147,24,'terug'),(84,23,24,'Terug naar Linnaeus NG \'root\''),(85,125,24,'Kenmerken'),(86,115,24,'Bijdragen van | Medewerkers'),(95,142,24,'verwijder'),(88,15,24,'login'),(89,2,24,'projecten'),(90,134,24,'Toestanden'),(91,114,24,'Welkom'),(92,177,24,'taal toevoegen'),(97,113,24,'Inhoud'),(130,208,24,'< vorige'),(99,182,24,'Taal'),(132,129,24,'& andere matrices'),(131,466,24,'\n\n'),(118,337,24,'\n'),(109,391,24,'Acties'),(110,394,24,'toevoegen'),(111,422,24,'Voeg een nieuwe categorie toe:'),(117,544,24,'\n'),(135,331,24,'(dubbelklik om te verwijderen)\n'),(134,163,24,'(huidig)\n'),(133,622,24,'\n'),(129,288,24,'\n'),(136,273,24,'(nieuwe stap)\n'),(143,842,24,'vrouw'),(142,841,24,'man'),(144,66,24,'Naamgeving'),(146,64,24,'Foto\'s');
/*!40000 ALTER TABLE `interface_translations` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `labels_languages`
--
LOCK TABLES `labels_languages` WRITE;
/*!40000 ALTER TABLE `labels_languages` DISABLE KEYS */;
INSERT INTO `labels_languages` VALUES (1,1,24,24,'Nederlands','2013-11-06 00:00:00','2014-01-22 12:27:01'),(2,1,26,24,'Engels','0000-00-00 00:00:00','2014-01-23 12:02:45'),(3,1,123,24,'Wetenschappelijk','2014-01-23 12:45:28','2014-01-23 12:02:47'),(4,1,31,24,'Frans','2014-10-29 14:59:39','2014-10-29 14:59:39'),(5,1,36,24,'Duits','2014-10-29 14:59:39','2015-06-17 11:46:26'),(6,1,99,24,'Spaans','2014-10-29 15:00:05','2014-10-29 15:00:05');
/*!40000 ALTER TABLE `labels_languages` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `languages`
--
LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'Abkhaz','abk','ab',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:07'),(2,'Afrikaans','afr','af','Afrikaans','af_ZA','ltr',NULL,'2010-09-06 13:44:07'),(3,'Albanian','alb',NULL,'Albanian','sq_AL','ltr',NULL,'2010-09-06 13:44:07'),(4,'Amharic','amh','am',NULL,'am_ET','ltr',NULL,'2010-09-06 13:44:07'),(5,'Arabic','ara','ar','Arabic_Egypt','ar_EG','rtl',NULL,'2010-09-06 13:44:07'),(6,'Assyrian/Syriac','syr',NULL,'Syriac',NULL,'rtl',NULL,'2010-09-06 13:44:07'),(7,'Armenian','arm',NULL,'Armenian',NULL,'ltr',NULL,'2010-09-06 13:44:07'),(8,'Assamese','asm','as',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:07'),(9,'Aymara','aym','ay',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:07'),(10,'Azeri','aze','az','Azeri_Cyrillic',NULL,'rtl',NULL,'2010-09-06 13:44:07'),(11,'Basque','baq',NULL,'Basque','eu_ES','ltr',NULL,'2010-09-06 13:44:07'),(12,'Belarusian','bel','be','Belarusian','be_BY','ltr',NULL,'2010-09-06 13:44:07'),(13,'Bengali','ben','bn','Bengali_India','bn_IN','ltr',NULL,'2010-09-06 13:44:07'),(14,'Bislama','bis','bi',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:07'),(15,'Bosnian','bos','bs','Bosnian_Latin','bs_BA','ltr',NULL,'2010-09-06 13:44:07'),(16,'Bulgarian','bul','bg','Bulgarian','bg_BG','ltr',NULL,'2010-09-06 13:44:07'),(17,'Burmese','bur',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:07'),(18,'Catalan','cat','ca','Catalan','ca_ES','ltr',NULL,'2010-09-06 13:44:07'),(19,'Chinese','chi',NULL,'Chinese_PRC','zh_CN','ltr',NULL,'2010-09-06 13:44:07'),(20,'Croatian','hrv','hr','Croatian',NULL,'ltr',NULL,'2010-09-06 13:44:07'),(21,'Czech','cze',NULL,'Czech','cs_CZ','ltr',NULL,'2010-09-06 13:44:07'),(22,'Danish','dan','da','Danish','da_DK','ltr',NULL,'2010-09-06 13:44:07'),(23,'Dhivehi','div','dv','Divehi',NULL,'rtl',NULL,'2010-09-06 13:44:07'),(24,'Dutch','dut','nl','Dutch','nl_NL','ltr',2,'2010-09-06 13:44:08'),(25,'Dzongkha','dzo','dz',NULL,'dz_BT','ltr',NULL,'2010-09-06 13:44:08'),(26,'English','eng','en','English','en_GB','ltr',1,'2010-09-06 13:44:08'),(27,'Estonian','est','et','Estonian','et_EE','ltr',NULL,'2010-09-06 13:44:08'),(28,'Fijian','fij','fj',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(29,'Filipino','fil',NULL,NULL,'fil_PH','ltr',NULL,'2010-09-06 13:44:08'),(30,'Finnish','fin','fi','Finnish','fi_FI','ltr',NULL,'2010-09-06 13:44:08'),(31,'French','fre','fr','French_Standard','fr_FR','ltr',5,'2010-09-06 13:44:08'),(32,'Frisian','frs',NULL,NULL,'fy_NL','ltr',NULL,'2010-09-06 13:44:08'),(33,'Gagauz','gag',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(34,'Galician','glg','gl','Galician',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(35,'Georgian','geo','ka','Georgian',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(36,'German','ger','de','German_Standard','de_DE','ltr',4,'2010-09-06 13:44:08'),(37,'Greek','gre','el','Greek',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(38,'Gujarati','guj','gu','Gujarati',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(39,'Haitian Creole','hat','ht',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(40,'Hebrew','heb','he','Hebrew',NULL,'rtl',NULL,'2010-09-06 13:44:08'),(41,'Hindi','hin','hi','Hindi',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(42,'Hiri Motu','hmo','ho',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(43,'Hungarian','hun','hu','Hungarian',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(44,'Icelandic','ice','is','Icelandic',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(45,'Indonesian','ind','id','Indonesian',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(46,'Inuinnaqtun','ikt',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(47,'Inuktitut','iku','iu',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(48,'Irish','gle','ga',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(49,'Italian','ita','it','Italian_Standard',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(50,'Japanese','jpn','ja','Japanese','ja_JP','ltr',NULL,'2010-09-06 13:44:08'),(51,'Kannada','kan','kn','Kannada',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(52,'Kashmiri','kas','ks','Kazakh',NULL,'rtl',NULL,'2010-09-06 13:44:08'),(53,'Kazakh','kaz','kk',NULL,NULL,'rtl',NULL,'2010-09-06 13:44:08'),(54,'Khmer','khm','km',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(55,'Korean','kor','ko','Korean','ko_KR','ltr',NULL,'2010-09-06 13:44:08'),(56,'Kurdish','kur','ku','Kyrgyz',NULL,'rtl',NULL,'2010-09-06 13:44:08'),(57,'Kyrgyz','kir','ky',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(58,'Lao','lao','lo',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(59,'Latin','lat','la',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(60,'Latvian','lav','lv','Latvian',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(61,'Lithuanian','lit','lt','Lithuanian',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(62,'Luxembourgish','ltz','lb',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(63,'Macedonian','mac','mk','Macedonian',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(64,'Malagasy','mlg','mg',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(65,'Malay','may','ms','Malay_Malaysia',NULL,'rtl',NULL,'2010-09-06 13:44:08'),(66,'Malayalam','mal','ml','Malayalam',NULL,'rtl',NULL,'2010-09-06 13:44:08'),(67,'Maltese','mlt','mt','Maltese',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(68,'Manx Gaelic','glv','gv',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(69,'Ma-ori','mao','mi','Maori',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(70,'Marathi','mar','mr','Marathi',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(71,'Mayan','myn',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(72,'Moldovan','rum','ro',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(73,'Mongolian','mon','mn','Mongolian',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(74,'Ndebele','nde','nd',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(75,'Nepali','nep','ne',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(76,'Northern Sotho','nso',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(77,'Norwegian','nor','no','Norwegian_Bokmal',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(78,'Occitan','oci','oc',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(79,'Oriya','ori','or',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(80,'Ossetian','oss','os',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(81,'Papiamento','pap',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:08'),(82,'Pashto','pus','ps',NULL,NULL,'rtl',NULL,'2010-09-06 13:44:08'),(83,'Persian','per','fa',NULL,NULL,'rtl',NULL,'2010-09-06 13:44:08'),(84,'Polish','pol','pl','Polish',NULL,'ltr',NULL,'2010-09-06 13:44:08'),(85,'Portuguese','por','pt','Portuguese_Standard',NULL,'ltr',NULL,'2010-09-06 13:44:09'),(86,'Punjabi','pan','pa','Punjabi',NULL,'rtl',NULL,'2010-09-06 13:44:09'),(87,'Quechua','que','qu','Quechua_Bolivia',NULL,'ltr',NULL,'2010-09-06 13:44:09'),(88,'Rhaeto-Romansh','roh','rm',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:09'),(89,'Russian','rus','ru','Russian','ru_RU','ltr',NULL,'2010-09-06 13:44:09'),(90,'Sanskrit','san','sa','Sanskrit','sa_IN','ltr',NULL,'2010-09-06 13:44:09'),(91,'Serbian','srp','sr','Serbian_Cyrillic','sr_ME','ltr',NULL,'2010-09-06 13:44:09'),(92,'Shona','sna','sn',NULL,NULL,'ltr',NULL,'2010-09-06 13:44:09'),(93,'Sindhi','snd','sd',NULL,'sd_IN','rtl',NULL,'2010-09-06 13:44:09'),(94,'Sinhala','sin','si',NULL,'si_LK','ltr',NULL,'2010-09-06 13:44:09'),(95,'Slovak','slo','sk','Slovak','sk_SK','ltr',NULL,'2010-09-06 13:44:09'),(96,'Slovene','slv','sl','Slovenian','sl_SI','ltr',NULL,'2010-09-06 13:44:09'),(97,'Somali','som','so',NULL,'so_SO','rtl',NULL,'2010-09-06 13:44:09'),(98,'Sotho','sot','st',NULL,'st_ZA','ltr',NULL,'2010-09-06 13:44:09'),(99,'Spanish','spa','es',NULL,'es_ES','ltr',3,'2010-09-06 13:44:09'),(100,'Sranan Tongo','srn',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:09'),(101,'Swahili','swa','sw','Swahili',NULL,'ltr',NULL,'2010-09-06 13:44:09'),(102,'Swati','ssw','ss',NULL,'ss_ZA','ltr',NULL,'2010-09-06 13:44:09'),(103,'Swedish','swe','sv','Swedish','sv_SE','ltr',NULL,'2010-09-06 13:44:09'),(104,'Tajik','tgk','tg',NULL,'tg_TJ','ltr',NULL,'2010-09-06 13:44:09'),(105,'Tamil','tam','ta','Tamil','ta_IN','ltr',NULL,'2010-09-06 13:44:09'),(106,'Telugu','tel','te','Telugu','te_IN','ltr',NULL,'2010-09-06 13:44:09'),(107,'Tetum','tet',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:09'),(108,'Thai','tha','th','Thai','th_TH','ltr',NULL,'2010-09-06 13:44:09'),(109,'Tok Pisin','tpi',NULL,NULL,NULL,'ltr',NULL,'2010-09-06 13:44:09'),(110,'Tsonga','tog',NULL,NULL,'ts_ZA','ltr',NULL,'2010-09-06 13:44:09'),(111,'Tswana','tsn','tn',NULL,'tn_ZA','ltr',NULL,'2010-09-06 13:44:09'),(112,'Turkish','tur','tr','Turkish',NULL,'ltr',NULL,'2010-09-06 13:44:09'),(113,'Turkmen','tuk','tk',NULL,'tk_TM','rtl',NULL,'2010-09-06 13:44:09'),(114,'Ukrainian','ukr','uk','Ukrainian','uk_UA','ltr',NULL,'2010-09-06 13:44:09'),(115,'Urdu','urd','ur','Urdu','ur_PK','rtl',NULL,'2010-09-06 13:44:09'),(116,'Uzbek','uzb','uz','Uzbek_Cyrillic','uz_UZ','ltr',NULL,'2010-09-06 13:44:09'),(117,'Venda','ven','cy',NULL,'ve_ZA','ltr',NULL,'2010-09-06 13:44:09'),(118,'Vietnamese','vie','vi','Vietnamese','vi_VN','ltr',NULL,'2010-09-06 13:44:09'),(119,'Welsh','wel',NULL,'Welsh','cy_GB','ltr',NULL,'2010-09-06 13:44:09'),(120,'Xhosa','xho','xh','Xhosa','xh_ZA','ltr',NULL,'2010-09-06 13:44:09'),(121,'Yiddish','yid','yi',NULL,'yi_US','rtl',NULL,'2010-09-06 13:44:09'),(122,'Zulu','zul','zu','Zulu','zu_ZA','ltr',NULL,'2010-09-06 13:44:09'),(123,'Scientific','sci',NULL,NULL,NULL,'ltr',NULL,'2013-09-25 14:43:47');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `module_settings`
--
LOCK TABLES `module_settings` WRITE;
/*!40000 ALTER TABLE `module_settings` DISABLE KEYS */;
INSERT INTO `module_settings` VALUES (1,7,'calc_char_h_val','do or don\'t calculate the H-value for characters (disabling increases performance) [0,1]','1','2016-04-11 14:26:45','2016-03-15 13:28:42'),(2,7,'allow_empty_species','allow empty species (species with no content in the species module) to appear in the matrix (L2 legacy keys only) [0,1]','1','2016-04-11 14:26:45','2015-07-30 09:06:55'),(4,7,'use_emerging_characters','disable characters as long as their states do not apply to all remaining species/taxa. [0,1]','1','2016-04-11 14:26:45','2015-07-30 08:18:02'),(5,7,'use_character_groups','allow characters to be organised in groups. [0,1]','1','2016-04-11 14:26:45','2015-07-30 08:18:03'),(6,7,'browse_style','style of browsing through result sets [expand, paginate, show_all]','expand','2016-04-11 14:26:45','2015-07-30 08:17:56'),(7,7,'items_per_line','number of resulting species per line [number]','4','2016-04-11 14:26:45','2015-07-30 07:23:35'),(8,7,'items_per_page','number of resulting species per page (no effect when browse_style = \'show_all\') [number]','16','2016-04-11 14:26:45','2015-07-30 07:23:39'),(9,7,'always_show_details','icon for species characters normally only appears when resultset <= items_per_page. set this to 1 to always display the icon, regardless of the size of the resultset. [0,1]','1','2016-04-11 14:26:45','2015-07-31 05:57:08'),(10,7,'score_threshold','threshold of match percentage during identifying above which species displayed. setting to 100 only shows full matches, i.e. species that have all selected states. [0...100]','100','2016-04-11 14:26:45','2015-07-30 07:24:05'),(12,7,'img_to_thumb_regexp_pattern','reg exp replace pattern to match the URL of a species normal image (from the nsr_extras table) against for automatic creation of a thumbnail URL. works in unison with \'img_to_thumb_regexp_replacement\'. take *great* care that the reg exp is valid and properly escaped, as there is currently no check on its validity, and a broken reg exp will cause errors.\r\nthe default applies specifically to NSR-related keys.\r\n','/http:\\/\\/images.naturalis.nl\\/original\\//','2016-04-11 14:26:45','2015-07-29 11:36:15'),(13,7,'img_to_thumb_regexp_replacement','replacement string for the reg exp in \r\n\'img_to_thumb_regexp_pattern\' (see there). can be empty!','http://images.naturalis.nl/comping/','2016-04-11 14:26:45','2015-07-29 11:37:08'),(15,7,'initial_sort_column','column to initially sort the data set on (without settting, program sorts on scientific name)',NULL,'2016-04-11 14:26:45','2015-08-20 05:29:40'),(16,7,'always_sort_by_initial','sort result set on \'initial_sort_column\' after matching percentages have been calculated (default behaviour is sorting by match percentage) [1,0]','0','2016-04-11 14:26:45','2015-08-20 05:24:16'),(17,7,'species_info_url','external URL for further info on a species. overrides the species-specific URL from the nsr_extras table as link under the info-icon (though in some skins that URL is also displayed in the details pop-up). expects a webservice URL that returns a JSON-object that at least has an element \'page\' with an element \'body\'. URL can be parametrised with %TAXON% (scientific name, key) and, optionally, %PID% (project ID). example:\r\n\r\nhttp://www.nederlandssoortenregister.nl/linnaeus_ng/app/views/webservices/taxon_page?pid=1&taxon=%TAXON%&cat=163',NULL,'2016-04-11 14:26:45','2015-07-29 11:45:10'),(18,7,'introduction_topic_colophon_citation','topic name of the page from the introduction module to be used as colophon and citation-info.','Matrix colophon & citation','2016-04-11 14:26:45','2015-08-24 05:49:43'),(19,7,'introduction_topic_versions','topic name of the page from the introduction module to be used as version history.','Matrix version history','2016-04-11 14:26:45','2015-07-29 11:50:49'),(20,7,'introduction_topic_inline_info','topic name of the page from the introduction module containing additional info, to be displayed inline beneath the legend.','Matrix additional info','2016-04-11 14:26:45','2015-08-24 05:49:43'),(21,7,'popup_species_link_text','text for the remote link that appears in the pop-up that shows the info retrieved with species_info_url. only relevant when species_info_url is defined and if there\'s a species-specific info-URL in the nsr_extras as well. note: strictly speaking, this is not the right place for something purely textually, as setting-values are not considered to be language-dependent. oh well.','Meer details','2016-04-11 14:26:45','2015-07-29 11:54:03'),(23,7,'image_orientation','orientation of taxon images in search results of matrix key [landscape, portrait]','portrait','2016-04-11 14:26:45','2015-08-18 07:33:49'),(24,7,'show_scores','show the matching percentage in the results (only useful when score_threshold is below 100). [0,1]','0','2016-04-11 14:26:45','2015-07-29 11:54:03'),(25,7,'enable_treat_unknowns_as_matches','enables the function \"treat unknowns as matches\", which scores a taxon for which no state has been defined within a certain character as a match for that character (a sort of \"rather safe than sorry\"-setting). [0,1]','0','2016-04-11 14:26:45','2015-07-29 11:54:03'),(28,7,'suppress_details','suppresses retrieval and displaying of all character states for each item in de dataset. siginificantly reduces the footprint of the initial data-load. [0,1]','0','2016-04-11 14:26:45','2015-09-03 08:22:16'),(29,7,'similar_species_show_distinct_details_only','when displaying similar species or search results, normally all details are displayed, rather than only the distinct details of each species. set this setting to 1 to switch to distinct-only.','1','2016-04-11 14:26:45','2015-10-07 10:47:04'),(30,-1,'skin','styling of graphical interface of application front-end.','linnaeus_ng','2016-02-04 11:40:58','2016-02-04 09:41:30'),(31,-1,'skin_mobile','styling of graphical interface of application front-end, specific for mobile devices.',NULL,'2016-02-04 11:41:32','2016-02-04 09:41:32'),(32,-1,'skin_gsm','styling of graphical interface of application front-end, specific for mobile phones (overrides \'skin_mobile\').',NULL,'2016-02-04 11:41:53','2016-02-04 09:41:53'),(33,-1,'force_skin_mobile','force the use of skin_mobile (if defined), even if values for skin or skin_gsm have been defined.','0','2016-02-04 11:42:43','2016-02-04 09:43:01'),(34,-1,'suppress_restore_state','suppress the restoring of a module\'s earlier state from the same session when re-accessing the module (front-end only).','0','2016-02-04 11:44:20','2016-02-04 09:44:20'),(35,-1,'start_page','specific URL (relative) to redirect to when a user first opens the application (front-end).',NULL,'2016-02-04 11:45:32','2016-02-04 09:45:32'),(36,-1,'db_lc_time_names','MySQL locale for date and time names.','nl_NL','2016-02-04 11:45:58','2016-02-04 09:45:58'),(37,12,'url_help_search_presence','URL of the user help for the search category \"presence\" (NSR specific).',NULL,'2016-02-04 11:51:42','2016-02-04 09:51:42'),(38,4,'use_taxon_variations','allow the use of taxon variations (currently in use in the matrix key only)',NULL,'2016-02-04 11:54:43','2016-02-04 09:54:43'),(39,4,'base_url_images_main','base URL of main image in NSR-style search results.',NULL,'2016-02-04 11:57:24','2016-02-04 09:57:24'),(40,4,'base_url_images_thumb','base URL of thumb images in NSR-style search results.',NULL,'2016-02-04 11:57:37','2016-02-04 09:57:37'),(41,4,'base_url_images_overview','base URL of overview images in NSR-style search results.',NULL,'2016-02-04 11:57:52','2016-02-04 09:57:52'),(43,4,'base_url_images_thumb_s','base URL of smaller thumb images in NSR-style search results.',NULL,'2016-02-04 11:59:27','2016-02-04 09:59:27'),(44,-1,'taxon_main_image_base_url','taxon_main_image_base_url (needs to be re-examined)',NULL,'2016-02-04 12:01:05','2016-02-04 10:01:05'),(45,4,'taxon_fetch_ez_data','taxon_fetch_ez_data (should be re-examined)',NULL,'2016-02-04 12:02:20','2016-02-04 10:02:20'),(46,4,'include_overview_in_media','include the overview image in the general media page of a taxon as well.',NULL,'2016-02-04 12:02:55','2016-02-04 10:02:55'),(47,4,'lookup_list_species_max_results','max. results in species lookup list (front-end)',NULL,'2016-02-04 12:11:17','2016-02-04 10:11:17'),(48,13,'literature2_import_match_threshold','default matching threshold for literature bulk import (percentage).','75','2016-02-04 12:12:10','2016-02-04 10:12:10'),(49,6,'keytype','l2 or lng (not sure what the difference is anymore)',NULL,'2016-02-04 12:27:08','2016-02-04 10:27:08'),(50,7,'matrixtype','nbc (EIS-style) or lng (old L2-style). when the old style disappears, this will become obsolete.',NULL,'2016-02-04 12:28:18','2016-02-04 10:28:18'),(51,-1,'image_root_skin','root of the image files that come with the skin',NULL,'2016-02-04 12:30:15','2016-02-04 10:30:15'),(52,12,'min_search_length','minimum length of search string','3','2016-02-08 12:01:02','2016-02-08 10:15:50'),(53,12,'max_search_length','maximum length of search string','50','2016-02-08 12:15:46','2016-02-08 10:15:46'),(54,12,'excerpt_pre-match_length','length of the displayed text excerpt preceding a search result.','35','2016-02-08 12:16:34','2016-02-08 10:16:34'),(55,12,'excerpt_post-match_length','length of the displayed text excerpt following a search result.','35','2016-02-08 12:17:02','2016-02-08 10:17:02'),(56,12,'excerpt_pre_post_match_string','text string to embed preceding and following text with','...','2016-02-08 12:17:37','2016-02-08 10:17:37'),(58,7,'image_root_skin','relative image root of the skin-images.','../../media/system/skins/responsive_matrix/','2016-02-08 16:30:04','2016-02-08 14:30:04'),(59,-1,'url_to_picture_license_info','URL to the page explaining the various picture licensing options (be aware, the same setting also exists, and should also be mainained, in the \'species\' module).\r\n',NULL,'2016-04-11 14:26:45','2015-10-07 10:51:05'),(60,-1,'picture_license_default','the default license shown for pictures for which no license has been specified in the meta-data.',NULL,'2016-04-11 14:26:45','2015-10-07 10:51:13'),(61,7,'use_overview_image','use overview image from the species module as main species image.',NULL,'2016-03-15 14:53:47','2016-03-15 12:53:47'),(62,7,'species_module_link','link to use for the info-link when none is available for the taxon in the database. can be parametrised with %s for substitution of the taxon ID. note: \'species_info_url\' gets precedence.\n\n','../species/nsr_taxon.php?id=%s','2016-03-15 15:13:02','2016-03-15 13:23:44'),(63,7,'species_module_link_force','link to use for the info-link, even when there is one available in the database. can be parametrised with %s for substitution of the taxon ID.  note: \'species_info_url\' gets precedence.','../species/nsr_taxon.php?id=%s','2016-03-15 15:14:20','2016-03-15 13:23:51'),(64,7,'info_link_target','target of the info-link when retrieved from the database or specified by \'species_module_link\' or \'species_module_link_force\'. has no effect if \'species_info_url\' is defined, as that setting takes precedence and causes taxon-info to be displayed in a pop-up. leave blank for _blank (ha).',NULL,'2016-03-15 15:26:53','2016-03-15 13:27:32'),(65,-1,'wiki_base_url',' Base URL to the help Wiki. Can be parametrized with %module% (translated to controllerPublicName) and %page% (translated to pageName)','http://localhost/wikkawiki/wikka.php?wakka=%module%#hn_%page%','2016-04-11 14:28:52','2016-04-11 12:29:20'),(66,0,'rs_base_url','Base url to ResourceSpace server','https://rs.naturalis.nl/plugins/','2016-04-12 10:03:16','2016-04-12 08:03:16'),(67,0,'rs_new_user_api','Name of RS API to create new RS user','api_new_user_lng','2016-04-12 10:03:16','2016-04-12 08:03:16'),(68,0,'rs_upload_api','Name of RS API to upload to RS','rs_upload_api','2016-04-12 10:03:16','2016-04-12 08:03:16'),(69,0,'rs_search_api','Name of RS API to search RS','rs_search_api','2016-04-12 10:03:16','2016-04-12 08:03:16'),(70,0,'rs_user_key','RS API user key for current project (set dynamically when user is created)',NULL,'2016-04-12 10:03:16','2016-04-12 08:03:16'),(71,0,'rs_collection_id','RS collection ID for current project (set dynamically when user is created)',NULL,'2016-04-12 10:03:16','2016-04-12 08:03:16'),(72,0,'rs_password','RS password (set dynamically when user is created)',NULL,'2016-04-12 10:03:16','2016-04-12 08:03:16'),(73,0,'rs_user_name','RS user name (project name @ server name)',NULL,'2016-04-12 10:03:16','2016-04-12 08:03:16'),(74,-1,'rs_base_url','rs_base_url',NULL,'2016-04-12 10:19:58','2016-04-12 08:19:58'),(75,19,'rs_base_url','Base url to ResourceSpace server','https://rs.naturalis.nl/plugins/','2016-04-12 10:27:44','2016-04-12 08:29:31'),(76,19,'rs_user_key','RS API user key for current project (set dynamically when user is created)',NULL,'2016-04-12 10:28:03','2016-04-12 08:30:33'),(77,19,'rs_collection_id','RS collection ID for current project (set dynamically when user is created)',NULL,'2016-04-12 10:28:15','2016-04-12 08:30:41'),(78,19,'rs_upload_api','Name of RS API to upload to RS','rs_upload_api','2016-04-12 10:28:18','2016-04-12 08:30:07'),(79,19,'rs_new_user_api','Name of RS API to create new RS user','api_new_user_lng','2016-04-12 10:28:22','2016-04-12 08:29:49'),(80,19,'rs_search_api','Name of RS API to search RS','rs_search_api','2016-04-12 10:28:26','2016-04-12 08:30:25'),(81,19,'rs_user_name','RS user name (project name @ server name)',NULL,'2016-04-12 10:28:29','2016-04-12 08:30:55'),(82,19,'rs_password','RS password (set dynamically when user is created)',NULL,'2016-04-12 10:28:33','2016-04-12 08:30:48');
/*!40000 ALTER TABLE `module_settings` ENABLE KEYS */;
UNLOCK TABLES;

insert into module_settings (`module_id`, `setting`, `info`, created) values (-1, 'tree_show_upper_taxon', 'Show the most upper taxon in the taxonomic trees; if set to false, the top of the tree will display the name of the project instead.', CURRENT_TIMESTAMP)



--
-- Dumping data for table `module_settings_values`
--
LOCK TABLES `module_settings_values` WRITE;
/*!40000 ALTER TABLE `module_settings_values` DISABLE KEYS */;
INSERT INTO `module_settings_values` VALUES (1,3,30,'linnaeus_ng','2016-03-15 13:57:50','2016-03-15 11:57:50'),(3,3,58,'../../media/system/skins/linnaeus_ng/','2016-03-15 14:28:18','2016-03-15 12:28:18'),(4,3,10,'0','2016-03-15 14:37:06','2016-03-15 12:43:14'),(5,3,24,'1','2016-03-15 14:39:44','2016-03-15 12:39:44'),(6,3,61,'1','2016-03-15 14:56:24','2016-03-15 12:56:24'),(7,3,4,'0','2016-03-15 14:59:20','2016-03-15 12:59:20'),(8,3,62,'../species/nsr_taxon.php?id=%s','2016-03-15 15:14:48','2016-03-15 13:14:48'),(9,3,64,'_top','2016-03-15 15:31:25','2016-03-15 13:31:25'),(10,1,60,'Alle rechten voorbehouden','2016-04-11 14:29:51','2016-04-11 12:29:51'),(11,1,59,'http://www.nederlandsesoorten.nl/content/gebruiksvoorwaarden-fotos','2016-04-11 14:30:03','2016-04-11 12:30:03'),(12,1,65,'http://localhost/wikkawiki/wikka.php?wakka=%module%#hn_%page%','2016-04-11 14:30:11','2016-04-11 12:30:11'),(13,1,35,'/linnaeus_ng/app/views/species/nsr_taxon.php?id=138998&cat=names','2016-04-11 15:22:57','2016-04-13 07:42:22'),(14,1,30,'nbc_soortenregister','2016-04-11 15:23:50','2016-04-11 13:23:50'),(15,1,66,'https://rs.naturalis.nl/plugins/','2016-04-12 10:03:16','2016-04-12 08:03:16'),(16,1,67,'api_new_user_lng','2016-04-12 10:03:16','2016-04-12 08:03:16'),(17,1,68,'rs_upload_api','2016-04-12 10:03:16','2016-04-12 08:03:16'),(18,1,69,'rs_search_api','2016-04-12 10:03:16','2016-04-12 08:03:16'),(19,1,0,'https://rs.naturalis.nl/plugins/','2016-04-12 10:03:22','2016-04-12 08:03:22'),(20,1,74,'https://d3js.org/bla/','2016-04-12 10:20:20','2016-04-12 08:20:20'),(21,1,75,'https://rs.naturalis.nl/plugins/','2016-04-12 10:31:03','2016-04-12 08:31:03'),(22,1,79,'api_new_user_lng','2016-04-12 10:31:03','2016-04-12 08:31:03'),(23,1,80,'rs_search_api','2016-04-12 10:31:03','2016-04-12 08:31:03'),(24,1,78,'rs_upload_api','2016-04-12 10:31:03','2016-04-12 08:31:03'),(25,1,76,'test_key','2016-04-12 10:31:33','2016-04-12 08:31:33'),(26,1,81,'test_name','2016-04-12 10:31:33','2016-04-12 08:31:33'),(27,1,77,'test_collection_id','2016-04-12 10:31:45','2016-04-12 08:31:45'),(28,1,82,'test_password','2016-04-12 10:31:50','2016-04-12 08:31:50');
/*!40000 ALTER TABLE `module_settings_values` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `modules`
--
LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES
	(1,'Introduction','Comprehensive project introduction','introduction',0,1,1,'2010-08-30 18:18:24','2010-08-30 14:18:24'),
	(2,'Glossary','Project glossary','glossary',2,1,1,'2010-08-30 18:18:24','2010-08-30 14:18:24'),
	(3,'Literature (old)','Literary references','literature',3,0,0,'2010-08-30 18:18:24','2010-08-30 14:18:24'),
	(4,'Species (old)','Detailed pages for taxa','species',4,0,0,'2010-08-30 18:18:24','2010-08-30 14:18:24'),
	(19,'Media','Media management','media',1,1,1,'2016-02-22 00:00:00','2016-02-22 09:02:05'),
	(6,'Dichotomous key','Dichotomic key based on pictures and text','key',6,1,1,'2010-08-30 18:18:24','2010-08-30 14:18:24'),
	(7,'Matrix key','Key based on attributes','matrixkey',7,1,1,'2010-08-30 18:18:24','2010-08-30 14:18:24'),
	(10,'Additional texts','Welcome, About ETI, etc','content',9,0,0,'2011-10-27 14:48:04','2011-10-27 10:48:07'),
	(11,'Index','Index module','index',1,0,1,'2011-10-27 16:27:21','2011-10-27 12:27:24'),
	(12,'Search','Search and replace within all modules.','utilities',10,0,0,'2011-11-17 12:31:32','2011-11-17 10:31:35'),
	(13,'Literature','Literary references','literature2',4,1,1,'2010-08-30 18:18:24','2010-08-30 16:18:24'),
	(14,'Actoren','Actoren: personen & organisaties','actors',0,1,0,'2014-08-28 00:00:00','2014-08-28 13:45:39'),
	(15,'Taxon editor','Taxon editor','nsr',0,1,0,'2014-11-11 12:27:34','2014-11-11 12:27:34'),
	(16,'Traits','Traits','traits',0,1,0,'2015-03-13 10:33:09','2015-03-13 10:33:09'),
	(17,'Project management','Project management','projects',99,1,0,'2016-04-06 17:26:35','2016-04-06 15:26:35'),
	(18,'User management','User management','users',99,1,0,'2016-04-06 17:26:35','2016-04-06 15:26:35');
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `name_types`
--
LOCK TABLES `name_types` WRITE;
/*!40000 ALTER TABLE `name_types` DISABLE KEYS */;
INSERT INTO `name_types` VALUES (1,1,'isValidNameOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(2,1,'isSynonymOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(3,1,'isSynonymSLOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(4,1,'isBasionymOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(5,1,'isHomonymOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(6,1,'isAlternativeNameOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(7,1,'isPreferredNameOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(8,1,'isMisspelledNameOf','2014-05-07 10:35:15','0000-00-00 00:00:00'),(9,1,'isInvalidNameOf','2014-05-07 10:35:15','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `name_types` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `presence`
--
LOCK TABLES `presence` WRITE;
/*!40000 ALTER TABLE `presence` DISABLE KEYS */;
INSERT INTO `presence` VALUES (1,1,'presence statussen',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(2,1,'Exoot. Precieze status nog niet bepaald.',0,'2014-05-07 10:40:54','2014-08-06 10:17:11'),(3,1,'Exoot. Tussen 10 en 100 jaar zelfstandige handhaving.',1,'2014-05-07 10:40:54','2014-05-07 08:43:11'),(4,1,'Exoot. Incidentele import, geen voortplanting.',0,'2014-05-07 10:40:54','2014-05-07 08:43:11'),(5,1,'Exoot. Minder dan 10 jaar zelfstandige handhaving.',0,'2014-05-07 10:40:54','2014-05-07 08:43:11'),(6,1,'Exoot. Minimaal 100 jaar zelfstandige handhaving.',1,'2014-05-07 10:40:54','2014-05-07 08:43:11'),(7,1,'Incidenteel/Periodiek. Minder dan 10 jaar achtereen voortplanting en toevallige gasten.',0,'2014-05-07 10:40:54','2014-05-07 08:43:11'),(8,1,'Oorspronkelijk. Minimaal 10 jaar achtereen voortplanting.',1,'2014-05-07 10:40:54','2014-05-07 08:43:11'),(9,1,'In Nederland. Precieze status nog niet bepaald.',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(10,1,'Onterecht gebruikte naam (auct.)',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(11,1,'Onterecht gemeld.',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(12,1,'Gemeld. Onvoldoende gegevens voor beoordeling.',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(13,1,'Oorspronkelijk. Precieze status nog niet bepaald.',0,'2014-05-07 10:40:54','2014-08-06 10:17:11'),(14,1,'Overig',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(15,1,'Gemeld. Nog niet beoordeeld.',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(16,1,'Verwacht.',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(17,1,'presence statussen sinds 1982',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(18,1,'herverschenen?',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(19,1,'Nog te bepalen',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(20,1,'verdwenen',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(21,1,'verschenen',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(22,1,'verschenen?',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(23,1,'geen verandering',NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(24,1,'Verwachte exoten',NULL,'2014-12-03 09:37:41','2014-12-03 09:37:41');
/*!40000 ALTER TABLE `presence` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `presence_labels`
--
LOCK TABLES `presence_labels` WRITE;
/*!40000 ALTER TABLE `presence_labels` DISABLE KEYS */;
INSERT INTO `presence_labels` VALUES (1,1,1,24,'presence statussen',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(2,1,2,24,'Exoot. Precieze status nog niet bepaald.','Door de mens geïntroduceerd, precieze status moet nog bepaald worden.','Exoot. Precieze status nog niet bepaald.','Exoot (onbepaald)','2','2014-05-07 10:40:54','2014-05-07 08:43:11'),(3,1,3,24,'Exoot. Tussen 10 en 100 jaar zelfstandige handhaving.','Door de mens geïntroduceerd en heeft zich tussen 10 en 100 jaar zelfstandig kunnen handhaven (voortplantend).','Exoot. Tussen 10 en 100 jaar zelfstandige handhaving.','Exoot (10-99 jaar)','2b','2014-05-07 10:40:54','2015-03-06 09:43:28'),(4,1,4,24,'Exoot. Incidentele import, geen voortplanting.','Door de mens geïntroduceerd en zich niet voortplantend. Vaak zullen deze soorten niet worden opgenomen.\n nb. Voor langlevende soorten als bomen alleen 2a Ingeburgerd (min. drie generaties, 3 locaties) en 2c en 2d. Criteria NHN.','Exoot. Incidentele import, geen voortplanting.','Exoot: Incidentele import','2d','2014-05-07 10:40:54','2014-05-07 08:43:11'),(5,1,5,24,'Exoot. Minder dan 10 jaar zelfstandige handhaving.','Door de mens geïntroduceerd en heeft zich minder dan 10 jaar zelfstandig kunnen handhaven (voortplantend).','Exoot. Minder dan 10 jaar zelfstandige handhaving.','Exoot (minder dan 10 jaar)','2c','2014-05-07 10:40:54','2015-03-06 09:43:28'),(6,1,6,24,'Exoot. Minimaal 100 jaar zelfstandige handhaving.','Door de mens geïntroduceerd, en heeft zich minimaal 100 jaar na introductie zelfstandig kunnen handhaven (voortplantend).','Exoot. Minimaal 100 jaar zelfstandige handhaving.','Exoot (minstens 100 jaar)','2a','2014-05-07 10:40:54','2015-03-06 09:43:28'),(7,1,7,24,'Incidenteel/Periodiek. Minder dan 10 jaar achtereen voortplanting en toevallige gasten.','Op eigen kracht ons land bereikt, heeft zich minder dan 10 jaar achtereen voortgeplant. Ook voor toevallige gasten en soorten die periodiek (wintergasten) in ons land voorkomen/kwamen.','Incidenteel/Periodiek. Minder dan 10 jaar achtereen voortplanting en toevallige gasten.','Incidenteel/Periodiek','1b','2014-05-07 10:40:54','2014-05-07 08:43:11'),(8,1,8,24,'Oorspronkelijk. Minimaal 10 jaar achtereen voortplanting.','Op eigen kracht ons land bereikt en heeft zich minimaal 10 jaar achtereen voortgeplant. Deze categorie wordt ook wel Autochtoon genoemd.','Oorspronkelijk. Minimaal 10 jaar achtereen voortplanting.','Oorspronkelijk','1a','2014-05-07 10:40:54','2014-05-07 08:43:11'),(9,1,9,24,'In Nederland. Precieze status nog niet bepaald.','Soort komt in Nederland voor maar de precieze status moet nog worden bepaald.','In Nederland. Precieze status nog niet bepaald.','Correct, te verfijnen','0a','2014-05-07 10:40:54','2014-05-07 08:43:11'),(10,1,10,24,'Onterecht gebruikte naam (auct.)','Onterecht gebruikte naam, bijvoorbeeld als gevolg van een fout in een determinatietabel (auct nec - gevallen). Alleen voor soorten die niet in Nederland voorkomen. Indien bekend is dat de naam voor Nederland is gemeld, dan wordt 3b gekozen. In opmerkingenveld wordt aangeven welke soort bedoeld werd.','Onterecht gebruikte naam (auct.).','Auct','3d','2014-05-07 10:40:54','2014-05-07 08:43:11'),(11,1,11,24,'Onterecht gemeld.','Gemeld voor Nederland, maar onterecht, bijvoorbeeld als gevolg van een determinatiefout.','Onterecht gemeld.','Onterecht gemeld','3b','2014-05-07 10:40:54','2014-05-07 08:43:11'),(12,1,12,24,'Gemeld. Onvoldoende gegevens voor beoordeling.','Gemeld voor Nederland, maar de status is onduidelijk. Bijvoorbeeld namen zonder adequate bronvermelding, incidentele waarnemingen waaraan geen interpretatie te geven is of in de literatuur vermelde twijfelachtige vondsten waarvan geen bewijsmateriaal bewaard is gebleven. Uitleg in opmerkingenveld.','Gemeld. Onvoldoende gegevens voor beoordeling.','Onvoldoende gegevens','3a','2014-05-07 10:40:54','2014-05-07 08:43:11'),(13,1,13,24,'Oorspronkelijk. Precieze status nog niet bepaald.','Op eigen kracht ons land bereikt, precieze status moet nog bepaald worden.','Oorspronkelijk. Precieze status nog niet bepaald.','Oorspronkelijk (onbepaald)','1','2014-05-07 10:40:54','2014-05-07 08:43:11'),(14,1,14,24,'Overig','Namen die niet direct betrekking hebben op Nederland, maar waarvan het om een of andere reden toch wenselijk is dat ze worden opgenomen.','Overig.','overig','4','2014-05-07 10:40:54','2014-05-07 08:43:11'),(15,1,15,24,'Gemeld. Nog niet beoordeeld.','Gemeld voor de Nederlandse lijst, maar nog niet beoordeeld.','Gemeld. Nog niet beoordeeld.','Te beoordelen','0','2014-05-07 10:40:54','2014-05-07 08:43:11'),(16,1,16,24,'Verwacht.','Is niet gemeld voor Nederland, maar komt er mogelijk wel voor (of kan er op korte termijn terecht komen) op basis van waarnemingen in het buitenland.','Verwacht.','Verwacht','3c','2014-05-07 10:40:54','2014-05-07 08:43:11'),(17,1,17,24,'presence statussen sinds 1982',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(18,1,18,24,'herverschenen?',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(19,1,19,24,'Nog te bepalen',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(20,1,20,24,'verdwenen',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(21,1,21,24,'verschenen',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(22,1,22,24,'verschenen?',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(23,1,23,24,'geen verandering',NULL,NULL,NULL,NULL,'2014-05-07 10:40:54','0000-00-00 00:00:00'),(24,1,24,24,'Exoot, verwacht.','Door de mens geïntroduceerd, verwacht.','Exoot, verwacht. ','Exoot (verwacht)','3cE','2014-12-03 09:37:52','2014-12-03 09:37:52');
/*!40000 ALTER TABLE `presence_labels` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `ranks`
--
LOCK TABLES `ranks` WRITE;
/*!40000 ALTER TABLE `ranks` DISABLE KEYS */;
INSERT INTO `ranks` VALUES (1,'regio',NULL,'Empire',NULL,NULL,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:00'),(2,'regnum',NULL,'Kingdom',NULL,1,1,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:00'),(3,'subregnum',NULL,'Subkingdom',NULL,2,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:00'),(4,'branch',NULL,'Branch',NULL,3,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(5,'infrakingdom',NULL,'Infrakingdom',NULL,4,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(6,'superphylum','or superdivision in botany','Superphylum ',NULL,5,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(7,'phylum','or division in botany','Phylum',NULL,6,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(8,'subphylum','or subdivision in botany','Subphylum',NULL,7,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(9,'infraphylum','or infradivision in botany','Infraphylum',NULL,8,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(10,'microphylum',NULL,'Microphylum',NULL,9,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(11,'supercohort','botany','Supercohort',NULL,10,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(12,'cohort','botany','Cohort',NULL,11,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(13,'subcohort','botany','Subcohort',NULL,12,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(14,'infracohort','botany','Infracohort',NULL,13,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(15,'superclass',NULL,'Superclass',NULL,14,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(16,'classis',NULL,'Class',NULL,15,1,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:32'),(17,'subclassis',NULL,'Subclass',NULL,16,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:32'),(18,'infraclass',NULL,'Infraclass',NULL,17,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(19,'parvclass',NULL,'Parvclass',NULL,18,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(20,'superdivisio','zoology','Superdivision',NULL,19,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(21,'divisio','zoology','Division',NULL,20,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(22,'subdivisio','zoology','Subdivision',NULL,21,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(23,'infradivision','zoology','Infradivision',NULL,22,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(24,'superlegion','zoology','Superlegion',NULL,23,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(25,'legion','zoology','Legion',NULL,24,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(26,'sublegion','zoology','Sublegion',NULL,25,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(27,'infralegion','zoology','Infralegion',NULL,26,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(28,'supercohort','zoology','Supercohort',NULL,27,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(29,'cohort','zoology','Cohort',NULL,28,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(30,'subcohort','zoology','Subcohort',NULL,29,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(31,'infracohort','zoology','Infracohort',NULL,30,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(32,'gigaorder','zoology','Gigaorder',NULL,31,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(33,'magnorder or megaorder','zoology','Megaorder',NULL,32,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(34,'grandorder or capaxorder','zoology','Grandorder',NULL,33,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(35,'mirorder or hyperorder','zoology','Hyperorder',NULL,34,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(36,'superorder',NULL,'Superorder',NULL,35,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(37,'series','for fishes','Series',NULL,36,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(38,'ordo',NULL,'Order',NULL,37,1,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(39,'parvorder','position in some  classifications','Parvorder',NULL,38,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(40,'nanorder','zoological','Nanorder',NULL,39,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(41,'hypoorder','zoological','Hypoorder',NULL,40,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(42,'minorder','zoological','Minorder',NULL,41,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(43,'subordo',NULL,'Suborder',NULL,42,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(44,'infraorder',NULL,'Infraorder',NULL,43,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(45,'parvorder','(usual position) or microorder (zoology)','Parvorder',NULL,44,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:47:01'),(46,'sectio','zoology','Section ',NULL,45,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(47,'subsectio','zoology','Subsection',NULL,46,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(48,'gigafamily','zoology','Gigafamily',NULL,47,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(49,'megafamily','zoology','Megafamily',NULL,48,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(50,'grandfamily','zoology','Grandfamily',NULL,49,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(51,'hyperfamily','zoology','Hyperfamily ',NULL,50,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(52,'superfamilia',NULL,'Superfamily',NULL,51,1,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(53,'epifamily','zoology','Epifamily ',NULL,52,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(54,'series','for lepidoptera','Series ',NULL,53,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:47:01'),(55,'group','for lepidoptera','Group',NULL,54,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:47:01'),(56,'familia',NULL,'Family',NULL,55,1,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(57,'subfamilia',NULL,'Subfamily',NULL,56,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(58,'infrafamily',NULL,'Infrafamily',NULL,57,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(59,'supertribe',NULL,'Supertribe',NULL,58,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(60,'tribus',NULL,'Tribe',NULL,59,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(61,'subtribus',NULL,'Subtribe',NULL,60,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(62,'infratribe',NULL,'Infratribe',NULL,61,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(63,'genus',NULL,'Genus',NULL,62,1,1,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(65,'subgenus',NULL,'Subgenus',NULL,63,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(66,'infragenus',NULL,'Infragenus',NULL,65,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(67,'sectio',NULL,'Section',NULL,66,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(68,'subsectio','botany','Subsection',NULL,67,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(69,'series','botany','Series',NULL,68,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(70,'subseries','botany','Subseries',NULL,69,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(71,'superspecies or species-group',NULL,'Species Group',NULL,70,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(72,'species subgroup',NULL,'Species Subgroup',NULL,71,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(73,'species complex',NULL,'Species Complex',NULL,72,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(74,'species aggregate',NULL,'Species Aggregate',NULL,73,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(75,'species',NULL,'Species',NULL,74,1,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(77,'infraspecies',NULL,'Infraspecies',NULL,75,0,1,74,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(78,'subspecific aggregate',NULL,'Subspecific Aggregate',NULL,77,0,1,74,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(79,'subspecies','or forma specialis for fungi, or variety for bacteria','Subspecies','subsp.',78,0,1,74,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(81,'varietas','zoology','Variety','var.',79,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(83,'subvarietas','botany','Subvariety','subvar.',81,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(84,'subsubvarietas',NULL,'Subsubvariety','subsubvar.',83,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(85,'forma','botany','Form','f.',84,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(86,'subforma','botany','Subform','subf.',85,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(87,'subsubforma',NULL,'Subsubform','subsubf.',86,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(88,'candidate',NULL,'Candidate',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(89,'cultivar',NULL,'Cultivar',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(90,'cultivar group',NULL,'Cultivar-group',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(91,'denomination class',NULL,'Denomination Class',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(92,'graft-chimaera',NULL,'Graft-chimaera',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(94,'patho-variety',NULL,'Patho-variety',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(95,'forma_specialis',NULL,'Special form',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(96,'bio-variety',NULL,'Bio-variety',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(64,'nothogenus',NULL,'Nothogenus',NULL,62,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28'),(76,'nothospecies',NULL,'Nothospecies',NULL,74,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28'),(80,'nothosubspecies',NULL,'Nothosubspecies',NULL,78,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28'),(82,'nothovarietas',NULL,'Nothovarietas',NULL,79,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28');
/*!40000 ALTER TABLE `ranks` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `roles`
--
LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'System administrator','ETI admin; creates new projects and lead experts',0,'2010-08-26 08:46:38'),(2,'Lead expert','Project administrator',0,'2010-08-26 08:46:38'),(3,'Editor','Project editor',0,'2010-08-26 08:46:38');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;



UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


