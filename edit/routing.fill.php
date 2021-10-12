<?php
# Script: routing.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "routing.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from routing");

      $q_string  = "select route_companyid,route_address,route_gateway,route_mask,route_source,route_interface,route_desc,route_static ";
      $q_string .= "from routing ";
      $q_string .= "where route_id = " . $formVars['id'];
      $q_routing = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_routing = mysqli_fetch_array($q_routing);
      mysqli_free_result($q_routing);

      $q_string  = "select int_id ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_routing['route_companyid'] . " ";
      $q_string .= "order by int_face";
      $interface = return_Index($db, $a_routing['route_interface'], $q_string);

      print "document.edit.route_address.value = '" . mysqli_real_escape_string($db, $a_routing['route_address']) . "';\n";
      print "document.edit.route_gateway.value = '" . mysqli_real_escape_string($db, $a_routing['route_gateway']) . "';\n";
      print "document.edit.route_desc.value = '"    . mysqli_real_escape_string($db, $a_routing['route_desc'])    . "';\n";
      print "document.edit.route_source.value = '"  . mysqli_real_escape_string($db, $a_routing['route_source'])  . "';\n";

      print "document.edit.route_mask['"      . $a_routing['route_mask'] . "'].selected = true;\n";
      print "document.edit.route_interface['" . $interface               . "'].selected = true;\n";

      if ($a_routing['route_static']) {
        print "document.edit.route_static.checked = true;\n";
      } else {
        print "document.edit.route_static.checked = false;\n";
      }

      print "document.edit.route_id.value = " . $formVars['id'] . ";\n";

      print "document.edit.route_update.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
