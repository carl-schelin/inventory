<?php
# Script: duplex.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "duplex.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from int_duplex");

      $q_string  = "select dup_text,dup_default ";
      $q_string .= "from int_duplex ";
      $q_string .= "where dup_id = " . $formVars['id'];
      $q_int_duplex = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_int_duplex = mysqli_fetch_array($q_int_duplex);
      mysqli_free_result($q_int_duplex);

      print "document.formUpdate.dup_text.value = '" . mysqli_real_escape_string($db, $a_int_dupex['dup_text']) . "';\n";

      if ($a_int_duplex['dup_default']) {
        print "document.formUpdate.dup_default.checked = true;\n";
      } else {
        print "document.formUpdate.dup_default.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
