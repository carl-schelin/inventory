<?php
# Script: system.undel.php
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
    $package = "system.undel.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Undeleting " . $formVars['id'] . " from operatingsystems");

      $q_string  = "update operatingsystem ";
      $q_string .= "set os_delete = 0,os_user = " . $_SESSION['uid'] . " ";
      $q_string .= "where os_id = " . $formVars['id'];
      $update = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "alert('Operating System undeleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
