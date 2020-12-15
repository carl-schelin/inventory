<?php
# Script: keywords.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from keywords");

      $q_string  = "select key_description,key_page,key_email,key_annotate,key_critical_annotate,key_deleted ";
      $q_string .= "from keywords ";
      $q_string .= "where key_id = " . $formVars['id'];
      $q_keywords = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_keywords = mysqli_fetch_array($q_keywords);
      mysqli_free_result($q_keywords);

      print "document.keywords.key_description.value = '"       . mysqli_real_escape_string($a_keywords['key_description'])       . "';\n";
      print "document.keywords.key_page.value = '"              . mysqli_real_escape_string($a_keywords['key_page'])              . "';\n";
      print "document.keywords.key_email.value = '"             . mysqli_real_escape_string($a_keywords['key_email'])             . "';\n";
      print "document.keywords.key_annotate.value = '"          . mysqli_real_escape_string($a_keywords['key_annotate'])          . "';\n";
      print "document.keywords.key_critical_annotate.value = '" . mysqli_real_escape_string($a_keywords['key_critical_annotate']) . "';\n";

      if ($a_keywords['key_deleted']) {
        print "document.keywords.key_deleted.checked = true;\n";
      } else {
        print "document.keywords.key_deleted.checked = false;\n";
      }

      print "document.keywords.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
