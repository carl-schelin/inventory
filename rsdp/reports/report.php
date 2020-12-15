<?php
# Script: report.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('2');

  $package = "report.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP Task Report</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">


</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php
  switch ($formVars['id']) {
    case  2: $title = "Platforms Report";
             $previous = "Initial";
             $taskpoc = "rsdp_platformspoc";
             $tasktitle = "Platforms";
             $taskdepend = "st_step = 1";
             $linkstart = "<a href=\"" . $RSDProot . "/build/build.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case  3: $title = "Storage Report";
             $previous = "Platforms";
             $taskpoc = "rsdp_sanpoc";
             $tasktitle = "Storage Engineering";
             $taskdepend = "st_step = 2";
             $linkstart = "<a href=\"" . $RSDProot . "/san/designed.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case  4: $title = "Network Engineering Report";
             $previous = "Platforms";
             $taskpoc = "rsdp_networkpoc";
             $tasktitle = "Network Engineering";
             $taskdepend = "st_step = 2";
             $linkstart = "<a href=\"" . $RSDProot . "/network/network.php?rsdp=";
             $rsdplabel = "#tabs-4";
             $tasklabel = "#interface";
             break;
    case  5: $title = "Virtualization Report";
             $previous = "SAN/Networking";
             $taskpoc = "rsdp_virtpoc";
             $tasktitle = "Virtualization";
             $taskdepend = "(st_step = 3 or st_step = 4)";
             $linkstart = "<a href=\"" . $RSDProot . "/virtual/virtual.php?rsdp=";
             $rsdplabel = "#tabs-2";
             $tasklabel = "#hardware";
             break;
    case  6: $title = "Data Center";
             $previous = "SAN/Networking";
             $taskpoc = "rsdp_dcpoc";
             $tasktitle = "Data Center";
             $taskdepend = "(st_step = 3 and st_step = 4)";
             $linkstart = "<a href=\"" . $RSDProot . "/physical/physical.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 10: $title = "Operating System Installation";
             $previous = "DC/Virtualization";
             $taskpoc = "rsdp_platformspoc";
             $tasktitle = "Platforms";
             $taskdepend = "(st_step = 5 or st_step = 9)";
             $linkstart = "<a href=\"" . $RSDProot . "/system/installed.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 11: $title = "SAN Provisioning";
             $previous = "Operating System Installation";
             $taskpoc = "rsdp_sanpoc";
             $tasktitle = "San Provisioning";
             $taskdepend = "st_step = 10";
             $linkstart = "<a href=\"" . $RSDProot . "/san/provisioned.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 12: $title = "Operating System Configuration";
             $previous = "SAN/Networking";
             $taskpoc = "rsdp_dcpoc";
             $tasktitle = "Data Center";
             $taskdepend = "st_step = 11";
             $linkstart = "<a href=\"" . $RSDProot . "/system/configured.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 13: $title = "Backup Configuration";
             $previous = "Operating System Configuration";
             $taskpoc = "rsdp_sanpoc";
             $tasktitle = "SAN and Backup";
             $taskdepend = "st_step = 12";
             $linkstart = "<a href=\"" . $RSDProot . "/backups/backups.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 14: $title = "System Monitoring\n";
             $previous = "Operating System Configuration";
             $taskpoc = "rsdp_monpoc";
             $tasktitle = "Monitoring";
             $taskdepend = "st_step = 12";
             $linkstart = "<a href=\"" . $RSDProot . "/monitoring/monitoring.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 15: $title = "Application Installation";
             $previous = "Operating System Configuration";
             $taskpoc = "rsdp_apppoc";
             $tasktitle = "Applications";
             $taskdepend = "st_step = 12";
             $linkstart = "<a href=\"" . $RSDProot . "/application/installed.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 16: $title = "Applications Monitoring";
             $previous = "Application Installation";
             $taskpoc = "rsdp_monpoc";
             $tasktitle = "Monitoring";
             $taskdepend = "st_step = 15";
             $linkstart = "<a href=\"" . $RSDProot . "/application/monitored.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 17: $title = "Application Configuration";
             $previous = "Applications Monitoring";
             $taskpoc = "rsdp_apppoc";
             $tasktitle = "Applications";
             $taskdepend = "st_step = 16";
             $linkstart = "<a href=\"" . $RSDProot . "/application/configured.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
    case 18: $title = "Security Scan";
             $previous = "Applications Configuration";
             $taskpoc = "rsdp_platformspoc";
             $tasktitle = "Security Scan";
             $taskdepend = "st_step = 17";
             $linkstart = "<a href=\"" . $RSDProot . "/infosec/scanned.php?rsdp=";
             $rsdplabel = "";
             $tasklabel = "";
             break;
  }
