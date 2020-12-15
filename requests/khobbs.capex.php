<?php
# Script: khobbs.capex.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "khobbs.capex.php";

  logaccess($db, $formVars['uid'], $package, "Getting a capex listing.");

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $formVars['sort'] = clean($_GET['sort'], 30);
    $orderby .= $formVars['sort'] . ",";
  }
  $orderby .= "inv_name";

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Ken Hobbs: 2012 CAPEX</title>

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

<div class="main">

<table class="ui-styled-table">
<?php

  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Product</th>\n";
  print "  <th class=\"ui-state-default\">Service Level</th>\n";
  print "  <th class=\"ui-state-default\">Server</th>\n";
  print "  <th class=\"ui-state-default\">Description</th>\n";
  print "  <th class=\"ui-state-default\">HW End of Support</th>\n";
  print "  <th class=\"ui-state-default\">SW End of Support</th>\n";
  print "  <th class=\"ui-state-default\">Notes</th>\n";
  print "  <th class=\"ui-state-default\">Recommendation</th>\n";
  print "  <th class=\"ui-state-default\">Capital / Expense</th>\n";
  print "</tr>\n";

  $date = date("Ym");
  $total = 0;

  $q_string  = "select inv_id,inv_name,inv_function,inv_class,prod_name,ct_city,svc_name,sw_software,hw_purchased,mod_vendor,mod_eol ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join products on inventory.inv_product = products.prod_id ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join service on service.svc_id = inventory.inv_class ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 and sw_type = 'OS' and hw_primary = 1 and mod_virtual = 0 ";
  $q_string .= "order by prod_name,inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {

    $newdate = "";

    if ($a_inventory['mod_vendor'] == 'Dell') {
      $date = explode("-", $a_inventory['hw_purchased']);
      $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
      $newdate = date("Y-m-d",$support);
    } else {
      $newdate = $a_inventory['mod_eol'];
    }

    if ($newdate == '0000-00-00') {
      $newdate = "";
    }

    $hwnotes = '';
    if ($newdate != "") {
      $output = explode("-", $newdate);
      $support = mktime(0,0,0,$output[0],$output[2],$output[1] - 1);
      $now = time();
      if ($now > $support) {
        $hardware = "Replace server hardware";
      } else {
        $hardware = "Keep the existing hardware";
      }
    } else {
      $hwnotes = "Determine the hardware expiration date";
    }

    $class = "ui-widget-content";

# Software breakdown as it's not in the database (yet).
    $osdate = '';
    $date[2] = "";
    $date = explode(" ", $a_inventory['sw_software']);
    if ($date[0] == 'Solaris') {
      if ($date[1] == 8) {
        $osdate = '2012-03-31';
      }
      if ($date[1] == 9) {
        $osdate = '2014-10-31';
      }
      if ($date[1] == 10) {
        $output = explode("/", $date[2]);
        $support = mktime(0, 0, 0, $output[0], 1, $output[1] + 3);
        $osdate = date("Y-m-d", $support);
      }
    }
    if ($date[0] == "Oracle" && $date[2] == 4) {
      $osdate = "2015-02-28";
    }
    if ($date[0] == "Red" && $date[3] == 2.1) {
      $osdate = "2009-03-31";
    }
    if ($date[0] == "Red" && $date[3] == 3) {
      $osdate = "2013-10-31";
    }
    if ($date[0] == "Red" && $date[3] == 4) {
      $osdate = "2015-02-28";
    }
    if ($date[0] == "Red" && ($date[3] == 5.1 || $date[3] == 5.2 || $date[3] == 5.4)) {
      $osdate = "2017-03-31";
    }
    if ($date[0] == "Tru64") {
      $osdate = "2009-03-31";
    }
    if ($date[0] == "HP-UX") {
      if ($date[1] == "B.11.00") {
        $osdate = "2006-12-31";
      }
      if ($date[1] == "B.11.11") {
        $osdate = "2013-12-31";
      }
      if ($date[1] == "B.11.23") {
        $osdate = "2013-12-31";
      }
      if ($date[1] == "B.11.31") {
        $osdate = "2020-12-31";
      }
    }
    if ($date[0] == 'FreeBSD') {
      $osdate = "2007-01-31";
    }
    if ($date[0] == 'Windows' && $date[2] == "2003") {
      $osdate = "2005-07-14";
    }
    if ($date[0] == 'Windows' && $date[2] == "2008") {
      $osdate = "2018-07-10";
    }
    if ($date[0] == "None" || $date[0] == "CMM ILOM") {
      $osdate = "";
    }

# exploding on '.' for the NonStops
    $date = explode(".", $a_inventory['sw_software']);
    if ($date[0] == 'G06') {
      $osdate = "2012-12-31";
    }
    if ($date[0] == 'D48') {
      $osdate = "2006-12-31";
    }
    if ($date[0] == 'J06') {
      $osdate = "2020-12-31";
    }

    $swnotes = '';
    if ($osdate != "") {
      $output = explode("-", $osdate);
      $support = mktime(0, 0, 0, $output[0], $output[2], $output[1] - 1);
      $now = time();
      if ($now > $support) {
        $software = " and replace the existing operating system";
      } else {
        $software = " and keep the existing operating system.";
      }
    } else {
      $osdate = $a_inventory['sw_software'];
      $swnotes = " and determine the software expiration date.";
    }

    print "<tr>\n";
    print "  <td class=\"" . $class . "\">" . $a_inventory['prod_name'] . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $a_inventory['svc_name'] . "</td>\n";
    print "  <td class=\"" . $class . "\"><a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</a></td>\n";
    print "  <td class=\"" . $class . "\">" . $a_inventory['inv_function'] . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $newdate . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $osdate . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $hwnotes . $swnotes . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $hardware . $software . "</td>\n";
    print "  <td class=\"" . $class . "\" align=\"right\">$10,000</td>\n";
    print "</tr>\n";

    $total += 10000;
  }

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\" colspan=\"8\" align=\"right\">Total:</td>\n";
  print "  <td class=\"ui-widget-content\" align=\"right\">" . $total . "</td>\n";
  print "</tr>\n";
?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
