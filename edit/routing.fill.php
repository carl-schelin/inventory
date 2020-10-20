<?php
# Script: routing.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from routing");

      $q_string  = "select route_companyid,route_address,route_gateway,route_mask,route_source,route_interface,route_desc,route_static ";
      $q_string .= "from routing ";
      $q_string .= "where route_id = " . $formVars['id'];
      $q_routing = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_routing = mysql_fetch_array($q_routing);
      mysql_free_result($q_routing);

      $q_string  = "select int_id ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_routing['route_companyid'] . " ";
      $q_string .= "order by int_face";
      $interface = return_Index($a_routing['route_interface'], $q_string);

      print "document.edit.route_address.value = '" . mysql_real_escape_string($a_routing['route_address']) . "';\n";
      print "document.edit.route_gateway.value = '" . mysql_real_escape_string($a_routing['route_gateway']) . "';\n";
      print "document.edit.route_desc.value = '"    . mysql_real_escape_string($a_routing['route_desc'])    . "';\n";
      print "document.edit.route_source.value = '"  . mysql_real_escape_string($a_routing['route_source'])  . "';\n";

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
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
