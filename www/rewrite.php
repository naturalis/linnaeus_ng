<?php

	$pId = isset($_GET['p']) ? $_GET['p'] : null;
	$url = isset($_GET['u']) ? $_GET['u'] : null;

	if (defined(FIXED_PROJECT_ID))
		$pId=FIXED_PROJECT_ID;

	// silent fail on missing project id or no URL
	//if (is_null($pId) || is_null($url)) // rewrite of drupal search has no URL
	if (is_null($pId))
		return;

	$c=new Controller;
	$file='../configuration/app/rewrite/rewrite-'.$c->getProjectFSCode($pId).'.php';
	
	// silent fail on no file with rewrite actions
	if (!file_exists($file))
		return;

	$parameters=array();

	foreach((array)$_GET as $key=>$val)
	{
		if ($key!='p' && $key!='u')
		{
			$parameters[$key]=$val;
		}
	}

	/*
		available variables:
		$url: original requested URL
		$parameters: query-parameterd of original request (if applicable)
	*/

	include_once($file);
