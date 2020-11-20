<?php
# Script: message_group.mysql.php
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
    $package = "message_group.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (isset($_SESSION['sort'])) {
      $orderby = "order by " . clean($_SESSION['sort'], 20) . " ";
    } else {
      $orderby = "order by msg_group ";
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],               10);
        $formVars['msg_group']      = clean($_GET['msg_group'],       255);
        $formVars['msg_deleted']    = clean($_GET['msg_deleted'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['msg_deleted'] == 'true') {
          $formVars['msg_deleted'] = 1;
        } else {
          $formVars['msg_deleted'] = 0;
        }

        if (strlen($formVars['msg_group']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "msg_group       = \"" . $formVars['msg_group']       . "\"," .
            "msg_deleted     =   " . $formVars['msg_deleted'];

          if ($formVars['update'] == 0) {
            $query = "insert into message_group set msg_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update message_group set " . $q_string . " where msg_id = " . $formVars['id'];
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['msg_group']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Message Group Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('group-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"group-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Message Group Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an Message Group to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Message Group Management</strong> title bar to toggle the <strong>Message Group Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"message_group.php?sort=msg_group\">Description</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"message_group.php?sort=msg_deleted\">Deleted</a></th>";
      $output .= "</tr>";

      $q_string  = "select msg_id,msg_group,msg_deleted ";
      $q_string .= "from message_group ";
      $q_string .= $orderby;
      $q_message_group = mysqli_query($db, $q_string) or die (mysqli_error($db));
      while ($a_message_group = mysqli_fetch_array($q_message_group)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('message_group.fill.php?id="  . $a_message_group['msg_id'] . "');jQuery('#dialogGroup').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Delete\" onclick=\"delete_line('message_group.del.php?id=" . $a_message_group['msg_id'] . "');\">";
        $linkend = "</a>";

        if ($a_message_group['msg_deleted']) {
          $delete = "Yes";
          $class = "ui-state-highlight";
        } else {
          $delete = "No";
          $class = "ui-widget-content";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                     . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_message_group['msg_group']        . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">"              . $delete                                         . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysqli_free_result($q_message_group);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.groups.msg_group.value = '';\n";
      print "document.groups.msg_deleted.checked = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
