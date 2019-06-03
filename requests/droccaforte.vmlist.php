<?php
# Script: drocceforte.vmlist.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "droccaforte.vmlist.php";

  logaccess($formVars['uid'], $package, "Getting a listing of VMs and Applications.");

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

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Daniel Rocceforte: Virtual Machine Listing</title>

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
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=inv_name\">System Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=inv_function\">Function</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=svc_acronym\">Service Class</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=grp_name\">Device Owner</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=sw_software\">Operating System</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?group=" . $formVars['group'] . "&sort=ct_city\">Location</a></th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,inv_function,svc_acronym,ct_city,grp_name,zone_name,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join service on service.svc_id = inventory.inv_class ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where mod_virtual = 1 and sw_type = 'OS' ";
  $q_string .= $orderby;
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ( $a_inventory = mysql_fetch_array($q_inventory) ) {

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_name']                                         . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_function']                                     . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['svc_acronym']                                      . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['grp_name']                                         . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['sw_software']                                       . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['ct_city'] . " (" . $a_inventory['zone_name'] . ")" . "</td>\n";
    print "</tr>\n";

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type != 'RPM' and sw_type != 'PKG' and sw_type != 'OS' ";
    $q_string .= "order by sw_software";
    $q_software = mysql_query($q_string) or die(mysql_error());
    $software = "";
    while ($a_software = mysql_fetch_array($q_software)) {
      $software .= $a_software['sw_software'] . " ";
    }
    if ($software == "") {
      $software = "No applications identified";
    }

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"6\">" . $software . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
