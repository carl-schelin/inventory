<?php
# Script: initial.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "initial.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }
    $ticket = 'yes';
    $ticket = 'no';

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rsdp_server");

      $userid = $_SESSION['uid'];

      $q_string  = "select rsdp_requestor,rsdp_location,rsdp_product,rsdp_completion,rsdp_project,";
      $q_string .= "rsdp_platformspoc,rsdp_sanpoc,rsdp_networkpoc,rsdp_virtpoc,rsdp_dcpoc,rsdp_srpoc,rsdp_monitorpoc,";
      $q_string .= "rsdp_apppoc,rsdp_backuppoc,rsdp_platform,rsdp_application,rsdp_service,rsdp_vendor,rsdp_function,";
      $q_string .= "rsdp_processors,rsdp_memory,rsdp_ossize,rsdp_osmonitor,rsdp_appmonitor,rsdp_datapalette,rsdp_opnet,";
      $q_string .= "rsdp_newrelic,rsdp_centrify,rsdp_backup ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_id = " . $formVars['rsdp'];
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_server) > 0) {
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        $userid = $a_rsdp_server['rsdp_requestor'];


# work harder at getting index values because of the ordering of users.
        $sanpoc = 0;
        $count = 1;
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_SAN . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_sanpoc'] == $a_users['usr_id']) {
            $sanpoc = $count;
          }
          $count++;
        }
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_SAN . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_sanpoc'] == $a_users['usr_id']) {
            $sanpoc = $count;
          }
          $count++;
        }

        $networkpoc = 0;
        $count = 1;
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Networking . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_networkpoc'] == $a_users['usr_id']) {
            $networkpoc = $count;
          }
          $count++;
        }
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Networking . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_networkpoc'] == $a_users['usr_id']) {
            $networkpoc = $count;
          }
          $count++;
        }

        $virtpoc = 0;
        $count = 1;
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Virtualization . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_virtpoc'] == $a_users['usr_id']) {
            $virtpoc = $count;
          }
          $count++;
        }
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Virtualization . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_virtpoc'] == $a_users['usr_id']) {
            $virtpoc = $count;
          }
          $count++;
        }

        $dcpoc = 0;
        $count = 1;
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_DataCenter . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_dcpoc'] == $a_users['usr_id']) {
            $dcpoc = $count;
          }
          $count++;
        }
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_DataCenter . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_dcpoc'] == $a_users['usr_id']) {
            $dcpoc = $count;
          }
          $count++;
        }

        $monitorpoc = 0;
        $count = 1;
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Monitoring . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_monitorpoc'] == $a_users['usr_id']) {
            $monitorpoc = $count;
          }
          $count++;
        }
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Monitoring . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_monitorpoc'] == $a_users['usr_id']) {
            $monitorpoc = $count;
          }
          $count++;
        }

        $backuppoc = 0;
        $count = 1;
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Backups . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_backuppoc'] == $a_users['usr_id']) {
            $backuppoc = $count;
          }
          $count++;
        }
        $q_string  = "select usr_id ";
        $q_string .= "from users ";
        $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Backups . " ";
        $q_string .= "order by usr_last";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_rsdp_server['rsdp_backuppoc'] == $a_users['usr_id']) {
            $backuppoc = $count;
          }
          $count++;
        }

# retrieve index values
        $products     = return_Index($a_rsdp_server['rsdp_product'],      "select prod_id from products order by prod_name");
