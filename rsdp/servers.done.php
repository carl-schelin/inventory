<?php
# Script: servers.done.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "servers.done.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Admin)) {
      logaccess($db, $_SESSION['uid'], $package, "Completing RSDP " . $formVars['id']);

      for ($i = 1; $i < 19; $i++) {
        $q_string  = "select st_id ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $formVars['id'] . " and st_step = " . $i;
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

        if ($a_rsdp_status['st_id'] == '') {
          $q_string  = "insert into rsdp_status set ";
          $q_string .= "st_id        = null, ";
          $q_string .= "st_rsdp      = " . $formVars['id'] . ", ";
          $q_string .= "st_completed = 1, ";
          $q_string .= "st_user      = 1, ";
          $q_string .= "st_step      = " . $i;
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }
      }

      print "alert('Server marked as complete.');\n";

      print "clear_fields();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
