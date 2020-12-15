<?php
# Script: kklovdahl.inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "kklovdahl.inventory.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the inventory.");

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
<title>Karen Klovdahl: Inventory Listing</title>

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

<?php

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=inv_name\">System Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=inv_ipaddress\">IP Address</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=inv_function\">Function</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=sw_software\">Software</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=sw_type\">Instance Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=hw_type\">Hardware</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=sw_os\">Operating System</a></th>\n";
  print "</tr>\n";

  $q_string  = "select sw_id,sw_companyid,sw_software,sw_vendor,sw_product,sw_type,sw_group,sw_verified,inv_name,inv_function ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where sw_group = " . $formVars['group'] . " and sw_type = \"Commercial\" ";
  $q_string .= "order by inv_name";
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ( $a_software = mysqli_fetch_array($q_software) ) {

    $interface = "";
    $console = "";
    $q_string  = "select int_face,int_addr ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = \"" . $a_software['sw_companyid'] . "\" and int_ip6 = 0 and int_eth != 'loopback' and int_face != 'netmgt:' ";
    $q_string .= "order by int_face";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_interface = mysqli_fetch_array($q_interface)) {
      $interface .= $a_interface['int_face'] . "=" . $a_interface['int_addr'] . " ";
    }

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_type = 'OS' and sw_companyid = " . $a_software['sw_companyid'];
    $q_os = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_os = mysqli_fetch_array($q_os);

    $instances = "";
    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_type = 'Instance' and sw_companyid = " . $a_software['sw_companyid'] . " ";
    $q_string .= "order by sw_software";
    $q_instance = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_instance = mysqli_fetch_array($q_instance)) {
      $instances .= $a_instance['sw_software'] . ", ";
    }

    $q_string  = "select mod_name ";
    $q_string .= "from hardware ";
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= "where hw_companyid = " . $a_software['sw_companyid'] . " and hw_type = 15";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_hardware = mysqli_fetch_array($q_hardware);

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['inv_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $interface . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['inv_function'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_software'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $instances . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_os['sw_software'] . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
