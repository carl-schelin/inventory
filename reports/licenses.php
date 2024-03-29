<?php
# Script: licenses.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "licenses.php";

  logaccess($db, $formVars['uid'], $package, "Listing of Licenses.");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
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
    $orderby = " order by inv_name";
    $_SESSION['sort'] = '';
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
<title>License Report</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

# now build the where clause
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

  $where = $product . $group . $location . $type;

  $passthrough = 
    "&group="    . $formVars['group']    .
    "&product="  . $formVars['product']  .
    "&type="     . $formVars['type']     .
    "&country="  . $formVars['country']  .
    "&state="    . $formVars['state']    .
    "&city="     . $formVars['city']     .
    "&location=" . $formVars['location'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">License Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the software licenses that have been assigned. Licenses are managed in the License Manager and associated \n";
  print "with the software and system on the system Software edit page.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";


  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"    . $passthrough . "\">System Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=prod_name"   . $passthrough . "\">Product Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_software" . $passthrough . "\">Software</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=lic_product" . $passthrough . "\">License</a></th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,sw_software,lic_product,prod_name ";
  $q_string .= "from inv_software ";
  $q_string .= "left join inv_licenses     on inv_licenses.lic_id              = inv_software.sw_licenseid ";
  $q_string .= "left join inv_products     on inv_products.prod_id             = inv_software.sw_product ";
  $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid  = inv_software.sw_id ";
  $q_string .= "left join inv_inventory    on inv_inventory.inv_id             = inv_svr_software.svr_companyid ";
  $q_string .= "left join inv_locations    on inv_locations.loc_id             = inv_inventory.inv_location ";
  $q_string .= $where . " and sw_licenseid > 0 ";
  $q_string .= $orderby;
  $q_inv_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_software) > 0) {
    while ($a_inv_software = mysqli_fetch_array($q_inv_software)) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inv_software['inv_id'] . "\">";
      $linkend   = "</a>";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['inv_name']    . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['prod_name']   . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['sw_software'] . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['lic_product'] . $linkend . "</td>\n";
      print "</tr>\n";

    }
  }

?>
</table>
</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
