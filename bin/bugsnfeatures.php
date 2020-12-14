#!/usr/local/bin/php
<?php
# Script: bugsnfeatures.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve the list of open bug and feature requests and email to interested parties.

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# In debug mode, it prints out the email vs sending it.
  $debug = 'yes';
  $debug = 'no';

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";
  $color[2] = "yellow";
  $color[3] = "#fa8072";

  $output  = "<html>\n";
  $output .= "<body>\n";


  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">Open Bugs</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Module</th>\n";
  $output .= "  <th>Severity</th>\n";
  $output .= "  <th>Priority</th>\n";
  $output .= "  <th>Discovered</th>\n";
  $output .= "  <th>Opened By</th>\n";
  $output .= "  <th>Subject</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select mod_name,sev_name,bug_priority,bug_discovered,usr_first,usr_last,bug_subject ";
  $q_string .= "from bugs ";
  $q_string .= "left join modules on modules.mod_id   = bugs.bug_module ";
  $q_string .= "left join severity on severity.sev_id = bugs.bug_severity ";
  $q_string .= "left join users on users.usr_id       = bugs.bug_openby ";
  $q_string .= "where bug_closeby = 0 ";
  $q_string .= "order by bug_severity,bug_priority,bug_discovered ";
  $q_bugs = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_bugs) > 0) {
    while ($a_bugs = mysqli_fetch_array($q_bugs)) {

      $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_bugs['mod_name']                               . "</td>\n";
      $output .= "  <td>" . $a_bugs['sev_name']                               . "</td>\n";
      $output .= "  <td>" . $a_bugs['bug_priority']                           . "</td>\n";
      $output .= "  <td>" . $a_bugs['bug_discovered']                         . "</td>\n";
      $output .= "  <td>" . $a_bugs['usr_last'] . ", " . $a_bugs['usr_first'] . "</td>\n";
      $output .= "  <td>" . $a_bugs['bug_subject']                            . "</td>\n";
      $output .= "</tr>\n";
    }
  } else {
    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">\n";
    $output .= "  <td>No outstanding bugs remain: Congratulations!</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>\n\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">Open Features</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Module</th>\n";
  $output .= "  <th>Severity</th>\n";
  $output .= "  <th>Priority</th>\n";
  $output .= "  <th>Discovered</th>\n";
  $output .= "  <th>Opened By</th>\n";
  $output .= "  <th>Subject</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select mod_name,sev_name,feat_priority,feat_discovered,usr_first,usr_last,feat_subject ";
  $q_string .= "from features ";
  $q_string .= "left join modules on modules.mod_id   = features.feat_module ";
  $q_string .= "left join severity on severity.sev_id = features.feat_severity ";
  $q_string .= "left join users on users.usr_id       = features.feat_openby ";
  $q_string .= "where feat_closeby = 0 ";
  $q_string .= "order by feat_severity,feat_priority,feat_discovered ";
  $q_features = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_features) > 0) {
    while ($a_features = mysqli_fetch_array($q_features)) {

      $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_features['mod_name']                                   . "</td>\n";
      $output .= "  <td>" . $a_features['sev_name']                                   . "</td>\n";
      $output .= "  <td>" . $a_features['feat_priority']                              . "</td>\n";
      $output .= "  <td>" . $a_features['feat_discovered']                            . "</td>\n";
      $output .= "  <td>" . $a_features['usr_last'] . ", " . $a_features['usr_first'] . "</td>\n";
      $output .= "  <td>" . $a_features['feat_subject']                               . "</td>\n";
      $output .= "</tr>\n";
    }
  } else {
    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">\n";
    $output .= "  <td>No outstanding features remain: Congratulations!</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>\n\n";

  $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

  $output .= "</body>\n";
  $output .= "</html>\n";

  $body = $output;

  if ($debug == 'yes') {
    print "mail($Sitedev, \"Bug and Feature Report\", $body, $headers);\n";
  } else {
    mail($Sitedev, "Bug and Feature Report", $body, $headers);
  }

  mysqli_free_result($db);

?>
