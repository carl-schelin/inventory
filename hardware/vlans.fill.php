<?php
# Script: vlans.fill.php
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
    $package = "vlans.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from vlans");

      $q_string  = "select vlan_vlan,vlan_zone,vlan_name,vlan_description,vlan_range,vlan_gateway,vlan_netmask ";
      $q_string .= "from vlans ";
      $q_string .= "where vlan_id = " . $formVars['id'];
      $q_vlans = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_vlans = mysqli_fetch_array($q_vlans);

      print "document.vlans.vlan_vlan.value = '"        . mysqli_real_escape_string($a_vlans['vlan_vlan'])        . "';\n";
      print "document.vlans.vlan_zone.value = '"        . mysqli_real_escape_string($a_vlans['vlan_zone'])        . "';\n";
      print "document.vlans.vlan_name.value = '"        . mysqli_real_escape_string($a_vlans['vlan_name'])        . "';\n";
      print "document.vlans.vlan_description.value = '" . mysqli_real_escape_string($a_vlans['vlan_description']) . "';\n";
      print "document.vlans.vlan_range.value = '"       . mysqli_real_escape_string($a_vlans['vlan_range'])       . "';\n";
      print "document.vlans.vlan_netmask.value = '"     . mysqli_real_escape_string($a_vlans['vlan_netmask'])     . "';\n";
      print "document.vlans.vlan_gateway.value = '"     . mysqli_real_escape_string($a_vlans['vlan_gateway'])     . "';\n";

      print "document.vlans.id.value = '" . $formVars['id'] . "';\n";

      print "document.vlans.update.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
