<?php

	// this file exists solely to avoid the need to parametrise the imagepath in the mapkey.js javascript file

	require_once('../../../../configuration/app/controllers/Controller.php');
	
	$c = new Controller();

	require_once('skins/'.$c->generalSettings['app']['skinName'].'/map_marker.php');
