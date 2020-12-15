<?php
# Script: populate.php
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

  $package = "populate.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }
  if (isset($_GET['rsdp'])) {
    $formVars['rsdp'] = clean($_GET['rsdp'], 10);
  } else {
    $formVars['rsdp'] = 0;
  }

  $query = '';
  $newinvid = 0;
  $date = date('Y-m-d');

# 1. Create a new record in the inventory table. This gives us the inventory record number to use in all the other tables.

# rsdp_applications - Checkboxes; no need to read data
# rsdp_backups - Backup details - populate inventory "backups" table
# rsdp_datacenter - Mostly checkboxes, a path field is entered - where to stash that.
# rsdp_designed - san checkboxes - no need to save data
# rsdp_filesystem - additional filesystems to be added to the system such as /u01 along with size and other details
# rsdp_infosec - checkboxes - no need to save data
# rsdp_infrastructure - general checkboxes for most tasks. nothing to be saved
# rsdp_interface - copy all data to the inventory "interface" table
# rsdp_osteam - populate software table with this; operating system
# rsdp_platform - populate hardware table with this; physical machine
# rsdp_san - san data - can be saved in the "filesystem" table
# rsdp_server - save data in the main interface table
# rsdp_status - rsdp tracking table.

# 2. List of tables to be updated:

# inventory table
# backup table
# interface table
# software table
# hardware table
# filesystem table 

# first get the data from all the RSDP tables. Makes it easier to populate the various tables if the data is retrieved first.
# the interface information has more than one so it'll be retrieved in the save area.

  $q_string = "select rsdp_location,rsdp_product,rsdp_project,rsdp_platform,rsdp_application,rsdp_service,rsdp_vendor,rsdp_function ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $q_string  = "select bu_start,bu_include,bu_retention,bu_sunday,bu_monday,bu_tuesday,bu_wednesday,bu_thursday,bu_friday,bu_saturday, ";
  $q_string .= "bu_suntime,bu_montime,bu_tuetime,bu_wedtime,bu_thutime,bu_fritime,bu_sattime ";
  $q_string .= "from rsdp_backups ";
  $q_string .= "where bu_rsdp = " . $formVars['rsdp'];
  $q_rsdp_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);

  $q_string = "select os_sysname,os_fqdn,os_software ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

  $servername = $a_rsdp_osteam['os_sysname'];
  $fqdn = $a_rsdp_osteam['os_fqdn'];

  $q_string  = "select pf_model,pf_asset,pf_serial,pf_row,pf_rack,pf_unit ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "where pf_rsdp = " . $formVars['rsdp'];
  $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

##################################################
# 1, create a new inventory table record and retrieve the inv_id number

  $q_string = "insert into inventory set inv_id = NULL";
  mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $newinvid = last_insert_id($db);

  if ($newinvid == 0) {
    print "alert('Inventory record creation failed.');\n";
    exit(1);
  }

  logaccess($db, $_SESSION['uid'], $package, "New invid: " . $newinvid);
##################################################

##################################################
# 2, Create the backup insert string

  $q_string =
    "bu_companyid =   " . $newinvid                       . "," .
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

  $query = "insert into backups set bu_id = null," . $q_string;
  mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
##################################################

##################################################
# 3, Create the interface records.
#   need to associate child interfaces with parents based 
#   on configuration in rsdp

#####
# insert rsdp interface table here for review (delete when done)
#####

  $q_string  = "select if_id,if_name,if_sysport,if_interface,if_groupname,if_mac,if_zone,if_vlan,";
  $q_string .= "if_ip,if_mask,if_gate,if_speed,if_duplex,if_redundant,if_media,if_type,";
  $q_string .= "if_cid,if_switch,if_port,if_description,if_virtual ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = 0 ";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

#####
# insert production interface table here for review (delete when done)
#####

