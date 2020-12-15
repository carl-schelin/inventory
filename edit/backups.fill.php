<?php
# Script: backups.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "backups.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from backups");

      $q_string  = "select bu_id,bu_companyid,bu_start,bu_include,bu_retention,";
      $q_string .= "bu_sunday,bu_monday,bu_tuesday,bu_wednesday,bu_thursday,bu_friday,bu_saturday,";
      $q_string .= "bu_suntime,bu_montime,bu_tuetime,bu_wedtime,bu_thutime,bu_fritime,bu_sattime,bu_notes ";
      $q_string .= "from backups ";
      $q_string .= "where bu_companyid = " . $formVars['id'];
      $q_backups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_backups = mysqli_fetch_array($q_backups);

      if (mysqli_num_rows($q_backups) > 0) {

        print "document.edit.bu_start.value = '"   . mysqli_real_escape_string($db, $a_backups['bu_start'])   . "';\n";
        print "document.edit.bu_suntime.value = '" . mysqli_real_escape_string($db, $a_backups['bu_suntime']) . "';\n";
        print "document.edit.bu_montime.value = '" . mysqli_real_escape_string($db, $a_backups['bu_montime']) . "';\n";
        print "document.edit.bu_tuetime.value = '" . mysqli_real_escape_string($db, $a_backups['bu_tuetime']) . "';\n";
        print "document.edit.bu_wedtime.value = '" . mysqli_real_escape_string($db, $a_backups['bu_wedtime']) . "';\n";
        print "document.edit.bu_thutime.value = '" . mysqli_real_escape_string($db, $a_backups['bu_thutime']) . "';\n";
        print "document.edit.bu_fritime.value = '" . mysqli_real_escape_string($db, $a_backups['bu_fritime']) . "';\n";
        print "document.edit.bu_sattime.value = '" . mysqli_real_escape_string($db, $a_backups['bu_sattime']) . "';\n";
        print "document.edit.bu_notes.value = '"   . mysqli_real_escape_string($db, $a_backups['bu_notes'])   . "';\n";

        print "document.edit.bu_retention['" . $a_backups['bu_retention'] . "'].selected = true;\n";

        print "document.edit.bu_sunday['"    . $a_backups['bu_sunday']    . "'].checked = true;\n";
        print "document.edit.bu_monday['"    . $a_backups['bu_monday']    . "'].checked = true;\n";
        print "document.edit.bu_tuesday['"   . $a_backups['bu_tuesday']   . "'].checked = true;\n";
        print "document.edit.bu_wednesday['" . $a_backups['bu_wednesday'] . "'].checked = true;\n";
        print "document.edit.bu_thursday['"  . $a_backups['bu_thursday']  . "'].checked = true;\n";
        print "document.edit.bu_friday['"    . $a_backups['bu_friday']    . "'].checked = true;\n";
        print "document.edit.bu_saturday['"  . $a_backups['bu_saturday']  . "'].checked = true;\n";

        if ($a_backups['bu_include']) {
          print "document.edit.bu_include.checked = true;\n";
        } else {
          print "document.edit.bu_include.checked = false;\n";
        }

        print "document.edit.bu_id.value = '" . $a_backups['bu_id'] . "'\n";
      }

      mysqli_free_result($q_backups);

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
