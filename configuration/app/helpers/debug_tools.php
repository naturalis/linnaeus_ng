<?php


if (function_exists("xdebug_disable")) xdebug_disable();

class DebugTools {}

//q(timeTaken(),1);


function q ($v,$d=false)
{
	echo '<pre>';
    //var_dump($v);
	print_r($v);
    if ($d) die();
}

function markTime(&$list,$marker=null)
{

	$list[] = array((is_null($marker) ? '--'.count((array)$list) : $marker),microtime(true));

}

function getTime(&$list,$output=true)
{

	/*
	
(markTime).*(;)
	
	
		usage:
	
		markTime($this->l,'waypoint 1');
		markTime($this->l,'waypoint 2');

		[...]

		markTime($this->l,'waypoint n');
		getTime($this->l); // includes final timestamp

	*/

	$list[] = array('finish',microtime(true));

	$b = 'timer results:'.chr(10).chr(10);

	$p = $tot = 0;
	
	foreach((array)$list as $val) {
	
		$p = round(($p==0 ? 0 : $val[1]-$p)*1000,4);
		$b .= ($p > 10 ? ($p > 100 ? ($p > 1000 ? '!!!' : ' !!' ) : '  !' ) : '   ' ).' '.$val[0].': '.($p).'ms'.chr(10);
		$tot += $p;
		$p = $val[1];
	
	}
	
	$b .= chr(10).'total: '.$tot.'ms ('.round(($tot/1000),2).'s)';
	
	if ($output) echo chr(10).'<!--'.chr(10).$b;
	if ($output) echo chr(10).'-->'.chr(10);

}

function timeTaken()
{
	global $excStartMicroTime;
	return microtime(true)-$excStartMicroTime;
}