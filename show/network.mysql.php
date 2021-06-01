<?php
# Script: network.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "network.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

# if help has not been seen yet,
  if (show_Help($db, 'shownetwork')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

  $q_string  = "select inv_manager ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#network\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Interface Information";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('network-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"network-help\" style=\"" . $display . "\">";
  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Interface Name</strong> - The name assigned to individual interfaces. Due to routing and management, access to a system needs to be through an interface identified as maintenance.</li>\n";
  $output .= "  <li><strong>Logical Interface</strong> - The operating system designation for an interface.</li>\n";
  if (return_Virtual($db, $formVars['id']) == 0) {
    $output .= "  <li><strong>Physical Port</strong> - The port where the cable plugs into the computer.</li>\n";
  }
  $output .= "  <li><strong>MAC Address</strong> - The MAC address for this interface.</li>\n";
  $output .= "  <li><strong>IP Address/Netmask</strong> - The assigned IP address and netmask.</li>\n";
  $output .= "  <li><strong>Zone</strong> - The network zone this server sits in.</li>\n";
  $output .= "  <li><strong>Gateway</strong> - The gateway assigned for use by this interface.</li>\n";
  if (return_Virtual($db, $formVars['id']) == 0) {
    $output .= "  <li><strong>Switch</strong> - What switch is being used to accept traffic from this computer.</li>\n";
    $output .= "  <li><strong>Port</strong> - What port has been assigned on the switch (where the cable plugs in that comes from the server).</li>\n";
  }
  $output .= "  <li><strong>Type</strong> - The type of interface.</li>\n";
  $output .= "  <li><strong>Updated</strong> - The last time this interface was updated.</li>\n";
  $output .= "</ul>\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>&gt;</strong> The interfaces that are children of a virtual interface. Two (&gt;&gt;) indicate a grandchild interface. These are typically grouped to form a redundant network connection.</li>\n";
  $output .= "  <li><strong>(M)</strong> The (M) indicates this is interface is a default Management interface.</li>\n";
  $output .= "  <li><strong>(B)</strong> The (B) indicates this is interface is used for backups.</li>\n";
  $output .= "  <li><strong>(sh)</strong> The (sh) indicates this is interface is used by Secure Shell when logging in to a server.</li>\n";
  $output .= "  <li><strong>(O)</strong> The (O) indicates this is interface is monitored through OpenView.</li>\n";
  $output .= "  <li><strong>(N)</strong> The (N) indicates this is interface is monitored through <a href=\"" . $Nagiosurl . "\" target=\"_blank\">Nagios</a>.</li>\n";
  $output .= "  <li><strong>(r)</strong> The (r) designates an interface is part of a redundancy. A group of interfaces intended on ensuring uptime.</li>\n";
  $output .= "  <li><strong>(v)</strong> The (v) indicates this is a virtual interface with no physical componant.</li>\n";
  $output .= "  <li><strong>&#x2713;</strong> There will be a checkmark if the information in this column was gathered automatically.</li>\n";
  $output .= "  <li><strong>Highlight</strong> An interface that <span class=\"ui-state-highlight\">is highlighted</span> indicates that this interface is the default route for network traffic.</li>\n";
  $output .= "</ul>\n";

  $output .= "</div>";
  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Interface Name</th>";
  $output .= "<th class=\"ui-state-default\">Logical Interface</th>";
  if (return_Virtual($db, $formVars['id']) == 0) {
    $output .= "<th class=\"ui-state-default\">Physical Port</th>";
  }
  $output .= "<th class=\"ui-state-default\">MAC Address</th>";
  $output .= "<th class=\"ui-state-default\">IPv4 Address/Netmask</th>";
  $output .= "<th class=\"ui-state-default\">Zone</th>";
  $output .= "<th class=\"ui-state-default\">Gateway</th>";
  if (return_Virtual($db, $formVars['id']) == 0) {
    $output .= "<th class=\"ui-state-default\">Switch</th>";
    $output .= "<th class=\"ui-state-default\">Port</th>";
  }
  $output .= "<th class=\"ui-state-default\">Type</th>";
  $output .= "<th class=\"ui-state-default\">Updated</th>";
  $output .= "</tr>";

  $q_string  = "select int_id,int_server,int_domain,int_face,int_addr,int_vaddr,int_eth,int_veth,int_mask,int_verified,";
  $q_string .= "int_sysport,int_redundancy,int_virtual,int_switch,int_port,int_primary,itp_acronym,int_gate,";
  $q_string .= "int_vgate,int_note,int_update,int_type,zone_name,int_nagios,int_openview,int_backup,int_management,int_login ";
  $q_string .= "from interface ";
  $q_string .= "left join net_zones on interface.int_zone = net_zones.zone_id  ";
  $q_string .= "left join inttype  on interface.int_type = inttype.itp_id ";
  $q_string .= "where int_companyid = " . $formVars['id'] . " and int_int_id = 0 and int_ip6 = 0 ";
  $q_string .= "order by int_face,int_addr";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  while ( $a_interface = mysqli_fetch_array($q_interface) ) {

    $intnote = " title=\"" . $a_interface['int_note'] . "\"";
    $checkmark = "";
    if ($a_interface['int_verified']) {
      $checkmark = "&#x2713;";
    }
    $gatecheckmark = "";
    if ($a_interface['int_vgate']) {
      $gatecheckmark = "&#x2713;";
    }
    $addrcheckmark = "";
    if ($a_interface['int_vaddr']) {
      $addrcheckmark = "&#x2713;";
    }
    $ethcheckmark = "";
    if ($a_interface['int_veth']) {
      $ethcheckmark = "&#x2713;";
    }
    $servername = $a_interface['int_server'];
    if ($a_interface['int_domain'] != '') {
      $servername = $a_interface['int_server'] . "." . $a_interface['int_domain'];
    }
    $pristart = " class=\"ui-widget-content\"";
    if ($a_interface['int_primary'] == 1) {
      $pristart = " class=\"ui-state-highlight\"";
    }
    if ($a_interface['int_eth'] == '00:00:00:00:00:00' ) {
      $showmac = '';
    } else {
      $showmac = $a_interface['int_eth'];
    }
    if ($a_interface['int_addr'] == '' ) {
      $showmask = '';
    } else {
      $showmask = '/' . $a_interface['int_mask'];
    }
    $redundancy = '';
    if ($a_interface['int_redundancy'] > 0 ) {
      $redundancy = ' (r)';
    }
    $virtual = '';
    if ($a_interface['int_virtual'] == 1 ) {
      $virtual = ' (v)';
    }
    $management = '';
    if ($a_interface['int_management'] == 1 ) {
      $management = ' (M)';
    }
    $backup = '';
    if ($a_interface['int_backup'] == 1 ) {
      $backup = ' (B)';
    }
    $login = '';
    if ($a_interface['int_login'] == 1 ) {
      $login = ' (sh)';
    }

    if ($a_interface['int_type'] == 4 || $a_interface['int_type'] == 6) {
      $linkstart = "<a href=\"http://" . $a_interface['int_addr'] . "\" target=\"_blank\">";
      $linkend = "</a>";
    } else {
      $linkstart = "";
      $linkend = "";
    }

    $monitor = '';
    if ($a_interface['int_nagios'] || $a_interface['int_openview']) {
      $monitor = ' (';
      if ($a_interface['int_nagios']) {
        $monitor .= "N";
      }
      if ($a_interface['int_openview']) {
        $monitor .= "O";
      }
      $monitor .= ")";
    }

    $output .= "<tr>";
    $output .= "<td" . $pristart . $intnote . ">"              . $servername . $redundancy . $monitor . $management . $backup . $login . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['int_face'] . $virtual                 . "</td>";
    if (return_Virtual($db, $formVars['id']) == 0) {
      $output .= "<td" . $pristart . $intnote . ">"            . $a_interface['int_sysport']                         . "</td>";
    }
    $output .= "<td" . $pristart . $intnote . ">"              . $showmac . $ethcheckmark                            . "</td>";
    $output .= "<td" . $pristart . $intnote . ">" . $linkstart . $a_interface['int_addr']     . $showmask . $addrcheckmark . $linkend . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['zone_name']                           . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['int_gate'] . $gatecheckmark           . "</td>";
    if (return_Virtual($db, $formVars['id']) == 0) {
      $output .= "<td" . $pristart . $intnote . ">"            . $a_interface['int_switch']                          . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"            . $a_interface['int_port']                            . "</td>";
    }
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['itp_acronym']                         . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['int_update']  . $checkmark            . "</td>";
    $output .= "</tr>";


# redundant interfaces
    $q_string  = "select int_id,int_server,int_domain,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,";
    $q_string .= "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_name,int_groupname,";
    $q_string .= "int_vaddr,int_veth,int_vgate,int_nagios,int_openview,int_management,int_backup,int_login ";
    $q_string .= "from interface ";
    $q_string .= "left join net_zones on interface.int_zone = net_zones.zone_id  ";
    $q_string .= "left join inttype on interface.int_type = inttype.itp_id ";
    $q_string .= "where int_companyid = " . $formVars['id'] . " and int_int_id = " . $a_interface['int_id'] . " and int_ip6 = 0 ";
    $q_string .= "order by int_face,int_addr";
    $q_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    while ( $a_redundancy = mysqli_fetch_array($q_redundancy) ) {

      $intnote = " title=\"" . $a_redundancy['int_note'] . "\"";
      $checkmark = "";
      if ($a_redundancy['int_verified']) {
        $checkmark = "&#x2713;";
      }
      $addrcheckmark = "";
      if ($a_redundancy['int_vaddr']) {
        $addrcheckmark = "&#x2713;";
      }
      $ethcheckmark = "";
      if ($a_redundancy['int_veth']) {
        $ethcheckmark = "&#x2713;";
      }
      $gatecheckmark = "";
      if ($a_redundancy['int_vgate']) {
        $gatecheckmark = "&#x2713;";
      }
      $servername = $a_redundancy['int_server'];
      if ($a_redundancy['int_domain'] != '') {
        $servername = $a_redundancy['int_server'] . "." . $a_redundancy['int_domain'];
      }
      $pristart = " class=\"ui-widget-content\"";
      if ($a_redundancy['int_primary'] == 1) {
        $pristart = " class=\"ui-state-highlight\"";
      }
      if ($a_redundancy['int_eth'] == '00:00:00:00:00:00' ) {
        $showmac = '';
      } else {
        $showmac = $a_redundancy['int_eth'];
      }
      if ($a_redundancy['int_addr'] == '' ) {
        $showmask = '';
      } else {
        $showmask = '/' . $a_redundancy['int_mask'];
      }
      $group = '';
      if ($a_redundancy['int_groupname'] != '') {
        $group = ' (' . $a_redundancy['int_groupname'] . ')';
      }
      $virtual = '';
      if ($a_redundancy['int_virtual'] == 1 ) {
        $virtual = ' (v)';
      }
      $management = '';
      if ($a_redundancy['int_management'] == 1 ) {
        $management = ' (M)';
      }
      $backup = '';
      if ($a_redundancy['int_backup'] == 1 ) {
        $backup = ' (B)';
      }
      $login = '';
      if ($a_redundancy['int_login'] == 1 ) {
        $login = ' (sh)';
      }

      if ($a_redundancy['int_type'] == 4 || $a_redundancy['int_type'] == 6) {
        $linkstart = "<a href=\"http://" . $a_redundancy['int_addr'] . "\" target=\"_blank\">";
        $linkend = "</a>";
      } else {
        $linkstart = "";
        $linkend = "";
      }

      $monitor = '';
      if ($a_redundancy['int_nagios'] || $a_redundancy['int_openview']) {
        $monitor = ' (';
        if ($a_redundancy['int_nagios']) {
          $monitor .= "N";
        }
        if ($a_redundancy['int_openview']) {
          $monitor .= "O";
        }
        $monitor .= ")";
      }

      $output .= "<tr>";
      $output .= "<td" . $pristart . $intnote . ">> "            . $servername . $group . $monitor . $management . $backup . $login . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['int_face'] . $virtual                 . "</td>";
      if (return_Virtual($db, $formVars['id']) == 0) {
        $output .= "<td" . $pristart . $intnote . ">"            . $a_redundancy['int_sysport']                         . "</td>";
      }
      $output .= "<td" . $pristart . $intnote . ">"              . $showmac . $ethcheckmark                            . "</td>";
      $output .= "<td" . $pristart . $intnote . ">" . $linkstart . $a_redundancy['int_addr']     . $showmask . $addrcheckmark . $linkend . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['zone_name']                           . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['int_gate'] . $gatecheckmark           . "</td>";
      if (return_Virtual($db, $formVars['id']) == 0) {
        $output .= "<td" . $pristart . $intnote . ">"            . $a_redundancy['int_switch']                          . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"            . $a_redundancy['int_port']                            . "</td>";
      }
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['itp_acronym']                         . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['int_update']  . $checkmark            . "</td>";
      $output .= "</tr>";


# secondary redundant interfaces
      $q_string  = "select int_id,int_server,int_domain,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,";
      $q_string .= "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_name,int_groupname,";
      $q_string .= "int_vaddr,int_veth,int_vgate,int_nagios,int_openview,int_management,int_backup,int_login ";
      $q_string .= "from interface ";
      $q_string .= "left join net_zones on interface.int_zone = net_zones.zone_id  ";
      $q_string .= "left join inttype on interface.int_type = inttype.itp_id ";
      $q_string .= "where int_companyid = " . $formVars['id'] . " and int_int_id = " . $a_redundancy['int_id'] . " and int_ip6 = 0 ";
      $q_string .= "order by int_face,int_addr";
      $q_secondary = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      while ( $a_secondary = mysqli_fetch_array($q_secondary) ) {

        $intnote = " title=\"" . $a_secondary['int_note'] . "\"";
        $checkmark = "";
        if ($a_secondary['int_verified']) {
          $checkmark = "&#x2713;";
        }
        $addrcheckmark = "";
        if ($a_secondary['int_vaddr']) {
          $addrcheckmark = "&#x2713;";
        }
        $ethcheckmark = "";
        if ($a_secondary['int_veth']) {
          $ethcheckmark = "&#x2713;";
        }
        $gatecheckmark = "";
        if ($a_secondary['int_vgate']) {
          $gatecheckmark = "&#x2713;";
        }
        $servername = $a_secondary['int_server'];
        if ($a_secondary['int_domain'] != '') {
          $servername = $a_secondary['int_server'] . "." . $a_secondary['int_domain'];
        }
        $pristart = " class=\"ui-widget-content\"";
        if ($a_secondary['int_primary'] == 1) {
          $pristart = " class=\"ui-state-highlight\"";
        }
        if ($a_secondary['int_eth'] == '00:00:00:00:00:00' ) {
          $showmac = '';
        } else {
          $showmac = $a_secondary['int_eth'];
        }
        if ($a_secondary['int_addr'] == '' ) {
          $showmask = '';
        } else {
          $showmask = '/' . $a_secondary['int_mask'];
        }
        $group = '';
        if ($a_secondary['int_groupname'] != '') {
          $group = ' (' . $a_secondary['int_groupname'] . ')';
        }
        $virtual = '';
        if ($a_secondary['int_virtual'] == 1 ) {
          $virtual = ' (v)';
        }
        $management = '';
        if ($a_secondary['int_management'] == 1 ) {
          $management = ' (M)';
        }
        $backup = '';
        if ($a_secondary['int_backup'] == 1 ) {
          $backup = ' (B)';
        }
        $login = '';
        if ($a_secondary['int_login'] == 1 ) {
          $login = ' (sh)';
        }

        if ($a_secondary['int_type'] == 4 || $a_secondary['int_type'] == 6) {
          $linkstart = "<a href=\"http://" . $a_secondary['int_addr'] . "\" target=\"_blank\">";
          $linkend = "</a>";
        } else {
          $linkstart = "";
          $linkend = "";
        }

        $monitor = '';
        if ($a_secondary['int_nagios'] || $a_secondary['int_openview']) {
          $monitor = ' (';
          if ($a_secondary['int_nagios']) {
            $monitor .= "N";
          }
          if ($a_secondary['int_openview']) {
            $monitor .= "O";
          }
          $monitor .= ")";
        }

        $output .= "<tr>";
        $output .= "<td" . $pristart . $intnote . ">>> "           . $servername . $group . $monitor . $management . $backup . $login . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['int_face'] . $virtual                 . "</td>";
        if (return_Virtual($db, $formVars['id']) == 0) {
          $output .= "<td" . $pristart . $intnote . ">"            . $a_secondary['int_sysport']                         . "</td>";
        }
        $output .= "<td" . $pristart . $intnote . ">"              . $showmac . $ethcheckmark                            . "</td>";
        $output .= "<td" . $pristart . $intnote . ">" . $linkstart . $a_secondary['int_addr']     . $showmask . $addrcheckmark . $linkend . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['zone_name']                           . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['int_gate'] . $gatecheckmark           . "</td>";
        if (return_Virtual($db, $formVars['id']) == 0) {
          $output .= "<td" . $pristart . $intnote . ">"            . $a_secondary['int_switch']                          . "</td>";
          $output .= "<td" . $pristart . $intnote . ">"            . $a_secondary['int_port']                            . "</td>";
        }
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['itp_acronym']                         . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['int_update']  . $checkmark            . "</td>";
        $output .= "</tr>";
      }
    }
  }
  $output .= "</table>";

  print "document.getElementById('network_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";


# IP6 table

  $ipv6 = 0;

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Interface Name</th>";
  $output .= "<th class=\"ui-state-default\">Logical Interface</th>";
  if (return_Virtual($db, $formVars['id']) == 0) {
    $output .= "<th class=\"ui-state-default\">Physical Port</th>";
  }
  $output .= "<th class=\"ui-state-default\">MAC Address</th>";
  $output .= "<th class=\"ui-state-default\">IPv6 Address/Netmask</th>";
  $output .= "<th class=\"ui-state-default\">Zone</th>";
  $output .= "<th class=\"ui-state-default\">Gateway</th>";
  if (return_Virtual($db, $formVars['id']) == 0) {
    $output .= "<th class=\"ui-state-default\">Switch</th>";
    $output .= "<th class=\"ui-state-default\">Port</th>";
  }
  $output .= "<th class=\"ui-state-default\">Type</th>";
  $output .= "<th class=\"ui-state-default\">Updated</th>";
  $output .= "</tr>";

  $q_string = "select int_id,int_server,int_domain,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,"
            .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_name,int_veth,int_vaddr,int_vgate "
            . "from interface "
            . "left join net_zones on interface.int_zone = net_zones.zone_id  "
            . "left join inttype on interface.int_type = inttype.itp_id "
            . "where int_companyid = " . $formVars['id'] . " and int_int_id = 0 and int_ip6 = 1 "
            . "order by int_face,int_addr";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  while ( $a_interface = mysqli_fetch_array($q_interface) ) {

    $ipv6 = 1;

    $intnote = " title=\"" . $a_interface['int_note'] . "\"";
    $checkmark = "";
    if ($a_interface['int_verified']) {
      $checkmark = "&#x2713;";
    }
    $addrcheckmark = "";
    if ($a_interface['int_vaddr']) {
      $addrcheckmark = "&#x2713;";
    }
    $ethcheckmark = "";
    if ($a_interface['int_veth']) {
      $ethcheckmark = "&#x2713;";
    }
    $gatecheckmark = "";
    if ($a_interface['int_vgate']) {
      $gatecheckmark = "&#x2713;";
    }
    $pristart = " class=\"ui-widget-content\"";
    if ($a_interface['int_primary'] == 1) {
      $pristart = " class=\"ui-state-highlight\"";
    }
    if ($a_interface['int_eth'] == '00:00:00:00:00:00' ) {
      $showmac = '';
    } else {
      $showmac = $a_interface['int_eth'];
    }
    if ($a_interface['int_addr'] == '' ) {
      $showmask = '';
    } else {
      $showmask = '/' . $a_interface['int_mask'];
    }
    $redundancy = '';
    if ($a_interface['int_redundancy'] > 0 ) {
      $redundancy = ' (r)';
    }
    $virtual = '';
    if ($a_interface['int_virtual'] == 1 ) {
      $virtual = ' (v)';
    }

    if ($a_interface['int_type'] == 4 || $a_interface['int_type'] == 6) {
      $linkstart = "<a href=\"http://" . $a_interface['int_addr'] . "\" target=\"_blank\">";
      $linkend = "</a>";
    } else {
      $linkstart = "";
      $linkend = "";
    }

    $output .= "<tr>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['int_server'] . $redundancy            . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['int_face'] . $virtual                 . "</td>";
    if (return_Virtual($db, $formVars['id']) == 0) {
      $output .= "<td" . $pristart . $intnote . ">"            . $a_interface['int_sysport']                         . "</td>";
    }
    $output .= "<td" . $pristart . $intnote . ">"              . $showmac . $ethcheckmark                            . "</td>";
    $output .= "<td" . $pristart . $intnote . ">" . $linkstart . $a_interface['int_addr']     . $showmask . $addrcheckmark . $linkend . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['zone_name']                           . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['int_gate'] . $gatecheckmark           . "</td>";
    if (return_Virtual($db, $formVars['id']) == 0) {
      $output .= "<td" . $pristart . $intnote . ">"            . $a_interface['int_switch']                          . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"            . $a_interface['int_port']                            . "</td>";
    }
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['itp_acronym']                         . "</td>";
    $output .= "<td" . $pristart . $intnote . ">"              . $a_interface['int_update']  . $checkmark            . "</td>";
    $output .= "</tr>";


# redundant interfaces
    $q_string = "select int_id,int_server,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,"
              .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_name,int_groupname,int_vaddr,int_veth,int_vgate "
              . "from interface "
              . "left join net_zones on interface.int_zone = net_zones.zone_id  "
              . "left join inttype on interface.int_type = inttype.itp_id "
              . "where int_companyid = " . $formVars['id'] . " and int_int_id = " . $a_interface['int_id'] . " and int_ip6 = 1 "
              . "order by int_face,int_addr";
    $q_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    while ( $a_redundancy = mysqli_fetch_array($q_redundancy) ) {

      $intnote = " title=\"" . $a_redundancy['int_note'] . "\"";
      $checkmark = "";
      if ($a_redundancy['int_verified']) {
        $checkmark = "&#x2713;";
      }
      $addrcheckmark = "";
      if ($a_redundancy['int_vaddr']) {
        $addrcheckmark = "&#x2713;";
      }
      $ethcheckmark = "";
      if ($a_redundancy['int_veth']) {
        $ethcheckmark = "&#x2713;";
      }
      $gatecheckmark = "";
      if ($a_redundancy['int_vgate']) {
        $gatecheckmark = "&#x2713;";
      }
      $pristart = " class=\"ui-widget-content\"";
      if ($a_redundancy['int_primary'] == 1) {
        $pristart = " class=\"ui-state-highlight\"";
      }
      if ($a_redundancy['int_eth'] == '00:00:00:00:00:00' ) {
        $showmac = '';
      } else {
        $showmac = $a_redundancy['int_eth'];
      }
      if ($a_redundancy['int_addr'] == '' ) {
        $showmask = '';
      } else {
        $showmask = '/' . $a_redundancy['int_mask'];
      }
      $group = '';
      if ($a_redundancy['int_groupname'] != '') {
        $group = ' (' . $a_redundancy['int_groupname'] . ')';
      }
      $virtual = '';
      if ($a_redundancy['int_virtual'] == 1 ) {
        $virtual = ' (v)';
      }

      if ($a_redundancy['int_type'] == 4 || $a_redundancy['int_type'] == 6) {
        $linkstart = "<a href=\"http://" . $a_redundancy['int_addr'] . "\" target=\"_blank\">";
        $linkend = "</a>";
      } else {
        $linkstart = "";
        $linkend = "";
      }

      $output .= "<tr>";
      $output .= "<td" . $pristart . $intnote . ">> "            . $a_redundancy['int_server'] . $group                 . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['int_face'] . $virtual                 . "</td>";
      if (return_Virtual($db, $formVars['id']) == 0) {
        $output .= "<td" . $pristart . $intnote . ">"            . $a_redundancy['int_sysport']                         . "</td>";
      }
      $output .= "<td" . $pristart . $intnote . ">"              . $showmac . $ethcheckmark                            . "</td>";
      $output .= "<td" . $pristart . $intnote . ">" . $linkstart . $a_redundancy['int_addr']     . $showmask . $addrcheckmark . $linkend . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['zone_name']                           . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['int_gate'] . $gatecheckmark           . "</td>";
      if (return_Virtual($db, $formVars['id']) == 0) {
        $output .= "<td" . $pristart . $intnote . ">"            . $a_redundancy['int_switch']                          . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"            . $a_redundancy['int_port']                            . "</td>";
      }
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['itp_acronym']                         . "</td>";
      $output .= "<td" . $pristart . $intnote . ">"              . $a_redundancy['int_update']  . $checkmark            . "</td>";
      $output .= "</tr>";

# secondary redundant interfaces
      $q_string = "select int_id,int_server,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,"
                .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_name,int_groupname,int_vaddr,int_veth,int_vgate "
                . "from interface "
                . "left join net_zones on interface.int_zone = net_zones.zone_id  "
                . "left join inttype on interface.int_type = inttype.itp_id "
                . "where int_companyid = " . $formVars['id'] . " and int_int_id = " . $a_redundancy['int_id'] . " and int_ip6 = 1 "
                . "order by int_face,int_addr";
      $q_secondary = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      while ( $a_secondary = mysqli_fetch_array($q_secondary) ) {

        $intnote = " title=\"" . $a_secondary['int_note'] . "\"";
        $checkmark = "";
        if ($a_secondary['int_verified']) {
          $checkmark = "&#x2713;";
        }
        $addrcheckmark = "";
        if ($a_secondary['int_vaddr']) {
          $addrcheckmark = "&#x2713;";
        }
        $ethcheckmark = "";
        if ($a_secondary['int_veth']) {
          $ethcheckmark = "&#x2713;";
        }
        $gatecheckmark = "";
        if ($a_secondary['int_vgate']) {
          $gatecheckmark = "&#x2713;";
        }
        $pristart = " class=\"ui-widget-content\"";
        if ($a_secondary['int_primary'] == 1) {
          $pristart = " class=\"ui-state-highlight\"";
        }
        if ($a_secondary['int_eth'] == '00:00:00:00:00:00' ) {
          $showmac = '';
        } else {
          $showmac = $a_secondary['int_eth'];
        }
        if ($a_secondary['int_addr'] == '' ) {
          $showmask = '';
        } else {
          $showmask = '/' . $a_secondary['int_mask'];
        }
        $group = '';
        if ($a_secondary['int_groupname'] != '') {
          $group = ' (' . $a_secondary['int_groupname'] . ')';
        }
        $virtual = '';
        if ($a_secondary['int_virtual'] == 1 ) {
          $virtual = ' (v)';
        }

        if ($a_secondary['int_type'] == 4 || $a_secondary['int_type'] == 6) {
          $linkstart = "<a href=\"http://" . $a_secondary['int_addr'] . "\" target=\"_blank\">";
          $linkend = "</a>";
        } else {
          $linkstart = "";
          $linkend = "";
        }

        $output .= "<tr>";
        $output .= "<td" . $pristart . $intnote . ">>> "           . $a_secondary['int_server'] . $group                 . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['int_face'] . $virtual                 . "</td>";
        if (return_Virtual($db, $formVars['id']) == 0) {
          $output .= "<td" . $pristart . $intnote . ">"            . $a_secondary['int_sysport']                         . "</td>";
        }
        $output .= "<td" . $pristart . $intnote . ">"              . $showmac . $ethcheckmark                            . "</td>";
        $output .= "<td" . $pristart . $intnote . ">" . $linkstart . $a_secondary['int_addr']     . $showmask . $addrcheckmark . $linkend . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['zone_name']                           . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['int_gate'] . $gatecheckmark           . "</td>";
        if (return_Virtual($db, $formVars['id']) == 0) {
          $output .= "<td" . $pristart . $intnote . ">"            . $a_secondary['int_switch']                          . "</td>";
          $output .= "<td" . $pristart . $intnote . ">"            . $a_secondary['int_port']                            . "</td>";
        }
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['itp_acronym']                         . "</td>";
        $output .= "<td" . $pristart . $intnote . ">"              . $a_secondary['int_update']  . $checkmark            . "</td>";
        $output .= "</tr>";
      }
    }
  }
  $output .= "</table>";

# only throw a table if ipv6 exists
  if ($ipv6) {
    print "document.getElementById('ipv6network_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";
  } else {
    print "document.getElementById('ipv6network_mysql').innerHTML = '';\n";
  }

?>
