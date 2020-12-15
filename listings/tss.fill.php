<?php
# Script: tss.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "tss.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from changelog");

      $q_string  = "select cl_id,cl_name ";
      $q_string .= "from changelog ";
      $q_string .= "where cl_id = " . $formVars['id'];
      $q_changelog = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_changelog = mysqli_fetch_array($q_changelog);
      mysqli_free_result($q_changelog);

      print "document.changelog.cl_name.value = '" . mysqli_real_escape_string($db, $a_changelog['cl_name']) . "';\n";

      print "document.changelog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
