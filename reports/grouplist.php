<?php
# Script: grouplist.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "grouplist.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

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
<title>User Group Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">User Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>List of all active users and the primary group and secondary groups they belong to. The Grouplist column should contain the primary group as well.</p>\n";

  print "</div>\n";

  print "</div>\n";

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">First</th>\n";
  print "  <th class=\"ui-state-default\">Last</th>\n";
  print "  <th class=\"ui-state-default\">Group</th>\n";
  print "  <th class=\"ui-state-default\">Grouplist</th>\n";
  print "</tr>";

  $q_string  = "select usr_id,usr_first,usr_last,grp_name ";
  $q_string .= "from inv_users ";
  $q_string .= "left join inv_groups on inv_groups.grp_id = inv_users.usr_group ";
  $q_string .= "left join inv_titles on inv_titles.tit_id = inv_users.usr_title ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inv_users['usr_first'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inv_users['usr_last']  . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_inv_users['grp_name']  . "</td>\n";
    print "  <td class=\"ui-widget-content\">";

    $comma = '';
    $q_string  = "select grp_name ";
    $q_string .= "from inv_grouplist ";
    $q_string .= "left join inv_groups on inv_groups.grp_id = inv_grouplist.gpl_group ";
    $q_string .= "where gpl_user = " . $a_inv_users['usr_id'] . " ";
    $q_inv_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    while ($a_inv_grouplist = mysqli_fetch_array($q_inv_grouplist)) {
      print $comma . $a_inv_grouplist['grp_name'];
      $comma = ", ";
    }
    print "</td>\n";
    print "</tr>";

  }

  mysqli_free_result($q_inv_users);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
