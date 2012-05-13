#!/usr/local/bin/php
<?php

# code bit to load passwords from a file vs hard coding in the script.
  $pw_array = file("/var/apache2/passwords");

  $linuxdo = "no";
  $solarisdo = "no";
  $hpuxno = "no";
  $tru64no = "no";
  $freebadno = "no";

  for ($i = 0; $i < count($pw_array); $i++) {
    $value = chop($pw_array[$i]);
    $list = split(":", $value);

    if ($list[0] == "inventory") {
      $pw_db = "inventory";
      $pw_admin = $list[2];
      $pw_password = $list[3];
    }
  }

  $connection = mysql_pconnect("localhost", $pw_admin, $pw_password) or die("Error: ".mysql_error());

  mysql_select_db($pw_db, $connection) or die("Error: ".mysql_error());

  $q_string = "select inv_id,inv_name from inventory where inv_manager = 1 and inv_status = 0 and inv_ssh = 1 order by inv_name";
  $q_inventory = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string = "select sw_software from software where sw_type = 'OS' and sw_companyid = " . $a_inventory['inv_id'];
    $q_software = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

# determine operating system
    $value = split(" ", $a_software['sw_software']);

    if ($value[0] == "Solaris") {
      $os = "SunOS";
    }
    if ($value[0] == "Red" || $value[0] == "RedHat") {
      $os = "Linux";
    }
    if ($value[0] == "Debian" || $value[0] == "Ubuntu" || $value[0] == "CentOS") {
      $os = "Linux";
    }
    if ($value[0] == "Oracle") {
      $os = "Linux";
    }
    if ($value[0] == "HP-UX") {
      $os = "HP-UX";
    }
    if ($value[0] == "Tru64") {
      $os = "OSF1";
    }
    if ($value[0] == "Free") {
      $os = "FreeBSD";
    }
    if ($os == "") {
      $os = $value[0];
    }

    $name = split("/", $a_inventory['inv_name']);


