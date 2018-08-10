<?php

include "FaceDetector.php";

use svay\FaceDetector;
 
/* We now extend the above class so we can add our own methods */
class FaceModify extends FaceDetector {
 
  public function Rotate() {
    $canvas = imagecreatetruecolor($this->face['w'], $this->face['w']);
    imagecopy($canvas, $this->canvas, 0, 0, $this->face['x'], 
              $this->face['x'], $this->face['w'], $this->face['w']);
    $canvas = imagerotate($canvas, 180, 0);
    $this->_outImage($canvas);
  }
 
  public function toGrayScale() {
    $canvas = imagecreatetruecolor($this->face['w'], $this->face['w']);
    imagecopy($canvas, $this->canvas, 0, 0, $this->face['x'], 
              $this->face['x'], $this->face['w'], $this->face['w']);
    imagefilter ($canvas, IMG_FILTER_GRAYSCALE);
    $this->_outImage($canvas);
  }
 
  public function resizeFace($width, $height) {
    
    $canvas = imagecreatetruecolor($width, $height);

    imagecopyresized($canvas, $this->canvas, 
        0, 0, 
        $this->face['x'] - 20, 
        $this->face['y'] - 30, 
        $width, 
        $height, 
        $this->face['w'] + 50, $this->face['w'] + 50);

    imagefilter($canvas, IMG_FILTER_GRAYSCALE);
    // imagefilter($canvas, IMG_FILTER_GAUSSIAN_BLUR);
    // imagefilter($canvas, IMG_FILTER_CONTRAST, 1);
    imagefilter($canvas, IMG_FILTER_MEAN_REMOVAL);
    
    // imagefilter($canvas, IMG_FILTER_EMBOSS);
    // imagefilter($canvas, IMG_FILTER_NEGATE);
    // imagefilter($canvas, IMG_FILTER_EDGEDETECT);

    $width_x = imagesx($canvas);
    $height_y = imagesy($canvas);

    // $canvas = draw_self(array($width_x, $height_y), $canvas);

    $face_draw = array();

    for($x = 0; $x < $width_x; $x++) {
        for($y = 0; $y < $height_y; $y++) {
            // pixel color at (x, y)
            $rgb = imagecolorat($canvas, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            if ($this->allowedRGB($r, $g, $b)) {
                array_push($face_draw, array('x' => $x, 'y' => $y));
            }
        }
    }

    $canvas = imagecreatetruecolor($width, $width);
    imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));

    //Allocate color
    $red = imagecolorallocate($canvas, 0, 0, 0);

    foreach ($face_draw as $rgb_pixels) {
         //Draw line each with its own color
        imageline($canvas, $rgb_pixels['x'], $rgb_pixels['y'], $rgb_pixels['x'], $rgb_pixels['y'], $red);
    }

    $this->_outImage($canvas);
  }

  private function allowedRGB($red, $green, $blue)
  {
        $skin_tones = array(
            // array(255,223,196),
            // array(240,213,190),
            // array(238,206,179),
            // array(225,184,153),
            // array(229,194,152),
            // array(255,220,178),
            // array(229,184,143),
            // array(229,160,115),
            // array(231,158,109),
            // array(219,144,101),
            // array(206,150,124),
            // array(198,120,86),
            // array(186,108,73),
            // array(165,114,87),
            // array(240,200,201),
            // array(221,168,160),
            // array(185,124,109),
            // array(168,117,108),
            // array(173,100,82),
            // array(92,56,54),
            // array(203,132,66),
            // array(189,114,60),
            // array(112,65,57),
            // array(163,134,106),
            // array(135,4,0),
            // array(113,1,1),
            // array(67,0,0),
            // array(91,0,1),
            // array(48,46,46),
            // array(0,0,0),
            // array(42,42,42),
            // array(127, 127, 127),
            // array(128,128,128),
            // array(105,105,105)
            // array(80,68,68),
        );

        // foreach ($skin_tones as $rgb) {
        //     if (in_array($red, $rgb) && in_array($green, $rgb) && in_array($blue, $rgb)) {
        //         return false;
        //     }
        // }
        
        if ($red < 100 && $green < 100 && $blue < 100) {
            return true;
        }

        return false;
  }
 
  private function _outImage($canvas) {
    header('Content-type: image/jpeg');
    imagejpeg($canvas);
    imagedestroy($canvas);
  }
}
 
 
/* Using the extended class */
$face_detect = new FaceModify('detection.dat');
$face_detect->faceDetect('test.jpg');
$face_detect->resizeFace(600, 600);
// $face_detect->toJpeg();


function draw_self($im_data, $canvas)
{
    // a butterfly image picked on flickr
// $source_image = __DIR__ . "/black-african-man.jpg";

// creating the image
// $starting_img = imagecreatefromjpeg($source_image);

// getting image information (I need only width and height)
// $im_data = getimagesize($source_image);

// this will be the final image, same width and height of the original
$final = imagecreatetruecolor($im_data[0],$im_data[1]);

// imagefilter($starting_img,IMG_FILTER_GRAYSCALE);

// looping through ALL pixels!!
for($x=1;$x<$im_data[0] - 1;$x++){
    for($y=1;$y<$im_data[1] - 1;$y++){
        // getting gray value of all surrounding pixels
        $pixel_up = get_luminance(imagecolorat($canvas,$x,$y-1));
        $pixel_down = get_luminance(imagecolorat($canvas,$x,$y+1)); 
        $pixel_left = get_luminance(imagecolorat($canvas,$x-1,$y));
        $pixel_right = get_luminance(imagecolorat($canvas,$x+1,$y));
        $pixel_up_left = get_luminance(imagecolorat($canvas,$x-1,$y-1));
        $pixel_up_right = get_luminance(imagecolorat($canvas,$x+1,$y-1));
        $pixel_down_left = get_luminance(imagecolorat($canvas,$x-1,$y+1));
        $pixel_down_right = get_luminance(imagecolorat($canvas,$x+1,$y+1));
        
        // appliying convolution mask
        $conv_x = ($pixel_up_right+($pixel_right*2)+$pixel_down_right)-($pixel_up_left+($pixel_left*2)+$pixel_down_left);
        $conv_y = ($pixel_up_left+($pixel_up*2)+$pixel_up_right)-($pixel_down_left+($pixel_down*2)+$pixel_down_right);
        
        // calculating the distance
        $gray = abs($conv_x)+abs($conv_y);
        
        // inverting the distance not to get the negative image                
        $gray = 255-$gray;
        
        // adjusting distance if it's greater than 255 or less than zero (out of color range)
        if($gray > 255){
            $gray = 255;
        }
        if($gray < 0){
            $gray = 0;
        }
        
        // creation of the new gray
        $new_gray  = imagecolorallocate($final,$gray,$gray,$gray);
        
        // adding the gray pixel to the new image        
        imagesetpixel($final,$x,$y,$new_gray);          
    }
}

return $final;
}

// function to get the luminance value
function get_luminance($pixel){
    $pixel = sprintf('%06x',$pixel);
    $red = hexdec(substr($pixel,0,2))*0.30;
    $green = hexdec(substr($pixel,2,2))*0.59;
    $blue = hexdec(substr($pixel,4))*0.11;
    return $red+$green+$blue;
}