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

<p><strong>Certificate Listing</strong></p>

<p>This page is listing all servers that have selected a certificate. The intention is to let you easily locate the 
server so you know which ones are impacted should a certificate be expiring. Then you can update the certificate on 
all affected servers.</p>

<p>The listing displays the vendor and software that has the certificate, the type of software, which product is 
impacted, and which group needs to be contacted to get the certificate updated.</p>

</div>

</div>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server</th>
  <th class="ui-state-default">Software</th>
  <th class="ui-state-default">Type</th>
  <th class="ui-state-default">Product</th>
  <th class="ui-state-default">Department</th>
</tr>
<?php

  $q_string  = "select inv_id,inv_name,sw_software,ven_name,prod_name,typ_name,dep_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join svr_software on svr_software.svr_companyid = inventory.inv_id ";
  $q_string .= "left join software     on software.sw_id             = svr_software.svr_softwareid ";
  $q_string .= "left join vendors      on vendors.ven_id             = software.sw_vendor ";
  $q_string .= "left join products     on products.prod_id           = software.sw_product ";
  $q_string .= "left join inv_sw_types     on inv_sw_types.typ_id            = software.sw_type ";
  $q_string .= "left join department   on department.dep_id          = software.sw_department ";
  $q_string .= "where svr_certid = " . $formVars['id'] . " ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {

      $linkstart  = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
      $linkstart .= "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/pencil.gif\" height=\"10\">";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name']    . $linkend . "</td>";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['ven_name'] . " " . $a_inventory['sw_software']            . "</td>";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['typ_name']               . "</td>";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['prod_name']              . "</td>";
      print "  <td class=\"ui-widget-content\">"              . $a_inventory['dep_name']               . "</td>";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"5\">No servers are associated with this certificate.</td>";
    print "</tr>\n";
  }
?>
</table>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
