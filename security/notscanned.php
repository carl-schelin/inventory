<?php
# Script: notscanned.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "ipreport.php";

  logaccess($db, $formVars['uid'], $package, "Getting a report on IPs.");

  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
    if ($formVars['csv'] == 'true') {
      $formVars['csv'] = 1;
    } else {
      $formVars['csv'] = 0;
    }
  } else {
    $formVars['csv'] = 0;
  }

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 1;
  }

  if (isset($_GET['product'])) {
    if ($_GET['product'] == 0) {
      $formVars['product'] = '';
    } else {
      $formVars['product'] = "and inv_product = " . clean($_GET['product'], 10) . " ";
    }
  } else {
    $formVars['product'] = '';
  }

  if (isset($_GET['project'])) {
    if ($_GET['project'] == 0) {
      $formVars['project'] = '';
    } else {
      $formVars['project'] = "and inv_project = " . clean($_GET['project'], 10) . " ";
    }
  } else {
    $formVars['project'] = '';
  }

# if help has not been seen yet,
  if (show_Help($db, 'notscanned')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>IP's Not Scanned</title>

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
  print "  <th class=\"ui-state-default\">IP Unscanned Report</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report lists all IPs that don't have Vulnerability scan results associated with them. As the scan always finds \n";
  print "at least one low level (Info) result, an IP with no vulnerabilities is likely to not have been scanned. Note too that some \n";
  print "IPs can't be easily scanned as they're a non-routeable IP. Also note that a non-intrusive scan (aka an internal scan vs an \n";
  print "external scan) may not report on additional interfaces in a system.</p>\n";

  print "<p>Servers that are <span class=\"ui-state-highlight\">highlighted</span> are still being built and are in a project. Scans \n";
  print "are typically done before the servers go live.</p>\n";

  print "<p>Listing is only of Lights Out Management (LOM), Application, and Management interfaces. It also excludes IPv6 addresses.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  if ($formVars['csv']) {
    print "<pre style=\"text-align: left;\">";
    print "\"" . "Server"       . "\",";
    print "\"" . "Function"     . "\",";
    print "\"" . "Product"      . "\",";
    print "\"" . "Project"      . "\",";
    print "\"" . "IP Address"   . "\",";
    print "\"" . "Type"         . "\"\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\" colspan=\"6\">Server Listing</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">" . "Server"       . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "Function"     . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "Product"      . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "Project"      . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "IP Address"   . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "Type"         . "</th>\n";
    print "</tr>\n";
  }

  $inventory = '';
  $output = '';
  $count = 0;
  $scount = 0;
  $q_string  = "select int_id,int_server,int_addr,inv_id,inv_name,inv_function,prod_name,prj_name,itp_name ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "left join products  on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects  on projects.prj_id  = inventory.inv_project ";
  $q_string .= "left join inttype   on inttype.itp_id   = interface.int_type ";
  $q_string .= "where inv_manager = " . $formVars['group'] . " and int_addr != '' and int_ip6 = 0 and int_addr != '127.0.0.1' and inv_status = 0 and (int_type = 1 or int_type = 2 or int_type = 6) ";
  $q_string .= $formVars['product'];
  $q_string .= $formVars['project'];
  $q_string .= "order by inv_name ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      if ($inventory != $a_interface['inv_name']) {
        if ($vulnflag == 0) {
          print $output;
          $scount++;
        }
        $inventory = $a_interface['inv_name'];
        $output = '';
        $vulnflag = 0;
      }

      $q_string  = "select hw_active ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_companyid = " . $a_interface['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 ";
      $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_hardware = mysqli_fetch_array($q_hardware);

      $q_string  = "select vuln_id ";
      $q_string .= "from vulnerabilities ";
      $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " ";
      $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_vulnerabilities) == 0) {

        $class = "ui-widget-content";
        if ($a_hardware['hw_active'] == '0000-00-00') {
          $class = "ui-state-highlight";
        }

        if ($formVars['csv']) {
          $output .= "\"" . $a_interface['inv_name']     . "\",";
          $output .= "\"" . $a_interface['inv_function'] . "\",";
          $output .= "\"" . $a_interface['prod_name']    . "\",";
          $output .= "\"" . $a_interface['prj_name']     . "\",";
          $output .= "\"" . $a_interface['int_addr']     . "\",";
          $output .= "\"" . $a_interface['itp_name']     . "\"\n";
        } else {
          $output .= "<tr>\n";
          if ($invname != $a_interface['inv_name']) {
            $output .= "  <td class=\"" . $class . "\">" . $a_interface['inv_name']   . "</td>\n";
            $invname = $a_interface['inv_name'];
          } else {
            $output .= "  <td class=\"" . $class . "\">&nbsp;</td>\n";
          }
          $output .= "  <td class=\"" . $class . "\">" . $a_interface['inv_function']  . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . $a_interface['prod_name']     . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . $a_interface['prj_name']      . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . $a_interface['int_addr']      . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">" . $a_interface['itp_name']      . "</td>\n";
          $output .= "</tr>\n";
          $count++;
        }
      } else {
        $vulnflag = 1;
      }
    }
  } else {
    print "</tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"5\">No unscanned interfaces</td>\n";
    print "</tr>\n";
  }

  if ($formVars['csv']) {
    print "</pre>";
  } else {
    print "</table>\n";
    print "<p class=\"ui-widget-content\">Total Servers: " . $scount . " Interfaces: " . $count . "</p>\n";
  }

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
