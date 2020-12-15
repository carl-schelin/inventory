<?php
# Script: changelog.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "changelog.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the interfaces.");

  if (isset($_GET['group'])) {
    $formVars['group']     = clean($_GET['group'],    10);
  } else {
    $formVars['group']     = 0;
  }

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by cl_name";
    $_SESSION['sort'] = '';
  }

  $and = " where";

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " cl_group = " . $formVars['group'] . " ";
    $and = " and";
  }

  $where = $group;

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
<title>Changelog Application Listing</title>

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

  $passthrough = "&group=" . $formVars['group'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Changelog Applications</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the changelog entries for the manual views. Manual entries can be pretty custom and not be associated with an ";
  print "Intrado product or server. This page lets you view these custom entries. Click on the server or application to see the report for ";
  print "the item. In the group listing, clicking on the group gives you the full report. In the full report, clicking on the group gives ";
  print "you the listing for that group.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=cl_name"  . $passthrough . "\">Application</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=grp_name" . $passthrough . "\">Group</a></th>\n";
  print "</tr>\n";

  $q_string  = "select cl_id,cl_name,cl_group,grp_name ";
  $q_string .= "from changelog ";
  $q_string .= "left join groups on groups.grp_id = changelog.cl_group ";
  $q_string .= $where . " ";
  $q_string .= $orderby;
  $q_changelog = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_changelog) > 0) {
    while ($a_changelog = mysqli_fetch_array($q_changelog)) {

      $linkstart  = "<a href=\"" . $Reportroot . "/changelog.view.php?id=" . $a_changelog['cl_id'] . "\">";
      if ($formVars['group'] == 0) {
        $groupstart = "<a href=\"" . $Reportroot . "/changelog.php?group=" . $a_changelog['cl_group'] . "\">";
      } else {
        $groupstart = "<a href=\"" . $Reportroot . "/changelog.php\">";
      }
      $linkend    = "</a>";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart  . $a_changelog['cl_name']  . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $groupstart . $a_changelog['grp_name'] . $linkend . "</td>\n";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"2\">No records found</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
