<?php
# Script: openview.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Delete association entries

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "openview.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from alarm_blocks");

      $q_string  = "select block_text ";
      $q_string .= "from alarm_blocks ";
      $q_string .= "where block_id = " . $formVars['id'] . " ";
      $q_alarm_blocks = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_alarm_blocks = mysqli_fetch_array($q_alarm_blocks) or die($q_string . ": " . mysqli_error($db));

      $q_string  = "update ";
      $q_string .= "alarms ";
      $q_string .= "set alarm_disabled = 0 ";
      $q_string .= "where alarm_text like \"%" . $a_alarm_blocks['block_text'] . "%\"";
      $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "delete ";
      $q_string .= "from alarm_blocks ";
      $q_string .= "where block_id= " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      print "alert('Block deleted.');\n";

      print "clear_fields();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
