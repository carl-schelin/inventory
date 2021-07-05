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
<title>IP Address Management Tutorial</title>

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

<p><strong><u>IP Address Management Tutorial</u></strong></p>

<p>The purpose of this tutorial is to provide direction in how to use the <strong>IP Address Manager</strong> 
or <strong>IPAM</strong> module in the <strong>Inventory</strong>. The <strong>Inventory</strong> is intended 
to be a building block type tool where there are several modules and supporting editing pages to provide the 
blocks necessary to identify and assemble a device.</p>

<p>An <strong>IPAM</strong> in the context of the <strong>Inventory</strong> is a means of tracking and 
managing the Internet Protocol space used in a network. This module is certainly not what might be 
considered an official <strong>IPAM</strong> in that an <strong>IPAM</strong> is also used to manage DNS 
entries and even DHCP services. In this application, the <strong>IPAM</strong> module is intended to manage 
only the Internet Protocol space which is then used to configure devices in the Inventory.</p>

<p><strong><u>Network Zones</u></strong></p>

<p><strong>Network Zones</strong> are basically layers of a network. The external layer would be the 
DMZ. This zone generally consists of servers that are presented to the Internet. You would have web 
servers in this zone. Corporate would be the internal layer that might consist of internal tools and 
database servers. Internal users might be in this zone as well. Third might be a Lab zone where 
developers and Quality Assurance folks might have servers for testing.</p>

<p>When you select <strong>Network Zones</strong> from the <strong>IPAM</strong> menu, you'll be 
presented with the following screen. Initially there will be no <strong>Network Zones</strong>. 
Click the <strong>Add Network Zone</strong> button to add a new <strong>Network Zone</strong>.</p>

<img src="imgs/netzones.png">

<p>The following dialog box will be presented. There are only two fields here. One to describe 
the <strong>Network Zone</strong> and a second to describe the <strong>Zone Acronym</strong>, 
a shorter string (no more than 5 characters) that might be used as part of a device hostname. 
My tool server is named, lnmt1cuomtool11 where the 'c' identifies the server as being hosted 
in the Corporate Network Zone.</p>

<p>After filling in the fields, click the <strong>Add Network Zone</strong> button.</p>

<img src="imgs/netzones2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. Network Zone, Zone Acronym, Created By, and Date are reasonably clear. The extra field, 
Members, provides a count of the number of devices that are in the <strong>Network Zone</strong>. 
As a precaution, if this count is greater than zero, the Remove button will not be displayed.</p>

<p>If you wish to edit a <strong>Network Zone</strong>, click on the <strong>Network Zone</strong> 
name in the listing.</p>

<img src="imgs/netzones3.png">

<p>The following dialog box will be presented. Make your changes and click the <strong>Update 
Network Zone</strong> button and changes will be applied. You can also use this dialog box to 
make a slight change to a <strong>Network Zone</strong> and save it as a new <strong>Network 
Zone</strong>. Simply click the <strong>Add Network Zone</strong> button to create a new 
<strong>Network Zone</strong>.</p>

<img src="imgs/netzones4.png">

<p><strong><u>IP Address Zone</u></strong></p>

<p>The <strong>IP Address Zone</strong> provides a granular description of an IP Address. 
While a network might be in the Corporate Network Zone, an IP Address in the Corporate 
Network Zone might be further described as an Application specific IP Address where only 
Application network traffic might transverse. This might apply to an environment where 
multiple network interfaces are created on a server. One for Application specific network 
traffic and one for Administration of the server such as administrators logging into the 
server.</p>

<p>When you select <strong>IP Address Zones</strong> from the IPAM menu, you'll be presented 
with the following screen. Initially there will be no <strong>IP Address Zones</strong>. 
Click the <strong>Add IP Address Zone</strong> button to add a new <strong>IP Address 
Zone</strong>.</p>

<img src="imgs/ipzones.png">

<p>The following dialog box will be presented. There are three fields here. One to describe 
the <strong>IP Address Zone</strong>, a second to identify which <strong>Network Zone</strong> 
this <strong>IP Address Zone</strong> is a child of, and a third to describe the <strong>IP 
Address Zone</strong>.</p>

<img src="imgs/ipzones2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. Network Zone, IP Address Zone, Created By, and Date are reasonably clear. The extra field, 
Members, provides a count of the number of devices that are further described by this <strong>IP 
Address Zone</strong>. As a precaution, if this count is greater than zero, the Remove button will not 
be displayed.</p>

<p>If you wish to edit an <strong>IP Address Zone</strong>, click on the <strong>Network</strong> 
zone name in the listing.</p>

<img src="imgs/ipzones3.png">

<p>The following dialog box will then be presented. Make your changes and click the <strong>Update 
IP Address Zone</strong> button and changes will be applied. You can also use this dialog box to make a 
slight change to an <strong>IP Address Zone</strong> and save it as a new <strong>IP Address 
Zone</strong>. Simply click the <strong>Add IP Address Zone</strong> button to create a new <strong>IP 
Address Zone</strong>.</p>

<img src="imgs/ipzones4.png">

<p><strong><u>IP Address Types</u></strong></p>

<p><strong>IP Address Types</strong> let you provide a brief definition of what an <strong>IP 
Address</strong> might be used for.  You can then reserve <strong>IP Addresses</strong> to prevent their 
use by a network device. You can identify the network gateway which is then used by all network devices.</p>

