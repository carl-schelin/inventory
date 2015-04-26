#!/usr/local/bin/php
<?php
# Script: morning.report.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $formVars['date'] = date('Y-m-d');

  $usermail = "";
  $comma = "";
  $q_string  = "select usr_email ";
  $q_string .= "from users ";
  $q_string .= "where usr_report = 1 and usr_disabled = 0 and usr_id != 1 and usr_email like '%@intrado.com' ";
  $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    $usermail = $usermail . $comma . $a_users['usr_email'];
    $comma = ",";
  }
# send To: the admin account
  $q_string  = "select usr_email ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = 1";
  $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
  $a_users = mysql_fetch_array($q_users);
  $adminmail = $a_users['usr_email'];

  $subject = "TechOps Morning Report: " . $formVars['date'];

  $headers  = "From: Morning Report <report@" . $Sitehttp . ">\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
  $headers .= "BCC: " . $usermail . "\r\n";

  $status[0] = "No Status Report";
  $status[1] = "Green";
  $status[2] = "Yellow";
  $status[3] = "Red";

  $output  = "<html>\n";
  $output .= "<body>\n";
  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Morning Report Status</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Group</th>\n";
  $output .= "  <th>Status</th>\n";
  $output .= "  <th>Description</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_report != 0 ";
  $q_string .= "order by grp_report";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {

# set a flag so at least one entry from each department is displayed.
    $flag = 0;
    $q_string  = "select rep_id,rep_status,rep_timestamp,rep_task ";
    $q_string .= "from report ";
    $q_string .= "where rep_group = " . $a_groups['grp_id'] . " ";
    $q_string .= "and rep_date = '" . $formVars['date'] . "' ";
    $q_string .= "order by rep_task";
    $q_report = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_report = mysql_fetch_array($q_report)) {

      $color[0] = "#99ccff";
      $color[1] = "#bced91";
      $color[2] = "#ffff00";
      $color[3] = "#fa8072";

      $list = explode(" ", $a_report['rep_timestamp']);

      if ($list[0] != $formVars['date']) {
        $color[2] = "#cdcd00";
        $color[3] = "#eeb4b4";
      }

      $flag = 1;
      if ($a_report['rep_task'] == '') {
        $a_report['rep_task'] = 'No Issues';
      }
      if ($a_report['rep_status'] == '') {
        $a_report['rep_status'] = 0;
      }
      
      $output .= "<tr style=\"background-color: " . $color[$a_report['rep_status']] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
      $output .= "  <td>" . $status[$a_report['rep_status']] . "</td>\n";
      $output .= "  <td>" . $a_report['rep_task'] . "</td>\n";
      $output .= "</tr>\n";
    }
    if ($flag == 0) {
      $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
      $output .= "  <td>No Status Report</td>\n";
      $output .= "  <td>&nbsp;</td>\n";
      $output .= "</tr>\n";
    }
  }
  $output .= "</table>\n\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"2\">Daily Events</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Group</th>\n";
  $output .= "  <th>Description</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select evt_id,evt_group,evt_task ";
  $q_string .= "from events ";
  $q_string .= "where evt_date = '" . $formVars['date'] . "'";
  $q_events = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_events = mysql_fetch_array($q_events)) {

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_events['evt_group'];
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    $output .= "<tr style=\"background-color: #ffffcc; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
    $output .= "  <td>" . $a_events['evt_task'] . "</td>\n";
    $output .= "</tr>\n";
  }
  $output .= "</table>\n\n";

  $output .= "<p>This message is from the Inventory Management application.\n";
  $output .= "<br><a href=\"" . $Morningroot . "/morning.report.php?date=" . $formVars['date'] . "\">Today's Status Report</a></p>\n";

  $output .= "<p>Legend:</p>\n";

  $output .= "<ul>\n";
  $output .= "  <li style=\"background:#bced91\">Green - All functional areas under the control of the team is at 100%.</li>\n";
  $output .= "  <li style=\"background:#ffff00\">Yellow - Non critical functional areas are experiencing issues such as lab systems or support services.</li>\n";
  $output .= "  <li style=\"background:#cdcd00\">Yellow - Same as above but indicates an entry copied from the previous business day.</li>\n";
  $output .= "  <li style=\"background:#fa8072\">Red - A critical area is experiencing issues which will be generating an OMaR or Incident.</li>\n";
  $output .= "  <li style=\"background:#eeb4b4\">Red - Same as above but indicates an entry copied from the previous business day.</li>\n";
  $output .= "  <li style=\"background:#99ccff\">Blue - Group has failed to send a report or sent one with an invalid status.</li>\n";
  $output .= "</ul>\n\n";

  $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

  $output .= "</body>\n";
  $output .= "</html>\n";

#  $body = strip_tags($output);
  $body = $output;

  mail($adminmail, $subject, $body, $headers);
?>
