<?php
# Script: search.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "search.php";

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# the intention is to return a standard data block with extra bits depending on what's being searched for
# this searches for partial names, ip addresses, etc similar to to the inventory email query tool

  if (isset($_GET['ip'])) {
    $formVars['ip'] = clean($_GET['ip'], 60);
  } else {
    $formVars['ip'] = '';
  }
  if (isset($_GET['mac'])) {
    $formVars['mac'] = clean($_GET['mac'], 60);
  } else {
    $formVars['mac'] = '';
  }
  if (isset($_GET['asset'])) {
    $formVars['asset'] = clean($_GET['asset'], 60);
  } else {
    $formVars['asset'] = '';
  }
  if (isset($_GET['serial'])) {
    $formVars['serial'] = clean($_GET['serial'], 60);
  } else {
    $formVars['serial'] = '';
  }

  class Server {
    public $inventory_name = '';
    public $inventory_uuid = '';
    public $inventory_sysadmins = '';
    public $inventory_appadmins = '';
    public $inventory_function = '';
    public $inventory_documentation = '';
    public $inventory_service_class = '';
    public $inventory_maintenance_window = '';
    public $inventory_product = '';
    public $inventory_project = '';
    public $inventory_location = '';
    public $inventory_timezone = '';
    public $inventory_hardware = '';
    public $inventory_network = '';
  }

  class IP_Address {
    public $interface_name = '';
    public $interface_fqdn = '';
    public $interface_label = '';
    public $interface_address = '';
    public $interface_ethernet = '';
    public $interface_netmask = '';
    public $interface_gateway = '';
    public $interface_vlan = '';
    public $interface_type = '';
    public $interface_default = '';
    public $interface_role = '';
    public $interface_redundant = '';
    public $interface_groupname = '';
    public $interface_virtual = '';
    public $interface_netzone = '';
    public $interface_management = '';
    public $interface_backup = '';

    public $physical_port = '';
    public $physical_switch = '';
    public $physical_switch_port = '';
    public $physical_media = '';
    public $physical_speed = '';
    public $physical_duplex = '';

    public $monitor_openview = '';
    public $monitor_nagios = '';
    public $monitor_ping = '';
    public $monitor_ssh = '';
    public $monitor_http = '';
    public $monitor_ftp = '';
    public $monitor_smtp = '';
    public $monitor_cfg2html = '';
    public $monitor_notify = '';
    public $monitor_hours = '';
  }

