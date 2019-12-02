<?php
# Script: i3admins.mysql.php
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
    $package            = "i3admins.mysql.php";
    $myfill             = "i3admins.fill.php";
    $mydel              = "i3admins.del.php";
    $mygroup            = $GRP_i3;
    $myserver           = "servers.i3";
    $formVars['update'] = clean($_GET['update'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {

# only a few zones so load the up into an array
      $q_string  = "select zone_id,zone_name ";
      $q_string .= "from zones ";
      $q_zones = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      while ($a_zones = mysql_fetch_array($q_zones)) {
        $zonename[$a_zones['zone_id']] = $a_zones['zone_name'];
      }

      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],      10);
        $formVars['cl_name']         = str_replace(" ", "_", clean($_GET['cl_name'], 60));
        $formVars['cl_owner']        = $_SESSION['uid'];
        $formVars['cl_group']        = $mygroup;

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

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

# now write the servers file
      if ($formVars['update'] == 2) {
        $file = $Sitepath . "/" . $myserver;
        $handle = fopen($file, 'w');
        if ($handle) {

          $q_string  = "select inv_id,inv_name,inv_fqdn,inv_zone,inv_ssh ";
          $q_string .= "from software ";
          $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
          $q_string .= "where (inv_manager = " . $mygroup . " or inv_appadmin = " . $mygroup . " or sw_group = " . $mygroup . ") and inv_status = 0 ";
          $q_string .= "group by inv_name ";
          $q_software = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          while ($a_software = mysql_fetch_array($q_software)) {

# determine operating system
            $os = "";
            $os = return_System($a_software['inv_id']);

# add a comment character to the server list for live servers but not ssh'able.
# scripts use the "^#" part to make sure commented servers are able to use the changelog process
            $pre = "";
            if ($a_software['inv_ssh'] == 0) {
              $pre = '#';
            }

            $tags = '';
            $q_string  = "select tag_name ";
            $q_string .= "from tags ";
            $q_string .= "where tag_companyid = " . $a_software['inv_id'];
            $q_tags = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            while ($a_tags = mysql_fetch_array($q_tags)) {
              $tags .= "," . $a_tags['tag_name'] . ",";
            }

            $interfaces = '';
            $q_string  = "select int_server ";
            $q_string .= "from interface ";
            $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
            $q_interface = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            while ($a_interface = mysql_fetch_array($q_interface)) {
              $interfaces .= "," . $a_interface['int_server'] . ",";
            }

            $output = $pre . $a_software['inv_name'] . ":" . $a_software['inv_fqdn'] . ":$os:" . $zonename[$a_software['inv_zone']] . ":$tags:$interfaces:" . $a_software['inv_id'] . "\n";
            fwrite($handle, $output);

          }

# add applications for changelog work
          $q_string  = "select cl_name ";
          $q_string .= "from changelog ";
          $q_string .= "where cl_group = " . $mygroup . " and cl_delete = 0 ";
          $q_string .= "group by cl_name ";
          $q_changelog = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          while ($a_changelog = mysql_fetch_array($q_changelog)) {

            $output = '#' . $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0\n";
            fwrite($handle, $output);

          }

          fclose($handle);

          print "alert('\"" . $myserver . "\" written.');\n";

        } else {
          print "alert('Unable to create \"" . $myserver . "\".\\nEnsure the web server is able to write to the file.');\n";
        }
      }


# automatic listing
      if ($formVars['update'] == -1) {
        $output  = "<p></p>\n";
        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">Server Name</th>\n";
        $output .= "  <th class=\"ui-state-default\">Domain</th>\n";
        $output .= "  <th class=\"ui-state-default\">Operating System</th>\n";
        $output .= "  <th class=\"ui-state-default\">Time Zone</th>\n";
        $output .= "  <th class=\"ui-state-default\">Tags</th>\n";
        $output .= "  <th class=\"ui-state-default\">Interfaces</th>\n";
        $output .= "  <th class=\"ui-state-default\">Inventory ID</th>\n";
        $output .= "</tr>\n";

        $q_string  = "select inv_id,inv_name,inv_fqdn,inv_zone,inv_ssh ";
        $q_string .= "from software ";
        $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
        $q_string .= "where (inv_manager = " . $mygroup . " or inv_appadmin = " . $mygroup . " or sw_group = " . $mygroup . ") and inv_status = 0 ";
        $q_string .= "group by inv_name ";
        $q_software = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
        while ($a_software = mysql_fetch_array($q_software)) {

          $serverexists[$a_software['inv_name']] = 1;

# determine operating system
          $os = "";
          $os = return_System($a_software['inv_id']);

# add a comment character to the server list for live servers but not ssh'able.
# scripts use the "^#" part to make sure commented servers are able to use the changelog process
          $pre = "";
          if ($a_software['inv_ssh'] == 0) {
            $pre = '#';
          }

          $tags = '';
          $q_string  = "select tag_name ";
          $q_string .= "from tags ";
          $q_string .= "where tag_companyid = " . $a_software['inv_id'];
          $q_tags = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          while ($a_tags = mysql_fetch_array($q_tags)) {
            $tags .= "," . $a_tags['tag_name'] . ", ";
          }

          $interfaces = '';
          $q_string  = "select int_server ";
          $q_string .= "from interface ";
          $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
          $q_interface = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          while ($a_interface = mysql_fetch_array($q_interface)) {
            $interfaces .= "," . $a_interface['int_server'] . ", ";
          }

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $pre . $a_software['inv_name']     . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $a_software['inv_fqdn']            . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $os                                . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $zonename[$a_software['inv_zone']] . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $tags                              . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $interfaces                        . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">" . $a_software['inv_id']              . "</td>\n";
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
      $output .= "  <th class=\"ui-state-default\">Domain</th>\n";
      $output .= "  <th class=\"ui-state-default\">Operating System</th>\n";
      $output .= "  <th class=\"ui-state-default\">Time Zone</th>\n";
      $output .= "  <th class=\"ui-state-default\">Tags</th>\n";
      $output .= "  <th class=\"ui-state-default\">Interfaces</th>\n";
      $output .= "  <th class=\"ui-state-default\">Inventory ID</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select cl_id,cl_name ";
      $q_string .= "from changelog ";
      $q_string .= "where cl_group = " . $mygroup . " and cl_delete = 0 ";
      $q_string .= "group by cl_name ";
      $q_changelog = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      while ($a_changelog = mysql_fetch_array($q_changelog)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('" . $myfill . "?id="  . $a_changelog['cl_id'] . "');jQuery('#dialogListing').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('" . $mydel . "?id=" . $a_changelog['cl_id'] . "');\">";
        $linkend   = "</a>";

        $class = 'ui-widget-content';
        if (isset($serverexists[$a_changelog['cl_name']])) {
          $class = 'ui-state-error';
        }

        $output .= "<tr>\n";
        $output .= "  <td class=\"" . $class . " delete\">"  . $linkdel                                                    . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">#"        . $linkstart . $a_changelog['cl_name']             . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . ""                                  . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . "," . $a_changelog['cl_name'] . "," . $linkend . "</td>\n";
        $output .= "  <td class=\"" . $class . "\">"         . $linkstart . "0"                                 . $linkend . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";

      print "document.getElementById('manual_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
