<?php
# Script: monitoring.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "monitoring.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],       10);
        $formVars['mon_system']   = clean($_GET['mon_system'], 10);
        $formVars['mon_type']     = clean($_GET['mon_type'], 10);
        $formVars['mon_active']   = clean($_GET['mon_active'], 10);
        $formVars['notification'] = clean($_GET['notification'], 10);
        $formVars['mon_group']    = clean($_GET['mon_group'], 10);
        $formVars['mon_user']     = clean($_GET['mon_user'], 10);
        $formVars['mon_notify']   = clean($_GET['mon_notify'], 10);
        $formVars['mon_hours']    = clean($_GET['mon_hours'], 10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['mon_active'] == 'true') {
          $formVars['mon_active'] = 1;
        } else {
          $formVars['mon_active'] = 1;
        }
# select either group notification or user notification
        if ($formVars['notification'] == 0) {
          $formVars['mon_user'] = 0;
        } else {
          $formVars['mon_group'] = 0;
        }
# if email, email is always notify
        if ($formVars['mon_notify'] == 1) {
          $formVars['mon_hours'] = 1;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "mon_system     =   " . $formVars['mon_system']   . "," .
          "mon_type       =   " . $formVars['mon_type']     . "," .
          "mon_active     =   " . $formVars['mon_active']   . "," .
          "mon_group      =   " . $formVars['mon_group']    . "," .
          "mon_user       =   " . $formVars['mon_user']     . "," .
          "mon_notify     =   " . $formVars['mon_notify']   . "," .
          "mon_hours      =   " . $formVars['mon_hours'] ;

        if ($formVars['update'] == 0) {
          $query = "insert into monitoring set org_id = null," . $q_string;
          $message = "Monitoring added.";
        }
        if ($formVars['update'] == 1) {
          $query = "update monitoring set " . $q_string . " where mon_id = " . $formVars['id'];
          $message = "Monitoring updated.";
        }

        logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['mon_system']);

        mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

        print "alert('" . $message . "');\n";
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Monitoring Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('monitoring-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"monitoring-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Monitoring Listing</strong>\n";
      $output .= "  <ul>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this montioring item from the Inventory.</li>\n";
      }
      $output .= "    <li><strong>Editing</strong> - Click on an monitoring to toggle the form and edit the monitoring.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Server</th>\n";
      $output .= "  <th class=\"ui-state-default\">Interface</th>\n";
      $output .= "  <th class=\"ui-state-default\">System</th>\n";
      $output .= "  <th class=\"ui-state-default\">What to monitor</th>\n";
      $output .= "  <th class=\"ui-state-default\">Active?</th>\n";
      $output .= "  <th class=\"ui-state-default\">Group</th>\n";
      $output .= "  <th class=\"ui-state-default\">User</th>\n";
      $output .= "  <th class=\"ui-state-default\">Notification</th>\n";
      $output .= "  <th class=\"ui-state-default\">Hours</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_name,int_server,mon_id,mon_system,mt_name,mon_active,mon_group,grp_name,mon_user,usr_last,usr_first,mon_notify,mon_hours ";
      $q_string .= "from monitoring ";
      $q_string .= "left join interface on interface.int_id = monitoring.mon_interfaceid ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "left join mon_type on mon_type.mt_id = monitoring.mon_type ";
      $q_string .= "left join a_groups on a_groups.grp_id = monitoring.mon_group ";
      $q_string .= "left join users on users.usr_id = monitoring.mon_user ";
      $q_string .= "where inv_status = 0 ";
      $q_string .= "order by inv_name,int_server ";
      $q_monitoring = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_monitoring) > 0) {
        while ($a_monitoring = mysqli_fetch_array($q_monitoring)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('monitoring.fill.php?id=" . $a_monitoring['mon_id'] . "');jQuery('#dialogMonitoring').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('monitoring.del.php?id=" . $a_monitoring['mon_id'] . "');\">";
          $linkend   = "</a>";

          $system = "Openview";
          if ($a_monitoring['mon_system']) {
            $system = "Nagios";
          }
          $active = "No";
          if ($a_monitoring['mon_active']) {
            $active = "Yes";
          }
          $notify = "None";
          if ($a_monitoring['mon_notify'] == 1) {
            $notify = "Email";
          }
          if ($a_monitoring['mon_notify'] == 2) {
            $notify = "Page";
          }
          $hours = "Business Day";
          if ($a_monitoring['mon_hours']) {
            $hours = "24x7";
          }

          $group = "None";
          if ($a_monitoring['mon_group'] > 0) {
            $group = $a_monitoring['grp_name'];
          }
          $user = "None";
          if ($a_monitoring['mon_user'] > 0) {
            $user = $a_monitoring['usr_last'] . ", " . $a_monitoring['usr_first'];
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\" width=\"60\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_monitoring['mon_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_monitoring['inv_name'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_monitoring['int_server'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $system . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_monitoring['mt_name'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $active . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $group . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $user . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $notify . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $hours . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"11\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_monitoring);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

      print "document.monitoring.mon_openvew.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
