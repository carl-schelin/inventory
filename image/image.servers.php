<?php
# Script: image.servers.php
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

# get a list of all servers that have this image associated with it.

      $output = '';
      if ($a_images['img_facing']) {
        $q_string  = "select inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_front = " . $formVars['id'] . " ";
        $q_string .= "order by inv_name ";
        $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inventory) > 0) {
          $comma = '';
          while ($a_inventory = mysqli_fetch_array($q_inventory)) {
            $output .= $comma . $a_inventory['inv_name'];
            $comma = ' ';
          }
        } else {
          $output = "No servers are associated with this image.";
        }
      } else {
        $q_string  = "select inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_rear = " . $formVars['id'] . " ";
        $q_string .= "order by inv_name ";
        $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inventory) > 0) {
          $comma = '';
          while ($a_inventory = mysqli_fetch_array($q_inventory)) {
            $output .= $comma . $a_inventory['inv_name'];
            $comma = ' ';
          }
        } else {
          $output = "No servers are associated with this image.";
        }
      }

      print "document.getElementById('image_memo').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
