<?php
# Script: interface.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_interface");

      $q_string  = "select int_companyid,int_face,int_int_id,int_eth,int_switch,";
      $q_string .= "int_note,int_primary,int_type,int_media,int_speed,int_port,int_sysport,";
      $q_string .= "int_duplex,int_redundancy,int_groupname,";
      $q_string .= "int_backup,int_management,int_virtual,";
      $q_string .= "int_login,int_ipaddressid ";
      $q_string .= "from inv_interface ";
      $q_string .= "where int_id = " . $formVars['id'];
      $q_inv_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_interface = mysqli_fetch_array($q_inv_interface);
      mysqli_free_result($q_inv_interface);

      $inttypes      = return_Index($db, $a_inv_interface['int_type'],       "select itp_id from inv_int_types order by itp_id");
      $intmedia      = return_Index($db, $a_inv_interface['int_media'],      "select med_id from inv_int_media order by med_default desc,med_text");
      $intspeed      = return_Index($db, $a_inv_interface['int_speed'],      "select spd_id from inv_int_speed order by spd_default desc,spd_text");
      $intduplex     = return_Index($db, $a_inv_interface['int_duplex'],     "select dup_id from inv_int_duplex order by dup_default desc,dup_text");
      $intintid      = return_Index($db, $a_inv_interface['int_int_id'],     "select int_id from inv_interface where int_companyid = " . $a_inv_interface['int_companyid'] . " and int_redundancy > 0 order by int_face");
      $intredundancy = return_Index($db, $a_inv_interface['int_redundancy'], "select red_id from inv_int_redundancy order by red_default desc,red_text");
      $intaddress    = return_Index($db, $a_inv_interface['int_ipaddressid'], "select ip_id from inv_ipaddress order by ip_hostname,ip_ipv4");

      print "document.formInterfaceUpdate.int_face.value = '"      . mysqli_real_escape_string($db, $a_inv_interface['int_face'])      . "';\n";
      print "document.formInterfaceUpdate.int_int_id.value = '"    . mysqli_real_escape_string($db, $a_inv_interface['int_int_id'])    . "';\n";
      print "document.formInterfaceUpdate.int_eth.value = '"       . mysqli_real_escape_string($db, $a_inv_interface['int_eth'])       . "';\n";
      print "document.formInterfaceUpdate.int_note.value = '"      . mysqli_real_escape_string($db, $a_inv_interface['int_note'])      . "';\n";
      print "document.formInterfaceUpdate.int_switch.value = '"    . mysqli_real_escape_string($db, $a_inv_interface['int_switch'])    . "';\n";
      print "document.formInterfaceUpdate.int_port.value = '"      . mysqli_real_escape_string($db, $a_inv_interface['int_port'])      . "';\n";
      print "document.formInterfaceUpdate.int_sysport.value = ' "  . mysqli_real_escape_string($db, $a_inv_interface['int_sysport'])   . "';\n";
      print "document.formInterfaceUpdate.int_groupname.value = '" . mysqli_real_escape_string($db, $a_inv_interface['int_groupname']) . "';\n";

      if ($inttypes > 0) {
        print "document.formInterfaceUpdate.int_type['"       . $inttypes      . "'].selected = true;\n";
      }
      if ($intmedia > 0) {
        print "document.formInterfaceUpdate.int_media['"      . $intmedia      . "'].selected = true;\n";
      }
      if ($intspeed > 0) {
        print "document.formInterfaceUpdate.int_speed['"      . $intspeed      . "'].selected = true;\n";
      }
      if ($intduplex > 0) {
        print "document.formInterfaceUpdate.int_duplex['"     . $intduplex     . "'].selected = true;\n";
      }
      if ($intredundancy > 0) {
        print "document.formInterfaceUpdate.int_redundancy['" . $intredundancy . "'].selected = true;\n";
      }
      print "document.formInterfaceUpdate.int_ipaddressid['" . $intaddress . "'].selected = true;\n";

      if ($a_inv_interface['int_primary']) {
        print "document.formInterfaceUpdate.int_primary.checked = true;\n";
      } else {
        print "document.formInterfaceUpdate.int_primary.checked = false;\n";
      }
      if ($a_inv_interface['int_backup']) {
        print "document.formInterfaceUpdate.int_backup.checked = true;\n";
      } else {
        print "document.formInterfaceUpdate.int_backup.checked = false;\n";
      }
      if ($a_inv_interface['int_management']) {
        print "document.formInterfaceUpdate.int_management.checked = true;\n";
      } else {
        print "document.formInterfaceUpdate.int_management.checked = false;\n";
      }
      if ($a_inv_interface['int_login']) {
        print "document.formInterfaceUpdate.int_login.checked = true;\n";
      } else {
        print "document.formInterfaceUpdate.int_login.checked = false;\n";
      }
      if ($a_inv_interface['int_virtual']) {
        print "document.formInterfaceUpdate.int_virtual.checked = true;\n";
      } else {
        print "document.formInterfaceUpdate.int_virtual.checked = false;\n";
      }

      print "document.formInterfaceUpdate.int_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
