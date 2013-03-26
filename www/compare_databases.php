<?php

	$e = $p = array();
	$bConn = $cConn = false;

	if (isset($_REQUEST['action'])) {
		
		if (connect($_REQUEST)) {

		$d = getBaselineTables();


/*
get tables
per table
	get columns
	per column
		get type
		get length
		get nullable
		get default 
	get indexes
get views


*/



		} else {
		
			$e[] = 'process terminated (failed to connect)';
			
		}

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<style>
input {
	font-family:inherit;
	font-size:inherit;	
	width:225px;
}
h4 {
	margin-bottom:3px;	
}
</style>
<body style="font-family:'Lucida Console', Monaco, monospace;font-size:11px;background-color:black;color:yellow">
<form method="post">
<p>
<h4>baseline database (db1)</h4>
    user: <input type="text" name="bUser" value="linnaeus_user" /><br />
    pass: <input type="text" name="bPass" value="car0lu5" /><br />
    host: <input type="text" name="bHost" value="localhost" /><br />
    pref: <input type="text" name="bPref" value="dev_" /><br />
</p>
<p>

<h4>database to check (db2)</h4>
    user: <input type="text" name="cUser" value="<?php echo isset($_REQUEST['cUser']) ? $_REQUEST['cUser'] : 'root' ?>" /><br />
    pass: <input type="text" name="cPass" value="<?php echo isset($_REQUEST['cPass']) ? $_REQUEST['cPass'] : 'dUra1dal' ?>" /><br />
    host: <input type="text" name="cHost" value="<?php echo @$_REQUEST['cHost']; ?>" /><br />
    pref: <input type="text" name="cPref" value="dev_" /><br />
</p>
<p>
	<input type="submit" name="action" value="compare" style="background-color:#888;color:yellow" />
</p>
</form>
<?php

	if ($e) {
		echo'<h4>errors</h4>';
		foreach($e as $error)
			echo $error.'<br />';
	}
	if ($p) {
		echo'<h4>progress</h4>';
		foreach($p as $progres)
			echo $progres.'<br />';
	}

?>
</body>
</html>
<?php



 
	function alwaysFlush () {
		@apache_setenv('no-gzip', 1);
			@ini_set('zlib.output_compression', 0);
			@ini_set('implicit_flush', 1);
			for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
			ob_implicit_flush(1);
	}














die();

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

	function connect($vars)
	{
	
		global $bConn, $cConn, $e, $p;

		if ($vars['bHost'] && $vars['bUser'] && $vars['bPass']) {
			$bConn = @mysql_connect($vars['bHost'],$vars['bUser'],$vars['bPass']);
			if ($bConn)
				$p[] = 'connected to db1';
			else
				$e[] = 'failed to connect to db1';
		} else {
			$e[] = 'incomplete data for db1';
		}

		if ($_REQUEST['cHost'] && $vars['cUser'] && $vars['cPass']) {
			$cConn = @mysql_connect($vars['cHost'],$vars['cUser'],$vars['cPass']);
			if ($cConn)
				$p[] = 'connected to db2';
			else
				$e[] = 'failed to connect to db2';
		} else {
			$e[] = 'incomplete data for db2';
		}
		
		if ($bConn) {
			mysql_select_db('linnaeus_ng',$bConn);
			$p[] = 'selected db1';
		} else {
			$e[] = 'failed to select db1';
		}

		if ($cConn) {
			mysql_select_db('linnaeus_ng',$cConn);
			$p[] = 'selected db2';
		} else {
			$e[] = 'failed to select db2';
		}
		
		return ($bConn && $cConn);
	
	}

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

function getBaselineTables()
{
	
	global $p;
	
	$tables = array();
	
	$q = mysql_query('show tables');
	while($row = mysql_fetch_array($q)) {

		$tables[] = array('name' => $row[0]);
		$p[] = $row[0];
		
	}
	
}


?>