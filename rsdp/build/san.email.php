<?php
# Script: san.email.php
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
    $package = "san.email.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Emailing Storage about " . $formVars['id']);

      $headers  = "From: RSDP <rsdp@" . $hostname . ">\r\n";

      $q_string  = "select rsdp_sanpoc ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_id = " . $formVars['rsdp'];
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

      if ($a_rsdp_server['rsdp_sanpoc'] > 0) {
        $q_string  = "select usr_email ";
        $q_string .= "from users ";
        $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_sanpoc'];
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_users = mysqli_fetch_array($q_users);

        $email = $a_users['usr_email'];
      } else {
        $q_string  = "select grp_email ";
        $q_string .= "from a_groups ";
        $q_string .= "where grp_id = " . $GRP_SAN . " ";
        $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_groups = mysqli_fetch_array($q_groups);

        $email = $a_groups['grp_email'];
      }

      $q_string  = "select san_switch,san_port ";
      $q_string .= "from rsdp_san ";
      $q_string .= "where san_id = " . $formVars['id'];
      $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_san = mysqli_fetch_array($q_rsdp_interface);

      $body = "An RSDP SAN interface has been deleted and the following configuration can now be returned to availability:\n\n";

      if (strlen($a_rsdp_san['san_switch']) > 0) {
        $body .= "Switch: " . $a_rsdp_san['san_switch'] . "/" . $a_rsdp_san['san_port'] . "\n";
      }

      $body .= "\nIf you have questions, please contact: " . $_SESSION['username'] . "\n\n";

      if ($Siteenv == 'PROD') {
        $mailto = $email . "," . $Siteadmins;
      } else {
        if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
          $mailto = $Sitedev . "," . $_SESSION['email'];
        } else {
          $mailto = $Sitedev;
        }
      }

      mail($mailto, "RSDP: IP/Switch Recovery", $body, $headers);

      print "alert('Email sent.');\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
