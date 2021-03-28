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

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],              10);
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
        $formVars['loc_xpoint']       = clean($_GET['loc_xpoint'],      10);
        $formVars['loc_ypoint']       = clean($_GET['loc_ypoint'],      10);
        $formVars['loc_xlen']         = clean($_GET['loc_xlen'],        10);
        $formVars['loc_ylen']         = clean($_GET['loc_ylen'],        10);
        $formVars['loc_identity']     = clean($_GET['loc_identity'],    10);
        $formVars['loc_environment']  = clean($_GET['loc_environment'], 10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['loc_default'] == 'true') {
          $formVars['loc_default'] = 1;
        } else {
          $formVars['loc_default'] = 0;
        }
        if ($formVars['loc_instance'] == '') {
          $formVars['loc_instance'] = 0;
        }
        if ($formVars['loc_xpoint'] == '') {
          $formVars['loc_xpoint'] = 0;
        }
        if ($formVars['loc_ypoint'] == '') {
          $formVars['loc_ypoint'] = 0;
        }
        if ($formVars['loc_xlen'] == '') {
          $formVars['loc_xlen'] = 0;
        }
        if ($formVars['loc_ylen'] == '') {
          $formVars['loc_ylen'] = 0;
        }
    
        if (strlen($formVars['loc_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# the selection on the form is city, state, and country.
# have the city id, use it to get the state and use the state id to get the country
# add them to the location in order to do the reverse selection on the main page
# select country, select state, select city/county, select data center.
          $q_string  = "select ct_state ";
          $q_string .= "from cities ";
          $q_string .= "where ct_id = " . $formVars['loc_city'];
          $q_cities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_cities) > 0) {
            $a_cities = mysqli_fetch_array($q_cities);
          } else {
            $a_cities['ct_state'] = 0;
          }
          $q_string  = "select st_country ";
          $q_string .= "from states ";
          $q_string .= "where st_id = " . $a_cities['ct_state'];
          $q_states = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_states) > 0) {
            $a_states = mysqli_fetch_array($q_states);
          } else {
            $a_states['st_country'] = 0;
          }

          $q_string =
            "loc_name        = \"" . $formVars['loc_name']        . "\"," .
            "loc_type        =   " . $formVars['loc_type']        . "," .
            "loc_suite       = \"" . $formVars['loc_suite']       . "\"," .
            "loc_country     =   " . $a_states['st_country']      . "," .
            "loc_addr1       = \"" . $formVars['loc_addr1']       . "\"," .
            "loc_addr2       = \"" . $formVars['loc_addr2']       . "\"," .
            "loc_city        =   " . $formVars['loc_city']        . "," .
            "loc_state       =   " . $a_cities['ct_state']        . "," .
            "loc_zipcode     = \"" . $formVars['loc_zipcode']     . "\"," .
            "loc_contact1    = \"" . $formVars['loc_contact1']    . "\"," .
            "loc_contact2    = \"" . $formVars['loc_contact2']    . "\"," .
            "loc_details     = \"" . $formVars['loc_details']     . "\"," .
            "loc_default     =   " . $formVars['loc_default']     . "," .
            "loc_instance    =   " . $formVars['loc_instance']    . "," .
            "loc_identity    = \"" . $formVars['loc_identity']    . "\"," .
            "loc_environment =   " . $formVars['loc_environment'] . "," .
            "loc_xpoint      =   " . $formVars['loc_xpoint']      . "," .
            "loc_ypoint      =   " . $formVars['loc_ypoint']      . "," .
            "loc_xlen        =   " . $formVars['loc_xlen']        . "," .
            "loc_ylen        =   " . $formVars['loc_ylen'];

          if ($formVars['update'] == 0) {
            $query = "insert into locations set loc_id = NULL, " . $q_string;
            $message = "Location added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update locations set " . $q_string . " where loc_id = " . $formVars['id'];
            $message = "Location modified.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['loc_name']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $datacenter = '';

      $datacenterheader  = "<p></p>\n";
      $datacenterheader .= "<table class=\"ui-styled-table\">\n";
      $datacenterheader .= "<tr>\n";
      $datacenterheader .= "  <th class=\"ui-state-default\">Data Center Listing</th>\n";
      $datacenterheader .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('datacenter-listing-help');\">Help</a></th>\n";
      $datacenterheader .= "</tr>\n";
      $datacenterheader .= "</table>\n";

      $datacenterheader .= "<div id=\"datacenter-listing-help\" style=\"display: none\">\n";


      $noc = '';

      $nocheader  = "<p></p>\n";
      $nocheader .= "<table class=\"ui-styled-table\">\n";
      $nocheader .= "<tr>\n";
      $nocheader .= "  <th class=\"ui-state-default\">NOC Contacts Listing</th>\n";
      $nocheader .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('noc-listing-help');\">Help</a></th>\n";
      $nocheader .= "</tr>\n";
      $nocheader .= "</table>\n";

      $nocheader .= "<div id=\"noc-listing-help\" style=\"display: none\">\n";


      $customer = '';

      $customerheader  = "<p></p>\n";
      $customerheader .= "<table class=\"ui-styled-table\">\n";
      $customerheader .= "<tr>\n";
      $customerheader .= "  <th class=\"ui-state-default\">Customer Listing</th>\n";
      $customerheader .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('customer-listing-help');\">Help</a></th>\n";
      $customerheader .= "</tr>\n";
      $customerheader .= "</table>\n";

      $customerheader .= "<div id=\"customer-listing-help\" style=\"display: none\">\n";


      $header  = "<div class=\"main-help ui-widget-content\">\n";
      $header .= "<ul>\n";
      $header .= "  <li><strong>Location Listing</strong>\n";
      $header .= "  <ul>\n";
      $header .= "    <li><strong>Editing</strong> - Click on a location to edit it.</li>\n";
      $header .= "  </ul></li>\n";
      $header .= "</ul>\n";

      $header .= "</div>\n";

      $header .= "</div>\n";

      $header .= "<table class=\"ui-styled-table\">\n";
      $header .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $header .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $header .= "  <th class=\"ui-state-default\">Descriptive Label</th>\n";
      $header .= "  <th class=\"ui-state-default\">Address 1</th>\n";
      $header .= "  <th class=\"ui-state-default\">Address 2</th>\n";
      $header .= "  <th class=\"ui-state-default\">Suite</th>\n";
      $header .= "  <th class=\"ui-state-default\">City</th>\n";
      $header .= "  <th class=\"ui-state-default\">State</th>\n";
      $header .= "  <th class=\"ui-state-default\">Zipcode</th>\n";
      $header .= "  <th class=\"ui-state-default\">Country</th>\n";
      $header .= "  <th class=\"ui-state-default\">CLLI</th>\n";
      $header .= "  <th class=\"ui-state-default\">Identity</th>\n";
      $header .= "  <th class=\"ui-state-default\">Instance</th>\n";
      $header .= "  <th class=\"ui-state-default\">Env</th>\n";
      $header .= "</tr>\n";

      $q_string  = "select loc_id,loc_name,loc_addr1,loc_addr2,loc_suite,ct_city,loc_type,loc_identity,";
      $q_string .= "st_acronym,loc_zipcode,cn_acronym,loc_details,loc_default,ct_clli,loc_instance,";
      $q_string .= "env_abb ";
      $q_string .= "from locations ";
      $q_string .= "left join cities  on cities.ct_id  = locations.loc_city ";
      $q_string .= "left join states  on states.st_id  = cities.ct_state ";
      $q_string .= "left join country on country.cn_id = states.st_country ";
      $q_string .= "left join environment on environment.env_id = locations.loc_environment ";
      $q_string .= "order by loc_identity,loc_name,ct_city ";
      $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_locations) > 0) {
        while ($a_locations = mysqli_fetch_array($q_locations)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('datacenter.fill.php?id="  . $a_locations['loc_id'] . "');jQuery('#dialogDatacenter').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('datacenter.del.php?id=" . $a_locations['loc_id'] . "');\">";
          $linkend   = "</a>";

          if ($a_locations['loc_default']) {
            $class = "ui-state-highlight";
          } else {
            $class = "ui-widget-content";
          }

          $output  = "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                            . "</td>";
          }
          $output .= "  <td class=\"" . $class . "\" title=\"ID=" . $a_locations['loc_id'] . "\">"          . $linkstart . $a_locations['loc_name']       . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['loc_addr1']      . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['loc_addr2']      . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['loc_suite']      . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['ct_city']        . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['st_acronym']     . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['loc_zipcode']    . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['cn_acronym']     . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['ct_clli']        . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['loc_identity']   . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['loc_instance']   . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"          . $linkstart . $a_locations['env_abb']        . $linkend . "</td>";
          $output .= "</tr>";

          if ($a_locations['loc_type'] == 1) {
            $datacenter .= $output;
          }
          if ($a_locations['loc_type'] == 3) {
            $noc .= $output;
          }
          if ($a_locations['loc_type'] == 4) {
            $customer .= $output;
          }

        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"12\">No records found.</td>";
        $output .= "</tr>";
      }

      $footer = "</table>";

      mysqli_free_result($q_locations);

      $datacenter_output = $datacenterheader . $header . $datacenter . $footer;
      $noc_output        = $nocheader        . $header . $noc        . $footer;
      $customer_output   = $customerheader   . $header . $customer   . $footer;

      print "document.getElementById('datacenter_mysql').innerHTML = '" . mysqli_real_escape_string($db, $datacenter_output) . "';\n\n";
      print "document.getElementById('noc_mysql').innerHTML = '"        . mysqli_real_escape_string($db, $noc_output)        . "';\n\n";
      print "document.getElementById('customer_mysql').innerHTML = '"   . mysqli_real_escape_string($db, $customer_output)   . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
