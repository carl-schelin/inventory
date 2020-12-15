<?php
# Script: morning.report.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "morning.report.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

# get the date for the log file display; yesterday's logs
  $date = date('Ymd', mktime(0, 0, 0, date('m'), date('d')-1, date('Y')) );

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }

  if ($formVars['inwork'] == '') {
    $formVars['inwork'] = 'false';
  }
  if ($formVars['project'] == '') {
    $formVars['project'] = 0;
  }
  if ($formVars['country'] == '') {
    $formVars['country'] = 0;
  }
  if ($formVars['state'] == '') {
    $formVars['state'] = 0;
  }
  if ($formVars['city'] == '') {
    $formVars['city'] = 0;
  }
  if ($formVars['location'] == '') {
    $formVars['location'] = 0;
  }
  if ($formVars['csv'] == '') {
    $formVars['csv'] = 'false';
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
    $orderby = "order by inv_name";
    $_SESSION['sort'] = '';
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
<title>Morning Report</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

function toggleAll(group) {
  var a = document.getElementsByTagName('div');
  var n = a.length;

  for (var i = 0; i < n; i++) {
    if (group == "rrdtool") {
      if (a[i].id.indexOf('_rrdtool') != -1) {
        toggleDiv(a[i].id);
      }
    }
    if (group == "messages") {
      if (a[i].id.indexOf('_messages') != -1) {
        toggleDiv(a[i].id);
      }
    }
    if (group == "morning") {
      if (a[i].id.indexOf('_morning') != -1) {
        toggleDiv(a[i].id);
      }
    }
    if (group == "sudoers") {
      if (a[i].id.indexOf('_sudoers') != -1) {
        toggleDiv(a[i].id);
      }
    }
  }
}

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
  <th class="ui-state-default">Mass Effect</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<h2>Morning Report Dashboard</h2>

<p>This page shows all servers that are accessible using the UnixSvc account as this account is used to retrieve all the 
data that is viewable in this report. For each accessible server you have four possible sub-tables:</p>

<ul>
  <li><strong>Performance Review</strong> - Scripts have been written to gather performance data. A tool called 'rrdtool' is 
used to store the information and then generate graphs for trending review. These graphs are generated every 15 minutes 
but are only retrieved in the morning for review. If you click on the Performance Review link on the server bar, you will 
see the four primary thumbnails for that server. Clicking on the 'Click for more detail' link will bring up a new page 
with detailed graphs. The graphs are set in groups of three; daily, weekly, and a longer 3 month bar under the two. Clicking 
on any of the graphs brings up even more detail.</li>
  <li><strong>Message Log Review</strong> - Each server's message file is first processed on the server to reduce the size. Then 
the processed files are retrieved to a central server. A second set of processing is performed to create these reports. Clicking 
on the 'Click to view the raw message log file' in the title bar will bring up a new window with the unprocessed messages file.</li>
  <li><strong>Server Audit</strong> - Various scripts check specific actionable issues on each server and report them if there 
is a discrepency. A majority of the items are lower priority however we do get details on failed drives where the system is 
not being monitored by OpenView.</li>
  <li><strong>User/Sudoers Audit</strong> - There is a chkuser and chksudoers script that parses out user details from 
/etc/passwd and /etc/shadow and from the /etc/sudoers file. It flags a user when they have left the company and flags users who 
have privileged access beyond the ticket expiration date. This isn't technically needed for the morning report but it is 
reviewed and worked on.</li>
</ul>

<p>The goal for the last three sub-tables is to be empty. This tells us that no additional work needs to be done.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('rrdtool');">Toggle All Performance Reviews</a></th>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('messages');">Toggle All Message Logs</a></th>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('morning');">Toggle All Server Audits</a></th>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('sudoers');">Toggle All User/Sudoers</a></th>
</tr>
</table>

<?php

# now build the where clause
  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
    $and = " and";
  }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

  $location = '';
  if ($formVars['country'] == 0 && $formVars['location'] > 0) {
    $location = $and . " inv_location = " . $formVars['location'] . " ";
    $and = " and";
  } else {
    if ($formVars['country'] > 0) {
      $location .= $and . " loc_country = " . $formVars['country'] . " ";
      $and = " and";
    }
    if ($formVars['state'] > 0) {
      $location .= $and . " loc_state = " . $formVars['state'] . " ";
      $and = " and";
    }
    if ($formVars['city'] > 0) {
      $location .= $and . " loc_city = " . $formVars['city'] . " ";
      $and = " and";
    }
    if ($formVars['location'] > 0) {
      $location .= $and . " inv_location = " . $formVars['location'] . " ";
      $and = " and";
    }
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $inwork . $location . $type;

  $title_load   = "title=\"Load Average: Should be under 1. Shows how busy the system is. Spikes here should be reflected with spikes in CPU and Memory\"";
  $title_queue  = "title=\"Run Queues: Should be under 2. Red shows disk I/O blocking. Blue shows CPU blocking.\"";
  $title_cpu    = "title=\"CPU Usage: All cpus are shown so multiple colors are possible. The more colors, the more cpus are in use.\"";
  $title_memory = "title=\"Memory Usage: Red=Program usage, Orange=Memory being cached, Yellow=Disk Buffers being cached\nHigh program is a performance hit and could indicate a need for more RAM\"";
  $title_swap   = "title=\"Swap Usage: Generally red is 50% but can go higher. 100% will start Disk Performance alerts.\"";

  $q_string  = "select inv_id,inv_zone,IF(INSTR(inv_name,'/'),LEFT(inv_name,LOCATE('/',inv_name)-1),inv_name) as inv_name,int_server,zone_name,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware  on inventory.inv_id      = hardware.hw_companyid ";
  $q_string .= "left join interface on inventory.inv_id      = interface.int_companyid ";
  $q_string .= "left join zones     on zones.zone_id         = inventory.inv_zone ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join software  on software.sw_companyid = inventory.inv_id ";
  $q_string .= $where . " and sw_type = 'OS' and int_management = 1 ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $os = return_System($db, $a_inventory['inv_id']);

#####
# This is the main server line with links if there is data to be displayed
#####
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\" style=\"text-align: left;\" title=\"" . $a_inventory['sw_software'] . "/" . $a_inventory['zone_name'] . "\">" . $a_inventory['int_server'] . "</th>\n";
    if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['int_server'] . "/load-day-thumb.png")) {
      print "  <th class=\"ui-state-default\" width=\"20%\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('" . $a_inventory['int_server'] . "_rrdtool');\">Performance Review</a></th>\n";
    } else {
      print "  <th class=\"ui-state-default\" width=\"20%\">&nbsp;</th>\n";
    }
    if (file_exists("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/morning.report")) {
      print "  <th class=\"ui-state-default\" width=\"20%\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('" . $a_inventory['int_server'] . "_messages');\">Message Log Review</a></th>\n";
    } else {
      print "  <th class=\"ui-state-default\" width=\"20%\">&nbsp;</th>\n";
    }
    if (file_exists("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/morning.status")) {
      print "  <th class=\"ui-state-default\" width=\"20%\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('" . $a_inventory['int_server'] . "_morning');\">Server Audit</a></th>\n";
    } else {
      print "  <th class=\"ui-state-default\" width=\"20%\">&nbsp;</th>\n";
    }
    if (file_exists("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/sudoers.status")) {
      print "  <th class=\"ui-state-default\" width=\"20%\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('" . $a_inventory['int_server'] . "_sudoers');\">User/Sudoers Audit</a></th>\n";
    } else {
      print "  <th class=\"ui-state-default\" width=\"20%\">&nbsp;</th>\n";
    }
    print "</tr>\n";
    print "</table>\n\n";

