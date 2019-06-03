<?php
# Script: system.fill.php
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
    $package = "system.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from operatingsystem");

      $q_string  = "select os_vendor,os_software,os_exception ";
      $q_string .= "from operatingsystem ";
      $q_string .= "where os_id = " . $formVars['id'];
      $q_operatingsystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_operatingsystem = mysql_fetch_array($q_operatingsystem);
      mysql_free_result($q_operatingsystem);

      print "document.dialog.os_vendor.value = '"    . mysql_real_escape_string($a_operatingsystem['os_vendor'])   . "';\n";
      print "document.dialog.os_software.value  = '" . mysql_real_escape_string($a_operatingsystem['os_software']) . "';\n";

      if ($a_operatingsystem['os_exception']) {
        print "document.dialog.os_exception.checked = true;\n";
      } else {
        print "document.dialog.os_exception.checked = false;\n";
      }

      print "document.dialog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
