<?php
# Script: timezones.fill.php
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
    $package = "timezones.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from zones");

      $q_string  = "select zone_name,zone_description,zone_offset ";
      $q_string .= "from zones ";
      $q_string .= "where zone_id = " . $formVars['id'];
      $q_zones = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_zones = mysql_fetch_array($q_zones);
      mysql_free_result($q_zones);

      print "document.zones.zone_name.value = '"         . mysql_real_escape_string($a_zones['zone_name'])        . "';\n";
      print "document.zones.zone_description.value  = '" . mysql_real_escape_string($a_zones['zone_description']) . "';\n";
      print "document.zones.zone_offset.value = '"       . mysql_real_escape_string($a_zones['zone_offset'])      . "';\n";

      print "document.zones.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
