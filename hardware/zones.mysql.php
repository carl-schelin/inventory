<?php
# Script: zones.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],               10);
        $formVars['zone_name']       = clean($_GET['zone_name'],        50);
        $formVars['zone_desc']       = clean($_GET['zone_desc'],        50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['zone_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "zone_name = \"" . $formVars['zone_name'] . "\"," .
            "zone_desc = \"" . $formVars['zone_desc'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into ip_zones set zone_id = NULL, " . $q_string;
            $message = "Network Zone added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update ip_zones set " . $q_string . " where zone_id = " . $formVars['id'];
            $message = "Network Zone updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['zone_name']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Zone Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('zone-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"zone-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Zone Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Network Zone to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Zone Management</strong> title bar to toggle the <strong>Zone Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel(1)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Network Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select zone_id,zone_name,zone_desc ";
      $q_string .= "from ip_zones ";
      $q_string .= "order by zone_name "; 
      $q_ip_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_ip_zones) > 0) {
        while ($a_ip_zones = mysql_fetch_array($q_ip_zones)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('zones.fill.php?id="  . $a_ip_zones['zone_id'] . "');showDiv('zone-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('zones.del.php?id=" . $a_ip_zones['zone_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel(1)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . 'x'                      . $linkend . "</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_ip_zones['zone_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_ip_zones['zone_name'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_ip_zones['zone_desc'] . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysql_free_result($q_ip_zones);

      print "document.getElementById('table_mysql').innerHTML = '"   . mysql_real_escape_string($output) . "';\n\n";

      print "document.zones.update.disabled = true;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
