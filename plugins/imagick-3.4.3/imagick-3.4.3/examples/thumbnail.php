<?php
 
/*
	A simple example demonstrate thumbnail creation.
*/ 
 
/* Create the Imagick object */
echo "start";
$im = new Imagick();
echo $im; 
/* Read the image file */
$im->readImage( '/tmp/test.png' );
 
/* Thumbnail the image ( width 100, preserve dimensions ) */
$im->thumbnailImage( 100, null );
 
/* Write the thumbail to disk */
$im->writeImage( '/tmp/th_test.png' );
 
/* Free resources associated to the Imagick object */
$im->destroy();
 
?>
