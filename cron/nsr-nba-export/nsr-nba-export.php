<?php

	//$outdir = "/var/opt/nba-brondata-nsr/"; // root@various-linnaeusng-027:/var/opt# git clone git@git-brondata-nsr:naturalis/nba-brondata-nsr.git
    $outdir = "/var/www/html/tmp/"; // root@various-linnaeusng-027:/var/opt# git clone git@git-brondata-nsr:naturalis/nba-brondata-nsr.git
	$outfilebasename = "nsr-export";
	$filelist = "filelist";
	$tag = date('Y.m.d--H.i.s');
	 

	if (!file_exists($outdir)) {
        if (!mkdir($outdir) && !is_dir($outdir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $outdir));
        }
    }

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
	$fp = fopen( $outdir . $filelist, 'w' );
	fclose($fp);



	include_once(__DIR__ . "/../../configuration/admin/constants.php");
	include_once(__DIR__ . "/../../configuration/admin/configuration.php");

	$c=new configuration;
	$conn=$c->getDatabaseSettings();
	$conn['project_id']=1;

	include_once("class.nsr-nba-export.php");

	ini_set('memory_limit','2048M');

	$b = new taxonXmlExporter;
	$b->setConnectData( $conn );
	$b->setLanguageId( 24 );  // 24 dutch, 26 english (affects image metadata)
	$b->setIdsToSuppressInClassification( [116297] ); // excluding "life"
	$b->setImageBaseUrl( 'https://images.naturalis.nl/original/' );
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
	$filename = 'nsr-' . date('Y-m-d') . '.tar.gz';
	// echo shell_exec( 'cd ' . $outdir . '; tar zcf ' . $filename . ' *.xml');
	echo shell_exec( 'cd ' . $outdir . '; tar zcf ' . $filename . ' *.jsonl');
//  echo shell_exec('/usr/local/bin/mc cp ' . $outdir . $filename . ' s3-nba-brondata/brondata-nsr/');

//	echo "adding\n";
//	echo shell_exec( "cd " . $outdir ."; git add -A" );

//	echo "committing\n";
//	echo shell_exec( "cd " . $outdir ."; git commit -m \"new dataset " . $tag ."\"" );
//	echo shell_exec( "cd " . $outdir ."; ssh-agent bash -c 'ssh-add /root/.ssh/githubkey_nba_data; git push git@github.com:naturalis/nba-brondata-nsr.git'" );
//	echo shell_exec( "cd " . $outdir ."; ssh-agent bash -c 'ssh-add /root/.ssh/githubkey_nba_data; git push origin " . $tag . " git@github.com:naturalis/nba-brondata-nsr.git'" );
//	echo shell_exec( "cd " . $outdir ."; git push git@git-brondata-nsr:naturalis/nba-brondata-nsr.git" );

//	echo "tagging ".$tag."\n";
//	echo shell_exec( "cd " . $outdir ."; git tag " . $tag );
//	echo shell_exec( "cd " . $outdir ."; git push --tags git@git-brondata-nsr:naturalis/nba-brondata-nsr.git" );
//	echo "committing\n";

