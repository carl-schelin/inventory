#!/usr/local/bin/php
<?php
# Script: lcampbell.spreadsheet.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# 

# root.cron: # Lennie Campbell requesting spreadsheet output
# root.cron: 30 8 * * * /usr/local/bin/php /usr/local/httpd/bin/lcampbell.spreadsheet.php > /usr/local/httpd/htsecure/reports/lcampbell.spreadsheet.csv 2>/dev/null

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# domain
# subsequent IPs, mac, and interface to be on a separate sheet

  print "\"Server Name\",\"Operating System\",\"Location Name\",\"Address\",\"Address\",\"Suite\",\"City\",\"State\",\"Zipcode\",\",\"Make\",\"Model\",\"Function\",\"Serial/VM UUID\",\"CPUs\",\"Architecture\",\"Memory\",\"Disk Storage\",\"IP Address\",\"MAC Address\",\"Interface Name\"\n";

  $q_string  = "select inv_id,inv_name,inv_function,sw_software,mod_vendor,mod_name,loc_name,loc_addr1,loc_addr2,loc_suite,loc_city,ct_city,loc_state,st_state,loc_zipcode,inv_uuid ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join cities on locations.loc_city = cities.ct_id ";
  $q_string .= "left join states on cities.ct_state = states.st_id ";
  $q_string .= "where inv_status = 0 and sw_type = 'OS' and inv_manager = 1 and hw_primary = 1 and inv_project = 480 and loc_environment = 1 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

# get number of total cores
    $cpus = 0;
    $q_string  = "select mod_size ";
    $q_string .= "from models ";
    $q_string .= "left join hardware on hardware.hw_vendorid = models.mod_id ";
    $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
    $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " and mod_type = 8 ";
    $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_models = mysql_fetch_array($q_models)) {
      $value = explode(" ", trim($a_models['mod_size']));
      $cpus += $value[0];
    }

# get amount of ram
    $memory = 0;
    $q_string  = "select hw_size ";
    $q_string .= "from hardware ";
    $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
    $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " and hw_type = 4 ";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_hardware = mysql_fetch_array($q_hardware)) {
      $value = explode(" ", trim($a_hardware['hw_size']));
      if ($value[1] == 'kB') {
        $memory += ceil($value[0] / 1048576);
      }
      if ($value[1] == 'MB') {
        $memory += ceil($value[0] / 1024);
      }
      if ($value[1] == 'GB') {
        $memory += $value[0];
      }
    }

# get amount of disk
    $disk = 0;
    $q_string  = "select hw_size ";
    $q_string .= "from hardware ";
    $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
    $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " and hw_type = 2 ";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_hardware = mysql_fetch_array($q_hardware)) {
      $value = explode(" ", trim($a_hardware['hw_size']));
      $disk += $value[0];
    }

# get IP info
    $q_string  = "select int_addr,int_eth,int_face ";
    $q_string .= "from interface ";
    $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_type = 16 ";
    $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_interface) > 0) {
      $a_interface = mysql_fetch_array($q_interface);

      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . "\",";
      print "\"" . $a_interface['int_addr'] . "\",";
      print "\"" . $a_interface['int_eth'] . "\",";
      print "\"" . $a_interface['int_face'] . "\"";
      print "\n";

    }
  }

?>
