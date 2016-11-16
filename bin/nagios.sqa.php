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
  $q_string .= "int_addr,int_gate,inv_ssh,inv_location,int_xpoint,int_ypoint,int_zpoint,int_ssh,int_ping,";
  $q_string .= "grp_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "where int_nagios = 1 and inv_status = 0 and sw_type = 'OS' and int_ip6 = 0 and int_type = 1 and inv_location = 39 and inv_manager != 12 ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $groupname = str_replace(" ", "-", $a_inventory['grp_name']);

    if (filter_var($a_inventory['int_addr'], FILTER_VALIDATE_IP)) {

# determine operating system
      $value = split(" ", $a_inventory['sw_software']);

# straight linux check
      if ($value[0] == 'Linux' || $value[1] == 'Linux' || $value[2] == 'Linux') {
        $os = "linux-server";
      }
# red hat based systems
      if ($value[0] == 'CentOS' || $value[0] == 'Fedora' || $value[0] == 'Red') {
        $os = "linux-server";
      }
# misc non redhat/linux systems
      if ($value[0] == 'Debian' || $value[0] == 'Ubuntu' || $value[0] == 'SUSE') {
        $os = "linux-server";
      }
      if ($value[0] == "Solaris" || $value[1] == 'Solaris') {
        $os = "solaris-server";
      }
      if ($value[0] == "HP-UX") {
        $os = "hpux-server";
      }
      if ($value[0] == "Tru64") {
        $os = "osf1-server";
      }
      if ($value[0] == "Free") {
        $os = "freebsd-server";
      }

      if (strlen($a_inventory['inv_function']) == 0) {
        $a_inventory['inv_function'] = $a_inventory['sw_software'];
      }

      $q_string  = "select int_xpoint,int_ypoint,int_zpoint ";
      $q_string .= "from interface ";
      $q_string .= "where int_addr = '" . $a_inventory['int_gate'] . "' ";
      $q_intgate = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_intgate = mysql_fetch_array($q_intgate);

      if (($a_inventory['inv_xpoint'] + $a_inventory['inv_ypoint'] + $a_inventory['inv_zpoint']) > 0) {
        $a_inventory['int_xpoint'] += $a_intgate['int_xpoint'];
        $a_inventory['int_ypoint'] += $a_intgate['int_ypoint'];
        $a_inventory['int_zpoint'] += $a_intgate['int_zpoint'];
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
      print "\tparents\t\t\t" . $a_inventory['int_gate'] . "\n";
      print "\ticon_image_alt\t\t" . $a_inventory['inv_function'] . "\n";
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

# production longmont
      if ($a_inventory['inv_location'] == 3) {
        $prodservers .= $prodcomma . $a_inventory['inv_name'];
        $prodcomma = ",";
      }
# sqa servers
      if ($a_inventory['inv_location'] == 39) {
        $sqaservers .= $sqacomma . $a_inventory['inv_name'];
        $sqacomma = ",";
      }
# lab servers
      if ($a_inventory['inv_location'] == 31) {
        $labservers .= $labcomma . $a_inventory['inv_name'];
        $labcomma = ",";
      }
# contact one servers
      if ($a_inventory['inv_location'] == 29) {
        $contactoneservers .= $contactonecomma . $a_inventory['inv_name'];
        $contactonecomma = ",";
      }

# ssh to servers
      if ($a_inventory['int_ssh'] == 1 && $a_inventory['inv_ssh'] == 1) {
        $sshservers .= $sshcomma . $a_inventory['inv_name'];
        $sshcomma = ",";
      }
# ping servers
      if ($a_inventory['int_ping'] == 1) {
        $pingservers .= $pingcomma . $a_inventory['inv_name'];
        $pingcomma = ",";
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
  print "# Define an optional hostgroup for Linux machines\n";
  print "\n";
  if (strlen($contactoneservers) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  contactone ; The name of the hostgroup\n";
    print "        alias           ContactOne Servers ; Long name of the group\n";
    print "        members         " . $contactoneservers . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($labservers) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  labservers ; The name of the hostgroup\n";
    print "        alias           TechOps Lab Servers ; Long name of the group\n";
    print "        members         " . $labservers . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($sqaservers) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  sqaservers ; The name of the hostgroup\n";
    print "        alias           SQA Servers ; Long name of the group\n";
    print "        members         " . $sqaservers . "\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($prodservers) > 0) {
    print "define hostgroup{\n";
    print "        hostgroup_name  prodservers ; The name of the hostgroup\n";
    print "        alias           Production Servers ; Long name of the group\n";
    print "        members         " . $prodservers . "\n";
    print "        }\n";
    print "\n";
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
  }

?>
