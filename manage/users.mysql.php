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
    $formVars['update']   = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],           10);
        $formVars['mu_username']   = clean($_GET['mu_username'],  60);
        $formVars['mu_name']       = clean($_GET['mu_name'],      60);
        $formVars['mu_email']      = clean($_GET['mu_email'],     60);
        $formVars['mu_account']    = clean($_GET['mu_account'],   10);
        $formVars['mu_comment']    = clean($_GET['mu_comment'],  255);
        $formVars['mu_locked']     = clean($_GET['mu_locked'],    10);
        $formVars['mu_ticket']     = clean($_GET['mu_ticket'],    60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['mu_locked'] == 'true') {
          $formVars['mu_locked'] = 1;
        } else {
          $formVars['mu_locked'] = 0;
        }
        if ($formVars['mu_exclude'] == 'true') {
          $formVars['mu_exclude'] = 1;
        } else {
          $formVars['mu_exclude'] = 0;
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "mu_username    = \"" . $formVars['mu_username']   . "\"," .
          "mu_name        = \"" . $formVars['mu_name']       . "\"," .
          "mu_email       = \"" . $formVars['mu_email']      . "\"," .
          "mu_account     =   " . $formVars['mu_account']    . "," .
          "mu_comment     = \"" . $formVars['mu_comment']    . "\"," .
          "mu_locked      =   " . $formVars['mu_locked']     . "," .
          "mu_ticket      = \"" . $formVars['mu_ticket']     . "\"";

        if ($formVars['update'] == 0) {
          $query = "insert into manageusers set mu_id = null," . $q_string;
          $message = "User added.";
        }
        if ($formVars['update'] == 1) {
          $query = "update manageusers set " . $q_string . " where mu_id = " . $formVars['id'];
          $message = "User updated.";
        }

        logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['mu_username']);

        mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

        print "alert('" . $message . "');\n";
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">GECOS Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('gecos-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"gecos-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>GECOS Listing</strong></p>\n";

      $output .= "<p>This listing is used to create the <strong>valid.email</strong> file. The file is used by the <strong>update.gecos</strong> script to ensure the users ";
      $output .= "GECOS field is accurate and consistent on all systems. Comments are added to the <strong>valid.email</strong> file as a comment.</p>\n";

      $output .= "<p>Only entries with a name and email address will be exported into the <strong>valid.email</strong> file.</p>\n";

      $output .= "<p>Any line that is <span class=\"ui-state-highlight\">highlighted</span> is an account that doesn't exist on any live system and will not be added to ";
      $output .= "the <strong>valid.email</strong> file.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Username</th>\n";
      $output .= "  <th class=\"ui-state-default\">GECOS</th>\n";
      $output .= "  <th class=\"ui-state-default\">Comment</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select mu_id,mu_username,mu_name,mu_email,mu_comment ";
      $q_string .= "from manageusers ";
      $q_string .= "where (mu_account = 0 or mu_account = 2) and mu_name != \"\" and mu_email != \"\" ";
      $q_string .= "order by mu_username ";
      $q_manageusers = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_manageusers) > 0) {
        while ($a_manageusers = mysqli_fetch_array($q_manageusers)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_manageusers['mu_id'] . "');jQuery('#dialogUsers').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_users('users.del.php?id=" . $a_manageusers['mu_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select pwd_user ";
          $q_string .= "from syspwd ";
          $q_string .= "left join inventory on inventory.inv_id = syspwd.pwd_companyid ";
          $q_string .= "where pwd_user = \"" . $a_manageusers['mu_username'] . "\" and inv_status = 0 ";
          $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_syspwd) > 0) {
            $class = "ui-widget-content";
          } else {
            $class = "ui-state-highlight";
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\" width=\"60\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_manageusers['mu_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_username'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_name'] . "," . $a_manageusers['mu_email'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_comment'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_manageusers);

      print "document.getElementById('gecos_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";



      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Lock User Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('lockuser-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"lockuser-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>Lockuser Listing</strong></p>\n";

      $output .= "<p>This listing is used to create the <strong>lockuser.dat</strong> file which is used by the <strong>lockuser</strong> script to lock a user ";
      $output .= "that has left the company. Comments and Ticket info are added to the <strong>lockuser.dat</strong> file as a comment. <strong>Do not lock users ";
      $output .= "who change departments with this feature.</strong></p>\n";

      $output .= "<p>Any line that is <span class=\"ui-state-highlight\">highlighted</span> is an account that doesn't exist on any live system and will not be added to ";
      $output .= "the <strong>lockuser.dat</strong> file.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Username</th>\n";
      $output .= "  <th class=\"ui-state-default\">Email</th>\n";
      $output .= "  <th class=\"ui-state-default\">Ticket Info</th>\n";
      $output .= "  <th class=\"ui-state-default\">Comment</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select mu_id,mu_username,mu_email,mu_ticket,mu_comment ";
      $q_string .= "from manageusers ";
      $q_string .= "where mu_locked = 1 ";
      $q_string .= "order by mu_username ";
      $q_manageusers = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_manageusers) > 0) {
        while ($a_manageusers = mysqli_fetch_array($q_manageusers)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_manageusers['mu_id'] . "');jQuery('#dialogUsers').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_users('users.del.php?id=" . $a_manageusers['mu_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select pwd_user ";
          $q_string .= "from syspwd ";
          $q_string .= "left join inventory on inventory.inv_id = syspwd.pwd_companyid ";
          $q_string .= "where pwd_user = \"" . $a_manageusers['mu_username'] . "\" and inv_status = 0 ";
          $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_syspwd) > 0) {
            $class = "ui-widget-content";
          } else {
            $class = "ui-state-highlight";
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\" width=\"60\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_manageusers['mu_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_username'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_email'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_ticket'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_comment'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_manageusers);

      print "document.getElementById('lockuser_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";


      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Exclude System Account Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('system-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"system-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>Exclude System Account Listing</strong></p>\n";

      $output .= "<p>This listing is used to create the <strong>users.exclude</strong> file which is used by the <strong>chkservers</strong> script to block the ";
      $output .= "reporting of the system user as not being employed any further. This identifies only system level accounts which aren't owned or managed ";
      $output .= "by other Operations teams. Comments are added to the <strong>users.exclude</strong> file as a comment.</p>\n";

      $output .= "<p>The reason for two entries is that the system account listing will not have a point of contact which a service account should have.</p>\n";

      $output .= "<p>Any line that is <span class=\"ui-state-highlight\">highlighted</span> is an account that doesn't exist on any live system and will not be added to ";
      $output .= "the <strong>users.exclude</strong> file.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Username</th>\n";
      $output .= "  <th class=\"ui-state-default\">Comment</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select mu_id,mu_username ";
      $q_string .= "from manageusers ";
      $q_string .= "where mu_account = 1 ";
      $q_string .= "order by mu_username ";
      $q_manageusers = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_manageusers) > 0) {
        while ($a_manageusers = mysqli_fetch_array($q_manageusers)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_manageusers['mu_id'] . "');jQuery('#dialogUsers').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_users('users.del.php?id=" . $a_manageusers['mu_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select pwd_user ";
          $q_string .= "from syspwd ";
          $q_string .= "left join inventory on inventory.inv_id = syspwd.pwd_companyid ";
          $q_string .= "where pwd_user = \"" . $a_manageusers['mu_username'] . "\" and inv_status = 0 ";
          $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_syspwd) > 0) {
            $class = "ui-widget-content";
          } else {
            $class = "ui-state-highlight";
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\" width=\"60\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_manageusers['mu_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_username'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_comment'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_manageusers);

      print "document.getElementById('system_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";


      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Exclude Service Account Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('service-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"service-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>Exclude Service Account Listing</strong></p>\n";

      $output .= "<p>This listing is used to create the <strong>users.exclude</strong> file which is used by the <strong>chkserver</strong> script to block the ";
      $output .= "reporting of the service account user as not being employed any further. This identifies service accounts which are managed by other ";
      $output .= "Operations teams. These accounts are also displayed in the GECOS tab where system accounts are not. Comments are added to the ";
      $output .= "<strong>users.exclude</strong> file as a comment.</p>\n";

      $output .= "<p>The reason for two listings for a single file is that the expectation is that service accounts will have a user or group point of contact ";
      $output .= "email address. This listing provides both the name and email address if found for a service account.</p>\n";

      $output .= "<p>Any line that is <span class=\"ui-state-highlight\">highlighted</span> is an account that doesn't exist on any live system and will not be added to ";
      $output .= "the <strong>users.exclude</strong> file.</p>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Username</th>\n";
      $output .= "  <th class=\"ui-state-default\">GECOS</th>\n";
      $output .= "  <th class=\"ui-state-default\">Comment</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select mu_id,mu_username,mu_name,mu_email,mu_comment ";
      $q_string .= "from manageusers ";
      $q_string .= "where mu_account = 2 ";
      $q_string .= "order by mu_username ";
      $q_manageusers = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_manageusers) > 0) {
        while ($a_manageusers = mysqli_fetch_array($q_manageusers)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('users.fill.php?id=" . $a_manageusers['mu_id'] . "');jQuery('#dialogUsers').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_users('users.del.php?id=" . $a_manageusers['mu_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select pwd_user ";
          $q_string .= "from syspwd ";
          $q_string .= "left join inventory on inventory.inv_id = syspwd.pwd_companyid ";
          $q_string .= "where pwd_user = \"" . $a_manageusers['mu_username'] . "\" and inv_status = 0 ";
          $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_syspwd) > 0) {
            $class = "ui-widget-content";
          } else {
            $class = "ui-state-highlight";
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\" width=\"60\">" . $linkdel   . "</td>\n";
          }
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_manageusers['mu_id']   . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_username'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_name'] . "," . $a_manageusers['mu_email'] . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_manageusers['mu_comment'] . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_manageusers);

      print "document.getElementById('service_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";


      print "document.users.mu_username.value = '';\n";
      print "document.users.mu_name.value = '';\n";
      print "document.users.mu_email.value = '';\n";
      print "document.users.mu_account[0].checked = true;\n";
      print "document.users.mu_comment.value = '';\n";
      print "document.users.mu_locked.checked = false;\n";
      print "document.users.mu_ticket.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
