<?php
# Script: virtualization.mysql.php
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
    $package = "virtualization.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {

      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],      10);
        $formVars['cl_name']         = str_replace(" ", "_", clean($_GET['cl_name'], 60));
        $formVars['cl_owner']        = $_SESSION['uid'];
        $formVars['cl_group']        = $GRP_Virtualization;

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['cl_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "cl_name  = \"" . $formVars['cl_name']  . "\"," .
            "cl_owner =   " . $formVars['cl_owner'] . "," .
            "cl_group =   " . $formVars['cl_group'];

          if ($formVars['update'] == 0) {
            $query = "insert into changelog set cl_id = NULL, " . $q_string;
            $message = "Listing added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update changelog set " . $q_string . " where cl_id = " . $formVars['id'];
            $message = "Listing updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['cl_name']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

# now write the virtualization servers file
      if ($formVars['update'] == 2) {
        $file = $Sitepath . "/servers.vtt";
        $handle = fopen($file, 'w');
        if ($handle) {

          $q_string  = "select inv_id,inv_name,zone_name,hw_serial,inv_notes ";
          $q_string .= "from inventory ";
          $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
          $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
          $q_string .= "where inv_status = 0 and inv_manager = " . $GRP_Virtualization . " and hw_primary = 1 ";
          $q_string .= "order by inv_name";
          $q_inventory = mysql_query($q_string) or die(mysql_error());
          while ($a_inventory = mysql_fetch_array($q_inventory)) {

            $os = '';
            $tags = "";

            $value = explode("/", $a_inventory['inv_name']);
            if (!isset($value[1])) {
              $value[1] = '';
            }

            $tags = '';
            $q_string  = "select tag_name ";
            $q_string .= "from tags ";
            $q_string .= "where tag_inv_id = " . $a_inventory['inv_id'];
            $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
            while ($a_tags = mysql_fetch_array($q_tags)) {
              $tags .= "," . $a_tags['tag_name'] . ",";
            }

# Convert all to lowercase
            $value[0]                 = strtolower($value[0]);
            $value[1]                 = strtolower($value[1]);
            $os                       = strtolower(return_System($a_inventory['inv_id']));
            $a_inventory['zone_name'] = strtolower($a_inventory['zone_name']);
            $a_inventory['inv_notes'] = strtolower($a_inventory['inv_notes']);

            $output = "$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . "::" . $a_inventory['inv_notes'] . ":" . $a_inventory['hw_serial'] . "\n";
            fwrite($handle, $output);

          }

# add the centrify application for changelog work
          $q_string  = "select cl_name ";
          $q_string .= "from changelog ";
          $q_string .= "where cl_group = " . $GRP_Virtualization . " and cl_delete = 0 ";
          $q_string .= "order by cl_name";
          $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          while ($a_changelog = mysql_fetch_array($q_changelog)) {

            $output = $a_changelog['cl_name'] . ":::::::\n";
            fwrite($handle, $output);

          }

          fclose($handle);

          print "alert('\"servers.vtt\" written.');\n";

        } else {
          print "alert('Unable to create \"servers.vtt\".\\nEnsure the web server is able to write to the file.');\n";
        }
      }


      if ($formVars['update'] == -1) {
        $output  = "<p></p>\n";
        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">Server Name</th>\n";
        $output .= "  <th class=\"ui-state-default\">Cluster Name</th>\n";
        $output .= "  <th class=\"ui-state-default\">Operating System</th>\n";
        $output .= "  <th class=\"ui-state-default\">Zone</th>\n";
        $output .= "  <th class=\"ui-state-default\">Tag</th>\n";
        $output .= "  <th class=\"ui-state-default\">Notes</th>\n";
        $output .= "  <th class=\"ui-state-default\">Dell Service Tag</th>\n";
        $output .= "</tr>\n";

        $q_string  = "select inv_id,inv_name,zone_name,hw_serial,inv_notes ";
        $q_string .= "from inventory ";
        $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
        $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $GRP_Virtualization . " and hw_primary = 1 ";
        $q_string .= "order by inv_name";
        $q_inventory = mysql_query($q_string) or die(mysql_error());
        while ($a_inventory = mysql_fetch_array($q_inventory)) {

# set a variable indicating the server is being monitored
# in order to mark an entry in the manual list as already
# in the changelog servers file
          $serverexists[$a_inventory['inv_name']] = 1;

          $os = '';
          $note = '';
          $config = '';
          $tags = "";

          $value = explode("/", $a_inventory['inv_name']);
          if (!isset($value[1])) {
            $value[1] = '';
          }

          $tags = '';
          $q_string  = "select tag_name ";
          $q_string .= "from tags ";
          $q_string .= "where tag_inv_id = " . $a_inventory['inv_id'];
          $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          while ($a_tags = mysql_fetch_array($q_tags)) {
            $tags .= "," . $a_tags['tag_name'] . ", ";
          }

# Convert all to lowercase
          $value[0]                 = strtolower($value[0]);
          $value[1]                 = strtolower($value[1]);
          $os                       = strtolower(return_System($a_inventory['inv_id']));
          $a_inventory['zone_name'] = strtolower($a_inventory['zone_name']);
          $a_inventory['inv_notes'] = strtolower($a_inventory['inv_notes']);

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $value[0]                  . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $value[1]                  . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $os                        . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $a_inventory['zone_name']  . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $tags                      . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $a_inventory['inv_notes']  . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $a_inventory['hw_serial'] . "</td>\n";
          $output .= "</tr>\n";

        }

        $output .= "</table>\n";

        print "document.getElementById('automatic_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      }


# manual listing
      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Manual Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('manual-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"manual-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<p>This is a listing of the Applications located in the servers file.</p>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Manual Listing Management</strong> title bar to toggle the <strong>Manual Listing Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Delete</th>\n";
      $output .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Cluster Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Operating System</th>\n";
      $output .= "  <th class=\"ui-state-default\">Time Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Tag</th>\n";
      $output .= "  <th class=\"ui-state-default\">Notes</th>\n";
      $output .= "  <th class=\"ui-state-default\">Dell Service Tag</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select cl_id,cl_name ";
      $q_string .= "from changelog ";
      $q_string .= "where cl_group = " . $GRP_Virtualization . " and cl_delete = 0 ";
      $q_string .= "order by cl_name";
      $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_changelog = mysql_fetch_array($q_changelog)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('virtualization.fill.php?id="  . $a_changelog['cl_id'] . "');jQuery('#dialogListing').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('virtualization.del.php?id=" . $a_changelog['cl_id'] . "');\">";
        $linkend   = "</a>";

        $class = 'ui-widget-content';
        if (isset($serverexists[$a_changelog['cl_name']])) {
          $class = 'ui-state-error';
        }

        $output .= "<tr>\n";
        $output .= "  <td class=\"" . $class . " delete\">"  . $linkdel                                                    . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . $a_changelog['cl_name']             . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";

      print "document.getElementById('manual_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
