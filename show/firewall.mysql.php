<?php
# Script: firewall.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "firewall.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_manager "
            . "from inventory "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $q_string = "select zone_id,zone_name from ip_zones";
  $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ': ' . mysqli_error($db));
  while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
    $zoneval[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
  }

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#firewall\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Firewall Rules";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('firewall-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"firewall-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Source</strong> - The source IP.</li>";
  $output .= "  <li><strong>Zone</strong> - The source zone of the IP.</li>";
  $output .= "  <li><strong>Destination</strong> - The destination IP.</li>";
  $output .= "  <li><strong>Zone</strong> - The destination zone of the IP.</li>";
  $output .= "  <li><strong>Port</strong> - The destination port.</li>";
  $output .= "  <li><strong>Desc</strong> - Description of the port being accessed.</li>";
  $output .= "  <li><strong>Protocol</strong> - Protocol (tcp/udp) for the rule.</li>";
  $output .= "  <li><strong>Timeout</strong> - Firewall timeout before the connection is broken.</li>";
  $output .= "  <li><strong>Ticket</strong> - The InfoSec or Network Engineering ticket number.</li>";
  $output .= "  <li><strong>Notes</strong> - Purpose of the rule request.</li>";
  $output .= "</ul>";
  $output .= "<ul>";
  $output .= "  <li><strong>Related Firewall Rules</strong> - This lists the firewall rules in place that have this server as a destination. So destination information is a description of this server.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Source</th>";
  $output .= "<th class=\"ui-state-default\">Zone</th>";
  $output .= "<th class=\"ui-state-default\">Destination</th>";
  $output .= "<th class=\"ui-state-default\">Zone</th>";
  $output .= "<th class=\"ui-state-default\">Port</th>";
  $output .= "<th class=\"ui-state-default\">Desc</th>";
  $output .= "<th class=\"ui-state-default\">Protocol</th>";
  $output .= "<th class=\"ui-state-default\">Timeout</th>";
  $output .= "<th class=\"ui-state-default\">Ticket</th>";
  $output .= "<th class=\"ui-state-default\">Notes</th>";
  $output .= "</tr>";

  $q_string  = "select fw_id,fw_source,fw_sourcezone,fw_destination,fw_destinationzone,";
  $q_string .= "fw_port,fw_protocol,fw_timeout,fw_ticket,fw_description,fw_portdesc ";
  $q_string .= "from firewall ";
  $q_string .= "where fw_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by fw_source,fw_destination,fw_port";
  $q_firewall = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_firewall = mysqli_fetch_array($q_firewall)) {

    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_source']                    . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $zoneval[$a_firewall['fw_sourcezone']]      . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_destination']               . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $zoneval[$a_firewall['fw_destinationzone']] . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_port']                      . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_portdesc']                  . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_protocol']                  . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_timeout']                   . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_ticket']                    . "</td>";
    $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_description']               . "</td>";
    $output .= "</tr>";
  }

  $output .= "</table>";


  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\" colspan=\"11\">Related Firewall Rules</th>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Server</th>";
  $output .= "<th class=\"ui-state-default\">Source</th>";
  $output .= "<th class=\"ui-state-default\">Zone</th>";
  $output .= "<th class=\"ui-state-default\">Destination</th>";
  $output .= "<th class=\"ui-state-default\">Zone</th>";
  $output .= "<th class=\"ui-state-default\">Port</th>";
  $output .= "<th class=\"ui-state-default\">Desc</th>";
  $output .= "<th class=\"ui-state-default\">Protocol</th>";
  $output .= "<th class=\"ui-state-default\">Timeout</th>";
  $output .= "<th class=\"ui-state-default\">Ticket</th>";
  $output .= "<th class=\"ui-state-default\">Notes</th>";
  $output .= "</tr>";

#get the list of IPs for the current server
#loop through the firewall rules looking for the ip
#if found, display output

  $q_string  = "select int_addr,inv_name ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "where int_companyid = " . $formVars['id'];
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_interface = mysqli_fetch_array($q_interface)) {
    $q_string  = "select fw_id,fw_source,fw_sourcezone,fw_destination,fw_destinationzone,";
    $q_string .= "fw_port,fw_protocol,fw_timeout,fw_ticket,fw_description,fw_portdesc,inv_name ";
    $q_string .= "from firewall ";
    $q_string .= "left join inventory on inventory.inv_id = firewall.fw_companyid ";
    $q_string .= "where fw_source = '" . $a_interface['int_addr'] . "' or fw_destination = '" . $a_interface['int_addr'] . "' ";
    $q_string .= "order by fw_source,fw_destination,fw_port";
    $q_firewall = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_firewall = mysqli_fetch_array($q_firewall)) {

      if ($a_interface['inv_name'] != $a_firewall['inv_name']) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['inv_name']                     . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_source']                    . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $zoneval[$a_firewall['fw_sourcezone']]      . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_destination']               . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $zoneval[$a_firewall['fw_destinationzone']] . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_port']                      . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_portdesc']                  . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_protocol']                  . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_timeout']                   . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_ticket']                    . "</td>";
        $output .= "  <td class=\"ui-widget-content\">" . $a_firewall['fw_description']               . "</td>";
        $output .= "</tr>";
      }
    }
  }

  $output .= "</table>";

?>

document.getElementById('firewall_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

