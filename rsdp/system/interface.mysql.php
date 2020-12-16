<?php
# Script: interface.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.mysql.php";
    $formVars['update']        = clean($_GET['update'],       10);
    $formVars['rsdp']          = clean($_GET['rsdp'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],           10);
        $formVars['if_mac']        = clean($_GET['if_mac'],       20);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['if_mac']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "if_rsdp =   " . $formVars['rsdp']    . "," . 
            "if_mac  = \"" . $formVars['if_mac']  . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into rsdp_interface set if_id = NULL, " . $q_string;
            $message = "Interface added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update rsdp_interface set " . $q_string . " where if_id = " . $formVars['id'];
            $message = "Interface updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

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
      $output .= "  <th class=\"ui-state-default\">Interface Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('interface-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"interface-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Interface Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an Interface to update the MAC Address.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Interface Management</strong> title bar to toggle the <strong>Interface Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "<th class=\"ui-state-default\">Interface Name</th>";
      $output .= "<th class=\"ui-state-default\">Name in DNS</th>";
      $output .= "<th class=\"ui-state-default\">Interface</th>";
      $output .= "<th class=\"ui-state-default\">Assigned IP/Netmask</th>";
      $output .= "<th class=\"ui-state-default\">Gateway</th>";
      $output .= "<th class=\"ui-state-default\">MAC</th>";
      $output .= "</tr>";

      $q_string  = "select if_id,if_name,if_interface,if_ip,if_gate,if_mask,if_mac ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = 0 ";
      $q_string .= "order by if_interface";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

        $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('interface.fill.php?id=" . $a_rsdp_interface['if_id'] . "');jQuery('#dialogInterface').dialog('open');\">";
        $linkend   = "</a>";

        $ip = gethostbyname($a_rsdp_interface['if_name']);
        if ($ip != $a_rsdp_interface['if_name']) {
          $indns = "Yes";
        } else {
          $indns = "No";
        }

        if ($a_rsdp_interface['if_mask'] > 0) {
          $mask = $a_rsdp_interface['if_ip'] . "/" . $a_rsdp_interface['if_mask'];
        } else {
          $mask = '';
        }

        $output .= "<tr>";
        $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_interface['if_name']      . $linkend . "</td>";
        $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $indns                            . $linkend . "</td>";
        $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_interface['if_interface'] . $linkend . "</td>";
        $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $mask                             . $linkend . "</td>";
        $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_interface['if_gate']      . $linkend . "</td>";
        $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_interface['if_mac']       . $linkend . "</td>";
        $output .= "</tr>";

        $q_string  = "select if_id,if_name,if_interface,if_ip,if_gate,if_mask,if_mac ";
        $q_string .= "from rsdp_interface ";
        $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
        $q_string .= "order by if_interface";
        $q_redundant = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_redundant = mysqli_fetch_array($q_redundant)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('interface.fill.php?id=" . $a_redundant['if_id'] . "');jQuery('#dialogInterface').dialog('open');\">";
          $linkend   = "</a>";

          $ip = gethostbyname($a_redundant['if_name']);
          if ($ip != $a_redundant['if_name']) {
            $indns = "Yes";
          } else {
            $indns = "No";
          }

          if ($a_redundant['if_mask'] > 0) {
            $mask = $a_redundant['if_ip'] . "/" . $a_redundant['if_mask'];
          } else {
            $mask = '';
          }

          $output .= "<tr>";
          $output .=   "<td class=\"ui-widget-content\">&gt; " . $linkstart . $a_redundant['if_name']      . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"      . $linkstart . $indns                       . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"      . $linkstart . $a_redundant['if_interface'] . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"      . $linkstart . $mask                        . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"      . $linkstart . $a_redundant['if_gate']      . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"      . $linkstart . $a_redundant['if_mac']       . $linkend . "</td>";
          $output .= "</tr>";
        }
      }

      $output .= "</table>";

      mysqli_free_result($q_rsdp_interface);

      print "document.getElementById('interface_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.getElementById('if_name').innerHTML = '';\n";
      print "document.getElementById('if_ip').innerHTML = '';\n";
      print "document.interface.if_mac.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
