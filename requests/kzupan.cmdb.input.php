<?php
# Script: kzupan.cmdb.input.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "kzupan.cmdb.input.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

  if (isset($_GET["group"])) {
    $formVars['group'] = clean($_GET["group"], 10);
  } else {
    $formVars['group'] = 1;
  }
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
<title>CMDB Input Listing</title>

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
  print "  <th class=\"ui-state-default\">CMDB Input Listing</th>";
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
    print "\"System Name\",";
    print "\"IP Address\",";
    print "\"Class\",";
    print "\"Location\",";
    print "\"Owning Group\",";
    print "\"Assignment Group\",";
    print "\"Support Group\",";
    print "\"Environment\",";
    print "\"Operational Status\",";
    print "\"sys_updated_on\",";
    print "\"sys_updated_by\",";
    print "\"Primary Assigned App (field on servers only)\",";
    print "\"Cost Center\",";
    print "\"Serial\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">";
    print "<tr>";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">IP Address</th>\n";
    print "  <th class=\"ui-state-default\">Class</th>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Owning Group</th>\n";
    print "  <th class=\"ui-state-default\">Assignment Group</th>\n";
    print "  <th class=\"ui-state-default\">Support Group</th>\n";
    print "  <th class=\"ui-state-default\">Environment</th>\n";
    print "  <th class=\"ui-state-default\">Operational Status</th>\n";
    print "  <th class=\"ui-state-default\">sys_updated_on</th>\n";
    print "  <th class=\"ui-state-default\">sys_updated_by</th>\n";
    print "  <th class=\"ui-state-default\">Primary Assigned App (field on servers only)</th>\n";
    print "  <th class=\"ui-state-default\">Cost Center</th>\n";
    print "  <th class=\"ui-state-default\">Serial</th>\n";
    print "</tr>";
  }

  $q_string  = "select inv_id,inv_name,inv_function,inv_manager,inv_appadmin,loc_west,mod_name,part_name,env_name,hw_serial,inv_uuid,sw_software,grp_name,grp_snow,hw_active ";
  $q_string .= "from inventory ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= "left join locations  on locations.loc_id        = inventory.inv_location ";
  $q_string .= "left join environment  on environment.env_id        = inventory.inv_env ";
  $q_string .= "left join ip_zones  on ip_zones.zone_id        = interface.int_zone ";
  $q_string .= "left join service   on service.svc_id          = inventory.inv_class ";
  $q_string .= "left join software  on software.sw_companyid   = inventory.inv_id ";
  $q_string .= "left join hardware  on hardware.hw_companyid   = inventory.inv_id ";
  $q_string .= "left join models    on models.mod_id           = hardware.hw_vendorid ";
  $q_string .= "left join parts    on parts.part_id           = models.mod_type ";
  $q_string .= "left join groups    on groups.grp_id           = inventory.inv_manager ";
  $q_string .= "left join products  on products.prod_id        = software.sw_product ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $formVars['group'] . " and hw_primary = 1 ";
  $q_string .= "group by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $svrclass = '';
    if (return_System($db, $a_inventory['inv_id']) == 'Linux') {
      $svrclass = "Linux Server";
    }
    if (return_System($db, $a_inventory['inv_id']) == 'HP-UX') {
      $svrclass = "HP-UX Server";
    }
    if (return_System($db, $a_inventory['inv_id']) == 'FreeBSD') {
      $svrclass = "FreeBSD Server";
    }
    if (return_System($db, $a_inventory['inv_id']) == 'SunOS') {
      $svrclass = "Solaris Server";
    }
    if (return_System($db, $a_inventory['inv_id']) == 'Windows') {
      $svrclass = "Windows Server";
    }

    if ($a_inventory['part_name'] == 'Global Load Balancer' || $a_inventory['part_name'] == 'Local Load Balancer') {
      $svrclass = "Load Balancer";
    }
    if ($a_inventory['part_name'] == 'Blade Chassis') {
      $svrclass = "Server Chassis";
    }
    if ($a_inventory['part_name'] == 'Storage Array') {
      $svrclass = "Storage Server";
    }
    if ($a_inventory['part_name'] == 'Terminal Server') {
      $svrclass = "TermServer";
    }

    $linkedit   = "";
    $linkend   = "";
    if (check_userlevel($db, $AL_Edit)) {
      if (check_grouplevel($db, $a_inventory['inv_manager'])) {
        $linkedit = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\"><img src=\"" . $Imgsroot . "/pencil.gif\"></a>";
        $linkend   = "</a>";
      }
    }

    $ip_address = '';
    $comma = '';
    $q_string  = "select int_addr ";
    $q_string .= "from interface ";
    $q_string .= "where int_ip6 = 0 and int_type != 7 and int_type != 12 and int_addr != \"\" and int_companyid = " . $a_inventory['inv_id'] . " ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_interface = mysqli_fetch_array($q_interface)) {
      $ip_address .= $comma . $a_interface['int_addr'];
      $comma = ", ";
    }


    $q_string  = "select grp_name,grp_snow ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);

    $class = "ui-widget-content";
    $inv_group = $a_inventory['grp_snow'];
    if ($a_inventory['grp_snow'] == '') {
      $inv_group = "[" . $a_inventory['grp_name'] . "]";
      $class = "ui-state-error";
    }

    $inv_appadmin = $a_groups['grp_snow'];
    if ($a_groups['grp_snow'] == '') {
      $inv_appadmin = "[" . $a_groups['grp_name'] . "]";
      $class = "ui-state-error";
    }

    $serial = $a_inventory['hw_serial'];
    if ($a_inventory['hw_serial'] == '') {
      $serial = $a_inventory['inv_uuid'];
    }

    $status = 'In Use';
    if ($a_inventory['hw_active'] == '0000-00-00') {
      $status = 'Build in Progress';
    }

    if ($a_inventory['mod_name'] != 'Virtual Interface') {
      if ($formVars['csv']) {
        print "\"" . $a_inventory['inv_name'] . "\",";
        print "\"" . $ip_address . "\",";
        print "\"" . $svrclass . "\",";
        print "\"" . $a_inventory['loc_west'] . "\",";
        print "\"" . $inv_appadmin . "\",";
        print "\"" . $inv_appadmin . "\",";
        print "\"" . $inv_group . "\",";
        print "\"" . $a_inventory['env_name'] . "\",";
        print "\"" . $status . "\",";
        print "\"" . "&nbsp;" . "\",";
        print "\"" . "&nbsp;" . "\",";
        print "\"" . $a_inventory['inv_function'] . "\",";
        print "\"" . "WSS" . "\",";
        print "\"" . $serial . "\"</br>\n";
      } else {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">" . $linkedit . $a_inventory['inv_name']     . $linkend . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $ip_address                             . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $svrclass                               . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $a_inventory['loc_west']                . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $inv_appadmin                           . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $inv_appadmin                           . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $inv_group                              . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $a_inventory['env_name']                . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $status                                 . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . "&nbsp;"                                . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . "&nbsp;"                                . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $a_inventory['inv_function']            . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . "WSS"                                   . "</td>\n";
        print "  <td class=\"" . $class . "\">"             . $serial                                 . "</td>\n";
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
