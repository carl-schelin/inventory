<?php
# Script: city.del.php
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
    $package = "city.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Admin)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from cities");

      $q_string  = "delete ";
      $q_string .= "from cities ";
      $q_string .= "where ct_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "alert('City deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
