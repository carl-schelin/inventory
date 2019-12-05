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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_infrastructure");

      $q_string  = "select if_id,if_sanfs,if_verified,if_checklist,if_wiki,if_svrmgt ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'];
      $q_rsdp_infrastructure = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      if (mysql_num_rows($q_rsdp_infrastructure) > 0) {
        $a_rsdp_infrastructure = mysql_fetch_array($q_rsdp_infrastructure);

        if ($a_rsdp_infrastructure['if_checklist']) {
          print "document.rsdp.if_checklist.checked = true;\n";
        } else {
          print "document.rsdp.if_checklist.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_sanfs']) {
          print "document.rsdp.if_sanfs.checked = true;\n";
        } else {
          print "document.rsdp.if_sanfs.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_verified']) {
          print "document.rsdp.if_verified.checked = true;\n";
        } else {
          print "document.rsdp.if_verified.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_wiki']) {
          print "document.rsdp.if_wiki.checked = true;\n";
        } else {
          print "document.rsdp.if_wiki.checked = false;\n";
        }
        if ($a_rsdp_infrastructure['if_svrmgt']) {
          print "document.rsdp.if_svrmgt.checked = true;\n";
        } else {
          print "document.rsdp.if_svrmgt.checked = false;\n";
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
