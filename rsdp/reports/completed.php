<?php
# Script: completed.php
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

  $package = "completed.php";

  logaccess($db, $_SESSION['uid'], $package, "Showing completed server listing");

  if (isset($_GET['user'])) {
    $formVars['user'] = clean($_GET['user'], 30);
  } else {
    $formVars['user'] = '';
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Rapid Server Deployment Process</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Completed RSDP Servers</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('completed-listing-help');">Help</a></th>
</tr>
</table>

<div id="completed-listing-help" style="display: none">

<div class="main-help ui-widget-content">

<p>The chart below lists all the servers where the server build tasks have been completed.</p>

<p>Tasks <span class="ui-state-error">highlighted</span> are tasks that were asked to be closed by the 
Reqestor generally because the work had been completed outside of the RSDP process. This will skew 
task completion charts due to the tasks all being identified as completed in a short period of time.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="21">Server Build Task</th>
</tr>
<tr>
  <th class="ui-state-default" colspan="2">Project</th>
  <th class="ui-state-default">Server</th>
  <th class="ui-state-default">SAN</th>
  <th class="ui-state-default">Net</th>
  <th class="ui-state-default" colspan="5">Data Center</th>
  <th class="ui-state-default">OS</th>
  <th class="ui-state-default">SAN</th>
  <th class="ui-state-default">OS</th>
  <th class="ui-state-default">Backup</th>
  <th class="ui-state-default">Sys</th>
  <th class="ui-state-default" colspan="3">Application</th>
  <th class="ui-state-default">Sec</th>
</tr>
<tr>
  <th class="ui-state-default">ID</th>
  <th class="ui-state-default" title="Server Request Page">Requestor</th>
  <th class="ui-state-default" title="Server Ordered Page">Designed</th>
  <th class="ui-state-default" title="SAN Designed">Des</th>
  <th class="ui-state-default" title="Network Designed">Des</th>
  <th class="ui-state-default" title="Data Center Prep Page">Prep</th>
  <th class="ui-state-default" title="Data Center Quoted Page">Quote</th>
  <th class="ui-state-default" title="Data Center Verified">Ver</th>
  <th class="ui-state-default" title="Data Center Received Page">Recv</th>
  <th class="ui-state-default" title="Data Center Installed and Cabled Page">Inst</th>
  <th class="ui-state-default" title="OS Installed Page: Server">Inst</th>
  <th class="ui-state-default" title="SAN Provisioned Page">Prov</th>
  <th class="ui-state-default" title="OS Configured Page: Server">Conf</th>
  <th class="ui-state-default" title="Backups Configured Page">Conf</th>
  <th class="ui-state-default" title="HW/OS Monitoring Configured Page">Mon</th>
  <th class="ui-state-default" title="Application Installed Page">Inst</th>
  <th class="ui-state-default" title="Application Monitoring Page">Mon</th>
  <th class="ui-state-default" title="Application Configured Page">Conf</th>
  <th class="ui-state-default" title="InfoSec Scan Page">Scan</th>
</tr>
<?php

  $rsdptask[1]  = $RSDProot . "/build/initial.php";
  $rsdptask[2]  = $RSDProot . "/build/build.php";
  $rsdptask[3]  = $RSDProot . "/san/designed.php";
  $rsdptask[4]  = $RSDProot . "/network/network.php";
  $rsdptask[5]  = $RSDProot . "/physical/physical.php";
  $rsdptask[6]  = $RSDProot . "/virtual/virtual.php";
  $rsdptask[7]  = $RSDProot . "/physical/physical.php";
  $rsdptask[8]  = $RSDProot . "/physical/physical.php";
  $rsdptask[9]  = $RSDProot . "/physical/physical.php";
  $rsdptask[10] = $RSDProot . "/system/installed.php";
  $rsdptask[11] = $RSDProot . "/san/provisioned.php";
  $rsdptask[12] = $RSDProot . "/system/configured.php";
  $rsdptask[13] = $RSDProot . "/backups/backups.php";
  $rsdptask[14] = $RSDProot . "/monitoring/monitoring.php";
  $rsdptask[15] = $RSDProot . "/application/installed.php";
  $rsdptask[16] = $RSDProot . "/application/monitored.php";
  $rsdptask[17] = $RSDProot . "/application/configured.php";
  $rsdptask[18] = $RSDProot . "/infosec/scanned.php";

  $q_string  = "select rsdp_id,usr_last,prj_code,rsdp_complete,os_sysname,st_user ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join rsdp_status on rsdp_status.st_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "left join projects on projects.prj_id = rsdp_server.rsdp_project ";
  $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "where st_step = 18 ";
  if ($formVars['user'] != '') {
    $q_string .= "and st_user = " . $formVars['user'] . " ";
  }
  $q_string .= "order by rsdp_created ";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_server) > 0) {
    while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

      if ($a_rsdp_server['st_user'] == 1) {
        $class = "ui-state-error";
      } else {
        $class = "ui-widget-content";
      }

      print "<tr>\n";

      for ($i = 1; $i < 19; $i++) {
        $q_string  = "select st_id,st_completed,st_timestamp,st_user ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = " . $i . " ";
        $q_string .= "order by st_step ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

        $linkstart = "<a href=\"" . $rsdptask[$i] . "?rsdp=" . $a_rsdp_server['rsdp_id'] . "\" target=\"_blank\">";
        $userstart = "<a href=\"completed.php?user=" . $a_rsdp_server['st_user'] . "\" target=\"_blank\">";
        $linkend = "</a>";

        $text = 'Work';
        if ($a_rsdp_status['st_completed'] == 1) {
          $text = 'Done';
        } else {
          if ($a_rsdp_status['st_completed'] == 2) {
            $text = 'Skip';
          }
        }

        if ($i == 1) {
          print "  <td class=\"" . $class . " rsdp\">" . $linkstart . $a_rsdp_server['rsdp_id'] . $linkend . "</td>\n";
          print "  <td class=\"" . $class . " rsdp\">" . $linkstart . $a_rsdp_server['usr_last'] . $linkend . " " . $userstart . "Filter" . $linkend . "</td>\n";
        }

        if ($i == 2) {
          if ($a_rsdp_server['os_sysname'] == '') {
            print "  <td class=\"" . $class . " rsdp\">" . $linkstart . "Unnamed" . $linkend . "</td>\n";
          } else {
            print "  <td class=\"" . $class . " rsdp\">" . $linkstart . $a_rsdp_server['os_sysname'] . $linkend . "</td>\n";
          }
        }

        if ($i > 2) {
          if ($i > 4 && $i < 10) {
            if (rsdp_Virtual($a_rsdp_server['rsdp_id'])) {
              if ($i == 5) {
                print "  <td colspan=\"5\" class=\"" . $class . " rsdp\"><a href=\"" . $rsdptask[6] . "\">Virtual Machine</a></td>\n";
              }
            } else {
              print "  <td class=\"" . $class . " rsdp\">" . $linkstart . "Done" . $linkend . "</td>\n";
            }
          } else {
            print "  <td class=\"" . $class . " rsdp\">" . $linkstart . $text . $linkend . "</td>\n";
          }
        }
      }
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content rsdp\" colspan=\"19\">No Servers found.</td>\n";
    print "</tr>\n";
  }
?>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
