#!/usr/local/bin/php
<?php
# Script: bigfix.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'no';
  if ($argv[$argc - 1] == 'debug') {
    $debug = 'yes';
  }

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";  # green
  $color[2] = "yellow";   # yellow
  $color[3] = "#fa8072";  # red

# for search; give me all patches for today
  $current_date = date('Y-m-d');
  $formVars['back'] = date('Y-m-d', strtotime($current_date . ' -1 day'));
  $formVars['forward'] = date('Y-m-d', strtotime($current_date . ' +1 day'));

  $q_string  = "select usr_id,usr_group,usr_email ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_bigfix = 1 ";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {

    $email = $a_users['usr_email'];
    $output  = "<html>\n";
    $output .= "<body>\n";

    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">BigFix Server Listing</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <th>Servername</th>\n";
    $output .= "  <th>Function</th>\n";
    $output .= "  <th>Number of Patches</th>\n";
    $output .= "  <th>Reboot?</th>\n";
    $output .= "  <th>Platform Managed By</th>\n";
    $output .= "  <th>Applications Managed By</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select inv_id,inv_name,inv_function,inv_manager,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_status = 0 and (inv_manager = " . $a_users['usr_group'] . " or inv_appadmin = " . $a_users['usr_group'] . ") ";
    $q_string .= "order by inv_name ";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_inventory = mysql_fetch_array($q_inventory)) {

      $bgcolor = '';
      $numpatches = 0;
      $reboot = "No";
      $flagged = '';
      $q_string  = "select big_id,big_fixlet,big_severity ";
      $q_string .= "from bigfix ";
      $q_string .= "where big_companyid = " . $a_inventory['inv_id'] . " and big_scheduled > \"" . $formVars['back'] . "\" and big_scheduled < \"" . $formVars['forward'] . "\" ";
      $q_bigfix = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_bigfix) > 0) {
        while ($a_bigfix = mysql_fetch_array($q_bigfix)) {
          $numpatches++;
          if ($a_bigfix['big_fixlet'] == "Restart Server") {
             $reboot = "Yes";
          }
# 2 == Critical
          if ($a_bigfix['big_severity'] == "2") {
             $bgcolor = $color[3];
          }
# 3 == Important but only if Critical hasn't already been selected
          if ($a_bigfix['big_severity'] == "3" && $bgcolor == '') {
             $bgcolor = $color[3];
          }
        }

        if ($bgcolor == '') {
          $bgcolor = $color[0];
        }

        $q_string  = "select grp_name ";
        $q_string .= "from groups ";
        $q_string .= "where grp_id = " . $a_inventory['inv_manager'] . " ";
        $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_manager = mysql_fetch_array($q_groups);

        $q_string  = "select grp_name ";
        $q_string .= "from groups ";
        $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
        $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_appadmin = mysql_fetch_array($q_groups);

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_inventory['inv_name']     . "</td>\n";
        $output .= "  <td>" . $a_inventory['inv_function'] . "</td>\n";
        $output .= "  <td>" . $numpatches . "</td>\n";
        $output .= "  <td>" . $reboot . "</td>\n";
        $output .= "  <td>" . $a_manager['grp_name']     . "</td>\n";
        $output .= "  <td>" . $a_appadmin['grp_name']        . "</td>\n";
        $output .= "</tr>\n";
      }
    }
    $output .= "</table>\n\n";

    $output .= "<p><a href=\"" . $Reportroot . "/bigfix.php?group=" . $a_users['usr_group'] . "\">BigFix Patch Listing</a></p>\n\n";

    $output .= "<br>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    if ($debug == 'yes') {
      print "mail($email, \"BigFix Server Listing For: " . $current_date . "\", $body, $headers);\n\n";
    } else {
      mail($email, "BigFix Server Listing For: " . $current_date, $body, $headers);
    }
  }

?>
