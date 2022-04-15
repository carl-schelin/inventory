<div id="header">

<p><a href="<?php print $Siteroot; ?>"><img src="<?php print $Siteroot; ?>/imgs/<?php print $Siteheader; ?>"></a></p>

</div>

<div class="main">

<div class="menu">

<ul id="topmenu">
  <li id="tm_home"><a href="<?php print $Siteroot; ?>">Home</a>
    <ul>
      <li><a href="<?php print $Editroot; ?>/inventory.php">Add Server or Device</a></li>
    </ul>
  </li>
  <li id="tm_tools"><a href="<?php print $Siteroot; ?>/index.tools.php">Tools</a>
    <ul>
      <li><a href="<?php print $Issueroot;   ?>/issue.php">Issue Tracker</a></li>
      <li><a href="<?php print $Certsroot;   ?>/certs.php">Web Certificate Management</a></li>
      <li><a href="<?php print $Certsroot;   ?>/webapps.certs.php">-View Certificates</a></li>
      <li><a href="<?php print $Reportroot;  ?>/hostname.php">Hostname Encode/Decode</a></li>
      <li><a href="<?php print $Excluderoot; ?>/exclude.php">Exclusion Manager</a></li>
      <li><a href="<?php print $Imageroot;   ?>/image.php">Image Manager</a></li>
      <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Adminroot; ?>/tags.php');">Tag Manager</a></li>
      <li><a href="<?php print $Statusroot;  ?>/home.php" target="_blank">Status Management</a></li>
<?php
    if (check_userlevel($db, $AL_Admin)) {
?>
      <li><a href="<?php print $Assetroot;  ?>/assets.php" target="_blank">Bulk Upload</a></li>
      <li><a href="<?php print $Manageroot; ?>/users.php" target="_blank">User Manager</a></li>
<?php
  }
?>
      <li><a href="<?php print $Nagiosroot; ?>" target="_blank">Lab Monitoring (Nagios)</a></li>
    </ul>
  </li>

  <li id="tm_ipam"><a href="<?php print $Siteroot; ?>/index.ipam.php">IPAM</a>
    <ul>
      <li><a href="<?php print $IPAMroot; ?>/ipam.php">IP Address Manager</a></li>
      <li><a href="<?php print $IPAMroot; ?>/subzones.php">IP Address Zones</a></li>
      <li><a href="<?php print $IPAMroot; ?>/types.php">IP Address Types</a></li>
      <li><a href="<?php print $IPAMroot; ?>/description.php">Interface Descriptions</a></li>
      <li><a href="<?php print $IPAMroot; ?>/network.php">Network Manager</a></li>
      <li><a href="<?php print $IPAMroot; ?>/zones.php">Network Zones</a></li>
    </ul>
  </li>

  <li id="tm_hardware"><a href="<?php print $Siteroot; ?>/index.hardware.php">Assets</a>
    <ul>
      <li><a href="<?php print $Hardwareroot; ?>/assets.php">Asset Manager</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/device.php">Device Manager</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/devicetype.php">Device Types</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/cpu.php">CPU Manager</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/memory.php">Memory Manager</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/storage.php">Storage Manager</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/other.php">Miscellaneous Hardware</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/hardware.php">Hardware Models</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/parts.php">Part Descriptions</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/speed.php">Speed Setting</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/duplex.php">Duplex Setting</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/media.php">Media Types</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/redundancy.php">Redundancy Types</a></li>
    </ul>
  </li>

  <li id="tm_software"><a href="<?php print $Siteroot; ?>/index.software.php">Software</a>
    <ul>
      <li><a href="<?php print $Softwareroot; ?>/software.php">Software Manager</a></li>
      <li><a href="<?php print $Softwareroot; ?>/license.php">License Manager</a></li>
      <li><a href="<?php print $Softwareroot; ?>/support.php">Software Support Manager</a></li>
    </ul>
  </li>

  <li id="tm_location"><a href="<?php print $Siteroot; ?>/index.datacenter.php">Data Centers</a>
    <ul>
      <li><a href="<?php print $DCroot;      ?>/datacenter.php">Data Center Manager</a></li>
      <li><a href="<?php print $DCroot;      ?>/city.php">City/County Manager</a></li>
      <li><a href="<?php print $DCroot;      ?>/state.php">State Manager</a></li>
      <li><a href="<?php print $DCroot;      ?>/country.php">Country Manager</a></li>
    </ul>
  </li>

  <li id="tm_siteadmin"><a href="<?php print $Siteroot; ?>/index.siteadmin.php">Site Administration</a>
    <ul>
      <li><a href="<?php print $Usersroot; ?>/organization.php">Organizations</a></li>
      <li><a href="<?php print $Usersroot; ?>/business.php">Business Names</a></li>
      <li><a href="<?php print $Usersroot; ?>/department.php">Department Names</a></li>
      <li><a href="<?php print $Usersroot; ?>/groups.php">Group Management</a></li>
      <li><a href="<?php print $Usersroot; ?>/users.php">User Management</a></li>
    </ul>
  </li>

  <li id="tm_database"><a href="<?php print $Siteroot; ?>/index.manage.php">Database</a>
    <ul>
      <li><a href="<?php print $Usersroot; ?>/roles.php">Role Names</a></li>
      <li><a href="<?php print $Adminroot; ?>/maintenance.windows.php">Maintenance Windows</a></li>
      <li><a href="<?php print $Productroot; ?>/product.php">Products and Services</a></li>
      <li><a href="<?php print $Adminroot; ?>/service.php">Service Class</a></li>
      <li><a href="<?php print $Adminroot; ?>/software.php">Software Support</a></li>
      <li><a href="<?php print $Adminroot; ?>/support.php">Support Contracts</a></li>
      <li><a href="<?php print $Adminroot; ?>/timezones.php">Time Zones</a></li>
    </ul>
  </li>
