#!/usr/local/bin/php
<?php
# Script: import.php
# By: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# This script reads in a comma delimited file created by the chksys script. The chksys script has various keywords 
# which are parsed by this script and then imported into the inventory database.

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  function mask2cidr($mask) {
    $long = ip2long($mask);
    $base = ip2long('255.255.255.255');
    return 32-log(($long ^ $base)+1,2);
  }

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the import file name.\n";
    exit(1);
  } else {
    $email = $argv[1];
  }

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# so first, get the server names from the inventory table to identify the server id

  $date = date('Y-m-d');

  $server = '';

  $file = fopen($argv[1], "r") or die;
  while(!feof($file)) {
# show item skip if something isn't imported
    $skip = 'yes';

    $process = trim(fgets($file));

    $value = split(",", $process);

    if ($value[0] != '') {
      $q_string  = "select inv_name,inv_id,inv_manager,inv_appadmin,inv_product ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_name = '" . $value[0] . "'";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_inventory = mysql_fetch_array($q_inventory);


# okay, can't find it in the main inventory; probably a different hostname vs the inventory name
      if ($a_inventory['inv_id'] == '') {
# check the interface table
        print "can't find it in the inventory, checking interface names... ";
        $q_string  = "select int_companyid ";
        $q_string .= "from interface ";
        $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
        $q_string .= "where inv_status = 0 and inv_ssh = 1 and int_server = '" . $value[0] . "' ";
        $q_string .= "group by int_companyid";
        $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_interface = mysql_fetch_array($q_interface);

        if ($a_interface['int_companyid'] != '') {
          $q_string  = "select inv_name,inv_id,inv_manager,inv_appadmin,inv_product ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_id = " . $a_interface['int_companyid'];
          $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_inventory = mysql_fetch_array($q_inventory);
        }
      }

      if ($a_inventory['inv_id'] != '') {
# okay, found an existing server.
# now delete any CPU from the system where the date is older than today.
# in general, memory is a single entry and drive sizes don't change.
# when a system is moved though, the cpu matches the ESX host cpu so needs to be refreshed.
# since there's no way to know if it goes from 1 2 core to 2 1 core cpus, the best bet is to remove the old ones.
        $q_string  = "delete ";
        $q_string .= "from hardware ";
        $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_type = 8 and hw_update < \"" . date('Y-m-d') . "\" ";
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

# Since we're not adding to routing tables, just documenting them, let's delete any routes that are in the table 
# that are older than today's date for this server and aren't static routes..
# this way we aren't clearing ancient stuff so I don't have to muck with the database
# and any manually managed routes (static) stick around, 
# plus Kubernetes adds a ton of routes for the pods on the servers and they come and go.
        $q_string  = "delete ";
        $q_string .= "from routing ";
        $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " and route_update < \"" . date('Y-m-d') . "\" and route_static = 0 ";
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

# now check for sub processes
# don't want to delete and add since other information might have been added
# so check to see if a key piece already exists and update the record
# don't forget to set the user and date for each record.

        print "Found " . $a_inventory['inv_id'] . "\n";

# tatoarqadw10,system,timezone,MDT
# table: inventory; inv_zone
        if ($value[1] == 'system') {
          print "system found:\n";

# okay, it's assumed the system is the one discovered for each individual server
# due to multiple entries of the exact same item for some things, when the system entry 
# is found, clear out all the 'verified' flags for items where there can be more than 
# one of the same thing in the system that has no other identifying marks (CPU for instance).
# then for the multiple items one, select limit 1 and where verified is 0

# set the server flag so subsequent system entries don't reset the install.

          if ($server != $a_inventory['inv_name']) {
            $server = $a_inventory['inv_name'];

            print "clearing cpu validate flags.\n";
# remove verified from all cpu entries for this system
            $q_string  = "update ";
            $q_string .= "hardware ";
            $q_string .= "set hw_verified = 0 ";
            $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_type = 8";
            $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());

            print "clearing hard disk validate flags.\n";
# remove verified from all hard disk entries for this system
            $q_string  = "update ";
            $q_string .= "hardware ";
            $q_string .= "set hw_verified = 0 ";
            $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_type = 2";
            $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          }

          if ($value[2] == 'timezone') {
            print "timezone found:\n";

            $q_string  = "select zone_id ";
            $q_string .= "from zones ";
            $q_string .= "where zone_name like '%" . $value[3] . "%'";
            $q_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            $a_zones = mysql_fetch_array($q_zones);

# if we found the timezone in the database, update the inventory with it.
            if ($a_zones['zone_id'] > 0) {
              $skip = 'no';
              $query = 
                "inv_zone = " . $a_zones['zone_id'];

              $q_string = "update inventory set " . $query . " where inv_id = " . $a_inventory['inv_id'];
              if ($debug == 'no') {
                $result = mysql_query($q_string) or die($q_string . mysql_error());
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            }
          }

          if ($value[2] == 'uuid') {
            print "system uuid found:\n";
            $skip = 'no';

            $q_string = "update inventory set inv_uuid = '" . $value[3] . "' where inv_id = " . $a_inventory['inv_id'];
            if ($debug == 'no') {
              $result = mysql_query($q_string) or die($q_string . mysql_error());
            }
            if ($debug == 'yes') {
              print $q_string . "\n";
            }
          }


# mark the hostname in the interface table as a hostname
          if ($value[2] == 'hostname') {
            print "hostname found:\n";
            $skip = 'no';

# clear all the old 'int_hostname' entries as it'll break things.
            $q_string = "update interface set int_hostname = 0 where int_companyid = " . $a_inventory['inv_id'] . " ";
            if ($debug == 'no') {
              $result = mysql_query($q_string) or die($q_string . mysql_error());
            }
            if ($debug == 'yes') {
              print $q_string . "\n";
            }

# then no matter what, only the actual hostname will be marked as a hostname as there should be just one hostname entry in the file.
            $q_string = "update interface set int_hostname = 1 where int_companyid = " . $a_inventory['inv_id'] . " and int_server = \"" . $value[3] . "\" ";
            if ($debug == 'no') {
              $result = mysql_query($q_string) or die($q_string . mysql_error());
            }
            if ($debug == 'yes') {
              print $q_string . "\n";
            }
          }


# updates the last time the system was updated
# inv_kernel holds the yyyy-mm-dd
####lnmtcodcerl10,system,kernel,2012-10-08
          if ($value[2] == 'kernel') {
            print "last patch date found:\n";

            if (strlen($value[3]) > 0) {

# old info might still exist.
              if ($value[3] > 1000000) {
                $value[3] = date('Y-m-d', $value[3]);
              }

              $skip = 'no';
              $query = 
                "inv_kernel = \"" . $value[3] . "\"";

              $q_string = "update inventory set " . $query . " where inv_id = " . $a_inventory['inv_id'];
              if ($debug == 'no') {
                $result = mysql_query($q_string) or die($q_string . mysql_error());
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            }
          }

          if ($value[2] == 'filesystem') {
            print "filesystem found:\n";
# table: filesystem; rows: fs_verified, fs_user, fs_update;
# check the device itself and update if exists or add if it doesn't exist
#sqatxt-vmapp01,filesystem,/dev/mapper/vg00-root,2064208,/,20000,10000,20%
#sqatxt-vmapp01,filesystem,/dev/sda1,495844,/boot,20000,10000,20%
#sqatxt-vmapp01,filesystem,/dev/mapper/vg00-home,8256952,/home,20000,10000,20%
#sqatxt-vmapp01,filesystem,/dev/mapper/vg00-opt,41286208,/opt,20000,10000,20%
#sqatxt-vmapp01,filesystem,/dev/mapper/vg00-tmp,4128448,/tmp,20000,10000,20%
#sqatxt-vmapp01,filesystem,/dev/mapper/vg00-usr,4128448,/usr,20000,10000,20%
#sqatxt-vmapp01,[system],filesystem,/dev/mapper/vg00-var, 4128448,/var,20000,10000,20%
#tato0eudcnedb30,system, filesystem,/dev/mapper/vg00-root,1998672,/,20000,10000,20%
#    0              1      2            3                    4     5
# checking fs_device

            if (strlen($value[3]) > 0 && strlen($value[4]) > 0 && strlen($value[5]) > 0) {
              $skip = 'no';

              if ($value[4] == '') {
                $value[4] = 0;
              }
              if ($value[6] == '') {
                $value[6] = 0;
              }
              if ($value[7] == '') {
                $value[7] = 0;
              }

              $query = 
                "fs_companyid =   " . $a_inventory['inv_id'] . "," . 
                "fs_device    = \"" . $value[3]              . "\"," . 
                "fs_size      =   " . $value[4]              . "," . 
                "fs_mount     = \"" . $value[5]              . "\"," . 
                "fs_used      =   " . $value[6]              . "," . 
                "fs_avail     =   " . $value[7]              . "," . 
                "fs_percent   = \"" . $value[8]              . "\"," . 
                "fs_verified  =   " . '1'                    . "," . 
                "fs_user      =   " . '1'                    . "," . 
                "fs_update    = \"" . $date                  . "\"";

              $q_string  = "select fs_id ";
              $q_string .= "from filesystem ";
              $q_string .= "where fs_device = '" . $value[3] . "' and fs_companyid = " . $a_inventory['inv_id'];
              $q_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_filesystem = mysql_fetch_array($q_filesystem);

              if ($a_filesystem['fs_id'] == '') {
                $q_string = "insert into filesystem set fs_id = null," . $query;
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update filesystem set " . $query . " where fs_id = " . $a_filesystem['fs_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } 
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            }
          }
        }

# config has the rules for the configuration manager. Acknowledge them but don't process (doesn't report 'Skipping')
#brhm2euasa9aer2,config,resolv,domain,we911.esinet.pri
        if ($value[1] == 'config') {
          print "configuration found:\n";

          $skip = 'no';
        }

# model table has been updated. Should be able to find the entry properly
####sqatxt-vmapp01,hardware,cpu,Intel(R) Xeon(R) CPU X7560 2.27GHz

        if ($value[1] == 'hardware') {
          print "hardware found:\n";

# need to get the primary device for the data import in order to associate the hardware with the primary device.

          $q_string  = "select hw_id ";
          $q_string .= "from hardware ";
          $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1 ";
          $q_primary = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          if (mysql_num_rows($q_primary) > 0) {
            $a_primary = mysql_fetch_array($q_primary);
            $primary = $a_primary['hw_id'];
          } else {
            $primary = 0;
          }

# harder because some systems have hardware raid which hides the individual drives
# may not be able to import this information other than as a list of devices.
####sqatxt-vmapp01,hardware,hard disk,85.9 GB
#### wkkihiecerb30,hardware,hard disk,146.2 GB

          if ($value[2] == 'hard disk') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
              $q_string  = "select hw_id ";
              $q_string .= "from hardware ";
              $q_string .= "where hw_type = 2 and hw_companyid = " . $a_inventory['inv_id'] . " and hw_verified = 0 and hw_size = '" . $value[3] . "' ";
              $q_string .= "limit 1";
              $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_hardware = mysql_fetch_array($q_hardware);
            
              $query = 
                "hw_companyid =  " . $a_inventory['inv_id']      . "," . 
                "hw_hw_id     =  " . $primary                    . "," . 
                "hw_type      =  " . "2"                         . "," . 
                "hw_size      = '" . trim($value[3])             . "'," . 
                "hw_verified  =  " . '1'                         . "," . 
                "hw_user      =  " . '1'                         . "," . 
                "hw_update    = '" . $date                       . "'";

              if ($a_hardware['hw_id'] == '') {
                $q_string = "insert into hardware set hw_id = null," . $query . ",hw_group = " . $a_inventory['inv_manager'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update hardware set " . $query . " where hw_id = " . $a_hardware['hw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Hard Disk\n";
              }
            }
          }

# one entry for memory so no problem in identifiying and updating
####sqatxt-vmapp01,hardware,memory,3925308 kB
          if ($value[2] == 'memory') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
              $q_string  = "select hw_id ";
              $q_string .= "from hardware ";
              $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_type = 4 ";
              $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_hardware = mysql_fetch_array($q_hardware);

              $query = 
                "hw_companyid =  " . $a_inventory['inv_id']      . "," . 
                "hw_hw_id     =  " . $primary                    . "," . 
                "hw_type      =  " . "4"                         . "," . 
                "hw_size      = '" . trim($value[3])             . "'," . 
                "hw_vendorid  =  " . "0"                         . "," . 
                "hw_verified  =  " . '1'                         . "," . 
                "hw_user      =  " . '1'                         . "," . 
                "hw_update    = '" . $date                       . "'";

              if ($a_hardware['hw_id'] == '') {
                $q_string = "insert into hardware set hw_id = null," . $query . ",hw_group = " . $a_inventory['inv_manager'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update hardware set " . $query . " where hw_id = " . $a_hardware['hw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Memory\n";
              }
            }
          }

####sqatxt-vmapp01,hardware,cpu,cputype,numcores
# checking number of cores value; if blank, then not Sun so skip it for now
          if ($value[2] == 'cpu') {
            if (strlen($value[3]) > 0 && strlen($value[4]) > 0) {
              $skip = 'no';
              $q_string  = "select hw_id,mod_id,mod_speed ";
              $q_string .= "from hardware ";
              $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
              $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_type = 8 and mod_name = '" . trim($value[3]) . "' and mod_size = '" . $value[4] . "' and hw_verified = 0 ";
              $q_string .= "limit 1";
              $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_hardware) > 0) {
                $a_hardware = mysql_fetch_array($q_hardware);

                $query = 
                  "hw_companyid =  " . $a_inventory['inv_id']      . "," . 
                  "hw_hw_id     =  " . $primary                    . "," . 
                  "hw_type      =  " . "8"                         . "," . 
                  "hw_size      = '" . trim($value[4])             . "'," . 
                  "hw_speed     = '" . $a_hardware['mod_speed']    . "'," . 
                  "hw_vendorid  =  " . $a_hardware['mod_id']       . "," . 
                  "hw_verified  =  " . '1'                         . "," . 
                  "hw_user      =  " . '1'                         . "," . 
                  "hw_update    = '" . $date                       . "'";
  
                $q_string = "update hardware set " . $query . " where hw_id = " . $a_hardware['hw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string  = "select mod_id,mod_speed ";
                $q_string .= "from models ";
                $q_string .= "where mod_name = '" . trim($value[3]) . "' and mod_size = '" . $value[4] . "' ";
                $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
                if (mysql_num_rows($q_models) > 0) {
                  $a_models = mysql_fetch_array($q_models);

                  $query = 
                    "hw_companyid =  " . $a_inventory['inv_id']      . "," . 
                    "hw_hw_id     =  " . $primary                    . "," . 
                    "hw_type      =  " . "8"                         . "," . 
                    "hw_size      = '" . trim($value[4])             . "'," . 
                    "hw_speed     = '" . $a_models['mod_speed']      . "'," . 
                    "hw_vendorid  =  " . $a_models['mod_id']         . "," . 
                    "hw_verified  =  " . '1'                         . "," . 
                    "hw_user      =  " . '1'                         . "," . 
                    "hw_update    = '" . $date                       . "'";
  
                  $q_string = "insert into hardware set hw_id = null," . $query . ",hw_group = " . $a_inventory['inv_manager'];
                  if ($debug == 'no') {
                    $result = mysql_query($q_string) or die($q_string . mysql_error());
                  }
                } else {
                  print "ERROR: Unable to locate model: \"" . trim($value[3]) . "\" size: \"" . $value[4] . "\"\n";
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing CPU Info: " . $value[0] . "\n";
              }
            }
          }

        }

        if ($value[1] == 'software') {
          print "software found:\n";
# table: software; rows: sw_verified, sw_user, sw_update;
#sqatxt-vmapp01,software,os,Red Hat Enterprise Linux Server release 6.2 (Santiago)
# check: sw_type = OS; just update it with the text
# current software listing: os, backup, monitor, centrify, instance, mysqld, informix, postgres, opnet, datapal, oracle, sudo, httpd, wildfly, vmtoolsd, newrelic

# Operating System
          if ($value[2] == 'os') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
# determine operating system
              $vendortrim = trim($value[3]);
              $list = split(" ", $vendortrim);

              $vendor = 'Unknown: ' . $vendortrim;
              $os = "Unknown";
# straight linux check
              if ($list[0] == 'Linux' || $list[1] == 'Linux' || $list[2] == 'Linux') {
                $vendor = "Linux";
                $os = "Linux";
              }
# red hat based systems
              if ($list[0] == 'CentOS' || $list[0] == 'Fedora' || $list[0] == 'Red') {
                $vendor = "Red Hat";
                $os = "Linux";
              }
# misc non redhat/linux systems
              if ($list[0] == 'Debian' || $list[0] == 'Ubuntu') {
                $vendor = "Debian";
                $os = "Linux";
              }
# misc non redhat/linux systems
              if ($list[0] == 'SUSE') {
                $vendor = "SUSE";
                $os = "Linux";
              }
              if ($list[0] == "Solaris" || $list[1] == 'Solaris') {
                $vendor = "Oracle";
                $os = "SunOS";
              }
              if ($list[0] == "HP-UX" || $list[0] == 'Tru64') {
                $vendor = "HP";
                $os = "HP-UX";
              }
              if ($list[0] == "Free") {
                $vendor = "FreeBSD";
                $os = "FreeBSD";
              }

              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_software = mysql_fetch_array($q_software);

              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . $vendor                     . "\"," . 
                "sw_type      = \"" . 'OS'                        . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

              if ($a_software['sw_id'] == '') {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_manager'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing OS\n";
              }
            }
          }

#incojs01,software,backup,NetBackup-Solaris_x86_10_64 7.1.0.4
          if ($value[2] == 'backup') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Backups'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_software = mysql_fetch_array($q_software);

              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Symantec'                  . "\"," . 
                "sw_type      = \"" . 'Backups'                   . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

              if ($a_software['sw_id'] == '') {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 9";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Backups\n";
              }
            }
          }

