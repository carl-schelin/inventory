<?php
# Script: serverstats.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/login/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_ReadOnly);

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 1;
  }

  if ($formVars['group'] == -1) {
    $a_groups['grp_name'] = "All";
  } else {
    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $formVars['group'];
    $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_groups = mysqli_fetch_array($q_groups);
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
<title><?php print $a_groups['grp_name']; ?> Server Growth Data</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><?php print $a_groups['grp_name']; ?> Servers</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<h2>Server Statistics</h2>

<p>This page provides insight into the selected team's working environment. The number of servers added over the years, listings of how many servers are assigned to which product, and a breakdown of the servers 
to software and hardware section.</p>

<ul>
  <li><strong>Growth Chart</strong> - The growth chart provides visibility to three pieces of data.
  <ul>
    <li><strong>Blue Line</strong> - This line shows the increase in the number of servers being managed by the team over the time period. This line is a reflection of the managed systems during that time period.</li>
    <li><strong>Green Bar</strong> - This part of the bar chart shows how many systems have been retired during the course of that year. A longer line means more servers have been decommissioned.</li>
    <li><strong>Red Bar</strong> - This part of the bar chart shows how many systems were built during the course of that year.</li>
  </ul></li>
  <li><strong>Yearly Server Count</strong> - This provides four rows of data. The final row lists the total number of systems that are either in the Project Management pipeline or are currently live. Since not every system has a <strong>Built</strong> date set yet, this number includes them in the count as well.
  <ul>
    <li><strong>Built</strong> - Any system that was built (has a <strong>Built</strong> date set) during that year is counted here.</li>
    <li><strong>Active</strong> - Any system that was made live (has an <strong>Active</strong> date set) is counted here.</li>
    <li><strong>Retired</strong> - Any system that was retired (has a <strong>Retired</strong> date set) during that year is counted here.</li>
    <li><strong>Reused</strong> - Any system that was put back into service as a new system (has a <strong>Reused</strong> date set) during that year is counted here.</li>
  </ul></li>
  <li><strong>Monthly Server Count</strong> - This is a break down of just the servers identified as <strong>Built</strong> with a note at the bottom indicating the number of servers that don't have the <strong>Built</strong> dates set. These are most likely new systems that are at the beginnings of the Project Management timeline or old systems that were decommissioned before we could determine the build date.</li>
  <li><strong>Product Listing</strong> - A breakdown of every Intrado Product supported by the selected team and the number of servers associated with that product. The linked page will display all systems associated with the Product which may be the same or more overall systems as other teams have systems in that same Product.</li>
  <li><strong>Software Listing</strong> - A breakdown of the Operating Systems supported by the selected team and the number of servers associated with that Operating System. The linked page will display all systems associated with the Software which may be the same or more overall systems as other teams have systems with the same installed Software on them.</li>
  <li><strong>Hardware Listing</strong> - A breakdown of the Server Hardware or Virtual System supported by the selected team and the number of servers associated with that Device. The linked page will display all systems associated with the Hardware which may be the same or more overall systems as other teams have systems with the same Hardware.</li>
</ul>

</div>

</div>


<div id="tabs">

<ul>
  <li><a href="#graph">Growth Graph</a></li>
  <li><a href="#yearly">Yearly Server Count</a></li>
  <li><a href="#count">Monthly Server Count</a></li>
  <li><a href="#products">Product Listing</a></li>
  <li><a href="#software">Software Listing</a></li>
  <li><a href="#hardware">Hardware Listing</a></li>
  <li><a href="#service">Service Class Count</a></li>
</ul>


<div id="graph">

<img src="servergraph.php?group=<?php print $formVars['group']; ?>">

</div>


<div id="yearly">

<table class="ui-styled-table">
<?php

  $tgone = 0;
  $tyear = 0;

  if ($formVars['group'] == -1) {
    $admin = "";
  } else {
    $admin = " and inv_manager = " . $formVars['group'];
  }

  $built   = array();
  $active  = array();
  $retired = array();
  $reused  = array();
  $total_live           = 0;
  $total_built          = 0;
  $total_active         = 0;
  $total_retired        = 0;
  $total_reused         = 0;
  $total_decommissioned = 0;

  $q_string  = "select hw_built,hw_active,hw_retired,hw_reused,inv_status ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "where hw_primary = 1 " . $admin . " ";
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    if ($a_hardware['inv_status'] == 0) {
      $total_live++;
    } else {
      $total_decommissioned++;
    }

# count all the servers for each year
    $dbyear = explode("-", $a_hardware['hw_built']);
    if ($dbyear[0] != '0000') {
      if (isset($built[$dbyear[0]])) {
        $built[$dbyear[0]]++;
      } else {
        $built[$dbyear[0]] = 1;
      }
      $total_built++;
    }

    $dbyear = explode("-", $a_hardware['hw_active']);
    if ($dbyear[0] != '0000') {
      if (isset($active[$dbyear[0]])) {
        $active[$dbyear[0]]++;
      } else {
        $active[$dbyear[0]] = 1;
      }
      $total_active++;
    }

    $dbyear = explode("-", $a_hardware['hw_retired']);
    if ($dbyear[0] != '0000') {
      if (isset($retired[$dbyear[0]])) {
        $retired[$dbyear[0]]++;
      } else {
        $retired[$dbyear[0]] = 1;
      }
      $total_retired++;
    }

    $dbyear = explode("-", $a_hardware['hw_reused']);
    if ($dbyear[0] != '0000') {
      if (isset($reused[$dbyear[0]])) {
        $reused[$dbyear[0]]++;
      } else {
        $reused[$dbyear[0]] = 1;
      }
      $total_reused++;
    }
  }

  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Servers</th>\n";
  for ($i = 2000; $i <= date('Y'); $i++) {
    print "  <th class=\"ui-state-default\">$i</th>\n"; 
  }
  print "  <th class=\"ui-state-default\">Total</th>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">Built</td>\n";
  $ttotal = 0;
  for ($i = 2000; $i <= date('Y'); $i++) {
    if (isset($built[$i])) {
      print "  <td class=\"ui-widget-content\">$built[$i]</td>\n";
    } else {
      print "  <td class=\"ui-widget-content\">0</td>\n";
    }
  }
  print "  <td class=\"ui-widget-content\">$total_built</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">Active</td>\n";
  for ($i = 2000; $i <= date('Y'); $i++) {
    if (isset($active[$i])) {
      print "  <td class=\"ui-widget-content\">$active[$i]</td>\n";
    } else {
      print "  <td class=\"ui-widget-content\">0</td>\n";
    }
  }
  print "  <td class=\"ui-widget-content\">$total_active</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-state-highlight\">Retired</td>\n";
  for ($i = 2000; $i <= date('Y'); $i++) {
    if (isset($retired[$i])) {
      print "  <td class=\"ui-state-highlight\">$retired[$i]</td>\n";
    } else {
      print "  <td class=\"ui-state-highlight\">0</td>\n";
    }
  }
  print "  <td class=\"ui-state-highlight\">$total_retired</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-state-highlight\">Reused</td>\n";
  for ($i = 2000; $i <= date('Y'); $i++) {
    if (isset($reused[$i])) {
      print "  <td class=\"ui-state-highlight\">$reused[$i]</td>\n";
    } else {
      print "  <td class=\"ui-state-highlight\">0</td>\n";
    }
  }
  print "  <td class=\"ui-state-highlight\">$total_reused</td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\" colspan=\"35\">Total Production Systems: $total_live (includes Built/Active device with unset dates)</td>\n";
  print "</tr>\n";
