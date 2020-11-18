<?php
# Script: walkthrough.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "walkthrough.php";

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
    if ($formVars['location'] != 0) {
      $orderby = "order by inv_row,inv_rack,inv_unit,inv_name ";
    } else {
      $orderby = 'order by st_acronym,ct_city,loc_name,inv_row,inv_rack,inv_unit,inv_name ';
    }
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
<title>Datacenter Walkthrough</title>

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
  $and = " where mod_virtual = 0 and";
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

  if ($formVars['inwork'] == 'true') {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
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

  $parent = $and . " inv_companyid = 0 ";
  $and = " and";

  $where = $product . $group . $inwork . $location . $type;

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
  print "  <th class=\"ui-state-default\">Data Center Walkthrough</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page displays all physical hardware located in the selected data center. This is put in order of the row and then rack ";
  print "to make it easier for the admin to reference the document when performing the walk through. Walk throughs should be done regularly ";
  print "to identify hardware issues that may have been missed or an alert not generated.</p>";

  print "<p>If All Data Centers is selected, then a Location column is added with the physical location of the Data Center.<p>";

  print "<p><strong>Note:</strong> Column headers are sort/reverse sort able.</p>";

  print "<p><strong>Note:</strong> Click on the 'Pencil' to edit the server.</p>";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"         . $passthrough . "\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_rack,inv_row" . $passthrough . "\">Rack Location</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_unit"         . $passthrough . "\">Unit Location</a></th>\n";
  if ($formVars['location'] == 0) {
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=loc_name"       . $passthrough . "\">Location</a></th>\n";
  }
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_asset"         . $passthrough . "\">Asset Tag</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_serial"        . $passthrough . "\">Serial Number</a></th>\n";
  print "</tr>\n";

  $locheader = '';
  $q_string  = "select inv_id,inv_name,inv_rack,inv_row,inv_unit,inv_manager,loc_name,";
  $q_string .= "ct_city,st_acronym,hw_asset,hw_serial ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on inventory.inv_location = locations.loc_id ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = locations.loc_state ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= $where . $parent;
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if ($a_inventory['inv_unit'] == 0) {
      $unit = '';
    } else {
      $unit = "U" . $a_inventory['inv_unit'];
    }

    $linkedit = '';
    if (check_userlevel($AL_Edit)) {
      if ($a_inventory['inv_manager'] == $_SESSION['group']) {
        $linkedit = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\" height=10></a>";
      }
    }

    if ($location == '' && $locheader != $a_inventory['loc_name']) {
      print "<tr>\n";
      print "  <th class=\"ui-state-default\" colspan=\"7\">" . $a_inventory['loc_name'] . " (" . $a_inventory['ct_city'] . " " . $a_inventory['st_acronym'] . ")</th>\n";
      print "</tr>\n";
      $locheader = $a_inventory['loc_name'];
    }

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkedit . $a_inventory['inv_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_row'] . " - " . $a_inventory['inv_rack'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $unit . "</td>\n";
    if ($formVars['location'] == 0) {
      print "  <td class=\"ui-widget-content\">" . $a_inventory['loc_name'] . "</td>\n";
    }
    print "  <td class=\"ui-widget-content\">" . $a_inventory['hw_asset'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inventory['hw_serial'] . "</td>\n";
    print "</tr>\n";


    $q_string  = "select inv_id,inv_name,inv_rack,inv_row,inv_unit,inv_manager,loc_name,";
    $q_string .= "ct_city,st_acronym,hw_asset,hw_serial ";
    $q_string .= "from inventory ";
    $q_string .= "left join locations on inventory.inv_location = locations.loc_id ";
    $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
    $q_string .= "left join states on states.st_id = locations.loc_state ";
    $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= $where . $and . " inv_companyid = " . $a_inventory['inv_id'] . " ";
    $q_string .= "order by inv_unit ";
    $q_child = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    while ($a_child = mysqli_fetch_array($q_child)) {

      $linkedit = '';
      if (check_userlevel($AL_Edit)) {
        if ($a_child['inv_manager'] == $_SESSION['group']) {
          $linkedit = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_child['inv_id'] . "\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\" height=10></a>";
        }
      }

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">&gt; " . $linkedit . $a_child['inv_name'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">Blade</td>\n";
      print "  <td class=\"ui-widget-content\">Slot " . $a_child['inv_unit'] . "</td>\n";
      if ($formVars['location'] == 0) {
        print "  <td class=\"ui-widget-content\">" . $a_child['loc_name'] . "</td>\n";
      }
      print "  <td class=\"ui-widget-content\">" . $a_child['hw_asset'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_child['hw_serial'] . "</td>\n";
      print "</tr>\n";

    }
  }

?>

</table>

<p>* Indicates that the Asset tag was previously gathered but is not able to be confirmed visually.</p>

<p><a href='walkthrough.pdf.php?group=<?php print $formVars['group']; ?>&location=<?php print $formVars['location']; ?>'>PDF Version</a></p>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
