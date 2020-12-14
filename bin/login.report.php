#!/usr/local/bin/php
<?php
# Script: login.report.php
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

  $q_string  = "select rsdp_id,rsdp_function,rsdp_processors,rsdp_memory,rsdp_ossize,os_sysname,rsdp_osmonitor,";
  $q_string .= "rsdp_application,rsdp_opnet,rsdp_datapalette,rsdp_centrify,rsdp_backup ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "where rsdp_platform = " . $GRP_Unix . " or rsdp_platform = " . $GRP_OpsEng . " ";
  $q_string .= "order by os_sysname ";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

    if ($debug == 'yes') {
      print "Processing: " . $a_rsdp_server['os_sysname'] . "\n";
    }

    $hostname = $servername = $a_rsdp_server['os_sysname'];
# type 2 == Application interface
    $q_string  = "select if_name ";
    $q_string .= "from rsdp_interface ";
    $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " and (if_type = 2 or if_type = 1) ";
    if ($debug == 'yes') {
      print "  Query: " . $q_string . "\n";
    }
    $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {
      $servername = $a_rsdp_interface['if_name'];

      if ($debug == 'yes') {
        print "  Processing: " . $servername . "\n";
      }
# here is where the output begins
      if ($a_rsdp_server['rsdp_application'] == $GRP_DBAdmins) {
        $configuration .= $servername . ":Group:dbadmins\n";
        $configuration .= $servername . ":Sudoers:dbadmins\n";
      }
      if ($a_rsdp_server['rsdp_application'] == $GRP_Mobility) {
        $configuration .= $servername . ":Group:mobadmin\n";
        $configuration .= $servername . ":Sudoers:mobadmin\n";
      }
      if ($a_rsdp_server['rsdp_application'] == $GRP_WebApps) {
        $configuration .= $servername . ":Group:webapps\n";
        $configuration .= $servername . ":Sudoers:webapps\n";
      }
      if ($a_rsdp_server['rsdp_application'] == $GRP_SCM) {
        $configuration .= $servername . ":Group:scmadmins\n";
        $configuration .= $servername . ":Sudoers:scmadmins\n";
      }

      $configuration .= $servername . ":Hostname:" . $hostname . ":" . $a_rsdp_server['rsdp_id'] . "\n";
      $configuration .= $servername . ":CPU:"      . $a_rsdp_server['rsdp_processors'] . "\n";
      $configuration .= $servername . ":Memory:"   . $a_rsdp_server['rsdp_memory']     . "\n";
      $configuration .= $servername . ":Disk:"     . $a_rsdp_server['rsdp_ossize']     . "\n";

      $q_string  = "select fs_volume,fs_size ";
      $q_string .= "from rsdp_filesystem ";
      $q_string .= "where fs_rsdp = " . $a_rsdp_server['rsdp_id'];
      $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
        while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
          $configuration .= $servername . ":Disk:" . $a_rsdp_filesystem['fs_size'] . "\n";
        }
      }

# except console (4) or lom (6)
      $q_string  = "select if_ip,if_vlan,if_ipcheck,if_gate ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " and if_type != 4 and if_type != 6 ";
      $q_string .= "order by if_interface";
      $q_rsdp_console = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_console = mysqli_fetch_array($q_rsdp_console)) {

        if ($a_rsdp_console['if_ipcheck']) {
          $configuration .= $servername . ":IP:" . $a_rsdp_console['if_ip'] . ":" . $a_rsdp_console['if_gate'] . "\n";
        }
      }

# set up the service accounts
      if ($a_rsdp_server['rsdp_osmonitor']) {
        $configuration .= $servername . ":Openview\n";
        $configuration .= $servername . ":Service:opc_op\n";
      }
      if ($a_rsdp_server['rsdp_opnet']) {
        $configuration .= $servername . ":OpNet\n";
        $configuration .= $servername . ":Service:opnet\n";
      }
      if ($a_rsdp_server['rsdp_datapalette']) {
        $configuration .= $servername . ":Datapalette\n";
      }
      if ($a_rsdp_server['rsdp_centrify']) {
        $configuration .= $servername . ":Centrify\n";
      }
