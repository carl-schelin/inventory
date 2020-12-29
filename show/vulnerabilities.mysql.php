<?php
# Script: vulnerabilities.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "vulnerabilities.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $top  = "<p></p>";
  $top .= "<table class=\"ui-styled-table\">";
  $top .= "<tr>";
  $top .= "  <th class=\"ui-state-default\">Vulnerability Listing</th>";
  $top .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('vulnerabilities-help');\">Help</a></th>";
  $top .= "</tr>";
  $top .= "</table>";

  $top .= "<div id=\"vulnerabilities-help\" style=\"display: none\">";

  $top .= "<div class=\"main-help ui-widget-content\">";

  $top .= "<p>This page shows all vulnerabilities identified for this server or device. The vulnerability listing is extracted from Security Center weekly and imported into the Inventory. Only system IPs that ";
  $top .= "exist in the Inventory will have vulnerabilities associated with it.</p>";
  $top .= "<ul>";
  $top .= "  <li><strong>ID</strong> - This is the Security Center Vulnerability ID. You can get more details about the vulnerability by logging in to Security Center, searching for the IP, and reviewing the ID.</li>";
  $top .= "  <li><strong>Server</strong> - The primary Server name as recorded in the Inventory.</li>";
  $top .= "  <li><strong>Interface</strong> - The hostname or DNS name of the IP that's associated with the primary Server name.</li>";
  $top .= "  <li><strong>IP Addr</strong> - The IP address.</li>";
  $top .= "  <li><strong>Vulnerability</strong> - A description of the vulnerability.</li>";
  $top .= "  <li><strong>Custodian</strong> - The group that is responsible at the system level such as Operating System for servers.</li>";
  $top .= "  <li><strong>Responsible</strong> - The group that has been identified as responsible for correcting the Vulnerability.</li>";
  $top .= "  <li><strong>Ticket</strong> - If a ticket was created, the ticket number. Note that the automatic ticket process was unable to retrieve the ticket number so WO000000 was recorded as the ticket number. ";
  $top .= "This pseudo ticket number indicates a ticket was created though. Blank indicates no ticket was created.</li>";
  $top .= "  <li><strong>Detail</strong> - Any details that were entered in for this ticket by the Custodian.</li>";
  $top .= "</ul>";

  $top .= "<p><strong>Note:</strong> No vulnerabilities doesn't necessarily mean there were no vulnerabilities discovered for the server. Due to the nature of the environment, not all systems may be scanned.</p>\n";

  $top .= "</div>";

  $top .= "</div>";

  print "document.getElementById('vulnerability_mysql').innerHTML = '" . mysqli_real_escape_string($db, $top) . "';\n\n";

  $header  = "<table class=\"ui-styled-table\">\n";
  $header .= "<tr>\n";
  $header .= "  <th class=\"ui-state-default\">Id</th>\n";
  $header .= "  <th class=\"ui-state-default\">Interface</th>\n";
  $header .= "  <th class=\"ui-state-default\">IP Addr</th>\n";
  $header .= "  <th class=\"ui-state-default\">Vulnerability</th>\n";
  $header .= "  <th class=\"ui-state-default\">Seen</th>\n";
  $header .= "  <th class=\"ui-state-default\">Custodian</th>\n";
  $header .= "  <th class=\"ui-state-default\">Responsible</th>\n";
  $header .= "  <th class=\"ui-state-default\">Ticket</th>\n";
  $header .= "  <th class=\"ui-state-default\">Detail</th>\n";
  $header .= "</tr>\n";

