<?php
# Script: config.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],               10);
        $formVars['clu_association'] = clean($_GET['clu_association'],  10);
        $formVars['clu_notes']       = clean($_GET['clu_notes'],       255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['clu_companyid'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

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

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['clu_association']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -3) {

        logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

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

        print "document.getElementById('config_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

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

      $q_string  = "select inv_name,inv_function,inv_rsdp,loc_name,loc_west,inv_location,prod_name,inv_product,prj_name,inv_project,inv_manager,inv_appadmin ";
      $q_string .= "from inventory ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
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



# IMPORTANT!!! Everything is pulled from RSDP tables, not from Inventory tables. RSDP is the Configuration of Record for all servers.
# Ultimately there will be indications that there are differences plus buttons to sync things up.
# button to sync Inventory with RSDP or RSDP with Inventory. A << >> button?

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" colspan=\"6\">Server Details</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('cfg_details-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"cfg_details-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      if ($a_inventory['inv_rsdp'] < 1) {
        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "</tr>\n";
        if ($a_inventory['inv_rsdp'] == -1) {
          $output .=   "<td class=\"ui-widget-content\">Notice: This device will not have a Configuration Entry</td>\n";
        }
        if ($a_inventory['inv_rsdp'] == 0) {
          $output .=   "<td class=\"ui-widget-content\">Notice: No Configuration Entry exists for this server.</td>\n";
        }
        $output .= "</tr>\n";
        $output .= "</table>\n";
      } else {

        $q_string  = "select os_sysname ";
        $q_string .= "from rsdp_osteam ";
        $q_string .= "where os_rsdp = " . $a_inventory['inv_rsdp'] . " ";
        $q_rsdp_osteam = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

        $q_string  = "select rsdp_function,grp_name,rsdp_platform,rsdp_application,loc_name,loc_west,rsdp_location,prod_name,rsdp_product,prj_name,rsdp_project ";
        $q_string .= "from rsdp_server ";
        $q_string .= "left join groups on groups.grp_id = rsdp_server.rsdp_platform ";
        $q_string .= "left join locations on locations.loc_id = rsdp_server.rsdp_location ";
        $q_string .= "left join products on products.prod_id = rsdp_server.rsdp_product ";
        $q_string .= "left join projects on projects.prj_id = rsdp_server.rsdp_project ";
        $q_string .= "where rsdp_id = " . $a_inventory['inv_rsdp'] . " ";
        $q_rsdp_server = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        $q_string  = "select grp_name ";
        $q_string .= "from groups ";
        $q_string .= "where grp_id = " . $a_rsdp_server['rsdp_application'] . " ";
        $q_application = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_application = mysqli_fetch_array($q_application);

        $output .= "<table class=\"ui-styled-table\">\n";

        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $class = "ui-widget-content";
        $button = "";
        if ($a_rsdp_osteam['os_sysname'] != $a_inventory['inv_name']) {
          $class = "ui-state-error";
          $button =   "<input type=\"button\" value=\"Update Inventory\"> or <input type=\"button\" value=\"Update Config\">";
        }
        $output .=   "<td class=\"" . $class . "\">Server Name: " . $a_rsdp_osteam['os_sysname'] . $button . "</td>\n";

        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $class = "ui-widget-content";
        $button = "";
        if ($a_rsdp_server['rsdp_function'] != $a_inventory['inv_function']) {
          $class = "ui-state-error";
          $button = " <input type=\"button\" value=\"Update Inventory\"> or <input type=\"button\" value=\"Update Config\">";
        }
        $output .=   "<td class=\"" . $class . "\">Function: " . $a_rsdp_server['rsdp_function'] . $button . "</td>\n";

        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $class = "ui-widget-content";
        $button = "";
        if ($a_rsdp_server['rsdp_platform'] != $a_inventory['inv_manager']) {
          $class = "ui-state-error";
          $button =   "<input type=\"button\" value=\"Update Inventory\"> or <input type=\"button\" value=\"Update Config\">";
        }
        $output .=   "<td class=\"" . $class . "\">Custodian: " . $a_rsdp_server['grp_name'] . $button . "</td>\n";

        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $class = "ui-widget-content";
        $button = "";
        if ($a_rsdp_server['rsdp_application'] != $a_inventory['inv_appadmin']) {
          $class = "ui-state-error";
          $button =   "<input type=\"button\" value=\"Update Inventory\"> or <input type=\"button\" value=\"Update Config\">";
        }
        $output .=   "<td class=\"" . $class . "\">Application Admins: " . $a_application['grp_name'] . $button . "</td>\n";

        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $class = "ui-widget-content";
        $button = "";
        $text = "Location: " . $a_rsdp_server['loc_name'] . " (" . $a_rsdp_server['loc_west'] . ")";
        if ($a_rsdp_server['rsdp_location'] != $a_inventory['inv_location']) {
          $class = "ui-state-error";
          $text  = "<input type=\"radio\" checked=\"true\" value=\"0\" name=\"loc_error\">" . $a_rsdp_server['loc_name'] . " (" . $a_rsdp_server['loc_west'] . ") ";
          $text .= "<input type=\"radio\" value=\"1\" name=\"loc_error\">" . $a_inventory['loc_name'] . " (" . $a_inventory['loc_west'] . ") ";
          $text .= "<input type=\"button\" value=\"Correct Entry\">";
        }
        $output .=   "<td class=\"" . $class . "\" colspan=\"2\">Location: " . $text . "</td>\n";

        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .=   "<th class=\"ui-state-default\">Configuration Item</th>\n";
        $output .=   "<th class=\"ui-state-default\">Configuration Manager</th>\n";
        $output .=   "<th class=\"ui-state-default\" colspan=\"2\">Correction</th>\n";
        $output .=   "<th class=\"ui-state-default\">Inventory</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";

        $class = "ui-widget-content";
        if ($a_rsdp_server['rsdp_product'] != $a_inventory['inv_product']) {
          $class = "ui-state-error";
        }
        $output .=   "<td class=\"" . $class . "\">Product</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . $a_rsdp_server['prod_name'] . "</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . "<input type=\"button\" value=\"&lt;&lt;\">" . "</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . "<input type=\"button\" value=\"&gt;&gt;\">" . "</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . $a_inventory['prod_name'] . "</td>\n";

        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $class = "ui-widget-content";
        if ($a_rsdp_server['rsdp_project'] != $a_inventory['inv_project']) {
          $class = "ui-state-error";
        }
        $output .=   "<td class=\"" . $class . "\">Project</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . $a_rsdp_server['prj_name'] . "</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . "<input type=\"button\" value=\"&lt;&lt;\">" . "</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . "<input type=\"button\" value=\"&gt;&gt;\">" . "</td>\n";
        $output .=   "<td class=\"" . $class . "\">" . $a_inventory['prj_name'] . "</td>\n";

        $output .= "</tr>\n";
        $output .= "</table>\n";
      }

      print "document.getElementById('cfg_detail_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";


# the hardware form

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" colspan=\"6\">Hardware</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('cfg_hardware-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"cfg_hardware-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      if ($a_inventory['inv_rsdp'] < 1) {
        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "</tr>\n";
        if ($a_inventory['inv_rsdp'] == -1) {
          $output .=   "<td class=\"ui-widget-content\">Notice: This device will not have a Configuration Entry</td>\n";
        }
        if ($a_inventory['inv_rsdp'] == 0) {
          $output .=   "<td class=\"ui-widget-content\">Notice: No Configuration Entry exists for this server.</td>\n";
        }
        $output .= "</tr>\n";
        $output .= "</table>\n";
      } else {

        $q_string  = "select rsdp_processors,rsdp_memory,rsdp_ossize ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $a_inventory['inv_rsdp'] . " ";
        $q_rsdp_server = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .=   "<td class=\"ui-widget-content\">CPUs: " . $a_rsdp_server['rsdp_processors'] . "</td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .=   "<td class=\"ui-widget-content\">Memory: " . $a_rsdp_server['rsdp_memory'] . "G" . "</td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .=   "<td class=\"ui-widget-content\">Main Drive: " . $a_rsdp_server['rsdp_ossize'] . "G" . "</td>\n";
        $output .= "</tr>\n";

        $q_string  = "select fs_volume,fs_size ";
        $q_string .= "from rsdp_filesystem ";
        $q_string .= "where fs_rsdp = " . $a_inventory['inv_rsdp'] . " ";
        $q_rsdp_filesystem = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
          $output .= "<tr>\n";
          $output .=   "<td class=\"ui-widget-content\">Subsequent Drives: " . $a_rsdp_filesystem['fs_volume'] . ", " . $a_rsdp_filesystem['fs_size'] . "G" . "</td>\n";
          $output .= "</tr>\n";
        }

        $output .= "</table>\n";
      }

      print "document.getElementById('cfg_hardware_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";


# the network interface form

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" colspan=\"6\">Network</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('cfg_network-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"cfg_network-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      if ($a_inventory['inv_rsdp'] < 1) {
        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        if ($a_inventory['inv_rsdp'] == -1) {
          $output .=   "<td class=\"ui-widget-content\">Notice: This device will not have a Configuration Entry</td>\n";
        }
        if ($a_inventory['inv_rsdp'] == 0) {
          $output .=   "<td class=\"ui-widget-content\">Notice: No Configuration Entry exists for this server.</td>\n";
        }
        $output .= "</tr>\n";
        $output .= "</table>\n";
      } else {

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">Hostname</th>\n";
        $output .= "  <th class=\"ui-state-default\">IP Address</th>\n";
        $output .= "  <th class=\"ui-state-default\">Netmask</th>\n";
        $output .= "  <th class=\"ui-state-default\">MAC</th>\n";
        $output .= "  <th class=\"ui-state-default\">Gateway</th>\n";
        $output .= "  <th class=\"ui-state-default\">Network Zone</th>\n";
        $output .= "  <th class=\"ui-state-default\">Interface Type</th>\n";
        $output .= "</tr>\n";

        $q_string  = "select if_id,if_name,if_interface,if_mac,zone_zone,if_ip,if_mask,if_gate,itp_acronym ";
        $q_string .= "from rsdp_interface ";
        $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
        $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
        $q_string .= "where if_rsdp = " . $a_inventory['inv_rsdp'] . " ";
        $q_string .= "order by if_name ";
        $q_rsdp_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_rsdp_interface) > 0) {
          while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

            $output .= "<tr>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_name'] . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_ip']   . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_mask'] . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_mac']  . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_gate'] . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['zone_zone'] . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['itp_acronym'] . "</td>\n";
            $output .= "</tr>\n";

          }
        }
        $output .= "</table>\n";
      }

      print "document.getElementById('cfg_network_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";


# the backups form
# need backup information associated with locations and zones
# see chkserver for how to determine from location and network zone.
#      print "document.getElementById('cfg_backup_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
