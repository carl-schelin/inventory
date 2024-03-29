<?php
# Script: family.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "family.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_family");

      $q_string  = "select fam_name ";
      $q_string .= "from inv_family ";
      $q_string .= "where fam_id = " . $formVars['id'];
      $q_inv_family = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_family = mysqli_fetch_array($q_inv_family);
      mysqli_free_result($q_inv_family);

      print "document.family.fam_name.value = '"   . mysqli_real_escape_string($db, $a_inv_family['fam_name'])   . "';\n";

      print "document.family.id.value = " . $formVars['id'] . ";\n";

      print "document.family.fam_name.focus();\n";

      print "document.family.update.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
