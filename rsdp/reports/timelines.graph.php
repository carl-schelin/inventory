<?php
# Script: timelines.graph.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/login/check.php');
  include($Sitepath . '/function.php');
  check_login('3');

  if (isset($_GET['start'])) {
    $formVars['start'] = clean($_GET['start'], 15);
  } else {
    $formVars['start'] = '2014-01-01';
  }
  if (isset($_GET['end'])) {
    $formVars['end'] = clean($_GET['end'], 15);
  } else {
    $formVars['end'] = '2014-12-31';
  }
  $where = "where rsdp_created >= '" . $formVars['start'] . "' and rsdp_created <= '" . $formVars['end'] . "' ";

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 0;
  }

  if ($formVars['group'] > 0) {
    $where .= "and grp_id = " . $formVars['group'] . " ";
  }

  if (isset($_GET['rsdp'])) {
    $formVars['rsdp'] = clean($_GET['rsdp'], 10);
  } else {
    $formVars['rsdp'] = 0;
  }

  if ($formVars['rsdp'] > 0) {
    $where = "where rsdp_id = " . $formVars['rsdp'] . " ";
  }

# set the divisor for how to show the data.
# options are ;days and hours. seconds are too high
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 20);
  } else {
    $formVars['type'] = 'days';
  }

  settype($task[0], "float");
  settype($task[1], "float");
  settype($task[2], "float");
  settype($task[3], "float");
  settype($task[4], "float");
  settype($task[5], "float");
  settype($task[6], "float");
  settype($task[7], "float");
  settype($task[8], "float");
  settype($task[9], "float");
  settype($task[10], "float");
  settype($task[11], "float");
  settype($task[12], "float");
  settype($task[13], "float");
  settype($task[14], "float");
  settype($task[15], "float");
  settype($task[16], "float");
  settype($task[17], "float");

  $label[1] = 'Beg';
  $label[2] = 'Pro';
  $label[3] = 'San';
  $label[4] = 'Net';
  $label[5] = 'V/D';
  $label[6] = 'DC';
  $label[7] = 'DC';
  $label[8] = 'SR';
  $label[9] = 'DC';
  $label[10] = 'Sys';
  $label[11] = 'San';
  $label[12] = 'Sys';
  $label[13] = 'Bck';
  $label[14] = 'Mon';
  $label[15] = 'App';
  $label[16] = 'Mon';
  $label[17] = 'App';
  $label[18] = 'Scn';

# get the height of the graph
  $total = 0;
  $q_string  = "select rsdp_id,rsdp_created ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join locations on locations.loc_id = rsdp_server.rsdp_location ";
  $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
  $q_string .= "left join groups on groups.grp_id = users.usr_group ";
  $q_string .= $where;
  $q_string .= "order by rsdp_id ";
  $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_rsdp_server = mysql_fetch_array($q_rsdp_server)) {

    for ($i = 0; $i < 19; $i++) {
      $task[$i] = 0;
    }

    for ($i = 0; $i < 19; $i++) {
      $diff[$i] = 0;
    }

    $baseline = 0;
    for ($i = 1; $i < 19; $i++) {

      $q_string  = "select st_completed,st_timestamp ";
      $q_string .= "from rsdp_status ";
      $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = " . $i . " ";
      $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_status) > 0) {
        $a_rsdp_status = mysql_fetch_array($q_rsdp_status);

        $task[$i] = strtotime($a_rsdp_status['st_timestamp']);

        if ($i == 1) {
          $baseline = $task[$i];
        }
      } else {
        $task[$i] = $task[$i-1];
      }

      $task[$i] = $task[$i] - $baseline;
      if ($task[$i] < 0) {
        $task[$i] = $task[$i-1];
      }

      if ($task[$i] > 0) {
        $diff[$i] = $task[$i] - $task[$i - 1];
      }
      if ($diff[$i] < 0) {
        $diff[$i] = 0;
      }