###################################
##### Linux Interface Capture #####
###################################
# This section parses the ifconfig.good file for linux systems
# The output looks something like this:
#bond0     Link encap:Ethernet  HWaddr 00:15:C5:E4:26:1E  
#          inet addr:10.109.204.20  Bcast:10.109.204.255  Mask:255.255.255.0
#          inet6 addr: fe80::215:c5ff:fee4:261e/64 Scope:Link
#          UP BROADCAST RUNNING MASTER MULTICAST  MTU:1500  Metric:1
#          collisions:0 txqueuelen:0 
#
#bond1     Link encap:Ethernet  HWaddr 00:15:C5:E4:26:20  
#          inet addr:10.109.200.20  Bcast:10.109.200.255  Mask:255.255.255.0
#          inet6 addr: fe80::215:c5ff:fee4:2620/64 Scope:Link
#          UP BROADCAST RUNNING MASTER MULTICAST  MTU:1500  Metric:1
#          collisions:0 txqueuelen:0 
#
#eth0      Link encap:Ethernet  HWaddr 00:15:C5:E4:26:1E  
#          UP BROADCAST RUNNING SLAVE MULTICAST  MTU:1500  Metric:1
#          collisions:0 txqueuelen:1000 
#          Interrupt:169 Memory:f8000000-f8012100 
#
#eth1      Link encap:Ethernet  HWaddr 00:15:C5:E4:26:20  
#          UP BROADCAST RUNNING SLAVE MULTICAST  MTU:1500  Metric:1
#          collisions:0 txqueuelen:1000 
#          Interrupt:169 Memory:f4000000-f4012100 
#
#eth2      Link encap:Ethernet  HWaddr 00:15:C5:E4:26:20  
#          UP BROADCAST RUNNING SLAVE MULTICAST  MTU:1500  Metric:1
#          collisions:0 txqueuelen:1000 
#
#eth3      Link encap:Ethernet  HWaddr 00:15:C5:E4:26:1E  
#          UP BROADCAST RUNNING SLAVE MULTICAST  MTU:1500  Metric:1
#          collisions:0 txqueuelen:1000 
#
#lo        Link encap:Local Loopback  
#          inet addr:127.0.0.1  Mask:255.0.0.0
#          inet6 addr: ::1/128 Scope:Host
#          UP LOOPBACK RUNNING  MTU:16436  Metric:1
#          collisions:0 txqueuelen:0 

    if ($os == "Linux" && file_exists("/usr/local/admin/servers/" . $name[0] . "/ifconfig.good")) {
      $file = fopen("/usr/local/admin/servers/" . $name[0] . "/ifconfig.good", "r") or exit("unable to open: /usr/local/admin/servers/" . $name[0] . "/ifconfig.good");
      while(!feof($file)) {
        $process = trim(fgets($file));

        if (preg_match("/Link encap/",$process)) {

          $value = split(" ", $process);
          $iface = $value[0];
          $hwaddr = $value[10];
# if off by one, move ahead by one; for xotn interfaces, it'll be blank and caught by the default below
          if ($hwaddr == "HWaddr") {
            $hwaddr = $value[11];
          }
# change to all zeros if Loopback or blank to make it easier to parse in a script; (kevin request)
          if ($hwaddr == "Loopback" || $hwaddr == '') {
            $hwaddr = "00:00:00:00:00:00";
          }

# now get the inet output
          $process = trim(fgets($file));
          $value = split(" ", $process);
# clear and set defaults (again, kevin's request to make it easier to parse)
          $addr[0] = '';
          $addr[1] = '';
          $mask[0] = '255.255.255.0';
          $mask[1] = '255.255.255.0';
          if ($value[0] == "inet") {
            $addr = split(":", $value[1]);
            $mask = split(":", $value[3]);
          }
          $q_string = "select int_id,int_server,int_addr,int_eth,int_mask,int_verified from interface where int_companyid = " . $a_inventory['inv_id'] . " and int_face = '" . $iface . "' and int_ip6 = 0";
          $q_interface = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
          $a_interface = mysql_fetch_array($q_interface);

          $q_string = 
            "int_addr      = '" . $addr[1]                   . "'," . 
            "int_eth       = '" . $hwaddr                    . "'," . 
            "int_mask      = '" . $mask[1]                   . "'," . 
            "int_verified  =  " . '1';

          if ($a_interface['int_verified'] == '0') {
            if ($a_interface['int_addr'] == $addr[1] && $a_interface['int_eth'] == $hwaddr && $a_interface['int_mask'] == $mask[1]) {
              $q_string = "int_verified = 1";
            }
          }

# if there's no entry for the interface, prepare to add one
          if ($a_interface['int_id'] == '') {
            if ($iface == 'lo') {
              $a_inventory['inv_name'] = "loopback";
            }

            $q_interface = 
              "int_server    = '" . $a_inventory['inv_name'] . "'," . 
              "int_companyid =  " . $a_inventory['inv_id']   . "," . 
              "int_face      = '" . $iface                   . "'," . 
              "int_ip6       =  " . '0';

            $query = "insert into interface set int_id = NULL," . $q_string . "," . $q_interface;
            if ($linuxdo == "no") {
              print $query . "\n";
            } else {
              mysql_query($query, $connection) or die($query . ": " . mysql_error());
            }
          } else {
            if ($a_interface['int_verified'] == 0) {
              $query = "update interface set " . $q_string . " where int_id = " . $a_interface['int_id'];
              if ($linuxdo == "no") {
                print $query . "\n";
              } else {
                mysql_query($query, $connection) or die($query . ": " . mysql_error());
              }
            }
          }

# now get the inet6 output
          $process = trim(fgets($file));
          $value = split(" ", $process);
# clear and set defaults (again, kevin's request to make it easier to parse)
          $addr6[0] = '';
          $addr6[1] = '';
          $mask6[0] = '';
          $mask6[1] = '';
          if ($value[0] == "inet6") {
            $addr6 = split("\/", $value[2]);
            $mask6 = split("\/", $value[2]);

            $q_string = "select int_id,int_addr,int_eth,int_mask,int_verified from interface where int_companyid = " . $a_inventory['inv_id'] . " and int_face = '" . $iface . "' and int_ip6 = 1";
            $q_interface = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
            $a_interface = mysql_fetch_array($q_interface);

            $q_string = 
              "int_addr      = '" . $addr6[0]              . "'," . 
              "int_eth       = '" . $hwaddr                . "'," . 
              "int_mask      = '" . $mask6[1]              . "'," . 
              "int_verified  =  " . '1';

            if ($a_interface['int_verified'] == '0') {
              if ($a_interface['int_addr'] == $addr6[0] && $a_interface['int_eth'] == $hwaddr && $a_interface['int_mask'] == $mask6[1]) {
                $q_string = "int_verified = 1";
              }
            }

# if there's no entry for the interface, prepare to add one
            if ($a_interface['int_id'] == '') {
              if ($iface == 'lo') {
                $a_inventory['inv_name'] = "loopback";
              }

              $q_interface = 
                "int_server    = '" . $a_inventory['inv_name'] . "'," . 
                "int_companyid =  " . $a_inventory['inv_id']   . "," . 
                "int_face      = '" . $iface                   . "'," . 
                "int_ip6       =  " . '1';

              $query = "insert into interface set int_id = NULL," . $q_string . "," . $q_interface;
              if ($linuxdo == "no") {
                print $query . "\n";
              } else {
                mysql_query($query, $connection) or die($query . ": " . mysql_error());
              }
            } else {
              if ($a_interface['int_verified'] == 0) {
                $query = "update interface set " . $q_string . " where int_id = " . $a_interface['int_id'];
                if ($linuxdo == "no") {
                  print $query . "\n";
                } else {
                  mysql_query($query, $connection) or die($query . ": " . mysql_error());
                }
              }
            }
          } else {
            $q_string = "select int_id from interface where int_companyid = " . $a_inventory['inv_id'] . " and int_face = '" . $iface . "' and int_ip6 = 1";
            $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            while ($a_interface = mysql_fetch_array($q_interface)) {
              $q_string = "delete from interface where int_id = " . $a_interface['int_id'];
              if ($linuxdo == "no") {
                print $q_string . "\n";
              } else {
                mysql_query($q_string) or die($q_string . ": " . mysql_error());
              }
            }
          }
        }

      }
      fclose($file);
      $q_string  = "select int_id,int_face,int_addr,int_eth,int_mask,int_verified,int_ip6 from interface where int_companyid = " . $a_inventory['inv_id'];
      $q_string .= " and (";
      $q_string .= "int_face not like 'eth%' and ";
      $q_string .= "int_face not like 'lo%' and ";
      $q_string .= "int_face not like 'e1000g%' and ";
      $q_string .= "int_face not like 'bond%' and ";
      $q_string .= "int_face not like 'eri%' and ";
      $q_string .= "int_face not like 'bge%' and ";
      $q_string .= "int_face not like 'ce%' and ";
      $q_string .= "int_face not like 'nge%' and ";
      $q_string .= "int_face != 'netmgt:' and ";
      $q_string .= "int_face != 'sermgt:' and ";
      $q_string .= "int_face not like 'xot%' and ";
      $q_string .= "int_face not like 'sit0%' and ";
      $q_string .= "int_face not like 'jnet0%' and ";
      $q_string .= "int_face not like 'pcn0%' and ";
      $q_string .= "int_face not like 'nxge%' and ";
      $q_string .= "int_face not like 'as0t%' and ";
      $q_string .= "int_face not like 'tap0%' and ";
      $q_string .= "int_face not like 'fxp0%' and ";
      $q_string .= "int_face not like 'br0%'";
      $q_string .= ")";
      $q_interface = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        print $a_inventory['inv_name'] . " - " . $a_interface['int_face'] . " - " . $a_interface['int_addr'] . " - " . $a_interface['int_eth'] . " - " . $a_interface['int_mask'] . "\n";
      }

