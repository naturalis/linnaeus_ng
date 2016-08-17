/*
 * This script contains database updates for projects created pre-refactoring
 * Before running this script, make sure that check_db.php has run and the database is up-to-date!
 *
 */


/* Replace ranks */
truncate table ranks;
INSERT INTO `ranks` VALUES (1,'regio',NULL,'Empire',NULL,NULL,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:00'),(2,'regnum',NULL,'Kingdom',NULL,1,1,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:00'),(3,'subregnum',NULL,'Subkingdom',NULL,2,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:00'),(4,'branch',NULL,'Branch',NULL,3,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(5,'infrakingdom',NULL,'Infrakingdom',NULL,4,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(6,'superphylum','or superdivision in botany','Superphylum ',NULL,5,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(7,'phylum','or division in botany','Phylum',NULL,6,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(8,'subphylum','or subdivision in botany','Subphylum',NULL,7,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(9,'infraphylum','or infradivision in botany','Infraphylum',NULL,8,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:47:01'),(10,'microphylum',NULL,'Microphylum',NULL,9,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(11,'supercohort','botany','Supercohort',NULL,10,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(12,'cohort','botany','Cohort',NULL,11,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(13,'subcohort','botany','Subcohort',NULL,12,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(14,'infracohort','botany','Infracohort',NULL,13,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(15,'superclass',NULL,'Superclass',NULL,14,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(16,'classis',NULL,'Class',NULL,15,1,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:32'),(17,'subclassis',NULL,'Subclass',NULL,16,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:46:32'),(18,'infraclass',NULL,'Infraclass',NULL,17,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(19,'parvclass',NULL,'Parvclass',NULL,18,0,0,NULL,'2010-10-14 13:25:37','2013-10-11 08:45:51'),(20,'superdivisio','zoology','Superdivision',NULL,19,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(21,'divisio','zoology','Division',NULL,20,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(22,'subdivisio','zoology','Subdivision',NULL,21,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(23,'infradivision','zoology','Infradivision',NULL,22,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(24,'superlegion','zoology','Superlegion',NULL,23,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(25,'legion','zoology','Legion',NULL,24,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(26,'sublegion','zoology','Sublegion',NULL,25,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(27,'infralegion','zoology','Infralegion',NULL,26,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(28,'supercohort','zoology','Supercohort',NULL,27,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(29,'cohort','zoology','Cohort',NULL,28,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(30,'subcohort','zoology','Subcohort',NULL,29,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(31,'infracohort','zoology','Infracohort',NULL,30,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(32,'gigaorder','zoology','Gigaorder',NULL,31,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(33,'magnorder or megaorder','zoology','Megaorder',NULL,32,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(34,'grandorder or capaxorder','zoology','Grandorder',NULL,33,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(35,'mirorder or hyperorder','zoology','Hyperorder',NULL,34,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(36,'superorder',NULL,'Superorder',NULL,35,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(37,'series','for fishes','Series',NULL,36,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(38,'ordo',NULL,'Order',NULL,37,1,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(39,'parvorder','position in some  classifications','Parvorder',NULL,38,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(40,'nanorder','zoological','Nanorder',NULL,39,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(41,'hypoorder','zoological','Hypoorder',NULL,40,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(42,'minorder','zoological','Minorder',NULL,41,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(43,'subordo',NULL,'Suborder',NULL,42,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(44,'infraorder',NULL,'Infraorder',NULL,43,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(45,'parvorder','(usual position) or microorder (zoology)','Parvorder',NULL,44,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:47:01'),(46,'sectio','zoology','Section ',NULL,45,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(47,'subsectio','zoology','Subsection',NULL,46,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(48,'gigafamily','zoology','Gigafamily',NULL,47,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(49,'megafamily','zoology','Megafamily',NULL,48,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(50,'grandfamily','zoology','Grandfamily',NULL,49,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(51,'hyperfamily','zoology','Hyperfamily ',NULL,50,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(52,'superfamilia',NULL,'Superfamily',NULL,51,1,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(53,'epifamily','zoology','Epifamily ',NULL,52,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(54,'series','for lepidoptera','Series ',NULL,53,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:47:01'),(55,'group','for lepidoptera','Group',NULL,54,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:47:01'),(56,'familia',NULL,'Family',NULL,55,1,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(57,'subfamilia',NULL,'Subfamily',NULL,56,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(58,'infrafamily',NULL,'Infrafamily',NULL,57,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(59,'supertribe',NULL,'Supertribe',NULL,58,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(60,'tribus',NULL,'Tribe',NULL,59,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(61,'subtribus',NULL,'Subtribe',NULL,60,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:46:32'),(62,'infratribe',NULL,'Infratribe',NULL,61,0,0,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(63,'genus',NULL,'Genus',NULL,62,1,1,NULL,'2010-10-14 13:25:38','2013-10-11 08:45:51'),(65,'subgenus',NULL,'Subgenus',NULL,63,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(66,'infragenus',NULL,'Infragenus',NULL,65,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(67,'sectio',NULL,'Section',NULL,66,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(68,'subsectio','botany','Subsection',NULL,67,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(69,'series','botany','Series',NULL,68,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(70,'subseries','botany','Subseries',NULL,69,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(71,'superspecies or species-group',NULL,'Species Group',NULL,70,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(72,'species subgroup',NULL,'Species Subgroup',NULL,71,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(73,'species complex',NULL,'Species Complex',NULL,72,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(74,'species aggregate',NULL,'Species Aggregate',NULL,73,0,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(75,'species',NULL,'Species',NULL,74,1,1,63,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(77,'infraspecies',NULL,'Infraspecies',NULL,75,0,1,74,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(78,'subspecific aggregate',NULL,'Subspecific Aggregate',NULL,77,0,1,74,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(79,'subspecies','or forma specialis for fungi, or variety for bacteria','Subspecies','subsp.',78,0,1,74,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(81,'varietas','zoology','Variety','var.',79,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(83,'subvarietas','botany','Subvariety','subvar.',81,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(84,'subsubvarietas',NULL,'Subsubvariety','subsubvar.',83,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(85,'forma','botany','Form','f.',84,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(86,'subforma','botany','Subform','subf.',85,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),
(88,'candidate',NULL,'Candidate',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),
(89,'cultivar','botany','Cultivar',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),
(90,'cultivar group','botany','Cultivar-group',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(91,'denomination class',NULL,'Denomination Class',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(92,'graft-chimaera',NULL,'Graft-chimaera',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(94,'patho-variety',NULL,'Patho-variety',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),
(95,'forma specialis','fungi','forma specialis',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),(96,'bio-variety',NULL,'Bio-variety',NULL,-1,0,1,NULL,'2010-10-14 13:25:38','2016-04-11 13:26:28'),
(64,'nothogenus','botany, used for taxa of hybrid origin','Nothogenus',NULL,62,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28'),
(76,'nothospecies','botany, used for taxa of hybrid origin','Nothospecies',NULL,74,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28'),
(80,'nothosubspecies','botany, used for taxa of hybrid origin','Nothosubspecies',NULL,78,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28'),
(82,'nothovarietas','botany, used for taxa of hybrid origin','Nothovarietas',NULL,79,0,1,NULL,'2016-04-11 15:26:28','2016-04-11 13:26:28');



/* Update modules */
ALTER TABLE modules DROP INDEX module;
ALTER TABLE modules ADD UNIQUE INDEX (module);
update modules set module='Taxon editor' where controller = 'nsr';
update modules set show_in_menu = 0 where controller in ('index','utilities','content');
update modules set module='Literature (old)' where controller = 'literature';
update modules set module='Literature' where controller = 'literature2';
insert ignore into modules values (null,'Project management','Project management','projects',99,1,0,now(),now());
insert ignore into modules values (null,'User management','User management','users',99,1,0,now(),now());
insert ignore into modules values (null,'Media','Media management','media',99,1,0,now(),now());
insert ignore into modules values (null,'Taxon editor','Taxon editor','nsr',99,1,0,now(),now());
insert ignore into modules values (null,'Actors','Actors: persons & organizations','actors',99,1,0,now(),now());

update modules set show_in_menu=0, description='Front-end implementation of the Taxon Editor' where controller='species';
update modules set description='Taxon editor, back-end implementation of Species module' where controller='nsr';
update modules set show_in_menu=0 where controller='literature';
update modules set show_in_menu=0 where controller='highertaxa';
update modules set module='Actors' where controller = 'actors';
update modules set module='Traits' where controller = 'traits';


/* Update roles */
delete from roles where id in (4,5);
update roles set role='Editor' where id = 3;
update roles set description='System administrator' where id =1;
update roles set description='Project administrator' where id =2;
update roles set description='Project editor' where id =3;
update projects_roles_users set role_id = 1 where user_id = (select id from users where username = 'sysadmin');



/* Matrix */
update characteristics _a set _a.sys_name = (select _b.label from characteristics_labels _b where _a.id=_b.characteristic_id limit 1);
update characteristics_states _a set _a.sys_name = (select _b.label from characteristics_labels_states _b where _a.id=_b.state_id limit 1);



/* moving welcome and contributors content to introduction */
select @welcome_subject := subject, @welcome_content := content, @language_id := language_id , @project_id := project_id from content where subject = 'Welcome';
select @contributors_subject := subject, @contributors_content := content from content where subject = 'Contributors';

select ifnull(max(id)+1,0) into @next_intro_page_id from introduction_pages;
insert into introduction_pages (id, project_id, got_content, show_order, hide_from_index, created, last_change) values (@next_intro_page_id,@project_id,1,99,1,now(),now());
insert into introduction_pages (id, project_id, got_content, show_order, hide_from_index, created, last_change) values (@next_intro_page_id+1,@project_id,1,99,1,now(),now());
insert into content_introduction values (null,@project_id,@next_intro_page_id,@language_id,@welcome_subject,@welcome_content,now(),now());
insert into content_introduction values (null,@project_id,@next_intro_page_id+1,@language_id,@contributors_subject,@contributors_content,now(),now());


/* Update module settings */
TRUNCATE TABLE `module_settings`;
LOCK TABLES `module_settings` WRITE;
INSERT IGNORE INTO `module_settings` VALUES
(1,7,'calc_char_h_val','do or don\'t calculate the H-value for characters (disabling increases performance) [0,1]','1',NOW(),NOW()),
(2,7,'allow_empty_species','allow empty species (species with no content in the species module) to appear in the matrix (L2 legacy keys only) [0,1]','1',NOW(),NOW()),
(4,7,'use_emerging_characters','disable characters as long as their states do not apply to all remaining species/taxa. [0,1]','1',NOW(),NOW()),
(5,7,'use_character_groups','allow characters to be organised in groups. [0,1]','1',NOW(),NOW()),
(6,7,'browse_style','style of browsing through result sets [expand, paginate, show_all]','expand',NOW(),NOW()),
(7,7,'items_per_line','number of resulting species per line [number]','4',NOW(),NOW()),
(8,7,'items_per_page','number of resulting species per page (no effect when browse_style = \'show_all\') [number]','16',NOW(),NOW()),
(9,7,'always_show_details','icon for species characters normally only appears when resultset <= items_per_page. set this to 1 to always display the icon, regardless of the size of the resultset. [0,1]','1',NOW(),NOW()),
(10,7,'score_threshold','threshold of match percentage during identifying above which species displayed. setting to 100 only shows full matches, i.e. species that have all selected states. [0...100]','100',NOW(),NOW()),
(12,7,'img_to_thumb_regexp_pattern','reg exp replace pattern to match the URL of a species normal image (from the nsr_extras table) against for automatic creation of a thumbnail URL. works in unison with \'img_to_thumb_regexp_replacement\'. take *great* care that the reg exp is valid and properly escaped, as there is currently no check on its validity, and a broken reg exp will cause errors.\r\nthe default applies specifically to NSR-related keys.\r\n','/http:\\/\\/images.naturalis.nl\\/original\\//',NOW(),NOW()),
(13,7,'img_to_thumb_regexp_replacement','replacement string for the reg exp in \r\n\'img_to_thumb_regexp_pattern\' (see there). can be empty!','http://images.naturalis.nl/comping/',NOW(),NOW()),
(15,7,'initial_sort_column','column to initially sort the data set on (without settting, program sorts on scientific name)',NULL,NOW(),NOW()),
(16,7,'always_sort_by_initial','sort result set on \'initial_sort_column\' after matching percentages have been calculated (default behaviour is sorting by match percentage) [1,0]','0',NOW(),NOW()),
(17,7,'species_info_url','external URL for further info on a species. overrides the species-specific URL from the nsr_extras table as link under the info-icon (though in some skins that URL is also displayed in the details pop-up). expects a webservice URL that returns a JSON-object that at least has an element \'page\' with an element \'body\'. URL can be parametrised with %TAXON% (scientific name, key) and, optionally, %PID% (project ID). example:\r\n\r\nhttp://www.nederlandssoortenregister.nl/linnaeus_ng/app/views/webservices/taxon_page?pid=1&taxon=%TAXON%&cat=163',NULL,NOW(),NOW()),
(18,7,'introduction_topic_colophon_citation','topic name of the page from the introduction module to be used as colophon and citation-info.','Matrix colophon & citation',NOW(),NOW()),
(19,7,'introduction_topic_versions','topic name of the page from the introduction module to be used as version history.','Matrix version history',NOW(),NOW()),
(20,7,'introduction_topic_inline_info','topic name of the page from the introduction module containing additional info, to be displayed inline beneath the legend.','Matrix additional info',NOW(),NOW()),
(21,7,'popup_species_link_text','text for the remote link that appears in the pop-up that shows the info retrieved with species_info_url. only relevant when species_info_url is defined and if there\'s a species-specific info-URL in the nsr_extras as well. note: strictly speaking, this is not the right place for something purely textually, as setting-values are not considered to be language-dependent. oh well.','Meer details',NOW(),NOW()),
(23,7,'image_orientation','orientation of taxon images in search results of matrix key [landscape, portrait]','portrait',NOW(),NOW()),
(24,7,'show_scores','show the matching percentage in the results (only useful when score_threshold is below 100). [0,1]','0',NOW(),NOW()),
(25,7,'enable_treat_unknowns_as_matches','enables the function \"treat unknowns as matches\", which scores a taxon for which no state has been defined within a certain character as a match for that character (a sort of \"rather safe than sorry\"-setting). [0,1]','0',NOW(),NOW()),
(28,7,'suppress_details','suppresses retrieval and displaying of all character states for each item in de dataset. siginificantly reduces the footprint of the initial data-load. [0,1]','0',NOW(),NOW()),
(29,7,'similar_species_show_distinct_details_only','when displaying similar species or search results, normally all details are displayed, rather than only the distinct details of each species. set this setting to 1 to switch to distinct-only.','1',NOW(),NOW()),
(30,-1,'skin','styling of graphical interface of application front-end.','linnaeus_ng',NOW(),NOW()),
(31,-1,'skin_mobile','styling of graphical interface of application front-end, specific for mobile devices.',NULL,NOW(),NOW()),
(32,-1,'skin_gsm','styling of graphical interface of application front-end, specific for mobile phones (overrides \'skin_mobile\').',NULL,NOW(),NOW()),
(33,-1,'force_skin_mobile','force the use of skin_mobile (if defined), even if values for skin or skin_gsm have been defined.','0',NOW(),NOW()),
(34,-1,'suppress_restore_state','suppress the restoring of a module\'s earlier state from the same session when re-accessing the module (front-end only).','0',NOW(),NOW()),
(35,-1,'start_page','specific URL (relative) to redirect to when a user first opens the application (front-end).','/linnaeus_ng/app/views/linnaeus/',NOW(),NOW()),
(36,-1,'db_lc_time_names','MySQL locale for date and time names.','nl_NL',NOW(),NOW()),
(37,12,'url_help_search_presence','URL of the user help for the search category \"presence\" (NSR specific).',NULL,NOW(),NOW()),
(38,4,'use_taxon_variations','allow the use of taxon variations (currently in use in the matrix key only)',NULL,NOW(),NOW()),
(39,4,'base_url_images_main','base URL of main image in NSR-style search results.',NULL,NOW(),NOW()),
(40,4,'base_url_images_thumb','base URL of thumb images in NSR-style search results.',NULL,NOW(),NOW()),
(41,4,'base_url_images_overview','base URL of overview images in NSR-style search results.',NULL,NOW(),NOW()),
(43,4,'base_url_images_thumb_s','base URL of smaller thumb images in NSR-style search results.',NULL,NOW(),NOW()),
(44,-1,'taxon_main_image_base_url','taxon_main_image_base_url (needs to be re-examined)',NULL,NOW(),NOW()),
(45,4,'taxon_fetch_ez_data','taxon_fetch_ez_data (should be re-examined)',NULL,NOW(),NOW()),
(46,4,'include_overview_in_media','include the overview image in the general media page of a taxon as well.',NULL,NOW(),NOW()),
(47,4,'lookup_list_species_max_results','max. results in species lookup list (front-end)',NULL,NOW(),NOW()),
(48,13,'literature2_import_match_threshold','default matching threshold for literature bulk import (percentage).','75',NOW(),NOW()),
(49,6,'keytype','l2 or lng (not sure what the difference is anymore)',NULL,NOW(),NOW()),
(50,7,'matrixtype','nbc (EIS-style) or lng (old L2-style). when the old style disappears, this will become obsolete.',NULL,NOW(),NOW()),
(51,-1,'image_root_skin','root of the image files that come with the skin',NULL,NOW(),NOW()),
(52,12,'min_search_length','minimum length of search string','3',NOW(),NOW()),
(53,12,'max_search_length','maximum length of search string','50',NOW(),NOW()),
(54,12,'excerpt_pre-match_length','length of the displayed text excerpt preceding a search result.','35',NOW(),NOW()),
(55,12,'excerpt_post-match_length','length of the displayed text excerpt following a search result.','35',NOW(),NOW()),
(56,12,'excerpt_pre_post_match_string','text string to embed preceding and following text with','...',NOW(),NOW()),
(58,7,'image_root_skin','relative image root of the skin-images.','../../media/system/skins/responsive_matrix/',NOW(),NOW()),
(59,-1,'url_to_picture_license_info','URL to the page explaining the various picture licensing options (be aware, the same setting also exists, and should also be mainained, in the \'species\' module).\r\n',NULL,NOW(),NOW()),
(60,-1,'picture_license_default','the default license shown for pictures for which no license has been specified in the meta-data.',NULL,NOW(),NOW()),
(61,7,'use_overview_image','use overview image from the species module as main species image.',NULL,NOW(),NOW()),
(62,7,'species_module_link','link to use for the info-link when none is available for the taxon in the database. can be parametrised with %s for substitution of the taxon ID. note: \'species_info_url\' gets precedence.\n\n','../species/nsr_taxon.php?id=%s',NOW(),NOW()),
(63,7,'species_module_link_force','link to use for the info-link, even when there is one available in the database. can be parametrised with %s for substitution of the taxon ID.  note: \'species_info_url\' gets precedence.','../species/nsr_taxon.php?id=%s',NOW(),NOW()),
(64,7,'info_link_target','target of the info-link when retrieved from the database or specified by \'species_module_link\' or \'species_module_link_force\'. has no effect if \'species_info_url\' is defined, as that setting takes precedence and causes taxon-info to be displayed in a pop-up. leave blank for _blank (ha).',NULL,NOW(),NOW()),
(65,-1,'wiki_base_url',' Base URL to the help Wiki. Can be parametrized with %module% (translated to controllerPublicName) and %page% (translated to pageName)','http://linnaeus.naturalis.nl/wiki/%module%#hn_%page%',NOW(),NOW()),
(75,19,'rs_base_url','Base url to ResourceSpace server','https://resourcespace.naturalis.nl/plugins/',NOW(),NOW()),
(76,19,'rs_user_key','RS API user key for current project (set dynamically when user is created)',NULL,NOW(),NOW()),
(77,19,'rs_collection_id','RS collection ID for current project (set dynamically when user is created)',NULL,NOW(),NOW()),
(78,19,'rs_upload_api','Name of RS API to upload to RS','api_upload_lng',NOW(),NOW()),
(79,19,'rs_new_user_api','Name of RS API to create new RS user','api_new_user_lng',NOW(),NOW()),
(80,19,'rs_search_api','Name of RS API to search RS','api_search_lng',NOW(),NOW()),
(81,19,'rs_user_name','RS user name (project name @ server name)',NULL,NOW(),NOW()),
(82,19,'rs_password','RS password (set dynamically when user is created)',NULL,NOW(),NOW()),
(83,-1, 'tree_show_upper_taxon', 'Show the most upper taxon in the taxonomic tree; if set to false, the top of the tree will display the name of the project instead.', NULL, NOW(), NOW()),
(null,1, 'welcome_topic_id', 'ID of the page with the old migrated welcome-page', null, NOW(), NOW()),
(null,4,'obsolete_passport_tabs','Legacy tab titles that should be flagged as obsolete in the passport editor (use JSON-string: {"Old":"New","Totally obsolete":null})',NULL,NOW(),NOW()),
(null,7,'no_taxon_images','Make no attempt to show images for taxa',1,NOW(),NOW()),
(null,4,'higher_taxa_rank_prefix','Always prefix the taxon name with the rank for higher species',1,NOW(),NOW()),
(null,-1,'show_nsr_specific_stuff','Show or hide(*) various NSR-specific function',0,NOW(),NOW()),
(null,-1,'show_automatic_hybrid_markers','Show or hide automatic × marker for taxa of hybrid ranks',1,NOW(),NOW()),
(null,-1,'show_automatic_infixes','Show or hide automatic infixes "var.", "subsp." and "f." for taxa of appropriate ranks',1,NOW(),NOW()),
(null,-1,'concept_base_url','Base URL for concepts (requires the project generates NSR-style pseudo PURLs)',null,NOW(),NOW()),
(null,-1,'show_advanced_search_in_public_menu','Show advanced search link in public menu',1,NOW(),NOW()),
(null,1,'no_media','Don\'t use media module in the Introduction.',1,NOW(),NOW()),
(null,7,'no_media','Don\'t use media module in the Matrix.',1,NOW(),NOW())
;


UNLOCK TABLES;

/*
TRUNCATE TABLE `module_settings_values`;
LOCK TABLES `module_settings_values` WRITE;
INSERT INTO `module_settings_values` VALUES
(1,3,30,'linnaeus_ng',NOW(),NOW()),
(3,3,58,'../../media/system/skins/linnaeus_ng/',NOW(),NOW()),
(4,3,10,'0',NOW(),NOW()),
(5,3,24,'1',NOW(),NOW()),
(6,3,61,'1',NOW(),NOW()),
(7,3,4,'0',NOW(),NOW()),
(8,3,62,'../species/nsr_taxon.php?id=%s',NOW(),NOW()),
(9,3,64,'_top',NOW(),NOW()),
(10,1,60,'Alle rechten voorbehouden',NOW(),NOW()),
(11,1,59,'http://www.nederlandsesoorten.nl/content/gebruiksvoorwaarden-fotos',NOW(),NOW()),
(12,1,65,'http://linnaeus.naturalis.nl/wiki/%module%#hn_%page%',NOW(),NOW()),
(13,1,35,'/linnaeus_ng/app/views/species/nsr_taxon.php',NOW(),NOW()),
(14,1,30,'nbc_soortenregister',NOW(),NOW()),
(21,1,75,'https://resourcespace.naturalis.nl/plugins/',NOW(),NOW()),
(22,1,79,'api_new_user_lng',NOW(),NOW()),
(23,1,80,'api_search_lng',NOW(),NOW()),
(24,1,78,'api_upload_lng',NOW(),NOW());
UNLOCK TABLES;

*/
