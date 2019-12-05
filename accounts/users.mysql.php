<?php
# Script: users.mysql.php
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
    $package = "users.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Admin)) {
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
        $formVars['usr_clientid']   = clean($_GET['usr_clientid'],   30);
        $formVars['usr_altemail']   = clean($_GET['usr_altemail'],  255);
        $formVars['usr_theme']      = clean($_GET['usr_theme'],      10);
        $formVars['usr_passwd']     = clean($_GET['usr_passwd'],     32);
        $formVars['usr_reenter']    = clean($_GET['usr_reenter'],    32);
        $formVars['usr_reset']      = clean($_GET['usr_reset'],      10);
        $formVars['usr_phone']      = clean($_GET['usr_phone'],      15);
        $formVars['usr_notify']     = clean($_GET['usr_notify'],     10);
        $formVars['usr_freq']       = clean($_GET['usr_freq'],       10);
        $formVars['usr_report']     = clean($_GET['usr_report'],     10);
        $formVars['usr_confirm']    = clean($_GET['usr_confirm'],    10);

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
        if ($formVars['usr_report'] == 'true') {
          $formVars['usr_report'] = 1;
        } else {
          $formVars['usr_report'] = 0;
        }
        if ($formVars['usr_confirm'] == 'true') {
          $formVars['usr_confirm'] = 1;
        } else {
          $formVars['usr_confirm'] = 0;
        }

        if (strlen($formVars['usr_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

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
            "usr_clientid    = \"" . $formVars['usr_clientid']  . "\"," .
            "usr_altemail    = \"" . $formVars['usr_altemail']  . "\"," .
            "usr_theme       =   " . $formVars['usr_theme']     . "," .
            "usr_reset       =   " . $formVars['usr_reset']     . "," .
            "usr_report      =   " . $formVars['usr_report']    . "," .
            "usr_confirm     =   " . $formVars['usr_confirm'];

          if (strlen($formVars['usr_passwd']) > 0 && $formVars['usr_passwd'] === $formVars['usr_reenter']) {
            logaccess($_SESSION['uid'], $package, "Resetting user " . $formVars['usr_name'] . " password.");
            $q_string .= ",usr_passwd = '" . MD5($formVars['usr_passwd']) . "' ";
          }

          if ($formVars['update'] == 0) {
            $query = "insert into users set usr_id = NULL, " . $q_string;
            $formVars['id'] = last_insert_id();
            $message = "User added.";
          }
          if ($formVars['update'] == 1) {
# and now update the users information
            $query = "update users set " . $q_string . " where usr_id = " . $formVars['id'];
            $message = "User updated.";

# if user is changing groups; change to read-only for old group. Need to get old group info first.
            $q_string  = "select usr_group ";
            $q_string .= "from users ";
            $q_string .= "where usr_id = " . $formVars['id'] . " ";
            $q_users = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            $a_users = mysql_fetch_array($q_users);

# then see if the user is in the grouplist (should be but let's be sure and get the id to be updated)
            if ($a_users['usr_group'] != $formVars['usr_group']) {
              $q_string  = "select gpl_id ";
              $q_string .= "from grouplist ";
              $q_string .= "where gpl_user = " . $formVars['id'] . " and gpl_group = " . $formVars['usr_group'] . " ";
              $q_grouplist = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
              if (mysql_num_rows($q_grouplist) == 1) {
# and if > 0, they're there; change to read only...
                $a_grouplist = mysql_fetch_array($q_grouplist);

                $q_string  = "update grouplist ";
                $q_string .= "set ";
                $q_string .= "gpl_edit = 0 ";
                $q_string .= "where gpl_id = " . $a_grouplist['gpl_id'] . " ";

                mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
              }
            }
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['usr_name']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

          print "alert('" . $message . "');\n";

          if ($formVars['usr_disabled'] == 1 ) {
# clear from grouplist
            $q_string  = "delete ";
            $q_string .= "from grouplist ";
            $q_string .= "where gpl_user = " . $formVars['id'];
            mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          } else {
            $q_string  = "select gpl_id ";
            $q_string .= "from grouplist ";
            $q_string .= "where gpl_user = " . $formVars['id'] . " and gpl_group = " . $formVars['usr_group'] . " ";
            $q_grouplist = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            if (mysql_num_rows($q_grouplist) == 0) {
# if not in the grouplist, add them
# removing them will be done elsewhere.
              $q_string  = "insert ";
              $q_string .= "into grouplist ";
              $q_string .= "set ";
              $q_string .= "gpl_id = null,";
              $q_string .= "gpl_group = " . $formVars['usr_group'] . ",";
              $q_string .= "gpl_user  = " . $formVars['id'] . ",";
              $q_string .= "gpl_edit  = " . "1";

              mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));

            } else {
# if they're already in the system, change user status to manage assets;
              $a_grouplist = mysql_fetch_array($q_grouplist);

              $q_string  = "update grouplist ";
              $q_string .= "set ";
              $q_string .= "gpl_edit = 1 ";
              $q_string .= "where gpl_id = " . $a_grouplist['gpl_id'] . " ";

              mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            }
          }

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

######
# New User Listing
######

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">New User Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('newuser-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"newuser-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>New User Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this user from the Inventory. It's better to disable the user.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a user to toggle the form and edit the user.</li>\n";
      $output .= "    <li><strong>Highlight</strong> - If a user is <span class=\"ui-state-highlight\">highlighted</span>, then the user's Reset Password on Next Login flag has been set.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>User Management</strong> title bar to toggle the <strong>User Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" colspan=\"13\">New Users</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">ID</th>\n";
      $output .=   "<th class=\"ui-state-default\">Level</th>\n";
      $output .=   "<th class=\"ui-state-default\">Login</th>\n";
      $output .=   "<th class=\"ui-state-default\">First Name</th>\n";
      $output .=   "<th class=\"ui-state-default\">Last Name</th>\n";
      $output .=   "<th class=\"ui-state-default\">E-Mail</th>\n";
      $output .=   "<th class=\"ui-state-default\">Alternate E-Mail</th>\n";
      $output .=   "<th class=\"ui-state-default\">Reset</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .=   "<th class=\"ui-state-default\">Registered Date</th>\n";
      $output .=   "<th class=\"ui-state-default\">ClientID</th>\n";
      $output .=   "<th class=\"ui-state-default\">Theme</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select usr_id,lvl_name,usr_disabled,usr_name,usr_clientid,usr_first,usr_last,usr_email,usr_altemail,usr_reset,grp_name,usr_timestamp,theme_title ";
      $q_string .= "from users ";
      $q_string .= "left join levels on levels.lvl_id = users.usr_level ";
      $q_string .= "left join groups on groups.grp_id = users.usr_group ";
      $q_string .= "left join themes on themes.theme_id = users.usr_theme ";
      $q_string .= "where usr_disabled = 0 and usr_group = 0 and usr_level > 1 ";
      $q_string .= "order by usr_last,usr_first";
      $q_users = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_users) > 0) {
        while ($a_users = mysql_fetch_array($q_users)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_users['usr_id'] . "');jQuery('#dialogUser').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_user('users.del.php?id=" . $a_users['usr_id'] . "');\">";
          $linkend = "</a>";

          if ($a_users['usr_reset']) {
            $default = " class=\"ui-state-highlight\"";
            $defaultdel = " class=\"ui-state-highlight delete\"";
          } else {
            if ($a_users['usr_disabled']) {
              $default = " class=\"ui-state-error\"";
              $defaultdel = " class=\"ui-state-error delete\"";
            } else {
              $default = " class=\"ui-widget-content\"";
              $defaultdel = " class=\"ui-widget-content delete\"";
            }
          }

          $timestamp = strtotime($a_users['usr_timestamp']);
          $reg_date = date('d M y @ H:i' ,$timestamp);

          if ($a_users['usr_reset']) {
            $pwreset = 'Yes';
          } else {
            $pwreset = 'No';
          }

          $output .= "<tr>\n";
          $output .=   "<td" . $defaultdel . ">" . $linkdel   . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_id']                 . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['lvl_name']               . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_name']               . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_first']              . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_last']               . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_email']              . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_altemail']           . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $pwreset                           . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['grp_name']               . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $reg_date                          . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_clientid']           . $linkend . "</td>\n";
          $output .= "  <td" . $default    . ">" . $linkstart . $a_users['theme_title']            . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"13\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      print "document.getElementById('new_users_table').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";


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
      print "document.user.usr_altemail.value = '';\n";
      print "document.user.usr_reset.checked = false;\n";
      print "document.user.usr_clientid.value = '';\n";
      print "document.user.usr_theme[0].selected = true;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

