<?php
# Script: filesystem.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from filesystem");

      $q_string  = "select fs_backup,fs_device,fs_mount,fs_group,fs_size,fs_wwid,fs_subsystem,fs_lun,fs_volume,fs_volid,fs_path ";
      $q_string .= "from filesystem ";
      $q_string .= "where fs_id = " . $formVars['id'];
      $q_filesystem = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_filesystem = mysqli_fetch_array($q_filesystem);
      mysqli_free_result($q_filesystem);

      $group = return_Index($db, $a_filesystem['fs_group'], "select grp_id from inv_groups where grp_disabled = 0 order by grp_name");

      print "document.formFilesystemUpdate.fs_device.value = '"    . mysqli_real_escape_string($db, $a_filesystem['fs_device'])    . "';\n";
      print "document.formFilesystemUpdate.fs_mount.value = '"     . mysqli_real_escape_string($db, $a_filesystem['fs_mount'])     . "';\n";
      print "document.formFilesystemUpdate.fs_size.value = '"      . mysqli_real_escape_string($db, $a_filesystem['fs_size'])      . "';\n";
      print "document.formFilesystemUpdate.fs_wwid.value = '"      . mysqli_real_escape_string($db, $a_filesystem['fs_wwid'])      . "';\n";
      print "document.formFilesystemUpdate.fs_subsystem.value = '" . mysqli_real_escape_string($db, $a_filesystem['fs_subsystem']) . "';\n";
      print "document.formFilesystemUpdate.fs_volume.value = '"    . mysqli_real_escape_string($db, $a_filesystem['fs_volume'])    . "';\n";
      print "document.formFilesystemUpdate.fs_lun.value = '"       . mysqli_real_escape_string($db, $a_filesystem['fs_lun'])       . "';\n";
      print "document.formFilesystemUpdate.fs_volid.value = '"     . mysqli_real_escape_string($db, $a_filesystem['fs_volid'])     . "';\n";
      print "document.formFilesystemUpdate.fs_path.value = '"      . mysqli_real_escape_string($db, $a_filesystem['fs_path'])      . "';\n";

      if ($group > 0) {
        print "document.formFilesystemUpdate.fs_group['" . $group . "'].selected = true;\n";
      }

      if ($a_filesystem['fs_backup']) {
        print "document.formFilesystemUpdate.fs_backup.checked = true;\n";
      } else {
        print "document.formFilesystemUpdate.fs_backup.checked = false;\n";
      }

      print "document.formFilesystemUpdate.fs_id.value = "         . mysqli_real_escape_string($db, $formVars['id'])               . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
