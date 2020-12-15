<?php
# Script: initial.dup.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('2');

  $package = "initial.dup.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['rsdp']              = clean($_GET['rsdp'],             10);
  $formVars['chk_filesystem']    = clean($_GET['chk_filesystem'],   10);
  $formVars['chk_ipaddr']        = clean($_GET['chk_ipaddr'],       10);
  $formVars['chk_san1']          = clean($_GET['chk_san1'],         10);
  $formVars['chk_net']           = clean($_GET['chk_net'],          10);
  $formVars['chk_virt']          = clean($_GET['chk_virt'],         10);
  $formVars['chk_sys1']          = clean($_GET['chk_sys1'],         10);
  $formVars['chk_san2']          = clean($_GET['chk_san2'],         10);
  $formVars['chk_sys2']          = clean($_GET['chk_sys2'],         10);
  $formVars['chk_backup']        = clean($_GET['chk_backup'],       10);
  $formVars['chk_mon1']          = clean($_GET['chk_mon1'],         10);
  $formVars['chk_app1']          = clean($_GET['chk_app1'],         10);
  $formVars['chk_mon2']          = clean($_GET['chk_mon2'],         10);
  $formVars['chk_app2']          = clean($_GET['chk_app2'],         10);
  $formVars['chk_infosec']       = clean($_GET['chk_infosec'],      10);

  if ($formVars['rsdp'] == '') {
    $formVars['rsdp'] = 0;
  }
  if ($formVars['chk_filesystem'] == 'true') {
    $formVars['chk_filesystem'] = 1;
  } else {
    $formVars['chk_filesystem'] = 0;
  }
  if ($formVars['chk_ipaddr'] == 'true') {
    $formVars['chk_ipaddr'] = 1;
  } else {
    $formVars['chk_ipaddr'] = 0;
  }
  if ($formVars['chk_san1`'] == 'true') {
    $formVars['chk_san1`'] = 1;
  } else {
    $formVars['chk_san1`'] = 0;
  }
  if ($formVars['chk_net'] == 'true') {
    $formVars['chk_net'] = 1;
  } else {
    $formVars['chk_net'] = 0;
  }
  if ($formVars['chk_virt'] == 'true') {
    $formVars['chk_virt'] = 1;
  } else {
    $formVars['chk_virt'] = 0;
  }
  if ($formVars['chk_sys1'] == 'true') {
    $formVars['chk_sys1'] = 1;
  } else {
    $formVars['chk_sys1'] = 0;
  }
  if ($formVars['chk_san2'] == 'true') {
    $formVars['chk_san2'] = 1;
  } else {
    $formVars['chk_san2'] = 0;
  }
  if ($formVars['chk_sys2'] == 'true') {
    $formVars['chk_sys2'] = 1;
  } else {
    $formVars['chk_sys2'] = 0;
  }
  if ($formVars['chk_backup'] == 'true') {
    $formVars['chk_backup'] = 1;
  } else {
    $formVars['chk_backup'] = 0;
  }
  if ($formVars['chk_mon1'] == 'true') {
    $formVars['chk_mon1'] = 1;
  } else {
    $formVars['chk_mon1'] = 0;
  }
  if ($formVars['chk_app1'] == 'true') {
    $formVars['chk_app1'] = 1;
  } else {
    $formVars['chk_app1'] = 0;
  }
  if ($formVars['chk_mon2'] == 'true') {
    $formVars['chk_mon2'] = 1;
  } else {
    $formVars['chk_mon2'] = 0;
  }
  if ($formVars['chk_app2'] == 'true') {
    $formVars['chk_app2'] = 1;
  } else {
    $formVars['chk_app2'] = 0;
  }
  if ($formVars['chk_infosec'] == 'true') {
    $formVars['chk_infosec'] = 1;
  } else {
    $formVars['chk_infosec'] = 0;
  }

######
# Get the 'copy from' RSDP server information
######
  $q_string  = "select rsdp_requestor,rsdp_location,rsdp_product,rsdp_completion,rsdp_magic,rsdp_project,rsdp_platform,rsdp_application,";
  $q_string .= "rsdp_service,rsdp_vendor,rsdp_function,rsdp_processors,rsdp_memory,rsdp_ossize,rsdp_osmonitor,rsdp_appmonitor,rsdp_datapalette,";
  $q_string .= "rsdp_opnet,rsdp_newrelic,rsdp_centrify,rsdp_backup,rsdp_platformspoc,rsdp_sanpoc,rsdp_networkpoc,rsdp_virtpoc,rsdp_dcpoc,rsdp_srpoc,";
  $q_string .= "rsdp_monitorpoc,rsdp_apppoc,rsdp_backuppoc ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

