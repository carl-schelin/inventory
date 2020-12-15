<?php
# Script: kzupan.interfaces.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "kzupan.interfaces.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

  if (isset($_GET["csv"])) {
    $formVars['csv'] = clean($_GET["csv"], 10);
  }

  if ($formVars['csv'] == 'true') {
    $formVars['csv'] = 1;
  } else {
    $formVars['csv'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Server Listing</title>

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
  print "  <th class=\"ui-state-default\">Server Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"display:none\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page presents a list of all server.</p>\n";

  print "</div>\n";

  print "</div>\n";

  if ($formVars['csv']) {
    print "<p style=\"text; left\">\n";
    print "\"Server\",";
    print "\"Function\",";
    print "\"Product\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">";
    print "<tr>";
    print "  <th class=\"ui-state-default\">Server</th>\n";
    print "  <th class=\"ui-state-default\">Function</th>\n";
    print "  <th class=\"ui-state-default\">Product</th>\n";
    print "</tr>";
  }

  $q_string  = "select inv_id,inv_name,inv_function,prod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join products  on products.prod_id      = inventory.inv_product ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $GRP_Unix . " ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $q_string  = "select int_id ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_query($db, ));
    if (mysqli_num_rows($q_interface) == 0) {

      if ($formVars['csv']) {
        print "\"" . $a_inventory['inv_name'] . "\",";
        print "\"" . $a_inventory['inv_function'] . "\",";
        print "\"" . $a_inventory['prod_name'] . "\"</br>\n";
      } else {
        print "<tr>\n";
        print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_name']     . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_function'] . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_inventory['prod_name']    . "</td>\n";
        print "</tr>";
      }
    }
  }

  mysqli_free_result($q_inventory);

  if ($formVars['csv']) {
    print "</p>\n";
  } else {
    print "</table>\n";
  }
?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
