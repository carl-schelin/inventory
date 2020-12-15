<?php
# Script: tenable.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "tenable.php";

  logaccess($db, $formVars['uid'], $package, "Listing of Product IPs.");

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
<title>Security Center Report</title>

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
  print "  <th class=\"ui-state-default\">Tenable IP Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report provides the IPs of all the servers broken out by product and network zone. The intention is to manage the groups within Tenable in order to open tickets and request scans.</p>\n";

  print "<p>In each server's detail record, under networking, if a network zone hasn't been selected then the line will not have zone information and will be <span class=\"ui-state-highlight\">highlighted</span>.</p>\n";

  print "<p>Log in to <a href=\"https://10.100.5.10\">Tenable</a> and under the Assets tab, either update the existing entries or create new ones by clicking on the Add button at the top.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";


  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=prod_name"   . $passthrough . "\">Product Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_addr"    . $passthrough . "\">IP Addresses</a></th>\n";
  print "</tr>\n";

#select int_addr from interface left join inventory on inv_id = int_companyid where inv_status = 0 and inv_manager = 1 and int_ip6 = 0 and int_type != 7 and inv_product = 134 order by int_addr;

  $product = '';
  $zone = '';
  $prodip = '';

  $q_string  = "select inv_id,int_addr,prod_name,zone_zone ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id      = interface.int_companyid ";
  $q_string .= "left join products  on products.prod_id      = inventory.inv_product ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join ip_zones  on ip_zones.zone_id      = interface.int_zone ";
  $q_string .= $where . " and int_ip6 = 0 and int_addr != '' and int_addr != '0.0.0.0' and int_addr != '127.0.0.1' and (int_type = 1 or int_type = 2 or int_type = 4 or int_type = 6) ";
  $q_string .= "order by prod_name,zone_zone ";
  $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $class = "ui-widget-content";

      $linkstart = '';
      $linkend = '';
      if ($a_interface['zone_zone'] == 'Unknown' || $a_interface['zone_zone'] == '') {
        $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_interface['inv_id'] . "#network\" target=\"_blank\">";
        $linkend = "</a>";
      }

      if ($product != $a_interface['prod_name'] || $zone != $a_interface['zone_zone']) {
        $product = $a_interface['prod_name'];
        $zone    = $a_interface['zone_zone'];

        if ($a_interface['zone_zone'] == 'Unknown' || $a_interface['zone_zone'] == '') {
          $class = 'ui-state-highlight';
        }

        print "</td>\n";
        print "</tr>\n";
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">" . $product . " " . $zone . "</td>\n";
        print "  <td class=\"" . $class . "\">" . $linkstart . $a_interface['int_addr'] . $linkend . ", ";
      } else {
        print $linkstart . $a_interface['int_addr'] . $linkend . ", ";
      }

    }
  }

?>
</table>
</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
