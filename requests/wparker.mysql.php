<?php
# Script: wparker.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "wparker.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

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

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>MySQL Software Listing</title>

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
  print "  <th class=\"ui-state-default\">MySQL Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"display:none\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page presents a list of MySQL installations.</p>\n";

  print "<p>A checkmark next to the date indicates the software was automatically gathered from the system. The date indicates when the last update was made to the software.</p>\n";

  print "<p>Click <a href=\"wparker.mysql.csv.php\">here</a> for a page formatted as a csv, suitable for import into a spreadsheet.</p>\n";

  print "</div>\n";

  print "</div>\n";

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"     . "\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=prod_name"    . "\">Product</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_vendor"    . "\">Vendor</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_software"  . "\">Software</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_type"      . "\">Type</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=grp_name"     . "\">Group</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_update"    . "\">Updated</a></th>\n";
  print "</tr>";

  $q_string  = "select sw_id,sw_software,sw_vendor,sw_product,sw_type,sw_verified,sw_update,inv_name,grp_name,prod_name ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join groups    on groups.grp_id         = software.sw_group ";
  $q_string .= "left join products  on products.prod_id      = software.sw_product ";
  $q_string .= "where inv_status = 0 and sw_software like '%mysql%' ";
  $q_string .= $orderby;
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_software = mysqli_fetch_array($q_software)) {

    $checkmark = '';
    if ($a_software['sw_verified']) {
      $checkmark = "&#x2713;&nbsp;";
    }

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['inv_name']                  . "</a></td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['prod_name']                 . "</a></td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_vendor']                 . "</a></td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_software']               . "</a></td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_type']                   . "</a></td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['grp_name']                  . "</a></td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_update']    . $checkmark . "</a></td>\n";
    print "</tr>";

  }

  mysqli_free_result($q_software);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
