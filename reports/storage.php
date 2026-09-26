<?php
# Script: storage.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "storage.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

  $formVars['product'] = 0;
  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  }
  $formVars['group'] = 0;
  if (isset($_GET['group'])) {
    $formVars['group']     = clean($_GET['group'],    10);
  }
  $formVars['inwork'] = false;
  if (isset($_GET['inwork'])) {
    $formVars['inwork']    = clean($_GET['inwork'],   10);
  }
  $formVars['country'] = 0;
  if (isset($_GET['country'])) {
    $formVars['country']   = clean($_GET['country'],  10);
  }
  $formVars['state'] = 0;
  if (isset($_GET['state'])) {
    $formVars['state']     = clean($_GET['state'],    10);
  }
  $formVars['city'] = 0;
  if (isset($_GET['city'])) {
    $formVars['city']      = clean($_GET['city'],     10);
  }
  $formVars['location'] = 0;
  if (isset($_GET['location'])) {
    $formVars['location']  = clean($_GET['location'], 10);
  }

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
<title>Storage Listing</title>

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
      $product = $and . " sw_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " sw_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " svr_groupid = " . $formVars['group'] . " ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
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

  $passthrough = 
    "&group="    . $formVars['group']    .
    "&product="  . $formVars['product']  .
    "&inwork="   . $formVars['inwork']   .
    "&type="     . $formVars['type']     .
    "&country="  . $formVars['country']  .
    "&state="    . $formVars['state']    .
    "&city="     . $formVars['city']     .
    "&location=" . $formVars['location'];
?>
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Storage Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This page presents a list of the software installed on the systems as identified by the filters on the main page.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="<php print $package; ?>?sort=inv_name<?php    print $passthrough; ?>">Server</a></th>
  <th class="ui-state-default"><a href="<php print $package; ?>?sort=prod_name<?php   print $passthrough; ?>">Product</a></th>
  <th class="ui-state-default"><a href="<php print $package; ?>?sort=ven_name<?php    print $passthrough; ?>">Vendor</a></th>
  <th class="ui-state-default"><a href="<php print $package; ?>?sort=sw_software<?php print $passthrough; ?>">Software</a></th>
  <th class="ui-state-default"><a href="<php print $package; ?>?sort=typ_name<?php    print $passthrough; ?>">Type</a></th>
  <th class="ui-state-default"><a href="<php print $package; ?>?sort=grp_name<?php    print $passthrough; ?>">Group</a></th>
  <th class="ui-state-default"><a href="<php print $package; ?>?sort=svr_update<?php  print $passthrough; ?>">Updated</a></th>
</tr>

<?php
# select Hard Disk or Solid State Drive

  $q_string  = "select inv_name,prod_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_products on inv_products.prod_id = inv_inventory.inv_product ";
  $q_string .= "order by inv_name ";
#  $q_string .= $where;
#  $q_string .= $orderby;
  $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_inventory)) {
    while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

#      $q_string  = "select mod_name ";
#      $q_string .= "from inv_models ";
#      $q_string j

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inv_inventory['inv_name']                  . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inv_inventory['prod_name']                  . "</a></td>\n";
      print "</tr>";

    }
  }
  mysqli_free_result($q_inv_inventory);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
