<?php
# Script: keywords.fill.php
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
    $package = "keywords.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from keywords");

      $q_string  = "select key_description,key_page,key_email,key_annotate,key_critical_annotate,key_deleted ";
      $q_string .= "from keywords ";
      $q_string .= "where key_id = " . $formVars['id'];
      $q_keywords = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_keywords = mysql_fetch_array($q_keywords);
      mysql_free_result($q_keywords);

      print "document.keywords.key_description.value = '"       . mysql_real_escape_string($a_keywords['key_description'])       . "';\n";
      print "document.keywords.key_page.value = '"              . mysql_real_escape_string($a_keywords['key_page'])              . "';\n";
      print "document.keywords.key_email.value = '"             . mysql_real_escape_string($a_keywords['key_email'])             . "';\n";
      print "document.keywords.key_annotate.value = '"          . mysql_real_escape_string($a_keywords['key_annotate'])          . "';\n";
      print "document.keywords.key_critical_annotate.value = '" . mysql_real_escape_string($a_keywords['key_critical_annotate']) . "';\n";

      if ($a_keywords['key_deleted']) {
        print "document.keywords.key_deleted.checked = true;\n";
      } else {
        print "document.keywords.key_deleted.checked = false;\n";
      }

      print "document.keywords.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