######
# Build the 'copy to' RSDP server information: First interaction with rsdp_server (initializing new record)
######
  $q_server = "insert into rsdp_server set " . 
    "rsdp_id           =   " . "NULL"                              . "," .
    "rsdp_requestor    =   " . $a_rsdp_server['rsdp_requestor']    . "," .
    "rsdp_location     =   " . $a_rsdp_server['rsdp_location']     . "," .
    "rsdp_product      =   " . $a_rsdp_server['rsdp_product']      . "," .
    "rsdp_completion   = \"" . $a_rsdp_server['rsdp_completion']   . "\"," .
    "rsdp_magic        =   " . $a_rsdp_server['rsdp_magic']        . "," .
    "rsdp_project      =   " . $a_rsdp_server['rsdp_project']      . "," .
    "rsdp_platformspoc =   " . $a_rsdp_server["rsdp_platformspoc"] . "," .
    "rsdp_sanpoc       =   " . $a_rsdp_server["rsdp_sanpoc"]       . "," .
    "rsdp_networkpoc   =   " . $a_rsdp_server["rsdp_networkpoc"]   . "," .
    "rsdp_virtpoc      =   " . $a_rsdp_server["rsdp_virtpoc"]      . "," .
    "rsdp_dcpoc        =   " . $a_rsdp_server["rsdp_dcpoc"]        . "," .
    "rsdp_srpoc        =   " . $a_rsdp_server["rsdp_srpoc"]        . "," .
    "rsdp_monitorpoc   =   " . $a_rsdp_server["rsdp_monitorpoc"]   . "," .
    "rsdp_apppoc       =   " . $a_rsdp_server["rsdp_apppoc"]       . "," .
    "rsdp_backuppoc    =   " . $a_rsdp_server["rsdp_backuppoc"]    . "," .
    "rsdp_platform     =   " . $a_rsdp_server['rsdp_platform']     . "," .
    "rsdp_application  =   " . $a_rsdp_server['rsdp_application']  . "," .
    "rsdp_service      =   " . $a_rsdp_server['rsdp_service']      . "," .
    "rsdp_vendor       =   " . $a_rsdp_server['rsdp_vendor']       . "," .
    "rsdp_function     = \"" . $a_rsdp_server['rsdp_function']     . "\"," .
    "rsdp_processors   =   " . $a_rsdp_server['rsdp_processors']   . "," .
    "rsdp_memory       = \"" . $a_rsdp_server['rsdp_memory']       . "\"," .
    "rsdp_ossize       = \"" . $a_rsdp_server['rsdp_ossize']       . "\"," .
    "rsdp_osmonitor    =   " . $a_rsdp_server['rsdp_osmonitor']    . "," .
    "rsdp_appmonitor   =   " . $a_rsdp_server['rsdp_appmonitor']   . "," .
    "rsdp_datapalette  =   " . $a_rsdp_server['rsdp_datapalette']  . "," .
    "rsdp_opnet        =   " . $a_rsdp_server['rsdp_opnet']        . "," .
    "rsdp_newrelic     =   " . $a_rsdp_server['rsdp_newrelic']     . "," .
    "rsdp_centrify     =   " . $a_rsdp_server['rsdp_centrify']     . "," .
    "rsdp_backup       =   " . $a_rsdp_server['rsdp_backup'];

  mysqli_query($db, $q_server) or die($q_server . ": " . mysqli_error($db));

######
# Get the 'copy to' RSDP ID
######
  $rsdp = last_insert_id($db);

######
# Get the 'copy from' backup information: First interaction with rsdp_backups (initialization of record)
######
  $q_string  = "select bu_start,bu_include,bu_retention,bu_sunday,bu_monday,bu_tuesday,bu_wednesday,bu_thursday,bu_friday,bu_saturday,";
  $q_string .= "bu_suntime,bu_montime,bu_tuetime,bu_wedtime,bu_thutime,bu_fritime,bu_sattime ";
  $q_string .= "from rsdp_backups ";
  $q_string .= "where bu_rsdp = " . $formVars['rsdp'];
  $q_rsdp_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);

