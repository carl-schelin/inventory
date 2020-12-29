<?php
# Script: network.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "network.mysql.php";
    $formVars['projectid']       = clean($_GET['projectid'],       10);
    $formVars['productid']       = clean($_GET['productid'],       10);
    $formVars['filter']          = clean($_GET['filter'],        1024);

    if ($formVars['projectid'] == '') {
      $formVars['projectid'] = 0;
    }
    if ($formVars['productid'] == '') {
      $formVars['productid'] = 0;
    }

    $filter = '';
    if (strlen($formVars['filter']) > 0) {

      $filterrsdp = explode(",", $formVars['filter']);

      $filter = 'and (';
      $or = '';
      for ($i = 0; $i < count($filterrsdp); $i++) {
        $filter .= $or . "rsdp_id = " . $filterrsdp[$i] . " ";
        $or = 'or ';
      }
      $filter .= ") ";
    }

    $formVars['URL'] = '';
    $systemurl = '';
    $hardwareurl = '';
    $interfaceurl = '';
    if (strlen($formVars['filter']) > 0) {
      $question = "?";
      $formVars['URL'] = "<p class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/network.php";
      if ($formVars['projectid'] > 0) {
        $formVars['URL'] .= $question . "projectid=" . $formVars['projectid'];
        $question = "&";
      }
      if ($formVars['productid'] > 0) {
        $formVars['URL'] .= $question . "productid=" . $formVars['productid'];
        $question = "&";
      }
      if ($formVars['filter'] != '') {
        $formVars['URL'] .= $question . "filter=" . $formVars['filter'];
        $question = "&";
      }
      $formVars['URL'] .= "\">Link</a></p>";
    }

    if (check_userlevel($db, $AL_Edit)) {

# prepopulate the small tables to increase lookup time.
      $q_string  = "select zone_id,zone_name ";
      $q_string .= "from ip_zones ";
      $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
        $ip_zones[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
      }

      $q_string  = "select med_id,med_text ";
      $q_string .= "from int_media ";
      $q_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_media = mysqli_fetch_array($q_int_media)) {
        $int_media[$a_int_media['med_id']] = $a_int_media['med_text'];
      }

      $q_string  = "select itp_id,itp_acronym ";
      $q_string .= "from inttype ";
      $q_inttype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_inttype = mysqli_fetch_array($q_inttype)) {
        $inttype[$a_inttype['itp_id']] = $a_inttype['itp_acronym'];
      }

      $q_string  = "select spd_id,spd_text ";
      $q_string .= "from int_speed ";
      $q_int_speed = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_speed = mysqli_fetch_array($q_int_speed)) {
        $int_speed[$a_int_speed['spd_id']] = $a_int_speed['spd_text'];
      }

      $q_string  = "select dup_id,dup_text ";
      $q_string .= "from int_duplex ";
      $q_int_duplex = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_duplex = mysqli_fetch_array($q_int_duplex)) {
        $int_duplex[$a_int_duplex['dup_id']] = $a_int_duplex['dup_text'];
      }

      $q_string  = "select red_id,red_text ";
      $q_string .= "from int_redundancy ";
      $q_int_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_redundancy = mysqli_fetch_array($q_int_redundancy)) {
        $int_redundancy[$a_int_redundancy['red_id']] = $a_int_redundancy['red_text'];
      }

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

