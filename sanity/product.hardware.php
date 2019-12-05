<?php
# Script: product.hardware.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "product.hardware.php";

  logaccess($formVars['uid'], $package, "Accessing script");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
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
    $orderby = " order by grp_name,inv_name";
    $_SESSION['sort'] = '';
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Product Hardware Sanity Listing</title>

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

<?php

  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
    $and = " and";
  }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

  $location = '';
  if ($formVars['country'] == 0 && $formVars['location'] > 0) {
    $location = $and . " inv_location = " . $formVars['location'] . " ";
    $and = " and";
  } else {
    if ($formVars['country'] > 0) {
      $location .= $and . " loc_country = " . $formVars['country'] . " ";
      $and = " and";
    }
    if ($formVars['state'] > 0) {
      $location .= $and . " loc_state = " . $formVars['state'] . " ";
      $and = " and";
    }
    if ($formVars['city'] > 0) {
      $location .= $and . " loc_city = " . $formVars['city'] . " ";
      $and = " and";
    }
    if ($formVars['location'] > 0) {
      $location .= $and . " inv_location = " . $formVars['location'] . " ";
      $and = " and";
    }
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $inwork . $location . $type;

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">Product Hardware Sanity Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"display:none\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all hardware that has a product id of zero, as in not associated with any product.</p>\n";

  print "</div>\n";

  print "</div>\n";

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">Server</th>\n";
  print "  <th class=\"ui-state-default\">Server Product</th>\n";
  print "  <th class=\"ui-state-default\">Custodian</th>\n";
  print "  <th class=\"ui-state-default\">Hardware</th>\n";
  print "  <th class=\"ui-state-default\">Hardware Product</th>\n";
  print "</tr>";

  $q_string  = "select inv_id,inv_name,grp_name,prod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware  on hardware.hw_companyid   = inventory.inv_id ";
  $q_string .= "left join locations on locations.loc_id        = inventory.inv_location ";
  $q_string .= "left join groups    on groups.grp_id           = inventory.inv_manager ";
  $q_string .= "left join products  on products.prod_id        = inventory.inv_product ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

   $editstart = '';
    $editend = '';
    if (check_userlevel($AL_Edit)) {
      $editpencil = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/pencil.gif\" height=\"10\"></a>";
      if (check_grouplevel($a_inventory['inv_manager'])) {
        $editstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\"          target=\"_blank\">" . $editpencil;
        $editend = "</a>";
      }
    }

    $q_string  = "select hw_id,hw_type,prod_name,mod_name ";
    $q_string .= "from hardware ";
    $q_string .= "left join products on products.prod_id = hardware.hw_product ";
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_product = 0 ";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_hardware = mysql_fetch_array($q_hardware)) {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $editstart . $a_inventory['inv_name']  . $editend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $editstart . $a_inventory['prod_name'] . $editend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $editstart . $a_inventory['grp_name']  . $editend . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_hardware['mod_name']              . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_hardware['prod_name']             . "</td>\n";
      print "</tr>";
    }
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
