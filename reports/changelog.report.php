<?php
# Script: changelog.report.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "changelog.report.php";

  logaccess($formVars['uid'], $package, "Viewing the script");


  $formVars['month'] = date('m');
  $formVars['year'] = date('Y');
  $formVars['name'] = '';

  if (isset($_GET['month'])) {
    $formVars['month'] = clean($_GET['month'],10);
  }
  if (isset($_GET['year'])) {
    $formVars['year']  = clean($_GET['year'],10);
  }
  if (isset($_GET['name'])) {
    $formVars['name']  = clean($_GET['name'],40);
  }
  if (isset($_POST['month'])) {
    $formVars['month'] = clean($_POST['month'],10);
  }
  if (isset($_POST['year'])) {
    $formVars['year']  = clean($_POST['year'],10);
  }
  if (isset($_POST['name'])) {
    $formVars['name']  = clean($_POST['name'],40);
  }

  if ($formVars['month'] == '') {
    $formVars['month'] = date('m');
  }
  if ($formVars['year'] == '') {
    $formVars['year'] = date('Y');
  }

  $sel_month[0] = 'All';
  $sel_month[1] = 'January';
  $sel_month[2] = 'February';
  $sel_month[3] = 'March';
  $sel_month[4] = 'April';
  $sel_month[5] = 'May';
  $sel_month[6] = 'June';
  $sel_month[7] = 'July';
  $sel_month[8] = 'August';
  $sel_month[9] = 'September';
  $sel_month[10] = 'October';
  $sel_month[11] = 'November';
  $sel_month[12] = 'December';

  $sel_year[2019] = "2019";
  $sel_year[2018] = "2018";
  $sel_year[2017] = "2017";
  $sel_year[2016] = "2016";
  $sel_year[2015] = "2015";
  $sel_year[2014] = "2014";
  $sel_year[2013] = "2013";
  $sel_year[2012] = "2012";
  $sel_year[2011] = "2011";
  $sel_year[2010] = "2010";
  $sel_year[2009] = "2009";

  $title_month = $sel_month[intval($formVars['month'])];

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
<title>View <?php print $Sitecompany; ?> Changelogs</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

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
  <th class="ui-state-default">Changelog: <?php print $title_month . " " . $formVars['year']; ?></th>
</tr>
</table>

<?php

# initialize changelog arrays and variables
  $allservers = array();
  $count = 0;

