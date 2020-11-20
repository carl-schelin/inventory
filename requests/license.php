<?php
# Script: license.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "license.php";

  logaccess($formVars['uid'], $package, "Viewing License Info");

  if (isset($_GET['group'])) {
    $formVars['group'] = trim(clean($_GET['group'], 10));
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

  if (isset($_GET['license'])) {
    $formVars['license'] = trim(clean($_GET['license'], 10));
  } else {
    $formVars['license'] = -1;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Viewing License Info</title>

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
  <th class="ui-state-default">Server</th>
  <th class="ui-state-default">Serial Number</th>
  <th class="ui-state-default">Asset Tag</th>
  <th class="ui-state-default">Support Info</th>
  <th class="ui-state-default">Model</th>
</tr>
<?php

  $support = "";
  if ($formVars['license'] == 0) {
    $support = "hw_supportid = 0 and ";
  }

  $q_string  = "select inv_name,hw_serial,hw_asset,sup_company,mod_vendor ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join support on support.sup_id = hardware.hw_supportid ";
  $q_string .= "where " . $support . "inv_status = 0 ";
  $q_string .= "and hw_group = " . $formVars['group'] . " and hw_primary = 1 and inv_ssh = 1 and inv_virtual = 0 ";
  $q_string .= "order by inv_name";
  $q_hardware = mysqli_query($db, $q_string) or die (mysqli_error($db));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['inv_name']    . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['hw_serial']   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['hw_asset']    . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['sup_company'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_vendor']  . "</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
