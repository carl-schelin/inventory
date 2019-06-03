<?php
# Script: scanned.fill.php
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
    $package = "scanned.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infosec");

      $q_string  = "select is_id,is_ticket,is_scan,is_verified,is_checklist ";
      $q_string .= "from rsdp_infosec ";
      $q_string .= "where is_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infosec = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_infosec) > 0) {
        $a_rsdp_infosec = mysql_fetch_array($q_rsdp_infosec);

        print "document.rsdp.is_ticket.value = '" . mysql_real_escape_string($a_rsdp_infosec['is_ticket']) . "';\n";

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

      mysql_free_result($q_rsdp_infosec);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