# project
      $project  = "<form name=\"projects\">\n";
      $project .= "<table id=\"project-table\" class=\"ui-styled-table\">\n";
      $project .= "<tr>\n";
      $project .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $project .= "  <th class=\"ui-state-default\">Requestor</th>\n";
      $project .= "  <th class=\"ui-state-default\">Systems Group</th>\n";
      $project .= "  <th class=\"ui-state-default\">Systems Admin</th>\n";
      $project .= "  <th class=\"ui-state-default\">Applications Group</th>\n";
      $project .= "  <th class=\"ui-state-default\">Applications Admin</th>\n";
      $project .= "  <th class=\"ui-state-default\">SAN Admin</th>\n";
      $project .= "  <th class=\"ui-state-default\">Network Admin</th>\n";
      $project .= "  <th class=\"ui-state-default\">Virtualization Admin</th>\n";
      $project .= "  <th class=\"ui-state-default\">Data Center Admin</th>\n";
      $project .= "  <th class=\"ui-state-default\">Monitoring Admin</th>\n";
      $project .= "  <th class=\"ui-state-default\">Backup Admin</th>\n";
      $project .= "</tr>\n";

      $q_string  = "select rsdp_id ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_platform on rsdp_platform.pf_rsdp = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by os_sysname ";
      $q_rsdp_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_id = mysqli_fetch_array($q_rsdp_id)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_status) == 0) {

          $linkstart = "<a href=\"" . $RSDProot . "/tasks.php?id=" . $a_rsdp_id['rsdp_id'] . "\" target=\"_blank\">";
          $linkend   = "</a>";

          $q_string  = "select usr_last,usr_first,dep_name,bus_name,";
          $q_string .= "rsdp_completion,prj_name,prj_code,rsdp_application,grp_name,";
          $q_string .= "rsdp_sanpoc,rsdp_networkpoc,rsdp_virtpoc,rsdp_dcpoc,rsdp_srpoc,rsdp_monitorpoc,rsdp_backuppoc ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
          $q_string .= "left join department on department.dep_id = users.usr_deptname ";
          $q_string .= "left join business_unit on business_unit.bus_id = department.dep_unit ";
          $q_string .= "left join a_groups on a_groups.grp_id = rsdp_server.rsdp_platform ";
          $q_string .= "left join projects on projects.prj_id = rsdp_server.rsdp_project ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

          $q_string  = "select grp_name ";
          $q_string .= "from a_groups ";
          $q_string .= "where grp_id = " . $a_rsdp_server['rsdp_application'];
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_groups = mysqli_fetch_array($q_groups);

          $q_string  = "select os_sysname ";
          $q_string .= "from rsdp_osteam ";
          $q_string .= "where os_rsdp = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_platformspoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_platformadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_platformadmin = mysqli_fetch_array($q_platformadmin);
          if ($a_platformadmin['usr_last'] == '') {
            $platformadmin = "--";
          } else {
            $platformadmin = $a_platformadmin['usr_last'] . ", " . $a_platformadmin['usr_first'];
          }

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_apppoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_appadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_appadmin = mysqli_fetch_array($q_appadmin);
          if ($a_appadmin['usr_last'] == '') {
            $appadmin = "--";
          } else {
            $appadmin = $a_appadmin['usr_last'] . ", " . $a_appadmin['usr_first'];
          }

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_sanpoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_sanadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_sanadmin = mysqli_fetch_array($q_sanadmin);
          if ($a_sanadmin['usr_last'] == '') {
            $sanadmin = "--";
          } else {
            $sanadmin = $a_sanadmin['usr_last'] . ", " . $a_sanadmin['usr_first'];
          }

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_networkpoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_networkadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_networkadmin = mysqli_fetch_array($q_networkadmin);
          if ($a_networkadmin['usr_last'] == '') {
            $networkadmin = "--";
          } else {
            $networkadmin = $a_networkadmin['usr_last'] . ", " . $a_networkadmin['usr_first'];
          }

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_virtpoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_virtualizationadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_virtualizationadmin = mysqli_fetch_array($q_virtualizationadmin);
          if ($a_virtualizationadmin['usr_last'] == '') {
            $virtualizationadmin = "--";
          } else {
            $virtualizationadmin = $a_virtualizationadmin['usr_last'] . ", " . $a_virtualizationadmin['usr_first'];
          }

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_dcpoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_datacenteradmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_datacenteradmin = mysqli_fetch_array($q_datacenteradmin);
          if ($a_datacenteradmin['usr_last'] == '') {
            $datacenteradmin = "--";
          } else {
            $datacenteradmin = $a_datacenteradmin['usr_last'] . ", " . $a_datacenteradmin['usr_first'];
          }

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_monitorpoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_monitoringadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_monitoringadmin = mysqli_fetch_array($q_monitoringadmin);
          if ($a_monitoringadmin['usr_last'] == '') {
            $monitoringadmin = "--";
          } else {
            $monitoringadmin = $a_monitoringadmin['usr_last'] . ", " . $a_monitoringadmin['usr_first'];
          }

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_backuppoc ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_backupadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_backupadmin = mysqli_fetch_array($q_backupadmin);
          if ($a_backupadmin['usr_last'] == '') {
            $backupadmin = "--";
          } else {
            $backupadmin = $a_backupadmin['usr_last'] . ", " . $a_backupadmin['usr_first'];
          }

          $project .= "<tr>\n";
          $project .= "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_osteam['os_sysname'] . $linkend . "</td>\n";
          $project .= "<td class=\"ui-widget-content\">"                                                                                                                  . $a_rsdp_server['usr_last'] . ", " . $a_rsdp_server['usr_first'] . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"psg" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"systems_Group('psg"        . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_server['grp_name']                                      . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"psa" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"platforms_Admin('psa"      . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $platformadmin                                                  . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"pag" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"applications_Group('pag"   . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_groups['grp_name']                                           . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"paa" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"applications_Admin('paa"   . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $appadmin                                                       . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"pss" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"SAN_Admin('pss"            . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $sanadmin                                                       . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"pna" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"network_Admin('pna"        . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $networkadmin                                                   . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"pva" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"virtualization_Admin('pva" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $virtualizationadmin                                            . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"pdc" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"datacenter_Admin('pdc"     . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $datacenteradmin                                                . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"pma" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"monitoring_Admin('pma"     . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $monitoringadmin                                                . "</u></td>\n";
          $project .= "<td class=\"ui-widget-content\" id=\"pba" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"backup_Admin('pba"         . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $backupadmin                                                    . "</u></td>\n";
          $project .= "</tr>\n";

        }
      }

      $project .= "</table>\n";
      $project .= $formVars['URL'];
      $project .= "</form>\n";

      print "document.getElementById('project_mysql').innerHTML = '" . mysqli_real_escape_string($db, $project) . "';\n";

