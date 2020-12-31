<?php
# Script: support.contract.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath  . '/guest.php');

  $package = "support.contract.php";

  logaccess($db, $formVars['uid'], $package, "Support Listing of Production Systems.");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);
  $formVars['csv']       = clean($_GET['csv'],      10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }

  if ($formVars['inwork'] == '') {
    $formVars['inwork'] = 'false';
  }
  if ($formVars['project'] == '') {
    $formVars['project'] = 0;
  }
  if ($formVars['country'] == '') {
    $formVars['country'] = 0;
  }
  if ($formVars['state'] == '') {
    $formVars['state'] = 0;
  }
  if ($formVars['city'] == '') {
    $formVars['city'] = 0;
  }
  if ($formVars['location'] == '') {
    $formVars['location'] = 0;
  }
  if ($formVars['csv'] == '') {
    $formVars['csv'] = 'false';
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
<title>Support Contract</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#search-tabs" ).tabs( ).addClass( "tab-shadow" );

});

</script>

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

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 and mod_virtual = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 and mod_virtual = 0 ";
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

  $where = $product . $group . $inwork . $location;

?>
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hardware Support Contracts</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<h2>Asset and Support Management</h2>

<p>The <strong>System Name, Model, Model, Location, Asset Tag, and Serial Number</strong> columns are records as managed through the inventory.</p>

<p><strong><u>Supported Hardware</u></strong></p>

<p><strong>Support, Response, Start, and End</strong> are extracted from the support contract spreadsheet Lynda Lilly maintains. Response is the actual contracted support level.</p>

<p><strong><u>Unsupported Hardware</u></strong></p>

<p><strong>Suggested</strong> column is a <strong>recommended</strong> support level. Someone has reviewed and suggested this device <strong>should</strong> be supported at this level.</p>

<p><strong><u>Missing Serial Number</u></strong></p>

<p>This list of devices don't have any identifying information for the Serial Number. This information needs to be located and added in order for the support contract to be verified.</p>

<p><strong><u>Supported But Retired</u></strong></p>

<p>These systems have a <strong>Retired</strong> date (as listed) set but are still showing up as being paid for. This is the same information as what's in the Supported Hardware tab. For highlighted systems, the 'Reused Date' 
needs to be set on the original equipment. If the system is confirmed as retired, report this back to Lynda Lilly to be removed from the contract support.</p>

<p><b>Note:</b> In working with the Asset Manager, the Akibia, Datalink, and Incentra entries are updates from that group and are likely accurate.</p>

<p>An asterisk (*) in a serial, asset, or service tag column indicates the information could not be visually verified and is either missing or was transitioned from a spreadsheet or email message.</p>

</div>

</div>

<p></p>

<div id="tabs">

<ul>
  <li><a href="#supported">Supported Hardware</a></li>
  <li><a href="#unsupported">Unsupported Hardware</a></li>
  <li><a href="#inwork">Unsupported In Work Hardware</a></li>
  <li><a href="#error">Missing Serial Number</a></li>
  <li><a href="#retired">Supported But Retired</a></li>
</ul>




<div id="supported">

