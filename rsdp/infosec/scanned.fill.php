<?php
# Script: scanned.fill.php
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
    $package = "scanned.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infosec");

      $q_string  = "select is_id,is_ticket,is_scan,is_verified,is_checklist ";
      $q_string .= "from rsdp_infosec ";
      $q_string .= "where is_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infosec = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_infosec) > 0) {
        $a_rsdp_infosec = mysqli_fetch_array($q_rsdp_infosec);

        print "document.rsdp.is_ticket.value = '" . mysqli_real_escape_string($a_rsdp_infosec['is_ticket']) . "';\n";

        if ($a_rsdp_infosec['is_checklist']) {
          print "document.rsdp.is_checklist.checked = true;\n";
        } else {
          print "document.rsdp.is_checklist.checked = false;\n";
        }
        if ($a_rsdp_infosec['is_scan']) {
          print "document.rsdp.is_scan.checked = true;\n";
        } else {
          print "document.rsdp.is_scan.checked = false;\n";
        }
        if ($a_rsdp_infosec['is_verified']) {
          print "document.rsdp.is_verified.checked = true;\n";
        } else {
          print "document.rsdp.is_verified.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_infosec['is_id'] . ";\n";

      }

      mysqli_free_result($q_rsdp_infosec);

      print "validate_Form();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