# system
      $carl = 'yes';
      if ($carl == 'no') {
        $current_task = array(
          0 => "Server Initialization",
          1 => "Server Provisioning",
          2 => "SAN Design",
          3 => "Network Configuration",
          4 => "Data Center/Virtualization",
          5 => "Data Center/Virtualization",
          6 => "Data Center/Virtualization",
          7 => "Data Center/Virtualization",
          8 => "Data Center/Virtualization",
          9 => "System Installation",
          10 => "SAN Provisioning",
          11 => "System Configuration",
          12 => "System Backups",
          13 => "Monitoring Configuration",
          14 => "Application Installed",
          15 => "Monitoring Complete",
          16 => "Application Configured",
          17 => "InfoSec Completed"
        );
      } else {
        $current_task = array(
          0 => "1",
          1 => "2",
          2 => "3",
          3 => "4",
          4 => "5",
          5 => "6",
          6 => "7",
          7 => "8",
          8 => "9",
          9 => "10",
          10 => "11",
          11 => "12",
          12 => "13",
          13 => "14",
          14 => "15",
          15 => "16",
          16 => "17",
          17 => "18"
        );
      }

      $current_script = array(
        0 => "/build/initial.php",
        1 => "/build/build.php",
        2 => "/san/designed.php",
        3 => "/network/network.php",
        4 => "/virtual/virtual.php",
        5 => "/virtual/virtual.php",
        6 => "/virtual/virtual.php",
        7 => "/virtual/virtual.php",
        8 => "/virtual/virtual.php",
        9 => "/system/installed.php",
        10 => "/san/provisioned.php",
        11 => "/system/configured.php",
        12 => "/backups/backups.php",
        13 => "/monitoring/monitoring.php",
        14 => "/application/installed.php",
        15 => "/application/monitored.php",
        16 => "/application/configured.php",
        17 => "/infosec/scanned.php",
      );

      $system  = "<form name=\"systems\">\n";
      $system .= "<table id=\"system-table\" class=\"ui-styled-table\">\n";
      $system .= "<tr>\n";
      $system .= "  <th class=\"ui-state-default\">Filter</th>\n";
      $system .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $system .= "  <th class=\"ui-state-default\">Task List</th>\n";
      $system .= "  <th class=\"ui-state-default\">Function</th>\n";
      $system .= "  <th class=\"ui-state-default\">Operating System</th>\n";
      $system .= "  <th class=\"ui-state-default\">Service Class</th>\n";
      $system .= "  <th class=\"ui-state-default\">Data Center</th>\n";
      $system .= "</tr>\n";

      $q_string  = "select rsdp_id ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by os_sysname ";
      $q_rsdp_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_id = mysqli_fetch_array($q_rsdp_id)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_status) == 0) {

          $linkstart = "<a href=\"" . $RSDProot . "/tasks.php?id=" . $a_rsdp_id['rsdp_id'] . "\" target=\"_blank\">";
          $taskstart = "<a href=\"" . $RSDProot . $current_script[$a_rsdp_status['st_step']] . "?rsdp=" . $a_rsdp_id['rsdp_id'] . "\" target=\"_blank\">";
          $linkend   = "</a>";

          $q_string  = "select rsdp_function,rsdp_location,rsdp_service,svc_name,loc_name ";
          $q_string .= "from rsdp_server ";
          $q_string .= "left join service   on service.svc_id   = rsdp_server.rsdp_service ";
          $q_string .= "left join locations on locations.loc_id = rsdp_server.rsdp_location ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " " ;
          $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);
          if ($a_rsdp_server['rsdp_location'] == 0) {
            $a_rsdp_server['loc_name'] = '--';
          }
          if ($a_rsdp_server['rsdp_service'] == 0) {
            $a_rsdp_server['svc_name'] = '--';
          }

          $q_string  = "select os_sysname,os_software ";
          $q_string .= "from rsdp_osteam ";
          $q_string .= "where os_rsdp = " . $a_rsdp_id['rsdp_id'] . " " ;
          $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

          $q_string  = "select os_software ";
          $q_string .= "from operatingsystem ";
          $q_string .= "where os_id = " . $a_rsdp_osteam['os_software'] . " " ;
          $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);
          if ($a_rsdp_osteam['os_software'] == 0) {
            $a_operatingsystem['os_software'] = '--';
          }

          if ($carl == 'no') {
            $q_string  = "select st_step ";
            $q_string .= "from rsdp_status ";
            $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " ";
            $q_string .= "order by st_step desc limit 1 ";
            $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);
            $tasks = $taskstart . $current_task[$a_rsdp_status['st_step']] . $linkend;
          } else {
            $tasks = '';
            $comma = '';
            for ($i = 1; $i < 19; $i++) {
              if ($i < 6 || $i > 9) {
                $taskstart = "<a href=\"" . $RSDProot . $current_script[$i - 1] . "?rsdp=" . $a_rsdp_id['rsdp_id'] . "\" target=\"_blank\">";

                $q_string  = "select st_step ";
                $q_string .= "from rsdp_status ";
                $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = " . $i . " ";
                $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
                if (mysqli_num_rows($q_rsdp_status) == 0) {
                  $tasks .= $comma . $taskstart . $i . $linkend;
                  $comma = ',';
                } else {
                  $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);
                  $tasks .= $comma . "<b>" . $taskstart . $a_rsdp_status['st_step'] . $linkend . "</b>";
                  $comma = ',';
                }
              }
            }
          }

          $system .= "<tr>\n";
          $system .= "<td class=\"ui-widget-content delete\"><input type=\"checkbox\" id=\"filter_" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"set_Filter('" . $a_rsdp_id['rsdp_id'] . "');\"></td>\n";
          $system .= "  <td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_osteam['os_sysname'] . $linkend . "<input type=\"hidden\" name=\"rsdp_virtual\" id=\"rsdp_virtual\" value=\"" . rsdp_Virtual($db, $a_rsdp_id['rsdp_id']) . "\"></td>\n";
          $system .= "  <td class=\"ui-widget-content\">" . $tasks . "</td>\n";
          $system .= "  <td class=\"ui-widget-content\" id=\"ssf" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"server_Function('ssf"   . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_server['rsdp_function']   . "</u></td>\n";
          $system .= "  <td class=\"ui-widget-content\" id=\"sos" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"operating_System('sos"  . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_operatingsystem['os_software'] . "</u></td>\n";
          $system .= "  <td class=\"ui-widget-content\" id=\"ssc" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"service_Class('ssc"     . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_server['svc_name']        . "</u></td>\n";
          $system .= "  <td class=\"ui-widget-content\" id=\"sdc" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"data_Center('sdc"       . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_server['loc_name']        . "</u></td>\n";
          $system .= "</tr>\n";

        }
      }

      $system .= "</table>\n";
      $system .= $formVars['URL'] . $systemurl;
      $system .= "</form>\n";

      print "document.getElementById('system_mysql').innerHTML = '" . mysqli_real_escape_string($db, $system) . "';\n";


