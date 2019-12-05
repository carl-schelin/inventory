<?php
# Script: index.rsdp.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "index.rsdp.php";

  logaccess($formVars['uid'], $package, "Accessing script");

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
<title>Server Management System</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

function attach_group( p_script_url ) {
  var ag_form = document.rsdp;
  var ag_url;

  ag_url  = "?myrsdp="   + "yes";
  ag_url += "&group="    + ag_form.group.value;

  script = document.createElement('script');
  script.src = p_script_url + ag_url;
  window.location.href=script.src;
}

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<div class="main ui-widget-content">

<form name="rsdp">

<h4>Rapid Server Deployment Process (RSDP)</h4>

<ul>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $RSDProot; ?>/index.php');">My RSDP</a> - Shows a filtered list of servers; if you're the requestor, or the platform owner, or if an upcoming task is yours or your team's.
<select name="group">
<?php
  $q_string  = "select grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $formVars['group'];
  $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  $a_groups = mysql_fetch_array($q_groups);

  print "<option value=\"" . $formVars['group'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";

  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 and grp_id != " . $formVars['group'] . " ";
  $q_string .= "order by grp_name";
  $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_groups = mysql_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></li>
  <li><a href="<?php print $RSDProot; ?>/index.php?myrsdp=no">Rapid Server Deployment Process</a> - This is the main RSDP page showing the current status of all active server builds.</li>
  <li><a href="<?php print $RSDProot; ?>/reports/completed.php">Completed RSDP Servers</a> - This is the listing of all the completed server builds</li>
  <li><a href="<?php print $RSDProot; ?>/reports/timelines.php">RSDP Timeline of Tasks</a> - Graphically shows how long it takes to complete tasks.</li>
  <li><a href="<?php print $RSDProot; ?>/reports/iplisting.php">RSDP IP Listing</a> - List of all the systems in RSDP by IP. Makes it easier to locate systems by scan IP.</li> 
  <li><a href="<?php print $RSDProot; ?>/admin/checklist.php">Checklist Manager</a> - Customized Checklists are available. This script manages those items.</li>
  <li><a href="<?php print $RSDProot; ?>/admin/project.php">Manage Project Codes table</a></li>
  <li><a href="<?php print $RSDProot; ?>/admin/system.php">Manage the Operating Systems list</a> - On the Server Designed task is a drop down with a list of Operating Systems. This manages that table.</li>
</ul>

<ul>
  <li><a href="<?php print $RSDProot; ?>/rsdp/rsdp.php">Original MyRSDP Page</a> - This is the original listing of your RSDP servers that showed each server and the associated tasks.</li>
  <li><a href="<?php print $RSDProot; ?>/rsdp/rsdp.php?myrsdp=no">Original RSDP Page</a> - This is the original listing of all RSDP servers that showed each server and the associated tasks.</li>
<?php
  if (check_userlevel($AL_Admin)) {
?>
  <li><a href="<?php print $RSDProot; ?>/admin/status.php">Manage RSDP Status Entries</a> - Delete individual line items or an entire project from this script.</li>
<?php
  }
?>
</ul>

</div>

</form>

<div class="main ui-widget-content">

<h4>Reports</h4>

<p>Lists the servers waiting to be completed for the specific task. A user will be named if individually assigned or the group name.</p>

<ul>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=2">Platforms</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=3">Storage</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=4">Network Engineering</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=5">Virtualization</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=6">Data Center</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=10">Operating System</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=11">SAN Provisioning</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=12">OS Configuration</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=13">Backup Configuration</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=14">System Monitoring</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=15">Application Installation</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=16">Application Monitoring</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=17">Application Configuration</a></li>
  <li><a href="<?php print $RSDProot;  ?>/reports/report.php?id=18">Security Scan</a></li>
</ul>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