#       $projects     = return_Index($a_rsdp_server['rsdp_project'],      "select prj_id from projects where prj_group = " . $_SESSION['group'] . " and prj_close = 0 group by prj_name");
        $platformspoc = return_Index($a_rsdp_server['rsdp_platformspoc'], "select usr_id from users where usr_disabled = 0 and usr_id != 1 order by usr_last");
        $apppoc       = return_Index($a_rsdp_server['rsdp_apppoc'],       "select usr_id from users where usr_disabled = 0 and usr_id != 1 order by usr_last");
        $platform     = return_Index($a_rsdp_server['rsdp_platform'],     "select grp_id from groups where grp_disabled = 0 order by grp_name");
        $application  = return_Index($a_rsdp_server['rsdp_application'],  "select grp_id from groups where grp_disabled = 0 order by grp_name");
        $service      = return_Index($a_rsdp_server['rsdp_service'],      "select svc_id from service order by svc_id");
        $vendor       = return_Index($a_rsdp_server['rsdp_vendor'],       "select slv_id from supportlevel order by slv_value");
        $location     = return_Index($a_rsdp_server['rsdp_location'],     "select loc_id from locations left join cities on cities.ct_id = locations.loc_city where loc_type = 1 order by ct_city,loc_name");


        print "document.rsdp.rsdp_product['"      . $products     . "'].selected = true;\n";
#       print "document.rsdp.rsdp_project['"      . $projects     . "'].selected = true;\n";
        print "document.rsdp.rsdp_platformspoc['" . $platformspoc . "'].selected = true;\n";
        print "document.rsdp.rsdp_sanpoc['"       . $sanpoc       . "'].selected = true;\n";
        print "document.rsdp.rsdp_networkpoc['"   . $networkpoc   . "'].selected = true;\n";
        print "document.rsdp.rsdp_virtpoc['"      . $virtpoc      . "'].selected = true;\n";
        print "document.rsdp.rsdp_dcpoc['"        . $dcpoc        . "'].selected = true;\n";
        print "document.rsdp.rsdp_monitorpoc['"   . $monitorpoc   . "'].selected = true;\n";
        print "document.rsdp.rsdp_apppoc['"       . $apppoc       . "'].selected = true;\n";
        print "document.rsdp.rsdp_backuppoc['"    . $backuppoc    . "'].selected = true;\n";
        print "document.rsdp.rsdp_platform['"     . $platform     . "'].selected = true;\n";
        print "document.rsdp.rsdp_application['"  . $application  . "'].selected = true;\n";
        print "document.rsdp.rsdp_service['"      . $service      . "'].selected = true;\n";
        print "document.rsdp.rsdp_vendor['"       . $vendor       . "'].selected = true;\n";
        print "document.rsdp.rsdp_location['"     . $location     . "'].selected = true;\n";

        print "document.rsdp.rsdp_completion.value = '" . mysqli_real_escape_string($a_rsdp_server['rsdp_completion']) . "';\n";
        print "document.rsdp.rsdp_function.value = '"   . mysqli_real_escape_string($a_rsdp_server['rsdp_function'])   . "';\n";
        print "document.rsdp.rsdp_processors.value ='"  . mysqli_real_escape_string($a_rsdp_server['rsdp_processors']) . "';\n";
        print "document.rsdp.rsdp_memory.value = '"     . mysqli_real_escape_string($a_rsdp_server['rsdp_memory'])     . "';\n";
        print "document.rsdp.rsdp_ossize.value = '"     . mysqli_real_escape_string($a_rsdp_server['rsdp_ossize'])     . "';\n";

        if ($a_rsdp_server['rsdp_osmonitor']) {
          print "document.rsdp.rsdp_osmonitor.checked = true;\n";
        } else {
          print "document.rsdp.rsdp_osmonitor.checked = false;\n";
        }
        if ($a_rsdp_server['rsdp_appmonitor']) {
          print "document.rsdp.rsdp_appmonitor.checked = true;\n";
        } else {
          print "document.rsdp.rsdp_appmonitor.checked = false;\n";
        }
        if ($a_rsdp_server['rsdp_datapalette']) {
          print "document.rsdp.rsdp_datapalette.checked = true;\n";
        } else {
          print "document.rsdp.rsdp_datapalette.checked = false;\n";
        }
        if ($a_rsdp_server['rsdp_opnet']) {
          print "document.rsdp.rsdp_opnet.checked = true;\n";
        } else {
          print "document.rsdp.rsdp_opnet.checked = false;\n";
        }
        if ($a_rsdp_server['rsdp_newrelic']) {
          print "document.rsdp.rsdp_newrelic.checked = true;\n";
        } else {
          print "document.rsdp.rsdp_newrelic.checked = false;\n";
        }
        if ($a_rsdp_server['rsdp_centrify']) {
          print "document.rsdp.rsdp_centrify.checked = true;\n";
        } else {
          print "document.rsdp.rsdp_centrify.checked = false;\n";
        }
        if ($a_rsdp_server['rsdp_backup']) {
          print "document.rsdp.rsdp_backup.checked = true;\n";
        } else {
          print "document.rsdp.rsdp_backup.checked = false;\n";
        }

      }

      mysqli_free_result($q_rsdp_server);

