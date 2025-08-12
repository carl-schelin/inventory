<?php
# Script: plugtype.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "plugtype.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],            10);
        $formVars['plug_text']    = clean($_GET['plug_text'],     60);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['plug_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "plug_text       = \"" . $formVars['plug_text'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_int_plugtype set plug_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_int_plugtype set " . $q_string . " where plug_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['plug_text']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Plug Type</th>";
      }
      $output .= "  <th class=\"ui-state-default\">Plug Name</th>";
      $output .= "  <th class=\"ui-state-default\">Members</th>";
      $output .= "</tr>";

      $total = 0;
      $q_string  = "select plug_id,plug_text ";
      $q_string .= "from inv_int_plugtype ";
      $q_string .= "order by plug_text";
      $q_inv_int_plugtype = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_int_plugtype) > 0) {
        while ($a_inv_int_plugtype = mysqli_fetch_array($q_inv_int_plugtype)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('plugtype.fill.php?id="     . $a_inv_int_plugtype['plug_id']   . "');jQuery('#dialogUpdate').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('plugtype.del.php?id=" . $a_inv_int_plugtype['plug_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select mod_id ";
          $q_string .= "from inv_models ";
          $q_string .= "where mod_plugtype = " . $a_inv_int_plugtype['plug_id'] . " ";
          $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $total = mysqli_num_rows($q_models);

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_int_plugtype['plug_text']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                                         . "</td>";
          $output .= "</tr>";
        }
      } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No power plug types found</td>";
          $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_int_plugtype);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
