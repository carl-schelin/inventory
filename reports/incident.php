<?php
# Script: incident.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "incident.php";

  logaccess($db, $formVars['uid'], $package, "Getting a listing of servers and products.");

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

  $orderby = " group by inv_name";

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
<title>Incident Report</title>

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

<form name="inventory">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Incident Table</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong><u>External Unix Platforms</u></strong> Server Naming Conventions</p>

<p><strong>Legacy Naming Conventions</strong></p>

<ul>
  <li>The first two characters are the company identifier.
  <ul>
    <li><strong>in</strong> for Intrado</li>
    <li><strong>ps</strong> for Positron Systems.</li>
  </ul></li>
  <li>The second two characters are location identifiers.
  <ul>
    <li><strong>co</strong> for Colorado</li>
    <li><strong>fl</strong> for Florida</li>
    <li><strong>ca</strong> for Calgary Canada</li>
    <li><strong>to</strong> for Toronto Canada</li>
  </ul></li>
  <li>The next two or three characters are Product identifiers.</li>
  <li>The final one or two characters are instance numbers.</li>
</ul>

<p>In addition there are inherited legacy conventions such as the ALI systems</p>

<ul>
  <li>The first two characters are <strong>hp</strong> indicating Hewlett Packard Unix systems.</li>
  <li>The following characters excluding the final character indicate the location of the system.</li>
  <li>The final character is either an instance number or a location such as 'e' for East, 'w' for West, and 'n' for North.</li>
</ul>

<p>Finally systems from companies Intrado purchased such as HBF or Contact One</p>

<p><strong>Current Naming Conventions</strong></p>

<ul>
  <li>The first six characters are the location of the system.
  <ul>
    <li><strong>lnmtco</strong> Longmont Colorado</li>
    <li><strong>miamfl</strong> Miami Florida</li>
  </ul></li>
  <li>The next two characters indicate the type of location
  <ul>
    <li><strong>dc</strong> Production Data Center</li>
    <li><strong>dz</strong> In the DMZ</li>
    <li><strong>cs</strong> IEN Voice CSS Site</li>
    <li><strong>ec</strong> IEN Voice ECMC Site</li>
  </ul></li>
  <li>The next two characters depend upon the type of location. IEN Voice has a different convention than the non IEN systems. Non-IEN systems use the next two characters as Product identifiers. IEN Voice is listed below.</li>
  <ul>
    <li><strong>ad</strong> Admin Server</li>
    <li><strong>db</strong> Standalone Database Server</li>
    <li><strong>dc</strong> Database Cluster Server</li>
    <li><strong>er</strong> ECR Server</li>
    <li><strong>gc</strong> Provisioning Gateway Server (PGW)</li>
    <li><strong>mp</strong> Management Portal</li>
    <li><strong>ms</strong> Media Server</li>
    <li><strong>ut</strong> Utility Server</li>
  </ul></li>
  <li>The next character(s) will indicate the instance member in the cluster if 'a' or 'b' or the instance if numeric.</li>
  <li>The final character indicates the network interface such as '0' for 'bond0', 'eth0', or 'e1000g0'.</li>
</ul>

<p><strong>Note:</strong> Systems highlighted in 'Red' are flagged as being in the 911 Call Path.</p>

</div>

</div>

<table class="ui-styled-table">
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
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
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

  $where = $product . $group . $inwork . $location . $type;

  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Node Name</th>\n";
  print "  <th class=\"ui-state-default\">Product Name</th>\n";
  print "  <th class=\"ui-state-default\">Description</th>\n";
  print "  <th class=\"ui-state-default\">Documentation</th>\n";
  print "  <th class=\"ui-state-default\">Issues</th>\n";
  print "  <th class=\"ui-state-default\">Location</th>\n";
  print "  <th class=\"ui-state-default\">Group Responsible</th>\n";
  print "</tr>\n";

  $q_string = "select inv_id,inv_name,inv_function,grp_name,inv_location,ct_city,st_state,inv_product,prod_name,inv_callpath,inv_document,hw_active "
            . "from inventory "
            . "left join hardware  on hardware.hw_companyid = inventory.inv_id "
            . "left join products  on products.prod_id      = inventory.inv_product "
            . "left join locations on locations.loc_id      = inventory.inv_location "
            . "left join cities    on cities.ct_id          = locations.loc_city "
            . "left join states    on states.st_id          = locations.loc_state "
            . "left join a_groups    on a_groups.grp_id         = inventory.inv_manager "
            . $where . " " 
            . $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inventory) > 0) {
    while ($a_inventory = mysqli_fetch_array($q_inventory) ) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id']      . "\" target=\"_blank\">";
      $prodstart = "<a href=\"" . $Reportroot . "/show.product.php?id="       . $a_inventory['inv_product'] . "\" target=\"_blank\">";
      $linkend   = "</a>";

      $class = " class=\"ui-widget-content\"";
      if ($a_inventory['hw_active'] == '0000-00-00') {
        $class = " class=\"ui-state-highlight\"";
      }
      if ($a_inventory['inv_callpath'] == 1) {
        $class = " class=\"ui-state-error\"";
      }

      if ($a_inventory['inv_location'] == 31) {
        $lab = " (Lab)";
      } else {
        $lab = "";
      }

      print "<tr id=\"" . $a_inventory['inv_id'] . "\">\n";
      print "  <td" . $class . ">" . $linkstart . $a_inventory['inv_name'] . $linkend . "</td>\n";
      print "  <td" . $class . ">" . $prodstart . $a_inventory['prod_name'] . $linkend . "</td>\n";
      print "  <td" . $class . ">" . $linkstart . $a_inventory['inv_function'] . $linkend . "</td>\n";

      if (strlen($a_inventory['inv_document']) > 0) {
        print "  <td" . $class . "><a href=\"" . $a_inventory['inv_document'] . "\">Documentation</a></td>\n";
      } else {
        print "  <td" . $class . ">&nbsp;</td>\n";
      }

      print "  <td" . $class . "><a href=\"" . $Siteroot . "/issue/issue.php?server=" . $a_inventory['inv_id'] . "\">Issues</a></td>\n";
      print "  <td" . $class . ">" . $linkstart . $a_inventory['ct_city'] . ", " . $a_inventory['st_state'] . $lab . $linkend . "</td>\n";
      print "  <td" . $class . ">" . $a_inventory['grp_name'] . "</td>\n";
      print "</tr>\n";

    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"7\">No matching records.</td>\n";
    print "</tr>\n";
  }

?>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
