<?php
# Script: titles.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "titles.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']        = clean($_GET['id'],               10);
        $formVars['tit_name']  = clean($_GET['tit_name'],  60);
        $formVars['tit_level'] = clean($_GET['tit_level'], 10);
        $formVars['tit_order'] = clean($_GET['tit_order'], 10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['tit_level'] == '') {
          $formVars['tit_level'] = 0;
        }
        if ($formVars['tit_order'] == '') {
          $formVars['tit_order'] = 0;
        }

        if (strlen($formVars['tit_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "tit_name  = \"" . $formVars['tit_name']  . "\"," .
            "tit_level =   " . $formVars['tit_level'] . "," .
            "tit_order =   " . $formVars['tit_order'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_titles set tit_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_titles set " . $q_string . " where tit_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['tit_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Title</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Level</th>\n";
      $output .= "  <th class=\"ui-state-default\">Order</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select tit_id,tit_name,tit_level,tit_order ";
      $q_string .= "from inv_titles ";
      $q_string .= "order by tit_name ";
      $q_inv_titles = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_titles) > 0) {
        while ($a_inv_titles = mysqli_fetch_array($q_inv_titles)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('titles.fill.php?id=" . $a_inv_titles['tit_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('titles.del.php?id="  . $a_inv_titles['tit_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_inv_titles['tit_name']  . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_inv_titles['tit_level'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_inv_titles['tit_order'] . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_titles);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