?>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><?php print $title; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="display:none">

<div class="main-help ui-widget-content">

<h2>RSDP Report</h2>

<p>This page shows which RSDP projects are waiting on the <?php print $title; ?> task to be completed.</p>

<ul>
  <li><strong>RSDP ID</strong> - This is the RSDP ID. It's a link to the specific task as well so clicking here will put you on that task.</li>
  <li><strong>Requestor</strong> - The overall person requesting this server be built.</li>
  <li><strong>Resouce</strong> - Which person or group is responsible for completing this task.</li>
  <li><strong>Project</strong> - The Project this task belongs to.</li>
  <li><strong>Date Initial Task Completed</strong> - The date the previous task was completed (shows how long it's been since this task was ready to be worked).</li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">RSDP ID</th>
  <th class="ui-state-default">Requestor</th>
  <th class="ui-state-default">Resource</th>
  <th class="ui-state-default">Server</th>
  <th class="ui-state-default">Project</th>
  <th class="ui-state-default">Date <?php print $previous; ?> Task Completed</th>
</tr>
<?php

  $q_string  = "select rsdp_id,rsdp_project,rsdp_requestor,rsdp_platformspoc,rsdp_sanpoc,rsdp_networkpoc,";
  $q_string .= "rsdp_virtpoc,rsdp_dcpoc,rsdp_monitorpoc,rsdp_apppoc,rsdp_backuppoc,rsdp_platform,rsdp_application,";
  $q_string .= "os_sysname ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "order by rsdp_id";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

    $link = $linkstart . $a_rsdp_server['rsdp_id'] . $rsdplabel . "\" target=\"_blank\">";

    $q_string  = "select st_id ";
    $q_string .= "from rsdp_status ";
    $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = " . $formVars['id'];
    $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  
    if (mysqli_num_rows($q_rsdp_status) == 0) {

      if ($formVars['id'] == 5 || $formVars['id'] == 6) {
        $q_string  = "select mod_name,mod_virtual ";
        $q_string .= "from rsdp_platform ";
        $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
        $q_string .= "where pf_rsdp = " . $a_rsdp_server['rsdp_id'];
        $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

        if ($formVars['id'] == 5 && $a_rsdp_platform['mod_virtual'] == 1) {
          $q_string  = "select st_timestamp ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and " . $taskdepend;
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if (mysqli_num_rows($q_rsdp_status) == 2) {
            $timestamp = '';
            $and = '';
            while ($a_rsdp_status = mysqli_fetch_array($q_rsdp_status)) {
              $timestamp .= $and . $a_rsdp_status['st_timestamp'];
              $and = " and ";
            }
            print "<tr>\n";
            print "  <td class=\"ui-widget-content\">" . $link . $a_rsdp_server['rsdp_id'] . "</a></td>\n";

            $q_string  = "select usr_last,usr_first ";
            $q_string .= "from users ";
            $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_requestor'];
            $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_users = mysqli_fetch_array($q_users);
  
            print "  <td class=\"ui-widget-content\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</td>\n";

            $taskgroup = $tasktitle;
            if ($a_rsdp_server[$taskpoc] != 0) {
              if ($taskpoc == "rsdp_apppoc") {
                $q_string  = "select grp_name ";
                $q_string .= "from groups ";
                $q_string .= "where grp_id = " . $a_rsdp_server['rsdp_application'];
                $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
                $a_groups = mysqli_fetch_array($q_groups);

                $taskgroup = $a_groups['grp_name'];
              } else {
                $q_string  = "select usr_last,usr_first ";
                $q_string .= "from users ";
                $q_string .= "where usr_id = " . $a_rsdp_server[$taskpoc];
                $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
                $a_users = mysqli_fetch_array($q_users);

                $taskgroup = $a_users['usr_first'] . " " . $a_users['usr_last'];
              }
            }

            print "  <td class=\"ui-widget-content\">" . $taskgroup . "</td>\n";
            print "  <td class=\"ui-widget-content\">" . $a_rsdp_server['os_sysname'] . "</td>\n";

            $q_string  = "select prj_name,prj_code ";
            $q_string .= "from projects ";
            $q_string .= "where prj_id = " . $a_rsdp_server['rsdp_project'];
            $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_projects = mysqli_fetch_array($q_projects);

            print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/network.php?projectid=" . $a_rsdp_server['rsdp_project'] . $tasklabel . "\" target=\"_blank\">" . $a_projects['prj_name'] . " (" . $a_projects['prj_code'] . ")</a></td>\n";

            print "  <td class=\"ui-widget-content\">" . $timestamp . "</td>\n";

            print "</tr>\n";
          }
        }
        if ($formVars['id'] == 6 && $a_rsdp_platform['mod_virtual'] == 0) {
          $q_string  = "select st_timestamp ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and " . $taskdepend;
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

          if (mysqli_num_rows($q_rsdp_status) == 2) {
            $timestamp = '';
            $and = '';
            while ($a_rsdp_status = mysqli_fetch_array($q_rsdp_status)) {
              $timestamp .= $and . $a_rsdp_status['st_timestamp'];
              $and = " and ";
            }
            print "<tr>\n";
            print "  <td class=\"ui-widget-content\">" . $link . $a_rsdp_server['rsdp_id'] . "</a></td>\n";

            $q_string  = "select usr_last,usr_first ";
            $q_string .= "from users ";
            $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_requestor'];
            $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_users = mysqli_fetch_array($q_users);
  
            print "  <td class=\"ui-widget-content\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</td>\n";

            $taskgroup = $tasktitle;
            if ($a_rsdp_server[$taskpoc] != 0) {
              $q_string  = "select usr_last,usr_first ";
              $q_string .= "from users ";
              $q_string .= "where usr_id = " . $a_rsdp_server[$taskpoc];
              $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
              $a_users = mysqli_fetch_array($q_users);
    
              $taskgroup = $a_users['usr_first'] . " " . $a_users['usr_last'];
            }

            print "  <td class=\"ui-widget-content\">" . $taskgroup . "</td>\n";
            print "  <td class=\"ui-widget-content\">" . $a_rsdp_server['os_sysname'] . "</td>\n";

            $q_string  = "select prj_name,prj_code ";
            $q_string .= "from projects ";
            $q_string .= "where prj_id = " . $a_rsdp_server['rsdp_project'];
            $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_projects = mysqli_fetch_array($q_projects);

            print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/network.php?projectid=" . $a_rsdp_server['rsdp_project'] . $tasklabel . "\" target=\"_blank\">" . $a_projects['prj_name'] . " (" . $a_projects['prj_code'] . ")</a></td>\n";

            $q_string  = "select st_timestamp ";
            $q_string .= "from rsdp_status ";
            $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and " . $taskdepend;
            $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

            print "  <td class=\"ui-widget-content\">" . $a_rsdp_status['st_timestamp'] . "</td>\n";

            print "</tr>\n";
          }
        }
      } else {
        $q_string  = "select st_timestamp ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and " . $taskdepend;
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

        if (mysqli_num_rows($q_rsdp_status) > 0) {
          print "<tr>\n";
          print "  <td class=\"ui-widget-content\">" . $link . $a_rsdp_server['rsdp_id'] . "</a></td>\n";

          $q_string  = "select usr_last,usr_first ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_requestor'];
          $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_users = mysqli_fetch_array($q_users);

          print "  <td class=\"ui-widget-content\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</td>\n";

          $taskgroup = $tasktitle;
          if ($a_rsdp_server[$taskpoc] != 0) {
            $q_string  = "select usr_last,usr_first ";
            $q_string .= "from users ";
            $q_string .= "where usr_id = " . $a_rsdp_server[$taskpoc];
            $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_users = mysqli_fetch_array($q_users);

            $taskgroup = $a_users['usr_first'] . " " . $a_users['usr_last'];
          }
  
          print "  <td class=\"ui-widget-content\">" . $taskgroup . "</td>\n";
          print "  <td class=\"ui-widget-content\">" . $a_rsdp_server['os_sysname'] . "</td>\n";

          $q_string = "select prj_name,prj_code ";
          $q_string .= "from projects ";
          $q_string .= "where prj_id = " . $a_rsdp_server['rsdp_project'];
          $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_projects = mysqli_fetch_array($q_projects);

          print "  <td class=\"ui-widget-content\"><a href=\"" . $RSDProot . "/network.php?projectid=" . $a_rsdp_server['rsdp_project'] . $tasklabel . "\" target=\"_blank\">" . $a_projects['prj_name'] . " (" . $a_projects['prj_code'] . ")</a></td>\n";

          $q_string  = "select st_timestamp ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and " . $taskdepend;
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

          print "  <td class=\"ui-widget-content\">" . $a_rsdp_status['st_timestamp'] . "</td>\n";

          print "</tr>\n";
        }
      }
    }

  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
