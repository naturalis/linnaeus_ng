/*
 * This script contains database updates for projects created pre-refactoring
 * Before running this script, make sure that check_db.php has run and the database is up-to-date!
 *
 */


/* Update ranks */
set @this_rank = 'genus';
select @this_id := id FROM ranks WHERE rank = @this_rank;
update ranks set id=id+1 WHERE id > @this_id order by id desc;
update ranks set parent_id=parent_id+1 WHERE parent_id > @this_id order by parent_id desc;
update projects_ranks set rank_id=rank_id+1 WHERE rank_id > @this_id order by rank_id desc;
select @parent_id := parent_id FROM ranks WHERE rank = @this_rank;
insert into ranks values (@this_id+1,'nothogenus',null,'Nothogenus',null,@parent_id,0,1,null,now(),now());

set @this_rank = 'species';
select @this_id := id FROM ranks WHERE rank = @this_rank;
update ranks set id=id+1 WHERE id > @this_id order by id desc;
update ranks set parent_id=parent_id+1 WHERE parent_id > @this_id order by parent_id desc;
update projects_ranks set rank_id=rank_id+1 WHERE rank_id > @this_id order by rank_id desc;
select @parent_id := parent_id FROM ranks WHERE rank = @this_rank;
insert into ranks values (@this_id+1,'nothospecies',null,'Nothospecies',null,@parent_id,0,1,null,now(),now());

set @this_rank = 'subspecies';
select @this_id := id FROM ranks WHERE rank = @this_rank;
update ranks set id=id+1 WHERE id > @this_id order by id desc;
update ranks set parent_id=parent_id+1 WHERE parent_id > @this_id order by parent_id desc;
update projects_ranks set rank_id=rank_id+1 WHERE rank_id > @this_id order by rank_id desc;
select @parent_id := parent_id FROM ranks WHERE rank = @this_rank;
insert into ranks values (@this_id+1,'nothosubspecies',null,'Nothosubspecies',null,@parent_id,0,1,null,now(),now());

set @this_rank = 'varietas';
select @this_id := id FROM ranks WHERE rank = @this_rank;
update ranks set id=id+1 WHERE id > @this_id order by id desc;
update ranks set parent_id=parent_id+1 WHERE parent_id > @this_id order by parent_id desc;
update projects_ranks set rank_id=rank_id+1 WHERE rank_id > @this_id order by rank_id desc;
select @parent_id := parent_id FROM ranks WHERE rank = @this_rank;
insert into ranks values (@this_id+1,'nothovarietas',null,'Nothovarietas',null,@parent_id,0,1,null,now(),now());

update ranks set rank = 'forma specialis' where rank = 'forma_specialis';
update ranks set additional = 'fungi' where rank = 'forma specialis';
update ranks set additional = 'botany' where rank in ('cultivar','cultivar group');
update ranks set additional = 'botany, used for taxa of hybrid origin' where rank in ('nothogenus','nothospecies','nothosubspecies','nothovarietas');
delete from ranks where rank = 'subsubforma';


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
(null,-1,'show_nsr_specific_stuff','Show or hide(*) various NSR-specific function',1,NOW(),NOW()),
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
