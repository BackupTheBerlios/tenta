<?php
/* rateme.php

	ChangeLog:
		2002-09-18 Jonas Björk, jonas@mbs.nu
			* Generisk rateme som klarar både stora och små bilder
			* Snyggade upp koden lite

		2002-03-29 Jonas Björk, jonas@mbs.nu
			* Skrev första versionen

	Syntax:
		rateme.php?result=10&max=20&g=50&vg=80&type=small

		result	resultatet
		max 	max poäng
		g		är gränsen för godkänt i procent
		vg		är gränsen för väl godkänt i procent
		type	anger du ingen får du en stor, anger du "small" får du en liten
*/
	header("Content-type: image/png");

	if(!$result) { $result = 0; }
	if(!$max) { $max = 40; }
	if(!$g) { $g = 50; } // ange i %
	if(!$vg) { $vg = 80; } // ange i %
	if($type=="small") {
		$scale = 1.98; $iCreX = 200; $iCreY = 15;
		$fill = 200; $fill2 = 15;
		$fill3 = 13; $fill4 = 198; $fill5 = 13;
		$iStr = 2; $iStr2 = 60; $iStr3 = 1;
	} else {
		$scale = 5.98; $iCreX = 600; $iCreY = 20;
		$fill = 600; $fill2 = 20;
		$fill3 = 18; $fill4 = 598; $fill5 = 18;
		$iStr = 3; $iStr2 = 250; $iStr3 = 3;
	}

	/* --- skapar bilden -- */
	$pnt = ($result / $max) * 100;
	$pnt = number_format($pnt, 0, '.', '');
	$pntimg = $pnt * $scale;
	$image = imagecreate($iCreX,$iCreY);
	$result = number_format($result, 0, '.', '');
	$colRed = imagecolorallocate($image, 255, 0 , 0);
	$colBlue = imagecolorallocate($image, 0, 0, 255);
	$colBlack = imagecolorallocate($image, 0, 0, 0);
	$colWhite = imagecolorallocate($image, 255,255,255);
	imageFilledRectangle($image, 0, 0, $fill, $fill2, $colBlack);
	if($pnt < $g) {
		$col = imagecolorallocate($image, 255,0,0);
	} elseif ($pnt < $vg) {
		$col = imagecolorallocate($image, 255,255,0);
	} else {
		$col = imagecolorallocate($image, 0,255,0);
	}
	imageFilledRectangle($image, 1, 1, $pntimg, $fill3, $col);
	imageFilledRectangle($image, (1+$pntimg), 1, $fill4, $fill5, $colWhite);
	imagestring($image, $iStr, $iStr2, $iStr3, "$result / $max ( $pnt % )", $colBlack);
	imagepng($image);
	imagedestroy($image);
?>