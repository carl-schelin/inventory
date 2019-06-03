<?php
# Script: psaps.mysql.php
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
    $package = "psaps.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);
    $formVars['pagination'] = clean($_GET['pagination'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['pagination'] == '') {
      $formVars['pagination'] = 0;
    }

    if (isset($_SESSION['sort'])) {
      $orderby = "order by " . clean($_SESSION['sort'], 20) . " ";
    } else {
      $orderby = "order by psap_description ";
    }

    $pages = $formVars['pagination'];
    $formVars['pagination'] *= 20;

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                 10);
        $formVars['psap_ali_id']      = clean($_GET['psap_ali_id'],        10);
        $formVars['psap_companyid']   = clean($_GET['psap_companyid'],     10);
        $formVars['psap_psap_id']     = clean($_GET['psap_psap_id'],       20);
        $formVars['psap_description'] = clean($_GET['psap_description'],  255);
        $formVars['psap_lport']       = clean($_GET['psap_lport'],         10);
        $formVars['psap_circuit_id']  = clean($_GET['psap_circuit_id'],   255);
        $formVars['psap_texas']       = clean($_GET['psap_texas'],         10);
        $formVars['psap_updated']     = clean($_GET['psap_updated'],       20);
        $formVars['psap_delete']      = clean($_GET['psap_delete'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['psap_texas'] == 'true') {
          $formVars['psap_texas'] = 1;
        } else {
          $formVars['psap_texas'] = 0;
        }

        if ($formVars['psap_delete'] == 'true') {
          $formVars['psap_delete'] = 1;
        } else {
          $formVars['psap_delete'] = 0;
        }

        if (strlen($formVars['psap_description']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "psap_customerid     =   " . 1319                          . "," .
            "psap_parentid       =   " . 0                             . "," .
            "psap_ali_id         = \"" . $formVars['psap_ali_id']      . "\"," .
            "psap_companyid      =   " . $formVars['psap_companyid']   . "," .
            "psap_psap_id        =   " . $formVars['psap_psap_id']     . "," . 
            "psap_description    = \"" . $formVars['psap_description'] . "\"," .
            "psap_lport          =   " . $formVars['psap_lport']       . "," .
            "psap_circuit_id     = \"" . $formVars['psap_circuit_id']  . "\"," .
            "psap_texas          =   " . $formVars['psap_texas']       . "," .
            "psap_updated        = \"" . $formVars['psap_updated']     . "\"," .
            "psap_delete         =   " . $formVars['psap_delete'];

          if ($formVars['update'] == 0) {
            $query = "insert into psaps set psap_id = NULL, " . $q_string;
            $message = "PSAP added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update psaps set " . $q_string . " where psap_id = " . $formVars['id'];
            $message = "PSAP updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['psap_ali_id']);

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
      $output .= "  <th class=\"ui-state-default\">PSAP Listing</th>\n";
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
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_ali_id\">ALI ID</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=inv_name\">ALI Name</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_psap_id\">PSAP ID</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_description\">PSAP Name</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_lport\">LPort</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_circuit_id\">Circuit ID</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_texas\">Texas CSEC</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_updated\">Last Updated</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"psaps.php?sort=psap_delete\">Delete</a></th>";
      $output .= "</tr>";

      $q_string  = "select psap_id,psap_ali_id,inv_name,psap_psap_id,psap_description,";
      $q_string .= "psap_lport,psap_circuit_id,psap_texas,psap_updated,psap_delete ";
      $q_string .= "from psaps ";
      $q_string .= "left join inventory on inventory.inv_id = psaps.psap_companyid ";
      $q_string .= "where psap_customerid = 1319 ";
      $q_string .= $orderby;
      $q_string .= "limit " . $formVars['pagination'] . ", 20 ";
      $q_psaps = mysql_query($q_string) or die (mysql_error());
      while ($a_psaps = mysql_fetch_array($q_psaps)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('psaps.fill.php?id="  . $a_psaps['psap_id'] . "');jQuery('#dialogPSAP').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Delete\" onclick=\"delete_line('psaps.del.php?id=" . $a_psaps['psap_id'] . "');\">";
        $linkend = "</a>";

        if ($a_psaps['psap_texas']) {
          $texas = "Yes";
        } else {
          $texas = "No";
        }

        if ($a_psaps['psap_delete']) {
          $delete = "Yes";
          $class = "ui-state-highlight";
        } else {
          $delete = "No";
          $class = "ui-widget-content";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                              . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_psaps['psap_ali_id']       . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_psaps['inv_name']          . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_psaps['psap_psap_id']      . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_psaps['psap_description']  . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_psaps['psap_lport']        . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_psaps['psap_circuit_id']   . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $texas                        . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_psaps['psap_updated']      . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $delete                       . $linkend . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysql_free_result($q_psaps);

# need how many rows there are;
      $q_string  = "select psap_id ";
      $q_string .= "from psaps ";
      $q_string .= "where psap_customerid = 1319 ";
      $q_psaps = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $numpsaps = mysql_num_rows($q_psaps);

      $output .= return_Pagination( "psaps.mysql.php", $pages, $numpsaps, 20 );

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
