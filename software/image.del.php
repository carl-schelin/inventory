<?php
# Script: image.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Admin)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from inv_image");

      $q_string  = "delete ";
      $q_string .= "from inv_image ";
      $q_string .= "where img_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
