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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from mon_type");

      $q_string  = "select mt_name ";
      $q_string .= "from mon_type ";
      $q_string .= "where mt_id = " . $formVars['id'];
      $q_mon_type = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_mon_type = mysqli_fetch_array($q_mon_type);
      mysqli_free_result($q_mon_type);

      print "document.types.mt_name.value = '" . mysqli_real_escape_string($db, $a_mon_type['mt_name']) . "';\n";

      print "document.types.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
