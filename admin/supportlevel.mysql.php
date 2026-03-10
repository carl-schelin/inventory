<?php
# Script: supportlevel.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "supportlevel.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],                50);
        $formVars['slv_value']      = clean($_GET['slv_value'],        100);
        $formVars['slv_translate']  = clean($_GET['slv_translate'],    100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['slv_value']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "slv_value          = \"" . $formVars['slv_value']          . "\"," .
            "slv_translate      = \"" . $formVars['slv_translate']      . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_supportlevel set slv_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_supportlevel set " . $q_string . " where slv_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sup_company']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Support Levels</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Support Level</th>\n";
      $output .= "  <th class=\"ui-state-default\">Short Value</th>\n";
      $output .= "  <th class=\"ui-state-default\">Hardware Members</th>\n";
      $output .= "  <th class=\"ui-state-default\">Software Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select slv_id,slv_value,slv_translate ";
      $q_string .= "from inv_supportlevel ";
      $q_string .= "order by slv_value ";
      $q_inv_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_supportlevel) > 0) {
        while ($a_inv_supportlevel = mysqli_fetch_array($q_inv_supportlevel)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('supportlevel.fill.php?id="  . $a_inv_supportlevel['slv_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('supportlevel.del.php?id=" . $a_inv_supportlevel['slv_id'] . "');\">";
          $linkend   = "</a>";

          $hwtotal = 0;
          $q_string  = "select sup_id ";
          $q_string .= "from inv_support ";
          $q_string .= "where sup_hwresponse = " . $a_inv_supportlevel['slv_id'] . " ";
          $q_inv_support = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_support) > 0) {
            while ($a_inv_support = mysqli_fetch_array($q_inv_support)) {
              $hwtotal++;
            }
          }
          $swtotal = 0;
          $q_string  = "select sup_id ";
          $q_string .= "from inv_support ";
          $q_string .= "where sup_swresponse = " . $a_inv_supportlevel['slv_id'] . " ";
          $q_inv_support = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_support) > 0) {
            while ($a_inv_support = mysqli_fetch_array($q_inv_support)) {
              $swtotal++;
            }
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_inv_supportlevel['slv_value']  . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $a_inv_supportlevel['slv_translate']         . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $hwtotal                                     . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $swtotal                                     . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_supportlevel);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
