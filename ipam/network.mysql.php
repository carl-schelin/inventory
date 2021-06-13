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
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],                10);
        $formVars['net_ipv4']        = clean($_GET['net_ipv4'],          20);
        $formVars['net_ipv6']        = clean($_GET['net_ipv6'],          50);
        $formVars['net_mask']        = clean($_GET['net_mask'],          10);
        $formVars['net_zone']        = clean($_GET['net_zone'],          10);
        $formVars['net_location']    = clean($_GET['net_location'],      10);
        $formVars['net_vlan']        = clean($_GET['net_vlan'],          20);
        $formVars['net_description'] = clean($_GET['net_description'],  100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
    
        if (strlen($formVars['net_ipv4']) > 0 || $formVars['net_ipv6'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "net_ipv4        = \"" . $formVars['net_ipv4']        . "\"," .
            "net_ipv6        = \"" . $formVars['net_ipv6']        . "\"," .
            "net_mask        =   " . $formVars['net_mask']        . "," .
            "net_zone        =   " . $formVars['net_zone']        . "," .
            "net_location    =   " . $formVars['net_location']    . "," .
            "net_vlan        = \"" . $formVars['net_vlan']        . "\"," . 
            "net_user        =   " . $_SESSION['uid']             . ",";
            "net_description = \"" . $formVars['net_description'] . "\"";
  
          if ($formVars['update'] == 0) {
            $query = "insert into network set net_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update network set " . $q_string . " where net_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['net_ipv6'] . "/" . $formVars['net_ipv6']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Network Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('network-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"network-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>Network Listing</strong></p>\n";

      $output .= "<p>This page lists both the IPv4 Networks and the IPv6 Networks which will be used when creating ";
      $output .= "IP Addresses.</p>\n";

      $output .= "<p>To add a new Network, click the <strong>Add Network</strong> button. This will bring up a dialog ";
      $output .= "box which you can then use to create a new network.</p>\n";

      $output .= "<p>To edit an existing Network, click on the entry in the listing. A dialog box will be displayed ";
      $output .= "where you can edit the current entry, or if there is a small difference, you can make changes and ";
      $output .= "add a new network.</p>\n";

      $output .= "<p>Note that under the Members column is a number which indicates the number of IP addresses that ";
      $output .= "are currently assigned to this network. You cannot remove a network until this value is zero. ";
      $output .= "Clicking on the number will take you to the IP Address editing screen where you can delete or change ";
      $output .= "the network for that IP address.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Network</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">IPv4 Network/Mask</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "  <th class=\"ui-state-default\">Network Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Location</th>\n";
      $output .= "  <th class=\"ui-state-default\">VLan</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Changed By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select net_id,net_ipv4,net_mask,zone_zone,loc_name,net_vlan,net_description,usr_first,usr_last,net_timestamp ";
      $q_string .= "from network ";
      $q_string .= "left join users on users.usr_id = network.net_user ";
      $q_string .= "left join net_zones on net_zones.zone_id = network.net_zone ";
      $q_string .= "left join locations on locations.loc_id = network.net_location ";
      $q_string .= "where net_ipv4 != '' ";
      $q_string .= "order by net_ipv4 ";
      $q_network = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_network) > 0) {
        while ($a_network = mysqli_fetch_array($q_network)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('network.fill.php?id=" . $a_network['net_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('network.del.php?id=" . $a_network['net_id'] . "');\">";
          $ipstart   = "<a href=\"ipaddress.php?network=" . $a_network['net_id'] . "\" target=\"_blank\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select count(ip_ipv4) ";
          $q_string .= "from ipaddress ";
          $q_string .= "where ip_network = " . $a_network['net_id'] . " ";
          $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            $a_ipaddress = mysqli_fetch_array($q_ipaddress);
            $total = $a_ipaddress['count(ip_ipv4)'];
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">" . $linkstart . $a_network['net_ipv4'] . "/" . $a_network['net_mask'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $ipstart . $total . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['zone_zone']       . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['loc_name']        . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['net_vlan']        . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['net_description'] . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['usr_first'] . " " . $a_network['usr_last'] . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['net_timestamp'] . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_network);

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Network</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">IPv6 Network/Mask</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "  <th class=\"ui-state-default\">Network Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Location</th>\n";
      $output .= "  <th class=\"ui-state-default\">VLan</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Changed By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select net_id,net_ipv6,net_mask,zone_zone,loc_name,net_vlan,net_description,usr_first,usr_last,net_timestamp ";
      $q_string .= "from network ";
      $q_string .= "left join users on users.usr_id = network.net_user ";
      $q_string .= "left join net_zones on net_zones.zone_id = network.net_zone ";
      $q_string .= "left join locations on locations.loc_id = network.net_location ";
      $q_string .= "where net_ipv6 != '' ";
      $q_string .= "order by net_ipv6 ";
      $q_network = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_network) > 0) {
        while ($a_network = mysqli_fetch_array($q_network)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('network.fill.php?id=" . $a_network['net_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('network.del.php?id=" . $a_network['net_id'] . "');\">";
          $ipstart   = "<a href=\"ipaddress.php?network=" . $a_network['net_id'] . "\" target=\"_blank\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select count(ip_ipv6) ";
          $q_string .= "from ipaddress ";
          $q_string .= "where ip_network = " . $a_network['net_id'] . " ";
          $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            $a_ipaddress = mysqli_fetch_array($q_ipaddress);
            $total = $a_ipaddress['count(ip_ipv6)'];
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">" . $linkstart . $a_network['net_ipv6'] . "/" . $a_network['net_mask'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $ipstart . $total . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['zone_zone']       . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['loc_name']        . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['net_vlan']        . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['net_description'] . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['usr_first'] . " " . $a_network['usr_last'] . "</td>";
          $output .= "  <td class=\"ui-widget-content\">" . $a_network['net_timestamp'] . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_network);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
