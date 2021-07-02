<?php
# Script: zones.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "zones.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                      10);
        $formVars['zone_zone']        = clean($_GET['zone_zone'],               10);
        $formVars['zone_acronym']     = clean($_GET['zone_acronym'],             5);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['zone_zone']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "zone_zone              = \"" . $formVars['zone_zone']        . "\"," .
            "zone_acronym           = \"" . $formVars['zone_acronym']     . "\"," .
            "zone_user              =   " . $_SESSION['uid'];

          if ($formVars['update'] == 0) {
            $query = "insert into net_zones set zone_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update net_zones set " . $q_string . " where zone_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['zone_zone']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Network Zone Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('zone-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"zone-listing-help\" style=\"display:none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>Network Zone Listing</strong></p>\n";

      $output .= "<p>This page lists all the currently defined network zones.</p>\n";

      $output .= "<p>To add a new Network Zone, click the <strong>Add Network Zone</strong> ";
      $output .= "button on the upper right. A dialog box will be displayed that will let you ";
      $output .= "enter the necessary information to create a new Network Zone listing.</p>\n";

      $output .= "<p>If you want to edit an existing Network Zone, click the entry in the listing. ";
      $output .= "This will bring up a dialog box where you can edit the current listing or, if you ";
      $output .= "have a Network Zone with just a minor change, you can edit it and save it as a new ";
      $output .= "listing.</p>\n";

      $output .= "<p>As a note, the Zone Acronym can be used if your hostnames are created using ";
      $output .= "Network Zone as part of the hostname.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Network Zone</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Network Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "  <th class=\"ui-state-default\">Zone Acronym</th>\n";
      $output .= "  <th class=\"ui-state-default\">Created By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select zone_id,zone_zone,zone_acronym,usr_first,usr_last,zone_timestamp ";
      $q_string .= "from net_zones ";
      $q_string .= "left join users on users.usr_id = zone_user ";
      $q_string .= "order by zone_zone "; 
      $q_net_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_net_zones) > 0) {
        while ($a_net_zones = mysqli_fetch_array($q_net_zones)) {

          $total = 0;
          $q_string  = "select ip_id,net_id ";
          $q_string .= "from ipaddress ";
          $q_string .= "left join network on network.net_id = ipaddress.ip_network ";
          $q_string .= "where net_zone = " . $a_net_zones['zone_id'] . " ";
          $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {
              $total++;
            }
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('zones.fill.php?id="  . $a_net_zones['zone_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('zones.del.php?id=" . $a_net_zones['zone_id'] . "');\">";
          if ($total > 0) {
            $ipstart   = "<a href=\"ipaddress.php?network=" . $a_ipaddress['net_id'] . "\" target=\"_blank\">";
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
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_net_zones['zone_zone'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"   . $ipstart   . $total                    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"          . $a_net_zones['zone_acronym']     . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"          . $a_net_zones['usr_first'] . " " . $a_net_zones['usr_last'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"          . $a_net_zones['zone_timestamp'] . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_net_zones);

      print "document.getElementById('table_mysql').innerHTML = '"   . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
