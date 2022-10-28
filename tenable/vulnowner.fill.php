<?php
# Script: vulnowner.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "vulnowner.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_vulnowner");

      $q_string  = "select int_id,int_addr,sec_id,sec_name ";
      $q_string .= "from inv_vulnowner ";
      $q_string .= "left join interface on interface.int_id = inv_vulnowner.vul_interface ";
      $q_string .= "left join security  on security.sec_id  = inv_vulnowner.vul_security ";
      $q_string .= "where vul_id = " . $formVars['id'];
      $q_inv_vulnowner = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_vulnowner = mysqli_fetch_array($q_inv_vulnowner);
      mysqli_free_result($q_inv_vulnowner);

      print "document.getElementById('vuln_interface').innerHTML = '"  . mysqli_real_escape_string($db, $a_inv_vulnowner['int_addr']) . "';\n";
      print "document.getElementById('vuln_securityid').innerHTML = '" . mysqli_real_escape_string($db, $a_inv_vulnowner['sec_name']) . "';\n";

      print "document.owner.int_id.value = " . $a_inv_vulnowner['int_id'] . ";\n";
      print "document.owner.sec_id.value = " . $a_inv_vulnowner['sec_id'] . ";\n";

# get vulowner and ticket from interface id and security id in vulowner table
      $q_string  = "select grp_name,vul_group,vul_ticket,vul_exception,vul_description ";
      $q_string .= "from inv_vulnowner ";
      $q_string .= "left join inv_groups on inv_groups.grp_id = inv_vulnowner.vul_group ";
      $q_string .= "where vul_security = " . $a_inv_vulnowner['sec_id'] . " and vul_interface = " . $a_inv_vulnowner['int_id'] . " ";
      $q_inv_vulnowner = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_vulnowner) > 0) {
        $a_inv_vulnowner = mysqli_fetch_array($q_inv_vulnowner);

        print "document.owner.vul_ticket.value = '"      . mysqli_real_escape_string($db, $a_inv_vulnowner['vul_ticket'])      . "';\n";
        print "document.owner.vul_description.value = '" . mysqli_real_escape_string($db, $a_inv_vulnowner['vul_description']) . "';\n";

        $group = return_Index($db, $a_inv_vulnowner['vul_group'], "select grp_id from inv_groups where grp_disabled = 0 order by grp_name");

        print "document.owner.vul_group['" . $group . "'].selected = true;\n";

        if ($a_inv_vulnowner['vul_exception']) {
          print "document.owner.vul_exception.checked = true;\n";
        } else {
          print "document.owner.vul_exception.checked = false;\n";
        }

        print "document.owner.update.disabled = false;\n";
      } else {
#        print "document.owner.vul_ticket.value = '';\n";
#        print "document.owner.vul_description.value = '';\n";
#        print "document.owner.vul_group['0'].selected = true;\n";
#        print "document.owner.vul_exception.checked = false;\n";
# if no entry in the table, don't let someone 'update' an empty record.

        print "document.owner.update.disabled = false;\n";
      }

      print "document.owner.id.value = "     . $formVars['id']     . ";\n";

      print "document.owner.vul_group.focus();\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
