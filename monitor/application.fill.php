<?php
# Script: application.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "application.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from application");

      $q_string  = "select app_description,app_deleted ";
      $q_string .= "from application ";
      $q_string .= "where app_id = " . $formVars['id'];
      $q_application = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_application = mysqli_fetch_array($q_application);
      mysqli_free_result($q_application);

      print "document.application.app_description.value = '" . mysqli_real_escape_string($db, $a_application['app_description'])       . "';\n";

      if ($a_application['app_deleted']) {
        print "document.application.app_deleted.checked = true;\n";
      } else {
        print "document.application.app_deleted.checked = false;\n";
      }

      print "document.application.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
