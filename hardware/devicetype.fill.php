<?php
# Script: devicetype.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "devicetype.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_device");

      $q_string  = "select dev_type,dev_description,dev_infrastructure,dev_notes ";
      $q_string .= "from inv_device ";
      $q_string .= "where dev_id = " . $formVars['id'];
      $q_inv_device = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_device = mysqli_fetch_array($q_inv_device);
      mysqli_free_result($q_inv_device);

      print "document.formUpdate.dev_type.value = '"        . mysqli_real_escape_string($db, $a_inv_device['dev_type'])        . "';\n";
      print "document.formUpdate.dev_description.value = '" . mysqli_real_escape_string($db, $a_inv_device['dev_description']) . "';\n";
      print "document.formUpdate.dev_notes.value = '"       . mysqli_real_escape_string($db, $a_inv_device['dev_notes'])       . "';\n";

      if ($a_inv_device['dev_infrastructure']) {
        print "document.formUpdate.dev_infrastructure.checked = true\n;";
       } else {
        print "document.formUpdate.dev_infrastructure.checked = false\n;";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
