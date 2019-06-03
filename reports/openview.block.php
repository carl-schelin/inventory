<?php
# Script: openview.block.php
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
    $package = "openview.block.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0) {
        $formVars['id']          = clean($_GET['id'],          10);
        $formVars['block_text']  = clean($_GET['block_text'], 255);
        $formVars['block_user']  = clean($_SESSION['uid'],     10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['block_text']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string  = "insert ";
          $q_string .= "into alarm_blocks ";
          $q_string .= "set block_id = NULL,block_text = \"" . $formVars['block_text'] . "\",block_user=" . $formVars['block_user'];

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['block_text']);

          mysql_query($q_string) or die($q_string . ": " . mysql_error());

          print "alert('Block added.');\n";

          $q_string  = "update ";
          $q_string .= "alarms ";
          $q_string .= "set alarm_disabled = 1 ";
          $q_string .= "where alarm_text like \"%" . $formVars['block_text'] . "%\"";
          mysql_query($q_string) or die($q_string . ": " . mysql_error());

          print "alert('Block replicated.');\n";

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Blocked Alarms</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('block-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"block-help\" style=\"" . $display . "\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li>When adding a block, the text is a 'like' updated. You can add '%' into your text which is used as a wildcard search.</li>\n";
      $output .= "  <li>Click the 'Remove' button to delete an existing block. This clears the block flag from all alarms.</li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">ID</th>";
      $output .= "  <th class=\"ui-state-default\">Blocked Text</th>";
      $output .= "  <th class=\"ui-state-default\">Entered By</th>";
      $output .= "</tr>";

      $q_string  = "select block_id,block_text,usr_first,usr_last ";
      $q_string .= "from alarm_blocks ";
      $q_string .= "left join users on users.usr_id = alarm_blocks.block_user ";
      $q_alarm_blocks = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_alarm_blocks = mysql_fetch_array($q_alarm_blocks)) {

        $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_block('openview.del.php?id=" . $a_alarm_blocks['block_id'] . "');\">";

        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content delete\">" . $linkdel                      . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">"        . $a_alarm_blocks['block_id']   . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">"        . $a_alarm_blocks['block_text'] . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">"        . $a_alarm_blocks['usr_first'] . " " . $a_alarm_blocks['usr_last']   . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";

      print "document.getElementById('block_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }

?>
