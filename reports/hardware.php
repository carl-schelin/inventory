<?php
# Script: hardware.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "hardware.php";

  logaccess($formVars['uid'], $package, "Checking out the hardware.");

  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }
  if (isset($_GET['inwork'])) {
    $formVars['inwork']    = clean($_GET['inwork'],   10);
  } else {
    $formVars['inwork'] = 'false';
  }
  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  } else {
    $formVars['product'] = 0;
  }
  if (isset($_GET['group'])) {
    $formVars['group']     = clean($_GET['group'],    10);
  } else {
    $formVars['group'] = 0;
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
    $orderby = "order by inv_name,hw_type";
    $_SESSION['sort'] = '';
  }

# if help has not been seen yet,
  if (show_Help($Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Hardware Report</title>

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

  $and = " where mod_virtual = 0 and ";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " hw_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " hw_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " hw_group = " . $formVars['group'] . " ";
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

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Hardware Table</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>The purpose of this report is to provide a list of hardware based on the Filter criteria.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";


  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"   . $passthrough . "\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_serial"  . $passthrough . "\">Serial</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_asset"   . $passthrough . "\">Asset</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_product" . $passthrough . "\">Product</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_name"   . $passthrough . "\">Model</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_size"    . $passthrough . "\">Size</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_speed"   . $passthrough . "\">Speed</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_type"    . $passthrough . "\">Type</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_updated" . $passthrough . "\">Updated</a></th>\n";
  print "</tr>\n";

  $q_string  = "select hw_id,inv_name,part_name,hw_serial,hw_asset,hw_speed,inv_status,hw_deleted,";
  $q_string .= "hw_size,mod_name,prod_name,hw_active,hw_retired,hw_group,hw_supportid,hw_primary,hw_verified,hw_update ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join models    on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join parts     on parts.part_id    = hardware.hw_type ";
  $q_string .= "left join products  on products.prod_id = hardware.hw_product ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_hardware = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_hardware = mysql_fetch_array($q_hardware)) {

    if ($a_hardware['hw_deleted'] == 1) {
      $class = " class=\"ui-state-highlight\"";
    } else {
      if ($a_hardware['inv_status'] == 1) {
        $class = " class=\"ui-state-error\"";
      } else {
        $class = " class=\"ui-widget-content\"";
      }
    }

    $checkmark = '';
    if ($a_hardware['hw_verified']) {
      $checkmark = "&#x2713;&nbsp;";
    }

    print "<tr>";
    print "  <td" . $class . ">" . $a_hardware['inv_name']                . "</td>";
    print "  <td" . $class . ">" . $a_hardware['hw_serial']               . "</td>";
    print "  <td" . $class . ">" . $a_hardware['hw_asset']                . "</td>";
    print "  <td" . $class . ">" . $a_hardware['prod_name']               . "</td>";
    print "  <td" . $class . ">" . $a_hardware['mod_name']                . "</td>";
    print "  <td" . $class . ">" . $a_hardware['hw_size']                 . "</td>";
    print "  <td" . $class . ">" . $a_hardware['hw_speed']                . "</td>";
    print "  <td" . $class . ">" . $a_hardware['part_name']               . "</td>";
    print "  <td" . $class . ">" . $a_hardware['hw_update']  . $checkmark . "</td>";
    print "</tr>";

  }

  mysql_free_result($q_hardware);
?>
</table>
</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
