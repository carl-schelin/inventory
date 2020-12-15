<?php
# Script: llilly.sunmiami.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "llilly.sunmiami.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the Miami Sun inventory.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Lynda Lilly: Sun Equipment in Miami</title>

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
<tr>
  <th class="ui-state-default">System Name</th>
  <th class="ui-state-default">Vendor</th>
  <th class="ui-state-default">Model</th>
  <th class="ui-state-default">Location</th>
  <th class="ui-state-default">Serial #</th>
  <th class="ui-state-default">Asset Tag</th>
</tr>
<?php

  $q_string  = "select inv_id,inv_name,mod_vendor,mod_name,ct_city,hw_serial,hw_asset ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "where inv_status = 0 and mod_vendor = 'Sun' and ct_city = 'Miami' ";
  $q_string .= "order by hw_serial";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {
    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']   . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['mod_vendor']            . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['mod_name']              . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['ct_city']               . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['hw_serial']             . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['hw_asset']              . "</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
