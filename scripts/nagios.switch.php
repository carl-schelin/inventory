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
  $pingcomma = '';

# get systems to be managed through nagios

  $q_string  = "select inv_id,inv_name,inv_function,";
  $q_string .= "sw_software,";
  $q_string .= "int_server,int_addr,int_gate,inv_ssh,inv_location,inv_product,int_ssh,int_ping,int_http,int_ftp,int_smtp,";
  $q_string .= "grp_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_svr_software on inv_svr_software.svr_companyid = inv_inventory.inv_id ";
  $q_string .= "left join inv_software     on inv_software.sw_id             = inv_svr_software.svr_softwareid ";
  $q_string .= "left join inv_sw_types     on inv_sw_types.typ_id            = inv_software.sw_type ";
  $q_string .= "left join inv_interface    on inv_interface.int_companyid    = inv_inventory.inv_id ";
  $q_string .= "left join inv_groups       on inv_groups.grp_id              = inv_inventory.inv_manager ";
  $q_string .= "where int_nagios = 1 and inv_status = 0 and typ_name = 'OS' and int_ip6 = 0 and int_type = 1 and inv_manager = 12 ";
  $q_string .= "order by int_addr ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

    $groupname = str_replace(" ", "-", $a_inv_inventory['grp_name']);

    if (filter_var($a_inv_inventory['int_addr'], FILTER_VALIDATE_IP)) {

      $os = "generic-switch";

      if (strlen($a_inv_inventory['inv_function']) == 0) {
        $a_inv_inventory['inv_function'] = $a_inv_inventory['sw_software'];
      }

# default contact_groups is 'admins'
# int_hours 0 = workhours, 1 = 24x7
# default check_period is 24x7
# int_notify 0 = none, 1 = email, 2 = page
# if no notifications, notifications_enabled = 0
# if none and email, normal group,
# if page, tack on -page,

      $disabled = '';
      if ($a_inv_inventory['int_notify'] == 0) {
        $disabled = "\tnotifications_enabled\t0\n";
      }

      if ($a_inv_inventory['int_notify'] == 2) {
        $groupname = $groupname . "-page";
      }

      print "define host{\n";
      print "\tuse\t\t\t" . $os . "\n";
      print "\thost_name\t\t" . $a_inv_inventory['int_addr'] . "\n";
      print "\talias\t\t\t" . $a_inv_inventory['int_addr'] . "\n";
      print "\taddress\t\t\t" . $a_inv_inventory['int_addr'] . "\n";
      if ($a_inv_inventory['int_gate'] != '10.100.128.1') {
        print "\tparents\t\t\t" . $a_inv_inventory['int_gate'] . "\n";
      }
      print "\ticon_image\t\tswitch40.png\n";
      print "\ticon_image_alt\t\t" . $a_inv_inventory['inv_function'] . "\n";
      print "\tvrml_image\t\tswitch40.png\n";
      print "\tstatusmap_image\t\tswitch40.gd2\n";
      print "\tcontact_groups\t\t" . $groupname . ",Monitoring\n";
      print $disabled;
      if ($a_inv_inventory['int_hours'] == 0) {
        print "\tcheck_period\t\tworkhours\n";
      } else {
        print "\tcheck_period\t\t24x7\n";
      }

      print "\t}\n\n";

     if (!isset($productcomma[$a_inv_inventory['inv_product']])) {
        $productcomma[$a_inv_inventory['inv_product']] = '';
      }
# add to production hostgroups
      if ($a_inv_inventory['inv_product'] > 0) {
        $products[$a_inv_inventory['inv_product']] .= $productcomma[$a_inv_inventory['inv_product']] . $a_inv_inventory['int_addr'];
        $productcomma[$a_inv_inventory['inv_product']] = ',';
      }

# lab servers
      if ($a_inv_inventory['inv_location'] == 31) {
        if (strpos($lab_members, $a_inv_inventory['int_addr']) === false) {
          $lab_members .= $labcomma . $a_inv_inventory['int_addr'];
          $labcomma = ",";
        }
      }
# sqa servers
      if ($a_inv_inventory['inv_location'] == 39) {
        if (strpos($sqa_members, $a_inv_inventory['int_addr']) === false) {
          $sqa_members .= $sqacomma . $a_inv_inventory['int_addr'];
          $sqacomma = ",";
        }
      }
# production longmont
      if ($a_inv_inventory['inv_location'] == 3) {
        if (strpos($prod_members, $a_inv_inventory['int_addr']) === false) {
          $prod_members .= $prodcomma . $a_inv_inventory['int_addr'];
          $prodcomma = ",";
        }
      }
# contact one servers
      if ($a_inv_inventory['inv_location'] == 29) {
        if (strpos($contactone_members, $a_inv_inventory['int_addr']) === false) {
          $contactone_members .= $contactonecomma . $a_inv_inventory['int_addr'];
          $contactonecomma = ",";
        }
      }

# ssh to servers
      if ($a_inv_inventory['int_ssh'] == 1) {
        $sshservers .= $sshcomma . $a_inv_inventory['int_addr'];
        $sshcomma = ",";
      }
# ping servers
      if ($a_inv_inventory['int_ping'] == 1) {
        $pingservers .= $pingcomma . $a_inv_inventory['int_addr'];
        $pingcomma = ",";
      }
# http servers
      if ($a_inv_inventory['int_http'] == 1) {
        $httpservers .= $httpcomma . $a_inv_inventory['int_addr'];
        $httpcomma = ",";
      }
# ftp servers
      if ($a_inv_inventory['int_ftp'] == 1) {
        $ftpservers .= $ftpcomma . $a_inv_inventory['int_addr'];
        $ftpcomma = ",";
      }
# smtp servers
      if ($a_inv_inventory['int_smtp'] == 1) {
        $smtpservers .= $smtpcomma . $a_inv_inventory['inv_name'];
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

  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from inv_products ";
  $q_string .= "order by prod_name ";
  $q_inv_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_products = mysqli_fetch_array($q_inv_products)) {
    if (strlen($products[$a_inv_products['prod_id']]) > 0) {

      $hostgroup = str_replace(" ", "_", $a_inv_products['prod_name']);

      print "define hostgroup{\n";
      print "        hostgroup_name  switch_" . $hostgroup . " ; The name of the hostgroup\n";
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
  if (strlen($smtpservers) > 0) {
    print "define service{\n"; 
    print "        use                             local-service         ; Name of service template to use\n";
    print "        host_name                       " . $smtpservers . "\n";
    print "        service_description             SMTP\n";
    print "        check_command                   check_smtp\n";
    print "        }\n";
    print "\n";
  }

  mysqli_close($db);

?>
