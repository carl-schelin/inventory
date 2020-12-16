<?php
# Script: errors.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "errors.php";

  logaccess($db, $formVars['uid'], $package, "Managing Errors.");

# get the totals for each problem to properly display tabs
  $q_string  = "select count(ce_id) ";
  $q_string .= "from chkerrors ";
  $q_string .= "where ce_priority = 1 and ce_delete = 0 ";
  $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkerrors = mysqli_fetch_array($q_chkerrors);
  $priority1 = $a_chkerrors['count(ce_id)'];

  $q_string  = "select count(ce_id) ";
  $q_string .= "from chkerrors ";
  $q_string .= "where ce_priority = 2 and ce_delete = 0 ";
  $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkerrors = mysqli_fetch_array($q_chkerrors);
  $priority2 = $a_chkerrors['count(ce_id)'];

  $q_string  = "select count(ce_id) ";
  $q_string .= "from chkerrors ";
  $q_string .= "where ce_priority = 3 and ce_delete = 0 ";
  $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkerrors = mysqli_fetch_array($q_chkerrors);
  $priority3 = $a_chkerrors['count(ce_id)'];

  $q_string  = "select count(ce_id) ";
  $q_string .= "from chkerrors ";
  $q_string .= "where ce_priority = 4 and ce_delete = 0 ";
  $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkerrors = mysqli_fetch_array($q_chkerrors);
  $priority4 = $a_chkerrors['count(ce_id)'];

  $q_string  = "select count(ce_id) ";
  $q_string .= "from chkerrors ";
  $q_string .= "where ce_priority = 5 and ce_delete = 0 ";
  $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkerrors = mysqli_fetch_array($q_chkerrors);
  $priority5 = $a_chkerrors['count(ce_id)'];

# if help has not been seen yet,
  if (show_Help($db, 'mainerrormanagement')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Managing Errors</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function clear_fields() {
  show_file('errors.mysql.php');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="errors">


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Error Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


<h2>Instructions</h2>

<p>This page contains a unique list of errors as discovered as part of the chkserver import. By default, new lines identified as an error 
are set to Priority 2 and warnings are set to Priority 5.</p>

<p>To change the overall priority of an error, which changes the listing on the Server Errors page, click the appripriate priority on the 
radio buttons.</p>

</div>

</div>

<div id="tabs">

<ul>
  <li><a href="#priority_1">Priority 1 (<span id="priority1"><?php print $priority1; ?></span>)</a></li>
  <li><a href="#priority_2">Priority 2 (<span id="priority2"><?php print $priority2; ?></span>)</a></li>
  <li><a href="#priority_3">Priority 3 (<span id="priority3"><?php print $priority3; ?></span>)</a></li>
  <li><a href="#priority_4">Priority 4 (<span id="priority4"><?php print $priority4; ?></span>)</a></li>
  <li><a href="#priority_5">Priority 5 (<span id="priority5"><?php print $priority5; ?></span>)</a></li>
</ul>

<div id="priority_1">

<span id="pri1_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


<div id="priority_2">

<span id="pri2_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


<div id="priority_3">

<span id="pri3_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


<div id="priority_4">

<span id="pri4_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


<div id="priority_5">

<span id="pri5_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


</div>

</form>


</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
