<?php
# Script: image.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "image.fill.php";
    $formVars['id']    = clean($_GET['id'],    10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_image");

      $q_string  = "select img_id,img_name,img_hypervisor ";
      $q_string .= "from inv_image ";
      $q_string .= "where img_id = " . $formVars['id'];
      $q_inv_image = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inv_image = mysqli_fetch_array($q_inv_image);
      mysqli_free_result($q_inv_image);

      $imghypervisor = return_Index($db, $a_inv_image['img_hypervisor'], "select hv_id from inv_hypervisor order by hv_name") + 1;
      print "document.formUpdate.img_name.value = '" . mysqli_real_escape_string($db, $a_inv_image['img_name'])  . "';\n";

      print "document.formUpdate.img_hypervisor['"  . $imghypervisor  . "'].selected = true;\n";

      print "document.formUpdate.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
