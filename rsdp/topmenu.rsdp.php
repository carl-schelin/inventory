<?php
  $q_string  = "select rsdp_project ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdpmenu = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_rsdpmenu) > 0) {
    $a_rsdpmenu = mysql_fetch_array($q_rsdpmenu);

    print "  <li><a href=\"" . $RSDProot . "/servers.php?projectid="             . $a_rsdpmenu['rsdp_project'] . "\">" . $task[0] . "RSDP Servers</a>\n";
    print "    <ul>\n";
    print "    <li><a href=\"" . $RSDProot . "/tasks.php?id="                    . $formVars['rsdp'] . "\">RSDP Tasks</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/build/initial.php?rsdp="          . $formVars['rsdp'] . "\">" . $task[1] . "Server Initialization</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/build/build.php?rsdp="            . $formVars['rsdp'] . "\">" . $task[2] . "System Provisioning</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/san/designed.php?rsdp="           . $formVars['rsdp'] . "\">" . $task[3] . "SAN Design</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/network/network.php?rsdp="        . $formVars['rsdp'] . "\">" . $task[4] . "Network Configuration</a></li>\n";

    if (rsdp_Virtual($formVars['rsdp'])) {
      print "    <li><a href=\"" . $RSDProot . "/virtual/virtual.php?rsdp="      . $formVars['rsdp'] . "\">" . $task[5] . "Virtualization</a></li>\n";
    } else {
      print "    <li><a href=\"" . $RSDProot . "/physical/physical.php?rsdp="    . $formVars['rsdp'] . "\">" . $task[5] . "Data Center</a></li>\n";
    }

    print "    <li><a href=\"" . $RSDProot . "/system/installed.php?rsdp="       . $formVars['rsdp'] . "\">" . $task[10] . "System Installation</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/san/provisioned.php?rsdp="        . $formVars['rsdp'] . "\">" . $task[11] . "SAN Provisioning</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/system/configured.php?rsdp="      . $formVars['rsdp'] . "\">" . $task[12] . "System Configuration</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/backups/backups.php?rsdp="        . $formVars['rsdp'] . "\">" . $task[13] . "System Backups</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/monitoring/monitoring.php?rsdp="  . $formVars['rsdp'] . "\">" . $task[14] . "Monitoring Configuration</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/application/installed.php?rsdp="  . $formVars['rsdp'] . "\">" . $task[15] . "Application Installed</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/application/monitored.php?rsdp="  . $formVars['rsdp'] . "\">" . $task[16] . "Monitoring Complete</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/application/configured.php?rsdp=" . $formVars['rsdp'] . "\">" . $task[17] . "Application Configured</a></li>\n";
    print "    <li><a href=\"" . $RSDProot . "/infosec/scanned.php?rsdp="        . $formVars['rsdp'] . "\">" . $task[18] . "InfoSec Completed</a></li>\n";
    print "    </ul>\n";
    print "  </li>\n";
  }
?>
