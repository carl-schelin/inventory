<?php
# Script: grouplist.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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
          $q_string .= "from inv_grouplist ";
          $q_string .= "where gpl_group = " . $formVars['gpl_group'] . " ";
          if (check_userlevel($db, $AL_Admin) == 0) {
            $q_string .= "and gpl_user = " . $_SESSION['uid'] . " ";
          }

          $q_inv_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_grouplist) > 0) {

            $q_string =
              "gpl_group   =   " . $formVars['gpl_group'] . "," .
              "gpl_user    =   " . $formVars['gpl_user']  . "," . 
              "gpl_edit    =   " . $formVars['gpl_edit'];

            if ($formVars['update'] == 0) {
              $q_string = "insert into inv_grouplist set gpl_id = NULL," . $q_string;
            }
            if ($formVars['update'] == 1) {
              $q_string = "update inv_grouplist set " . $q_string . " where gpl_id = " . $formVars['id'];
            }

            logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['gpl_group']);

            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          } else {
            print "alert('You are not allowed to manage groups you aren\'t a member of.');\n";
          }
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" width=\"160\">Delete Membershp</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .=   "<th class=\"ui-state-default\">User</th>\n";
      $output .=   "<th class=\"ui-state-default\">Title</th>\n";
      $output .=   "<th class=\"ui-state-default\">Manager</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select gpl_id,gpl_group,gpl_edit,grp_name,usr_first,usr_last,usr_disabled,usr_group,tit_name,usr_manager ";
      $q_string .= "from inv_grouplist ";
      $q_string .= "left join inv_groups on inv_groups.grp_id = inv_grouplist.gpl_group ";
      $q_string .= "left join inv_users on inv_users.usr_id = inv_grouplist.gpl_user ";
      $q_string .= "left join inv_titles on inv_titles.tit_id = inv_users.usr_title ";
      $q_string .= "where grp_disabled = 0 ";
      $q_string .= "order by grp_name,usr_last ";
      $q_inv_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_grouplist) > 0) {
        while ($a_inv_grouplist = mysqli_fetch_array($q_inv_grouplist)) {

# essentially list all the groups and members of the groups you belong to
# so if the user being selected is in one of the groups you're a member of
# you can see the user and group

          $q_string  = "select gpl_id ";
          $q_string .= "from inv_grouplist ";
          $q_string .= "where gpl_group = " . $a_inv_grouplist['gpl_group'] . " and gpl_user = " . $_SESSION['uid'] . " "; 
          $q_gltest = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_gltest) > 0 || check_userlevel($db, $AL_Admin)) {

            if ($a_inv_grouplist['usr_disabled']) {
              $linkstart = "";
              $linkend   = '';
            } else {
              $linkstart = "<a href=\"#\" onclick=\"show_file('grouplist.fill.php?id="  . $a_inv_grouplist['gpl_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
              $linkend   = '</a>';
            }

            $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_grouplist('grouplist.del.php?id=" . $a_inv_grouplist['gpl_id'] . "');\">";

            $readwrite = "";
            if ($a_inv_grouplist['gpl_edit']) {
              $readwrite = "*";
            }

            $class = "ui-widget-content";
            if ($a_inv_grouplist['gpl_group'] != $a_inv_grouplist['usr_group']) {
              $class = "ui-state-highlight";
            }

            if ($a_inv_grouplist['usr_disabled']) {
              $class = "ui-state-error";
            }

            if ($a_inv_grouplist['usr_manager'] > 0) {
              $q_string  = "select usr_last,usr_first ";
              $q_string .= "from inv_users ";
              $q_string .= "where usr_id = " . $a_inv_grouplist['usr_manager'] . " ";
              $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              $a_inv_users = mysqli_fetch_array($q_inv_users);
            }

            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                                . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_grouplist['grp_name']                        . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_grouplist['usr_first'] . " " . $a_inv_grouplist['usr_last'] . $readwrite . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_grouplist['tit_name']                                   . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_users['usr_first'] . " " . $a_inv_users['usr_last']         . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_inv_grouplist);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
