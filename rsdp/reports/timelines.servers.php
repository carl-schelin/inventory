<?php
# Script: timelines.servers.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "servers.mysql.php";

    if (check_userlevel(2)) {
      if (isset($_GET['start'])) {
        $formVars['start'] = clean($_GET['start'], 15);
      } else {
        $formVars['start'] = '2014-01-01';
      }
      if (isset($_GET['end'])) {
        $formVars['end'] = clean($_GET['end'], 15);
      } else {
        $formVars['end'] = '2014-12-31';
      }
      $where = "where rsdp_created >= '" . $formVars['start'] . "' and rsdp_created <= '" . $formVars['end'] . "' ";

      if (isset($_GET['group'])) {
        $formVars['group'] = clean($_GET['group'], 10);
      } else {
        $formVars['group'] = 0;
      }

      if ($formVars['group'] > 0) {
        $where .= "and grp_id = " . $formVars['group'] . " ";
      }

      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Server Graph Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('server-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"server-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Project</strong> - The name of the Project.</li>\n";
      $output .= "  <li><strong>Product</strong> - The primary Product this Project is building servers for.</li>\n";
      $output .= "  <li><strong>Server</strong> - The server in work.</li>\n";
      $output .= "  <li><strong>Tasks Completed</strong> - How many of the tasks have been completed.</li>\n";
      if (check_userlevel(1)) {
        $output .= "  <li><strong>Delete Server</strong> - This deletes all data for this server.</li>\n";
        $output .= "  <li><strong>Close Tasks</strong> - This marks the server build as complete, closing all tasks.</li>\n";
      }
      $output .= "</ul>\n";

      $output .= "<p>Servers that are <span class=\"ui-state-highlight\">highlighted</span> have been completed.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Server</th>\n";
      $output .= "  <th class=\"ui-state-default\">Project</th>\n";
      $output .= "  <th class=\"ui-state-default\">Product</th>\n";
      $output .= "  <th class=\"ui-state-default\">Tasks Completed</th>\n";
      if (check_userlevel(1)) {
        $output .= "  <th class=\"ui-state-default\">Complete</th>\n";
        $output .= "  <th class=\"ui-state-default\">Delete</th>\n";
      }
      $output .= "</tr>\n";

      $q_string  = "select rsdp_id,prod_id,rsdp_product,prod_name,os_sysname,prj_name,prj_code,rsdp_requestor,rsdp_platformspoc,";
      $q_string .= "rsdp_sanpoc,rsdp_networkpoc,rsdp_virtpoc,rsdp_dcpoc,rsdp_srpoc,rsdp_monitorpoc,rsdp_apppoc,rsdp_backuppoc ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join products on products.prod_id = rsdp_server.rsdp_product ";
      $q_string .= "left join projects on projects.prj_id = rsdp_server.rsdp_project ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
      $q_string .= "left join groups on groups.grp_id = users.usr_group ";
      $q_string .= $where;
      $q_string .= "group by os_sysname ";
      $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_rsdp_server = mysql_fetch_array($q_rsdp_server)) {

        $linkstart = "<a href=\"" . $RSDProot . "/tasks.php?id=" . $a_rsdp_server['rsdp_id'] . "&myrsdp=" . $formVars['myrsdp'] . "\">";
        $linkend   = "</a>";
        if (check_userlevel(1)) {
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('" . $RSDProot . "/servers.del.php?id=" . $a_rsdp_server['rsdp_id'] . "');\">";
          $linkclose = "<a href=\"#\" onclick=\"close_line('" . $RSDProot . "/servers.done.php?id=" . $a_rsdp_server['rsdp_id'] . "');\">";
        }

        if ($a_rsdp_server['prj_name'] == '') {
          $project = 'Unknown';
        } else {
          $project = $a_rsdp_server['prj_name'] . " (" . $a_rsdp_server['prj_code'] . ")";
        }
        if ($a_rsdp_server['prod_name'] == '') {
          $a_rsdp_server['prod_name'] = 'Unknown';
        }
        if ($a_rsdp_server['os_sysname'] == '') {
          $a_rsdp_server['os_sysname'] = 'New Server';
        }

        $q_string  = "select COUNT(*) ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " ";
        $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_rsdp_status = mysql_fetch_array($q_rsdp_status);

        if ($a_rsdp_status['COUNT(*)'] > 13) {
          $class = "ui-state-highlight";
        } else {
          $class = "ui-widget-content";
        }

        $output .= "<tr>\n";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_rsdp_server['os_sysname']          . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $project                              . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_rsdp_server['prod_name']           . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"                     . $a_rsdp_status['COUNT(*)'] . " of 14"            . "</td>\n";
        if (check_userlevel(1)) {
          $output .= "  <td class=\"" . $class . " delete\">" . $linkclose . "Close Tasks"                         . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . " delete\">" . $linkdel   . "Delete Server"                       . $linkend . "</td>\n";
        }
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"" . $class . " delete\" colspan=\"6\"><img src=\"" . $RSDProot . "/reports/timelines.graph.php?rsdp=" . $a_rsdp_server['rsdp_id'] . "\"></td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      print "document.getElementById('server_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
