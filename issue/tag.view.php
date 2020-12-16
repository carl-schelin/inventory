<?php
# Script: tag.view.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_ReadOnly);

  $package = "tag.view.php";

  logaccess($db, $_SESSION['uid'], $package, "Issue: tag view.");

  $formVars['tag']  = clean($_GET['tag'], 20);
  $formVars['type'] = clean($_GET['type'], 10);
  if ($formVars['type'] == '') {
    $formVars['type'] = 0;
  }

  if ($formVars['type'] == 0) {
    $title = "Private Tag View: " . $formVars['tag'];
  }
  if ($formVars['type'] == 1) {
    $title = "Group Tag View: " . $formVars['tag'];
  }
  if ($formVars['type'] == 2) {
    $title = "Public Tag View: " . $formVars['tag'];
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php print $title; ?></title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function clear_fields() {
  show_file('issue.open.mysql.php<?php print   "?tag=" . $formVars['tag'] . "&type=" . $formVars['type']; ?>');
  show_file('issue.closed.mysql.php<?php print "?tag=" . $formVars['tag'] . "&type=" . $formVars['type']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form id="inventory">

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><?php print $title; ?></th>
</tr>
</table>

<div id="tabs">

<ul>
  <li><a href="#open">Open Issues</a></li>
  <li><a href="#closed">Closed Issues</a></li>
</ul>

<div id="open">

<span id="open_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="closed">

<span id="closed_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>

</div>

</form>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
