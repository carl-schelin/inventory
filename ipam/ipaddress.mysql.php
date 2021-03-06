<?php
# Script: ipaddress.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "ipaddress.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);
    $formVars['network'] = clean($_GET['network'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    $net_name = "";
    $where = '';
    if ($formVars['network'] > 0) {
      $where = "and net_id = " . $formVars['network'] . " ";

# get the passed network for the title bar.
      $q_string  = "select net_ipv4,net_ipv6,net_mask,zone_zone ";
      $q_string .= "from network ";
      $q_string .= "left join net_zones on net_zones.zone_id = network.net_zone ";
      $q_string .= "where net_id = " . $formVars['network'] . " ";
      $q_network = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_network) > 0) {
        $a_network = mysqli_fetch_array($q_network);

        if (strlen($a_network['net_ipv4']) == 0) {
          $net_name = " for " . $a_network['net_ipv6'] . "/" . $a_network['net_mask'] . " " . $a_network['zone_zone'] . " Zone";
        } else {
          $net_name = " for " . $a_network['net_ipv4'] . "/" . $a_network['net_mask'] . " " . $a_network['zone_zone'] . " Zone";
        }
      } else {
        $net_name = "";
      }
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],             10);
        $formVars['ip_ipv4']         = clean($_GET['ip_ipv4'],        20);
        $formVars['ip_ipv6']         = clean($_GET['ip_ipv6'],        50);
        $formVars['ip_hostname']     = clean($_GET['ip_hostname'],   100);
        $formVars['ip_domain']       = clean($_GET['ip_domain'],     255);
        $formVars['ip_network']      = clean($_GET['ip_network'],     10);
        $formVars['ip_subzone']      = clean($_GET['ip_subzone'],     10);
        $formVars['ip_type']         = clean($_GET['ip_type'],        10);
        $formVars['ip_description']  = clean($_GET['ip_description'], 50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['ip_subzone'] == '') {
          $formVars['ip_subzone'] = 0;
        }
        if ($formVars['ip_type'] == '') {
          $formVars['ip_type'] = 0;
        }

