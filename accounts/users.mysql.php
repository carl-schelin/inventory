<?php
# Script: users.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "users.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Admin)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['usr_first']      = clean($_GET['usr_first'],     255);
        $formVars['usr_last']       = clean($_GET['usr_last'],      255);
        $formVars['usr_name']       = clean($_GET['usr_name'],      120);
        $formVars['usr_disabled']   = clean($_GET['usr_disabled'],   10);
        $formVars['usr_level']      = clean($_GET['usr_level'],      10);
        $formVars['usr_manager']    = clean($_GET['usr_manager'],    10);
        $formVars['usr_title']      = clean($_GET['usr_title'],      10);
        $formVars['usr_email']      = clean($_GET['usr_email'],     255);
        $formVars['usr_group']      = clean($_GET['usr_group'],      10);
        $formVars['usr_theme']      = clean($_GET['usr_theme'],      10);
        $formVars['usr_passwd']     = clean($_GET['usr_passwd'],     32);
        $formVars['usr_reenter']    = clean($_GET['usr_reenter'],    32);
        $formVars['usr_reset']      = clean($_GET['usr_reset'],      10);
        $formVars['usr_phone']      = clean($_GET['usr_phone'],      15);
        $formVars['usr_notify']     = clean($_GET['usr_notify'],     10);
        $formVars['usr_freq']       = clean($_GET['usr_freq'],       10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['usr_notify'] == '') {
          $formVars['usr_notify'] = -1;
        }
        if ($formVars['usr_freq'] == '') {
          $formVars['usr_freq'] = -1;
        }
        if ($formVars['usr_reset'] == 'true') {
          $formVars['usr_reset'] = 1;
        } else {
          $formVars['usr_reset'] = 0;
        }

        if (strlen($formVars['usr_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "usr_first       = \"" . $formVars['usr_first']     . "\"," .
            "usr_last        = \"" . $formVars['usr_last']      . "\"," .
            "usr_name        = \"" . $formVars['usr_name']      . "\"," .
            "usr_disabled    =   " . $formVars['usr_disabled']  . "," .
            "usr_level       =   " . $formVars['usr_level']     . "," .
            "usr_manager     =   " . $formVars['usr_manager']   . "," .
            "usr_title       =   " . $formVars['usr_title']     . "," .
            "usr_email       = \"" . $formVars['usr_email']     . "\"," .
            "usr_phone       = \"" . $formVars['usr_phone']     . "\"," .
            "usr_group       =   " . $formVars['usr_group']     . "," .
            "usr_notify      =   " . $formVars['usr_notify']    . "," .
            "usr_freq        =   " . $formVars['usr_freq']      . "," .
            "usr_theme       =   " . $formVars['usr_theme']     . "," .
            "usr_reset       =   " . $formVars['usr_reset'];

          if (strlen($formVars['usr_passwd']) > 0 && $formVars['usr_passwd'] === $formVars['usr_reenter']) {
            logaccess($db, $_SESSION['uid'], $package, "Resetting user " . $formVars['usr_name'] . " password.");
            $q_string .= ",usr_passwd = '" . MD5($formVars['usr_passwd']) . "' ";
          }

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_users set usr_id = NULL, " . $q_string;
            $formVars['id'] = last_insert_id($db);
          }
          if ($formVars['update'] == 1) {
# and now update the users information
            $q_string = "update inv_users set " . $q_string . " where usr_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['usr_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

# now manage the grouplist
          if ($formVars['update'] == 1) {
# if user is changing groups; change to read-only for old group. Need to get old group info first.
            $q_string  = "select usr_group ";
            $q_string .= "from inv_users ";
            $q_string .= "where usr_id = " . $formVars['id'] . " ";
            $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            $a_inv_users = mysqli_fetch_array($q_inv_users);

# then see if the user is in the grouplist (should be but let's be sure and get the id to be updated)
            if ($a_inv_users['usr_group'] != $formVars['usr_group']) {
              $q_string  = "select gpl_id ";
              $q_string .= "from inv_grouplist ";
              $q_string .= "where gpl_user = " . $formVars['id'] . " and gpl_group = " . $formVars['usr_group'] . " ";
              $q_inv_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_inv_grouplist) == 1) {
# and if > 0, they're there; change to read only...
                $a_inv_grouplist = mysqli_fetch_array($q_inv_grouplist);

                $q_string  = "update inv_grouplist ";
                $q_string .= "set ";
                $q_string .= "gpl_edit = 0 ";
                $q_string .= "where gpl_id = " . $a_inv_grouplist['gpl_id'] . " ";

                mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              }
            }
          }

          if ($formVars['usr_disabled'] == 1 ) {
# clear from grouplist
            $q_string  = "delete ";
            $q_string .= "from inv_grouplist ";
            $q_string .= "where gpl_user = " . $formVars['id'] . " ";
            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

# also clear any managerial duties.
            $q_string  = "update inv_users ";
            $q_string .= "set usr_manager = 0 ";
            $q_string .= "where usr_manager = " . $formVars['id'] . " ";
            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

# and clear from inv_groups as well.
            $q_string  = "update inv_groups ";
            $q_string .= "set grp_manager = 0 ";
            $q_string .= "where grp_manager = " . $formVars['id'] . " ";
            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

          } else {
            $q_string  = "select gpl_id ";
            $q_string .= "from inv_grouplist ";
            $q_string .= "where gpl_user = " . $formVars['id'] . " and gpl_group = " . $formVars['usr_group'] . " ";
            $q_inv_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_inv_grouplist) == 0) {
# if not in the grouplist, add them
# removing them will be done elsewhere.
              $q_string  = "insert ";
              $q_string .= "into inv_grouplist ";
              $q_string .= "set ";
              $q_string .= "gpl_id = null,";
              $q_string .= "gpl_group = " . $formVars['usr_group'] . ",";
              $q_string .= "gpl_user  = " . $formVars['id'] . ",";
              $q_string .= "gpl_edit  = " . "1";

              mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

            } else {
# if they're already in the system, change user status to manage assets;
              $a_inv_grouplist = mysqli_fetch_array($q_inv_grouplist);

              $q_string  = "update inv_grouplist ";
              $q_string .= "set ";
              $q_string .= "gpl_edit = 1 ";
              $q_string .= "where gpl_id = " . $a_inv_grouplist['gpl_id'] . " ";

              mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            }
          }

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