# hardware
      $hardware  = "<form name=\"hardware\">\n";
      $hardware .= "<table id=\"hardware-table\" class=\"ui-styled-table\">\n";
      $hardware .= "<tr>\n";
      $hardware .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Vendor</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Model</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">CPUs</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">RAM</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">OS Disk</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Add Disk</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Power</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Draw</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">@Startup</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Power Supplies</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Redundant</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Plug Type</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Row</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Rack</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Unit</th>\n";
      $hardware .= "  <th class=\"ui-state-default\">Number</th>\n";
      $hardware .= "</tr>\n";

      $q_string  = "select rsdp_id,os_sysname,os_fqdn,pf_model,mod_virtual ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_platform on rsdp_platform.pf_rsdp = rsdp_server.rsdp_id ";
      $q_string .= "left join models        on models.mod_id         = rsdp_platform.pf_model ";
      $q_string .= "left join rsdp_osteam   on rsdp_osteam.os_rsdp   = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by os_sysname ";
      $q_rsdp_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_id = mysqli_fetch_array($q_rsdp_id)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_status) == 0) {

          $linkstart       = "<a href=\"" . $RSDProot . "/tasks.php?id=" . $a_rsdp_id['rsdp_id'] . "\" target=\"_blank\">";
          $filesystemstart = "<a href=\"" . $RSDProot . "/build/initial.php?rsdp=" . $a_rsdp_id['rsdp_id'] . "#filesystems\" target=\"_blank\">";
          $linkend         = "</a>";

          if ($a_rsdp_server['mod_virtual']) {
            $virtual = 1;
          } else {
            $virtual = 0;
          }

          $q_string  = "select rsdp_processors,rsdp_memory,rsdp_ossize,rsdp_function ";
          $q_string .= "from rsdp_server ";
          $q_string .= "where rsdp_id = " . $a_rsdp_id['rsdp_id'] . " " ;
          $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

          $q_string  = "select os_sysname,os_software ";
          $q_string .= "from rsdp_osteam ";
          $q_string .= "where os_rsdp = " . $a_rsdp_id['rsdp_id'] . " " ;
          $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

          $q_string  = "select os_software ";
          $q_string .= "from operatingsystem ";
          $q_string .= "where os_id = " . $a_rsdp_osteam['os_software'];
          $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);

          $q_string  = "select pf_special,mod_vendor,mod_name,part_name,volt_text,mod_plugs,pf_redundant,plug_text,mod_draw,mod_start,pf_rack,pf_row,pf_unit,mod_size ";
          $q_string .= "from rsdp_platform ";
          $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
          $q_string .= "left join int_volts on int_volts.volt_id = models.mod_volts ";
          $q_string .= "left join int_plugtype on int_plugtype.plug_id = models.mod_plugtype ";
          $q_string .= "left join parts on parts.part_id = models.mod_type ";
          $q_string .= "where pf_rsdp = " . $a_rsdp_id['rsdp_id'] . " ";
          $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

          $fstitle = '';
          $filesystems = '';
          $comma = '';
          $q_string  = "select fs_volume,fs_size ";
          $q_string .= "from rsdp_filesystem ";
          $q_string .= "where fs_rsdp = " . $a_rsdp_id['rsdp_id'];
          $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
            while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
              $fstitle .= $comma . $a_rsdp_filesystem['fs_volume'];
              $filesystems .= $comma . $a_rsdp_filesystem['fs_size'] . " GB";
              $comma = ", ";
            }
          } else {
            $filesystems = '--';
          }

          if ($a_rsdp_platform['pf_redundant']) {
            $redundant = 'Yes';
          } else {
            $redundant = 'No';
          }

          if ($a_rsdp_platform['pf_row'] == '') {
            $a_rsdp_platform['pf_row'] = 'N/A';
          }
          if ($a_rsdp_platform['pf_rack'] == '') {
            $a_rsdp_platform['pf_rack'] = 'N/A';
          }

          $hardware .= "<tr>\n";
          $hardware .= "  <td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_osteam['os_sysname'] . $linkend . "<input type=\"hidden\" name=\"rsdp_virtual\" id=\"rsdp_virtual\" value=\"" . rsdp_Virtual($db, $a_rsdp_id['rsdp_id']) . "\"></td>\n";
          $hardware .= "  <td class=\"ui-widget-content\">"                                                                                                               . $a_rsdp_platform['mod_vendor']    . "</td>\n";
          $hardware .= "  <td class=\"ui-widget-content\">"                                                                                                               . $a_rsdp_platform['mod_name']      . "</td>\n";
          $hardware .= "  <td class=\"ui-widget-content\" id=\"hcp" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"central_Processor('hcp" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_server['rsdp_processors'] . "</u></td>\n";
          $hardware .= "  <td class=\"ui-widget-content\" id=\"hsm" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"system_Memory(    'hsm" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_server['rsdp_memory']     . " GB</u></td>\n";
          $hardware .= "  <td class=\"ui-widget-content\" id=\"hss" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"system_Size(      'hss" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_server['rsdp_ossize']     . " GB</u></td>\n";
          $hardware .= "  <td class=\"ui-widget-content\" title=\"" . $fstitle . "\">" . $filesystemstart . $filesystems . $linkend . "</td>\n";
          if (rsdp_Virtual($db, $a_rsdp_id['rsdp_id']) == 0) {
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hvt" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"volt_Text(        'hvt" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['volt_text']     . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hpd" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"power_Draw(       'hpd" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['mod_draw']      . " Amps</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hps" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"power_Start(      'hps" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['mod_start']     . " Amps</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hpp" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"power_Plugs(      'hpp" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['mod_plugs']     . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hpr" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"power_Redundant(  'hpr" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $redundant                        . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hpt" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"plug_Text(        'hpt" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['plug_text']     . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hrw" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"start_Row(        'hrw" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['pf_row']        . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hrk" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"start_Rack(       'hrk" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['pf_rack']       . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hsu" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"start_Unit(       'hsu" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['pf_unit']       . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hnu" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"number_Units(     'hnu" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_rsdp_platform['mod_size']      . "</u></td>\n";
          } else {
            $hardware .= "  <td class=\"delete ui-widget-content\" colspan=\"10\">Virtual Machine</td>\n";
          }
          $hardware .= "</tr>\n";

          if (rsdp_Virtual($db, $a_rsdp_id['rsdp_id']) == 0) {
            $hardware .= "<tr>\n";
            $hardware .= "  <th class=\"ui-state-default\">System</th>\n";
            $hardware .= "  <th class=\"ui-state-default\">Switch</th>\n";
            $hardware .= "  <th class=\"ui-state-default\">Port</th>\n";
            $hardware .= "  <th class=\"ui-state-default\">Media</th>\n";
            $hardware .= "  <th class=\"ui-state-default\">WWNN Zone</th>\n";
            $hardware .= "</tr>\n";

            $q_string  = "select san_id,san_sysport,san_switch,san_port,med_text,san_wwnnzone ";
            $q_string .= "from rsdp_san ";
            $q_string .= "left join int_media on int_media.med_id = rsdp_san.san_media ";
            $q_string .= "where san_rsdp = " . $a_rsdp_id['rsdp_id'] . " ";
            $q_string .= "order by san_sysport";
            $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            if (mysqli_num_rows($q_rsdp_san) > 0) {
              while ($a_rsdp_san = mysqli_fetch_array($q_rsdp_san)) {
                $hardware .= "<tr>\n";
                $hardware .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_sysport']  . "</td>\n";
                $hardware .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_switch']   . "</td>\n";
                $hardware .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_port']     . "</td>\n";
                $hardware .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['med_text']     . "</td>\n";
                $hardware .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_wwnnzone'] . "</td>\n";
                $hardware .= "</tr>\n";
              }
            } else {
              $hardware .= "<tr>\n";
              $hardware .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>\n";
              $hardware .= "</tr>\n";
            }
          }
        }
      }

      $hardware .= "</table>\n";
      $hardware .= $formVars['URL'] . $hardwareurl;
      $hardware .= "</form>\n";

      print "document.getElementById('hardware_mysql').innerHTML = '" . mysqli_real_escape_string($db, $hardware) . "';\n";


# interfaces
      $interface  = "<table id=\"interface-table\" class=\"ui-styled-table\">\n";
      $interface .= "<tr>\n";
      $interface .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Interface Name</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Monitor</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Type</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Logical Interface</th>\n";
      $interface .= "  <th class=\"ui-state-default\">IP Address</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Netmask</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Zone</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Gateway</th>\n";
      $interface .= "  <th class=\"ui-state-default\">VLAN</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Physical Port</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Media</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Switch</th>\n";
      $interface .= "  <th class=\"ui-state-default\">Port</th>\n";
      $interface .= "</tr>\n";

      $servername = '&nbsp;';
      $q_string  = "select rsdp_id,os_sysname,os_fqdn,pf_model ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_platform on rsdp_platform.pf_rsdp = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by os_sysname ";
      $q_rsdp_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_id = mysqli_fetch_array($q_rsdp_id)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_status) == 0) {

          $linkstart = "<a href=\"" . $RSDProot . "/build/build.php?rsdp=" . $a_rsdp_id['rsdp_id'] . "#tabs-5\" target=\"_blank\">";
          $linkend   = "</a>";
          $servername = $a_rsdp_id['os_sysname'];

          $q_string  = "select if_id,if_name,if_monitored,if_interface,if_sysport,if_ip,if_mask,zone_name,";
          $q_string .= "if_gate,if_switch,if_port,itp_acronym,if_virtual,med_text,if_vlan ";
          $q_string .= "from rsdp_interface ";
          $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
          $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
          $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
          $q_string .= "where if_rsdp = " . $a_rsdp_id['rsdp_id'] . " and if_if_id = 0 ";
          $q_string .= "order by if_name,if_interface";
          $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_rsdp_interface) > 0) {
            while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

              $class = "ui-widget-content";
              $virtual = '';
              if ($a_rsdp_interface['if_virtual']) {
                $class = "ui-state-highlight";
                $virtual = ' (v)';
              }

              if ($a_rsdp_interface['if_name'] == '') {
                $a_rsdp_interface['if_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              $checked = "";
              if ($a_rsdp_interface['if_monitored']) {
                $checked = " checked";
              }
              if ($a_rsdp_interface['itp_acronym'] == '') {
                $a_rsdp_interface['itp_acronym'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['if_interface'] == '') {
                $a_rsdp_interface['if_interface'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['if_ip'] == '') {
                $a_rsdp_interface['if_ip'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['zone_name'] == '') {
                $a_rsdp_interface['zone_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['if_gate'] == '') {
                $a_rsdp_interface['if_gate'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['if_vlan'] == '') {
                $a_rsdp_interface['if_vlan'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['if_sysport'] == '') {
                $a_rsdp_interface['if_sysport'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['med_text'] == '') {
                $a_rsdp_interface['med_text'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['if_switch'] == '') {
                $a_rsdp_interface['if_switch'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_rsdp_interface['if_port'] == '') {
                $a_rsdp_interface['if_port'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }


              $interface .= "<tr>\n";
              $interface .= "  <td class=\"" . $class . "\">" . $linkstart . $servername                       . $linkend . "</td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"isn" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Name(       'isn" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_name']      . $virtual . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $checked . " id=\"if_mon_" . $a_rsdp_interface['if_id'] . "\" onclick=\"check_Monitored('" . $a_rsdp_interface['if_id'] . "');\"></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"iia" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Acronym(    'iia" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['itp_acronym']             . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"ifc" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Description('ifc" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_interface']            . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"iad" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Address(    'iad" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_ip']                   . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"ian" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Netmask(    'ian" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_mask']                 . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"izn" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Zone(       'izn" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['zone_name']               . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"igw" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Gateway(    'igw" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_gate']                 . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"ivl" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_VLAN(       'ivl" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_vlan']                 . "</u></td>\n";
              if (rsdp_Virtual($db, $a_rsdp_id['rsdp_id']) == 0) {
                $interface .= "  <td class=\"" . $class . "\" id=\"isp" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Device(     'isp" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_sysport']              . "</u></td>\n";
                $interface .= "  <td class=\"" . $class . "\" id=\"imt" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Media(      'imt" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['med_text']                . "</u></td>\n";
                $interface .= "  <td class=\"" . $class . "\" id=\"isw" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Switch(     'isw" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_switch']               . "</u></td>\n";
                $interface .= "  <td class=\"" . $class . "\" id=\"ipt" . $a_rsdp_interface['if_id'] . "\" onclick=\"interface_Port(       'ipt" . $a_rsdp_interface['if_id'] . "');\"><u>" . $a_rsdp_interface['if_port']                 . "</u></td>\n";
              } else {
                $interface .= "  <td class=\"delete " . $class . "\" colspan=\"4\">Virtual Machine</td>\n";
              }
              $interface .= "</tr>\n";

              $q_string  = "select if_id,if_name,if_interface,if_sysport,if_ip,if_mask,zone_name,if_gate,if_switch,if_port,itp_acronym,if_virtual,med_text,if_vlan ";
              $q_string .= "from rsdp_interface ";
              $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
              $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
              $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
              $q_string .= "where if_rsdp = " . $a_rsdp_id['rsdp_id'] . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
              $q_string .= "order by if_name,if_interface";
              $q_rsdp_child = mysqli_query($db, $q_string);
              if (mysqli_num_rows($q_rsdp_child) > 0) {
                while ($a_rsdp_child = mysqli_fetch_array($q_rsdp_child)) {

                  $class = "ui-widget-content";
                  $virtual = '';
                  if ($a_rsdp_child['if_virtual']) {
                    $class = "ui-state-highlight";
                    $virtual = ' (v)';
                  }

                  if ($a_rsdp_child['if_name'] == '') {
                    $a_rsdp_child['if_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['itp_acronym'] == '') {
                    $a_rsdp_child['itp_acronym'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['if_interface'] == '') {
                    $a_rsdp_child['if_interface'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['if_ip'] == '') {
                    $a_rsdp_child['if_ip'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['zone_name'] == '') {
                    $a_rsdp_child['zone_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['if_gate'] == '') {
                    $a_rsdp_child['if_gate'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['if_vlan'] == '') {
                    $a_rsdp_child['if_vlan'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['if_sysport'] == '') {
                    $a_rsdp_child['if_sysport'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['med_text'] == '') {
                    $a_rsdp_child['med_text'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['if_switch'] == '') {
                    $a_rsdp_child['if_switch'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_rsdp_child['if_port'] == '') {
                    $a_rsdp_child['if_port'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }

                  $interface .= "<tr>\n";
                  $interface .= "  <td class=\"" . $class . "\">"      . $servername                              . "</td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"isn" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Name(       'isn" . $a_rsdp_child['if_id'] . "');\">&gt; <u>" . $a_rsdp_child['if_name']      . $virtual . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"iia" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Acronym(    'iia" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['itp_acronym']             . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"ifc" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Description('ifc" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_interface']            . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"iad" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Address(    'iad" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_ip']                   . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"ian" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Netmask(    'ian" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_mask']                 . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"izn" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Zone(       'izn" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['zone_name']               . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"igw" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Gateway(    'igw" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_gate']                 . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"ivl" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_VLAN(       'ivl" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_vlan']                 . "</u></td>\n";
                  if (rsdp_Virtual($db, $a_rsdp_id['rsdp_id']) == 0) {
                    $interface .= "  <td class=\"" . $class . "\" id=\"isp" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Device(     'isp" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_sysport']              . "</u></td>\n";
                    $interface .= "  <td class=\"" . $class . "\" id=\"imt" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Media(      'imt" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['med_text']                . "</u></td>\n";
                    $interface .= "  <td class=\"" . $class . "\" id=\"isw" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Switch(     'isw" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_switch']               . "</u></td>\n";
                    $interface .= "  <td class=\"" . $class . "\" id=\"ipt" . $a_rsdp_child['if_id'] . "\" onclick=\"interface_Port(       'ipt" . $a_rsdp_child['if_id'] . "');\"><u>"      . $a_rsdp_child['if_port']                 . "</u></td>\n";
                  } else {
                    $interface .= "  <td class=\"delete " . $class . "\" colspan=\"4\">Virtual Machine</td>\n";
                  }
                  $interface .= "</tr>\n";
                }
              }
              $servername = '&nbsp;';
              $linkstart = '';
              $linkend = '';
            }
          } else {
            $interface .= "<tr>\n";
            $interface .= "  <td class=\"ui-widget-content\" colspan=\"11\">No records found.</td>\n";
            $interface .= "</tr>\n";
          }
        }
      }

      $interface .= "</table>\n";
      $interface .= $formVars['URL'] . $interfaceurl;

      print "document.getElementById('interface_mysql').innerHTML = '" . mysqli_real_escape_string($db, $interface) . "';\n";

# kickstart
      $parameters = '<p>';

      $q_string  = "select rsdp_id,os_sysname,os_fqdn,pf_model ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_platform on rsdp_platform.pf_rsdp = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by os_sysname ";
      $q_rsdp_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_id = mysqli_fetch_array($q_rsdp_id)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_status) == 0) {

          if ($a_rsdp_server['pf_model'] == 45) {
            $virtual = 1;
          } else {
            $virtual = 0;
          }

          $q_string  = "select if_id,if_name,if_sysport,if_interface,if_ip,if_gate,if_mask,if_vlan,if_switch,if_port,";
          $q_string .= "if_zone,if_media,if_speed,if_duplex,if_redundant,if_type,if_description,if_ipcheck,if_swcheck ";
          $q_string .= "from rsdp_interface ";
          $q_string .= "where if_rsdp = " . $a_rsdp_id['rsdp_id'] . " and if_if_id = 0 ";
          $q_string .= "order by if_interface desc";
          $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {
            if ($a_rsdp_interface['if_ipcheck']) {
              if ($inttype[$a_rsdp_interface['if_type']] == 'Mgt') {
                $parameters .= "DOMAIN="   . $a_rsdp_id['os_fqdn']         . "</br>";
                $parameters .= "MGT_IF="   . $a_rsdp_interface['if_interface'] . "</br>";
                $parameters .= "MGT_NAME=" . $a_rsdp_interface['if_name']      . "</br>";
                $parameters .= "MGT_IP="   . $a_rsdp_interface['if_ip']        . "</br>";
                $parameters .= "MGT_NM="   . $a_rsdp_interface['if_mask']      . "</br>";
                $parameters .= "MGT_GW="   . $a_rsdp_interface['if_gate']      . "</br></br>";
#                $parameters .= "export MGT_IP MGT_NAME MGT_NM MGT_GW MGT_IF DOMAIN</br></br>";
#                $parameters .= "########</br>";
#                $parameters .= "## Additional information for dual interface systems.  Default to none.</br>";
#                $parameters .= "########</br>";
              }
              if ($inttype[$a_rsdp_interface['if_type']] == 'App') {
                $parameters .= "HOSTNAME="       . $a_rsdp_interface['if_name']                    . "</br>";
                $parameters .= "HOST_IP="        . $a_rsdp_interface['if_ip']                      . "</br>";
                $parameters .= "GATEWAY_IP="     . $a_rsdp_interface['if_gate']                    . "</br>";
                $parameters .= "HOST_NETMASK="   . createNetmaskAddr($a_rsdp_interface['if_mask']) . "</br>";
                $parameters .= "APP_IF="         . $a_rsdp_interface['if_interface']               . "</br></br>";
              }
#            } else {
#              $parameters .= "HOSTNAME=none</br>";
#              $parameters .= "HOST_IP=none</br>";
#              $parameters .= "GATEWAY_IP=none</br>";
#              $parameters .= "HOST_NETMASK=none</br>";
#              $parameters .= "APP_IF=none</br>";
            }
          }

#          $parameters .= "export HOSTNAME HOST_IP GATEWAY_IP HOST_NETMASK APP_IF</br>";
#          $parameters .= "########</br></br>";
#          $parameters .= "# Install</br>";
#          $parameters .= "LOCAL='true'</br>";
#          $parameters .= "CENTRIFY='false'</br>";
#          $parameters .= "FORCEUSERS='false'</br>";
#          $parameters .= "OPENVIEW='true'</br>";
#          $parameters .= "NETBACKUP='false'</br>";
#          $parameters .= "MGT_ISDFLT='false'</br>";
#          $parameters .= "export CENTRIFY OPENVIEW NETBACKUP FORCEUSERS MGT_ISDFLT</br></br>";
#          $parameters .= "#############################################################################################</br>";
#          $parameters .= "## Remove the two lines below to acknowledge that you actually configured this and didn't just run it.</br>";
#          $parameters .= "#############################################################################################</br>";
#          $parameters .= "#echo \"Please update the configuration section of the script before running!\"</br>";
#          $parameters .= "#exit 1</br></br>";
          $parameters .= "----------</br>";

        }
      }

      $parameters .= "</p>";

      print "document.getElementById('kickstart_mysql').innerHTML = '" . mysqli_real_escape_string($db, $parameters) . "';\n";


# dns field
      $dns = '<p><pre>';

      $q_string  = "select rsdp_id,os_sysname,os_fqdn,pf_model,if_name,if_ip ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_platform  on rsdp_platform.pf_rsdp  = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_osteam    on rsdp_osteam.os_rsdp    = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_interface on rsdp_interface.if_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "and if_ipcheck = 1 and (if_type = 1 or if_type = 2) ";
      $q_string .= "order by if_ip ";
      $q_rsdp_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_id = mysqli_fetch_array($q_rsdp_id)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_status) == 0) {

          $dns .= $a_rsdp_id['if_ip'] . "\t" . $a_rsdp_id['if_name'] . "." . $a_rsdp_id['os_fqdn'] . "\n";

        }
      }

      $dns .= "</pre></p>";

      print "document.getElementById('dns_mysql').innerHTML = '" . mysqli_real_escape_string($db, $dns) . "';\n";


# hosts field
      $hosts = '<p><pre>';

      $q_string  = "select rsdp_id,os_sysname,os_fqdn,pf_model,if_name,if_ip ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_platform  on rsdp_platform.pf_rsdp  = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_osteam    on rsdp_osteam.os_rsdp    = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_interface on rsdp_interface.if_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "and if_ipcheck = 1 and (if_type = 1 or if_type = 2) ";
      $q_string .= "order by if_ip ";
      $q_rsdp_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_id = mysqli_fetch_array($q_rsdp_id)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_id['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));

        if (mysqli_num_rows($q_rsdp_status) == 0) {
          $hosts .= $a_rsdp_id['if_ip'] . "\t" . $a_rsdp_id['if_name'] . "." . $a_rsdp_id['os_fqdn'] . "\t" . $a_rsdp_id['if_name'] . "\n";
        }
      }

      $hosts .= "</pre></p>";

      print "document.getElementById('hosts_mysql').innerHTML = '" . mysqli_real_escape_string($db, $hosts) . "';\n";

# vulnerability listing
      $address = '';
      $hostname = '';
      $comma = '';

      $q_string  = "select rsdp_id,if_name,if_ip,os_fqdn ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "left join rsdp_server on rsdp_interface.if_rsdp = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= "and if_ipcheck = 1 and if_zone != 31 ";
      $q_string .= $filter;
      $q_string .= "order by if_name,if_interface";
      $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_interface) > 0) {

        $address = "<p>List of IP Addresses: ";
        $hostname = "<p>List of DNS Entries: ";
        while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

          $q_string  = "select st_id ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_interface['rsdp_id'] . " and st_step = 18 ";
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));

          if (mysqli_num_rows($q_rsdp_status) == 0) {
            $address .= $comma . $a_rsdp_interface['if_ip'];
            $hostname .= $comma . $a_rsdp_interface['if_name'] . "." . $a_rsdp_interface['os_fqdn'];
            $comma = ", ";
          }

        }
        $address .= "</p>";
        $hostname .= "</p>";
      }

      print "document.getElementById('vulnerability_mysql').innerHTML = '" . mysqli_real_escape_string($db, $address . $hostname) . "';\n";


# Virtualization wants a csv type output
# "Hostname","Function","CPU","Memory","Disks","Disks","Disks","IP","VLAN","IP","VLAN","IP","VLAN"

      $virtualization = "<p><pre>";

      $q_string  = "select rsdp_id,rsdp_function,rsdp_processors,rsdp_memory,rsdp_ossize,os_sysname ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by os_sysname ";
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));

        if (mysqli_num_rows($q_rsdp_status) == 0) {
          $virtualization .= "\"" . $a_rsdp_server['os_sysname'] . "\",\"" . $a_rsdp_server['rsdp_function'] . "\",\"" . $a_rsdp_server['rsdp_processors'] . "\",\"" . $a_rsdp_server['rsdp_memory'] . "\",\"" . $a_rsdp_server['rsdp_ossize'] . "\"";

          $q_string  = "select fs_volume,fs_size ";
          $q_string .= "from rsdp_filesystem ";
          $q_string .= "where fs_rsdp = " . $a_rsdp_server['rsdp_id'];
          $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
            while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
              $virtualization .= ",\"" . $a_rsdp_filesystem['fs_size'] . "\"";
            }
          }

          $q_string  = "select if_ip,if_vlan,if_ipcheck ";
          $q_string .= "from rsdp_interface ";
          $q_string .= "where if_rsdp = " . $a_rsdp_server['rsdp_id'] . " ";
          $q_string .= "order by if_interface";
          $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

            if ($a_rsdp_interface['if_ipcheck']) {
              $virtualization .= ",\"" . $a_rsdp_interface['if_ip'] . "\",\"" . $a_rsdp_interface['if_vlan'] . "\"";
            }
          }

          $virtualization .= "</br>";

        }
      }

      $virtualization .= "</pre></p>";

      print "document.getElementById('virtualization_mysql').innerHTML = '" . mysqli_real_escape_string($db, $virtualization) . "';\n";


# list servers and interfaces that will be monitored

      $monitoring  = "<table id=\"interface-table\" class=\"ui-styled-table\">\n";
      $monitoring .= "<tr>\n";
      $monitoring .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $monitoring .= "  <th class=\"ui-state-default\">Function</th>\n";
      $monitoring .= "  <th class=\"ui-state-default\">IP Address</th>\n";
      $monitoring .= "</tr>\n";

      $q_string  = "select rsdp_id,rsdp_function,if_monitored,if_ip,os_sysname ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      $q_string .= "left join rsdp_interface on rsdp_interface.if_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      $q_string .= $filter . " and if_monitored = 1 ";
      $q_string .= "order by os_sysname ";
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . " :" . mysqli_error($db));

        if (mysqli_num_rows($q_rsdp_status) == 0) {
          $monitoring .= "<tr>\n";
          $monitoring .= "  <td class=\"ui-widget-content\">" . $a_rsdp_server['os_sysname'] . "</td>\n";
          $monitoring .= "  <td class=\"ui-widget-content\">" . $a_rsdp_server['rsdp_function'] . "</td>\n";
          $monitoring .= "  <td class=\"ui-widget-content\">" . $a_rsdp_server['if_ip'] . "</td>\n";
          $monitoring .= "</tr>\n";
        }
      }

      $monitoring .= "</table>";

      print "document.getElementById('monitoring_mysql').innerHTML = '" . mysqli_real_escape_string($db, $monitoring) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
