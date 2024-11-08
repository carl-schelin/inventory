#!/usr/local/bin/php
<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: This script reads the command line for the server name and optional parameters
# by default, it shows the main support page
# Usage: inventory.php -hsnc -p product search|server
#  -h List of hardware
#  -s List of software
#  -n Interface listing
#  -p product - Product listing
#  -r serverid - Remove this server from the inventory
#  search of server or product

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $force = '';
  $retired = '';
  $server = '';
  $ipaddr = '';
  $macaddr = '';
  $project = '';
  $serialnumber = '';
  $assettag = '';
  $hardware = 'no';
  $software = 'no';
  $network = 'no';
  $csv = 'no';
  $remove = 0;

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the server name at the minimum.\n\n";
    print " Usage: inventory.php [device | [ -fhsnc [ -p product | -d device | -a ipaddress | -m macaddress ] ]\n";
    print " Passing the Device name by itself returns the Core information about the Device or you can use the parameters below to get more information\n\n";
    print "  -f List from retired servers\n";
    print "  -h In addition to core info, list server hardware\n";
    print "  -s In addition to core info, list server software\n";
    print "  -n In addition to core info, list server interfaces and network info\n";
    print "  -c Output is in csv format with header line\n";
    print "  -p product - Product listing\n";
    print "  -d device      - Core Device listing\n";
    print "  -a IP Address  - Core Device listing\n";
    print "  -m MAC Address - Core Device listing\n";
    print "  -t Asset Tag   - Core Device listing\n";
    print "  -v Serial Number/Dell Service Tag - Core Device listing\n";
    print "  -r serverid - Remove server from the Inventory. You MUST pass the server ID and not the server NAME! This ensures the correct server is being removed.\n";

    exit(1);
  }

  if ($argc == 2) {
    $server = $argv[1];
  } else {
    $options = getopt("fhsncp:r:d:a:m:t:v:");

    if (isset($options['f'])) {
      $force = 'yes';
    }
    if (isset($options['c'])) {
      $csv = 'yes';
    }
    if (isset($options['d'])) {
      $server = $options['d'];
      $serialnumber = '';
      $assettag = '';
      $ipaddr = '';
      $macaddr = '';
    }
    if (isset($options['h'])) {
      $hardware = 'yes';
    }
    if (isset($options['n'])) {
      $network = 'yes';
    }
    if (isset($options['p'])) {
      $project = $options['p'];
    }
    if (isset($options['a'])) {
      $ipaddr = $options['a'];
      $serialnumber = '';
      $assettag = '';
      $server = '';
      $macaddr = '';
    }
    if (isset($options['m'])) {
      $macaddr = $options['m'];
      $serialnumber = '';
      $assettag = '';
      $ipaddr = '';
      $server = '';
    }
    if (isset($options['t'])) {
      $assettag = $options['t'];
      $serialnumber = '';
      $macaddr = '';
      $ipaddr = '';
      $server = '';
    }
    if (isset($options['v'])) {
      $serialnumber = $options['v'];
      $assettag = '';
      $macaddr = '';
      $ipaddr = '';
      $server = '';
    }
    if (isset($options['r'])) {
      $remove = $options['r'];
      $hardware = 'yes';
      $network = 'yes';
      $software = 'yes';
    }
    if (isset($options['s'])) {
      $software = 'yes';
    }
  }

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'no';
  $debug = 'yes';

