<?php
# Script: certs.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "certs.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from certs");

      $q_string  = "select cert_id,cert_desc,cert_url,cert_expire,cert_authority,cert_group,cert_ca,cert_memo,cert_isca ";
      $q_string .= "from certs ";
      $q_string .= "where cert_id = " . $formVars['id'];
      $q_certs = mysqli_query($db, $q_string) or die($q_string. ": " . mysqli_error($db));
      $a_certs = mysqli_fetch_array($q_certs);
      mysqli_free_result($q_certs);

      $group = return_Index($db, $a_certs['cert_group'], "select grp_id from a_groups where grp_disabled = 0 order by grp_name");
      $cert  = return_Index($db, $a_certs['cert_ca'],    "select cert_id from certs where cert_isca = 1 order by cert_desc");

      print "document.dialog.cert_desc.value = '"      . mysqli_real_escape_string($db, $a_certs['cert_desc'])      . "';\n";
      print "document.dialog.cert_url.value = '"       . mysqli_real_escape_string($db, $a_certs['cert_url'])       . "';\n";
      print "document.dialog.cert_expire.value = '"    . mysqli_real_escape_string($db, $a_certs['cert_expire'])    . "';\n";
      print "document.dialog.cert_authority.value = '" . mysqli_real_escape_string($db, $a_certs['cert_authority']) . "';\n";
      print "document.dialog.cert_memo.value = '"      . mysqli_real_escape_string($db, $a_certs['cert_memo'])      . "';\n";

      if ($a_certs['cert_isca']) {
        print "document.dialog.cert_isca.checked = true;\n";
      } else {
        print "document.dialog.cert_isca.checked = false;\n";
      }

# if your group matches the cert group for the item or if you're in webapps (group 25) or if the user is an admin
      if (check_grouplevel($db, $GRP_WebApps)) {
        print "document.dialog.cert_group[" . ($group - 1) . "].selected = true;\n";
      } else {
        print "document.dialog.cert_group.value = " . ($group - 1) . ";\n";
      }

      print "document.dialog.cert_ca[" . $cert . "].selected = true;\n";

      print "document.dialog.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
