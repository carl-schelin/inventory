<?php
# Script: network.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from interface");

      $q_string  = "select int_companyid,int_face,int_int_id,int_eth,int_switch,";
      $q_string .= "int_note,int_primary,int_type,int_media,int_speed,int_port,int_sysport,";
      $q_string .= "int_duplex,int_redundancy,int_groupname,";
      $q_string .= "int_backup,int_management,int_virtual,";
      $q_string .= "int_login,int_ipaddressid ";
      $q_string .= "from interface ";
      $q_string .= "where int_id = " . $formVars['id'];
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_interface = mysqli_fetch_array($q_interface);
      mysqli_free_result($q_interface);

      $inttypes      = return_Index($db, $a_interface['int_type'],       "select itp_id from int_types order by itp_id");
      $intmedia      = return_Index($db, $a_interface['int_media'],      "select med_id from int_media order by med_text");
      $intspeed      = return_Index($db, $a_interface['int_speed'],      "select spd_id from int_speed order by spd_text");
      $intduplex     = return_Index($db, $a_interface['int_duplex'],     "select dup_id from int_duplex order by dup_text");
      $intintid      = return_Index($db, $a_interface['int_int_id'],     "select int_id from interface where int_companyid = " . $a_interface['int_companyid'] . " and int_redundancy > 0 order by int_face");
      $intredundancy = return_Index($db, $a_interface['int_redundancy'], "select red_id from int_redundancy order by red_text");
      $intaddress    = return_Index($db, $a_interface['int_ipaddressid'], "select ip_id from ipaddress order by ip_hostname,ip_ipv4") + 1;

      print "document.edit.int_face.value = '"      . mysqli_real_escape_string($db, $a_interface['int_face'])      . "';\n";
      print "document.edit.int_int_id.value = '"    . mysqli_real_escape_string($db, $a_interface['int_int_id'])    . "';\n";
      print "document.edit.int_eth.value = '"       . mysqli_real_escape_string($db, $a_interface['int_eth'])       . "';\n";
      print "document.edit.int_note.value = '"      . mysqli_real_escape_string($db, $a_interface['int_note'])      . "';\n";
      print "document.edit.int_switch.value = '"    . mysqli_real_escape_string($db, $a_interface['int_switch'])    . "';\n";
      print "document.edit.int_port.value = '"      . mysqli_real_escape_string($db, $a_interface['int_port'])      . "';\n";
      print "document.edit.int_sysport.value = ' "  . mysqli_real_escape_string($db, $a_interface['int_sysport'])   . "';\n";
      print "document.edit.int_groupname.value = '" . mysqli_real_escape_string($db, $a_interface['int_groupname']) . "';\n";

      if ($inttypes > 0) {
        print "document.edit.int_type['"       . $inttypes      . "'].selected = true;\n";
      }
      if ($intmedia > 0) {
        print "document.edit.int_media['"      . $intmedia      . "'].selected = true;\n";
      }
      if ($intspeed > 0) {
        print "document.edit.int_speed['"      . $intspeed      . "'].selected = true;\n";
      }
      if ($intduplex > 0) {
        print "document.edit.int_duplex['"     . $intduplex     . "'].selected = true;\n";
      }
      if ($intredundancy > 0) {
        print "document.edit.int_redundancy['" . $intredundancy . "'].selected = true;\n";
      }
      print "document.edit.int_ipaddressid['" . $intaddress . "'].selected = true;\n";

      if ($a_interface['int_primary']) {
        print "document.edit.int_primary.checked = true;\n";
      } else {
        print "document.edit.int_primary.checked = false;\n";
      }
      if ($a_interface['int_backup']) {
        print "document.edit.int_backup.checked = true;\n";
      } else {
        print "document.edit.int_backup.checked = false;\n";
      }
      if ($a_interface['int_management']) {
        print "document.edit.int_management.checked = true;\n";
      } else {
        print "document.edit.int_management.checked = false;\n";
      }
      if ($a_interface['int_login']) {
        print "document.edit.int_login.checked = true;\n";
      } else {
        print "document.edit.int_login.checked = false;\n";
      }
      if ($a_interface['int_virtual']) {
        print "document.edit.int_virtual.checked = true;\n";
      } else {
        print "document.edit.int_virtual.checked = false;\n";
      }

      print "document.edit.int_id.value = " . $formVars['id'] . ";\n";

      print "document.edit.int_update.disabled = false;\n";

      print "document.edit.int_server.focus();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