# if removing, query the server to get the server name for the lookup and then remove it after questioned.

  $q_string  = "select inv_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "where inv_id = " . $remove . " ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_inventory) > 0) {
    $a_inv_inventory = mysqli_fetch_array($q_inv_inventory);
    $server = $a_inv_inventory['inv_name'];
  }

  $q_string  = "select inv_id,inv_name,inv_companyid,inv_function,prod_name,grp_name,inv_appadmin,ven_name,mod_name,hw_serial,";
  $q_string .= "hw_asset,hw_service,loc_name,loc_addr1,ct_city,st_state,loc_zipcode,inv_status,inv_rack,inv_row,inv_unit ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_products  on inv_products.prod_id        = inv_inventory.inv_product ";
  $q_string .= "left join inv_groups    on inv_groups.grp_id           = inv_inventory.inv_manager ";
  $q_string .= "left join inv_hardware  on inv_hardware.hw_companyid   = inv_inventory.inv_id ";
  $q_string .= "left join inv_models    on inv_models.mod_id           = inv_hardware.hw_vendorid ";
  $q_string .= "left join inv_vendors   on inv_vendors.ven_id          = inv_models.mod_vendor ";
  $q_string .= "left join inv_locations on inv_locations.loc_id        = inv_inventory.inv_location ";
  $q_string .= "left join inv_cities    on inv_cities.ct_id            = inv_locations.loc_city ";
  $q_string .= "left join inv_states    on inv_states.st_id            = inv_locations.loc_state ";
  $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_inventory.inv_id ";
  if (strlen($server) > 0) {
    $q_string .= "where inv_name = '" . $server . "' ";
  }
  if (strlen($ipaddr) > 0) {
    $q_string .= "where int_addr = '" . $ipaddr . "' ";
  }
  if (strlen($macaddr) > 0) {
    $q_string .= "where int_eth = '" . $macaddr . "' ";
  }
  if (strlen($serialnumber) > 0) {
    $q_string .= "where hw_serial = '" . $serialnumber . "' ";
  }
  if (strlen($assettag) > 0) {
    $q_string .= "where hw_asset = '" . $assettag . "' ";
  }
  if ($force == '') {
    $q_string .= "and inv_status = 0 ";
  }
  if ($remove > 0) {
    $q_string .= "and inv_id = " . $remove . " ";
  }
  $q_string .= "and hw_primary = 1 ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_inventory) > 0) {
    $a_inv_inventory = mysqli_fetch_array($q_inv_inventory);

    if ($a_inv_inventory['inv_status'] == 1 ) {
      $retired = " == RETIRED ==";
    }

    $q_string  = "select grp_name ";
    $q_string .= "from inv_groups ";
    $q_string .= "where grp_id = " . $a_inv_inventory['inv_appadmin'] . " ";
    $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_inv_groups = mysqli_fetch_array($q_inv_groups);

    print "--------------------\n";
    print "Inventory Management\n";
    print "--------------------\n";
    if ($csv == 'yes') {
      print "\"ID\",\"Server\",\"Function\",\"Product\",\"Platform Managed By\",\"Applications Managed By\",\"Vendor\",\"Model\",\"Serial Number\",\"Asset Tag\",\"Dell Service Tag\",\"Data Center\",\"Address\",";
      if ($a_inv_inventory['inv_companyid']) {
        print "\"Chassis\",\"Chassis Rack/Unit\",\"Blade Number\"\n";
      } else {
        print "\"Rack/Unit\"\n";
      }
      print "\"" . $a_inv_inventory['inv_id']              . "\",";
      print "\"" . $a_inv_inventory['inv_name'] . $retired . "\",";
      print "\"" . $a_inv_inventory['inv_function']        . "\",";
      print "\"" . $a_inv_inventory['prod_name']           . "\",";
      print "\"" . $a_inv_inventory['grp_name']            . "\",";
      print "\"" . $a_inv_groups['grp_name']               . "\",";
      print "\"" . $a_inv_inventory['ven_name']            . "\",";
      print "\"" . $a_inv_inventory['mod_name']            . "\",";
      print "\"" . $a_inv_inventory['hw_serial']           . "\",";
      print "\"" . $a_inv_inventory['hw_asset']            . "\",";
      print "\"" . $a_inv_inventory['hw_service']          . "\",";
      print "\"" . $a_inv_inventory['loc_name']            . "\",";
      print "\"" . $a_inv_inventory['loc_addr1'] . " " . $a_inv_inventory['ct_city'] . " " . $a_inv_inventory['st_state'] . " " . $a_inv_inventory['loc_zipcode'] . "\",";
      if ($a_inv_inventory['inv_companyid']) {
        $q_string  = "select inv_name,inv_rack,inv_row,inv_unit ";
        $q_string .= "from inv_inventory ";
        $q_string .= "where inv_id = " . $a_inv_inventory['inv_companyid'] . " ";
        $q_chassis = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_chassis = mysqli_fetch_array($q_chassis);

        print "\"" . $a_chassis['inv_name'] . "\",";
        print "\"" . $a_chassis['inv_rack'] . "-" . $a_chassis['inv_row'] . "/U" . $a_chassis['inv_unit'] . "\",";
        print "\"" . $a_inv_inventory['inv_unit'] . "\"\n";
      } else {
        print "\"" . $a_inv_inventory['inv_rack'] . "-" . $a_inv_inventory['inv_row'] . "/U" . $a_inv_inventory['inv_unit'] . "\"\n";
      }
    } else {
      print "Server ID: " . $a_inv_inventory['inv_id'] . "\n";
      print "Server: " . $a_inv_inventory['inv_name'] . $retired . "\n";
      print "Function: " . $a_inv_inventory['inv_function'] . "\n";
      print "Product: " . $a_inv_inventory['prod_name'] . "\n";
      print "Platform Managed By: " . $a_inv_inventory['grp_name'] . "\n";
      print "Applications Managed By: " . $a_inv_groups['grp_name'] . "\n";
      print "----------------------------\n";
      print "Primary Hardware Information\n";
      print "----------------------------\n";
      print "Vendor: " . $a_inv_inventory['ven_name'] . "\n";
      print "Model: " . $a_inv_inventory['mod_name'] . "\n";
      print "Serial Number: " . $a_inv_inventory['hw_serial'] . "\n";
      print "Asset Tag: " . $a_inv_inventory['hw_asset'] . "\n";
      print "Dell Service Tag: " . $a_inv_inventory['hw_service'] . "\n";
      print "--------------------\n";
      print "Location Information\n";
      print "--------------------\n";
      print "Data Center: " . $a_inv_inventory['loc_name'] . "\n";
      print "Address: " . $a_inv_inventory['loc_addr1'] . " " . $a_inv_inventory['ct_city'] . " " . $a_inv_inventory['st_state'] . " " . $a_inv_inventory['loc_zipcode'] . "\n";
      if ($a_inv_inventory['inv_companyid']) {
        $q_string  = "select inv_name,inv_rack,inv_row,inv_unit ";
        $q_string .= "from inv_inventory ";
        $q_string .= "where inv_id = " . $a_inv_inventory['inv_companyid'] . " ";
        $q_chassis = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_chassis = mysqli_fetch_array($q_chassis);

        print "Chassis: " . $a_chassis['inv_name'] . "\n";
        print "Chassis Rack/Unit: " . $a_chassis['inv_rack'] . "-" . $a_chassis['inv_row'] . "/U" . $a_chassis['inv_unit'] . "\n";
        print "Blade Number: " . $a_inv_inventory['inv_unit'] . "\n";
      } else {
        print "Rack/Unit: " . $a_inv_inventory['inv_rack'] . "-" . $a_inv_inventory['inv_row'] . "/U" . $a_inv_inventory['inv_unit'] . "\n";
      }
    }


    if ($hardware == 'yes') {
      print "--------------------\n";
      print "Hardware Information\n";
      print "--------------------\n";

      if ($csv == 'yes') {
        print "\"Serial\", \"Asset\",\"Service\",\"Vendor\",\"Model\",\"Size\",\"Speed\",\"Type\"\n";
      } else {
        printf("%20s %10s %8s %20s %30s %20s %15s %20s\n", "Serial", "Asset", "Service", "Vendor", "Model", "Size", "Speed", "Type");
      }

      $q_string  = "select hw_id,hw_serial,hw_asset,hw_service,hw_vendorid,part_name,hw_verified,hw_update ";
      $q_string .= "from inv_hardware ";
      $q_string .= "left join inv_parts on inv_parts.part_id = inv_hardware.hw_type ";
      $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inv_inventory['inv_id'] . " and hw_hw_id = 0 and hw_hd_id = 0 ";
      $q_string .= "order by part_name";
      $q_inv_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_inv_hardware = mysqli_fetch_array($q_inv_hardware)) {
        $q_string  = "select ven_name,mod_name,mod_size,mod_speed ";
        $q_string .= "from inv_models ";
        $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
        $q_string .= "where mod_id = " . $a_inv_hardware['hw_vendorid'] . " ";
        $q_inv_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_inv_models = mysqli_fetch_array($q_inv_models);

        if ($csv == 'yes') {
          print "\"" . $a_inv_hardware['hw_serial']  . "\",";
          print "\"" . $a_inv_hardware['hw_asset']   . "\",";
          print "\"" . $a_inv_hardware['hw_service'] . "\",";
          print "\"" . $a_inv_models['ven_name']     . "\",";
          print "\"" . $a_inv_models['mod_name']     . "\",";
          print "\"" . $a_inv_models['mod_size']     . "\",";
          print "\"" . $a_inv_models['mod_speed']    . "\",";
          print "\"" . $a_inv_hardware['part_name']  . "\"\n";
        } else {
          printf("%20s %10s %8s %20s %30s %20s %15s %20s\n", $a_inv_hardware['hw_serial'], $a_inv_hardware['hw_asset'], $a_inv_hardware['hw_service'], $a_inv_models['ven_name'], $a_inv_models['mod_name'], $a_inv_models['mod_size'], $a_inv_models['mod_speed'], $a_inv_hardware['part_name']);
        }

        $q_string  = "select hw_id,hw_serial,hw_asset,hw_service,hw_vendorid,part_name,hw_verified,hw_update ";
        $q_string .= "from inv_hardware ";
        $q_string .= "left join inv_parts on inv_parts.part_id = inv_hardware.hw_type ";
        $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inv_inventory['inv_id'] . " and hw_hw_id = " . $a_inv_hardware['hw_id'] . " and hw_hd_id = 0 ";
        $q_string .= "order by part_name";
        $q_hwselect = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        while ($a_hwselect = mysqli_fetch_array($q_hwselect)) {
          $q_string  = "select ven_name,mod_name,mod_size,mod_speed ";
          $q_string .= "from inv_models ";
          $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
          $q_string .= "where mod_id = " . $a_hwselect['hw_vendorid'] . " ";
          $q_inv_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
          $a_inv_models = mysqli_fetch_array($q_inv_models);

          if ($csv == 'yes') {
            print "\"" . $a_hwselect['hw_serial']  . "\",";
            print "\"" . $a_hwselect['hw_asset']   . "\",";
            print "\"" . $a_hwselect['hw_service'] . "\",";
            print "\"" . $a_inv_models['ven_name']   . "\",";
            print "\"" . $a_inv_models['mod_name']     . "\",";
            print "\"" . $a_inv_models['mod_size']    . "\",";
            print "\"" . $a_inv_models['mod_speed']   . "\",";
            print "\"" . $a_hwselect['part_name']  . "\"\n";
          } else {
            printf("%20s %10s %8s %20s >%29s %20s %15s %20s\n", $a_hwselect['hw_serial'], $a_hwselect['hw_asset'], $a_hwselect['hw_service'], $a_inv_models['ven_name'], $a_inv_models['mod_name'], $a_inv_models['mod_size'], $a_inv_models['mod_speed'], $a_hwselect['part_name']);
          }

          $q_string  = "select hw_id,hw_serial,hw_asset,hw_service,hw_vendorid,part_name,hw_verified,hw_update ";
          $q_string .= "from inv_hardware ";
          $q_string .= "left join inv_parts on inv_parts.part_id = inv_hardware.hw_type ";
          $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inv_inventory['inv_id'] . " and hw_hw_id = " . $a_inv_hardware['hw_id'] . " and hw_hd_id = " . $a_hwselect['hw_id'] . " ";
          $q_string .= "order by part_name";
          $q_hwdisk = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
          while ($a_hwdisk = mysqli_fetch_array($q_hwdisk)) {
            $q_string  = "select ven_name,mod_name,mod_size,mod_speed ";
            $q_string .= "from inv_models ";
            $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
            $q_string .= "where mod_id = " . $a_hwdisk['hw_vendorid'] . " ";
            $q_inv_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
            $a_inv_models = mysqli_fetch_array($q_inv_models);

            if ($csv == 'yes') {
              print "\"" . $a_hwdisk['hw_serial']  . "\",";
              print "\"" . $a_hwdisk['hw_asset']   . "\",";
              print "\"" . $a_hwdisk['hw_service'] . "\",";
              print "\"" . $a_inv_models['ven_name']   . "\",";
              print "\"" . $a_inv_models['mod_name']   . "\",";
              print "\"" . $a_inv_models['mod_size']   . "\",";
              print "\"" . $a_inv_models['mod_speed']  . "\",";
              print "\"" . $a_hwdisk['part_name']  . "\"\n";
            } else {
              printf("%20s %10s %8s %20s >>%28s %20s %15s %20s\n", $a_hwdisk['hw_serial'], $a_hwdisk['hw_asset'], $a_hwdisk['hw_service'], $a_inv_models['ven_name'], $a_inv_models['mod_name'], $a_inv_models['mod_size'], $a_inv_models['mod_speed'], $a_hwdisk['part_name']);
            }
          }
        }
      }
    }

    if ($software == 'yes') {
      print "--------------------\n";
      print "Software Information\n";
      print "--------------------\n";

      if ($csv == 'yes') {
        print "\"Product\",\"Vendor\",\"Software\",\"Type\",\"Group\"\n";
      } else {
        printf("%20s %20s %100s %15s %30s\n", "Product", "Vendor", "Software", "Type", "Group");
      }

      $q_string  = "select sw_product,ven_name,sw_software,typ_name,svr_groupid,svr_verified,svr_update ";
      $q_string .= "from inv_software ";
      $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid = inv_software.sw_id ";
      $q_string .= "left join inv_vendors      on inv_vendors.ven_id              = inv_software.sw_vendor ";
      $q_string .= "left join inv_sw_types     on inv_sw_types.typ_id             = inv_software.sw_type ";
      $q_string .= "where (typ_name != 'PKG' and typ_name != 'RPM') and svr_companyid = " . $a_inv_inventory['inv_id'] . " ";
      $q_string .= "order by sw_software";
      $q_inv_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_inv_software = mysqli_fetch_array($q_inv_software)) {
        $q_string = "select prod_name from inv_products where prod_id = " . $a_inv_software['sw_product'];
        $q_inv_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_inv_products = mysqli_fetch_array($q_inv_products);

        $q_string  = "select grp_name ";
        $q_string .= "from inv_groups ";
        $q_string .= "where grp_id = " . $a_inv_software['svr_groupid'];
        $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ":(5): " . mysqli_error($db) . "\n\n");
        $a_inv_groups = mysqli_fetch_array($q_inv_groups);

        if ($csv == 'yes') {
          print "\"" . $a_inv_products['prod_name']   . "\",";
          print "\"" . $a_inv_software['ven_name']   . "\",";
          print "\"" . $a_inv_software['sw_software'] . "\",";
          print "\"" . $a_inv_software['typ_name']     . "\",";
          print "\"" . $a_inv_groups['grp_name']      . "\"\n";
        } else {
          printf("%20s %20s %100s %15s %30s\n", $a_inv_products['prod_name'], $a_inv_software['ven_name'], $a_inv_software['sw_software'], $a_inv_software['typ_name'], $a_inv_groups['grp_name']);
        }
        
      }
    }

    if ($network == 'yes') {
      print "-------------------\n";
      print "Network Information\n";
      print "-------------------\n";

      if ($csv == 'yes') {
        print "\"Name\",\"Interface\",\"IP Address/Mask\",\"MAC\",\"Gateway\",\"Type\",\"Zone\"\n";
      } else {
        printf("%30s %10s %20s %20s %20s %5s %10s\n", "Name", "Interface", "IP Address/Mask", "MAC", "Gateway", "Type", "Zone");
      }

      $q_string  = "select int_server,int_face,int_ip6,int_addr,int_eth,int_mask,int_gate,int_verified,int_type,int_update,zone_zone ";
      $q_string .= "from inv_interface ";
      $q_string .= "left join inv_net_zones on inv_net_zones.zone_id = inv_interface.int_zone ";
      $q_string .= "where int_companyid = " . $a_inv_inventory['inv_id'] . " and int_ip6 = 0 ";
      $q_string .= "order by int_face";
      $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_inv_interface = mysqli_fetch_array($q_inv_interface)) {
        $q_string  = "select itp_acronym ";
        $q_string .= "from inv_int_types ";
        $q_string .= "where itp_id = " . $a_inv_interface['int_type'];
        $q_inv_int_types = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_inv_int_types = mysqli_fetch_array($q_inv_int_types);

        if ($csv == 'yes') {
          print "\"" . $a_inv_interface['int_server'] . "\",";
          print "\"" . $a_inv_interface['int_face']   . "\",";
          print "\"" . $a_inv_interface['int_addr']   . "/" . $a_inv_interface['int_mask'] . "\",";
          print "\"" . $a_inv_interface['int_eth']    . "\",";
          print "\"" . $a_inv_interface['int_gate']   . "\",";
          print "\"" . $a_inv_int_types['itp_acronym']  . "\"\n";
          print "\"" . $a_inv_interface['zone_zone']  . "\"\n";
        } else {
          printf("%30s %10s %20s %20s %20s %5s %10s\n", $a_inv_interface['int_server'], $a_inv_interface['int_face'], $a_inv_interface['int_addr'] . "/" . $a_inv_interface['int_mask'], $a_inv_interface['int_eth'], $a_inv_interface['int_gate'], $a_inv_int_types['itp_acronym'], $a_inv_interface['zone_zone']);
        }
      }
    }

    if ($remove > 0) {

# Need to grep for 'companyid' and in a couple of places, 'inv_id'
# cd /var/tmp/mysql/carl/inventory/inventory
# egrep "(companyid|inv_id)" *sql
#inv_backups.sql:       `bu_companyid`    int(10) NOT NULL default '0',
#inv_cluster.sql:       `clu_companyid`   int(10) NOT NULL default '0',
#inv_filesystem.sql:    `fs_companyid`    int(10) NOT NULL default '0',
#inv_hardware.sql:      `hw_companyid`    int(8)  NOT NULL default '0',
#inv_interface.sql:     `int_companyid`   int(10) NOT NULL default '0',
#inv_inventory.sql:     `inv_companyid`   int(10) NOT NULL default '0',
#inv_ip_addresses.sql:  `ip_companyid`    int(10) NOT NULL default '0',
#inv_issue.sql:         `iss_companyid`   int(10) NOT NULL default '0',
#inv_packages.sql:      `pkg_inv_id`      int(10) NOT NULL default '0',
#inv_routing.sql:       `route_companyid` int(10) NOT NULL default '0',
#inv_svr_software.sql:  `svr_companyid`   int(10) NOT NULL default '0',
#inv_sysgrp.sql:        `grp_companyid`   int(10) NOT NULL default '0',
#inv_syspwd.sql:        `pwd_companyid`   int(10) NOT NULL default '0',
#inv_tags.sql:          `tag_companyid`   int(10) NOT NULL default '0',

      print "\n\n=========================[REMOVE SERVER]=========================\n\n";
      print "You have indicated you want to remove this server from the inventory.\n\n";
      print "Servers are typically retired in order to maintain continuity and \n";
      print "records on server builds over the years plus retirements.\n\n";
      print "Only delete servers when there are actual problems or false entries.\n";
      print "If a server is being retired or replaced, use the proper mechanisms \n";
      print "located in the Inventory.\n\n";

# if we're here, we do want to delete the server.
# let's show the number of entries for each of the tables for this server id.
      $backups = 0;
      $q_string  = "select bu_companyid ";
      $q_string .= "from inv_backups ";
      $q_string .= "where bu_companyid = " . $remove . " ";
      $q_inv_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_backups) > 0) {
        print "There is a backup record " . mysqli_num_rows($q_inv_backups) . " for " . $a_inv_inventory['inv_name'] . "\n";
        $backups = mysqli_num_rows($q_inv_backups);
      }

      $cluster = 0;
      $q_string  = "select clu_companyid ";
      $q_string .= "from inv_cluster ";
      $q_string .= "where clu_companyid = " . $remove . " ";
      $q_inv_cluster = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_cluster) > 0) {
        print "There are " . mysqli_num_rows($q_inv_cluster) . " cluster records for " . $a_inv_inventory['inv_name'] . "\n";
        $cluster = mysqli_num_rows($q_inv_cluster);
      }

      $filesystem = 0;
      $q_string  = "select fs_companyid ";
      $q_string .= "from inv_filesystem ";
      $q_string .= "where fs_companyid = " . $remove . " ";
      $q_inv_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_filesystem) > 0) {
        print "There are " . mysqli_num_rows($q_inv_filesystem) . " filesystem records for " . $a_inv_inventory['inv_name'] . "\n";
        $filesystem = mysqli_num_rows($q_inv_filesystem);
      }

      $hardware = 0;
      $q_string  = "select hw_companyid ";
      $q_string .= "from inv_hardware ";
      $q_string .= "where hw_companyid = " . $remove . " ";
      $q_inv_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_hardware) > 0) {
        print "There are " . mysqli_num_rows($q_inv_hardware) . " hardware records for " . $a_inv_inventory['inv_name'] . "\n";
        $hardware = mysqli_num_rows($q_inv_hardware);
      }

      $interface = 0;
      $q_string  = "select int_id,int_companyid,int_addr ";
      $q_string .= "from inv_interface ";
      $q_string .= "where int_companyid = " . $remove . " ";
      $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_interface) > 0) {
        print "There are " . mysqli_num_rows($q_inv_interface) . " interface records for " . $a_inv_inventory['inv_name'] . "\n";
        $interface = mysqli_num_rows($q_inv_interface);
      }

      $issues = 0;
      $q_string  = "select iss_id,iss_companyid ";
      $q_string .= "from inv_issue ";
      $q_string .= "where iss_companyid = " . $remove . " ";
      $q_inv_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_issue) > 0) {
        print "There are " . mysqli_num_rows($q_inv_issue) . " issue tracker records for " . $a_inv_inventory['inv_name'] . "\n";
        $issues = mysqli_num_rows($q_inv_issue);
        while ($a_inv_issue = mysqli_fetch_array($q_inv_issue)) {
          $q_string  = "select det_issue ";
          $q_string .= "from inv_issue_detail ";
          $q_string .= "where det_issue = " . $a_inv_issue['iss_id'];
          $q_inv_issue_detail = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_issue_detail) > 0) {
            print " -- There are " . mysqli_num_rows($q_inv_issue_detail) . " detail records for issue " . $a_inv_issue['iss_id'] . "\n";
          }
          $q_string  = "select sup_issue ";
          $q_string .= "from inv_issue_support ";
          $q_string .= "where sup_issue = " . $a_inv_issue['iss_id'];
          $q_inv_issue_support = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_issue_support) > 0) {
            print " -- There are " . mysqli_num_rows($q_inv_issue_support) . " support records for issue " . $a_inv_issue['iss_id'] . "\n";
          }
        }
      }

      $packages = 0;
      $q_string  = "select pkg_inv_id ";
      $q_string .= "from inv_packages ";
      $q_string .= "where pkg_inv_id = " . $remove . " ";
      $q_inv_packages = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_packages) > 0) {
        print "There are " . mysqli_num_rows($q_inv_packages) . " package records for " . $a_inv_inventory['inv_name'] . "\n";
        $packages = mysqli_num_rows($q_inv_packages);
      }

      $routes = 0;
      $q_string  = "select route_companyid ";
      $q_string .= "from inv_routing ";
      $q_string .= "where route_companyid = " . $remove . " ";
      $q_inv_routing = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_routing) > 0) {
        print "There are " . mysqli_num_rows($q_inv_routing) . " routing records for " . $a_inv_inventory['inv_name'] . "\n";
        $routes = mysqli_num_rows($q_inv_routing);
      }

      $svr_software = 0;
      $q_string  = "select svr_companyid ";
      $q_string .= "from inv_svr_software ";
      $q_string .= "where svr_companyid = " . $remove . " ";
      $q_inv_svr_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_svr_software) > 0) {
        print "There are " . mysqli_num_rows($q_inv_svr_software) . " software records for " . $a_inv_inventory['inv_name'] . "\n";
        $svr_software = mysqli_num_rows($q_inv_svr_software);
      }

      $groups = 0;
      $q_string  = "select grp_companyid ";
      $q_string .= "from inv_sysgrp ";
      $q_string .= "where grp_companyid = " . $remove . " ";
      $q_inv_sysgrp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_sysgrp) > 0) {
        print "There are " . mysqli_num_rows($q_inv_sysgrp) . " system group records for " . $a_inv_inventory['inv_name'] . "\n";
        $groups = mysqli_num_rows($q_inv_sysgrp);
      }

      $users = 0;
      $q_string  = "select pwd_companyid ";
      $q_string .= "from inv_syspwd ";
      $q_string .= "where pwd_companyid = " . $remove . " ";
      $q_inv_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_syspwd) > 0) {
        print "There are " . mysqli_num_rows($q_inv_syspwd) . " system user records for " . $a_inv_inventory['inv_name'] . "\n";
        $users = mysqli_num_rows($q_inv_syspwd);
      }

      $tags = 0;
      $q_string  = "select tag_companyid ";
      $q_string .= "from inv_tags ";
      $q_string .= "where tag_companyid = " . $remove . " ";
      $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_tags) > 0) {
        print "There are " . mysqli_num_rows($q_inv_tags) . " tags records for " . $a_inv_inventory['inv_name'] . "\n";
        $tags = mysqli_num_rows($q_inv_tags);
      }


      if (rand(0,1)) {
        print "Are you sure you want to delete this server from the Inventory? (y/N) ";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if (trim($line) != 'y'){
          print "Abandoning delete!\n";
          exit;
        }
      } else {
        print "Cancel this request and don't delete this server? (Y/n) ";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if (trim($line) != 'n'){
          print "Abandoning delete!\n";
          exit;
        }
      }


