<?php
# Script: source_node.mysql.php
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
    $package = "source_node.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (isset($_SESSION['sort'])) {
      $orderby = "order by " . clean($_SESSION['sort'], 20) . " ";
    } else {
      $orderby = "order by src_node ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],               10);
        $formVars['src_node']       = clean($_GET['src_node'],        255);
        $formVars['src_deleted']    = clean($_GET['src_deleted'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['src_deleted'] == 'true') {
          $formVars['src_deleted'] = 1;
        } else {
          $formVars['src_deleted'] = 0;
        }

        if (strlen($formVars['src_node']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "src_node       = \"" . $formVars['src_node']       . "\"," .
            "src_deleted    =   " . $formVars['src_deleted'];

          if ($formVars['update'] == 0) {
            $query = "insert into source_node set src_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update source_node set " . $q_string . " where src_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['src_node']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Source Node Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('node-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"node-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Source Node Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Source Node to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Source Node Management</strong> title bar to toggle the <strong>Source Node Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"source_node.php?sort=src_node\">Description</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"source_node.php?sort=src_deleted\">Deleted</a></th>";
      $output .= "</tr>";

      $q_string  = "select src_id,src_node,src_deleted ";
      $q_string .= "from source_node ";
      $q_string .= $orderby;
      $q_source_node = mysqli_query($db, $q_string) or die (mysqli_error($db));
      while ($a_source_node = mysqli_fetch_array($q_source_node)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('source_node.fill.php?id="  . $a_source_node['src_id'] . "');jQuery('#dialogNode').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Delete\" onclick=\"delete_line('source_node.del.php?id=" . $a_source_node['src_id'] . "');\">";
        $linkend = "</a>";

        if ($a_source_node['src_deleted']) {
          $delete = "Yes";
          $class = "ui-state-highlight";
        } else {
          $delete = "No";
          $class = "ui-widget-content";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                  . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_source_node['src_node']        . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">"              . $delete                                      . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysqli_free_result($q_source_node);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.nodes.src_node.value = '';\n";
      print "document.nodes.src_deleted.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
