<?php
# Script: checklist.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "checklist.mysql.php";
    $formVars['update']         = clean($_GET['update'],      10);
    $formVars['chk_group']      = clean($_GET['chk_group'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['chk_group'] == '') {
      $formVars['chk_group'] = $_SESSION['group'];
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],          10);
        $formVars['chk_index']      = clean($_GET['chk_index'],   10);
        $formVars['chk_text']       = clean($_GET['chk_text'],   255);
        $formVars['chk_link']       = clean($_GET['chk_link'],   255);
        $formVars['chk_task']       = clean($_GET['chk_task'],    10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['chk_index'] == '') {
# get the last index if unset
          $q_string  = "select chk_index ";
          $q_string .= "from checklist ";
          $q_string .= "where chk_task = " . $formVars['chk_task'] . " and chk_group = " . $formVars['chk_group'] . " ";
          $q_string .= "order by chk_index desc ";
          $q_string .= "limit 1 ";
          $q_checklist = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_checklist) > 0) {
            $a_checklist = mysqli_fetch_array($q_checklist);
            $formVars['chk_index'] = $a_checklist['chk_index'] + 1;
          } else {
            $formVars['chk_index'] = 1;
          }
        }

        if (strlen($formVars['chk_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# see if there is already an entry for the index requested (but only if adding a new entry)
# if so, then loop through the remaining entries incrementing the index by 1
# then save the new entry.

# if we're adding a new item (update == 0), see if the requested index (chk_index) is already in use
          if ($formVars['update'] == 0) {
            $q_string  = "select chk_index ";
            $q_string .= "from checklist ";
            $q_string .= "where chk_task = " . $formVars['chk_task'] . " and chk_group = " . $formVars['chk_group'] . " and chk_index = " . $formVars['chk_index'];
            $q_checklist = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# if it is in use already, increment all following indexes by one
            if (mysqli_num_rows($q_checklist) > 0) {
              $a_checklist = mysqli_fetch_array($q_checklist);

              $q_string  = "select chk_id,chk_index ";
              $q_string .= "from checklist ";
              $q_string .= "where chk_group = " . $formVars['chk_group'] . " ";
              $q_string .= "and chk_task = " . $formVars['chk_task'] . " ";
              $q_string .= "and chk_index >= " . $a_checklist['chk_index'] . " ";
              $q_string .= "order by chk_index";
              $q_checklist = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
              while ($a_checklist = mysqli_fetch_array($q_checklist)) {
                $q_string = "update checklist set chk_index = " . ($a_checklist['chk_index'] + 1) . " where chk_id = " . $a_checklist['chk_id'];
                $q_updatecl = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
              }
            }
          }

          $q_string = 
            "chk_group =   " . $formVars['chk_group'] . "," . 
            "chk_index =   " . $formVars['chk_index'] . "," . 
            "chk_text  = \"" . $formVars['chk_text']  . "\"," . 
            "chk_link  = \"" . $formVars['chk_link']  . "\"," . 
            "chk_task  =   " . $formVars['chk_task'];


          if ($formVars['update'] == 0) {
            $query = "insert into checklist set chk_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update checklist set " . $q_string . " where chk_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['chk_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }



      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select grp_name ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $formVars['chk_group'] . " ";
      $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_groups = mysqli_fetch_array($q_groups);

# need to loop through the various checklist possibilities and display them.
# The display is only for the group selected for display.
# mysql spans:
# Systems, 10: installed_mysql
# Systems, 12: configured_mysql
# Network, 4: networking_mysql
# Storage, 3: storage_mysql
# Storage, 11: provisioned_mysql
# Data Center/Virtualization, 5: datavirt_mysql
# Backups, 13: backups_mysql
# Monitoring, 14: monitored_mysql
# Monitoring, 16: app_monitored_mysql
# Applications, 15: app_installed_mysql
# Applications, 17: app_configured_mysql
# InfoSec, 18: infosec_mysql
# 
# array with each of the mysql entries at the appropriate task

      $task[3] = "storage";
      $task[4] = "networking";
      $task[5] = "datacenter";
      $task[6] = "virtualization";
      $task[7] = "";
      $task[8] = "";
      $task[9] = "";
      $task[10] = "installed";
      $task[11] = "provisioned";
      $task[12] = "configured";
      $task[13] = "backups";
      $task[14] = "monitored";
      $task[15] = "app_installed";
      $task[16] = "app_monitored";
      $task[17] = "app_configured";
      $task[18] = "infosec";

      for ($i = 3; $i < 19; $i++) {
        if (strlen($task[$i]) > 0) {
          $output  = "<p></p>\n";
          $output .= "<table class=\"ui-styled-table\">\n";
          $output .= "<tr>\n";
          $output .= "  <th class=\"ui-state-default\">" . $a_groups['grp_name'] . " Checklist Listing</th>\n";
          $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('" . $task[$i] . "-listing-help');\">Help</a></th>\n";
          $output .= "</tr>\n";
          $output .= "</table>\n";

          $output .= "<div id=\"" . $task[$i] . "-listing-help\" style=\"display: none\">\n";

          $output .= "<div class=\"main-help ui-widget-content\">\n";
          $output .= "<ul>\n";
          $output .= "  <li><strong>Checklist Listing</strong>\n";
          $output .= "  <ul>\n";
          $output .= "    <li><strong>Delete (x)</strong> - Delete a task from the listing.</li>\n";
          $output .= "    <li><strong>Editing</strong> - Click on a task to edit it.</li>\n";
          $output .= "  </ul></li>\n";
          $output .= "</ul>\n";

          $output .= "<ul>\n";
          $output .= "  <li><strong>Notes</strong>\n";
          $output .= "  <ul>\n";
          $output .= "    <li>Click the <strong>Checklist Management</strong> title bar to toggle the <strong>Checklist Form</strong>.</li>\n";
          $output .= "  </ul></li>\n";
          $output .= "</ul>\n";

          $output .= "</div>\n";

          $output .= "</div>\n";

          $output .= "<table class=\"ui-styled-table\">";
          $output .= "<tr>";
          $output .= "  <th class=\"ui-state-default\">Del</th>";
          $output .= "  <th class=\"ui-state-default\">List Order</th>";
          $output .= "  <th class=\"ui-state-default\">Text Description</th>";
          $output .= "  <th class=\"ui-state-default\">Document Link</th>";
          $output .= "</tr>";

          $q_string  = "select chk_id,chk_group,grp_name,chk_index,chk_text,chk_link ";
          $q_string .= "from checklist ";
          $q_string .= "left join groups on groups.grp_id = checklist.chk_group ";
          $q_string .= "where chk_group = " . $formVars['chk_group'] . " and chk_task = " . $i . " ";
          $q_string .= "order by chk_group,chk_index";
          $q_checklist = mysqli_query($db, $q_string) or die (mysqli_error($db));
          if (mysqli_num_rows($q_checklist) > 0) {
            while ($a_checklist = mysqli_fetch_array($q_checklist)) {

              $linkstart = "<a href=\"#\" onclick=\"show_file('checklist.fill.php?id=" . $a_checklist['chk_id'] . "');showDiv('checklist-hide');\">";
              $linkend   = "</a>";
              if (check_grouplevel($db, $a_checklist['chk_group'])) {
                $linkdel = "<a href=\"#\" onclick=\"delete_line('checklist.del.php?id=" . $a_checklist['chk_id'] . "&task=" . $i . "');\">";
              } else {
                $linkdel = '';
              }

              $output .= "<tr>";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "x"                       . $linkend . "</td>";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_checklist['chk_index'] . $linkend . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_checklist['chk_text']  . $linkend . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_checklist['chk_link']  . $linkend . "</td>";
              $output .= "</tr>";

            }

            mysqli_free_result($q_checklist);

          } else {
            $output .= "<tr>";
            $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">Checklist not created.</td>";
            $output .= "</tr>";
          }

          $output .= "</table>";

          print "document.getElementById('" . $task[$i] . "_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";
        }
      }

      print "document.checklists.chk_text.value = '';\n";
      print "document.checklists.chk_link.value = '';\n";

      print "document.checklists.update.disabled = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
