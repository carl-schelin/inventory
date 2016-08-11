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
# Add in a split showing totals for virtual vs physical
#

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'no';
  $debug = 'yes';

# initialize number of OS's
  $plinux = 0;
  $vlinux = 0;
  $pcentos = 0;
  $vcentos = 0;
  $pdebian = 0;
  $vdebian = 0;
  $poracle = 0; # oracle enterprise linux, oracle linux
  $voracle = 0; # oracle enterprise linux, oracle linux
  $predhat = 0;
  $vredhat = 0;
  $psuse = 0;
  $vsuse = 0;
  $pfedora = 0;
  $vfedora = 0;
  $pubuntu = 0;
  $vubuntu = 0;
  $pother = 0;  # other linux
  $vother = 0;  # other linux
  $phpux = 0;
  $vhpux = 0;
  $psolaris = 0;
  $vsolaris = 0;
  $ptotalos = 0;
  $vtotalos = 0;
  $pesx = 0;
  $vesx = 0;

# initialize locations
  $pproduction = 0;
  $vproduction = 0;
  $psupport = 0;
  $vsupport = 0;
  $psqa = 0;
  $vsqa = 0;
  $pdevelopment = 0;
  $vdevelopment = 0;
  $plab4 = 0;
  $vlab4 = 0;
  $ptotalloc = 0;
  $vtotalloc = 0;

  $system = '';
  $location = '';

  print "\"Server\",\"OS\",\"Location\",\"Group\"\n";

