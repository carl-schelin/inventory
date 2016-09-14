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
# need location breakdown as well
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

# more locations
# physical/virtual unix production - linux version
  $pup_centos = 0;
  $vup_centos = 0;
  $pup_debian = 0;
  $vup_debian = 0;
  $pup_oracle = 0; # oracle enterprise linux, oracle linux
  $vup_oracle = 0; # oracle enterprise linux, oracle linux
  $pup_redhat = 0;
  $vup_redhat = 0;
  $pup_suse = 0;
  $vup_suse = 0;
  $pup_fedora = 0;
  $vup_fedora = 0;
  $pup_ubuntu = 0;
  $vup_ubuntu = 0;
  $pup_other = 0;  # other linux
  $vup_other = 0;  # other linux
  $pup_hpux = 0;
  $vup_hpux = 0;
  $pup_solaris = 0;
  $vup_solaris = 0;
# physical/virtual unix production support - linux version
  $pus_centos = 0;
  $vus_centos = 0;
  $pus_debian = 0;
  $vus_debian = 0;
  $pus_oracle = 0; # oracle enterprise linux, oracle linux
  $vus_oracle = 0; # oracle enterprise linux, oracle linux
  $pus_redhat = 0;
  $vus_redhat = 0;
  $pus_suse = 0;
  $vus_suse = 0;
  $pus_fedora = 0;
  $vus_fedora = 0;
  $pus_ubuntu = 0;
  $vus_ubuntu = 0;
  $pus_other = 0;  # other linux
  $vus_other = 0;  # other linux
  $pus_hpux = 0;
  $vus_hpux = 0;
  $pus_solaris = 0;
  $vus_solaris = 0;
# physical/virtual unix sqa - linux version
  $puq_centos = 0;
  $vuq_centos = 0;
  $puq_debian = 0;
  $vuq_debian = 0;
  $puq_oracle = 0; # oracle enterprise linux, oracle linux
  $vuq_oracle = 0; # oracle enterprise linux, oracle linux
  $puq_redhat = 0;
  $vuq_redhat = 0;
  $puq_suse = 0;
  $vuq_suse = 0;
  $puq_fedora = 0;
  $vuq_fedora = 0;
  $puq_ubuntu = 0;
  $vuq_ubuntu = 0;
  $puq_other = 0;  # other linux
  $vuq_other = 0;  # other linux
  $puq_hpux = 0;
  $vuq_hpux = 0;
  $puq_solaris = 0;
  $vuq_solaris = 0;
# physical/virtual unix development - linux version
  $pud_centos = 0;
  $vud_centos = 0;
  $pud_debian = 0;
  $vud_debian = 0;
  $pud_oracle = 0; # oracle enterprise linux, oracle linux
  $vud_oracle = 0; # oracle enterprise linux, oracle linux
  $pud_redhat = 0;
  $vud_redhat = 0;
  $pud_suse = 0;
  $vud_suse = 0;
  $pud_fedora = 0;
  $vud_fedora = 0;
  $pud_ubuntu = 0;
  $vud_ubuntu = 0;
  $pud_other = 0;  # other linux
  $vud_other = 0;  # other linux
  $pud_hpux = 0;
  $vud_hpux = 0;
  $pud_solaris = 0;
  $vud_solaris = 0;
