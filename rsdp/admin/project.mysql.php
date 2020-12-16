<?php
# Script: project.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "project.mysql.php";
    $formVars['update']        = clean($_GET['update'],       10);
    $formVars['prj_group']     = clean($_GET['prj_group'],    10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['prj_group'] == '') {
      $formVars['prj_group'] = $_SESSION['group'];
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],           10);
        $formVars['prj_name']      = clean($_GET['prj_name'],     30);
        $formVars['prj_code']      = clean($_GET['prj_code'],     10);
        $formVars['prj_close']     = clean($_GET['prj_close'],    10);
        $formVars['prj_product']   = clean($_GET['prj_product'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['prj_close'] == 'true') {
          $formVars['prj_close'] = 1;
        } else {
          $formVars['prj_close'] = 0;
        }

        if (strlen($formVars['prj_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "prj_name      = \"" . $formVars['prj_name']  . "\"," .
            "prj_code      =   " . $formVars['prj_code']  . "," .
            "prj_close     =   " . $formVars['prj_close'] . "," .
            "prj_group     =   " . $formVars['prj_group'] . "," .
            "prj_product   =   " . $formVars['prj_product'];

          if ($formVars['update'] == 0) {
            $query = "insert into projects set prj_id = NULL, " . $q_string;
            $message = "Project added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update projects set " . $q_string . " where prj_id = " . $formVars['id'];
            $message = "Project updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['prj_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select grp_name ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $formVars['prj_group'] . " ";
      $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_groups = mysqli_fetch_array($q_groups);

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">" . $a_groups['grp_name'] . " Project Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('project-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"project-listing-help\" style=\"display: none\">\n";

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

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Code</th>\n";
      $output .= "  <th class=\"ui-state-default\">Product</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select prj_id,prj_name,prj_code,prj_close,prod_name ";
      $q_string .= "from projects ";
      $q_string .= "left join products on products.prod_id = projects.prj_product ";
      $q_string .= "where prj_group = " . $formVars['prj_group'] . " ";
      $q_string .= "order by prj_name ";
      $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_projects) > 0) {
        while ($a_projects = mysqli_fetch_array($q_projects)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('project.fill.php?id="  . $a_projects['prj_id'] . "');jQuery('html,body').scrollTop(0);jQuery('#dialogProject').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('project.del.php?id=" . $a_projects['prj_id'] . "');\">";
          $linkend   = "</a>";

          $class = "ui-widget-content";
          if ($a_projects['prj_close']) {
            $class = "ui-state-highlight";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                        . "</td>";
          }
          $output .= "  <td class=\"" . $class . " delete\">"   . $linkstart . $a_projects['prj_id']      . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_projects['prj_name']    . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_projects['prj_code']    . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_projects['prod_name']   . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_projects);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.dialog.prj_name.value = '';\n";
      print "document.dialog.prj_code.value = '';\n";
      print "document.dialog.prj_close.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
