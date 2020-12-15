<?php
# Script: apigroups.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "apigroups.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from apigroups");

      $q_string  = "select api_name ";
      $q_string .= "from apigroups ";
      $q_string .= "where api_id = " . $formVars['id'];
      $q_apigroups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_apigroups = mysqli_fetch_array($db, $q_apigroups);
      mysqli_free_result($q_apigroups);

      print "document.apigroups.api_name.value = '" . mysqli_real_escape_string($db, $a_apigroups['api_name']) . "';\n";

      print "document.apigroups.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