#####
# This is the set of links for the rrdtool set of info
# Data is hidden from here to the bottom
#####
    print "<div id=\"" . $a_inventory['int_server'] . "_rrdtool\" style=\"" . $display . "\">\n";

    if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['int_server'] . "/load-day-thumb.png")) {
      print "<table class=\"ui-styled-table\">\n";
      print "<tr>\n";
      print "  <th class=\"ui-state-default\" colspan=\"5\"><a href=\"" . $Siteurl . "/rrdtool/" . $a_inventory['int_server'] . "\" target=\"_blank\">Click for more detail</a></th>\n";
      print "</tr>\n";
      print "<tr>\n";
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['int_server'] . "/load-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" " . $title_load   . " style=\"text-align: center\" width=\"20%\"><img height=\"95\" src=\"" . $Siteurl . "/rrdtool/" . $a_inventory['int_server'] . "/load-day-thumb.png\" width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center\" width=\"20%\">Image not found</td>\n";
      }
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['int_server'] . "/mem-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" " . $title_queue  . " style=\"text-align: center\" width=\"20%\"><img height=\"95\" src=\"" . $Siteurl . "/rrdtool/" . $a_inventory['int_server'] . "/mem-day-thumb.png\"  width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center\" width=\"20%\">Image not found</td>\n";
      }
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['int_server'] . "/cpu-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" " . $title_cpu    . " style=\"text-align: center\" width=\"20%\"><img height=\"95\" src=\"" . $Siteurl . "/rrdtool/" . $a_inventory['int_server'] . "/cpu-day-thumb.png\"  width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center\" width=\"20%\">Image not found</td>\n";
      }
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['int_server'] . "/ram-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" " . $title_memory . " style=\"text-align: center\" width=\"20%\"><img height=\"95\" src=\"" . $Siteurl . "/rrdtool/" . $a_inventory['int_server'] . "/ram-day-thumb.png\"  width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center\" width=\"20%\">Image not found</td>\n";
      }
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['int_server'] . "/swap-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" " . $title_swap  . " style=\"text-align: center\" width=\"20%\"><img height=\"95\" src=\"" . $Siteurl . "/rrdtool/" . $a_inventory['int_server'] . "/swap-day-thumb.png\" width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center\" width=\"20%\">Image not found</td>\n";
      }
      print "</tr>\n";
      print "</table>\n\n";
    }

    print "</div>\n\n";

