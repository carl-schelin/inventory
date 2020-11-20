<?php
# Script: scanreport.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "scanreport.php";

  logaccess($formVars['uid'], $package, "Getting a report on vulnerabilities.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Scan Report</title>

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

  print "<div id=\"help\" style=\"display:none\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report lists the Vulnerability totals, the Exceptions as identified by the team, and the \n";
  print "Vulnerabilities that have Resolved. The report breaks it down by Severity, by Group Owner, and by Product.</p>\n";

  print "<ul>\n";
  print "  <li><strong>Actionable</strong> - This column lists the Vulnerabilities that have been identified as needing correction.</li>\n";
  print "  <li><strong>Exceptions</strong> - This column lists the Vulnerabilities that have an exception. An exception generally \n";
  print "consists of two types. Either a due to the server in the process of being replaced within the next 6 months \n";
  print "or due to the age of a system (which will be in the Lifecycle report). Exceptions will need to be collected \n";
  print "and presented to Management for approval. Other exception types can be added and will be identified in the report to Management.</li>\n";
  print "  <li><strong>Resolved</strong> - This column lists the Vulnerabilities that have fallen off of the report. In the Inventory \n";
  print "each Vulnerability is assigned an Owner which is different than the Custodian. The Owner will address the \n";
  print "listed Vulnerability. If a Vulnerability has been addressed, when the next csv file from SecurityCenter is \n";
  print "imported, the Vulnerability will not be in that listing. This column is the count of Vulnerabilities that are \n";
  print "not in the current report (ie, have been resolved).</li>\n";
  print "</ul>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  $linkstart = "<a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=";
  $linkend   = "</a>";

  $total = 0;
  $total_exception = 0;
  $critical = 0;
  $critical_exception = 0;
  $high = 0;
  $high_exception = 0;
  $medium = 0;
  $medium_exception = 0;
  $low = 0;
  $low_exception = 0;
  $info = 0;
  $info_exception = 0;
  $q_string  = "select vuln_id,vul_exception,vul_group,vuln_group,sev_name,inv_product ";
  $q_string .= "from vulnerabilities ";
  $q_string .= "left join security on security.sec_id = vulnerabilities.vuln_securityid ";
  $q_string .= "left join severity on severity.sev_id = security.sec_severity ";
  $q_string .= "left join interface on interface.int_id = vulnerabilities.vuln_interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "left join vulnowner on vulnowner.vul_interface = vulnerabilities.vuln_interface and vulnowner.vul_security = vulnerabilities.vuln_securityid ";
  $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities)) {
    if (!isset($group[$a_vulnerabilities['vul_group']])) {
      $group[$a_vulnerabilities['vul_group']] = 0;
    }
    if (!isset($group_exc[$a_vulnerabilities['vul_group']])) {
      $group_exc[$a_vulnerabilities['vul_group']] = 0;
    }

    if (!isset($product[$a_vulnerabilities['inv_product']])) {
      $product[$a_vulnerabilities['inv_product']] = 0;
    }
    if (!isset($product_dup[$a_vulnerabilities['inv_product']])) {
      $product_dup[$a_vulnerabilities['inv_product']] = 0;
    }

    if ($a_vulnerabilities['sev_name'] == "Critical") {
      if ($a_vulnerabilities['vul_exception']) {
        $critical_exception++;
        $total_exception++;
        $group_exc[$a_vulnerabilities['vul_group']]++;
        $product_dup[$a_vulnerabilities['inv_product']]++;
      } else {
        $critical++;
        $total++;
        $group[$a_vulnerabilities['vul_group']]++;
        $product[$a_vulnerabilities['inv_product']]++;
      }
    }
    if ($a_vulnerabilities['sev_name'] == "High") {
      if ($a_vulnerabilities['vul_exception']) {
        $high_exception++;
        $total_exception++;
        $group_exc[$a_vulnerabilities['vul_group']]++;
        $product_dup[$a_vulnerabilities['inv_product']]++;
      } else {
        $high++;
        $total++;
        $group[$a_vulnerabilities['vul_group']]++;
        $product[$a_vulnerabilities['inv_product']]++;
      }
    }
    if ($a_vulnerabilities['sev_name'] == "Medium") {
      if ($a_vulnerabilities['vul_exception']) {
        $medium_exception++;
        $total_exception++;
        $group_exc[$a_vulnerabilities['vul_group']]++;
        $product_dup[$a_vulnerabilities['inv_product']]++;
      } else {
        $medium++;
        $total++;
        $group[$a_vulnerabilities['vul_group']]++;
        $product[$a_vulnerabilities['inv_product']]++;
      }
    }
    if ($a_vulnerabilities['sev_name'] == "Low") {
      if ($a_vulnerabilities['vul_exception']) {
        $low_exception++;
        $total_exception++;
        $group_exc[$a_vulnerabilities['vul_group']]++;
        $product_dup[$a_vulnerabilities['inv_product']]++;
      } else {
        $low++;
        $total++;
        $group[$a_vulnerabilities['vul_group']]++;
        $product[$a_vulnerabilities['inv_product']]++;
      }
    }
    if ($a_vulnerabilities['sev_name'] == "Info") {
      if ($a_vulnerabilities['vul_exception']) {
        $info_exception++;
        $total_exception++;
        $group_exc[$a_vulnerabilities['vul_group']]++;
        $product_dup[$a_vulnerabilities['inv_product']]++;
      } else {
        $info++;
        $total++;
        $group[$a_vulnerabilities['vul_group']]++;
        $product[$a_vulnerabilities['inv_product']]++;
      }
    }
  }

