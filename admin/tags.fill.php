<?php
# Script: tags.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "tags.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_tags");

      $q_string  = "select tag_companyid,tag_name,tag_owner,tag_group ";
      $q_string .= "from inv_tags ";
      $q_string .= "where tag_id = " . $formVars['id'] . " and tag_type = 1 ";
      $q_inv_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_tags = mysqli_fetch_array($q_inv_tags);
      mysqli_free_result($q_inv_tags);

      $tag_owner       = return_Index($db, $a_inv_tags['tag_owner'],     "select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first");
      $q_string  = "select inv_id ";
      $q_string .= "from inv_inventory ";
      $q_string .= "where inv_status = 0 ";
      if ($_SESSION['p_group'] > 0) {
        $q_string .= "and inv_manager = " . $_SESSION['p_group'] . " ";
      }
      $q_string .= "order by inv_name ";
      $tag_companyid   = return_Index($db, $a_inv_tags['tag_companyid'], $q_string);
      $tag_group       = return_Index($db, $a_inv_tags['tag_group'],     "select grp_id from inv_groups where grp_disabled = 0 order by grp_name");

      print "document.formUpdate.tag_owner['"     . $tag_owner     . "'].selected = true;\n";
      print "document.formUpdate.tag_companyid['" . $tag_companyid . "'].selected = true;\n";
      print "document.formUpdate.tag_group['"     . $tag_group     . "'].selected = true;\n";

      print "document.formUpdate.tag_name.value = '"  . mysqli_real_escape_string($db, $a_inv_tags['tag_name'])  . "';\n";

      print "document.formUpdate.tag_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
