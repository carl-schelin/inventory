<?php
# Script: support.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "support.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],                50);
        $formVars['sup_company']    = clean($_GET['sup_company'],       50);
        $formVars['sup_phone']      = clean($_GET['sup_phone'],         20);
        $formVars['sup_email']      = clean($_GET['sup_email'],         50);
        $formVars['sup_web']        = clean($_GET['sup_web'],          255);
        $formVars['sup_contract']   = clean($_GET['sup_contract'],      20);
        $formVars['sup_wiki']       = clean($_GET['sup_wiki'],         255);
        $formVars['sup_hwresponse'] = clean($_GET['sup_hwresponse'],    10);
        $formVars['sup_swresponse'] = clean($_GET['sup_swresponse'],    10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['sup_company']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "sup_company    = \"" . $formVars['sup_company']    . "\"," .
            "sup_phone      = \"" . $formVars['sup_phone']      . "\"," .
            "sup_email      = \"" . $formVars['sup_email']      . "\"," .
            "sup_web        = \"" . $formVars['sup_web']        . "\"," .
            "sup_contract   = \"" . $formVars['sup_contract']   . "\"," .
            "sup_wiki       = \"" . $formVars['sup_wiki']       . "\"," .
            "sup_hwresponse =   " . $formVars['sup_hwresponse'] . "," .
            "sup_swresponse =   " . $formVars['sup_swresponse'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into support set sup_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update support set " . $q_string . " where sup_id = " . $formVars['id'];
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
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Support Contract</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Company</th>\n";
      $output .= "  <th class=\"ui-state-default\">Contract</th>\n";
      $output .= "  <th class=\"ui-state-default\">Phone</th>\n";
      $output .= "  <th class=\"ui-state-default\">E-Mail</th>\n";
      $output .= "  <th class=\"ui-state-default\">Hardware Response</th>\n";
      $output .= "  <th class=\"ui-state-default\">Software Response</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sup_id,sup_company,sup_phone,sup_email,sup_web,sup_contract,sup_wiki,sup_hwresponse,sup_swresponse ";
      $q_string .= "from support ";
      $q_string .= "order by sup_company ";
      $q_support = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_support) > 0) {
        while ($a_support = mysqli_fetch_array($q_support)) {

          $q_string  = "select slv_value ";
          $q_string .= "from inv_supportlevel ";
          $q_string .= "where slv_id = " . $a_support['sup_hwresponse'];
          $q_hwsupport = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_hwsupport = mysqli_fetch_array($q_hwsupport);

          $q_string  = "select slv_value ";
          $q_string .= "from inv_supportlevel ";
          $q_string .= "where slv_id = " . $a_support['sup_swresponse'];
          $q_swsupport = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_swsupport = mysqli_fetch_array($q_swsupport);

          $linkstart = "<a href=\"#\" onclick=\"show_file('support.fill.php?id="  . $a_support['sup_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('support.del.php?id=" . $a_support['sup_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_support['sup_company']  . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_support['sup_contract'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_support['sup_phone']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_support['sup_email']    . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_hwsupport['slv_value']  . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_swsupport['slv_value']  . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_support);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
