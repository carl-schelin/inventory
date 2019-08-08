<?php
# Script: business.fill.php
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
    $package = "business.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from business_unit");

      $q_string  = "select bus_unit,bus_name ";
      $q_string .= "from business_unit ";
      $q_string .= "where bus_id = " . $formVars['id'];
      $q_business_unit = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_business_unit = mysql_fetch_array($q_business_unit);
      mysql_free_result($q_business_unit);

      print "document.business.bus_unit.value = '" . mysql_real_escape_string($a_business_unit['bus_unit']) . "';\n";
      print "document.business.bus_name.value = '" . mysql_real_escape_string($a_business_unit['bus_name']) . "';\n";

      print "document.business.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
