<?php
# Script: objects.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "objects.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (isset($_SESSION['sort'])) {
      $orderby = "order by " . clean($_SESSION['sort'], 20) . " ";
    } else {
      $orderby = "order by obj_name ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],               10);
        $formVars['obj_name']       = clean($_GET['obj_name'],        255);
        $formVars['obj_deleted']    = clean($_GET['obj_deleted'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['obj_deleted'] == 'true') {
          $formVars['obj_deleted'] = 1;
        } else {
          $formVars['obj_deleted'] = 0;
        }

        if (strlen($formVars['obj_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "obj_name       = \"" . $formVars['obj_name']       . "\"," .
            "obj_deleted    =   " . $formVars['obj_deleted'];

          if ($formVars['update'] == 0) {
            $query = "insert into objects set obj_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update objects set " . $q_string . " where obj_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['obj_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Object Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('object-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"object-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Object Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an Object to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Object Management</strong> title bar to toggle the <strong>Object Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"objects.php?sort=obj_name\">Description</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"objects.php?sort=obj_deleted\">Deleted</a></th>";
      $output .= "</tr>";

      $q_string  = "select obj_id,obj_name,obj_deleted ";
      $q_string .= "from objects ";
      $q_string .= $orderby;
      $q_objects = mysqli_query($db, $q_string) or die (mysqli_error($db));
      while ($a_objects = mysqli_fetch_array($q_objects)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('objects.fill.php?id="  . $a_objects['obj_id'] . "');jQuery('#dialogObject').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Delete\" onclick=\"delete_line('objects.del.php?id=" . $a_objects['obj_id'] . "');\">";
        $linkend = "</a>";

        if ($a_objects['obj_deleted']) {
          $delete = "Yes";
          $class = "ui-state-highlight";
        } else {
          $delete = "No";
          $class = "ui-widget-content";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                              . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_objects['obj_name']        . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">"              . $delete                                  . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysqli_free_result($q_objects);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.objects.obj_name.value = '';\n";
      print "document.objects.obj_deleted.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
