<?php
# Script: upload.it.sysadmins.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if (isset($argv[1])) {
    $debug = 'yes';
  } else {
    $debug = 'no';
  }

  $file = 'it.sysadmins.current.csv';

# legend
  print "Usage:\n";
  print "php upload.it.sysadmins.php\n";
  print "f - Found the hardware\n";
  print ". - Found the Serial Number but did not find the hardware.\n";

# 00-Date
# 01-Computer_Name - inv_name and int_servername
# 02-Domain_Name - inv_domain
# 03-IP_Address - int_addr
# 04-Hardware_Make - mod_vendor
# 05-Hardware_Model - mod_name
# 06-Memory__MB_ - mode name
# 07-Serial_Number - hw_serial
# 08-BIOS_Release_Date - sw_software
# 09-OS_Name - sw_software
# 10-Version - + sw_software
# 11-Service_Pack - + sw_software
# 12-MAC_Address - int_eth

# Import the above data into the inventory.
# Exceptions:
#   if not Marcus' ($GRP_Windows), ignore
#   if more than one with the same name, ignore

$updated = '';
$newsvr = '';
$notown = '';
$dupsvr = '';
$dupip = '';
$modellost = "";
$modelfound = "";
$modeldup = "";

if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

# check to see if the system already exists. If it exists, check and update only if Windows; report system and owners

    $q_string  = "select inv_id,inv_name,inv_manager,grp_name ";
    $q_string .= "from inventory ";
    $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_name = \"" . $data[1] . "\" and inv_status = 0 ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {
      if (mysqli_num_rows($q_inventory) == 0) {
# no servers found
        $newsvr .= "Server not found. Server: " . $data[1] . "\n";
      }
      if (mysqli_num_rows($q_inventory) == 1) {

# update the main inventory record
        if ($a_inventory['inv_manager'] == $GRP_Windows) {
          $q_string  = "update ";
          $q_string .= "inventory ";
          $q_string .= "set ";
          $q_string .= "inv_name = \"" . $data[1] . "\",";
          $q_string .= "inv_domain = \"" . $data[2] . "\" ";
          $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $updated .= "Entry Updated. Server: " . $data[1] . "\n";

# look for the listed IP address and update it if found.
          $q_string  = "select int_id,int_addr ";
          $q_string .= "from interface ";
          $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_addr = \"" . $data[3] . "\" ";
          $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_interface) == 0) {
# no interface found
            $ipv6 = '0';
            if (strpos($data[12], ":") !== false) {
              $ipv6 = '1';
            }

            $q_string  = "insert into interface set ";
            $q_string .= "int_id        =   " . "null"                 . ",";
            $q_string .= "int_companyid =   " . $a_inventory['inv_id'] . ",";
            $q_string .= "int_server    = \"" . $data[1]               . "\",";
            $q_string .= "int_addr      = \"" . $data[3]               . "\",";
            $q_string .= "int_eth       = \"" . $data[12]              . "\",";
            $q_string .= "int_ip6       =   " . $ipv6;

            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $updated .= "- Unable to locate IP. Added: " . $data[3] . "\n";
          }
          if (mysqli_num_rows($q_interface) == 1) {
            $a_interface = mysqli_fetch_array($q_interface);

            $ipv6 = '0';
            if (strpos($data[12], ":") !== false) {
              $ipv6 = '1';
            }

            $q_string  = "update ";
            $q_string .= "interface ";
            $q_string .= "set ";
            $q_string .= "int_eth    = \"" . $data[12] . "\",";
            $q_string .= "int_server = \"" . $data[1]  . "\",";
            $q_string .= "int_ip6    =   " . $ipv6     . " ";
            $q_string .= "where int_id = " . $a_interface['int_id'] . " ";

            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $updated .= "- IP Address Updated. IP: " . $data[3] . "\n";
          }
          if (mysqli_num_rows($q_interface) > 1) {
            $dupip .= "- Found more than one interface with the same IP. Server: " . $data[1] . " IP Address: " . $data[3] . ", Copies: " . mysqli_num_rows($q_interface) . "\n";
          }

# update the main hardware record
          $q_string  = "select mod_id ";
          $q_string .= "from models ";
          $q_string .= "where mod_name = \"" . $data[5] . "\" ";
          $q_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_models) == 0) {
            if ($data[5] == 'VMware Virtual Platform') {
              $modelfound .= "Located: " . $data[5] . "\n";
            } else {
              $modellost .= "Unable to locate: " . $data[5] . "\n";
            }
          }
          if (mysqli_num_rows($q_models) == 1) {
            $modelfound .= "Located: " . $data[5] . "\n";
          }
          if (mysqli_num_rows($q_models) > 1) {
            $modeldup .= "More than one? " . $data[5] . "\n";
          }


          $q_string  = "select hw_id ";
          $q_string .= "from hardware ";
          $q_string .= "where ";











# update the main software record
          $q_string  = "select sw_id ";
          $q_string .= "from software ";
          $q_string .= "where ";









        } else {
# display the found server and who owns it if not windows
          $notown .= "Not system owner. Server: " . $data[1] . ", Owner: " . $a_inventory['grp_name'] . "\n";
        }
      }
# if more than 1 regardless of owner
      if (mysqli_num_rows($q_inventory) > 1) {
        $dupsvr .= "Found more than one server with the same name. Server: " . $data[1] . ", Copies: " . mysqli_num_rows($q_inventory) . ", Owner: " . $a_inventory['grp_name'] . "\n";
      }
    }
  }
  fclose($handle);
}

#print $updated . "\n\n";

#print $newsvr . "\n\n";

#print $notown . "\n\n";

#print $dupsvr . "\n\n";

#print $dupip . "\n\n";

print $modellost . "\n\n";

#print $modelfound . "\n\n";

#print $modeldup . "\n\n";

?>
