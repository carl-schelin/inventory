<?php
# Script: zones.fill.php
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
    $package = "zones.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from ip_zones");

      $q_string  = "select zone_name,zone_desc ";
      $q_string .= "from ip_zones ";
      $q_string .= "where zone_id = " . $formVars['id'];
      $q_ip_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_ip_zones = mysql_fetch_array($q_ip_zones);
      mysql_free_result($q_ip_zones);

      print "document.zones.zone_name.value = '" . mysql_real_escape_string($a_ip_zones['zone_name']) . "';\n";
      print "document.zones.zone_desc.value = '" . mysql_real_escape_string($a_ip_zones['zone_desc']) . "';\n";

      print "document.zones.id.value = " . $formVars['id'] . ";\n";

      print "document.zones.update.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
