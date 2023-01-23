<?php
# Script: subzones.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "subzones.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                      10);
        $formVars['sub_name']         = clean($_GET['sub_name'],                50);
        $formVars['sub_zone']         = clean($_GET['sub_zone'],                10);
        $formVars['sub_description']  = clean($_GET['sub_description'],         50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['sub_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "sub_name              = \"" . $formVars['sub_name']        . "\"," .
            "sub_zone              =   " . $formVars['sub_zone']        . "," .
            "sub_user              =   " . $_SESSION['uid']             . "," .
            "sub_description       = \"" . $formVars['sub_description'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_sub_zones set sub_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_sub_zones set " . $q_string . " where sub_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sub_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete IP Address Zone</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Network Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">IP Address Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Created By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sub_id,sub_name,zone_zone,usr_first,usr_last,sub_timestamp,sub_description ";
      $q_string .= "from inv_sub_zones ";
      $q_string .= "left join inv_users     on inv_users.usr_id      = inv_sub_zones.sub_user ";
      $q_string .= "left join inv_net_zones on inv_net_zones.zone_id = inv_sub_zones.sub_zone ";
      $q_string .= "order by zone_zone,sub_name "; 
      $q_inv_sub_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_sub_zones) > 0) {
        while ($a_inv_sub_zones = mysqli_fetch_array($q_inv_sub_zones)) {

          $total = 0;
          $q_string  = "select ip_id,net_id ";
          $q_string .= "from inv_ipaddress ";
          $q_string .= "left join inv_network   on inv_network.net_id    = inv_ipaddress.ip_network ";
          $q_string .= "left join inv_net_zones on inv_net_zones.zone_id = inv_network.net_zone ";
          $q_string .= "where ip_subzone = " . $a_inv_sub_zones['sub_id'] . " ";
          $q_inv_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_ipaddress) > 0) {
            while ($a_inv_ipaddress = mysqli_fetch_array($q_inv_ipaddress)) {
              $total++;
            }
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('subzones.fill.php?id="  . $a_inv_sub_zones['sub_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('subzones.del.php?id=" . $a_inv_sub_zones['sub_id'] . "');\">";
          if ($total > 0) {
            $ipstart   = "<a href=\"ipaddress.php?network=" . $a_inv_ipaddress['net_id'] . "\" target=\"_blank\">";
          } else {
            $ipstart   = "";
          }
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>\n";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>\n";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_sub_zones['zone_zone'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_sub_zones['sub_name']             . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $ipstart   . $total                  . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_sub_zones['sub_description']      . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_sub_zones['usr_first'] . " " . $a_inv_sub_zones['usr_last'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_inv_sub_zones['sub_timestamp']         . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_inv_sub_zones);

      print "document.getElementById('table_mysql').innerHTML = '"   . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
