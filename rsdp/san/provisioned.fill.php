<?php
# Script: provisioned.fill.php
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
    $package = "provisioned.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infrastructure");

      $q_string  = "select if_id,if_sanconf,if_provisioned,if_procheck ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {

        if ($a_rsdp_infrastructure['if_procheck']) {
          print "document.rsdp.if_procheck.checked = true;\n";
        } else {
          print "document.rsdp.if_procheck.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_sanconf']) {
          print "document.rsdp.if_sanconf.checked = true;\n";
        } else {
          print "document.rsdp.if_sanconf.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_provisioned']) {
          print "document.rsdp.if_provisioned.checked = true;\n";
        } else {
          print "document.rsdp.if_provisioned.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_infrastructure['if_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_infrastructure);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
