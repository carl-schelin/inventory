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

      $q_string  = "select int_companyid,int_face,int_int_id,int_server,int_addr,int_eth,int_switch,int_mask,";
      $q_string .= "int_gate,int_note,int_ip6,int_primary,int_type,int_media,int_speed,int_port,int_sysport,";
      $q_string .= "int_duplex,int_role,int_redundancy,int_groupname,int_vlan,int_zone,int_openview,int_nagios,";
      $q_string .= "int_backup,int_management,int_xpoint,int_virtual,int_ypoint,int_zpoint,int_ping,int_ssh,";
      $q_string .= "int_http,int_ftp,int_smtp,int_snmp,int_load,int_uptime,int_cpu,int_swap,int_memory,";
      $q_string .= "int_cfg2html,int_notify,int_hours,int_domain,int_login ";
      $q_string .= "from interface ";
      $q_string .= "where int_id = " . $formVars['id'];
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_interface = mysqli_fetch_array($q_interface);
      mysqli_free_result($q_interface);

      $inttype       = return_Index($db, $a_interface['int_type'],       "select itp_id from inttype order by itp_id");
      $intzone       = return_Index($db, $a_interface['int_zone'],       "select zone_id from net_zones order by zone_name");
      $intmedia      = return_Index($db, $a_interface['int_media'],      "select med_id from int_media order by med_text");
      $intspeed      = return_Index($db, $a_interface['int_speed'],      "select spd_id from int_speed order by spd_text");
      $intduplex     = return_Index($db, $a_interface['int_duplex'],     "select dup_id from int_duplex order by dup_text");
      $introle       = return_Index($db, $a_interface['int_role'],       "select rol_id from int_role order by rol_text");
      $intintid      = return_Index($db, $a_interface['int_int_id'],     "select int_id from interface where int_companyid = " . $a_interface['int_companyid'] . " and int_redundancy > 0 order by int_face");
      $intredundancy = return_Index($db, $a_interface['int_redundancy'], "select red_id from int_redundancy order by red_text");


      print "document.edit.int_server.value = '"    . mysqli_real_escape_string($db, $a_interface['int_server'])    . "';\n";
      print "document.edit.int_domain.value = '"    . mysqli_real_escape_string($db, $a_interface['int_domain'])    . "';\n";
      print "document.edit.int_face.value = '"      . mysqli_real_escape_string($db, $a_interface['int_face'])      . "';\n";
      print "document.edit.int_int_id.value = '"    . mysqli_real_escape_string($db, $a_interface['int_int_id'])    . "';\n";
      print "document.edit.int_addr.value = '"      . mysqli_real_escape_string($db, $a_interface['int_addr'])      . "';\n";
      print "document.edit.int_eth.value = '"       . mysqli_real_escape_string($db, $a_interface['int_eth'])       . "';\n";
      print "document.edit.int_mask.value = '"      . mysqli_real_escape_string($db, $a_interface['int_mask'])      . "';\n";
      print "document.edit.int_gate.value = '"      . mysqli_real_escape_string($db, $a_interface['int_gate'])      . "';\n";
      print "document.edit.int_note.value = '"      . mysqli_real_escape_string($db, $a_interface['int_note'])      . "';\n";
      print "document.edit.int_switch.value = '"    . mysqli_real_escape_string($db, $a_interface['int_switch'])    . "';\n";
      print "document.edit.int_port.value = '"      . mysqli_real_escape_string($db, $a_interface['int_port'])      . "';\n";
      print "document.edit.int_sysport.value = ' "  . mysqli_real_escape_string($db, $a_interface['int_sysport'])   . "';\n";
      print "document.edit.int_vlan.value = '"      . mysqli_real_escape_string($db, $a_interface['int_vlan'])      . "';\n";
      print "document.edit.int_groupname.value = '" . mysqli_real_escape_string($db, $a_interface['int_groupname']) . "';\n";
      print "document.edit.int_xpoint.value = '"    . mysqli_real_escape_string($db, $a_interface['int_xpoint'])    . "';\n";
      print "document.edit.int_ypoint.value = '"    . mysqli_real_escape_string($db, $a_interface['int_ypoint'])    . "';\n";
      print "document.edit.int_zpoint.value = '"    . mysqli_real_escape_string($db, $a_interface['int_zpoint'])    . "';\n";

      print "document.edit.int_type['"       . $inttype       . "'].selected = true;\n";
      print "document.edit.int_zone['"       . $intzone       . "'].selected = true;\n";
      print "document.edit.int_media['"      . $intmedia      . "'].selected = true;\n";
      print "document.edit.int_speed['"      . $intspeed      . "'].selected = true;\n";
      print "document.edit.int_duplex['"     . $intduplex     . "'].selected = true;\n";
      print "document.edit.int_role['"       . $introle       . "'].selected = true;\n";
      print "document.edit.int_redundancy['" . $intredundancy . "'].selected = true;\n";

      print "document.edit.int_notify['" . $a_interface['int_notify'] . "'].checked = true;\n";
      print "document.edit.int_hours['"  . $a_interface['int_hours']  . "'].checked = true;\n";

      if ($a_interface['int_ip6']) {
        print "document.edit.int_ip6.checked = true;\n";
      } else {
        print "document.edit.int_ip6.checked = false;\n";
      }
      if ($a_interface['int_primary']) {
        print "document.edit.int_primary.checked = true;\n";
      } else {
        print "document.edit.int_primary.checked = false;\n";
      }
      if ($a_interface['int_openview']) {
        print "document.edit.int_openview.checked = true;\n";
      } else {
        print "document.edit.int_openview.checked = false;\n";
      }
      if ($a_interface['int_nagios']) {
        print "document.edit.int_nagios.checked = true;\n";
      } else {
        print "document.edit.int_nagios.checked = false;\n";
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
      if ($a_interface['int_ping']) {
        print "document.edit.int_ping.checked = true;\n";
      } else {
        print "document.edit.int_ping.checked = false;\n";
      }
      if ($a_interface['int_ssh']) {
        print "document.edit.int_ssh.checked = true;\n";
      } else {
        print "document.edit.int_ssh.checked = false;\n";
      }
      if ($a_interface['int_http']) {
        print "document.edit.int_http.checked = true;\n";
      } else {
        print "document.edit.int_http.checked = false;\n";
      }
      if ($a_interface['int_ftp']) {
        print "document.edit.int_ftp.checked = true;\n";
      } else {
        print "document.edit.int_ftp.checked = false;\n";
      }
      if ($a_interface['int_smtp']) {
        print "document.edit.int_smtp.checked = true;\n";
      } else {
        print "document.edit.int_smtp.checked = false;\n";
      }
      if ($a_interface['int_snmp']) {
        print "document.edit.int_snmp.checked = true;\n";
      } else {
        print "document.edit.int_snmp.checked = false;\n";
      }
      if ($a_interface['int_load']) {
        print "document.edit.int_load.checked = true;\n";
      } else {
        print "document.edit.int_load.checked = false;\n";
      }
      if ($a_interface['int_uptime']) {
        print "document.edit.int_uptime.checked = true;\n";
      } else {
        print "document.edit.int_uptime.checked = false;\n";
      }
      if ($a_interface['int_cpu']) {
        print "document.edit.int_cpu.checked = true;\n";
      } else {
        print "document.edit.int_cpu.checked = false;\n";
      }
      if ($a_interface['int_swap']) {
        print "document.edit.int_swap.checked = true;\n";
      } else {
        print "document.edit.int_swap.checked = false;\n";
      }
      if ($a_interface['int_memory']) {
        print "document.edit.int_memory.checked = true;\n";
      } else {
        print "document.edit.int_memory.checked = false;\n";
      }
      if ($a_interface['int_cfg2html']) {
        print "document.edit.int_cfg2html.checked = true;\n";
      } else {
        print "document.edit.int_cfg2html.checked = false;\n";
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