##########################
##########################
##########################

      print "Deleting ";

      if ($remove > 0) {
        print "Inventory ";
        $q_string = "delete from inv_inventory     where inv_id = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($backups > 0) {
        print "Backups ";
        $q_string = "delete from inv_backups    where bu_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($cluster > 0) {
        print "Cluster ";
        $q_string = "delete from inv_cluster    where clu_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($filesystem > 0) {
        print "Filesystem ";
        $q_string = "delete from inv_filesystem where fs_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($hardware > 0) {
        print "Hardware ";
        $q_string = "delete from inv_hardware   where hw_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($interface > 0) {
        print "Interfaces ";
        $q_string  = "select int_id,int_companyid,int_addr ";
        $q_string .= "from inv_interface ";
        $q_string .= "where int_companyid = " . $remove . " ";
        $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inv_interface) > 0) {
          $q_string = "delete from inv_interface where int_companyid = " . $remove;
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }
      }

      if ($issues > 0) {
        print "Issues ";
# need to loop through the issues in order to clean out the sub-tables
        $q_string  = "select iss_id,iss_companyid ";
        $q_string .= "from inv_issue ";
        $q_string .= "where iss_companyid = " . $remove . " ";
        $q_inv_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inv_issue) > 0) {
          while ($a_inv_issue = mysqli_fetch_array($q_inv_issue)) {
            $q_string = "delete from inv_issue_detail where det_issue = " . $a_inv_issue['iss_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $q_string = "delete from inv_issue_support where sup_issue = " . $a_inv_issue['iss_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          }
          $q_string = "delete from inv_issue      where iss_companyid   = " . $remove;
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }
      }

      if ($packages > 0) {
        print "Packages ";
        $q_string = "delete from inv_packages   where pkg_inv_id      = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($routes > 0) {
        print "Route Tables ";
        $q_string = "delete from inv_routing    where route_companyid = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($svr_software > 0) {
        print "Software ";
        $q_string = "delete from inv_svr_software   where svr_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($groups > 0) {
        print "System Groups ";
        $q_string = "delete from inv_sysgrp     where grp_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($users > 0) {
        print "System Users ";
        $q_string = "delete from inv_syspwd where pwd_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($tags > 0) {
        print "Tags ";
        $q_string = "delete from inv_tags       where tag_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      print "Completed.\n";

    }
  } else {
    print "Inventory: Unable to locate " . $server . $macaddr . $ipaddr . $serialnumber . $assettag . "\n";
    exit(1);
  }
  exit(0);

  mysqli_close($db);

?>
