<?php
# Script: exclude.checked.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "exclude.checked.php";
    $formVars['noexpire'] = clean($_GET['noexpire'], 10);

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['noexpire'] == 'true') {
        $expire = "2038-01-01";
      }

      if ($formVars['noexpire'] == 'false') {
        $expire = date('Y-m-d');
      }

      print "document.exclude.ex_expiration.value = '" . $expire . "';";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
