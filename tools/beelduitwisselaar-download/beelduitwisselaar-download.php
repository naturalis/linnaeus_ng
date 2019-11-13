<?php

    include_once( __DIR__ . "/config.php" );
    include_once( __DIR__ . "/class.beelduitwisselaar-download.php" );

    $b = new beeldbankDownloader;

    $b->setPrintParameters( false );

    if (!empty($fetchFromDateOverride))
    {
        $b->setFetchFromDateOverride( $fetchFromDateOverride );
    }
    
    if (!empty($urlWebImageSearch))
    {
        $b->seturlWebImageSearch( $urlWebImageSearch );
    }
    //$b->setDoRequestWebImageSearchPages( false );

    //$b->setDoMoveImages( false );
    //$b->setDoDownloadImages( false );
    //$b->setDoWriteToDatabase( false );

    $b->setConnectData( $conn );
    $b->setWebserviceUrl( $webSeviceUrl );
    $b->setFetchLimit( 9999 );

    $b->setScpRemoteUser( $remoteHost['user'] );
    $b->setScpRemoteUserPrivKeyFile( __DIR__ . '/imageupload.key' );
    $b->setScpRemoteAddress( $remoteHost['host'] );
    $b->setScpRemoteBasePath( $remoteHost['path'] );
    $b->setScpRemoteFolder( $remoteHost['folder'] );

    $b->setDoWriteDeleteList( true );
    $b->setScpOriginUser( $origHost['user'] );
    $b->setScpOriginUserPrivKeyFile( __DIR__ . '/imageupload.key' );
    $b->setScpOriginAddress( $origHost['host'] );
    $b->setScpOriginBasePath( $origHost['path'] );

    // Default to Dutch for NSR; added to config for CSR!
    $b->setMetaDataLanguage( !empty($lang) ? $lang : 24 ); 
    $b->run();
