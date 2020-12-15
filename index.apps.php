<?php
# Script: index.apps.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.apps.php";

  logaccess($db, $db, $formVars['uid'], $package, "Checking out the index.");

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

<div id="main">

<div id="tabs">

<ul>
  <li><a href="#issues">Issue Tracker</a></li>
  <li><a href="#morning">Morning Report</a></li>
  <li><a href="#locations">Location Manager</a></li>
  <li><a href="#certificates">Certificate Manager</a></li>
  <li><a href="#images">Image Manager</a></li>
<?php
  if (check_grouplevel($db, $GRP_Monitoring)) {
?>
  <li><a href="#monitor">Manage Monitoring</a></li>
<?php
  }
?>
<?php
  if (check_grouplevel($db, $GRP_Unix)) {
?>
  <li><a href="#psaps">PSAP Manager</a></li>
<?php
  }
?>
</ul>


<div id="issues">

<p>Issue Tracker<p>

<ul>
  <li><a href="<?php print $Issueroot; ?>/issue.php">View current Issues</a> - View a server to open a new issue.</li>
  <li><a href="<?php print $Issueroot; ?>/search.php">Search Issue tracker</a> - Simple search on the issue detail.</li>
</ul>

</div>


<div id="morning">

<p>Morning Report</p>

<ul>
  <li><a href="<?php print $Morningroot; ?>/morning.report.php">Morning Report</a></li>
  <li><a href="<?php print $Morningroot; ?>/calendar.php#<?php print date('F'); ?>">Morning Report Calendar</a></li>
  <li><a href="<?php print $Morningroot; ?>/users.php">View Users of the Morning Reports</a></li>
</ul>

</div>


<div id="locations">

<p>Locations</p>

<ul>
  <li><a href="<?php print $DCroot; ?>/company.php">Company Manager</a></li>
  <li><a href="<?php print $DCroot; ?>/datacenter.php">Site Manager</a></li>
  <li><a href="<?php print $DCroot; ?>/contacts.php">Contact Manager</a></li>
  <li><a href="<?php print $DCroot; ?>/city.php">Manage Cities</a></li>
  <li><a href="<?php print $DCroot; ?>/state.php">Manage States</a></li>
  <li><a href="<?php print $DCroot; ?>/country.php">Manage Countries</a></li>
</ul>

</div>


<div id="certificates">

<p>Web Site Certificate Management</p>

<ul>
  <li><a href="<?php print $Certsroot; ?>/certs.php">Manage Web Site Certificates</a></li>
  <li><a href="<?php print $Certsroot; ?>/webapps.certs.php">View Certificates</a></li>
</ul>

</div>


<div id="images">

<p>Inventory Image Manager<p>

<ul>
  <li><a href="<?php print $Adminroot; ?>/image.php">Manage Inventory Images</a></li>
</ul>

</div>


<?php
  if (check_grouplevel($db, $GRP_Monitoring)) {
?>
<div id="monitor">

<p>Manage Monitoring<p>

<ul>
  <li><a href="<?php print $Monitorroot; ?>/rules.php">Manage Monitoring Rules</a></li>
  <li><a href="<?php print $Monitorroot; ?>/keywords.php">Manage Page Group Definitions</a></li>
  <li><a href="<?php print $Monitorroot; ?>/application.php">Manage Applications</a></li>
  <li><a href="<?php print $Monitorroot; ?>/source_node.php">Manage Nodes</a></li>
  <li><a href="<?php print $Monitorroot; ?>/objects.php">Manage Objects</a></li>
  <li><a href="<?php print $Monitorroot; ?>/message_group.php">Manage Message Groups</a></li>
</ul>

</div>
<?php
  }
?>


<?php
  if (check_grouplevel($db, $GRP_Unix)) {
?>
<div id="psaps">

<p>PSAP Manager<p>

<ul>
  <li><a href="<?php print $PSAProot; ?>/psaps.php">Manage PSAPs</a></li>
</ul>

</div>
<?php
  }
?>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
