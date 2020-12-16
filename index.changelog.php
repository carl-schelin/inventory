<?php
# Script: index.changelog.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.changelog.php";

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
<title>Server Management System</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<div id="tabs">

<ul>
  <li><a href="#unix">Unix</a></li>
  <li><a href="#virtualization">Virtualization</a></li>
  <li><a href="#database">Database</a></li>
  <li><a href="#tandem">Tandem</a></li>
  <li><a href="#webapps">WebApps</a></li>
  <li><a href="#storage">Storage/Backups</a></li>
  <li><a href="#icl">ICL</a></li>
  <li><a href="#windows">Windows</a></li>
  <li><a href="#monitoring">Monitoring</a></li>
  <li><a href="#mobility">Mobility</a></li>
  <li><a href="#network">Network</a></li>
  <li><a href="#engineering">Systems Engineering</a></li>
  <li><a href="#vns">Voice Network Support</a></li>
  <li><a href="#alimsysadmin">ALIM SysAdmins</a></li>
  <li><a href="#tss">TSS SysAdmins</a></li>
  <li><a href="#ienv">IEN Voice SysAdmins</a></li>
  <li><a href="#scm">SCM</a></li>
  <li><a href="#i3">i3 App Sys Admins</a></li>
</ul>


<div id="unix">

<p><strong>External Unix Changelogs</strong></p>

<ul>
  <li><a href="/changelog">Server Changelog Pages</a></li>
  <li><a href="/changelog/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/changelog/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Unix)) {
?>
  <li><a href="<?php print $Listingroot; ?>/unix.php">Manage Server and Application Listings</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Unix; ?>">Application Changelog Report</a> - View changelog entries for Applications. Server changelog entries are viewable on the server detail record under the Changelog tab.</li>
  <li><a href="<?php print $Siteroot; ?>/servers.unix">servers.unix Listing review</a> - The final list of servers and applications.</li>
</ul>

</div>


<div id="virtualization">

<p><strong>Virtualization Team Changelogs</strong></p>

<ul>
  <li><a href="/chglogvtt">Server Changelog Pages</a></li>
  <li><a href="/chglogvtt/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogvtt/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Virtualization)) {
?>
  <li><a href="<?php print $Listingroot; ?>/virtualization.php">Manage Virtualization Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Virtualization; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="database">

<p><strong>Database Admins Changelogs</strong></p>

<ul>
  <li><a href="/chglogdba">Server Changelog Pages</a></li>
  <li><a href="/chglogdba/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogdba/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_DBAdmins)) {
?>
  <li><a href="<?php print $Listingroot; ?>/dbadmins.php">Add DBA Changelog Listing</a> - The changelog Mail directory permissions are updated every 30 minutes on the half hour.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_DBAdmins; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="tandem">

<p><strong>Tandem Changelogs</strong></p>

<ul>
  <li><a href="/chglogtdm">Server Changelog Pages</a></li>
  <li><a href="/chglogtdm/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogtdm/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Tandem)) {
?>
  <li><a href="<?php print $Listingroot; ?>/tandem.php">Add Tandem Changelog Listing</a> - The changelog Mail directory permissions are updated every 30 minutes on the half hour.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Tandem; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="webapps">

<p><strong>Web Applications Changelog</strong></p>

<ul>
  <li><a href="/chglogweb">Server Changelog Pages</a> This lists every changelog entry and a link to the file if any changes have been entered.</li>
  <li><a href="/chglogweb/listing.php">Sorted Changelog Pages</a> This is a list of all changes for the group with dates, owner, and the first line of the change.</li>
  <li><a href="/chglogweb/countoff.php">Weekly Changelog Count</a> This counts all the changes by week.</li>
