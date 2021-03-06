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

      $users = return_Index($db, $a_images['img_owner'], "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.images.img_title.value = '" . mysqli_real_escape_string($db, $a_images['img_title'])    . "';\n";
      print "document.images.img_file.value = '"  . mysqli_real_escape_string($db, $a_images['img_file'])     . "';\n";
      print "document.images.img_date.value = '"  . mysqli_real_escape_string($db, $a_images['img_date'])     . "';\n";

      if ($users > 0) {
        print "document.images.img_owner['"  . $users                  . "'].selected = true;\n";
      }
      if ($a_images['img_facing'] > 0) {
        print "document.images.img_facing['" . $a_images['img_facing'] . "'].checked  = true;\n";
      }

      print "document.images.id.value = " . $formVars['id'] . ";\n";

      print "document.images.update.disabled = false;\n";

#      $output .= "<div id="main">\n"
#      $output .= "<center><a href=\"" . $Pictureroot . "/" . $a_images['img_file'] . "\"><img src=\"" . $Pictureroot . "/" . $a_images['img_file'] . "\" width=\"800\"></a></center>\n";
#      $output .= "</div>\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