##########################
##### Memory Capture #####
##########################

      if (file_exists("/usr/local/admin/servers/" . $name[0] . "/meminfo")) {
        $file = fopen("/usr/local/admin/servers/" . $name[0] . "/meminfo", "r") or exit("unable to open: /usr/local/admin/servers/" . $name[0] . "/meminfo");
        while(!feof($file)) {
          $process = trim(fgets($file));

          if (preg_match("/MemTotal:/",$process)) {

            $value = preg_split("/\s+/", $process);

            $q_string = "select mod_size,hw_id,hw_size from models left join hardware on hardware.hw_vendorid = models.mod_id where hw_companyid = " . $a_inventory['inv_id'] . " and hw_type = 4";
            $q_models = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
            $a_models = mysql_fetch_array($q_models);

            $models = preg_split("/\s+/", $a_models['mod_size']);

            $gig = 1024 * 1024;

# creating a sloppy table for figuring out the gross size. but save the info in the hardware size column for the actual value.
            $size = 0;
            if ($value[1] >   250000 && $value[1] <   750000) $size = 256;
            if ($value[1] >   750000 && $value[1] <  1250000) $size = 235;
            if ($value[1] >  1250000 && $value[1] <  1750000) $size = 236;
            if ($value[1] >  1750000 && $value[1] <  2250000) $size = 240;
            if ($value[1] >  2250000 && $value[1] <  2750000) $size = 255;
            if ($value[1] >  2750000 && $value[1] <  3400000) $size = 249;
            if ($value[1] >  3750000 && $value[1] <  4250000) $size = 242;
            if ($value[1] >  5750000 && $value[1] <  6250000) $size = 252;
            if ($value[1] >  7750000 && $value[1] <  8350000) $size = 244;
            if ($value[1] >  9750000 && $value[1] < 10250000) $size = 237;
            if ($value[1] > 11750000 && $value[1] < 12250000) $size = 238;
            if ($value[1] > 15750000 && $value[1] < 17250000) $size = 239;
            if ($value[1] > 31750000 && $value[1] < 33000000) $size = 251;
            if ($value[1] > 37000000 && $value[1] < 38250000) $size = 257;
            if ($value[1] > 47750000 && $value[1] < 50250000) $size = 250;

            $q_string =
              "hw_companyid =   " . $a_inventory['inv_id'] . "," . 
              "hw_vendorid  =   " . $size                  . "," . 
              "hw_type      =   " . "4"                    . "," . 
              "hw_size      =   " . $value[1]              . "," . 
              "hw_verified  =   " . "1";

            if ($a_models['hw_id'] == '') {
              $query = "insert into hardware set hw_id = NULL," . $q_string;
              if ($linuxdo == "no") {
                print $query . "\n";
              } else {
                mysql_query($query, $connection) or die($query . ": " . mysql_error());
              }
            } else {
              if ($a_models['hw_vendorid'] != $size && $a_models['hw_size'] != $value[1]) {
                $query = "update hardware set " . $q_string . " where hw_id = " . $a_models['hw_id'];
                if ($linuxdo == "no") {
                  print $query . "\n";
                } else {
                  mysql_query($query, $connection) or die($query . ": " . mysql_error());
                }
              }
            }
          }
        }
        fclose($file);
      } else {
#        print "Linux: Unable to open /usr/local/admin/servers/" . $name[0] . "/meminfo\n";
      }
    } else {
#      print "Linux: Unable to open /usr/local/admin/servers/" . $name[0] . "/ifconfig.good\n";
    }