# if we search for the ip or mac address
# get the inv_id
# then return a basic server block
# plus the interface block

  if ($formVars['ip'] != '') {
    $q_string  = "select inv_id ";
    $q_string .= "from interface ";
    $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
    $q_string .= "where inv_status = 0 and int_addr = \"" . $formVars['ip'] . "\" ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_interface = mysqli_fetch_array($q_interface);

    if (mysqli_num_rows($q_interface) > 0) {
      $formVars['inv_id'] = $a_interface['inv_id'];
      $formVars['interfaces'] = "yes";
    } else {
      $formVars['inv_id'] = 0;
      $formVars['interfaces'] = 'no';
    }
  }

  if ($formVars['mac'] != '') {
    $q_string  = "select inv_id ";
    $q_string .= "from interface ";
    $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
    $q_string .= "where inv_status = 0 and int_eth = \"" . $formVars['mac'] . "\" ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_interface = mysqli_fetch_array($q_interface);

    if (mysqli_num_rows($q_interface) > 0) {
      $formVars['inv_id'] = $a_interface['inv_id'];
      $formVars['interfaces'] = "yes";
    } else {
      $formVars['inv_id'] = 0;
      $formVars['interfaces'] = 'no';
    }
  }

  if ($formVars['asset'] != '') {
    $q_string  = "select inv_id ";
    $q_string .= "from hardware ";
    $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
    $q_string .= "where inv_status = 0 and hw_asset = \"" . $formVars['asset'] . "\" ";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_hardware = mysqli_fetch_array($q_hardware);

    if (mysqli_num_rows($q_hardware) > 0) {
      $formVars['inv_id'] = $a_hardware['inv_id'];
    }
  }

  if ($formVars['serial'] != '') {
    $q_string  = "select inv_id ";
    $q_string .= "from hardware ";
    $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
    $q_string .= "where inv_status = 0 and hw_serial = \"" . $formVars['serial'] . "\" ";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_hardware = mysqli_fetch_array($q_hardware);

    if (mysqli_num_rows($q_hardware) > 0) {
      $formVars['inv_id'] = $a_hardware['inv_id'];
    }
  }

  $q_string  = "select inv_id,inv_name,inv_uuid,inv_satuuid,inv_class,inv_location,inv_function,man_text,";
  $q_string .= "inv_document,inv_power,inv_rack,inv_row,inv_unit,prod_name,prj_name,zone_name,grp_name,inv_appadmin ";
  $q_string .= "from inventory ";
  $q_string .= "left join timezones on timezones.zone_id = inventory.inv_zone ";
  $q_string .= "left join inv_maintenance on inv_maintenance.man_id = inventory.inv_maint ";
  $q_string .= "left join inv_groups on inv_groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where inv_status = 0 and inv_id = " . $formVars['inv_id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string  . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if (!isset($a_inventory['prod_name'])) {
      $a_inventory['prod_name'] = 'Unknown';
    }
    if (!isset($a_inventory['prj_name'])) {
      $a_inventory['prj_name'] = 'Unknown';
    }
    if (!isset($a_inventory['zone_name'])) {
      $a_inventory['zone_name'] = 'Unknown';
    }

    $q_string  = "select grp_name ";
    $q_string .= "from inv_groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_inv_groups = mysqli_fetch_array($q_inv_groups);

    $servers[$a_inventory['inv_name']] = new Server();
    $servers[$a_inventory['inv_name']]->inventory_name               = $a_inventory['inv_name'];
    $servers[$a_inventory['inv_name']]->inventory_fqdn               = $fqdn;
    $servers[$a_inventory['inv_name']]->inventory_sysadmins          = $a_inventory['grp_name'];
    $servers[$a_inventory['inv_name']]->inventory_appadmins          = $a_inv_groups['grp_name'];
    $servers[$a_inventory['inv_name']]->inventory_uuid               = $a_inventory['inv_uuid'];
    $servers[$a_inventory['inv_name']]->inventory_satellite_uuid     = $a_inventory['inv_satuuid'];
    $servers[$a_inventory['inv_name']]->inventory_function           = $a_inventory['inv_function'];
    $servers[$a_inventory['inv_name']]->inventory_documentation      = $a_inventory['inv_document'];
    $servers[$a_inventory['inv_name']]->inventory_product            = $a_inventory['prod_name'];
    $servers[$a_inventory['inv_name']]->inventory_project            = $a_inventory['prj_name'];
    $servers[$a_inventory['inv_name']]->inventory_timezone           = $a_inventory['zone_name'];
    $servers[$a_inventory['inv_name']]->inventory_maintenance_window = $a_inventory['man_text'];

    $q_string  = "select loc_identity ";
    $q_string .= "from inv_locations ";
    $q_string .= "where loc_id = " . $a_inventory['inv_location'] . " ";
    $q_inv_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_inv_locations = mysqli_fetch_array($q_inv_locations);

    $servers[$a_inventory['inv_name']]->inventory_location = $a_inv_locations['loc_identity'];

    $q_string  = "select svc_name ";
    $q_string .= "from inv_service ";
    $q_string .= "where svc_id = " . $a_inventory['inv_class'] . " ";
    $q_inv_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_inv_service = mysqli_fetch_array($q_inv_service);

    $servers[$a_inventory['inv_name']]->inventory_service_class = $a_inv_service['svc_name'];

    $count = 0;
    $q_string  = "select int_id,int_server,int_domain,int_face,int_addr,int_eth,int_mask,int_gate,int_vlan,itp_name,";
    $q_string .= "int_openview,int_nagios,int_ping,int_ssh,int_http,int_ftp,int_smtp,int_cfg2html,int_notify,int_hours,";
    $q_string .= "int_primary,int_switch,int_port,med_text,spd_text,dup_text,rol_text,int_management,int_backup,";
    $q_string .= "int_redundancy,int_groupname,int_virtual,zone_zone,zone_acronym,int_sysport ";
    $q_string .= "from interface ";
    $q_string .= "left join net_zones  on net_zones.zone_id = interface.int_zone ";
    $q_string .= "left join int_types    on int_types.itp_id    = interface.int_type ";
    $q_string .= "left join inv_int_duplex on inv_int_duplex.dup_id = interface.int_duplex ";
    $q_string .= "left join inv_int_media  on inv_int_media.med_id  = interface.int_media ";
    $q_string .= "left join int_speed  on int_speed.spd_id  = interface.int_speed ";
    $q_string .= "left join int_role   on int_role.rol_id   = interface.int_role ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = 0 ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      if ($formVars['interfaces'] == 'yes') {

        if ($a_interface['int_virtual']) {
          $virtual = 'Yes';
        } else {
          $virtual = 'No';
        }

        if ($a_interface['int_domain'] == '') {
          $fqdn = $a_interface['int_server'];
        } else {
          $fqdn = $a_interface['int_server'] . "." . $a_interface['int_domain'];
        }

        $q_string  = "select red_text ";
        $q_string .= "from int_redundancy ";
        $q_string .= "where red_id = " . $a_interface['int_redundancy'] . " ";
        $q_int_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_int_redundancy = mysqli_fetch_array($q_int_redundancy);
        if ($a_int_redundancy['red_text'] == '') {
          $a_int_redundancy['red_text'] = "None";
        }

        $notify = 'None';
        if ($a_interface['int_notify'] == 1) {
          $notify = 'Email';
        }
        if ($a_interface['int_notify'] == 2) {
          $notify = 'Page';
        }

        $hours = 'Business Hours';
        if ($a_interface['int_hours'] == 1) {
          $hours = '24x7';
        }

        $index = "interface_" . $count++;

        $servers[$a_inventory['inv_name']]->inventory_network[$index] = new IP_Address();
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_name         = $a_interface['int_server'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_fqdn         = $fqdn;
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_label        = $a_interface['int_face'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_address      = $a_interface['int_addr'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_ethernet     = $a_interface['int_eth'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_netmask      = $a_interface['int_mask'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_gateway      = $a_interface['int_gate'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_vlan         = $a_interface['int_vlan'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_type         = $a_interface['itp_name'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_default      = ($a_interface['int_primary'] ? "Default Route" : "");
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_role         = $a_interface['rol_text'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_redundant    = $a_int_redundancy['red_text'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_groupname    = $a_interface['int_groupname'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_virtual      = $virtual;
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_netzone      = $a_interface['zone_zone'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_management   = ($a_interface['int_management'] ? 'Yes' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_backup       = ($a_interface['int_backup'] ? 'Yes' : 'No');

        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_port        = $a_interface['int_sysport'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_switch      = $a_interface['int_switch'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_switch_port = $a_interface['int_port'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_media       = $a_interface['med_text'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_speed       = $a_interface['spd_text'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_duplex      = $a_interface['dup_text'];

        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_openview  = ($a_interface['int_openview'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_openview  = ($a_interface['int_openview'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_nagios    = ($a_interface['int_nagios'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_ping      = ($a_interface['int_ping'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_ssh       = ($a_interface['int_ssh'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_http      = ($a_interface['int_http'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_ftp       = ($a_interface['int_ftp'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_smtp      = ($a_interface['int_smtp'] ? 'Monitored' : 'No');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_cfg2html  = ($a_interface['int_cfg2html'] ? 'Ignored' : 'Monitored');
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_notify    = $notify;
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->monitor_hours     = $hours;


        $intcount = 0;
        $q_string  = "select int_id,int_server,int_domain,int_face,int_addr,int_eth,int_mask,int_gate,int_vlan,itp_name,";
        $q_string .= "int_openview,int_nagios,int_ping,int_ssh,int_http,int_ftp,int_smtp,int_cfg2html,int_notify,int_hours,";
        $q_string .= "int_primary,int_switch,int_port,med_text,spd_text,dup_text,rol_text,int_management,int_backup,";
        $q_string .= "int_redundancy,int_groupname,int_virtual,zone_zone,int_sysport ";
        $q_string .= "from interface ";
        $q_string .= "left join net_zones  on net_zones.zone_id = interface.int_zone ";
        $q_string .= "left join int_types    on int_types.itp_id    = interface.int_type ";
        $q_string .= "left join inv_int_duplex on inv_int_duplex.dup_id = interface.int_duplex ";
        $q_string .= "left join inv_int_media  on inv_int_media.med_id  = interface.int_media ";
        $q_string .= "left join int_speed  on int_speed.spd_id  = interface.int_speed ";
        $q_string .= "left join int_role   on int_role.rol_id   = interface.int_role ";
        $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = " . $a_interface['int_id'] . " ";
        $q_internal = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_internal = mysqli_fetch_array($q_internal)) {

          if ($a_internal['int_virtual']) {
            $virtual = 'Yes';
          } else {
            $virtual = 'No';
          }

          if ($a_internal['int_domain'] == '') {
            $fqdn = $a_internal['int_server'];
          } else {
            $fqdn = $a_internal['int_server'] . "." . $a_internal['int_domain'];
          }

          $q_string  = "select red_text ";
          $q_string .= "from int_redundancy ";
          $q_string .= "where red_id = " . $a_internal['int_redundancy'] . " ";
          $q_int_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_int_redundancy = mysqli_fetch_array($q_int_redundancy);
          if ($a_int_redundancy['red_text'] == '') {
            $a_int_redundancy['red_text'] = "Child";
          }

          $notify = 'None';
          if ($a_internal['int_notify'] == 1) {
            $notify = 'Email';
          }
          if ($a_internal['int_notify'] == 2) {
            $notify = 'Page';
          }

          $hours = 'Business Hours';
          if ($a_internal['int_hours'] == 1) {
            $hours = '24x7';
          }

          $cindex = "child_" . $intcount++;

          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex] = new IP_Address();
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_name         = $a_internal['int_server'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_fqdn         = $fqdn;
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_label        = $a_internal['int_face'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_address      = $a_internal['int_addr'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_ethernet     = $a_internal['int_eth'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_netmask      = $a_internal['int_mask'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_gateway      = $a_internal['int_gate'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_vlan         = $a_internal['int_vlan'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_type         = $a_internal['itp_name'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_default      = ($a_internal['int_primary'] ? "Default Route" : "");
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_role         = $a_internal['rol_text'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_redundant    = $a_int_redundancy['red_text'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_groupname    = $a_internal['int_groupname'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_virtual      = $virtual;
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_netzone      = $a_internal['zone_zone'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_management   = ($a_internal['int_management'] ? 'Yes' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_backup       = ($a_internal['int_backup'] ? 'Yes' : 'No');

          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->physical_port        = $a_internal['int_sysport'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->physical_switch      = $a_internal['int_switch'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->physical_switch_port = $a_internal['int_port'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->physical_media       = $a_internal['med_text'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->physical_speed       = $a_internal['spd_text'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->physical_duplex      = $a_internal['dup_text'];

          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_openview  = ($a_internal['int_openview'] ? 'Monitored' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_nagios    = ($a_internal['int_nagios'] ? 'Monitored' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_ping      = ($a_internal['int_ping'] ? 'Monitored' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_ssh       = ($a_internal['int_ssh'] ? 'Monitored' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_http      = ($a_internal['int_http'] ? 'Monitored' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_ftp       = ($a_internal['int_ftp'] ? 'Monitored' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_smtp      = ($a_internal['int_smtp'] ? 'Monitored' : 'No');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_cfg2html  = ($a_internal['int_cfg2html'] ? 'Ignored' : 'Monitored');
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_notify    = $notify;
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->monitor_hours     = $hours;
        }
      } else {
        if ($a_interface['itp_name'] == 'Management') {
          if ($a_interface['zone_acronym'] == 'C') {
            $servers[$a_inventory['inv_name']]->inventory_network = "Corporate Zone";
          }
          if ($a_interface['zone_acronym'] == 'D') {
            $servers[$a_inventory['inv_name']]->inventory_network = "DMZ";
          }
          if ($a_interface['zone_acronym'] == 'E') {
            $servers[$a_inventory['inv_name']]->inventory_network = "E911 Zone";
          }
        }
      }
    }

    $hwcount = 0;
    $q_string  = "select mod_name ";
    $q_string .= "from hardware ";
    $q_string .= "left join inv_models on inv_models.mod_id = hardware.hw_vendorid ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = 0 and hw_deleted = 0 and hw_primary = 1 ";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_hardware = mysqli_fetch_array($q_hardware);

    $servers[$a_inventory['inv_name']]->inventory_hardware = $a_hardware['mod_name'];
  }

  echo json_encode($servers);

?>
