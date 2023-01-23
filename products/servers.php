<?php
# Script: servers.php
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

  $package = "servers.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['id'] = 0;
  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  }

# if help has not been seen yet,
  if (show_Help($db, $Productpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Server Listing</title>

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

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('server-help');">Help</a></th>
</tr>
</table>

<div id="server-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update License</strong> - Save any changes to this form.</li>
    <li><strong>Add License</strong> - Add a new License.</li>
  </ul></li>
</ul>

</div>

</div>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server</th>
  <th class="ui-state-default">Function</th>
  <th class="ui-state-default">Product</th>
  <th class="ui-state-default">Department</th>
</tr>
<?php

  $q_string  = "select inv_id,inv_name,inv_function,prod_name,dep_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_products     on inv_products.prod_id           = inv_inventory.inv_product ";
  $q_string .= "left join inv_department   on inv_department.dep_id          = inv_inventory.inv_department ";
  $q_string .= "where inv_product = " . $formVars['id'] . " ";
  $q_string .= "order by inv_name ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_inventory) > 0) {
    while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

      $linkstart  = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inv_inventory['inv_id'] . "\" target=\"_blank\">";
      $linkstart .= "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/pencil.gif\" height=\"10\">";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inv_inventory['inv_name']    . $linkend . "</td>";
      print "  <td class=\"ui-widget-content\">"              . $a_inv_inventory['inv_function']           . "</td>";
      print "  <td class=\"ui-widget-content\">"              . $a_inv_inventory['prod_name']              . "</td>";
      print "  <td class=\"ui-widget-content\">"              . $a_inv_inventory['dep_name']               . "</td>";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"4\">No servers are associated with this product.</td>";
    print "</tr>\n";
  }
?>
</table>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
