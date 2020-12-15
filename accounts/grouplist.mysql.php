<?php
# Script: grouplist.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "grouplist.mysql.php";
    $formVars['update']             = clean($_GET['update'],             10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],            10);
        $formVars['gpl_group']     = clean($_GET['gpl_group'],     10);
        $formVars['gpl_user']      = clean($_GET['gpl_user'],      10);
        $formVars['gpl_edit']      = clean($_GET['gpl_edit'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['gpl_edit'] == 'true') {
          $formVars['gpl_edit'] = 1;
        } else {
          $formVars['gpl_edit'] = 0;
        }

        if ($formVars['gpl_group'] > 0 && $formVars['gpl_user'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string  = "select gpl_id ";
          $q_string .= "from grouplist ";
          if (check_userlevel($db, $AL_Admin) == 0) {
            $q_string .= "where gpl_user = " . $_SESSION['uid'] . " and gpl_group = " . $formVars['gpl_group'] . " ";
          }
          $q_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_grouplist) > 0) {

            $q_string =
              "gpl_group   =   " . $formVars['gpl_group'] . "," .
              "gpl_user    =   " . $formVars['gpl_user']  . "," . 
              "gpl_edit    =   " . $formVars['gpl_edit'];

            if ($formVars['update'] == 0) {
              $query = "insert into grouplist set gpl_id = NULL," . $q_string;
              $message = "Group Association added.";
            }
            if ($formVars['update'] == 1) {
              $query = "update grouplist set " . $q_string . " where gpl_id = " . $formVars['id'];
              $message = "Group Association updated.";
            }

            logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fw_source']);

            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

            print "alert('" . $message . "');\n";
          } else {
            print "alert('You are not allowed to manage groups you aren\'t a member of.');\n";
          }
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Group Membership Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('group-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"group-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Group Membership Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Delete this association.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an association to bring up the form and edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Users that are <span class=\"ui-state-highlight\">highlighted</span> indicate this is their <strong>Primary</strong> group. Removing them here may not prevent their access to the group's assets if they've only changed organizations within the company. Contact the Inventory Admin to correct the Primary group membership.</li>\n";
      $output .= "    <li>Users that are <span class=\"ui-state-error\">highlighted</span> are users who have been Disabled in the system and should be removed from the group.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .=   "<th class=\"ui-state-default\">User</th>\n";
      $output .=   "<th class=\"ui-state-default\">Title</th>\n";
      $output .=   "<th class=\"ui-state-default\">Manager</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select gpl_id,gpl_group,gpl_edit,grp_name,usr_first,usr_last,usr_disabled,usr_group,tit_name,usr_manager ";
      $q_string .= "from grouplist ";
      $q_string .= "left join groups on groups.grp_id = grouplist.gpl_group ";
      $q_string .= "left join users on users.usr_id = grouplist.gpl_user ";
      $q_string .= "left join titles on titles.tit_id = users.usr_title ";
      $q_string .= "where grp_disabled = 0 ";
      $q_string .= "order by grp_name,usr_last ";
      $q_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_grouplist) > 0) {
        while ($a_grouplist = mysqli_fetch_array($q_grouplist)) {

# essentially list all the groups and members of the groups you belong to
# so if the user being selected is in one of the groups you're a member of
# you can see the user and group

          $q_string  = "select gpl_id ";
          $q_string .= "from grouplist ";
          $q_string .= "where gpl_group = " . $a_grouplist['gpl_group'] . " and gpl_user = " . $_SESSION['uid'] . " "; 
          $q_gltest = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_gltest) > 0 || check_userlevel($db, $AL_Admin)) {

            $linkstart = '';
            $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_grouplist('grouplist.del.php?id=" . $a_grouplist['gpl_id'] . "');\">";
            $linkend   = '';

            $primary = "";
            if ($a_grouplist['gpl_edit']) {
              $primary = "*";
            }

            $class = "ui-widget-content";
            if ($a_grouplist['gpl_group'] != $a_grouplist['usr_group']) {
              $class = "ui-state-highlight";
            }

            if ($a_grouplist['usr_disabled']) {
              $class = "ui-state-error";
            }

            if ($a_grouplist['usr_manager'] != '') {
              $q_string  = "select usr_last,usr_first ";
              $q_string .= "from users ";
              $q_string .= "where usr_id = " . $a_grouplist['usr_manager'] . " ";
              $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              $a_users = mysqli_fetch_array($q_users);
            }

            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                                . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_grouplist['grp_name'] . $primary                        . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_grouplist['usr_first'] . " " . $a_grouplist['usr_last'] . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_grouplist['tit_name']                                   . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_users['usr_first'] . " " . $a_users['usr_last']         . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_grouplist);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.grouplist.gpl_group[0].selected = true;\n";
      print "document.grouplist.gpl_user[0].selected = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