# physical/virtual unix lab4 - linux version
  $pu4_centos = 0;
  $vu4_centos = 0;
  $pu4_debian = 0;
  $vu4_debian = 0;
  $pu4_oracle = 0; # oracle enterprise linux, oracle linux
  $vu4_oracle = 0; # oracle enterprise linux, oracle linux
  $pu4_redhat = 0;
  $vu4_redhat = 0;
  $pu4_suse = 0;
  $vu4_suse = 0;
  $pu4_fedora = 0;
  $vu4_fedora = 0;
  $pu4_ubuntu = 0;
  $vu4_ubuntu = 0;
  $pu4_other = 0;  # other linux
  $vu4_other = 0;  # other linux
  $pu4_hpux = 0;
  $vu4_hpux = 0;
  $pu4_solaris = 0;
  $vu4_solaris = 0;

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
          if ($os == 'Linux') {
            if ($a_inventory['mod_virtual']) {
              $vus_linux++;
            } else {
              $pus_linux++;
            }

            if (preg_match("/centos/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_centos++;
              } else {
                $pusupport++;
                $pus_centos++;
              }
            }
            if (preg_match("/debian/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_debian++;
              } else {
                $pusupport++;
                $pus_debian++;
              }
            }
            if (preg_match("/oracle.*linux/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_oracle++;
              } else {
                $pusupport++;
                $pus_oracle++;
              }
            }
            if (preg_match("/red hat/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_redhat++;
              } else {
                $pusupport++;
                $pus_redhat++;
              }
            }
            if (preg_match("/suse/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_suse++;
              } else {
                $pusupport++;
                $pus_suse++;
              }
            }
            if (preg_match("/fedora/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_fedora++;
              } else {
                $pusupport++;
                $pus_fedora++;
              }
            }
            if (preg_match("/ubuntu/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_ubuntu++;
              } else {
                $pusupport++;
                $pus_ubuntu++;
              }
            }
            if (preg_match("/other.*linux/i", $a_software['sw_software'])) {
              if ($a_inventory['mod_virtual']) {
                $vusupport++;
                $vus_other++;
              } else {
                $pusupport++;
                $pus_other++;
              }
            }
          }
          if ($os == 'HP-UX') {
            if ($a_inventory['mod_virtual']) {
              $vusupport++;
              $vus_hpux++;
            } else {
              $pusupport++;
              $pus_hpux++;
            }
          }
          if ($os == 'SunOS') {
            if ($a_inventory['mod_virtual']) {
              $vusupport++;
              $vus_solaris++;
            } else {
              $pusupport++;
              $pus_solaris++;
            }
          }

          $location = "CIL";
        } else {
          if ($a_inventory['loc_name'] == 'Intrado SQA Data Center - Longmont') {
            if ($os == 'Linux') {
              if ($a_inventory['mod_virtual']) {
                $vuq_linux++;
              } else {
                $puq_linux++;
              }

              if (preg_match("/centos/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_centos++;
                } else {
                  $pusqa++;
                  $puq_centos++;
                }
              }
              if (preg_match("/debian/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_debian++;
                } else {
                  $pusqa++;
                  $puq_debian++;
                }
              }
              if (preg_match("/oracle.*linux/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_oracle++;
                } else {
                  $pusqa++;
                  $puq_oracle++;
                }
              }
              if (preg_match("/red hat/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_redhat++;
                } else {
                  $pusqa++;
                  $puq_redhat++;
                }
              }
              if (preg_match("/suse/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_suse++;
                } else {
                  $pusqa++;
                  $puq_suse++;
                }
              }
              if (preg_match("/fedora/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_fedora++;
                } else {
                  $pusqa++;
                  $puq_fedora++;
                }
              }
              if (preg_match("/ubuntu/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_ubuntu++;
                } else {
                  $pusqa++;
                  $puq_ubuntu++;
                }
              }
              if (preg_match("/other.*linux/i", $a_software['sw_software'])) {
                if ($a_inventory['mod_virtual']) {
                  $vusqa++;
                  $vuq_other++;
                } else {
                  $pusqa++;
                  $puq_other++;
                }
              }
            }
            if ($os == 'HP-UX') {
              if ($a_inventory['mod_virtual']) {
                $vusqa++;
                $vuq_hpux++;
              } else {
                $pusqa++;
                $puq_hpux++;
              }
            }
            if ($os == 'SunOS') {
              if ($a_inventory['mod_virtual']) {
                $vusqa++;
                $vuq_solaris++;
              } else {
                $pusqa++;
                $puq_solaris++;
              }
            }

            $location = "SQA";
          } else {
            if ($a_inventory['loc_name'] == 'Intrado Corp Dev Data Center - Longmont') {
              if ($os == 'Linux') {
                if ($a_inventory['mod_virtual']) {
                  $vud_linux++;
                } else {
                  $pud_linux++;
                }

                if (preg_match("/centos/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_centos++;
                  } else {
                    $pudevelopment++;
                    $pud_centos++;
                  }
                }
                if (preg_match("/debian/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_debian++;
                  } else {
                    $pudevelopment++;
                    $pud_debian++;
                  }
                }
                if (preg_match("/oracle.*linux/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_oracle++;
                  } else {
                    $pudevelopment++;
                    $pud_oracle++;
                  }
                }
                if (preg_match("/red hat/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_redhat++;
                  } else {
                    $pudevelopment++;
                    $pud_redhat++;
                  }
                }
                if (preg_match("/suse/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_suse++;
                  } else {
                    $pudevelopment++;
                    $pud_suse++;
                  }
                }
                if (preg_match("/fedora/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_fedora++;
                  } else {
                    $pudevelopment++;
                    $pud_fedora++;
                  }
                }
                if (preg_match("/ubuntu/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_ubuntu++;
                  } else {
                    $pudevelopment++;
                    $pud_ubuntu++;
                  }
                }
                if (preg_match("/other.*linux/i", $a_software['sw_software'])) {
                  if ($a_inventory['mod_virtual']) {
                    $vudevelopment++;
                    $vud_other++;
                  } else {
                    $pudevelopment++;
                    $pud_other++;
                  }
                }
              }
              if ($os == 'HP-UX') {
                if ($a_inventory['mod_virtual']) {
                  $vudevelopment++;
                  $vud_hpux++;
                } else {
                  $pudevelopment++;
                  $pud_hpux++;
                }
              }
              if ($os == 'SunOS') {
                if ($a_inventory['mod_virtual']) {
                  $vudevelopment++;
                  $vud_solaris++;
                } else {
                  $pudevelopment++;
                  $pud_solaris++;
                }
              }

              $location = "Dev";
            } else {
              if ($a_inventory['loc_name'] == 'Intrado Lab 4 Data Center - Longmont') {
                if ($os == 'Linux') {
                  if ($a_inventory['mod_virtual']) {
                    $vu4_linux++;
                  } else {
                    $pu4_linux++;
                  }

                  if (preg_match("/centos/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_centos++;
                    } else {
                      $pulab4++;
                      $pu4_centos++;
                    }
                  }
                  if (preg_match("/debian/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_debian++;
                    } else {
                      $pulab4++;
                      $pu4_debian++;
                    }
                  }
                  if (preg_match("/oracle.*linux/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_oracle++;
                    } else {
                      $pulab4++;
                      $pu4_oracle++;
                    }
                  }
                  if (preg_match("/red hat/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_redhat++;
                    } else {
                      $pulab4++;
                      $pu4_redhat++;
                    }
                  }
                  if (preg_match("/suse/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_suse++;
                    } else {
                      $pulab4++;
                      $pu4_suse++;
                    }
                  }
                  if (preg_match("/fedora/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_fedora++;
                    } else {
                      $pulab4++;
                      $pu4_fedora++;
                    }
                  }
                  if (preg_match("/ubuntu/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_ubuntu++;
                    } else {
                      $pulab4++;
                      $pu4_ubuntu++;
                    }
                  }
                  if (preg_match("/other.*linux/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vulab4++;
                      $vu4_other++;
                    } else {
                      $pulab4++;
                      $pu4_other++;
                    }
                  }
                }
                if ($os == 'HP-UX') {
                  if ($a_inventory['mod_virtual']) {
                    $vulab4++;
                    $vu4_hpux++;
                  } else {
                    $pulab4++;
                    $pu4_hpux++;
                  }
                }
                if ($os == 'SunOS') {
                  if ($a_inventory['mod_virtual']) {
                    $vulab4++;
                    $vu4_solaris++;
                  } else {
                    $pulab4++;
                    $pu4_solaris++;
                  }
                }
                $location = "Lab 4";
              } else {
                if ($os == 'Linux') {
                  if ($a_inventory['mod_virtual']) {
                    $vup_linux++;
                  } else {
                    $pup_linux++;
                  }

                  if (preg_match("/centos/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_centos++;
                    } else {
                      $puproduction++;
                      $pup_centos++;
                    }
                  }
                  if (preg_match("/debian/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_debian++;
                    } else {
                      $puproduction++;
                      $pup_debian++;
                    }
                  }
                  if (preg_match("/oracle.*linux/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_oracle++;
                    } else {
                      $puproduction++;
                      $pup_oracle++;
                    }
                  }
                  if (preg_match("/red hat/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_redhat++;
                    } else {
                      $puproduction++;
                      $pup_redhat++;
                    }
                  }
                  if (preg_match("/suse/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_suse++;
                    } else {
                      $puproduction++;
                      $pup_suse++;
                    }
                  }
                  if (preg_match("/fedora/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_fedora++;
                    } else {
                      $puproduction++;
                      $pup_fedora++;
                    }
                  }
                  if (preg_match("/ubuntu/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_ubuntu++;
                    } else {
                      $puproduction++;
                      $pup_ubuntu++;
                    }
                  }
                  if (preg_match("/other.*linux/i", $a_software['sw_software'])) {
                    if ($a_inventory['mod_virtual']) {
                      $vuproduction++;
                      $vup_other++;
                    } else {
                      $puproduction++;
                      $pup_other++;
                    }
                  }
                }
                if ($os == 'HP-UX') {
                  if ($a_inventory['mod_virtual']) {
                    $vuproduction++;
                    $vup_hpux++;
                  } else {
                    $puproduction++;
                    $pup_hpux++;
                  }
                }
                if ($os == 'SunOS') {
                  if ($a_inventory['mod_virtual']) {
                    $vuproduction++;
                    $vup_solaris++;
                  } else {
                    $puproduction++;
                    $pup_solaris++;
                  }
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
  $body .= " -- Linux - "                               . $pup_linux      . "/" . $vup_linux     . " (" . ($pup_linux     + $vup_linux)     . ")\n";
  $body .= " --- Red Hat - "                            . $pup_redhat     . "/" . $vup_redhat    . " (" . ($pup_redhat    + $vup_redhat)    . ")\n";
  $body .= " --- Centos - "                             . $pup_centos     . "/" . $vup_centos    . " (" . ($pup_centos    + $vup_centos)    . ")\n";
  $body .= " --- Debian - "                             . $pup_debian     . "/" . $vup_debian    . " (" . ($pup_debian    + $vup_debian)    . ")\n";
  $body .= " --- Oracle Unbreakable Linux - "           . $pup_oracle     . "/" . $vup_oracle    . " (" . ($pup_oracle    + $vup_oracle)    . ")\n";
  $body .= " --- SUSE - "                               . $pup_suse       . "/" . $vup_suse      . " (" . ($pup_suse      + $vup_suse)      . ")\n";
  $body .= " --- Fedora - "                             . $pup_fedora     . "/" . $vup_fedora    . " (" . ($pup_fedora    + $vup_fedora)    . ")\n";
  $body .= " --- Ubuntu - "                             . $pup_ubuntu     . "/" . $vup_ubuntu    . " (" . ($pup_ubuntu    + $vup_ubuntu)    . ")\n";
  $body .= " --- Other Linux - "                        . $pup_other      . "/" . $vup_other     . " (" . ($pup_other     + $vup_other)     . ")\n";
  $body .= " -- HP-UX - "                               . $pup_hpux       . "/" . $vup_hpux      . " (" . ($pup_hpux      + $vup_hpux)      . ")\n";
  $body .= " -- Solaris - "                             . $pup_solaris    . "/" . $vup_solaris   . " (" . ($pup_solaris   + $vup_solaris)   . ")\n";
  $body .= " - Production Support - "                   . $pusupport      . "/" . $vusupport     . " (" . ($pusupport     + $vusupport)     . ")\n";
  $body .= " -- Linux - "                               . $pus_linux      . "/" . $vus_linux     . " (" . ($pus_linux     + $vus_linux)     . ")\n";
  $body .= " --- Red Hat - "                            . $pus_redhat     . "/" . $vus_redhat    . " (" . ($pus_redhat    + $vus_redhat)    . ")\n";
  $body .= " --- Centos - "                             . $pus_centos     . "/" . $vus_centos    . " (" . ($pus_centos    + $vus_centos)    . ")\n";
  $body .= " --- Debian - "                             . $pus_debian     . "/" . $vus_debian    . " (" . ($pus_debian    + $vus_debian)    . ")\n";
  $body .= " --- Oracle Unbreakable Linux - "           . $pus_oracle     . "/" . $vus_oracle    . " (" . ($pus_oracle    + $vus_oracle)    . ")\n";
  $body .= " --- SUSE - "                               . $pus_suse       . "/" . $vus_suse      . " (" . ($pus_suse      + $vus_suse)      . ")\n";
  $body .= " --- Fedora - "                             . $pus_fedora     . "/" . $vus_fedora    . " (" . ($pus_fedora    + $vus_fedora)    . ")\n";
  $body .= " --- Ubuntu - "                             . $pus_ubuntu     . "/" . $vus_ubuntu    . " (" . ($pus_ubuntu    + $vus_ubuntu)    . ")\n";
  $body .= " --- Other Linux - "                        . $pus_other      . "/" . $vus_other     . " (" . ($pus_other     + $vus_other)     . ")\n";
  $body .= " -- HP-UX - "                               . $pus_hpux       . "/" . $vus_hpux      . " (" . ($pus_hpux      + $vus_hpux)      . ")\n";
  $body .= " -- Solaris - "                             . $pus_solaris    . "/" . $vus_solaris   . " (" . ($pus_solaris   + $vus_solaris)   . ")\n";
  $body .= " - SQA - "                                  . $pusqa          . "/" . $vusqa         . " (" . ($pusqa         + $vusqa)         . ")\n";
  $body .= " -- Linux - "                               . $puq_linux      . "/" . $vuq_linux     . " (" . ($puq_linux     + $vuq_linux)     . ")\n";
  $body .= " --- Red Hat - "                            . $puq_redhat     . "/" . $vuq_redhat    . " (" . ($puq_redhat    + $vuq_redhat)    . ")\n";
  $body .= " --- Centos - "                             . $puq_centos     . "/" . $vuq_centos    . " (" . ($puq_centos    + $vuq_centos)    . ")\n";
  $body .= " --- Debian - "                             . $puq_debian     . "/" . $vuq_debian    . " (" . ($puq_debian    + $vuq_debian)    . ")\n";
  $body .= " --- Oracle Unbreakable Linux - "           . $puq_oracle     . "/" . $vuq_oracle    . " (" . ($puq_oracle    + $vuq_oracle)    . ")\n";
  $body .= " --- SUSE - "                               . $puq_suse       . "/" . $vuq_suse      . " (" . ($puq_suse      + $vuq_suse)      . ")\n";
  $body .= " --- Fedora - "                             . $puq_fedora     . "/" . $vuq_fedora    . " (" . ($puq_fedora    + $vuq_fedora)    . ")\n";
  $body .= " --- Ubuntu - "                             . $puq_ubuntu     . "/" . $vuq_ubuntu    . " (" . ($puq_ubuntu    + $vuq_ubuntu)    . ")\n";
  $body .= " --- Other Linux - "                        . $puq_other      . "/" . $vuq_other     . " (" . ($puq_other     + $vuq_other)     . ")\n";
  $body .= " -- HP-UX - "                               . $puq_hpux       . "/" . $vuq_hpux      . " (" . ($puq_hpux      + $vuq_hpux)      . ")\n";
  $body .= " -- Solaris - "                             . $puq_solaris    . "/" . $vuq_solaris   . " (" . ($puq_solaris   + $vuq_solaris)   . ")\n";
  $body .= " - Development - "                          . $pudevelopment  . "/" . $vudevelopment . " (" . ($pudevelopment + $vudevelopment) . ")\n";
  $body .= " -- Linux - "                               . $pud_linux      . "/" . $vud_linux     . " (" . ($pud_linux     + $vud_linux)     . ")\n";
  $body .= " --- Red Hat - "                            . $pud_redhat     . "/" . $vud_redhat    . " (" . ($pud_redhat    + $vud_redhat)    . ")\n";
  $body .= " --- Centos - "                             . $pud_centos     . "/" . $vud_centos    . " (" . ($pud_centos    + $vud_centos)    . ")\n";
  $body .= " --- Debian - "                             . $pud_debian     . "/" . $vud_debian    . " (" . ($pud_debian    + $vud_debian)    . ")\n";
  $body .= " --- Oracle Unbreakable Linux - "           . $pud_oracle     . "/" . $vud_oracle    . " (" . ($pud_oracle    + $vud_oracle)    . ")\n";
  $body .= " --- SUSE - "                               . $pud_suse       . "/" . $vud_suse      . " (" . ($pud_suse      + $vud_suse)      . ")\n";
  $body .= " --- Fedora - "                             . $pud_fedora     . "/" . $vud_fedora    . " (" . ($pud_fedora    + $vud_fedora)    . ")\n";
  $body .= " --- Ubuntu - "                             . $pud_ubuntu     . "/" . $vud_ubuntu    . " (" . ($pud_ubuntu    + $vud_ubuntu)    . ")\n";
  $body .= " --- Other Linux - "                        . $pud_other      . "/" . $vud_other     . " (" . ($pud_other     + $vud_other)     . ")\n";
  $body .= " -- HP-UX - "                               . $pud_hpux       . "/" . $vud_hpux      . " (" . ($pud_hpux      + $vud_hpux)      . ")\n";
  $body .= " -- Solaris - "                             . $pud_solaris    . "/" . $vud_solaris   . " (" . ($pud_solaris   + $vud_solaris)   . ")\n";
  $body .= " - Lab 4 - "                                . $pulab4         . "/" . $vulab4        . " (" . ($pulab4        + $vulab4)        . ")\n";
  $body .= " -- Linux - "                               . $pu4_linux      . "/" . $vu4_linux     . " (" . ($pu4_linux     + $vu4_linux)     . ")\n";
  $body .= " --- Red Hat - "                            . $pu4_redhat     . "/" . $vu4_redhat    . " (" . ($pu4_redhat    + $vu4_redhat)    . ")\n";
  $body .= " --- Centos - "                             . $pu4_centos     . "/" . $vu4_centos    . " (" . ($pu4_centos    + $vu4_centos)    . ")\n";
  $body .= " --- Debian - "                             . $pu4_debian     . "/" . $vu4_debian    . " (" . ($pu4_debian    + $vu4_debian)    . ")\n";
  $body .= " --- Oracle Unbreakable Linux - "           . $pu4_oracle     . "/" . $vu4_oracle    . " (" . ($pu4_oracle    + $vu4_oracle)    . ")\n";
  $body .= " --- SUSE - "                               . $pu4_suse       . "/" . $vu4_suse      . " (" . ($pu4_suse      + $vu4_suse)      . ")\n";
  $body .= " --- Fedora - "                             . $pu4_fedora     . "/" . $vu4_fedora    . " (" . ($pu4_fedora    + $vu4_fedora)    . ")\n";
  $body .= " --- Ubuntu - "                             . $pu4_ubuntu     . "/" . $vu4_ubuntu    . " (" . ($pu4_ubuntu    + $vu4_ubuntu)    . ")\n";
  $body .= " --- Other Linux - "                        . $pu4_other      . "/" . $vu4_other     . " (" . ($pu4_other     + $vu4_other)     . ")\n";
  $body .= " -- HP-UX - "                               . $pu4_hpux       . "/" . $vu4_hpux      . " (" . ($pu4_hpux      + $vu4_hpux)      . ")\n";
  $body .= " -- Solaris - "                             . $pu4_solaris    . "/" . $vu4_solaris   . " (" . ($pu4_solaris   + $vu4_solaris)   . ")\n\n";

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
