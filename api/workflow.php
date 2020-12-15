<?php
# Script: workflow.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "workflow.php";

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if (isset($_GET['server'])) {
    $formVars['server'] = clean($_GET['server'], 60);
  } else {
    $formVars['server'] = '';
  }

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = '';
  }


  $formVars['group'] = 1;


# output
# server:Group:groupname
# server:Sudoers:groupname
# server:Hostname:hostname:rsdp_id
# server:CPU:number
# server:Memory:amount
# server:Disk:size (core size plus extra requested disks)
# server:IP:ip:gateway
# server:DataPalette
# server:Openview
# server:Centrify
# server:OpNet
# server:NetBackup
# server:Service:service account
# server:IPAddressMonitored:ip address
# server:InterfaceMonitored:interface
# server:MonitoringServer:servername
# server:Cron:account (account that is permitted to use cron)




  class Server {
    public $config_appadmin = '';
    public $config_appsudo = '';
    public $config_sysadmin = '';
    public $config_syssudo = '';
    public $config_workflowid = '';
    public $config_cpus = '';
    public $config_memory = '';
    public $config_network = '';
    public $config_disk = '';
    public $config_agent = '';
  }

  class IP_Address {
    public $interface_address = '';
    public $interface_ethernet = '';
    public $interface_gateway = '';
    public $interface_monitored = '';
  }

  class Disk {
    public $disk_size = '';
    public $disk_mount = '';
  }

  class Agent {
    public $agent_name = '';
    public $agent_account = '';
  }

  $where = '';
  $and = "where ";
  if ($formVars['server'] != '') {
    $where .= $and . "os_sysname = \"" . $formVars['server'] . "\" ";
    $and = "and ";
  }
  if ($formVars['group'] != '') {
    $where .= $and . "rsdp_platform = " . $formVars['group'] . " ";
    $and = "and ";
  }

  $countint = 0;
  $countdisk = 0;
  $q_string  = "select rsdp_id,rsdp_function,rsdp_processors,rsdp_memory,rsdp_ossize,os_sysname,rsdp_osmonitor,";
  $q_string .= "rsdp_application,rsdp_opnet,rsdp_datapalette,rsdp_centrify,rsdp_backup,rsdp_platform ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
  $q_string .= $where;
  $q_string .= "order by os_sysname ";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

    $hostname = $servername = $a_rsdp_server['os_sysname'];
# type 2 == Application interface
    $q_string  = "select if_name ";
    $q_string .= "from rsdp_interface ";
    $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " and if_type = 2 ";
    $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_interface) > 0) {
      $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);
      $servername = $a_rsdp_interface['if_name'];
    } else {
# type 1 == Management interface
      $q_string  = "select if_name ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " and if_type = 1 ";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_interface) > 0) {
        $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);
        $servername = $a_rsdp_interface['if_name'];
      }
    }

    $servers[$a_rsdp_server['os_sysname']] = new Server();
    $servers[$a_rsdp_server['os_sysname']]->config_workflowid = $a_rsdp_server['rsdp_id'];
    $servers[$a_rsdp_server['os_sysname']]->config_cpus       = $a_rsdp_server['rsdp_processors'];
    $servers[$a_rsdp_server['os_sysname']]->config_memory     = $a_rsdp_server['rsdp_memory'];

# configure disks
    $index = '/';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = '2';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = '/';

    $index = '/usr';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = '8';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = '/usr';

    $index = 'swap';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = '2';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = 'swap';

    $index = '/home';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = '8';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = '/home';

    $index = '/opt';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = '4';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = '/opt';

    $index = '/tmp';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = '4';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = '/tmp';

    $index = '/var';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = '4';
    $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = '/var';

    $q_string  = "select fs_id,fs_volume,fs_size ";
    $q_string .= "from rsdp_filesystem ";
    $q_string .= "where fs_rsdp = " . $a_rsdp_server['rsdp_id'];
    $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
      while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
        $index = $a_rsdp_filesystem['fs_volume'];
        $servers[$a_rsdp_server['os_sysname']]->config_disk[$index] = new Disk();
        $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_size   = $a_rsdp_filesystem['fs_size'];
        $servers[$a_rsdp_server['os_sysname']]->config_disk[$index]->disk_mount   = $a_rsdp_filesystem['fs_volume'];
      }
    }

# define group information based on the platform owner
    if ($a_rsdp_server['rsdp_platform'] == $GRP_Unix) {
      $servers[$a_rsdp_server['os_sysname']]->config_sysadmin   = 'sysadmins';
      $servers[$a_rsdp_server['os_sysname']]->config_syssudo    = 'sysadmins';
    }