#####################################
##### Solaris Interface Capture #####
#####################################
# This section parses the ifconfig.good file for solaris systems
# The output looks something like this:
#lo0: flags=2001000849<UP,LOOPBACK,RUNNING,MULTICAST,IPv4,VIRTUAL> mtu 8232 index 1
#        inet 127.0.0.1 netmask ff000000 
#e1000g0: flags=1000843<UP,BROADCAST,RUNNING,MULTICAST,IPv4> mtu 1500 index 2
#        inet 10.109.200.80 netmask ffffff00 broadcast 10.109.200.255
#        ether 0:14:4f:2a:e9:52 
#e1000g4: flags=1000843<UP,BROADCAST,RUNNING,MULTICAST,IPv4> mtu 1500 index 3
#        inet 192.168.240.20 netmask ffffffc0 broadcast 192.168.240.63
#        groupname orapriv
#        ether 0:15:17:14:9b:68 
#e1000g4:1: flags=9040843<UP,BROADCAST,RUNNING,MULTICAST,DEPRECATED,IPv4,NOFAILOVER> mtu 1500 index 3
#        inet 192.168.240.10 netmask ffffffc0 broadcast 192.168.240.63
#e1000g5: flags=1000843<UP,BROADCAST,RUNNING,MULTICAST,IPv4> mtu 1500 index 4
#        inet 10.109.204.90 netmask ffffff00 broadcast 10.109.204.255
#        groupname orapub
#        ether 0:15:17:14:9b:69 
#e1000g5:1: flags=9040843<UP,BROADCAST,RUNNING,MULTICAST,DEPRECATED,IPv4,NOFAILOVER> mtu 1500 index 4
#        inet 10.109.204.80 netmask ffffff00 broadcast 10.109.204.255
#e1000g5:2: flags=1040843<UP,BROADCAST,RUNNING,MULTICAST,DEPRECATED,IPv4> mtu 1500 index 4
#        inet 10.109.204.91 netmask ffffff00 broadcast 10.109.204.255
#e1000g6: flags=69040843<UP,BROADCAST,RUNNING,MULTICAST,DEPRECATED,IPv4,NOFAILOVER,STANDBY,INACTIVE> mtu 1500 index 5
#        inet 192.168.240.11 netmask ffffffc0 broadcast 192.168.240.63
#        groupname orapriv
#        ether 0:15:17:14:9c:a 
#e1000g7: flags=69040843<UP,BROADCAST,RUNNING,MULTICAST,DEPRECATED,IPv4,NOFAILOVER,STANDBY,INACTIVE> mtu 1500 index 6
#        inet 10.109.204.81 netmask ffffff00 broadcast 10.109.204.255
#        groupname orapub
#        ether 0:15:17:14:9c:b 

    if ($os == "SunOS" && file_exists("/usr/local/admin/servers/" . $name[0] . "/ifconfig.good")) {

      if ($name[0] == "incoag13") {
        $name[0] = "incoag10";
      }
      if ($name[0] == "incoag23") {
        $name[0] = "incoag20";
      }
      if ($name[0] == "incoga13") {
        $name[0] = "incoga10";
      }
      if ($name[0] == "incoga23") {
        $name[0] = "incoga20";
      }

      $file = fopen("/usr/local/admin/servers/" . $name[0] . "/ifconfig.good", "r") or exit("unable to open: /usr/local/admin/servers/" . $name[0] . "/ifconfig.good");
      while(!feof($file)) {
        $process = trim(fgets($file));

        if (preg_match("/flags/",$process)) {
          $value = split(" ", $process);
          $face = $value[0];

# now get the next line which should always be either inet or inet6
          $process = trim(fgets($file));
          $addr = split(" ", $process);
          if ($addr[0] == "inet") {
            $ipaddr = $addr[1];
            $mask = long2ip(hexdec($addr[3]));
            $ip6value = 0;
          }
          if ($addr[0] == "inet6") {
            $ip6 = split('\/', $addr[1]);
            $ipaddr = $ip6[0];
            $mask = $ip6[1];
            $ip6value = 1;
          }
# loopback interfaces only have the one interface
          $hwaddr = "00:00:00:00:00:00";
          if ($face != "lo0:") {
# now get either groupname for ipmp interfaces or ether for standalone interfaces
            $process = trim(fgets($file));
            $value = split(" ", $process);
            if ($value[0] == "ether") {
              $hwaddr = strtoupper($value[1]);
            }
            if ($value[0] == "groupname") {
              $process = trim(fgets($file));
              $value = split(" ", $process);
              if ($value[0] == "ether") {
                $hwaddr = strtoupper($value[1]);
              }
            }
          }

# fix hwaddr as on Sun boxes, it can be 0:4:3...
          $value = split(":", $hwaddr);
          $hwaddr = sprintf("%02s:%02s:%02s:%02s:%02s:%02s", $value[0], $value[1], $value[2], $value[3], $value[4], $value[5]);

          $q_string  = "select int_id,int_addr,int_eth,int_mask,int_verified,int_ip6 from interface ";
          $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_face = '" . $face . "' and int_ip6 = " . $ip6value;
          $q_interface = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
          $a_interface = mysql_fetch_array($q_interface);

          $q_string = 
            "int_addr     = '" . $ipaddr   . "'," . 
            "int_eth      = '" . $hwaddr   . "'," . 
            "int_mask     = '" . $mask     . "'," . 
            "int_ip6      = '" . $ip6value . "'," . 
            "int_verified =  " . '1';

          if ($a_interface['int_verified'] == '0') {
            if ($a_interface['int_addr'] == $ipaddr && $a_interface['int_eth'] == $hwaddr && $a_interface['int_mask'] == $mask) {
              $q_string = "int_verified = 1";
            }
          }

# if there's no entry for the interface, prepare to add one
          if ($a_interface['int_id'] == '') {
            $q_interface = 
              "int_server    = '" . $a_inventory['inv_name'] . "'," . 
              "int_companyid =  " . $a_inventory['inv_id']   . "," . 
              "int_face      = '" . $face                    . "'";

            $query = "insert into interface set int_id = NULL," . $q_string . "," . $q_interface;
            if ($solarisdo == "no") {
              print $query . "\n";
            } else {
               mysql_query($query, $connection) or die($query . ": " . mysql_error());
            }
          } else {
            if ($a_interface['int_verified'] == 0) {
              $query = "update interface set " . $q_string . " where int_id = " . $a_interface['int_id'];
              if ($solarisdo == "no") {
                print $query . "\n";
              } else {
                mysql_query($query, $connection) or die($query . ": " . mysql_error());
              }
            }
          }
        }
      }
      $q_string  = "select int_id,int_face,int_addr,int_eth,int_mask,int_verified,int_ip6 from interface where int_companyid = " . $a_inventory['inv_id'];
      $q_string .= " and (";
      $q_string .= "int_face not like 'eth%' and ";
      $q_string .= "int_face not like 'lo%' and ";
      $q_string .= "int_face not like 'e1000g%' and ";
      $q_string .= "int_face not like 'bond%' and ";
      $q_string .= "int_face not like 'eri%' and ";
      $q_string .= "int_face not like 'bge%' and ";
      $q_string .= "int_face not like 'ce%' and ";
      $q_string .= "int_face not like 'nge%' and ";
      $q_string .= "int_face != 'netmgt:' and ";
      $q_string .= "int_face != 'sermgt:' and ";
      $q_string .= "int_face not like 'xot%' and ";
      $q_string .= "int_face not like 'sit0%' and ";
      $q_string .= "int_face not like 'jnet0%' and ";
      $q_string .= "int_face not like 'pcn0%' and ";
      $q_string .= "int_face not like 'nxge%' and ";
      $q_string .= "int_face not like 'as0t%' and ";
      $q_string .= "int_face not like 'tap0%' and ";
      $q_string .= "int_face not like 'fxp0%' and ";
      $q_string .= "int_face not like 'br0%'";
      $q_string .= ")";
      $q_interface = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        print $a_inventory['inv_name'] . " - " . $a_interface['int_face'] . " - " . $a_interface['int_addr'] . " - " . $a_interface['int_eth'] . " - " . $a_interface['int_mask'] . "\n";
      }

