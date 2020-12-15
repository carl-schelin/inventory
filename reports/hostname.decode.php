<?php
# Script: hostname.decode.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  $formVars['server'] = clean($_GET['server'],       60);

  $formVars['linkstart'] = '';

  $formVars['i15location'] = '';
  $formVars['i15instance'] = '';
  $formVars['i15zone'] = '';
  $formVars['i15device'] = '';
  $formVars['i15service'] = '';
  $formVars['i15freeform'] = '';

  $formVars['c15location'] = '';
  $formVars['c15state'] = '';
  $formVars['c15type'] = '';
  $formVars['c15device'] = '';
  $formVars['c15instance'] = '';

  $formVars['08location'] = '';
  $formVars['08state'] = '';
  $formVars['08type'] = '';
  $formVars['08device'] = '';
  $formVars['08instance'] = '';
  $formVars['08interface'] = '';

  $formVars['company'] = '';
  $formVars['state'] = '';
  $formVars['freeform'] = '';

# okay, break down the passed server name to fill in the blanks.

  if (strlen($formVars['server']) > 0) {

    $q_string  = "select inv_id,inv_function ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $formVars['server'] . "' ";
    $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_inventory) == 0) {
      $q_string  = "select inv_id,inv_name,inv_function ";
      $q_string .= "from interface "; 
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "where int_server = '" . $formVars['server'] . "' ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_interface) > 0) {
        $a_interface = mysqli_fetch_array($q_interface);
        print "document.getElementById('gohere').innerHTML = ' Server found! <a href=\"" . $Showroot . "/inventory.php?server=" . $a_interface['inv_id'] . "\" target=\"_blank\">" . $a_interface['inv_name'] . "</a> Function: " . $a_interface['inv_function'] . "';\n";
      } else {
        print "document.getElementById('gohere').innerHTML = '';\n";
      }
    } else {
      $a_inventory = mysqli_fetch_array($q_inventory);
      print "document.getElementById('gohere').innerHTML = ' Server found! <a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">" . $formVars['server'] . "</a> Function: " . $a_inventory['inv_function'] . "';\n";
    }

    $os_location = strtolower(substr($formVars['server'],  0, 4));
    $os_instance = strtolower(substr($formVars['server'],  4, 1));
    $os_zone     = strtolower(substr($formVars['server'],  5, 1));
    $os_device   = strtolower(substr($formVars['server'],  6, 3));
    $os_service  = strtolower(substr($formVars['server'],  9, 2));
    $os_freeform = strtolower(substr($formVars['server'], 11));