<?php

  if ($formVars['csv'] == 'true') {
    print "<p style=\"text-align: left;\"><textarea cols=\"120\" rows=\"40\">\"System Name\",\"Model\",\"Model\",\"Location\",\"Asset Tag\",\"Serial #\",\"Support\",\"Response\",\"Suggested\",\"Start\",\"End\"\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Asset Tag</th>\n";
    print "  <th class=\"ui-state-default\">Serial #</th>\n";
    print "  <th class=\"ui-state-default\">Support</th>\n";
    print "  <th class=\"ui-state-default\">Response</th>\n";
    print "  <th class=\"ui-state-default\">Suggested</th>\n";
    print "  <th class=\"ui-state-default\">Start</th>\n";
    print "  <th class=\"ui-state-default\">End</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_status,mod_vendor,mod_name,ct_city,st_acronym,slv_value,sup_company,inv_response,";
  $q_string .= "hw_serial,hw_asset,hw_built,hw_active,hw_retired,hw_reused,hw_supid_verified,hw_supportstart,hw_supportend ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware     on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models       on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join support      on support.sup_id        = hardware.hw_supportid ";
  $q_string .= "left join supportlevel on supportlevel.slv_id   = hardware.hw_response ";
  $q_string .= "left join locations    on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities       on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join states       on states.st_id          = locations.loc_state ";
  $q_string .= $where . " and inv_status = 0 and hw_supportend > '" . date('Y-m-d') . "' and hw_supportend != '1971-01-01' ";
#and hw_supid_verified = 1 ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {
    $class = "ui-widget-content";
    if ($a_inventory['hw_supid_verified'] == 0) {
      $class = "ui-state-highlight";
    }
    if ($a_inventory['hw_serial'] == '') {
      $class = "ui-state-error";
    }

    if ($a_inventory['hw_supportend'] < date('Y-m-d')) {
      $class = "ui-state-error";
    }

    $q_string  = "select slv_value ";
    $q_string .= "from supportlevel ";
    $q_string .= "where slv_id = " . $a_inventory['inv_response'] . " ";
    $q_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_supportlevel = mysqli_fetch_array($q_supportlevel);

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    if ($formVars['csv'] == 'true') {
      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . $a_inventory['mod_vendor'] . "\",";
      print "\"" . $a_inventory['mod_name'] . "\",";
      print "\"" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . "\",";
      print "\"" . $a_inventory['hw_asset'] . "\",";
      print "\"" . $a_inventory['hw_serial'] . "\",";
      print "\"" . $a_inventory['sup_company'] . "\",";
      print "\"" . $a_inventory['slv_value'] . "\",";
      print "\"" . $a_supportlevel['slv_value'] . "\",";
      print "\"" . $a_inventory['hw_supportstart'] . "\",";
      print "\"" . $a_inventory['hw_supportend'] . "\"\n";
    } else {
      print "<tr>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['inv_name']                                    . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_vendor']                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_name']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym']            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_asset']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_serial']                                              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['sup_company']                                            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['slv_value']                                              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_supportlevel['slv_value']                                           . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportstart']                                        . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportend']                                          . "</td>\n";
      print "</tr>\n";
    }
  }

  if ($formVars['csv'] == 'true') {
    print "</textarea>";
    print "</p>\n";
  } else {
    print "</table>\n";
    print "<p class=\"ui-widget-content\">Total: " . mysqli_num_rows($q_inventory) . "</td>\n";
  }

  print "</div>\n";






  print "<div id=\"unsupported\">\n";

  if ($formVars['csv'] == 'true') {
    print "<p style=\"text-align: left;\"><textarea cols=\"120\" rows=\"40\">\"System Name\",\"Model\",\"Model\",\"Location\",\"Asset Tag\",\"Serial #\",\"Support\",\"Suggested\",\"Start\",\"End\"\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Asset Tag</th>\n";
    print "  <th class=\"ui-state-default\">Serial #</th>\n";
    print "  <th class=\"ui-state-default\">Support</th>\n";
    print "  <th class=\"ui-state-default\">Suggested</th>\n";
    print "  <th class=\"ui-state-default\">Start</th>\n";
    print "  <th class=\"ui-state-default\">End</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_status,mod_vendor,mod_name,ct_city,st_acronym,slv_value,sup_company,";
  $q_string .= "hw_serial,hw_asset,hw_built,hw_active,hw_retired,hw_reused,hw_supid_verified,hw_supportstart,hw_supportend ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware     on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models       on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join support      on support.sup_id        = hardware.hw_supportid ";
  $q_string .= "left join supportlevel on supportlevel.slv_id   = inventory.inv_response ";
  $q_string .= "left join locations    on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities       on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join states       on states.st_id          = locations.loc_state ";
  $q_string .= $where . " and inv_status = 0 and hw_supportend < '" . date('Y-m-d') . "' and hw_active != \"1971-01-01\" ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {
    $class = "ui-widget-content";
    if ($a_inventory['hw_supid_verified'] == 0) {
      $class = "ui-state-highlight";
    }
    if ($a_inventory['hw_serial'] == '') {
      $class = "ui-state-error";
    } else {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
      $linkend   = "</a>";

      if ($formVars['csv'] == 'true') {
        print "\"" . $a_inventory['inv_name'] . "\",";
        print "\"" . $a_inventory['mod_vendor'] . "\",";
        print "\"" . $a_inventory['mod_name'] . "\",";
        print "\"" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . "\",";
        print "\"" . $a_inventory['hw_asset'] . "\",";
        print "\"" . $a_inventory['hw_serial'] . "\",";
        print "\"" . $a_inventory['sup_company'] . "\",";
        print "\"" . $a_inventory['slv_value'] . "\",";
        print "\"" . $a_inventory['hw_supportstart'] . "\",";
        print "\"" . $a_inventory['hw_supportend'] . "\"\n";
      } else {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['inv_name']                                    . $linkend . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_vendor']                                             . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_name']                                               . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym']            . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_asset']                                               . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_serial']                                              . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['sup_company']                                            . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['slv_value']                                              . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportstart']                                        . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportend']                                          . "</td>\n";
        print "</tr>\n";
      }
    }

  }
  if ($formVars['csv'] == 'true') {
    print "</textarea>";
    print "</p>\n";
  } else {
    print "</table>\n";
    print "<p class=\"ui-widget-content\">Total: " . mysqli_num_rows($q_inventory) . "</td>\n";
  }

  print "</div>\n";





  print "<div id=\"inwork\">\n";

  if ($formVars['csv'] == 'true') {
    print "<p style=\"text-align: left;\"><textarea cols=\"120\" rows=\"40\">\"System Name\",\"Model\",\"Model\",\"Location\",\"Asset Tag\",\"Serial #\",\"Support\",\"Suggested\",\"Start\",\"End\"\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Asset Tag</th>\n";
    print "  <th class=\"ui-state-default\">Serial #</th>\n";
    print "  <th class=\"ui-state-default\">Support</th>\n";
    print "  <th class=\"ui-state-default\">Suggested</th>\n";
    print "  <th class=\"ui-state-default\">Start</th>\n";
    print "  <th class=\"ui-state-default\">End</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_status,mod_vendor,mod_name,ct_city,st_acronym,slv_value,sup_company,";
  $q_string .= "hw_serial,hw_asset,hw_built,hw_active,hw_retired,hw_reused,hw_supid_verified,hw_supportstart,hw_supportend ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware     on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models       on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join support      on support.sup_id        = hardware.hw_supportid ";
  $q_string .= "left join supportlevel on supportlevel.slv_id   = inventory.inv_response ";
  $q_string .= "left join locations    on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities       on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join states       on states.st_id          = locations.loc_state ";
  $q_string .= $where . " and inv_status = 0 and hw_supportend < '" . date('Y-m-d') . "' and hw_active = \"1971-01-01\" ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {
    $class = "ui-widget-content";
    if ($a_inventory['hw_supid_verified'] == 0) {
      $class = "ui-state-highlight";
    }
    if ($a_inventory['hw_serial'] == '') {
      $class = "ui-state-error";
    } else {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
      $linkend   = "</a>";

      if ($formVars['csv'] == 'true') {
        print "\"" . $a_inventory['inv_name'] . "\",";
        print "\"" . $a_inventory['mod_vendor'] . "\",";
        print "\"" . $a_inventory['mod_name'] . "\",";
        print "\"" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . "\",";
        print "\"" . $a_inventory['hw_asset'] . "\",";
        print "\"" . $a_inventory['hw_serial'] . "\",";
        print "\"" . $a_inventory['sup_company'] . "\",";
        print "\"" . $a_inventory['slv_value'] . "\",";
        print "\"" . $a_inventory['hw_supportstart'] . "\",";
        print "\"" . $a_inventory['hw_supportend'] . "\"\n";
      } else {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['inv_name']                                    . $linkend . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_vendor']                                             . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_name']                                               . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym']            . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_asset']                                               . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_serial']                                              . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['sup_company']                                            . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['slv_value']                                              . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportstart']                                        . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportend']                                          . "</td>\n";
        print "</tr>\n";
      }
    }

  }
  if ($formVars['csv'] == 'true') {
    print "</textarea>";
    print "</p>\n";
  } else {
    print "</table>\n";
    print "<p class=\"ui-widget-content\">Total: " . mysqli_num_rows($q_inventory) . "</td>\n";
  }

  print "</div>\n";






  print "<div id=\"error\">\n";

  if ($formVars['csv'] == 'true') {
    print "<p style=\"text-align: left;\"><textarea cols=\"120\" rows=\"40\">\"System Name\",\"Model\",\"Model\",\"Location\",\"Asset Tag\",\"Serial #\"\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Asset Tag</th>\n";
    print "  <th class=\"ui-state-default\">Serial #</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_status,mod_vendor,mod_name,ct_city,st_acronym,slv_value,sup_company,";
  $q_string .= "hw_serial,hw_asset,hw_built,hw_active,hw_retired,hw_reused,hw_supid_verified ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware     on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models       on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join support      on support.sup_id        = hardware.hw_supportid ";
  $q_string .= "left join supportlevel on supportlevel.slv_id   = hardware.hw_response ";
  $q_string .= "left join locations    on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities       on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join states       on states.st_id          = locations.loc_state ";
  $q_string .= $where . " and inv_status = 0 and hw_supid_verified = 0 and hw_serial = '' ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {
    $class = "ui-widget-content";
    if ($a_inventory['hw_supid_verified'] == 0) {
      $class = "ui-state-highlight";
    }
    if ($a_inventory['hw_serial'] == '') {
      $class = "ui-state-error";
    }

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    if ($formVars['csv'] == 'true') {
      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . $a_inventory['mod_vendor'] . "\",";
      print "\"" . $a_inventory['mod_name'] . "\",";
      print "\"" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . "\",";
      print "\"" . $a_inventory['hw_asset'] . "\",";
      print "\"" . $a_inventory['hw_serial'] . "\"\n";
    } else {
      print "<tr>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['inv_name']                                    . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_vendor']                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_name']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym']            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_asset']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_serial']                                              . "</td>\n";
      print "</tr>\n";
    }
  }
  if ($formVars['csv'] == 'true') {
    print "</textarea>";
    print "</p>\n";
  } else {
    print "</table>\n";
    print "<p class=\"ui-widget-content\">Total: " . mysqli_num_rows($q_inventory) . "</td>\n";
  }

  print "</div>\n";






  print "<div id=\"retired\">\n";

  if ($formVars['csv'] == 'true') {
    print "<p style=\"text-align: left;\"><textarea cols=\"120\" rows=\"40\">\"System Name\",\"Live Name\",\"Retired\",\"Model\",\"Model\",\"Location\",\"Asset Tag\",\"Serial #\",\"Support\",\"Suggested\",\"Start\",\"End\"\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">Live Name</th>\n";
    print "  <th class=\"ui-state-default\">Retired</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Asset Tag</th>\n";
    print "  <th class=\"ui-state-default\">Serial #</th>\n";
    print "  <th class=\"ui-state-default\">Support</th>\n";
    print "  <th class=\"ui-state-default\">Suggested</th>\n";
    print "  <th class=\"ui-state-default\">Start</th>\n";
    print "  <th class=\"ui-state-default\">End</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_status,mod_vendor,mod_name,ct_city,st_acronym,slv_value,sup_company,hw_retired,";
  $q_string .= "hw_serial,hw_asset,hw_built,hw_active,hw_retired,hw_reused,hw_supid_verified,hw_supportstart,hw_supportend ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware     on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models       on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join support      on support.sup_id        = hardware.hw_supportid ";
  $q_string .= "left join supportlevel on supportlevel.slv_id   = inventory.inv_response ";
  $q_string .= "left join locations    on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities       on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join states       on states.st_id          = locations.loc_state ";
  $q_string .= $where . " and inv_status = 1 and hw_supid_verified = 1 and hw_reused = '1971-01-01' ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ( $a_inventory = mysqli_fetch_array($q_inventory) ) {

    $a_inv['inv_name'] = '';
    $class = "ui-widget-content";
    if ($a_inventory['hw_serial'] == '') {
      $a_inv['inv_name'] = '';
      $class = "ui-state-error";
    } else {
      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
      $q_string .= "where ";
      $or = '(';
      $tail = '';
      $and = '';
      if ($a_inventory['hw_serial'] != '') {
        $q_string .= "(hw_serial = '" . $a_inventory['hw_serial'] . "'";
        $or = ' or ';
        $tail = ') ';
        $and = 'and ';
      }
      $q_string .= $tail . $and . "inv_status = 0 ";
      $q_inv = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv) > 0) {
        $a_inv = mysqli_fetch_array($q_inv);
        $class = "ui-state-highlight";
      }
    }

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">";
    $linkend   = "</a>";

    if ($formVars['csv'] == 'true') {
      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . $a_inv['inv_name'] . "\",";
      print "\"" . $a_inventory['hw_retired'] . "\",";
      print "\"" . $a_inventory['mod_vendor'] . "\",";
      print "\"" . $a_inventory['mod_name'] . "\",";
      print "\"" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . "\",";
      print "\"" . $a_inventory['hw_asset'] . "\",";
      print "\"" . $a_inventory['hw_serial'] . "\",";
      print "\"" . $a_inventory['sup_company'] . "\",";
      print "\"" . $a_inventory['slv_value'] . "\",";
      print "\"" . $a_inventory['hw_supportstart'] . "\",";
      print "\"" . $a_inventory['hw_supportend'] . "\"\n";
    } else {
      print "<tr>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_inventory['inv_name']                                    . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inv['inv_name']                                                     . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_retired']                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_vendor']                                             . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['mod_name']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym']            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_asset']                                               . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_serial']                                              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['sup_company']                                            . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['slv_value']                                              . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportstart']                                        . "</td>\n";
      print "  <td class=\"" . $class . "\">"              . $a_inventory['hw_supportend']                                          . "</td>\n";
      print "</tr>\n";
    }
  }
  if ($formVars['csv'] == 'true') {
    print "</textarea>";
    print "</p>\n";
  } else {
    print "</table>\n";
    print "<p class=\"ui-widget-content\">Total: " . mysqli_num_rows($q_inventory) . "</td>\n";
  }

?>

</div>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
