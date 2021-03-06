<?php
	/*
		generic endpoint for LNG-data delivered outside of the LNG-codebase
		call this file with paremeter 'set=name':
		
		http://localhost/linnaeus_ng/external.php?set=name
	
		and it will look for and include a file 'external/name.php'
		
		in that file, there must be a class called 'ExternalService' with at
		least the following functions:

		- setDatabaseSettings(): receives dB host, user, pass etc in the same
		  format as te output of Configuration::getDatabaseSettings()
		- setParameters(): receives all REQUEST-parameters
		- httpHeaders(): for outputting extra headers, for instance 
		  header('Content-Type: application/json'))
		- main(): main program
		- getOutput(): returns output of the service, should be printable "as is" 

		these are the only four possible interfaces with this file, but they 
		don't _need_ to exist.

		the files in 'external' (and indeed the directory itself) are excluded
		from the LNG GIT-repo as they are project-specific.

		do NOT alter THIS file to achieve some sort of output-effect,
		conditionality or other logic. it is meant to be as barebones as
		possible and should stay like that for maximum compatibility.
	*/


	$set=isset($_REQUEST['set']) ? $_REQUEST['set'] : null;

	if (is_null($set)) pageNotFound();

	$set=strtolower(preg_replace('/\W/','', $set));

	include_once('../configuration/app/configuration.php');

	$c=new Configuration;

	$s=$c->getGeneralSettings();

	$file=$s['lngFileRoot'] . '/external/'.$set.'.php';

	if (!file_exists($file)) pageNotFound();

	include_once($file);

	if (!class_exists('ExternalService')) pageNotFound();

	$e=new ExternalService;

	if ( method_exists($e,'setDatabaseSettings') )
		$e->setDatabaseSettings( $c->getDatabaseSettings() );

	if ( method_exists($e,'setParameters') )
		$e->setParameters( $_REQUEST );

	if ( method_exists($e,'main') ) 
		$e->main();

	if ( method_exists($e,'httpHeaders') ) 
		$e->httpHeaders();

	if ( method_exists($e,'getOutput') ) 
		echo $e->getOutput();

	function pageNotFound()
	{
		header('HTTP/1.0 404 Not Found', true, 404);
		die();
	}
