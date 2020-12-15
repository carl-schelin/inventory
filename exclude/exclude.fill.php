<?php
# Script: exclude.fill.php
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
    $package = "exclude.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from excludes");

      $q_string  = "select ex_id,ex_companyid,ex_text,ex_comments,ex_expiration,ex_deleted ";
      $q_string .= "from excludes ";
      $q_string .= "where ex_id = " . $formVars['id'];
      $q_excludes = mysqli_query($db, $q_string) or die($q_string . " " . mysqli_error($db));
      $a_excludes = mysqli_fetch_array($q_excludes);
      mysqli_free_result($q_excludes);

      $server = return_Index($db, $a_excludes['ex_companyid'], "select inv_id from inventory where inv_ssh = 1 and inv_status = 0 and inv_manager = " . $GRP_Unix . " order by inv_name");

      print "document.exclude.ex_text.value = '"       . mysqli_real_escape_string($a_excludes['ex_text'])       . "';\n";
      print "document.exclude.ex_comments.value = '"   . mysqli_real_escape_string($a_excludes['ex_comments'])   . "';\n";
      print "document.exclude.ex_expiration.value = '" . mysqli_real_escape_string($a_excludes['ex_expiration']) . "';\n";

      print "document.exclude.ex_companyid['" . $server . "'].selected = true;\n";

      if ($a_excludes['ex_expiration'] == '2038-01-01') {
        print "document.exclude.noexpire.checked = true;\n";
      } else {
        print "document.exclude.noexpire.checked = false;\n";
      }
      if ($a_excludes['ex_deleted'] > 0) {
        print "document.exclude.ex_deleted.checked = true;\n";
      } else {
        print "document.exclude.ex_deleted.checked = false;\n";
      }

      print "document.exclude.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
