<?php
# Script: ovmessages.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "ovmessages.php";

  logaccess($formVars['uid'], $package, "Checking out the openview messages.");

  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  } else {
    $formVars['product']   = 0;
  }
  if (isset($_GET['project'])) {
    $formVars['project']   = clean($_GET['project'],  10);
  } else {
    $formVars['project']   = 0;
  }
  if (isset($_GET['group'])) {
    $formVars['group']    = clean($_GET['group'],   10);
  } else {
    $formVars['group']    = 1;
  }
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }
  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
  } else {
    $formVars['csv'] = '';
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
    $orderby = "order by inv_name ";
    $_SESSION['sort'] = '';
  }

  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
      }
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " (inv_manager = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . ") ";
    $and = " and";
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $type;

  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from ip_zones";
  $q_ip_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
    $zoneval[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
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
<title>Openview Messages</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript" language="javascript">

function flip_Bit( p_id, p_bit ) {
  script = document.createElement('script');
  script.src = 'monitoring.toggle.php?id=' + p_id + '&flip=' + p_bit;
  document.getElementsByTagName('head')[0].appendChild(script);
}

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  $q_string  = "select itp_id,itp_acronym ";
  $q_string .= "from inttype ";
  $q_inttype = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inttype = mysqli_fetch_array($q_inttype)) {
    $inttype[$a_inttype['itp_id']] = $a_inttype['itp_acronym'];
  }

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Current Monitoring Status</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";
?>
<div class="main-help ui-widget-content">

<p><strong>Monitoring Health</strong></p>

<p>The purpose of this page is to display all Unix servers that have been identified as being monitored by the official monitoring solution however which have not 
received an alert of any severity over the past 5 days. This report will identify the servers which will need to be reviewed with the monitoring team to understand 
and resolve.</p>

<p><strong>Description</strong></p>

<p>The assumption is that all servers that are supposed to be monitored are in fact monitored. With server events such as application upgrades or hardware replacements, 
alarms are generally temporarily disabled for the duration of the event. There has been no process to ensure alarms have been reenabled after the event. If a server 
has been running well with no issues, then there's the possibility that no alerts will ever be generated.</p>

<p><strong>Background</strong></p>

<p>Notifications by Openview are sent to the Unix Admins On Call group. A member of this group is the Openview Message Capture (ovmc) account. As the group receives 
notifications from Openview, the notification is also received by the ovmc account. Every 30 minutes, notifications are imported into the Inventory database and 
associated with a server. Any notifications that the Unix Team receives and a server can't be found is emailed to the team to be corrected so the storage is 
considered accurate.</p>

<p>The ability to send notifications through the command line on the servers has been implemented in coordination with the Monitoring team. This ensures the message 
is properly constructed and that the Monitoring team is aware of the work being performed. This health check message lets the Unix Team send a test through Openview 
periodically to ensure monitoring is working as expected; the agent is active and is connected to the Openview console.</p>

<p><strong>Report</strong></p>

<p>The report will display all servers that have not received an alert in the past 5 days. There are three possible display options in order to quickly visually 
identify problems that need to be reviewed.</p>

<ul>
  <li><span class="ui-content-widget">Both Alarms Received</span> - This system has successfully sent a Test Message through the system and there is a non-Test Message 
that has been received by the Unix Team.</li>
  <li><span class="ui-state-highlight">No Test Alarm Received</span> - A non-Test Message has been received but a Test Message has not been received. This is more likely 
a problem with the message parsing shell script.</li>
  <li><span class="ui-state-error">No Alarms Received</span> - For these systems, no Alarms, Test or Live, have ever been received. While this could be due to the system 
not having any issues over its lifetime plus the additional problem with the message parsing shell script, this is likely some that should be reviewed based on the Service 
Class and status (Live, 911 Call Path, etc).</li>
</ul>

<p>There are several possible reasons for errors. Notifications could be disabled due to a new system, being disabled in the past due to an incident or maintenance event, 
or by request for another reason. In addition, the message parsing script is enormous, complicated, and prone to errors. The issue needs to be reviewed and addressed 
regardless of the error.</p>

<p><strong>Note</strong> that the Inventory has been storing alarms since 2009 and uses it for the highlighted message last seen date, <strong<however</strong> if the 
server has been built since the last Test Message was sent (see the "Last Test Date" column), then it will be highlighted. A step for testing problems is to manually 
send a test message outside of the normal process. For new systems, when the "chkserver" script runs, a test message is sent automatically.</p>

