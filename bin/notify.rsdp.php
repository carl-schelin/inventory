<?php
# Script: notify.rsdp.php
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

  $debug = 'yes';
  $debug = 'no';

  $headers  = "From: Server Build Workflow <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

# tasks if a number exists, use the next number for who we're waiting on.
# task is:   Waiting on:
# blank    - rsdp_requestor
# 1        - rsdp_platformpoc
# 2        - rsdp_sanpoc if physical
# 3        - rsdp_networkpoc
# 4        - rsdp_virtpoc if virtual or rsdp_dcpoc if physical
# 5        - rsdp_platformpoc
# 6        - rsdp_platformpoc
# 7        - rsdp_platformpoc
# 8        - rsdp_platformpoc
# 9        - rsdp_sanpoc if physical
# 10       - rsdp_platformpoc
# 11       - rsdp_monitorpoc, rsdp_backuppoc, and rsdp_apppoc; parallel tasks
# 12       - rsdp_backuppoc and rsdp_apppoc, ultimately rsdp_monitorpoc if all three are done
# 13       - rsdp_monitorpoc and rsdp_apppoc, ultimately rsdp_monitorpoc if all three are done
# 14       - rsdp_backuppoc and rsdpmonitorpoc, ultimately rsdp_monitorpoc if all three are done
# 15       - rsdp_monitorpoc
# 16       - rsdp_apppoc
# 17       - rsdp_platformpoc

# configure two arrays; one for groups and one for users.
# add gid to groupid[rsdp]
# add uid to userid[rsdp]
# if user disabled, use group of user instead
  $groupid = array();
  $userid = array();
# then sort when done and build email from each individual user.

# pull all rsdp projects from rsdp
# any project where there's an rsdp id but no status = 18 (completed) is an incomplete project

  $q_string  = "select rsdp_id,rsdp_requestor,rsdp_platformspoc,rsdp_sanpoc,rsdp_networkpoc,rsdp_virtpoc,";
  $q_string .= "rsdp_dcpoc,rsdp_srpoc,rsdp_monitorpoc,rsdp_apppoc,rsdp_backuppoc,rsdp_platform,";
  $q_string .= "rsdp_application,rsdp_function ";
  $q_string .= "from rsdp_server ";
  $q_string .= "order by rsdp_id ";
  $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_rsdp_server = mysql_fetch_array($q_rsdp_server)) {

# need to get the last 
    $q_string  = "select st_step ";
    $q_string .= "from rsdp_status ";
    $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " ";
    $q_string .= "order by st_step desc limit 1 ";
    $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_rsdp_status = mysql_fetch_array($q_rsdp_status);

    if ($a_rsdp_status['st_step'] != 18) {
      $waitingon = '';
      $disabled = '';
      $virtual = 1;

      $q_string  = "select mod_virtual ";
      $q_string .= "from rsdp_platform ";
      $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
      $q_string .= "where pf_rsdp = " . $a_rsdp_server['rsdp_id'] . " ";
      $q_rsdp_platform = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_platform) > 0) {
        $a_rsdp_platform = mysql_fetch_array($q_rsdp_platform);

        $virtual = $a_rsdp_platform['mod_virtual'];
      }

      if ($a_rsdp_status['st_step'] == '') {

        $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
        $q_string .= "from users ";
        $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_requestor'] . " ";
        $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_users = mysql_fetch_array($q_users);

        if ($a_users['usr_disabled']) {
          $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
        } else {
          $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
        }
      }

      if ($a_rsdp_status['st_step'] == '1' || $a_rsdp_status['st_step'] == '8' || $a_rsdp_status['st_step'] == '10' || $a_rsdp_status['st_step'] == '16') {

        if ($a_rsdp_server['rsdp_platformspoc'] == 0) {
          $groupid[$a_rsdp_server['rsdp_id']] = $a_rsdp_server['rsdp_platform'];
        } else {
          $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_platformspoc'] . " ";
          $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_users = mysql_fetch_array($q_users);

          if ($a_users['usr_disabled']) {
            $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
          } else {
            $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
          }
        }
      }

      if ($a_rsdp_status['st_step'] == '2' || $a_rsdp_status['st_step'] == '9') {

        if ($a_rsdp_server['rsdp_sanpoc'] == 0) {
          $groupid[$a_rsdp_server['rsdp_id']] = $GRP_SAN;
        } else {
          $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_sanpoc'] . " ";
          $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_users = mysql_fetch_array($q_users);

          if ($a_users['usr_disabled']) {
            $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
          } else {
            $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
          }
        }
      }

      if ($a_rsdp_status['st_step'] == '3') {

        if ($a_rsdp_server['rsdp_networkpoc'] == 0) {
          $groupid[$a_rsdp_server['rsdp_id']] = $GRP_Networking;
        } else {
          $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_networkpoc'] . " ";
          $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_users = mysql_fetch_array($q_users);

          if ($a_users['usr_disabled']) {
            $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
          } else {
            $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
          }
        }
      }

