<?php
# Script: datacenter.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "datacenter.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);
    $formVars['id']               = clean($_GET['id'],              10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['loc_name']         = clean($_GET['loc_name'],        60);
        $formVars['loc_type']         = clean($_GET['loc_type'],        10);
        $formVars['loc_suite']        = clean($_GET['loc_suite'],       60);
        $formVars['loc_addr1']        = clean($_GET['loc_addr1'],       60);
        $formVars['loc_addr2']        = clean($_GET['loc_addr2'],       60);
        $formVars['loc_city']         = clean($_GET['loc_city'],        60);
        $formVars['loc_zipcode']      = clean($_GET['loc_zipcode'],     60);
        $formVars['loc_contact1']     = clean($_GET['loc_contact1'],   255);
        $formVars['loc_contact2']     = clean($_GET['loc_contact2'],   255);
        $formVars['loc_details']      = clean($_GET['loc_details'],    150);
        $formVars['loc_default']      = clean($_GET['loc_default'],     10);
        $formVars['loc_instance']     = clean($_GET['loc_instance'],     5);
        $formVars['loc_identity']     = clean($_GET['loc_identity'],    10);
        $formVars['loc_tags']         = clean($_GET['loc_tags'],       255);
        $formVars['loc_environment']  = clean($_GET['loc_environment'], 10);

        if ($formVars['loc_default'] == 'true') {
          $formVars['loc_default'] = 1;
        } else {
          $formVars['loc_default'] = 0;
        }
        if ($formVars['loc_instance'] == '') {
          $formVars['loc_instance'] = 0;
        }
    
        if (strlen($formVars['loc_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# the selection on the form is city, state, and country.
# have the city id, use it to get the state and use the state id to get the country
# add them to the location in order to do the reverse selection on the main page
# select country, select state, select city/county, select data center.
          $q_string  = "select ct_state ";
          $q_string .= "from inv_cities ";
          $q_string .= "where ct_id = " . $formVars['loc_city'];
          $q_inv_cities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_cities) > 0) {
            $a_inv_cities = mysqli_fetch_array($q_inv_cities);
          } else {
            $a_inv_cities['ct_state'] = 0;
          }
          $q_string  = "select st_country ";
          $q_string .= "from inv_states ";
          $q_string .= "where st_id = " . $a_inv_cities['ct_state'];
          $q_inv_states = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_states) > 0) {
            $a_inv_states = mysqli_fetch_array($q_inv_states);
          } else {
            $a_inv_states['st_country'] = 0;
          }

          $q_string =
            "loc_name        = \"" . $formVars['loc_name']        . "\"," .
            "loc_type        =   " . $formVars['loc_type']        . "," .
            "loc_suite       = \"" . $formVars['loc_suite']       . "\"," .
            "loc_country     =   " . $a_inv_states['st_country']      . "," .
            "loc_addr1       = \"" . $formVars['loc_addr1']       . "\"," .
            "loc_addr2       = \"" . $formVars['loc_addr2']       . "\"," .
            "loc_city        =   " . $formVars['loc_city']        . "," .
            "loc_state       =   " . $a_inv_cities['ct_state']        . "," .
            "loc_zipcode     = \"" . $formVars['loc_zipcode']     . "\"," .
            "loc_contact1    = \"" . $formVars['loc_contact1']    . "\"," .
            "loc_contact2    = \"" . $formVars['loc_contact2']    . "\"," .
            "loc_details     = \"" . $formVars['loc_details']     . "\"," .
            "loc_default     =   " . $formVars['loc_default']     . "," .
            "loc_instance    =   " . $formVars['loc_instance']    . "," .
            "loc_identity    = \"" . $formVars['loc_identity']    . "\"," .
            "loc_environment =   " . $formVars['loc_environment'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_locations set loc_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_locations set " . $q_string . " where loc_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['loc_name']);

          mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
# need to get the new loc_id in case there is a tag too.
          if ($formVars['update'] == 0) {
            $formVars['id'] = last_insert_id($db);
          }

