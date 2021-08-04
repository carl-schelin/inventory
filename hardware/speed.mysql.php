<?php
# Script: speed.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "speed.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']        = clean($_GET['id'],         10);
        $formVars['spd_text']  = clean($_GET['spd_text'],   30);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['spd_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "spd_text     = \"" . $formVars['spd_text'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into int_speed set spd_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update int_speed set " . $q_string . " where spd_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['spd_text']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Description</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Speed Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select spd_id,spd_text ";
      $q_string .= "from int_speed ";
      $q_string .= "order by spd_text";
      $q_int_speed = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_int_speed) > 0) {
        while ($a_int_speed = mysqli_fetch_array($q_int_speed)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('speed.fill.php?id=" . $a_int_speed['spd_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('speed.del.php?id=" . $a_int_speed['spd_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select ast_id ";
          $q_string .= "from assets ";
          $q_string .= "where ast_modelid = " . $a_int_speed['spd_id'] . " ";
          $q_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $total = mysqli_num_rows($q_assets);

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_int_speed['spd_text'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                              . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No Speed Description entries</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_int_speed);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
