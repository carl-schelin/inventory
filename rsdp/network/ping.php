<?php
# Script: ping.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  $formVars['address'] = clean($_GET['address'], 20);

  $ping = "no";
  $dns = "no";
  if (filter_var($formVars['address'], FILTER_VALIDATE_IP)) {
    if (ping($formVars['address'])) {
      $ping = "yes";
    }
    $dns = gethostbyaddr($formVars['address']);
  }

  if ($ping == 'yes') {
    $message = "The IP provided is currently responding to ping";
    if ($dns != 'no') {
      $message .= " and is assigned to " . $dns;
    }
    $message .= ".\n\nAre you sure you want to assign " . $formVars['address'] . " to this server/interface?";
    print "alert('" . mysqli_real_escape_string($message) . "');\n";
  }
?>
