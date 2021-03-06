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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from net_zones");

      $q_string  = "select zone_zone,zone_acronym ";
      $q_string .= "from net_zones ";
      $q_string .= "where zone_id = " . $formVars['id'];
      $q_net_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_net_zones = mysqli_fetch_array($q_net_zones);
      mysqli_free_result($q_net_zones);

      print "document.updateDialog.zone_zone.value = '"        . mysqli_real_escape_string($db, $a_net_zones['zone_zone'])        . "';\n";
      print "document.updateDialog.zone_acronym.value = '"     . mysqli_real_escape_string($db, $a_net_zones['zone_acronym'])     . "';\n";

      print "document.updateDialog.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
