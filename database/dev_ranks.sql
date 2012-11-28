-- phpMyAdmin SQL Dump
-- version 2.11.9.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2012 at 03:38 PM
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

--
-- Dumping data for table `dev_ranks`
--

INSERT INTO `dev_ranks` (`id`, `rank`, `additional`, `default_label`, `parent_id`, `in_col`, `can_hybrid`, `ideal_parent_id`, `created`, `last_change`) VALUES
(1, 'Domain or Empire', NULL, 'Empire', NULL, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(2, 'Kingdom', NULL, 'Kingdom', 1, 1, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:38'),
(3, 'Subkingdom', NULL, 'Subkingdom', 2, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(4, 'Branch', NULL, 'Branch', 3, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(5, 'Infrakingdom', NULL, 'Infrakingdom', 4, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(6, 'Superphylum', 'or Superdivision in botany', 'Superphylum ', 5, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(7, 'Phylum', 'or Division in botany', 'Phylum', 6, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(8, 'Subphylum', 'or Subdivision in botany', 'Subphylum', 7, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(9, 'Infraphylum', 'or Infradivision in botany', 'Infraphylum', 8, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(10, 'Microphylum', NULL, 'Microphylum', 9, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(11, 'Supercohort', 'botany', 'Supercohort', 10, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(12, 'Cohort', 'botany', 'Cohort', 11, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(13, 'Subcohort', 'botany', 'Subcohort', 12, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(14, 'Infracohort', 'botany', 'Infracohort', 13, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(15, 'Superclass', NULL, 'Superclass', 14, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(16, 'Class', NULL, 'Class', 15, 1, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:38'),
(17, 'Subclass', NULL, 'Subclass', 16, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(18, 'Infraclass', NULL, 'Infraclass', 17, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(19, 'Parvclass', NULL, 'Parvclass', 18, 0, 0, NULL, '2010-10-14 13:25:37', '2010-10-14 13:25:37'),
(20, 'Superdivision', 'zoology', 'Superdivision', 19, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(21, 'Division', 'zoology', 'Division', 20, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(22, 'Subdivision', 'zoology', 'Subdivision', 21, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(23, 'Infradivision', 'zoology', 'Infradivision', 22, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(24, 'Superlegion', 'zoology', 'Superlegion', 23, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(25, 'Legion', 'zoology', 'Legion', 24, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(26, 'Sublegion', 'zoology', 'Sublegion', 25, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(27, 'Infralegion', 'zoology', 'Infralegion', 26, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(28, 'Supercohort', 'zoology', 'Supercohort', 27, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(29, 'Cohort', 'zoology', 'Cohort', 28, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(30, 'Subcohort', 'zoology', 'Subcohort', 29, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(31, 'Infracohort', 'zoology', 'Infracohort', 30, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(32, 'Gigaorder', 'zoology', 'Gigaorder', 31, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(33, 'Magnorder or Megaorder', 'zoology', 'Megaorder', 32, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(34, 'Grandorder or Capaxorder', 'zoology', 'Grandorder', 33, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(35, 'Mirorder or Hyperorder', 'zoology', 'Hyperorder', 34, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(36, 'Superorder', NULL, 'Superorder', 35, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(37, 'Series', 'for fishes', 'Series', 36, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(38, 'Order', NULL, 'Order', 37, 1, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(39, 'Parvorder', 'position in some  classifications', 'Parvorder', 38, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(40, 'Nanorder', 'zoological', 'Nanorder', 39, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(41, 'Hypoorder', 'zoological', 'Hypoorder', 40, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(42, 'Minorder', 'zoological', 'Minorder', 41, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(43, 'Suborder', NULL, 'Suborder', 42, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(44, 'Infraorder', NULL, 'Infraorder', 43, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(45, 'Parvorder', '(usual position) or Microorder (zoology)', 'Parvorder', 44, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(46, 'Section', 'zoology', 'Section ', 45, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(47, 'Subsection', 'zoology', 'Subsection', 46, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(48, 'Gigafamily', 'zoology', 'Gigafamily', 47, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(49, 'Megafamily', 'zoology', 'Megafamily', 48, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(50, 'Grandfamily', 'zoology', 'Grandfamily', 49, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(51, 'Hyperfamily', 'zoology', 'Hyperfamily ', 50, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(52, 'Superfamily', NULL, 'Superfamily', 51, 1, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(53, 'Epifamily', 'zoology', 'Epifamily ', 52, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(54, 'Series', 'for Lepidoptera', 'Series ', 53, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(55, 'Group', 'for Lepidoptera', 'Group', 54, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(56, 'Family', NULL, 'Family', 55, 1, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(57, 'Subfamily', NULL, 'Subfamily', 56, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(58, 'Infrafamily', NULL, 'Infrafamily', 57, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(59, 'Supertribe', NULL, 'Supertribe', 58, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(60, 'Tribe', NULL, 'Tribe', 59, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(61, 'Subtribe', NULL, 'Subtribe', 60, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(62, 'Infratribe', NULL, 'Infratribe', 61, 0, 0, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(63, 'Genus', NULL, 'Genus', 62, 1, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(64, 'Subgenus', NULL, 'Subgenus', 63, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(65, 'Infragenus', NULL, 'Infragenus', 64, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(66, 'Section', NULL, 'Section', 65, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(67, 'Subsection', 'botany', 'Subsection', 66, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(68, 'Series', 'botany', 'Series', 67, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(69, 'Subseries', 'botany', 'Subseries', 68, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(70, 'Superspecies or Species-group', NULL, 'Species Group', 69, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(71, 'Species Subgroup', NULL, 'Species Subgroup', 70, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(72, 'Species Complex', NULL, 'Species Complex', 71, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(73, 'Species Aggregate', NULL, 'Species Aggregate', 72, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(74, 'Species', NULL, 'Species', 73, 1, 1, 63, '2010-10-14 13:25:38', '2012-11-23 15:36:56'),
(75, 'Infraspecies', NULL, 'Infraspecies', 74, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(76, 'Subspecific Aggregate', NULL, 'Subspecific Aggregate', 75, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(77, 'Subspecies', 'or Forma Specialis for fungi, or Variety for bacteria', 'Subspecies', 76, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(78, 'Varietas/Variety or Form/Morph', 'zoology', 'Variety', 77, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(79, 'Subvariety', 'botany', 'Subvariety', 78, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(80, 'Subsubvariety', NULL, 'Subsubvariety', 79, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(81, 'Forma/Form', 'botany', 'Form', 80, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(82, 'Subform', 'botany', 'Subform', 81, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(83, 'Subsubform', NULL, 'Subsubform', 82, 0, 1, 74, '2010-10-14 13:25:38', '2012-11-23 15:37:39'),
(84, 'Candidate', NULL, 'Candidate', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(85, 'Cultivar', NULL, 'Cultivar', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(86, 'Cultivar-group', NULL, 'Cultivar-group', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(87, 'Denomination Class', NULL, 'Denomination Class', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(88, 'Graft-chimaera', NULL, 'Graft-chimaera', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(89, 'Grex', NULL, 'Grex', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(90, 'Patho-variety', NULL, 'Patho-variety', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(91, 'Special form', NULL, 'Special form', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38'),
(92, 'Bio-variety', NULL, 'Bio-variety', -1, 0, 1, NULL, '2010-10-14 13:25:38', '2010-10-14 13:25:38');