?>

</table>

</div>


<div id="count">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Number of Servers Built by Month/Year</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Year</th>
  <th class="ui-state-default">Jan</th>
  <th class="ui-state-default">Feb</th>
  <th class="ui-state-default">Mar</th>
  <th class="ui-state-default">Apr</th>
  <th class="ui-state-default">May</th>
  <th class="ui-state-default">Jun</th>
  <th class="ui-state-default">Jul</th>
  <th class="ui-state-default">Aug</th>
  <th class="ui-state-default">Sep</th>
  <th class="ui-state-default">Oct</th>
  <th class="ui-state-default">Nov</th>
  <th class="ui-state-default">Dec</th>
  <th class="ui-state-default">Total</th>
</tr>
<?php

  $tgone = 0;
  $tyear = 0;
  $start = 200701;

  if ($formVars['group'] == -1) {
    $admin = "";
  } else {
    $admin = " and hw_group = " . $formVars['group'];
  }

  $q_string  = "select hw_built ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "where hw_built != '0000-00-00' and hw_primary = 1 " . $admin . " ";
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    $dbyear = explode("-", $a_hardware['hw_built']);
    $dbyear[1] = $dbyear[1] + 0;
    $myear[$dbyear[0]][$dbyear[1]]++;

  }

  $overall = 0;
  for ($j = 2000; $j <= date('Y'); $j++) {
    $ytotal = 0;
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">" . $j . "</th>\n";
    for ($i = 1; $i < 13; $i++) {
      print "  <td class=\"ui-widget-content\">";
      if (!isset($myear[$j][$i])) {
        $myear[$j][$i] = 0;
      }
      print $myear[$j][$i];
      $ytotal += $myear[$j][$i];
      print "</td>\n";
    }
    print "<td class=\"ui-widget-content\">" . $ytotal . "</td>\n";
    print "</tr>\n";
    $overall += $ytotal;
  }
  print "<tr>\n";
  print "  <td colspan=\"14\">Total Servers Built Since 2000: " . $overall ."</td>\n";
  print "</tr>\n";
  print "</table>\n";

  $q_string  = "select count(*) ";
  $q_string .= "from hardware ";
  $q_string .= "left join software on software.sw_companyid = hardware.hw_companyid ";
  $q_string .= "where hw_companyid != 0 " . $admin . " ";
  $q_string .= "and hw_built = '0000-00-00' ";
  $q_string .= "and hw_primary = 1";
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_hardware = mysqli_fetch_row($q_hardware);

  print "<p>Note: There are " . $a_hardware[0] . " servers with 0000-00-00 build dates which weren't counted.</p>\n";

