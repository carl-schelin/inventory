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
<title>IP Address Management</title>

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

<p><strong><u>IP Address Management</u></strong></p>

<p>An <strong>IP Address Manager</strong> or IPAM in the context of the <strong>Inventory</strong> is 
a means of tracking and managing the Internet Protocol space used in a network. This module is 
certainly not what might be considered an official <strong>IPAM</strong> in that an <strong>IPAM</strong> 
is also used to manage DNS entries and even DHCP services. In this application, the <strong>IPAM</strong> 
module is intended to manage only the Internet Protocol space which is then used to configure devices in 
the Inventory.</p>

<p>The purpose of this article is to provide direction in how to use the <strong>IPAM</strong> module 
in the <strong>Inventory</strong>. The <strong>Inventory</strong> is intended to be a building block 
type tool where there are several modules and supporting editing pages to provide the blocks necessary 
to identify and assemble a device.</p>

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
the Network Zone and a second to describe a shorter string (no more than 5 characters) that 
might be used as part of a device hostname. My tool server is named, lnmt1cuomtool11 where the 
'c' identifies the server as being hosted in the Corporate Network Zone.</p>

<p>After filling in the fields, click the Add Network Zone button.</p>

<img src="imgs/netzones2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. Network Zone, Zone Acronym, Changed By, and Date are reasonably clear. The extra field, 
Members, provides a count of the number of devices that are in the Network Zone. As a precaution, 
if this count is greater than zero, the Remove button will not be displayed.</p>

<p>If you wish to edit a Network Zone, click on the Network Zone name in the listing.</p>

<img src="imgs/netzones3.png">

<p>The following dialog box will be presented. Make your changes and click the Update Network Zone 
button and changes will be applied. You can also use this dialog box to make a slight change to 
a Network Zone and save it as a new Network Zone. Simply click the Add Network Zone button to 
create a new Network Zone.</p>

<img src="imgs/netzones4.png">

<p><strong><u>IP Address Zone</u></strong></p>

<p>The IP Address Zone provides a granular description of an IP Address. While a Network might 
be in the Corporate Network Zone, an IP Address in the Corporate Network Zone might be further 
described as an Application specific IP Address where only Application network traffic might 
transverse. This might apply to an environment where multiple network interfaces are created 
on a server. One for Application specific network traffic and one for Administration of the 
server such as administrators logging into the server.</p>

<p>When you select IP Address Zones from the IPAM menu, you'll be presented with the following 
screen. Initially there will be no IP Address Zones. Click the Add IP Address Zone button to 
add a new IP Address Zone.</p>

<img src="imgs/ipzones.png">

<p>The following dialog box will be presented. There are three fields here. One to describe 
the IP Address Zone, a second to identify which Network Zone this IP Address Zone is a child 
of, and a third to describe the IP Address Zone.</p>

<img src="imgs/ipzones2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. Network Zone, IP Address Zone, Changed By, and Date are reasonably clear. The extra field, 
Members, provides a count of the number of devices that are further described by this IP 
Address Zone. As a precaution, if this count is greater than zero, the Remove button will not 
be displayed.</p>

<p>If you wish to edit an IP Address Zone, click on the Network zone name in the listing.</p>

<img src="imgs/ipzones3.png">

<p>The following dialog box will then be presented. Make your changes and click the Update 
IP Address Zone button and changes will be applied. You can also use this dialog box to make a 
slight change to an IP Address Zone and save it as a new IP Address Zone. Simply click the 
Add IP Address Zone button to create a new IP Address Zone.</p>

<img src="imgs/ipzones4.png">

<p><strong><u>IP Address Types</u></strong></p>

<p>IP Address Types let you provide a brief definition of what an IP Address might be used for. 
You can then reserve IP Addresses to prevent their use by a network device. You can identify the 
network gateway which is then used by all network devices.</p>

<p>When you select IP Address Types from the IPAM menu, you'll be presented with the following 
screen. Initially there will be no IP Address Types. Click the Add IP Address Type button to 
add a new IP Address Type.</p>

<img src="imgs/iptypes.png">

<p>The following dialog box will be presented. There are two fields here. One to create 
the IP Address Type and a second to describe the IP Address Type.</p>

<img src="imgs/iptypes2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. IP Address Type, Description, Changed By, and Date are reasonably clear. The extra field, 
Members, provides a count of the number of devices that are further described by this IP 
Address Type. As a precaution, if this count is greater than zero, the Remove button will not 
be displayed.</p>

<p>If you wish to edit an IP Address Type, click on the IP Address Type name in the listing.</p>

<img src="imgs/iptypes3.png">

<p>The following dialog box will then be presented. Make your changes and click the Update 
IP Address Type button and changes will be applied. You can also use this dialog box to make a 
slight change to an IP Address Type and save it as a new IP Address Type. Simply click the 
Add IP Address Type button to create a new IP Address Type.</p>

<img src="imgs/iptypes4.png">


<p><strong><u>Networks</u></strong></p>

<p></p>

<img src="imgs/network.png">

<p></p>

<img src="imgs/network2.png">

<p>When added, you'll see the entry in the listing as shown below. The fields should be pretty 
obvious. IPv4 or IPv6 Network/Mask, Network Zone, Location, VLan, Description, Changed By, and 
Date are reasonably clear. The extra field, Members, provides a count of the number of devices 
that are further described by this IP Address Type. As a precaution, if this count is greater 
than zero, the Remove button will not be displayed.</p>

<p>If you wish to edit an IP Address Type, click on the IP Address Type name in the listing.</p>

<img src="imgs/network3.png">

<p>The following dialog box will then be presented. Make your changes and click the Update 
Network button and changes will be applied. You can also use this dialog box to make a 
slight change to an Network and save it as a new Network. Simply click the 
Add Network button to create a new Network.</p>

<img src="imgs/network4.png">


<p><strong><u>IPAM</u></strong></p>

<p></p>

<img src="imgs/ipam.png">

<p><strong><u>IP Addresses></u></strong></p>

<p>The IP Address editor is where you allocate an IP Address. When defined, the IP Address becomes selectable when you're building a device.The IP Address Zone provides a granular description of an IP Address. While a Network might 
be in the Corporate Network Zone, an IP Address in the Corporate Network Zone might be further 
described as an Application specific IP Address where only Application network traffic might 
transverse. This might apply to an environment where multiple network interfaces are created 
on a server. One for Application specific network traffic and one for Administration of the 
server such as administrators logging into the server.</p>

<p>When you select IP Address Zones from the IPAM menu, you'll be presented with the following 
screen. Initially there will be no IP Address Zones. Click the Add IP Address Zone button to 
add a new IP Address Zone.</p>

<img src="imgs/ipaddress.png">

<p></p>

<img src="imgs/ipaddress2.png">

<p></p>

<img src="imgs/ipaddress3.png">

<p></p>

<img src="imgs/ipaddress4.png">


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