######
# Build the 'copy to' backup information record
######
  $q_backup = "insert into rsdp_backups set " . 
    "bu_id        =   " . "NULL"                          . "," .
    "bu_rsdp      =   " . $rsdp                           . "," .
    "bu_start     = \"" . $a_rsdp_backups['bu_start']     . "\"," .
    "bu_include   =   " . $a_rsdp_backups['bu_include']   . "," .
    "bu_retention =   " . $a_rsdp_backups['bu_retention'] . "," .
    "bu_sunday    =   " . $a_rsdp_backups['bu_sunday']    . "," .
    "bu_monday    =   " . $a_rsdp_backups['bu_monday']    . "," .
    "bu_tuesday   =   " . $a_rsdp_backups['bu_tuesday']   . "," .
    "bu_wednesday =   " . $a_rsdp_backups['bu_wednesday'] . "," .
    "bu_thursday  =   " . $a_rsdp_backups['bu_thursday']  . "," .
    "bu_friday    =   " . $a_rsdp_backups['bu_friday']    . "," .
    "bu_saturday  =   " . $a_rsdp_backups['bu_saturday']  . "," .
    "bu_suntime   = \"" . $a_rsdp_backups['bu_suntime']   . "\"," .
    "bu_montime   = \"" . $a_rsdp_backups['bu_montime']   . "\"," .
    "bu_tuetime   = \"" . $a_rsdp_backups['bu_tuetime']   . "\"," .
    "bu_wedtime   = \"" . $a_rsdp_backups['bu_wedtime']   . "\"," .
    "bu_thutime   = \"" . $a_rsdp_backups['bu_thutime']   . "\"," .
    "bu_fritime   = \"" . $a_rsdp_backups['bu_fritime']   . "\"," .
    "bu_sattime   = \"" . $a_rsdp_backups['bu_sattime']   . "\"";

  mysqli_query($db, $q_backup) or die($q_backup . ": " . mysqli_error($db));


######
# Get the 'copy from' Comments: First interaction with rsdp_comments
######
  $q_string  = "select com_task,com_text,com_timestamp,com_user ";
  $q_string .= "from rsdp_comments ";
  $q_string .= "where com_rsdp = " . $formVars['rsdp'];
  $q_rsdp_comments = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_comments = mysqli_fetch_array($q_rsdp_comments)) {

######
# Build the 'copy to' Comment records
######
    $q_comments = "insert into rsdp_comments set " . 
      "com_id        =   " . "NULL"                            . "," .
      "com_rsdp      =   " . $rsdp                             . "," .
      "com_task      = \"" . $a_rsdp_comments['com_task']      . "\"," .
      "com_text      = \"" . $a_rsdp_comments['com_text']      . "\"," .
      "com_timestamp = \"" . $a_rsdp_comments['com_timestamp'] . "\"," .
      "com_user      =   " . $a_rsdp_comments['com_user'];

    mysqli_query($db, $q_comments) or die($q_comments . ": " . mysqli_error($db));
  }


######
# Get the 'copy from' file system details if it will be copied: First interaction with rsdp_filesystem
######
  if ($formVars['chk_filesystem']) {
    $q_string  = "select fs_volume,fs_size,fs_backup ";
    $q_string .= "from rsdp_filesystem ";
    $q_string .= "where fs_rsdp = " . $formVars['rsdp'];
    $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {

######
# Build the 'copy to' file system details
######
      $q_filesystem = "insert into rsdp_filesystem set " . 
        "fs_id     =   " . "NULL"                          . "," .
        "fs_rsdp   =   " . $rsdp                           . "," .
        "fs_volume = \"" . $a_rsdp_filesystem['fs_volume'] . "\"," .
        "fs_size   = \"" . $a_rsdp_filesystem['fs_size']   . "\"," .
        "fs_backup =   " . $a_rsdp_filesystem['fs_backup'];

      mysqli_query($db, $q_filesystem) or die($q_filesystem . ": " . mysqli_error($db));
    }
  }


######
# All the System Initialzation Tasks have been copied.
# Next, copy all the System Provisioning Tasks
######

