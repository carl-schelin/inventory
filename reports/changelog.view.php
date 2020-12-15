<?php
# Script: changelog.view.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "changelog.view.php";

  logaccess($db, $formVars['uid'], $package, "Changelog Report.");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }

# if help has not been seen yet,
  if (show_Help($db, $Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Searching Hardware</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

function clear_fields() {
  show_file('changelog.mysql.php?id=<?php print $formVars['id']; ?>');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Changelog Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('changelog-listing-help');">Help</a></th>
</tr>
</table>

<div id="changelog-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Shows the first line of every changelog entry for this server or application.</p>

</div>

</div>

<span id="changelog_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
