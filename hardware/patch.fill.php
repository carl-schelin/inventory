<?php
# Script: patch.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "patch.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_patch");

      $q_string  = "select pat_deviceid,pat_name,pat_type,pat_active,pat_desc,pat_office ";
      $q_string .= "from inv_patch ";
      $q_string .= "where pat_id = " . $formVars['id'];
      $q_inv_patch = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_patch = mysqli_fetch_array($q_inv_patch);
      mysqli_free_result($q_inv_patch);

      $patdeviceid = return_Index($db, $a_inv_patch['pat_deviceid'], "select ast_id from inv_assets where ast_name != \"\" order by ast_name ");
      $pattype     = return_Index($db, $a_inv_patch['pat_type'],     "select port_id from inv_int_porttype order by port_text");

      print "document.formUpdate.pat_name.value = '"    . mysqli_real_escape_string($db, $a_inv_patch['pat_name'])    . "';\n";
      print "document.formUpdate.pat_desc.value = '"    . mysqli_real_escape_string($db, $a_inv_patch['pat_desc'])    . "';\n";
      print "document.formUpdate.pat_office.value = '"  . mysqli_real_escape_string($db, $a_inv_patch['pat_office'])  . "';\n";

      print "document.formUpdate.pat_deviceid['" . $patdeviceid . "'].selected = true;\n";
      print "document.formUpdate.pat_type['"     . $pattype     . "'].selected = true;\n";

      if ($a_inv_patch['pat_active']) {
        print "document.formUpdate.pat_active.checked = true;\n";
      } else {
        print "document.formUpdate.pat_active.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
