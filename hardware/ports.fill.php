<?php
# Script: ports.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "ports.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_ports");

      $q_string  = "select port_deviceid,port_name,port_type,port_active,port_desc,port_facing ";
      $q_string .= "from inv_ports ";
      $q_string .= "where port_id = " . $formVars['id'];
      $q_inv_ports = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_ports = mysqli_fetch_array($q_inv_ports);
      mysqli_free_result($q_inv_ports);

      $portdeviceid = return_Index($db, $a_inv_ports['port_deviceid'], "select ast_id from inv_assets where ast_name != \"\" order by ast_name ");
      $porttype     = return_Index($db, $a_inv_ports['port_type'],     "select plug_id from inv_int_plugtype order by plug_text" );

      print "document.formUpdate.port_name.value = '"    . mysqli_real_escape_string($db, $a_inv_ports['port_name'])    . "';\n";
      print "document.formUpdate.port_desc.value = '"    . mysqli_real_escape_string($db, $a_inv_ports['port_desc'])    . "';\n";

      print "document.formUpdate.port_deviceid['" . $portdeviceid . "'].selected = true;\n";
      print "document.formUpdate.port_type['"     . $porttype     . "'].selected = true;\n";

      if ($a_inv_ports['port_active']) {
        print "document.formUpdate.port_active.checked = true;\n";
      } else {
        print "document.formUpdate.port_active.checked = false;\n";
      }
      if ($a_inv_ports['port_facing']) {
        print "document.formUpdate.port_facing.checked = true;\n";
      } else {
        print "document.formUpdate.port_facing.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
