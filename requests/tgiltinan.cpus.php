<?php
# Script: tgiltinan.cpus.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "tgilginan.cpus.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

  if (isset($_GET["csv"])) {
    $formVars['csv'] = clean($_GET["csv"], 10);
  } else {
    $formVars['csv'] = 'false';
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
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/FormTables/formTables.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/FormTables/formTables.css">

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

  print "<p>This page presents a list of all servers where Oracle is installed.</p>\n";

  print "</div>\n";

  print "</div>\n";

# per Tim, need:
# Physical Machine Name
# Server Make
# Server Model
# Processor Model
# Processor Speed
# Physical CPU count
# cores per proc

  if ($formVars['csv'] == 'true') {
    print "\"Server\",";
    print "\"Vendor\",";
    print "\"Software\",";
    print "\"Vendor\",";
    print "\"Model\",";
    print "\"Vendor\",";
    print "\"Model\",";
    print "\"# CPUs\",";
    print "\"# Cores\"</br>";
  } else {
    print "<table class=\"ui-styled-table\">";
    print "<tr>";
    print "  <th class=\"ui-state-default\">Server</th>\n";
    print "  <th class=\"ui-state-default\">Vendor</th>\n";
    print "  <th class=\"ui-state-default\">Software</th>\n";
    print "  <th class=\"ui-state-default\">Vendor</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\">Vendor</th>\n";
    print "  <th class=\"ui-state-default\">Model</th>\n";
    print "  <th class=\"ui-state-default\"># CPUs</th>\n";
    print "  <th class=\"ui-state-default\"># Cores</th>\n";
    print "</tr>";
  }

  $q_string  = "select sw_id,sw_software,sw_vendor,sw_product,sw_type,sw_verified,sw_update,inv_id,inv_name,grp_name,prod_name ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join a_groups    on a_groups.grp_id         = software.sw_group ";
  $q_string .= "left join products  on products.prod_id      = software.sw_product ";
  $q_string .= "where inv_status = 0 and inv_manager = 1 and grp_name = 'DBA Admin' and sw_software like '%oracle%' ";
  $q_string .= $orderby;
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_software = mysqli_fetch_array($q_software)) {

    $q_string  = "select mod_name,mod_vendor ";
    $q_string .= "from hardware "; 
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= "where hw_companyid = " . $a_software['inv_id'] . " and hw_primary = 1 and hw_deleted = 0 ";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_hardware = mysqli_fetch_array($q_hardware);

    $q_string  = "select mod_name,mod_vendor,mod_size ";
    $q_string .= "from hardware "; 
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= "where hw_companyid = " . $a_software['inv_id'] . " and hw_type = 8 and hw_deleted = 0 ";
    $q_cpu = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_cpu = mysqli_fetch_array($q_cpu);
    $count = mysqli_num_rows($q_cpu);

    $count_name = " CPU";
    if ($count > 1) {
      $count_name = " CPUs";
    }

    if ($formVars['csv'] == 'true') {
      print "\"" . $a_software['inv_name'] . "\",";
      print "\"" . $a_software['sw_vendor'] . "\",";
      print "\"" . $a_software['sw_software'] . "\",";
      print "\"" . $a_hardware['mod_vendor'] . "\",";
      print "\"" . $a_hardware['mod_name'] . "\",";
      print "\"" . $a_cpu['mod_vendor'] . "\",";
      print "\"" . $a_cpu['mod_name'] . "\",";
      print "\"" . $count . $count_name . "\",";
      print "\"" . $a_cpu['mod_size'] . "\"</br>";
    } else {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $a_software['inv_name']                  . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_software['sw_vendor']                 . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_software['sw_software']               . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_vendor']                 . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_name']                 . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_cpu['mod_vendor']                  . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_cpu['mod_name']                   . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $count . $count_name   . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_cpu['mod_size']    . "</a></td>\n";
      print "</tr>";
    }
  }

  mysqli_free_result($q_software);

  if ($formVars['csv'] == 'true') {
    print "</div>\n";
  } else {
    print "</table>\n";
    print "</div>\n";
  }
?>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