######
# If it exists, get the 'copy from' system details: first interaction with rsdp_osteam
######
  $q_string  = "select os_sysname,os_fqdn,os_software ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'] . " ";
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
######
# If there are any 'copy from' records, copy them to the new system adding -"rsdp" to the system name
######
  if (mysqli_num_rows($q_rsdp_osteam) > 0) {
    $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

    $q_osteam = "insert into rsdp_osteam set " .
      "os_id       =   " . "NULL"                                . "," . 
      "os_rsdp     =   " . $rsdp                                 . "," . 
      "os_sysname  = \"" . $a_rsdp_osteam['os_sysname'] . "-" . $rsdp . "\"," . 
      "os_fqdn     = \"" . $a_rsdp_osteam['os_fqdn']             . "\"," . 
      "os_software =   " . $a_rsdp_osteam['os_software'];

    mysqli_query($db, $q_osteam) or die($q_osteam . ": " . mysqli_error($db));
  } else {
######
# If there aren't any 'copy from' records yet, add it as an 'Unnamed' system to make it unique
######
    $q_osteam = "insert into rsdp_osteam set " .
      "os_id       =   " . "NULL"             . "," . 
      "os_rsdp     =   " . $rsdp              . "," . 
      "os_sysname  = \"" . "Unnamed-" . $rsdp . "\"";

    mysqli_query($db, $q_osteam) or die($q_osteam . ": " . mysqli_error($db));
  }


######
# If it exists, get the 'copy from' platform details: first interaction with rsdp_platform
######
  $q_string  = "select pf_model,pf_asset,pf_serial,pf_hba,pf_redundant,pf_row,pf_rack,";
  $q_string .= "pf_unit,pf_special,pf_circuita,pf_circuitb ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "where pf_rsdp = " . $formVars['rsdp'] . " ";
  $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
######
# If there are any records yet, create a new record with details from the 'copy from' system
######
  if (mysqli_num_rows($q_rsdp_platform) > 0) {
    $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

    $q_platform = "insert into rsdp_platform set " .
      "pf_id            =   " . "NULL"                                . "," . 
      "pf_rsdp          =   " . $rsdp                                 . "," . 
      "pf_model         =   " . $a_rsdp_platform['pf_model']          . "," . 
# note, this is all physical server details for the Data Center folks
      "pf_asset         = \"" . $a_rsdp_platform['pf_asset']          . "\"," . 
      "pf_serial        = \"" . $a_rsdp_platform['pf_serial']         . "\"," . 
      "pf_hba           =   " . $a_rsdp_platform['pf_hba']            . "," . 
      "pf_redundant     =   " . $a_rsdp_platform['pf_redundant']      . "," . 
      "pf_row           = \"" . $a_rsdp_platform['pf_row']            . "\"," . 
      "pf_rack          = \"" . $a_rsdp_platform['pf_rack']           . "\"," . 
      "pf_unit          =   " . $a_rsdp_platform['pf_unit']           . "," . 
      "pf_special       = \"" . $a_rsdp_platform['pf_special']        . "\"," . 
      "pf_circuita      = \"" . $a_rsdp_platform['pf_circuita']       . "\"," . 
      "pf_circuitb      = \"" . $a_rsdp_platform['pf_circuitb']       . "\"";

    mysqli_query($db, $q_platform) or die($q_platform . ": " . mysqli_error($db));
  } else {
######
# Otherwise, create a new vmware system
######
    $q_platform = "insert into rsdp_platform set " .
      "pf_id       =   " . "NULL"                      . "," . 
      "pf_rsdp     =   " . $rsdp                       . "," . 
      "pf_model    =   " . "45";

    mysqli_query($db, $q_platform) or die($q_platform . ": " . mysqli_error($db));
  }