#incojs01,software,monitor,08.60.501
          if ($value[2] == 'monitor') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Monitoring'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_software = mysql_fetch_array($q_software);

              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'HP'                        . "\"," . 
                "sw_type      = \"" . 'Monitoring'                . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

              if ($a_software['sw_id'] == '') {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 10";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Monitoring\n";
              }
            }
          }

# Centrify
# server, software, centrify, inv_centrify, inv_adzone, inv_domain
#lnmtcodcew40,software,centrify,2012-12-18,WebApps,e911zone.intrado.pri
          if ($value[2] == 'centrify') {
            $skip = 'no';
            $query = 
              "inv_centrify = '" . trim($value[3]) . "'," . 
              "inv_adzone   = '" . trim($value[4]) . "'," . 
              "inv_domain   = '" . trim($value[5]) . "'";

            $q_string = "update inventory set " . $query . " where inv_id = " . $a_inventory['inv_id'];

            if ($debug == 'no') {
              $result = mysql_query($q_string) or die($q_string . mysql_error());
            }
            if ($debug == 'yes') {
              print $q_string . "\n";
            }
          } else {
            if ($debug == 'yes') {
              print "Missing Centrify\n";
            }
          }
# instance
#enwdcocsdca25,software,instance,+ASM2
          if ($value[2] == 'instance') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Oracle'                    . "\"," . 
                "sw_type      = \"" . 'Instance'                  . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Instance' and sw_software = '" . trim($value[3]) . "'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 8";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Instance\n";
              }
            }
          }

