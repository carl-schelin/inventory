<?php
# Script: monitorvers.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "monitorvers.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the interfaces.");

  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  } else {
    $formVars['product']   = 0;
  }
  if (isset($_GET['project'])) {
    $formVars['project']   = clean($_GET['project'],  10);
  } else {
    $formVars['project']   = 0;
  }
  if (isset($_GET['group'])) {
    $formVars['group']    = clean($_GET['group'],   10);
  } else {
    $formVars['group']    = 1;
  }
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }
  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
  } else {
    $formVars['csv'] = '';
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
    $orderby = "order by inv_name,int_addr ";
    $_SESSION['sort'] = '';
  }

  $and = " where";
  $ampersand = "?";
  $sortby = "";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
      }
      $and = " and";
    }
    $sortby .= $ampersand . "product=" . $formVars['product'];
    $ampersand = "&";
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
    $and = " and";
    $sortby .= $ampersand . "group=" . $formVars['group'];
    $ampersand = "&";
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $type;

  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from ip_zones";
  $q_ip_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
    $zoneval[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
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
<title>Current Openview Configuration</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript" language="javascript">

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  $q_string  = "select itp_id,itp_acronym ";
  $q_string .= "from inttype ";
  $q_inttype = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inttype = mysqli_fetch_array($q_inttype)) {
    $inttype[$a_inttype['itp_id']] = $a_inttype['itp_acronym'];
  }

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Current Monitoring Status</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all servers for the selected group and includes several column details. Only the OS column is not sortable. Sorts are reversable; clicking a second time on any ";
  print "column will sort it in reverse order.</p>";

  print "<p>The report shows all interfaces for a server that are type LOM, Application, or Management and associates the HP software with the entire server and not with a specific interface. ";
  print "Any IP/Interface that is marked in the Inventory as being monitored by Openview is <span class=\"ui-state-highlight\">highlighted</span></p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  if ($formVars['csv'] == 'true') {
    print "<p style=\"text-align: left;\">\"IP Address\",\"Hostname\",\"OS\",\"Function\",\"Location\",\"System Owner\",\"Application Owner\",\"Product\",\"Agent Version\"</br>";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=int_addr\">IP Address</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=int_type\"Type</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=int_server\">Hostname</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=int_server\">OS</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=int_function\">Function</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=loc_name\">Location</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=grp_name\">System Owner</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=grp_name\">Application Owner</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=prod_name\">Product</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"monitorvers.php" . $sortby . "&sort=sw_software\">Agent Version</a></th>\n";
    print "</tr>\n";
  }

  $q_string  = "select int_id,inv_name,inv_function,int_companyid,int_server,int_addr,inv_appadmin,grp_name,int_type,int_openview,loc_name,prod_name,sw_software ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id   = interface.int_companyid ";
  $q_string .= "left join a_groups    on a_groups.grp_id      = inventory.inv_manager ";
  $q_string .= "left join locations on locations.loc_id   = inventory.inv_location ";
  $q_string .= "left join products  on products.prod_id   = inventory.inv_product ";
  $q_string .= "right join software  on software.sw_companyid   = inventory.inv_id ";
# don't want to see signaling, serial, loopback, interconnect, or backup interfaces as they won't be monitored regardless.
  $q_string .= $where . " and int_ip6 = 0 and int_addr != '' and int_type != 3 and int_type != 5 and int_type != 7 and int_type != 8 and int_type != 16 and sw_group = " . $GRP_Monitoring . " and sw_vendor = \"HP\" ";
  $q_string .= $orderby;
  $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_interface['int_companyid'] . "#network\" target=\"blank\">";
      $linkend   = "</a>";

      $q_string  = "select grp_name ";
      $q_string .= "from a_groups ";
      $q_string .= "where grp_id = " . $a_interface['inv_appadmin'] . " ";
      $q_appadmin = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_appadmin = mysqli_fetch_array($q_appadmin);

      $q_string  = "select sw_software ";
      $q_string .= "from software ";
      $q_string .= "where sw_companyid = " . $a_interface['int_companyid'] . " and sw_type = \"OS\" ";
      $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_software = mysqli_fetch_array($q_software);

      if ($a_interface['int_openview'] == 1) {
        $class = "ui-state-highlight";
      } else {
        $class = "ui-widget-content";
        $a_interface['sw_software'] == "--";
      }

      if ($formVars['csv'] == 'true') {
        if ($a_interface['int_openview']) {
          print "\"" . $a_interface['int_addr'] . "\",";
          print "\"" . $a_interface['inv_name'] . "\",";
          print "\"" . $a_interface['inv_function'] . "\",";
          print "\"" . $a_interface['loc_name'] . "\",";
          print "\"" . $a_interface['grp_name'] . "\",";
          print "\"" . $a_appadmin['grp_name'] . "\",";
          print "\"" . $a_interface['prod_name'] . "\",";
          print "\"" . $a_interface['sw_software'] . "\"";
          print "</br>\n";
        }
      } else {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['int_addr']                      . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $inttype[$a_interface['int_type']]            . "</td>\n";
        print "  <td class=\"" . $class . "\">"        . $linkstart . $a_interface['inv_name']           . $linkend . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_software['sw_software']                    . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['inv_function']                  . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['loc_name']                      . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['grp_name']                      . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_appadmin['grp_name']                       . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['prod_name']                     . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['sw_software']                   . "</td>\n";
        print "</tr>\n";
      }
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"6\">No records found</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