######
# If IP Addresses (network information) has been created, copy it all to the new system if requested: first interaction with rsdp_interface
######
  if ($formVars['chk_ipaddr']) {
    $q_string  = "select if_name,if_sysport,if_interface,if_groupname,if_mac,if_zone,if_vlan,if_ip,if_ipcheck,";
    $q_string .= "if_mask,if_gate,if_speed,if_duplex,if_redundant,if_media,if_type,if_cid,if_switch,if_swcheck,";
    $q_string .= "if_port,if_description,if_virtual ";
    $q_string .= "from rsdp_interface ";
    $q_string .= "where if_rsdp = " . $formVars['rsdp'];
    $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

######
# Build the 'copy to' file system details
######
      $q_interface = "insert into rsdp_interface set " . 
        "if_id            =   " . "NULL"                          		. "," .
        "if_rsdp          =   " . $rsdp                           		. "," .
        "if_name          = \"" . $a_rsdp_interface['if_name']    		. "\"," .
        "if_sysport       = \"" . $a_rsdp_interface['if_sysport']    		. "\"," .
        "if_interface     = \"" . $a_rsdp_interface['if_interface']    		. "\"," .
        "if_groupname     = \"" . $a_rsdp_interface['if_groupname']    		. "\"," .
        "if_mac           = \"" . $a_rsdp_interface['if_mac']    		. "\"," .
        "if_zone          =   " . $a_rsdp_interface['if_zone']    		. "," .
        "if_vlan          = \"" . $a_rsdp_interface['if_vlan']    		. "\"," .
        "if_ip            = \"" . $a_rsdp_interface['if_ip']    		. "\"," .
        "if_ipcheck       =   " . $a_rsdp_interface['if_ipcheck']    		. "," .
        "if_mask          = \"" . $a_rsdp_interface['if_mask']    		. "\"," .
        "if_gate          = \"" . $a_rsdp_interface['if_gate']    		. "\"," .
        "if_speed         =   " . $a_rsdp_interface['if_speed']    		. "," .
        "if_duplex        =   " . $a_rsdp_interface['if_duplex']    		. "," .
        "if_redundant     =   " . $a_rsdp_interface['if_redundant']    		. "," .
        "if_media         =   " . $a_rsdp_interface['if_media']    		. "," .
        "if_type          =   " . $a_rsdp_interface['if_type']    		. "," .
        "if_cid           = \"" . $a_rsdp_interface['if_cid']    		. "\"," .
        "if_switch        = \"" . $a_rsdp_interface['if_switch']    		. "\"," .
        "if_swcheck       =   " . $a_rsdp_interface['if_swcheck']    		. "," .
        "if_port          = \"" . $a_rsdp_interface['if_port']    		. "\"," .
        "if_description   = \"" . $a_rsdp_interface['if_description']    	. "\"," .
        "if_virtual       =   " . $a_rsdp_interface['if_virtual'];

      mysqli_query($db, $q_interface) or die($q_interface . ": " . mysqli_error($db));
    }
  }


######
# Get the 'copy from' SAN details if any: First interaction with rsdp_designed;
######
  if ($formVars['chk_san1']) {
    $q_string  = "select san_checklist ";
    $q_string .= "from rsdp_designed ";
    $q_string .= "where san_rsdp = " . $formVars['rsdp'] . " ";
    $q_rsdp_designed = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_designed) > 0) {
      $a_rsdp_designed = mysqli_fetch_array($q_rsdp_designed);

      $q_string =
        "san_rsdp      =   " . $rsdp                       . "," .
        "san_checklist =   " . $a_rsdp_designed['san_checklist'];

      $query = "insert into rsdp_designed set san_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    } else {
      $q_string =
        "san_rsdp      =   " . $rsdp;

      $query = "insert into rsdp_designed set san_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    }
  }



######
# Get the 'copy from' network details if any: First interaction with rsdp_infrastructure;
######
  if ($formVars['chk_net']) {
    $q_string  = "select if_netcheck ";
    $q_string .= "from rsdp_infrastructure ";
    $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
    $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      $q_string =
        "if_rsdp       =   " . $rsdp                                 . "," .
        "if_netcheck   =   " . $a_rsdp_infrastructure['if_netcheck'];

      $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    } else {
      $q_string =
        "if_rsdp        =   " . $rsdp;

      $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    }
  }


