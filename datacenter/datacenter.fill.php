<?php
# Script: datacenter.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "datacenter.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from locations");

      $q_string  = "select loc_name,loc_addr1,loc_addr2,loc_suite,loc_city,loc_default,loc_type,";
      $q_string .= "loc_zipcode,loc_details,loc_instance,loc_xpoint,loc_ypoint,loc_xlen,loc_ylen, ";
      $q_string .= "loc_contact1,loc_contact2,loc_west,loc_environment ";
      $q_string .= "from locations ";
      $q_string .= "where loc_id = " . $formVars['id'];
      $q_locations = mysql_query($q_string) or die (mysql_error());
      $a_locations = mysql_fetch_array($q_locations);
      mysql_free_result($q_locations);

      $q_string  = "select ct_id,ct_city,st_acronym,cn_acronym ";
      $q_string .= "from cities ";
      $q_string .= "left join states on states.st_id = cities.ct_state ";
      $q_string .= "left join country on country.cn_id = states.st_country ";
      $q_string .= "order by ct_city,st_acronym,cn_acronym ";

      $city = return_Index($a_locations['loc_city'], $q_string);
      $type = return_Index($a_locations['loc_type'], "select typ_id from loc_types order by typ_name");

      print "document.locations.loc_name.value = '"       . mysql_real_escape_string($a_locations['loc_name'])       . "';\n";
      print "document.locations.loc_addr1.value = '"      . mysql_real_escape_string($a_locations['loc_addr1'])      . "';\n";
      print "document.locations.loc_addr2.value = '"      . mysql_real_escape_string($a_locations['loc_addr2'])      . "';\n";
      print "document.locations.loc_suite.value = '"      . mysql_real_escape_string($a_locations['loc_suite'])      . "';\n";
      print "document.locations.loc_zipcode.value = '"    . mysql_real_escape_string($a_locations['loc_zipcode'])    . "';\n";
      print "document.locations.loc_contact1.value = '"   . mysql_real_escape_string($a_locations['loc_contact1'])   . "';\n";
      print "document.locations.loc_contact2.value = '"   . mysql_real_escape_string($a_locations['loc_contact2'])   . "';\n";
      print "document.locations.loc_details.value = '"    . mysql_real_escape_string($a_locations['loc_details'])    . "';\n";
      print "document.locations.loc_instance.value = '"   . mysql_real_escape_string($a_locations['loc_instance'])   . "';\n";
      print "document.locations.loc_west.value = '"       . mysql_real_escape_string($a_locations['loc_west'])       . "';\n";
      print "document.locations.loc_xpoint.value = '"     . mysql_real_escape_string($a_locations['loc_xpoint'])     . "';\n";
      print "document.locations.loc_ypoint.value = '"     . mysql_real_escape_string($a_locations['loc_ypoint'])     . "';\n";
      print "document.locations.loc_xlen.value = '"       . mysql_real_escape_string($a_locations['loc_xlen'])       . "';\n";
      print "document.locations.loc_ylen.value = '"       . mysql_real_escape_string($a_locations['loc_ylen'])       . "';\n";

      print "document.locations.loc_city['"        . $city                           . "'].selected = true;\n";
      print "document.locations.loc_type['"        . $type                           . "'].selected = true;\n";
      print "document.locations.loc_environment['" . $a_locations['loc_environment'] . "'].selected = true;\n";

      if ($a_locations['loc_default']) {
        print "document.locations.loc_default.checked = true;\n";
      } else {
        print "document.locations.loc_default.checked = false;\n";
      }

      print "document.locations.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
