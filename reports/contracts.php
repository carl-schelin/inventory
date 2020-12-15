<?php
# Script: contracts.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath  . '/guest.php');

  $package = "contracts.php";

  logaccess($db, $formVars['uid'], $package, "Listing of Lynda's support contract import.");

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

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = " order by sup_company,inv_name ";
    $_SESSION['sort'] = '';
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
<title>Contract Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#search-tabs" ).tabs( ).addClass( "tab-shadow" );

});

</script>

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

  $where = $product . $group . $inwork . $location;

?>
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Lynda's Spreadsheet</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<h2>Lynda's Spreadsheet Report</h2>

<p>Just some notes on the output.</p>

<ul>
  <li><strong>Company</strong> - Not captured. Expected is basically TRDO or Intrado and Positron Systems</li>
  <li><strong>Comment</strong> - Not captured.</li>
  <li><strong>Description</strong> - Not captured. Mainly the vendor and model information.</li>
  <li><strong>Custodian</strong> - If the Custodian is inactive/gone from the company or just not in the Inventory as a user, this is 'Unknown'</li>
  <li><strong>BUC</strong> - Same as Custodian</li>
  <li><strong>Business</strong> - If not in the Inventory then Unknown. Can be updated from the Inventory</li>
  <li><strong>Department</strong> - If not in the Inventory then Unknown. Can be updated from the Inventory</li>
  <li><strong>Project</strong> - Not captured. I already have product information. If no project id was saved for the hardware, I pulled Product name associated with the server</li>
  <li><strong>Quantity</strong> - Not captured. There can only be one.</li>
</ul>

</div>

</div>

<p></p>

