<?php

	define('IMAGE_WIDTH',20);
	define('IMAGE_HEIGHT',34);
	
	$debug = false;

	$img = imagecreate(IMAGE_WIDTH,IMAGE_HEIGHT);
	
	$invertColour = isset($_REQUEST['i']) && $_REQUEST['i']=='y';

	if (isset($_REQUEST['c'])) {

		$c = ltrim($_REQUEST['c'],'#');

		if (strlen($c)==3) {
		
			$r = hexdec(substr($c,0,1).substr($c,0,1));
			$g = hexdec(substr($c,1,1).substr($c,1,1));
			$b = hexdec(substr($c,2,1).substr($c,2,1));

		} else
		if (strlen($c)==6) {

			$r = hexdec(substr($c,0,2));
			$g = hexdec(substr($c,2,2));
			$b = hexdec(substr($c,4,2));

		} else {

			$r = hexdec('ff');
			$g = hexdec('77');
			$b = hexdec('6b');

		}

	} else {

			$r = hexdec('ff');
			$g = hexdec('77');
			$b = hexdec('6b');

	}

	$frontColor =
		imagecolorallocate(
			$img,
			($invertColour ? 255 - $r : $r),
			($invertColour ? 255 - $g : $g),
			($invertColour ? 255 - $b : $b)
		);

	$bgColor = imagecolorallocate($img,1,2,3);

	if ($bgColor==$frontColor) $bgColor = imagecolorallocate($img,4,5,6);

	imagefilledrectangle($img, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT, $bgColor);
	
	$borderColor = imagecolorallocate($img,0,0,0);

	if ($borderColor==$frontColor) $borderColor = imagecolorallocate($img,128,128,128);

	imageantialias($img,true);
	imagesetthickness ($img,2);
	imagecolortransparent ($img,$bgColor);

	imagesetthickness ($img,4);
	imageline($img, 10, 10, 10, 25, $borderColor);
	imagesetthickness ($img,2);
	imageline($img, 10,10,10, 40, $borderColor);

	imagefilledrectangle($img, 0, 0, 20, 19, $borderColor);
	imagefilledrectangle($img, 2, 2, 17, 17, $frontColor);
	imagefilledrectangle($img, 7, 7, 12, 12, $borderColor);

	if ($debug) die();

	header('Content-Type: image/png');
	imagepng($img);
	imagedestroy($img);

?>