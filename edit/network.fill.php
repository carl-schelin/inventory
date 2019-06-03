<?php
# Script: network.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from interface");

      $q_string  = "select int_companyid,int_face,int_int_id,int_server,int_addr,int_eth,int_switch,int_mask,";
      $q_string .= "int_gate,int_note,int_ip6,int_primary,int_type,int_media,int_speed,int_port,int_sysport,";
      $q_string .= "int_duplex,int_role,int_redundancy,int_groupname,int_vlan,int_zone,int_openview,int_nagios,";
      $q_string .= "int_xpoint,int_virtual,int_ypoint,int_zpoint,int_ping,int_ssh,int_http,int_ftp,int_smtp,";
      $q_string .= "int_cfg2html,int_notify,int_hours ";
      $q_string .= "from interface ";
      $q_string .= "where int_id = " . $formVars['id'];
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_interface = mysql_fetch_array($q_interface);
      mysql_free_result($q_interface);

      $inttype       = return_Index($a_interface['int_type'],       "select itp_id from inttype order by itp_id");
      $intzone       = return_Index($a_interface['int_zone'],       "select zone_id from ip_zones order by zone_name");
      $intmedia      = return_Index($a_interface['int_media'],      "select med_id from int_media order by med_text");
      $intspeed      = return_Index($a_interface['int_speed'],      "select spd_id from int_speed order by spd_text");
      $intduplex     = return_Index($a_interface['int_duplex'],     "select dup_id from int_duplex order by dup_text");
      $introle       = return_Index($a_interface['int_role'],       "select rol_id from int_role order by rol_text");
      $intintid      = return_Index($a_interface['int_int_id'],     "select int_id from interface where int_companyid = " . $a_interface['int_companyid'] . " and int_redundancy > 0 order by int_face");
      $intredundancy = return_Index($a_interface['int_redundancy'], "select red_id from int_redundancy order by red_text");


      print "document.edit.int_server.value = '"    . mysql_real_escape_string($a_interface['int_server'])    . "';\n";
      print "document.edit.int_face.value = '"      . mysql_real_escape_string($a_interface['int_face'])      . "';\n";
      print "document.edit.int_int_id.value = '"    . mysql_real_escape_string($a_interface['int_int_id'])    . "';\n";
      print "document.edit.int_addr.value = '"      . mysql_real_escape_string($a_interface['int_addr'])      . "';\n";
      print "document.edit.int_eth.value = '"       . mysql_real_escape_string($a_interface['int_eth'])       . "';\n";
      print "document.edit.int_mask.value = '"      . mysql_real_escape_string($a_interface['int_mask'])      . "';\n";
      print "document.edit.int_gate.value = '"      . mysql_real_escape_string($a_interface['int_gate'])      . "';\n";
      print "document.edit.int_note.value = '"      . mysql_real_escape_string($a_interface['int_note'])      . "';\n";
      print "document.edit.int_switch.value = '"    . mysql_real_escape_string($a_interface['int_switch'])    . "';\n";
      print "document.edit.int_port.value = '"      . mysql_real_escape_string($a_interface['int_port'])      . "';\n";
      print "document.edit.int_sysport.value = ' "  . mysql_real_escape_string($a_interface['int_sysport'])   . "';\n";
      print "document.edit.int_vlan.value = '"      . mysql_real_escape_string($a_interface['int_vlan'])      . "';\n";
      print "document.edit.int_groupname.value = '" . mysql_real_escape_string($a_interface['int_groupname']) . "';\n";
      print "document.edit.int_xpoint.value = '"    . mysql_real_escape_string($a_interface['int_xpoint'])    . "';\n";
      print "document.edit.int_ypoint.value = '"    . mysql_real_escape_string($a_interface['int_ypoint'])    . "';\n";
      print "document.edit.int_zpoint.value = '"    . mysql_real_escape_string($a_interface['int_zpoint'])    . "';\n";

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
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
