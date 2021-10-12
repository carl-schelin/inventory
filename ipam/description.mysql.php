<?php
# Script: description.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "description.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],               10);
        $formVars['itp_name']        = clean($_GET['itp_name'],         30);
        $formVars['itp_acronym']     = clean($_GET['itp_acronym'],      10);
        $formVars['itp_description'] = clean($_GET['itp_description'], 255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['itp_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "itp_name        = \"" . $formVars['itp_name']        . "\"," .
            "itp_acronym     = \"" . $formVars['itp_acronym']     . "\"," .
            "itp_description = \"" . $formVars['itp_description'] . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into int_types set itp_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update int_types set " . $q_string . " where itp_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['itp_name']);

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
      $output .= "  <th class=\"ui-state-default\">Descriptive Label</th>\n";
      $output .= "  <th class=\"ui-state-default\">Acronym</th>\n";
      $output .= "  <th class=\"ui-state-default\">Type Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select itp_id,itp_name,itp_acronym,itp_description ";
      $q_string .= "from int_types ";
      $q_string .= "order by itp_name ";
      $q_int_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_int_types) > 0) {
        while ($a_int_types = mysqli_fetch_array($q_int_types)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('description.fill.php?id="  . $a_int_types['itp_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('description.del.php?id=" . $a_int_types['itp_id'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select ast_id ";
          $q_string .= "from assets ";
          $q_string .= "where ast_modelid = " . $a_int_types['itp_id'] . " ";
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
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_int_types['itp_name']        . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_int_types['itp_acronym']                . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                     . $a_int_types['itp_description']            . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total                                     . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_int_types);

      print "document.getElementById('table_mysql').innerHTML = '"   . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
