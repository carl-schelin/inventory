<?php
# Script: dba.report.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "dba.report.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the inventory.");

# these are passed to each report
  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['location']  = clean($_GET['location'], 10);

# this is only passwd for complete listings
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }

# passed when a sort header is clicked
  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = " order by inv_name";
    $_SESSION['sort'] = '';
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
      $and = " and";
    }
  }

  if ($formVars['group'] == 0) {
    $group = '';
  } else {
    if ($formVars['group'] == -1) {
      $group = $and . " sw_group = 0 ";
      $and = " and";
    } else {
      $group = $and . " sw_group = " . $formVars['group'] . " ";
      $and = " and";
    }
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
    $and = " and";
  }

  if ($formVars['location'] == 0) {
    $location = '';
  } else {
    $location = $and . " inv_location = " . $formVars['location'] . " ";
    $and = " and";
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $inwork . $group . $location . $type;

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DBA Report</title>

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

  $sorting = "<a href=\"" . $package . "?group=" . $formVars['group'] . "&inwork=" . $formVars['inwork'] . "&product=" . $formVars['product'] . "&location=" . $formVars['location'] . "&type=" . $formVars['type'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Database Report</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\">Help</th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . $sorting . "&sort=inv_name\">System Name</a></th>\n";
  print "  <th class=\"ui-state-default\">" . $sorting . "&sort=int_ipaddr\">IP Address</a></th>\n";
  print "  <th class=\"ui-state-default\">" . $sorting . "&sort=inv_function\">Function</a></th>\n";
  print "  <th class=\"ui-state-default\">" . $sorting . "&sort=sw_software\">Software</a></th>\n";
  print "  <th class=\"ui-state-default\">" . $sorting . "&sort=sw_type\">Instance Name</a></th>\n";
  print "  <th class=\"ui-state-default\">" . $sorting . "&sort=hw_type\">Hardware</a></th>\n";
  print "  <th class=\"ui-state-default\">" . $sorting . "&sort=sw_os\">Operating System</a></th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,inv_function,sw_id,sw_companyid,sw_software,sw_vendor,sw_product,sw_type,sw_group,sw_verified ";
  $q_string .= "from inventory ";
  $q_string .= "left join software  on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= $where;
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $interface = "";
    $console = "";

    $q_string  = "select int_face,int_addr,int_type,itp_acronym,int_ip6 ";
    $q_string .= "from interface ";
    $q_string .= "left join inttype on inttype.itp_id = interface.int_type ";
    $q_string .= "where int_companyid = " . $a_inventory['sw_companyid'] . " and int_type != 7 and int_type != 6 and int_addr != '' and int_ip6 = 0 ";
    $q_string .= "order by itp_acronym ";
    $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    while ($a_interface = mysqli_fetch_array($q_interface)) {
      $interface .= $a_interface['itp_acronym'] . "=" . $a_interface['int_addr'] . " ";
    }

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_type = 'OS' and sw_companyid = " . $a_inventory['sw_companyid'];
    $q_os = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_os = mysqli_fetch_array($q_os);

    $instances = "";
    $comma = "";
    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_type = 'Instance' and sw_companyid = " . $a_inventory['sw_companyid'] . " ";
    $q_string .= "order by sw_software";
    $q_instance = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    while ($a_instance = mysqli_fetch_array($q_instance)) {
      $instances .= $comma . $a_instance['sw_software'];
      $comma = ", ";
    }

    $q_string  = "select hw_vendorid ";
    $q_string .= "from hardware ";
    $q_string .= "where hw_companyid = " . $a_inventory['sw_companyid'] . " and hw_type = 15 ";
    $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_hardware = mysqli_fetch_array($q_hardware);

    if ($a_hardware['hw_vendorid'] != '') {
      $q_string  = "select mod_name ";
      $q_string .= "from models ";
      $q_string .= "where mod_id = " . $a_hardware['hw_vendorid'];
      $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_models = mysqli_fetch_array($q_models);
    }

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $interface . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_function'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['sw_software'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $instances . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_models['mod_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_os['sw_software'] . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
