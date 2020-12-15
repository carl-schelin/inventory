<?php
# Script: network.filter.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "network.filter.php";
    $formVars['rsdp']     = clean($_GET['rsdp'],       10);
    $formVars['filter']   = clean($_GET['filter'],    255);
    $formVars['status']   = clean($_GET['status'],     10);

    if (check_userlevel($db, $AL_Edit)) {

      if ($formVars['status'] == 'true') {
        if (strlen($formVars['filter']) == 0) {
          $filter = $formVars['rsdp'];
        } else {
          $filter = $formVars['filter'] . "," . $formVars['rsdp'];
        }
      }

      if ($formVars['status'] == 'false') {
        $filter = str_replace($formVars['rsdp'], '', $formVars['filter']);
      }

      $filter = str_replace(",,", ',', $filter);
      $filter = preg_replace('/,$/', '', $filter);
      $filter = preg_replace('/^,/', '', $filter);

      print "document.rsdp.filter.value = '" . $filter . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
