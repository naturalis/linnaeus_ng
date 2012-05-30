<?php

	require_once ('../configuration/app/controllers/Controller.php');
	
	$c = new Controller();

	$s = isset($c->generalSettings['startUpUrl']) ? $c->generalSettings['startUpUrl'] : 'index.php';
	
	// boot sequence goes here
	
	header('Location:'.$s);