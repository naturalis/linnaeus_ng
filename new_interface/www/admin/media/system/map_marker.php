<?php

	define('IMAGE_WIDTH',20);
	define('IMAGE_HEIGHT',34);

	$img = imagecreate(IMAGE_WIDTH,IMAGE_HEIGHT);

	if (isset($_REQUEST['c'])) {

		$c = ltrim($_REQUEST['c'],'#');

		if (strlen($c)==3) {

			$frontColor =
				imagecolorallocate(
					$img,
					hexdec(substr($c,0,1).substr($c,0,1)),
					hexdec(substr($c,1,1).substr($c,1,1)),
					hexdec(substr($c,2,1).substr($c,2,1))
				);

		} else
		if (strlen($c)==6) {

			$frontColor =
				imagecolorallocate(
					$img,
					hexdec(substr($c,0,2)),
					hexdec(substr($c,2,2)),
					hexdec(substr($c,4,2))
				);

		} else {

			$frontColor = imagecolorallocate($img,0,255,255);

		}

	} else {

		$frontColor =imagecolorallocate($img,hexdec('ff'),hexdec('77'),hexdec('6b'));

	}

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

	header('Content-Type: image/png');
	imagepng($img);
	imagedestroy($img);

?>