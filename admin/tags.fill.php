<?php
# Script: tags.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from tags");

      $q_string  = "select tag_companyid,tag_name,tag_view,tag_owner,tag_group ";
      $q_string .= "from tags ";
      $q_string .= "where tag_id = " . $formVars['id'];
      $q_tags = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      $a_tags = mysql_fetch_array($q_tags);
      mysql_free_result($q_tags);

      $tag_owner       = return_Index($a_tags['tag_owner'],     "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");
      $q_string  = "select inv_id ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_status = 0 ";
      if ($_SESSION['p_group'] > 0) {
        $q_string .= "and inv_manager = " . $_SESSION['p_group'] . " ";
      }
      $q_string .= "order by inv_name ";
      $tag_companyid   = return_Index($a_tags['tag_companyid'], $q_string);
      $tag_group       = return_Index($a_tags['tag_group'],     "select grp_id from groups where grp_disabled = 0 order by grp_name");

      print "document.tags.tag_view['"      . $a_tags['tag_view'] . "'].selected = true;\n";
      print "document.tags.tag_owner['"     . $tag_owner     . "'].selected = true;\n";
      print "document.tags.tag_companyid['" . $tag_companyid . "'].selected = true;\n";
      print "document.tags.tag_group['"     . $tag_group     . "'].selected = true;\n";

      print "document.tags.tag_name.value = '"  . $a_tags['tag_name']  . "';\n";

      print "document.tags.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