##################
# Tag Management
##################
#
# Step 1, remove all tags associated with this location. We only need to do this for 
# locations that are updates. New locations will have all new tags.
          if ($formVars['update'] == 0 || $formVars['update'] == 1) {
            $q_string  = "delete ";
            $q_string .= "from inv_tags ";
            $q_string .= "where tag_type = 2 and tag_companyid = " . $formVars['id'] . " ";
            mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# Step 2, okay we've cleared all the tags from the tag system for this server.
# next is to parse the inputted data and create an array. remove any commas and duplicate spaces.
# as a note, the clean() function will remove any leading or trailing spaces so that
# prevents blank tags.
            $formVars['loc_tags'] = str_replace(',', ' ', $formVars['loc_tags']);
            $formVars['loc_tags'] = preg_replace('!\s+!', ' ', $formVars['loc_tags']);

# Step 3, now loop through the tags and add them to the tags table
            if (strlen($formVars['loc_tags']) > 0) {
              $loc_tags = explode(" ", $formVars['loc_tags']);
              for ($i = 0; $i < count($loc_tags); $i++) {

                $q_string = 
                  "tag_companyid    =   " . $formVars['id'] . "," . 
                  "tag_name         = \"" . $loc_tags[$i]   . "\"," . 
                  "tag_type         =   " . "2"             . "," . 
                  "tag_owner        =   " . $_SESSION['uid'] . "," . 
                  "tag_group        =   " . $_SESSION['group'];

                $q_string = "insert into inv_tags set tag_id = NULL, " . $q_string;
                mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
              }
            }
          }

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Data Center</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Location Address</th>\n";
      $output .= "  <th class=\"ui-state-default\">Location Tags</th>\n";
      $output .= "  <th class=\"ui-state-default\">CLLI</th>\n";
      $output .= "  <th class=\"ui-state-default\">Identity</th>\n";
      $output .= "  <th class=\"ui-state-default\">Instance</th>\n";
      $output .= "  <th class=\"ui-state-default\">Env</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select loc_id,loc_name,loc_addr1,loc_addr2,loc_suite,ct_city,loc_type,loc_identity,";
      $q_string .= "st_acronym,loc_zipcode,cn_acronym,loc_details,loc_default,ct_clli,loc_instance,";
      $q_string .= "env_abb ";
      $q_string .= "from inv_locations ";
      $q_string .= "left join inv_cities      on inv_cities.ct_id       = inv_locations.loc_city ";
      $q_string .= "left join inv_states      on inv_states.st_id       = inv_cities.ct_state ";
      $q_string .= "left join inv_country     on inv_country.cn_id      = inv_states.st_country ";
      $q_string .= "left join inv_environment on inv_environment.env_id = inv_locations.loc_environment ";
      $q_string .= "order by loc_default desc,loc_name,ct_city,st_state ";
      $q_inv_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_locations) > 0) {
        while ($a_inv_locations = mysqli_fetch_array($q_inv_locations)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('datacenter.fill.php?id="  . $a_inv_locations['loc_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('datacenter.del.php?id=" . $a_inv_locations['loc_id'] . "');\">";
          $linkend   = "</a>";

          if ($a_inv_locations['loc_default']) {
            $class = "ui-state-highlight";
          } else {
            $class = "ui-widget-content";
          }

          $loc_tags = '';
          $q_string  = "select tag_name ";
          $q_string .= "from inv_tags ";
          $q_string .= "where tag_companyid = " . $a_inv_locations['loc_id'] . " and tag_type = 2 ";
          $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_tags) > 0) {
            while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {
              $loc_tags .= $a_inv_tags['tag_name'] . " ";
            }
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                            . "</td>";
          }
          $output .= "  <td class=\"" . $class . "\">" . $linkstart . $a_inv_locations['loc_name'] . $linkend . ", ";
          if (strlen($a_inv_locations['loc_addr1']) > 0) {
            $output .= $a_inv_locations['loc_addr1'] . ", ";
          }
          if (strlen($a_inv_locations['loc_addr2']) > 0) {
            $output .= $a_inv_locations['loc_addr2'] . ", ";
          }
          if (strlen($a_inv_locations['loc_suite']) > 0) {
            $output .= "Suite: " . $a_inv_locations['loc_suite'] . ", ";
          }
          $output .= $a_inv_locations['ct_city'] . ", " . $a_inv_locations['st_acronym'] . " " . $a_inv_locations['loc_zipcode'] . ", " . $a_inv_locations['cn_acronym'] . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"                       . $loc_tags                                 . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"                       . $a_inv_locations['ct_clli']                   . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"                       . $a_inv_locations['loc_identity']              . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"                       . $a_inv_locations['loc_instance']              . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"                       . $a_inv_locations['env_abb']                   . "</td>";
          $output .= "</tr>";

        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"12\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_locations);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
