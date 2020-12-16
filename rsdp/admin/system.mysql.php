<?php
# Script: system.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "system.mysql.php";
    $formVars['update']        = clean($_GET['update'],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],             10);
        $formVars['os_vendor']     = clean($_GET['os_vendor'],     100);
        $formVars['os_software']   = clean($_GET['os_software'],   100);
        $formVars['os_exception']  = clean($_GET['os_exception'],   10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['os_exception'] == 'true') {
          $formVars['os_exception'] = 1;
        } else {
          $formVars['os_exception'] = 0;
        }

        if (strlen($formVars['os_software']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "os_vendor    = \"" . $formVars['os_vendor']    . "\"," . 
            "os_software  = \"" . $formVars['os_software']  . "\"," .
            "os_exception =   " . $formVars['os_exception'];

          if ($formVars['update'] == 0) {
            $query = "insert into operatingsystem set os_id = NULL, " . $q_string;
            $message = "Operating System added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update operatingsystem set " . $q_string . " where os_id = " . $formVars['id'];
            $message = "Operating System updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['prj_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Operating System Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('system-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"system-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Project Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Remove</strong> - Delete a project from the listing. Historical builds are still maintained so removing projects should be done with care.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Project to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Highlight</strong> A <span class=\"ui-state-highlight\">highlighted project</span> indicates a Project has been closed.</li>\n";
      $output .= "    <li>Click the <strong>Project Management</strong> title bar to toggle the <strong>Project Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output = "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Vendor</th>";
      $output .= "  <th class=\"ui-state-default\">Operating System</th>";
      $output .= "  <th class=\"ui-state-default\">Exception Required?</th>";
      $output .= "  <th class=\"ui-state-default\">Last Change By</th>";
      $output .= "</tr>";

      $count = 0;
      $q_string  = "select os_id,os_vendor,os_software,os_delete,usr_name,os_exception ";
      $q_string .= "from operatingsystem ";
      $q_string .= "left join users on users.usr_id = operatingsystem.os_user ";
      $q_string .= "order by os_vendor,os_software ";
      $q_operatingsystem = mysqli_query($db, $q_string) or die (mysqli_error($db));
      while ($a_operatingsystem = mysqli_fetch_array($q_operatingsystem)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('system.fill.php?id="  . $a_operatingsystem['os_id'] . "');jQuery('html,body').scrollTop(0);jQuery('#dialogSystem').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('system.del.php?id=" . $a_operatingsystem['os_id'] . "');\">";
        $linkundel = "<input type=\"button\" value=\"Undelete\" onclick=\"undelete_line('system.undel.php?id=" . $a_operatingsystem['os_id'] . "');\">";
        $linkend   = "</a>";

        if ($a_operatingsystem['os_delete']) {
          $class = "ui-state-highlight";
        } else {
          $class = "ui-widget-content";
        }

        if ($a_operatingsystem['os_exception']) {
          $exception = 'Yes';
        } else {
          $exception = 'No';
        }

        $output .= "<tr>";
        if ($a_operatingsystem['os_delete']) {
          $output .= "  <td class=\"" . $class . " delete\">" . $linkundel                                             . "</td>";
        } else {
          $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                               . "</td>";
        }
        $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_operatingsystem['os_vendor']   . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_operatingsystem['os_software'] . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $exception                        . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_operatingsystem['usr_name']    . $linkend . "</td>";
        $output .= "</tr>";

      }

      $output .= "</table>";

      mysqli_free_result($q_operatingsystem);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.dialog.os_vendor.value = '';\n";
      print "document.dialog.os_software.value = '';\n";
      print "document.dialog.os_exception.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
