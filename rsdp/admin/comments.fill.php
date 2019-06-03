<?php
# Script: comments.fill.php
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
    $package = "comments.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rsdp_comments");

      $q_string  = "select com_text ";
      $q_string .= "from rsdp_comments ";
      $q_string .= "where com_id = " . $formVars['id'];
      $q_rsdp_comments = mysql_query($q_string) or die (mysql_error());
      $a_rsdp_comments = mysql_fetch_array($q_rsdp_comments);
      mysql_free_result($q_rsdp_comments);

      $updated = str_replace("<br />", "\n", $a_rsdp_comments['com_text']);
      print "document.comments.com_text.value = '" . mysql_real_escape_string($updated) . "';\n";

      print "document.comments.com_id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
