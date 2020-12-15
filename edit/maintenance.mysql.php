<?php
# Script: maintenance.mysql.php
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
    $package = "maintenance.mysql.php";
    $formVars['update']       = clean($_GET['update'],       10);
    $formVars['id']           = clean($_GET['id'],           10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {

      if ($formVars['update'] == -2) {
        $formVars['copyfrom'] = clean($_GET['copyfrom'], 10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select hw_type,hw_vendorid,hw_speed,hw_supportid,hw_size,hw_primary ";
          $q_string .= "from hardware ";
          $q_string .= "where hw_companyid = " . $formVars['copyfrom'];
          $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_hardware = mysqli_fetch_array($q_hardware)) {

            $q_string =
              "hw_companyid =   " . $formVars['hw_companyid']   . "," . 
              "hw_group     =   " . $formVars['hw_group']       . "," . 
              "hw_product   =   " . $formVars['hw_product']     . "," . 
              "hw_vendorid  =   " . $a_hardware['hw_vendorid']  . "," . 
              "hw_type      =   " . $a_hardware['hw_type']      . "," . 
              "hw_size      = \"" . $a_hardware['hw_size']      . "\"," . 
              "hw_speed     = \"" . $a_hardware['hw_speed']     . "\"," . 
              "hw_supportid =   " . $a_hardware['hw_supportid'] . "," .
              "hw_primary   =   " . $a_hardware['hw_primary'];

            $query = "insert into hardware set hw_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"maint_refresh\" value=\"Refresh Maintenance Listing\" onClick=\"javascript:attach_hardware('maintenance.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"maint_update\"  value=\"Update Maintenance\"          onClick=\"javascript:attach_hardware('maintenance.mysql.php',  1);hideDiv('maintenance-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"maint_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"maint_addbtn\"  value=\"Add New Maintenance\"         onClick=\"javascript:attach_hardware('maintenance.mysql.php', 0);\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\" title=\"Does not copy Unique data\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\"   value=\"Copy Maintenance Table From:\" onClick=\"javascript:attach_maintenance('maintenance.mysql.php', -2);\">\n";
        $output .= "<select name=\"maint_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";


        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Maintenance Window Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Start: <select name=\"inv_mstart\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";
        $output .= "<option value=\"1\">Midnight</option>\n";
        $output .= "<option value=\"2\">1 am</option>\n";
        $output .= "<option value=\"3\">2 am</option>\n";
        $output .= "<option value=\"4\">3 am</option>\n";
        $output .= "<option value=\"5\">4 am</option>\n";
        $output .= "<option value=\"6\">5 am</option>\n";
        $output .= "<option value=\"7\">6 am</option>\n";
        $output .= "<option value=\"8\">7 am</option>\n";
        $output .= "<option value=\"9\">8 am</option>\n";
        $output .= "<option value=\"10\">9 am</option>\n";
        $output .= "<option value=\"11\">10 am</option>\n";
        $output .= "<option value=\"12\">11 am</option>\n";
        $output .= "<option value=\"13\">Noon</option>\n";
        $output .= "<option value=\"14\">1 pm</option>\n";
        $output .= "<option value=\"15\">2 pm</option>\n";
        $output .= "<option value=\"16\">3 pm</option>\n";
        $output .= "<option value=\"17\">4 pm</option>\n";
        $output .= "<option value=\"18\">5 pm</option>\n";
        $output .= "<option value=\"19\">6 pm</option>\n";
        $output .= "<option value=\"20\">7 pm</option>\n";
        $output .= "<option value=\"21\">8 pm</option>\n";
        $output .= "<option value=\"22\">9 pm</option>\n";
        $output .= "<option value=\"23\">10 pm</option>\n";
        $output .= "<option value=\"24\">11 pm</option>\n";
        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">End: <select name=\"inv_mend\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";
        $output .= "<option value=\"1\">Midnight</option>\n";
        $output .= "<option value=\"2\">1 am</option>\n";
        $output .= "<option value=\"3\">2 am</option>\n";
        $output .= "<option value=\"4\">3 am</option>\n";
        $output .= "<option value=\"5\">4 am</option>\n";
        $output .= "<option value=\"6\">5 am</option>\n";
        $output .= "<option value=\"7\">6 am</option>\n";
        $output .= "<option value=\"8\">7 am</option>\n";
        $output .= "<option value=\"9\">8 am</option>\n";
        $output .= "<option value=\"10\">9 am</option>\n";
        $output .= "<option value=\"11\">10 am</option>\n";
        $output .= "<option value=\"12\">11 am</option>\n";
        $output .= "<option value=\"13\">Noon</option>\n";
        $output .= "<option value=\"14\">1 pm</option>\n";
        $output .= "<option value=\"15\">2 pm</option>\n";
        $output .= "<option value=\"16\">3 pm</option>\n";
        $output .= "<option value=\"17\">4 pm</option>\n";
        $output .= "<option value=\"18\">5 pm</option>\n";
        $output .= "<option value=\"19\">6 pm</option>\n";
        $output .= "<option value=\"20\">7 pm</option>\n";
        $output .= "<option value=\"21\">8 pm</option>\n";
        $output .= "<option value=\"22\">9 pm</option>\n";
        $output .= "<option value=\"23\">10 pm</option>\n";
        $output .= "<option value=\"24\">11 pm</option>\n";
        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Day of the Week: <select name=\"inv_mdow\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";
        $output .= "<option value=\"1\">Sunday</option>\n";
        $output .= "<option value=\"2\">Monday</option>\n";
        $output .= "<option value=\"3\">Tuesday</option>\n";
        $output .= "<option value=\"4\">Wednesday</option>\n";
        $output .= "<option value=\"5\">Thursday</option>\n";
        $output .= "<option value=\"6\">Friday</option>\n";
        $output .= "<option value=\"7\">Saturday</option>\n";
        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Interval: ";
        $output .= "<label><input type=\"radio\" checked value=\"0\" name=\"inv_minterval\"> Weekly</label> ";
        $output .= "<label><input type=\"radio\"         value=\"1\" name=\"inv_minterval\"> Bi-weekly</label> ";
        $output .= "<label><input type=\"radio\"         value=\"2\" name=\"inv_minterval\"> Monthly</label> ";
        $output .= "<label><input type=\"radio\"         value=\"3\" name=\"inv_minterval\"> Quarterly</label></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"2\">Patching Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Select a Patching Increment: <select name=\"inv_patchid\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select inv_manager ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $formVars['id'] . " ";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_inventory = mysqli_fetch_array($q_inventory);

        $q_string  = "select patch_id,patch_name ";
        $q_string .= "from patching ";
        $q_string .= "where patch_group = " . $a_inventory['inv_manager'] . " ";
        $q_string .= "order by patch_name ";
        $q_patching = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while($a_patching = mysqli_fetch_array($q_patching)) {
          print "<option value=\"" . $a_patching['patch_id'] . "\">" . $a_patching['patch_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">Enter the Date the system was last patched: <input type=\"text\" name=\"inv_patched\" size=\"20\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('maintenance_form').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
