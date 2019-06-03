<?php
# Script: endoflife.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "endoflife.php";

  logaccess($formVars['uid'], $package, "Listing of End of Life Software.");

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
<title>License Report</title>

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
  print "  <th class=\"ui-state-default\">Operating System Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the operating systems and the end of life dates.</p>\n";

  print "<ul>\n";
  print "  <li><span class=\"ui-state-highlight\">Highlight</span> - Indicates a system without a set date (set to default of 0000-00-00).</li>\n";
  print "  <li><span class=\"ui-state-error\">Highlight</span> - Indicates a system with a expiration date older than today's date.</li>\n";
  print "</ul>\n";

  print "</div>\n\n";

  print "</div>\n\n";


  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"    . $passthrough . "\">System Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=prod_name"   . $passthrough . "\">Product Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_software" . $passthrough . "\">Software</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_eol"      . $passthrough . "\">End of Life Date</a></th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,sw_software,sw_eol,prod_name ";
  $q_string .= "from software ";
  $q_string .= "left join products  on products.prod_id      = software.sw_product ";
  $q_string .= "left join inventory on inventory.inv_id      = software.sw_companyid ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= $where . " and sw_type = 'OS' ";
  $q_string .= $orderby;
  $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_software) > 0) {
    while ($a_software = mysql_fetch_array($q_software)) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_software['inv_id'] . "\">";
      $linkend   = "</a>";

      $class = "ui-widget-content";
      if ($a_software['sw_eol'] < date('Y-m-d')) {
        $class = "ui-state-error";
      }
      if ($a_software['sw_eol'] == '0000-00-00') {
        $class = "ui-state-highlight";
      }

      print "<tr>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_software['inv_name']    . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_software['prod_name']   . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_software['sw_software'] . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_software['sw_eol']      . $linkend . "</td>\n";
      print "</tr>\n";

    }
  }

?>
</table>
</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
