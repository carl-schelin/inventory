<?php
# Script: image.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "image.mysql.php";
    $formVars['id']           = clean($_GET['id'],            10);
    $formVars['update']       = clean($_GET['update'],        10);

    if ($formVars['id'] == '') {
      $formVars['id'] = 0;
    }
    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['img_title']    = clean($_GET['img_title'],    255);
        $formVars['img_file']     = clean($_GET['img_file'],     255);
        $formVars['img_facing']   = clean($_GET['img_facing'],    10);
        $formVars['img_date']     = clean($_GET['img_date'],      15);
        $formVars['img_owner']    = clean($_GET['img_owner'],     10);

        if (strlen($formVars['img_file']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "img_title  = \"" . $formVars['img_title']  . "\"," .
            "img_file   = \"" . $formVars['img_file']   . "\"," .
            "img_owner  =   " . $formVars['img_owner']  . "," .
            "img_facing =   " . $formVars['img_facing'] . "," .
            "img_date   = \"" . $formVars['img_date']   . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_images set img_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_images set " . $q_string . " where img_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['img_file']);

          $q_image = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Image</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Facing</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Members</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Date</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Owner</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select img_id,img_title,img_file,img_owner,usr_first,usr_last,img_facing,img_date ";
      $q_string .= "from inv_images ";
      $q_string .= "left join inv_users on inv_users.usr_id = inv_images.img_owner ";
      $q_string .= "order by img_title,img_file";
      $q_inv_images = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_images) > 0) {
        while ($a_inv_images = mysqli_fetch_array($q_inv_images)) {

          $linkstart  = "<a href=\"#\" onclick=\"show_file('image.fill.php?id="  . $a_inv_images['img_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $totalstart = "<a href=\"#\" onclick=\"show_file('image.servers.php?id="  . $a_inv_images['img_id'] . "');jQuery('#dialogServers').dialog('open');return false;\">";
          $linkdel    = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('image.del.php?id=" . $a_inv_images['img_id'] . "');\">";
          $linkend    = "</a>";

          $class = "ui-state-highlight";
          if (file_exists($Picturepath . "/" . $a_inv_images['img_file'])) {
            $class = "ui-widget-content";
          }

          $title = $a_inv_images['img_title'];
          if ($a_inv_images['img_title'] == '') {
            $title = "Edit Description";
          }

          $total = 0;
          if ($a_inv_images['img_facing'] == 1) {
            $facing = "Front";
            $q_string  = "select inv_id ";
            $q_string .= "from inventory ";
            $q_string .= "where inv_front = " . $a_inv_images['img_id'] . " ";
            $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_inventory) > 0) {
              while ($a_inventory = mysqli_fetch_array($q_inventory)) {
                $total++;
              }
            }
          } else {
            $facing = "Rear";
            $q_string  = "select inv_id ";
            $q_string .= "from inventory ";
            $q_string .= "where inv_rear = " . $a_inv_images['img_id'] . " ";
            $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_inventory) > 0) {
              while ($a_inventory = mysqli_fetch_array($q_inventory)) {
                $total++;
              }
            }
          }

          $output .= "<tr>";
          if ($a_images['img_owner'] == $_SESSION['uid'] || check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
            }
          } else {
            $output .= "  <td class=\"" . $class . " delete\">" . '--'     . "</td>";
          }
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart  . $title                                    . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"                      . $a_inv_images['img_file']                                . "</td>\n";
          $output .= "  <td class=\"" . $class . " delete\">"               . $facing                                              . "</td>\n";
          $output .= "  <td class=\"" . $class . " delete\">" . $totalstart . $total                                    . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . " delete\">"               . $a_inv_images['img_date']                                . "</td>\n";
          $output .= "  <td class=\"" . $class . " delete\">"               . $a_inv_images['usr_first'] . " " . $a_inv_images['usr_last'] . "</td>\n";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_images);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