##########################
##### Memory Capture #####
##########################

      if (file_exists("/usr/local/admin/servers/" . $name[0] . "/prtconf.output")) {
        $file = fopen("/usr/local/admin/servers/" . $name[0] . "/prtconf.output", "r") or exit("unable to open: /usr/local/admin/servers/" . $name[0] . "/prtconf.output");
        while(!feof($file)) {
          $process = trim(fgets($file));

          if (preg_match("/Memory Total:/",$process)) {

            $value = preg_split("/\s+/", $process);

            $q_string = "select mod_size,hw_id,hw_size from models left join hardware on hardware.hw_vendorid = models.mod_id where hw_companyid = " . $a_inventory['inv_id'] . " and hw_type = 4";
            $q_models = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
            $a_models = mysql_fetch_array($q_models);

            $models = preg_split("/\s+/", $a_models['mod_size']);

            $gig = 1024 * 1024;

# creating a sloppy table for figuring out the gross size. but save the info in the hardware size column for the actual value.
            $size = 0;
            if ($value[1] >   250000 && $value[1] <   750000) $size = 256;
            if ($value[1] >   750000 && $value[1] <  1250000) $size = 235;
            if ($value[1] >  1250000 && $value[1] <  1750000) $size = 236;
            if ($value[1] >  1750000 && $value[1] <  2250000) $size = 240;
            if ($value[1] >  2250000 && $value[1] <  2750000) $size = 255;
            if ($value[1] >  2750000 && $value[1] <  3400000) $size = 249;
            if ($value[1] >  3750000 && $value[1] <  4250000) $size = 242;
            if ($value[1] >  5750000 && $value[1] <  6250000) $size = 252;
            if ($value[1] >  7750000 && $value[1] <  8350000) $size = 244;
            if ($value[1] >  9750000 && $value[1] < 10250000) $size = 237;
            if ($value[1] > 11750000 && $value[1] < 12250000) $size = 238;
            if ($value[1] > 15750000 && $value[1] < 17250000) $size = 239;
            if ($value[1] > 31750000 && $value[1] < 33000000) $size = 251;
            if ($value[1] > 37000000 && $value[1] < 38250000) $size = 257;
            if ($value[1] > 47750000 && $value[1] < 50250000) $size = 250;

            $q_string =
              "hw_companyid =   " . $a_inventory['inv_id'] . "," . 
              "hw_vendorid  =   " . $size                  . "," . 
              "hw_type      =   " . "4"                    . "," . 
              "hw_size      =   " . $value[1]              . "," . 
              "hw_verified  =   " . "1";

            if ($a_models['hw_id'] == '') {
              $query = "insert into hardware set hw_id = NULL," . $q_string;
              if ($solarisdo == "no") {
                print $query . "\n";
              } else {
                mysql_query($query, $connection) or die($query . ": " . mysql_error());
              }
            } else {
              if ($a_models['hw_vendorid'] != $size && $a_models['hw_size'] != $value[1]) {
                $query = "update hardware set " . $q_string . " where hw_id = " . $a_models['hw_id'];
                if ($solarisdo == "no") {
                  print $query . "\n";
                } else {
                  mysql_query($query, $connection) or die($query . ": " . mysql_error());
                }
              }
            }
          }
        }
        fclose($file);
      } else {
#        print "Unable to open /usr/local/admin/servers/" . $name[0] . "/prtconf.output\n";
      }
    } else {
#      print "Unable to open /usr/local/admin/servers/" . $name[0] . "/ifconfig.good\n";
    }

