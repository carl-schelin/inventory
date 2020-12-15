<?php
# Script: rules.fill.php
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
    $package = "rules.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from rules");

      $q_string  = "select rule_description,rule_annotate,rule_group,rule_source,rule_application,rule_object,rule_message,rule_page,";
      $q_string .= "rule_email,rule_autoack,rule_deleted ";
      $q_string .= "from rules ";
      $q_string .= "where rule_id = " . $formVars['id'];
      $q_rules = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rules = mysqli_fetch_array($q_rules);
      mysqli_free_result($q_rules);

      $rule_parent      = return_Index($db, $a_rules['rule_parent'],      "select rule_id from rules         where rule_deleted = 0 order by rule_description ");
      $rule_group       = return_Index($db, $a_rules['rule_group'],       "select key_id  from keywords      where key_deleted = 0  order by key_description  ");
      $rule_source      = return_Index($db, $a_rules['rule_source'],      "select src_id  from source_node   where src_deleted = 0  order by src_node         ");
      $rule_application = return_Index($db, $a_rules['rule_application'], "select app_id  from application   where app_deleted = 0  order by app_description  ");
      $rule_object      = return_Index($db, $a_rules['rule_object'],      "select obj_id  from objects       where obj_deleted = 0  order by obj_name         ");
      $rule_message     = return_Index($db, $a_rules['rule_message'],     "select msg_id  from message_group where msg_deleted = 0  order by msg_group        ");

      print "document.rules.rule_description.value = '" . mysqli_real_escape_string($a_rules['rule_description']) . "';\n";
      print "document.rules.rule_annotate.value = '"    . mysqli_real_escape_string($a_rules['rule_annotate']) . "';\n";

      print "document.rules.rule_parent['"       . $rule_parent       . "'].selected = true;\n";
      print "document.rules.rule_group['"        . $rule_group        . "'].selected = true;\n";
      print "document.rules.rule_source['"       . $rule_source       . "'].selected = true;\n";
      print "document.rules.rule_application['"  . $rule_application  . "'].selected = true;\n";
      print "document.rules.rule_object['"       . $rule_object       . "'].selected = true;\n";
      print "document.rules.rule_message['"      . $rule_message      . "'].selected = true;\n";

      print "document.rules.rule_page['"  . $a_rules['rule_page']  . "'].checked = true;\n";
      print "document.rules.rule_email['" . $a_rules['rule_email'] . "'].checked = true;\n";

      if ($a_rules['rule_autoack']) {
        print "document.rules.rule_autoack.checked = true;\n";
      } else {
        print "document.rules.rule_autoack.checked = false;\n";
      }
      if ($a_rules['rule_deleted']) {
        print "document.rules.rule_deleted.checked = true;\n";
      } else {
        print "document.rules.rule_deleted.checked = false;\n";
      }

      print "document.rules.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
