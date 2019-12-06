<?php
# Script: routing.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "routing.php";

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
    $orderby = " order by route_address,inv_name";
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
<title>Routing Report</title>

<style>
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

  print "<table class=\"ui-styled-table\">\n";
  print "  <th class=\"ui-state-default\">Routing Report</a></th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This lists the routing tables for all selected systems. Routes that are <span class=\"ui-state-highlight\">highlighted</span> are identified as default routes (0.0.0.0 or default).</p>\n";

  print "<p>The table is sortable and clicking on a server will take you to the Routing link on the server detail page.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  $passthrough = 
    "&group="    . $formVars['group']    . 
    "&product="  . $formVars['product']  . 
    "&inwork="   . $formVars['inwork']   . 
    "&type="     . $formVars['type']     . 
    "&country="  . $formVars['country']  . 
    "&state="    . $formVars['state']    . 
    "&city="     . $formVars['city']     . 
    "&location=" . $formVars['location'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"      . $passthrough . "\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=route_address" . $passthrough . "\">Destination</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=route_address" . $passthrough . "\">DNS</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=route_mask"    . $passthrough . "\">Netmask</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=route_gateway" . $passthrough . "\">Gateway</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=int_face"      . $passthrough . "\">Interface</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=itp_acronym"   . $passthrough . "\">Type</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=route_desc"    . $passthrough . "\">Description</a></th>\n";
  print "</tr>\n";

  $q_string = "select inv_id,inv_name,route_id,route_address,route_gateway,route_mask,route_desc,int_face,itp_acronym "
            . "from inventory "
            . "left join routing   on routing.route_companyid = inventory.inv_id "
            . "left join interface on interface.int_id        = routing.route_interface "
            . "left join hardware  on hardware.hw_companyid   = inventory.inv_id "
            . "left join locations on locations.loc_id        = inventory.inv_location "
            . "left join models    on models.mod_id           = hardware.hw_vendorid "
            . "left join groups    on groups.grp_id           = inventory.inv_manager "
            . "left join inttype   on inttype.itp_id          = interface.int_type "
            . $where
            . $orderby;
  $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#routing\" target=\"_blank\">";
    $linkend = "</a>";

    $dns = $a_inventory['route_address'];
## validate the IP before trying to ping or look it up (unnecessary delays)
#    if (filter_var($a_routing['route_address'], FILTER_VALIDATE_IP) && ($a_interface['int_face'] != 'lo' || $a_interface['int_face'] != 'lo0')) {
## ensure it's a -host based ip, no need to ping or look up -net ranges.
#      if ($a_routing['route_mask'] == 32) {
#        $ping = ' class="ui-state-error" ';
#        if (ping($a_routing['route_address'])) {
#          $ping = ' class="ui-state-highlight" ';
#        }
#        $dns = gethostbyaddr($a_routing['route_address']);
#      }
#    }

    if ($a_inventory['route_address'] == '0.0.0.0' || $a_inventory['route_address'] == 'default') {
      $class = "ui-state-highlight";
    } else {
      $class = "ui-widget-content";
    }

    print "<tr>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['inv_name']                      . $linkend . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['route_address']                 . $linkend . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['route_address']                 . $linkend . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . createNetmaskAddr($a_inventory['route_mask']) . $linkend . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['route_gateway']                 . $linkend . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['int_face']                      . $linkend . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['itp_acronym']                   . $linkend . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['route_desc']                    . $linkend . "</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php

  print "<div id=\"main\">\n";
  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Systems Without Routes</th>\n";
  print "</tr>\n";

#            . "where inv_status = 0 and inv_ssh = 1 and inv_manager = 1 ";

  $q_string = "select inv_id,inv_name "
            . "from inventory "
            . "left join hardware  on hardware.hw_companyid   = inventory.inv_id "
            . "left join locations on locations.loc_id        = inventory.inv_location "
            . "left join groups    on groups.grp_id           = inventory.inv_manager "
            . $where
            . "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string  = "select route_id from routing ";
    $q_string .= "where route_companyid = " . $a_inventory['inv_id'];
    $q_routing = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
    $a_routing = mysql_fetch_array($q_routing);

    $linkstart = "<a href=\"" . $Editroot . "/routing.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
    $linkend   = "</a>";

    if ($a_routing['route_id'] == '') {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name'] . $linkend . "</td>\n";
      print "</tr>\n";
    }
  }
  print "</table>\n";
  print "</div>\n";
?>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
