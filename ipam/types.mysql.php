<?php
# Script: types.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "types.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                      10);
        $formVars['ip_name']          = clean($_GET['ip_name'],                 30);
        $formVars['ip_description']   = clean($_GET['ip_description'],          50);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['ip_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "ip_name        =  \"" . $formVars['ip_name']        . "\"," .
            "ip_user        =    " . $_SESSION['uid']            . "," .
            "ip_description =  \"" . $formVars['ip_description'] . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into ip_types set ip_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update ip_types set " . $q_string . " where ip_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ip_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">IP Address Type Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('type-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"type-listing-help\" style=\"display:none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p><strong>IP Address Type Listing</strong></p>\n";

      $output .= "<p>This page simply lists all the IP Address Types currently defined.</p>\n";

      $output .= "<p>To add a new IP Address Type, click on the <strong>Add IP Address Type</strong> ";
      $output .= "button. A dialog box will be displayed which will let you enter the new IP Address Type.</p>\n";

      $output .= "<p>To edit an existing IP Address Type, click on one of the entries and a dialog box ";
      $output .= "will be displayed. This will let you edit the existing one, or if you like, you can ";
      $output .= "make a minor change and save it as a new entry.</p>\n";

      $output .= "Note that under the Members column is a number which indicates the number of IP addresses ";
      $output .= "that are currently assigned an IP Address Type. You cannot remove an IP Address Type until this ";
      $output .= "value is zero.";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete IP Address Type</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">IP Address Type</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Created By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ip_id,ip_name,usr_first,usr_last,ip_timestamp,ip_description ";
      $q_string .= "from ip_types ";
      $q_string .= "left join users on users.usr_id = ip_user ";
      $q_string .= "order by ip_name "; 
      $q_ip_types = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_ip_types) > 0) {
        while ($a_ip_types = mysqli_fetch_array($q_ip_types)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('types.fill.php?id="  . $a_ip_types['ip_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('types.del.php?id=" . $a_ip_types['ip_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select ip_id ";
          $q_string .= "from ipaddress ";
          $q_string .= "where ip_type = " . $a_ip_types ['ip_id'] . " ";
          $q_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_ipaddress) > 0) {
            while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {
              $total++;
            }
          }

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\" width=\"160\">" . $linkdel   . "</td>\n";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\" width=\"160\">Members &gt; 0</td>\n";
            }
          }
          $output .= "  <td class=\"ui-widget-content\">" . $linkstart . $a_ip_types['ip_name'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"              . $total . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"              . $a_ip_types['ip_description'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"              . $a_ip_types['usr_first'] . " " . $a_ip_types['usr_last'] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"              . $a_ip_types['ip_timestamp'] . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No records found.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";

      mysqli_free_result($q_ip_types);

      print "document.getElementById('table_mysql').innerHTML = '"   . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
