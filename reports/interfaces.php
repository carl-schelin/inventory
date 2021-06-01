<?php
# Script: interfaces.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "interfaces.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the interfaces.");

  $formVars['group']     = clean($_GET['group'],     10);
  $formVars['product']   = clean($_GET['product'],   10);
  $formVars['active']    = clean($_GET['active'],    10);
  $formVars['ipv6']      = clean($_GET['ip6'],       10);
  $formVars['loopback']  = clean($_GET['loopback'],  10);
  $formVars['virtual']   = clean($_GET['virtual'],   10);
  $formVars['inwork']    = clean($_GET['inwork'],    10);
  $formVars['country']   = clean($_GET['country'],   10);
  $formVars['state']     = clean($_GET['state'],     10);
  $formVars['city']      = clean($_GET['city'],      10);
  $formVars['location']  = clean($_GET['location'],  10);
  $formVars['csv']       = clean($_GET['csv'],       10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }

  if (isset($_GET["sort"])) {
    $formVars['sort']    = clean($_GET["sort"],      20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by INET_ATON(int_addr),int_server";
    $_SESSION['sort'] = '';
  }

  if ($formVars['inwork'] == '') {
    $formVars['inwork'] = 'false';
  }
  if ($formVars['project'] == '') {
    $formVars['project'] = 0;
  }
  if ($formVars['country'] == '') {
    $formVars['country'] = 0;
  }
  if ($formVars['state'] == '') {
    $formVars['state'] = 0;
  }
  if ($formVars['city'] == '') {
    $formVars['city'] = 0; 
  }
  if ($formVars['location'] == '') {
    $formVars['location'] = 0;
  }
  if ($formVars['csv'] == '') {
    $formVars['csv'] = 'false';
  }

  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from net_zones";
  $q_net_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_net_zones = mysqli_fetch_array($q_net_zones)) {
    $zoneval[$a_net_zones['zone_id']] = $a_net_zones['zone_name'];
  }

# if help has not been seen yet,
  if (show_Help($db, $Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Active Interface Listing</title>

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

# now build the where clause
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
    $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
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

  $title = 'Interfaces including: ';
  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
    $title .= "Active ";
  }

  if ($formVars['ipv6'] == 'true') {
    $ipv6 = '';
    $title .= "IPv6 ";
  } else {
    $ipv6 = $and . " int_ip6 = 0 ";
    $and = " and";
  }

  if ($formVars['loopback'] == "true") {
    $loopback = '';
    $title .= "Loopbacks ";
  } else {
    $loopback = $and . " int_type != 7 ";
    $and = " and";
  }

  if ($formVars['virtual'] == "true") {
    $virtual = '';
    $title .= "Virtual ";
  } else {
    $virtual = $and . " int_virtual = 0 ";
    $and = " and";
  }

  $where = $product . $group . $inwork . $location . $type . $ipv6 . $loopback . $virtual . $and . " int_addr != '' ";

  $passthrough = 
    "&group="    . $formVars['group']    .
    "&product="  . $formVars['product']  .
    "&active="   . $formVars['active']   . 
    "&ip6="      . $formVars['ipv6']     . 
    "&loopback=" . $formVars['loopback'] . 
    "&virtual="  . $formVars['virtual']  . 
    "&inwork="   . $formVars['inwork']   .
    "&type="     . $formVars['type']     .
    "&country="  . $formVars['country']  .
    "&state="    . $formVars['state']    .
    "&city="     . $formVars['city']     .
    "&location=" . $formVars['location'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . $title . "</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page presents a list of the IP addresses in use based on the Filter selection.<p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  if ($formVars['csv'] == 'false') {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_server"            . $passthrough . "\">Interface Name</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_face"              . $passthrough . "\">Logical Interface</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_sysport"           . $passthrough . "\">Physical Port</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_eth"               . $passthrough . "\">MAC Address</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=INET_ATON(int_addr)"   . $passthrough . "\">IP Address</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_zone"              . $passthrough . "\">Zone</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_gate"              . $passthrough . "\">Gateway</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_switch"            . $passthrough . "\">Switch</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_port"              . $passthrough . "\">Port</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_port"              . $passthrough . "\">Type</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_update"            . $passthrough . "\">Updated</a></th>\n";
    print "</tr>\n";
  } else {
    print "<p>\"Interface Name\",";
    print "\"Logical Interface\",";
    print "\"Physical Port\",";
    print "\"MAC Address\",";
    print "\"IP Address\",";
    print "\"Zone\",";
    print "\"Gateway\",";
    print "\"Switch\",";
    print "\"Port\",";
    print "\"Type\",";
    print "\"Updated\"";
    print "</br>\n";
  }

  $q_string  = "select int_id,int_companyid,int_server,int_face,int_sysport,int_eth,int_addr,int_mask,";
  $q_string .= "zone_name,int_gate,int_switch,int_port,itp_acronym,int_update,int_verified,int_primary,";
  $q_string .= "inv_manager,inv_status ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_id ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = locations.loc_state ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join net_zones on net_zones.zone_id = interface.int_zone ";
  $q_string .= "left join inttype on inttype.itp_id = interface.int_type ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_interface = mysqli_fetch_array($q_interface)) {

    if ($a_interface['int_primary']) {
      $class = " class=\"ui-state-highlight\"";
    } else {
      $class = " class=\"ui-widget-content\"";
    }

    if ($a_interface['int_verified']) {
      $checked = "&#x2713;&nbsp;";
    } else {
      $checked = '';
    }

    if ($a_interface['int_eth'] == '00:00:00:00:00:00') {
      $showmac = '';
    } else {
      $showmac = $a_interface['int_eth'];
    }

    if ($formVars['csv'] == 'false') {
      print "<tr>\n";
      print "  <td" . $class . ">" . $a_interface['int_server']                    . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['int_face']                      . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['int_sysport']                   . "</td>\n";
      print "  <td" . $class . ">" . $showmac                                      . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['int_addr'] . '/' . $a_interface['int_mask'] . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['zone_name']                     . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['int_gate']                      . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['int_switch']                    . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['int_port']                      . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['itp_acronym']                   . "</td>\n";
      print "  <td" . $class . ">" . $a_interface['int_update']         . $checked . "</td>\n";
      print "</tr>\n";
    } else {
      print "\"" . $a_interface['int_server'] . "\",";
      print "\"" . $a_interface['int_face'] . "\",";
      print "\"" . $a_interface['int_sysport'] . "\",";
      print "\"" . $showmac . "\",";
      print "\"" . $a_interface['int_addr'] . '/' . $a_interface['int_mask'] . "\",";
      print "\"" . $a_interface['zone_name'] . "\",";
      print "\"" . $a_interface['int_gate'] . "\",";
      print "\"" . $a_interface['int_switch'] . "\",";
      print "\"" . $a_interface['int_port'] . "\",";
      print "\"" . $a_interface['itp_acronym'] . "\",";
      print "\"" . $a_interface['int_update'] . "\"";
      print "</br>\n";
    }

  }

?>
</table>

<p>&#x2713; indicates the interface data was captured automatically.</p>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
