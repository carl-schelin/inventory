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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_certs");

      $q_string  = "select cert_id,cert_desc,cert_url,cert_expire,cert_authority,";
      $q_string .= "cert_filename,cert_subject,cert_group,cert_ca,cert_memo,cert_isca,cert_top ";
      $q_string .= "from inv_certs ";
      $q_string .= "where cert_id = " . $formVars['id'];
      $q_inv_certs = mysqli_query($db, $q_string) or die($q_string. ": " . mysqli_error($db));
      $a_inv_certs = mysqli_fetch_array($q_inv_certs);
      mysqli_free_result($q_inv_certs);

      $group = return_Index($db, $a_inv_certs['cert_group'], "select grp_id from inv_groups where grp_disabled = 0 order by grp_name");
      $cert  = return_Index($db, $a_inv_certs['cert_ca'],    "select cert_id from inv_certs where cert_isca = 1 order by cert_desc") + 1;

      if ($a_inv_certs['cert_ca'] == 0) {
        $cert = 0;
      }

      print "document.formUpdate.cert_desc.value = '"      . mysqli_real_escape_string($db, $a_inv_certs['cert_desc'])      . "';\n";
      print "document.formUpdate.cert_url.value = '"       . mysqli_real_escape_string($db, $a_inv_certs['cert_url'])       . "';\n";
      print "document.formUpdate.cert_filename.value = '"  . mysqli_real_escape_string($db, $a_inv_certs['cert_filename'])  . "';\n";
      print "document.formUpdate.cert_expire.value = '"    . mysqli_real_escape_string($db, $a_inv_certs['cert_expire'])    . "';\n";
      print "document.formUpdate.cert_authority.value = '" . mysqli_real_escape_string($db, $a_inv_certs['cert_authority']) . "';\n";
      print "document.formUpdate.cert_subject.value = '"   . mysqli_real_escape_string($db, $a_inv_certs['cert_subject'])   . "';\n";
      print "document.formUpdate.cert_memo.value = '"      . mysqli_real_escape_string($db, $a_inv_certs['cert_memo'])      . "';\n";

      if ($a_inv_certs['cert_isca']) {
        print "document.formUpdate.cert_isca.checked = true;\n";
      } else {
        print "document.formUpdate.cert_isca.checked = false;\n";
      }
      if ($a_inv_certs['cert_top']) {
        print "document.formUpdate.cert_top.checked = true;\n";
      } else {
        print "document.formUpdate.cert_top.checked = false;\n";
      }

# if your group matches the cert group for the item or if you're in webapps (group 25) or if the user is an admin
      if (check_grouplevel($db, $GRP_WebApps)) {
        print "document.formUpdate.cert_group[" . $group . "].selected = true;\n";
      } else {
        print "document.formUpdate.cert_group.value = " . $group . ";\n";
      }

      print "document.formUpdate.cert_ca[" . $cert . "].selected = true;\n";

      print "document.formUpdate.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
