#!/usr/local/bin/php
<?php
# Script: tcooper.spreadsheet.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Export of the database for Trudy Cooper in Corporate for the HPAM software
# 

# root.cron: # Trudy Cooper requesting spreadsheet output
# root.cron: 30 6 * * * /usr/local/bin/php /usr/local/httpd/bin/tcooper.spreadsheet.php > /usr/local/httpd/htsecure/reports/tcooper.spreadsheet.csv 2>/dev/null

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# resource class is a mixture of device type and what's running on it:

#Blade Chassis
#Controller
#Disk Array
#Firewall
#Multilayer Switch
#Router
#Tape Library

#Appliance
#Appliance_VM
#ESXi
#HP UX
#Linux
#Linux_VM
#Other Unix/Linux
#Solaris_VM
#Sun/Solaris
#Windows
#Windows_VM

#"Blade Chassis"
#"Cluster"
#"Router"
#?"Server"
#"Storage Array"
#"Switch"

#"ESXi"
#"FreeBSD"
#"HP-UX"
#"HP-UX_VM"
#"Linux"
#"Linux_VM"
#"SunOS"
#"SunOS_VM"
#?"Virtual Machine"
#?"Virtual Machine_VM"
#"Windows"
#"Windows_VM"


# essentially if it's not running an operating system, use the physical device type.
# if function returns 'VMWare', replace with 'ESXi'. If Cisco, go with physical.

# Notes:
#   Resource Class = Virtual: Windows_VM, Linux_VM, Firewall, Physical: Windows, Linux, HP-UX, ESXi, Disk Array, Router, Blade Chassis, Multilayer Switch, Sun/Solaris, Other Unix/Linux, Appliance, Solaris_VM

#  inv_name, figure out, Active, TRDO, 
  print "\"Asset Name\",\"Resource Class\",\"Status\",\"Cost Center\",\"Brand\",\"Model\",\"Serial #\",\"Location\",\"IP Address\",\"Primary Application\",\"Owning Group\",\"Support Group\",\"Environment\",\"Security Risk\",\"Oper. System\",\"Service Pack\"\n";

  $q_string  = "select inv_id,inv_name,inv_uuid,inv_function,hw_serial,inv_virtual,mod_vendor,mod_name,loc_west,part_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models    on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join parts     on parts.part_id         = models.mod_type ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "where (inv_manager = " . $GRP_Unix . " or inv_manager = " . $GRP_Windows . " or inv_manager = " . $GRP_Virtualization . " or inv_manager = " . $GRP_ICLAdmins . " or inv_manager = " . $GRP_Networking . ") and inv_status = 0 and hw_primary = 1 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $resource_class = return_System($a_inventory['inv_id']);
    if ($resource_class == 'VMWare') {
      $resource_class = 'ESXi';
    }
    if ($resource_class != 'Appliance' && $resource_class != 'Linux' && $resource_class != 'SunOS' && $resource_class != 'HP-UX' && $resource_class != 'OSF1' && $resource_class != 'FreeBSD' && $resource_class != 'Windows' && $resource_class != 'ESXi') {
      $resource_class = $a_inventory['part_name'];
    }

    $serial = $a_inventory['hw_serial'];
    if ($a_inventory['inv_virtual']) {
      $resource_class .= "_VM";
      if (strlen($a_inventory['inv_uuid']) > 0) {
        $serial = "VMware-" . $a_inventory['inv_uuid'];
      }
    }

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_type = 'OS' and sw_companyid = " . $a_inventory['inv_id'] . " ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $interface = '';
    $comma = '';
    $q_string  = "select int_addr,itp_acronym ";
    $q_string .= "from interface ";
    $q_string .= "left join inttype on inttype.itp_id = interface.int_type ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and (int_type = 1 or int_type = 2 or int_type = 6) and int_ip6 = 0 ";
    $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_interface = mysql_fetch_array($q_interface)) {
      $interface .= $comma . $a_interface['itp_acronym'] . ":" . $a_interface['int_addr'];
      $comma = ',';
    }

    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"" . $resource_class . "\",";
    print "\"" . "Active" . "\",";
    print "\"" . "TRDO" . "\",";
    print "\"" . $a_inventory['mod_vendor'] . "\",";
    print "\"" . $a_inventory['mod_name'] . "\",";
    print "\"" . $serial . "\",";
    print "\"" . $a_inventory['loc_west'] . "\",";
    print "\"" . $interface . "\",";
    print "\"" . $a_inventory['inv_function'] . "\",";
    print "\"" . "" . "\",";
    print "\"" . "" . "\",";
    print "\"" . "" . "\",";
    print "\"" . "" . "\",";
    print "\"" . $a_software['sw_software'] . "\",";
    print "\"" . "\"\n";

  }

?>
