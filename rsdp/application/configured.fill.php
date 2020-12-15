<?php
# Script: configured.fill.php
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
    $package = "configured.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_applications");

      $q_string  = "select app_id,app_tested,app_integrated,app_failover,app_concheck ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = " . $formVars['rsdp'];
      $q_rsdp_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_applications) > 0) {
        $a_rsdp_applications = mysqli_fetch_array($q_rsdp_applications);

        if ($a_rsdp_applications['app_concheck']) {
          print "document.rsdp.app_concheck.checked = true;\n";
        } else {
          print "document.rsdp.app_concheck.checked = false;\n";
        }
        if ($a_rsdp_applications['app_tested']) {
          print "document.rsdp.app_tested.checked = true;\n";
        } else {
          print "document.rsdp.app_tested.checked = false;\n";
        }
        if ($a_rsdp_applications['app_integrated']) {
          print "document.rsdp.app_integrated.checked = true;\n";
        } else {
          print "document.rsdp.app_integrated.checked = false;\n";
        }
        if ($a_rsdp_applications['app_failover']) {
          print "document.rsdp.app_failover.checked = true;\n";
        } else {
          print "document.rsdp.app_failover.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_applications['app_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_applications);

      print "validate_Form();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
