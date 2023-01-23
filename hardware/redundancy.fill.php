<?php
# Script: redundancy.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "redundancy.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_int_redundancy");

      $q_string  = "select red_text,red_default ";
      $q_string .= "from inv_int_redundancy ";
      $q_string .= "where red_id = " . $formVars['id'];
      $q_inv_int_redundancy = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_int_redundancy = mysqli_fetch_array($q_inv_int_redundancy);
      mysqli_free_result($q_inv_int_redundancy);

      print "document.formUpdate.red_text.value = '" . mysqli_real_escape_string($db, $a_inv_int_redundancy['red_text']) . "';\n";

      if ($a_inv_int_redundancy['red_default']) {
        print "document.formUpdate.red_default.checked = true;\n";
      } else {
        print "document.formUpdate.red_default.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
