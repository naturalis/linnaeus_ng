<?php

	/*
		script to call from a local AJAX-function
		to circumvent cross-domain policies.
		
		example implementation:

		$.ajax({
			url : "http://localhost/linnaeus_ng/shared/tools/remote_service.php",
			type: "POST",
			data : ({
				url : encodeURIComponent('https://drive.google.com/uc?export=download&id=0B6lEuEkvUQ7PaXE1a1hKWUZlNjQ'),
				original_headers : 1
			}),
			success : function( response )
			{
				var data = $.parseJSON( response );
			}
		});

		make sure that encoding of the url-paramtere bu the calling function matches the decoding one below
		(JS::encodeURIComponent matched PHP::rawurldecode)
	*/

	require_once( "class.RemoteService.php" );
	
	$url = isset( $_REQUEST["url"] ) ? rawurldecode($_REQUEST["url"]) : null;
	$original_headers = isset( $_REQUEST["original_headers"] ) && $_REQUEST["original_headers"]==1 ? true : false;

	$r = new RemoteService;

	$r->setUrl( $url );
	$r->fetchData();
	if ( $original_headers ) $r->sendHeaders();
	$r->printData();
