<?php
# Script: esxlisting.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "esxlisting.php";

  logaccess($formVars['uid'], $package, "Accessing script");

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
<title>ESX Host Listing</title>

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

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">ESX Host Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the hosts owned by the Virtualization Team where there are more than 0 hosts on it. A report is sent by the ";
  print "Virtualization team and is incorporated into the Inventory. Hosts are identified by uuid which is also extracted nightly for an easy ";
  print "match. Other matching is by the name as presented by the listing in the inventory and then the interface server name. The uuid is more ";
  print "accurate in part because Virtualization doesn't always name systems per the actual system name.</p>\n";

  print "<p>Clicking on an ESX host will bring you to that server's detail page. The page will list all the servers with links to that server's detail page. ";
  print "In reverse, if you're looking at a server's detail page, clicking on the 'Guest of' link will take you to the ESX server.</p>\n";

  print "<p>Note that the data is gathered once a month but is reloaded each night. So if a server is added and the uuid or name matches, it's ";
  print "added automatically. And new data can be added when a new data file is generated.</p>\n";

  print "</div>\n";

  print "</div>\n";

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">ESX Host</th>\n";
  print "  <th class=\"ui-state-default\">Number of Guests</th>\n";
  print "</tr>";

  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_manager = 4 ";
  $q_string .= "group by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $q_string  = "select count(inv_id) ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_companyid = " . $a_inventory['inv_id'] . " ";
    $q_count = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_count = mysqli_fetch_array($q_count);

    $linkstart = "<a href=\"" . $Siteroot . "/show/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"blank_\">";
    $linkend = "</a>";

    if ($a_count['count(inv_id)'] > 0) {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name'] .$linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_count['count(inv_id)']                       . "</td>\n";
      print "</tr>";
    }

  }

  mysqli_free_result($q_inventory);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
