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

  if ($debug == 'no') {
    $email = "ed.kenner@intrado.com";
  } else {
    $email = "carl.schelin@intrado.com";
  }

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";  # green
  $color[2] = "yellow";   # yellow
  $color[3] = "#fa8072";  # red

  $bgcolor = $color[0];

  $headers  = "From: System Count <root@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "CC: " . $Sitedev . "\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $output  = "<html>\n";
  $output .= "<body>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Unix Servers by Operating System</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $plinux             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vlinux             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($plinux + $vlinux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Red Hat"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $predhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vredhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($predhat + $vredhat) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "CentOS"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pcentos             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vcentos             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pcentos + $vcentos) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Debian"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pdebian             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vdebian             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pdebian + $vdebian) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Oracle Unbreakable Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $poracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . $voracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($poracle + $voracle) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SUSE"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $psuse             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vsuse             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($psuse + $vsuse) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Fedora"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pfedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vfedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pfedora + $vfedora) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Ubuntu"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pubuntu + $vubuntu) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Other Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pother             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vother             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pother + $vother) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "HP-UX"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $phpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vhpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($phpux + $vhpux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Solaris"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $psolaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vsolaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($psolaris + $vsolaris) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>"                  . "<strong>Total Unix Servers</strong>"           . "</td>\n";
  $output .= "  <td align=\"center\">" . "<strong>" . $ptotalunix . "</strong>"                 . "</td>\n";
  $output .= "  <td align=\"center\">" . "<strong>" . $vtotalunix . "</strong>"                 . "</td>\n";
  $output .= "  <td align=\"center\">" . "<strong>" . ($ptotalunix + $vtotalunix) . "</strong>" . "</td>\n";
  $output .= "</tr>\n";

  $output .= "</table>\n";

  $output .= "<br>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Unix Servers by Operating System and Location</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Production Servers</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>"                  . "Linux"  . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_linux + $vup_linux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Red Hat"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_redhat + $vup_redhat) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "CentOS"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_centos + $vup_centos) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Debian"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_debian + $vup_debian) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Oracle Unbreakable Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_oracle + $vup_oracle) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SUSE"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_suse + $vup_suse) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Fedora"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_fedora + $vup_fedora) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Ubuntu"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_ubuntu + $vup_ubuntu) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Other Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_other + $vup_other) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "HP-UX"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_hpux + $vup_hpux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Solaris"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pup_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vup_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pup_solaris + $vup_solaris) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total Production Unix Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puproduction             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuproduction             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puproduction + $vuproduction) . "</td>\n";
  $output .= "</tr>\n";


  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Production Support Servers</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>"                  . "Linux"  . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_linux + $vus_linux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Red Hat"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_redhat + $vus_redhat) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "CentOS"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_centos + $vus_centos) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Debian"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_debian + $vus_debian) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Oracle Unbreakable Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_oracle + $vus_oracle) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SUSE"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_suse + $vus_suse) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Fedora"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_fedora + $vus_fedora) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Ubuntu"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_ubuntu + $vus_ubuntu) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Other Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_other + $vus_other) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "HP-UX"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_hpux + $vus_hpux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Solaris"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pus_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vus_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pus_solaris + $vus_solaris) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total Production Support Unix Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pusupport             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vusupport             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pusupport + $vusupport) . "</td>\n";
  $output .= "</tr>\n";


  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">SQA Servers</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>"                  . "Linux"  . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_linux + $vuq_linux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Red Hat"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_redhat + $vuq_redhat) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "CentOS"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_centos + $vuq_centos) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Debian"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_debian + $vuq_debian) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Oracle Unbreakable Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_oracle + $vuq_oracle) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SUSE"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_suse + $vuq_suse) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Fedora"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_fedora + $vuq_fedora) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Ubuntu"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_ubuntu + $vuq_ubuntu) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Other Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_other + $vuq_other) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "HP-UX"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_hpux + $vuq_hpux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Solaris"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $puq_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vuq_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($puq_solaris + $vuq_solaris) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total SQA Unix Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pusqa             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vusqa             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pusqa + $vusqa) . "</td>\n";
  $output .= "</tr>\n";


  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Development Servers</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>"                  . "Linux"  . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_linux + $vud_linux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Red Hat"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_redhat + $vud_redhat) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "CentOS"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_centos + $vud_centos) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Debian"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_debian + $vud_debian) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Oracle Unbreakable Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_oracle + $vud_oracle) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SUSE"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_suse + $vud_suse) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Fedora"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_fedora + $vud_fedora) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Ubuntu"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_ubuntu + $vud_ubuntu) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Other Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_other + $vud_other) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "HP-UX"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_hpux + $vud_hpux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Solaris"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pud_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vud_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pud_solaris + $vud_solaris) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total Development Unix Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pudevelopment             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vudevelopment             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pudevelopment + $vudevelopment) . "</td>\n";
  $output .= "</tr>\n";


  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Lab 4 Servers</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>"                  . "Linux"  . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_linux                . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_linux + $vu4_linux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Red Hat"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_redhat             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_redhat + $vu4_redhat) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "CentOS"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_centos             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_centos + $vu4_centos) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Debian"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_debian             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_debian + $vu4_debian) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Oracle Unbreakable Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_oracle             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_oracle + $vu4_oracle) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SUSE"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_suse             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_suse + $vu4_suse) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Fedora"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_fedora             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_fedora + $vu4_fedora) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Ubuntu"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_ubuntu             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_ubuntu + $vu4_ubuntu) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Other Linux"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_other             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_other + $vu4_other) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "HP-UX"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_hpux             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_hpux + $vu4_hpux) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Solaris"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pu4_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vu4_solaris             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pu4_solaris + $vu4_solaris) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total Lab 4 Unix Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pulab4             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vulab4             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pulab4 + $vulab4) . "</td>\n";
  $output .= "</tr>\n";


  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total Unix Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\"><strong>" . $putotalloc             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . $vutotalloc             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . ($putotalloc + $vutotalloc) . "</strong></td>\n";
  $output .= "</tr>\n";

  $output .= "</table>\n";

  $output .= "</br>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">ESX/VMWare Servers</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total ESX/VMWare Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\"><strong>" . $ptotalesx             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . $vtotalesx             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . ($ptotalesx + $vtotalesx) . "</strong></td>\n";
  $output .= "</tr>\n";

  $output .= "</table>\n";

  $output .= "</br>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">ESX/VMWare Servers by Location</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Production"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $peproduction             . "</td>\n";
  $output .= "  <td align=\"center\">" . $veproduction             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($peproduction + $veproduction) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Production Support"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pesupport             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vesupport             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pesupport + $vesupport) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SQA"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pesqa             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vesqa             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pesqa + $vesqa) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Development"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pedevelopment             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vedevelopment             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pedevelopment + $vedevelopment) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Lab"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pelab4             . "</td>\n";
  $output .= "  <td align=\"center\">" . $velab4             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pelab4 + $velab4) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total ESX/VMWare Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\"><strong>" . $petotalloc             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . $vetotalloc             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . ($petotalloc + $vetotalloc) . "</strong></td>\n";
  $output .= "</tr>\n";

  $output .= "</table>\n";

  $output .= "</br>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Windows Servers</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total Windows Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\"><strong>" . $ptotalwindows             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . $vtotalwindows             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . ($ptotalwindows + $vtotalwindows) . "</strong></td>\n";
  $output .= "</tr>\n";

  $output .= "</table>\n";

  $output .= "</br>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Windows Servers by Location</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>Operating System</th>\n";
  $output .= "  <th>Physical</th>\n";
  $output .= "  <th>Virtual</th>\n";
  $output .= "  <th>Total</th>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Production"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pwproduction             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vwproduction             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pwproduction + $vwproduction) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Product Support"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pwsupport             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vwsupport             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pwsupport + $vwsupport) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "SQA"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pwsqa             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vwsqa             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pwsqa + $vwsqa) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Development"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pwdevelopment             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vwdevelopment             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pwdevelopment + $vwdevelopment) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "Lab 4"             . "</td>\n";
  $output .= "  <td align=\"center\">" . $pwlab4             . "</td>\n";
  $output .= "  <td align=\"center\">" . $vwlab4             . "</td>\n";
  $output .= "  <td align=\"center\">" . ($pwlab4 + $vwlab4) . "</td>\n";
  $output .= "</tr>\n";

  $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <td>" . "<strong>Total Windows Servers</strong>"             . "</td>\n";
  $output .= "  <td align=\"center\"><strong>" . $pwtotalloc             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . $vwtotalloc             . "</strong></td>\n";
  $output .= "  <td align=\"center\"><strong>" . ($pwtotalloc + $vwtotalloc) . "</strong></td>\n";
  $output .= "</tr>\n";

  $output .= "</table>\n";

  $output .= "<p>This excludes systems owned by miscellaneous (TechOps) as they're likely imports from SecurityCenter and reference workstations.</p>\n";

  $output .= "<p>This mail box is not monitored, please do not reply.</p>\n";

  $output .= "</body>\n";
  $output .= "</html>\n";


  mail($email, "Weekly System Count", $output, $headers);

?>
