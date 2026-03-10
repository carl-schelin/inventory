<?php
# Script: fiber.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "fiber.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_fiber");

      $q_string  = "select fib_deviceid,fib_name,fib_type,fib_active,fib_desc,fib_office ";
      $q_string .= "from inv_fiber ";
      $q_string .= "where fib_id = " . $formVars['id'];
      $q_inv_fiber = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_fiber = mysqli_fetch_array($q_inv_fiber);
      mysqli_free_result($q_inv_fiber);

      $patdeviceid = return_Index($db, $a_inv_fiber['fib_deviceid'], "select ast_id from inv_assets where ast_name != \"\" order by ast_name ");
      $pattype     = return_Index($db, $a_inv_fiber['fib_type'],     "select ft_id from inv_fibertype order by ft_name");

      print "document.formUpdate.fib_name.value = '"    . mysqli_real_escape_string($db, $a_inv_fiber['fib_name'])    . "';\n";
      print "document.formUpdate.fib_desc.value = '"    . mysqli_real_escape_string($db, $a_inv_fiber['fib_desc'])    . "';\n";
      print "document.formUpdate.fib_office.value = '"  . mysqli_real_escape_string($db, $a_inv_fiber['fib_office'])  . "';\n";

      print "document.formUpdate.fib_deviceid['" . $patdeviceid . "'].selected = true;\n";
      print "document.formUpdate.fib_type['"     . $pattype     . "'].selected = true;\n";

      if ($a_inv_fiber['fib_active']) {
        print "document.formUpdate.fib_active.checked = true;\n";
      } else {
        print "document.formUpdate.fib_active.checked = false;\n";
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
