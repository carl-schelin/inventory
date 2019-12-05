<?php
# Script: application.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from application");

      $q_string  = "select app_description,app_deleted ";
      $q_string .= "from application ";
      $q_string .= "where app_id = " . $formVars['id'];
      $q_application = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_application = mysql_fetch_array($q_application);
      mysql_free_result($q_application);

      print "document.application.app_description.value = '" . mysql_real_escape_string($a_application['app_description'])       . "';\n";

      if ($a_application['app_deleted']) {
        print "document.application.app_deleted.checked = true;\n";
      } else {
        print "document.application.app_deleted.checked = false;\n";
      }

      print "document.application.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
