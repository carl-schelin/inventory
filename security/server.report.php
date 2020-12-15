<?php
# Script: server.report.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "server.report.php";

  logaccess($db, $formVars['uid'], $package, "Getting a report on vulnerabilities.");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['project']   = clean($_GET['project'],  10);
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

if ($formVars['inwork'] == '') {
    $formVars['inwork'] = 'false';
  }
  if ($formVars['product'] == '') {
    $formVars['product'] = 0;
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
      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
      }
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 and mod_virtual = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 and mod_virtual = 0 ";
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

  $where = $product . $group . $location;

# if help has not been seen yet,
  if (show_Help($db, 'server.report')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Server Scan Report</title>

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

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Scan Report Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report lists the IPs for all the servers in the selected product or project. It counts the number of each type of severity and displays it for each interface.";
  print "For a non-intrusive scan, generally only one interface will be identified in the results. For an intrusive scan, all interfaces will have been scanned. Each total ";
  print "is displayed for review.</p>";

  print "<p>If a server (block of IPs) have no scan results, then that server block is <span class=\"ui-state-highlight\">highlighted</span>. This is noted because there is ";
  print "always a result of some sort when a system is scanned. No results should be called out to identify a server or network that hasn't been scanned.</p>";

  print "<p>If an IP returns a result of a High or Critical result, the IP will be <span class=\"ui-state-error\">highlighted</span>.</p>";

  print "</div>\n\n";

  print "</div>\n\n";

  $linkstart = "<a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=";
  $linkend   = "</a>";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Server Name</th>\n";
  print "  <th class=\"ui-state-default\">Product</th>\n";
  print "  <th class=\"ui-state-default\">Project</th>\n";
  print "  <th class=\"ui-state-default\">IP Address</th>\n";
  print "  <th class=\"ui-state-default\">Information</th>\n";
  print "  <th class=\"ui-state-default\">Low</th>\n";
  print "  <th class=\"ui-state-default\">Medium</th>\n";
  print "  <th class=\"ui-state-default\">High</th>\n";
  print "  <th class=\"ui-state-default\">Critical</th>\n";
  print "</tr>\n";

  $class = "ui-state-highlight";
  $inventory_id = 0;
  $total = 0;
  $q_string  = "select inv_id,int_id,int_server,int_addr,prod_name,prj_name ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id      = interface.int_companyid ";
  $q_string .= "left join products  on products.prod_id      = inventory.inv_product ";
  $q_string .= "left join projects  on projects.prj_id       = inventory.inv_project ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities    on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join states    on states.st_id          = locations.loc_state ";
  $q_string .= $where . " and int_ip6 = 0 and int_type != 7 and inv_status = 0 ";
  $q_string .= "order by int_server,int_addr ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_interface = mysqli_fetch_array($q_interface)) {

# check for Info
    $q_string  = "select count(sec_severity) ";
    $q_string .= "from vulnerabilities ";
    $q_string .= "left join security on security.sec_id = vulnerabilities.vuln_securityid ";
    $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 5 ";
    $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_vulnerabilities . ": " . mysqli_error($db));
    $a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities);
    $info = $a_vulnerabilities['count(sec_severity)'];
# check for Low
    $q_string  = "select count(sec_severity) ";
    $q_string .= "from vulnerabilities ";
    $q_string .= "left join security on security.sec_id = vulnerabilities.vuln_securityid ";
    $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 4 ";
    $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_vulnerabilities . ": " . mysqli_error($db));
    $a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities);
    $low = $a_vulnerabilities['count(sec_severity)'];
# check for medium
    $q_string  = "select count(sec_severity) ";
    $q_string .= "from vulnerabilities ";
    $q_string .= "left join security on security.sec_id = vulnerabilities.vuln_securityid ";
    $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 3 ";
    $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_vulnerabilities . ": " . mysqli_error($db));
    $a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities);
    $medium = $a_vulnerabilities['count(sec_severity)'];
# check for high
    $q_string  = "select count(sec_severity) ";
    $q_string .= "from vulnerabilities ";
    $q_string .= "left join security on security.sec_id = vulnerabilities.vuln_securityid ";
    $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 2 ";
    $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_vulnerabilities . ": " . mysqli_error($db));
    $a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities);
    $high = $a_vulnerabilities['count(sec_severity)'];
# check for critical
    $q_string  = "select count(sec_severity) ";
    $q_string .= "from vulnerabilities ";
    $q_string .= "left join security on security.sec_id = vulnerabilities.vuln_securityid ";
    $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 1 ";
    $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_vulnerabilities . ": " . mysqli_error($db));
    $a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities);
    $critical = $a_vulnerabilities['count(sec_severity)'];

    $total += $info + $low + $warning + $high + $critical;

    if ($inventory_id != $a_interface['inv_id']) {
      if ($total == 0) {
        $class = "ui-state-highlight";
      } else {
        $class = "ui-widget-content";
      }
      $total = 0;
      $inventory_id = $a_interface['inv_id'];
    }

    if ($high > 0 || $critical > 0) {
      $output = "ui-state-error";
    } else {
      $output = $class;
    }

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_interface['inv_id'] . "#vulnerabilities\" target=\"_blank\">";
    $linkend = "</a>";

    print "<tr>\n";
    print "  <td class=\"" . $output . "\">"        . $linkstart . $a_interface['int_server'] . $linkend . "</td>\n";
    print "  <td class=\"" . $output . "\">"                     . $a_interface['prod_name']             . "</td>\n";
    print "  <td class=\"" . $output . "\">"                     . $a_interface['prj_name']              . "</td>\n";
    print "  <td class=\"" . $output . "\">"                     . $a_interface['int_addr']              . "</td>\n";
    print "  <td class=\"" . $output . " delete\">"              . $info                                 . "</td>\n";
    print "  <td class=\"" . $output . " delete\">"              . $low                                  . "</td>\n";
    print "  <td class=\"" . $output . " delete\">"              . $medium                               . "</td>\n";
    print "  <td class=\"" . $output . " delete\">"              . $high                                 . "</td>\n";
    print "  <td class=\"" . $output . " delete\">"              . $critical                             . "</td>\n";

    print "</tr>\n";

  }

?>

</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
