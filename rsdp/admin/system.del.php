<?php
# Script: system.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "system.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from operatingsystems");

      $q_string  = "update operatingsystem ";
      $q_string .= "set os_delete = 1,os_user = " . $_SESSION['uid'] . " ";
      $q_string .= "where os_id = " . $formVars['id'];
      $update = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "alert('Operating System deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
