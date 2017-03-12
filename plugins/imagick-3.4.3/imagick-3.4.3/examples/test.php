header('Content-type: image/jpeg');

$image = new Imagick('/tmp/test.png');

// If 0 is provided as a width or height parameter,
// aspect ratio is maintained
$image->thumbnailImage(100, 0);

echo $image;