# Create the interface insert string
    $q_string =
      "int_server     = \"" . $a_rsdp_interface['if_name']       . "\"," .
      "int_companyid  =   " . $newinvid                          . "," .
      "int_face       = \"" . $a_rsdp_interface['if_interface']  . "\"," .
      "int_sysport    = \"" . $a_rsdp_interface['if_sysport']    . "\"," .
      "int_addr       = \"" . $a_rsdp_interface['if_ip']         . "\"," .
      "int_eth        = \"" . $a_rsdp_interface['if_mac']        . "\"," .
      "int_mask       = \"" . $a_rsdp_interface['if_mask']       . "\"," .
      "int_gate       = \"" . $a_rsdp_interface['if_gate']       . "\"," .
      "int_switch     = \"" . $a_rsdp_interface['if_switch']     . "\"," .
      "int_port       = \"" . $a_rsdp_interface['if_port']       . "\"," .
      "int_type       =   " . $a_rsdp_interface['if_type']       . "," .
      "int_vlan       = \"" . $a_rsdp_interface['if_vlan']       . "\"," .
      "int_user       =   " . $_SESSION['uid']                   . "," .
      "int_update     = \"" . $date                              . "\"," .
      "int_media      =   " . $a_rsdp_interface['if_media']      . "," .
      "int_speed      =   " . $a_rsdp_interface['if_speed']      . "," .
      "int_duplex     =   " . $a_rsdp_interface['if_duplex']     . "," .
      "int_redundancy =   " . $a_rsdp_interface['if_redundant']  . "," . 
      "int_groupname  = \"" . $a_rsdp_interface['if_groupname']  . "\"," . 
      "int_virtual    =   " . $a_rsdp_interface['if_virtual']    . "," .
      "int_zone       =   " . $a_rsdp_interface['if_zone'];

    $query = "insert into interface set int_id = null," . $q_string;
    mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
    $newinterface = last_insert_id($db);

# insert any child interfaces into the inventory
    $q_string  = "select if_name,if_sysport,if_interface,if_groupname,if_mac,if_zone,if_vlan,";
    $q_string .= "if_ip,if_mask,if_gate,if_speed,if_duplex,if_redundant,if_media,if_type,";
    $q_string .= "if_cid,if_switch,if_port,if_description,if_virtual ";
    $q_string .= "from rsdp_interface ";
    $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
    $q_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_redundancy = mysqli_fetch_array($q_redundancy)) {

      $q_string =
        "int_server     = \"" . $a_redundancy['if_name']       . "\"," .
        "int_companyid  =   " . $newinvid                      . "," .
        "int_face       = \"" . $a_redundancy['if_interface']  . "\"," .
        "int_sysport    = \"" . $a_redundancy['if_sysport']    . "\"," .
        "int_int_id     =   " . $newinterface                  . "," .
        "int_addr       = \"" . $a_redundancy['if_ip']         . "\"," .
        "int_eth        = \"" . $a_redundancy['if_mac']        . "\"," .
        "int_mask       = \"" . $a_redundancy['if_mask']       . "\"," .
        "int_gate       = \"" . $a_redundancy['if_gate']       . "\"," .
        "int_switch     = \"" . $a_redundancy['if_switch']     . "\"," .
        "int_port       = \"" . $a_redundancy['if_port']       . "\"," .
        "int_type       =   " . $a_redundancy['if_type']       . "," .
        "int_vlan       = \"" . $a_redundancy['if_vlan']       . "\"," .
        "int_user       =   " . $_SESSION['uid']               . "," .
        "int_update     = \"" . $date                          . "\"," .
        "int_media      =   " . $a_redundancy['if_media']      . "," .
        "int_speed      =   " . $a_redundancy['if_speed']      . "," .
        "int_duplex     =   " . $a_redundancy['if_duplex']     . "," .
        "int_redundancy =   " . $a_redundancy['if_redundant']  . "," . 
        "int_groupname  = \"" . $a_redundancy['if_groupname']  . "\"," . 
        "int_virtual    =   " . $a_redundancy['if_virtual']    . "," .
        "int_zone       =   " . $a_redundancy['if_zone'];

      $query = "insert into interface set int_id = null," . $q_string;
      mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

    }
  }
##################################################

##################################################
# 4, Create the software/os table

#####
# insert rsdp os table and operatingsystem table here for review (delete when done)
#####

  $q_string  = "select os_vendor,os_software ";
  $q_string .= "from operatingsystem ";
  $q_string .= "where os_id = " . $a_rsdp_osteam['os_software'];
  $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);

