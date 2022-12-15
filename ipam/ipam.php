<?php
# Script: ipam.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "ipam.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the ip addresses.");

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
<title>IP Address Manager</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">IP Address Manager</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('ipaddress-help');">Help</a></th>
</tr>
</table>

<div id="ipaddress-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>An IP Address Manager or IPAM is a tool used to manage the network definitions which are then assigned to 
devices in the network infrastructure.</p>

<p>When entering the IPAM, you will be presented with a list of defined networks along with a list of the 
number of IP addresses assigned to the networks. Clicking on a Network will take you to the IP Address tool 
which then lets you manage the IP Addresses that are assigned to this network.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Network</td>
  <th class="ui-state-default">Members</td>
  <th class="ui-state-default">Network Zone</td>
  <th class="ui-state-default">Location</td>
  <th class="ui-state-default">VLAN ID</td>
  <th class="ui-state-default">Description</td>
</tr>
<?php
  $q_string  = "select net_id,net_ipv4,net_mask,zone_zone,loc_name,net_vlan,net_description ";
  $q_string .= "from network ";
  $q_string .= "left join inv_net_zones on inv_net_zones.zone_id = network.net_zone ";
  $q_string .= "left join inv_locations on inv_locations.loc_id = network.net_location ";
  $q_string .= "where net_ipv4 != '' ";
  $q_string .= "order by net_ipv4 ";
  $q_network = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_network) > 0) {
    while ($a_network = mysqli_fetch_array($q_network)) {

      $linkstart = "<a href=\"ipaddress.php?network=" . $a_network['net_id'] . "\" target=\"_blank\">";
      $linkend = "</a>";

      $total = 0;
      $q_string  = "select ip_ipv4 ";
      $q_string .= "from inv_ipaddress ";
      $q_string .= "where ip_network = " . $a_network['net_id'] . " ";
      $q_inv_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_ipaddress) > 0) {
        while ($a_inv_ipaddress = mysqli_fetch_array($q_inv_ipaddress)) {
          $total++;
        }
      }

      $class = "ui-widget-content";

      print "<tr>\n";
      print "  <td class=\"" . $class . "\">" . $linkstart . $a_network['net_ipv4'] . "/" . $a_network['net_mask'] . $linkend . "</td>\n";
      print "  <td class=\"" . $class . " delete\">" . $total . "</td>\n";
      print "  <td class=\"" . $class . "\">" . $a_network['zone_zone'] . "</td>\n";
      print "  <td class=\"" . $class . "\">" . $a_network['loc_name'] . "</td>\n";
      print "  <td class=\"" . $class . "\">" . $a_network['net_vlan'] . "</td>\n";
      print "  <td class=\"" . $class . "\">" . $a_network['net_description'] . "</td>\n";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"6\">You need to add network records.</td>\n";
    print "</tr>\n";
  }
?>
</table>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
