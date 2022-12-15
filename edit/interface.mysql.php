<?php
# Script: interface.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars['int_companyid']  = clean($_GET['int_companyid'],  10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['int_companyid'] == '') {
      $formVars['int_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['int_id']             = clean($_GET['int_id'],             10);
        $formVars['int_ipaddressid']    = clean($_GET['int_ipaddressid'],    10);
        $formVars['int_face']           = clean($_GET['int_face'],           20);
        $formVars['int_int_id']         = clean($_GET['int_int_id'],         10);
        $formVars['int_virtual']        = clean($_GET['int_virtual'],        10);
        $formVars['int_eth']            = clean($_GET['int_eth'],            20);
        $formVars['int_note']           = clean($_GET['int_note'],          255);
        $formVars['int_switch']         = clean($_GET['int_switch'],         50);
        $formVars['int_port']           = clean($_GET['int_port'],           50);
        $formVars['int_sysport']        = clean($_GET['int_sysport'],        50);
        $formVars['int_primary']        = clean($_GET['int_primary'],        10);
        $formVars['int_type']           = clean($_GET['int_type'],           10);
        $formVars['int_media']          = clean($_GET['int_media'],          10);
        $formVars['int_speed']          = clean($_GET['int_speed'],          10);
        $formVars['int_duplex']         = clean($_GET['int_duplex'],         10);
        $formVars['int_redundancy']     = clean($_GET['int_redundancy'],     10);
        $formVars['int_groupname']      = clean($_GET['int_groupname'],      20);
        $formVars['int_backup']         = clean($_GET['int_backup'],         10);
        $formVars['int_management']     = clean($_GET['int_management'],     10);
        $formVars['int_login']          = clean($_GET['int_login'],          10);

        if ($formVars['int_id'] == '') {
          $formVars['int_id'] = 0;
        }
        if ($formVars['int_ipaddressid'] == '') {
          $formVars['int_ipaddressid'] = 0;
        }
        if ($formVars['int_duplex'] == '') {
          $formVars['int_duplex'] = 0;
        }
        if ($formVars['int_speed'] == '') {
          $formVars['int_speed'] = 0;
        }
        if ($formVars['int_media'] == '') {
          $formVars['int_media'] = 0;
        }
        if ($formVars['int_int_id'] == '') {
          $formVars['int_int_id'] = 0;
        }
        if ($formVars['int_redundancy'] == '') {
          $formVars['int_redundancy'] = 0;
        }
        if ($formVars['int_type'] == '') {
          $formVars['int_type'] = 0;
        }
        if ($formVars['int_eth'] == '') {
          $formVars['int_eth'] = "00:00:00:00:00:00";
        }
        if ($formVars['int_primary'] == 'true') {
          $formVars['int_primary'] = 1;
        } else {
          $formVars['int_primary'] = 0;
        }
        if ($formVars['int_backup'] == 'true') {
          $formVars['int_backup'] = 1;
        } else {
          $formVars['int_backup'] = 0;
        }
        if ($formVars['int_management'] == 'true') {
          $formVars['int_management'] = 1;
        } else {
          $formVars['int_management'] = 0;
        }
        if ($formVars['int_login'] == 'true') {
          $formVars['int_login'] = 1;
        } else {
          $formVars['int_login'] = 0;
        }
        if ($formVars['int_virtual'] == 'true') {
          $formVars['int_virtual'] = 1;
        } else {
          $formVars['int_virtual'] = 0;
        }

        if ($formVars['int_companyid'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# for now repopulate the info from the ipaddress table.
          $q_string  = "select ip_ipv4,ip_hostname,ip_domain,ip_network ";
          $q_string .= "from inv_ipaddress ";
          $q_string .= "where ip_id = " . $formVars['int_ipaddressid'] . " ";
          $q_inv_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_ipaddress) > 0) {
            $a_inv_ipaddress = mysqli_fetch_array($q_inv_ipaddress);

# now get the netmask and netvlan
            $q_string  = "select net_mask,net_vlan ";
            $q_string .= "from network ";
            $q_string .= "where net_id = " . $a_inv_ipaddress['ip_network'] . " ";
            $q_network = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_network) > 0) {
              $a_network = mysqli_fetch_array($q_network);
            } else {
              $a_network['net_mask'] = 0;
              $a_network['net_vlan'] = "";
            }

            $q_string  = "select ip_ipv4 ";
            $q_string .= "from inv_ipaddress ";
            $q_string .= "left join inv_ip_types on inv_ip_types.ip_id = inv_ipaddress.ip_type ";
            $q_string .= "where ip_network = " . $a_inv_ipaddress['ip_network'] . " and ip_name = \"Gateway\" ";
            $q_string .= "limit 1 ";
            $q_addr = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_addr) > 0) {
              $a_addr = mysqli_fetch_array($q_addr);
            } else {
              $a_addr['ip_ipv4'] = '';
            }

          } else {
            $a_inv_ipaddress['ip_ipv4'] = '';
            $a_inv_ipaddress['ip_hostname'] = '';
            $a_inv_ipaddress['ip_domain'] = '';
            $a_inv_ipaddress['ip_network'] = 0;
          }

