<?php
# Script: server.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "server.php";

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
  if (isset($_GET['location'])) {
    $formVars['location'] = clean($_GET['location'], 10);
  } else {
    $formVars['location'] = 'no';
  }
  if (isset($_GET['service'])) {
    $formVars['service'] = clean($_GET['service'], 10);
  } else {
    $formVars['service'] = 'no';
  }
  if (isset($_GET['interfaces'])) {
    $formVars['interfaces'] = clean($_GET['interfaces'], 10);
  } else {
    $formVars['interfaces'] = 'no';
  }
  if (isset($_GET['hardware'])) {
    $formVars['hardware'] = clean($_GET['hardware'], 10);
  } else {
    $formVars['hardware'] = 'no';
  }
  if (isset($_GET['vulnerabilities'])) {
    $formVars['vulnerabilities'] = clean($_GET['vulnerabilities'], 10);
  } else {
    $formVars['vulnerabilities'] = 'no';
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

  class Location {
    public $location_type = '';
    public $location_name = '';
    public $location_address1 = '';
    public $location_address2 = '';
    public $location_suite = '';
    public $location_datacenter = '';
    public $location_city = '';
    public $location_state = '';
    public $location_zipcode = '';
    public $location_country = '';
    public $location_clli = '';
    public $location_instance = '';
    public $location_designation = '';
    public $location_environment = '';
  }

  class ServiceClass {
    public $service_name = '';
    public $service_acronym = '';
    public $service_availability = '';
    public $service_downtime = '';
    public $service_mtbf = '';
    public $service_redundant = '';
    public $service_mttr = '';
    public $service_sharing = '';
    public $service_restore = '';
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
    public $interface_scanned = '';
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

  class Hardware {
    public $hardware_type = '';
    public $hardware_serial_number = '';
    public $hardware_asset_tag = '';
    public $hardware_service = '';
    public $hardware_vendor = '';
    public $hardware_project = '';
    public $hardware_product = '';
    public $hardware_admins = '';
    public $hardware_purchased = '';
    public $hardware_built = '';
    public $hardware_active = '';
    public $hardware_eol = '';
  }


  $q_string  = "select inv_id,inv_name,inv_uuid,inv_satuuid,inv_class,inv_location,inv_function,win_text,";
  $q_string .= "inv_document,inv_power,inv_rack,inv_row,inv_unit,prod_name,prj_name,zone_name,grp_name,inv_appadmin ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join maint_window on maint_window.win_id = inventory.inv_maint ";
  $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where inv_status = 0 ";
  if ($formVars['server'] != '') {
    $q_string .= "and inv_name = \"" . $formVars['server'] . "\" ";
  }
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
    $q_string .= "from a_groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);

    $servers[$a_inventory['inv_name']] = new Server();
    $servers[$a_inventory['inv_name']]->inventory_name               = $a_inventory['inv_name'];
    $servers[$a_inventory['inv_name']]->inventory_sysadmins          = $a_inventory['grp_name'];
    $servers[$a_inventory['inv_name']]->inventory_appadmins          = $a_groups['grp_name'];
    $servers[$a_inventory['inv_name']]->inventory_uuid               = $a_inventory['inv_uuid'];
    $servers[$a_inventory['inv_name']]->inventory_satellite_uuid     = $a_inventory['inv_satuuid'];
    $servers[$a_inventory['inv_name']]->inventory_function           = $a_inventory['inv_function'];
    $servers[$a_inventory['inv_name']]->inventory_documentation      = $a_inventory['inv_document'];
    $servers[$a_inventory['inv_name']]->inventory_product            = $a_inventory['prod_name'];
    $servers[$a_inventory['inv_name']]->inventory_project            = $a_inventory['prj_name'];
    $servers[$a_inventory['inv_name']]->inventory_timezone           = $a_inventory['zone_name'];
    $servers[$a_inventory['inv_name']]->inventory_maintenance_window = $a_inventory['win_text'];

    $q_string  = "select typ_name,loc_name,loc_addr1,loc_addr2,loc_suite,ct_city,st_state,loc_zipcode,cn_country,ct_clli,loc_instance,loc_west,loc_environment ";
    $q_string .= "from locations ";
    $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
    $q_string .= "left join states on states.st_id = cities.ct_state ";
    $q_string .= "left join country on country.cn_id = states.st_country ";
    $q_string .= "left join loc_types on loc_types.typ_id = locations.loc_type ";
    $q_string .= "where loc_id = " . $a_inventory['inv_location'] . " ";
    $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_locations = mysqli_fetch_array($q_locations);

    $environment = '';
    if ($a_locations['loc_environment'] == 1) {
      $environment = 'Production';
    }
    if ($a_locations['loc_environment'] == 2) {
      $environment = 'Pre-Production';
    }
    if ($a_locations['loc_environment'] == 3) {
      $environment = 'Quality Assurance';
    }
    if ($a_locations['loc_environment'] == 4) {
      $environment = 'Engineering';
    }
    if ($a_locations['loc_environment'] == 5) {
      $environment = 'Development';
    }

    if ($formVars['location'] == 'yes') {
      $servers[$a_inventory['inv_name']]->inventory_location = new Location();
      $servers[$a_inventory['inv_name']]->inventory_location->location_type        = $a_locations['typ_name'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_name        = $a_locations['loc_name'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_address1    = $a_locations['loc_addr1'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_address2    = $a_locations['loc_addr2'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_suite       = $a_locations['loc_suite'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_datacenter  = $a_inventory['inv_row'] . "-" . $a_inventory['inv_rack'] . "/U" . $a_inventory['inv_unit'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_city        = $a_locations['ct_city'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_state       = $a_locations['st_state'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_zipcode     = $a_locations['loc_zipcode'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_country     = $a_locations['cn_country'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_clli        = $a_locations['ct_clli'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_instance    = $a_locations['loc_instance'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_designation = $a_locations['loc_west'];
      $servers[$a_inventory['inv_name']]->inventory_location->location_environment = $environment;
    } else {
      $servers[$a_inventory['inv_name']]->inventory_location = $a_locations['loc_west'];
    }


    $q_string  = "select svc_name,svc_acronym,svc_availability,svc_downtime,svc_mtbf,svc_geographic,svc_mttr,svc_resource,svc_restore ";
    $q_string .= "from service ";
    $q_string .= "where svc_id = " . $a_inventory['inv_class'] . " ";
    $q_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_service = mysqli_fetch_array($q_service);

    if ($formVars['service'] == 'yes') {
      $servers[$a_inventory['inv_name']]->inventory_service_class = new ServiceClass();
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_name         = $a_service['svc_name'];
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_acronym      = $a_service['svc_acronym'];
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_availability = $a_service['svc_availability'];
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_downtime     = $a_service['svc_downtime'];
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_mtbf         = $a_service['svc_mtbf'];
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_redundant    = ($a_service['svc_geographic'] ? 'Yes' : 'No');
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_mttr         = $a_service['svc_mttr'];
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_sharing      = ($a_service['svc_resource'] ? 'Yes' : 'No');
      $servers[$a_inventory['inv_name']]->inventory_service_class->service_restore      = $a_service['svc_restore'];
    } else {
      $servers[$a_inventory['inv_name']]->inventory_service_class = $a_service['svc_name'];
    }
    
    $count = 0;
    $q_string  = "select int_id,int_server,int_domain,int_face,int_addr,int_eth,int_mask,int_gate,int_vlan,itp_name,";
    $q_string .= "int_openview,int_nagios,int_ping,int_ssh,int_http,int_ftp,int_smtp,int_cfg2html,int_notify,int_hours,";
    $q_string .= "int_primary,int_switch,int_port,med_text,spd_text,dup_text,rol_text,int_management,int_backup,";
    $q_string .= "int_redundancy,int_groupname,int_virtual,zone_name,zone_acronym,int_sysport ";
    $q_string .= "from interface ";
    $q_string .= "left join ip_zones   on ip_zones.zone_id  = interface.int_zone ";
    $q_string .= "left join inttype    on inttype.itp_id    = interface.int_type ";
    $q_string .= "left join int_duplex on int_duplex.dup_id = interface.int_duplex ";
    $q_string .= "left join int_media  on int_media.med_id  = interface.int_media ";
    $q_string .= "left join int_speed  on int_speed.spd_id  = interface.int_speed ";
    $q_string .= "left join int_role   on int_role.rol_id   = interface.int_role ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = 0 ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      if ($formVars['interfaces'] == 'yes') {
        $q_string  = "select vul_id ";
        $q_string .= "from vulnowner ";
        $q_string .= "where vul_interface = " . $a_interface['int_id'] . " ";
        $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . "p: " . mysqli_error($db));
        if (mysqli_num_rows($q_vulnowner) > 0) {
          $scanned = 'Yes';
        } else {
          $scanned = 'No';
        }

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
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_netzone      = $a_interface['zone_name'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_scanned      = $scanned;
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_management   = ($a_interface['int_management'] ? "Yes" : "No");
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_backup       = ($a_interface['int_backup'] ? "Yes" : "No");

        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_port        = $a_interface['int_sysport'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_switch      = $a_interface['int_switch'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_switch_port = $a_interface['int_port'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_media       = $a_interface['med_text'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_speed       = $a_interface['spd_text'];
        $servers[$a_inventory['inv_name']]->inventory_network[$index]->physical_duplex      = $a_interface['dup_text'];

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
        $q_string .= "int_redundancy,int_groupname,int_virtual,zone_name,int_sysport ";
        $q_string .= "from interface ";
        $q_string .= "left join ip_zones   on ip_zones.zone_id  = interface.int_zone ";
        $q_string .= "left join inttype    on inttype.itp_id    = interface.int_type ";
        $q_string .= "left join int_duplex on int_duplex.dup_id = interface.int_duplex ";
        $q_string .= "left join int_media  on int_media.med_id  = interface.int_media ";
        $q_string .= "left join int_speed  on int_speed.spd_id  = interface.int_speed ";
        $q_string .= "left join int_role   on int_role.rol_id   = interface.int_role ";
        $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = " . $a_interface['int_id'] . " ";
        $q_internal = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_internal = mysqli_fetch_array($q_internal)) {

          $q_string  = "select vul_id ";
          $q_string .= "from vulnowner ";
          $q_string .= "where vul_interface = " . $a_internal['int_id'] . " ";
          $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . "c: " . mysqli_error($db));
          if (mysqli_num_rows($q_vulnowner) > 0) {
            $scanned = 'Yes';
          } else {
            $scanned = 'No';
          }

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
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_netzone      = $a_internal['zone_name'];
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_scanned      = $scanned;
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_management   = ($a_internal['int_management'] ? "Yes" : "No");
          $servers[$a_inventory['inv_name']]->inventory_network[$index]->inventory_network[$cindex]->interface_backup       = ($a_internal['int_backup'] ? "Yes" : "No");

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
    $q_string  = "select hw_id,part_name,hw_serial,hw_asset,hw_service,mod_vendor,mod_name,mod_type,mod_size,mod_speed,prj_name,prod_name,hw_group,";
    $q_string .= "hw_purchased,hw_built,hw_active,hw_eol,hw_primary ";
    $q_string .= "from hardware ";
    $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= "left join projects on projects.prj_id = hardware.hw_projectid ";
    $q_string .= "left join products on products.prod_id = hardware.hw_product ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = 0 and hw_deleted = 0 ";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_hardware = mysqli_fetch_array($q_hardware)) {

      $q_string  = "select grp_name ";
      $q_string .= "from a_groups ";
      $q_string .= "where grp_id = " . $a_hardware['hw_group'] . " ";
      $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_groups = mysqli_fetch_array($q_groups);

      if ($a_hardware['mod_type'] != '') {
        $q_string  = "select part_name ";
        $q_string .= "from parts ";
        $q_string .= "where part_id = " . $a_hardware['mod_type'] . " ";
        $q_parts = mysqli_query($db, $q_string) or die($q_string . "p: " . mysqli_error($db));
        $a_parts = mysqli_fetch_array($q_parts);
      } else {
        $a_parts['part_name'] = 'Unknown';
      }

      $index = "hardware_" . $hwcount++;

      if ($formVars['hardware'] == 'yes') {
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index] = new Hardware();
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_type           = $a_hardware['part_name'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_serial_number  = $a_hardware['hw_serial'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_asset_tag      = $a_hardware['hw_asset'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_service        = $a_hardware['hw_service'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_vendor         = $a_hardware['mod_vendor'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_model          = $a_hardware['mod_name'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_model_type     = $a_parts['part_name'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_size           = $a_hardware['mod_size'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_speed          = $a_hardware['mod_speed'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_project        = $a_hardware['prj_name'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_product        = $a_hardware['prod_name'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_admins         = $a_groups['grp_name'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_purchased      = $a_hardware['hw_purchased'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_built          = $a_hardware['hw_built'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_active         = $a_hardware['hw_active'];
        $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->hardware_eol            = $a_hardware['hw_eol'];


        $hwintcount = 0;
        $q_string  = "select hw_id,part_name,hw_serial,hw_asset,hw_service,mod_vendor,mod_name,mod_type,mod_size,mod_speed,prj_name,prod_name,hw_group,";
        $q_string .= "hw_purchased,hw_built,hw_active,hw_eol ";
        $q_string .= "from hardware ";
        $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
        $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
        $q_string .= "left join projects on projects.prj_id = hardware.hw_projectid ";
        $q_string .= "left join products on products.prod_id = hardware.hw_product ";
        $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " ";
        $q_internal = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_internal = mysqli_fetch_array($q_internal)) {

          $q_string  = "select grp_name ";
          $q_string .= "from a_groups ";
          $q_string .= "where grp_id = " . $a_internal['hw_group'] . " ";
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_groups = mysqli_fetch_array($q_groups);

          if ($a_internal['mod_type'] != '') {
            $q_string  = "select part_name ";
            $q_string .= "from parts ";
            $q_string .= "where part_id = " . $a_internal['mod_type'] . " ";
            $q_parts = mysqli_query($db, $q_string) or die($q_string . "c: " . mysqli_error($db));
            $a_parts = mysqli_fetch_array($q_parts);
          } else {
            $a_parts['part_name'] = 'Unknown';
          }

          $cindex = "child_" . $hwintcount++;

          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex] = new Hardware();
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_type           = $a_internal['part_name'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_serial_number  = $a_internal['hw_serial'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_asset_tag      = $a_internal['hw_asset'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_service        = $a_internal['hw_service'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_vendor         = $a_internal['mod_vendor'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_model          = $a_internal['mod_name'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_model_type     = $a_parts['part_name'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_size           = $a_internal['mod_size'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_speed          = $a_internal['mod_speed'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_project        = $a_internal['prj_name'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_product        = $a_internal['prod_name'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_admins         = $a_groups['grp_name'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_purchased      = $a_internal['hw_purchased'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_built          = $a_internal['hw_built'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_active         = $a_internal['hw_active'];
          $servers[$a_inventory['inv_name']]->inventory_hardware[$index]->inventory_hardware[$cindex]->hardware_eol            = $a_internal['hw_eol'];
        }
      } else {
        if ($a_hardware['hw_primary'] == 1) {
          $servers[$a_inventory['inv_name']]->inventory_hardware = $a_hardware['mod_name'];
        }
      }
    }
  }

  echo json_encode($servers);

?>
