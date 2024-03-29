<?php
# Script: show.project.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "show.project.php";

  logaccess($db, $formVars['uid'], $package, "Viewing the Projects table");

  $formVars['id'] = clean($_GET['id'],10);

  $q_string  = "select prj_name,prj_product ";
  $q_string .= "from inv_projects ";
  $q_string .= "where prj_id = " . $formVars['id'] . " ";
  $q_inv_projects = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_inv_projects = mysqli_fetch_array($q_inv_projects);

  $q_string  = "select prod_name ";
  $q_string .= "from inv_products ";
  $q_string .= "where prod_id = " . $a_inv_projects['prj_product'] . " ";
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
<title>View Projects: <?php print $a_inv_projects['prj_name']; ?></title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( );
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><?php print $a_products['prod_name'] . ": " . $a_inv_projects['prj_name']; ?></th>
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
  <li><strong>Server</strong> - The Name of the server. An asterisk (*) indicates the server is accessible using ssh. An at (&) indicates the server is available to Ansible Playbooks</li>
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

  $q_string  = "select hw_id,inv_id,inv_name,hw_asset,hw_serial,mod_name,grp_name,inv_ssh,inv_ansible,hw_verified,hw_update ";
  $q_string .= "from inv_hardware ";
  $q_string .= "left join inv_inventory  on inv_hardware.hw_companyid = inv_inventory.inv_id ";
  $q_string .= "left join inv_groups on inv_hardware.hw_group = inv_groups.grp_id ";
  $q_string .= "left join inv_models on inv_hardware.hw_vendorid = inv_models.mod_id ";
  $q_string .= "where inv_project = " . $formVars['id'] . " and inv_status = 0 and hw_primary = 1 ";
  $q_string .= "order by inv_name";
  $q_inv_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_hardware) > 0) {
    while ($a_inv_hardware = mysqli_fetch_array($q_inv_hardware)) {

      $ssh = "";
      if ($a_inv_hardware['inv_ssh']) {
        $ssh = "*";
      }
      $ansible = "";
      if ($a_inv_hardware['inv_ansible']) {
        $ansible = "@";
      }

      $checkmark = "";
      if ($a_inv_hardware['hw_verified']) {
        $checkmark = "&#x2713;";
      }

      $editpencil = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/pencil.gif\" height=\"10\">";
      $editstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inv_hardware['inv_id'] . "\" target=\"_blank\">";
      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inv_hardware['inv_id'] . "\" target=\"_blank\">";
      $linkend   = "</a>";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $editstart . $editpencil . $linkend . $linkstart . $a_inv_hardware['inv_name']             . $linkend . $ssh . $ansible . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_hardware['hw_asset']             . $linkend                   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_hardware['hw_serial']            . $linkend                   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_hardware['mod_name']             . $linkend                   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_hardware['grp_name']             . $linkend                   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_hardware['hw_update']            . $linkend . $checkmark      . "</td>\n";
      print "</tr>\n";

    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"6\">No hardware found</td>\n";
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

  $q_string  = "select sw_id,svr_companyid,ven_name,typ_name,svr_groupid,sw_software,svr_verified,svr_update,inv_name,grp_name ";
  $q_string .= "from inv_software ";
  $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid = inv_software.sw_id ";
  $q_string .= "left join inv_inventory    on inv_svr_software.svr_companyid  = inv_inventory.inv_id ";
  $q_string .= "left join inv_groups       on inv_svr_software.svr_groupid    = inv_groups.grp_id ";
  $q_string .= "left join inv_vendors      on inv_vendors.ven_id              = inv_software.sw_vendor ";
  $q_string .= "left join inv_sw_types     on inv_sw_types.typ_id             = inv_software.sw_type ";
  $q_string .= "where inv_project = " . $formVars['id'] . " and inv_status = 0 ";
  $q_string .= "order by inv_name,sw_software";
  $q_inv_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_software) > 0) {
    while ($a_inv_software = mysqli_fetch_array($q_inv_software)) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inv_software['svr_companyid'] . "\" target=\"_blank\">";
      $linkend   = "</a>";

      $checkmark = "";
      if ($a_inv_software['svr_verified']) {
        $checkmark = "&#x2713;";
      }

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['inv_name']                  . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['ven_name']                  . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['sw_software']               . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['typ_name']                  . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['grp_name']                  . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_software['svr_update']   . $checkmark . $linkend . "</td>\n";
      print "</tr>\n";

    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"6\">No software found</td>\n";
    print "</tr>\n";
  }

  mysqli_free_result($q_inv_software);

?>
</table>

</div>


</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
