<?php
# Script: image.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
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

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['img_title']    = clean($_GET['img_title'],    255);
        $formVars['img_file']     = clean($_GET['img_file'],     255);
        $formVars['img_facing']   = clean($_GET['img_facing'],    10);
        $formVars['img_date']     = clean($_GET['img_date'],      15);
        $formVars['img_owner']    = clean($_GET['img_owner'],     10);

        if (strlen($formVars['img_file']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "img_title  = \"" . $formVars['img_title']  . "\"," .
            "img_file   = \"" . $formVars['img_file']   . "\"," .
            "img_owner  =   " . $formVars['img_owner']  . "," .
            "img_facing =   " . $formVars['img_facing'] . "," .
            "img_date   = \"" . $formVars['img_date']   . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into images set img_id = NULL, " . $q_string;
            $message = "Image added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update images set " . $q_string . " where img_id = " . $formVars['id'];
            $message = "Image updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['img_file']);

          mysql_query($query) or die($query . ": " . mysql_error());

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Image Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('image-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"image-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Image Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an Image to edit it.</li>\n";
      $output .= "    <li><strong>Highlighted</strong> - A line that is <span class=\"ui-state-highlight\">highlighted</span> indicates an entry where the file is missing.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Image Management</strong> title bar to toggle the <strong>Image Form</strong>.</li>\n";
      $output .= "    <li>Click the <strong>File Management</strong> title bar to toggle the <strong>File Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Facing</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Date</th>\n";
      $output .= "  <th class=\"ui-state-default\">Image Owner</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select img_id,img_title,img_file,img_owner,usr_first,usr_last,img_facing,img_date ";
      $q_string .= "from images ";
      $q_string .= "left join users on users.usr_id = images.img_owner ";
      $q_string .= "order by img_title,img_file";
      $q_images = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_images) > 0) {
        while ($a_images = mysql_fetch_array($q_images)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('image.fill.php?id="  . $a_images['img_id'] . "');showDiv('image-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('image.del.php?id=" . $a_images['img_id'] . "');\">";
          $linkend   = "</a>";

          if ($a_images['img_facing'] == 1) {
            $facing = "Front";
          } else {
            $facing = "Rear";
          }

          $class = "ui-state-highlight";
          if (file_exists($Picturepath . "/" . $a_images['img_file'])) {
            $class = "ui-widget-content";
          }

          $output .= "<tr>";
          if ($a_images['img_owner'] == $_SESSION['uid'] || check_userlevel(1)) {
            $output .= "  <td class=\"" . $class . " delete\">" . $linkdel   . 'x'                          . $linkend . "</td>";
          } else {
            $output .= "  <td class=\"" . $class . " delete\">" .              '--'                                    . "</td>";
          }
          $output .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_images['img_id']                                  . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_images['img_title']                               . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_images['img_file']                                . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $facing                                              . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_images['img_date']                                . $linkend . "</td>\n";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_images['usr_first'] . " " . $a_images['usr_last'] . $linkend . "</td>\n";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysql_free_result($q_images);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      if ($formVars['id'] == 0) {
        print "document.images.update.disabled = true;\n";
      }

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
