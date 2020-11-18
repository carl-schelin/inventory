<?php
# Script: config.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: This is intended to be a copy of the RSDP information for the server. The idea is to continue to use  
# RSDP information as how the system should be configured. Then the audit script (chkserver) can report on servers
# that don't match the expected configuration. Eventually once we're sure the information is accurate, we can use 
# it to force servers to be configured remotely vs having to log in to the server.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "config.mysql.php";
    $formVars['update']          = clean($_GET['update'],        10);
    $formVars['cfg_companyid']   = clean($_GET['cfg_companyid'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['cfg_companyid'] == '') {
      $formVars['cfg_companyid'] = 0;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],               10);
        $formVars['clu_association'] = clean($_GET['clu_association'],  10);
        $formVars['clu_notes']       = clean($_GET['clu_notes'],       255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['clu_companyid'] > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "clu_companyid   =   " . $formVars['clu_companyid']   . "," .
            "clu_association =   " . $formVars['clu_association'] . "," .
            "clu_notes       = \"" . $formVars['clu_notes']       . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into cluster set clu_id = NULL," . $q_string;
            $message = "Association added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update cluster set " . $q_string . " where clu_id = " . $formVars['id'];
            $message = "Association updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['clu_association']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -3) {

        logaccess($_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"cfg_refresh\" value=\"Refresh Configuration Listing\" onClick=\"javascript:attach_config('config.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"cfg_update\"  value=\"Update Configuration\"          onClick=\"javascript:attach_config('config.mysql.php', 1);hideDiv('config-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"cfg_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"cfg_addbtn\"  value=\"Add Configuration\"             onClick=\"javascript:attach_config('config.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">Configuration Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Configuration With: <select name=\"cfg_association\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 ";
        $q_string .= "order by inv_name ";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Notes <input type=\"text\" name=\"cfg_notes\" size=\"60\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('config_form').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

# if the inv_rsdp setting is 0, then automatically create an entry with the information from the inventory
# rsdp_server for the main entry.
# - rsdp_requestor int _SESSION[uid]
# - rsdp_location int inv_location
# - rsdp_product int inv_product
# - rsdp_project int inv_project
# - rsdp_platform int inv_manager
# - rsdp_application int inv_appadmin
# - rsdp_service int inv_class
# - rsdp_processors int (count number of CPUs ; parts parts_id = 8
# - rsdp_memory char(20) type 4; remove GB from the model output
# rsdp_backups for backup schedules.
# rsdp_interface for the interfaces.
# - if_name char(60) int_server
# - if_sysport char(60) int_port - verify it's not sysport
# - if_interface char(30) int_face
# - if_groupname char(30) int_groupname
# - if_if_id int int_int_id
# - if_mac char(30) int_eth
# - if_zone int int_zone
# - if_vlan char(30) int_vlan
# - if_ip char(60) int_addr
# - if_mask char(60) int_mask
# - if_gate char(60) int_gate
# - if_speed int int_speed
# - if_duplex int int_duplex
# - if_redundant int int_redundancy
# - if_media int int_media
# - if_type int int_type from inttype; for application and management only?
# - if_switch char(50) int_switch
# - if_port char(50) int_sysport - verify it's not int_port
# - if_virtual int int_virtual
# - if_monitored int int_openview
# rsdp_osteam for the operating system
# - os_sysname char(60)
# - os_software int
# rsdp_status to mark it complete.

      $q_string  = "select inv_rsdp ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['cfg_companyid'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inventory = mysqli_fetch_array($q_inventory);

# The intention here is to show the items currently being set in the chkserver.input file
# Server:Group: [dbadmins|mobadmin|webapps|scmadmins]
# Server:Sudoers: [dbadmins|mobadmin|webapps|scmadmins]
# Server:Hostname: hostname : rsdpid
# SErver:CPU: number of cpus
# Server:Memory: amount of ram
# Server:Disk: os size (80G mainly) # not currently used for anything right now
# Server:Disk: Then all the other configured disks; not LVM volumes but actual raw media
# Server:IP: ipaddr : gateway ; only if not type 4 and not type 6;
# Server:Openview (basically is openview installed and running)
# Server:Service:opc_op (service account)
# Server:OpNet
# Server:Service:opnet
# Server:Datapalette
# Server:Centrify
# Server:Netbackup

# this is grabbed from the Inventory and not from RSDP.
# Server:MonitoringIPAddress: ip address
# Server:MonitoringInterface: interface
# Server:MonitoringServer: lnmtcodcom1vip.scc911.com
# Server:BackupIPAddress: ip address
# Server:BackupInterface: interface
# Server:NetworkZone: network zone
# Server:Location: West location identifier
# Server:Cron:oracle
# Server:Service:oracle





      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" colspan=\"6\">Server Details</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('association-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"association-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Association Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Clicking the <strong>x</strong> will delete this association from this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an association to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Association Management</strong> title bar to toggle the <strong>Association Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Association</th>\n";
      $output .=   "<th class=\"ui-state-default\">Platform</th>\n";
      $output .=   "<th class=\"ui-state-default\">Notes</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select clu_id,clu_association,clu_notes,grp_name,inv_name ";
      $q_string .= "from cluster ";
      $q_string .= "left join inventory on inventory.inv_id = cluster.clu_companyid ";
      $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
      $q_string .= "where clu_companyid = " . $formVars['clu_companyid'] . " ";
      $q_string .= "order by inv_name,clu_association,clu_port";
      $q_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_cluster) > 0) {
        while ($a_cluster = mysqli_fetch_array($q_cluster)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('association.fill.php?id=" . $a_cluster['clu_id'] . "');showDiv('association-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_association('association.del.php?id="  . $a_cluster['clu_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                          . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_cluster['inv_name']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_cluster['grp_name']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_cluster['clu_notes']   . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No Associations created.</td>\n";
      }
      $output .= "</table>\n";

      mysqli_free_result($q_cluster);

      print "document.getElementById('cfg_detail_form').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";
      print "document.edit.clu_update.disabled = true;\n";



      print "document.getElementById('cfg_network_form').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";
      print "document.edit.clu_update.disabled = true;\n";


      print "document.getElementById('cfg_server_form').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";
      print "document.edit.clu_update.disabled = true;\n";


    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
