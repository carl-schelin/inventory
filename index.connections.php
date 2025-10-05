<?php
# Script: index.connections.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.connections.php";

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
<title>Connection Management Tutorial</title>

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

<div class="main">

<div class="main ui-widget-content">

<p><strong><u>Connection Management</u></strong></p>

<p>The purpose behind these scripts is to show the connection between devices and network drops. 
There are multiple scripts to manage specific ports such as power and ethernet. When ports 
are all defined, you use the Connection Manager script to show this connections.</p>

<p>The various support scripts do show what the associated connection is but only for the information 
being defined. For example, for Power Outlets, it will show the Power Supplies but not 
ethernet or telephone ports.</p>

<p>There are four script to manage connections plus the connection manager.</p>

<p><strong><u>Power Supplies</u></strong></p>

<p>Unless the PSU has labeling that provides different designations, I count PSUs from left to 
right or bottom to top.</p>

<p><strong><u>Power Outlets</u></strong></p>

<p>Unless the outlets have labeling, I count outlets left to right and bottom to top. One of the 
UPSs I reviewed had two rows of three outlets so bottom left would be PSU0, top right would be 
PSU5.</p>

<p><strong><u>Ethernet Ports</u></strong></p>

<p>For the source ports, if it's connected to a network drop, the device is the source, network 
drop is the target.</p>

<p>If it's a device and a switch, the device is the source and the switch is the target.</p>

<p>This lists RJ45 but also RJ11 (telephone) and USB ports.</p>

<p><strong><u>Fiber Ports</u></strong></p>

<p>This lists just the Fiber ports on devices. Check the cable. Many are LC connectors but I 
found a lot are ST connectors.</p>


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
