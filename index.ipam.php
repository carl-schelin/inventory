<?php
# Script: index.ipam.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.ipam.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the index.");

# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Table Management</title>

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

<div class="main">

<div class="main ui-widget-content">

<h4>IP Address Management</h4>

<p>An IP Address Manager or IPAM in the context of the Inventory is a means of tracking and managing the 
Internet Protocol space used in a network. This module is certainly not what might be considered an 
official IPAM in that an IPAM is also used to manage DNS entries and even DHCP services. In this 
application, the IPAM module is intended to manage only the Internet Protocol space which is then 
used to configure servers.</p>

<p>When looking at the IPAM menu, there are 5 management pages. You will enter these pages and make 
updates in order to properly define the Network and IP Addresses used in these networks.</p>

<h4>Configuration</h4>

<p>The two lowest level management pages are the IP Address Types and IP Address Zones.</p>

<p>Once these are modified, you then will create networks using the Network management page.</p>

<p>Finally, once all other steps are completed, you will then be able to add IP Addresses.</p>

<h4>IP Address Types</h4>

<p>IP Address Types let you provide a brief definition of what an IP Address might be used for. 
You can then reserve IP Addresses to prevent their use by a network device. You can identify the 
network gateway which is then used by all network devices.</p>

<h4>Network Zones</h4>

<p>We know what a Network Zone is. It's one of the layers of the Network. It can be the Corporate 
Network used by internal servers. It can be a DMZ network which faces the Internet. It can also 
be Integration Lab, Staging, Development, or even Sandbox.</p>

<h4>IP Address Zones</h4>

<p>An IP Address Zone provides a bit more granularity to an IP Address definition. For example, if 
you have an IP address that's specific to a Windows or Linux server. Or even if you are defining 
Internet of Things devices such as a WebCam or Soda Machine. The Network Zone might be Corporate 
but the IP address might be for a Soda Machine.</p>

<h4>Network Manager</h4>

<p>The Network Manager lets you define what the overall network and range of IPs will be.</p>

<h4>IP Address Manager</h4>

<p>This is the main tool used to list network and permit access to manage IP Addresses.</p>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
