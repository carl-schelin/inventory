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
  $q_string .= "from inventory ";
  $q_string .= "where inv_id = " . $remove . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {
    $a_inventory = mysqli_fetch_array($q_inventory);
    $server = $a_inventory['inv_name'];
  }

  $q_string  = "select inv_id,inv_name,inv_companyid,inv_function,prod_name,grp_name,inv_appadmin,mod_vendor,mod_name,hw_serial,";
  $q_string .= "hw_asset,hw_service,loc_name,loc_addr1,ct_city,st_state,loc_zipcode,inv_status,inv_rack,inv_row,inv_unit ";
  $q_string .= "from inventory ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = locations.loc_state ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
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
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {
    $a_inventory = mysqli_fetch_array($q_inventory);

    if ($a_inventory['inv_status'] == 1 ) {
      $retired = " == RETIRED ==";
    }

    $q_string  = "select grp_name ";
    $q_string .= "from a_groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);

    print "--------------------\n";
    print "Inventory Management\n";
    print "--------------------\n";
    if ($csv == 'yes') {
      print "\"ID\",\"Server\",\"Function\",\"Product\",\"Platform Managed By\",\"Applications Managed By\",\"Vendor\",\"Model\",\"Serial Number\",\"Asset Tag\",\"Dell Service Tag\",\"Data Center\",\"Address\",";
      if ($a_inventory['inv_companyid']) {
        print "\"Chassis\",\"Chassis Rack/Unit\",\"Blade Number\"\n";
      } else {
        print "\"Rack/Unit\"\n";
      }
      print "\"" . $a_inventory['inv_id']       . "\",";
      print "\"" . $a_inventory['inv_name'] . $retired . "\",";
      print "\"" . $a_inventory['inv_function'] . "\",";
      print "\"" . $a_inventory['prod_name']    . "\",";
      print "\"" . $a_inventory['grp_name']     . "\",";
      print "\"" . $a_groups['grp_name']        . "\",";
      print "\"" . $a_inventory['mod_vendor']   . "\",";
      print "\"" . $a_inventory['mod_name']     . "\",";
      print "\"" . $a_inventory['hw_serial']    . "\",";
      print "\"" . $a_inventory['hw_asset']     . "\",";
      print "\"" . $a_inventory['hw_service']   . "\",";
      print "\"" . $a_inventory['loc_name']     . "\",";
      print "\"" . $a_inventory['loc_addr1'] . " " . $a_inventory['ct_city'] . " " . $a_inventory['st_state'] . " " . $a_inventory['loc_zipcode'] . "\",";
      if ($a_inventory['inv_companyid']) {
        $q_string  = "select inv_name,inv_rack,inv_row,inv_unit ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $a_inventory['inv_companyid'] . " ";
        $q_chassis = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_chassis = mysqli_fetch_array($q_chassis);

        print "\"" . $a_chassis['inv_name'] . "\",";
        print "\"" . $a_chassis['inv_rack'] . "-" . $a_chassis['inv_row'] . "/U" . $a_chassis['inv_unit'] . "\",";
        print "\"" . $a_inventory['inv_unit'] . "\"\n";
      } else {
        print "\"" . $a_inventory['inv_rack'] . "-" . $a_inventory['inv_row'] . "/U" . $a_inventory['inv_unit'] . "\"\n";
      }
    } else {
      print "Server ID: " . $a_inventory['inv_id'] . "\n";
      print "Server: " . $a_inventory['inv_name'] . $retired . "\n";
      print "Function: " . $a_inventory['inv_function'] . "\n";
      print "Product: " . $a_inventory['prod_name'] . "\n";
      print "Platform Managed By: " . $a_inventory['grp_name'] . "\n";
      print "Applications Managed By: " . $a_groups['grp_name'] . "\n";
      print "----------------------------\n";
      print "Primary Hardware Information\n";
      print "----------------------------\n";
      print "Vendor: " . $a_inventory['mod_vendor'] . "\n";
      print "Model: " . $a_inventory['mod_name'] . "\n";
      print "Serial Number: " . $a_inventory['hw_serial'] . "\n";
      print "Asset Tag: " . $a_inventory['hw_asset'] . "\n";
      print "Dell Service Tag: " . $a_inventory['hw_service'] . "\n";
      print "--------------------\n";
      print "Location Information\n";
      print "--------------------\n";
      print "Data Center: " . $a_inventory['loc_name'] . "\n";
      print "Address: " . $a_inventory['loc_addr1'] . " " . $a_inventory['ct_city'] . " " . $a_inventory['st_state'] . " " . $a_inventory['loc_zipcode'] . "\n";
      if ($a_inventory['inv_companyid']) {
        $q_string  = "select inv_name,inv_rack,inv_row,inv_unit ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $a_inventory['inv_companyid'] . " ";
        $q_chassis = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_chassis = mysqli_fetch_array($q_chassis);

        print "Chassis: " . $a_chassis['inv_name'] . "\n";
        print "Chassis Rack/Unit: " . $a_chassis['inv_rack'] . "-" . $a_chassis['inv_row'] . "/U" . $a_chassis['inv_unit'] . "\n";
        print "Blade Number: " . $a_inventory['inv_unit'] . "\n";
      } else {
        print "Rack/Unit: " . $a_inventory['inv_rack'] . "-" . $a_inventory['inv_row'] . "/U" . $a_inventory['inv_unit'] . "\n";
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

      $q_string  = "select hw_id,hw_serial,hw_asset,hw_service,hw_vendorid,hw_size,hw_speed,part_name,hw_verified,hw_update ";
      $q_string .= "from hardware ";
      $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
      $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = 0 and hw_hd_id = 0 ";
      $q_string .= "order by part_name";
      $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_hardware = mysqli_fetch_array($q_hardware)) {
        $q_string  = "select mod_vendor,mod_name,mod_size,mod_speed from models where mod_id = " . $a_hardware['hw_vendorid'];
        $q_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_models = mysqli_fetch_array($q_models);

        if ($csv == 'yes') {
          print "\"" . $a_hardware['hw_serial']  . "\",";
          print "\"" . $a_hardware['hw_asset']   . "\",";
          print "\"" . $a_hardware['hw_service'] . "\",";
          print "\"" . $a_models['mod_vendor']   . "\",";
          print "\"" . $a_models['mod_name']     . "\",";
          print "\"" . $a_hardware['hw_size']    . "\",";
          print "\"" . $a_hardware['hw_speed']   . "\",";
          print "\"" . $a_hardware['part_name']  . "\"\n";
        } else {
          printf("%20s %10s %8s %20s %30s %20s %15s %20s\n", $a_hardware['hw_serial'], $a_hardware['hw_asset'], $a_hardware['hw_service'], $a_models['mod_vendor'], $a_models['mod_name'], $a_hardware['hw_size'], $a_hardware['hw_speed'], $a_hardware['part_name']);
        }

        $q_string  = "select hw_id,hw_serial,hw_asset,hw_service,hw_vendorid,hw_size,hw_speed,part_name,hw_verified,hw_update ";
        $q_string .= "from hardware ";
        $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
        $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " and hw_hd_id = 0 ";
        $q_string .= "order by part_name";
        $q_hwselect = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        while ($a_hwselect = mysqli_fetch_array($q_hwselect)) {
          $q_string  = "select mod_vendor,mod_name,mod_size,mod_speed from models where mod_id = " . $a_hwselect['hw_vendorid'];
          $q_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
          $a_models = mysqli_fetch_array($q_models);

          if ($csv == 'yes') {
            print "\"" . $a_hwselect['hw_serial']  . "\",";
            print "\"" . $a_hwselect['hw_asset']   . "\",";
            print "\"" . $a_hwselect['hw_service'] . "\",";
            print "\"" . $a_models['mod_vendor']   . "\",";
            print "\"" . $a_models['mod_name']     . "\",";
            print "\"" . $a_hwselect['hw_size']    . "\",";
            print "\"" . $a_hwselect['hw_speed']   . "\",";
            print "\"" . $a_hwselect['part_name']  . "\"\n";
          } else {
            printf("%20s %10s %8s %20s >%29s %20s %15s %20s\n", $a_hwselect['hw_serial'], $a_hwselect['hw_asset'], $a_hwselect['hw_service'], $a_models['mod_vendor'], $a_models['mod_name'], $a_hwselect['hw_size'], $a_hwselect['hw_speed'], $a_hwselect['part_name']);
          }

          $q_string  = "select hw_id,hw_serial,hw_asset,hw_service,hw_vendorid,hw_size,hw_speed,part_name,hw_verified,hw_update ";
          $q_string .= "from hardware ";
          $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
          $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " and hw_hd_id = " . $a_hwselect['hw_id'] . " ";
          $q_string .= "order by part_name";
          $q_hwdisk = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
          while ($a_hwdisk = mysqli_fetch_array($q_hwdisk)) {
            $q_string  = "select mod_vendor,mod_name,mod_size,mod_speed from models where mod_id = " . $a_hwdisk['hw_vendorid'];
            $q_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
            $a_models = mysqli_fetch_array($q_models);

            if ($csv == 'yes') {
              print "\"" . $a_hwdisk['hw_serial']  . "\",";
              print "\"" . $a_hwdisk['hw_asset']   . "\",";
              print "\"" . $a_hwdisk['hw_service'] . "\",";
              print "\"" . $a_models['mod_vendor']   . "\",";
              print "\"" . $a_models['mod_name']     . "\",";
              print "\"" . $a_hwdisk['hw_size']    . "\",";
              print "\"" . $a_hwdisk['hw_speed']   . "\",";
              print "\"" . $a_hwdisk['part_name']  . "\"\n";
            } else {
              printf("%20s %10s %8s %20s >>%28s %20s %15s %20s\n", $a_hwdisk['hw_serial'], $a_hwdisk['hw_asset'], $a_hwdisk['hw_service'], $a_models['mod_vendor'], $a_models['mod_name'], $a_hwdisk['hw_size'], $a_hwdisk['hw_speed'], $a_hwdisk['part_name']);
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

      $q_string  = "select sw_product,sw_vendor,sw_software,sw_type,sw_group,sw_verified,sw_update ";
      $q_string .= "from software ";
      $q_string .= "where (sw_type != 'PKG' and sw_type != 'RPM') and sw_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by sw_software";
      $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_software = mysqli_fetch_array($q_software)) {
        $q_string = "select prod_name from products where prod_id = " . $a_software['sw_product'];
        $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_products = mysqli_fetch_array($q_products);

        $q_string  = "select grp_name ";
        $q_string .= "from a_groups ";
        $q_string .= "where grp_id = " . $a_software['sw_group'];
        $q_groups = mysqli_query($db, $q_string) or die($q_string . ":(5): " . mysqli_error($db) . "\n\n");
        $a_groups = mysqli_fetch_array($q_groups);

        if ($csv == 'yes') {
          print "\"" . $a_products['prod_name']   . "\",";
          print "\"" . $a_software['sw_vendor']   . "\",";
          print "\"" . $a_software['sw_software'] . "\",";
          print "\"" . $a_software['sw_type']     . "\",";
          print "\"" . $a_groups['grp_name']      . "\"\n";
        } else {
          printf("%20s %20s %100s %15s %30s\n", $a_products['prod_name'], $a_software['sw_vendor'], $a_software['sw_software'], $a_software['sw_type'], $a_groups['grp_name']);
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

      $q_string  = "select int_server,int_face,int_ip6,int_addr,int_eth,int_mask,int_gate,int_verified,int_type,int_update,zone_name ";
      $q_string .= "from interface ";
      $q_string .= "left join ip_zones on ip_zones.zone_id = interface.int_zone ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 ";
      $q_string .= "order by int_face";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_interface = mysqli_fetch_array($q_interface)) {
        $q_string  = "select itp_acronym ";
        $q_string .= "from inttype ";
        $q_string .= "where itp_id = " . $a_interface['int_type'];
        $q_inttype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_inttype = mysqli_fetch_array($q_inttype);

        if ($csv == 'yes') {
          print "\"" . $a_interface['int_server'] . "\",";
          print "\"" . $a_interface['int_face']   . "\",";
          print "\"" . $a_interface['int_addr']   . "/" . $a_interface['int_mask'] . "\",";
          print "\"" . $a_interface['int_eth']    . "\",";
          print "\"" . $a_interface['int_gate']   . "\",";
          print "\"" . $a_inttype['itp_acronym']  . "\"\n";
          print "\"" . $a_interface['zone_name']  . "\"\n";
        } else {
          printf("%30s %10s %20s %20s %20s %5s %10s\n", $a_interface['int_server'], $a_interface['int_face'], $a_interface['int_addr'] . "/" . $a_interface['int_mask'], $a_interface['int_eth'], $a_interface['int_gate'], $a_inttype['itp_acronym'], $a_interface['zone_name']);
        }
      }
    }

    if ($remove > 0) {

# Need to grep for 'companyid' and in a couple of places, 'inv_id'
# cd /var/tmp/mysql/carl/inventory/inventory
# egrep "(companyid|inv_id)" *sql
#backups.sql:       `bu_companyid`    int(10) NOT NULL default '0',
#cluster.sql:       `clu_companyid`   int(10) NOT NULL default '0',
#filesystem.sql:    `fs_companyid`    int(10) NOT NULL default '0',
#firewall.sql:      `fw_companyid`    int(8)  NOT NULL default '0',
#hardware.sql:      `hw_companyid`    int(8)  NOT NULL default '0',
#interface.sql:     `int_companyid`   int(10) NOT NULL default '0',
#inventory.sql:     `inv_companyid`   int(10) NOT NULL default '0',
#ip_addresses.sql:  `ip_companyid`    int(10) NOT NULL default '0',
#issue.sql:         `iss_companyid`   int(10) NOT NULL default '0',
#maint.sql:         `man_companyid`   int(10) NOT NULL default '0',
#outage.sql:        `out_companyid`   int(10) NOT NULL default '0',
#packages.sql:      `pkg_inv_id`      int(10) NOT NULL default '0',
#retire.sql:        `ret_companyid`   int(10) NOT NULL default '0',
#routing.sql:       `route_companyid` int(10) NOT NULL default '0',
#san.sql:           `san_companyid`   int(10) NOT NULL default '0',
#software.sql:      `sw_companyid`    int(10) NOT NULL default '0',
#sysgrp.sql:        `grp_companyid`   int(10) NOT NULL default '0',
#syspwd.sql:        `pwd_companyid`   int(10) NOT NULL default '0',
#tags.sql:          `tag_companyid`   int(10) NOT NULL default '0',

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
      $q_string .= "from backups ";
      $q_string .= "where bu_companyid = " . $remove . " ";
      $q_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_backups) > 0) {
        print "There is a backup record " . mysqli_num_rows($q_backups) . " for " . $a_inventory['inv_name'] . "\n";
        $backups = mysqli_num_rows($q_backups);
      }

      $cluster = 0;
      $q_string  = "select clu_companyid ";
      $q_string .= "from cluster ";
      $q_string .= "where clu_companyid = " . $remove . " ";
      $q_cluster = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_cluster) > 0) {
        print "There are " . mysqli_num_rows($q_cluster) . " cluster records for " . $a_inventory['inv_name'] . "\n";
        $cluster = mysqli_num_rows($q_cluster);
      }

      $filesystem = 0;
      $q_string  = "select fs_companyid ";
      $q_string .= "from filesystem ";
      $q_string .= "where fs_companyid = " . $remove . " ";
      $q_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_filesystem) > 0) {
        print "There are " . mysqli_num_rows($q_filesystem) . " filesystem records for " . $a_inventory['inv_name'] . "\n";
        $filesystem = mysqli_num_rows($q_filesystem);
      }

      $firewall = 0;
      $q_string  = "select fw_companyid ";
      $q_string .= "from firewall ";
      $q_string .= "where fw_companyid = " . $remove . " ";
      $q_firewall = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_firewall) > 0) {
        print "There are " . mysqli_num_rows($q_firewall) . " firewall records for " . $a_inventory['inv_name'] . "\n";
        $firewall = mysqli_num_rows($q_firewall);
      }

      $hardware = 0;
      $q_string  = "select hw_companyid ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_companyid = " . $remove . " ";
      $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_hardware) > 0) {
        print "There are " . mysqli_num_rows($q_hardware) . " hardware records for " . $a_inventory['inv_name'] . "\n";
        $hardware = mysqli_num_rows($q_hardware);
      }

      $interface = 0;
      $q_string  = "select int_id,int_companyid,int_addr ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $remove . " ";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_interface) > 0) {
        print "There are " . mysqli_num_rows($q_interface) . " interface records for " . $a_inventory['inv_name'] . "\n";
        $interface = mysqli_num_rows($q_interface);
        while ($a_interface = mysqli_fetch_array($q_interface)) {
          $q_string  = "select vuln_id ";
          $q_string .= "from vulnerabilities ";
          $q_string .= "where vuln_interface = " . $a_interface['int_id'];
          $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_vulnerabilities) > 0) {
            print " -- There are " . mysqli_num_rows($q_vulnerabilities) . " vulnerability records for " . $a_interface['int_addr'] . "\n";
          }
          $q_string  = "select vul_id ";
          $q_string .= "from vulnowner ";
          $q_string .= "where vul_interface = " . $a_interface['int_id'];
          $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_vulnowner) > 0) {
            print " -- There are " . mysqli_num_rows($q_vulnowner) . " vulnerability ticket records for " . $a_interface['int_addr'] . "\n";
          }
        }
      }

      $issues = 0;
      $q_string  = "select iss_id,iss_companyid ";
      $q_string .= "from issue ";
      $q_string .= "where iss_companyid = " . $remove . " ";
      $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_issue) > 0) {
        print "There are " . mysqli_num_rows($q_issue) . " issue tracker records for " . $a_inventory['inv_name'] . "\n";
        $issues = mysqli_num_rows($q_issue);
        while ($a_issue = mysqli_fetch_array($q_issue)) {
          $q_string  = "select det_issue ";
          $q_string .= "from issue_detail ";
          $q_string .= "where det_issue = " . $a_issue['iss_id'];
          $q_issue_detail = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_issue_detail) > 0) {
            print " -- There are " . mysqli_num_rows($q_issue_detail) . " detail records for issue " . $a_issue['iss_id'] . "\n";
          }
          $q_string  = "select morn_issue ";
          $q_string .= "from issue_morning ";
          $q_string .= "where morn_issue = " . $a_issue['iss_id'];
          $q_issue_morning = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_issue_morning) > 0) {
            print " -- There are " . mysqli_num_rows($q_issue_morning) . " morning report records for issue " . $a_issue['iss_id'] . "\n";
          }
          $q_string  = "select sup_issue ";
          $q_string .= "from issue_support ";
          $q_string .= "where sup_issue = " . $a_issue['iss_id'];
          $q_issue_support = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_issue_support) > 0) {
            print " -- There are " . mysqli_num_rows($q_issue_support) . " support records for issue " . $a_issue['iss_id'] . "\n";
          }
          $q_string  = "select rep_issue ";
          $q_string .= "from report ";
          $q_string .= "where rep_issue = " . $a_issue['iss_id'];
          $q_report = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_report) > 0) {
            print " -- There are " . mysqli_num_rows($q_report) . " morning report entries for issue " . $a_issue['iss_id'] . "\n";
          }
        }
      }

      $maintenance = 0;
      $q_string  = "select man_companyid ";
      $q_string .= "from maint ";
      $q_string .= "where man_companyid = " . $remove . " ";
      $q_maint = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_maint) > 0) {
        print "There are " . mysqli_num_rows($q_maint) . " maintenance outage records for " . $a_inventory['inv_name'] . "\n";
        $maintenance = mysqli_num_rows($q_maint);
      }

      $outages = 0;
      $q_string  = "select out_companyid ";
      $q_string .= "from outage ";
      $q_string .= "where out_companyid = " . $remove . " ";
      $q_outage = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_outage) > 0) {
        print "There are " . mysqli_num_rows($q_outage) . " outage records for " . $a_inventory['inv_name'] . "\n";
        $outages = mysqli_num_rows($q_outage);
      }

      $packages = 0;
      $q_string  = "select pkg_inv_id ";
      $q_string .= "from packages ";
      $q_string .= "where pkg_inv_id = " . $remove . " ";
      $q_packages = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_packages) > 0) {
        print "There are " . mysqli_num_rows($q_packages) . " package records for " . $a_inventory['inv_name'] . "\n";
        $packages = mysqli_num_rows($q_packages);
      }

      $psaps = 0;
      $q_string  = "select psap_companyid ";
      $q_string .= "from psaps ";
      $q_string .= "where psap_companyid = " . $remove . " ";
      $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_psaps) > 0) {
        print "There are " . mysqli_num_rows($q_psaps) . " psap records for " . $a_inventory['inv_name'] . "\n";
        $psaps = mysqli_num_rows($q_psaps);
      }

      $retirements = 0;
      $q_string  = "select ret_companyid ";
      $q_string .= "from retire ";
      $q_string .= "where ret_companyid = " . $remove . " ";
      $q_retire = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_retire) > 0) {
        print "There are " . mysqli_num_rows($q_retire) . " retirement records for " . $a_inventory['inv_name'] . "\n";
        $retirements = mysqli_num_rows($q_retire);
      }

      $routes = 0;
      $q_string  = "select route_companyid ";
      $q_string .= "from routing ";
      $q_string .= "where route_companyid = " . $remove . " ";
      $q_routing = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_routing) > 0) {
        print "There are " . mysqli_num_rows($q_routing) . " routing records for " . $a_inventory['inv_name'] . "\n";
        $routes = mysqli_num_rows($q_routing);
      }

      $san = 0;
      $q_string  = "select san_companyid ";
      $q_string .= "from san ";
      $q_string .= "where san_companyid = " . $remove . " ";
      $q_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_san) > 0) {
        print "There are " . mysqli_num_rows($q_san) . " san storage records for " . $a_inventory['inv_name'] . "\n";
        $san = mysqli_num_rows($q_san);
      }

      $software = 0;
      $q_string  = "select sw_companyid ";
      $q_string .= "from software ";
      $q_string .= "where sw_companyid = " . $remove . " ";
      $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_software) > 0) {
        print "There are " . mysqli_num_rows($q_software) . " software records for " . $a_inventory['inv_name'] . "\n";
        $software = mysqli_num_rows($q_software);
      }

      $groups = 0;
      $q_string  = "select grp_companyid ";
      $q_string .= "from sysgrp ";
      $q_string .= "where grp_companyid = " . $remove . " ";
      $q_sysgrp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_sysgrp) > 0) {
        print "There are " . mysqli_num_rows($q_sysgrp) . " system group records for " . $a_inventory['inv_name'] . "\n";
        $groups = mysqli_num_rows($q_sysgrp);
      }

      $users = 0;
      $q_string  = "select pwd_companyid ";
      $q_string .= "from syspwd ";
      $q_string .= "where pwd_companyid = " . $remove . " ";
      $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_syspwd) > 0) {
        print "There are " . mysqli_num_rows($q_syspwd) . " system user records for " . $a_inventory['inv_name'] . "\n";
        $users = mysqli_num_rows($q_syspwd);
      }

      $tags = 0;
      $q_string  = "select tag_companyid ";
      $q_string .= "from tags ";
      $q_string .= "where tag_companyid = " . $remove . " ";
      $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_tags) > 0) {
        print "There are " . mysqli_num_rows($q_tags) . " tags records for " . $a_inventory['inv_name'] . "\n";
        $tags = mysqli_num_rows($q_tags);
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
        $q_string = "delete from inventory     where inv_id = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($alarms > 0) {
        print "Alarms ";
        $q_string = "delete from alarms     where alarm_companyid = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($backups > 0) {
        print "Backups ";
        $q_string = "delete from backups    where bu_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($cluster > 0) {
        print "Cluster ";
        $q_string = "delete from cluster    where clu_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($filesystem > 0) {
        print "Filesystem ";
        $q_string = "delete from filesystem where fs_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($firewall > 0) {
        print "Firewall ";
        $q_string = "delete from firewall   where fw_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($hardware > 0) {
        print "Hardware ";
        $q_string = "delete from hardware   where hw_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($interface > 0) {
        print "Interfaces ";
        $q_string  = "select int_id,int_companyid,int_addr ";
        $q_string .= "from interface ";
        $q_string .= "where int_companyid = " . $remove . " ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_interface) > 0) {
          while ($a_interface = mysqli_fetch_array($q_interface)) {
            $q_string = "delete from vulnerabilities where vuln_interface = " . $a_interface['int_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $q_string = "delete from vulnowner where vul_interface = " . $a_interface['int_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          }
          $q_string = "delete from interface where int_companyid = " . $remove;
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }
      }

      if ($issues > 0) {
        print "Issues ";
# need to loop through the issues in order to clean out the sub-tables
        $q_string  = "select iss_id,iss_companyid ";
        $q_string .= "from issue ";
        $q_string .= "where iss_companyid = " . $remove . " ";
        $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_issue) > 0) {
          while ($a_issue = mysqli_fetch_array($q_issue)) {
            $q_string = "delete from issue_detail where det_issue = " . $a_issue['iss_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $q_string = "delete from issue_morning where morn_issue = " . $a_issue['iss_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $q_string = "delete from issue_support where sup_issue = " . $a_issue['iss_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $q_string = "delete from report where rep_issue = " . $a_issue['iss_id'];
            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          }
          $q_string = "delete from issue      where iss_companyid   = " . $remove;
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }
      }

      if ($maintenance > 0) {
        print "Maintenance ";
        $q_string = "delete from maint      where man_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($outages > 0) {
        print "Outages ";
        $q_string = "delete from outage     where out_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($packages > 0) {
        print "Packages ";
        $q_string = "delete from packages   where pkg_inv_id      = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($psaps > 0) {
        print "PSAPs ";
        $q_string = "delete from psaps      where psap_companyid  = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($retirements > 0) {
        print "Retirements ";
        $q_string = "delete from retire     where ret_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($routes > 0) {
        print "Route Tables ";
        $q_string = "delete from routing    where route_companyid = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($san > 0) {
        print "SAN ";
        $q_string = "delete from san        where san_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($software > 0) {
        print "Software ";
        $q_string = "delete from software   where sw_companyid    = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($groups > 0) {
        print "System Groups ";
        $q_string = "delete from sysgrp     where grp_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($users > 0) {
        print "System Users ";
        $q_string = "delete from syspwd     where pwd_companyid   = " . $remove;
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      if ($tags > 0) {
        print "Tags ";
        $q_string = "delete from tags       where tag_companyid    = " . $remove;
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
