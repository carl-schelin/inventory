<?php
# Script: filesystems.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "filesystems.php";

  logaccess($db, $formVars['uid'], $package, "Listing the Filesystems.");

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
    $orderby = "order by inv_name,fs_mount";
    $_SESSION['sort'] = '';
  }

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
  if ($formVars['group'] == 1) {
    $group = $and . " (fs_group = " . $formVars['group'] . " or fs_group = 0) ";
    $and = " and";
  } else {
    if ($formVars['group'] > 0) {
      $group = $and . " fs_group = " . $formVars['group'] . " ";
      $and = " and";
    }
  }

  if ($formVars['inwork'] == 'true') {
    $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
    $and = " and";
  } else {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
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
<title>Filesystem Listing</title>

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

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Filesystem Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>The intention is to provide an alternate method of monitoring file systems. This is key for environments like Development, SQA, and the CIL or any environment where monitoring is unable to be installed.</p>\n";

  print "<p>The columns should be pretty clear. You can click on a header to sort the column if you choose.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"      . $passthrough . "\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_function"  . $passthrough . "\">Function</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=prod_name"     . $passthrough . "\">Product</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fs_device"     . $passthrough . "\">Device</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fs_mount"      . $passthrough . "\">Mount</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fs_size"       . $passthrough . "\">Size</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fs_used"       . $passthrough . "\">Used</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fs_avail"      . $passthrough . "\">Available</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fs_percent"    . $passthrough . "\">Percent</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=fs_update"     . $passthrough . "\">Update</a></th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,inv_function,prod_name,fs_id,fs_device,fs_mount,fs_size,fs_used,fs_avail,fs_percent,fs_verified,fs_update ";
  $q_string .= "from filesystem ";
  $q_string .= "left join inventory on inventory.inv_id      = filesystem.fs_companyid ";
  $q_string .= "left join products on products.prod_id       = inventory.inv_product ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= $where . " ";
  $q_string .= $orderby;
  $q_filesystem = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_filesystem) > 0) {
    while ($a_filesystem = mysqli_fetch_array($q_filesystem)) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_filesystem['inv_id'] . "#filesystem\">";
      $linkend   = "</a>";

      $class = "ui-widget-content";
      if ($a_filesystem['fs_percent'] >= 85) {
        $class = "ui-state-highlight";
      }
      if ($a_filesystem['fs_percent'] >= 95) {
        $class = "ui-state-error";
      }

      $checked = "";
      if ($a_filesystem['fs_verified']) {
        $checked = "&#x2713;";
      }

      print "<tr>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_filesystem['inv_name']  . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['inv_function']         . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['prod_name']            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['fs_device']            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['fs_mount']             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['fs_size']              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['fs_used']              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['fs_avail']             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['fs_percent']           . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_filesystem['fs_update'] . $checked . "</td>\n";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"10\">No records found</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
