<?php


$image_server = getEnv("IMAGE_SERVER_ADDRESS") ?? "images.naturalis.nl";


$conn = array( 
    'host'=>'localhost', 
    'user'=>'db_user', 
    'password'=>'db_password', 
    'database'=>'linnaeusng', 
    'prefix'=>'', 
    'project_id'=>1 
);

$webSeviceUrl='http://natuurinbeeld.nederlandsesoorten.nl/webservice/newimages?date=%DATE%&limit=%LIMIT%';
	

$origHost = [
     'user' => '',
     'host' => '',
     'path' => ''
];
$remoteHost = [
     'user' => '',
     'host' => $image_server,
     'path' => '',
     'folder' => ''
];
$lang = 24; // Dutch
