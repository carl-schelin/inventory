<?php
# Script: application.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "application.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (isset($_SESSION['sort'])) {
      $orderby = "order by " . clean($_SESSION['sort'], 20) . " ";
    } else {
      $orderby = "order by app_description ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],               10);
        $formVars['app_description']  = clean($_GET['app_description'], 255);
        $formVars['app_deleted']      = clean($_GET['app_deleted'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['app_deleted'] == 'true') {
          $formVars['app_deleted'] = 1;
        } else {
          $formVars['app_deleted'] = 0;
        }

        if (strlen($formVars['app_description']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "app_description   = \"" . $formVars['app_description'] . "\"," .
            "app_deleted       =   " . $formVars['app_deleted'];

          if ($formVars['update'] == 0) {
            $query = "insert into application set app_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update application set " . $q_string . " where app_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['app_description']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Application Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('application-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"application-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Application Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an Application to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Application Management</strong> title bar to toggle the <strong>Application Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"application.php?sort=app_description\">Description</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"application.php?sort=app_deleted\">Deleted</a></th>";
      $output .= "</tr>";

      $q_string  = "select app_id,app_description,app_deleted ";
      $q_string .= "from application ";
      $q_string .= $orderby;
      $q_application = mysqli_query($db, $q_string) or die (mysqli_error($db));
      while ($a_application = mysqli_fetch_array($q_application)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('application.fill.php?id="  . $a_application['app_id'] . "');jQuery('#dialogApplication').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Delete\" onclick=\"delete_line('application.del.php?id=" . $a_application['app_id'] . "');\">";
        $linkend = "</a>";

        if ($a_application['app_deleted']) {
          $delete = "Yes";
          $class = "ui-state-highlight";
        } else {
          $delete = "No";
          $class = "ui-widget-content";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                  . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_application['app_description'] . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">"              . $delete                                      . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysqli_free_result($q_application);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.application.app_description.value = '';\n";
      print "document.application.app_deleted.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