<?php

  if ($formVars['csv'] == 'true') {
    print "<div class=\"main-help ui-widget-content\">\n";

    print "\"Vendor\",\"Company\",\"PO\",\"Comment\",\"Description\",Start Date\",\"End Date\",\"Custodian\",\"BUC\",\"BU\",\"Dept\",\"Acct\",\"Project #\",\"Location #\",\"Customer #\",\"Quantity\",\"Serial Number\",\"System Name\",\"Coverage\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Vendor</th>\n";
    print "  <th class=\"ui-state-default\">Company</th>\n";
    print "  <th class=\"ui-state-default\">Purchase Order</th>\n";
    print "  <th class=\"ui-state-default\">Comment</th>\n";
    print "  <th class=\"ui-state-default\">Description</th>\n";
    print "  <th class=\"ui-state-default\">Start Date</th>\n";
    print "  <th class=\"ui-state-default\">End Date</th>\n";
    print "  <th class=\"ui-state-default\">Custodian</th>\n";
    print "  <th class=\"ui-state-default\">BUC</th>\n";
    print "  <th class=\"ui-state-default\">Business</th>\n";
    print "  <th class=\"ui-state-default\">Department</th>\n";
    print "  <th class=\"ui-state-default\">Expense Account</th>\n";
    print "  <th class=\"ui-state-default\">Project</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Customer</th>\n";
    print "  <th class=\"ui-state-default\">Quantity</th>\n";
    print "  <th class=\"ui-state-default\">Serial Number</th>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Coverage</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_status,mod_vendor,mod_name,ct_city,st_acronym,slv_value,sup_company,inv_response,";
  $q_string .= "hw_serial,hw_projectid,hw_product,hw_asset,hw_built,hw_active,hw_retired,hw_reused,hw_poid,hw_supid_verified,hw_supportstart,hw_supportend,";
  $q_string .= "hw_custodian,hw_buc,hw_business,hw_dept,hw_expense,hw_customer ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware     on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models       on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join support      on support.sup_id        = hardware.hw_supportid ";
  $q_string .= "left join supportlevel on supportlevel.slv_id   = hardware.hw_response ";
  $q_string .= "left join locations    on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities       on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join states       on states.st_id          = locations.loc_state ";
  $q_string .= $where . " and inv_status = 0 and hw_supid_verified = 1 ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {
    $class = "ui-widget-content";
    if ($a_inventory['hw_supid_verified'] == 0) {
      $class = "ui-state-highlight";
    }
    if ($a_inventory['hw_serial'] == '') {
      $class = "ui-state-error";
    }

    $q_string  = "select slv_value ";
    $q_string .= "from supportlevel ";
    $q_string .= "where slv_id = " . $a_inventory['inv_response'] . " ";
    $q_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_supportlevel = mysqli_fetch_array($q_supportlevel);

    $q_string  = "select usr_first,usr_last ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_inventory['hw_custodian'] . " ";
    $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_users) > 0) {
      $a_users = mysqli_fetch_array($q_users);
      $custodian = $a_users['usr_first'] . " " . $a_users['usr_last'];
    } else {
      $custodian = 'Unknown';
    }

    $q_string  = "select usr_first,usr_last ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_inventory['hw_buc'] . " ";
    $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_users) > 0) {
      $a_users = mysqli_fetch_array($q_users);
      $buc = $a_users['usr_first'] . " " . $a_users['usr_last'];
    } else {
      $buc = 'Unknown';
    }

    $q_string  = "select bus_name ";
    $q_string .= "from business_unit ";
    $q_string .= "where bus_id = " . $a_inventory['hw_business'] . " ";
    $q_business_unit = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_business_unit) > 0) {
      $a_business_unit = mysqli_fetch_array($q_business_unit);
      $business_unit = $a_business_unit['bus_name'];
    } else {
      $business_unit = 'Unknown';
    }

    $q_string  = "select po_number ";
    $q_string .= "from purchaseorder ";
    $q_string .= "where po_id = " . $a_inventory['hw_poid'] . " ";
    $q_purchaseorder = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_purchaseorder) > 0) {
      $a_purchaseorder = mysqli_fetch_array($q_purchaseorder);
      $purchaseorder = $a_purchaseorder['po_number'];
    } else {
      $purchaseorder = 'Unknown';
    }

    $q_string  = "select dep_name ";
    $q_string .= "from department ";
    $q_string .= "where dep_unit = " . $a_inventory['hw_business'] . " and dep_dept = " . $a_inventory['hw_dept'] . " ";
    $q_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_department) > 0) {
      $a_department = mysqli_fetch_array($q_department);
      $department = $a_department['dep_name'];
    } else {
      $department = 'Unknown';
    }

    if ($a_inventory['hw_projectid'] == 0) {
      $q_string  = "select prod_name ";
      $q_string .= "from products ";
      $q_string .= "where prod_id = " . $a_inventory['hw_product'] . " ";
      $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_products) > 0) {
        $a_products = mysqli_fetch_array($q_products);
        $products = $a_products['prod_name'];
      } else {
        $products = 'Unknown';
      }
    } else {
      $products = $a_inventory['hw_projectid'];
    }

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    if ($formVars['csv'] == 'true') {
      print "\"" . $a_inventory['sup_company'] . "\",";
      print "\"" . "Blank" . "\",";
      print "\"" . $purchaseorder . "\",";
      print "\"" . "Blank" . "\",";
      print "\"" . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name'] . "\",";
      print "\"" . $a_inventory['hw_supportstart'] . "\",";
      print "\"" . $a_inventory['hw_supportend'] . "\",";
      print "\"" . $custodian . "\",";
      print "\"" . $buc . "\",";
      print "\"" . $business_unit . "\",";
      print "\"" . $department . "\",";
      print "\"" . $a_inventory['hw_expense'] . "\",";
      print "\"" . $products . "\",";
      print "\"" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . "\",";
      print "\"" . $a_inventory['hw_customer'] . "\",";
      print "\"" . "Blank" . "\",";
      print "\"" . $a_inventory['hw_serial'] . "\",";
      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . $a_inventory['slv_value'] . "\"</br>";
    } else {
      print "<tr>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['sup_company']                                    . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . "Blank"                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $purchaseorder                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . "Blank"                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name']   . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportstart']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportend']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $custodian                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $buc                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $business_unit                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $department                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_expense']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $products                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym']            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_customer']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . "Blank"                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_serial']                                              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['inv_name']                                              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['slv_value']                                            . "</td>\n";
      print "</tr>\n";
    }
  }

  if ($formVars['csv'] == 'true') {
    print "</div>\n";
  } else {
    print "</table>\n";
  }

  print "</div>\n";

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
