<?php 
# Script: san.fill.php
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
    $package = "san.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rsdp_san");

      $q_string  = "select san_id,san_sysport,san_switch,san_port,san_media ";
      $q_string .= "from rsdp_san ";
      $q_string .= "where san_id = " . $formVars['id'];
      $q_rsdp_san = mysql_query($q_string) or die ($q_string . ": " . mysql_error());
      $a_rsdp_san = mysql_fetch_array($q_rsdp_san);
      mysql_free_result($q_rsdp_san);

      $sanmedia = return_Index($a_rsdp_san['san_media'], "select med_id from int_media order by med_text");

      print "document.san.san_sysport.value = '" . mysql_real_escape_string($a_rsdp_san['san_sysport']) . "';\n";
      print "document.san.san_switch.value = '"  . mysql_real_escape_string($a_rsdp_san['san_switch'])  . "';\n";
      print "document.san.san_port.value = '"    . mysql_real_escape_string($a_rsdp_san['san_port'])    . "';\n";

      print "document.san.san_media['" . $sanmedia . "'].selected = true;\n";

      print "document.san.san_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
