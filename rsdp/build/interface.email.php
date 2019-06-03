<?php
# Script: interface.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.email.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['rsdp'] = 0;
    if (isset($_GET['rsdp'])) {
      $formVars['rsdp'] = clean($_GET['rsdp'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Emailing Network Engineering about " . $formVars['id']);

      $headers  = "From: RSDP <rsdp@incojs01.scc911.com>\r\n";

      $q_string  = "select rsdp_networkpoc ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_id = " . $formVars['rsdp'];
      $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

      if ($a_rsdp_server['rsdp_networkpoc'] > 0) {
        $q_string  = "select usr_email ";
        $q_string .= "from users ";
        $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_networkpoc'];
        $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_users = mysql_fetch_array($q_users);

        $email = $a_users['usr_email'];
      } else {
        $q_string  = "select grp_email ";
        $q_string .= "from groups ";
        $q_string .= "where grp_id = " . $GRP_Networking . " ";
        $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_groups = mysql_fetch_array($q_groups);

        $email = $a_groups['grp_email'];
      }

      $q_string  = "select if_ip,if_ipcheck,if_switch,if_swcheck,if_port ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "where if_id = " . $formVars['id'];
      $q_rsdp_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_interface = mysql_fetch_array($q_rsdp_interface);

      $body = "An RSDP interface has been deleted and the following configurations can now be returned to availability:\n\n";

      if ($a_rsdp_interface['if_ipcheck'] && strlen($a_rsdp_interface['if_ip']) > 0) {
        $body .= "IP: " . $a_rsdp_interface['if_ip'] . "\n";
      }
      if ($a_rsdp_interface['if_swcheck'] && strlen($a_rsdp_interface['if_switch']) > 0) {
        $body .= "Switch: " . $a_rsdp_interface['if_switch'] . "/" . $a_rsdp_interface['if_port'] . "\n";
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
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