# if backup checkbox is checked, then encrypted backups are in place so don't plug netbackup in
      if ($a_rsdp_server['rsdp_backup'] == 0) {
        $q_string  = "select bu_retention ";
        $q_string .= "from rsdp_backups ";
        $q_string .= "where bu_rsdp = " . $a_rsdp_server['rsdp_id'] . " ";
        $q_rsdp_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_backups) > 0) {
          $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);

          if ($a_rsdp_backups['bu_retention']) {
            $configuration .= $servername . ":Netbackup\n";
          }
        }
      }
    }
  }

  $configuration .= "\n";


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
  $q_string  = "select inv_id,inv_name,loc_west ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_manager = " . $GRP_Unix . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

# default in case there are no management interfaces
    $hostname = $servername = $a_inventory['inv_name'];
    $monitoringip = '';
    $monitoringface = '';
    $backupip = '';
    $backupface = '';
    $managementip = '';
    $managementface = '';

# interface information
    $q_string  = "select int_addr,int_face,int_management,int_backup,int_openview,zone_zone ";
    $q_string .= "from interface ";
    $q_string .= "left join ip_zones on ip_zones.zone_id = interface.int_zone ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_management = 1 or int_backup = 1 or int_openview = 1) ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_interface) > 0) {
      while ($a_interface = mysqli_fetch_array($q_interface)) {

        if ($a_interface['int_management']) {
          $managementip = $a_interface['int_addr'];
          $managementface = $a_interface['int_face'];
        }

        if ($a_interface['int_openview']) {
          $monitoringip = $a_interface['int_addr'];
          $monitoringface = $a_interface['int_face'];
        }

        if ($a_interface['int_backup']) {
          $backupip = $a_interface['int_addr'];
          $backupface = $a_interface['int_face'];
        }

        $networkzone = $a_interface['zone_zone'];
      }
    }

# need the actual hostname in order for the host to parse the file. 2 is Application interface, desc shows it first. If no App interface, then type 1 aka Management is selected.
    $q_string  = "select int_server ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 or int_type = 2) ";
    $q_string .= "order by int_type desc ";
    $q_intapp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_intapp) > 0) {
      $a_intapp = mysqli_fetch_array($q_intapp);

      $hostname = $a_intapp['int_server'];
    }

    if ($monitoringip != '') {
      $configuration .= $hostname . ":MonitoringIPAddress:" . $monitoringip . "\n";
      $configuration .= $hostname . ":MonitoringInterface:" . $monitoringface . "\n";
      $configuration .= $hostname . ":MonitoringServer:lnmtcodcom1vip.scc911.com\n";
    }

    if ($backupip != '') {
      $configuration .= $hostname . ":BackupIPAddress:" . $backupip . "\n";
      $configuration .= $hostname . ":BackupInterface:" . $backupface . "\n";
    }

    $configuration .= $hostname . ":ManagementIPAddress:" . $managementip . "\n";
    $configuration .= $hostname . ":ManagementInterface:" . $managementface . "\n";

# adding in network zone for the interface and location, not necessarily needed for the monitoring, etc interface descriptions.
    $configuration .= $hostname . ":NetworkZone:" . $networkzone . "\n";
    $configuration .= $hostname . ":Location:" . $a_inventory['loc_west'] . "\n";
  }

# check software first for ability to run cron

  $q_string  = "select inv_id,inv_name,sw_software ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
  $q_string .= "where inv_status = 0 and inv_ssh = 1 and sw_software like '%Oracle%' and sw_group = " . $GRP_DBAdmins . " ";
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_software) > 0) {
    while ($a_software = mysqli_fetch_array($q_software)) {

      $hostname = $servername = $a_software['inv_name'];
      $q_string  = "select int_server ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_type = 2 ";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_interface) > 0) {
        $a_interface = mysqli_fetch_array($q_interface);
        $servername = $a_interface['int_server'];
      } else {
        $q_string  = "select int_server ";
        $q_string .= "from interface ";
        $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_type = 1 ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_interface) > 0) {
          $a_interface = mysqli_fetch_array($q_interface);
          $servername = $a_interface['int_server'];
        }
      }

      $configuration .= $servername . ":Cron:oracle\n";
      $configuration .= $servername . ":Service:oracle\n";
    }
  }

  print $configuration . "\n";

  mysqli_free_result($db);

?>