# this is the gather from all systems for the changelog part of the listing
  $grpcount = 0;
  $q_string  = "select grp_changelog,grp_clfile ";
  $q_string .= "from groups ";
  $q_string .= "where grp_changelog != ''";
  $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_groups = mysql_fetch_array($q_groups)) {
    $grouplist[$grpcount] = $a_groups['grp_changelog'];
    $filename[$grpcount++] = "." . $a_groups['grp_clfile'];
  }

  $debug = '';
  for ($i = 0; $i < count($grouplist); $i++) {

# need to get every server from the inventory for the group
# plus every application for the windows or webapps folks
# where a file exists.

# check all servers; better than figuring out which one belongs to which group
    $q_string  = "select inv_name ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_status = 0 ";
    $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
    while ($a_inventory = mysql_fetch_array($q_inventory)) {

      if (file_exists( $Sitedir . "/" . $grouplist[$i] . "/" . $a_inventory['inv_name'] . $filename[$i])) {
        $svrlist = file($Sitedir . "/" . $grouplist[$i] . "/" . $a_inventory['inv_name'] . $filename[$i]);
        for ($j = 0; $j < count($svrlist); $j++) {
          $list = explode(" ", $svrlist[$j]);
# create the data field
          if ($list[0] == "Date:") {
            switch($list[3]) {
              case "Jan" : $month = "01"; break;
              case "Feb" : $month = "02"; break;
              case "Mar" : $month = "03"; break;
              case "Apr" : $month = "04"; break;
              case "May" : $month = "05"; break;
              case "Jun" : $month = "06"; break;
              case "Jul" : $month = "07"; break;
              case "Aug" : $month = "08"; break;
              case "Sep" : $month = "09"; break;
              case "Oct" : $month = "10"; break;
              case "Nov" : $month = "11"; break;
              case "Dec" : $month = "12"; break;
            }
            if ($list[2] < 10) {
              $zero = "0";
            } else {
              $zero = "";
            }
            $cl_year  = $list[4];
            $cl_month = $month;
            $cl_day   = $list[2];
            $finaldate = $list[4] . "/" . $month . "/" . $zero . $list[2] . "&nbsp;" . $list[5] . "</td>";
            $finalserver = "<a href=\"/" . $grouplist[$i] . "/server.php?server=" . $a_inventory['inv_name'] . "\">";
          }
# create the from field
          if ($list[0] == "From:") {
            if ($list[1][0] == '"') {
              $from = substr($list[1], 1, strlen($list[1]) - 2);
            } else {
              $from = $list[2];
            }
          }
# process the text file itself
          if ($svrlist[$j] == "--------------\n") {
            $add = 0;
            if ($svrlist[$j + 4] == "--------------\n" || $svrlist[$j + 4] == "Content-Transfer-Encoding: quoted-printable\n") {
              $add = 4;
            }
            $finalname = $from;
            if ($svrlist[$j + $add + 1] != "\n") {
              $finaltext = mysql_real_escape_string(rtrim($svrlist[$j + $add + 1]));
            } else {
              if ($svrlist[$j + $add + 2] != "\n") {
                $finaltext = mysql_real_escape_string(rtrim($svrlist[$j + $add + 2]));
              } else {
                $finaltext = mysql_real_escape_string(rtrim($svrlist[$j + $add + 3]));
              }
            }

            $updatestring = 
              $finaldate . 
              "<td class=\"ui-widget-content\">" . "<a href=\"changelog.report.php?year=" . $formVars['year'] . "&month=" . $formVars['month'] . "&name=" . $finalname . "\">" . $finalname . "</a></td>\n" . 
              "<td class=\"ui-widget-content\">" . $finalserver . $a_inventory['inv_name'] . "</a></td>\n" . 
              "<td class=\"ui-widget-content\">" . $finaltext . "</td>\n" . 
              "</tr>\n";

# two cases
# year always
#   if name is blank
#     if month == selected or month == 0
#   else
#     if name matches selected
#       if month == selected or month == 0

            if ($cl_year == $formVars['year']) {
              if ($formVars['name'] == '') {
                if ($cl_month == $formVars['month'] || $formVars['month'] == 0) {
                  $allservers[$count++] = $updatestring;
                }
              } else {
                if ($finalname == $formVars['name']) {
                  if ($cl_month == $formVars['month'] || $formVars['month'] == 0) {
                    $allservers[$count++] = $updatestring;
                  }
                }
              }
              $cl_year  = '';
              $cl_month = '';
              $cl_day   = '';
            }
          }
        }
      }
    }



# check all changelog lists which are application specific
    $q_string  = "select cl_name ";
    $q_string .= "from changelog ";
    $q_string .= "where cl_delete = 0 ";
    $q_changelog = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
    while ($a_changelog = mysql_fetch_array($q_changelog)) {

      if (file_exists( $Sitedir . "/" . $grouplist[$i] . "/" . $a_changelog['cl_name'] . $filename[$i])) {
        $svrlist = file($Sitedir . "/" . $grouplist[$i] . "/" . $a_changelog['cl_name'] . $filename[$i]);
        for ($j = 0; $j < count($svrlist); $j++) {
          $list = explode(" ", $svrlist[$j]);
# create the data field
          if ($list[0] == "Date:") {
            switch($list[3]) {
              case "Jan" : $month = "01"; break;
              case "Feb" : $month = "02"; break;
              case "Mar" : $month = "03"; break;
              case "Apr" : $month = "04"; break;
              case "May" : $month = "05"; break;
              case "Jun" : $month = "06"; break;
              case "Jul" : $month = "07"; break;
              case "Aug" : $month = "08"; break;
              case "Sep" : $month = "09"; break;
              case "Oct" : $month = "10"; break;
              case "Nov" : $month = "11"; break;
              case "Dec" : $month = "12"; break;
            }
            if ($list[2] < 10) {
              $zero = "0";
            } else {
              $zero = "";
            }
            $cl_year  = $list[4];
            $cl_month = $month;
            $cl_day   = $list[2];
            $finaldate = $list[4] . "/" . $month . "/" . $zero . $list[2] . "&nbsp;" . $list[5] . "</td>";
            $finalserver = "<a href=\"/" . $grouplist[$i] . "/server.php?server=" . $a_changelog['cl_name'] . "\">";
          }
# create the from field
          if ($list[0] == "From:") {
            if ($list[1][0] == '"') {
              $from = substr($list[1], 1, strlen($list[1]) - 2);
            } else {
              $from = $list[2];
            }
          }
# process the text file itself
          if ($svrlist[$j] == "--------------\n") {
            $add = 0;
            if ($svrlist[$j + 4] == "--------------\n" || $svrlist[$j + 4] == "Content-Transfer-Encoding: quoted-printable\n") {
              $add = 4;
            }
            $finalname = $from;
            if ($svrlist[$j + $add + 1] != "\n") {
              $finaltext = mysql_real_escape_string(rtrim($svrlist[$j + $add + 1]));
            } else {
              if ($svrlist[$j + $add + 2] != "\n") {
                $finaltext = mysql_real_escape_string(rtrim($svrlist[$j + $add + 2]));
              } else {
                $finaltext = mysql_real_escape_string(rtrim($svrlist[$j + $add + 3]));
              }
            }

            $updatestring = 
              $finaldate . 
              "<td class=\"ui-widget-content\">" . "<a href=\"changelog.report.php?year=" . $formVars['year'] . "&month=" . $formVars['month'] . "&name=" . $finalname . "\">" . $finalname . "</a></td>\n" . 
              "<td class=\"ui-widget-content\">" . $finalserver . $a_changelog['cl_name'] . "</a>*</td>\n" . 
              "<td class=\"ui-widget-content\">" . $finaltext . "</td>\n" . 
              "</tr>\n";

# two cases
# year always
#   if name is blank
#     if month == selected or month == 0
#   else
#     if name matches selected
#       if month == selected or month == 0

            if ($cl_year == $formVars['year']) {
              if ($formVars['name'] == '') {
                if ($cl_month == $formVars['month'] || $formVars['month'] == 0) {
                  $allservers[$count++] = $updatestring;
                }
              } else {
                if ($finalname == $formVars['name']) {
                  if ($cl_month == $formVars['month'] || $formVars['month'] == 0) {
                    $allservers[$count++] = $updatestring;
                  }
                }
              }
              $cl_year  = '';
              $cl_month = '';
              $cl_day   = '';
            }
          }
        }
      }
    }
  }
