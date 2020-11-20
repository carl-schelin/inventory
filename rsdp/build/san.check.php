<?php
# Script: san.check.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "san.check.php";
    $formVars['ask'] = 0;
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Checking interface " . $formVars['id']);

# checking for the existance of a requirement to get an IP or switch 
# configuration and then if there's any data in those fields.
      $q_string  = "select san_switch ";
      $q_string .= "from rsdp_san ";
      $q_string .= "where san_id = " . $formVars['id'];
      $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_san = mysqli_fetch_array($q_rsdp_san);

      if (strlen($a_rsdp_san['san_switch']) > 0) {
# if switch is set, then set to 1
        $formVars['ask'] = 1;
      }

      print "document.rsdp.sanokay.value = " . $formVars['ask'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
