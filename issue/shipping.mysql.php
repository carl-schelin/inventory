<?php
# Script: shipping.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "shipping.mysql.php";
    $formVars['update']        = clean($_GET['update'],       10);
    $formVars['hw_server']     = clean($_GET['hw_server'],    10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']        = clean($_GET['id'],           10);
        $formVars['hw_id']     = clean($_GET['hw_radio'],     10);
        $formVars['hw_rma']    = clean($_GET['hw_rma'],       50);
        $formVars['hw_note']   = clean($_GET['hw_note'],     255);
        $formVars['det_user']  = clean($_SESSION['uid'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['hw_rma']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "det_issue =   " . $formVars['id'] . "," . 
            "det_text  = \"" . "Hardware failure identified. Shipping & Receiving notified." . "\"," . 
            "det_user  =   " . $formVars['det_user'];

          if ($formVars['update'] == 0) {
            $query = "insert into issue_detail set det_id = null," . $q_string;
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['hw_rma']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          $q_string =
            "hw_rma      = \"" . $formVars['hw_rma'] . "\"";

          $query = "update hardware set " . $q_string . " where hw_id = " . $formVars['hw_id'];

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

##########
# Notify Shipping and Receiving of an incoming package
##########

// get the e-mail address of shipping and receiving.
          $q_string  = "select grp_email ";
          $q_string .= "from groups ";
          $q_string .= "where grp_id = " . $GRP_Shipping . " ";
          $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_groups = mysqli_fetch_array($q_groups);

// get the e-mail address of the user
          $q_string  = "select usr_first,usr_last,usr_email ";
          $q_string .= "from users ";
          $q_string .= "where usr_id = " . $_SESSION['uid'];
          $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_users = mysqli_fetch_array($q_users);

// set the subject line
          $subject = "Notice: Hardware replacement in progress.";

// set the headers
          $headers  = "From: Inventory <root@" . $Sitehttp . ">\r\n";
          $headers .= "CC: " . $a_users['usr_email'] . "," . $Siteadmins . "\r\n";
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

// build the message
          $body  = "<p>" . $a_users['usr_first'] . " " . $a_users['usr_last'] . " is notifying your group that the hardware ";
          $body .= "identified below has failed and a replacement is being shipped to Intrado.</p>\n";


          $q_string  = "select inv_name,loc_name,ct_city,st_acronym,hw_asset,hw_serial ";
          $q_string .= "from inventory ";
          $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
          $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
          $q_string .= "left join states on states.st_id = locations.loc_state ";
          $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
          $q_string .= "where inv_id = " . $formVars['hw_server'] . " and hw_primary = 1 and hw_deleted = 0 ";
          $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_inventory = mysqli_fetch_array($q_inventory);

          $body .= "<ul>\n";
          $body .= "  <li>Affected System\n";
          $body .= "  <ul>\n";
          $body .= "    <li>Name: "                    . $a_inventory['inv_name']   . "</li>\n";
          $body .= "    <li>Location: "                . $a_inventory['loc_name']   . " (" . $a_inventory['ct_city'] . " " . $a_inventory['st_acronym'] . ")</li>\n";
          $body .= "    <li>Asset Tag Number: "        . $a_inventory['hw_asset']   . "</li>\n";
          $body .= "    <li>Serial Number: "           . $a_inventory['hw_serial']  . "</li>\n";
          $body .= "  </ul></li>\n";


          $q_string  = "select part_name,hw_serial,hw_asset,mod_vendor,mod_name,hw_rma ";
          $q_string .= "from hardware ";
          $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
          $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
          $q_string .= "where hw_id = " . $formVars['hw_id'];
          $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_hardware = mysqli_fetch_array($q_hardware);

          $body .= "  <li>Failed Hardware\n";
          $body .= "  <ul>\n";
          $body .= "    <li>Vendor: "                  . $a_hardware['mod_vendor'] . "</li>\n";
          $body .= "    <li>Model: "                   . $a_hardware['mod_name']   . "</li>\n";
          $body .= "    <li>Type: "                    . $a_hardware['part_name']  . "</li>\n";
          $body .= "    <li>RMA: "                     . $a_hardware['hw_rma']     . "</li>\n";
          $body .= "    <li>Asset Tag Number: "        . $a_hardware['hw_asset']   . "</li>\n";
          $body .= "    <li>Serial Number: "           . $a_hardware['hw_serial']  . "</li>\n";
          $body .= "  </ul></li>\n";
          $body .= "</ul>\n";

          $body .= "<p>Additional Note: " . $formVars['hw_note'] . "</p>\n";

          $body .= "<p>This message is from the <a hef=\"" . $Siteroot . "\">Inventory</a> application.\n";
          $body .= "<br>This mail box is not monitored, replies will be ignored.</p>\n\n";

          if ($Siteenv == 'PROD') {
            $mailto = $a_groups['grp_email'];
          } else {
            if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
              $mailto = $Sitedev . "," . $_SESSION['email'];
            } else {
              $mailto = $Sitedev;
            }
          }
          mail($mailto, $subject, $body, $headers);
          print "alert('E-mail notification sent to Shipping & Receiving');";

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>";
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Hardware Listing</th>";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('hardware-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"hardware-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Hardware Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>RMA</strong> - Select the appropriate radio button to identify the hardware being replaced. This is combined with the RMA number to create the email notification to Shipping and Receiving. Text here indicates the item is in the process of being replaced. Edit the hardware for the server to remove it from the server.</li>\n";
      $output .= "    <li><strong>Highlighted</strong> - This hardware item is the <span class=\"ui-state-highlight\">Primary Container</span> and typically contains all the other listed equipment.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Hardware Management</strong> title bar to toggle the <strong>Hardware Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">RMA</th>";
      $output .= "  <th class=\"ui-state-default\">Asset</th>";
      $output .= "  <th class=\"ui-state-default\">Serial</th>";
      $output .= "  <th class=\"ui-state-default\">Model</th>";
      $output .= "  <th class=\"ui-state-default\">Speed</th>";
      $output .= "  <th class=\"ui-state-default\">Size</th>";
      $output .= "  <th class=\"ui-state-default\">Type</th>";
      $output .= "</tr>";

      $checked = "checked ";
      $count = 0;
      $q_string  = "select hw_id,part_name,hw_serial,hw_asset,mod_name,mod_speed,mod_size,hw_speed,hw_size,hw_primary,hw_rma ";
      $q_string .= "from hardware ";
      $q_string .= "left join parts  on parts.part_id = hardware.hw_type ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "where hw_deleted = 0 and hw_companyid = " . $formVars['hw_server'] . " ";
      $q_string .= "order by hw_type,hw_vendorid";
      $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_hardware = mysqli_fetch_array($q_hardware)) {

        if ($a_hardware['hw_primary'] == 1) {
          $class = "ui-state-highlight";
        } else {
          $class = "ui-widget-content";
        }

        if (strlen($a_hardware['hw_speed']) > 0) {
          $a_hardware['mod_speed'] = $a_hardware['hw_speed'];
        }
        if (strlen($a_hardware['hw_size']) > 0) {
          $a_hardware['mod_size'] = $a_hardware['hw_size'];
        }

        $output .= "<tr>";
        if ($a_hardware['hw_rma'] == '') {
          $output .= "  <td class=\"" . $class . " delete\"><input type=\"radio\" " . $checked . "name=\"hw_radio\" value=\"" . $a_hardware['hw_id'] . "\"></td>";
# only count if an rma hasn't been allocated;
          $count++;
          $checked = '';
        } else {
          $output .= "  <td class=\"" . $class . " delete\">" . $a_hardware['hw_rma'] . "</td>";
        }
        $output .= "  <td class=\"" . $class . "\">" . $a_hardware['hw_asset']   . "</td>";
        $output .= "  <td class=\"" . $class . "\">" . $a_hardware['hw_serial']  . "</td>";
        $output .= "  <td class=\"" . $class . "\">" . $a_hardware['mod_name']   . "</td>";
        $output .= "  <td class=\"" . $class . "\">" . $a_hardware['mod_speed']  . "</td>";
        $output .= "  <td class=\"" . $class . "\">" . $a_hardware['mod_size']   . "</td>";
        $output .= "  <td class=\"" . $class . "\">" . $a_hardware['part_name']  . "</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      print "document.getElementById('mysql_hardware').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

      print "document.start.hw_count.value = '" . $count . "';\n";
      print "document.start.hw_rma.value = '';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
