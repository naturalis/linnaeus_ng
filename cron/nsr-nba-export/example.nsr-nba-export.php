<?php

	$outdir = "/var/opt/nba-brondata-nsr/";
	$outfilebasename = "nsr-export";
	$filelist = "filelist";
	$compressor = "compress.sh";
	$tag = date('Y.m.d--H.i.s');
	 

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
	$fp = fopen( $outdir . $filelist, 'wb');
	fclose($fp);



	include_once("/var/www/linnaeusng/configuration/admin/constants.php");
	include_once("/var/www/linnaeusng/configuration/admin/configuration.php");

	$c=new configuration;
	$conn=$c->getDatabaseSettings();
	$conn['project_id']=1;

	include_once("class.nsr-nba-export.php");

	ini_set('memory_limit','2048M');

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
	$b->setExportFolder( $outdir );
	$b->run();
	
	echo "writing to " . $outdir . $filelist ."\n";
	file_put_contents( $outdir . $filelist, implode( PHP_EOL, $b->getFilelist() ) );

	echo "compressing\n";
	echo shell_exec( "cd " . $outdir ."; sh ./" . $compressor );

	echo "adding\n";
	echo shell_exec( "cd " . $outdir ."; git add -A" );

	echo "tagging ".$tag."\n";
	echo shell_exec( "cd " . $outdir ."; git tag " . $tag );

	echo "committing\n";
	echo shell_exec( "cd " . $outdir ."; git commit -m \"new dataset " . $tag ."\"" );
	echo shell_exec( "cd " . $outdir ."; ssh-agent bash -c 'ssh-add /root/.ssh/githubkey_nba_data; git push git@github.com:naturalis/nba-brondata-nsr.git'" );
//  not working properly
//	echo shell_exec( "cd " . $outdir ."; ssh-agent bash -c 'ssh-add /root/.ssh/githubkey_nba_data; git push origin " . $tag . " git@github.com:naturalis/nba-brondata-nsr.git'" );
	echo "committing\n";
