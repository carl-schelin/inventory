<?php
# Script: tasks.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('4');

  $package = "tasks.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing RSDP Listing");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: Tasks</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function clear_task( p_script_url ) {
  var answer = confirm("Reset this task to Incomplete?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function clear_fields() {
  show_file('tasks.mysql.php?update=-1&id=<?php print $formVars['id']; ?>');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form action="build/initial.php" method="POST">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Task Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="display:none">

<div class="main-help ui-widget-content">

<h2>Instructions</h2>

<p>The <strong>Rapid Server Deployment Process</strong> (RSDP) was designed to help with 90% of the builds in operations. The various groups were gathered and a list of
tasks for provisioning common server deployments performed by each group was compiled. The groups were consulted many times to refine this list which was transformed into
the RSDP module of the Inventory.</p>

<p>This page provides, by default, a listing of all the Tasks that have been worked, are skipped, or are waiting on work to be completed. As each task
is completed, a Magic ticket is generated and an email sent providing a quick description and a link to the task.</p>

<p>To request a new server, click the <strong>Request New Server</strong> button and fill out the forms.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content" style="text-align: right"><input type="submit" name="clone" id="button" value="Request New Server"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
