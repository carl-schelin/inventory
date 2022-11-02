<?php
# Script: maintenance.windows.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "maintenance.windows.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_maintenance");

      $q_string  = "select man_text ";
      $q_string .= "from inv_maintenance ";
      $q_string .= "where man_id = " . $formVars['id'];
      $q_inv_maintenance = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_maintenance = mysqli_fetch_array($q_inv_maintenance);
      mysqli_free_result($q_inv_maintenance);

      print "document.formUpdate.man_text.value = '" . mysqli_real_escape_string($db, $a_inv_maintenance['man_text'])        . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
