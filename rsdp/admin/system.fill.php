<?php
# Script: system.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from operatingsystem");

      $q_string  = "select os_vendor,os_software,os_exception ";
      $q_string .= "from operatingsystem ";
      $q_string .= "where os_id = " . $formVars['id'];
      $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);
      mysqli_free_result($q_operatingsystem);

      print "document.dialog.os_vendor.value = '"    . mysqli_real_escape_string($a_operatingsystem['os_vendor'])   . "';\n";
      print "document.dialog.os_software.value  = '" . mysqli_real_escape_string($a_operatingsystem['os_software']) . "';\n";

      if ($a_operatingsystem['os_exception']) {
        print "document.dialog.os_exception.checked = true;\n";
      } else {
        print "document.dialog.os_exception.checked = false;\n";
      }

      print "document.dialog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