# want to get vlan, gateway, subnetmask, etc at least until the info is fully rewritten.
# something like get the network from above, then get the vlan from there and the gateway from the listing of IPs associated with the network.

          $q_string = 
            "int_server       = \"" . $a_inv_ipaddress['ip_hostname']   . "\"," .
            "int_domain       = \"" . $a_inv_ipaddress['ip_domain']     . "\"," .
            "int_addr         = \"" . $a_inv_ipaddress['ip_ipv4']       . "\"," .

            "int_mask         =   " . $a_network['net_mask']        . "," .
            "int_vlan         = \"" . $a_network['net_vlan']        . "\"," .

            "int_gate         = \"" . $a_addr['ip_ipv4']            . "\"," .

            "int_companyid    =   " . $formVars['int_companyid']    . "," .
            "int_ipaddressid  =   " . $formVars['int_ipaddressid']  . "," .
            "int_face         = \"" . $formVars['int_face']         . "\"," .
            "int_int_id       =   " . $formVars['int_int_id']       . "," .
            "int_virtual      =   " . $formVars['int_virtual']      . "," .
            "int_vaddr        =   " . "0"                           . "," .
            "int_eth          = \"" . $formVars['int_eth']          . "\"," .
            "int_veth         =   " . "0"                           . "," .
            "int_vgate        =   " . "0"                           . "," .
            "int_note         = \"" . $formVars['int_note']         . "\"," .
            "int_verified     =   " . "0"                           . "," . 
            "int_switch       = \"" . $formVars['int_switch']       . "\"," . 
            "int_port         = \"" . $formVars['int_port']         . "\"," . 
            "int_sysport      = \"" . $formVars['int_sysport']      . "\"," . 
            "int_primary      =   " . $formVars['int_primary']      . "," .
            "int_type         =   " . $formVars['int_type']         . "," . 
            "int_media        =   " . $formVars['int_media']        . "," . 
            "int_speed        =   " . $formVars['int_speed']        . "," . 
            "int_duplex       =   " . $formVars['int_duplex']       . "," . 
            "int_redundancy   =   " . $formVars['int_redundancy']   . "," . 
            "int_groupname    = \"" . $formVars['int_groupname']    . "\"," . 
            "int_user         =   " . $_SESSION['uid']              . "," . 
            "int_update       = \"" . date('Y-m-d')                 . "\"," . 
            "int_backup       =   " . $formVars['int_backup']       . "," .
            "int_management   =   " . $formVars['int_management']   . "," .
            "int_login        =   " . $formVars['int_login'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into interface set int_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update interface set " . $q_string . " where int_id = " . $formVars['int_id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['int_id']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Interface</th>\n";
      $output .= "  <th class=\"ui-state-default\">Hostname/FQDN</th>\n";
      $output .= "  <th class=\"ui-state-default\">Fwd</th>\n";
      $output .= "  <th class=\"ui-state-default\">Rev</th>\n";
      $output .= "  <th class=\"ui-state-default\">Logical Interface</th>\n";
      if (return_Virtual($db, $formVars['int_companyid']) == 0) {
        $output .= "  <th class=\"ui-state-default\">Physical Port</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">MAC</th>\n";
      $output .= "  <th class=\"ui-state-default\">IP Address/Netmask</th>\n";
      $output .= "  <th class=\"ui-state-default\">Gateway</th>\n";
      if (return_Virtual($db, $formVars['int_companyid']) == 0) {
        $output .= "  <th class=\"ui-state-default\">Switch</th>\n";
        $output .= "  <th class=\"ui-state-default\">Port</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Type</th>\n";
      $output .= "  <th class=\"ui-state-default\">Updated</th>\n";
      $output .= "</tr>\n";

      $mgtcount = 0;
      $q_string  = "select int_id,int_server,int_domain,int_companyid,red_text,red_default,int_management,int_login,";
      $q_string .= "int_backup,int_face,int_addr,int_eth,int_mask,int_switch,int_vaddr,int_veth,int_vgate,";
      $q_string .= "int_virtual,int_port,int_sysport,int_verified,int_primary,itp_acronym,";
      $q_string .= "itp_description,int_gate,int_update,usr_name,int_nagios,int_openview,int_ip6,ip_ipv4 ";
      $q_string .= "from interface ";
      $q_string .= "left join inv_int_types on inv_int_types.itp_id = interface.int_type ";
      $q_string .= "left join inv_ipaddress on inv_ipaddress.ip_id = interface.int_ipaddressid ";
      $q_string .= "left join inv_users on inv_users.usr_id = interface.int_user ";
      $q_string .= "left join inv_int_redundancy on inv_int_redundancy.red_id = interface.int_redundancy ";
      $q_string .= "where int_companyid = " . $formVars['int_companyid'] . " and int_int_id = 0 ";
      $q_string .= "order by int_face,int_addr,int_server";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_interface) > 0) {
        while ($a_interface = mysqli_fetch_array($q_interface)) {

          $default    = " class=\"ui-widget-content\"";
          $defaultdel = " class=\"ui-widget-content delete\"";
          if ($a_interface['int_primary'] == 1) {
            $default    = " class=\"ui-state-highlight\"";
            $defaultdel = " class=\"ui-state-highlight delete\"";
          }
          $servername = $a_interface['int_server'];
          $fqdn_flag = 0;
          if ($a_interface['int_domain'] != '') {
            $servername = $a_interface['int_server'] . "." . $a_interface['int_domain'];
            $fqdn_flag = 1;
          }
          $forward = "";
          $fwdtitle = "";
          $reverse = "";
          $revtitle = "";
	  if ($a_interface['int_ip6'] == 0) {
# verify the interface has a valid IP first. No need to further check if not
            if (filter_var($a_interface['int_addr'], FILTER_VALIDATE_IP)) {
              $actualhost = gethostbyaddr($a_interface['int_addr']);
              if ($actualhost == $a_interface['int_addr'] || $actualhost != $servername) {
                if ($actualhost == $a_interface['int_addr']) {
                  $revtitle = "IP Lookup Failed: " . $actualhost . ".\nShould have returned: " . $a_interface['int_addr'] . ".";
                }
                if ($actualhost != $servername) {
                  $revtitle = "Hostname Mismatch: " . $actualhost . ".\nShould have returned: " . $servername . ".";
                }
                $reverse = "";
              } else {
# clear it once determined.
                $revtitle = "";
                $reverse = "&#x2713;";
              }
# get the IP Address from the hostname but only if the hostname isn't an IP address and skip if the IP errors out
              if ($a_interface['int_addr'] != $servername) {
                if ($fqdn_flag) {
                  $actualip = gethostbyname($servername);
                  if ($actualip == $servername || $actualip != $a_interface['int_addr']) {
                    if ($actualip == $servername) {
                      $fwdtitle = "Hostname Lookup Failed: " . $actualip . ".\nShould have returned: " . $servername . ".";
                    }
                    if ($actualip != $a_interface['int_addr']) {
                      $fwdtitle = "IP Mismatch: " . $actualip . ".\nShould have returned: " . $a_interface['int_addr'] . ".";
                    }
                    $forward = "";
                  } else {
# clear it once determined.
                    $fwdtitle = "";
                    $forward = "&#x2713;";
                  }
                } else {
                  $fwdtitle = "Hostname is not a FQDN.";
                  $forward = "";
                }
              }
            }
          }
          if ($a_interface['int_eth'] == '00:00:00:00:00:00') {
            $showmac = '';
          } else {
            $showmac = $a_interface['int_eth'];
          }
          if ($a_interface['int_addr'] == '') {
            $showmask = '';
          } else {
            $showmask = "/" . $a_interface['int_mask'];
          }
          $addrchecked = "";
          if ($a_interface['int_vaddr']) {
            $addrchecked = "&#x2713;";
          }
          $ethchecked = "";
          if ($a_interface['int_veth']) {
            $ethchecked = "&#x2713;";
          }
          $gatechecked = "";
          if ($a_interface['int_vgate']) {
            $gatechecked = "&#x2713;";
          }
          $checked = "";
          if ($a_interface['int_verified']) {
            $checked = "&#x2713;";
          }
          $redundancy = '';
# if not one of the default interfaces, then it's redundant
          if ($a_interface['red_default'] == 0) {
            $redundancy = ' (r)';
# new bridge interface
            if ($a_interface['red_text'] == "LACP") {
              $redundancy = ' (b)';
            }
          }
          $virtual = '';
          if ($a_interface['int_virtual'] > 0) {
            $virtual = ' (v)';
          }
          $login = '';
          if ($a_interface['int_login'] > 0) {
            $login = ' (sh)';
          }
          $management = '';
          if ($a_interface['int_management'] > 0) {
            $management = ' (M)';
            $mgtcount++;
          }
          $backups = '';
          if ($a_interface['int_backup'] > 0) {
            $backups = ' (B)';
          }
          $title = " title=\"Updated by: " . $a_interface['usr_name'] . "\"";

          $monitor = '';
          if ($a_interface['int_nagios'] || $a_interface['int_openview']) {
            $monitor = ' (';
            if ($a_interface['int_nagios']) {
              $monitor .= "N";
            }
            if ($a_interface['int_openview']) {
              $monitor .= "O";
            }
            $monitor .= ')';
          }

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('interface.fill.php?id=" . $a_interface['int_id'] . "');jQuery('#dialogInterfaceUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('interface.del.php?id="  . $a_interface['int_id'] . "');\">";
          $linkend = "</a>";

          $output .= "<tr>\n";
          $output .= "  <td"          . $defaultdel . ">" . $linkdel                                                                      . "</td>\n";
          $output .= "  <td"          . $default    . ">" . $linkstart . $servername   . $redundancy   . $monitor . $management . $backups . $login . $linkend   . "</td>\n";
          $output .= "  <td"          . $defaultdel . " title=\"" . $fwdtitle . "\">" . $linkstart . $forward                 . $linkend   . "</td>\n";
          $output .= "  <td"          . $defaultdel . " title=\"" . $revtitle . "\">" . $linkstart . $reverse                . $linkend   . "</td>\n";
          $output .= "  <td"          . $default    . ">" . $linkstart . $a_interface['int_face'] . $virtual                 . $linkend   . "</td>\n";
          if (return_Virtual($db, $formVars['int_companyid']) == 0) {
            $output .= "  <td"        . $default    . ">" . $linkstart . $a_interface['int_sysport']                         . $linkend   . "</td>\n";
          }
          $output .= "  <td"          . $default    . ">" . $linkstart . $showmac                 . $ethchecked              . $linkend   . "</td>\n";
          $output .= "  <td"          . $default    . ">" . $linkstart . $a_interface['int_addr'] . $showmask . $addrchecked . $linkend   . "</td>\n";
          $output .= "  <td"          . $default    . ">" . $linkstart . $a_interface['int_gate'] . $gatechecked             . $linkend   . "</td>\n";
          if (return_Virtual($db, $formVars['int_companyid']) == 0) {
            $output .= "  <td"        . $default    . ">" . $linkstart . $a_interface['int_switch']                          . $linkend   . "</td>\n";
            $output .= "  <td"        . $default    . ">" . $linkstart . $a_interface['int_port']                            . $linkend   . "</td>\n";
          }
          $output .= "  <td"          . $default    . " title=\"" . $a_interface['itp_description'] . "\">" . $linkstart . $a_interface['itp_acronym']              . $linkend   . "</td>\n";
          $output .= "  <td" . $title . $default    . ">" . $linkstart . $a_interface['int_update'] . $checked               . $linkend . "</td>\n";
          $output .= "</tr>\n";


# Display any redundancy memberships here
          $q_string  = "select int_id,int_server,int_domain,int_companyid,int_face,int_addr,";
          $q_string .= "int_eth,int_mask,int_switch,int_groupname,int_vaddr,int_veth,int_vgate,";
          $q_string .= "int_virtual,int_port,int_sysport,int_verified,int_primary,itp_acronym,";
          $q_string .= "itp_description,int_gate,int_update,usr_name,int_nagios,int_openview,";
          $q_string .= "red_text,red_default,int_management,int_backup,int_ip6,int_login ";
          $q_string .= "from interface ";
          $q_string .= "left join inv_int_types on inv_int_types.itp_id = interface.int_type ";
          $q_string .= "left join inv_users on inv_users.usr_id = interface.int_user ";
          $q_string .= "left join inv_int_redundancy on inv_int_redundancy.red_id = interface.int_redundancy ";
          $q_string .= "where int_companyid = " . $formVars['int_companyid'] . " and int_int_id = " . $a_interface['int_id'] . " ";
          $q_string .= "order by int_face,int_addr,int_server";
          $q_redundancy = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_redundancy) > 0) {
            while ($a_redundancy = mysqli_fetch_array($q_redundancy)) {

              $default    = " class=\"ui-widget-content\"";
              $defaultdel = " class=\"ui-widget-content delete\"";
              if ($a_redundancy['int_primary'] == 1) {
                $default    = " class=\"ui-state-highlight\"";
                $defaultdel = " class=\"ui-state-highlight delete\"";
              }
              $servername = $a_redundancy['int_server'];
              $fqdn_flag = 0;
              if ($a_redundancy['int_domain'] != '') {
                $servername = $a_redundancy['int_server'] . "." . $a_redundancy['int_domain'];
                $fqdn_flag = 1;
              }
              $forward = "";
              $fwdtitle = "";
              $reverse = "";
              $revtitle = "";
	      if ($a_redundancy['int_ip6'] == 0) {
# verify the interface has a valid IP first. No need to further check if not
                if (filter_var($a_redundancy['int_addr'], FILTER_VALIDATE_IP)) {
                  $actualhost = gethostbyaddr($a_redundancy['int_addr']);
                  if ($actualhost == $a_redundancy['int_addr'] || $actualhost != $servername) {
                    if ($actualhost == $a_redundancy['int_addr']) {
                      $revtitle = "IP Lookup Failed: " . $actualhost . ".\nShould have returned: " . $a_redundancy['int_addr'] . ".";
                    }
                    if ($actualhost != $servername) {
                      $revtitle = "Hostname Mismatch: " . $actualhost . ".\nShould have returned: " . $servername . ".";
                    }
                    $reverse = "";
                  } else {
# clear it once determined.
                    $revtitle = "";
                    $reverse = "&#x2713;";
                  }
# get the IP Address from the hostname but only if the hostname isn't an IP address and skip if the IP errors out
                  if ($a_redundancy['int_addr'] != $servername) {
                    if ($fqdn_flag) {
                      $actualip = gethostbyname($servername);
                      if ($actualip == $servername) {
                        if ($actualip == $servername) {
                          $fwdtitle = "Hostname Lookup Failed: " . $actualip . ".\nShould have returned: " . $servername . ".";
                        }
                        if ($actualip != $a_redundancy['int_addr']) {
                          $fwdtitle = "IP Mismatch: " . $actualip . ".\nShould have returned: " . $a_redundancy['int_addr'] . ".";
                        }
                        $forward = "";
                      } else {
# clear it once determined.
                        $fwdtitle = "";
                        $forward = "&#x2713;";
                      }
                    } else {
                      $fwdtitle = "Hostname is not a FQDN.";
                      $forward = "";
                    }
                  }
                }
              }
              if ($a_redundancy['int_eth'] == '00:00:00:00:00:00') {
                $showmac = '';
              } else {
                $showmac = $a_redundancy['int_eth'];
              }
              if ($a_redundancy['int_addr'] == '') {
                $showmask = '';
              } else {
                $showmask = "/" . $a_redundancy['int_mask'];
              }
              $addrchecked = "";
              if ($a_redundancy['int_vaddr']) {
                $addrchecked = "&#x2713;";
              }
              $ethchecked = "";
              if ($a_redundancy['int_veth']) {
                $ethchecked = "&#x2713;";
              }
              $gatechecked = "";
              if ($a_redundancy['int_vgate']) {
                $gatechecked = "&#x2713;";
              }
              $checked = "";
              if ($a_redundancy['int_verified']) {
                $checked = "&#x2713;";
              }
              $redundancy = '';
# basically if not a default interface, then redundancy exists
              if ($a_redundancy['red_default'] == 0) {
                $redundancy = ' (r)';
# new bridge interface
                if ($a_redundancy['red_text'] == 'LACP') {
                  $redundancy = ' (b)';
                }
              }
              $virtual = '';
              if ($a_redundancy['int_virtual'] > 0) {
                $virtual = ' (v)';
              }
              $login = '';
              if ($a_redundancy['int_login'] > 0) {
                $login = ' (sh)';
              }
              $management = '';
              if ($a_redundancy['int_management'] > 0) {
                $management = ' (M)';
                $mgtcount++;
              }
              $backups = '';
              if ($a_redundancy['int_backup'] > 0) {
                $backups = ' (B)';
              }
              $group = '';
              if ($a_redundancy['int_groupname'] != '') {
                $group = ' (' . $a_redundancy['int_groupname'] . ')';
              }
              $title = " title=\"Updated by: " . $a_redundancy['usr_name'] . "\"";

              $monitor = '';
              if ($a_redundancy['int_nagios'] || $a_redundancy['int_openview']) {
                $monitor = ' (';
                if ($a_redundancy['int_nagios']) {
                  $monitor .= "N";
                }
                if ($a_redundancy['int_openview']) {
                  $monitor .= "O";
                }
                $monitor .= ')';
              }

              $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('interface.fill.php?id=" . $a_redundancy['int_id'] . "');jQuery('#dialogInterfaceUpdate').dialog('open');return false;\">";
              $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('interface.del.php?id="  . $a_redundancy['int_id'] . "');\">";
              $linkend = "</a>";

              $output .= "<tr>\n";
              $output .= "  <td"          . $defaultdel . ">"   . $linkdel                                                                  . "</td>\n";
              $output .= "  <td"          . $default    . ">> " . $linkstart . $servername . $redundancy . $group . $monitor . $management . $backups . $login . $linkend . "</td>\n";
              $output .= "  <td"          . $defaultdel . " title=\"" . $fwdtitle . "\">" . $linkstart . $forward                 . $linkend   . "</td>\n";
              $output .= "  <td"          . $defaultdel . " title=\"" . $revtitle . "\">" . $linkstart . $reverse                . $linkend   . "</td>\n";
              $output .= "  <td"          . $default    . ">"   . $linkstart . $a_redundancy['int_face']   . $virtual . $linkend            . "</td>\n";
              if (return_Virtual($db, $formVars['int_companyid']) == 0) {
                $output .= "  <td"        . $default    . ">"   . $linkstart . $a_redundancy['int_sysport']           . $linkend            . "</td>\n";
              }
              $output .= "  <td"          . $default    . ">"   . $linkstart . $showmac                               . $ethchecked . $linkend            . "</td>\n";
              $output .= "  <td"          . $default    . ">"   . $linkstart . $a_redundancy['int_addr'] . $showmask  . $addrchecked . $linkend            . "</td>\n";
              $output .= "  <td"          . $default    . ">"   . $linkstart . $a_redundancy['int_gate']              . $gatechecked . $linkend            . "</td>\n";
              if (return_Virtual($db, $formVars['int_companyid']) == 0) {
                $output .= "  <td"        . $default    . ">"   . $linkstart . $a_redundancy['int_switch']            . $linkend            . "</td>\n";
                $output .= "  <td"        . $default    . ">"   . $linkstart . $a_redundancy['int_port']              . $linkend            . "</td>\n";
              }
              $output .= "  <td"          . $default    . " title=\"" . $a_redundancy['itp_description'] . "\">"   . $linkstart . $a_redundancy['itp_acronym']           . $linkend            . "</td>\n";
              $output .= "  <td" . $title . $default    . ">"   . $linkstart . $a_redundancy['int_update']            . $linkend . $checked . "</td>\n";
              $output .= "</tr>\n";

# Display any secondary redundancy memberships here
              $q_string  = "select int_id,int_server,int_domain,int_companyid,int_face,int_addr,int_eth,int_mask,int_switch,int_groupname,int_vaddr,int_veth,int_vgate,";
              $q_string .= "int_virtual,int_port,int_sysport,int_verified,int_primary,itp_acronym,itp_description,int_gate,int_update,usr_name,";
              $q_string .= "red_text,red_default,int_nagios,int_openview,int_management,int_backup,int_ip6,int_login ";
              $q_string .= "from interface ";
              $q_string .= "left join inv_int_types on inv_int_types.itp_id = interface.int_type ";
              $q_string .= "left join inv_users on inv_users.usr_id = interface.int_user ";
              $q_string .= "left join inv_int_redundancy on inv_int_redundancy.red_id = interface.int_redundancy ";
              $q_string .= "where int_companyid = " . $formVars['int_companyid'] . " and int_int_id = " . $a_redundancy['int_id'] . " ";
              $q_string .= "order by int_face,int_addr,int_server";
              $q_secondary = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_secondary) > 0) {
                while ($a_secondary = mysqli_fetch_array($q_secondary)) {

                  $default    = " class=\"ui-widget-content\"";
                  $defaultdel = " class=\"ui-widget-content delete\"";
                  if ($a_secondary['int_primary'] == 1) {
                    $default    = " class=\"ui-state-highlight\"";
                    $defaultdel = " class=\"ui-state-highlight delete\"";
                  }
                  $servername = $a_secondary['int_server'];
                  $fqdn_flag = 0;
                  if ($a_secondary['int_domain'] != '') {
                    $servername = $a_secondary['int_server'] . '.' . $a_secondary['int_domain'];
                    $fqdn_flag = 1;
                  }
                  $forward = "";
                  $fwdtitle = "";
                  $reverse = "";
                  $revtitle = "";
	          if ($a_secondary['int_ip6'] == 0) {
# verify the interface has a valid IP first. No need to further check if not
                    if (filter_var($a_secondary['int_addr'], FILTER_VALIDATE_IP)) {
                      $actualhost = gethostbyaddr($a_secondary['int_addr']);
                      if ($actualhost == $a_secondary['int_addr'] || $actualhost != $servername) {
                        if ($actualhost == $a_secondary['int_addr']) {
                          $revtitle = "IP Lookup Failed: " . $actualhost . ".\nShould have returned: " . $a_secondary['int_addr'] . ".";
                        }
                        if ($actualhost != $servername) {
                          $revtitle = "Hostname Mismatch: " . $actualhost . ".\nShould have returned: " . $servername . ".";
                        }
                        $reverse = "";
                      } else {
# clear it once determined.
                        $revtitle = "";
                        $reverse = "&#x2713;";
                      }
# get the IP Address from the hostname but only if the hostname isn't an IP address and skip if the IP errors out
                      if ($a_secondary['int_addr'] != $servername) {
                        if ($fqdn_flag) {
                          $actualip = gethostbyname($servername);
                          if ($actualip == $servername) {
                            if ($actualip == $servername) {
                              $fwdtitle = "Hostname Lookup Failed: " . $actualip . ".\nShould have returned: " . $servername . ".";
                            }
                            if ($actualip != $a_secondary['int_addr']) {
                              $fwdtitle = "IP Mismatch: " . $actualip . ".\nShould have returned: " . $a_secondary['int_addr'] . ".";
                            }
                            $forward = "";
                          } else {
# clear it once determined.
                            $fwdtitle = "";
                            $forward = "&#x2713;";
                          }
                        } else {
                          $fwdtitle = "Hostname is not a FQDN.";
                          $forward = "";
                        }
                      }
                    }
                  }
                  if ($a_secondary['int_eth'] == '00:00:00:00:00:00') {
                    $showmac = '';
                  } else {
                    $showmac = $a_secondary['int_eth'];
                  }
                  if ($a_secondary['int_addr'] == '') {
                    $showmask = '';
                  } else {
                    $showmask = "/" . $a_secondary['int_mask'];
                  }
                  $addrchecked = "";
                  if ($a_secondary['int_vaddr']) {
                    $addrchecked = "&#x2713;";
                  }
                  $ethchecked = "";
                  if ($a_secondary['int_veth']) {
                    $ethchecked = "&#x2713;";
                  }
                  $gatechecked = "";
                  if ($a_secondary['int_vgate']) {
                    $gatechecked = "&#x2713;";
                  }
                  $checked = "";
                  if ($a_secondary['int_verified']) {
                    $checked = "&#x2713;";
                  }
                  $redundancy = '';
# basically if not a default value which is 'no redundancy', then it's redundant
                  if ($a_secondary['red_default'] == 0) {
                    $redundancy = ' (r)';
# new bridge interface
                    if ($a_secondary['red_text'] == 'LACP') {
                      $redundancy = ' (b)';
                    }
                  }
                  $virtual = '';
                  if ($a_secondary['int_virtual'] > 0) {
                    $virtual = ' (v)';
                  }
                  $login = '';
                  if ($a_secondary['int_login'] > 0) {
                    $login = ' (sh)';
                  }
                  $management = '';
                  if ($a_secondary['int_management'] > 0) {
                    $management = ' (M)';
                    $mgtcount++;
                  }
                  $backups = '';
                  if ($a_secondary['int_backup'] > 0) {
                    $backups = ' (B)';
                  }
                  $group = '';
                  if ($a_secondary['int_groupname'] != '') {
                    $group = ' (' . $a_secondary['int_groupname'] . ')';
                  }
                  $title = " title=\"Updated by: " . $a_secondary['usr_name'] . "\"";

                  $monitor = '';
                  if ($a_secondary['int_nagios'] || $a_secondary['int_openview']) {
                    $monitor = ' (';
                    if ($a_secondary['int_nagios']) {
                      $monitor .= "N";
                    }
                    if ($a_secondary['int_openview']) {
                      $monitor .= "O";
                    }
                    $monitor .= ')';
                  }

                  $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('interface.fill.php?id=" . $a_secondary['int_id'] . "');jQuery('#dialogInterfaceUpdate').dialog('open');return false;\">";
                  $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('interface.del.php?id="  . $a_secondary['int_id'] . "');\">";
                  $linkend = "</a>";

                  $output .= "<tr>\n";
                  $output .=   "<td"          . $defaultdel . ">"   . $linkdel                                                                  . "</td>\n";
                  $output .= "  <td"          . $default    . ">>> " . $linkstart . $a_secondary['int_server'] . $redundancy . $group . $monitor . $management . $backups . $login . $linkend . "</td>\n";
                  $output .= "  <td"          . $defaultdel . " title=\"" . $fwdtitle . "\">" . $linkstart . $forward                 . $linkend   . "</td>\n";
                  $output .= "  <td"          . $defaultdel . " title=\"" . $revtitle . "\">" . $linkstart . $reverse                . $linkend   . "</td>\n";
                  $output .= "  <td"          . $default    . ">"   . $linkstart . $a_secondary['int_face']   . $virtual . $linkend            . "</td>\n";
                  if (return_Virtual($db, $formVars['int_companyid']) == 0) {
                    $output .= "  <td"        . $default    . ">"   . $linkstart . $a_secondary['int_sysport']           . $linkend            . "</td>\n";
                  }
                  $output .= "  <td"          . $default    . ">"   . $linkstart . $showmac                               . $ethchecked . $linkend            . "</td>\n";
                  $output .= "  <td"          . $default    . ">"   . $linkstart . $a_secondary['int_addr'] . $showmask  . $addrchecked . $linkend            . "</td>\n";
                  $output .= "  <td"          . $default    . ">"   . $linkstart . $a_secondary['int_gate']              . $gatechecked . $linkend            . "</td>\n";
                  if (return_Virtual($db, $formVars['int_companyid']) == 0) {
                    $output .= "  <td"        . $default    . ">"   . $linkstart . $a_secondary['int_switch']            . $linkend            . "</td>\n";
                    $output .= "  <td"        . $default    . ">"   . $linkstart . $a_secondary['int_port']              . $linkend            . "</td>\n";
                  }
                  $output .= "  <td"          . $default    . " title=\"" . $a_secondary['itp_description'] . "\">"   . $linkstart . $a_secondary['itp_acronym']           . $linkend            . "</td>\n";
                  $output .= "  <td" . $title . $default    . ">"   . $linkstart . $a_secondary['int_update']            . $linkend . $checked . "</td>\n";
                  $output .= "</tr>\n";
                }
              }
            }
          }
        }
      } else {
        $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No Network Interfaces added.</td>\n";
      }

      mysqli_free_result($q_interface);

      $output .= "</table>\n";

      print "document.getElementById('interface_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";