# if virtual then virtualization, if physical then data center. 5, 6, 7, and 8.
      if ($a_rsdp_status['st_step'] == '4' || $a_rsdp_status['st_step'] == '5' || $a_rsdp_status['st_step'] == '6' || $a_rsdp_status['st_step'] == '7') {

        if ($virtual) {
          if ($a_rsdp_server['rsdp_virtpoc'] == 0) {
            $groupid[$a_rsdp_server['rsdp_id']] = $GRP_Virtualization;
          } else {
            $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
            $q_string .= "from users ";
            $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_virtpoc'] . " ";
            $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            $a_users = mysql_fetch_array($q_users);

            if ($a_users['usr_disabled']) {
              $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
            } else {
              $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
            }
          }
        } else {
          if ($a_rsdp_server['rsdp_dcpoc'] == 0) {
            $groupid[$a_rsdp_server['rsdp_id']] = $GRP_DataCenter;
          } else {
            $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
            $q_string .= "from users ";
            $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_dcpoc'] . " ";
            $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            $a_users = mysql_fetch_array($q_users);

            if ($a_users['usr_disabled']) {
              $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
            } else {
              $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
            }
          }
        }
      }


      if ($a_rsdp_status['st_step'] == '11') {

        if ($a_rsdp_server['rsdp_backuppoc'] == 0) {
          $groupid[$a_rsdp_server['rsdp_id']] = $GRP_Backups;
        } else {
          $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_backuppoc'] . " ";
          $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_users = mysql_fetch_array($q_users);

          if ($a_users['usr_disabled']) {
            $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
          } else {
            $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
          }
        }
      }

      if ($a_rsdp_status['st_step'] == '12' || $a_rsdp_status['st_step'] == '14') {

        if ($a_rsdp_server['rsdp_monitoringpoc'] == 0) {
          $groupid[$a_rsdp_server['rsdp_id']] = $GRP_Monitoring;
        } else {
          $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_monitoringpoc'] . " ";
          $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_users = mysql_fetch_array($q_users);

          if ($a_users['usr_disabled']) {
            $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
          } else {
            $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
          }
        }
      }


      if ($a_rsdp_status['st_step'] == '13' || $a_rsdp_status['st_step'] == '15') {

        if ($a_rsdp_server['rsdp_applicationpoc'] == 0) {
          $groupid[$a_rsdp_server['rsdp_id']] = $a_rsdp_server['rsdp_application'];
        } else {
          $q_string  = "select usr_id,usr_name,usr_disabled,usr_group ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_applicationpoc'] . " ";
          $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_users = mysql_fetch_array($q_users);

          if ($a_users['usr_disabled']) {
            $groupid[$a_rsdp_server['rsdp_id']] = $a_users['usr_group'];
          } else {
            $userid[$a_rsdp_server['rsdp_id']] = $a_users['usr_id'];
          }
        }
      }
    }
  }
# add in email build.
  asort($groupid);

