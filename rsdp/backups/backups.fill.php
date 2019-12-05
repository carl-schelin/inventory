<?php
# Script: backups.fill.php
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
    $package = "backups.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infrastructure");

      $q_string  = "select if_id,if_backups,if_buverified,if_bucheck ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infrastructure = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_infrastructure) > 0) {
        $a_rsdp_infrastructure = mysql_fetch_array($q_rsdp_infrastructure);

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

      mysql_free_result($q_rsdp_infrastructure);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
