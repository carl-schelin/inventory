<?php
# Script: hostname.encode.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  $formVars['location']  = clean($_GET['location'],   10);
  $formVars['zone']      = clean($_GET['zone'],       10);
  $formVars['device']    = clean($_GET['device'],     10);
  $formVars['service']   = clean($_GET['service'],    10);
  $formVars['freeform']  = clean($_GET['freeform'],   10);

  $formVars['hostname'] = '';
  $zone[0] = "";
  $zone[1] = "C";
  $zone[2] = "E";
  $zone[3] = "D";
  $zone[4] = "A";
  $zone[5] = "M";

  $q_string  = "select loc_instance,ct_clli ";
  $q_string .= "from inv_locations ";
  $q_string .= "left join inv_cities on inv_cities.ct_id = inv_locations.loc_city ";
  $q_string .= "where loc_id = " . $formVars['location'] . " ";
  $q_inv_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_inv_locations = mysqli_fetch_array($q_inv_locations);

  $q_string  = "select dev_type,dev_infrastructure ";
  $q_string .= "from inv_device ";
  $q_string .= "where dev_id = " . $formVars['device'] . " ";
  $q_inv_device = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_inv_device = mysqli_fetch_array($q_inv_device);

  $q_string  = "select prod_code ";
  $q_string .= "from inv_products ";
  $q_string .= "where prod_id = " . $formVars['service'] . " ";
  $q_inv_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_inv_products = mysqli_fetch_array($q_inv_products);

  if ($a_inv_device['dev_infrastructure']) {
    $a_inv_products['prod_code'] = '';
    print "document.getElementById(\"service\").disabled = true;\n";
    print "document.getElementById('characters').innerHTML = 'six';\n";
    print "document.hostname.service[0].text = 'Infrastructure';\n";
    print "document.hostname.service[0].selected = true;\n";
  } else {
    print "document.getElementById(\"service\").disabled = false;\n";
    print "document.getElementById('characters').innerHTML = 'four';\n";
    print "document.hostname.service[0].text = 'Unassigned';\n";
# don't do selected as it'll move the menu unnecessarily.
  }


# now build the hostname
  $formVars['hostname'] = 
    $a_inv_locations['ct_clli']      . 
    $a_inv_locations['loc_instance'] . 
    $zone[$formVars['zone']]     . 
    $a_inv_device['dev_type']        . 
    $a_inv_products['prod_code']     . 
    strtoupper($formVars['freeform']);

  if (strlen($formVars['hostname']) > 0) {
    $q_string  = "select inv_function ";
    $q_string .= "from inv_inventory ";
    $q_string .= "where inv_name = '" . $formVars['hostname'] . "' and inv_status = 0 ";
    $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_inv_inventory) > 0) {
      $a_inv_inventory = mysqli_fetch_array($q_inv_inventory);
      $formVars['hostname'] .= " (System is in the Inventory: Function: " . $a_inv_inventory['inv_function'] . ")";
    }
  }

  print "document.getElementById('encodedhostname').innerHTML = '" . mysqli_real_escape_string($db, $formVars['hostname']) . "';\n";

?>