# get just enough information to provide a link to the server
# server name, os, project.
# get group email and name
# get user email and name

  $group = 0;
  foreach ($groupid as $key => $val) {

    if ($group != $val) {
      $output .= "</table>\n";
      $output .= "<p><strong>Note:</strong> This mailbox is not monitored. Do not reply.</p>\n";

      if ($group != 0) {
        print "Send email\n";
        if ($debug == 'yes') {
          mail($Sitedev, "Server Build Workflow (RSDP) Status", $output, $headers);
        } else {
          if ($a_groups['grp_email'] != '') {
            mail($a_groups['grp_email'], "Server Build Workflow (RSDP) Status", $output, $headers);
          }
        }
      }

      $q_string  = "select grp_name,grp_email ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $val . " ";
      $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_groups = mysql_fetch_array($q_groups);

      $output = "<p>The following Server Build Workflow (RSDP) servers are waiting for someone from your group to mark the task as complete.</p>\n";
      $output .= "<p><strong>Note:</strong> If the task has been completed, either click on the server and navigate to your task and mark it completed, or contact Carl Schelin to remove the task.</p>\n";
      $output .= "<p><strong>NOTE:</strong> This is intended to be a daily run to make sure you or your team is aware of pending RSDP tasks. This is a <strong>first</strong> run at the report. ";
      $output .= "I'll run it again weekly in order to clean up the report. Once the old RSDP entries have been cleared, this will turn into a daily report.</p>\n";
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">" . $a_groups['grp_name'] . "</th>\n";
      $output .= "</tr>\n";

      $group = $val;
    }

    $q_string  = "select os_sysname,os_software "; 
    $q_string .= "from rsdp_osteam ";
    $q_string .= "where os_rsdp = " . $key . " ";
    $q_rsdp_osteam = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_rsdp_osteam) > 0) {
      $a_rsdp_osteam = mysql_fetch_array($q_rsdp_osteam);

      $q_string  = "select os_software ";
      $q_string .= "from operatingsystem ";
      $q_string .= "where os_id = " . $a_rsdp_osteam['os_software'] . " ";
      $q_operatingsystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_operatingsystem = mysql_fetch_array($q_operatingsystem);

    } else {
      $a_rsdp_osteam['os_sysname'] = 'Unknown';
      $a_operatingsystem['os_software'] = 'Unknown';
    }

    $q_string  = "select rsdp_function,prod_name ";
    $q_string .= "from rsdp_server ";
    $q_string .= "left join products on products.prod_id = rsdp_server.rsdp_product ";
    $q_string .= "where rsdp_id = " . $key . " ";
    $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

    $output .= "<tr style=\"background-color: #bced91; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><a href=\"" . $RSDProot . "/tasks.php?id=" . $key . "\">" . $a_rsdp_osteam['os_sysname'] . "</a></td>\n";
    $output .= "  <td>" . $a_rsdp_server['rsdp_function'] . "</td>\n";
    $output .= "  <td>" . $a_operatingsystem['os_software'] . "</td>\n";
    $output .= "  <td>" . $a_rsdp_server['prod_name'] . "</td>\n";
    $output .= "</tr>\n";


  }

# now do the users
  asort($userid);


  $users = 0;
  foreach ($userid as $key => $val) {

    if ($users != $val) {
      $output .= "</table>\n";
      $output .= "<p><strong>Note:</strong> This mailbox is not monitored. Do not reply.</p>\n";

      if ($users != 0) {
        if ($debug == 'yes') {
          mail($Sitedev, "Server Build Workflow (RSDP) Status", $output, $headers);
        } else {
          if ($a_users['usr_email'] != '') {
            mail($a_users['usr_email'], "Server Build Workflow (RSDP) Status", $output, $headers);
          }
        }
      }

      $q_string  = "select usr_last,usr_first,usr_email ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $val . " ";
      $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_users = mysql_fetch_array($q_users);

      $output = "<p>The following Server Build Workflow (RSDP) servers are waiting for you to mark your task as complete.</p>\n";
      $output .= "<p><strong>Note:</strong> If the task has been completed, either click on the server and navigate to your task and mark it completed, or contact Carl Schelin to remove the task.</p>\n";
      $output .= "<p><strong>NOTE:</strong> This is intended to be a daily run to make sure you or your team is aware of pending RSDP tasks. This is a <strong>first</strong> run at the report. ";
      $output .= "I'll run it again weekly in order to clean up the report. Once the old RSDP entries have been cleared, this will turn into a daily report.</p>\n";
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</th>\n";
      $output .= "</tr>\n";

      $users = $val;
    }

    $q_string  = "select os_sysname,os_software "; 
    $q_string .= "from rsdp_osteam ";
    $q_string .= "where os_rsdp = " . $key . " ";
    $q_rsdp_osteam = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_rsdp_osteam) > 0) {
      $a_rsdp_osteam = mysql_fetch_array($q_rsdp_osteam);

      $q_string  = "select os_software ";
      $q_string .= "from operatingsystem ";
      $q_string .= "where os_id = " . $a_rsdp_osteam['os_software'] . " ";
      $q_operatingsystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_operatingsystem = mysql_fetch_array($q_operatingsystem);

    } else {
      $a_rsdp_osteam['os_sysname'] = 'Unknown';
      $a_operatingsystem['os_software'] = 'Unknown';
    }

    $q_string  = "select rsdp_function,prod_name ";
    $q_string .= "from rsdp_server ";
    $q_string .= "left join products on products.prod_id = rsdp_server.rsdp_product ";
    $q_string .= "where rsdp_id = " . $key . " ";
    $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

    $output .= "<tr style=\"background-color: #bced91; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><a href=\"" . $RSDProot . "/tasks.php?id=" . $key . "\">" . $a_rsdp_osteam['os_sysname'] . "</a></td>\n";
    $output .= "  <td>" . $a_rsdp_server['rsdp_function'] . "</td>\n";
    $output .= "  <td>" . $a_operatingsystem['os_software'] . "</td>\n";
    $output .= "  <td>" . $a_rsdp_server['prod_name'] . "</td>\n";
    $output .= "</tr>\n";

  }

?>
