<?php
# Script: pschmidt.lab.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "pschmidt.lab.php";

  logaccess($db, $formVars['uid'], $package, "Getting a listing of Lab Application.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Pete Schmidt: Lab Applications</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . '/mobile.php'); ?>
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
  print "  <th class=\"ui-state-default\">System Name</th>\n";
  print "  <th class=\"ui-state-default\">Product</th>\n";
  print "  <th class=\"ui-state-default\">Application</th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,prod_name ";
  $q_string .= "from inventory ";
  $q_string .= "inner join products on inventory.inv_product = products.prod_id ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 and (inv_location = 31 or inv_location = 33 or inv_location = 34) ";
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $comma = "";
    $application = '';

    $q_string  = "select sw_software,prod_name,grp_name ";
    $q_string .= "from software ";
    $q_string .= "left join groups on groups.grp_id = software.sw_group ";
    $q_string .= "left join products on products.prod_id = software.sw_product ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type != 'OS' and sw_group != 1 ";
    $q_string .= "order by prod_name,grp_name";
    $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_software = mysqli_fetch_array($q_software)) {
      $application .= $comma . $a_software['sw_software'] . " (" . $a_software['prod_name'] . ":" . $a_software['grp_name'] . ")";
      $comma = "<br>";
    }

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']  . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['prod_name']            . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $application                         . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
