<?php
# Script: support.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel(2)) {
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
          logaccess($_SESSION['uid'], $package, "Building the query.");

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
            $query = "insert into support set sup_id = NULL, " . $q_string;
            $message = "Support Contract added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update support set " . $q_string . " where sup_id = " . $formVars['id'];
            $message = "Support Contract updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sup_company']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Support Contract Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('support-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"support-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Support Contract Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a contract to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel(1)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Company</th>\n";
      $output .= "  <th class=\"ui-state-default\">Contract</th>\n";
      $output .= "  <th class=\"ui-state-default\">Phone</th>\n";
      $output .= "  <th class=\"ui-state-default\">E-Mail</th>\n";
      $output .= "  <th class=\"ui-state-default\">Hardware Response</th>\n";
      $output .= "  <th class=\"ui-state-default\">Software Response</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sup_id,sup_company,sup_phone,sup_email,sup_web,sup_contract,sup_wiki,sup_hwresponse,sup_swresponse ";
      $q_string .= "from support";
      $q_support = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_support) > 0) {
        while ($a_support = mysql_fetch_array($q_support)) {

          $q_string  = "select slv_value ";
          $q_string .= "from supportlevel ";
          $q_string .= "where slv_id = " . $a_support['sup_hwresponse'];
          $q_hwsupport = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_hwsupport = mysql_fetch_array($q_hwsupport);

          $q_string  = "select slv_value ";
          $q_string .= "from supportlevel ";
          $q_string .= "where slv_id = " . $a_support['sup_swresponse'];
          $q_swsupport = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_swsupport = mysql_fetch_array($q_swsupport);

          $linkstart = "<a href=\"#\" onclick=\"show_file('support.fill.php?id="  . $a_support['sup_id'] . "');jQuery('#dialogSupport').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('support.del.php?id=" . $a_support['sup_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          if (check_userlevel(1)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_support['sup_id']       . $linkend . "</td>";
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
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysql_free_result($q_support);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.support.sup_company.value = '';\n";
      print "document.support.sup_hwresponse[0].selected = true;\n";
      print "document.support.sup_swresponse[0].selected = true;\n";
      print "document.support.sup_phone.value = '';\n";
      print "document.support.sup_email.value = '';\n";
      print "document.support.sup_contract.value = '';\n";
      print "document.support.sup_web.value = '';\n";
      print "document.support.sup_wiki.value = '';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