# define group information based on the application owner
    if ($a_rsdp_server['rsdp_application'] == $GRP_DBAdmins) {
      $servers[$a_rsdp_server['os_sysname']]->config_appadmin = 'dbadmins';
      $servers[$a_rsdp_server['os_sysname']]->config_appsudo = 'dbadmins';
    }
    if ($a_rsdp_server['rsdp_application'] == $GRP_Mobility) {
      $servers[$a_rsdp_server['os_sysname']]->config_appadmin = 'mobadmin';
      $servers[$a_rsdp_server['os_sysname']]->config_appsudo = 'mobadmin';
    }
    if ($a_rsdp_server['rsdp_application'] == $GRP_WebApps) {
      $servers[$a_rsdp_server['os_sysname']]->config_appadmin = 'webapps';
      $servers[$a_rsdp_server['os_sysname']]->config_appsudo = 'webapps';
    }
    if ($a_rsdp_server['rsdp_application'] == $GRP_SCM) {
      $servers[$a_rsdp_server['os_sysname']]->config_appadmin = 'scmadmins';
      $servers[$a_rsdp_server['os_sysname']]->config_appsudo = 'scmadmins';
    }

# define interfaces except console (4) or lom (6); can't configure from the OS
    $q_string  = "select if_id,if_ip,if_vlan,if_ipcheck,if_interface,if_gate,if_type ";
    $q_string .= "from rsdp_interface ";
    $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " and if_type != 4 and if_type != 6 ";
    $q_string .= "order by if_interface";
    $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

      if ($a_rsdp_interface['if_ipcheck']) {
        $index = $a_rsdp_interface['if_interface'];
        $servers[$a_rsdp_server['os_sysname']]->config_network[$index] = new IP_Address();

        $servers[$a_rsdp_server['os_sysname']]->config_network[$index]->interface_address   = $a_rsdp_interface['if_ip'];
        $servers[$a_rsdp_server['os_sysname']]->config_network[$index]->interface_ethernet  = $a_rsdp_interface['if_interface'];
        $servers[$a_rsdp_server['os_sysname']]->config_network[$index]->interface_gateway   = $a_rsdp_interface['if_gate'];
        if ($a_rsdp_interface['if_type'] == 1) {
          $servers[$a_rsdp_server['os_sysname']]->config_network[$index]->interface_monitored  = 'Yes';
        } else {
          $servers[$a_rsdp_server['os_sysname']]->config_network[$index]->interface_monitored  = 'No';
        }
      }
    }


# define agents
    if ($a_rsdp_server['rsdp_osmonitor']) {
      $servers[$a_rsdp_server['os_sysname']]->config_agent['openview'] = new Agent();
      $servers[$a_rsdp_server['os_sysname']]->config_agent['openview']->agent_name = 'Openview';
      $servers[$a_rsdp_server['os_sysname']]->config_agent['openview']->agent_account = 'opc_op';
    }
    if ($a_rsdp_server['rsdp_opnet']) {
      $servers[$a_rsdp_server['os_sysname']]->config_agent['opnet'] = new Agent();
      $servers[$a_rsdp_server['os_sysname']]->config_agent['opnet']->agent_name = 'OpNet';
      $servers[$a_rsdp_server['os_sysname']]->config_agent['opnet']->agent_account = 'opnet';
    }
    if ($a_rsdp_server['rsdp_datapalette']) {
      $servers[$a_rsdp_server['os_sysname']]->config_agent['datapalette'] = new Agent();
      $servers[$a_rsdp_server['os_sysname']]->config_agent['datapalette']->agent_name = 'Datapalette';
      $servers[$a_rsdp_server['os_sysname']]->config_agent['datapalette']->agent_account = 'none';
    }
    if ($a_rsdp_server['rsdp_centrify']) {
      $servers[$a_rsdp_server['os_sysname']]->config_agent['centrify'] = new Agent();
      $servers[$a_rsdp_server['os_sysname']]->config_agent['centrify']->agent_name = 'Centrify';
      $servers[$a_rsdp_server['os_sysname']]->config_agent['centrify']->agent_account = 'none';
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
          $servers[$a_rsdp_server['os_sysname']]->config_agent['netbackup'] = new Agent();
          $servers[$a_rsdp_server['os_sysname']]->config_agent['netbackup']->agent_name = 'Netbackup';
          $servers[$a_rsdp_server['os_sysname']]->config_agent['netbackup']->agent_account = 'none';
        }
      }
    }

# check software first for ability to run cron
    $q_string  = "select inv_id,inv_name,sw_software ";
    $q_string .= "from software ";
    $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
    $q_string .= "where inv_manager = 1 and inv_status = 0 and sw_software like '%Oracle%' and sw_group = " . $GRP_DBAdmins . " ";
    $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_software) > 0) {
      while ($a_software = mysqli_fetch_array($q_software)) {

        $configuration .= $servername . ":Cron:oracle\n";
        $configuration .= $servername . ":Service:oracle\n";
      }
    }
  }

  echo json_encode($servers);

?>