# block to retrieve user data
      $q_string  = "select usr_phone,usr_email,usr_deptname ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $userid;
      $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_users) > 0) {
        $a_users = mysqli_fetch_array($q_users);

        $requestor = return_Index($userid,                  "select usr_id from users where usr_disabled = 0 order by usr_last");
        $deptname  = return_Index($a_users['usr_deptname'], "select dep_id from department left join business_unit on business_unit.bus_unit = department.dep_unit order by bus_name,dep_name");

        print "document.rsdp.rsdp_requestor['"    . $requestor    . "'].selected = true;\n";
        print "document.rsdp.usr_deptname['"      . $deptname     . "'].selected = true;\n";

        print "document.rsdp.usr_phone.value = '"       . mysqli_real_escape_string($a_users['usr_phone'])             . "';\n";
        print "document.rsdp.usr_email.value = '"       . mysqli_real_escape_string($a_users['usr_email'])             . "';\n";

      }

      mysqli_free_result($q_users);

# block to retrieve ticket data
      if ($ticket == 'yes') {
        $q_string  = "select tkt_id,tkt_build,tkt_san,tkt_network,tkt_datacenter,tkt_virtual,tkt_sysins,";
        $q_string .= "tkt_sysdns,tkt_storage,tkt_syscnf,tkt_backups,tkt_monitor,tkt_appins,tkt_appmon,";
        $q_string .= "tkt_appcnf,tkt_infosec,tkt_sysscan ";
        $q_string .= "from rsdp_tickets ";
        $q_string .= "where tkt_rsdp = " . $formVars['rsdp'];
        $q_rsdp_tickets = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_tickets) > 0) {
          $a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets);

          if ($a_rsdp_tickets['tkt_build']) {
            print "document.rsdp.tkt_build.checked = true;\n";
          } else {
            print "document.rsdp.tkt_build.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_san']) {
            print "document.rsdp.tkt_san.checked = true;\n";
          } else {
            print "document.rsdp.tkt_san.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_network']) {
            print "document.rsdp.tkt_network.checked = true;\n";
          } else {
            print "document.rsdp.tkt_network.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_datacenter']) {
            print "document.rsdp.tkt_datacenter.checked = true;\n";
          } else {
            print "document.rsdp.tkt_datacenter.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_virtual']) {
            print "document.rsdp.tkt_virtual.checked = true;\n";
          } else {
            print "document.rsdp.tkt_virtual.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_sysins']) {
            print "document.rsdp.tkt_sysins.checked = true;\n";
          } else {
            print "document.rsdp.tkt_sysins.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_sysdns']) {
            print "document.rsdp.tkt_sysdns.checked = true;\n";
          } else {
            print "document.rsdp.tkt_sysdns.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_storage']) {
            print "document.rsdp.tkt_storage.checked = true;\n";
          } else {
            print "document.rsdp.tkt_storage.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_syscnf']) {
            print "document.rsdp.tkt_syscnf.checked = true;\n";
          } else {
            print "document.rsdp.tkt_syscnf.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_backups']) {
            print "document.rsdp.tkt_backups.checked = true;\n";
          } else {
            print "document.rsdp.tkt_backups.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_monitor']) {
            print "document.rsdp.tkt_monitor.checked = true;\n";
          } else {
            print "document.rsdp.tkt_monitor.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_appins']) {
            print "document.rsdp.tkt_appins.checked = true;\n";
          } else {
            print "document.rsdp.tkt_appins.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_appmon']) {
            print "document.rsdp.tkt_appmon.checked = true;\n";
          } else {
            print "document.rsdp.tkt_appmon.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_appcnf']) {
            print "document.rsdp.tkt_appcnf.checked = true;\n";
          } else {
            print "document.rsdp.tkt_appcnf.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_infosec']) {
            print "document.rsdp.tkt_infosec.checked = true;\n";
          } else {
            print "document.rsdp.tkt_infosec.checked = false;\n";
          }
          if ($a_rsdp_tickets['tkt_sysscan']) {
            print "document.rsdp.tkt_sysscan.checked = true;\n";
          } else {
            print "document.rsdp.tkt_sysscan.checked = false;\n";
          }
          print "document.rsdp.tkt_id.value = '" . $a_rsdp_tickets['tkt_id'] . "';\n";

        }

        mysqli_free_result($q_rsdp_tickets);
      }


