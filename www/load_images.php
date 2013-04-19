<?php

	die('eeek!');

	$cfg = '../configuration/app/configuration.php';

	include($cfg);
	
	//delete FROM dev_media_taxon WHERE project_id = 

	$c = new configuration;
	
	$s = $c->getDatabaseSettings();

	$normalImages = 
	array(
		'177943.jpg' => 97510,
		'135731.jpg' => 97510,
		'75408.jpg' => 97510
	);

	$overviewImages = 
		array(
			'207725.jpg' => 97510,
			'w800_207725.jpg' => 97510,
		);

	$img2taxonId = $normalImages;	$overviewImage = 0; $path = 'C:\Users\mschermer\Desktop\determinatiesleutels\02. dierenzoeker\ripped images\images\\';
	$img2taxonId = $overviewImages;	$overviewImage = 1; $path = 'C:\Users\mschermer\Desktop\determinatiesleutels\02. dierenzoeker\ripped images\overview_images\\';

	function alwaysFlush () {
		@apache_setenv('no-gzip', 1);
		@ini_set('zlib.output_compression', 0);
		@ini_set('implicit_flush', 1);
		for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
		ob_implicit_flush(1);
	}

	$cfg = 'configuration/admin/configuration.php';

	$pId = 240;
	
	die('is this project id still correct: '.$pId.'?');
	
	$mime = 'image/jpeg';
	
	
	$tgt = 'C:\Program Files\wamp\www\linnaeus_ng\www\shared\media\project\\'.sprintf('%04s', $pId).'\\';
	


	$d = @mysql_connect($s['host'],$s['user'],$s['password']) or die ('cannot connect');
	@mysql_select_db($s['database'],$d) or die ('cannot select db');

	echo '<pre>';
	
	$vrot = array();

	foreach((array)$img2taxonId as $file => $tId) {
		
		if (file_exists($path.$file)) {
			
			$r = mysql_fetch_array(mysql_query('select id,project_id from dev_taxa where id = '.$tId));

			if (empty($r['id'])) {
				echo 'taxon id not found: '.$tId.chr(10);
			} else
			if ($r['project_id']!=$pId) {
				
				echo 'taxon id part of other project: '.$tId.chr(10);

			} else {
				
				if (isset($img2taxonId['w800_'.$file]) && $img2taxonId['w800_'.$file] == $img2taxonId[$file] && file_exists('w800_'.$file.$file)) {
					echo 'skipped '.$file.' (larger version exists)'.chr(10);
					continue;
				}
				
				$vrot[$tId] = isset($vrot[$tId]) ? $vrot[$tId]+1 : 0;

				$bla = mysql_query("
					INSERT INTO dev_media_taxon 
						(id, project_id, taxon_id, file_name, thumb_name, original_name, mime_type, file_size, sort_order, overview_image, created, last_change) 
					VALUES 
						(NULL, ".$pId.", ".$tId.", '".$file."', null, '".$file."', '".$mime."', ".filesize($path.$file).",".$vrot[$tId].",".$overviewImage.",now(),CURRENT_TIMESTAMP)");
				
				if ($bla) {
					copy($path.$file,$tgt.$file);
					echo 'copied '.$file.chr(10);
				} else {
					echo 'failed '.$file.chr(10);
				}
				
				alwaysFlush();

			}
			
		} else {
			
			echo 'skipped '.$path.$file.' (not found)<br />';
			
		}
		
	}

?>
