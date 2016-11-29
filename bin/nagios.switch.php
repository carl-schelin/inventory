#!/usr/local/bin/php
<?php

# code bit to load passwords from a file vs hard coding in the script.
  $pw_array = file("/var/apache2/passwords");

  $linuxdo = "no";
  $solarisdo = "no";
  $hpuxdo = "no";
  $tru64do = "no";
  $freebsddo = "no";

  for ($i = 0; $i < count($pw_array); $i++) {
    $value = chop($pw_array[$i]);
    $list = split(":", $value);

    if ($list[0] == "inventory") {
      $pw_db = "inventory";
      $pw_admin = $list[2];
      $pw_password = $list[3];
    }
  }


# build a nagios config file listing:
#
#define host{
#	use                     solaris-server
#	host_name		coolcacsdca15
#	alias			Database Server
#	address			10.105.200.80
#	parents			10.105.200.254
#	icon_image_alt		Solaris 10 5/08
#        }
#

# only get systems that are checked for 'nagios' on the main page.
# only get interfaces that are marked 'mgt'.
# only get gateways that are set.



  $connection = mysql_pconnect("localhost", $pw_admin, $pw_password) or die("Error: ".mysql_error());

  mysql_select_db($pw_db, $connection) or die("Error: ".mysql_error());


  print "###############################################################################\n";
  print "# LOCALHOST.CFG - SAMPLE OBJECT CONFIG FILE FOR MONITORING THIS MACHINE\n";
  print "#\n";
  print "#\n";
  print "# NOTE: This config file is intended to serve as an *extremely* simple\n";
  print "#       example of how you can create configuration entries to monitor\n";
  print "#       the local (Linux) machine.\n";
  print "#\n";
  print "###############################################################################\n";
  print "\n";
  print "\n";
  print "\n";
  print "\n";
  print "###############################################################################\n";
  print "###############################################################################\n";
  print "#\n";
  print "# HOST DEFINITION\n";
  print "#\n";
  print "###############################################################################\n";
  print "###############################################################################\n";
  print "\n";
  print "# Define a host for the local machine\n";
  print "\n";

  $prodcomma = '';
  $sqacomma = '';
  $labcomma = '';
  $contactonecomma = '';
  $sshcomma = '';
  $pingcomma = '';

