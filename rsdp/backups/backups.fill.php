<?php
# Script: backups.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "backups.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infrastructure");

      $q_string  = "select if_id,if_backups,if_buverified,if_bucheck ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
        $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

        if ($a_rsdp_infrastructure['if_bucheck']) {
          print "document.rsdp.if_bucheck.checked = true;\n";
        } else {
          print "document.rsdp.if_bucheck.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_backups']) {
          print "document.rsdp.if_backups.checked = true;\n";
        } else {
          print "document.rsdp.if_backups.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_buverified']) {
          print "document.rsdp.if_buverified.checked = true;\n";
        } else {
          print "document.rsdp.if_buverified.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_infrastructure['if_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_infrastructure);

      print "validate_Form();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
