#!/usr/local/bin/php
<?php
# Script: convert.monitoring.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'no';
  if ($argv[$argc - 1] == 'debug') {
    $debug = 'yes';
  }

  $q_string  = "select int_id,int_openview,inv_manager,int_nagios,int_ping,int_ssh,int_http,int_ftp,int_smtp,int_snmp,int_load,int_uptime,int_cpu,int_swap,int_memory,int_notify,int_hours ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "where int_openview = 1 or int_nagios = 1 ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_interface = mysqli_fetch_array($q_interface)) {

    if ($a_interface['int_openview']) {
      if ($a_interface['int_ping']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=1,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_ssh']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=2,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_http']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=3,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_ftp']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=4,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_smtp']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=5,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_load']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=6,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_uptime']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=7,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_cpu']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=8,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_swap']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=9,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_memory']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=0,mon_type=10,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
    }

    if ($a_interface['int_nagios']) {
      if ($a_interface['int_ping']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=1,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_ssh']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=2,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_http']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=3,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_ftp']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=4,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_smtp']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=5,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_load']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=6,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_uptime']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=7,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_cpu']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=8,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_swap']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=9,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
      if ($a_interface['int_memory']) {
        $q_string = "insert into monitoring set mon_id=null,mon_group=" . $a_interface['inv_manager'] . ",mon_interfaceid=" . $a_interface['int_id'] . ",mon_system=1,mon_type=10,mon_notify=" . $a_interface['int_notify'] . ",mon_hours=" . $a_interface['int_hours'] . " ";
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string);
        }
      }
    }
  }

?>
