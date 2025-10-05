<?php
# Script: racks.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/login/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_ReadOnly);

  $height = 900;
  $width = 300;

############
# Image drawing portion
############
# build the graphic first
  $data = imagecreatetruecolor($width,$height);

# Create common variables
  $black = imagecolorallocate($data,   0,   0,   0);
  $green = imagecolorallocate($data,   0, 250, 154);
  $red   = imagecolorallocate($data, 192,  80,  77);
  $blue  = imagecolorallocate($data,  60, 116, 182);
  $white = imagecolorallocate($data, 255, 255, 255);
  $grey  = imagecolorallocate($data, 187, 187, 187);

# Default is a black background; change it to white
  imagefill($data, 0, 0, $white);


  imagerectangle($data, 50, 30, 250, 870, $grey);
  for ($i = 50; $i <= 870; $i+=20) {
    imagerectangle($data, 50, 30, 250, $i, $grey);
  }


#  imagerectangle($data, 50, 30, 250, 50, $grey);
#  imagerectangle($data, 50, 50, 250, 70, $grey);
#  imagerectangle($data, 50, 70, 250, 90, $grey);
#  imagerectangle($data, 50, 90, 250, 110, $grey);
#  imagerectangle($data, 50, 110, 250, 130, $grey);
#  imagerectangle($data, 50, 130, 250, 150, $grey);
#  imagerectangle($data, 50, 150, 250, 170, $grey);
#  imagerectangle($data, 50, 170, 250, 190, $grey);
#  imagerectangle($data, 50, 190, 250, 210, $grey);
#  imagerectangle($data, 50, 210, 250, 230, $grey);
#  imagerectangle($data, 50, 230, 250, 250, $grey);
#  imagerectangle($data, 50, 250, 250, 270, $grey);
#  imagerectangle($data, 50, 270, 250, 290, $grey);
#  imagerectangle($data, 50, 290, 250, 310, $grey);
#  imagerectangle($data, 50, 310, 250, 330, $grey);
#  imagerectangle($data, 50, 330, 250, 350, $grey);
#  imagerectangle($data, 50, 350, 250, 370, $grey);
#  imagerectangle($data, 50, 370, 250, 390, $grey);
#  imagerectangle($data, 50, 390, 250, 410, $grey);
#  imagerectangle($data, 50, 410, 250, 430, $grey);
#  imagerectangle($data, 50, 430, 250, 450, $grey);
#  imagerectangle($data, 50, 450, 250, 470, $grey);
#  imagerectangle($data, 50, 470, 250, 490, $grey);
#  imagerectangle($data, 50, 490, 250, 510, $grey);
#  imagerectangle($data, 50, 510, 250, 530, $grey);
#  imagerectangle($data, 50, 530, 250, 550, $grey);
#  imagerectangle($data, 50, 550, 250, 570, $grey);
#  imagerectangle($data, 50, 570, 250, 590, $grey);
#  imagerectangle($data, 50, 590, 250, 610, $grey);
#  imagerectangle($data, 50, 610, 250, 630, $grey);
#  imagerectangle($data, 50, 630, 250, 650, $grey);
#  imagerectangle($data, 50, 650, 250, 670, $grey);
#  imagerectangle($data, 50, 670, 250, 690, $grey);
#  imagerectangle($data, 50, 690, 250, 710, $grey);
#  imagerectangle($data, 50, 710, 250, 730, $grey);
#  imagerectangle($data, 50, 730, 250, 750, $grey);
#  imagerectangle($data, 50, 750, 250, 770, $grey);
#  imagerectangle($data, 50, 770, 250, 790, $grey);
#  imagerectangle($data, 50, 790, 250, 810, $grey);
#  imagerectangle($data, 50, 810, 250, 830, $grey);
#  imagerectangle($data, 50, 730, 250, 850, $grey);
#  imagerectangle($data, 50, 750, 250, 870, $grey);



$start = 35;
for ($x = 42; $x >= 1; $x--) {
  imagestring($data, 1, 35, $start, "$x", $red);
  $start = $start + 20;
}

#  imagestring($data, 1, 35, 35,  '42', $blue);
#  imagestring($data, 1, 35, 55,  '41', $blue);
#  imagestring($data, 1, 35, 75,  '40', $blue);
#  imagestring($data, 1, 35, 95,  '39', $blue);
#  imagestring($data, 1, 35, 115,  '38', $blue);
#  imagestring($data, 1, 35, 135,  '37', $blue);
#  imagestring($data, 1, 35, 155,  '36', $blue);
#  imagestring($data, 1, 35, 175,  '35', $blue);
#  imagestring($data, 1, 35, 195,  '34', $blue);
#  imagestring($data, 1, 35, 215,  '33', $blue);
#  imagestring($data, 1, 35, 235,  '32', $blue);
#  imagestring($data, 1, 35, 255,  '31', $blue);
#  imagestring($data, 1, 35, 275,  '30', $blue);
#  imagestring($data, 1, 35, 295,  '29', $blue);
#  imagestring($data, 1, 35, 315,  '28', $blue);
#  imagestring($data, 1, 35, 335,  '27', $blue);
#  imagestring($data, 1, 35, 355,  '26', $blue);
#  imagestring($data, 1, 35, 375,  '25', $blue);
#  imagestring($data, 1, 35, 395,  '24', $blue);
#  imagestring($data, 1, 35, 415,  '23', $blue);
#  imagestring($data, 1, 35, 435,  '22', $blue);
#  imagestring($data, 1, 35, 455,  '21', $blue);
#  imagestring($data, 1, 35, 475,  '20', $blue);
#  imagestring($data, 1, 35, 495,  '19', $blue);
#  imagestring($data, 1, 35, 515,  '18', $blue);
#  imagestring($data, 1, 35, 535,  '17', $blue);
#  imagestring($data, 1, 35, 555,  '16', $blue);
#  imagestring($data, 1, 35, 575,  '15', $blue);
#  imagestring($data, 1, 35, 595,  '14', $blue);
#  imagestring($data, 1, 35, 615,  '13', $blue);
#  imagestring($data, 1, 35, 635,  '12', $blue);
#  imagestring($data, 1, 35, 655,  '11', $blue);
#  imagestring($data, 1, 35, 675,  '10', $blue);
#  imagestring($data, 1, 35, 695,  ' 9', $blue);
#  imagestring($data, 1, 35, 715,  ' 8', $blue);
#  imagestring($data, 1, 35, 735,  ' 7', $blue);
#  imagestring($data, 1, 35, 755,  ' 6', $blue);
#  imagestring($data, 1, 35, 775,  ' 5', $blue);
#  imagestring($data, 1, 35, 795,  ' 4', $blue);
#  imagestring($data, 1, 35, 815,  ' 3', $blue);
#  imagestring($data, 1, 35, 835,  ' 2', $blue);
#  imagestring($data, 1, 35, 855,  ' 1', $blue);


  header('Content-type: image/png');
  imagepng($data);

  imagedestroy($data);

?>
