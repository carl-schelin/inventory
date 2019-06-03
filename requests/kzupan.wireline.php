<?php
# Script: kzupan.wireline.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "kzupan.wireline.php";

  logaccess($formVars['uid'], $package, "Getting a listing of Wireline.");

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $formVars['sort']  = clean($_GET['sort'], 30);
    $orderby .= $formVars['sort'] . ",";
  }
  $orderby .= "inv_name";

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

  if (isset($_GET['type'])) {
    $formVars['type']  = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Kevin Zupan: Wireline Listing</title>

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


  $linkstart = "<a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=";
  $linkend   = "</a>";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_name\">"     . "System Name"       . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_function\">" . "Function"          . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_class\">"    . "Service Class"     . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_manager\">"  . "Device Owner"      . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">"                                  . "Operating System"             . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_location\">" . "Location"          . $linkend . "</th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,inv_function,svc_acronym,loc_city,zone_name,grp_name ";
  $q_string .= "from inventory ";
  $q_string .= "join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "join service on service.svc_id = inventory.inv_class ";
  $q_string .= "where inv_manager = 1 and sw_group = 7 order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ( $a_inventory = mysql_fetch_array($q_inventory) ) {

    $q_string  = "select sw_software ";
    $q_strinb .= "from software ";
    $q_strinb .= "where sw_type = 'OS' and sw_companyid = " . $a_inventory['inv_id'];
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']                                          . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['inv_function']                                                 . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['svc_acronym']                                                  . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['grp_name']                                                     . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_software['sw_software']                                                   . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['loc_city'] . " (" . $a_inventory['zone_name'] . ")"            . "</td>\n";
    print "</tr>\n";

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_group = 7 and sw_type != 'OS' ";
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
