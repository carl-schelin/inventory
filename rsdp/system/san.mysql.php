<?php
# Script: san.mysql.php
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
    $package = "san.mysql.php";
    $formVars['update']       = clean($_GET['update'],       10);
    $formVars['rsdp']         = clean($_GET['rsdp'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],           10);
        $formVars['san_wwnnzone'] = clean($_GET['san_wwnnzone'], 20);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['san_wwnnzone']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "san_rsdp     =   " . $formVars['rsdp']         . "," . 
            "san_wwnnzone = \"" . $formVars['san_wwnnzone'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into rsdp_san set san_id = NULL, " . $q_string;
            $message = "SAN added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update rsdp_san set " . $q_string . " where san_id = " . $formVars['id'];
            $message = "SAN updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rsdp']);

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
      $output .= "  <th class=\"ui-state-default\">SAN Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('san-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"san-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>SAN Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a interface to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>SAN Management</strong> title bar to toggle the <strong>SAN Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table width=\"100%\" id=\"san-table\" class=\"full ui-styled-table\">";
      $output .= "<tr>";
      $output .= "<th class=\"ui-state-default\">System Slot/Port</th>";
      $output .= "<th class=\"ui-state-default\">Switch</th>";
      $output .= "<th class=\"ui-state-default\">Port</th>";
      $output .= "<th class=\"ui-state-default\">Media</th>";
      $output .= "<th class=\"ui-state-default\">WWNN Zone</th>";
      $output .= "</tr>";

      $q_string  = "select san_id,san_sysport,san_switch,san_port,med_text,san_wwnnzone ";
      $q_string .= "from rsdp_san ";
      $q_string .= "left join int_media on int_media.med_id = rsdp_san.san_media ";
      $q_string .= "where san_rsdp = " . $formVars['rsdp'];
      $q_rsdp_san = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      if (mysql_num_rows($q_rsdp_san) > 0) {
        while ($a_rsdp_san = mysql_fetch_array($q_rsdp_san)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('san.fill.php?id=" . $a_rsdp_san['san_id'] . "');jQuery('#dialogSAN').dialog('open');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_san['san_sysport']  . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_san['san_switch']   . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_san['san_port']     . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_san['med_text']     . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_rsdp_san['san_wwnnzone'] . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .=   "<td class=\"ui-widget-content\" colspan=\"5\">No HBA Cards Installed</td>";
        $output .= "</tr>";
      }

      mysql_free_result($q_rsdp_san);

      $output .= "</table>";

      print "document.getElementById('san_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.getElementById('san_sysport').innerHTML = '';\n";
      print "document.getElementById('san_switch').innerHTML = '';\n";
      print "document.getElementById('san_port').innerHTML = '';\n";
      print "document.getElementById('san_media').innerHTML = '';\n";
      print "document.san.san_wwnnzone.value = '';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