######
# Get the 'copy from' rsdp_infrastructure details: Second interaction with rsdp_infrastructure;
######
  if ($formVars['chk_virt']) {
# see if an rsdp_infrastructure record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select if_id ";
    $q_string .= "from rsdp_infrastructure ";
    $q_string .= "where if_rsdp = " . $rsdp . " ";
    $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      $q_string  = "select if_vmcheck,if_netprov,if_sanprov,if_vmprov,if_vmnote ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp        =   " . $rsdp                            . "," .
          "if_vmcheck     =   " . $a_infrastructure['if_vmcheck']  . "," .
          "if_netprov     =   " . $a_infrastructure['if_netprov']  . "," .
          "if_sanprov     =   " . $a_infrastructure['if_sanprov']  . "," .
          "if_vmprov      =   " . $a_infrastructure['if_vmprov']   . "," .
          "if_vmnote      = \"" . $a_infrastructure['if_vmnote']   . "\"";

        $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $a_rsdp_infrastructure['if_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select if_vmcheck,if_netprov,if_sanprov,if_vmprov,if_vmnote ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp        =   " . $rsdp                            . "," .
          "if_vmcheck     =   " . $a_infrastructure['if_vmcheck']  . "," .
          "if_netprov     =   " . $a_infrastructure['if_netprov']  . "," .
          "if_sanprov     =   " . $a_infrastructure['if_sanprov']  . "," .
          "if_vmprov      =   " . $a_infrastructure['if_vmprov']   . "," .
          "if_vmnote      = \"" . $a_infrastructure['if_vmnote']   . "\"";

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "if_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }



######
# Get the 'copy from' rsdp_infrastructure details: Third interaction with rsdp_infrastructure;
######
  if ($formVars['chk_sys1']) {
# see if an rsdp_infrastructure record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select if_id ";
    $q_string .= "from rsdp_infrastructure ";
    $q_string .= "where if_rsdp = " . $rsdp . " ";
    $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      $q_string  = "select if_config,if_built,if_network,if_dns,if_inscheck ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp        =   " . $rsdp                            . "," .
          "if_config    =   " . $a_infrastructure['if_config']     . "," .
          "if_built     =   " . $a_infrastructure['if_built']      . "," .
          "if_network   =   " . $a_infrastructure['if_network']    . "," .
          "if_dns       =   " . $a_infrastructure['if_dns']        . "," .
          "if_inscheck  = \"" . $a_infrastructure['if_inscheck']   . "\"";

        $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $a_rsdp_infrastructure['if_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select if_config,if_built,if_network,if_dns,if_inscheck ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp      =   " . $rsdp                              . "," .
          "if_config    =   " . $a_infrastructure['if_config']     . "," .
          "if_built     =   " . $a_infrastructure['if_built']      . "," .
          "if_network   =   " . $a_infrastructure['if_network']    . "," .
          "if_dns       =   " . $a_infrastructure['if_dns']        . "," .
          "if_inscheck  = \"" . $a_infrastructure['if_inscheck']   . "\"";

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "if_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }



######
# Get the 'copy from' rsdp_infrastructure details: Fourth interaction with rsdp_infrastructure;
######
  if ($formVars['chk_san2']) {
# see if an rsdp_infrastructure record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select if_id ";
    $q_string .= "from rsdp_infrastructure ";
    $q_string .= "where if_rsdp = " . $rsdp . " ";
    $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      $q_string  = "select if_procheck,if_sanconf,if_provisioned ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp        =   " . $rsdp                               . "," .
          "if_procheck    =   " . $a_infrastructure['if_procheck']    . "," .
          "if_sanconf     =   " . $a_infrastructure['if_sanconf']     . "," .
          "if_provisioned =   " . $a_infrastructure['if_provisioned'];

        $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $a_rsdp_infrastructure['if_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select if_procheck,if_sanconf,if_provisioned ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp        =   " . $rsdp                               . "," .
          "if_procheck    =   " . $a_infrastructure['if_procheck']    . "," .
          "if_sanconf     =   " . $a_infrastructure['if_sanconf']     . "," .
          "if_provisioned =   " . $a_infrastructure['if_provisioned'];

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "if_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }


######
# Get the 'copy from' rsdp_infrastructure details: Fifth interaction with rsdp_infrastructure;
######
  if ($formVars['chk_sys2']) {
# see if an rsdp_infrastructure record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select if_id ";
    $q_string .= "from rsdp_infrastructure ";
    $q_string .= "where if_rsdp = " . $rsdp . " ";
    $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      $q_string  = "select if_checklist,if_sanfs,if_verified,if_wiki,if_svrmgt ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp         =   " . $rsdp                                . "," .
          "if_checklist    =   " . $a_infrastructure['if_checklist']    . "," .
          "if_sanfs        =   " . $a_infrastructure['if_sanfs']        . "," .
          "if_verified     =   " . $a_infrastructure['if_verified']     . "," .
          "if_wiki         =   " . $a_infrastructure['if_wiki']         . "," .
          "if_svrmgt       =   " . $a_infrastructure['if_svrmgt'];

        $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $a_rsdp_infrastructure['if_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select if_checklist,if_sanfs,if_verified,if_wiki,if_svrmgt ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp        =   " . $rsdp                               . "," .
          "if_checklist    =   " . $a_infrastructure['if_checklist']    . "," .
          "if_sanfs        =   " . $a_infrastructure['if_sanfs']        . "," .
          "if_verified     =   " . $a_infrastructure['if_verified']     . "," .
          "if_wiki         =   " . $a_infrastructure['if_wiki']         . "," .
          "if_svrmgt       =   " . $a_infrastructure['if_svrmgt'];

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "if_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }


######
# Get the 'copy from' rsdp_infrastructure details: Sixth interaction with rsdp_infrastructure;
######
  if ($formVars['chk_backup']) {
# see if an rsdp_infrastructure record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select if_id ";
    $q_string .= "from rsdp_infrastructure ";
    $q_string .= "where if_rsdp = " . $rsdp . " ";
    $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      $q_string  = "select if_bucheck,if_backups,if_buverified ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp         =   " . $rsdp                                . "," .
          "if_bucheck      =   " . $a_infrastructure['if_bucheck']     . "," .
          "if_backups      =   " . $a_infrastructure['if_backups']     . "," .
          "if_buverified   =   " . $a_infrastructure['if_buverified'];

        $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $a_rsdp_infrastructure['if_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select if_bucheck,if_backups,if_buverified ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp         =   " . $rsdp                               . "," .
          "if_bucheck      =   " . $a_infrastructure['if_bucheck']     . "," .
          "if_backups      =   " . $a_infrastructure['if_backups']     . "," .
          "if_buverified   =   " . $a_infrastructure['if_buverified'];

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "if_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }


######
# Get the 'copy from' rsdp_infrastructure details: Seventh interaction with rsdp_infrastructure;
######
  if ($formVars['chk_mon1']) {
# see if an rsdp_infrastructure record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select if_id ";
    $q_string .= "from rsdp_infrastructure ";
    $q_string .= "where if_rsdp = " . $rsdp . " ";
    $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
      $a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure);

      $q_string  = "select if_moncheck,if_monitor,if_monverified ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp         =   " . $rsdp                                . "," .
          "if_moncheck      =   " . $a_infrastructure['if_moncheck']    . "," .
          "if_monitor       =   " . $a_infrastructure['if_monitor']     . "," .
          "if_monverified   =   " . $a_infrastructure['if_monverified'];

        $query = "update rsdp_infrastructure set " . $q_string . " where if_id = " . $a_rsdp_infrastructure['if_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select if_moncheck,if_monitor,if_monverified ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
      $q_infrastructure = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_infrastructure) > 0) {
        $a_infrastructure = mysqli_fetch_array($q_infrastructure);

        $q_string =
          "if_rsdp          =   " . $rsdp                               . "," .
          "if_moncheck      =   " . $a_infrastructure['if_moncheck']    . "," .
          "if_monitor       =   " . $a_infrastructure['if_monitor']     . "," .
          "if_monverified   =   " . $a_infrastructure['if_monverified'];

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "if_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_infrastructure set if_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }


######
# Get the 'copy from' rsdp_applications details: First interaction with rsdp_applications;
######
  if ($formVars['chk_app1']) {
    $q_string  = "select app_inscheck,app_installed,app_configured,app_mib,app_process,app_logfile ";
    $q_string .= "from rsdp_applications ";
    $q_string .= "where app_rsdp = " . $formVars['rsdp'] . " ";
    $q_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_applications) > 0) {
      $a_applications = mysqli_fetch_array($q_applications);

      $q_string =
        "app_rsdp          =   " . $rsdp                              . "," .
        "app_inscheck      =   " . $a_applications['app_inscheck']    . "," .
        "app_installed     =   " . $a_applications['app_installed']   . "," .
        "app_configured    =   " . $a_applications['app_configured']  . "," .
        "app_mib           =   " . $a_applications['app_mib']         . "," .
        "app_process       =   " . $a_applications['app_process']     . "," .
        "app_logfile       =   " . $a_applications['app_logfile'];

      $query = "insert into rsdp_applications set app_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    } else {
      $q_string =
        "app_rsdp        =   " . $rsdp;

      $query = "insert into rsdp_applications set app_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    }
  }


######
# Get the 'copy from' rsdp_applications details: Second interaction with rsdp_applications;
######
  if ($formVars['chk_mon2']) {
# see if an rsdp_applications record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select app_id ";
    $q_string .= "from rsdp_applications ";
    $q_string .= "where app_rsdp = " . $rsdp . " ";
    $q_rsdp_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_applications) > 0) {
      $a_rsdp_applications = mysqli_fetch_array($q_rsdp_applications);

      $q_string  = "select app_moncheck,app_monitor,app_verified ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = " . $formVars['rsdp'] . " ";
      $q_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_applications) > 0) {
        $a_applications = mysqli_fetch_array($q_applications);

        $q_string =
          "app_rsdp          =   " . $rsdp                              . "," .
          "app_moncheck      =   " . $a_applications['app_moncheck']    . "," .
          "app_monitor       =   " . $a_applications['app_monitor']     . "," .
          "app_verified      =   " . $a_applications['app_verified'];

        $query = "update rsdp_applications set " . $q_string . " where app_id = " . $a_rsdp_applications['app_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select app_moncheck,app_monitor,app_verified ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = " . $formVars['rsdp'] . " ";
      $q_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_applications) > 0) {
        $a_applications = mysqli_fetch_array($q_applications);

        $q_string =
          "app_rsdp          =   " . $rsdp                              . "," .
          "app_moncheck      =   " . $a_applications['app_moncheck']    . "," .
          "app_monitor       =   " . $a_applications['app_monitor']     . "," .
          "app_verified      =   " . $a_applications['app_verified'];

        $query = "insert into rsdp_applications set app_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "app_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_applications set app_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }


######
# Get the 'copy from' rsdp_applications details: Third interaction with rsdp_applications;
######
  if ($formVars['chk_app2']) {
# see if an rsdp_applications record has already been created
# if yes, update the record with the new information
#   if no, see if there's an existing 'copy from' record
#     if yes, copy the data to the existing 'copy to' record
#     if no, create a new 'copy to' record
# if no, see if there's an existing 'copy from' record
#   if yes, copy the data to the existing 'copy to' record
#   if no, create a new 'copy to' record
    $q_string  = "select app_id ";
    $q_string .= "from rsdp_applications ";
    $q_string .= "where app_rsdp = " . $rsdp . " ";
    $q_rsdp_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_applications) > 0) {
      $a_rsdp_applications = mysqli_fetch_array($q_rsdp_applications);

      $q_string  = "select app_tested,app_integrated,app_concheck,app_failover ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = " . $formVars['rsdp'] . " ";
      $q_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_applications) > 0) {
        $a_applications = mysqli_fetch_array($q_applications);

        $q_string =
          "app_rsdp          =   " . $rsdp                             . "," .
          "app_tested        =   " . $a_applications['app_tested']     . "," .
          "app_integrated    =   " . $a_applications['app_integrated'] . "," .
          "app_concheck      =   " . $a_applications['app_concheck'];

        $query = "update rsdp_applications set " . $q_string . " where app_id = " . $a_rsdp_applications['app_id'];
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    } else {
      $q_string  = "select app_tested,app_integrated,app_concheck,app_failover ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = " . $formVars['rsdp'] . " ";
      $q_applications = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_applications) > 0) {
        $a_applications = mysqli_fetch_array($q_applications);

        $q_string =
          "app_rsdp          =   " . $rsdp                             . "," .
          "app_tested        =   " . $a_applications['app_tested']     . "," .
          "app_integrated    =   " . $a_applications['app_integrated'] . "," .
          "app_concheck      =   " . $a_applications['app_concheck'];

        $query = "insert into rsdp_applications set app_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      } else {
        $q_string =
          "app_rsdp        =   " . $rsdp;

        $query = "insert into rsdp_applications set app_id = null," . $q_string;
        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }
    }
  }


######
# Get the 'copy from' rsdp_infosec details: First interaction with rsdp_infosec;
######
  if ($formVars['chk_infosec']) {
    $q_string  = "select is_ticket,is_scan,is_checklist,is_verified ";
    $q_string .= "from rsdp_infosec ";
    $q_string .= "where is_rsdp = " . $formVars['rsdp'] . " ";
    $q_rsdp_infosec = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_infosec) > 0) {
      $a_rsdp_infosec = mysqli_fetch_array($q_rsdp_infosec);

      $q_string =
        "is_rsdp      =   " . $rsdp                               . "," .
        "is_ticket    = \"" . $a_rsdp_infosec['is_ticket']        . "\"," .
        "is_scan      =   " . $a_rsdp_infosec['is_scan']          . "," .
        "is_checklist =   " . $a_rsdp_infosec['is_checklist']     . "," .
        "is_verified  =   " . $a_rsdp_infosec['is_verified'];

      $query = "insert into rsdp_infosec set is_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    } else {
      $q_string =
        "is_rsdp      =   " . $rsdp;

      $query = "insert into rsdp_infosec set is_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    }
  }

  print "alert(\"Duplication Complete. Click to load new server.\");\n";
  print "window.location.href = 'initial.php?rsdp=" . $rsdp . "';\n";

?>