######
# New User Listing
######

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" colspan=\"13\">New Users</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" width=\"160\">Delete User</th>\n";
      $output .=   "<th class=\"ui-state-default\">Level</th>\n";
      $output .=   "<th class=\"ui-state-default\">Login</th>\n";
      $output .=   "<th class=\"ui-state-default\">First Name</th>\n";
      $output .=   "<th class=\"ui-state-default\">Last Name</th>\n";
      $output .=   "<th class=\"ui-state-default\">E-Mail</th>\n";
      $output .=   "<th class=\"ui-state-default\">Reset</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .=   "<th class=\"ui-state-default\">Date Registered</th>\n";
      $output .=   "<th class=\"ui-state-default\">Theme</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select usr_id,lvl_name,usr_disabled,usr_name,usr_first,usr_last,usr_email,usr_reset,grp_name,usr_timestamp,theme_title ";
      $q_string .= "from inv_users ";
      $q_string .= "left join inv_levels on inv_levels.lvl_id = inv_users.usr_level ";
      $q_string .= "left join inv_groups on inv_groups.grp_id = inv_users.usr_group ";
      $q_string .= "left join inv_themes on inv_themes.theme_id = inv_users.usr_theme ";
      $q_string .= "where usr_disabled = 0 and usr_group = 0 and usr_level > 1 ";
      $q_string .= "order by usr_last,usr_first";
      $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_users) > 0) {
        while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_inv_users['usr_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_user('users.del.php?id=" . $a_inv_users['usr_id'] . "');\">";
          $linkend = "</a>";

          if ($a_inv_users['usr_reset']) {
            $default = " class=\"ui-state-highlight\"";
            $defaultdel = " class=\"ui-state-highlight delete\"";
          } else {
            if ($a_inv_users['usr_disabled']) {
              $default = " class=\"ui-state-error\"";
              $defaultdel = " class=\"ui-state-error delete\"";
            } else {
              $default = " class=\"ui-widget-content\"";
              $defaultdel = " class=\"ui-widget-content delete\"";
            }
          }

          $timestamp = strtotime($a_inv_users['usr_timestamp']);
          $reg_date = date('d M y @ H:i' ,$timestamp);

          if ($a_inv_users['usr_reset']) {
            $pwreset = 'Yes';
          } else {
            $pwreset = 'No';
          }

          $output .= "<tr>\n";
          $output .=   "<td" . $defaultdel . ">" . $linkdel                                        . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $a_inv_users['lvl_name']               . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_inv_users['usr_name']    . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $a_inv_users['usr_first']              . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $a_inv_users['usr_last']               . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $a_inv_users['usr_email']              . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $pwreset                           . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $a_inv_users['grp_name']               . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $reg_date                          . "</td>\n";
          $output .= "  <td" . $default    . ">"              . $a_inv_users['theme_title']            . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"13\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      print "document.getElementById('new_users_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";


      display_User("Registered", "registered", " and usr_disabled = 0 ");
      display_User("Admin",      "admin",      " and usr_disabled = 0 and usr_level = 1 ");
      display_User("Read-Only",  "readonly",   " and usr_disabled = 0 and usr_level = 3 ");
      display_User("Guest",      "guest",      " and usr_disabled = 0 and usr_level = 4 ");
      display_User("Disabled",   "disabled",   " and usr_disabled = 1 ");

      print "document.user.usr_level[0].selected = true;\n";
      print "document.user.usr_name.value = '';\n";
      print "document.user.usr_first.value = '';\n";
      print "document.user.usr_last.value = '';\n";
      print "document.user.usr_email.value = '';\n";
      print "document.user.usr_reset.checked = false;\n";
      print "document.user.usr_theme[0].selected = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

function display_user( $p_title, $p_toggle, $p_query ) {

  include('settings.php');

  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  $output = '';

  $groups = 0;
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));;
  if (mysqli_num_rows($q_inv_groups) > 0) {
    while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {

      $group  = "<table class=\"ui-styled-table\">\n";
      $group .= "<tr>\n";
      $group .=   "<th class=\"ui-state-default\" colspan=\"13\">" . $a_inv_groups['grp_name'] . "</th>\n";
      $group .= "</tr>\n";
      $group .= "<tr>\n";
      $group .=   "<th class=\"ui-state-default\" width=\"160\">Delete User</th>\n";
      $group .=   "<th class=\"ui-state-default\">Level</th>\n";
      $group .=   "<th class=\"ui-state-default\">Login</th>\n";
      $group .=   "<th class=\"ui-state-default\">First Name</th>\n";
      $group .=   "<th class=\"ui-state-default\">Last Name</th>\n";
      $group .=   "<th class=\"ui-state-default\">E-Mail</th>\n";
      $group .=   "<th class=\"ui-state-default\">Force Password Change</th>\n";
      $group .=   "<th class=\"ui-state-default\">Date Registered</th>\n";
      $group .=   "<th class=\"ui-state-default\">Theme</th>\n";
      $group .= "</tr>\n";

      $count = 0;
      $q_string  = "select usr_id,lvl_name,usr_disabled,usr_name,usr_first,usr_last,usr_email,usr_reset,usr_group,usr_timestamp,theme_title ";
      $q_string .= "from inv_users ";
      $q_string .= "left join inv_levels on inv_levels.lvl_id = inv_users.usr_level ";
      $q_string .= "left join inv_themes on inv_themes.theme_id = inv_users.usr_theme ";
      $q_string .= "where usr_group = " . $a_inv_groups['grp_id'] . " " . $p_query;
      $q_string .= "order by usr_last,usr_first";
      $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_users) > 0) {
        while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_inv_users['usr_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_user('users.del.php?id="  . $a_inv_users['usr_id'] . "');\">";
          $linkend = "</a>";

          if ($a_inv_users['usr_reset']) {
            $default = " class=\"ui-state-highlight\"";
            $defaultdel = " class=\"ui-state-highlight delete\"";
          } else {
            if ($a_inv_users['usr_disabled']) {
              $default = " class=\"ui-state-error\"";
              $defaultdel = " class=\"ui-state-error delete\"";
            } else {
              $default = " class=\"ui-widget-content\"";
              $defaultdel = " class=\"ui-widget-content delete\"";
            }
          }

          $timestamp = strtotime($a_inv_users['usr_timestamp']);
          $reg_date = date('d M y @ H:i' ,$timestamp);

          if ($a_inv_users['usr_reset']) {
            $pwreset = 'Yes';
          } else {
            $pwreset = 'No';
          }

          $missing = "";
          $q_string  = "select mail_id ";
          $q_string .= "from inv_email ";
          $q_string .= "where mail_address = \"" . $a_inv_users['usr_email'] . "\" and mail_disabled = 0 ";
          $q_inv_email = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_email) == 0) {
            $missing = "*";
          }

          $group .= "<tr>\n";
          $group .=   "<td" . $defaultdel . ">" . $linkdel   . "</td>\n";
          $group .= "  <td" . $default    . ">"              . $a_inv_users['lvl_name']               . $linkend . "</td>\n";
          $group .= "  <td" . $default    . ">" . $linkstart . $a_inv_users['usr_name']                          . "</td>\n";
          $group .= "  <td" . $default    . ">"              . $a_inv_users['usr_first']              . $linkend . "</td>\n";
          $group .= "  <td" . $default    . ">"              . $a_inv_users['usr_last']                          . "</td>\n";
          $group .= "  <td" . $default    . ">"              . $a_inv_users['usr_email'] . $missing              . "</td>\n";
          $group .= "  <td" . $default    . ">"              . $pwreset                                      . "</td>\n";
          $group .= "  <td" . $default    . ">"              . $reg_date                                     . "</td>\n";
          $group .= "  <td" . $default    . ">"              . $a_inv_users['theme_title']                       . "</td>\n";
          $group .= "</tr>\n";
          $count++;
        }
      }
      $group .= "</table>\n";

      if ($count > 0) {
        $output .= $group;
        $groups++;
      }
    }
  }

  if ($groups == 0) {
    $output .= "<table class=\"ui-styled-table\">\n";
    $output .= "<tr>\n";
    $output .=   "<th class=\"ui-state-default\" colspan=\"13\">No Users</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr>\n";
    $output .=   "<th class=\"ui-state-default\" width=\"160\">Delete User</th>\n";
    $output .=   "<th class=\"ui-state-default\">Level</th>\n";
    $output .=   "<th class=\"ui-state-default\">Login</th>\n";
    $output .=   "<th class=\"ui-state-default\">First Name</th>\n";
    $output .=   "<th class=\"ui-state-default\">Last Name</th>\n";
    $output .=   "<th class=\"ui-state-default\">E-Mail</th>\n";
    $output .=   "<th class=\"ui-state-default\">Force Password Change</th>\n";
    $output .=   "<th class=\"ui-state-default\">Date Registered</th>\n";
    $output .=   "<th class=\"ui-state-default\">Theme</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr>\n";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"13\">No Users Found</td>\n";
    $output .= "</tr>\n";
    $output .= "</table>\n";
  }

  print "document.getElementById('" . $p_toggle . "_users_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

}

?>
