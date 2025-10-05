<?php
# Script: index.hardware.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.hardware.php";

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
<title>Asset Management Tutorial</title>

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

<p><strong><u>Asset Management</u></strong></p>

Asset Management tools are used to track assets. In the Inventory application, this is any device 
that performs a function, however does not include items that generally don't have an asset tag 
such as system components like hard drives, video cards, ram, and interfaces.

In the Inventory application, the purpose is to document the computer rooms to make locating 
assets easier when troubleshooting. Especially when a computer room is in a remote location and 
not physically accessible without a lengthy trip.

A physical asset can consist of the following items:

* Case
* Network Interfaces
* Video Interfaces
* USB Interfaces
* Power Supplies
* Power Outlets
* Hard Drives
* Memory
* CPUs

In the Inventory, you'll be able to identify a device such as a server and then configure 
the device.

There are two purposes here.

1. To identify connections between devices and between power. For a UPS, for instance, 
it has both Power Supplies and Power Outlets.

2. 






Connections

In order to manage connections, we'll need to have a list of possible connection types.




Components









<p>The purpose behind Asset Management is to keep track of the Asset but in the Inventory, it's used to better 
understand the computer room environment.</p>

<p>There are two assignment possibilities here.</p>

<ol>
  <li>An individual asset tagged device such as a monitor, laptop, desktop, network device, or server.</li>
  <li>A component that makes up an Asset tagged device such as a hard drive, RAM, or video card.</li>
</ol>

<p>The Inventory lets you assemble components to create a completed Asset such as a server.</p>

<p>The steps involved in building an Asset, assuming an Asset such as a server, are as follows.</p>

<ol>
  <li>In the <a href="<?php print $Hardwareroot; ?>/parts.php" target="_blank">Parts Table</a>, verify there is an appropriate type for the Asset.</p>
  <li>In the appropriate Models table, add the new model.
    <ul><li><a href="<?php print $Hardwareroot; ?>/device.php" target="_blank">Device Manager</a></li>
        <li><a href="<?php print $Hardwareroot; ?>/cpu.php" target="_blank">CPU Manager</a></li>
        <li><a href="<?php print $Hardwareroot; ?>/memory.php" target="_blank">Memory Manager</a></li>
        <li><a href="<?php print $Hardwareroot; ?>/storage.php" target="_blank">Storage Manager</a></li>
        <li><a href="<?php print $Hardwareroot; ?>/other.php" target="_blank">Miscellaneous Hardware</a></li>
    </ul></li>
</ol>


<p><strong><u>Vendor Names</u></strong></p>

All devices have been created by a company. This is a list of all the Vendors that created the hardware you're defining.


<p><strong><u>Part Descriptions</u></strong></p>

The parts table defines the general purpose of a device. A Server is a server, a Switch is a switch, and so on.


<p>The <a href="<?php print $Hardwareroot; ?>/parts.php">Parts Table</a> lists all the part types.</p>

<p>There are two types of components.

<ul>
  <li>Primary components which can contain other components.</li>
  <li>Secondary components which are used to build assets.</li>
</ul>






<p><strong><u>Model Descriptions</u></strong></p>

At this point, you're down to the specific item. It'll have a model number. You'll connect a vendor with a part.


<p>There are multiple pages where you can edit specific types of systems. This reduces the clutter to just 
the type of system you're looking for. 



<p><strong><u>Primary Device Descriptions</u></strong></p>

<p>Primary Devices are Asset that generally have an Asset tag and can contain additional components such 
as hard drives, memory, and CPUs.</p>


<p><strong><u>CPU Descriptions</u></strong></p>

<p>CPUs are components that are generally described for a server but certainly other devices can define 
what CPU is in a system.</p>


<p><strong><u>Memory Descriptions</u></strong></p>

<p>Memory are components that are generally described for a server.</p>


<p><strong><u>Storage Descriptions</u></strong></p>

<p>Storage devices such as hard drives or SSDs are generally described for a server.</p>


<p><strong><u>Other Descriptions</u></strong></p>

<p>This is for most devices that aren't a main Asset tagged one but also aren't generally defined 
for a server.</p>





<p>In order to associate a RAID device with hard disks for a server, the model must begin with RAID</p>

<pre>
Dell R720D
> RAID Volume
>> Disk 1
</pre>

<p>Then the RAID devices show up in the second dropdown in hardware</p>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
