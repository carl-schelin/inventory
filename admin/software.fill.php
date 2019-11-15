<?php
# Script: software.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "software.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from sw_support");

      $q_string  = "select sw_software,sw_eol,sw_eos ";
      $q_string .= "from sw_support ";
      $q_string .= "where sw_id = " . $formVars['id'];
      $q_sw_support = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_sw_support = mysql_fetch_array($q_sw_support);
      mysql_free_result($q_sw_support);

      print "document.software.sw_software.value = '"  . $a_sw_support['sw_software']  . "';\n";
      print "document.software.sw_eol.value = '"       . $a_sw_support['sw_eol']       . "';\n";
      print "document.software.sw_eos.value = '"       . $a_sw_support['sw_eos']       . "';\n";

      print "document.software.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
