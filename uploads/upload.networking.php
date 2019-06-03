<?php
# Script: upload.west.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'yes';
  $debug = 'no';

  $file = 'networking.current.csv';

# run through the existing network devices. If the hardware retirement date is not set, set it and the status.
# then when the server list is run through, update the hardware retirement date to be '0000-00-00' to show it's not retired.

print "Setting all network hardware as retired but only if not already retired.\n";
print "Legend:\n";
print "i - Update inventory record\n";
print "f - Found hardware record\n";
print "h - Update hardware record\n";

  $q_string  = "select inv_id ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_manager = " . $GRP_Networking . " and inv_status = 0 and inv_id != 10805 ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string  = "update inventory ";
    $q_string .= "set ";
    $q_string .= "inv_status = 1 ";
    $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " ";
    $results = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    print "i";

    $q_string  = "select hw_id ";
    $q_string .= "from hardware ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1 and hw_retired = '0000-00-00' ";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_hardware) > 0) {
      $a_hardware = mysql_fetch_array($q_hardware);
      print "f";

      $q_string  = "update hardware ";
      $q_string .= "set ";
      $q_string .= "hw_retired = '" . date('Y-m-d') . "' ";
      $q_string .= "where hw_id = " . $a_hardware['hw_id'] . " ";
      $results = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      print "h";

    }
  }

print "\n";

print "Legend:\n";
print "M - Adding model info\n";
print "u - Updating inventory info\n";
print "h - Updating hardware info\n";
print "i - Updating interface info\n";
print "I - Adding interface info\n";
print "N - Adding inventory info\n";
print "H - Adding hardware info\n";

print "Searching for devices: ";

# 0                             1                 2        3         4            5               6                                   7
#Device Name,                 Access Address,Chassis Name,Model,    Serial Number,Vendor,       Last Successful CLI Collection Time,Last Successful SNMP Collection Time
#ACCYTXSO1RTA.inf.a911net.net,10.213.253.19, chassis,     CISCO1841,FTX1337Y1C7,  Cisco Systems,3/20/2017 19:21,                    3/20/2017 20:13

if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

# need to run through the Vendor and Model information in the Models table and add any missing devices

    $data[5] = clean($data[5], 24);
    if ($data[5] == '') {
      $data[5] = 'Unknown';
    }
    $data[3] = clean($data[3], 100);
    if ($data[3] == '') {
      $data[3] = 'Unknown';
    }

    $q_string  = "select mod_id ";
    $q_string .= "from models ";
    $q_string .= "where mod_vendor = '" . $data[5] . "' and mod_name = '" . $data[3] . "' ";
    $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_models) == 0) {

      $q_string  = "insert ";
      $q_string .= "into models ";
      $q_string .= "set ";
      $q_string .= "mod_id = null,";
      $q_string .= "mod_vendor = '" . $data[5] . "',";
      $q_string .= "mod_name = '" . $data[3] . "',";
      $q_string .= "mod_type = 38,";
      $q_string .= "mod_primary = 1";

      if ($debug == 'yes') {
        print "Missing, adding: Vendor: " . $data[5] . " Model: " . $data[3] . "\n";
        print $q_string . "\n";
      } else {
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        print "M";
      }
    }

# now break the fqdn into hostname and domain

    $hostname = explode(".", clean(strtolower($data[0]), 255));
    $domain = '';
    $dot = '';
    for ($i = 1; $i < count($hostname); $i++) {
      $domain .= $dot . $hostname[$i];
      $dot = '.';
    }

# now see if the record exists
    $q_string  = "select inv_id ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $hostname[0] . "' ";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_inventory) > 0) {
      $a_inventory = mysql_fetch_array($q_inventory);

# found it. now update the inventory record for name, domain, owner and app owner (same group)
      $q_string  = "update inventory ";
      $q_string .= "set ";
      $q_string .= "inv_name = '" . $hostname[0] . "',";
      $q_string .= "inv_fqdn = '" . $domain . "',";
      $q_string .= "inv_function = 'Network Device',";
      $q_string .= "inv_manager = " . $GRP_Networking . ",";
      $q_string .= "inv_appadmin = " . $GRP_Networking . ",";
      $q_string .= "inv_status = 0 ";
      $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " ";

      if ($debug == 'yes') {
        print "Found and updating hostname: " . $hostname[0] . " Domain: " . $domain . "\n";
        print $q_string . "\n";
      } else {
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        print "u";
      }


# next search for device information. update it with correct info and add the serial number. Add it if it doesn't exist (unlikely).
      $q_string  = "select mod_id ";
      $q_string .= "from models ";
      $q_string .= "where mod_vendor = '" . $data[5] . "' and mod_name = '" . $data[3] . "' ";
      $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_models = mysql_fetch_array($q_models);

