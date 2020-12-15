<?php
# Script: service.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "service.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],               10);
        $formVars['svc_name']         = clean($_GET['svc_name'],         50);
        $formVars['svc_acronym']      = clean($_GET['svc_acronym'],       5);
        $formVars['svc_availability'] = clean($_GET['svc_availability'], 12);
        $formVars['svc_downtime']     = clean($_GET['svc_downtime'],     20);
        $formVars['svc_mtbf']         = clean($_GET['svc_mtbf'],         20);
        $formVars['svc_geographic']   = clean($_GET['svc_geographic'],    1);
        $formVars['svc_mttr']         = clean($_GET['svc_mttr'],         12);
        $formVars['svc_resource']     = clean($_GET['svc_resource'],      1);
        $formVars['svc_restore']      = clean($_GET['svc_restore'],      12);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['svc_geographic'] == 'true') {
          $formVars['svc_geographic'] = 1;
        } else {
          $formVars['svc_geographic'] = 0;
        }

        if ($formVars['svc_resource'] == 'true') {
          $formVars['svc_resource'] = 1;
        } else {
          $formVars['svc_resource'] = 0;
        }

        if (strlen($formVars['svc_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "svc_name         = \"" . $formVars['svc_name']         . "\"," .
            "svc_acronym      = \"" . $formVars['svc_acronym']      . "\"," .
            "svc_availability = \"" . $formVars['svc_availability'] . "\"," .
            "svc_downtime     = \"" . $formVars['svc_downtime']     . "\"," .
            "svc_mtbf         = \"" . $formVars['svc_mtbf']         . "\"," .
            "svc_geographic   = \"" . $formVars['svc_geographic']   . "\"," .
            "svc_mttr         = \"" . $formVars['svc_mttr']         . "\"," .
            "svc_resource     = \"" . $formVars['svc_resource']     . "\"," .
            "svc_restore      = \"" . $formVars['svc_restore']      . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into service set svc_id = NULL, " . $q_string;
            $message = "Service Class added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update service set " . $q_string . " where svc_id = " . $formVars['id'];
            $message = "Service Class updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['svc_name']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Service Class Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('service-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"service-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Service Class Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Service Class to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Acronym</th>\n";
      $output .= "  <th class=\"ui-state-default\">Avail</th>\n";
      $output .= "  <th class=\"ui-state-default\">Downtime</th>\n";
      $output .= "  <th class=\"ui-state-default\">MTBF</th>\n";
      $output .= "  <th class=\"ui-state-default\">Geo</th>\n";
      $output .= "  <th class=\"ui-state-default\">MTTR</th>\n";
      $output .= "  <th class=\"ui-state-default\">Res</th>\n";
      $output .= "  <th class=\"ui-state-default\">Restore</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select svc_id,svc_name,svc_acronym,svc_availability,svc_downtime,";
      $q_string .= "svc_mtbf,svc_geographic,svc_mttr,svc_resource,svc_restore ";
      $q_string .= "from service ";
      $q_string .= "order by svc_id ";
      $q_service = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_service) > 0) {
        while ($a_service = mysqli_fetch_array($q_service)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('service.fill.php?id=" . $a_service['svc_id'] . "');jQuery('#dialogService').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('service.del.php?id="  . $a_service['svc_id'] . "');\">";
          $linkend   = "</a>";

          $geographic = "No";
          if ($a_service['svc_geographic']) {
            $geographic = "Yes";
          }
          $resource = "No";
          if ($a_service['svc_resource']) {
            $resource = "Yes";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_service['svc_id']            . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_service['svc_name']          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_service['svc_acronym']       . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_service['svc_availability']  . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_service['svc_downtime']      . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_service['svc_mtbf']          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $geographic                     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_service['svc_mttr']          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $resource                       . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_service['svc_restore']       . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_service);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.service.svc_name.value = '';\n";
      print "document.service.svc_acronym.value = '';\n";
      print "document.service.svc_availability.value = '';\n";
      print "document.service.svc_downtime.value = '';\n";
      print "document.service.svc_mtbf.value = '';\n";
      print "document.service.svc_geographic.checked = false;\n";
      print "document.service.svc_mttr.value = '';\n";
      print "document.service.svc_restore.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