# database listings
#incojs01,software,mysqld,/opt/csw/mysql5/libexec/amd64/mysqld  Ver 5.0.75 for pc-solaris2.10 on i386 (Source distribution)
          if ($value[2] == 'mysqld') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Oracle'                    . "\"," . 
                "sw_type      = \"" . 'Commercial'                . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software like '%mysqld%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 8";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Instance\n";
              }
            }
          }

# database listings
#lnmtcodced10,software,informix,shared memory not initialized for INFORMIXSERVER '<NULL>'
          if ($value[2] == 'informix') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Informix'                  . "\"," . 
                "sw_type      = \"" . 'Open Source'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Open Source' and sw_software like '%INFORMIXSERVER%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 8";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Instance\n";
              }
            }
          }

# other software
#hilali01,software,postgres,psql (PostgreSQL) 7.4.17
          if ($value[2] == 'postgres') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'PostgreSQL'                . "\"," . 
                "sw_type      = \"" . 'Open Source'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Open Source' and sw_software like '%psql%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 8";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Instance\n";
              }
            }
          }

#nonacocsdba10,software,opnet,
          if ($value[2] == 'opnet') {
# opnet software, can't get the version yet so just populate value[3] with 'OpNet' for now
            $value[3] = 'OpNet';
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Riverbed'                  . "\"," . 
                "sw_type      = \"" . 'Commercial'                . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software = 'OpNet' ";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
              if (mysql_num_rows($q_software) == 0) {
# Don't change it if it's an update but default owned by Monitoring
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 10 ";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing OpNet version\n";
              }
            }
          }

