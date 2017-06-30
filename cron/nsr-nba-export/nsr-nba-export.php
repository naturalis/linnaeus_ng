<?php

	$outdir = "/var/opt/nba-brondata-nsr/";
	$outfilebasename = "nsr-export";
	$filelist = "filelist";
	$compressor = "compress.sh";
	 

	$files = glob( $outdir . $outfilebasename .'*');
	foreach($files as $file)
	{
		if(is_file($file))
		{
			echo "deleting " . $file ."\n";
			unlink($file);
		}
	}

	echo "truncating " . $outdir . $filelist ."\n";
	$fp = fopen( $outdir . $filelist, "w" );
	fclose($fp);

	return;

	include_once("/var/www/linnaeusng/configuration/admin/constants.php");
	include_once("/var/www/linnaeusng/configuration/admin/configuration.php");
//	include_once("C:\www\linnaeus_ng\configuration\admin\constants.php");
//	include_once("C:\www\linnaeus_ng\configuration\admin\configuration.php");

	$c=new configuration;
	$conn=$c->getDatabaseSettings();
	$conn['project_id']=1;

	include_once("class.nsr-nba-export.php");

	$b = new taxonXmlExporter;
	$b->setConnectData( $conn );
	$b->setLanguageId( 24 );  // 24 dutch, 26 english (affects image metadata)
	$b->setIdsToSuppressInClassification( [116297] ); // excluding "life"
	$b->setImageBaseUrl( 'http://images.naturalis.nl/original/' );
	$b->setValidNameTypeId( 1 );
//	$b->setRanksToExport( ['ranks'=>74,'style'=>'and_lower'] );
//	$b->setLimit( 1000 );  // limit on number of taxa
	$b->setXmlRootelementName( 'nederlands_soortenregister' );
	$b->setFileNameBase( $outfilebasename );
	$b->setMaxBatchSize( 10000 ); // records per output file (files are numbered -00, -01 etc)
//	$b->setExportFolder( "C:\\data\\export\\" );
	$b->setExportFolder( $outdir );
	$b->run();
	

	echo "compressing\n";
	echo shell_exec( "sh " . $outdir . $compressor);

	echo "writing to " . $outdir . $filelist ."\n";
	file_put_contents( $outdir . $filelist, implode( PHP_EOL, $b->getFilelist() ) );

	
