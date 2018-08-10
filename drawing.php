<?php
header ("Content-type: image/gif");
$handle = ImageCreate (130, 50) or die ("Cannot Create image");
$bg_color = ImageColorAllocate ($handle, 255, 2, 0);
$txt_color = ImageColorAllocate ($handle, 10123123, 0, 0);
ImageString ($handle, 5, 5, 18, "PHP.About.com", $txt_color);
ImageGif ($handle);
?> 