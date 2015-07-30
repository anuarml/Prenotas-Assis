<?php
	function convertPngToBmp($name){
		// *** Include PHP Image Magician library
		require_once('php_image_magician.php');
		// *** Open PNG image
		$magicianObj = new imageLib($name.'.png');
		// *** Save image as a BMP
		$magicianObj -> saveImage($name.'.bmp');
	}
?>