?>

</div>


<div id="products">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Product Listing</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Product</th>
  <th class="ui-state-default">Number of Servers</th>
</tr>
<?php
  if ($formVars['group'] == -1) {
    $admin = '';
  } else {
    $admin = ' and inv_manager = ' . $formVars['group'];
  }
  $total = 0;

  $q_string  = "select inv_product,prod_name,count(inv_product) ";
  $q_string .= "from inventory ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product  ";
  $q_string .= "where inv_status = 0 " . $admin . " ";
  $q_string .= "group by prod_name";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $linkstart = "<a href=\"" . $Siteroot . "/reports/show.product.php?id=" . $a_inventory['inv_product']  . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['prod_name']          . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['count(inv_product)']            . "</td>\n";
    print "</tr>\n";
    $total += $a_inventory['count(inv_product)'];
  }
?>
<tr>
  <td class="ui-widget-content">&nbsp;</td>
  <td class="ui-widget-content">Total: <?php print $total; ?></td>
</tr>
</table>

</div>


<div id="software">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software Listing</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software</th>
  <th class="ui-state-default">Number of Servers</th>
</tr>
<?php
  if ($formVars['group'] == -1) {
    $admin = '';
  } else {
    $admin = ' and inv_manager = ' . $formVars['group'];
  }
  $total = 0;

  $q_string  = "select sw_software,count(sw_software) ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where inv_status = 0 and sw_type = 'OS' " . $admin . " ";
  $q_string .= "group by sw_software";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $linkstart = "<a href=\"" . $Siteroot . "/reports/search.software.php?search_for=" . mysqli_real_escape_string($a_inventory['sw_software']) . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['sw_software']        . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_inventory['count(sw_software)']            . "</td>\n";
    print "</tr>\n";
    $total += $a_inventory['count(sw_software)'];
  }
?>
<tr>
  <td class="ui-widget-content">&nbsp;</td>
  <td class="ui-widget-content">Total: <?php print $total; ?></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Major OS Breakdown</th>
</tr>
<?php

  $q_string  = "select inv_id ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 " . $admin . " ";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {
    $os = return_System($db, $a_inventory['inv_id']);
    if (strlen($os) == 0) {
      $os = "Unknown OS";
    }

    if (!isset($listing[$os])) {
      $listing[$os] = 0;
    }
    if (!isset($system[$os])) {
      $system[$os] = '';
    }

    $system[$os] = $os;
    $listing[$os]++;

  }

  sort($system);

  $total = 0;
  foreach ($system as &$display) {
    print "<tr>\n";
    print "  <td>" . $display . "</td>\n";
    print "  <td>" . $listing[$display] . "</td>\n";
    print "</tr>\n";
    $total += $listing[$display];
  }

  print "<tr>\n";
  print "  <td colspan=\"2\">Total Systems: " . $total . "</td>\n";
  print "</tr>\n";

?>
</table>

</div>


<div id="hardware">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hardware Listing</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Type</th>
  <th class="ui-state-default">Vendor</th>
  <th class="ui-state-default">Model</th>
  <th class="ui-state-default">Number of Servers</th>
</tr>
<?php
  if ($formVars['group'] == -1) {
    $admin = '';
  } else {
    $admin = ' and inv_manager = ' . $formVars['group'];
  }
  $total = 0;

  $q_string  = "select part_name,mod_vendor,mod_name,mod_virtual,count(inv_name) ";
  $q_string .= "from hardware ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join parts on parts.part_id = models.mod_type ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "where mod_primary = 1 and inv_status = 0 " . $admin . " ";
  $q_string .= "group by mod_vendor,mod_name ";
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    $linkvendor = "<a href=\"" . $Siteroot . "/reports/search.hardware.php?search_for=" . mysqli_real_escape_string($a_hardware['mod_vendor']) . "\">";
    $linkname   = "<a href=\"" . $Siteroot . "/reports/search.hardware.php?search_for=" . mysqli_real_escape_string($a_hardware['mod_name'])   . "\">";
    $linktype   = "<a href=\"" . $Siteroot . "/reports/search.hardware.php?search_for=" . mysqli_real_escape_string($a_hardware['part_name'])  . "\">";
    $linkend    = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linktype   . $a_hardware['part_name']       . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkvendor . $a_hardware['mod_vendor']      . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkname   . $a_hardware['mod_name']        . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"               . $a_hardware['count(inv_name)']            . "</td>\n";
    print "</tr>\n";
    $total += $a_hardware['count(inv_name)'];

  }
