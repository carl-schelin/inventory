<?php
# Script: firewall.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "firewall.php";

  logaccess($formVars['uid'], $package, "Checking out the interfaces.");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by inv_name,fw_source,fw_destination";
    $_SESSION['sort'] = '';
  }

  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
    $and = " and";
  }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

  $location = '';
  if ($formVars['country'] == 0 && $formVars['location'] > 0) {
    $location = $and . " inv_location = " . $formVars['location'] . " ";
    $and = " and";
  } else {
    if ($formVars['country'] > 0) {
      $location .= $and . " loc_country = " . $formVars['country'] . " ";
      $and = " and";
    }
    if ($formVars['state'] > 0) {
      $location .= $and . " loc_state = " . $formVars['state'] . " ";
      $and = " and";
    }
    if ($formVars['city'] > 0) {
      $location .= $and . " loc_city = " . $formVars['city'] . " ";
      $and = " and";
    }
    if ($formVars['location'] > 0) {
      $location .= $and . " inv_location = " . $formVars['location'] . " ";
      $and = " and";
    }
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $inwork . $location . $type;

  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from ip_zones";
  $q_ip_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
    $zoneval[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
  }

# if help has not been seen yet,
  if (show_Help($Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Firewall Rule Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Firewall Rules</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the firewall rules that have been entered into the Inventory based on your filter.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"       . $passthrough . "\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_source"      . $passthrough . "\">Source</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_source"      . $passthrough . "\">Zone</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_destination" . $passthrough . "\">Destination</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_destination" . $passthrough . "\">Zone</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_port"        . $passthrough . "\">Port</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_portdesc"    . $passthrough . "\">Desc</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_protocol"    . $passthrough . "\">Protocol</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_timeout"     . $passthrough . "\">Timeout</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_ticket"      . $passthrough . "\">Ticket</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fw_description" . $passthrough . "\">Notes</a></th>\n";
  print "</tr>\n";

  $q_string  = "select fw_id,fw_source,fw_sourcezone,fw_destination,fw_destinationzone,fw_port,";
  $q_string .= "fw_protocol,fw_timeout,fw_ticket,fw_description,fw_portdesc,inv_id,inv_name ";
  $q_string .= "from firewall ";
  $q_string .= "left join inventory on inventory.inv_id      = firewall.fw_companyid ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= $where . " ";
  $q_string .= $orderby;
  $q_firewall = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_firewall) > 0) {
    while ($a_firewall = mysqli_fetch_array($q_firewall)) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_firewall['inv_id'] . "#network\">";
      $linkend   = "</a>";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">"                               . $linkstart . $a_firewall['inv_name']                     . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $a_firewall['fw_source']                               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $zoneval[$a_firewall['fw_sourcezone']]                 . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $a_firewall['fw_destination']                          . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $zoneval[$a_firewall['fw_destinationzone']]            . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $a_firewall['fw_port']                                 . "</td>\n";
      print "  <td class=\"ui-widget-content\" style=\"white-space: nowrap\">"              . $a_firewall['fw_portdesc']                             . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $a_firewall['fw_protocol']                             . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $a_firewall['fw_timeout']                              . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $a_firewall['fw_ticket']                               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"                                            . $a_firewall['fw_description']                          . "</td>\n";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"11\">No records found</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
