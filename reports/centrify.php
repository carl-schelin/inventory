<?php
# Script: centrify.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "centrify.php";

  logaccess($db, $formVars['uid'], $package, "Getting a listing of Centrify Servers.");

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = 0;
  }

  $formVars['group']   = clean($_GET['group'],   10);

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by inv_adzone,inv_name";
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

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Centrify Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" .  $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>The purpose of this report is to list all the servers that are currently under Centrify control. Centrify is a connection 
to the Windows Active Directory services which changes the user management from the individual server to a central controller. There are 
several domains which are isolated from the other domains. The 'unix.intrado.pri' domain is connected to the central corp domain which 
is managed by the Windows team. The other domains are managed by the Unix team.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  $linkstart = "<a href=\"" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=";
  $linkend   = "</a>";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_name\">"     . "System Name" . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_function\">" . "Function"    . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_adzone\">"   . "Zone"        . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_centrify\">" . "Added"       . $linkend . "</th>\n";
  print "  <th class=\"ui-state-default\">" . $linkstart . "inv_domain\">"   . "Domain"      . $linkend . "</th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,inv_function,inv_centrify,inv_adzone,inv_domain ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_adzone != '' ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']     . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_function'] . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_adzone']   . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_centrify'] . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_domain']   . $linkend . "</td>\n";
    print "</tr>\n";

  }
  print "</table>\n";

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
