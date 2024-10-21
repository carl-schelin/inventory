<?php
# Script: cpu.members.php
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

  $package = "cpu.members.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['type'] = 0;
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  }
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
<title>CPU Member Listing</title>

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
  <th class="ui-state-default">CPU Member Listing</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('detail-help');">Help</a></th>
</tr>
</table>

<div id="detail-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">CPU Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('cpu-listing-help');">Help</a></th>
</tr>
</table>

<div id="cpu-listing-help" style="<?php print $display; ?>">

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
  <th class="ui-state-default">Server Name</th>
  <th class="ui-state-default">Description</th>
  <th class="ui-state-default">CPU</th>
  <th class="ui-state-default">Number of CPUs</th>
</tr>
<?php

  $class = "ui-widget-content";
  $q_string  = "select inv_id,inv_name,inv_function,mod_id,mod_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_hardware on inv_hardware.hw_companyid = inv_inventory.inv_id ";
  $q_string .= "left join inv_models   on inv_models.mod_id = inv_hardware.hw_vendorid ";
  $q_string .= "where hw_vendorid = " . $formVars['model'] . " and hw_type = " . $formVars['type'] . " and inv_status = 0 ";
  $q_string .= "group by inv_name ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_inventory) > 0) {
    while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
      print "<tr>\n";
      print "<td class=\"" . $class . "\">" . $a_inv_inventory['inv_name'] . "</td>\n";
      print "<td class=\"" . $class . "\">" . $a_inv_inventory['inv_function'] . "</td>\n";
      print "<td class=\"" . $class . "\">" . $a_inv_inventory['mod_name'] . "</td>\n";

      $total = 0;
      $q_string  = "select mod_size ";
      $q_string .= "from inv_models ";
      $q_string .= "where mod_id = " . $a_inv_inventory['mod_id'] . " ";
      $q_inv_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_models) > 0) {
        while ($a_inv_models = mysqli_fetch_array($q_inv_models)) {
          $size = preg_split('/\s+/', $a_inv_models['mod_size']);
          $number = $size[0];
        }
      }

      $count = 0;
      $q_string  = "select count(hw_id) ";
      $q_string .= "from inv_hardware ";
      $q_string .= "where hw_companyid = " . $a_inv_inventory['inv_id'] . " and hw_vendorid = " . $formVars['model'] . " and hw_type = " . $formVars['type'] . " ";
      $q_inv_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_hardware) > 0) {
        $a_inv_hardware = mysqli_fetch_array($q_inv_hardware);
        $count += $a_inv_hardware['count(hw_id)'];
      }

      $total = $number * $count;

      print "<td class=\"" . $class . " delete\">" . $total . "</td>\n";
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