<?php
  if (check_grouplevel($db, $GRP_WebApps)) {
?>
  <li><a href="<?php print $Listingroot; ?>/webapps.php">Manage Web Applications Changelog Server and Application listing</a> - The changelog Mail directory permissions are updated every 30 minutes on the half hour.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_WebApps; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
  <li><a href="<?php print $Siteroot; ?>/servers.web">Listing review</a> - The actual list of servers and applications.</li>
</ul>

</div>

<div id="storage">

<p><strong>Storage/Backup Admins Changelog</strong></p>

<ul>
  <li><a href="/chglogsba">Server Changelog Pages</a></li>
  <li><a href="/chglogsba/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogsba/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Backups)) {
?>
  <li><a href="<?php print $Listingroot; ?>/backups.php">Manage the Backup Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Backups; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="icl">

<p><strong>ICL Admins Changelog</strong></p>

<ul>
  <li><a href="/chgloglab">Server Changelog Pages</a></li>
  <li><a href="/chgloglab/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chgloglab/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_ICLAdmins)) {
?>
  <li><a href="<?php print $Listingroot; ?>/icladmins.php">Manage the ICL Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_ICLAdmins; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="windows">

<p><strong>Windows Admins Changelog</strong></p>

<ul>
  <li><a href="/windows">Server Changelog Pages</a></li>
  <li><a href="/windows/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/windows/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Windows)) {
?>
  <li><a href="<?php print $Listingroot; ?>/windows.php">Manage the Windows Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Windows; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="monitoring">

<p><strong>Monitoring Admins Changelog</strong></p>

<ul>
  <li><a href="/chglogmon">Server Changelog Pages</a></li>
  <li><a href="/chglogmon/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogmon/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Monitoring)) {
?>
  <li><a href="<?php print $Listingroot; ?>/monitoring.php">Manage the Monitoring Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Monitoring; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="mobility">

<p><strong>Mobility Admins Changelog</strong></p>

<ul>
  <li><a href="/chglogmob">Server Changelog Pages</a></li>
  <li><a href="/chglogmob/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogmob/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Mobility)) {
?>
  <li><a href="<?php print $Listingroot; ?>/mobility.php">Manage the Mobility Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Mobility; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="network">

<p><strong>Network Engineering Changelog</strong></p>

<ul>
  <li><a href="/chglognet">Server Changelog Pages</a></li>
  <li><a href="/chglognet/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglognet/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_Networking)) {
?>
  <li><a href="<?php print $Listingroot; ?>/networking.php">Manage Network Engineering Device Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_Networking; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>

<div id="engineering">

<p><strong>Systems Engineering Changelog</strong></p>

<ul>
  <li><a href="/chglogse">Server Changelog Pages</a></li>
  <li><a href="/chglogse/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogse/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_SysEng)) {
?>
  <li><a href="<?php print $Listingroot; ?>/systems.php">Manage Systems Engineering Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_SysEng; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>


<div id="vns">

<p><strong>Voice Network Support Changelog</strong></p>

<ul>
  <li><a href="/chglogvns">Server Changelog Pages</a></li>
  <li><a href="/chglogvns/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogvns/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_VoiceNetwork)) {
?>
  <li><a href="<?php print $Listingroot; ?>/vns.php">Manage Voice Network Support Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_VoiceNetwork; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>


<div id="alimsysadmin">

<p><strong>ALIM SysAdmin Changelog</strong></p>

<ul>
  <li><a href="/chglogali">Server Changelog Pages</a></li>
  <li><a href="/chglogali/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogali/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_ALIMAdmin)) {
?>
  <li><a href="<?php print $Listingroot; ?>/alimadmin.php">Manage ALIM SysAdmin Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_ALIMAdmin; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>


<div id="tss">

<p><strong>TSS SysAdmin Changelog</strong></p>

<ul>
  <li><a href="/chglogtss">Server Changelog Pages</a></li>
  <li><a href="/chglogtss/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogtss/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_TSS)) {
?>
  <li><a href="<?php print $Listingroot; ?>/tss.php">Manage TSS SysAdmin Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_TSS; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>


<div id="ienv">

<p><strong>IEN Voice SysAdmin Changelog</strong></p>

<ul>
  <li><a href="/chglogienv">Server Changelog Pages</a></li>
  <li><a href="/chglogienv/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogienv/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_IENV)) {
?>
  <li><a href="<?php print $Listingroot; ?>/ienv.php">Manage IEN Voice SysAdmin Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_IENV; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>


<div id="scm">

<p><strong>SCM Changelog</strong></p>

<ul>
  <li><a href="/chglogscm">Server Changelog Pages</a></li>
  <li><a href="/chglogscm/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/chglogscm/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_SCM)) {
?>
  <li><a href="<?php print $Listingroot; ?>/scm.php">Manage SCM Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_SCM; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>


<div id="i3">

<p><strong>i3 App Sys Admins Changelog</strong></p>

<ul>
  <li><a href="/i3chglog">Server Changelog Pages</a></li>
  <li><a href="/i3chglog/listing.php">Sorted Changelog Pages</a></li>
  <li><a href="/i3chglog/countoff.php">Weekly Changelog Count</a></li>
<?php
  if (check_grouplevel($db, $GRP_i3)) {
?>
  <li><a href="<?php print $Listingroot; ?>/i3admins.php">Manage SCM Server Listing</a>.</li>
<?php
  }
?>
  <li><a href="<?php print $Reportroot; ?>/changelog.php?group=<?php print $GRP_i3; ?>">Changelog Report</a> - View manual changelog entries and reports.</li>
</ul>

</div>



</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
