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
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],                   10);
        $formVars['prj_name']       = clean($_GET['prj_name'],            100);
        $formVars['prj_code']       = clean($_GET['prj_code'],             10);
        $formVars['prj_task']       = clean($_GET['prj_task'],             30);
        $formVars['prj_desc']       = clean($_GET['prj_desc'],            100);
        $formVars['prj_directory']  = clean($_GET['prj_directory'],       100);
        $formVars['prj_group']      = clean($_GET['prj_unit'],             10);
        $formVars['prj_product']    = clean($_GET['prj_service'],          10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['prj_group'] == '') {
          $formVars['prj_group'] = 0;
        }

        if (strlen($formVars['prod_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "prj_name       = \"" . $formVars['prj_name']      . "\"," .
            "prj_code       = \"" . $formVars['prj_code']      . "\"," .
            "prj_task       = \"" . $formVars['prj_code']      . "\"," .
            "prj_desc       = \"" . $formVars['prj_desc']      . "\"," .
            "prj_directory  = \"" . $formVars['prj_directory'] . "\"," .
            "prj_group      =   " . $formVars['prj_group']     . "," .
            "prj_product    =   " . $formVars['prj_product'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_projects set prj_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_projects set " . $q_string . " where proj_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['prj_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Project</th>";
      }
      $output .= "  <th class=\"ui-state-default\">Product Name</th>";
      $output .= "  <th class=\"ui-state-default\">Project Name</th>";
      $output .= "  <th class=\"ui-state-default\">Project Code</th>";
      $output .= "  <th class=\"ui-state-default\">Project Task</th>";
      $output .= "  <th class=\"ui-state-default\">Project Description</th>";
      $output .= "  <th class=\"ui-state-default\">Terraform</th>";
      $output .= "  <th class=\"ui-state-default\">Members</th>";
      $output .= "  <th class=\"ui-state-default\">Group</th>";
      $output .= "</tr>";

      $q_string  = "select prj_id,prj_name,prj_code,prj_task,prj_desc,prj_directory,grp_name,prod_name ";
      $q_string .= "from inv_projects ";
      $q_string .= "left join inv_groups on inv_groups.grp_id = inv_projects.prj_group ";
      $q_string .= "left join inv_products on inv_products.prod_id = inv_projects.prj_product ";
      $q_string .= "order by prj_name,prod_name ";
      $q_inv_projects = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_projects) > 0) {
        while ($a_inv_projects = mysqli_fetch_array($q_inv_projects)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('project.fill.php?id="  . $a_inv_projects['prj_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('project.del.php?id=" . $a_inv_projects['prj_id'] . "');\">";
          $prjstart = "<a href=\"project.members.php?id=" . $a_inv_projects['prj_id'] . "\" target=\"_blank\">";
          $linkend = "</a>";

          $class = "ui-widget-content";

          $total = 0;
          $q_string  = "select inv_id ";
          $q_string .= "from inv_inventory ";
          $q_string .= "where inv_project = " . $a_inv_projects['prj_id'] . " ";
          $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_inventory) > 0) {
            while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
              $total++;
            }
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_projects['prod_name']                . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_projects['prj_name']      . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_projects['prj_code']                 . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_projects['prj_task']                 . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_projects['prj_desc']                 . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_projects['prj_directory']            . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">" .  $prjstart . $total                           . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_projects['grp_name']                 . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No Projects Found</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_projects);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
