<?php
# Script: index.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
#
# Concept here is to get the values for the four drop downs and properly set the 
# various drop downs to appropriate values.
#

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "index.mysql.php";
    $formVars['country']    = clean($_GET['country'],    10);
    $formVars['state']      = clean($_GET['state'],      10);
    $formVars['city']       = clean($_GET['city'],       10);
    $formVars['location']   = clean($_GET['location'],   10);

# if country has been selected, then we can rebuild the state drop down listing based on the country selected
    if ($formVars['country'] > 0) {
# rebuild the state drop down box
      print "var selbox = document.index.state;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"State\",0);\n";

      $q_string  = "select st_id,st_state ";
      $q_string .= "from states ";
      $q_string .= "where st_country = " . $formVars['country'] . " ";
      $q_string .= "order by st_state ";
      $q_states = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_states) > 0) {
        while ($a_states = mysqli_fetch_array($q_states)) {
          print "selbox.options[selbox.options.length] = new Option(\"" . $a_states['st_state'] . "\"," . $a_states['st_id'] . ");\n";
          if ($formVars['state'] == $a_states['st_id']) {
            print "selbox.options[selbox.options.length - 1].selected = true;\n";
          }
        }
      }
      print "selbox.disabled = false;\n";
    } else {
      print "var selbox = document.index.state;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"State\",0);\n";
      print "selbox.disabled = true;\n";
      $formVars['state'] = 0;
    }

# if a state has been selected, then we can rebuild the city drop down listing based on the state selected
    if ($formVars['state'] > 0) {
# rebuild the city drop down box
      print "var selbox = document.index.city;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"City\",0);\n";

      $q_string  = "select ct_id,ct_city ";
      $q_string .= "from cities ";
      $q_string .= "where ct_state = " . $formVars['state'] . " ";
      $q_string .= "order by ct_city ";
      $q_cities = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_cities) > 0) {
        while ($a_cities = mysqli_fetch_array($q_cities)) {
          print "selbox.options[selbox.options.length] = new Option(\"" . $a_cities['ct_city'] . "\"," . $a_cities['ct_id'] . ");\n";
          if ($formVars['city'] == $a_cities['ct_id']) {
            print "selbox.options[selbox.options.length - 1].selected = true;\n";
          }
        }
      }
      print "selbox.disabled = false;\n";
    } else {
      print "var selbox = document.index.city;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"City\",0);\n";
      print "selbox.disabled = true;\n";
      $formVars['city'] = 0;
    }

# if a city has been selected, then we can rebuild the datacenter drop down listing based on the city selected
    if ($formVars['city'] > 0) {
# rebuild the datacenter drop down box
      print "var selbox = document.index.location;\n\n";
      print "selbox.options.length = 0;\n";
      print "selbox.options[selbox.options.length] = new Option(\"Data Center\",0);\n";

      $q_string  = "select loc_id,loc_name ";
      $q_string .= "from locations ";
      $q_string .= "where loc_city = " . $formVars['city'] . " ";
      $q_string .= "order by loc_name ";
      $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_locations) > 0) {
        while ($a_locations = mysqli_fetch_array($q_locations)) {
          print "selbox.options[selbox.options.length] = new Option(\"" . $a_locations['loc_name'] . "\"," . $a_locations['loc_id'] . ");\n";
          if ($formVars['location'] == $a_locations['loc_id']) {
            print "selbox.options[selbox.options.length - 1].selected = true;\n";
          }
        }
      }
      print "selbox.disabled = false;\n";
    } else {
      if ($formVars['country'] == 0 && $formVars['state'] == 0 && $formVars['city'] == 0) {
# rebuild the datacenter drop down box for just default locations
        print "var selbox = document.index.location;\n\n";
        print "selbox.options.length = 0;\n";
        print "selbox.options[selbox.options.length] = new Option(\"Data Center\",0);\n";

        $q_string  = "select loc_id,loc_name ";
        $q_string .= "from locations ";
        $q_string .= "where loc_type = 1 ";
        $q_string .= "order by loc_name ";
        $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_locations) > 0) {
          while ($a_locations = mysqli_fetch_array($q_locations)) {
            print "selbox.options[selbox.options.length] = new Option(\"" . $a_locations['loc_name'] . "\"," . $a_locations['loc_id'] . ");\n";
            if ($formVars['location'] == $a_locations['loc_id']) {
              print "selbox.options[selbox.options.length - 1].selected = true;\n";
            }
          }
        }
        print "selbox.disabled = false;\n";
      } else {
        print "var selbox = document.index.location;\n\n";
        print "selbox.options.length = 0;\n";
        print "selbox.options[selbox.options.length] = new Option(\"Data Center\",0);\n";
        print "selbox.disabled = true;\n";
        $formVars['location'] = 0;
      }
    }
  }
?>
