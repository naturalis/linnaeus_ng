insert into projects
  (id,sys_name,sys_description,title,short_name,css_url,includes_hybrids,keywords,description,`group`,published,created,last_change)
values
  (1,'Project internal name','Project description','Project visible title',null,NULL,0,NULL,NULL,NULL,1,now(),now())
;


insert into users
	(id,username,password,first_name,last_name,email_address,active,last_login,logins,last_password_change,created_by,last_change,created) 
values
	(1,'sysadmin','48a365b4ce1e322a55ae9017f3daf0c0','sys','admin','sys@admin.com',1,null,0,null,-1,now(),now())
;


insert into projects_roles_users
	(id,project_id,role_id,user_id,active,last_project_select,project_selects,created)
values
	(1,1,1,1,1,null,0,now())
;



insert ignore into languages_projects (id,language_id,project_id,def_language,active,tranlation_status,created)
	select null,id,1,1,'y',0,now() from languages where language = 'english';



insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,0,now(),now() from  module_settings where setting='show_nsr_specific_stuff' and module_id=-1;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'http://linnaeus.naturalis.nl/wiki/%module%#hn_%page%',now(),now() from  module_settings where setting='wiki_base_url' and module_id=-1;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'linnaeus_ng',now(),now() from  module_settings where setting='skin' and module_id=-1;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'../../media/system/skins/linnaeus_ng/',now(),now() from  module_settings where setting='image_root_skin' and module_id=-1;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'linnaeus@naturalis.nl',now(),now() from  module_settings where setting='support_email' and module_id=-1;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'{"host":"smtp.gmail.com","smtp_auth":1,"username":"noreply@naturalis.nl","password":"********","encryption":"ssl","port":465, "sender_mail" : "noreply@naturalis.nl", "sender_name" : "Linnaeus NG" }',now(),now() from  module_settings where setting='email_settings' and module_id=-1;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'UA-21555206-6',now(),now() from  module_settings where setting='google_analytics_code' and module_id=-1;



insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,5,now(),now() from  module_settings where setting='ext_tab_timeout' and module_id=4;



insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'expand',now(),now() from  module_settings where setting='browse_style' and module_id=7;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,1,now(),now() from  module_settings where setting='allow_empty_species' and module_id=7;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,100,now(),now() from  module_settings where setting='score_threshold' and module_id=7;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,0,now(),now() from  module_settings where setting='show_scores' and module_id=7;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'../species/nsr_taxon.php?id=%s',now(),now() from  module_settings where setting='species_module_link' and module_id=7;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,1,id,'1',now(),now() from  module_settings where setting='show_advanced_search_in_public_menu' and module_id=-1;




insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,'https://resourcespace.naturalis.nl/plugins/',id,0,now(),now() from  module_settings where setting='rs_base_url' and module_id=19;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,'api_new_user_lng',id,0,now(),now() from  module_settings where setting='rs_new_user_api' and module_id=19;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,'api_search_lng',id,0,now(),now() from  module_settings where setting='rs_search_api' and module_id=19;

insert ignore into module_settings_values (id,project_id,setting_id,value,created,last_change)
	select null,'api_upload_lng',id,0,now(),now() from  module_settings where setting='rs_upload_api' and module_id=19;


insert ignore into pages_taxa (id,project_id,page,def_page,external_reference,always_hide,page_blocks,created,last_change)
	values (null,1,'Description',0,NULL,0,NULL,now(),now());

