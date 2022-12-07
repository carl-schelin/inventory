<?php
# Script: description.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "description.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_int_types");

      $q_string  = "select itp_name,itp_acronym,itp_description ";
      $q_string .= "from inv_int_types ";
      $q_string .= "where itp_id = " . $formVars['id'];
      $q_inv_int_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_int_types = mysqli_fetch_array($q_inv_int_types);
      mysqli_free_result($q_inv_int_types);

      print "document.formUpdate.itp_name.value = '"        . mysqli_real_escape_string($db, $a_inv_int_types['itp_name'])        . "';\n";
      print "document.formUpdate.itp_acronym.value = '"     . mysqli_real_escape_string($db, $a_inv_int_types['itp_acronym'])     . "';\n";
      print "document.formUpdate.itp_description.value = '" . mysqli_real_escape_string($db, $a_inv_int_types['itp_description']) . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
