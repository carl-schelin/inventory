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

# just changes who gets the email.
  $debug = 'yes';
  $debug = 'no';

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
  $ptotalunix = 0;
  $vtotalunix = 0;
# get ESX Hosts
  $ptotalesx = 0;
  $vtotalesx = 0;
# get Windows Hosts
  $ptotalwindows = 0;
  $vtotalwindows = 0;

# initialize unix locations
  $puproduction = 0;
  $vuproduction = 0;
  $pusupport = 0;
  $vusupport = 0;
  $pusqa = 0;
  $vusqa = 0;
  $pudevelopment = 0;
  $vudevelopment = 0;
  $pulab4 = 0;
  $vulab4 = 0;
  $putotalloc = 0;
  $vutotalloc = 0;
# initialize esx locations
  $peproduction = 0;
  $veproduction = 0;
  $pesupport = 0;
  $vesupport = 0;
  $pesqa = 0;
  $vesqa = 0;
  $pedevelopment = 0;
  $vedevelopment = 0;
  $pelab4 = 0;
  $velab4 = 0;
  $petotalloc = 0;
  $vetotalloc = 0;
# initialize Windows locations
  $pwproduction = 0;
  $vwproduction = 0;
  $pwsupport = 0;
  $vwsupport = 0;
  $pwsqa = 0;
  $vwsqa = 0;
  $pwdevelopment = 0;
  $vwdevelopment = 0;
  $pwlab4 = 0;
  $vwlab4 = 0;
  $pwtotalloc = 0;
  $vwtotalloc = 0;


  $system = '';
  $location = '';

  print "\"Server\",\"OS\",\"Location\",\"Group\"\n";

# note: if location is not sqa, dev, or lab, then it's production due to hawaii, alaska, miami, etc...

# except TechOps as those are likely imports from security center and are workstations.
  $q_string  = "select inv_id,inv_name,loc_name,grp_name,mod_virtual ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join groups    on groups.grp_id         = inventory.inv_manager ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models    on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "where inv_status = 0 and hw_primary = 1 and inv_manager != 14 ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