# note: if location is not sqa, dev, or lab, then it's production due to hawaii, alaska, miami, etc...

  $q_string  = "select inv_id,inv_name,loc_name,grp_name,mod_virtual ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join groups    on groups.grp_id         = inventory.inv_manager ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models    on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "where inv_status = 0 and hw_primary = 1 ";
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

    if (preg_match("/esx/i", $a_software['sw_software'])) {
      if ($a_inventory['mod_virtual']) {
        $vesx++;
      } else {
        $pesx++;
      }
    }

    if ($os == 'Linux') {
      if ($a_inventory['mod_virtual']) {
        $vlinux++;
        $vtotalos++;
      } else {
        $plinux++;
        $ptotalos++;
      }
      $flag = 1;
      $system = "Linux";

      if (preg_match("/centos/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $vcentos++;
        } else {
          $pcentos++;
        }
        $system .= " (CentOS)";
      }
      if (preg_match("/debian/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $vdebian++;
        } else {
          $pdebian++;
        }
        $system .= " (Debian)";
      }
      if (preg_match("/oracle.*linux/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $voracle++;
        } else {
          $poracle++;
        }
        $system .= " (Oracle Unbreakable Linux)";
      }
      if (preg_match("/red hat/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $vredhat++;
        } else {
          $predhat++;
        }
        $system .= " (Red Hat)";
      }
      if (preg_match("/suse/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $vsuse++;
        } else {
          $psuse++;
        }
        $system .= " (SUSE)";
      }
      if (preg_match("/fedora/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $vfedora++;
        } else {
          $pfedora++;
        }
        $system .= " (Fedora)";
      }
      if (preg_match("/ubuntu/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $vubuntu++;
        } else {
          $pubuntu++;
        }
        $system .= " (Ubuntu)";
      }
      if (preg_match("/other.*linux/i", $a_software['sw_software'])) {
        if ($a_inventory['mod_virtual']) {
          $vother++;
        } else {
          $pother++;
        }
        $system .= " (Other Linux)";
      }
    }
    if ($os == 'HP-UX') {
      if ($a_inventory['mod_virtual']) {
        $vhpux++;
        $vtotalos++;
      } else {
        $phpux++;
        $ptotalos++;
      }
      $flag = 1;
      $system = "HP-UX";
    }
    if ($os == 'SunOS') {
      if ($a_inventory['mod_virtual']) {
        $vsolaris++;
        $vtotalos++;
      } else {
        $psolaris++;
        $ptotalos++;
      }
      $flag = 1;
      $system = "Solaris";
    }

    if ($flag) {
      if ($a_inventory['mod_virtual']) {
        $vtotalloc++;
      } else {
        $ptotalloc++;
      }
      if ($a_inventory['loc_name'] == 'Intrado CIL Data Center - Longmont') {
        if ($a_inventory['mod_virtual']) {
          $vsupport++;
        } else {
          $psupport++;
        }
        $location = "CIL";
      } else {
        if ($a_inventory['loc_name'] == 'Intrado SQA Data Center - Longmont') {
          if ($a_inventory['mod_virtual']) {
            $vsqa++;
          } else {
            $psqa++;
          }
          $location = "SQA";
        } else {
          if ($a_inventory['loc_name'] == 'Intrado Corp Dev Data Center - Longmont') {
            if ($a_inventory['mod_virtual']) {
              $vdevelopment++;
            } else {
              $pdevelopment++;
            }
            $location = "Dev";
          } else {
            if ($a_inventory['loc_name'] == 'Intrado Lab 4 Data Center - Longmont') {
              if ($a_inventory['mod_virtual']) {
                $vlab4++;
              } else {
                $plab4++;
              }
              $location = "Lab 4";
            } else {
              if ($a_inventory['mod_virtual']) {
                $vproduction++;
              } else {
                $pproduction++;
              }
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

  if ($debug == 'no') {
    $email = "ed.kenner@intrado.com";
  } else {
    $email = "carl.schelin@intrado.com";
  }

  $body  = "Total number of servers by OS: " . $ptotalos . "/" . $vtotalos . " (" . ($ptotalos   + $vtotalos)   . ")\n";
  $body .= " - Linux - "                     . $plinux   . "/" . $vlinux   . " (" . ($plinux     + $vlinux)     . ")\n";
  $body .= " -- Red Hat - "                  . $predhat  . "/" . $vredhat  . " (" . ($predhat    + $vredhat)    . ")\n";
  $body .= " -- Centos - "                   . $pcentos  . "/" . $vcentos  . " (" . ($pcentos    + $vcentos)    . ")\n";
  $body .= " -- Debian - "                   . $pdebian  . "/" . $vdebian  . " (" . ($pdebian    + $vdebian)    . ")\n";
  $body .= " -- Oracle Unbreakable Linux - " . $poracle  . "/" . $voracle  . " (" . ($poracle    + $voracle)    . ")\n";
  $body .= " -- SUSE - "                     . $psuse    . "/" . $vsuse    . " (" . ($psuse      + $vsuse)      . ")\n";
  $body .= " -- Fedora - "                   . $pfedora  . "/" . $vfedora  . " (" . ($pfedora    + $vfedora)    . ")\n";
  $body .= " -- Ubuntu - "                   . $pubuntu  . "/" . $vubuntu  . " (" . ($pubuntu    + $vubuntu)    . ")\n";
  $body .= " -- Other Linux - "              . $pother   . "/" . $vother   . " (" . ($pother     + $vother)     . ")\n";
  $body .= " - HP-UX - "                     . $phpux    . "/" . $vhpux    . " (" . ($phpux      + $vhpux)      . ")\n";
  $body .= " - Solaris - "                   . $psolaris . "/" . $vsolaris . " (" . ($psolaris   + $vsolaris)   . ")\n\n";

  $body .= "Total number of servers by Location: " . $ptotalloc     . "/" . $vtotalloc    . " (" . ($ptotalloc    + $vtotalloc)    . ")\n";
  $body .= " - Production - "                      . $pproduction   . "/" . $vproduction  . " (" . ($pproduction  + $vproduction)  . ")\n";
  $body .= " - Production Support - "              . $psupport      . "/" . $vsupport     . " (" . ($psupport     + $vsupport)     . ")\n";
  $body .= " - SQA - "                             . $psqa          . "/" . $vsqa         . " (" . ($psqa         + $vsqa)         . ")\n";
  $body .= " - Development - "                     . $pdevelopment  . "/" . $vdevelopment . " (" . ($pdevelopment + $vdevelopment) . ")\n";
  $body .= " - Lab 4 - "                           . $plab4         . "/" . $vlab4        . " (" . ($plab4        + $vlab4)        . ")\n\n";

  $body .= "Physical/Virtual (Total)\n\n";

  mail($email, "Monthly Unix Count", $body, $headers);

?>
