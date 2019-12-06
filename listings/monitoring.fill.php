<?php
# Script: monitoring.fill.php
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
    $package = "monitoring.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from changelog");


      $q_string  = "select cl_id,cl_name ";
      $q_string .= "from changelog ";
      $q_string .= "where cl_id = " . $formVars['id'];
      $q_changelog = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_changelog = mysql_fetch_array($q_changelog);
      mysql_free_result($q_changelog);

      print "document.changelog.cl_name.value = '" . mysql_real_escape_string($a_changelog['cl_name']) . "';\n";

      print "document.changelog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
