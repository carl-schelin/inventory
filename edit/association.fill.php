<?php
# Script: association.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_cluster");

      $q_string  = "select clu_companyid,clu_association,clu_type,clu_source,clu_target,clu_options,clu_local,clu_port,clu_protocol,clu_notes ";
      $q_string .= "from inv_cluster ";
      $q_string .= "where clu_id = " . $formVars['id'];
      $q_inv_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_cluster = mysqli_fetch_array($q_inv_cluster);
      mysqli_free_result($q_inv_cluster);

      $association = return_Index($db, $a_inv_cluster['clu_association'], "select inv_id from inv_inventory where inv_status = 0 order by inv_name") + 1;

      print "document.formAssociationUpdate.clu_association['" . $association               . "'].selected = true;\n";
      print "document.formAssociationUpdate.clu_type['"        . $a_inv_cluster['clu_type'] . "'].selected = true;\n";

      print "document.formAssociationUpdate.clu_source.value = '"   . mysqli_real_escape_string($db, $a_inv_cluster['clu_source'])   . "';\n\n";
      print "document.formAssociationUpdate.clu_target.value = '"   . mysqli_real_escape_string($db, $a_inv_cluster['clu_target'])   . "';\n\n";
      print "document.formAssociationUpdate.clu_options.value = '"  . mysqli_real_escape_string($db, $a_inv_cluster['clu_options'])  . "';\n\n";
      print "document.formAssociationUpdate.clu_notes.value = '"    . mysqli_real_escape_string($db, $a_inv_cluster['clu_notes'])    . "';\n\n";

      print "document.formAssociationUpdate.clu_id.value = " . $formVars['id'] . ";\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
