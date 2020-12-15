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
  $q_string .= "from projects ";
  $q_string .= "where prj_id = " . $formVars['id'] . " ";
  $q_projects = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_projects = mysqli_fetch_array($q_projects);

  $q_string  = "select prod_name ";
  $q_string .= "from products ";
  $q_string .= "where prod_id = " . $a_projects['prj_product'] . " ";
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
<title>View Projects: <?php print $a_projects['prj_name']; ?></title>

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
  <th class="ui-state-default"><?php print $a_products['prod_name'] . ": " . $a_projects['prj_name']; ?></th>
</tr>
</table>

<div id="tabs">

<ul>
  <li><a href="#hardware">Hardware</a></li>
  <li><a href="#software">Software</a></li>
  <li><a href="#changelog">Changelog</a></li>
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
  <li><strong>Asset</strong> - The Intrado Asset Tag.</li>
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

  $q_string  = "select hw_id,inv_id,inv_name,hw_asset,hw_serial,mod_name,grp_name,inv_ssh,inv_ansible,hw_verified,hw_update ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join groups on hardware.hw_group = groups.grp_id ";
  $q_string .= "left join models on hardware.hw_vendorid = models.mod_id ";
  $q_string .= "where inv_project = " . $formVars['id'] . " and inv_status = 0 and hw_primary = 1 ";
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
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['hw_asset']             . $linkend                   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['hw_serial']            . $linkend                   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['mod_name']             . $linkend                   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['grp_name']             . $linkend                   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_hardware['hw_update']            . $linkend . $checkmark      . "</td>\n";
    print "</tr>\n";


# this is the gather from all systems for the changelog part of the listing
    $grpcount = 0;
    $q_string = "select grp_changelog,grp_clfile from groups where grp_changelog != ''";
    $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    while ($a_groups = mysqli_fetch_array($q_groups)) {
      $grouplist[$grpcount] = $a_groups['grp_changelog'];
      $filename[$grpcount++] = "." . $a_groups['grp_clfile'];
    }

    $debug = '';
    for ($i = 0; $i < count($grouplist); $i++) {
      if (file_exists( $Sitedir . "/" . $grouplist[$i] . "/" . $a_hardware['inv_name'] . $filename[$i])) {
        $svrlist = file($Sitedir . "/" . $grouplist[$i] . "/" . $a_hardware['inv_name'] . $filename[$i]);
        for ($j = 0; $j < count($svrlist); $j++) {
          $list = explode(" ", $svrlist[$j]);
# create the data field
          if ($list[0] == "Date:") {
            switch($list[3]) {
              case "Jan" : $month = "01"; break;
              case "Feb" : $month = "02"; break;
              case "Mar" : $month = "03"; break;
              case "Apr" : $month = "04"; break;
              case "May" : $month = "05"; break;
              case "Jun" : $month = "06"; break;
              case "Jul" : $month = "07"; break;
              case "Aug" : $month = "08"; break;
              case "Sep" : $month = "09"; break;
              case "Oct" : $month = "10"; break;
              case "Nov" : $month = "11"; break;
              case "Dec" : $month = "12"; break;
            }
            if ($list[2] < 10) {
              $zero = "0";
            } else {
              $zero = "";
            }
            $finaldate = $list[4] . "/" . $month . "/" . $zero . $list[2] . "&nbsp;" . $list[5] . "</td>";
            $finalserver = "<a href=\"/" . $grouplist[$i] . "/server.php?server=" . $a_hardware['inv_name'] . "\">";
          }
# create the from field
          if ($list[0] == "From:") {
            if ($list[1][0] == '"') {
              $from = substr($list[1], 1, strlen($list[1]) - 2);
            } else {
              $from = $list[2];
            }
          }
# process the text file itself
          if ($svrlist[$j] == "--------------\n") {
            $add = 0;
            if ($svrlist[$j + 4] == "--------------\n" || $svrlist[$j + 4] == "Content-Transfer-Encoding: quoted-printable\n") {
              $add = 4;
            }
            $finalname = $from . "</td>";
            if ($svrlist[$j + $add + 1] != "\n") {
              $finaltext = mysqli_real_escape_string(rtrim($svrlist[$j + $add + 1])) . "</td>\n";
            } else {
              if ($svrlist[$j + $add + 2] != "\n") {
                $finaltext = mysqli_real_escape_string(rtrim($svrlist[$j + $add + 2])) . "</td>\n";
              } else {
                $finaltext = mysqli_real_escape_string(rtrim($svrlist[$j + $add + 3])) . "</td>\n";
              }
            }
            $allservers[$count++] = $finaldate . "<td class=\"ui-widget-content\">" . $finalname . "<td class=\"ui-widget-content\">" . $finalserver . $a_hardware['inv_name'] . "</a></td>\n<td class=\"ui-widget-content\">" . $finaltext . "</tr>\n";
          }
        }
      }
    }
# end of changelog gather for this server

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
  $q_string .= "left join groups on software.sw_group = groups.grp_id ";
  $q_string .= "where inv_project = " . $formVars['id'] . " and inv_status = 0 ";
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


<div id="changelog">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Changelog</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('changelog-help');">Help</a></th>
</tr>
</table>

<div id="changelog-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This page shows changes made to this system by any group permitted to make changes.</p>

<ul>
  <li><strong>Change Date</strong> - The date the email was sent to the group's changelog mailing list. It's in reverse order so the newest change is at the top of the list.</li>
  <li><strong>Changed By</strong> - The person who submitted the change.</li>
  <li><strong>Server</strong> - The server that had the change applied to it.</li>
  <li><strong>First Line of Change</strong> - The first line of the changelog submission is extracted from the email.</li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Change Date</th>
  <th class="ui-state-default">Changed By</th>
  <th class="ui-state-default">Server</th>
  <th class="ui-state-default">First Line of Change</th>
</tr>
<?php
  sort($allservers);
  $newarray = array();
  $newarray = array_reverse($allservers);

  for ($i = 0; $i < count($newarray); $i++) {
    print "<tr>\n";
    print "<td class=\"ui-widget-content\">" . $newarray[$i];
  }
?>
</table>

</div>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
