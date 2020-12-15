<?php
# Script: zones.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from ip_zones");

      $q_string  = "select zone_name,zone_desc ";
      $q_string .= "from ip_zones ";
      $q_string .= "where zone_id = " . $formVars['id'];
      $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_ip_zones = mysqli_fetch_array($q_ip_zones);
      mysqli_free_result($q_ip_zones);

      print "document.zones.zone_name.value = '" . mysqli_real_escape_string($a_ip_zones['zone_name']) . "';\n";
      print "document.zones.zone_desc.value = '" . mysqli_real_escape_string($a_ip_zones['zone_desc']) . "';\n";

      print "document.zones.id.value = " . $formVars['id'] . ";\n";

      print "document.zones.update.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
