<?php
# Script: severity.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "severity.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_severity");

      $q_string  = "select sev_name ";
      $q_string .= "from inv_severity ";
      $q_string .= "where sev_id = " . $formVars['id'];
      $q_inv_severity = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_severity = mysqli_fetch_array($q_inv_severity);
      mysqli_free_result($q_inv_severity);

      print "document.severity.sev_name.value = '"   . mysqli_real_escape_string($db, $a_inv_severity['sev_name'])   . "';\n";

      print "document.severity.id.value = " . $formVars['id'] . ";\n";

      print "document.severity.sev_name.focus();\n";

      print "document.severity.update.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
