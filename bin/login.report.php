#!/usr/local/bin/php
<?php
# Script: login.report.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# 

# root.cron: # Denise Durgee requesting spreadsheet output
# root.cron: 30 6 * * * /usr/local/bin/php /usr/local/httpd/bin/ddurgee.spreadsheet.php > /usr/local/httpd/htsecure/reports/ddurgee.spreadsheet.csv 2>/dev/null

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# Unix wants a csv type output
# text of what type of line it is
# followed by the value
# "Hostname","CPU","CPU"
# "Hostname","Memory","Memory"
# "Hostname","Disks","Disks"
# "Hostname","IP","IP"

  $configuration = "";

  $q_string  = "select rsdp_id,rsdp_function,rsdp_processors,rsdp_memory,rsdp_ossize,os_sysname,rsdp_osmonitor,";
  $q_string .= "rsdp_application,rsdp_opnet,rsdp_datapalette,rsdp_centrify,rsdp_backup ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "where rsdp_platform = 1 ";
  $q_string .= "order by os_sysname ";
  $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_rsdp_server = mysql_fetch_array($q_rsdp_server)) {

    $hostname = $servername = $a_rsdp_server['os_sysname'];
# type 2 == Application interface
    $q_string  = "select if_name ";
    $q_string .= "from rsdp_interface ";
    $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " and (if_type = 2 or if_type = 1) ";
    $q_rsdp_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_rsdp_interface = mysql_fetch_array($q_radp_interface)) {
      $servername = $a_rsdp_interface['if_name'];

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
      $q_rsdp_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_filesystem) > 0) {
        while ($a_rsdp_filesystem = mysql_fetch_array($q_rsdp_filesystem)) {
          $configuration .= $servername . ":Disk:" . $a_rsdp_filesystem['fs_size'] . "\n";
        }
      }

# except console (4) or lom (6)
      $q_string  = "select if_ip,if_vlan,if_ipcheck,if_gate ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " and if_type != 4 and if_type != 6 ";
      $q_string .= "order by if_interface";
      $q_rsdp_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_rsdp_interface = mysql_fetch_array($q_rsdp_interface)) {

        if ($a_rsdp_interface['if_ipcheck']) {
          $configuration .= $servername . ":IP:" . $a_rsdp_interface['if_ip'] . ":" . $a_rsdp_interface['if_gate'] . "\n";
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
        $q_rsdp_backups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        if (mysql_num_rows($q_rsdp_backups) > 0) {
          $a_rsdp_backups = mysql_fetch_array($q_rsdp_backups);

          if ($a_rsdp_backups['bu_retention']) {
            $configuration .= $servername . ":Netbackup\n";
          }
        }
      }
    }
  }

  $configuration .= "\n";

# now let's pull from the inventory itself to get more information
# unix only (inv_manager = 1)


# if interface is identified as management, set up the management interface and IP properly
# for the management route check
  $q_string  = "select inv_id,inv_name,int_addr,int_face ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 and int_type = 1 and int_ip6 = 0 and int_addr != '' ";
  $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_interface = mysql_fetch_array($q_interface)) {

    $hostname = $servername = $a_interface['inv_name'];
# type 2 == Application interface
    $q_string  = "select int_server ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_interface['inv_id'] . " and int_type = 2 ";
    $q_face = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_face) > 0) {
      $a_face = mysql_fetch_array($q_face);
      $servername = $a_face['int_server'];
    } else {
# type 1 == Management interface
      $q_string  = "select int_server ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_interface['inv_id'] . " and int_type = 1 ";
      $q_face = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_face) > 0) {
        $a_face = mysql_fetch_array($q_face);
        $servername = $a_face['int_server'];
      }
    }

    $configuration .= $servername . ":IPAddressMonitored:" . $a_interface['int_addr'] . "\n";
    $configuration .= $servername . ":InterfaceMonitored:" . $a_interface['int_face'] . "\n";
    $configuration .= $servername . ":MonitoringServer:lnmtcodcom1vip.scc911.com\n";
  }

# check software first for ability to run cron

  $q_string  = "select inv_id,inv_name,sw_software ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 and sw_software like '%Oracle%' and sw_group = " . $GRP_DBAdmins . " ";
  $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_software) > 0) {
    while ($a_software = mysql_fetch_array($q_software)) {

      $hostname = $servername = $a_software['inv_name'];
      $q_string  = "select int_server ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_type = 2 ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_interface) > 0) {
        $a_interface = mysql_fetch_array($q_interface);
        $servername = $a_interface['int_server'];
      } else {
        $q_string  = "select int_server ";
        $q_string .= "from interface ";
        $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_type = 1 ";
        $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        if (mysql_num_rows($q_interface) > 0) {
          $a_interface = mysql_fetch_array($q_interface);
          $servername = $a_interface['int_server'];
        }
      }

      $configuration .= $servername . ":Cron:oracle\n";
      $configuration .= $servername . ":Service:oracle\n";
    }
  }

  print $configuration . "\n";

?>
