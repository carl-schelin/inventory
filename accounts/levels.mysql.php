<?php
# Script: levels.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "levels.mysql.php";
    $formVars['update']        = clean($_GET['update'],        10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Admin)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],            10);
        $formVars['lvl_name']      = clean($_GET['lvl_name'],     255);
        $formVars['lvl_level']     = clean($_GET['lvl_level'],     10);
        $formVars['lvl_disabled']  = clean($_GET['lvl_disabled'],  10);
        $formVars['lvl_changedby'] = clean($_SESSION['uid'],       10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['lvl_level'] == '') {
          $formVars['lvl_level'] = 0;
        }

        if (strlen($formVars['lvl_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "lvl_name      = \"" . $formVars['lvl_name']      . "\"," . 
            "lvl_level     =   " . $formVars['lvl_level']     . "," . 
            "lvl_disabled  =   " . $formVars['lvl_disabled']  . "," . 
            "lvl_changedby =   " . $formVars['lvl_changedby'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into levels set lvl_id = NULL," . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update levels set " . $q_string . " where lvl_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['lvl_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Level</th>";
      }
      $output .= "  <th class=\"ui-state-default\">Access Level</th>";
      $output .= "  <th class=\"ui-state-default\">Level Name</th>";
      $output .= "  <th class=\"ui-state-default\">Members</th>";
      $output .= "</tr>";

      $q_string  = "select lvl_id,lvl_name,lvl_level,lvl_disabled ";
      $q_string .= "from levels ";
      $q_string .= "order by lvl_level,lvl_name";
      $q_levels = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_levels) > 0) {
        while ($a_levels = mysqli_fetch_array($q_levels)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('levels.fill.php?id=" . $a_levels['lvl_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_level('levels.del.php?id=" . $a_levels['lvl_id'] . "');\">";
          $linkend = "</a>";

          $class = "ui-widget-content";
          if ($a_levels['lvl_disabled']) {
            $class = "ui-state-error";
          }

          $total = 0;
          $disabled = 0;
          $q_string  = "select usr_id,usr_disabled ";
          $q_string .= "from inv_users ";
          $q_string .= "where usr_level = " . $a_levels['lvl_id'] . " ";
          $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_users) > 0) {
            while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
              if ($a_inv_users['usr_disabled'] == 0) {
                $total++;
              } else {
                $disabled++;
              }
            }
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_levels['lvl_level']     . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_levels['lvl_name']      . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">" . $total . " (" . $disabled . ")</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_levels);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
