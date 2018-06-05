what the program does (globally):
- fetch metadata for new images from beelduitwisselaar-webservice
- check that the image doesn't already exist (by name)
- check if the associated taxon exists (by ID)
- download valid images to local temp folder (over http)
- move downloaded images to remote server (by SCP)
- write image data to NSR database
- request to NSR new image page (for caching)
- write to NSR activity log table (per image) and process log table (per batch)
- write list files to be deleted
- put list of files to be deleted to beelduitwisselaar-server (by SCP)
  there is a separate process on the beelduitwisselaar-server that uses the list to delete the images
  
requires CURL
cron as root
script+keyfile in /usr/sbin/

* / 5 * * * * /usr/bin/php /usr/sbin/beelduitwisselaar-download/beelduitwisselaar-download.php >> /usr/bin/php /usr/sbin/beelduitwisselaar-download/log/beelduitwisselaar-download.log

rights on the remote subfolder:	
drwsr-sr-x	imageupload.www-data	beelduitwisselaartest

development:	http://162.13.81.40/webservice/newimages
productie:		http://95.138.190.198/webservice/newimages

drop table beelduitwisselaar_batches;
create table beelduitwisselaar_batches (
	`id` int(11) not null primary key auto_increment,
	`batch_identifier` varchar(32),
	`project_id` int(11) not null,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`number_in_feed` int(5) not null default 0,
	`number_downloaded` int(5) not null default 0,
	`number_moved` int(5) not null default 0,
	`number_saved` int(5) not null default 0
);

parse json per image:
	[filename] => mug.jpg
	[url] => http://162.13.81.40/sites/default/files/mug.jpg
	[nsrId] => 0DA12C0D482E
	[datePhoto] => 2015-03-13 00:00:00
	[locationPhoto] => Slootje, Lange voorhoud, Den Haag, Zuid Holland
	[description] => dit id eem opmerkiing
	[datePublished] => 2015-03-13 14:26:47
	[photographer] => fotograaf1 fotograaf1
	[photographerContact] => roy.kleukers@naturalis.nl
	[copyrightPhoto] => CC BY-NC-SA (Naamsvermelding - Niet Commercieel - Gelijk Delen)
	[validator] => expert1

