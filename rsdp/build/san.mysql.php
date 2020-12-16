<?php
# Script: san.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "san.mysql.php";
    $formVars['update']    = clean($_GET['update'],    10);
    $formVars['san_rsdp']  = clean($_GET['san_rsdp'],  10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['san_id'],      10);
        $formVars['san_sysport']  = clean($_GET['san_sysport'], 60);
        $formVars['san_switch']   = clean($_GET['san_switch'],  30);
        $formVars['san_port']     = clean($_GET['san_port'],    20);
        $formVars['san_media']    = clean($_GET['san_media'],   10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['san_sysport']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "san_rsdp     =   " . $formVars['san_rsdp']    . "," . 
            "san_sysport  = \"" . $formVars['san_sysport'] . "\"," .
            "san_switch   = \"" . $formVars['san_switch']  . "\"," .
            "san_port     = \"" . $formVars['san_port']    . "\"," .
            "san_media    =   " . $formVars['san_media'];

          if ($formVars['update'] == 0) {
            $query = "insert into rsdp_san set san_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update rsdp_san set " . $q_string . " where san_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['san_sysport']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      if ($formVars['update'] == -2) {
        $formVars['copyfrom']  = clean($_GET['copyfrom'],  10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select san_sysport ";
          $q_string .= "from rsdp_san ";
          $q_string .= "where san_rsdp = " . $formVars['copyfrom'];
          $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_rsdp_san = mysqli_fetch_array($q_rsdp_san)) {

            $q_string =
              "san_rsdp     =   " . $formVars['san_rsdp']      . "," .
              "san_sysport  = \"" . $a_rsdp_san['san_sysport'] . "\"";

            $query = "insert into rsdp_san set san_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          }
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">SAN Listing</th>";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('san-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"san-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>SAN Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Clicking the <strong>x</strong> will delete this SAN interface from this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a SAN interface to edit it.</li>\n";
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

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "<th class=\"ui-state-default\">Del</th>";
      $output .= "<th class=\"ui-state-default\">HBA Slot/Port in System</th>";
      $output .= "<th class=\"ui-state-default\">Switch</th>";
      $output .= "<th class=\"ui-state-default\">Switch Port</th>";
      $output .= "<th class=\"ui-state-default\">Media</th>";
      $output .= "</tr>";

      $q_string  = "select san_id,san_sysport,san_switch,san_port,med_text ";
      $q_string .= "from rsdp_san ";
      $q_string .= "left join int_media on int_media.med_id = rsdp_san.san_media ";
      $q_string .= "where san_rsdp = " . $formVars['san_rsdp'];
      $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      if (mysqli_num_rows($q_rsdp_san) > 0) {
        while ($a_rsdp_san = mysqli_fetch_array($q_rsdp_san)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('san.fill.php?id=" . $a_rsdp_san['san_id'] . "');jQuery('#dialogSAN').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_san('san.del.php?id=" . $a_rsdp_san['san_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          $output .=   "<td class=\"ui-widget-content delete\">" . $linkdel                                           . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_rsdp_san['san_sysport'] . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_rsdp_san['san_switch']  . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_rsdp_san['san_port']    . $linkend . "</td>";
          $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_rsdp_san['med_text']    . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
          $output .= "<tr>";
          $output .=   "<td colspan=\"5\" class=\"ui-widget-content\">There are no HBA slots defined.</td>";
          $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_rsdp_san);

      print "document.getElementById('san_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.san.san_sysport.value = '';\n";
      print "document.san.san_switch.value = '';\n";
      print "document.san.san_port.value = '';\n";
      print "document.san.san_media[0].selected = true;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
