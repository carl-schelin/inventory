<?php
# Script: aseifert.webapps.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "aseifert.webapps.php";

  logaccess($formVars['uid'], $package, "Getting a listing of Web Applications with Apache.");

  $formVars['csv'] = 0;
  if (isset($_GET['csv'])) {
    $formVars['csv'] = 1;
  }

# if help has not been seen yet,
  if (show_Help('aseifertapache')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Ashley Seifert: Web Applications With Apache</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . '/mobile.php'); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<?php

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">Server Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page presents a list of all servers where Web Applications is identified as the Application Admin or where Web Applications has identified Apache as software they manage..</p>\n";

  print "<p>To be identified as the Application Admin, you'll need to contact the Platform Owner (either Unix, Windows, or Lab Admins).</p>\n";

  print "<p>To claim ownership of software on a server, search for the server, click the pencil to edit it, click the Software tab, add the appropriate software as being owned by Web Applications.</p>\n";

  print "</div>\n";

  print "</div>\n";

  if ($formVars['csv'] == 0) {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Product</th>\n";
    print "  <th class=\"ui-state-default\">Apache Version</th>\n";
    print "</tr>\n";
  } else {
    print "\"System Name\",\"Product\",\"Apache Version\"</br>\n";
  }

  $q_string  = "select inv_id,inv_name,prod_name,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "inner join products on inventory.inv_product = products.prod_id ";
  $q_string .= "inner join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where (inv_appadmin = " . $GRP_WebApps . " or sw_group = " . $GRP_WebApps . ") and inv_status = 0 and sw_software like '%apache%' ";
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    if ($formVars['csv'] == 0) {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']  . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['prod_name']            . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['sw_software']          . "</td>\n";
      print "</tr>\n";
    } else {
      print "\"" . $a_inventory['inv_name'] . "\",\"" . $a_inventory['prod_name'] . "\",\"" . $a_inventory['sw_software'] . "\"</br>\n";
    }

  }

  if ($formVars['csv'] == 0) {
    print "</table>\n";
  }

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
