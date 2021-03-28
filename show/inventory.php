<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "inventory.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing the script.");

  if (isset($_GET['server'])) {
    $formVars['id'] = clean($_GET['server'], 10);
  }
  if (isset($_GET['servername'])) {
    $formVars['servername'] = clean($_GET['servername'], 20);
  }

  if (isset($formVars['servername'])) {
    $formVars['id'] = return_ServerID($db, $formVars['servername']);
  }

  if (!isset($formVars['id'])) {
    $formVars['id'] = 1109;
  }

  $q_string  = "select inv_id,inv_name,inv_function,inv_document,inv_class,inv_callpath,inv_manager,inv_appadmin,inv_product ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_id = " . $formVars['id'];
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  if (isset($_GET['start'])) {
    $formVars['start'] = clean($_GET['start'], 15);
  } else {
    $formVars['start'] = '2009-01-01';
  }
  if (isset($_GET['end'])) {
    $formVars['end'] = clean($_GET['end'], 15);
  } else {
    $formVars['end'] = date('Y-m-d');
  }

  logaccess($db, $_SESSION['uid'], $package, "Viewing server: " . $a_inventory['inv_name'] . " (" . $formVars['id'] . ").");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php print $a_inventory['inv_name'];?> Detail Record</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function attach_logs( p_script_url ) {
  var al_form = document.logs;
  var al_url;

  al_url  = "?id="            + <?php print $formVars['id']; ?>;
  al_url += "&startdate="     + encode_URI(al_form.startdate.value);
  al_url += "&enddate="       + encode_URI(al_form.enddate.value);

  script = document.createElement('script');
  script.src = p_script_url + al_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('detail.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('tags.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('hardware.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('support.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('filesystem.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('software.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('network.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('routing.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('backup.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('comments.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('logs.mysql.php?id=<?php print $formVars['id']; ?>');
  show_file('<?php print $Issueroot; ?>/issue.open.mysql.php?server=<?php print $formVars['id']; ?>');
  show_file('<?php print $Issueroot; ?>/issue.closed.mysql.php?server=<?php print $formVars['id']; ?>');
  show_file('errors.mysql.php?id=<?php print $formVars['id']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#vuln-tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#issue-tabs" ).tabs( ).addClass( "tab-shadow" );

  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'
  });

  $( "#startpick" ).datepicker();
  $( "#endpick" ).datepicker();

});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<div id="tabs">

<ul>
  <li><a href="#detail"><?php print $a_inventory['inv_name']; ?> Detail</a></li>
  <li><a href="#tags">Tags</a></li>
  <li><a href="#hardware">Hardware</a></li>
  <li><a href="#software">Software</a></li>
  <li><a href="#network">Network</a></li>
  <li><a href="#comments">Comments</a></li>
  <li><a href="#backup">Backup</a></li>
  <li><a href="#logs">Logs</a></li>
  <li><a href="#issues">Issue Tracker</a></li>
  <li><a href="#chkserver">Errors</a></li>
</ul>


<div id="detail">

<span id="detail_mysql"><?php print wait_Process('Details Waiting...')?></span>

</div>


<div id="tags">

<span id="tags_mysql"><?php print wait_Process('Tags Waiting...')?></span>

</div>


<div id="hardware">

<span id="hardware_mysql"><?php print wait_Process('Hardware Waiting...')?></span>

<span id="hardware_support_mysql"><?php print wait_Process('Support Waiting...')?></span>

<span id="filesystem_mysql"><?php print wait_Process('Filesystem Waiting...')?></span>

</div>


<div id="software">

<span id="software_mysql"><?php print wait_Process('Software Waiting...')?></span>

<span id="software_support_mysql"><?php print wait_Process('Support Waiting...')?></span>

<span id="package_mysql"><?php print wait_Process('Packages Waiting...')?></span>

</div>


<div id="network">

<span id="network_mysql"><?php print wait_Process('Network Waiting...')?></span>

<span id="ipv6network_mysql"><?php print wait_Process('IPv6 Network Waiting...')?></span>

<span id="routing_mysql"><?php print wait_Process('Routing Waiting...')?></span>

<span id="ipv6routing_mysql"><?php print wait_Process('IPv6 Routing Waiting...')?></span>

</div>


<div id="comments">

<span id="comments_mysql"><?php print wait_Process('Comments Waiting...')?></span>

</div>


<div id="backup">

<span id="backup_mysql"><?php print wait_Process('Backup Waiting...')?></span>

<span id="backuplog_mysql"><?php print wait_Process('Backup Log Waiting...')?></span>

</div>


<div id="logs">

<span id="logs_mysql"><?php print wait_Process('Logs Waiting...')?></span>

</div>


<div id="issues">

<div id="issue-tabs">

<ul>
  <li><a href="#open">Open Issues</a></li>
  <li><a href="#closed">Closed Issues</a></li>
</ul>


<div id="open">

<span id="open_mysql"><?php print wait_Process('Open Issues Waiting...')?></span>

</div>


<div id="closed">

<span id="closed_mysql"><?php print wait_Process('Closed Issues Waiting...')?></span>

</div>


</div>

</div>


<div id="chkserver">

<span id="chkserver_mysql"><?php print wait_Process('Errors Waiting...')?></span>

</div>



</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
