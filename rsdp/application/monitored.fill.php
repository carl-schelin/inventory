<?php
# Script: monitored.fill.php
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
    $package = "monitored.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_applications");

      $q_string  = "select app_id,app_monitor,app_verified,app_moncheck ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = " . $formVars['rsdp'];
      $q_rsdp_applications = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_applications) > 0) {
        $a_rsdp_applications = mysql_fetch_array($q_rsdp_applications);

        if ($a_rsdp_applications['app_moncheck']) {
          print "document.rsdp.app_moncheck.checked = true;\n";
        } else {
          print "document.rsdp.app_moncheck.checked = false;\n";
        }
        if ($a_rsdp_applications['app_monitor']) {
          print "document.rsdp.app_monitor.checked = true;\n";
        } else {
          print "document.rsdp.app_monitor.checked = false;\n";
        }
        if ($a_rsdp_applications['app_verified']) {
          print "document.rsdp.app_verified.checked = true;\n";
        } else {
          print "document.rsdp.app_verified.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_applications['app_id'] . ";\n";

      }

      mysql_free_result($q_rsdp_applications);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
