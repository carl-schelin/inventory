<?php
# Script: media.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "media.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],            10);
        $formVars['med_text']     = clean($_GET['med_text'],      30);
        $formVars['med_default']  = clean($_GET['med_default'],   10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['med_default'] == 'true') {
          $formVars['med_default'] = 1;
        } else {
          $formVars['med_default'] = 0;
        }

        if (strlen($formVars['med_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "med_text     = \"" . $formVars['med_text']    . "\"," .
            "med_default  =   " . $formVars['med_default'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_int_media set med_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_int_media set " . $q_string . " where med_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['med_text']);

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
      $output .= "  <th class=\"ui-state-default\">Media Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select med_id,med_text,med_default ";
      $q_string .= "from inv_int_media ";
      $q_string .= "order by med_text";
      $q_inv_int_media = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_int_media) > 0) {
        while ($a_inv_int_media = mysqli_fetch_array($q_inv_int_media)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('media.fill.php?id=" . $a_inv_int_media['med_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('media.del.php?id=" . $a_inv_int_media['med_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select int_id ";
          $q_string .= "from inv_interface ";
          $q_string .= "where int_media = " . $a_inv_int_media['med_id'] . " ";
          $q_inv_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_interface) > 0) {
            while ($a_inv_interface = mysqli_fetch_array($q_inv_interface)) {
              $total++;
            }
          }
          $class = "ui-widget-content";
          if ($a_inv_int_media['med_default']) {
            $class = "ui-state-highlight";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_int_media['med_text'] . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $total                              . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No Media Description entries</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_int_media);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
