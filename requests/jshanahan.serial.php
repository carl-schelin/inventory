<?php
# Script: jshanahan.serial.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "jshanahan.serial.php";

  logaccess($formVars['uid'], $package, "Accessing script");

  if (isset($_GET["csv"])) {
    $formVars['csv'] = clean($_GET["csv"], 10);
  } else {
    $formVars['csv'] = 'false';
  }

  $formVars['group'] = 0;
  if (isset($_GET["group"])) {
    $formVars['group'] = clean($_GET["group"], 10);
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Physical Server Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/FormTables/formTables.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/FormTables/formTables.css">

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">Server Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"display:none\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page presents a list of all physical servers.</p>\n";

  print "</div>\n";

  print "</div>\n";

  if ($formVars['csv'] == 'true') {
    print "\"Server\",";
    print "\"Asset Tag\",";
    print "\"Serial Number\"</br>";
  } else {
    print "<table class=\"ui-styled-table\">";
    print "<tr>";
    print "  <th class=\"ui-state-default\">Server</th>\n";
    print "  <th class=\"ui-state-default\">Custodian</th>\n";
    print "  <th class=\"ui-state-default\">Asset Tag</th>\n";
    print "  <th class=\"ui-state-default\">Serial Number</th>\n";
    print "</tr>";
  }

  $q_string  = "select inv_name,hw_id,hw_companyid,hw_asset,hw_serial,grp_name ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where inv_status = 0 and mod_virtual = 0 and hw_primary = 1 and hw_deleted = 0 ";
  if ($formVars['group'] > 0) {
    $q_string .= "and inv_manager = " . $formVars['group'] . " ";
  }
  $q_string .= "order by inv_name ";
  $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_hardware = mysql_fetch_array($q_hardware)) {

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_hardware['hw_companyid'] . "\">";
    $linkend = "</a>";

    if ($formVars['csv'] == 'true') {
      print "\"" . $a_hardware['inv_name'] . "\",";
      print "\"" . $a_hardware['grp_name'] . "\",";
      print "\"" . $a_hardware['hw_asset'] . "\",";
      print "\"" . $a_hardware['hw_serial'] . "\"</br>";
    } else {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['inv_name']  . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_hardware['grp_name']             . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_hardware['hw_asset']             . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_hardware['hw_serial']            . "</td>\n";
      print "</tr>";
    }
  }

  mysql_free_result($q_hardware);

  if ($formVars['csv'] == 'true') {
    print "</div>\n";
  } else {
    print "</table>\n";
    print "</div>\n";
  }
?>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