# end of changelog gather for this server

?>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Changelog</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('changelog-help');">Help</a></th>
</tr>
</table>

<div id="changelog-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This page shows changes made to this system by any group permitted to make changes.</p>

<ul>
  <li><strong>Change Date</strong> - The date the email was sent to the group's changelog mailing list. It's in reverse order so the newest change is at the top of the list.</li>
  <li><strong>Changed By</strong> - The person who submitted the change. Click on the name to show Changelog reports created by just that user.</li>
  <li><strong>Server</strong> - The server that had the change applied to it. Click on the server or service name to see all entries for that item.</li>
  <li><strong>First Line of Change</strong> - The first line of the changelog submission is extracted from the email.</li>
</ul>

<p><strong>Note:</strong> Default report is the current month and year. You can pass the year, month, and/or Changed By on the URL. Pass 'month=0' for all months of the selected year.</p>

<p>Example: <strong>https://incojs01.scc911.com/inventory/reports/changelog.report.php?year=2016&month=8&name=Ainsley</strong></p>

</div>

</div>


<form name="block" action="changelog.report.php" method="post">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="submit" value="Create Report"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Month <select name="month">
<?php
  for ($i = 0; $i < 13; $i++) {
    if ($formVars['month'] == $i) {
      print "<option selected value=\"" . $i . "\">" . $sel_month[$i] . "</option>\n";
    } else {
      print "<option value=\"" . $i . "\">" . $sel_month[$i] . "</option>\n";
    }
  }
?>
</select> Year <select name="year">
<?php
  for ($i = 2009; $i < 2020; $i++) {
    if ($formVars['year'] == $i) {
      print "<option selected value=\"" . $i . "\">" . $sel_year[$i] . "</option>\n";
    } else {
      print "<option value=\"" . $i . "\">" . $sel_year[$i] . "</option>\n";
    }
  }
?>
</select><input type="hidden" name="name" value="<?php print $formVars['name']; ?>"></td>
</tr>
</table>

</form>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Change Date</th>
  <th class="ui-state-default">Changed By</th>
  <th class="ui-state-default">Server</th>
  <th class="ui-state-default">First Line of Change</th>
</tr>
<?php
  sort($allservers);
  $newarray = array();
  $newarray = array_reverse($allservers);

  for ($i = 0; $i < count($newarray); $i++) {
    print "<tr>\n";
    print "<td class=\"ui-widget-content\">" . $newarray[$i];
    print "</tr>\n";
  }
?>
<tr>
  <td class="ui-widget-content" colspan="4"><strong>Total Changelog Entries for <?php print $title_month . "</strong>: " . count($newarray); ?></td>
</tr>
</table>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
