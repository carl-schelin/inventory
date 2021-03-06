<?php
# Script: network.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "network.mysql.php";
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
        $formVars['id']                 = clean($_GET['id'],                 10);
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

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
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
          $q_string .= "from ipaddress ";
          $q_string .= "where ip_id = " . $formVars['int_ipaddressid'] . " ";
          $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            $a_ipaddress = mysqli_fetch_array($q_ipaddress);

# now get the netmask and netvlan
            $q_string  = "select net_mask,net_vlan ";
            $q_string .= "from network ";
            $q_string .= "where net_id = " . $a_ipaddress['ip_network'] . " ";
            $q_network = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_network) > 0) {
              $a_network = mysqli_fetch_array($q_network);
            } else {
              $a_network['net_mask'] = 0;
              $a_network['net_vlan'] = "";
            }

            $q_string  = "select ip_ipv4 ";
            $q_string .= "from ipaddress ";
            $q_string .= "left join ip_types on ip_types.ip_id = ipaddress.ip_type ";
            $q_string .= "where ip_network = " . $a_ipaddress['ip_network'] . " and ip_name = \"Gateway\" ";
            $q_string .= "limit 1 ";
            $q_addr = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_addr) > 0) {
              $a_addr = mysqli_fetch_array($q_addr);
            } else {
              $a_addr['ip_ipv4'] = '';
            }

          } else {
            $a_ipaddress['ip_ipv4'] = '';
            $a_ipaddress['ip_hostname'] = '';
            $a_ipaddress['ip_domain'] = '';
            $a_ipaddress['ip_network'] = 0;
          }

