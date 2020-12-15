<?php
# Script: theath.curvature.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "theath.curvature.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

  if (isset($_GET['group'])) {
    $formVars['group'] = "and inv_manager = " . clean($_GET['group'], 10) . " ";
  } else {
    $formVars['group'] = '';
  }

  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
  } else {
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
    $orderby = " order by inv_name";
    $_SESSION['sort'] = '';
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Server Listing</title>

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
  print "  <th class=\"ui-state-default\">Curvature Server Support Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"display:none\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page presents a list of all servers where the support contract is under Curvature.</p>\n";

  print "</div>\n";

  print "</div>\n";

  if ($formVars['csv'] == 'true') {
    print "<p style=\";\">\"Server\",";
    print "\"Vendor\",";
    print "\"Model\",";
    print "\"Serial Number\",";
    print "\"Asset Tag\"</br>";
  } else {
    print "<table class=\"ui-styled-table\">";
    print "<tr>";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"     . "\">Server</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_vendor"    . "\">Vendor</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_name"    . "\">Model</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_serial"  . "\">Serial Number</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_asset"      . "\">Asset Tag</a></th>\n";
    print "</tr>";
  }

  $q_string  = "select inv_name,hw_id,mod_vendor,mod_name,hw_serial,hw_asset ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join support on support.sup_id = hardware.hw_supportid ";
  $q_string .= "where inv_status = 0 and sup_company = \"Curvature\" and hw_primary = 1 " . $formVars['group'];
  $q_string .= $orderby;
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    if ($formVars['csv'] == 'true') {
      print "\"" . $a_hardware['inv_name'] . "\",";
      print "\"" . $a_hardware['mod_vendor'] . "\",";
      print "\"" . $a_hardware['mod_name'] . "\",";
      print "\"" . $a_hardware['hw_serial'] . "\",";
      print "\"" . $a_hardware['hw_asset'] . "\"</br>";
    } else {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $a_hardware['inv_name']   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_vendor'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_name']   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_hardware['hw_serial']  . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_hardware['hw_asset']   . "</td>\n";
      print "</tr>";
    }
  }

  mysqli_free_result($q_hardware);

  if ($formVars['csv'] == 'true') {
    print "</p>\n";
  } else {
    print "</table>\n";
  }

?>
</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
