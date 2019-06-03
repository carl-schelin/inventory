<?php
# Script: morningreport.userlist.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "morningreport.userlist.php";

  logaccess($formVars['uid'], $package, "Accessing script");

  if (isset($_GET['csv'])) {
    $formVars['csv'] = 1;
  } else {
    $formVars['csv'] = 0;
  }

# if help has not been seen yet,
  if (show_Help($Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Morning Report User Listing</title>

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

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">Morning Report User Listing</th>";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>";
  print "</tr>";
  print "</table>";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists users, groups, managers, and if they're receiving a morning report email.</p>\n";

  print "</div>\n";

  print "</div>\n";

  print "<table class=\"ui-styled-table\">";
  print "<tr>";
  print "  <th class=\"ui-state-default\">User Name</th>\n";
  print "  <th class=\"ui-state-default\">Title</th>\n";
  print "  <th class=\"ui-state-default\">Grouplist</th>\n";
  print "  <th class=\"ui-state-default\">Manager</th>\n";
  print "  <th class=\"ui-state-default\">Morning Report/CC</th>\n";
  print "</tr>";

  $q_string  = "select usr_id,usr_first,usr_last,usr_manager,usr_report,usr_confirm,tit_name ";
  $q_string .= "from users ";
  $q_string .= "left join titles on titles.tit_id = users.usr_title ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {

    if ($a_users['usr_report']) {

      if ($a_users['usr_report']) {
        $morningreport = "Yes";
      } else {
        $morningreport = "No";
      }
      if ($a_users['usr_confirm']) {
        $confirm = "Yes";
      } else {
        $confirm = "No";
      }

      $q_string  = "select usr_first,usr_last ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $a_users['usr_manager'] . " ";
      $q_manager = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_manager = mysql_fetch_array($q_manager);

      if ($formVars['csv']) {
        print "\"" . $a_users['usr_last']   . ", " . $a_users['usr_first']   . "\",";
        print "\"" . $a_users['tit_name'] . "\",";
        print "\"";

        $comma = '';
        $q_string  = "select grp_name ";
        $q_string .= "from grouplist ";
        $q_string .= "left join groups on groups.grp_id = grouplist.gpl_group ";
        $q_string .= "where gpl_user = " . $a_users['usr_id'] . " ";
        $q_grouplist = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_grouplist = mysql_fetch_array($q_grouplist)) {
          print $comma . $a_grouplist['grp_name'];
          $comma = ", ";
        }
        print "\",";
        print "\"" . $a_manager['usr_last'] . ", " . $a_manager['usr_first'] . "\",";
        print "\"" . $morningreport . "/" . $confirm . "\",";
        print "</br>\n";

      } else {
        print "<tr>\n";
        print "  <td class=\"ui-widget-content\">" . $a_users['usr_last']   . ", " . $a_users['usr_first']   . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_users['tit_name'] . "</td>\n";
        print "  <td class=\"ui-widget-content\">";

        $comma = '';
        $q_string  = "select grp_name ";
        $q_string .= "from grouplist ";
        $q_string .= "left join groups on groups.grp_id = grouplist.gpl_group ";
        $q_string .= "where gpl_user = " . $a_users['usr_id'] . " ";
        $q_grouplist = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_grouplist = mysql_fetch_array($q_grouplist)) {
          print $comma . $a_grouplist['grp_name'];
          $comma = ", ";
        }
        print "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_manager['usr_last'] . ", " . $a_manager['usr_first'] . "</td>\n";
        print "  <td class=\"ui-widget-content delete\">" . $morningreport . "/" . $confirm . "</td>\n";
        print "</tr>";
      }
    }
  }

  mysql_free_result($q_users);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