###################################
##### HP-UX Interface Capture #####
###################################
# This section parses the ifconfig.good file for solaris systems
# The output looks something like this:
#Hardware Station        Crd Hdw   Net-Interface  NM  MAC       HP-DLPI DLPI
#Path     Address        In# State NamePPA        ID  Type      Support Mjr#
#0/3/1/0/6/1 0x0019BBEBEDD7 3   UP    lan3 snap3     4   ETHER     Yes     119
#0/4/1/0/6/1 0x0019BBEB0E2D 5   UP    lan5 snap5     6   ETHER     Yes     119
#LinkAgg0 0x001A4B05E86E 900 UP    lan900 snap900 9   ETHER     Yes     119
#LinkAgg1 0x001A4B05E86F 901 UP    lan901 snap901 10  ETHER     Yes     119
#LinkAgg2 0x000000000000 902 DOWN  lan902 snap902 11  ETHER     Yes     119
#LinkAgg3 0x000000000000 903 DOWN  lan903 snap903 12  ETHER     Yes     119
#LinkAgg4 0x000000000000 904 DOWN  lan904 snap904 13  ETHER     Yes     119
#lo0: flags=849<UP,LOOPBACK,RUNNING,MULTICAST>
#        inet 127.0.0.1 netmask ff000000 
#lan900: flags=8000000000001843<UP,BROADCAST,RUNNING,MULTICAST,CKO,PORT>
#        inet 10.161.10.12 netmask ffffff00 broadcast 10.161.10.255
#lan901: flags=8000000000001843<UP,BROADCAST,RUNNING,MULTICAST,CKO,PORT>
#        inet 216.67.95.42 netmask fffffff8 broadcast 216.67.95.47

    if ($os == "HP-UX-bob" && file_exists("/usr/local/admin/servers/" . $name[0] . "/ifconfig.good")) {
      $file = fopen("/usr/local/admin/servers/" . $name[0] . "/ifconfig.good", "r") or exit("unable to open: /usr/local/admin/servers/" . $name[0] . "/ifconfig.good");
      while(!feof($file)) {
        $process = trim(fgets($file));

        $hwaddr = '';
        if (preg_match("/ UP /",$process)) {
          $value = preg_split("/\s+/", $process);
          $ether[$value[4]] = $value[1];
print $value[1] . " - " . $value[8] . "\n";
        }
        if (preg_match("/flags/",$process)) {
          $value = split(" ", $process);
          $face = $value[0];
          $hwaddr = $ether[$face];

# now get the inet data;
          $process = trim(fgets($file));
          $value = split(" ", $process);
          $ipaddr = $value[1];
          $mask = long2ip(hexdec($value[3]));
          $ip6value = 0;

          $q_string  = "select int_id,int_addr,int_eth,int_mask,int_verified,int_ip6 from interface ";
          $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_face = '" . $face . "' and int_ip6 = " . $ip6value;
          $q_interface = mysql_query($q_string, $connection) or die($q_string . ": " . mysql_error());
          $a_interface = mysql_fetch_array($q_interface);

          $q_string = 
            "int_addr     = '" . $ipaddr   . "'," . 
            "int_eth      = '" . $hwaddr   . "'," . 
            "int_mask     = '" . $mask     . "'," . 
            "int_ip6      = '" . $ip6value . "'," . 
            "int_verified =  " . '1';

          if ($a_interface['int_verified'] == '0') {
            if ($a_interface['int_addr'] == $ipaddr && $a_interface['int_eth'] == $hwaddr && $a_interface['int_mask'] == $mask) {
              $q_string = "int_verified = 1";
            }
          }

# if there's no entry for the interface, prepare to add one
          if ($a_interface['int_id'] == '') {
            $q_interface = 
              "int_server    = '" . $a_inventory['inv_name'] . "'," . 
              "int_companyid =  " . $a_inventory['inv_id']   . "," . 
              "int_face      = '" . $face                    . "'";

            $query = "insert into interface set int_id = NULL," . $q_string . "," . $q_interface;
            if ($hpuxdo == "no") {
              print $query . "\n";
            } else {
              mysql_query($query, $connection) or die($query . ": " . mysql_error());
            }
          } else {
            if ($a_interface['int_verified'] == 0) {
              $query = "update interface set " . $q_string . " where int_id = " . $a_interface['int_id'];
              if ($hpuxdo == "no") {
                print $query . "\n";
              } else {
                mysql_query($query, $connection) or die($query . ": " . mysql_error());
              }
            }
          }
        }
      }
    }
  }
?>
