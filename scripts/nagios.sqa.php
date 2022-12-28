#!/usr/local/bin/php
<?php

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $directory = date('Ym');
  $date = date('Y-m-');
  $year = date('Y');
  $month = date('M');

# build a nagios config file listing:
#
#define host{
#       use                     solaris-server
#       host_name               lnmt1cuom2540
#       alias                   Database Server
#       address                 192.168.104.50
#       parents                 192.168.104.254
#       icon_image_alt          Solaris 10 5/08
#        }
#

# only get systems that are checked for 'nagios' on the main page.
# only get interfaces that are marked 'mgt'.
# only get gateways that are set.

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
  $c2hcomma = '';
  $pingcomma = '';

# get systems to be managed through nagios

  $q_string  = "select inv_id,inv_name,inv_function,";
  $q_string .= "sw_software,";
  $q_string .= "int_addr,int_gate,inv_ssh,inv_location,inv_product,";
  $q_string .= "int_ssh,int_ping,int_http,int_ftp,int_smtp,grp_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join inv_svr_software on inv_svr_software.svr_companyid = inventory.inv_id ";
  $q_string .= "left join software on software.sw_id = inv_svr_software.svr_softwareid ";
  $q_string .= "left join inv_sw_types on inv_sw_types.typ_id = software.sw_type ";
  if ($hostname == 'inventory.internal.pri') {
    $q_string .= "left join inv_hardware on inv_hardware.hw_companyid = inventory.inv_id ";
  }
  $q_string .= "left join inv_interface on inv_interface.int_companyid = inventory.inv_id ";
  $q_string .= "left join inv_groups    on inv_groups.grp_id           = inventory.inv_manager ";
  $q_string .= "where int_nagios = 1 and inv_status = 0 and typ_name = 'OS' and int_ip6 = 0 and int_management = 1 and inv_location = 39 and inv_manager != 12 ";
  if ($hostname == 'inventory.internal.pri') {
    $q_string .= "and hw_active != '1971-01-01' ";
  }
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $groupname = str_replace(" ", "-", $a_inventory['grp_name']);

    if (filter_var($a_inventory['int_addr'], FILTER_VALIDATE_IP)) {

# determine operating system
      $value = explode(" ", $a_inventory['sw_software']);

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
      print "\tcontact_groups\t\t" . $groupname . ",Monitoring\n";
      print $disabled;
      if ($a_inventory['int_hours'] == 0) {
        print "\tcheck_period\t\tworkhours\n";
      } else {
        print "\tcheck_period\t\t24x7\n";
      }

      print "\t}\n\n";

      if (!isset($productcomma[$a_inventory['inv_product']])) {
        $productcomma[$a_inventory['inv_product']] = '';
      }
# add to production hostgroups
      if ($a_inventory['inv_product'] > 0) {
        $products[$a_inventory['inv_product']] .= $productcomma[$a_inventory['inv_product']] . $a_inventory['inv_name'];
        $productcomma[$a_inventory['inv_product']] = ',';
      }

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
# ssh to servers but only if ssh flag is enabled
      if ($a_inventory['int_ssh'] == 1 && $a_inventory['inv_ssh'] == 1) {
        $sshservers .= $sshcomma . $a_inventory['inv_name'];
        $sshcomma = ",";
      }
# check cfg2html output but only if ssh is checked
      if ($a_inventory['int_cfg2html'] == 0 and $a_inventory['int_ssh'] == 1) {
        $c2hservers .= $c2hcomma . $a_inventory['inv_name'];
        $c2hcomma = ",";
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
# smtp servers
      if ($a_inventory['int_smtp'] == 1) {
        $smtpservers .= $smtpcomma . $a_inventory['inv_name'];
        $smtpcomma = ",";
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

  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from inv_products ";
  $q_string .= "order by prod_name ";
  $q_inv_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_products = mysqli_fetch_array($q_inv_products)) {
    if (strlen($products[$a_inv_products['prod_id']]) > 0) {

      $hostgroup = str_replace(" ", "_", $a_inv_products['prod_name']);

      print "define hostgroup{\n";
      print "        hostgroup_name  sqa_" . $hostgroup . " ; The name of the hostgroup\n";
      print "        alias           " . $a_inv_products['prod_name'] . " ; Long name of the group\n";
      print "        members         " . $products[$a_inv_products['prod_id']] . "\n";
      print "        }\n";
      print "\n";

    }
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
  if (strlen($httpservers) > 0) {
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
  if (strlen($smtpservers) > 0) {
    print "define service{\n"; 
    print "        use                             local-service         ; Name of service template to use\n";
    print "        host_name                       " . $smtpservers . "\n";
    print "        service_description             SMTP\n";
    print "        check_command                   check_smtp\n";
    print "        }\n";
    print "\n";
  }
  if (strlen($c2hservers) > 0) {
    print "define service{\n";
    print "        use                             local-service         ; Name of service template to use\n";
    print "        host_name                       " . $c2hservers . "\n";
    print "        service_description             cfg2html\n";
    print "        check_command                   check_http_content!" . $directory . "!(Created " . $date . "|" . $month . ".*" . $year . ")\n";
    print "        }\n";
    print "\n";
  }

  mysqli_close($db);

?>
