<?php
# Script: image.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "image.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from inv_images");

# delete the image from the system
      $q_string  = "select img_file,img_facing ";
      $q_string .= "from inv_images ";
      $q_string .= "where img_id = " . $formVars['id'];
      $q_inv_images = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inv_images = mysqli_fetch_array($q_inv_images);

      $q_string  = "select inv_id ";
      $q_string .= "from inv_inventory ";
      $q_string .= "where inv_front = " . $formVars['id'] . " or inv_rear = " . $formVars['id'] . " ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_inventory) == 1) {
        if (file_exists($Picturepath . "/" . $a_inv_images['img_file'])) {
          unlink($Picturepath . "/" . $a_inv_images['img_file']);
        }
      }

# now delete it from the database
      $q_string  = "delete ";
      $q_string .= "from inv_images ";
      $q_string .= "where img_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# find the first front facing one with no file.
# then find the first  rear facing one with no file.
# these are the blank or 'unattached' images.
# if none found, set value to 0.
# then we loop through the inventory looking for servers that have 
# the deleted id and replace it with the appropriate unassigned value.
# An 'unassigned' entry has no img_file value; blank.
# don't need to query the database twice;

      if ($a_inv_images['img_facing']) {

        $q_string  = "select img_id ";
        $q_string .= "from inv_images ";
        $q_string .= "where img_facing = 1 and img_file = '' ";
        $q_front = mysqli_query($db, $q_string) or die($q_string . ": " . $mysqli_error($db));
        if (mysqli_num_rows($q_front) > 0) {
          $a_front = mysqli_fetch_array($q_front);
        } else {
          $a_front['img_id'] = 0;
        }

        $q_string  = "select inv_id ";
        $q_string .= "from inv_inventory ";
        $q_string .= "where inv_front = " . $a_front['img_id'] . " ";
        $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . $mysqli_error($db));
        if (mysqli_num_rows($q_inv_inventory) > 0) {
          while ($a_inv_inventory = mysql_fetch_array($q_inv_inventory)) {
            $q_string  = "update ";
            $q_string .= "inv_inventory ";
            $q_string .= "set inv_front = " . $a_front['img_id'] . " ";
            $q_string .= "where inv_id = " . $a_inv_inventory['inv_id'] . " ";
            $q_result = mysqli_query($db, $q_string) or die($q_string . ": " . $mysqli_error($db));
          }
        }

      } else {

        $q_string  = "select img_id ";
        $q_string .= "from inv_images ";
        $q_string .= "where img_facing = 0 and img_file = '' ";
        $q_rear = mysqli_query($db, $q_string) or die($q_string . ": " . $mysqli_error($db));
        if (mysqli_num_rows($q_rear) > 0) {
          $a_rear = mysqli_fetch_array($q_rear);
        } else {
          $a_rear['img_id'] = 0;
        }

        $q_string  = "select inv_id ";
        $q_string .= "from inv_inventory ";
        $q_string .= "where inv_rear = " . $a_rear['img_id'] . " ";
        $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . $mysqli_error($db));
        if (mysqli_num_rows($q_inv_inventory) > 0) {
          while ($a_inv_inventory = mysql_fetch_array($q_inv_inventory)) {
            $q_string  = "update ";
            $q_string .= "inv_inventory ";
            $q_string .= "set inv_rear = " . $a_rear['img_id'] . " ";
            $q_string .= "where inv_id = " . $a_inv_inventory['inv_id'] . " ";
            $q_result = mysqli_query($db, $q_string) or die($q_string . ": " . $mysqli_error($db));
          }
        }
      }

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>