# rebuild the int_int_id drop down in case of changes in the virtual interface listing
      print "var selboxCreate = document.formInterfaceCreate.int_int_id;\n\n";
      print "var selboxUpdate = document.formInterfaceUpdate.int_int_id;\n\n";
      print "selboxCreate.options.length = 0;\n";
      print "selboxUpdate.options.length = 0;\n";

      $q_string  = "select int_id,int_face,int_ip6 ";
      $q_string .= "from interface ";
      $q_string .= "left join inv_int_redundancy on inv_int_redundancy.red_id = interface.int_redundancy ";
      $q_string .= "where int_companyid = " . $formVars['int_companyid'] . " and red_default = 0 ";
      $q_string .= "order by int_ip6,int_face";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_interface) > 0) {
        while ($a_interface = mysqli_fetch_array($q_interface)) {
          if ($a_interface['int_ip6'] == 1) {
            $ip6 = " (ipv6)";
          } else {
            $ip6 = "";
          }
          print "selboxCreate.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_interface['int_face'] . $ip6) . "\"," . $a_interface['int_id'] . ");\n";
          print "selboxUpdate.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_interface['int_face'] . $ip6) . "\"," . $a_interface['int_id'] . ");\n";
        }
      } else {
        print "selboxCreate.options[selbox.options.length] = new Option(\"No Redundant Interfaces identified\",0);\n";
        print "selboxUpdate.options[selbox.options.length] = new Option(\"No Redundant Interfaces identified\",0);\n";
      }


# Warn folks if there aren't any management devices
      if ($mgtcount == 0 && $formVars['int_companyid'] != 0) {
        print "alert(\"ERROR: No interfaces have been identified to be processing Management Traffic.\\n\\nSelect an interface and under the Monitoring tab, check the Management checkbox.\");\n";
      }

# Warn folks if a system has more than 1 interface marked for management traffic.
      if ($mgtcount > 1 && $formVars['int_companyid'] != 0) {
        print "alert(\"ERROR: " . $mgtcount . " management interfaces have been associated with this server.\\n\\nIdentify the device that will be permitting Management traffic and uncheck the Management checkbox for the rest of the interfaces.\");\n";
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
