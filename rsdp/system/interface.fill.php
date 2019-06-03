<?php
# Script: interface.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rsdp_interface");

      $q_string  = "select if_name,if_ip,if_mac ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_id = " . $formVars['id'];
      $q_rsdp_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_interface = mysql_fetch_array($q_rsdp_interface);
      mysql_free_result($q_rsdp_interface);

      print "document.getElementById('if_name').innerHTML = '" . mysql_real_escape_string($a_rsdp_interface['if_name']) . "';\n";
      print "document.getElementById('if_ip').innerHTML = '"   . mysql_real_escape_string($a_rsdp_interface['if_ip']) . "';\n";

      print "document.interface.if_mac.value = '"  . mysql_real_escape_string($a_rsdp_interface['if_mac'])  . "';\n";

      print "document.interface.if_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