function display_user( $p_title, $p_toggle, $p_query ) {

  $output  = "<p></p>\n";
  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .=   "<th class=\"ui-state-default\">" . $p_title . " User Listing</th>\n";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('" . $p_toggle . "-user-help');\">Help</a></th>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<div id=\"" . $p_toggle . "-user-help\" style=\"display: none\">\n";

  $output .= "<div class=\"main-help ui-widget-content\">\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Disabled User Listing</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li><strong>Delete (x)</strong> - Click here to delete this user from the Inventory. It's better to disable the user.</li>\n";
  $output .= "    <li><strong>Editing</strong> - Click on a user to toggle the form and edit the user.</li>\n";
  $output .= "    <li><strong>Highlight</strong> - If a user is <span class=\"ui-state-error\">highlighted</span>, then the user has been disabled.</li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Notes</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li>Click the <strong>User Management</strong> title bar to toggle the <strong>User Form</strong>.</li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "</div>\n";

  $output .= "</div>\n";

  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));;
  while ($a_groups = mysql_fetch_array($q_groups)) {

    $group  = "<table class=\"ui-styled-table\">\n";
    $group .= "<tr>\n";
    $group .=   "<th class=\"ui-state-default\" colspan=\"13\">" . $a_groups['grp_name'] . "</th>\n";
    $group .= "</tr>\n";
    $group .= "<tr>\n";
    $group .=   "<th class=\"ui-state-default\">Del</th>\n";
    $group .=   "<th class=\"ui-state-default\">ID</th>\n";
    $group .=   "<th class=\"ui-state-default\">Level</th>\n";
    $group .=   "<th class=\"ui-state-default\">Login</th>\n";
    $group .=   "<th class=\"ui-state-default\">First Name</th>\n";
    $group .=   "<th class=\"ui-state-default\">Last Name</th>\n";
    $group .=   "<th class=\"ui-state-default\">E-Mail</th>\n";
    $group .=   "<th class=\"ui-state-default\">Alternate E-Mail</th>\n";
    $group .=   "<th class=\"ui-state-default\">Force Password Change</th>\n";
    $group .=   "<th class=\"ui-state-default\">Registered Date</th>\n";
    $group .=   "<th class=\"ui-state-default\">ClientID</th>\n";
    $group .=   "<th class=\"ui-state-default\">Theme</th>\n";
    $group .= "</tr>\n";

    $count = 0;
    $q_string  = "select usr_id,lvl_name,usr_disabled,usr_name,usr_clientid,usr_first,usr_last,usr_email,usr_altemail,usr_reset,usr_group,usr_timestamp,theme_title ";
    $q_string .= "from users ";
    $q_string .= "left join levels on levels.lvl_id = users.usr_level ";
    $q_string .= "left join themes on themes.theme_id = users.usr_theme ";
    $q_string .= "where usr_group = " . $a_groups['grp_id'] . " " . $p_query;
    $q_string .= "order by usr_last,usr_first";
    $q_users = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
    if (mysql_num_rows($q_users) > 0) {
      while ($a_users = mysql_fetch_array($q_users)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_users['usr_id'] . "');jQuery('#dialogUser').dialog('open');return false;\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_user('users.del.php?id="  . $a_users['usr_id'] . "');\">";
        $linkend = "</a>";

        if ($a_users['usr_reset']) {
          $default = " class=\"ui-state-highlight\"";
          $defaultdel = " class=\"ui-state-highlight delete\"";
        } else {
          if ($a_users['usr_disabled']) {
            $default = " class=\"ui-state-error\"";
            $defaultdel = " class=\"ui-state-error delete\"";
          } else {
            $default = " class=\"ui-widget-content\"";
            $defaultdel = " class=\"ui-widget-content delete\"";
          }
        }

        $timestamp = strtotime($a_users['usr_timestamp']);
        $reg_date = date('d M y @ H:i' ,$timestamp);

        if ($a_users['usr_reset']) {
          $pwreset = 'Yes';
        } else {
          $pwreset = 'No';
        }

        $group .= "<tr>\n";
        $group .=   "<td" . $defaultdel . ">" . $linkdel   . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_id']                 . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['lvl_name']               . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_name']               . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_first']              . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_last']               . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_email']              . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_altemail']           . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $pwreset                           . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $reg_date                          . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['usr_clientid']           . $linkend . "</td>\n";
        $group .= "  <td" . $default    . ">" . $linkstart . $a_users['theme_title']            . $linkend . "</td>\n";
        $group .= "</tr>\n";
        $count++;
      }
    }

    $group .= "</table>\n";

    if ($count > 0) {
      $output .= $group;
    }

  }

  print "document.getElementById('" . $p_toggle . "_users_table').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

}

?>
