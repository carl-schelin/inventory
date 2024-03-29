<?php
# Script: ipaddress.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "ipaddress.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_ipaddress");

      $q_string  = "select ip_ipv4,ip_ipv6,ip_hostname,ip_domain,ip_type,ip_subzone,ip_description,ip_notes,net_zone ";
      $q_string .= "from inv_ipaddress ";
      $q_string .= "left join inv_network on inv_network.net_id = inv_ipaddress.ip_network ";
      $q_string .= "where ip_id = " . $formVars['id'];
      $q_inv_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_ipaddress = mysqli_fetch_array($q_inv_ipaddress);

      $ip_subzone = return_Index($db, $a_inv_ipaddress['ip_subzone'],  "select sub_id from inv_sub_zones where sub_zone = " . $a_inv_ipaddress['net_zone'] . " order by sub_name");
      $ip_type    = return_Index($db, $a_inv_ipaddress['ip_type'],     "select ip_id from inv_ip_types order by ip_name ");

      print "document.formUpdate.ip_ipv4.value = '"          . mysqli_real_escape_string($db, $a_inv_ipaddress['ip_ipv4'])          . "';\n";
      print "document.formUpdate.ip_ipv6.value = '"          . mysqli_real_escape_string($db, $a_inv_ipaddress['ip_ipv6'])          . "';\n";
      print "document.formUpdate.ip_hostname.value = '"      . mysqli_real_escape_string($db, $a_inv_ipaddress['ip_hostname'])      . "';\n";
      print "document.formUpdate.ip_domain.value = '"        . mysqli_real_escape_string($db, $a_inv_ipaddress['ip_domain'])        . "';\n";
      print "document.formUpdate.ip_description.value = '"   . mysqli_real_escape_string($db, $a_inv_ipaddress['ip_description'])   . "';\n";
      print "document.formUpdate.ip_notes.value = '"         . mysqli_real_escape_string($db, $a_inv_ipaddress['ip_notes'])         . "';\n";

      print "document.formUpdate.ip_subzone['"  . $ip_subzone    . "'].selected = true;\n";
      print "document.formUpdate.ip_type['"     . $ip_type       . "'].selected = true;\n";

      print "document.formUpdate.id.value = '" . $formVars['id'] . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
