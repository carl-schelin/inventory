<?php
# Script: timelines.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "timelines.php";

  logaccess($db, $_SESSION['uid'], $package, "Graph how long it takes");

  if (isset($_POST['start'])) {
    $formVars['start'] = clean($_POST['start'], 15);
  } else {
    $formVars['start'] = "2014-01-01";
  }
  if (isset($_POST['end'])) {
    $formVars['end'] = clean($_POST['end'], 15);
  } else {
    $formVars['end'] = "2014-12-31";
  }

  $where = "where st_timestamp >= '" . $formVars['start'] . "' and st_timestamp <= '" . $formVars['end'] . "' ";

  if (isset($_POST['rsdp'])) {
    $formVars['rsdp'] = clean($_POST['rsdp'], 10);
  } else {
    $formVars['rsdp'] = 0;
  }

  if ($formVars['rsdp'] > 0) {
    $where .= "and st_rsdp = " . $formVars['rsdp'] . " ";
  }

  if (isset($_POST['type'])) {
    $formVars['type'] = clean($_POST['type'], 20);
  } else {
    $formVars['type'] = 0;
  }

  if (isset($_POST['group'])) {
    $formVars['group'] = clean($_POST['group'], 20);
  } else {
    $formVars['group'] = 0;
  }

  if ($formVars['group'] > 0) {
    $where .= "and usr_group = " . $formVars['group'] . " ";
  }

  $q_string  = "select count(st_id) ";
  $q_string .= "from rsdp_status ";
  $q_string .= "left join users on usr_id = st_user ";
  $q_string .= "left join a_groups on a_groups.grp_id = users.usr_group ";
  $q_string .= $where;
  $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

  $total = $a_rsdp_status['count(st_id)'];

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: Graph Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Server?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function close_line( p_script_url ) {
  var answer = confirm("Mark this Server as Complete?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>

function clear_fields() {
  show_file('timelines.seconds.php?start=<?php print $formVars['start']; ?>&end=<?php print $formVars['end']; ?>&group=<?php print $formVars['group']; ?>');
  show_file('timelines.hours.php?start=<?php print $formVars['start']; ?>&end=<?php print $formVars['end']; ?>&group=<?php print $formVars['group']; ?>');
  show_file('timelines.days.php?start=<?php print $formVars['start']; ?>&end=<?php print $formVars['end']; ?>&group=<?php print $formVars['group']; ?>');
  show_file('timelines.servers.php?start=<?php print $formVars['start']; ?>&end=<?php print $formVars['end']; ?>&group=<?php print $formVars['group']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'
  });

  $( "#startdate" ).datepicker();
  $( "#enddate" ).datepicker();

});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<div id="tabs">

<ul>
  <li><a href="#graph">RSDP Graph</a></li>
  <li><a href="#days">By Days Report</a></li>
  <li><a href="#hours">By Hours Report</a></li>
  <li><a href="#seconds">By Seconds Report</a></li>
  <li><a href="#server">Graphs By RSDP ID</a></li>
</ul>

<div id="graph">

<p></p>
<table class="ui-styled-table">
<tr>
<?php
  if ($formVars['type'] == 'days') {
    print "<th class=\"ui-state-default\">" . $total . " RSDP Servers - By Days: " . $formVars['start'] . " to " . $formVars['end'] . "</th>\n";
  } else {
    print "<th class=\"ui-state-default\">" . $total . " RSDP Servers - By Hours: " . $formVars['start'] . " to " . $formVars['end'] . "</th>\n";
  }
?>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('graph-listing-help');">Help</a></th>
</tr>
</table>

<div id="graph-listing-help" style="display: none">

<div class="main-help ui-widget-content">

<p>The graph below shows the number of hours or days (by default) it takes to complete a task.</p>

<ul>
  <li><strong>Beg</strong> - The start of the RSDP task. Should always be zero.</li>
  <li><strong>Pro</strong> - Provision the new system. Select operating system, hardware or virtual, define interfaces and SAN HBA cards.</li>
  <li><strong>San</strong> - If a physical system and SAN space is required, this step will need to be completed. Generally 0 as a majority of systems are Virtual or don't need SAN space.</li>
  <li><strong>Net</strong> - Networking step. Assign IP addresses and Switch configurations if physical.</li>
  <li><strong>V/D</strong> - Virtualization provisioning or Data Center rack and cabling.</li>
  <li><strong>DC</strong> - Data Center. Note: Eliminated in Inventory 3.0.</li>
  <li><strong>DC</strong> - Data Center. Note: Eliminated in Inventory 3.0.</li>
  <li><strong>SR</strong> - Shipping and Receiving. Note: Eliminated in Inventory 3.0.</li>
  <li><strong>DC</strong> - Data Center. Note: Eliminated in Inventory 3.0.</li>
  <li><strong>Sys</strong> - Operating System installation and configuration.</li>
  <li><strong>San</strong> - If physical SAN space is required, this step ensures the new space has been provisioned and identified.</li>
  <li><strong>Sys</strong> - Final steps in the system configuration including installing accounts, agents, and other standard configurations.</li>
  <li><strong>Bck</strong> - Backups. Working with the Systems folks to ensure Backups are functioning if needed.</li>
  <li><strong>Mon</strong> - Monitoring. Working with the Systems folks to ensure basic system monitoring is functioning.</li>
  <li><strong>App</strong> - Application installation. Mostly non-Systems teams. Mobility, Web Applications, and other teams.</li>
  <li><strong>Mon</strong> - Application Monitoring. Working with Applications to ensure monitoring of the application is functioning.</li>
  <li><strong>App</strong> - Application Configuration. Final configuration and testing.</li>
  <li><strong>Scn</strong> - InfoSec scans. Submit request and resolve issues if anuy. Final step before going live.</li>
</ul>

<p><strong>Note:</strong> Default date range for this report is 2014-01-01 through 2014-12-31. First entry in the system is 2012-05-17. RSDP went live 2012-12-01.</p>

<p><strong>Note:</strong> There are a scattering of test servers with invalid information and a small percentage of servers were forced complete, overcome by events (server already up and running and RSDP skipped).</p>

</div>

</div>

<form action="timelines.php" method="post">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="submit" value="Generate Graph"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Start Date <input type="text" name="start" id="startdate" value="<?php print $formVars['start']; ?>"></td>
  <td class="ui-widget-content">End Date <input type="text" name="end" id="enddate" value="<?php print $formVars['end']; ?>"></td>
  <td class="ui-widget-content">RSDP ID <input type="text" name="rsdp" value="<?php print $formVars['rsdp']; ?>"></td>
  <td class="ui-widget-content">Requestor Group <select name="group">
<option value="0">All Groups</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    if ($formVars['group'] == $a_groups['grp_id']) {
      print "<option selected value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
</table>

</form>

<p></p>

<img src="timelines.graph.php?start=<?php print $formVars['start'] . "&end=" . $formVars['end'] . "&rsdp=" . $formVars['rsdp'] . "&type=" . $formVars['type'] . "&group=" . $formVars['group']; ?>">

</div>


<div id="days">

<span id="days_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="hours">

<span id="hours_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="seconds">

<span id="seconds_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="server">

<span id="server_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
