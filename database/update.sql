
select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Artikel';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Article', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Boek';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Book', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Boek (deel)';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Book (part)', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Database';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Database', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Hoofdstuk';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Chapter', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Literatuur';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Literature', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Manuscript';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Manuscript', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Persbericht';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Press release', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Persoonlijke mededeling';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Personal communication', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Rapport';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Report', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Serie';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Series', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Tijdschrift';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Periodical', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Website';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Web site', now(), now());


