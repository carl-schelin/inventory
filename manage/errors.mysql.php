<?php
# Script: errors.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "errors.mysql.php";

    if (check_userlevel($db, $AL_Edit)) {
      $formVars['update']       = clean($_GET['update'],    10);

      if ($formVars['update'] == 1) {
        $formVars['id']         = clean($_GET['id'],        10);
        $formVars['priority']   = clean($_GET['priority'],  10);

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "ce_priority =   " . $formVars['priority'];

        if ($formVars['update'] == 1) {
          $query = "update chkerrors set " . $q_string . " where ce_id = " . $formVars['id'];
        }

        logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $a_inventory['inv_name']);

        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

# priority 1
      $count = 0;
      $output  = "<form name=\"priority1\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Priority</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ce_id,ce_error ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_priority = 1 and ce_delete = 0 ";
      $q_string .= "order by ce_error ";
      $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkerrors = mysqli_fetch_array($q_chkerrors)) {

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\">" . $a_chkerrors['ce_error'] . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">";
        $output .= "<input type=\"radio\" checked=\"checked\" value=\"1\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=1');\"> 1 ";
        $output .= "<input type=\"radio\"                     value=\"2\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=2');\"> 2 ";
        $output .= "<input type=\"radio\"                     value=\"3\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=3');\"> 3 ";
        $output .= "<input type=\"radio\"                     value=\"4\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=4');\"> 4 ";
        $output .= "<input type=\"radio\"                     value=\"5\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=5');\"> 5";
        $output .= "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      print "document.getElementById('priority1').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
      print "document.getElementById('pri1_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";


# priority 2
      $count = 0;
      $output  = "<form name=\"priority2\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Priority</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ce_id,ce_error ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_priority = 2 and ce_delete = 0 ";
      $q_string .= "order by ce_error ";
      $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkerrors = mysqli_fetch_array($q_chkerrors)) {

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\">" . $a_chkerrors['ce_error'] . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">";
        $output .= "<input type=\"radio\"                     value=\"1\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=1');\"> 1 ";
        $output .= "<input type=\"radio\" checked=\"checked\" value=\"2\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=2');\"> 2 ";
        $output .= "<input type=\"radio\"                     value=\"3\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=3');\"> 3 ";
        $output .= "<input type=\"radio\"                     value=\"4\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=4');\"> 4 ";
        $output .= "<input type=\"radio\"                     value=\"5\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=5');\"> 5";
        $output .= "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      print "document.getElementById('priority2').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
      print "document.getElementById('pri2_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";


# priority 3
      $count = 0;
      $output  = "<form name=\"priority3\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Priority</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ce_id,ce_error ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_priority = 3 and ce_delete = 0 ";
      $q_string .= "order by ce_error ";
      $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkerrors = mysqli_fetch_array($q_chkerrors)) {

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\">" . $a_chkerrors['ce_error'] . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">";
        $output .= "<input type=\"radio\"                     value=\"1\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=1');\"> 1 ";
        $output .= "<input type=\"radio\"                     value=\"2\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=2');\"> 2 ";
        $output .= "<input type=\"radio\" checked=\"checked\" value=\"3\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=3');\"> 3 ";
        $output .= "<input type=\"radio\"                     value=\"4\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=4');\"> 4 ";
        $output .= "<input type=\"radio\"                     value=\"5\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=5');\"> 5";
        $output .= "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      print "document.getElementById('priority3').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
      print "document.getElementById('pri3_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";


# priority 4
      $count = 0;
      $output  = "<form name=\"priority4\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Priority</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ce_id,ce_error ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_priority = 4 and ce_delete = 0 ";
      $q_string .= "order by ce_error ";
      $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkerrors = mysqli_fetch_array($q_chkerrors)) {

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\">" . $a_chkerrors['ce_error'] . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">";
        $output .= "<input type=\"radio\"                     value=\"1\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=1');\"> 1 ";
        $output .= "<input type=\"radio\"                     value=\"2\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=2');\"> 2 ";
        $output .= "<input type=\"radio\"                     value=\"3\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=3');\"> 3 ";
        $output .= "<input type=\"radio\" checked=\"checked\" value=\"4\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=4');\"> 4 ";
        $output .= "<input type=\"radio\"                     value=\"5\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=5');\"> 5";
        $output .= "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      print "document.getElementById('priority4').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
      print "document.getElementById('pri4_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";


# priority 5
      $count = 0;
      $output  = "<form name=\"priority5\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Priority</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ce_id,ce_error ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_priority = 5 and ce_delete = 0 ";
      $q_string .= "order by ce_error ";
      $q_chkerrors = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkerrors = mysqli_fetch_array($q_chkerrors)) {

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\">" . $a_chkerrors['ce_error'] . "</td>\n";
        $output .= "<td class=\"ui-widget-content\">";
        $output .= "<input type=\"radio\"                     value=\"1\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=1');\"> 1 ";
        $output .= "<input type=\"radio\"                     value=\"2\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=2');\"> 2 ";
        $output .= "<input type=\"radio\"                     value=\"3\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=3');\"> 3 ";
        $output .= "<input type=\"radio\"                     value=\"4\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=4');\"> 4 ";
        $output .= "<input type=\"radio\" checked=\"checked\" value=\"5\" onclick=\"show_file('errors.mysql.php?update=1&id="  . $a_chkerrors['ce_id'] . "&priority=5');\"> 5";
        $output .= "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      print "document.getElementById('priority5').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
      print "document.getElementById('pri5_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";


    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
