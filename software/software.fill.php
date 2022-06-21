<?php
# Script: software.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "software.fill.php";
    $formVars['id']    = clean($_GET['id'],    10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from software");

      $q_string  = "select sw_software,sw_vendor,sw_product,sw_licenseid,sw_supportid,sw_type,sw_eol,sw_eos,sw_department ";
      $q_string .= "from software ";
      $q_string .= "where sw_id = " . $formVars['id'];
      $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_software = mysqli_fetch_array($q_software);
      mysqli_free_result($q_software);

      $sw_vendor     = return_Index($db, $a_software['sw_vendor'],     "select ven_id  from vendors    order by ven_name");
      $sw_product    = return_Index($db, $a_software['sw_product'],    "select prod_id from products   order by prod_name");
      $sw_licenseid  = return_Index($db, $a_software['sw_licenseid'],  "select lic_id  from licenses   order by lic_product");
      $sw_supportid  = return_Index($db, $a_software['sw_supportid'],  "select sup_id  from support    order by sup_company");
      $sw_type       = return_Index($db, $a_software['sw_type'],       "select typ_id  from sw_types   order by typ_name");
      $sw_department = return_Index($db, $a_software['sw_department'], "select dep_id  from department order by dep_name");

      print "document.formUpdate.sw_software.value = '"   . mysqli_real_escape_string($db, $a_software['sw_software'])   . "';\n";
      print "document.formUpdate.sw_eol.value = '"        . mysqli_real_escape_string($db, $a_software['sw_eol'])        . "';\n";
      print "document.formUpdate.sw_eos.value = '"        . mysqli_real_escape_string($db, $a_software['sw_eos'])        . "';\n";

      if ($sw_vendor > 0) {
        print "document.formUpdate.sw_vendor['"     . $sw_vendor     . "'].selected = true;\n";
      }
      if ($sw_product > 0) {
        print "document.formUpdate.sw_product['"    . $sw_product    . "'].selected = true;\n";
      }
      if ($sw_licenseid > 0) {
        print "document.formUpdate.sw_licenseid['"  . $sw_licenseid  . "'].selected = true;\n";
      }
      if ($sw_supportid > 0) {
        print "document.formUpdate.sw_supportid['"  . $sw_supportid  . "'].selected = true;\n";
      }
      if ($sw_type > 0) {
        print "document.formUpdate.sw_type['"       . $sw_type       . "'].selected = true;\n";
      }
      if ($sw_department > 0) {
        print "document.formUpdate.sw_department['" . $sw_department . "'].selected = true;\n";
      }

      $sw_tags = '';
      $space = '';
      $q_string  = "select tag_name ";
      $q_string .= "from tags ";
      $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_type = 4 ";
      $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_tags) > 0) {
        while ($a_tags = mysqli_fetch_array($q_tags)) {
          $sw_tags .= $space . $a_tags['tag_name'];
          $space = " ";
        }
      }
      print "document.formUpdate.sw_tags.value = '" . mysqli_real_escape_string($db, $sw_tags) . "';\n";

      print "document.formUpdate.id.value = '" . $formVars['id'] . "'\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
