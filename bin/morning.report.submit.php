#!/usr/bin/php
<?php
  include('/usr/local/httpd/htsecure/status/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','status','root','this4now!!');

  $headers  = "From: Morning Report <report@incomsu1.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

# $argv[0] = script name.
# $argc = the number of items in the $argv array

  if ($argc == 1) {
# you'll never get here if it's coming from the procmail script so you can't 'unlink' the file.
    print "ERROR: invalid command line parameters\n";
    exit(1);
  } else {
    $email = $argv[1];
  }

# received an "Out of Office:" message; just exit the script
# don't forget to delete the .report file or the next report will be whack.
  if ($argv[2] == "Out") {
    print "ERROR: Out of Office message received\n";
    unlink("/home/report/Mail/" . $email . ".report");
    exit(1);
  }

  $group = '';
  if ($argc == 2) {
    $impact = "none";
  } else {
    $impact = $argv[2];
# if the group is passed along as well
    if (strpos($impact, ":") > 0) {
      $list = split(":", $impact);
      $group = $list[0];
      $impact = $list[1];
      if ($impact == '') {
        $impact = "none";
      }
    }
  }

  $impact = strtolower($impact);
  $error = '';
  $mngbldstart = "";
  $mngbldend = "";

  if ($group != '') {
    $q_string = "select grp_id,grp_name from groups where grp_name like '" . $group . "%'";
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);
    if (mysql_num_rows($q_groups) > 1) {
      $body = "<p>Your group string doesn't contain enough characters to determine the proper group. Please add more characters to your group name (" . $group . ") and resend.</p>";
      mail($email, "Error: Search string too loose", $body, $headers);
      unlink("/home/report/Mail/" . $email . ".report");
      exit(1);
    } else {
      $group = $a_groups['grp_name'];
    }
  } else {
# check to see if the user is in the management group
    $q_string = "select usr_id,usr_group from users where usr_id != 1 and usr_email = '$email'";
    $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
    $a_users = mysql_fetch_array($q_users);

    if ($a_users['usr_group'] == 3) {
      $error = "<p><b>Error:</b> You are in the Management Group but didn't select a group that you manage for the update.</p>\n\n";
      $mngbldstart = "<b>";
      $mngbldend = "</b>";
      $impact = 'help';
    }
  }

  if ($impact == 'help') {
    $body  = $error . "<p>Usage:</p>\n";
    $body .= "<p>To: report@incomsu1.scc911.com\n";
    $body .= "<br>Subject: [groupname:][status]</p>\n\n";
    $body .= "<br>Description to be added to the Morning Report.</p>\n\n";

    $body .= "<p>The 'groupname:' can be any portion of the group name to make it unique to the script. For example, 'v:' matches two groups; Voice Engineering and Virtualization. ";
    $body .= "But if you provide 'vo:', you'll process information for the Voice Engineering group. " . $mngbldstart . "This keyword can be used by anyone however it's meant to be used by a Manager or ";
    $body .= "higher to provide a status for the groups they manage. For example, Ryan manages the Unix and NonStop team, the Virtualization team, and the Storage and Backup team. ";
    $body .= "Ryan can send a status for each group by passing 'u:', 'vi:', and 'st:'" . $mngbldend . "</p>\n\n";

    $body .= "<p>The 'status' portion of the Subject line can be red, yellow, or green indicating the status of the message you're sending. If you send a blank Subject line or one with ";
    $body .= "'groupname:' and no status, you'll receive a current status of either your default group or the group you put in the Subject line. For example, if Ryan sends an e-mail ";
    $body .= "with a Subject of 'vi:', he'll get an e-mail with the status for the Virtualization group. If Ryan sends an e-mail with a Subject of 'u:', he'll get an e-mail for the ";
    $body .= "Unix and NonStop Platforms team.</p>";

    $body .= "<p>The 'description' portion of the Subject line can be red, yellow, or green indicating the status of the message you're sending. If you send a blank Subject line or one with ";
    $body .= "'groupname:' and no status, you'll receive a current status of either your default group or the group you put in the Subject line. For example, if Ryan sends an e-mail ";
    $body .= "with a Subject of 'vi:', he'll get an e-mail with the status for the Virtualization group. If Ryan sends an e-mail with a Subject of 'u:', he'll get an e-mail for the ";
    $body .= "Unix and NonStop Platforms team.</p>";

    mail($email, "Morning Report: Help", $body, $headers);
    unlink("/home/report/Mail/" . $email . ".report");
    exit(1);
  }

  $formVars['date'] = date('Y-m-d');

  $formVars['status'] = 0;
  if ($impact == "green") {
    $formVars['status'] = 1;
  }
  if ($impact == "yellow") {
    $formVars['status'] = 2;
  }
  if ($impact == "red") {
    $formVars['status'] = 3;
  }

  $q_string = "select usr_id,usr_group from users where usr_id != 1 and usr_email = '$email'";
  $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
  $a_users = mysql_fetch_array($q_users);

  if ($group == '') {
    $q_string = "select grp_name from groups where grp_id = " . $a_users['usr_group'];
    $q_groups = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    $group = $a_groups['grp_name'];
    $formVars['group'] = $a_users['usr_group'];
  } else {
    $formVars['group'] = $a_groups['grp_id'];
  }

