<?php
# Script: device.members.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "device.members.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['model'] = 0;
  if (isset($_GET['model'])) {
    $formVars['model'] = clean($_GET['model'], 10);
  }

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
<title>Device Member Listing</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>


<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Device Member Listing</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('detail-help');">Help</a></th>
</tr>
</table>

<div id="detail-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Device Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('device-listing-help');">Help</a></th>
</tr>
</table>

<div id="device-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>CPU Listing</strong></p>

<p>This page lists all the defined CPUs that can be used to create an asset.</p>

<p>To add a CPU, click the <strong>Add CPU</strong> button. This will bring up a dialog box which you can use 
to create a new CPU.</p>

<p>To edit an existing CPU, click on an entry in the listing. A dialog box will be presented where you can edit 
the current entry, or if there is a small difference, you can make changes and add a new CPU.</p>

<p>Note that under the Members colum is a number which indicates the number of times this CPU is in use. You cannot 
delete a CPU as long as this value is greater than zero.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Device Name or Label</th>
  <th class="ui-state-default">Vendor</th>
  <th class="ui-state-default">Model</th>
</tr>
<?php

  $class = "ui-widget-content";
  $q_string  = "select ast_id,ast_name,mod_name,ven_name ";
  $q_string .= "from inv_assets ";
  $q_string .= "left join inv_models   on inv_models.mod_id  = inv_assets.ast_modelid ";
  $q_string .= "left join inv_vendors  on inv_vendors.ven_id = inv_models.mod_vendor ";
  $q_string .= "where ast_modelid = " . $formVars['model'] . " ";
  $q_string .= "group by ast_name ";
  $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_assets) > 0) {
    while ($a_inv_assets = mysqli_fetch_array($q_inv_assets)) {

      print "<tr>\n";
      print "<td class=\"" . $class . "\">" . $a_inv_assets['ast_name'] . "</td>\n";
      print "<td class=\"" . $class . "\">" . $a_inv_assets['ven_name'] . "</td>\n";
      print "<td class=\"" . $class . "\">" . $a_inv_assets['mod_name'] . "</td>\n";
      print "</tr>\n";

    }
  }

?>
</table>

</div>


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