#####
# insert production software table here for review (delete when done)
#####

  $q_string = 
    "sw_companyid =  " . $newinvid                         . "," . 
    "sw_software  = '" . $a_operatingsystem['os_software'] . "'," . 
    "sw_product   =  " . $a_rsdp_server['rsdp_product']    . "," . 
    "sw_vendor    = '" . $a_operatingsystem['os_vendor']   . "'," . 
    "sw_type      = '" . "OS"                              . "'," . 
    "sw_group     =  " . $a_rsdp_server['rsdp_platform']   . "," .
    "sw_user      =  " . $_SESSION['uid']                  . "," . 
    "sw_update    = '" . $date                             . "'";

  $query = "insert into software set sw_id = NULL," . $q_string;
  mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
##################################################

##################################################
# 5, Create the software/os table

#####
# insert production hardware table here for review (delete when done)
#####

#####
# insert rsdp platforms table here for review (delete when done)
#####

  $q_string = 
    "hw_companyid =  " . $newinvid                       . "," . 
    "hw_type      =  " . "15"                            . "," . 
    "hw_serial    = '" . $a_rsdp_platform['pf_serial']   . "'," . 
    "hw_asset     = '" . $a_rsdp_platform['pf_asset']    . "'," . 
    "hw_vendorid  =  " . $a_rsdp_platform['pf_model']    . "," . 
    "hw_product   =  " . $a_rsdp_server['rsdp_product']  . "," . 
    "hw_group     =  " . $a_rsdp_server['rsdp_platform'] . "," . 
    "hw_purchased = '" . $date                           . "'," . 
    "hw_built     = '" . $date                           . "'," . 
    "hw_primary   =  " . "1"                             . "," . 
    "hw_verified  =  " . "0"                             . "," .
    "hw_user      =  " . $_SESSION['uid']                . "," . 
    "hw_update    = '" . $date                           . "'";

  $query = "insert into hardware set hw_id = NULL," . $q_string;
  mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
##################################################

##################################################
# 6, Create the filesystem table

#####
# insert production filesystem table here for review (delete when done)
#####

#####
# insert rsdp san table here for review (delete when done)
#####

#####
# insert rsdp filesystem table here for review (delete when done)
#####

# get SAN information
  $q_string  = "select san_sysport,san_switch,san_port,san_media,san_wwnnzone ";
  $q_string .= "from rsdp_san ";
  $q_string .= "where san_rsdp = " . $formVars['rsdp'];
  $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_san = mysqli_fetch_array($q_rsdp_san)) {

  }



##################################################

##################################################
# 7, finally update the inventory table

#####
# insert production inventory table here for review (delete when done)
#####

#####
# insert rsdp server table here for review (delete when done)
#####

# really hardware identifies this but for historical reasons:
  $isvirtual = rsdp_Virtual($db, "$formVars['rsdp']);

  $q_string = 
    "inv_name     = '" . $servername                        . "'," . 
    "inv_rsdp     =  " . $formVars['rsdp']                  . "," . 
    "inv_fqdn     = '" . $fqdn                              . "'," . 
    "inv_function = '" . $a_rsdp_server['rsdp_function']    . "'," . 
    "inv_location =  " . $a_rsdp_server['rsdp_location']    . "," . 
    "inv_manager  =  " . $a_rsdp_server['rsdp_platform']    . "," . 
    "inv_appadmin =  " . $a_rsdp_server['rsdp_application'] . "," . 
    "inv_product  =  " . $a_rsdp_server['rsdp_product']     . "," . 
    "inv_project  =  " . $a_rsdp_server['rsdp_project']     . "," . 
    "inv_class    =  " . $a_rsdp_server['rsdp_service']     . "," . 
    "inv_rack     = '" . $a_rsdp_platform['pf_rack']        . "'," . 
    "inv_row      = '" . $a_rsdp_platform['pf_row']         . "'," . 
    "inv_unit     =  " . $a_rsdp_platform['pf_unit']        . "," . 
    "inv_ssh      =  " . "1"                                . "," . 
    "inv_ansible  =  " . "1"                                . "," . 
    "inv_virtual  =  " . $isvirtual;

  $query = "update inventory set " . $q_string . " where inv_id = " . $newinvid;
  if ($newinvid > 0) {
    mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
  }

  changelog($db, $newinvid, $servername, "New Server", "inventory", "inv_name", 0);

  print "document.rsdp.populate.value = '" . $servername . " In Inventory';\n";
  print "document.rsdp.populate.disabled = true;\n";

?>
