<?php
# Script: servergraph.php
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

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 1;
  }

# This script extracts the hw_built variable from the hardware database and increments
# the array for that entry, then adds a total to the year.

# The graph has two lines:
# 1. Sum for the Year (Sum Year). Total number of new servers for that year.
# 2. Cummulative chart. Monthly total of new servers for that month.

# Flow:
# Retrieve data from the inventory database.
# Break the hw_built value into three parts: Year, Month, and Day. Discard Day
# Increment year variable for that year by one
# Increment month variable for that year/month
# Create two charts:
# 1. Flat chart with totals and percentage increase
# 2. Graph with two lines; one for sumyear, one for cumulative

# hw_built date not null default '1971-01-01',

  $total = 5;

  if ($formVars['group'] == -1) {
    $admin = "";
  } else {
    $admin = " and inv_manager = " . $formVars['group'];
  }

  $gone = array();
  $year = array();
  $month = array();
  $yeartotal = 0;
  $total = 0;

  $q_string  = "select hw_built,hw_retired,hw_reused,inv_status ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "where hw_primary = 1 " . $admin . " ";
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    $dbyear = explode("-", $a_hardware['hw_built']);
    $yrmn = $dbyear[0] . "-" . $dbyear[1];
    $rtyear = explode("-", $a_hardware['hw_retired']);
    $ruyear = explode("-", $a_hardware['hw_reused']);
    if ($a_hardware['inv_status'] == 0) {
      if (isset($year[$dbyear[0]])) {
        $year[$dbyear[0]]++;
      } else {
        $year[$dbyear[0]] = 1;
      }
      if (isset($month[$yrmn])) {
        $month[$yrmn]++;
      } else {
        $month[$yrmn] = 1;
      }
      $total++;
    } else {
      if ($rtyear[0] > $ruyear[0]) {
        $gone[$rtyear[0]]++;
      } else {
        $gone[$ruyear[0]]++;
      }
    }
    if (($year[$dbyear[0]] + $gone[$dbyear[0]]) > $yeartotal) {
      $yeartotal = ($year[$dbyear[0]] + $gone[$dbyear[0]]);
    }

  }

# green is servers retired
# red is servers built

  $startyear = 2000;
  $endyear = date('Y');
  $endmon = date('n');

# calculate the numbers for the yearly total

# graph box parameters from the edge of the graph
  $btop = 3;
  $bleft = 7;
  $gtop = 18;
  $gleft = 70;
  $sline = 4; # length of each sum line

# graph box height and width
  $gheight = (((int)($total / 50) + 1) * 50);
  $gwidth = ((($endyear - $startyear) * 12) + $endmon) * $sline;

# get the yearly total chart set up
  $yheight = (((int)($yeartotal / 10) + 2) * 10);
  $ypoint = (int)($gheight / $yheight);  # then multiply this by # servers to get point. At 10's, draw a tick.
  $ytick = $ypoint * 10;

# graph bottom right parameters (box + top and left indents)
  $gbtm = $gheight + $gtop;
  $gright = $gwidth + $gleft;

# graph outside box height and width (left/top + grap left/right + text areas + right/bottom indents)
  $bbtm = $gbtm + 30;
  $bright = $gright + 60;

# graphic height and width
  $height = $bbtm + 3;
  $width = $bright + 7;


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

# build the two boxes for the border and the chart
  imagerectangle($data, $bleft, $btop, $bright, $bbtm, $black);

# draw lines for every 50 servers
# line from gleft - 3 to gright
# height is 300 - $i
  for ($i = $gheight; $i >= 0; $i -= 50) {
    imageline($data, $gleft - 3,  $gheight - $i + $gtop, $gleft, $gheight - $i + $gtop, $blue);
    imageline($data, $gleft,  $gheight - $i + $gtop, $gright, $gheight - $i + $gtop, $grey);
    if ($i == 50) {
      imagestring($data, 2, $gleft - 22, $gheight - $i + $gtop - 7, $i, $blue);
    } else {
      if ($i == 0) {
        imagestring($data, 2, $gleft - 16, $gheight - $i + $gtop - 7, $i, $blue);
      } else {
        imagestring($data, 2, $gleft - 28, $gheight - $i + $gtop - 7, $i, $blue);
      }
    }
  }

  imageline($data, $gleft, $gtop, $gright, $gtop, $black);
  imageline($data, $gleft, $gbtm, $gright, $gbtm, $black);
  imageline($data, $gleft, $gtop, $gleft, $gbtm, $blue);
  imageline($data, $gright, $gtop, $gright, $gbtm, $red);