#nonacocsdba10,software,datapalette,
          if ($value[2] == 'datapal') {
# datapalette software, can't get the version yet so just populate value[3] with 'DataPalette' for now
            $value[3] = 'DataPalette';
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'HP'                        . "\"," . 
                "sw_type      = \"" . 'Commercial'                . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software = 'DataPalette' ";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
              if (mysql_num_rows($q_software) == 0) {
# by default datapalette is used to update oracle tables. Don't change it if it's an update though
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 8";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing DataPalette version\n";
              }
            }
          }

#nonacocsdba10,software,oracle,
          if ($value[2] == 'oracle') {
# Oracle software, can't get the version yet so just populate value[3] with 'Oracle' for now
            $value[3] = 'Oracle';
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Oracle'                    . "\"," . 
                "sw_type      = \"" . 'Commercial'                . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software = 'Oracle' ";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
              if (mysql_num_rows($q_software) == 0) {
# by default Oracle is owned by the dbas but don't change it if it's an update
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = 8";
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Oracle version\n";
              }
            }
          }

# other software
#incojs01,software,sudo,Sudo version 1.7.2p6
          if ($value[2] == 'sudo') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Sudo'                      . "\"," . 
                "sw_type      = \"" . 'Open Source'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Open Source' and sw_software like '%sudo%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_manager'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Instance\n";
              }
            }
          }

