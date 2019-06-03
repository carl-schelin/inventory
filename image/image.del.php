<?php
# Script: image.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "image.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from images");

      $q_string  = "select img_file ";
      $q_string .= "from images ";
      $q_string .= "where img_id = " . $formVars['id'];
      $q_images = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_images = mysql_fetch_array($q_images);

      if (file_exists($Picturepath . "/" . $a_images['img_file'])) {
        unlink($Picturepath . "/" . $a_images['img_file']);
      }

      $q_string  = "delete ";
      $q_string .= "from images ";
      $q_string .= "where img_id = " . $formVars['id'];
      $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      print "alert('Image file and data deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
