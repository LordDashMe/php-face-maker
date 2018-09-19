<?php

function getCheckMarkCustomSize($jpg_image, $x, $y) 
{
    $imgresize = imagecreatefrompng('toggle_checked.png');
    $imgresize = resizePng($imgresize, 50, 50);

    $jpg_image = imageAddWatermark($jpg_image, $imgresize, $x, $y);

    return $jpg_image;
}

function imageAddWatermark($im, $stamp, $x, $y)
{
    // creating a cut resource 
    $cut = imagecreatetruecolor(imagesx($stamp), imagesy($stamp)); 

    // copying relevant section from background to the cut resource 
    imagecopy($cut, $im, 0, 0, $x, $y, imagesx($stamp), imagesy($stamp)); 
    
    // copying relevant section from watermark to the cut resource 
    imagecopy($cut, $stamp, 0, 0, 0, 0, imagesx($stamp), imagesy($stamp)); 

    // insert cut resource to destination image 
    imagecopymerge($im, $cut, $x, $y, 0, 0, imagesx($stamp), imagesy($stamp), 100);

    unset($stamp);
    unset($cut);
    
    return $im;
}

function resizePng($im, $dst_width, $dst_height) {
    
    $width = imagesx($im);
    $height = imagesy($im);

    $newImg = imagecreatetruecolor($dst_width, $dst_height);

    imagealphablending($newImg, false);
    imagesavealpha($newImg, true);
    $transparent = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
    imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
    imagecopyresampled($newImg, $im, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);

    return $newImg;
}

// quickCheckTransparentCode();

function quickCheckTransparentCode()
{
    //define the width and height of our images
    define("WIDTH", 200);
    define("HEIGHT", 200);

    $dest_image = imagecreatetruecolor(WIDTH, HEIGHT);

    //make sure the transparency information is saved
    imagesavealpha($dest_image, true);

    //create a fully transparent background (127 means fully transparent)
    $trans_background = imagecolorallocatealpha($dest_image, 0, 0, 0, 127);

    //fill the image with a transparent background
    imagefill($dest_image, 0, 0, $trans_background);

    $imgresize = imagecreatefrompng('toggle_checked.png');
    $imgresize = resizePng($imgresize, 50, 50);

    $dest_image = ImageAddWatermark($dest_image, $imgresize, 5, 5, 0);

    //send the appropriate headers and output the image in the browser
    header('Content-Type: image/png');
    imagepng($dest_image);

    exit;
}

// createText();

function createText()
{
    //Set the Content Type
    header('Content-type: image/jpeg');

    // Create Image From Existing File
    $jpg_image = imagecreatefromjpeg('blackmamba.jpg');
    list($width, $height) = getimagesize('blackmamba.jpg');

    // Allocate A Color For The Text
    $white = imagecolorallocate($jpg_image, 0, 0, 0);

    $jpg_image = getCheckMarkCustomSize($jpg_image, 25, 10);
    $jpg_image = getCheckMarkCustomSize($jpg_image, 25, 60);
    $jpg_image = getCheckMarkCustomSize($jpg_image, 25, 110);
    // $jpg_image = getCheckMarkCustomSize($jpg_image, 1, 1, 0);

    // Set Path to Font File
    $font_path = __DIR__ . '/century-gothic/Century Gothic.ttf';
    $gothicbi_font_path = __DIR__ . '/century-gothic/GOTHICBI.TTF';

    // Print Text On Image
    imagettftext($jpg_image, 25, 0, 75, 50, $white, $font_path, "This is shoes!");
    imagettftext($jpg_image, 25, 0, 75, 100, $white, $font_path, "This is Bag!");
    imagettftext($jpg_image, 25, 0, 75, 150, $white, $gothicbi_font_path, wordwrap("This is Loooong Message!", 15, "\n"));

    // Send Image to Browser
    imagejpeg($jpg_image);

    // Clear Memory
    imagedestroy($jpg_image);
}

/*
    NOTE:

    1. Autocompute of fontsize base on the volume of the array.
    2. The mark will follow the computed y of the text.
    3. The text must be auto wrap this must be base on the fontsize also.

    The class needed to be flexible base on the inputed values, all the generation process will be base on that.
    Also the limitation will be base on the given values + the base image (or background image).
*/
class CustomizedImageDoodle
{
    protected function iconsWatermark($icon_path, $jpg_image, $x, $y, $size = array())
    {
        $imgresize = imagecreatefrompng($icon_path);
        
        $imgresize = $this->resizeAnyPngImage($imgresize, $size['width'], $size['height']);

        $jpg_image = $this->waterMark($jpg_image, $imgresize, $x, $y);

        imagedestroy($imgresize);

        return $jpg_image;
    }

    protected function waterMark($jpg_image, $imgresize, $x, $y)
    {
        // creating a cut resource 
        $cut = imagecreatetruecolor(imagesx($imgresize), imagesy($imgresize)); 

        // copying relevant section from background to the cut resource 
        imagecopy($cut, $jpg_image, 0, 0, $x, $y, imagesx($imgresize), imagesy($imgresize)); 
        
        // copying relevant section from watermark to the cut resource 
        imagecopy($cut, $imgresize, 0, 0, 0, 0, imagesx($imgresize), imagesy($imgresize)); 

        // insert cut resource to destination image 
        imagecopymerge($jpg_image, $cut, $x, $y, 0, 0, imagesx($imgresize), imagesy($imgresize), 100);

        unset($imgresize);
        unset($cut);
        
        return $jpg_image;
    }

    protected function resizeAnyPngImage($jpg_image, $dst_width, $dst_height)
    {
        $width = imagesx($jpg_image);
        $height = imagesy($jpg_image);

        $newImg = imagecreatetruecolor($dst_width, $dst_height);

        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        
        $transparent = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
        
        imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
        imagecopyresampled($newImg, $jpg_image, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);

        return $newImg;
    }

    public function render()
    {
        //Set the Content Type
        header('Content-type: image/jpeg');

        // Create Image From Existing File
        $jpg_image = imagecreatefromjpeg('blackmamba.jpg');
        list($width, $height) = getimagesize('blackmamba.jpg');

        // Allocate A Color For The Text
        $white = imagecolorallocate($jpg_image, 0, 0, 0);

        $jpg_image = $this->iconsWatermark('toggle_checked.png', $jpg_image, 25, 10, array('width' => 50, 'height' => 50));
        $jpg_image = $this->iconsWatermark('toggle_checked.png', $jpg_image, 25, 60, array('width' => 50, 'height' => 50));
        $jpg_image = $this->iconsWatermark('toggle_checked.png', $jpg_image, 25, 110, array('width' => 50, 'height' => 50));
        // $jpg_image = getCheckMarkCustomSize($jpg_image, 1, 1, 0);

        // Set Path to Font File
        $font_path = __DIR__ . '/century-gothic/Century Gothic.ttf';
        $gothicbi_font_path = __DIR__ . '/century-gothic/GOTHICBI.TTF';

        // Print Text On Image
        imagettftext($jpg_image, 25, 0, 75, 50, $white, $font_path, "This is shoes!");
        imagettftext($jpg_image, 25, 0, 75, 100, $white, $font_path, "This is Bag!");
        imagettftext($jpg_image, 25, 0, 75, 150, $white, $gothicbi_font_path, wordwrap("This is Loooong Message!", 15, "\n"));

        // Send Image to Browser
        imagejpeg($jpg_image);

        // Clear Memory
        imagedestroy($jpg_image);  
    }
}

(new CustomizedImageDoodle())->render();