<?php
# Script: openview.report.php
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

  $package = "openview.report.php";

  logaccess($db, $_SESSION['uid'], $package, "View openview alert report");

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
<title>Alarm Report</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
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
  <th class="ui-state-default">Openview Alarm Report</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('alarm-listing-help');">Help</a></th>
</tr>
</table>

<div id="alarm-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This report shows the monthly, yearly, and overall listing of OpenView alarms received by the Unix team.
 The alarms for 1970 are due to the import script not being able to figure out the actual date of the alarm.</p>

</div>

</div>

<table class="ui-styled-table">
<?php

  $years = array('1970', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017');
  $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');

# go through each year and month and pull starting with 2009 forward, month by month.
  $overalltotal = 0;
  foreach ($years as &$ptryear) {

    $yeartotal = 0;
    print "<tr>\n";
    print " <th class=\"ui-state-default\" colspan=\"7\">Year: " . $ptryear . "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Date</th>\n";
    print "  <th class=\"ui-state-default\">Critical</th>\n";
    print "  <th class=\"ui-state-default\">Major</th>\n";
    print "  <th class=\"ui-state-default\">Minor</th>\n";
    print "  <th class=\"ui-state-default\">Warning</th>\n";
    print "  <th class=\"ui-state-default\">Normal</th>\n";
    print "  <th class=\"ui-state-default\">Total</th>\n";
    print "</tr>\n";

    foreach ($months as &$ptrmon) {

      $critical = 0;
      $major = 0;
      $minor = 0;
      $warning = 0;
      $normal = 0;
      $total = 0;

      $q_string  = "select alarm_level ";
      $q_string .= "from alarms ";
      $q_string .= "where alarm_timestamp < '" . $ptryear . "-" . $ptrmon . "-31' and alarm_timestamp > '" . $ptryear . "-" . $ptrmon . "-01' ";
      $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_alarms = mysqli_fetch_array($q_alarms)) {

        if ($a_alarms['alarm_level'] == 5) {
          $critical++;
        }
        if ($a_alarms['alarm_level'] == 4) {
          $major++;
        }
        if ($a_alarms['alarm_level'] == 3) {
          $minor++;
        }
        if ($a_alarms['alarm_level'] == 2) {
          $warning++;
        }
        if ($a_alarms['alarm_level'] == 1) {
          $normal++;
        }
        $total++;
        $yeartotal++;
        $overalltotal++;
      }


      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $ptryear . "-" . $ptrmon . "-01</td>\n";
      print "  <td class=\"ui-widget-content\">" . $critical . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $major . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $minor . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $warning . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $normal . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $total . "</td>\n";
      print "</tr>\n";
    }
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"7\">Year Total: " . $yeartotal . "</td>\n";
    print "</tr>\n";
  }
  print "<tr>\n";
  print "  <td class=\"ui-widget-content\" colspan=\"7\">Overall: " . $overalltotal . "</td>\n";;
  print "</tr>\n";

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
