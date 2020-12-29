<?php
# Script: wparker.mysql.csv.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "wparker.mysql.csv.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>MySQL Software Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  $q_string  = "select sw_id,sw_software,sw_vendor,sw_product,sw_type,sw_verified,sw_update,inv_name,grp_name,prod_name ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join a_groups    on a_groups.grp_id         = software.sw_group ";
  $q_string .= "left join products  on products.prod_id      = software.sw_product ";
  $q_string .= "where inv_status = 0 and sw_software like '%mysql%' ";
  $q_string .= $orderby;
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_software = mysqli_fetch_array($q_software)) {

    print "<br>\"" . $a_software['inv_name'] . "\",";
    print "\"" . $a_software['prod_name'] . "\",";
    print "\"" . $a_software['sw_vendor'] . "\",";
    print "\"" . $a_software['sw_software'] . "\",";
    print "\"" . $a_software['sw_type'] . "\",";
    print "\"" . $a_software['grp_name'] . "\",";
    print "\"" . $a_software['sw_update'] . "\"\n";

  }

  mysqli_free_result($q_software);

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
