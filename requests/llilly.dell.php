<?php
# Script: llilly.dell.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "llilly.dell.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the Miami Sun inventory.");

  $formVars['csv'] = 'false';
  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
  }

  if ($formVars['csv'] == 'true') {
    $formVars['csv'] = 1;
  } else {
    $formVars['csv'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Lynda Lilly: Dell Equipment</title>

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
  if ($formVars['csv'] == 0) {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Vendor</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Address</th>\n";
    print "  <th class=\"ui-state-default\">Address</th>\n";
    print "  <th class=\"ui-state-default\">City/County</th>\n";
    print "  <th class=\"ui-state-default\">State</th>\n";
    print "  <th class=\"ui-state-default\">Zipcode</th>\n";
    print "  <th class=\"ui-state-default\">Serial #</th>\n";
    print "  <th class=\"ui-state-default\">Asset Tag</th>\n";
    print "  <th class=\"ui-state-default\">Custodian</th>\n";
    print "</tr>\n";
  } else {
    print "<p>\"System Name\",";
    print "\"Vendor\",";
    print "\"Model\",";
    print "\"Address\",";
    print "\"Address\",";
    print "\"City/Count\",";
    print "\"State\",";
    print "\"Zipcode\",";
    print "\"Serial #\",";
    print "\"Asset Tag\",";
    print "\"Custodian\"</br>";
  }

  $q_string  = "select inv_id,inv_name,mod_vendor,mod_name,ct_city,loc_addr1,loc_addr2,loc_county,st_state,loc_zipcode,hw_serial,hw_asset,grp_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = cities.ct_state ";
  $q_string .= "where inv_status = 0 and mod_vendor = 'Dell' ";
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {

    if ($formVars['csv'] == 0) {
      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
      $linkend   = "</a>";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']   . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['mod_vendor']            . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['mod_name']              . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['loc_addr1']               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['loc_addr2']               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['ct_city']               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['st_state']               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['loc_zipcode']               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['hw_serial']             . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['hw_asset']              . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['grp_name']              . "</td>\n";
      print "</tr>\n";
    } else {
      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . $a_inventory['mod_vendor'] . "\",";
      print "\"" . $a_inventory['mod_name'] . "\",";
      print "\"" . $a_inventory['loc_addr1'] . "\",";
      print "\"" . $a_inventory['loc_addr2'] . "\",";
      print "\"" . $a_inventory['ct_city'] . "\",";
      print "\"" . $a_inventory['st_state'] . "\",";
      print "\"" . $a_inventory['loc_zipcode'] . "\",";
      print "\"" . $a_inventory['hw_serial'] . "\",";
      print "\"" . $a_inventory['hw_asset'] . "\",";
      print "\"" . $a_inventory['grp_name'] . "\"</br>";
    }
  }

  if ($formVars['csv'] == 0) {
    print "</table>\n";
    print "<p class=\"ui-widget-content\"><a href=\"" . $Siteroot . "/requests/llilly.dell.php?csv=true\">Click here for csv output.</a></p>\n";
  } else {
    print "</p>\n";
  }

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