# want to get vlan, gateway, subnetmask, etc at least until the info is fully rewritten.
# something like get the network from above, then get the vlan from there and the gateway from the listing of IPs associated with the network.

          $q_string = 
            "int_server       = \"" . $a_ipaddress['ip_hostname']   . "\"," .
            "int_domain       = \"" . $a_ipaddress['ip_domain']     . "\"," .
            "int_addr         = \"" . $a_ipaddress['ip_ipv4']       . "\"," .

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
            $query = "insert into interface set int_id = NULL, " . $q_string;
            $message = "Interface added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update interface set " . $q_string . " where int_id = " . $formVars['id'];
            $message = "Interface updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

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
        $output .= "<input type=\"button\" name=\"int_refresh\" value=\"Refresh Network Listing\" onClick=\"javascript:attach_interface('network.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"int_update\"  value=\"Update Interface\"        onClick=\"javascript:attach_interface('network.mysql.php', 1);hideDiv('network-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"int_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"int_addbtn\"  value=\"Add Interface\"           onClick=\"javascript:attach_interface('network.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"int_copyitem\" value=\"Copy Network Table From:\" onClick=\"javascript:attach_interface('network.mysql.php', -2);\">\n";
        $output .= "<select name=\"int_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('network_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";




        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">System Form</th>\n";
        $output .= "</tr>\n";

        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">IP Address* <select name=\"int_ipaddressid\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select ip_id,ip_ipv4,ip_hostname ";
        $q_string .= "from ipaddress ";
        $q_string .= "order by ip_hostname,ip_ipv4 ";
        $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_ipaddress) > 0) {
          while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {

            $q_string  = "select int_id ";
            $q_string .= "from interface ";
            $q_string .= "where int_ipaddressid = " . $a_ipaddress['ip_id'] . " ";
            $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_interface) == 0) {
              $output .= "<option value=\"" . $a_ipaddress['ip_id'] . "\">" . $a_ipaddress['ip_hostname'] . " " . $a_ipaddress['ip_ipv4'] . "</option>\n";
            } else {
              $output .= "<option value=\"" . $a_ipaddress['ip_id'] . "\">" . $a_ipaddress['ip_hostname'] . " " . $a_ipaddress['ip_ipv4'] . "*</option>\n";
            }
          }
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\"><label>Virtual Interface? <input type=\"checkbox\" name=\"int_virtual\"></label></td>\n"; 
        $output .= "  <td class=\"ui-widget-content\"><label>Default Route? <input type=\"checkbox\" name=\"int_primary\"></label></td>\n";
        $output .= "</tr>\n"; 

        $output .= "<tr>\n"; 
        $output .= "  <td class=\"ui-widget-content\">Logical Interface Name* <input type=\"text\" name=\"int_face\" size=\"10\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Interface Type: <select name=\"int_type\">\n";

        $q_string  = "select itp_id,itp_name ";
        $q_string .= "from int_types ";
        $q_string .= "order by itp_id";
        $q_int_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_int_types = mysqli_fetch_array($q_int_types)) {
          $output .= "<option value=\"" . $a_int_types['itp_id'] . "\">" . $a_int_types['itp_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">MAC* <input type=\"text\" name=\"int_eth\" value=\"00:00:00:00:00:00\" size=\"18\"></td>\n";
        $output .= "</tr>\n";

        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Redundancy: <select name=\"int_redundancy\">\n";
        $output .= "<option value=\"0\">Child Interface</option>\n";

        $q_string  = "select red_id,red_text ";
        $q_string .= "from int_redundancy ";
        $q_string .= "order by red_text";
        $q_int_redundancy = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_int_redundancy = mysqli_fetch_array($q_int_redundancy)) {
          $output .= "<option value=\"" . $a_int_redundancy['red_id'] . "\">" . $a_int_redundancy['red_text'] . "</option>\n";
        }
        $output .= "</select></td>\n"; 
        $output .= "  <td class=\"ui-widget-content\">";

        $os = return_System($db, $formVars['int_companyid']);

        if ($os == "Linux") {
          $output .= "Bond ";
        }
        if ($os == "HP-UX") {
          $output .= "APA ";
        }
        if ($os == "SunOS") {
          $output .= "IPMP ";
        }
        if ($os == "Windows") {
          $output .= "Teaming ";
        }

        $output .= "Assignment <select name=\"int_int_id\"></select></td>\n"; 
        $output .= "  <td class=\"ui-widget-content\">Group Name: <input type=\"text\" name=\"int_groupname\" size=\"20\"></td>\n";
        $output .= "</tr>\n"; 

        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\"><label><input type=\"checkbox\" name=\"int_management\"> Used for Management traffic</label></td>";
        $output .= "  <td class=\"ui-widget-content\"><label><input type=\"checkbox\" name=\"int_backup\"> Used for Backup traffic</label></td>";
        $output .= "  <td class=\"ui-widget-content\"><label><input type=\"checkbox\" name=\"int_login\"> Used for Secure Shell traffic</label></td>";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">Note: <input type=\"text\" name=\"int_note\" size=\"80\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('nwsystem_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";



        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"3\">Physical Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">Physical Hardware Port <input type=\"text\" name=\"int_sysport\" size=\"20\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";

        $output .= "  <td class=\"ui-widget-content\">Media: <select name=\"int_media\">\n";
        $output .= "<option value=\"0\">N/A</option>\n";

        $q_string = "select med_id,med_text from int_media order by med_text";
        $q_int_media = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_int_media = mysqli_fetch_array($q_int_media)) {
          $output .= "<option value=\"" . $a_int_media['med_id'] . "\">" . $a_int_media['med_text'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Speed*: <select name=\"int_speed\">\n";
        $output .= "<option value=\"0\">N/A</option>\n";

        $q_string = "select spd_id,spd_text from int_speed order by spd_text";
        $q_int_speed = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_int_speed = mysqli_fetch_array($q_int_speed)) {
          $output .= "<option value=\"" . $a_int_speed['spd_id'] . "\">" . $a_int_speed['spd_text'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Duplex*: <select name=\"int_duplex\">\n";
        $output .= "<option value=\"0\">N/A</option>\n";

        $q_string = "select dup_id,dup_text from int_duplex order by dup_text";
        $q_int_duplex = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_int_duplex = mysqli_fetch_array($q_int_duplex)) {
          $output .= "<option value=\"" . $a_int_duplex['dup_id'] . "\">" . $a_int_duplex['dup_text'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Switch <input type=\"text\" name=\"int_switch\" size=\"40\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Port <input type=\"text\" name=\"int_port\" size=\"20\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('nwphysical_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" colspan=\"12\">Interface Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('network-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"network-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Interface Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlighted</strong> - This interface is the <span class=\"ui-state-highlight\">Default Route</span>.</li>\n";
      $output .= "    <li><strong>Highlighted</strong> - This hostname either doesn't match the resolved hostname or is simply <span class=\"ui-state-error\">not in DNS</span>. If incorrect or incomplete, the identified DNS entry will be displayed. If no DNS entry, it will show the IP Address. Not all interfaces need to be in DNS but they will be highlighted if not.</li>\n";
      $output .= "    <li><strong>Delete</strong> - Clicking the <strong>Delete</strong> button will delete this interface from this server.</li>\n";
      $output .= "    <li><strong>Bridge</strong> - A bridge interface will be designed with a (b).</li>\n";
      $output .= "    <li><strong>Virtual Memberships</strong> - If a physical interface is a member of a virtual interface, it will be designated with a &gt; to the left of the name and will listed under the virtual interface. The main virtual interface of the group will be designated with (r). If Group or Teaming names are used, they will be listed next to the physical members of the group.\n";
      $output .= "    <ul>\n";
      $output .= "      <li><strong>Solaris</strong> virtual interfaces end in :number (e1000g1:1, e1000g5:1, etc).</li>\n";
      $output .= "      <li><strong>Linux</strong> virtual interfaces begin with bond (bond0, bond0.87, bond1, etc).</li>\n";
      $output .= "      <li><strong>HP-UX</strong> virtual interfaces are in the 900 range (lan900, lan901, etc).</li>\n";
      $output .= "      <li><strong>Windows</strong> virtual interfaces.</li>\n";
      $output .= "    </ul></li>\n";
      $output .= "    <li><strong>Virtual</strong> - A Virtual interface will be identified with a (v) next to the Logical Interface name. Not all Virtual interfaces are part of a Redundancy group.</li>\n";
      $output .= "    <li><strong>Management</strong> - A interface that is designated to pass management traffic will be identified with a (M). There should only be one interface identified as such.</li>\n";
      $output .= "    <li><strong>Backups</strong> - A interface that is designated to pass backup traffic will be identified with a (B). If it's not designated, by default the (M) interface is assumed to pass backup traffic.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an interface to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Network Management</strong> title bar to toggle the <strong>Network Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Del</th>\n";
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
      $q_string  = "select int_id,int_server,int_domain,int_companyid,int_redundancy,int_management,int_login,";
      $q_string .= "int_backup,int_face,int_addr,int_eth,int_mask,int_switch,int_vaddr,int_veth,int_vgate,";
      $q_string .= "int_redundancy,int_virtual,int_port,int_sysport,int_verified,int_primary,itp_acronym,";
      $q_string .= "itp_description,int_gate,int_update,usr_name,int_nagios,int_openview,int_ip6,ip_ipv4 ";
      $q_string .= "from interface ";
      $q_string .= "left join int_types on int_types.itp_id = interface.int_type ";
      $q_string .= "left join ipaddress on ipaddress.ip_id = interface.int_ipaddressid ";
      $q_string .= "left join users on users.usr_id = interface.int_user ";
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
          if ($a_interface['int_redundancy'] > 0) {
            $redundancy = ' (r)';
# new bridge interface
            if ($a_interface['int_redundancy'] == 12) {
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

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('network.fill.php?id=" . $a_interface['int_id'] . "');showDiv('network-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('network.del.php?id="  . $a_interface['int_id'] . "');\">";
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
          $q_string .= "int_redundancy,int_management,int_backup,int_ip6,int_login ";
          $q_string .= "from interface ";
          $q_string .= "left join int_types on int_types.itp_id = interface.int_type ";
          $q_string .= "left join users on users.usr_id = interface.int_user ";
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
              if ($a_redundancy['int_redundancy'] > 0) {
                $redundancy = ' (r)';
# new bridge interface
                if ($a_redundancy['int_redundancy'] == 12) {
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

              $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('network.fill.php?id=" . $a_redundancy['int_id'] . "');showDiv('network-hide');\">";
              $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('network.del.php?id="  . $a_redundancy['int_id'] . "');\">";
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
              $q_string .= "int_nagios,int_openview,int_management,int_backup,int_ip6,int_login ";
              $q_string .= "from interface ";
              $q_string .= "left join int_types on int_types.itp_id = interface.int_type ";
              $q_string .= "left join users on users.usr_id = interface.int_user ";
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
                  if ($a_secondary['int_redundancy'] > 0) {
                    $redundancy = ' (r)';
# new bridge interface
                    if ($a_secondary['int_redundancy'] == 12) {
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

                  $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('network.fill.php?id=" . $a_secondary['int_id'] . "');showDiv('network-hide');\">";
                  $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_interface('network.del.php?id="  . $a_secondary['int_id'] . "');\">";
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

      print "document.getElementById('network_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.edit.int_update.disabled = true;\n";

      print "document.edit.int_server.focus();\n";


# rebuild the int_int_id drop down in case of changes in the virtual interface listing
      print "var selbox = document.edit.int_int_id;\n\n";
      print "selbox.options.length = 0;\n";

      $q_string  = "select int_id,int_face,int_ip6 ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $formVars['int_companyid'] . " and int_redundancy > 0 ";
      $q_string .= "order by int_ip6,int_face";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        if ($a_interface['int_ip6'] == 1) {
          $ip6 = " (ipv6)";
        } else {
          $ip6 = "";
        }
        print "selbox.options[selbox.options.length] = new Option(\"" . htmlspecialchars($a_interface['int_face'] . $ip6) . "\"," . $a_interface['int_id'] . ");\n";
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
