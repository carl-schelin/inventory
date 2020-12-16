<?php
# Script: routing.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "routing.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_manager "
            . "from inventory "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#routing\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Routing Information";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('routing-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"routing-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Destination</strong> - The destination network or host for this route.</li>";
  $output .= "  <li><strong>DNS Lookup</strong> - If the destination is a host, a name service lookup will be called to help determine the intended target of the route.</li>";
  $output .= "  <li><strong>Gateway</strong> - The gateway being used by the network for packet traffic.</li>";
  $output .= "  <li><strong>Netmask</strong> - The netmask.</li>";
  $output .= "  <li><strong>Interface</strong> - The interface traffic will be routed.</li>";
  $output .= "  <li><strong>Description</strong> - A description of this destination.</li>";
  $output .= "  <li><strong>Updated</strong> - The last time this entry was updated. Since this data can be captured automatically, a checkmark will be added for routes that are automatically retrieved.</li>";
  $output .= "</ul>";

  $output .= "<ul>";
  $output .= "  <li><strong>Highlight</strong> An interface that <span class=\"ui-state-highlight\">is highlighted</span> indicates that this interface is reponding to ping.</li>\n";
  $output .= "  <li><strong>Highlight</strong> An interface that <span class=\"ui-state-error\">is highlighted</span> indicates that this interface is <strong>not</strong> responding to ping.</li>\n";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Destination</th>";
  $output .= "<th class=\"ui-state-default\">DNS Lookup</th>";
  $output .= "<th class=\"ui-state-default\">Gateway</th>";
  $output .= "<th class=\"ui-state-default\">Netmask</th>";
  $output .= "<th class=\"ui-state-default\">Interface</th>";
  $output .= "<th class=\"ui-state-default\">Description</th>";
  $output .= "<th class=\"ui-state-default\">Updated</th>";
  $output .= "</tr>";

  $interface = array();
  $q_string  = "select route_id,route_address,route_mask,route_gateway,int_face,route_desc,route_verified,route_user,route_update ";
  $q_string .= "from routing ";
  $q_string .= "left join interface on interface.int_id = routing.route_interface ";
  $q_string .= "where route_companyid = " . $formVars['id'] . " and route_ipv6 = 0 ";
  $q_string .= "order by route_address";
  $q_routing = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_routing = mysqli_fetch_array($q_routing)) {

    $checkmark = "";
    if ($a_routing['route_verified']) {
      $checkmark = "&#x2713;";
    }

    $ping = ' class="ui-widget-content"';
    $dns = '';
# validate the IP before trying to ping or look it up (unnecessary delays)
    if (filter_var($a_routing['route_address'], FILTER_VALIDATE_IP) && ($a_routing['int_face'] != 'lo' || $a_routing['int_face'] != 'lo0')) {
# ensure it's a -host based ip, no need to ping or look up -net ranges.
      if ($a_routing['route_mask'] == 32) {
        $ping = ' class="ui-state-error" ';
        if (ping($a_routing['route_address'])) {
          $ping = ' class="ui-state-highlight" ';
        }
        $dns = gethostbyaddr($a_routing['route_address']);
      }
    }

    $title = "title=\"Last Update: " . $a_routing['route_update'] . "\"";

    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\" " . $title . ">" . $a_routing['route_address']                            . "</td>";
    $output .= "<td" . $ping . ">"                               . $dns                                                   . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['route_gateway']                            . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . createNetmaskAddr($a_routing['route_mask'])            . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['int_face']                                 . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['route_desc']                               . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['route_update']                . $checkmark . "</td>";
    $output .= "</tr>";

  }
  $output .= "</table>";

  print "document.getElementById('routing_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";


# ipv6 output

  $ipv6 = 0;
  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Destination</th>";
  $output .= "<th class=\"ui-state-default\">DNS Lookup</th>";
  $output .= "<th class=\"ui-state-default\">Gateway</th>";
  $output .= "<th class=\"ui-state-default\">Netmask</th>";
  $output .= "<th class=\"ui-state-default\">Interface</th>";
  $output .= "<th class=\"ui-state-default\">Description</th>";
  $output .= "<th class=\"ui-state-default\">Updated</th>";
  $output .= "</tr>";

  $interface = array();
  $q_string  = "select route_id,route_address,route_mask,route_gateway,int_face,route_desc,route_verified,route_user,route_update ";
  $q_string .= "from routing ";
  $q_string .= "left join interface on interface.int_id = routing.route_interface ";
  $q_string .= "where route_companyid = " . $formVars['id'] . " and route_ipv6 = 1 ";
  $q_string .= "order by route_address";
  $q_routing = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_routing = mysqli_fetch_array($q_routing)) {

    $ipv6 = 1;

    $checkmark = "";
    if ($a_routing['route_verified']) {
      $checkmark = "&#x2713;";
    }

    $ping = ' class="ui-widget-content"';
    $dns = '';
# validate the IP before trying to ping or look it up (unnecessary delays)
    if (filter_var($a_routing['route_address'], FILTER_VALIDATE_IP) && ($a_routing['int_face'] != 'lo' || $a_routing['int_face'] != 'lo0')) {
# ensure it's a -host based ip, no need to ping or look up -net ranges.
      if ($a_routing['route_mask'] == 32) {
        $ping = ' class="ui-state-error" ';
        if (ping($a_routing['route_address'])) {
          $ping = ' class="ui-state-highlight" ';
        }
        $dns = gethostbyaddr($a_routing['route_address']);
      }
    }

    $title = "title=\"Last Update: " . $a_routing['route_update'] . "\"";

    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\" " . $title . ">" . $a_routing['route_address']                            . "</td>";
    $output .= "<td" . $ping . ">"                               . $dns                                                   . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['route_gateway']                            . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . createNetmaskAddr($a_routing['route_mask'])            . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['int_face']                                 . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['route_desc']                               . "</td>";
    $output .= "<td class=\"ui-widget-content\">"                . $a_routing['route_update']                . $checkmark . "</td>";
    $output .= "</tr>";

  }
  $output .= "</table>";

  if ($ipv6) {
    print "document.getElementById('ipv6routing_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";
  } else {
    print "document.getElementById('ipv6routing_mysql').innerHTML = '';\n";
  }

?>