# got the model info. Now get the hardware info and set it.
      $q_string  = "select hw_id,hw_built,hw_active ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1 and hw_deleted = 0 ";
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_hardware) > 0) {
        $a_hardware = mysql_fetch_array($q_hardware);

# now update the hardware information
        $q_string  = "update hardware ";
        $q_string .= "set ";
        $q_string .= "hw_retired = '0000-00-00',";
        if ($a_hardware['hw_active'] == '0000-00-00') {
          $q_string .= "hw_active = '" . date('Y-m-d') . "',";
        }
        if ($a_hardware['hw_built'] == '0000-00-00') {
          $q_string .= "hw_built = '" . date('Y-m-d') . "',";
        }
        $q_string .= "hw_vendorid = " . $a_models['mod_id'] . ",";
        $q_string .= "hw_group = " . $GRP_Networking . ",";
        $q_string .= "hw_primary = 1,";
        $q_string .= "hw_verified = 1,";
        $q_string .= "hw_serial = '" . $data[4] . "',";
        $q_string .= "hw_update = '" . date('Y-m-d') . "' ";
        $q_string .= "where hw_id = " . $a_hardware['hw_id'] . " ";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          print "h";
        }
      }


# now pull in the interface information for the IP address. If it exists, update it otherwise add a new one.
      $q_string  = "select int_id ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_addr = '" . $data[1] . "' ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_interface) > 0) {
        $a_interface = mysql_fetch_array($q_interface);

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_server = '" . $hostname[0] . "',";
        $q_string .= "int_vaddr = 1,";
        $q_string .= "int_type = 1,";
        $q_string .= "int_update = '" . date('Y-m-d') . "' ";
        $q_string .= "where int_id = " . $a_interface['int_id'] . " ";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          print "i";
        }
      } else {

        $q_string  = "insert ";
        $q_string .= "into interface ";
        $q_string .= "set ";
        $q_string .= "int_id = null,";
        $q_string .= "int_server = '" . $hostname[0] . "',";
        $q_string .= "int_addr = '" . $data[1] . "',";
        $q_string .= "int_type = 1,";
        $q_string .= "int_vaddr = 1,";
        $q_string .= "int_update = '" . date('Y-m-d') . "' ";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          print "I";
        }
      }

    } else {
      $server = 0;

      $q_string  = "insert ";
      $q_string .= "into inventory ";
      $q_string .= "set ";
      $q_string .= "inv_id = null,";
      $q_string .= "inv_name = '" . $hostname[0] . "',";
      $q_string .= "inv_fqdn = '" . $domain . "',";
      $q_string .= "inv_function = 'Network Device',";
      $q_string .= "inv_manager = " . $GRP_Networking . ",";
      $q_string .= "inv_appadmin = " . $GRP_Networking . ",";
      $q_string .= "inv_status = 0 ";

      if ($debug == 'yes') {
        print "Missing: " . $hostname[0] . " Domain: " . $domain . "\n";
        print $q_string . "\n";
      } else {
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $server = last_insert_id();
        print "N";
      }

      if ($server > 0) {
# add the hardware and IP address
# next search for device information. update it with correct info and add the serial number. Add it if it doesn't exist (unlikely).
        $q_string  = "select mod_id ";
        $q_string .= "from models ";
        $q_string .= "where mod_vendor = '" . $data[5] . "' and mod_name = '" . $data[3] . "' ";
        $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_models = mysql_fetch_array($q_models);

# now insert the hardware information
        $q_string  = "insert ";
        $q_string .= "into hardware ";
        $q_string .= "set ";
        $q_string .= "hw_id = null,";
        $q_string .= "hw_companyid = " . $server . ",";
        $q_string .= "hw_retired = '0000-00-00',";
        $q_string .= "hw_active = '" . date('Y-m-d') . "',";
        $q_string .= "hw_built = '" . date('Y-m-d') . "',";
        $q_string .= "hw_vendorid = " . $a_models['mod_id'] . ",";
        $q_string .= "hw_group = " . $GRP_Networking . ",";
        $q_string .= "hw_primary = 1,";
        $q_string .= "hw_verified = 1,";
        $q_string .= "hw_serial = '" . $data[4] . "',";
        $q_string .= "hw_update = '" . date('Y-m-d') . "' ";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          print "H";
        }

# and now insert the interface information
        $q_string  = "insert ";
        $q_string .= "into interface ";
        $q_string .= "set ";
        $q_string .= "int_id = null,";
        $q_string .= "int_companyid = " . $server . ",";
        $q_string .= "int_server = '" . $hostname[0] . "',";
        $q_string .= "int_addr = '" . $data[1] . "',";
        $q_string .= "int_type = 1,";
        $q_string .= "int_vaddr = 1,";
        $q_string .= "int_update = '" . date('Y-m-d') . "' ";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          print "I";
        }
      }
    }
  }

  fclose($handle);
}

?>
