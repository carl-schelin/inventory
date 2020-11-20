<?php
# Script: llilly.oracleas.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "llilly.oracleas.php";

  logaccess($formVars['uid'], $package, "Report on Oracle Unbreakable Linux.");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Lynda Lilly: Oracle AS Linux</title>

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

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">System Name</th>
  <th class="ui-state-default">Operating System</th>
</tr>
<?php

  $count = 1;
  $q_string  = "select sw_id,sw_companyid,sw_software,inv_name ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
  $q_string .= "where sw_software like 'Oracle Enterprise Linux Enterprise Linux%' and sw_type = 'OS' and inv_status = 0 ";
  $q_software = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ( $a_software = mysqli_fetch_array($q_software) ) {

    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_software['sw_companyid'] . "\">";
    $linkend   = "</a>";

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $linkstart . $a_software['inv_name']    . $linkend . "</td>\n";
    print "  <td class=\"ui-widget-content\">"              . $a_software['sw_software']            . "</td>\n";
    print "</tr>\n";

    $count++;
  }
  print "<tr>\n";
  print "  <td class=\"ui-widget-content\" colspan=\"2\">Total Systems: " . $count . "</td>";
  print "</tr>\n";
?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