# identify resolved vulnerabilities.
# when a vulnerability has been resolved, it drops off of the csv file. As long as 
# a vulnerability has an owner though, it stays in the vulnowner table. to identify 
# resolved scans, just identify the ones in the vulnowner table that have no 
# associated entries in the vulnerability table.
# to get this right, I'll need to import them from the first report. This makes 
# sure all vulnerabilities have owners

# need for totals, per group, and per product just like above

  $total_resolved = 0;
  $critical_resolved = 0;
  $high_resolved = 0;
  $medium_resolved = 0;
  $low_resolved = 0;
  $info_resolved = 0;
  $q_string  = "select vul_id,vul_interface,vul_security,vul_group,inv_product ";
  $q_string .= "from vulnowner ";
  $q_string .= "left join interface on interface.int_id = vulnowner.vul_interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_vulnowner = mysqli_fetch_array($q_vulnowner)) {
    $q_string  = "select vuln_id ";
    $q_string .= "from vulnerabilities ";
    $q_string .= "where vuln_securityid = " . $a_vulnowner['vul_security'] . " and vuln_interface = " . $a_vulnowner['vul_interface'] . " ";
    $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_vulnerabilities) == 0) {

      $q_string  = "select sev_name ";
      $q_string .= "from security ";
      $q_string .= "left join severity on severity.sev_id = security.sec_severity ";
      $q_string .= "where sec_id = " . $a_vulnowner['vul_security'] . " ";
      $q_security = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_security = mysqli_fetch_array($q_security);

      if (isset($group_res[$a_vulnowner['vul_group']])) {
        $group_res[$a_vulnowner['vul_group']]++;
      } else {
        $group_res[$a_vulnowner['vul_group']] = 1;
      }
      if (isset($product_res[$a_vulnowner['inv_product']])) {
        $product_res[$a_vulnowner['inv_product']]++;
      } else {
        $product_res[$a_vulnowner['inv_product']] = 1;
      }

      if ($a_security['sev_name'] == 'Critical') {
        $critical_resolved++;
        $total_resolved++;
      }
      if ($a_security['sev_name'] == 'High') {
        $high_resolved++;
        $total_resolved++;
      }
      if ($a_security['sev_name'] == 'Medium') {
        $medium_resolved++;
        $total_resolved++;
      }
      if ($a_security['sev_name'] == 'Low') {
        $low_resolved++;
        $total_resolved++;
      }
      if ($a_security['sev_name'] == 'Info') {
        $info_resolved++;
        $total_resolved++;
      }
    }
  }


  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\" colspan=\"5\">" . "Totals"   . "</th>\n";
  print "</tr>\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . "Severity"   . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Actionable" . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Exceptions" . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Resolved"   . "</th>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">Critical</td>\n";
  print "  <td class=\"ui-widget-content\">" . $critical . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $critical_exception . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $critical_resolved . "</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">High</td>\n";
  print "  <td class=\"ui-widget-content\">" . $high . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $high_exception . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $high_resolved . "</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">Medium</td>\n";
  print "  <td class=\"ui-widget-content\">" . $medium . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $medium_exception . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $medium_resolved . "</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">Low</td>\n";
  print "  <td class=\"ui-widget-content\">" . $low . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $low_exception . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $low_resolved . "</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">Info</td>\n";
  print "  <td class=\"ui-widget-content\">" . $info . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $info_exception . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $info_resolved . "</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\"><strong>Total</strong></td>\n";
  print "  <td class=\"ui-widget-content\"><strong>" . $total            . "</strong></td>\n";
  print "  <td class=\"ui-widget-content\"><strong>" . $total_exception  . "</strong></td>\n";
  print "  <td class=\"ui-widget-content\"><strong>" . $total_resolved   . "</strong></td>\n";
  print "</tr>\n";

  print "</table>\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\" colspan=\"5\">" . "By Group"   . "</th>\n";
  print "</tr>\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . "Group"      . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Actionable" . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Exceptions" . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Resolved"   . "</th>\n";
  print "</tr>\n";

  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    $groupset = 0;

    $output  = "<tr>\n";
    $output .= "<td class=\"ui-widget-content\">" . $a_groups['grp_name'] . "</td>\n";

    if (isset($group[$a_groups['grp_id']])) {
      $output .= "<td class=\"ui-widget-content\">" . $group[$a_groups['grp_id']] . "</td>\n";
      $groupset++;
    } else {
      $output .= "<td class=\"ui-widget-content\">0</td>\n";
    }

    if (isset($group_exc[$a_groups['grp_id']])) {
      $output .= "<td class=\"ui-widget-content\">" . $group_exc[$a_groups['grp_id']] . "</td>\n";
      $groupset++;
    } else {
      $output .= "<td class=\"ui-widget-content\">0</td>\n";
    }

    if (isset($group_res[$a_groups['grp_id']])) {
      $output .= "<td class=\"ui-widget-content\">" . $group_res[$a_groups['grp_id']] . "</td>\n";
      $groupset++;
    } else {
      $output .= "<td class=\"ui-widget-content\">0</td>\n";
    }

    if ($groupset > 0) {
      print $output;
      print "</tr>\n";
    }

  }

  print "</table>\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\" colspan=\"5\">" . "By Product"   . "</th>\n";
  print "</tr>\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . "Product"    . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Actionable" . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Exceptions" . "</th>\n";
  print "  <th class=\"ui-state-default\">" . "Resolved"   . "</th>\n";
  print "</tr>\n";

  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name ";
  $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_products = mysqli_fetch_array($q_products)) {
    $productset = 0;

    $output  = "<tr>\n";
    $output .= "<td class=\"ui-widget-content\">" . $a_products['prod_name'] . "</td>\n";

    if (isset($product[$a_products['prod_id']])) {
      $output .= "<td class=\"ui-widget-content\">" . $product[$a_products['prod_id']] . "</td>\n";
      $productset++;
    } else {
      $output .= "<td class=\"ui-widget-content\">0</td>\n";
    }

    if (isset($product_dup[$a_products['prod_id']])) {
      $output .= "<td class=\"ui-widget-content\">" . $product_dup[$a_products['prod_id']] . "</td>\n";
      $productset++;
    } else {
      $output .= "<td class=\"ui-widget-content\">0</td>\n";
    }

    if (isset($product_res[$a_products['prod_id']])) {
      $output .= "<td class=\"ui-widget-content\">" . $product_res[$a_products['prod_id']] . "</td>\n";
      $productset++;
    } else {
      $output .= "<td class=\"ui-widget-content\">0</td>\n";
    }

    if ($productset > 0) {
      print $output;
      print "</tr>\n";
    }

  }

  print "</table>\n";


# let's get a ticket count now. anything in vulowner which has number of entries, number of entries with tickets, and number of unique tickets.

  $q_string  = "select count(vul_id) ";
  $q_string .= "from vulnowner ";
  $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_vulnowner = mysqli_fetch_row($q_vulnowner);

  $total_entries = $a_vulnowner[0];

  $q_string  = "select count(vul_id) ";
  $q_string .= "from vulnowner ";
  $q_string .= "where vul_ticket != '' ";
  $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_vulnowner = mysqli_fetch_row($q_vulnowner);

  $total_tickets = $a_vulnowner[0];

  $total_unique = 0;
  $q_string  = "select vul_id ";
  $q_string .= "from vulnowner ";
  $q_string .= "where vul_ticket != '' ";
  $q_string .= "group by vul_ticket ";
  $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_vulnowner = mysqli_fetch_row($q_vulnowner)) {
    $total_unique++;
  }

  print "<p>Assigned Vulnerabilities: " . $total_entries . ", Vulnerabilities Ticketed: " . $total_tickets . ", Number of Tickets: " . $total_unique . "</p>\n";

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
