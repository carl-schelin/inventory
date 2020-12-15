<?php
# Script: jarmstrong.listing.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "jarmstrong.listing.php";

  logaccess($db, $formVars['uid'], $package, "Viewing Jeff's server listing.");

  if (isset($_GET['clean'])) {
    $formVars['csv'] = clean($_GET['clean'],10);
  } else {
    $formVars['csv'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Jeff Armstrong: Server Listing</title>

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

<?php
  if ($formVars['csv']) {
    print "<p>Class,";
    print "Name,";
    print "IP Address,";
    print "Manufacturer,";
    print "Model,";
    print "Serial Number,";
    print "Cost Center,";
    print "Location,";
    print "Status,";
    print "Install Date,";
    print "Acquired Date,";
    print "Warranty End Date,";
    print "Environment\n";
  } else {
    print "<div class=\"main\">\n";
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Class</th>\n";
    print "  <th class=\"ui-state-default\">Name</th>\n";
    print "  <th class=\"ui-state-default\">IP Address</th>\n";
    print "  <th class=\"ui-state-default\">Manufacturer</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Serial Number</th>\n";
    print "  <th class=\"ui-state-default\">Cost Center</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Status</th>\n";
    print "  <th class=\"ui-state-default\">Install Date</th>\n";
    print "  <th class=\"ui-state-default\">Acquired Date</th>\n";
    print "  <th class=\"ui-state-default\">Warranty End Date</th>\n";
    print "  <th class=\"ui-state-default\">Environment</th>\n";
    print "  <th class=\"ui-state-default\">Primary Assigned Application</th>\n";
    print "</tr>\n";
  }

  $count = 0;
  $q_string  = "select inv_id,inv_name,inv_manager,inv_location,loc_name,inv_status,inv_product,";
  $q_string .= "sw_software,hw_serial,hw_built,hw_active,mod_vendor,mod_name,prod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "where inv_status = 0 and sw_type = 'OS' and hw_primary = 1 and hw_deleted = 0 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory) ) {

    if ($a_inventory['hw_active'] == '0000-00-00') {
      $active = "In Progress";
    } else {
      $active = "Live";
    }

    $location = '';
    if ($a_inventory['inv_manager'] == 26) {
      $location = "Dev/SQA Lab";
    } else { 
      if ($a_inventory['inv_location'] == 31) {
        $location = "Production Lab";
      } else {
        if ($a_inventory['inv_location'] != 0) {
          $location = "Production";
        }
      }
    }

    $q_string  = "select int_addr ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_primary = 1";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_interface = mysqli_fetch_array($q_interface);

    if ($a_interface['int_addr'] == '') {
      $q_string  = "select int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_type = 2";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_interface = mysqli_fetch_array($q_interface);
    }

    if ($a_interface['int_addr'] == '') {
      $q_string  = "select int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'];
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_interface = mysqli_fetch_array($q_interface);
    }

    if ($formVars['csv']) {
      print "<br>\"" . $a_inventory['sw_software'] . "\",";
      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . $a_interface['int_addr'] . "\",";
      print "\"" . $a_inventory['mod_vendor'] . "\",";
      print "\"" . $a_inventory['mod_name'] . "\",";
      print "\"" . $a_inventory['hw_serial'] . "\",";
      print ",,";
      print "\"" . $a_inventory['loc_name'] . "\",";
      print "\"" . $active . "</td>\n";
      print "\"" . $a_inventory['hw_built'] . "\",";
      print ",,";
      print ",,";
      print "\"" . $location . "\",";
      print "\"" . $a_inventory['prod_name'] . "\"";
    } else {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['sw_software'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_name'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_addr'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['mod_vendor'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['mod_name'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['hw_serial'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">&nbsp;</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['loc_name'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $active . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['hw_built'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">&nbsp;</td>\n";
      print "  <td class=\"ui-widget-content\">&nbsp;</td>\n";
      print "  <td class=\"ui-widget-content\">" . $location . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_inventory['prod_name'] . "</td>\n";
      print "</tr>\n";
    }

    $count++;
  }

  if ($formVars['csv']) {
  } else {
    print "</table>\n";
    print "<p>There are " . $count . " servers listed.<p>\n";
  }

?>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
