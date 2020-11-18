<?php
# Script: speed.fill.php
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
    $package = "speed.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from int_speed");

      $q_string  = "select spd_text ";
      $q_string .= "from int_speed ";
      $q_string .= "where spd_id = " . $formVars['id'];
      $q_int_speed = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_int_speed = mysqli_fetch_array($q_int_speed);
      mysqli_free_result($q_int_speed);

      print "document.speed.spd_text.value = '" . mysqli_real_escape_string($a_int_speed['spd_text']) . "';\n";

      print "document.speed.id.value = " . $formVars['id'] . ";\n";

      print "document.speed.update.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