# get systems to be managed through nagios

  $q_string  = "select inv_id,inv_name,inv_function,";
  $q_string .= "sw_software,";
  $q_string .= "int_addr,int_gate,inv_ssh,inv_location,int_xpoint,int_ypoint,int_zpoint,int_ssh,int_ping,int_http,int_ftp,";
  $q_string .= "grp_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "where int_nagios = 1 and inv_status = 0 and sw_type = 'OS' and int_ip6 = 0 and int_type = 1 and inv_manager = 12 ";
  $q_string .= "order by int_addr ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $groupname = str_replace(" ", "-", $a_inventory['grp_name']);

    if (filter_var($a_inventory['int_addr'], FILTER_VALIDATE_IP)) {

      $os = "generic-switch";

      if (strlen($a_inventory['inv_function']) == 0) {
        $a_inventory['inv_function'] = $a_inventory['sw_software'];
      }

# default contact_groups is 'admins'
# int_hours 0 = workhours, 1 = 24x7
# default check_period is 24x7
# int_notify 0 = none, 1 = email, 2 = page
# if no notifications, notifications_enabled = 0
# if none and email, normal group,
# if page, tack on -page,

      $disabled = '';
      if ($a_inventory['int_notify'] == 0) {
        $disabled = "\tnotifications_enabled\t0\n";
      }

      if ($a_inventory['int_notify'] == 2) {
        $groupname = $groupname . "-page";
      }

      print "define host{\n";
      print "\tuse\t\t\t" . $os . "\n";
      print "\thost_name\t\t" . $a_inventory['inv_name'] . "\n";
      print "\talias\t\t\t" . $a_inventory['inv_name'] . "\n";
      print "\taddress\t\t\t" . $a_inventory['int_addr'] . "\n";
      if ($a_inventory['int_gate'] != '10.100.128.1') {
        print "\tparents\t\t\t" . $a_inventory['int_gate'] . "\n";
      }
      print "\ticon_image\t\tswitch40.png\n";
      print "\ticon_image_alt\t\t" . $a_inventory['inv_function'] . "\n";
      print "\tvrml_image\t\tswitch40.png\n";
      print "\tstatusmap_image\t\tswitch40.gd2\n";
      if (($a_inventory['inv_xpoint'] + $a_inventory['inv_ypoint'] + $a_inventory['inv_zpoint']) > 0) {
        print "\t2d_coords\t\t" . $a_inventory['int_xpoint'] . "," . $a_inventory['int_ypoint'] . "\n";
        print "\t3d_coords\t\t" . $a_inventory['int_xpoint'] . "," . $a_inventory['int_ypoint'] . "," . $a_inventory['int_zpoint'] . "\n";
      }
      print "\tcontact_groups\t\t" . $groupname . ",Monitoring\n";
      print $disabled;
      if ($a_inventory['int_hours'] == 0) {
        print "\tcheck_period\t\tworkhours\n";
      } else {
        print "\tcheck_period\t\t24x7\n";
      }

      print "\t}\n\n";

# lab servers
      if ($a_inventory['inv_location'] == 31) {
        if (strpos($lab_members, $a_inventory['int_addr']) === false) {
          $lab_members .= $labcomma . $a_inventory['int_addr'];
          $labcomma = ",";
        }
      }
# sqa servers
      if ($a_inventory['inv_location'] == 39) {
        if (strpos($sqa_members, $a_inventory['int_addr']) === false) {
          $sqa_members .= $sqacomma . $a_inventory['int_addr'];
          $sqacomma = ",";
        }
      }
# production longmont
      if ($a_inventory['inv_location'] == 3) {
        if (strpos($prod_members, $a_inventory['int_addr']) === false) {
          $prod_members .= $prodcomma . $a_inventory['int_addr'];
          $prodcomma = ",";
        }
      }
# contact one servers
      if ($a_inventory['inv_location'] == 29) {
        if (strpos($contactone_members, $a_inventory['int_addr']) === false) {
          $contactone_members .= $contactonecomma . $a_inventory['int_addr'];
          $contactonecomma = ",";
        }
      }

# ssh to servers
      if ($a_inventory['int_ssh'] == 1) {
        $sshservers .= $sshcomma . $a_inventory['inv_name'];
        $sshcomma = ",";
      }
# ping servers
      if ($a_inventory['int_ping'] == 1) {
        $pingservers .= $pingcomma . $a_inventory['inv_name'];
        $pingcomma = ",";
      }
# http servers
      if ($a_inventory['int_http'] == 1) {
        $httpservers .= $httpcomma . $a_inventory['inv_name'];
        $httpcomma = ",";
      }
# ftp servers
      if ($a_inventory['int_ftp'] == 1) {
        $ftpservers .= $ftpcomma . $a_inventory['inv_name'];
        $ftpcomma = ",";
      }

    }
  }

  print "\n";
  print "###############################################################################\n";
  print "###############################################################################\n";
  print "#\n";
  print "# HOST GROUP DEFINITION\n";
  print "#\n";
  print "###############################################################################\n";
  print "###############################################################################\n";
  print "\n";
  print "# Create a new hostgroup for switches\n";
  print "\n";
  if (strlen($lab_members) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  lab_switches            ; The name of the hostgroup\n";
    print "        alias           Gateways in the Lab     ; Long name of the group\n";
    print "        members         " . $lab_members . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($sqa_members) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  sqa_switches            ; The name of the hostgroup\n";
    print "        alias           SQA/Dev Gateways        ; Long name of the group\n";
    print "        members         " . $sqa_members . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($prod_members) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  corp_switches           ; The name of the hostgroup\n";
    print "        alias           Gateways in Corp        ; Long name of the group\n";
    print "        members         " . $prod_members . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($e911_members) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  e911_switches           ; The name of the hostgroup\n";
    print "        alias           Gateways in e911 Zone   ; Long name of the group\n";
    print "        members         " . $e911_members . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($dmz_members) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  dmz_switches            ; The name of the hostgroup\n";
    print "        alias           Gateways in the DMZ     ; Long name of the group\n";
    print "        members         " . $dmz_members . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($contactone_members) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  contactone_switches             ; The name of the hostgroup\n";
    print "        alias           Contactone Gateways     ; Long name of the group\n";
    print "        members         " . $contactone_members . "\n";
    print "        }\n";
  }
  print "###############################################################################\n";
  print "###############################################################################\n";
  print "#\n";
  print "# SERVICE DEFINITIONS\n";
  print "#\n";
  print "###############################################################################\n";
  print "###############################################################################\n";
  print "\n";
  print "\n";
  print "# Define a service to \"ping\" the local machine\n";
  print "\n";
  if (strlen($pingservers) > 0) {
    print "define service{\n";
    print "        use                             local-service         ; Name of service template to use\n";
    print "        host_name                       " . $pingservers . "\n";
    print "        service_description             PING\n";
    print "        check_command                   check_ping!100.0,20%!500.0,60%\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($sshservers) > 0) {
    print "define service{\n";
    print "        use                             local-service         ; Name of service template to use\n";
    print "        host_name                       " . $sshservers . "\n";
    print "        service_description             SSH\n";
    print "        check_command                   check_ssh\n";
    print "        }\n";
    print "\n";
  }  if (strlen($httpservers) > 0) {
    print "define service{\n";
    print "        use                             local-service         ; Name of service template to use\n";
    print "        host_name                       " . $httpservers . "\n";
    print "        service_description             HTTP\n";
    print "        check_command                   check_http\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($ftpservers) > 0) {
    print "define service{\n";
    print "        use                             local-service         ; Name of service template to use\n";
    print "        host_name                       " . $ftpservers . "\n";
    print "        service_description             FTP\n";
    print "        check_command                   check_ftp\n";
    print "        }\n";
    print "\n";
  }

?>