<p><strong>Note</strong> that if a server isn't monitored (such as a lab server) or is identified in error, go to the server detail record and uncheck the Openview 
monitoring checkbox for the server interface that was identified as being monitored.</p>

<p>Clicking on the server name will take you to the server detail record, Monitoring tab, to see the historical information for the server.</p>

<p><strong>Report Headings</strong></p>

<p>The "Last Test Date" column shows the date the last Test Message was sent through the system.</p>

<p>The last three columns display the last non-Test Message that was received for the system.</p>

<p>An asterisk (*) next to a server name, indicates the server has not been marked as <strong>live</strong> in the Inventory.</p>

<p>An asterisk (*) next to a service class, indicates the server is in the 911 Call Path.</p>

</div>

<?php
  print "</div>\n\n";

  if ($formVars['csv'] == 'true') {
    print "<p>\"Hostname\",\"Function\",\"Product\",\"Last Test Date\",\"Last Alarm Date\",\"Severity\",\"Last Alarm Text\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Hostname</th>\n";
    print "  <th class=\"ui-state-default\">Function</th>\n";
    print "  <th class=\"ui-state-default\">Product</th>\n";
    print "  <th class=\"ui-state-default\">Service Class</th>\n";
    print "  <th class=\"ui-state-default\">Last Test Date</th>\n";
    print "  <th class=\"ui-state-default\">Last Alarm Date</th>\n";
    print "  <th class=\"ui-state-default\">Severity</th>\n";
    print "  <th class=\"ui-state-default\">Last Alarm Text</th>\n";
    print "</tr>\n";
  }

  $date = date('Y-m-d', strtotime('-5 days'));