?>
<tr>
  <td class="ui-widget-content">&nbsp;</td>
  <td class="ui-widget-content">&nbsp;</td>
  <td class="ui-widget-content">&nbsp;</td>
  <td class="ui-widget-content">Total: <?php print $total; ?></td>
</tr>
</table>

</div>


<div id="service">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Service Class Listing</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Name</th>
  <th class="ui-state-default">Acronym</th>
  <th class="ui-state-default">Availability</th>
  <th class="ui-state-default">Downtime</th>
  <th class="ui-state-default">MTBF</th>
  <th class="ui-state-default">Geographically Redundant</th>
  <th class="ui-state-default">MTTR</th>
  <th class="ui-state-default">Shared Resources</th>
  <th class="ui-state-default">Time to Restore</th>
  <th class="ui-state-default">Total Devices</th>
</tr>
<?php
  if ($formVars['group'] == -1) {
    $admin = '';
  } else {
    $admin = ' and inv_manager = ' . $formVars['group'];
  }
  $total = 0;

  $undefined = 0;
  $lmcs = 0;
  $callpath = 0;
  $bcs = 0;
  $bes = 0;
  $bss = 0;
  $ubs = 0;
  $lab = 0;

  $q_string  = "select inv_class,inv_callpath ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 " . $admin . " ";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if ($a_inventory['inv_class'] == 0) {
      $undefined++;
    }
    if ($a_inventory['inv_class'] == 1) {
      $lmcs++;
    }
    if ($a_inventory['inv_callpath'] == 1) {
      $callpath++;
    }
    if ($a_inventory['inv_class'] == 2) {
      $bcs++;
    }
    if ($a_inventory['inv_class'] == 3) {
      $bes++;
    }
    if ($a_inventory['inv_class'] == 4) {
      $bss++;
    }
    if ($a_inventory['inv_class'] == 5) {
      $ubs++;
    }
    if ($a_inventory['inv_class'] == 6) {
      $lab++;
    }

  }

  $q_string  = "select svc_id,svc_name,svc_acronym,svc_availability,svc_downtime,svc_mtbf,svc_geographic,svc_mttr,svc_resource,svc_restore ";
  $q_string .= "from service ";
  $q_string .= "order by svc_id ";
  $q_service = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_service = mysqli_fetch_array($q_service)) {
  
    $geographic = 'No';
    if ($a_service['svc_geographic'] == 1) {
      $geographic = 'Yes';
    }
    $resource = 'No';
    if ($a_service['svc_resource'] == 1) {
      $resource = 'Yes';
    }

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_service['svc_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $a_service['svc_acronym'] . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $a_service['svc_availability'] . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $a_service['svc_downtime'] . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $a_service['svc_mtbf'] . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $geographic . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $a_service['svc_mttr'] . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $resource . "</td>\n";
    print "  <td class=\"ui-widget-content delete\">" . $a_service['svc_restore'] . "</td>\n";


    if ($a_service['svc_id'] == 1) {
      print "  <td class=\"ui-widget-content delete\">" . $lmcs . "/" . $callpath . "</td>\n";
    }
    if ($a_service['svc_id'] == 2) {
      print "  <td class=\"ui-widget-content delete\">" . $bcs . "</td>\n";
    }
    if ($a_service['svc_id'] == 3) {
      print "  <td class=\"ui-widget-content delete\">" . $bes . "</td>\n";
    }
    if ($a_service['svc_id'] == 4) {
      print "  <td class=\"ui-widget-content delete\">" . $bss . "</td>\n";
    }
    if ($a_service['svc_id'] == 5) {
      print "  <td class=\"ui-widget-content delete\">" . $ubs . "</td>\n";
    }
    if ($a_service['svc_id'] == 6) {
      print "  <td class=\"ui-widget-content delete\">" . $lab . "</td>\n";
    }

    print "</tr>\n";
  }
  print "<tr>\n";
  print "  <td class=\"ui-widget-content button\" colspan=\"9\">Undefined:</td>\n";
  print "  <td class=\"ui-widget-content delete\">" . $undefined . "</td>\n";
  print "</tr>\n";

?>
</table>

</div>



</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