# need to parse out the e-mail then delete it;
# get the border value
#Content-Type: multipart/mixed;
#	boundary="_000_CBAD9FE331D55CarlSchelinintradocom_"
# read until: ^--------------
# read until --boundary value
#--_000_CBAD9FE331D55CarlSchelinintradocom_
# read content type for text/
#Content-Type: text/plain; charset="us-ascii"
#
#--_000_4AF497FC81FF2244B4B567536C8D627E011EC33478LMV08MX04corp_
#Content-Type: text/plain; charset="us-ascii"
#Content-Transfer-Encoding: quoted-printable
#
# looking for base64 and need to convert before processing.
#--_000_51581CA9D0965243B6931E4CDEDF3E45033E271B4Clmv08mx02corp_
#Content-Type: text/plain; charset="utf-8"
#Content-Transfer-Encoding: base64
#
# save details
# until '-- ' or '--=20' or until --border value
# ignore the rest
# delete the file when done

# for blackberry messages, there is no mime encoding and the signature is actually an ***This message ...
# so save the lines of text (if not blank) and bail if the "This message" signature pops up.

  $savedlines = '';
  $boundary = '';
  $leave = 0;
  $report = '';
  $file = fopen("/home/report/Mail/" . $email . ".report", "r");
  while(!feof($file)) {
    $process = trim(fgets($file));

    if (preg_match("/boundary/", $process) && $leave == 0) {
      $value = split("\"", $process);
      $boundary = $value[1];
    }

    if (preg_match("/^--------------/", $process) && $leave == 0) {
      while (!feof($file)) {
        $process = trim(fgets($file));

# again, if a blackberry (bb uses '__' as signature sep), we're done
        if (preg_match("/This message has been sent via the Intrado Wireless Information Network/", $process) || preg_match("/_______________________/", $process)) {
          $report .= $savedlines;
          $leave = 1;
          break;
        }
# save the lines in case it's a plain text message from the blackberry; save after the exit due to the "Wireless" message.
        if ($process != '') {
          $savedlines .= $process . " ";
        }

# on the other hand, if it's an outlook message, parse out the mime encoding.
        if (preg_match("/Content-Transfer-Encoding: quoted-printable/", $process) && $leave == 0) {
          while (!feof($file)) {
            $process = trim(fgets($file));
            if (preg_match("/^--/", $process)) {
              $leave = 1;
              break;
            }
            if ($process != '') {
              $report .= preg_replace("/=$/", '', $process);
            }
          }
        }
# need to read it in, then convert it, _then_ loop through the resultent output looking for the *this message has been sent... or -- /r/n lines to save any encoded information
        if (preg_match("/Content-Transfer-Encoding: base64/", $process) && $leave == 0) {
          while (!feof($file)) {
            $process = trim(fgets($file));
            if (preg_match("/^--/", $process)) {
              $parse = explode("\n", base64_decode($report));
              $report = '';
              for ($i = 0; $i < count($parse); $i++) {
                if (preg_match("/_______________________/", $parse[$i])) {
                  $leave = 1;
                  break;
                } else {
                  $report .= $parse[$i];
                }
              }
              $leave = 1;
              break;
            }
            if ($process != '') {
              $report .= $process;
            }
          }
        }
      }
    }
  }
  fclose($file);
  unlink("/home/report/Mail/" . $email . ".report");

  $q_string = "insert into report set rep_id = NULL," . 
    "rep_user   =  " . $a_users['usr_id']  . "," . 
    "rep_group  =  " . $formVars['group']  . "," . 
    "rep_date   = '" . $formVars['date']   . "'," . 
    "rep_status =  " . $formVars['status'] . "," . 
    "rep_task   = '" . trim($report)       . "'";

# if the status == none, just send out the team's current status.
  if ($impact != "none") {
    mysql_query($q_string, $db);
  }

  $status[0] = "No Status Report";
  $status[1] = "Green";
  $status[2] = "Yellow";
  $status[3] = "Red";

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";
  $color[2] = "yellow";
  $color[3] = "#fa8072";

  $output  = "<html>\n";
  $output .= "<body>\n";
  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=4>Morning Report Status</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Group</th>\n";
  $output .= "  <th>Status</th>\n";
  $output .= "  <th>Description</th>\n";
  $output .= "</tr>\n";

  $flag = 0;
  $q_string = "select rep_id,rep_status,rep_task from report where rep_group = " . $formVars['group'] . " and rep_date = '" . $formVars['date'] . "' order by rep_task";
  $q_report = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_report = mysql_fetch_array($q_report)) {

    $flag = 1;
    if ($a_report['rep_task'] == '') {
      $a_report['rep_task'] = 'No Issues';
    }
    if ($a_report['rep_status'] == '') {
      $a_report['rep_status'] = 0;
    }
      
    $output .= "<tr style=\"background-color: " . $color[$a_report['rep_status']] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td>" . $group . "</td>\n";
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
  $output .= "</table>\n\n";

  $output .= "<p>This message is from the Status Report Management application.\n";
  $output .= "<br><a href=\"https://incomsu1/status/morning.report.php?date=" . $formVars['date'] . "\">Today's Status Report</a>\n";
  $output .= "<br>This mail box is not monitored, please do not reply.</p>\n\n";

  $output .= "</body>\n";
  $output .= "</html>\n";

  $body = $output;

  mail($email, "Morning Report for: " . $group, $body, $headers);

  $q_string = "select usr_id,usr_group from users where usr_id != 1 and usr_email = '$email'";
  $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
  $a_users = mysql_fetch_array($q_users);

# send to users who want to get the confirmation e-mail
  $q_string = "select usr_email from users where usr_id != 1 and usr_email != '$email' and usr_confirm = 1 and usr_group = " . $a_users['usr_group'];
  $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    mail($a_users['usr_email'], "Morning Report for: " . $group, $body, $headers);
  }

?>
