<?php
# Script: state.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "state.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],              10);
        $formVars['st_acronym']       = clean($_GET['st_acronym'],      10);
        $formVars['st_state']         = clean($_GET['st_state'],       255);
        $formVars['st_country']       = clean($_GET['st_country'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
    
        if (strlen($formVars['st_state']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "st_acronym      = \"" . $formVars['st_acronym']     . "\"," .
            "st_state        = \"" . $formVars['st_state']       . "\"," .
            "st_country      =   " . $formVars['st_country'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into states set st_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update states set " . $q_string . " where st_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['st_state']);

          mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete State</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">State</th>\n";
      $output .= "  <th class=\"ui-state-default\">Acronym</th>\n";
      $output .= "  <th class=\"ui-state-default\">Country</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select st_id,st_acronym,st_state,cn_country ";
      $q_string .= "from states ";
      $q_string .= "left join country on country.cn_id = states.st_country ";
      $q_string .= "order by st_state,cn_country ";
      $q_states = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_states) > 0) {
        while ($a_states = mysqli_fetch_array($q_states)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('state.fill.php?id="  . $a_states['st_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('state.del.php?id=" . $a_states['st_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_states['st_state']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $a_states['st_acronym']             . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $a_states['cn_country']             . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_states);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