# block to retrieve backup data
      $q_string  = "select bu_id,bu_rsdp,bu_start,bu_include,bu_retention,bu_sunday,bu_monday,bu_tuesday,bu_wednesday,";
      $q_string .= "bu_thursday,bu_friday,bu_saturday,bu_suntime,bu_montime,bu_tuetime,bu_wedtime,bu_thutime,";
      $q_string .= "bu_fritime,bu_sattime ";
      $q_string .= "from rsdp_backups ";
      $q_string .= "where bu_rsdp = " . $formVars['rsdp'];
      $q_rsdp_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_backups) > 0) {
        $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);

# process the backup information
        print "document.rsdp.bu_retention['" . $a_rsdp_backups['bu_retention'] . "'].selected = true;\n";

        print "document.rsdp.bu_sunday['"    . $a_rsdp_backups['bu_sunday']    . "'].checked = true;\n";
        print "document.rsdp.bu_monday['"    . $a_rsdp_backups['bu_monday']    . "'].checked = true;\n";
        print "document.rsdp.bu_tuesday['"   . $a_rsdp_backups['bu_tuesday']   . "'].checked = true;\n";
        print "document.rsdp.bu_wednesday['" . $a_rsdp_backups['bu_wednesday'] . "'].checked = true;\n";
        print "document.rsdp.bu_thursday['"  . $a_rsdp_backups['bu_thursday']  . "'].checked = true;\n";
        print "document.rsdp.bu_friday['"    . $a_rsdp_backups['bu_friday']    . "'].checked = true;\n";
        print "document.rsdp.bu_saturday['"  . $a_rsdp_backups['bu_saturday']  . "'].checked = true;\n";

        print "document.rsdp.bu_start.value = '"   . mysqli_real_escape_string($a_rsdp_backups['bu_start'])   . "';\n";
        print "document.rsdp.bu_suntime.value = '" . mysqli_real_escape_string($a_rsdp_backups['bu_suntime']) . "';\n";
        print "document.rsdp.bu_montime.value = '" . mysqli_real_escape_string($a_rsdp_backups['bu_montime']) . "';\n";
        print "document.rsdp.bu_tuetime.value = '" . mysqli_real_escape_string($a_rsdp_backups['bu_tuetime']) . "';\n";
        print "document.rsdp.bu_wedtime.value = '" . mysqli_real_escape_string($a_rsdp_backups['bu_wedtime']) . "';\n";
        print "document.rsdp.bu_thutime.value = '" . mysqli_real_escape_string($a_rsdp_backups['bu_thutime']) . "';\n";
        print "document.rsdp.bu_fritime.value = '" . mysqli_real_escape_string($a_rsdp_backups['bu_fritime']) . "';\n";
        print "document.rsdp.bu_sattime.value = '" . mysqli_real_escape_string($a_rsdp_backups['bu_sattime']) . "';\n";

        if ($a_rsdp_backups['bu_include']) {
          print "document.rsdp.bu_include.checked = true;\n";
        } else {
          print "document.rsdp.bu_include.checked = false;\n";
        }

        print "document.rsdp.bu_id.value = '" . $a_rsdp_backups['bu_id'] . "';\n";

        mysqli_free_result($q_rsdp_backups);
      }

      print "validate_Form();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
