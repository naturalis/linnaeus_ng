<?php


$image_server = getEnv("IMAGE_SERVER_ADDRESS") ?? "145.136.243.50";


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

$urlWebImageSearch="https://www.nederlandsesoorten.nl/linnaeus_ng/app/views/search/nsr_search_pictures.php?page=%s";
// $urlWebImageSearch="https://www.dutchcaribbeanspecies.org/linnaeus_ng/app/views/search/nsr_search_pictures.php?page=%s";

// $fetchFromDateOverride = '2018-05-06 23:59:59';