# find the newest test message and use that date as a baseline for all other alarms
# if an alarm is newer than the test message but still not in the 5 day window, then 
# it's still working correctly.
  $q_string  = "select alarm_timestamp ";
  $q_string .= "from alarms ";
  $q_string .= "left join inventory on inventory.inv_id = alarms.alarm_companyid ";
  $q_string .= "where inv_manager = " . $formVars['group'] . " and alarm_text = 'Unix Monitoring Test - Please Ignore' ";
  $q_string .= "order by alarm_timestamp ";
  $q_string .= "desc ";
  $q_string .= "limit 1 ";
  $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_alarms = mysqli_fetch_array($q_alarms);
  $alarm_baseline = $a_alarms['alarm_timestamp'];

  $goodalarms = 0;
  $notestalarms = 0;
  $nolivealarms = 0;
  $noalarms = 0;
  $totalalarms = 0;
  $oldalarms = 0;
  $nt_callpath = 0;
  $nt_lmcs = 0;
  $nt_bcs = 0;
  $nt_bes = 0;
  $nt_bss = 0;
  $nt_ubs = 0;
  $nt_lab = 0;
  $nl_callpath = 0;
  $nl_lmcs = 0;
  $nl_bcs = 0;
  $nl_bes = 0;
  $nl_bss = 0;
  $nl_ubs = 0;
  $nl_lab = 0;
  $na_callpath = 0;
  $na_lmcs = 0;
  $na_bcs = 0;
  $na_bes = 0;
  $na_bss = 0;
  $na_ubs = 0;
  $na_lab = 0;
  $q_string  = "select inv_id,inv_name,inv_function,prod_name,hw_active,svc_acronym,inv_callpath ";
  $q_string .= "from inventory ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= "left join service on service.svc_id = inventory.inv_class ";
  $q_string .= $where . " and int_openview = 1 and hw_primary = 1 and int_ip6 = 0 ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inventory) > 0) {
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {

# how many servers have received alarms
      $totalalarms++;

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#alarms\" target=\"blank\">";
      $linkend   = "</a>";

# if there are no alarms in the past 5 days, continue
      $q_string  = "select alarm_id ";
      $q_string .= "from alarms ";
      $q_string .= "where alarm_timestamp > '" . $date . "' and alarm_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "limit 1 ";
      $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_alarms) == 0) {
      
        $class = "ui-state-highlight";

        $test_alarm = '0000-00-00 00:00:00';
        $q_string  = "select alarm_timestamp ";
        $q_string .= "from alarms ";
        $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " and alarm_text = \"'Unix Monitoring Test - Please Ignore'\" ";
        $q_string .= "order by alarm_timestamp ";
        $q_string .= "desc ";
        $q_string .= "limit 1 ";
        $q_alarmtext = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_alarmtext) > 0) {
          $a_alarmtext = mysqli_fetch_array($q_alarmtext);
          $test_alarm = $a_alarmtext['alarm_timestamp'];
          $testalarms++;
        }

        $q_string  = "select alarm_timestamp,alarm_level,alarm_text ";
        $q_string .= "from alarms ";
        $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " and alarm_text !=  \"'Unix Monitoring Test - Please Ignore'\" ";
        $q_string .= "order by alarm_timestamp ";
        $q_string .= "desc ";
        $q_string .= "limit 1 ";
        $q_alarmtext = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_alarmtext) > 0) {
          $a_alarmtext = mysqli_fetch_array($q_alarmtext);
          if ($alarm_baseline < $a_alarmtext['alarm_timestamp']) {
            if ($test_alarm != '0000-00-00 00:00:00') {
              $class = "ui-widget-content";
              $goodalarms++;
            } else {
              $notestalarms++;

              if ($a_inventory['svc_acronym'] == 'LMCS') {
                $nt_lmcs++;
              }
              if ($a_inventory['svc_acronym'] == 'BCS') {
                $nt_bcs++;
              }
              if ($a_inventory['svc_acronym'] == 'BES') {
                $nt_bes++;
              }
              if ($a_inventory['svc_acronym'] == 'BSS') {
                $nt_bss++;
              }
              if ($a_inventory['svc_acronym'] == 'UBS') {
                $nt_ubs++;
              }
              if ($a_inventory['svc_acronym'] == 'LAB') {
                $nt_lab++;
              }
              if ($a_inventory['inv_callpath']) {
                $nt_callpath++;
              }
            }
          }
          $displayalarms++;
        } else {
          $a_alarmtext['alarm_timestamp'] = '0000-00-00 00:00:00';
          $a_alarmtext['alarm_level'] = 0;
          $a_alarmtext['alarm_text'] = 'No alarms since 2009-09-08 17:13:10';
          $class = "ui-state-error";
          if ($test_alarm != '0000-00-00 00:00:00') {
            $nolivealarms++;
            if ($a_inventory['svc_acronym'] == 'LMCS') {
              $nl_lmcs++;
            }
            if ($a_inventory['svc_acronym'] == 'BCS') {
              $nl_bcs++;
            }
            if ($a_inventory['svc_acronym'] == 'BES') {
              $nl_bes++;
            }
            if ($a_inventory['svc_acronym'] == 'BSS') {
              $nl_bss++;
            }
            if ($a_inventory['svc_acronym'] == 'UBS') {
              $nl_ubs++;
            }
            if ($a_inventory['svc_acronym'] == 'LAB') {
              $nl_lab++;
            }
            if ($a_inventory['inv_callpath']) {
              $nl_callpath++;
            }
          } else {
            $noalarms++;
            if ($a_inventory['svc_acronym'] == 'LMCS') {
              $na_lmcs++;
            }
            if ($a_inventory['svc_acronym'] == 'BCS') {
              $na_bcs++;
            }
            if ($a_inventory['svc_acronym'] == 'BES') {
              $na_bes++;
            }
            if ($a_inventory['svc_acronym'] == 'BSS') {
              $na_bss++;
            }
            if ($a_inventory['svc_acronym'] == 'UBS') {
              $na_ubs++;
            }
            if ($a_inventory['svc_acronym'] == 'LAB') {
              $na_lab++;
            }
            if ($a_inventory['inv_callpath']) {
              $na_callpath++;
            }
          }
        }

        $hwactive = '';
        if ($a_inventory['hw_active'] == '0000-00-00') {
          $hwactive = ' *';
        }

        $emergency = '';
        if ($a_inventory['inv_callpath']) {
          $emergency = ' (*)';
        }

        $level = 'No alarms';
        if ($a_alarmtext['alarm_level'] == 1) {
          $level = 'Normal';
        }
        if ($a_alarmtext['alarm_level'] == 2) {
          $level = 'Minor';
        }
        if ($a_alarmtext['alarm_level'] == 3) {
          $level = 'Warning';
        }
        if ($a_alarmtext['alarm_level'] == 4) {
          $level = 'Major';
        }
        if ($a_alarmtext['alarm_level'] == 5) {
          $level = 'Critical';
        }

        if ($formVars['csv'] == 'true') {
          print "\"" . $a_inventory['inv_name']        . "\",";
          print "\"" . $a_inventory['inv_function']    . "\",";
          print "\"" . $a_inventory['prod_name']       . "\",";
          print "\"" . $a_inventory['svc_acronym']     . "\",";
          print "\"" . $test_alarm                     . "\",";
          print "\"" . $a_alarmtext['alarm_timestamp'] . "\",";
          print "\"" . $level                          . "\",";
          print "\"" . $a_alarmtext['alarm_text']      . "\"";
          print "</br>\n";
        } else {
          print "<tr>\n";
          print "  <td class=\"" . $class . "\"><nobr>" . $linkstart . $a_inventory['inv_name'] . $linkend . $hwactive . "</nobr></td>\n";
          print "  <td class=\"" . $class . "\"><nobr>"              . $a_inventory['inv_function']        . "</nobr></td>\n";
          print "  <td class=\"" . $class . "\"><nobr>"              . $a_inventory['prod_name']           . "</nobr></td>\n";
          print "  <td class=\"" . $class . "\"><nobr>"              . $a_inventory['svc_acronym'] . $emergency . "</nobr></td>\n";
          print "  <td class=\"" . $class . "\"><nobr>"              . $test_alarm                         . "</nobr></td>\n";
          print "  <td class=\"" . $class . "\"><nobr>"              . $a_alarmtext['alarm_timestamp']     . "</nobr></td>\n";
          print "  <td class=\"" . $class . "\"><nobr>"              . $level                              . "</nobr></td>\n";
          print "  <td class=\"" . $class . "\">"                    . $a_alarmtext['alarm_text']          . "</td>\n";
          print "</tr>\n";
        }
      }
    }
  } else {
    if ($formVars['csv'] == 'true') {
      print "No records found</br>\n";
    } else {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\" colspan=\"6\">No records found</td>\n";
      print "</tr>\n";
    }
  }
 
  if ($formVars['csv'] == 'true') {
    print "</p>\n";
  } else {
    print "</table>\n";

    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Alarm Status</th>\n";
    print "  <th class=\"ui-state-default\">Count</th>\n";
    print "  <th class=\"ui-state-default\">LMCS</th>\n";
    print "  <th class=\"ui-state-default\">911 Call Path</th>\n";
    print "  <th class=\"ui-state-default\">BCS</th>\n";
    print "  <th class=\"ui-state-default\">BES</th>\n";
    print "  <th class=\"ui-state-default\">BSS</th>\n";
    print "  <th class=\"ui-state-default\">UBS</th>\n";
    print "  <th class=\"ui-state-default\">LAB</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">Number of Server Interfaces identified in the Inventory to be monitored by Openview</td>\n";
    print "  <td class=\"ui-widget-content\">" . $totalalarms . "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">Number of Server Interfaces that have generated a successful test alarm and have received live alarms</td>\n";
    print "  <td class=\"ui-widget-content\">" . $goodalarms . "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <td class=\"ui-state-highlight\">Number of Server Interfaces that have not generated a successful test alarm but have received live alarms</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $notestalarms . "</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $nt_lmcs      . "</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $nt_callpath  . "</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $nt_bcs       . "</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $nt_bes       . "</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $nt_bss       . "</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $nt_ubs       . "</td>\n";
    print "  <td class=\"ui-state-highlight\">" . $nt_lab       . "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <td class=\"ui-state-error\">Number of Server Interfaces that have generated a successful test alarm but no live alarms</td>\n";
    print "  <td class=\"ui-state-error\">" . $nolivealarms . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $nl_lmcs      . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $nl_callpath  . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $nl_bcs       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $nl_bes       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $nl_bss       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $nl_ubs       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $nl_lab       . "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <td class=\"ui-state-error\">Number of Server Interfaces that have not generated a test alarm or a live alarm</td>\n";
    print "  <td class=\"ui-state-error\">" . $noalarms     . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $na_lmcs      . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $na_callpath  . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $na_bcs       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $na_bes       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $na_bss       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $na_ubs       . "</td>\n";
    print "  <td class=\"ui-state-error\">" . $na_lab       . "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">Number of Server Interfaces with no current alarms since " . $date . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . ($goodalarms + $notestalarms + $nolivealarms + $noalarms) . "</td>\n";
    print "</tr>\n";
    print "</table>\n";

  }

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
