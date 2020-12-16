<?php
# Script: centurylink.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "psaps.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                 10);
        $formVars['psap_customerid']  = clean($_GET['psap_customerid'],    10);
        $formVars['psap_parentid']    = clean($_GET['psap_parentid'],      10);
        $formVars['psap_ali_id']      = clean($_GET['psap_ali_id'],        10);
        $formVars['psap_companyid']   = clean($_GET['psap_companyid'],     10);
        $formVars['psap_psap_id']     = clean($_GET['psap_psap_id'],       20);
        $formVars['psap_description'] = clean($_GET['psap_description'],  255);
        $formVars['psap_lport']       = clean($_GET['psap_lport'],         10);
        $formVars['psap_circuit_id']  = clean($_GET['psap_circuit_id'],   255);
        $formVars['psap_pseudo_cid']  = clean($_GET['psap_pseudo_cid'],   255);
        $formVars['psap_lec']         = clean($_GET['psap_lec'],           10);
        $formVars['psap_texas']       = clean($_GET['psap_texas'],         10);
        $formVars['psap_updated']     = clean($_GET['psap_updated'],       20);
        $formVars['psap_delete']      = clean($_GET['psap_delete'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['psap_description']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "psap_customerid     = \"" . $formVars['psap_customerid']  . "\"," .
            "psap_parentid       = \"" . $formVars['psap_parentid']    . "\"," .
            "psap_ali_id         = \"" . $formVars['psap_ali_id']      . "\"," .
            "psap_companyid      = \"" . $formVars['psap_companyid']   . "\"," .
            "psap_psap_id        = \"" . $formVars['psap_psap_id']     . "\"," . 
            "psap_description    = \"" . $formVars['psap_description'] . "\"," .
            "psap_lport          = \"" . $formVars['psap_lport']       . "\"," .
            "psap_circuit_id     = \"" . $formVars['psap_circuit_id']  . "\"," .
            "psap_pseudo_cid     = \"" . $formVars['psap_pseudo_cid']  . "\"," .
            "psap_lec            = \"" . $formVars['psap_lec']         . "\"," .
            "psap_updated        = \"" . $formVars['psap_updated']     . "\"," .
            "psap_delete         = \"" . $formVars['psap_delete']      . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into psaps set psap_id = NULL, " . $q_string;
            $message = "PSAP added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update psaps set " . $q_string . " where psap_id = " . $formVars['id'];
            $message = "PSAP updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['psap_ali_id']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">CenturyLink PSAP Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('psap-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"psap-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>" . $Sitecompany . "Product Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a " . $Sitecompany . "Product to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>" . $Sitecompany . "Product Management</strong> title bar to toggle the <strong>" . $Sitecompany . "Product Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Parent ID</th>";
      $output .= "  <th class=\"ui-state-default\">ALI ID</th>";
      $output .= "  <th class=\"ui-state-default\">ALI NAME</th>";
      $output .= "  <th class=\"ui-state-default\">PSAP ID</th>";
      $output .= "  <th class=\"ui-state-default\">Description</th>";
      $output .= "  <th class=\"ui-state-default\">LPort</th>";
      $output .= "  <th class=\"ui-state-default\">Circuit ID</th>";
      $output .= "  <th class=\"ui-state-default\">Pseudo Circuit ID</th>";
      $output .= "  <th class=\"ui-state-default\">LEC</th>";
      $output .= "  <th class=\"ui-state-default\">Texas</th>";
      $output .= "  <th class=\"ui-state-default\">Updated</th>";
      $output .= "  <th class=\"ui-state-default\">Delete</th>";
      $output .= "</tr>";

      $q_string  = "select psap_id,psap_parentid,psap_ali_id,inv_name,psap_companyid,psap_psap_id,psap_description,";
      $q_string .= "psap_lport,psap_circuit_id,psap_pseudo_cid,psap_lec,psap_texas,psap_updated,psap_delete ";
      $q_string .= "from psaps ";
      $q_string .= "left join inventory on inventory.inv_id = psaps.psap_companyid ";
      $q_string .= "where psap_customerid = 41 ";
      $q_string .= "order by psap_description ";
      $q_psaps = mysqli_query($db, $q_string) or die (mysqli_error($db));
      while ($a_psaps = mysqli_fetch_array($q_psaps)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('centurylink.fill.php?id="  . $a_psaps['psap_id'] . "');jQuery('#dialogPSAP').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('centurylink.del.php?id=" . $a_psaps['psap_id'] . "');\">";
        $linkend = "</a>";

        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_parentid']     . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_ali_id']       . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['inv_name']          . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_psap_id']      . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_description']  . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_lport']        . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_circuit_id']   . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_pseudo_cid']   . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_lec']          . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_texas']        . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_updated']      . $linkend . "</td>";
        $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_psaps['psap_delete']       . $linkend . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysqli_free_result($q_psaps);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