# other software
#incojs01,software,httpd,Server version: Apache/2.0.63
          if ($value[2] == 'httpd') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Apache Foundation'         . "\"," . 
                "sw_type      = \"" . 'Open Source'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Open Source' and (sw_software like '%apache%' or sw_software like '%lighttp%') ";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_manager'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Instance\n";
              }
            }
          }

#lnmt1duasneap10,software,wildfly,Installed
          if ($value[2] == 'wildfly') {
            if (strlen($value[3]) > 0) {
              $value[3] = 'Wildfly';
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Red Hat'                   . "\"," . 
                "sw_type      = \"" . 'Open Source'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Open Source' and sw_software like '%Wildfly%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_appadmin'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Version Information\n";
              }
            }
          }


#lnmt1duasneap10,software,vmtoolsd,VMware Tools daemon, version 10.0.6.54238 (build-3560309)
          if ($value[2] == 'vmtoolsd') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'VMware'                    . "\"," . 
                "sw_type      = \"" . 'Commercial'                . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software like '%VMware Tools daemon%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_manager'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Banner\n";
              }
            }
          }


#lnmt1duasneap10,software,newrelic,Installed
          if ($value[2] == 'newrelic') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
              $value[3] = 'NewRelic';

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'NewRelic'         . "\"," . 
                "sw_type      = \"" . 'Commercial'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software like '%newrelic%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_appadmin'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Installed String\n";
              }
            }
          }


