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
        $formVars['grp_role']         = clean($_GET['grp_role'],         100);
        $formVars['grp_organization'] = clean($_GET['grp_organization'],  10);
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
          $q_string .= "from a_groups ";
          $q_string .= "where grp_id = " . $formVars['id'] . " ";
          $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_groups) > 0) {
            $a_groups = mysqli_fetch_array($q_groups);
# got it, now update everyone in the same group with the same old manager assuming the group already exists.
            $q_string  = "update ";
            $q_string .= "users ";
            $q_string .= "set usr_manager = " . $formVars['grp_manager'] . " ";
            $q_string .= "where usr_group = " . $formVars['id'] . " and (usr_manager = " . $a_groups['grp_manager'] . " or usr_manager = 0) ";
            $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          }

# all done. now update a_groups with the new information.
          $q_string =
            "grp_name          = \"" . $formVars['grp_name']          . "\"," . 
            "grp_manager       =   " . $formVars['grp_manager']       . "," . 
            "grp_organization  =   " . $formVars['grp_organization']  . "," . 
            "grp_role          =   " . $formVars['grp_role']          . "," . 
            "grp_email         = \"" . $formVars['grp_email']         . "\"," . 
            "grp_disabled      =   " . $formVars['grp_disabled']      . "," . 
            "grp_changedby     =   " . $formVars['grp_changedby']     . "," . 
            "grp_status        =   " . $formVars['grp_status']        . "," . 
            "grp_server        =   " . $formVars['grp_server']        . "," . 
            "grp_import        =   " . $formVars['grp_import'];

          if ($formVars['update'] == 0) {
            $query = "insert into a_groups set grp_id = NULL," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update a_groups set " . $q_string . " where grp_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['grp_name']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      $group  = "<table class=\"ui-styled-table\">\n";
      $group .= "<tr>\n";
      $group .= "  <th class=\"ui-state-default\">Group Listing</th>\n";
      $group .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('group-listing-help');\">Help</a></th>\n";
      $group .= "</tr>\n";
      $group .= "</table>\n";

      $group .= "<div id=\"group-listing-help\" style=\"display: none\">\n";

      $header  = "<div class=\"main-help ui-widget-content\">\n";

      $header .= "<ul>\n";
      $header .= "  <li><strong>Group Listing</strong>\n";
      $header .= "  <ul>\n";
      $header .= "    <li><strong>Delete (x)</strong> - Click here to delete this group from the Inventory. It's better to disable the user.</li>\n";
      $header .= "    <li><strong>Editing</strong> - Click on a group to toggle the form and edit the group.</li>\n";
      $header .= "    <li><strong>Highlight</strong> - If a group is <span class=\"ui-state-error\">highlighted</span>, then the group has been disabled and will not be visible in any selection menus.</li>\n";
      $header .= "  </ul></li>\n";
      $header .= "</ul>\n";

      $header .= "</div>\n";

      $header .= "</div>\n";


      $title  = "<table class=\"ui-styled-table\">";
      $title .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $title .= "  <th class=\"ui-state-default\">Del</th>";
      }
      $title .= "  <th class=\"ui-state-default\">Id</th>";
      $title .= "  <th class=\"ui-state-default\">Organization</th>";
      $title .= "  <th class=\"ui-state-default\">Group</th>";
      $title .= "  <th class=\"ui-state-default\">Role</th>";
      $title .= "  <th class=\"ui-state-default\">Group EMail</th>";
      $title .= "  <th class=\"ui-state-default\">Group Manager</th>";
      $title .= "  <th class=\"ui-state-default\">Status</th>";
      $title .= "  <th class=\"ui-state-default\">Server</th>";
      $title .= "  <th class=\"ui-state-default\">Import</th>";
      $title .= "</tr>";

      $group     .= $header . $title;


      $q_string  = "select grp_id,grp_name,org_name,role_name,grp_email,usr_last,";
      $q_string .= "usr_first,grp_disabled,grp_status,grp_server,grp_import ";
      $q_string .= "from a_groups ";
      $q_string .= "left join organizations on organizations.org_id = a_groups.grp_organization ";
      $q_string .= "left join roles on roles.role_id = a_groups.grp_role ";
      $q_string .= "left join users on users.usr_id = a_groups.grp_manager ";
      $q_string .= "order by grp_name";
      $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_groups) > 0) {
        while ($a_groups = mysqli_fetch_array($q_groups)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('groups.fill.php?id="  . $a_groups['grp_id'] . "');jQuery('#dialogGroup').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('groups.del.php?id=" . $a_groups['grp_id'] . "');\">";
          $linkend = "</a>";

          $class = "ui-widget-content";
          if ($a_groups['grp_disabled']) {
            $class = "ui-state-error";
          }

          $grp_status = "No";
          if ($a_groups['grp_status']) {
            $grp_status = "Yes";
          }
          $grp_server = "No";
          if ($a_groups['grp_server']) {
            $grp_server = "Yes";
          }
          $grp_import = "No";
          if ($a_groups['grp_import']) {
            $grp_import = "Yes";
          }

          $group .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $group .= "  <td class=\"" . $class . " delete\">" . $linkdel   . "</td>";
          }
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_groups['grp_id']           . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_groups['org_name']         . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_groups['grp_name']         . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_groups['role_name']        . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_groups['grp_email']        . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_groups['usr_first'] . " " . $a_groups['usr_last'] . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $grp_status                   . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $grp_server                   . $linkend . "</td>";
          $group .= "  <td class=\"" . $class . "\">"        . $linkstart . $grp_import                   . $linkend . "</td>";
          $group .= "</tr>";


        }
      } else {
        $group .= "<tr>";
        $group .= "  <td class=\"" . $class . "\" colspan=\"6\">No records found.</td>";
        $group .= "</tr>";
      }

      mysqli_free_result($q_groups);

      $group .= "</table>";

      print "document.getElementById('group_mysql').innerHTML = '"     . mysqli_real_escape_string($db, $group)     . "';\n\n";

      print "document.groups.grp_organization[0].selected = true;\n";
      print "document.groups.grp_name.value = '';\n";
      print "document.groups.grp_role[0].selected = true;\n";
      print "document.groups.grp_email.value = '';\n";
      print "document.groups.grp_manager[0].selected = true;\n";
      print "document.groups.grp_disabled[0].selected = true;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