# draw the tick marks
# add the text for the yearly totals;
  for ($i = $yheight * $ypoint, $j = $yheight; $i >= 0; $i -= $ytick, $j -= 10) {
    imageline($data, $gright, $i + $gtop + 20, $gright + 3, $i + $gtop + 20, $red);
    imagestring($data, 2, $gright + 10, $gheight - $i + $gtop - 7, $j, $red);
  }

# text on right side of chart
  $textheight = (int)(($gheight + $gtop) / 2);
  imagestringup($data, 2, $gright + 30, $textheight + 44, "Yearly Total", $red);
  imagestringup($data, 2, $left + 20, $textheight + 37, "Cumulative", $blue);

#        btop
# bleft +------------------------------------------------+
#       |       gtop                                     |
#       | gleft +---------------------------------+      |
#       |       |          gheight                |      |
#       |       |             ^                   |      |
#       |       |             |                   |      |
#       |       |             |                   |      |
#       |       |<------------+------gwidth------>|      |
#       |       |             v                   |      |
#       |       +---------------------------------+gright|
#       |                                      gbtm      |
#       +------------------------------------------------+ bright
#                                                      bbtm


# zip through the $month variables and sum it to create the chart

  $sum = 0;
  $goldleft = $gleft;
  $goldbtm = $gbtm;
  for ($i = $startyear; $i <= $endyear; $i++) {

# draw the bottom tick marks and years
    imageline($data, $goldleft, $gbtm, $goldleft, $gbtm + 3, $black);
    imagestring($data, 2, $goldleft + 12, $gbtm + 8, $i, $black);

# draw the bar charts for each years total
    if (($goldleft + 48) > $gright) {
      imagerectangle($data, $gright - 5, $gbtm, $gright, $gbtm - ($ypoint * ($year[$i] + $gone[$i])), $black);
      if ($gone[$i] > 0) {
        imagerectangle($data, $gright - 5, $gbtm - ($ypoint * $year[$i]), $gright, $gbtm - ($ypoint * ($year[$i] + $gone[$i])), $black);
        imagefilltoborder($data, $gright - 4, $gbtm - ($ypoint * $year[$i]) - 1, $black, $green);
      }
      if ($year[$i] > 0) {
        imagefilltoborder($data, $gright - 4, $gbtm - 1, $black, $red);
      }
    } else {
      imagerectangle($data, $goldleft - 5 + 48, $gbtm, $goldleft + 5 + 48, $gbtm - ($ypoint * ($year[$i] + $gone[$i])), $black);
      if ($gone[$i] > 0) {
        imagerectangle($data, $goldleft - 5 + 48, $gbtm - ($ypoint * $year[$i]), $goldleft + 5 + 48, $gbtm - ($ypoint * ($year[$i] + $gone[$i])), $black);
        imagefilltoborder($data, $goldleft - 4 + 48, $gbtm - ($ypoint * $year[$i]) - 1, $black, $green);
      }
      if ($year[$i] > 0) {
        imagefilltoborder($data, $goldleft - 4 + 48, $gbtm - 1, $black, $red);
      }
    }

# draw the blue cumulitive line
    for ($j = 1; $j < 13; $j++) {
      $dateval = sprintf("%4s-%02s", $i, $j);

      if (isset($month[$dateval])) {
        $sum += $month[$dateval];
      }

      $gnewleft = $goldleft + $sline;
      $gnewbtm = $gbtm - $sum;

# basically, don't draw if it's past the current month and year
# reverse; draw only if it's equal or less than the current

      if ($i < $endyear) {
        imageline($data, $goldleft, $goldbtm, $gnewleft, $gnewbtm, $blue);
      }
      if ($i == $endyear && $j <= $endmon) {
        imageline($data, $goldleft, $goldbtm, $gnewleft, $gnewbtm, $blue);
      }

      $goldleft = $gnewleft;
      $goldbtm = $gnewbtm;

    }

  }

  header('Content-type: image/png');
  imagepng($data);

  imagedestroy($data);

?>
