<?php
# Script: filesystem.fill.php
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
    $package = "filesystem.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rsdp_filesystem");

      $q_string  = "select fs_id,fs_volume,fs_size,fs_backup ";
      $q_string .= "from rsdp_filesystem ";
      $q_string .= "where fs_id = " . $formVars['id'];
      $q_rsdp_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_rsdp_filesystem = mysql_fetch_array($q_rsdp_filesystem);
      mysql_free_result($q_rsdp_filesystem);

      print "document.filesystem.fs_volume.value = '" . mysql_real_escape_string($a_rsdp_filesystem['fs_volume']) . "';\n";
      print "document.filesystem.fs_size.value = '"   . mysql_real_escape_string($a_rsdp_filesystem['fs_size'])   . "';\n";

      if ($a_rsdp_filesystem['fs_backup']) {
        print "document.filesystem.fs_backup.checked = true;\n";
      } else {
        print "document.filesystem.fs_backup.checked = false;\n";
      }

      print "document.filesystem.fs_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
