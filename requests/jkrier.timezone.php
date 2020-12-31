<?php
# Script: jkrier.timezone.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "jkrier.timezone.php";

  logaccess($db, $formVars[' uid'], $package, "Checking out the Server timezones.");

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $formVars['sort'] = trim(clean($_GET['sort'], 30));
    $orderby .= $formVars['sort'] . ",";
  }
  $orderby .= "prod_name,inv_name";

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Janet Krier: Timezone</title>

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

<table>
<?php

  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name\">Server Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_zone\">Timezone</a></th>\n";
  print "</tr>\n";

  $product = "";

  $q_string  = "select inv_id,zone_name,inv_name,prod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "where (inv_product = 70 or inv_product = 62 or inv_product = 30 or ";
  $q_string .= "inv_product = 41 or inv_product = 40 or inv_product = 31 or ";
  $q_string .= "inv_product = 102 or inv_product = 103 or inv_product = 28 or ";
  $q_string .= "inv_product = 2 or inv_product = 83) ";
  $q_string .= "and hw_primary = 1 and (hw_retired = '1971-01-01' and hw_reused = '1971-01-01') ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die("Inventory: " . $q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if ($product != $a_inventory['prod_name']) {
      $product = $a_inventory['prod_name'];
      print "<tr>\n";
      print "  <th class=\"ui-state-default\" colspan=\"2\">" . $product . "</th>\n";
      print "</tr>\n";
    }

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']  . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['zone_name']            . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
