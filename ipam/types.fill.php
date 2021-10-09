<?php
# Script: types.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "types.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from ip_types");

      $q_string  = "select ip_name,ip_description ";
      $q_string .= "from ip_types ";
      $q_string .= "where ip_id = " . $formVars['id'];
      $q_ip_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_ip_types = mysqli_fetch_array($q_ip_types);
      mysqli_free_result($q_ip_types);

      print "document.formUpdate.ip_name.value = '"        . mysqli_real_escape_string($db, $a_ip_types['ip_name'])        . "';\n";
      print "document.formUpdate.ip_description.value = '" . mysqli_real_escape_string($db, $a_ip_types['ip_description']) . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
