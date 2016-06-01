#!/usr/local/bin/php
<?php
# Script: patch.count.php
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

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

  print "Server Name,OS,Target OS,Date Reached\n";

  $q_string  = "select inv_id,inv_name,inv_kernel ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $manager . " and inv_ssh = 1 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $target = 'Unknown';

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
    $a_software = mysql_fetch_array($q_software);

    if (strpos($a_software['sw_software'], 'Red Hat') !== false) {
      $target = 'RHEL 6.8';
      if (strpos($a_software['sw_software'], ' 5 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.1 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.2 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.3 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.4 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.5 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.6 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.7 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.8 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.9 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.10 ') !== false) {
        $target = 'RHEL 5.11';
      }
      if (strpos($a_software['sw_software'], ' 6 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.1 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.2 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.3 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.4 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.5 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.6 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.7 ') !== false) {
        $target = 'RHEL 6.8';
      }
      if (strpos($a_software['sw_software'], ' 7 ') !== false) {
        $target = 'RHEL 7.2';
      }
      if (strpos($a_software['sw_software'], ' 7.1 ') !== false) {
        $target = 'RHEL 7.2';
      }
    }
    if (strpos($a_software['sw_software'], 'CentOS') !== false) {
      $target = 'CentOS 6.8';
      if (strpos($a_software['sw_software'], ' 5 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.1 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.2 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.3 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.4 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.5 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.6 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.7 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.8 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.9 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 5.10 ') !== false) {
        $target = 'CentOS 5.11';
      }
      if (strpos($a_software['sw_software'], ' 6 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.1 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.2 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.3 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.4 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.5 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.6 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 6.7 ') !== false) {
        $target = 'CentOS 6.8';
      }
      if (strpos($a_software['sw_software'], ' 7 ') !== false) {
        $target = 'CentOS 7.2';
      }
      if (strpos($a_software['sw_software'], ' 7.1 ') !== false) {
        $target = 'CentOS 7.2';
      }
    }
    if (strpos($a_software['sw_software'], 'HP-UX') !== false) {
      $target = 'HP-UX B.11.31';
    }
    if (strpos($a_software['sw_software'], 'Solaris') !== false) {
      $target = 'Solaris 11';
    }

    print "\"" . $a_inventory['inv_name'] . "\",\"" . $a_software['sw_software'] . "\",\"" . $target . "\",\"" . $a_inventory['inv_kernel'] . "\"\n";
  }

?>
