<?php
# Script: upload.uuid_audit.php
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

# 0 - servername
# 1 - state
# 2 - serial number
# 3 - esxhost
# 4 - esxcluster

  $file = 'UUID_AUDIT.current.csv';

  if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

      $hostname = explode(".", $data[3]);

      $q_string  = "select inv_id ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_name = '" . $hostname[0] . "' ";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_inventory) > 0) {
        $a_inventory = mysql_fetch_array($q_inventory);

        $hostid = $a_inventory['inv_id'];
      } else {
        print "ESX Host not found: " . $data[3] . "\n";
        $hostid = 0;
      }

# I have the uuid in the file and the uuid captured from most of the servers.
# can we find the uuid first? If it's not there, then check the name against the inventory.
# report it if it can't find it after that.

      $q_string  = "select inv_id ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_uuid = '" . $data[2] . "' ";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_inventory) > 0) {
        $a_inventory = mysql_fetch_array($q_inventory);

# update the inventory with the companyid and uuid of the cluster.
        $q_string = "update inventory set inv_companyid = " . $hostid . ",inv_uuid='" . $data[2] . "' where inv_id = " . $a_inventory['inv_id'];
          
        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        }

      } else {

        $q_string  = "select inv_id ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_name = '" . $data[0] . "' ";
        $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        if (mysql_num_rows($q_inventory) > 0) {
          $a_inventory = mysql_fetch_array($q_inventory);

# update the inventory with the companyid and uuid of the cluster.
          $q_string = "update inventory set inv_companyid = " . $hostid . ",inv_uuid='" . $data[2] . "' where inv_id = " . $a_inventory['inv_id'];
          
          if ($debug == 'yes') {
            print $q_string . "\n";
          } else {
            $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          }

        } else {

          $q_string  = "select int_id,int_companyid ";
          $q_string .= "from interface ";
          $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
          $q_string .= "where int_server = '" . $data[0] . "' ";
          $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          if (mysql_num_rows($q_interface) > 0) {
            $a_interface = mysql_fetch_array($q_interface);

            $q_string = "update inventory set inv_companyid = " . $hostid . ",inv_uuid='" . $data[2] . "' where inv_id = " . $a_inventory['inv_id'];
          
            if ($debug == 'yes') {
              print $q_string . "\n";
            } else {
              $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            }
          } else {
            print "Missing: " . $data[0] . "\n";
          }
        }
      }
    }

    fclose($handle);
  }

?>