#####
# This is the set of links for the message log review
#####
    print "<div id=\"" . $a_inventory['int_server'] . "_messages\" style=\"" . $display . "\">\n";

    if (file_exists("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/morning.report")) {
      $output = file_get_contents("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/morning.report");
      print "<table class=\"ui-styled-table\">\n";
      print "<tr>\n";
      print "  <th class=\"ui-state-default\"><a href=\"/servers/" . $a_inventory['int_server'] . "/messages." . $date . "\" target=\"_blank\">Click to view the raw message log file</a></th>\n";
      print "</tr>\n";
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\"><pre>" . $output . "</pre></td>\n";
      print "</tr>\n";
      print "</table>\n\n";
    }

    print "</div>\n\n";

#####
# This is the set of links for the server audit review
#####
    print "<div id=\"" . $a_inventory['int_server'] . "_morning\" style=\"" . $display . "\">\n";

    if (file_exists("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/morning.status")) {
      $output = file_get_contents("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/morning.status");
      print "<table class=\"ui-styled-table\">\n";
      print "<tr>\n";
      print "  <th class=\"ui-state-default\">" . $a_inventory['int_server'] . "</th>\n";
      print "</tr>\n";
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\"><pre>" . $output . "</pre></td>\n";
      print "</tr>\n";
      print "</table>\n\n";
    }

    print "</div>\n\n";

#####
# This is the set of links for the user audit review
#####
    print "<div id=\"" . $a_inventory['int_server'] . "_sudoers\" style=\"" . $display . "\">\n";

     if (file_exists("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/sudoers.status")) {
       $output = file_get_contents("/usr/local/admin/servers/" . $a_inventory['int_server'] . "/sudoers.status");
       print "<table class=\"ui-styled-table\">\n";
       print "<tr>\n";
       print "  <th class=\"ui-state-default\">" . $a_inventory['int_server'] . "</th>\n";
       print "</tr>\n";
       print "<tr>\n";
       print "  <td class=\"ui-widget-content\"><pre>" . $output . "</pre></td>\n";
       print "</tr>\n";
       print "</table>\n\n";
     }

     print "</div>\n\n";

   }

?>
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('rrdtool');">Toggle All Performance Reviews</a></th>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('messages');">Toggle All Message Logs</a></th>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('morning');">Toggle All Server Audits</a></th>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleAll('sudoers');">Toggle All User/Sudoers</a></th>
</tr>
</table>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
