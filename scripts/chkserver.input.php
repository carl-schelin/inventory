#!/usr/local/bin/php
<?php
# Script: chkserver.input.php
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

  $configuration = "";

# there are three bits to check for, for verification of management, backup, and monitoring traffic.
# int_management
# int_backup
# int_openview
# 
# int_management is the default one for all management related traffic such as artifactory, etc.
# If no int_backup set, then backup is management
# if no openview set, then no monitoring check

# ===What came before; leaving in just in case===
# creating the management interface bit for the inventory.
# need the actual hostname of the system so the script knows which lines to use
# need the management ip and interface so we know where management traffic should traverse
# use the actual inventory vs the RSDP entries for accuracy

# need to get the interface used for management traffic.
# also need the hostname
# loop through the live servers
# give me the management ip and interface of the 'mgt' interface, or the 'app' interface if there is no 'mgt' interface.
# for the hostname, give me the 'app' interface or if it doesn't exist, the 'mgt' interface
# ===What came before; leaving in just in case===

# just get a list of all the servers
  $q_string  = "select inv_id,inv_name,loc_identity ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_locations on inv_locations.loc_id = inv_inventory.inv_location ";
  $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_manager = " . $GRP_Unix . " ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

# default in case there are no management interfaces
    $hostname = $servername = $a_inv_inventory['inv_name'];
    $monitoringip = '';
    $monitoringface = '';
    $backupip = '';
    $backupface = '';
    $managementip = '';
    $managementface = '';

# interface information
    $q_string  = "select int_addr,int_face,int_management,int_backup,int_openview,zone_zone ";
    $q_string .= "from inv_interface ";
    $q_string .= "left join inv_net_zones on inv_net_zones.zone_id = inv_interface.int_zone ";
    $q_string .= "where int_companyid = " . $a_inv_inventory['inv_id'] . " and int_ip6 = 0 and (int_management = 1 or int_backup = 1 or int_openview = 1) ";
    $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inv_interface) > 0) {
      while ($a_inv_interface = mysqli_fetch_array($q_inv_interface)) {

        if ($a_inv_interface['int_management']) {
          $managementip = $a_inv_interface['int_addr'];
          $managementface = $a_inv_interface['int_face'];
        }

        if ($a_inv_interface['int_openview']) {
          $monitoringip = $a_inv_interface['int_addr'];
          $monitoringface = $a_inv_interface['int_face'];
        }

        if ($a_inv_interface['int_backup']) {
          $backupip = $a_inv_interface['int_addr'];
          $backupface = $a_inv_interface['int_face'];
        }

        $networkzone = $a_inv_interface['zone_zone'];
      }
    }

# need the actual hostname in order for the host to parse the file. 2 is Application interface, desc shows it first. If no App interface, then type 1 aka Management is selected.
    $q_string  = "select int_server ";
    $q_string .= "from inv_interface ";
    $q_string .= "where int_companyid = " . $a_inv_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 or int_type = 2) ";
    $q_string .= "order by int_type desc ";
    $q_intapp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_intapp) > 0) {
      $a_intapp = mysqli_fetch_array($q_intapp);

      $hostname = $a_intapp['int_server'];
    }

    if ($monitoringip != '') {
      $configuration .= $hostname . ":MonitoringIPAddress:" . $monitoringip . "\n";
      $configuration .= $hostname . ":MonitoringInterface:" . $monitoringface . "\n";
      $configuration .= $hostname . ":MonitoringServer:lnmt1cuomnagios1.internal.pri\n";
    }

    if ($backupip != '') {
      $configuration .= $hostname . ":BackupIPAddress:" . $backupip . "\n";
      $configuration .= $hostname . ":BackupInterface:" . $backupface . "\n";
    }

    $configuration .= $hostname . ":ManagementIPAddress:" . $managementip . "\n";
    $configuration .= $hostname . ":ManagementInterface:" . $managementface . "\n";

# adding in network zone for the interface and location, not necessarily needed for the monitoring, etc interface descriptions.
    $configuration .= $hostname . ":NetworkZone:" . $networkzone . "\n";
    $configuration .= $hostname . ":Location:" . $a_inv_inventory['loc_identity'] . "\n";
  }

# check software first for ability to run cron

  $q_string  = "select inv_id,inv_name,sw_software ";
  $q_string .= "from inv_software ";
  $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid = inv_software.sw_id ";
  $q_string .= "left join inv_inventory        on inv_inventory.inv_id                = inv_svr_software.svr_companyid ";
  $q_string .= "where inv_status = 0 and inv_ssh = 1 and sw_software like '%Oracle%' and svr_groupid = " . $GRP_DBAdmins . " ";
  $q_inv_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_software) > 0) {
    while ($a_inv_software = mysqli_fetch_array($q_inv_software)) {

      $hostname = $servername = $a_inv_software['inv_name'];
      $q_string  = "select int_server ";
      $q_string .= "from inv_interface ";
      $q_string .= "where int_companyid = " . $a_inv_software['inv_id'] . " and int_type = 2 ";
      $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_interface) > 0) {
        $a_inv_interface = mysqli_fetch_array($q_inv_interface);
        $servername = $a_inv_interface['int_server'];
      } else {
        $q_string  = "select int_server ";
        $q_string .= "from inv_interface ";
        $q_string .= "where int_companyid = " . $a_inv_software['inv_id'] . " and int_type = 1 ";
        $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inv_interface) > 0) {
          $a_inv_interface = mysqli_fetch_array($q_inv_interface);
          $servername = $a_inv_interface['int_server'];
        }
      }

      $configuration .= $servername . ":Cron:oracle\n";
      $configuration .= $servername . ":Service:oracle\n";
    }
  }

  print $configuration . "\n";

  mysqli_close($db);

?>
