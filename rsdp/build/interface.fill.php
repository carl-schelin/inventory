<?php
# Script: interface.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rsdp_interface");

      $q_string  = "select if_id,if_if_id,if_rsdp,if_name,if_sysport,if_interface,if_ipcheck,if_swcheck,";
      $q_string .= "if_zone,if_media,if_speed,if_duplex,if_redundant,if_type,if_description,if_virtual,";
      $q_string .= "if_monitored,if_groupname ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_id = " . $formVars['id'];
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);
      mysqli_free_result($q_rsdp_interface);

      $ifmedia     = return_Index($db, $a_rsdp_interface['if_media'],     "select med_id from int_media order by med_text");
      $ifspeed     = return_Index($db, $a_rsdp_interface['if_speed'],     "select spd_id from int_speed order by spd_text");
      $ifduplex    = return_Index($db, $a_rsdp_interface['if_duplex'],    "select dup_id from int_duplex order by dup_text");
      $ifredundant = return_Index($db, $a_rsdp_interface['if_redundant'], "select red_id from int_redundancy order by red_text");
      $iftype      = return_Index($db, $a_rsdp_interface['if_type'],      "select itp_id from inttype order by itp_id");
      $ifzone      = return_Index($db, $a_rsdp_interface['if_zone'],      "select zone_id from ip_zones order by zone_name");
      $ififid      = return_Index($db, $a_rsdp_interface['if_if_id'],     "select if_id from rsdp_interface where if_rsdp = " . $a_rsdp_interface['if_rsdp'] . " and if_redundant > 0 order by if_interface");


      print "document.interface.if_name.value = '"        . mysqli_real_escape_string($db, $a_rsdp_interface['if_name'])        . "';\n";
      print "document.interface.if_sysport.value = '"     . mysqli_real_escape_string($db, $a_rsdp_interface['if_sysport'])     . "';\n";
      print "document.interface.if_interface.value = '"   . mysqli_real_escape_string($db, $a_rsdp_interface['if_interface'])   . "';\n";
      print "document.interface.if_description.value = '" . mysqli_real_escape_string($db, $a_rsdp_interface['if_description']) . "';\n";
      print "document.interface.if_groupname.value = '"   . mysqli_real_escape_string($db, $a_rsdp_interface['if_groupname'])   . "';\n";

      print "document.interface.if_zone['"      . $ifzone                      . "'].selected = true;\n";
      print "document.interface.if_media['"     . $ifmedia                     . "'].selected = true;\n";
      print "document.interface.if_speed['"     . $ifspeed                     . "'].selected = true;\n";
      print "document.interface.if_duplex['"    . $ifduplex                    . "'].selected = true;\n";
      print "document.interface.if_redundant['" . $ifredundant                 . "'].selected = true;\n";
      print "document.interface.if_type['"      . $iftype                      . "'].selected = true;\n";
      print "document.interface.if_if_id['"     . $ififid                      . "'].selected = true;\n";

      if ($a_rsdp_interface['if_virtual']) {
        print "document.interface.if_virtual.checked = true;\n";
      } else {
        print "document.interface.if_virtual.checked = false;\n";
      }
      if ($a_rsdp_interface['if_monitored']) {
        print "document.interface.if_monitored.checked = true;\n";
      } else {
        print "document.interface.if_monitored.checked = false;\n";
      }
      if ($a_rsdp_interface['if_ipcheck']) {
        print "document.interface.if_ipcheck.checked = true;\n";
      } else {
        print "document.interface.if_ipcheck.checked = false;\n";
      }
      if ($a_rsdp_interface['if_swcheck']) {
        print "document.interface.if_swcheck.checked = true;\n";
      } else {
        print "document.interface.if_swcheck.checked = false;\n";
      }

      print "document.interface.if_id.value = " . $formVars['id'] . ";\n";

      print "validate_Interface();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
