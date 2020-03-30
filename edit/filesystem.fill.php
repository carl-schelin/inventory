<?php
# Script: filesystem.fill.php
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
    $package = "filesystem.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from filesystem");

      $q_string  = "select fs_backup,fs_device,fs_mount,fs_group,fs_size,fs_wwid,fs_subsystem,fs_lun,fs_volume,fs_volid,fs_path ";
      $q_string .= "from filesystem ";
      $q_string .= "where fs_id = " . $formVars['id'];
      $q_filesystem = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_filesystem = mysql_fetch_array($q_filesystem);
      mysql_free_result($q_filesystem);

      $group = return_Index($a_filesystem['fs_group'], "select grp_id from groups where grp_disabled = 0 order by grp_name");

      print "document.edit.fs_device.value = '"    . mysql_real_escape_string($a_filesystem['fs_device'])    . "';\n";
      print "document.edit.fs_mount.value = '"     . mysql_real_escape_string($a_filesystem['fs_mount'])     . "';\n";
      print "document.edit.fs_size.value = '"      . mysql_real_escape_string($a_filesystem['fs_size'])      . "';\n";
      print "document.edit.fs_wwid.value = '"      . mysql_real_escape_string($a_filesystem['fs_wwid'])      . "';\n";
      print "document.edit.fs_subsystem.value = '" . mysql_real_escape_string($a_filesystem['fs_subsystem']) . "';\n";
      print "document.edit.fs_volume.value = '"    . mysql_real_escape_string($a_filesystem['fs_volume'])    . "';\n";
      print "document.edit.fs_lun.value = '"       . mysql_real_escape_string($a_filesystem['fs_lun'])       . "';\n";
      print "document.edit.fs_volid.value = '"     . mysql_real_escape_string($a_filesystem['fs_volid'])     . "';\n";
      print "document.edit.fs_path.value = '"      . mysql_real_escape_string($a_filesystem['fs_path'])      . "';\n";

      print "document.edit.fs_group['" . $group . "'].selected = true;\n";

      if ($a_filesystem['fs_backup']) {
        print "document.edit.fs_backup.checked = true;\n";
      } else {
        print "document.edit.fs_backup.checked = false;\n";
      }

      print "document.edit.fs_id.value = "         . mysql_real_escape_string($formVars['id'])               . ";\n";

      print "document.edit.fs_update.disabled = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
