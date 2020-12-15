<?php
# Script: vlans.mysql.php
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
    $package = "vlans.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                10);
        $formVars['vlan_vlan']        = clean($_GET['vlan_vlan'],         10);
        $formVars['vlan_zone']        = clean($_GET['vlan_zone'],         10);
        $formVars['vlan_name']        = clean($_GET['vlan_name'],        100);
        $formVars['vlan_description'] = clean($_GET['vlan_description'], 100);
        $formVars['vlan_range']       = clean($_GET['vlan_range'],        35);
        $formVars['vlan_gateway']     = clean($_GET['vlan_gateway'],      20);
        $formVars['vlan_netmask']     = clean($_GET['vlan_netmask'],      16);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
    
        if (strlen($formVars['vlan_vlan']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "vlan_vlan        = \"" . $formVars['vlan_vlan']        . "\"," .
            "vlan_zone        = \"" . $formVars['vlan_zone']        . "\"," .
            "vlan_name        = \"" . $formVars['vlan_name']        . "\"," .
            "vlan_description = \"" . $formVars['vlan_description'] . "\"," . 
            "vlan_range       = \"" . $formVars['vlan_range']       . "\"," .
            "vlan_gateway     = \"" . $formVars['vlan_gateway']     . "\"," .
            "vlan_netmask     = \"" . $formVars['vlan_netmask']     . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into vlans set vlan_id = NULL, " . $q_string;
            $message = "VLan added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update vlans set " . $q_string . " where vlan_id = " . $formVars['id'];
            $message = "VLan updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['vlan_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">VLan Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('vlan-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"vlan-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>VLan Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a vlan to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>VLan Management</strong> title bar to toggle the <strong>VLan Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">VLAN</th>\n";
      $output .= "  <th class=\"ui-state-default\">Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">IP</th>\n";
      $output .= "  <th class=\"ui-state-default\">Netmask</th>\n";
      $output .= "  <th class=\"ui-state-default\">Gateway</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select vlan_id,vlan_vlan,vlan_zone,vlan_name,vlan_description,vlan_range,vlan_gateway,vlan_netmask ";
      $q_string .= "from vlans ";
      $q_string .= "order by vlan_zone+0 asc,vlan_vlan";
      $q_vlans = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_vlans = mysqli_fetch_array($q_vlans)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('vlans.fill.php?id="  . $a_vlans['vlan_id'] . "');showDiv('vlan-hide');\">";
        $linkdel   = "<a href=\"#\" onclick=\"delete_line('vlans.del.php?id=" . $a_vlans['vlan_id'] . "');\">";
        $linkend   = "</a>";

        $output .= "<tr>";
        if (check_userlevel($db, $AL_Admin)) {
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "x"                          . $linkend . "</td>";
        }
        $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_vlans['vlan_id']          . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_vlans['vlan_vlan']        . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_vlans['vlan_zone']        . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_vlans['vlan_name']        . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_vlans['vlan_description'] . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_vlans['vlan_range']       . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_vlans['vlan_netmask']     . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_vlans['vlan_gateway']     . $linkend . "</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_vlans);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.vlans.update.disabled = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
