<?php

	$cfg = 'configuration/app/configuration.php';

	file_exists($cfg) or die('
	  unable to locate configuration-file.
	  this script should be in the root of a linnaeus NG-installation.
	');

	include($cfg);

	$c = new configuration;
	
	$s = $c->getDatabaseSettings();

	echo '
  this script changes the table prefix in a linnaeus NG database.

  reading from config file '.$cfg.'  
  the current database is: '.$s['database'].' @ '.$s['host'].'
  the current table prefix is: '.$s['tablePrefix'].'
  please enter the new table prefix (including optional ending underscore).
  leave the option  blank to remove the current prefix.
  
  ';

	$prefix = prompt('enter prefix (or leave empty to remove): ');

	echo '  about to change the prefix on all tables in '.$s['database'].' from '.$s['tablePrefix'].' to '.($prefix ? $prefix : '(no prefix)').'
  
  ';

	$cont = prompt('continue? (y/n) ', array('y','n'));

	if ($cont!='y') die('
  aborted
	');
	
	$d = @mysql_connect($s['host'],$s['user'],$s['password']) or die ('cannot connect');
	@mysql_select_db($s['database'],$d) or die ('cannot select db');

	$q = mysql_query('show tables');

	while ($r = mysql_fetch_array($q)) {

		$pF = substr($r[0],0,strlen($s['tablePrefix']));
	
		if ($pF==$s['tablePrefix']) {

			$new = $prefix.substr($r[0],strlen($s['tablePrefix']));
			echo '  renaming '.$r[0].' to '.$new.' ... ';
			if (mysql_query('rename table '.$r[0].' to '.$new)==1)
				echo 'done';
			else
				echo 'failed';
			echo chr(10);
		
		} else {
		
			echo '  ignoring '.$r[0].' (has no or wrong prefix)'.chr(10);
		
		}
	
	}
	
	mysql_close();
	
	echo '
  done
	
';

	function prompt($prompt, $valid_inputs=null, $default = '')
	{
		while(
			!isset($input) || 
			(is_array($valid_inputs) && !in_array($input, $valid_inputs)) || 
			(is_null($valid_inputs) && !isset($input)) || 
			($valid_inputs == 'is_file' && !is_file($input))
		) {

			echo $prompt;
			$input = strtolower(trim(fgets(STDIN)));
			if(empty($input) && !empty($default)) {
				$input = $default;
			}
		}
		return $input;
	}

?>