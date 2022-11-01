<?php
# Script: tags.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "tags.mysql.php";
    $formVars['update']       = clean($_GET['update'],      10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['tag_name']       = clean($_GET['tag_name'],       40);
        $formVars['tag_owner']      = clean($_GET['tag_owner'],      10);
        $formVars['tag_group']      = clean($_GET['tag_group'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['tag_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "tag_name    = \"" . $formVars['tag_name']     . "\"," .
            "tag_owner   =   " . $formVars['tag_owner']    . "," .
            "tag_group   =   " . $formVars['tag_group'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into tags set tag_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update tags set " . $q_string . " where tag_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['tag_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

# now build the where clause
      $and = " where";
      $project = '';
      if ($_SESSION['p_product'] == 0) {
        $product = '';
      } else {
        if ($_SESSION['p_product'] == -1) {
          $product = $and . " inv_product = 0 ";
          $and = " and";
        } else {
          $product = $and . " inv_product = " . $_SESSION['p_product'] . " ";
          $and = " and";
          if ($_SESSION['p_project'] == 0) {
            $project = '';
          } else {
            $project = $and . " inv_project = " . $_SESSION['p_project'] . " ";
            $and = " and";
          }
        }
      }

      $group = '';
      if ($_SESSION['p_group'] > 0) {
        $group = $and . " inv_manager = " . $_SESSION['p_group'] . " ";
        $and = " and";
      }

      if ($_SESSION['p_inwork'] == 'false') {
        $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
        $and = " and";
      } else {
        $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
        $and = " and";
      }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

      $location = '';
      if ($_SESSION['p_country'] == 0 && $_SESSION['p_location'] > 0) {
        $location = $and . " inv_location = " . $_SESSION['p_location'] . " ";
        $and = " and";
      } else {
        if ($_SESSION['p_country'] > 0) {
          $location .= $and . " loc_country = " . $_SESSION['p_country'] . " ";
          $and = " and";
        }
        if ($_SESSION['p_state'] > 0) {
          $location .= $and . " loc_state = " . $_SESSION['p_state'] . " ";
          $and = " and";
        }
        if ($_SESSION['p_city'] > 0) {
          $location .= $and . " loc_city = " . $_SESSION['p_city'] . " ";
          $and = " and";
        }
        if ($_SESSION['p_location'] > 0) {
          $location .= $and . " inv_location = " . $_SESSION['p_location'] . " ";
          $and = " and";
        }
      }

      $where = $product . $project . $group . $inwork . $location;

# show tags here
      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Tag</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Server</th>\n";
      $output .= "  <th class=\"ui-state-default\">Tag Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Owner</th>\n";
      $output .= "  <th class=\"ui-state-default\">Group</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select tag_id,tag_name,inv_name,usr_first,usr_last,grp_name ";
      $q_string .= "from tags ";
      $q_string .= "left join inventory    on inventory.inv_id      = tags.tag_companyid ";
      $q_string .= "left join inv_users        on inv_users.usr_id          = tags.tag_owner ";
      $q_string .= "left join inv_groups       on inv_groups.grp_id         = tags.tag_group ";
      $q_string .= "left join hardware     on hardware.hw_companyid = inventory.inv_id ";
      $q_string .= "left join models       on models.mod_id         = hardware.hw_vendorid ";
      $q_string .= "left join locations    on locations.loc_id      = inventory.inv_location ";
      $q_string .= "left join cities       on cities.ct_id          = locations.loc_city ";
      $q_string .= "left join states       on states.st_id          = locations.loc_state ";
      $q_string .= $where . "and tag_type = 1 ";
      $q_string .= "order by tag_name,inv_name,grp_name,usr_last,usr_first ";
      $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_tags) > 0) {
        while ($a_tags = mysqli_fetch_array($q_tags)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('tags.fill.php?id="  . $a_tags['tag_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('tags.del.php?id=" . $a_tags['tag_id'] . "');\">";
          $linkend   = "</a>";

          $inv_name = "All Servers";
          if ($a_tags['inv_name'] != '') {
            $inv_name = $a_tags['inv_name'];
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $inv_name                                        . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['tag_name']                              . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['usr_first'] . " " . $a_tags['usr_last'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['grp_name']                              . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_tags);

      print "document.getElementById('public_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
