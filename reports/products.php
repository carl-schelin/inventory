<?php
# Script: products.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "products.php";

  logaccess($db, $formVars['uid'], $package, "Viewing the Products table");

  $formVars['csv']       = clean($_GET['csv'],        10);

  if ($formVars['csv'] == '') {
    $formVars['csv'] = 'false';
  }

# if help has not been seen yet,
  if (show_Help($db, $Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>View Intrado Products</title>

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

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Product Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This provides a list of all installed Intrado products. Clicking on the link brings up a document with three 
tabs displaying all the hardware associated with the product, the installed software, and all changelog entries.</p>

</div>

</div>

<table class="ui-styled-table">
<?php
  if ($formVars['csv'] == 'false') {
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Product Name</a>\n";
    print "  <th class=\"ui-state-default\">Product Description</th>\n";
    print "</tr>\n";
  } else {
    print "<p>\"Product Name\",";
    print "\"Product Description\"";
    print "</br>\n";
  }

  $q_string  = "select prod_id,prod_name,prod_desc ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name";
  $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_products = mysqli_fetch_array($q_products)) {

    if ($formVars['csv'] == 'false') {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\"><a href=\"show.product.php?id=" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_products['prod_desc'] . "</td>\n";
      print "</tr>\n";
    } else {
      print "\"" . $a_products['prod_name'] . "\",";
      print "\"" . $a_products['prod_desc'] . "\"";
      print "</br>\n";
    }

  }

  mysqli_free_result($q_products);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
