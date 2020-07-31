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
    $formVars['update']        = clean($_GET['update'],        10);
    $formVars['pwd_companyid'] = clean($_GET['pwd_companyid'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['pwd_companyid'] == '') {
      $formVars['pwd_companyid'] = 0;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],              10);
        $formVars['mu_username']   = clean($_GET['mu_username'],     60);
        $formVars['mu_name']       = clean($_GET['mu_name'],         60);
        $formVars['mu_email']      = clean($_GET['mu_email'],        60);
        $formVars['mu_account']    = clean($_GET['mu_account'],      10);
        $formVars['mu_comment']    = clean($_GET['mu_comment'],     255);
        $formVars['mu_locked']     = clean($_GET['mu_locked'],       10);
        $formVars['mu_ticket']     = clean($_GET['mu_ticket'],       60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['mu_locked'] == 'true') {
          $formVars['mu_locked'] = 1;
        } else {
          $formVars['mu_locked'] = 0;
        }

        if (strlen($formVars['mu_username']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string = 
            "mu_username   = \"" . $formVars['mu_username']   . "\"," .
            "mu_name       = \"" . $formVars['mu_name']       . "\"," .
            "mu_email      = \"" . $formVars['mu_email']      . "\"," .
            "mu_account    =   " . $formVars['mu_account']    . "," .
            "mu_comment    = \"" . $formVars['mu_comment']    . "\"," . 
            "mu_locked     =   " . $formVars['mu_locked']     . "," . 
            "mu_ticket     = \"" . $formVars['mu_ticket']     . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into manageusers set mu_id = NULL," . $q_string;
            $message = "User added.";
          }

          if ($formVars['update'] == 1) {
            $query = "update manageusers set " . $q_string . " where mu_id = " . $formVars['id'];
            $message = "User updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['mu_username']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

          print "alert('" . $message . "');\n";

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -3) {
        logaccess($_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"pwd_refresh\" value=\"Refresh User Listing\" onClick=\"javascript:attach_user('users.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"mu_update\"   value=\"Update User\"          onClick=\"javascript:attach_user('users.mysql.php', 1);hideDiv('users-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"mu_id\"       value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"pwd_addbtn\"  value=\"Add User\"             onClick=\"javascript:attach_user('users.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"2\">User Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Username <input type=\"text\" name=\"mu_username\" size=\"20\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Lock User? <input type=\"checkbox\" name=\"mu_locked\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">User's Name <input type=\"text\" name=\"mu_name\" size=\"50\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">User's Email <input type=\"text\" name=\"mu_email\" size=\"50\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Account Type <input type=\"radio\" checked value=\"0\" name=\"mu_account\"> User <input type=\"radio\" value=\"1\" name=\"mu_account\"> System <input type=\"radio\" value=\"2\" name=\"mu_account\"> Service</td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Comment <input type=\"text\" name=\"mu_comment\" size=\"50\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Ticket <input type=\"text\" name=\"mu_ticket\" size=\"50\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";
        $output .= "<p>NOTE: Editing this form makes changes to the servers. See the Help for details.</p>\n";

        print "document.getElementById('users_form').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">User Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('users-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"users-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Route Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Delete this route.</li>\n";
      $output .= "    <li><strong>Ping Failure</strong> -  This route was <span class=\"ui-state-error\">not successfully reached</span> via ping.</li>\n";
      $output .= "    <li><strong>Ping Success</strong> - This route was <span class=\"ui-state-highlight\">successfully reached</span> via ping.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a route to bring up the form and edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Rows marked with a checkmark in the Updated column have been automatically captured where possible.</li>\n";
      $output .= "    <li>Click the <strong>Route Management</strong> title bar to toggle the <strong>Route Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Username</th>\n";
      $output .=   "<th class=\"ui-state-default\">Locked?</th>\n";
      $output .=   "<th class=\"ui-state-default\">Type</th>\n";
      $output .=   "<th class=\"ui-state-default\">UID</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .=   "<th class=\"ui-state-default\">GECOS Name</th>\n";
      $output .=   "<th class=\"ui-state-default\">GECOS Email</th>\n";
      $output .=   "<th class=\"ui-state-default\">Home</th>\n";
      $output .=   "<th class=\"ui-state-default\">Shell</th>\n";
      $output .=   "<th class=\"ui-state-default\">Ticket</th>\n";
      $output .=   "<th class=\"ui-state-default\">Comment</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select pwd_id,pwd_user,pwd_uid,pwd_gid,pwd_gecos,pwd_home,pwd_shell ";
      $q_string .= "from syspwd ";
      $q_string .= "where pwd_companyid = " . $formVars['pwd_companyid'] . " ";
      $q_string .= "order by pwd_user";
      $q_syspwd = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_syspwd) > 0) {
        while ($a_syspwd = mysql_fetch_array($q_syspwd)) {

          $q_string  = "select mu_id,mu_account,mu_comment,mu_locked,mu_ticket ";
          $q_string .= "from manageusers ";
          $q_string .= "where mu_username = \"" . $a_syspwd['pwd_user'] . "\" ";
          $q_manageusers = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          if (mysql_num_rows($q_manageusers) == 0) {
            $account = "--";
            $locked = "--";
            $a_manageusers['mu_id'] = 0;
            $a_manageusers['mu_account'] = 0;
            $a_manageusers['mu_comment'] = '';
            $a_manageusers['mu_ticket'] = '';
          } else {
            $a_manageusers = mysql_fetch_array($q_manageusers);

            $account = "--";
            if ($a_manageusers['mu_account'] == 0) {
              $account = "User";
            }
            if ($a_manageusers['mu_account'] == 1) {
              $account = "System";
            }
            if ($a_manageusers['mu_account'] == 2) {
              $account = "Service";
            }

            $locked = 'No';
            if ($a_syspwd['mu_locked']) {
              $locked = 'Yes';
            }
          }

          $gecos = explode(",", $a_syspwd['pwd_gecos']);

# system account so can't be locked.
          if ($a_syspwd['pwd_uid'] <= 100) {
            $locked = '--';
          }

          $q_string  = "select grp_name ";
          $q_string .= "from sysgrp ";
          $q_string .= "where grp_companyid = " . $formVars['pwd_companyid'] . " and grp_gid = " . $a_syspwd['pwd_gid'] . " ";
          $q_sysgrp = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          $a_sysgrp = mysql_fetch_array($q_sysgrp);

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('users.fill.php?id=" . $a_manageusers['mu_id'] . "&pwd_id=" . $a_syspwd['pwd_id'] . "');showDiv('users-hide');\">";
          $linkend   = "</a>";

          $class = "ui-widget-content";

          $output .= "<tr>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_syspwd['pwd_user'] . $linkend . "</td>\n";
          $output .=   "<td class=\"" . $class . " delete\">"              . $locked                          . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $account                         . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_syspwd['pwd_uid']             . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_sysgrp['grp_name'] . " (" . $a_syspwd['pwd_gid'] . ")</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $gecos[0]                        . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $gecos[1]                        . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_syspwd['pwd_home']            . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_syspwd['pwd_shell']           . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_manageusers['mu_ticket']      . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_manageusers['mu_comment']     . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No Users defined.</td>\n";
        $output .= "</tr>\n";
      }

      mysql_free_result($q_syspwd);

      $output .= "</table>\n";

      print "document.getElementById('users_table').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.edit.mu_update.disabled = true;\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
