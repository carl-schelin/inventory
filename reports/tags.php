<?php
# Script: tags.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Admin);

  $package = "tags.php";

  logaccess($db, $formVars['uid'], $package, "Viewing all tags.");

  if (isset($_GET['sort'])) {
    $orderby = "order by " . clean($_GET['sort'], 10);
  } else {
    $orderby = "order by inv_name";
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
<title>Tag Report</title>

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
  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Tag Table</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>The purpose of this report is to provide a list of all tags.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";


  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"tags.php?sort=inv_name\">Server</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"tags.php?sort=tag_name\">Tag</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"tags.php?sort=usr_name\">Creator</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"tags.php?sort=grp_name\">Group</a></th>\n";
  print "</tr>\n";

  $q_string  = "select tag_id,inv_name,tag_name,usr_name,grp_name ";
  $q_string .= "from inv_tags ";
  $q_string .= "left join inv_inventory on inv_inventory.inv_id = inv_tags.tag_companyid ";
  $q_string .= "left join inv_users     on inv_users.usr_id     = inv_tags.tag_owner ";
  $q_string .= "left join inv_groups    on inv_groups.grp_id    = inv_tags.tag_group ";
  $q_string .= "where inv_status = 0 and tag_type = 1 ";
  $q_string .= $orderby;
  $q_inv_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {

    print "<tr>";
    print "  <td class=\"ui-widget-content\">" . $a_inv_tags['inv_name'] . "</td>";
    print "  <td class=\"ui-widget-content\">" . $a_inv_tags['tag_name'] . "</td>";
    print "  <td class=\"ui-widget-content\">" . $a_inv_tags['usr_name'] . "</td>";
    print "  <td class=\"ui-widget-content\">" . $a_inv_tags['grp_name'] . "</td>";
    print "</tr>";

  }

  mysqli_free_result($q_inv_tags);
?>
</table>
</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
