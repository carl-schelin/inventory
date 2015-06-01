#!/usr/local/bin/php
<?php
# Script: morning.report.check.php
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

  $subject = "TechOps Morning Report - Reminder for " . $formVars['date'];

  $headers  = "From: Morning Report <report@" . $Sitehttp . ">\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $q_string  = "select grp_id,grp_name,grp_email ";
  $q_string .= "from groups ";
  $q_string .= "where grp_report != 0 ";
  $q_string .= "order by grp_report";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {

    $flag = 0;
    $q_string  = "select rep_id,rep_status,rep_task ";
    $q_string .= "from report ";
    $q_string .= "where rep_group = " . $a_groups['grp_id'] . " and rep_date = '" . $formVars['date'] . "'";
    $q_report = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_report = mysql_fetch_array($q_report)) {

# if there is an entry and someone select "No Status" (0), flip the flag back off
      $flag = 1;
      if ($a_report['rep_status'] == 0) {
        $flag = 0;
      }
    }

# if no entry was found or if someone entered 'no status', send an e-mail to the group
    if ($flag == 0) {
      $usermail = "";
      $comma = "";
      $q_string  = "select usr_email ";
      $q_string .= "from users ";
      $q_string .= "where usr_disabled = 0 and usr_group = " . $a_groups['grp_id'];
      $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
      while ($a_users = mysql_fetch_array($q_users)) {
        $usermail = $usermail . $comma . $a_users['usr_email'];
        $comma = ",";
      }

      if ($a_groups['grp_email'] == '') {
        $sendto = $usermail;
      } else {
        $sendto = $a_groups['grp_email'];
      }
      $sendto .= $Siteadmins;

      $output  = "<html>\n";
      $output .= "<body>\n";
      $output .= "<p>Good morning,</p>\n";
      $output .= "<p>As of this time, your group (" . $a_groups['grp_name'] . ") representative has not updated the morning status report for " . $formVars['date'] . ".</p>\n";
      $output .= "<p>Please take a moment to add your groups status prior to the morning e-mail distribution.</p>\n";
      $output .= "<p>Thank you.</p>\n";
      $output .= "<p><a href=\"" . $Morningroot . "/morning.report.php?date=" . $formVars['date'] . "\">Morning Report site</a></p>\n";
      $output .= "<p>Morning Report documentation: <a href=\"" . $Wikiroot . "/index.php/Morning_Report\">Morning Report Wiki Page</a></p>\n";

      $output .= "<p>This message is from the Inventory Management application.\n";
      $output .= "<br>This mail box is not monitored, please do not reply.</p>\n\n";

      $output .= "</body>\n";
      $output .= "</html>\n\n";

      mail($sendto, $subject, $output, $headers);
    }
  }

?>
