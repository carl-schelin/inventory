<?php
# Script: device.fill.php
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
    $package = "device.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from device");

      $q_string  = "select dev_type,dev_description,dev_infrastructure,dev_notes ";
      $q_string .= "from device ";
      $q_string .= "where dev_id = " . $formVars['id'];
      $q_device = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_device = mysql_fetch_array($q_device);
      mysql_free_result($q_device);

      print "document.device.dev_type.value = '"        . mysql_real_escape_string($a_device['dev_type'])        . "';\n";
      print "document.device.dev_description.value = '" . mysql_real_escape_string($a_device['dev_description']) . "';\n";
      print "document.device.dev_notes.value = '"       . mysql_real_escape_string($a_device['dev_notes'])       . "';\n";

      if ($a_device['dev_infrastructure']) {
        print "document.device.dev_infrastructure.checked = true\n;";
       } else {
        print "document.device.dev_infrastructure.checked = false\n;";
      }

      print "document.device.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