# set flag to count by location if it's a unix or linux box.
    $flag = 0;
    $locesx = 0;
    $locwindows = 0;
    $locunix = 0;

    $os = return_System($a_inventory['inv_id']); 

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    if (preg_match("/esx/i", $a_software['sw_software']) || preg_match("/vmware/i", $a_software['sw_software'])) {
      if ($a_inventory['mod_virtual']) {
        $vtotalesx++;
      } else {
        $ptotalesx++;
      }
      $locesx = 1;
      $flag = 1;
      $system = 'ESX';
    }

    if (preg_match("/windows/i", $a_software['sw_software'])) {
      if ($a_inventory['mod_virtual']) {
        $vtotalwindows++;
      } else {
        $ptotalwindows++;
      }
      $locwindows = 1;
      $flag = 1;
      $system = 'Windows';
    }

    if ($os == 'Linux') {
      $locunix = 1;
      if ($a_inventory['mod_virtual']) {
        $vlinux++;
        $vtotalunix++;
      } else {
        $plinux++;
        $ptotalunix++;
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
      $locunix = 1;
      if ($a_inventory['mod_virtual']) {
        $vhpux++;
        $vtotalunix++;
      } else {
        $phpux++;
        $ptotalunix++;
      }
      $flag = 1;
      $system = "HP-UX";
    }
    if ($os == 'SunOS') {
      $locunix = 1;
      if ($a_inventory['mod_virtual']) {
        $vsolaris++;
        $vtotalunix++;
      } else {
        $psolaris++;
        $ptotalunix++;
      }
      $flag = 1;
      $system = "Solaris";
    }

    if ($flag) {

      if ($locunix) {
        if ($a_inventory['mod_virtual']) {
          $vutotalloc++;
        } else {
          $putotalloc++;
        }
        if ($a_inventory['loc_name'] == 'Intrado CIL Data Center - Longmont') {
          if ($a_inventory['mod_virtual']) {
            $vusupport++;
          } else {
            $pusupport++;
          }
          $location = "CIL";
        } else {
          if ($a_inventory['loc_name'] == 'Intrado SQA Data Center - Longmont') {
            if ($a_inventory['mod_virtual']) {
              $vusqa++;
            } else {
              $pusqa++;
            }
            $location = "SQA";
          } else {
            if ($a_inventory['loc_name'] == 'Intrado Corp Dev Data Center - Longmont') {
              if ($a_inventory['mod_virtual']) {
                $vudevelopment++;
              } else {
                $pudevelopment++;
              }
              $location = "Dev";
            } else {
              if ($a_inventory['loc_name'] == 'Intrado Lab 4 Data Center - Longmont') {
                if ($a_inventory['mod_virtual']) {
                  $vulab4++;
                } else {
                  $pulab4++;
                }
                $location = "Lab 4";
              } else {
                if ($a_inventory['mod_virtual']) {
                  $vuproduction++;
                } else {
                  $puproduction++;
                }
                $location = "Prod";
              }
            }
          }
        }
        print "\"" . $a_inventory['inv_name'] . "\",\"" . $system . "\",\"" . $location . "\",\"" . $a_inventory['grp_name'] . "\"\n";
      }
  
  
      if ($locesx) {
        if ($a_inventory['mod_virtual']) {
          $vetotalloc++;
        } else {
          $petotalloc++;
        }
        if ($a_inventory['loc_name'] == 'Intrado CIL Data Center - Longmont') {
          if ($a_inventory['mod_virtual']) {
            $vesupport++;
          } else {
            $pesupport++;
          }
          $location = "CIL";
        } else {
          if ($a_inventory['loc_name'] == 'Intrado SQA Data Center - Longmont') {
            if ($a_inventory['mod_virtual']) {
              $vesqa++;
            } else {
              $pesqa++;
            }
            $location = "SQA";
          } else {
            if ($a_inventory['loc_name'] == 'Intrado Corp Dev Data Center - Longmont') {
              if ($a_inventory['mod_virtual']) {
                $vedevelopment++;
              } else {
                $pedevelopment++;
              }
              $location = "Dev";
            } else {
              if ($a_inventory['loc_name'] == 'Intrado Lab 4 Data Center - Longmont') {
                if ($a_inventory['mod_virtual']) {
                  $velab4++;
                } else {
                  $pelab4++;
                }
                $location = "Lab 4";
              } else {
                if ($a_inventory['mod_virtual']) {
                  $veproduction++;
                } else {
                  $peproduction++;
                }
                $location = "Prod";
              }
            }
          }
        }
        print "\"" . $a_inventory['inv_name'] . "\",\"" . $system . "\",\"" . $location . "\",\"" . $a_inventory['grp_name'] . "\"\n";
      }


      if ($locwindows) {
        if ($a_inventory['mod_virtual']) {
          $vwtotalloc++;
        } else {
          $pwtotalloc++;
        }
        if ($a_inventory['loc_name'] == 'Intrado CIL Data Center - Longmont') {
          if ($a_inventory['mod_virtual']) {
            $vwsupport++;
          } else {
            $pwsupport++;
          }
          $location = "CIL";
        } else {
          if ($a_inventory['loc_name'] == 'Intrado SQA Data Center - Longmont') {
            if ($a_inventory['mod_virtual']) {
              $vwsqa++;
            } else {
              $pwsqa++;
            }
            $location = "SQA";
          } else {
            if ($a_inventory['loc_name'] == 'Intrado Corp Dev Data Center - Longmont') {
              if ($a_inventory['mod_virtual']) {
                $vwdevelopment++;
              } else {
                $pwdevelopment++;
              }
              $location = "Dev";
            } else {
              if ($a_inventory['loc_name'] == 'Intrado Lab 4 Data Center - Longmont') {
                if ($a_inventory['mod_virtual']) {
                  $vwlab4++;
                } else {
                  $pwlab4++;
                }
                $location = "Lab 4";
              } else {
                if ($a_inventory['mod_virtual']) {
                  $vwproduction++;
                } else {
                  $pwproduction++;
                }
                $location = "Prod";
              }
            }
          }
        }
        print "\"" . $a_inventory['inv_name'] . "\",\"" . $system . "\",\"" . $location . "\",\"" . $a_inventory['grp_name'] . "\"\n";
      }
    }
  }

  $headers  = "From: System Count <root@incojs01.scc911.com>\r\n";
  $headers .= "CC: " . $Sitedev . "\r\n";

  if ($debug == 'no') {
    $email = "ed.kenner@intrado.com";
  } else {
    $email = "carl.schelin@intrado.com";
  }

  $body  = "Total number of Unix servers by OS: " . $ptotalunix . "/" . $vtotalunix . " (" . ($ptotalunix + $vtotalunix) . ")\n";
  $body .= " - Linux - "                     . $plinux     . "/" . $vlinux     . " (" . ($plinux     + $vlinux)     . ")\n";
  $body .= " -- Red Hat - "                  . $predhat    . "/" . $vredhat    . " (" . ($predhat    + $vredhat)    . ")\n";
  $body .= " -- Centos - "                   . $pcentos    . "/" . $vcentos    . " (" . ($pcentos    + $vcentos)    . ")\n";
  $body .= " -- Debian - "                   . $pdebian    . "/" . $vdebian    . " (" . ($pdebian    + $vdebian)    . ")\n";
  $body .= " -- Oracle Unbreakable Linux - " . $poracle    . "/" . $voracle    . " (" . ($poracle    + $voracle)    . ")\n";
  $body .= " -- SUSE - "                     . $psuse      . "/" . $vsuse      . " (" . ($psuse      + $vsuse)      . ")\n";
  $body .= " -- Fedora - "                   . $pfedora    . "/" . $vfedora    . " (" . ($pfedora    + $vfedora)    . ")\n";
  $body .= " -- Ubuntu - "                   . $pubuntu    . "/" . $vubuntu    . " (" . ($pubuntu    + $vubuntu)    . ")\n";
  $body .= " -- Other Linux - "              . $pother     . "/" . $vother     . " (" . ($pother     + $vother)     . ")\n";
  $body .= " - HP-UX - "                     . $phpux      . "/" . $vhpux      . " (" . ($phpux      + $vhpux)      . ")\n";
  $body .= " - Solaris - "                   . $psolaris   . "/" . $vsolaris   . " (" . ($psolaris   + $vsolaris)   . ")\n\n";

  $body .= "Total number of Unix servers by Location: " . $putotalloc     . "/" . $vutotalloc    . " (" . ($putotalloc    + $vutotalloc)    . ")\n";
  $body .= " - Production - "                           . $puproduction   . "/" . $vuproduction  . " (" . ($puproduction  + $vuproduction)  . ")\n";
  $body .= " - Production Support - "                   . $pusupport      . "/" . $vusupport     . " (" . ($pusupport     + $vusupport)     . ")\n";
  $body .= " - SQA - "                                  . $pusqa          . "/" . $vusqa         . " (" . ($pusqa         + $vusqa)         . ")\n";
  $body .= " - Development - "                          . $pudevelopment  . "/" . $vudevelopment . " (" . ($pudevelopment + $vudevelopment) . ")\n";
  $body .= " - Lab 4 - "                                . $pulab4         . "/" . $vulab4        . " (" . ($pulab4        + $vulab4)        . ")\n\n";

  $body .= "Total number of ESX/VMWare servers: " . $ptotalesx . "/" . $vtotalesx . " (" . ($ptotalesx + $vtotalesx) . ")\n\n";

  $body .= "Total number of ESX/VMWare servers by Location: " . $petotalloc     . "/" . $vetotalloc    . " (" . ($petotalloc    + $vetotalloc)    . ")\n";
  $body .= " - Production - "                                 . $peproduction   . "/" . $veproduction  . " (" . ($peproduction  + $veproduction)  . ")\n";
  $body .= " - Production Support - "                         . $pesupport      . "/" . $vesupport     . " (" . ($pesupport     + $vesupport)     . ")\n";
  $body .= " - SQA - "                                        . $pesqa          . "/" . $vesqa         . " (" . ($pesqa         + $vesqa)         . ")\n";
  $body .= " - Development - "                                . $pedevelopment  . "/" . $vedevelopment . " (" . ($pedevelopment + $vedevelopment) . ")\n";
  $body .= " - Lab 4 - "                                      . $pelab4         . "/" . $velab4        . " (" . ($pelab4        + $velab4)        . ")\n\n";

  $body .= "Total number of Windows servers: " . $ptotalwindows . "/" . $vtotalwindows . " (" . ($ptotalwindows + $vtotalwindows) . ")\n\n";

  $body .= "Total number of Windows servers by Location: " . $pwtotalloc     . "/" . $vwtotalloc    . " (" . ($pwtotalloc    + $vwtotalloc)    . ")\n";
  $body .= " - Production - "                              . $pwproduction   . "/" . $vwproduction  . " (" . ($pwproduction  + $vwproduction)  . ")\n";
  $body .= " - Production Support - "                      . $pwsupport      . "/" . $vwsupport     . " (" . ($pwsupport     + $vwsupport)     . ")\n";
  $body .= " - SQA - "                                     . $pwsqa          . "/" . $vwsqa         . " (" . ($pwsqa         + $vwsqa)         . ")\n";
  $body .= " - Development - "                             . $pwdevelopment  . "/" . $vwdevelopment . " (" . ($pwdevelopment + $vwdevelopment) . ")\n";
  $body .= " - Lab 4 - "                                   . $pwlab4         . "/" . $vwlab4        . " (" . ($pwlab4        + $vwlab4)        . ")\n\n";

  $body .= "Counts above are Physical/Virtual (Total)\n";
  $body .= "This excludes systems owned by miscellaneous (TechOps) as they're likely imports from SecuirtyCenter and reference workstations.\n\n";

  mail($email, "Weekly System Count", $body, $headers);

?>
