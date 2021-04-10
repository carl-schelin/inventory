<?php
# Script: show.product.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "show.product.php";

  logaccess($db, $formVars['uid'], $package, "Viewing the Products table");

  $formVars['id'] = clean($_GET['id'],10);

  $q_string  = "select prod_name ";
  $q_string .= "from products ";
  $q_string .= "where prod_id = " . $formVars['id'];
  $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_products = mysqli_fetch_array($q_products);

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
<title>View <?php print $a_products['prod_name']; ?></title>

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
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><?php print $a_products['prod_name']; ?></th>
</tr>
</table>

<div id="tabs">

<ul>
  <li><a href="#hardware">Hardware</a></li>
  <li><a href="#software">Software</a></li>
</ul>


<div id="hardware">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hardware Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('hardware-help');">Help</a></th>
</tr>
</table>

<div id="hardware-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Server</strong> - The Name of the server. An asterisk (*) indicates the server is accessible using ssh. An at (@) indicates the server is available to Ansible playbooks.</li>
  <li><strong>Asset</strong> - The Asset Tag.</li>
  <li><strong>Serial</strong> - The Vendor serial number.</li>
  <li><strong>Service</strong> - The Dell specific service tag.</li>
  <li><strong>Model</strong> - Model information.</li>
  <li><strong>Group</strong> - Hardware Custodian.</li>
  <li><strong>Update</strong> - The date of the last change. A checkmark indicates the change was automatically entered.</li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server</a>
  <th class="ui-state-default">Asset</a>
  <th class="ui-state-default">Serial</a>
  <th class="ui-state-default">Model</a>
  <th class="ui-state-default">Group</a>
  <th class="ui-state-default">Updated</a>
</tr>
<?php

# initialize changelog arrays and variables
  $allservers = array();
  $count = 0;

  $q_string  = "select hw_id,inv_id,inv_name,hw_asset,hw_serial,mod_name,grp_name,inv_ansible,inv_ssh,hw_verified,hw_update ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join a_groups on hardware.hw_group = a_groups.grp_id ";
  $q_string .= "left join models on hardware.hw_vendorid = models.mod_id ";
  $q_string .= "where hw_product = " . $formVars['id'] . " and inv_status = 0 and hw_primary = 1 ";
  $q_string .= "order by inv_name";
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    $ssh = "";
    if ($a_hardware['inv_ssh']) {
      $ssh = "*";
    }
    $ansible = "";
    if ($a_hardware['inv_ansible']) {
      $ansible = "@";
    }

    $checkmark = "";
    if ($a_hardware['hw_verified']) {
      $checkmark = "&#x2713;";
    }

    $editpencil = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/pencil.gif\" height=\"10\">";
    $editstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_hardware['inv_id'] . "\" target=\"_blank\">";
    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_hardware['inv_id'] . "\" target=\"_blank\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $editstart . $editpencil . $linkend . $linkstart . $a_hardware['inv_name']             . $linkend . $ssh . $ansible . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['hw_asset']             . $linkend              . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['hw_serial']            . $linkend              . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['mod_name']             . $linkend              . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['grp_name']             . $linkend              . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['hw_update']            . $linkend . $checkmark . "</td>\n";
    print "</tr>\n";

  }
?>
</table>

</div>


<div id="software">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('software-help');">Help</a></th>
</tr>
</table>

<div id="software-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Server</strong> - The server this software is installed on.</li>
  <li><strong>Vendor</strong> - The software vendor.</li>
  <li><strong>Software</strong> - The software name and version.</li>
  <li><strong>Type</strong> - The type of software. This is used in various places for reporting (such as OS and Instance).</li>
  <li><strong>Group</strong> - The group responsible for the software package.</li>
  <li><strong>Updated</strong> - The last time this entry was updated. A checkmark indicates the software information was gathered automatically.</li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server</a>
  <th class="ui-state-default">Vendor</a>
  <th class="ui-state-default">Software</a>
  <th class="ui-state-default">Type</a>
  <th class="ui-state-default">Group</a>
  <th class="ui-state-default">Updated</a>
</tr>
<?php

  $q_string  = "select sw_id,sw_companyid,sw_vendor,sw_type,sw_group,sw_software,sw_verified,sw_update,inv_name,grp_name ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join a_groups on software.sw_group = a_groups.grp_id ";
  $q_string .= "where sw_product = " . $formVars['id'] . " and inv_status = 0 ";
  $q_string .= "order by inv_name,sw_software";
  $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_software = mysqli_fetch_array($q_software)) {

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_software['sw_companyid'] . "\" target=\"_blank\">";
    $linkend   = "</a>";

    $checkmark = "";
    if ($a_software['sw_verified']) {
      $checkmark = "&#x2713;";
    }

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_software['inv_name']                  . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_software['sw_vendor']                 . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_software['sw_software']               . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_software['sw_type']                   . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_software['grp_name']                  . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_software['sw_update']    . $checkmark . $linkend . "</td>\n";
    print "</tr>\n";

  }

  mysqli_free_result($q_software);

?>
</table>

</div>


</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
