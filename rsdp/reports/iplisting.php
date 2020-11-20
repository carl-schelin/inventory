<?php
# Script: iplisting.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('2');

  $package = "iplisting.php";

  logaccess($_SESSION['uid'], $package, "List of RSDP systems by IP");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: IP Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="help" style="display:none">

<div id="main">

<h1>Help</h1>

<p>The Rapid Server Deployment Process is meant to take a common system build and manage the steps involved from start to finish.</p>

<p>This page provides a list of RSDP servers, requestors, IP addresses, and links to each of the RSDP processes.</p>

</div>

</div>

<div id="main">

<form name="iplisting">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">IP Address Management</th>
  <th class="ui-state-default" width="5"><a href="javascript:;" onmousedown="toggleDiv('address-help');">Help</a></th>
</tr>
</table>

<div id="address-help" style="display: none">

<div class="main-help ui-widget-content">


</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
    <input type="submit" name="clone" value="Request New Server">
  </td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">RSDP Listing</th>
  <th class="ui-state-default" width="5"><a href="javascript:;" onmousedown="toggleDiv('listing-help');">Help</a></th>
</tr>
</table>

<div id="listing-help" style="display: none">

<div class="main-help ui-widget-content">

<p>Click on any of the steps to access the task.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">ID</th>
  <th class="ui-state-default">Requestor</th>
  <th class="ui-state-default">Interface Name</th>
  <th class="ui-state-default">IP</th>
  <th class="ui-state-default">1</th>
  <th class="ui-state-default">2</th>
  <th class="ui-state-default">3</th>
  <th class="ui-state-default">4</th>
  <th class="ui-state-default">5</th>
  <th class="ui-state-default">10</th>
  <th class="ui-state-default">11</th>
  <th class="ui-state-default">12</th>
  <th class="ui-state-default">13</th>
  <th class="ui-state-default">14</th>
  <th class="ui-state-default">15</th>
  <th class="ui-state-default">16</th>
  <th class="ui-state-default">17</th>
  <th class="ui-state-default">18</th>
</tr>
<?php

  $q_string  = "select rsdp_id,usr_name,if_name,if_ip ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join rsdp_interface on rsdp_server.rsdp_id = rsdp_interface.if_rsdp ";
  $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
  $q_string .= "order by if_ip,if_name";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_server['rsdp_id']  . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_server['usr_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_server['if_name']  . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_server['if_ip']    . "</td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/build/initial.php?rsdp="          . $a_rsdp_server['rsdp_id'] . "\">Req</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/build/build.php?rsdp="            . $a_rsdp_server['rsdp_id'] . "\">Des</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/san/designed.php?rsdp="           . $a_rsdp_server['rsdp_id'] . "\">SAN</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/network/network.php?rsdp="        . $a_rsdp_server['rsdp_id'] . "\">Net</a></td>\n";
    if (rsdp_Virtual($a_rsdp_server['rsdp_id'])) {
      print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/physical/physical.php?rsdp="      . $a_rsdp_server['rsdp_id'] . "\">PHY</a></td>\n";
    } else {
      print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/virtual/virtual.php?rsdp="        . $a_rsdp_server['rsdp_id'] . "\">VM</a></td>\n";
    }
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/os/installed.php?rsdp="           . $a_rsdp_server['rsdp_id'] . "\">OS</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/san/provisioned.php?rsdp="        . $a_rsdp_server['rsdp_id'] . "\">SAN</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/os/configured.php?rsdp="          . $a_rsdp_server['rsdp_id'] . "\">OS</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/backups/backups.php?rsdp="        . $a_rsdp_server['rsdp_id'] . "\">BU</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/monitoring/monitoring.php?rsdp="  . $a_rsdp_server['rsdp_id'] . "\">Mon</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/application/installed.php?rsdp="  . $a_rsdp_server['rsdp_id'] . "\">App</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/application/monitored.php?rsdp="  . $a_rsdp_server['rsdp_id'] . "\">Mon</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/application/configured.php?rsdp=" . $a_rsdp_server['rsdp_id'] . "\">App</a></td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/infosec/scanned.php?rsdp="        . $a_rsdp_server['rsdp_id'] . "\">Scn</a></td>\n";
    print "</tr>\n";
  }
?>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
