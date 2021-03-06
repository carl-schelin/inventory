<?php
# Script: network.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "network.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from network");

      $q_string  = "select net_ipv4,net_ipv6,net_mask,net_zone,net_location,net_vlan,net_description ";
      $q_string .= "from network ";
      $q_string .= "where net_id = " . $formVars['id'];
      $q_network = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_network = mysqli_fetch_array($q_network);

      $netzone     = return_Index($db, $a_network['net_zone'],     "select zone_id from net_zones order by zone_zone");
      $netlocation = return_Index($db, $a_network['net_location'], "select loc_id from locations order by loc_name");

      print "document.updateDialog.net_ipv4.value = '"        . mysqli_real_escape_string($db, $a_network['net_ipv4'])        . "';\n";
      print "document.updateDialog.net_ipv6.value = '"        . mysqli_real_escape_string($db, $a_network['net_ipv6'])        . "';\n";
      print "document.updateDialog.net_vlan.value = '"        . mysqli_real_escape_string($db, $a_network['net_vlan'])        . "';\n";
      print "document.updateDialog.net_description.value = '" . mysqli_real_escape_string($db, $a_network['net_description']) . "';\n";

      print "document.updateDialog.net_mask['"     . $a_network['net_mask'] . "'].selected = true;\n";
      print "document.updateDialog.net_zone['"     . $netzone               . "'].selected = true;\n";
      print "document.updateDialog.net_location['" . $netlocation           . "'].selected = true;\n";

      print "document.updateDialog.id.value = '" . $formVars['id'] . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
