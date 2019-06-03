<?php
# Script: designed.fill.php
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
    $package = "designed.fill.php";
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['rsdp'] . " from rsdp_designed");

      $q_string  = "select san_id,san_checklist ";
      $q_string .= "from rsdp_designed ";
      $q_string .= "where san_rsdp = " . $formVars['rsdp'];
      $q_rsdp_designed = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_designed) > 0) {
        $a_rsdp_designed = mysql_fetch_array($q_rsdp_designed);

        if ($a_rsdp_designed['san_checklist']) {
          print "document.rsdp.san_checklist.checked = true;\n";
        } else {
          print "document.rsdp.san_checklist.checked = false;\n";
        }

        print "document.rsdp.id.value = " . $a_rsdp_designed['san_id'] . ";\n";

      }

      mysql_free_result($q_rsdp_designed);

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
