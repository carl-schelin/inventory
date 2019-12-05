<?php
# Script: index.php
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

  $package = "index.php";

  logaccess($_SESSION['uid'], $package, "Viewing RSDP index");

  if (isset($_GET['myrsdp'])) {
    $formVars['myrsdp'] = clean($_GET['myrsdp'], 10);
  } else {
    $formVars['myrsdp'] = 'yes';
  }
  if ($formVars['myrsdp'] == '') {
    $formVars['myrsdp'] = 'no';
  }

  $formVars['rsdp'] = 0;

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: Projects</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php
  if (check_userlevel($AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("This deletes every task and server associated with this project.\nMake sure you review the list of servers to ensure this is the correct action.\nYou can always delete individual servers vs the entire Project.\n\nAre you sure you want to delete this Project?")

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
  show_file('index.mysql.php?update=-1&myrsdp=<?php print $formVars['myrsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form action="build/initial.php" method="POST">

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="display:none">

<div class="main-help ui-widget-content">

<h2>Instructions</h2>

<p>The <strong>Rapid Server Deployment Process</strong> (RSDP) was designed to help with 90% of the builds in operations. The various groups were gathered and a list of 
tasks for provisioning common server deployments performed by each group was compiled. The groups were consulted many times to refine this list which was transformed into 
the RSDP module of the Inventory.</p>

<p>This page provides, by default, a listing of all the Projects where you are the resource tasked with completing a part of the entire server build process. As each task 
is completed, a Magic ticket is generated and an email sent providing a quick description and a link to the task.</p>

<p>To request a new server, click the <strong>Request New Server</strong> button and fill out the forms.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content" style="text-align: right"><input type="submit" name="clone" id="button" value="Request New Server"></td>
</tr>
<tr>
  <td class="ui-widget-content" style="text-align: right"><input type="submit" name="goto" id="button" value="Goto RSDP"><input type="text" name="rsdp" value="<?php print $formVars['rsdp']; ?>" size="10"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
