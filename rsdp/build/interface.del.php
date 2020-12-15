<?php
# Script: interface.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from rsdp_interface");

      $q_string  = "delete ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "alert('Interface deleted.');\n";

# Check all interfaces with this int_int_id and revert to 0 so they aren't lost
      $q_string  = "update ";
      $q_string .= "rsdp_interface ";
      $q_string .= "set if_if_id = 0 ";
      $q_string .= "where if_if_id = " . $formVars['id'];
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
