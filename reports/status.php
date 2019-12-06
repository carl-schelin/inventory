<?php
# Script: status.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "hardware.php";

  logaccess($formVars['uid'], $package, "Checking out the hardware.");

  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }
  if (isset($_GET['inwork'])) {
    $formVars['inwork']    = clean($_GET['inwork'],   10);
  } else {
    $formVars['inwork'] = 'false';
  }
  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  } else {
    $formVars['product'] = 0;
  }
  if (isset($_GET['group'])) {
    $formVars['group']     = clean($_GET['group'],    10);
  } else {
    $formVars['group'] = 0;
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
    $orderby = "order by inv_name,int_server";
    $_SESSION['sort'] = '';
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
<title>Monitoring Status Report</title>

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

  $and = " where mod_virtual = 0 and ";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " hw_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " hw_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " hw_group = " . $formVars['group'] . " ";
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

  $where = $product . $group . $inwork . $location . $type . " and int_mondate != \"0000-00-00\" ";

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Monitoring Status Report</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report lists all the servers for the selected filters (group, product, etc) and displays the status as of the most recently ";
  print "imported nnmiServerReport.txt file. The file is emailed to the team regularly however each file may not make it into the report as ";
  print "the saving and moving of the file is a manual process.</p>\n";

  print "<ul>\n";
  print "  <li>IP Address - This is the IP address as reported. All imports are based on the listed IP address in the report, not the first ";
  print "column/server name</li>\n";
  print "  <li>Monitored - This indicates Yes or No if the IP Address is marked as monitored by Openview in the server's interface listing. ";
  print "The intention is that the listed interface/IP address has been identified as the one Openview is supposed to be monitoring.</li>\n";
  print "  <li>Management - This indicates Yes or No if the IP Address is marked as the Management interface in the server's interface listing. ";
  print "The intention is that the Management interface and the Openview monitored interface should be the same.</li>\n";
  print "  <li>Node Status - The current, as of the report, status of that check.</li>\n";
  print "  <li>Management Mode - MANAGED if the node is being monitored, NOTMANAGED if the node is in the process of being decommissioned, and ";
  print "OUTOFSERVICE if the node is not currently being monitored.</li>\n";
  print "  <li>Last Seen - The reports are added to the inventory each night. This column just shows when the entry in the report was updated in ";
  print "the Inventory. Note that if no report was manually uploaded, the date will still be updated.</li>\n";
  print "</ul>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  $managed = 0;
  $notmanaged = 0;
  $outofservice = 0;

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"   . $passthrough . "\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_server"   . $passthrough . "\">Hostname</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_addr"  . $passthrough . "\">IP Address</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_openview"  . $passthrough . "\">Monitored</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_management"  . $passthrough . "\">Management</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_monstatus"   . $passthrough . "\">Node Status</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_monservice" . $passthrough . "\">Management Mode</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_mondate"    . $passthrough . "\">Last Seen</a></th>\n";
  print "</tr>\n";

  $q_string  = "select inv_name,int_server,int_monstatus,int_monservice,int_mondate,int_addr,int_openview,int_management ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join models    on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join parts     on parts.part_id    = hardware.hw_type ";
  $q_string .= "left join products  on products.prod_id = hardware.hw_product ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_interface = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_interface = mysql_fetch_array($q_interface)) {

    $managed++;
    $class = " class=\"ui-widget-content\"";
    if ($a_interface['int_monservice'] == 'NOTMANAGED') {
      $class = " class=\"ui-state-highlighted\"";
      $notmanaged++;
      $managed--;
    }
    if ($a_interface['int_monservice'] == 'OUTOFSERVICE') {
      $class = " class=\"ui-state-error\"";
      $outofservice++;
      $managed--;
    }

    $openview = 'No';
    if ($a_interface['int_openview']) {
      $openview = 'Yes';
    }
    $management = 'No';
    if ($a_interface['int_management']) {
      $management = 'Yes';
    }

    print "<tr>";
    print "  <td" . $class . ">" . $a_interface['inv_name']                . "</td>";
    print "  <td" . $class . ">" . $a_interface['int_server']                . "</td>";
    print "  <td" . $class . ">" . $a_interface['int_addr']                . "</td>";
    print "  <td" . $class . ">" . $openview               . "</td>";
    print "  <td" . $class . ">" . $management               . "</td>";
    print "  <td" . $class . ">" . $a_interface['int_monstatus']               . "</td>";
    print "  <td" . $class . ">" . $a_interface['int_monservice']                . "</td>";
    print "  <td" . $class . ">" . $a_interface['int_mondate']               . "</td>";
    print "</tr>";

  }

  mysql_free_result($q_interface);

  print "</table>\n";

  print "<p>Managed: " . $managed . ", Not Managed: " . $notmanaged . ", Out of Service: " . $outofservice . "</p>\n";

?>
</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
