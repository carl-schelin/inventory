<?php
# Script: datacenter.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_locations");

      $q_string  = "select loc_name,loc_addr1,loc_addr2,loc_suite,loc_city,loc_default,loc_type,";
      $q_string .= "loc_zipcode,loc_details,loc_instance,";
      $q_string .= "loc_contact1,loc_contact2,loc_identity,loc_environment ";
      $q_string .= "from inv_locations ";
      $q_string .= "where loc_id = " . $formVars['id'];
      $q_inv_locations = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_inv_locations = mysqli_fetch_array($q_inv_locations);
      mysqli_free_result($q_inv_locations);

      $q_string  = "select ct_id,ct_city,st_acronym,cn_acronym ";
      $q_string .= "from inv_cities ";
      $q_string .= "left join inv_states on inv_states.st_id = inv_cities.ct_state ";
      $q_string .= "left join country on country.cn_id = inv_states.st_country ";
      $q_string .= "order by ct_city,st_acronym,cn_acronym ";

      $city = return_Index($db, $a_inv_locations['loc_city'], $q_string);
      $type = return_Index($db, $a_inv_locations['loc_type'], "select typ_id from loc_types order by typ_name");
      $env  = return_Index($db, $a_inv_locations['loc_environment'], "select env_id from environment order by env_name");

      print "document.formUpdate.loc_name.value = '"       . mysqli_real_escape_string($db, $a_inv_locations['loc_name'])       . "';\n";
      print "document.formUpdate.loc_addr1.value = '"      . mysqli_real_escape_string($db, $a_inv_locations['loc_addr1'])      . "';\n";
      print "document.formUpdate.loc_addr2.value = '"      . mysqli_real_escape_string($db, $a_inv_locations['loc_addr2'])      . "';\n";
      print "document.formUpdate.loc_suite.value = '"      . mysqli_real_escape_string($db, $a_inv_locations['loc_suite'])      . "';\n";
      print "document.formUpdate.loc_zipcode.value = '"    . mysqli_real_escape_string($db, $a_inv_locations['loc_zipcode'])    . "';\n";
      print "document.formUpdate.loc_contact1.value = '"   . mysqli_real_escape_string($db, $a_inv_locations['loc_contact1'])   . "';\n";
      print "document.formUpdate.loc_contact2.value = '"   . mysqli_real_escape_string($db, $a_inv_locations['loc_contact2'])   . "';\n";
      print "document.formUpdate.loc_details.value = '"    . mysqli_real_escape_string($db, $a_inv_locations['loc_details'])    . "';\n";
      print "document.formUpdate.loc_instance.value = '"   . mysqli_real_escape_string($db, $a_inv_locations['loc_instance'])   . "';\n";
      print "document.formUpdate.loc_identity.value = '"   . mysqli_real_escape_string($db, $a_inv_locations['loc_identity'])   . "';\n";

      if ($city > 0) {
        print "document.formUpdate.loc_city['"        . $city . "'].selected = true;\n";
      }
      if ($type > 0) {
        print "document.formUpdate.loc_type['"        . $type . "'].selected = true;\n";
      }
      if ($env > 0) {
        print "document.formUpdate.loc_environment['" . $env  . "'].selected = true;\n";
      }

      if ($a_inv_locations['loc_default']) {
        print "document.formUpdate.loc_default.checked = true;\n";
      } else {
        print "document.formUpdate.loc_default.checked = false;\n";
      }

      $loc_tags = '';
      $space = '';
      $q_string  = "select tag_name ";
      $q_string .= "from tags ";
      $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_type = 2 ";
      $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_tags) > 0) {
        while ($a_tags = mysqli_fetch_array($q_tags)) {
          $loc_tags .= $space . $a_tags['tag_name'];
          $space = " ";
        }
      }
      print "document.formUpdate.loc_tags.value = '" . mysqli_real_escape_string($db, $loc_tags) . "';\n";

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
