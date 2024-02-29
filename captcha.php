<?php 
/*
*
* this code is based on captcha code by Simon Jarvis 
* http://www.white-hat-web-design.co.uk/articles/php-captcha.php
*
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation
*
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*/

session_start();
//Settings: You can customize the captcha here
$image_width = 195;
$image_height = 48;
$characters_on_image = 6;
$font = './assets/fonts/captchafont.ttf';

//The characters that can be used in the CAPTCHA code.
//Avoid confusing characters (l 1 and i for example)
$possible_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

$random_dots = 0;
$random_lines = 0;
$captcha_text_color="0x000000";
$captcha_noice_color = "0x959595";

$char = '';
$code = '';
$code_nospace = '';

/* Generating the random code */
for($i = 0; $i < $characters_on_image; $i++) { 
	$char = substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
	$code .= $char.' ';	//Code with Spaces between characters for better readability
	$code_nospace .= $char; //Code without spaces (Actual Code)
}

$font_size = $image_height * 0.65;
$image = @imagecreate($image_width, $image_height);


/* setting the background, text and noise colours here */
$background_color = imagecolorallocate($image, 149, 149, 149);

$arr_text_color = hexrgb($captcha_text_color);
$text_color = imagecolorallocate($image, $arr_text_color['red'], 
		$arr_text_color['green'], $arr_text_color['blue']);

$arr_noice_color = hexrgb($captcha_noice_color);
$image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], 
		$arr_noice_color['green'], $arr_noice_color['blue']);


/* generating the dots randomly in background */
for( $i=0; $i<$random_dots; $i++ ) {
imagefilledellipse($image, mt_rand(0,$image_width),
 mt_rand(0,$image_height), 2, 3, $image_noise_color);
}


/* generating lines randomly in background of image */
for( $i=0; $i<$random_lines; $i++ ) {
imageline($image, mt_rand(0,$image_width), mt_rand(0,$image_height),
 mt_rand(0,$image_width), mt_rand(0,$image_height), $image_noise_color);
}


/* create a text box and add the code in it */
$textbox = imagettfbbox($font_size, 0, $font, $code); 
$x = ($image_width - $textbox[4])/2;
$y = ($image_height - $textbox[5])/2;
imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code);


/* Show captcha image in the page html page */
header('Content-Type: image/jpeg');// defining the image type to be shown in browser widow
imagejpeg($image);//showing the image
imagedestroy($image);//destroying the image instance
$_SESSION['captcha_code'] = $code_nospace;

function hexrgb ($hexstr)
{
  $int = hexdec($hexstr);

  return array("red" => 0xFF & ($int >> 0x10),
               "green" => 0xFF & ($int >> 0x8),
               "blue" => 0xFF & $int);
}
?>