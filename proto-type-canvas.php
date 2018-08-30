<?php

$rustart = getrusage();

// echo phpinfo();exit;

function readImageBlob() {

    $base64 = file_get_contents('image.txt');
    $base64 = str_replace('data:image/svg+xml;base64,', '', $base64);
    $base64 = str_replace(' ', '+', $base64);

    $imageBlob = base64_decode($base64);

    unset($base64);

    $svg_xml = '<?xml version="1.0"?>';
    $svg_xml .= $imageBlob;

    unset($imageBlob);

    $imagick = new Imagick();
    $imagick->setResolution(110, 110);
    $imagick->readImageBlob($svg_xml);

    unset($svg_xml);

    // $imagick->enhanceImage();
    $imagick->setImageFormat('png');
    $dimagick = $imagick->getImageGeometry(); 

    $svgimage = imagecreatefromstring($imagick->getImage());

    unset($imagick);

    $dest = imagecreatefrompng('brick.png');
    
    list($width, $height) = getimagesize('brick.png');

    $newwidth = $dimagick['width'];
    $newheight = $dimagick['height'];

    unset($dimagick);

    $destination = imagecreatetruecolor($newwidth, $newheight);
    $color = imagecolorallocatealpha($destination, 255, 255, 255, 127); //fill transparent back
    
    imagefill($destination, 0, 0, $color);

    unset($color);

    imagesavealpha($destination, true);
    imagecopyresized($destination, $dest, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    unset($dest);
    unset($width);
    unset($height);
    unset($newwidth);
    unset($newheight);

    $svgimage = ImageAddWatermark($svgimage, $destination, 0, 0, 0);
    
    header("Content-Type: image/png");
    imagepng($svgimage);

    ob_start();

    imagepng($svgimage);

    $data = ob_get_clean();

    file_put_contents('tester_merged_image.png', $data);

    unset($data);

    imagedestroy($svgimage);
    imagedestroy($destination);
}

function ImageAddWatermark($im, $stamp, $onLeft, $onTop, $margin)
{
    if($onLeft){
        $orgX = $margin;
    } else {
        $orgX = imagesx($im)-$margin-imagesx($stamp);
    }

    if($onTop){
        $orgY = $margin;
    } else {
        $orgY = imagesy($im)-$margin-imagesy($stamp);
    }

    // creating a cut resource 
    $cut = imagecreatetruecolor(imagesx($stamp), imagesy($stamp)); 

    // copying relevant section from background to the cut resource 
    imagecopy($cut, $im, 0, 0, $orgX, $orgY, imagesx($stamp), imagesy($stamp)); 
    
    // copying relevant section from watermark to the cut resource 
    imagecopy($cut, $stamp, 0, 0, 0, 0, imagesx($stamp), imagesy($stamp)); 

    // insert cut resource to destination image 
    imagecopymerge($im, $cut, $orgX, $orgY, 0, 0, imagesx($stamp), imagesy($stamp), 100);

    unset($orgX);
    unset($orgY);
    unset($stamp);
    unset($cut);
    unset($onLeft);
    unset($onTop);
    unset($margin);
    
    return $im;
}

readImageBlob();

// Script end
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations\n";
echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls\n";

exit;