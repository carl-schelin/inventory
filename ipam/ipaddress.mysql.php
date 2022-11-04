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
    $formVars['update']  = clean($_GET['update'],   10);
    $formVars['network'] = clean($_GET['network'],  10);
    $formVars['sort']    = clean($_GET['sort'],     50);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    $range = array();
    $where = '';
    if ($formVars['network'] > 0) {
      $where = "and net_id = " . $formVars['network'] . " ";

# get the passed network range. You can't get here without it so it'll always be > 0
      $ipv4 = 0;
      $ipv6 = 0;
      $q_string  = "select net_ipv4,net_ipv6,net_mask,zone_zone ";
      $q_string .= "from network ";
      $q_string .= "left join net_zones on net_zones.zone_id = network.net_zone ";
      $q_string .= "where net_id = " . $formVars['network'] . " ";
      $q_network = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_network) > 0) {
        $a_network = mysqli_fetch_array($q_network);

        if (strlen($a_network['net_ipv4']) == 0) {
          $ipv6 = 1;
        } else {
          $ipv4 = 1;
          $range = ipRange($a_network['net_ipv4'] . "/" . $a_network['net_mask']);

          $startip = ip2long($range[0]);
          $endip   = ip2long($range[1]);
          $count = $endip - $startip;
        }
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
        $formVars['ip_notes']        = clean($_GET['ip_notes'],     1000);

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
          $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            $a_ipaddress = mysqli_fetch_array($q_ipaddress);
            print "alert('IPv4 " . $formVars['ip_ipv4'] . " has already been assigned to " . $a_ipaddress['ip_hostname'] . ".');";
            $assigned = 'Yes';
          }
 
          $q_string  = "select ip_ipv6,ip_hostname ";
          $q_string .= "from ipaddress ";
          $q_string .= "where ip_ipv6 != \"\" and ip_ipv6 = \"" . $formVars['ip_ipv6'] . "\" ";
          $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
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
              "ip_description   = \"" . $formVars['ip_description']    . "\"," . 
              "ip_notes         = \"" . $formVars['ip_notes']          . "\"";
  
            if ($formVars['update'] == 0) {
              $q_string = "insert into ipaddress set ip_id = NULL, " . $q_string;
            }
            if ($formVars['update'] == 1) {
              $q_string = "update ipaddress set " . $q_string . " where ip_id = " . $formVars['id'];
            }

            logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ip_ipv4'] . "/" . $formVars['ip_ipv6']);

            $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          } else {
            print "alert('You must input data before saving changes.');\n";
          }
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $passthrough = "&network=" . $formVars['network'] . " ";

      if ($ipv4) {
        $orderv4 = "ip_ipv4";
        if ($formVars['sort'] != '') {
          $orderv4 = $formVars['sort'];
        }
        if ($formVars['sort'] == 'ip_ipv6') {
          $orderv4 = "ip_ipv4";
        }

# show listing
        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        if (check_userlevel($db, $AL_Admin)) {
          $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete IP Address</th>\n";
        }
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_ipv4"            . $passthrough . "\">IPv4 Address/Mask</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_hostname"        . $passthrough . "\">Hostname</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=sub_name"           . $passthrough . "\">IP Zone</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_type"            . $passthrough . "\">Type</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_description"     . $passthrough . "\">Description</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">Notes</th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=usr_last,usr_first" . $passthrough . "\">Created By</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_timestamp"       . $passthrough . "\">Date</a></th>\n";
        $output .= "</tr>\n";

        for ($i = $startip + 1; $i < $endip; $i++) {

          $ipaddr = long2ip($i);

          $q_string  = "select ip_id,ip_ipv4,ip_hostname,ip_domain,net_mask,ip_type,usr_first,usr_last,ip_timestamp,ip_description,ip_notes,sub_name ";
          $q_string .= "from ipaddress ";
          $q_string .= "left join inv_users on inv_users.usr_id = ipaddress.ip_user ";
          $q_string .= "left join inv_sub_zones on inv_sub_zones.sub_id = ipaddress.ip_subzone ";
          $q_string .= "left join network  on network.net_id = ipaddress.ip_network ";
          $q_string .= "left join net_zones on net_zones.zone_id = network.net_zone ";
          $q_string .= "where ip_ipv4 = \"" . $ipaddr . "\" ";
          $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            $a_ipaddress = mysqli_fetch_array($q_ipaddress);

            $linkstart = "<a href=\"#\" onclick=\"show_file('ipaddress.fill.php?id="  . $a_ipaddress['ip_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
            $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('ipaddress.del.php?id=" . $a_ipaddress['ip_id'] . "');\">";
            $linkend   = "</a>";

            $q_string  = "select ip_name ";
            $q_string .= "from ip_types ";
            $q_string .= "where ip_id = " . $a_ipaddress['ip_type'] . " ";
            $q_ip_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_ip_types) > 0) {
              $a_ip_types = mysqli_fetch_array($q_ip_types);
            } else {
              $a_ip_types['ip_name'] = "Unassigned";
            }

            if (strlen($iprange) > 0) {

              if (strlen($ipendrange) > 0) {
                $iprange .= "-" . $ipendrange;
              }

              $class = 'ui-state-highlight';
              $output .= "<tr>";
              if (check_userlevel($db, $AL_Admin)) {
                $output .= "  <td class=\"" . $class . " delete\">--</td>\n";
              }
              $output .= "  <td class=\"" . $class . "\">" . $iprange     . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . "Unassigned" . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
              $output .= "</tr>";

              $iprange = '';
              $ipendrange = '';
            }

            $class = 'ui-widget-content';
            if ($assigned == 'Yes' && $a_ipaddress['ip_ipv4'] == $formVars['ip_ipv4']) {
              $class = 'ui-state-error';
            }

            $notes = 'No';
            if (strlen($a_ipaddress['ip_notes']) > 0) {
              $notes = 'Yes';
            }

            $output .= "<tr>";
            if (check_userlevel($db, $AL_Admin)) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            }
            $output .= "  <td class=\"" . $class . "\">" . $linkstart . $ipaddr                                            . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_hostname'] . "." . $a_ipaddress['ip_domain'] . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['sub_name']                                      . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ip_types['ip_name']                                        . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_description']                                . "</td>\n";
            $output .= "  <td class=\"" . $class . "\" title=\"Notes: " . $a_ipaddress['ip_notes'] . "\">" . $notes                   . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['usr_first'] . " " . $a_ipaddress['usr_last']    . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_timestamp']                                  . "</td>\n";
            $output .= "</tr>";

          } else {
            if ($iprange == '') {
              $iprange = $ipaddr;
            } else {
              $ipendrange = $ipaddr;
            }

          }
        }

        if (strlen($iprange) > 0) {

          if (strlen($ipendrange) > 0) {
            $iprange .= "-" . $ipendrange;
          }

          $class = 'ui-state-highlight';
          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\">--</td>\n";
          }
          $output .= "  <td class=\"" . $class . "\">" . $iprange     . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . "Unassigned" . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . "&nbsp;"     . "</td>\n";
          $output .= "</tr>";

          $iprange = '';
        }

        $output .= "</table>";

        mysqli_free_result($q_ipaddress);

        print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
      }


      if ($ipv6) {
        $orderv6 = "ip_ipv6";
        if ($formVars['sort'] != '') {
          $orderv6 = $formVars['sort'];
        }
        if ($formVars['sort'] == 'ip_ipv4') {
          $orderv6 = "ip_ipv6";
        }

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        if (check_userlevel($db, $AL_Admin)) {
          $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete IP Address</th>\n";
        }
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_ipv6"            . $passthrough . "\">IPv6 Address/Mask</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_hostname"        . $passthrough . "\">Hostname</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=sub_name"           . $passthrough . "\">IP Zone</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_type"            . $passthrough . "\">Type</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_description"     . $passthrough . "\">Description</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">Notes</th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=usr_last,usr_first" . $passthrough . "\">Created By</a></th>\n";
        $output .= "  <th class=\"ui-state-default\"><a href=\"ipaddress.php?sort=ip_timestamp"       . $passthrough . "\">Date</a></th>\n";
        $output .= "</tr>\n";

        $q_string  = "select ip_id,ip_ipv6,ip_hostname,ip_domain,net_mask,ip_type,usr_first,usr_last,ip_timestamp,ip_description,ip_notes,sub_name ";
        $q_string .= "from ipaddress ";
        $q_string .= "left join inv_users  on inv_users.usr_id = ipaddress.ip_user ";
        $q_string .= "left join inv_sub_zones  on inv_sub_zones.sub_id = ipaddress.ip_subzone ";
        $q_string .= "left join network  on network.net_id = ipaddress.ip_network ";
        $q_string .= "where ip_ipv6 != '' " . $where;
        $q_string .= "order by " . $orderv6 . " ";
        $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_ipaddress) > 0) {
          while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {

            $linkstart = "<a href=\"#\" onclick=\"show_file('ipaddress.fill.php?id="  . $a_ipaddress['ip_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
            $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('ipaddress.del.php?id=" . $a_ipaddress['ip_id'] . "');\">";
            $linkend   = "</a>";

            $q_string  = "select ip_name ";
            $q_string .= "from ip_types ";
            $q_string .= "where ip_id = " . $a_ipaddress['ip_type'] . " ";
            $q_ip_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_ip_types) > 0) {
              $a_ip_types = mysqli_fetch_array($q_ip_types);
            } else {
              $a_ip_types['ip_name'] = "Unassigned";
            }

            $class = 'ui-widget-content';
            if ($assigned == 'Yes' && $a_ipaddress['ip_ipv6'] == $formVars['ip_ipv6']) {
              $class = 'ui-state-error';
            }

            $notes = 'No';
            if (strlen($a_ipaddress['ip_notes']) > 0) {
              $notes = 'Yes';
            }

            $output .= "<tr>";
            if (check_userlevel($db, $AL_Admin)) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            }
            $output .= "  <td class=\"" . $class . "\">" . $linkstart . $a_ipaddress['ip_ipv6'] . "/" . $a_ipaddress['net_mask'] . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_hostname'] . "." . $a_ipaddress['ip_domain']       . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['sub_name']                                            . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ip_types['ip_name']                                              . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_description']                                      . "</td>\n";
            $output .= "  <td class=\"" . $class . "\" title=\"Notes: " . $ip_address['ip_notes'] . "\">" . $notes                          . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['usr_first'] . " " . $a_ipaddress['usr_last']          . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $a_ipaddress['ip_timestamp']                                        . "</td>\n";
            $output .= "</tr>";
          }
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No records found.</td>\n";
          $output .= "</tr>\n";
        }

        $output .= "</table>";

        mysqli_free_result($q_ipaddress);

        print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
