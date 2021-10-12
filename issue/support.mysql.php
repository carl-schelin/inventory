<?php
# Script: support.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "support.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);
    $formVars['id']            = clean($_GET['id'],  10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars["sup_id"]        = clean($_GET["sup_id"],        10);
        $formVars["sup_company"]   = clean($_GET["sup_company"],   60);
        $formVars["sup_case"]      = clean($_GET["sup_case"],      30);
        $formVars["sup_contact"]   = clean($_GET["sup_contact"],   60);
        $formVars["sup_email"]     = clean($_GET["sup_email"],    120);
        $formVars["sup_phone"]     = clean($_GET["sup_phone"],     20);
        $formVars["sup_govid"]     = clean($_GET["sup_govid"],     30);
        $formVars["sup_timestamp"] = clean($_GET["sup_timestamp"], 20);
        $formVars["sup_rating"]    = clean($_GET["sup_rating"],    10);

        if ($formVars['sup_rating'] == '') {
          $formVars['sup_rating'] = 0;
        }
# if blank, we want to set a new one
        if ($formVars['sup_timestamp'] == '1971-01-01 00:00:00' || $formVars['sup_timestamp'] == 'Current Time' || $formVars['sup_timestamp'] == '') {
          $timestamp = "sup_timestamp = \"" . date('Y-m-d H:i:s') . "\",";
        } else {
          $timestamp = "sup_timestamp = \"" . $formVars['sup_timestamp'] . "\",";
        }

        if (strlen($formVars['sup_case']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "sup_issue     =   " . $formVars['id']            . "," .
            "sup_company   = \"" . $formVars['sup_company']   . "\"," .
            "sup_case      = \"" . $formVars['sup_case']      . "\"," .
            "sup_contact   = \"" . $formVars['sup_contact']   . "\"," .
            "sup_email     = \"" . $formVars['sup_email']     . "\"," .
            "sup_phone     = \"" . $formVars['sup_phone']     . "\"," .
            "sup_govid     = \"" . $formVars['sup_govid']     . "\"," .
            $timestamp . 
            "sup_rating    =   " . $formVars['sup_rating'];

          if ($formVars['update'] == 0) {
            $query = "insert into issue_support set sup_id = NULL, " . $q_string;
            $message = "Support ticket added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update issue_support set " . $q_string . " where sup_id = " . $formVars['sup_id'];
            $message = "Support ticket updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sup_case']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select iss_closed ";
      $q_string .= "from issue ";
      $q_string .= "where iss_id = " . $formVars['id'];
      $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_issue = mysqli_fetch_array($q_issue);

      $output  = "<p></p>";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Support Listing</th>";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('support-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"support-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Support Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Click to delete this support ticket.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a ticket to bring up the form and edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Support Management</strong> title bar to toggle the <strong>Support Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Company</th>";
      $output .= "  <th class=\"ui-state-default\">Case</th>";
      $output .= "  <th class=\"ui-state-default\">Contact</th>";
      $output .= "  <th class=\"ui-state-default\">E-Mail</th>";
      $output .= "  <th class=\"ui-state-default\">Phone</th>";
      $output .= "  <th class=\"ui-state-default\">Gov ID</th>";
      $output .= "  <th class=\"ui-state-default\">Opened</th>";
      $output .= "</tr>";

      $q_string  = "select sup_id,sup_company,sup_case,sup_contact,sup_email,sup_phone,sup_govid,sup_timestamp ";
      $q_string .= "from issue_support ";
      $q_string .= "where sup_issue = " . $formVars['id'] . " ";
      $q_string .= "order by sup_timestamp";
      $q_issue_support = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_issue_support) > 0) {
        while ($a_issue_support = mysqli_fetch_array($q_issue_support)) {

          if ($a_issue['iss_closed'] == '1971-01-01') {
            $linkstart = "<a href=\"#\" onclick=\"show_file('"     . $Issueroot . "/support.fill.php?id=" . $a_issue_support['sup_id'] . "');showDiv('support-hide');\">";
            $linkdel   = "<a href=\"#\" onclick=\"delete_ticket('" . $Issueroot . "/support.del.php?id="  . $a_issue_support['sup_id'] . "');\">";
            $linkend   = "</a>";
            $linktext  = "x";
          } else {
            $linkstart = '';
            $linkdel = '';
            $linkend = '';
            $linktext  = "--";
          }

          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . $linktext                         . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_support['sup_company']   . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_support['sup_case']      . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_support['sup_contact']   . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_support['sup_email']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_support['sup_phone']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_support['sup_govid']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_issue_support['sup_timestamp'] . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No records found.</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_issue_support);

      $output .= "</table>";

      print "document.getElementById('support_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

      if ($a_issue['iss_closed'] == '1971-01-01') {
        print "document.start.sup_company.value = '';";
        print "document.start.sup_case.value = '';";
        print "document.start.sup_rating[0].checked = true;";
        print "document.start.sup_contact.value = '';";
        print "document.start.sup_email.value = '';";
        print "document.start.sup_phone.value = '';";
        print "document.start.sup_govid.value = '';";
        print "document.start.supupdate.disabled = true;";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
