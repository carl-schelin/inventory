<?php
# Script: source_node.fill.php
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
    $package = "source_node.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from source_node");

      $q_string  = "select src_node,src_deleted ";
      $q_string .= "from source_node ";
      $q_string .= "where src_id = " . $formVars['id'];
      $q_source_node = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_source_node = mysqli_fetch_array($q_source_node);
      mysqli_free_result($q_source_node);

      print "document.nodes.src_node.value = '"       . mysqli_real_escape_string($a_source_node['src_node'])       . "';\n";

      if ($a_source_node['src_deleted']) {
        print "document.nodes.src_deleted.checked = true;\n";
      } else {
        print "document.nodes.src_deleted.checked = false;\n";
      }

      print "document.nodes.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
