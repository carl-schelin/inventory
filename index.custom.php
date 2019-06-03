<?php
# Script: index.custom.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.custom.php";

  logaccess($formVars['uid'], $package, "Checking out the index.");

# if help has not been seen yet,
  if (show_Help($Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Special Requests</title>

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

<h2>Carl Schelin</h2>

<ul>
  <li><a href="requests/license.php">View the License information for your group.</a></li>
  <li><a href="requests/cschelin.interfaces.php">Show All Unassigned Network Interfaces</a></li>
  <li><a href="requests/cschelin.cpus.php">Show All CPU info for cleanup</a></li>
  <li><a href="requests/cschelin.disks.php">Show All Disk info for cleanup</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Jeff Sherard</h2>

<ul>
  <li><a href="requests/jsherard.listing.php">Spreadsheet of Unix servers</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Karen Klovdahl</h2>

<ul>
  <li><a href="requests/kklovdahl.inventory.php?group=8">List of Systems where the software is managed by the DBA Group</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Kevin Zupan</h2>

<ul>
  <li><a href="requests/kzupan.wireline.php">Listing of Wireline Servers</a></li>
  <li><a href="requests/kzupan.openview.php">Listing of Openview Servers by Location and Operating System</a></li>
  <li><a href="requests/kzupan.openview.php?csv=true">CSV Listing of Openview Servers by Location and Operating System</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Lynda Lilly</h2>

<ul>
  <li><a href="requests/llilly.sunmiami.php">List of Sun Equipment in Miami</a></li>
  <li><a href="requests/llilly.oracleas.php">List of Oracle Unbreakable Linux Systems</a></li>
  <li><a href="requests/llilly.dell.php">List of all Dell Systems</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Ken Hobbs</h2>

<ul>
  <li><a href="requests/khobbs.service.php">Service level of all active servers</a></li>
  <li><a href="requests/khobbs.capex.php">Team 2012 CapEx Review</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Daniel Roccaforte</h2>

<ul>
  <li><a href="requests/droccaforte.vmlist.php">List of all Virtual Systems with application listing</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Janet Krier</h2>

<ul>
  <li><a href="requests/jkrier.timezone.php">Server listing based on a specific product listing</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Jeff Armstrong</h2>

<ul>
  <li><a href="requests/jarmstrong.listing.php">West Audit server listing</a></li>
  <li><a href="requests/jarmstrong.listing.php?clean=1">West Audit server csv listing</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Terry Barrett</h2>

<ul>
  <li><a href="requests/tbarrett.btu.php">BTU Listing</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Pete Schmidt</h2>

<ul>
  <li><a href="requests/pschmidt.lab.php">Lab Application Listing</a> External Unix Systems</li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>William Parker</h2>

<ul>
  <li><a href="requests/wparker.mysql.php">MySQL Listing</a></li>
</ul>

</div>


<div class="main ui-widget-content">

<h2>Ashley Seifert</h2>

<ul>
  <li><a href="requests/aseifert.webapps.php">Web Application Servers with Apache</a></li>
  <li><a href="requests/aseifert.webapps.php?csv">Web Application Servers with Apache</a> - Comma Delimited output</li>
</ul>

</div>


</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
