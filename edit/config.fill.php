<?php
# Script: association.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Fill in the forms for editing

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "association.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from cluster");

      $q_string  = "select clu_companyid,clu_association,clu_notes ";
      $q_string .= "from cluster ";
      $q_string .= "where clu_id = " . $formVars['id'];
      $q_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_cluster = mysqli_fetch_array($q_cluster);
      mysqli_free_result($q_cluster);

      $association = return_Index($db, $a_cluster['clu_association'], "select inv_id from inventory where inv_status = 0 order by inv_name");

      print "document.edit.clu_association['" . $association . "'].selected = true;\n";

      print "document.edit.clu_notes.value = '"    . mysqli_real_escape_string($a_cluster['clu_notes'])    . "';\n\n";

      print "document.edit.clu_id.value = " . $formVars['id'] . ";\n";
      print "document.edit.clu_update.disabled = false;\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
