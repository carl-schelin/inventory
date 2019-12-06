<?php
# Script: openview.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from alarm_blocks");

      $q_string  = "select block_text ";
      $q_string .= "from alarm_blocks ";
      $q_string .= "where block_id = " . $formVars['id'] . " ";
      $q_alarm_blocks = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_alarm_blocks = mysql_fetch_array($q_alarm_blocks) or die($q_string . ": " . mysql_error());

      $q_string  = "update ";
      $q_string .= "alarms ";
      $q_string .= "set alarm_disabled = 0 ";
      $q_string .= "where alarm_text like \"%" . $a_alarm_blocks['block_text'] . "%\"";
      $insert = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));

      $q_string  = "delete ";
      $q_string .= "from alarm_blocks ";
      $q_string .= "where block_id= " . $formVars['id'];
      $insert = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));

      print "alert('Block deleted.');\n";

      print "clear_fields();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
