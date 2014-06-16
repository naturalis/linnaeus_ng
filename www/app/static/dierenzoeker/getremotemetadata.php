<?php
	if (!isset($_GET['image_id']))	return;
	$raw=@file_get_contents('http://images.ncbnaturalis.nl/getmetadata.asp?imageId='.$_GET['image_id']);
	if (!$raw) return;
	$xml=@simplexml_load_string($raw);
	if (!$xml) return;
	//var_dump($xml);
	echo json_encode(array('description'=>(string)$xml->record->description,'maker'=>(string)$xml->record->maker,'copyright'=>(string)$xml->record->copyright));