<p>When you select <strong>IP Address Types</strong> from the IPAM menu, you'll be presented with 
the following screen. Initially there will be no <strong>IP Address Types</strong>. Click the <strong>Add 
IP Address Type</strong> button to add a new <strong>IP Address Type</strong>.</p>

<img src="imgs/iptypes.png">

<p>The following dialog box will be presented. There are two fields here. One to create 
the <strong>IP Address Type</strong> and a second to describe the <strong>IP Address Type</strong>.</p>

<img src="imgs/iptypes2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. IP Address Type, Description, Created By, and Date are reasonably clear. The extra field, 
Members, provides a count of the number of devices that are further described by this <strong>IP 
Address Type</strong>. As a precaution, if this count is greater than zero, the Remove button will not 
be displayed.</p>

<p>If you wish to edit an <strong>IP Address Type</strong>, click on the <strong>IP Address Type</strong> 
name in the listing.</p>

<img src="imgs/iptypes3.png">

<p>The following dialog box will then be presented. Make your changes and click the <strong>Update 
IP Address Type</strong> button and changes will be applied. You can also use this dialog box to make a 
slight change to an <strong>IP Address Type</strong> and save it as a new <strong>IP Address 
Type</strong>. Simply click the <strong>Add IP Address Type</strong> button to create a new <strong>IP 
Address Type</strong>.</p>

<img src="imgs/iptypes4.png">


<p><strong><u>Networks</u></strong></p>

<p>In this case, the <strong>Network</strong> configuration here is the top level configuration 
of the networks in use. Once all the above information is completed, a <strong>Network</strong> 
can be created (or edited). The Network definition provides the subnet mask, network zone, 
location, and VLan information that will be used to define the network device.</p>

<p>When you select <strong>Network Manager</strong> from the IPAM menu, you'll be presented 
with the following screen. Initially there will be no <strong>Networks</strong>. 
Click the <strong>Add Network</strong> button to add a new <strong>Network</strong>.</p>

<img src="imgs/network.png">

<p>The following dialog box will be presented. There are multiple fields here. Most don't 
need much of a description. Note that if both the IPv4 and IPv6 fields are filled in, only 
the IPv4 data will be saved. The <strong>Location</strong> will be associated with the 
Network Device.</p>

<img src="imgs/network2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. IPv4 or IPv6 Network/Mask, Network Zone, Location, VLan, Description, Created By, and 
Date are reasonably clear. The extra field, Members, provides a count of the number of devices 
that are further described by this <strong>Network</strong>. As a precaution, if this count is 
greater than zero, the Remove button will not be displayed.</p>

<p>If you wish to edit an <strong>Network</strong>, click on the <strong>Network</strong> name 
in the listing.</p>

<img src="imgs/network3.png">

<p>The following dialog box will then be presented. Make your changes and click the <strong>Update 
Network</strong> button and changes will be applied. You can also use this dialog box to make a 
slight change to an <strong>Network</strong> and save it as a new <strong>Network</strong>. Simply 
click the <strong>Add Network</strong> button to create a new <strong>Network</strong>.</p>

<img src="imgs/network4.png">


<p><strong><u>IPAM</u></strong></p>

<p>The IP Address Manager is the entry point into defining IP Addresses. You will not be able to 
define an IP Address until at least one Network has been created. When that is done, click on the 
Network and you'll be taken to the IP Address Editor.</p>

<img src="imgs/ipam.png">

<p><strong><u>IP Addresses></u></strong></p>

<p>The <strong>IP Address</strong> editor is where you allocate an <strong>IP Address</strong> which 
will be used by a network device. When defined, the <strong>IP Address</strong> becomes selectable 
when you're building a device. </p>

<p>When you select a <strong>Network</strong> on the <strong>IPAM Management</strong> page, you will 
be taken to a page that lists all the <strong>IP Addresses</strong> currently allocated to that 
network. You'll be presented with the following screen. The listing header displays the network 
and mask along with the network zone. Initially there will be no <strong>IP Addresses</strong>. 
Click the <strong>Add IP Address</strong> button to add a new <strong>IP Address</strong>.</p>

<img src="imgs/ipaddress.png">

<p>The following dialog box will be presented. There are multiple fields here. Most don't 
need much of a description. Note that if both the IPv4 and IPv6 fields are filled in, only 
the IPv4 data will be saved.</p>

<img src="imgs/ipaddress2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. IPv4 or IPv6 Network/Mask, HOstname, IP Address Zone, IP Address Type, Description, 
Created By, and Date are reasonably clear.</p>

<p>If you wish to edit an <strong>IP Address</strong>, click on the <strong>IPv4 or IPv6 IP 
Address</strong> name in the listing.</p>

<img src="imgs/ipaddress3.png">

<p>The following dialog box will then be presented. Make your changes and click the <strong>Update 
IP Address</strong> button and changes will be applied. You can also use this dialog box to make a 
slight change to an <strong>IP Address</strong> and save it as a new <strong>IP Address</strong>. 
Simply click the <strong>Add IP Address</strong> button to create a new <strong>IP Address</strong>.</p>

<img src="imgs/ipaddress4.png">


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
