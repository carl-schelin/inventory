<?php
# Script: software.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from sw_support");

      $q_string  = "select sw_software,sw_eol,sw_eos ";
      $q_string .= "from sw_support ";
      $q_string .= "where sw_id = " . $formVars['id'];
      $q_sw_support = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_sw_support = mysqli_fetch_array($q_sw_support);
      mysqli_free_result($q_sw_support);

      print "document.formUpdate.sw_software.value = '"  . mysqli_real_escape_string($db, $a_sw_support['sw_software'])  . "';\n";
      print "document.formUpdate.sw_eol.value = '"       . mysqli_real_escape_string($db, $a_sw_support['sw_eol'])       . "';\n";
      print "document.formUpdate.sw_eos.value = '"       . mysqli_real_escape_string($db, $a_sw_support['sw_eos'])       . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
