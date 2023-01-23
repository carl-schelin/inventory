<?php
# Script: groups.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "groups.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Admin)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                10);
        $formVars['grp_disabled']     = clean($_GET['grp_disabled'],     255);
        $formVars['grp_name']         = clean($_GET['grp_name'],          60);
        $formVars['grp_manager']      = clean($_GET['grp_manager'],       10);
        $formVars['grp_department']   = clean($_GET['grp_department'],    10);
        $formVars['grp_email']        = clean($_GET['grp_email'],        255);
        $formVars['grp_changedby']    = clean($_SESSION['uid'],           10);
        $formVars['grp_status']       = clean($_GET['grp_status'],        10);
        $formVars['grp_server']       = clean($_GET['grp_server'],        10);
        $formVars['grp_import']       = clean($_GET['grp_import'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['grp_status'] == 'true') {
          $formVars['grp_status'] = 1;
        } else {
          $formVars['grp_status'] = 0;
        }
        if ($formVars['grp_server'] == 'true') {
          $formVars['grp_server'] = 1;
        } else {
          $formVars['grp_server'] = 0;
        }
        if ($formVars['grp_import'] == 'true') {
          $formVars['grp_import'] = 1;
        } else {
          $formVars['grp_import'] = 0;
        }

        if (strlen($formVars['grp_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# get old group manager.
          $q_string  = "select grp_manager ";
          $q_string .= "from inv_groups ";
          $q_string .= "where grp_id = " . $formVars['id'] . " ";
          $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_groups) > 0) {
            $a_inv_groups = mysqli_fetch_array($q_inv_groups);
# got it, now update everyone in the same group with the same old manager assuming the group already exists.
            $q_string  = "update ";
            $q_string .= "inv_users ";
            $q_string .= "set usr_manager = " . $formVars['grp_manager'] . " ";
            $q_string .= "where usr_group = " . $formVars['id'] . " and (usr_manager = " . $a_inv_groups['grp_manager'] . " or usr_manager = 0) ";
            $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          }

# all done. now update inv_groups with the new information.
          $q_string =
            "grp_name          = \"" . $formVars['grp_name']          . "\"," . 
            "grp_manager       =   " . $formVars['grp_manager']       . "," . 
            "grp_department    =   " . $formVars['grp_department']    . "," . 
            "grp_email         = \"" . $formVars['grp_email']         . "\"," . 
            "grp_disabled      =   " . $formVars['grp_disabled']      . "," . 
            "grp_changedby     =   " . $formVars['grp_changedby']     . "," . 
            "grp_status        =   " . $formVars['grp_status']        . "," . 
            "grp_server        =   " . $formVars['grp_server']        . "," . 
            "grp_import        =   " . $formVars['grp_import'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_groups set grp_id = NULL," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_groups set " . $q_string . " where grp_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['grp_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Group Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('group-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"group-listing-help\" style=\"display: none\">\n";

      $output  = "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Group Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this group from the Inventory. It's better to disable the user.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a group to toggle the form and edit the group.</li>\n";
      $output .= "    <li><strong>Highlight</strong> - If a group is <span class=\"ui-state-error\">highlighted</span>, then the group has been disabled and will not be visible in any selection menus.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output  = "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Group</th>";
      }
      $output .= "  <th class=\"ui-state-default\">Group</th>";
      $output .= "  <th class=\"ui-state-default\">Department</th>";
      $output .= "  <th class=\"ui-state-default\">Business</th>";
      $output .= "  <th class=\"ui-state-default\">Organization</th>";
      $output .= "  <th class=\"ui-state-default\">Manager</th>";
      $output .= "  <th class=\"ui-state-default\">Group EMail</th>";
      $output .= "  <th class=\"ui-state-default\">Members</th>";
      $output .= "  <th class=\"ui-state-default\">Status</th>";
      $output .= "  <th class=\"ui-state-default\">Server</th>";
      $output .= "  <th class=\"ui-state-default\">Import</th>";
      $output .= "</tr>";

      $output     .= $header . $title;


      $q_string  = "select grp_id,grp_name,dep_name,bus_name,org_name,grp_email,usr_last,";
      $q_string .= "usr_first,grp_disabled,grp_status,grp_server,grp_import ";
      $q_string .= "from inv_groups ";
      $q_string .= "left join inv_department    on inv_department.dep_id    = inv_groups.grp_department ";
      $q_string .= "left join inv_business      on inv_business.bus_id      = inv_department.dep_business ";
      $q_string .= "left join inv_organizations on inv_organizations.org_id = inv_business.bus_organization ";
      $q_string .= "left join inv_users on inv_users.usr_id = inv_groups.grp_manager ";
      $q_string .= "order by grp_name";
      $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_groups) > 0) {
        while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('groups.fill.php?id="  . $a_inv_groups['grp_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('groups.del.php?id=" . $a_inv_groups['grp_id'] . "');\">";
          $linkend = "</a>";

          $class = "ui-widget-content";
          if ($a_inv_groups['grp_disabled']) {
            $class = "ui-state-error";
          }

          $grp_status = "No";
          if ($a_inv_groups['grp_status']) {
            $grp_status = "Yes";
          }
          $grp_server = "No";
          if ($a_inv_groups['grp_server']) {
            $grp_server = "Yes";
          }
          $grp_import = "No";
          if ($a_inv_groups['grp_import']) {
            $grp_import = "Yes";
          }

          $total = 0;
          $q_string  = "select usr_id ";
          $q_string .= "from inv_users ";
          $q_string .= "where usr_group = " . $a_inv_groups['grp_id'] . " ";
          $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_users) > 0) {
            while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
              $total++;
            }
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_groups['grp_name']  . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_groups['dep_name']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_groups['bus_name']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_groups['org_name']             . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_groups['usr_first'] . " " . $a_inv_groups['usr_last'] . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_groups['grp_email']            . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $total                            . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $grp_status                       . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $grp_server                       . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $grp_import                       . "</td>";
          $output .= "</tr>";


        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No records found.</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_groups);

      $output .= "</table>";

      print "document.getElementById('group_mysql').innerHTML = '"     . mysqli_real_escape_string($db, $output)     . "';\n\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
