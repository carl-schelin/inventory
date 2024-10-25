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
    $formVars['update']         = clean($_GET['update'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    $orderby = "order by img_name";

   if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],                10);
        $formVars['img_name']       = clean($_GET['img_name'],         255);
        $formVars['img_hypervisor'] = clean($_GET['img_hypervisor'],    10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['img_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "img_name           = \"" . $formVars['img_name']        . "\"," . 
            "img_hypervisor     =   " . $formVars['img_hypervisor'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_image set img_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_image set " . $q_string . " where img_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['img_name']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

##############
### Now build the displayed table information
##############

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "<th class=\"ui-state-default\" width=\"160\">Delete License</th>\n";
      }
      $output .= "<th class=\"ui-state-default\">Name</th>\n";
      $output .= "<th class=\"ui-state-default\">Hypervisor</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select img_id,img_name,hv_name ";
      $q_string .= "from inv_image ";
      $q_string .= "left join inv_hypervisor on inv_hypervisor.hv_id = inv_image.img_hypervisor ";
      $q_string .= $orderby;
      $q_inv_image = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_image) > 0) {
        while ($a_inv_image = mysqli_fetch_array($q_inv_image)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('image.fill.php?id="  . $a_inv_image['img_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('image.del.php?id=" . $a_inv_image['img_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "<td class=\"ui-widget-content delete\">" . $linkdel . "</td>\n";
          }
          $output .= "<td class=\"ui-widget-content\">" . $linkstart . $a_inv_image['img_name'] . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"              . $a_inv_image['hv_name']  . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\" colspan=\"3\">No server images to display.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_image);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