# divide the seconds by 60 to get minutes, then divide the minutes to get hours, then divide the hours by 24 to get the number of days.
      if ($diff[$i] > 0) {
        if ($formVars['type'] == 'hours') {
          $diff[$i] = $diff[$i] / 60 / 60;
        } else {
          $diff[$i] = $diff[$i] / 60 / 60 / 24;
        }
      }

      if ($diff[$i] > $total) {
        $total = $diff[$i];
      }
    }
  }


# calculate the numbers for the yearly total

# graph box parameters from the edge of the graph
  $btop = 3;
  $bleft = 7;
  $gtop = 18;
  $gleft = 70;
  $sline = 4; # length of each sum line

# graph box height and width
  $gheight = (((int)($total / 50) + 1) * 50);
  $gwidth = 18 * 11.5 * $sline;

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
    imageline($data, $gleft - 3, $gheight - $i + $gtop, $gleft,  $gheight - $i + $gtop, $blue);
    imageline($data, $gleft,     $gheight - $i + $gtop, $gright, $gheight - $i + $gtop, $grey);
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

  $textheight = (int)(($gheight + $gtop) / 2);
  if ($formVars['type'] == 'hours') {
    imagestringup($data, 2, $gleft - 50, $textheight + 37, " By Hours", $blue);
  } else {
    imagestringup($data, 2, $gleft - 50, $textheight + 37, "  By Days", $blue);
  }

  imageline($data,  $gleft, $gtop, $gright, $gtop, $black);
  imageline($data,  $gleft, $gbtm, $gright, $gbtm, $black);
  imageline($data,  $gleft, $gtop, $gleft,  $gbtm, $blue);
  imageline($data, $gright, $gtop, $gright, $gbtm, $blue);

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

  $q_string  = "select rsdp_id,rsdp_created ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join locations on locations.loc_id = rsdp_server.rsdp_location ";
  $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
  $q_string .= "left join groups on groups.grp_id = users.usr_group ";
  $q_string .= $where;
  $q_string .= "order by rsdp_id ";
  $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_rsdp_server = mysql_fetch_array($q_rsdp_server)) {

    for ($i = 0; $i < 19; $i++) {
      $task[$i] = 0;
    }

    for ($i = 0; $i < 19; $i++) {
      $diff[$i] = 0;
    }

    $baseline = 0;
    for ($i = 1; $i < 19; $i++) {

      $q_string  = "select st_completed,st_timestamp ";
      $q_string .= "from rsdp_status ";
      $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = " . $i . " ";
      $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_status) > 0) {
        $a_rsdp_status = mysql_fetch_array($q_rsdp_status);

        $task[$i] = strtotime($a_rsdp_status['st_timestamp']);

        if ($i == 1) {
          $baseline = $task[$i];
        }
      } else {
        $task[$i] = $task[$i-1];
      }

      $task[$i] = $task[$i] - $baseline;
      if ($task[$i] < 0) {
        $task[$i] = $task[$i-1];
      }

      if ($task[$i] > 0) {
        $diff[$i] = $task[$i] - $task[$i - 1];
      }
      if ($diff[$i] < 0) {
        $diff[$i] = 0;
      }

# divide the seconds by 60 to get minutes, then divide the minutes to get hours, then divide the hours by 24 to get the number of days.
      if ($diff[$i] > 0) {
        if ($formVars['type'] == 'hours') {
          $diff[$i] = $diff[$i] / 60 / 60;
        } else {
          $diff[$i] = $diff[$i] / 60 / 60 / 24;
        }
      }
    }


# actual drawing of graph:
    $sum = 0;
    $goldleft = $gleft;
    $goldbtm = $gbtm;

    for ($i = 1; $i < 19; $i++) {

# draw the bottom tick marks and years
      imageline($data, $goldleft, $gbtm, $goldleft, $gbtm + 3, $black);
      imagestring($data, 2, $goldleft - 8, $gbtm + 8, $label[$i], $black);

      if ($i > 1) {
        imageline($data, $goldleft, $gbtm - $diff[$i], $goldold, $gbtm - $diff[$i - 1], $red);
      }

      $goldold = $goldleft;
      $goldleft = $goldleft + $sline * 12;

    }
  }

  header('Content-type: image/png');
  imagepng($data);

  imagedestroy($data);

?>