# see if the IP is already taken and report an error if so
        $assigned = 'No';
        if ($formVars['update'] == 0) {
          $q_string  = "select ip_ipv4,ip_hostname ";
          $q_string .= "from ipaddress ";
          $q_string .= "where ip_ipv4 != \"\" and ip_ipv4 = \"" . $formVars['ip_ipv4'] . "\" ";
          $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            $a_ipaddress = mysqli_fetch_array($q_ipaddress);
            print "alert('IPv4 " . $formVars['ip_ipv4'] . " has already been assigned to " . $a_ipaddress['ip_hostname'] . ".');";
            $assigned = 'Yes';
          }
 
          $q_string  = "select ip_ipv6,ip_hostname ";
          $q_string .= "from ipaddress ";
          $q_string .= "where ip_ipv6 != \"\" and ip_ipv6 = \"" . $formVars['ip_ipv6'] . "\" ";
          $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            $a_ipaddress = mysqli_fetch_array($q_ipaddress);
            print "alert('IPv6 " . $formVars['ip_ipv6'] . " has already been assigned to " . $a_ipaddress['ip_hostname'] . ".');";
            $assigned = 'Yes';
          }
        }

        if ($assigned == 'No') {
          if (strlen($formVars['ip_ipv4']) > 0 || strlen($formVars['ip_ipv6']) > 0) {
            logaccess($db, $_SESSION['uid'], $package, "Building the query.");

            $q_string =
              "ip_ipv4          = \"" . $formVars['ip_ipv4']           . "\"," .
              "ip_ipv6          = \"" . $formVars['ip_ipv6']           . "\"," .
              "ip_hostname      = \"" . $formVars['ip_hostname']       . "\"," .
              "ip_domain        = \"" . $formVars['ip_domain']         . "\"," .
              "ip_network       =   " . $formVars['ip_network']        . "," . 
              "ip_subzone       =   " . $formVars['ip_subzone']        . "," . 
              "ip_type          =   " . $formVars['ip_type']           . "," . 
              "ip_user          =   " . $_SESSION['uid']               . "," . 
              "ip_description   = \"" . $formVars['ip_description']    . "\"";
  
            if ($formVars['update'] == 0) {
              $query = "insert into ipaddress set ip_id = NULL, " . $q_string;
            }
            if ($formVars['update'] == 1) {
              $query = "update ipaddress set " . $q_string . " where ip_id = " . $formVars['id'];
            }

            logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ip_ipv4'] . "/" . $formVars['ip_ipv6']);

            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          } else {
            print "alert('You must input data before saving changes.');\n";
          }
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">IP Address Listing" . $net_name . "</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('ipaddress-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"ipaddress-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>IP Address Listing</strong></p>\n";

      $output .= "<p>This page lists all of the IPv4 or IPv6 IP Addresses associated with the network.</p>\n";

      $output .= "<p>To add a new IP Address, click on the <strong>Add IP Address</strong> button. This will bring up a dialog ";
      $output .= "box which you can use to add a new IP Address.</p>\n";

      $output .= "<p>If an entered IP Address already exists, you will be alerted and the existing line will be <span class=\"ui-state-error\">highlighted</span>. ";
      $output .= "Either enter a different IP Address or edit the existing one.</p>\n";

      $output .= "<p>Note that you should only fill out one of the fields. The default is the IPv4 Address field. If that is ";
      $output .= "filled in, the IPv6 field will be cleared before saving.</p>\n";

      $output .= "<p>To edit an existing IP Address, click on the entry in the listing. A dialog box will be displayed where you ";
      $output .= "can edit the current entry, or if there is a small difference, you can make changes and add a new IP Address</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete IP Address</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">IPv4 Address/Mask</th>\n";
      $output .= "  <th class=\"ui-state-default\">Hostname</th>\n";
      $output .= "  <th class=\"ui-state-default\">IP Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Type</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Created By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ip_id,ip_ipv4,ip_hostname,ip_domain,net_mask,ip_type,usr_first,usr_last,ip_timestamp,ip_description,sub_name ";
      $q_string .= "from ipaddress ";
      $q_string .= "left join users on users.usr_id = ipaddress.ip_user ";
      $q_string .= "left join sub_zones on sub_zones.sub_id = ipaddress.ip_subzone ";
      $q_string .= "left join network  on network.net_id = ipaddress.ip_network ";
      $q_string .= "left join net_zones on net_zones.zone_id = network.net_zone ";
      $q_string .= "where ip_ipv4 != '' " . $where;
      $q_string .= "order by ip_ipv4 ";
      $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_ipaddress) > 0) {
        while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('ipaddress.fill.php?id="  . $a_ipaddress['ip_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('ipaddress.del.php?id=" . $a_ipaddress['ip_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select ip_name ";
          $q_string .= "from ip_types ";
          $q_string .= "where ip_id = " . $a_ipaddress['ip_type'] . " ";
          $q_ip_types = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ip_types) > 0) {
            $a_ip_types = mysqli_fetch_array($q_ip_types);
          } else {
            $a_ip_types['ip_name'] = "Unassigned";
          }

          $class = 'ui-widget-content';
          if ($assigned == 'Yes' && $a_ipaddress['ip_ipv4'] == $formVars['ip_ipv4']) {
            $class = 'ui-state-error';
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
          }
          $output .= "  <td class=\"" . $class . "\">" . $linkstart . $a_ipaddress['ip_ipv4'] . "/" . $a_ipaddress['net_mask'] . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_hostname'] . "." . $a_ipaddress['ip_domain'] . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['sub_name']           . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ip_types['ip_name']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_description']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['usr_first'] . " " . $a_ipaddress['usr_last'] . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_timestamp']             . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_ipaddress);

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete IP Address</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">IPv6 Address/Mask</th>\n";
      $output .= "  <th class=\"ui-state-default\">Hostname</th>\n";
      $output .= "  <th class=\"ui-state-default\">IP Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Type</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Created By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ip_id,ip_ipv6,ip_hostname,ip_domain,net_mask,ip_type,usr_first,usr_last,ip_timestamp,ip_description,sub_name ";
      $q_string .= "from ipaddress ";
      $q_string .= "left join users  on users.usr_id = ipaddress.ip_user ";
      $q_string .= "left join sub_zones  on sub_zones.sub_id = ipaddress.ip_subzone ";
      $q_string .= "left join network  on network.net_id = ipaddress.ip_network ";
      $q_string .= "where ip_ipv6 != '' " . $where;
      $q_string .= "order by ip_ipv6 ";
      $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_ipaddress) > 0) {
        while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('ipaddress.fill.php?id="  . $a_ipaddress['ip_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('ipaddress.del.php?id=" . $a_ipaddress['ip_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select ip_name ";
          $q_string .= "from ip_types ";
          $q_string .= "where ip_id = " . $a_ipaddress['ip_type'] . " ";
          $q_ip_types = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ip_types) > 0) {
            $a_ip_types = mysqli_fetch_array($q_ip_types);
          } else {
            $a_ip_types['ip_name'] = "Unassigned";
          }

          $class = 'ui-widget-content';
          if ($assigned == 'Yes' && $a_ipaddress['ip_ipv6'] == $formVars['ip_ipv6']) {
            $class = 'ui-state-error';
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
          }
          $output .= "  <td class=\"" . $class . "\">" . $linkstart . $a_ipaddress['ip_ipv6'] . "/" . $a_ipaddress['net_mask'] . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_hostname'] . "." . $a_ipaddress['ip_domain'] . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['sub_name']          . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ip_types['ip_name']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_description']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['usr_first'] . " " . $a_ipaddress['usr_last'] . "</td>";
          $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_timestamp']             . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_ipaddress);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
