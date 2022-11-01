<?php
# Script: image.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "image.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from images");

      $q_string  = "select img_title,img_file,img_facing,img_owner,img_date ";
      $q_string .= "from images ";
      $q_string .= "where img_id = " . $formVars['id'];
      $q_images = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_images = mysqli_fetch_array($q_images);
      mysqli_free_result($q_images);

      $users = return_Index($db, $a_images['img_owner'], "select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.formUpdate.img_title.value = '" . mysqli_real_escape_string($db, $a_images['img_title'])    . "';\n";
      print "document.formUpdate.img_file.value = '"  . mysqli_real_escape_string($db, $a_images['img_file'])     . "';\n";
      print "document.formUpdate.img_date.value = '"  . mysqli_real_escape_string($db, $a_images['img_date'])     . "';\n";

      if ($users > 0) {
        print "document.formUpdate.img_owner['"  . $users                  . "'].selected = true;\n";
      }
      if ($a_image['img_facing']) {
        print "document.formUpdate.img_facing['" . $a_images['img_facing'] . "'].checked  = true;\n";
      } else {
        print "document.formUpdate.img_facing['" . $a_images['img_facing'] . "'].checked  = true;\n";
      }

      print "var cell = document.getElementById('image_name');\n";
      if ($a_images['img_file'] == '') {
        print "cell.innerHTML = 'No image found';\n";
      } else {
        if ( file_exists($Picturepath . "/" . $a_images['img_file'])) {
          print "cell.innerHTML = '<img src=\"" . $Pictureroot . "/" . $a_images['img_file'] . "\" width=\"500\">';\n";
        } else {
          print "cell.innerHTML = 'Invalid Image Name';\n";
        }
      }

      print "document.formUpdate.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