# critical vulnerabilities
  $output = '';
  $product = '';
  $count = 0;
  $q_string  = "select int_id,int_server,int_addr ";
  $q_string .= "from interface ";
  $q_string .= "where int_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by int_server ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $q_string  = "select vuln_interface,vuln_securityid,vuln_duplicate,vuln_date,grp_name,sev_name,sec_name ";
      $q_string .= "from vulnerabilities "; 
      $q_string .= "left join a_groups    on vulnerabilities.vuln_group = a_groups.grp_id ";
      $q_string .= "left join security  on security.sec_id            = vulnerabilities.vuln_securityid ";
      $q_string .= "left join severity  on severity.sev_id            = security.sec_severity ";
      $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 1 and vuln_delete = 0 ";
      $q_string .= "order by vuln_securityid ";
      $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities)) {

        $count++;
        $q_string  = "select vul_id,grp_name,vul_ticket,vul_exception,vul_description ";
        $q_string .= "from vulnowner "; 
        $q_string .= "left join a_groups on a_groups.grp_id = vulnowner.vul_group ";
        $q_string .= "where vul_security = " . $a_vulnerabilities['vuln_securityid'] . " and vul_interface = " . $a_vulnerabilities['vuln_interface'] . " ";
        $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_vulnowner) > 0) {
          $a_vulnowner = mysqli_fetch_array($q_vulnowner);
        } else {
          $a_vulnowner['vul_id'] = 0;
          $a_vulnowner['grp_name'] = '';
          $a_vulnowner['vul_ticket'] = '';
          $a_vulnowner['vul_exception'] = '';
          $a_vulnowner['vul_description'] = '';
        }

        if ($product != $a_vulnerabilities['prod_name']) {
          $output   .= "<tr>";
          $output .= "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_vulnerabilities['prod_name'] . "</th>\n";
          $output   .= "</tr>";
          $product = $a_vulnerabilities['prod_name'];
        }

        if ($a_vulnerabilities['vuln_securityid'] < 10000) {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nnm/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        } else {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nessus/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        }
        $linkend = "</a>";
        $class = "ui-widget-content";

        $duplicate = '';
        if ($a_vulnerabilities['vuln_duplicate']) {
          $duplicate = "< ";
          $class = "ui-widget-content";
        }

        $output   .= "<tr>";
        $output   .= "  <td class=\"" . $class . " delete\">" . $nessus . $a_vulnerabilities['vuln_securityid'] . $linkend . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_server']            . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_addr']              . "</td>";
        $output   .= "  <td class=\"" . $class . "\">" . $duplicate . $a_vulnerabilities['sec_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['vuln_date']       . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['grp_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['grp_name']              . "</td>";
        if ($a_vulnowner['vul_exception']) {
          $output   .= "  <td class=\"" . $class . "\">"            . "Exception"                           . "</td>";
        } else {
          $output   .= "  <td class=\"" . $class . "\">"            . $a_vulnowner['vul_ticket']            . "</td>";
        }
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['vul_description']       . "</td>";
        $output   .= "</tr>";
      }
    }
  } else {
    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No interface records found</td>";
    $output .= "</tr>";
  }

  if ($count == 0) {
    $output .= "<tr>\n";
    $output .= "<td class=\"ui-widget-content\" colspan=\"10\">No Critical vulnerabilities found.</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>";

  print "document.getElementById('critical_count').innerHTML = ' (" . mysqli_real_escape_string($db, $count) . ")';\n\n";
  print "document.getElementById('critical_vuln_mysql').innerHTML = '" . mysqli_real_escape_string($db, $header . $output) . "';\n\n";


# high vulnerabilities
  $output = '';
  $product = '';
  $count = 0;
  $q_string  = "select int_id,int_server,int_addr ";
  $q_string .= "from interface ";
  $q_string .= "where int_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by int_server ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $q_string  = "select vuln_interface,vuln_securityid,vuln_duplicate,vuln_date,grp_name,sev_name,sec_name ";
      $q_string .= "from vulnerabilities "; 
      $q_string .= "left join a_groups    on vulnerabilities.vuln_group = a_groups.grp_id ";
      $q_string .= "left join security  on security.sec_id            = vulnerabilities.vuln_securityid ";
      $q_string .= "left join severity  on severity.sev_id            = security.sec_severity ";
      $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 2 and vuln_delete = 0 ";
      $q_string .= "order by vuln_securityid ";
      $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities)) {

        $count++;
        $q_string  = "select vul_id,grp_name,vul_ticket,vul_exception,vul_description ";
        $q_string .= "from vulnowner "; 
        $q_string .= "left join a_groups on a_groups.grp_id = vulnowner.vul_group ";
        $q_string .= "where vul_security = " . $a_vulnerabilities['vuln_securityid'] . " and vul_interface = " . $a_vulnerabilities['vuln_interface'] . " ";
        $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_vulnowner) > 0) {
          $a_vulnowner = mysqli_fetch_array($q_vulnowner);
        } else {
          $a_vulnowner['vul_id'] = 0;
          $a_vulnowner['grp_name'] = '';
          $a_vulnowner['vul_ticket'] = '';
          $a_vulnowner['vul_exception'] = '';
          $a_vulnowner['vul_description'] = '';
        }

        if ($product != $a_vulnerabilities['prod_name']) {
          $output   .= "<tr>";
          $output .= "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_vulnerabilities['prod_name'] . "</th>\n";
          $output   .= "</tr>";
          $product = $a_vulnerabilities['prod_name'];
        }

        if ($a_vulnerabilities['vuln_securityid'] < 10000) {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nnm/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        } else {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nessus/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        }
        $linkend = "</a>";
        $class = "ui-widget-content";

        $duplicate = '';
        if ($a_vulnerabilities['vuln_duplicate']) {
          $duplicate = "< ";
          $class = "ui-widget-content";
        }

        $output   .= "<tr>";
        $output   .= "  <td class=\"" . $class . " delete\">" . $nessus . $a_vulnerabilities['vuln_securityid'] . $linkend . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_server']            . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_addr']              . "</td>";
        $output   .= "  <td class=\"" . $class . "\">" . $duplicate . $a_vulnerabilities['sec_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['vuln_date']       . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['grp_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['grp_name']              . "</td>";
        if ($a_vulnowner['vul_exception']) {
          $output   .= "  <td class=\"" . $class . "\">"            . "Exception"                           . "</td>";
        } else {
          $output   .= "  <td class=\"" . $class . "\">"            . $a_vulnowner['vul_ticket']            . "</td>";
        }
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['vul_description']       . "</td>";
        $output   .= "</tr>";
      }

    }
  } else {
    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No interface records found</td>";
    $output .= "</tr>";
  }

  if ($count == 0) {
    $output .= "<tr>\n";
    $output .= "<td class=\"ui-widget-content\" colspan=\"10\">No High vulnerabilities found.</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>";

  print "document.getElementById('high_count').innerHTML = ' (" . mysqli_real_escape_string($db, $count) . ")';\n\n";
  print "document.getElementById('high_vuln_mysql').innerHTML = '" . mysqli_real_escape_string($db, $header . $output) . "';\n\n";


# medium vulnerabilities
  $output = '';
  $product = '';
  $count = 0;
  $q_string  = "select int_id,int_server,int_addr ";
  $q_string .= "from interface ";
  $q_string .= "where int_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by int_server ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $q_string  = "select vuln_interface,vuln_securityid,vuln_duplicate,vuln_date,grp_name,sev_name,sec_name ";
      $q_string .= "from vulnerabilities "; 
      $q_string .= "left join a_groups    on vulnerabilities.vuln_group = a_groups.grp_id ";
      $q_string .= "left join security  on security.sec_id            = vulnerabilities.vuln_securityid ";
      $q_string .= "left join severity  on severity.sev_id            = security.sec_severity ";
      $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 3 and vuln_delete = 0 ";
      $q_string .= "order by vuln_securityid ";
      $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities)) {

        $count++;
        $q_string  = "select vul_id,grp_name,vul_ticket,vul_exception,vul_description ";
        $q_string .= "from vulnowner "; 
        $q_string .= "left join a_groups on a_groups.grp_id = vulnowner.vul_group ";
        $q_string .= "where vul_security = " . $a_vulnerabilities['vuln_securityid'] . " and vul_interface = " . $a_vulnerabilities['vuln_interface'] . " ";
        $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_vulnowner) > 0) {
          $a_vulnowner = mysqli_fetch_array($q_vulnowner);
        } else {
          $a_vulnowner['vul_id'] = 0;
          $a_vulnowner['grp_name'] = '';
          $a_vulnowner['vul_ticket'] = '';
          $a_vulnowner['vul_exception'] = '';
          $a_vulnowner['vul_description'] = '';
        }

        if ($product != $a_vulnerabilities['prod_name']) {
          $output   .= "<tr>";
          $output .= "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_vulnerabilities['prod_name'] . "</th>\n";
          $output   .= "</tr>";
          $product = $a_vulnerabilities['prod_name'];
        }

        if ($a_vulnerabilities['vuln_securityid'] < 10000) {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nnm/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        } else {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nessus/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        }
        $linkend = "</a>";
        $class = "ui-widget-content";

        $duplicate = '';
        if ($a_vulnerabilities['vuln_duplicate']) {
          $duplicate = "< ";
          $class = "ui-widget-content";
        }

        $output   .= "<tr>";
        $output   .= "  <td class=\"" . $class . " delete\">" . $nessus . $a_vulnerabilities['vuln_securityid'] . $linkend . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_server']            . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_addr']              . "</td>";
        $output   .= "  <td class=\"" . $class . "\">" . $duplicate . $a_vulnerabilities['sec_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['vuln_date']       . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['grp_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['grp_name']              . "</td>";
        if ($a_vulnowner['vul_exception']) {
          $output   .= "  <td class=\"" . $class . "\">"            . "Exception"                           . "</td>";
        } else {
          $output   .= "  <td class=\"" . $class . "\">"            . $a_vulnowner['vul_ticket']            . "</td>";
        }
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['vul_description']       . "</td>";
        $output   .= "</tr>";
      }

    }
  } else {
    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No interface records found</td>";
    $output .= "</tr>";
  }

  if ($count == 0) {
    $output .= "<tr>\n";
    $output .= "<td class=\"ui-widget-content\" colspan=\"10\">No Medium vulnerabilities found.</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>";

  print "document.getElementById('medium_count').innerHTML = ' (" . mysqli_real_escape_string($db, $count) . ")';\n\n";
  print "document.getElementById('medium_vuln_mysql').innerHTML = '" . mysqli_real_escape_string($db, $header . $output) . "';\n\n";


# low vulnerabilities
  $output = '';
  $product = '';
  $count = 0;
  $q_string  = "select int_id,int_server,int_addr ";
  $q_string .= "from interface ";
  $q_string .= "where int_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by int_server ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $q_string  = "select vuln_interface,vuln_securityid,vuln_duplicate,vuln_date,grp_name,sev_name,sec_name ";
      $q_string .= "from vulnerabilities "; 
      $q_string .= "left join a_groups    on vulnerabilities.vuln_group = a_groups.grp_id ";
      $q_string .= "left join security  on security.sec_id            = vulnerabilities.vuln_securityid ";
      $q_string .= "left join severity  on severity.sev_id            = security.sec_severity ";
      $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 4 and vuln_delete = 0 ";
      $q_string .= "order by vuln_securityid ";
      $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities)) {

        $count++;
        $q_string  = "select vul_id,grp_name,vul_ticket,vul_exception,vul_description ";
        $q_string .= "from vulnowner "; 
        $q_string .= "left join a_groups on a_groups.grp_id = vulnowner.vul_group ";
        $q_string .= "where vul_security = " . $a_vulnerabilities['vuln_securityid'] . " and vul_interface = " . $a_vulnerabilities['vuln_interface'] . " ";
        $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_vulnowner) > 0) {
          $a_vulnowner = mysqli_fetch_array($q_vulnowner);
        } else {
          $a_vulnowner['vul_id'] = 0;
          $a_vulnowner['grp_name'] = '';
          $a_vulnowner['vul_ticket'] = '';
          $a_vulnowner['vul_exception'] = '';
          $a_vulnowner['vul_description'] = '';
        }

        if ($product != $a_vulnerabilities['prod_name']) {
          $output   .= "<tr>";
          $output .= "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_vulnerabilities['prod_name'] . "</th>\n";
          $output   .= "</tr>";
          $product = $a_vulnerabilities['prod_name'];
        }

        if ($a_vulnerabilities['vuln_securityid'] < 10000) {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nnm/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        } else {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nessus/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        }
        $linkend = "</a>";
        $class = "ui-widget-content";

        $duplicate = '';
        if ($a_vulnerabilities['vuln_duplicate']) {
          $duplicate = "< ";
          $class = "ui-widget-content";
        }

        $output   .= "<tr>";
        $output   .= "  <td class=\"" . $class . " delete\">" . $nessus . $a_vulnerabilities['vuln_securityid'] . $linkend . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_server']            . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_addr']              . "</td>";
        $output   .= "  <td class=\"" . $class . "\">" . $duplicate . $a_vulnerabilities['sec_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['vuln_date']       . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['grp_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['grp_name']              . "</td>";
        if ($a_vulnowner['vul_exception']) {
          $output   .= "  <td class=\"" . $class . "\">"            . "Exception"                           . "</td>";
        } else {
          $output   .= "  <td class=\"" . $class . "\">"            . $a_vulnowner['vul_ticket']            . "</td>";
        }
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['vul_description']       . "</td>";
        $output   .= "</tr>";
      }

    }
  } else {
    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No interface records found</td>";
    $output .= "</tr>";
  }

  if ($count == 0) {
    $output .= "<tr>\n";
    $output .= "<td class=\"ui-widget-content\" colspan=\"10\">No Low vulnerabilities found.</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>";

  print "document.getElementById('low_count').innerHTML = ' (" . mysqli_real_escape_string($db, $count) . ")';\n\n";
  print "document.getElementById('low_vuln_mysql').innerHTML = '" . mysqli_real_escape_string($db, $header . $output) . "';\n\n";


# info vulnerabilities
  $output = '';
  $product = '';
  $count = 0;
  $q_string  = "select int_id,int_server,int_addr ";
  $q_string .= "from interface ";
  $q_string .= "where int_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by int_server ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $q_string  = "select vuln_interface,vuln_securityid,vuln_duplicate,vuln_date,grp_name,sev_name,sec_name ";
      $q_string .= "from vulnerabilities "; 
      $q_string .= "left join a_groups    on vulnerabilities.vuln_group = a_groups.grp_id ";
      $q_string .= "left join security  on security.sec_id            = vulnerabilities.vuln_securityid ";
      $q_string .= "left join severity  on severity.sev_id            = security.sec_severity ";
      $q_string .= "where vuln_interface = " . $a_interface['int_id'] . " and sec_severity = 5 and vuln_delete = 0 ";
      $q_string .= "order by vuln_securityid ";
      $q_vulnerabilities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_vulnerabilities = mysqli_fetch_array($q_vulnerabilities)) {

        $count++;
        $q_string  = "select vul_id,grp_name,vul_ticket,vul_exception,vul_description ";
        $q_string .= "from vulnowner "; 
        $q_string .= "left join a_groups on a_groups.grp_id = vulnowner.vul_group ";
        $q_string .= "where vul_security = " . $a_vulnerabilities['vuln_securityid'] . " and vul_interface = " . $a_vulnerabilities['vuln_interface'] . " ";
        $q_vulnowner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_vulnowner) > 0) {
          $a_vulnowner = mysqli_fetch_array($q_vulnowner);
        } else {
          $a_vulnowner['vul_id'] = 0;
          $a_vulnowner['grp_name'] = '';
          $a_vulnowner['vul_ticket'] = '';
          $a_vulnowner['vul_exception'] = '';
          $a_vulnowner['vul_description'] = '';
        }

        if ($product != $a_vulnerabilities['prod_name']) {
          $output   .= "<tr>";
          $output .= "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_vulnerabilities['prod_name'] . "</th>\n";
          $output   .= "</tr>";
          $product = $a_vulnerabilities['prod_name'];
        }

        if ($a_vulnerabilities['vuln_securityid'] < 10000) {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nnm/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        } else {
          $nessus = "<a href=\"https://www.tenable.com/plugins/nessus/" . $a_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
        }
        $linkend = "</a>";
        $class = "ui-widget-content";

        $duplicate = '';
        if ($a_vulnerabilities['vuln_duplicate']) {
          $duplicate = "< ";
          $class = "ui-widget-content";
        }

        $output   .= "<tr>";
        $output   .= "  <td class=\"" . $class . " delete\">" . $nessus . $a_vulnerabilities['vuln_securityid'] . $linkend . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_server']            . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_interface['int_addr']              . "</td>";
        $output   .= "  <td class=\"" . $class . "\">" . $duplicate . $a_vulnerabilities['sec_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['vuln_date']       . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnerabilities['grp_name']        . "</td>";
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['grp_name']              . "</td>";
        if ($a_vulnowner['vul_exception']) {
          $output   .= "  <td class=\"" . $class . "\">"            . "Exception"                           . "</td>";
        } else {
          $output   .= "  <td class=\"" . $class . "\">"            . $a_vulnowner['vul_ticket']            . "</td>";
        }
        $output   .= "  <td class=\"" . $class . "\">"              . $a_vulnowner['vul_description']       . "</td>";
        $output   .= "</tr>";
      }

    }
  } else {
    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No interface records found</td>";
    $output .= "</tr>";
  }

  if ($count == 0) {
    $output .= "<tr>\n";
    $output .= "<td class=\"ui-widget-content\" colspan=\"10\">No Info vulnerabilities found. As every system generally has at least 1 'Info' vulnerability, it's likely this system hasn't been scanned.</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>";

  $mysql = $header . $output;

  print "document.getElementById('info_count').innerHTML = ' (" . mysqli_real_escape_string($db, $count) . ")';\n\n";
  print "document.getElementById('info_vuln_mysql').innerHTML = '" . mysqli_real_escape_string($db, $mysql) . "';\n\n";

  mysqli_free_result($q_interface);


?>
