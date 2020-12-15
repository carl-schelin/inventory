<?php
# Script: firewall.mysql.php
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
    $package = "firewall.mysql.php";
    $formVars['update']             = clean($_GET['update'],             10);
    $formVars['fw_companyid']       = clean($_GET['fw_companyid'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['fw_companyid'] == '') {
      $formVars['fw_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                 = clean($_GET['id'],                 10);
        $formVars['fw_source']          = clean($_GET['fw_source'],          20);
        $formVars['fw_sourcezone']      = clean($_GET['fw_sourcezone'],      10);
        $formVars['fw_destination']     = clean($_GET['fw_destination'],     20);
        $formVars['fw_destinationzone'] = clean($_GET['fw_destinationzone'], 10);
        $formVars['fw_port']            = clean($_GET['fw_port'],            50);
        $formVars['fw_protocol']        = clean($_GET['fw_protocol'],        10);
        $formVars['fw_description']     = clean($_GET['fw_description'],    255);
        $formVars['fw_timeout']         = clean($_GET['fw_timeout'],         10);
        $formVars['fw_ticket']          = clean($_GET['fw_ticket'],          20);
        $formVars['fw_portdesc']        = clean($_GET['fw_portdesc'],        50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['fw_timeout'] == '') {
          $formVars['fw_timeout'] = 30;
        }

        if (strlen($formVars['fw_source']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "fw_companyid       =   " . $formVars['fw_companyid']       . "," .
            "fw_source          = \"" . $formVars['fw_source']          . "\"," .
            "fw_sourcezone      = \"" . $formVars['fw_sourcezone']      . "\"," .
            "fw_destination     = \"" . $formVars['fw_destination']     . "\"," .
            "fw_destinationzone = \"" . $formVars['fw_destinationzone'] . "\"," .
            "fw_port            = \"" . $formVars['fw_port']            . "\"," .
            "fw_protocol        = \"" . $formVars['fw_protocol']        . "\"," .
            "fw_description     = \"" . $formVars['fw_description']     . "\"," .
            "fw_timeout         =   " . $formVars['fw_timeout']         . "," .
            "fw_ticket          = \"" . $formVars['fw_ticket']          . "\"," . 
            "fw_portdesc        = \"" . $formVars['fw_portdesc']        . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into firewall set fw_id = NULL," . $q_string;
            $message = "Firewall Rule added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update firewall set " . $q_string . " where fw_id = " . $formVars['id'];
            $message = "Firewall Rule updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fw_source']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -2) {
        $formVars['copyfrom']           = clean($_GET['copyfrom'],           10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select fw_source,fw_sourcezone,fw_destination,fw_destinationzone,fw_port,";
          $q_string .= "fw_protocol,fw_description,fw_timeout,fw_ticket,fw_portdesc ";
          $q_string .= "from firewall ";
          $q_string .= "where fw_companyid = " . $formVars['copyfrom'];
          $q_firewall = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_firewall = mysqli_fetch_array($q_firewall)) {

            $q_string =
              "fw_companyid       =   " . $formVars['fw_companyid']         . "," .
              "fw_source          = \"" . $a_firewall['fw_source']          . "\"," .
              "fw_sourcezone      = \"" . $a_firewall['fw_sourcezone']      . "\"," .
              "fw_destination     = \"" . $a_firewall['fw_destination']     . "\"," .
              "fw_destinationzone = \"" . $a_firewall['fw_destinationzone'] . "\"," .
              "fw_port            = \"" . $a_firewall['fw_port']            . "\"," .
              "fw_protocol        = \"" . $a_firewall['fw_protocol']        . "\"," .
              "fw_description     = \"" . $a_firewall['fw_description']     . "\"," . 
              "fw_timeout         =   " . $a_firewall['fw_timeout']         . "," . 
              "fw_ticket          = \"" . $a_firewall['fw_ticket']          . "\"," . 
              "fw_portdesc        = \"" . $a_firewall['fw_portdesc']        . "\"";

            $query = "insert into firewall set fw_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"fw_refresh\" value=\"Refresh Firewall Listing\" onClick=\"javascript:attach_firewall('firewall.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"fw_update\"  value=\"Update Firewall Rule\"     onClick=\"javascript:attach_firewall('firewall.mysql.php', 1);hideDiv('firewall-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"fw_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"fw_addbtn\"  value=\"Add Firewall Rule\"        onClick=\"javascript:attach_firewall('firewall.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\" value=\"Copy Firewall Rules From:\" onClick=\"javascript:attach_firewall('firewall.mysql.php', -2);\">\n";
        $output .= "<select name=\"fw_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $q_string  = "select fw_id ";
          $q_string .= "from firewall ";
          $q_string .= "where fw_companyid = " . $a_inventory['inv_id'] . " ";
          $q_firewall = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $fw_total = mysqli_num_rows($q_firewall);

          if ($fw_total > 0) {
            $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . " (" . $fw_total . ")</option>\n";
          }
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"3\">Server Firewall Rules Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Source IP: <input type=\"text\" name=\"fw_source\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Source Zone: <select name=\"fw_sourcezone\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select zone_id,zone_name ";
        $q_string .= "from ip_zones ";
        $q_string .= "order by zone_name";
        $q_ip_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
          $output .= "<option value=\"" . $a_ip_zones['zone_id'] . "\">" . $a_ip_zones['zone_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Transport Protocol <input type=\"text\" name=\"fw_protocol\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Destination IP: <input type=\"text\" name=\"fw_destination\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Destination Zone: <select name=\"fw_destinationzone\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select zone_id,zone_name ";
        $q_string .= "from ip_zones ";
        $q_string .= "order by zone_name";
        $q_ip_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
          $output .= "<option value=\"" . $a_ip_zones['zone_id'] . "\">" . $a_ip_zones['zone_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Firewall Timeout: <input type=\"number\" name=\"fw_timeout\" value=\"30\" size=\"5\"> Minutes</td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Destination Port <input type=\"text\" name=\"fw_port\" size=\"10\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Port Desc: <input type=\"text\" name=\"fw_portdesc\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">InfoSec/Network Engineering Ticket: <input type=\"text\" name=\"fw_ticket\" size=\"10\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">Notes <input type=\"text\" name=\"fw_description\" size=\"70\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('firewall_form').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select zone_id,zone_name ";
      $q_string .= "from ip_zones";
      $q_ip_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
        $zone[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
      }

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Firewall Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('firewall-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"firewall-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Firewall Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Delete this firewall rule.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a firewall rule to bring up the form and edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Firewall Management</strong> title bar to toggle the <strong>Firewall Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Source</th>\n";
      $output .=   "<th class=\"ui-state-default\">Zone</th>\n";
      $output .=   "<th class=\"ui-state-default\">Destination</th>\n";
      $output .=   "<th class=\"ui-state-default\">Zone</th>\n";
      $output .=   "<th class=\"ui-state-default\">Port</th>\n";
      $output .=   "<th class=\"ui-state-default\">Desc</th>\n";
      $output .=   "<th class=\"ui-state-default\">Protocol</th>\n";
      $output .=   "<th class=\"ui-state-default\">Timeout</th>\n";
      $output .=   "<th class=\"ui-state-default\">Ticket</th>\n";
      $output .=   "<th class=\"ui-state-default\">Notes</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select fw_id,fw_source,fw_sourcezone,fw_destination,fw_destinationzone,";
      $q_string .= "fw_port,fw_protocol,fw_description,fw_timeout,fw_ticket,fw_portdesc,inv_manager ";
      $q_string .= "from firewall ";
      $q_string .= "left join inventory on inventory.inv_id = firewall.fw_companyid ";
      $q_string .= "where fw_companyid = " . $formVars['fw_companyid'] . " ";
      $q_string .= "order by fw_source,fw_destination,fw_port";
      $q_firewall = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_firewall) > 0) {
        while ($a_firewall = mysqli_fetch_array($q_firewall)) {

          if (filter_var($a_firewall['fw_source'], FILTER_VALIDATE_IP)) {
            $sourcevalid = ' class="ui-widget-content"';
          } else {
            $sourcevalid = ' class="ui-state-error"';
          }

          if (filter_var($a_firewall['fw_destination'], FILTER_VALIDATE_IP)) {
            $destinationvalid = ' class="ui-widget-content"';
          } else {
            $destinationvalid = ' class="ui-state-error"';
          }

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('firewall.fill.php?id="  . $a_firewall['fw_id'] . "');showDiv('firewall-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_firewall('firewall.del.php?id=" . $a_firewall['fw_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                                         . "</td>\n";
          $output .= "  <td" . $sourcevalid . ">"                . $linkstart . $a_firewall['fw_source']                 . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $zone[$a_firewall['fw_sourcezone']]      . $linkend . "</td>\n";
          $output .= "  <td" . $destinationvalid . ">"           . $linkstart . $a_firewall['fw_destination']            . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $zone[$a_firewall['fw_destinationzone']] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_firewall['fw_port']                   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_firewall['fw_portdesc']               . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_firewall['fw_protocol']               . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_firewall['fw_timeout']                . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_firewall['fw_ticket']                 . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_firewall['fw_description']            . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No firewall rules defined.</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n";

      $output .= "<p><p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "<th class=\"ui-state-default\" colspan=\"11\">Related Firewall Rules</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "<th class=\"ui-state-default\">Server</th>\n";
      $output .= "<th class=\"ui-state-default\">Source</th>\n";
      $output .= "<th class=\"ui-state-default\">Zone</th>\n";
      $output .= "<th class=\"ui-state-default\">Destination</th>\n";
      $output .= "<th class=\"ui-state-default\">Zone</th>\n";
      $output .= "<th class=\"ui-state-default\">Port</th>\n";
      $output .= "<th class=\"ui-state-default\">Desc</th>\n";
      $output .= "<th class=\"ui-state-default\">Protocol</th>\n";
      $output .= "<th class=\"ui-state-default\">Timeout</th>\n";
      $output .= "<th class=\"ui-state-default\">Ticket</th>\n";
      $output .= "<th class=\"ui-state-default\">Notes</th>\n";
      $output .= "</tr>\n";

#get the list of IPs for the current server
#loop through the firewall rules looking for the ip
#if found, display output

      $count = 0;
      $q_string  = "select int_addr,inv_name ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "where int_companyid = " . $formVars['fw_companyid'];
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        $q_string  = "select fw_id,fw_source,fw_sourcezone,fw_destination,fw_destinationzone,";
        $q_string .= "fw_port,fw_protocol,fw_timeout,fw_ticket,fw_description,fw_portdesc,inv_name ";
        $q_string .= "from firewall ";
        $q_string .= "left join inventory on inventory.inv_id = firewall.fw_companyid ";
        $q_string .= "where fw_source = '" . $a_interface['int_addr'] . "' or fw_destination = '" . $a_interface['int_addr'] . "' ";
        $q_string .= "order by fw_source,fw_destination,fw_port";
        $q_firewall = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_firewall = mysqli_fetch_array($q_firewall)) {

          if ($a_interface['inv_name'] != $a_firewall['inv_name']) {
            $output .= "<tr>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['inv_name']                  . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_source']                 . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $zone[$a_firewall['fw_sourcezone']]      . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_destination']            . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $zone[$a_firewall['fw_destinationzone']] . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_port']                   . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_portdesc']               . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_protocol']               . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_timeout']                . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_ticket']                 . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_description']            . "</td>\n";
            $output .= "</tr>\n";
            $count++;
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No associated firewall rules exist.</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n";

      mysqli_free_result($q_firewall);

      print "document.getElementById('firewall_table').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.edit.fw_update.disabled = true;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
