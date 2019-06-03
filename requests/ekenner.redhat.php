<?php
# Script: ekenner.redhat.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "ekenner.redhat.php";

  logaccess($formVars['uid'], $package, "Searching for Red Hat Installations.");

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 1;
  }

  $where = "where inv_status = 0 ";
  if ($formVars['group'] != -1) {
    $where .= " and grp_id = " . $formVars['group'] . " ";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Red Hat Installations</title>

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

# need to identify red hat systems
# 1 how many are virtual
# 2 how many are physical
# 3 how many in each area (lab, production)


  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Virtual</th>\n";
  print "  <th class=\"ui-state-default\">Physical</th>\n";
  print "</tr>\n";

  $q_string  = "select count(*) ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware  on inventory.inv_id        = hardware.hw_companyid ";
  $q_string .= "left join software  on inventory.inv_id        = software.sw_companyid ";
  $q_string .= "left join groups    on groups.grp_id           = hardware.hw_group ";
  $q_string .= "left join models    on models.mod_id           = hardware.hw_vendorid ";
  $q_string .= "left join support   on support.sup_id          = hardware.hw_supportid ";
  $q_string .= "left join products  on products.prod_id        = inventory.inv_product ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 and mod_virtual = 1 and hw_primary = 1 and sw_software like '%red hat%' ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_inventory = mysql_fetch_array($q_inventory);

  $virtual = $a_inventory['count(*)'];

  $q_string  = "select count(*) ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware  on inventory.inv_id        = hardware.hw_companyid ";
  $q_string .= "left join software  on inventory.inv_id        = software.sw_companyid ";
  $q_string .= "left join groups    on groups.grp_id           = hardware.hw_group ";
  $q_string .= "left join models    on models.mod_id           = hardware.hw_vendorid ";
  $q_string .= "left join support   on support.sup_id          = hardware.hw_supportid ";
  $q_string .= "left join products  on products.prod_id        = inventory.inv_product ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 and mod_virtual = 0 and hw_primary = 1 and sw_software like '%red hat%' ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_inventory = mysql_fetch_array($q_inventory);

  $physical = $a_inventory['count(*)'];

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">" . $virtual  . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $physical . "</td>\n";
  print "</tr>\n";

?>
</table>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