#lnmt1duasneap10,software,newrelic,Installed
          if ($value[2] == 'java') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
              $value[3] = 'java ' . $value[3];

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Oracle'                    . "\"," . 
                "sw_type      = \"" . 'Commercial'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software like '%java%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_appadmin'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Installed String\n";
              }
            }
          }


#lnmt1duasneap10,software,newrelic,Installed
          if ($value[2] == 'openjdk') {
            if (strlen($value[3]) > 0) {
              $skip = 'no';
              $value[3] = 'openjdk ' . $value[3];

# set up query
              $query = 
                "sw_companyid =   " . $a_inventory['inv_id']      . "," . 
                "sw_product   =   " . $a_inventory['inv_product'] . "," . 
                "sw_software  = \"" . trim($value[3])             . "\"," . 
                "sw_vendor    = \"" . 'Oracle'                    . "\"," . 
                "sw_type      = \"" . 'Commercial'               . "\"," . 
                "sw_verified  =   " . '1'                         . "," . 
                "sw_user      =   " . '1'                         . "," . 
                "sw_update    = \"" . $date                       . "\"";

# is it already in the inventory?
              $q_string  = "select sw_id ";
              $q_string .= "from software ";
              $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'Commercial' and sw_software like '%openjdk%'";
              $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              if (mysql_num_rows($q_software) == 0) {
                $q_string = "insert into software set sw_id = null," . $query . ",sw_group = " . $a_inventory['inv_appadmin'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $a_software = mysql_fetch_array($q_software);
                $q_string = "update software set " . $query . " where sw_id = " . $a_software['sw_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing Installed String\n";
              }
            }
          }




# Commercial
          if ($value[2] == 'commercial') {
          }
# Intrado
          if ($value[2] == 'custom') {
          }


        }


        if ($value[1] == 'network') {
          if ($value[2] == 'interface') {
            print "interface found:\n";
# table: interface; rows: int_verified, int_user, int_update, int_groupname, int_gate, int_default, int_int_id
#sqatxt-vmapp01,interface,eth0,,0,,,,1
#sqatxt-vmapp01,interface,eth1,10.105.64.74,0,255.255.255.192,00:50:56:B8:68:1B,orapub,10.105.64.254,0,lan900
#sqatxt-vmapp01,interface,lo,127.0.0.1,0,255.0.0.0,,10.105.64.254,0,
# check: int_addr or int_eth
# update!
#tato0eudcnedb30,network,interface,ens224,10.39.3.18,0,24,00:50:56:99:f4:7a,,10.39.3.254,0

# make sure we have good data. Only need to make sure an IP or MAC are available. bonded interfaces on ien voice have no ip, etc.
            if (strlen($value[4]) > 0 || strlen($value[7]) > 0) {
              $skip = 'no';

# default value for ipv6
              if ($value[5] == '') {
                $value[5] = 0;
              }

# check to see if there's a parent (field 11) and get the parent id if so.
# also update the parent to be virtual and a redundant interface (check for OS)
#bhmali32,network,interface,lan0,,0,,14:02:EC:7B:7B:B5,,,0,lan900
#bhmali32,network,interface,lan1,98.78.90.4,0,255.255.255.224,14:02:EC:7B:7B:B4,,,0,lan901
#bhmali32,network,interface,lan2,,0,,14:02:EC:7B:7B:B7,,,0,lan902
#bhmali32,network,interface,lan4,10.80.142.4,0,255.255.255.0,D0:BF:9C:40:68:04, ,10.80.142.254,0 ,lan900
#0       ,1      ,2        ,3   ,4          ,5,6            ,7                ,8,9            ,10,11
#bhmali32,network,interface,lan5,,0,,D0:BF:9C:40:68:05,,,0,lan901
#bhmali32,network,interface,lan6,192.168.1.1,0,255.255.255.0,D0:BF:9C:40:68:C0,,,0,lan902
#bhmali32,network,interface,lan900,10.80.142.4,0,255.255.255.0,D0:BF:9C:40:68:04,,10.80.142.254,1,
#bhmali32,network,interface,lan901,98.78.90.4,0,255.255.255.224,14:02:EC:7B:7B:B4,,,0,
#bhmali32,network,interface,lan902,192.168.1.1,0,255.255.255.0,D0:BF:9C:40:68:C0,,,0,
#bhmali32,network,interface,lo0,127.0.0.1,0,255.0.0.0,lo0,,,0,

# redundancy check of interfaces; bond0, lan900, etc
# blank it for now as it's causing issues
$value[11] = '';
              if ($value[11] != '') {
                $q_string  = "select int_id ";
                $q_string .= "from interface ";
                $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_face = '" . $value[11] . "' ";
                $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
                if (mysql_num_rows($q_interface) == 0) {
                  $value[11] = 0;
                } else {
                  $a_interface = mysql_fetch_array($q_interface);
                  $value[11] = $a_interface['int_id'];

                  $redundancy = 0;
                  if ($os == 'HP-UX') {
                    $redundancy = 11;
                  }
                  if ($os == 'Linux') {
                    $redundancy = 2;
                  }
                  if ($os == 'SunOS') {
                    $redundancy = 4;
                  }

                  $q_string  = "update ";
                  $q_string .= "interface ";
                  $q_string .= "set ";
                  $q_string .= "int_virtual = 1,";
                  $q_string .= "int_redundancy = " . $redundancy . " ";
                  $q_string .= "where int_id = " . $value[11] . " ";
                  $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

                }
              } else {
                $value[11] = 0;
              }

# see if the address exists
              $q_string  = "select int_id ";
              $q_string .= "from interface ";
              $q_string .= "where (int_addr = '" . $value[4] . "' or int_eth = '" . $value[7] . "') ";
              $q_string .= "and int_face = '" . $value[3] . "' ";
              $q_string .= "and int_companyid = " . $a_inventory['inv_id'] . " ";
              $q_string .= "and int_ip6 = " . $value[5] . " ";
              $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_interface = mysql_fetch_array($q_interface);

# if it's an actual IP address, convert to digital CIDR value
              if (filter_var($value[6], FILTER_VALIDATE_IP)) {
# returns INF so if > 32, return 0
                $mask = mask2cidr($value[6]);
                if ($mask > 32) {
                  $mask = 0;
                }
              } else {
                $mask = $value[6];
              }
              if ($mask == '') {
                $mask = 0;
              }

# put in once validation is done
#              "int_user      =   " . '1'                    . "," . 
              $query  = "int_companyid =   " . $a_inventory['inv_id'] . ",";
              if ($a_interface['int_id'] == '') {
                if ($value[3] == 'lo' || $value[3] == 'lo0') {
                  $query .= "int_server    = \"" . "localhost"            . "\",";
                  $query .= "int_type      =   " . "7"                    . ",";
                } else {
                  $query .= "int_server    = \"" . $value[0] . "\",";
                }
              } else {
# hard set name and type for loopback interfaces
                if ($value[3] == 'lo' || $value[3] == 'lo0') {
                  $query .= "int_server    = \"" . "localhost"            . "\",";
                  $query .= "int_type      =   " . "7"                    . ",";
                }
              }
#              $query .= "int_int_id    =   " . $value[11]             . ",";
              $query .= "int_face      = \"" . $value[3]              . "\",";
              $query .= "int_ip6       =   " . $value[5]              . ",";
              $query .= "int_addr      = \"" . $value[4]              . "\",";
              $query .= "int_vaddr     =   " . "1"                    . ",";
              $query .= "int_eth       = \"" . $value[7]              . "\",";
              $query .= "int_veth      =   " . "1"                    . ",";
# don't want to blank manually entered gateways during figuring out what the gateway is.
              if (strlen($value[9]) > 0) {
                $query .= "int_gate      = \"" . $value[9]              . "\",";
                $query .= "int_vgate     =   " . "1"                    . ",";
              }
# don't want to blank manually entered primary/default routes during figuring out what the actual default route is.
              if (strlen($value[10]) > 0) {
                $query .= "int_primary   = \"" . $value[10]              . "\",";
              }
              $query .= "int_mask      =   " . $mask                  . ",";
              $query .= "int_groupname = \"" . $value[8]              . "\",";
              $query .= "int_verified  =   " . '1'                    . ",";
              $query .= "int_update    = \"" . $date                  . "\"";

              if ($a_interface['int_id'] == '') {
# add a server name/interface name if the interface doesn't exist
                $q_string = "insert into interface set int_id = null," . $query;
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update interface set " . $query . " where int_id = " . $a_interface['int_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing either an IP or MAC from this line:\n" . $value[0] . "," . $value[1] . "," . $value[2] . "," . $value[3] . "," . $value[4] . "," . $value[5] . "," . $value[6] . "," . $value[7] . "\n";
              }
            }
          }
          if ($value[2] == 'routing') {
            print "routing found:\n";
# table: routing; rows: route_verified, route_user, route_update;
#     0           1     2    3           4             5             6
#sqatxt-vmapp01,routing,0,10.105.64.64,0.0.0.0,     255.255.255.192,eth1
#sqatxt-vmapp01,routing,0,0.0.0.0,     10.105.64.65,0.0.0.0,        eth1
#tato0eudcnedb30,network,routing,0,10.100.78.143,10.39.3.254,255.255.255.255,ens224
# checking route_address

# make sure we have good data
# solaris doesn't associate routes with interfaces all the way down.
            if (strlen($value[4]) > 0 && strlen($value[5]) > 0 && strlen($value[6]) > 0) {
              $skip = 'no';

# see if the address exists
              $q_string  = "select route_id ";
              $q_string .= "from routing ";
              $q_string .= "where route_address = '" . $value[4] . "' and route_companyid = " . $a_inventory['inv_id'];
              $q_routing = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_routing = mysql_fetch_array($q_routing);

              $q_string  = "select int_id ";
              $q_string .= "from interface ";
              $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_face = '" . $value[7] . "' and int_ip6 = " . $value[3] . " ";
              $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
              $a_interface = mysql_fetch_array($q_interface);

              if ($a_interface['int_id'] == '') {
                $a_interface['int_id'] = 0;
              }

# if it's an actual IP address, convert to digital CIDR value
              if (filter_var($value[6], FILTER_VALIDATE_IP)) {
# returns INF so if > 32, return 0
                $mask = mask2cidr($value[6]);
                if ($mask > 32) {
                  $mask = 0;
                }
              } else {
                $mask = $value[6];
              }

# check for static routes; if an old chksys script or for some reason a blank static route field
              if (!isset($value[7]) || $value[7] == '') {
                $value[7] = 0;
              }

              $query = 
                "route_companyid =   " . $a_inventory['inv_id'] . "," . 
                "route_address   = \"" . $value[4]              . "\"," . 
                "route_gateway   = \"" . $value[5]              . "\"," . 
                "route_mask      =   " . $mask                  . "," . 
                "route_ipv6      =   " . $value[3]              . "," . 
                "route_interface =   " . $a_interface['int_id'] . "," . 
                "route_verified  =   " . '1'                    . "," . 
                "route_user      =   " . '1'                    . "," . 
                "route_static    =   " . $value[7]              . "," . 
                "route_update    = \"" . $date                  . "\"";

              if ($a_routing['route_id'] == '') {
                $q_string = "insert into routing set route_id = null," . $query;
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              } else {
                $q_string = "update routing set " . $query . " where route_id = " . $a_routing['route_id'];
                if ($debug == 'no') {
                  $result = mysql_query($q_string) or die($q_string . mysql_error());
                }
              }
              if ($debug == 'yes') {
                print $q_string . "\n";
              }
            } else {
              if ($debug == 'yes') {
                print "Missing the source IP from this line:\n" . $value[0] . "," . $value[1] . "," . $value[2] . "," . $value[3] . "," . $value[4] . "," . $value[5] . "," . $value[6] . "," . $value[7] . "\n";
              }
            }
          }
        }
        if ($skip == 'yes') {
          print "Skipping: " . $process . "\n";
        }
      } else {
        print "Sorry: Unable to locate " . $value[0] . " in the database.\n";
      }
    } else {
      print "Server name is blank.\n";
    }
  }

?>