# if the 5 character is a number, this is an Internal 2015 standard name:
      if (is_numeric($os_instance) === true) {

        $q_string  = "select loc_name ";
        $q_string .= "from locations ";
        $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
        $q_string .= "where ct_clli = '" . $os_location . "' and loc_instance = " . $os_instance . " and loc_type = 1 ";
        $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_locations = mysqli_fetch_array($q_locations);

        $formVars['i15location'] = $a_locations['loc_name'];

        $formVars['i15instance'] = $os_instance;

        if (strlen($os_zone) > 0) {
          if ($os_zone == 'c') {
            $formVars['i15zone'] = "Enterprise/Corporate";
          }
          if ($os_zone == 'e') {
            $formVars['i15zone'] = "E911";
          }
          if ($os_zone == 'd') {
            $formVars['i15zone'] = "DMZ";
          }
          if ($os_zone == 'a') {
            $formVars['i15zone'] = "Agnostic/Cross Zones";
          }
          if ($os_zone == 'm') {
            $formVars['i15zone'] = "IDM Zone";
          }
        }

        if (strlen($os_device) > 0) {
          $q_string  = "select dev_id,dev_type,dev_description,dev_infrastructure ";
          $q_string .= "from device ";
          $q_device = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_device = mysqli_fetch_array($q_device)) {
            if ($os_device == strtolower($a_device['dev_type'])) {
              $formVars['i15device'] = $a_device['dev_type'] . " (" . $a_device['dev_description'] . ")";
              $os_infrastructure = $a_device['dev_infrastructure'];
            }
          }
        }

        if (strlen($os_service) > 0) {
          $q_string  = "select prod_name ";
          $q_string .= "from products ";
          $q_string .= "where prod_code = '" . $os_service . "' ";
          $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_products = mysqli_fetch_array($q_products);

          if ($os_infrastructure) {
            $formVars['i15service'] = 'Infrastructure';
            $os_freeform = $os_service . $os_freeform;
          } else {
            $formVars['i15service'] = $a_products['prod_name'];
          }
        }

        if (strlen($os_service) > 0) {
          $formVars['i15freeform'] = strtoupper($os_freeform);
        }
      } else {
# may be one of the older standards. check for the co in lnmtco (check states)

        $os_location = strtolower(substr($formVars['server'],  0, 4));
        $os_state    = strtolower(substr($formVars['server'],  4, 2));
        $os_site     = strtolower(substr($formVars['server'],  6, 2));

# after this it gets hairy.
#  Could be two or three characters such as eg or etl or frl
#  Then could be an instance number or letter (1, 2, 3 or a, b, c)
#  Then an interface number; 0 or 1
# *** Could Be ***
# get the length of the string. Subtract 8 (0-7; lnmt co dc) to get the last x digits. Then subtract 2 to get the length of the service
# One more for the instance
# One more for the interface
#
        if (strlen($formVars['server']) > 8) {
# this will be the length of the data; 2 or 3 characters typically.
          $svr_length = strlen($formVars['server']) - 8;
          $os_service  = strtolower(substr($formVars['server'],  8, $svr_length));

# length to the instance
          $svr_length = strlen($formVars['server']) - 1;
          $os_instance = strtolower(substr($formVars['server'], $svr_length, 1));

# length to the interface
          $svr_length = strlen($formVars['server']);
          $os_interface = strtolower(substr($formVars['server'], $svr_length, 1));

          $q_string  = "select loc_name ";
          $q_string .= "from locations ";
          $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
          $q_string .= "where ct_clli = '" . $os_location . "' and loc_type = 1 ";
          $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_locations = mysqli_fetch_array($q_locations);

          $formVars['08location']  = $a_locations['loc_name'];

          $q_string  = "select st_state ";
          $q_string .= "from states ";
          $q_string .= "where st_acronym = '" . $os_state . "' ";
          $q_states = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_states = mysqli_fetch_array($q_states);

          $formVars['08state']     = $a_states['st_state'];

          $formVars['08type'] = $os_site;
          if ($os_site == 'dc') {
            $formVars['08type'] = 'Data Center';
          }
          if ($os_site == 'dz') {
            $formVars['08type'] = 'Data Center, DMZ';
          }
# if it doesn't make sense, hack it :)
          if ($os_site == 'il') {
            $formVars['08type'] = 'Integration Lab';
            $formVars['08location'] = 'Intrado Lab Data Center - Longmont';
          }
          if ($os_site == 'ec') {
            $formVars['08type'] = 'ECMC Site';
          }
          if ($os_site == 'cs') {
            $formVars['08type'] = 'CSS Site';
          }

          $q_string  = "select prod_name ";
          $q_string .= "from inventory ";
          $q_string .= "left join products on products.prod_id = inventory.inv_product ";
          $q_string .= "where inv_name like '" . $os_location . $os_state . $os_site . $os_service . "%' ";
          $q_string .= "limit 1 ";
          $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_inventory = mysqli_fetch_array($q_inventory);

          $formVars['08device']    = $a_inventory['prod_name'];


          $formVars['08instance']  = $os_instance;
          $formVars['08interface'] = $os_interface;
#          $formVars['08interface'] = $q_string;
        } else {

# if it's not the 2015 or 2008 standard, it may be the original standard of inco, infl, psca, psto.

          $os_company        = strtolower(substr($formVars['server'],  0, 2));
          $os_state          = strtolower(substr($formVars['server'],  2, 2));

# could be a 2/2/2/2
          $os_producttwo     = strtolower(substr($formVars['server'],  4, 2));
          $os_instancetwo    = strtolower(substr($formVars['server'],  6, 2));

# could be a 2/2/3/1
          $os_productthree   = strtolower(substr($formVars['server'],  4, 3));
          $os_instanceone    = strtolower(substr($formVars['server'],  7, 1));

          if ($os_company == 'in') {
            $formVars['company'] = 'Intrado';
            $formVars['location'] = 'Intrado Production Data Center - Longmont';
          }
          if ($os_company == 'ps') {
            $formVars['company'] = 'Positron Systems';
          }

          if ($os_state == 'ca') {
            $formVars['state'] = 'Alberta (Calgary: Blame Todd)';
            $formVars['location'] = 'Care Factor - Calgary';
          } else {
            if ($os_state == 'to') {
              $formVars['state'] = 'Ontario (Toronto: Blame Todd)';
              $formVars['location'] = 'Switch and Data - Toronto';
            } else {
              if ($os_state == 'il') {
                $formVars['state'] = 'Colorado';
                $formVars['location'] = 'Intrado Lab Data Center - Longmont';
              } else {
                $q_string  = "select st_state ";
                $q_string .= "from states ";
                $q_string .= "where st_acronym = '" . $os_state . "' ";
                $q_states = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                $a_states = mysqli_fetch_array($q_states);

                $formVars['state'] = $a_states['st_state'];
              }
            }
          }

          if (strlen($os_producttwo) > 0) {
            $q_string  = "select prod_name ";
            $q_string .= "from products ";
            $q_string .= "where prod_oldcode = '" . $os_producttwo . "' ";
            $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_products) > 0) {
              $a_products = mysqli_fetch_array($q_products);

              $formVars['product'] = $a_products['prod_name'];
              $formVars['instance']  = $os_instancetwo;
            }

            $q_string  = "select prod_name ";
            $q_string .= "from products ";
            $q_string .= "where prod_oldcode = '" . $os_productthree . "' ";
            $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_products) > 0) {
              $a_products = mysqli_fetch_array($q_products);

              $formVars['product'] = $a_products['prod_name'];
              $formVars['instance']  = $os_instanceone;
            }
          }

          if ($formVars['product'] == '') {
            $formVars['product'] = strtoupper($os_producttwo);
            $formVars['instance'] = $os_instancetwo;
          }

        }
      }
    }


  print "document.getElementById('i15location').innerHTML = '" . mysqli_real_escape_string($db, $formVars['i15location']) . "';\n";
  print "document.getElementById('i15instance').innerHTML = '" . mysqli_real_escape_string($db, $formVars['i15instance']) . "';\n";
  print "document.getElementById('i15zone').innerHTML = '"     . mysqli_real_escape_string($db, $formVars['i15zone'])     . "';\n";
  print "document.getElementById('i15device').innerHTML = '"   . mysqli_real_escape_string($db, $formVars['i15device'])   . "';\n";
  print "document.getElementById('i15service').innerHTML = '"  . mysqli_real_escape_string($db, $formVars['i15service'])  . "';\n";
  print "document.getElementById('i15freeform').innerHTML = '" . mysqli_real_escape_string($db, $formVars['i15freeform']) . "';\n";

  print "document.getElementById('c15location').innerHTML = '" . mysqli_real_escape_string($db, $formVars['c15location']) . "';\n";
  print "document.getElementById('c15state').innerHTML = '"    . mysqli_real_escape_string($db, $formVars['c15state'])    . "';\n";
  print "document.getElementById('c15type').innerHTML = '"     . mysqli_real_escape_string($db, $formVars['c15type'])     . "';\n";
  print "document.getElementById('c15device').innerHTML = '"   . mysqli_real_escape_string($db, $formVars['c15device'])   . "';\n";
  print "document.getElementById('c15instance').innerHTML = '" . mysqli_real_escape_string($db, $formVars['c15instance']) . "';\n";

  print "document.getElementById('08location').innerHTML = '"  . mysqli_real_escape_string($db, $formVars['08location'])  . "';\n";
  print "document.getElementById('08state').innerHTML = '"     . mysqli_real_escape_string($db, $formVars['08state'])     . "';\n";
  print "document.getElementById('08type').innerHTML = '"      . mysqli_real_escape_string($db, $formVars['08type'])      . "';\n";
  print "document.getElementById('08device').innerHTML = '"    . mysqli_real_escape_string($db, $formVars['08device'])    . "';\n";
  print "document.getElementById('08instance').innerHTML = '"  . mysqli_real_escape_string($db, $formVars['08instance'])  . "';\n";
  print "document.getElementById('08interface').innerHTML = '" . mysqli_real_escape_string($db, $formVars['08interface']) . "';\n";

  print "document.getElementById('location').innerHTML = '"    . mysqli_real_escape_string($db, $formVars['location'])    . "';\n";
  print "document.getElementById('company').innerHTML = '"     . mysqli_real_escape_string($db, $formVars['company'])     . "';\n";
  print "document.getElementById('state').innerHTML = '"       . mysqli_real_escape_string($db, $formVars['state'])       . "';\n";
  print "document.getElementById('product').innerHTML = '"     . mysqli_real_escape_string($db, $formVars['product'])     . "';\n";
  print "document.getElementById('instance').innerHTML = '"    . mysqli_real_escape_string($db, $formVars['instance'])    . "';\n";

?>
