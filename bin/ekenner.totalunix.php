#!/usr/local/bin/php
<?php
# Script: ekenner.totalunix.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
#Here is what I am going to need to report on monthly going forward,
# 
#Total number of servers by OS,
#·         Linux -
#·         HPUS -
#·         Centos -
#·         Solaris –
#·         Window – (will be pulled from AD)
# 
#Total servers in Production - 
# 
#Total servers in DEV, TEST, SQA, CIL (all LABS) - 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# initialize number of OS's
  $linux = 0;
  $centos = 0;
  $debian = 0;
  $oracle = 0; # oracle enterprise linux, oracle linux
  $redhat = 0;
  $suse = 0;
  $fedora = 0;
  $ubuntu = 0;
  $other = 0;  # other linux
  $hpux = 0;
  $solaris = 0;
  $totalos = 0;

# initialize locations
  $production = 0;
  $support = 0;
  $sqa = 0;
  $development = 0;
  $lab4 = 0;
  $totalloc = 0;

  $system = '';
  $location = '';

  print "\"Server\",\"OS\",\"Location\",\"Group\"\n";

# note: if location is not sqa, dev, or lab, then it's production due to hawaii, alaska, miami, etc...

  $q_string  = "select inv_id,inv_name,loc_name,grp_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "where inv_status = 0 ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

# set flag to count by location if it's a unix or linux box.
    $flag = 0;
    $os = return_System($a_inventory['inv_id']); 

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    if ($os == 'Linux') {
      $linux++;
      $totalos++;
      $flag = 1;
      $system = "Linux";

      if (preg_match("/centos/i", $a_software['sw_software'])) {
        $centos++;
        $system .= " (CentOS)";
      }
      if (preg_match("/debian/i", $a_software['sw_software'])) {
        $debian++;
        $system .= " (Debian)";
      }
      if (preg_match("/oracle.*linux/i", $a_software['sw_software'])) {
        $oracle++;
        $system .= " (Oracle Unbreakable Linux)";
      }
      if (preg_match("/red hat/i", $a_software['sw_software'])) {
        $redhat++;
        $system .= " (Red Hat)";
      }
      if (preg_match("/suse/i", $a_software['sw_software'])) {
        $suse++;
        $system .= " (SUSE)";
      }
      if (preg_match("/fedora/i", $a_software['sw_software'])) {
        $fedora++;
        $system .= " (Fedora)";
      }
      if (preg_match("/ubuntu/i", $a_software['sw_software'])) {
        $ubuntu++;
        $system .= " (Ubuntu)";
      }
      if (preg_match("/other.*linux/i", $a_software['sw_software'])) {
        $other++;
        $system .= " (Other Linux)";
      }
    }
    if ($os == 'HP-UX') {
      $hpux++;
      $totalos++;
      $flag = 1;
      $system = "HP-UX";
    }
    if ($os == 'SunOS') {
      $solaris++;
      $totalos++;
      $flag = 1;
      $system = "Solaris";
    }

    if ($flag) {
      $totalloc++;
      if ($a_inventory['loc_name'] == 'Intrado CIL Data Center - Longmont') {
        $support++;
        $location = "CIL";
      } else {
        if ($a_inventory['loc_name'] == 'Intrado SQA Data Center - Longmont') {
          $sqa++;
          $location = "SQA";
        } else {
          if ($a_inventory['loc_name'] == 'Intrado Corp Dev Data Center - Longmont') {
            $development++;
            $location = "Dev";
          } else {
            if ($a_inventory['loc_name'] == 'Intrado Lab 4 Data Center - Longmont') {
              $lab4++;
              $location = "Lab 4";
            } else {
              $production++;
              $location = "Prod";
            }
          }
        }
      }
      print "\"" . $a_inventory['inv_name'] . "\",\"" . $system . "\",\"" . $location . "\",\"" . $a_inventory['grp_name'] . "\"\n";
    }
  }

  $headers  = "From: root <root@incojs01.scc911.com>\r\n";
  $headers .= "CC: " . $Sitedev . "\r\n";

  $email = "ed.kenner@intrado.com";

  $body  = "Total number of servers by OS: " . $totalos . "\n";
  $body .= " - Linux - " . $linux . "\n";
  $body .= " -- Red Hat - " . $redhat . "\n";
  $body .= " -- Centos - " . $centos . "\n";
  $body .= " -- Debian - " .  $debian . "\n";
  $body .= " -- Oracle Unbreakable Linux - " . $oracle . "\n";
  $body .= " -- SUSE - " . $suse . "\n";
  $body .= " -- Fedora - " . $fedora . "\n";
  $body .= " -- Ubuntu - " . $ubuntu . "\n";
  $body .= " -- Other Linux - " . $other . "\n";
  $body .= " - HP-UX - " . $hpux . "\n";
  $body .= " - Solaris - " . $solaris . "\n\n";

  $body .= "Total number of servers by Location: " . $totalloc . "\n";
  $body .= " - Production - " . $production . "\n";
  $body .= " - Production Support - " . $support . "\n";
  $body .= " - SQA - " . $sqa . "\n";
  $body .= " - Development - " . $development . "\n";
  $body .= " - Lab 4 - " . $lab4 . "\n\n";

  mail($email, "Monthly Unix Count", $body, $headers);

?>
