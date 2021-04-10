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
  <li id="tm_applications"><a href="<?php print $Siteroot; ?>/index.apps.php">Applications</a>
    <ul>
      <li><a href="<?php print $Issueroot;   ?>/issue.php">Issue Tracker</a></li>
      <li><a href="<?php print $Certsroot;   ?>/certs.php">Web Certificate Management</a></li>
      <li><a href="<?php print $Certsroot;   ?>/webapps.certs.php">-View Certificates</a></li>
      <li><a href="<?php print $DCroot;      ?>/datacenter.php">Location Manager</a></li>
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
  <li id="tm_database"><a href="<?php print $Siteroot; ?>/index.manage.php">Database</a>
    <ul>
      <li><a href="<?php print $Usersroot; ?>/business.php">Business Unit Names</a></li>
      <li><a href="<?php print $Usersroot; ?>/department.php">Department Names</a></li>
      <li><a href="<?php print $Usersroot; ?>/organization.php">Organizations</a></li>
      <li><a href="<?php print $Usersroot; ?>/roles.php">Role Names</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/hardware.php">Hardware Models</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/parts.php">Part Descriptions</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/type.php">Interface Names</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/vlans.php">VLAN Management</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/speed.php">Interface Speeds</a></li>
      <li><a href="<?php print $Hardwareroot; ?>/zones.php">Network Zones</a></li>
      <li><a href="<?php print $Licenseroot; ?>/license.php">License Manager</a></li>
      <li><a href="<?php print $Adminroot; ?>/maintenance.windows.php">Maintenance Windows</a></li>
      <li><a href="<?php print $Adminroot; ?>/product.php">Products and Services</a></li>
      <li><a href="<?php print $Adminroot; ?>/device.php">Device Types</a></li>
      <li><a href="<?php print $Adminroot; ?>/service.php">Service Class</a></li>
      <li><a href="<?php print $Adminroot; ?>/software.php">Software Support</a></li>
      <li><a href="<?php print $Adminroot; ?>/support.php">Support Contracts</a></li>
      <li><a href="<?php print $Adminroot; ?>/timezones.php">Time Zones</a></li>
    </ul>
  </li>
