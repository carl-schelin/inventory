#!/usr/local/bin/php
<?php
# Script: support.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve the list of open bug and feature requests and email to interested parties.

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

# In debug mode, it prints out the email vs sending it.
  $debug = 'no';
  $debug = 'yes';

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";
  $color[2] = "yellow";
  $color[3] = "#fa8072";

  $output  = "<html>\n";
  $output .= "<body>\n";

  $output .= "<p>The following systems were not found in the Support Contract spreadsheet managed by Lynda Lilly.</p>";
  $output .= "<p><b>NOTE: This does not mean the system is not supported</b>. There could be missing (line is highlighted) or invalid ";
  $output .= "Serial or Service Tag numbers. If there is an error in the Serial or Service Tag numbers in the Inventory, updating or ";
  $output .= "adding it will remove it from the listing after the next data load. Errors in Lynda's spreadsheet must be reported ";
  $otuput .= "to Lynda.</p>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">Unsupported Hardware</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>System Name</th>\n";
  $output .= "  <th>Model</th>\n";
  $output .= "  <th>Location</th>\n";
  $output .= "  <th>Asset Tag</th>\n";
  $output .= "  <th>Serial #</th>\n";
  $output .= "  <th>Service Tag</th>\n";
  $output .= "</tr>\n";

# need to get systems that are physical but don't have the flag set

  $q_string  = "select inv_name,mod_name,ct_city,st_state,hw_asset,hw_serial,hw_service ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = locations.loc_state ";
  $q_string .= "where inv_status = 0 and mod_virtual = 0 and hw_primary = 1 and hw_supid_verified = 0 and inv_manager = " . $manager . " ";
  $q_string .= "order by inv_name ";
  $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_hardware) > 0) {
    while ($a_hardware = mysql_fetch_array($q_hardware)) {

      if ($a_hardware['hw_serial'] == '' && $a_hardware['hw_service'] == '') {
        $bgcolor = $color[0];
      } else {
        $bgcolor = $color[1];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_hardware['inv_name']                                  . "</td>\n";
      $output .= "  <td>" . $a_hardware['mod_name']                                  . "</td>\n";
      $output .= "  <td>" . $a_hardware['ct_city'] . ", " . $a_hardware['st_state']  . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_asset']                                  . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_serial']                                 . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_service']                                . "</td>\n";
      $output .= "</tr>\n";
    }
  }

  $output .= "</table>\n\n";

  $output .= "<p>The following systems have been identified as Retired but were still found in the Support Contract spreadsheet managed by ";
  $output .= "Lynda Lilly.</p>";
  $output .= "<p>Per Lynda, if the Contract End date is only a few months away, contracts will likely just let it run out. Plus if the Retired ";
  $output .= "date is fairly recent, Lynda may not have updated the spreadsheet yet.</p>";
  $output .= "<p>In addition, a system listed here could have been retired and then repurposed but without having the 'Reused' date set. ";
  $output .= "Systems where a live system can be found are highlighted and the new server name noted.</p>\n";

  $output .= "<table width=80%>\n";
  $output .= "<tr>\n";
  $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"10\">Supported But Retired</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
  $output .= "  <th>System Name</th>\n";
  $output .= "  <th>New Name</th>\n";
  $output .= "  <th>Retired</th>\n";
  $output .= "  <th>Model</th>\n";
  $output .= "  <th>Location</th>\n";
  $output .= "  <th>Asset Tag</th>\n";
  $output .= "  <th>Serial #</th>\n";
  $output .= "  <th>Service Tag</th>\n";
  $output .= "  <th>Contract Start</th>\n";
  $output .= "  <th>Contract End</th>\n";
  $output .= "</tr>\n";

# need to get systems that are physical but don't have the flag set
  $q_string  = "select inv_name,mod_name,ct_city,st_state,hw_asset,hw_serial,hw_service,hw_retired,hw_supportstart,hw_supportend ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = locations.loc_state ";
  $q_string .= "where inv_status = 1 and mod_virtual = 0 and hw_primary = 1 and hw_supid_verified = 1 and inv_manager = " . $manager . " ";
  $q_string .= "order by inv_name ";
  $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_hardware) > 0) {
    while ($a_hardware = mysql_fetch_array($q_hardware)) {

      $q_string  = "select inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
      $q_string .= "where ";
      $or = '(';
      if ($a_hardware['hw_serial'] != '') {
        $q_string .= "(hw_serial = '" . $a_hardware['hw_serial'] . "'";
        $or = ' or ';
      }
      if ($a_hardware['hw_service'] != '') {
        $q_string .= $or . "hw_service = '" . $a_hardware['hw_service'] . "'";
      }
      $q_string .= ") and inv_status = 0 ";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_inventory) > 0) {
        $a_inventory = mysql_fetch_array($q_inventory);
        $bgcolor = $color[0];
      } else {
        $a_inventory['inv_name'] = '';
        $bgcolor = $color[1];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_hardware['inv_name']                                  . "</td>\n";
      $output .= "  <td>" . $a_inventory['inv_name']                                  . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_retired']                                  . "</td>\n";
      $output .= "  <td>" . $a_hardware['mod_name']                                  . "</td>\n";
      $output .= "  <td>" . $a_hardware['ct_city'] . ", " . $a_hardware['st_state']  . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_asset']                                  . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_serial']                                 . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_service']                                . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_supportstart']                                . "</td>\n";
      $output .= "  <td>" . $a_hardware['hw_supportend']                                . "</td>\n";
      $output .= "</tr>\n";
    }
  }

  $output .= "</table>\n\n";

  $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

  $output .= "</body>\n";
  $output .= "</html>\n";

  $body = $output;

  if ($debug == 'yes') {
    mail($Sitedev, "Unsupported Hardware Report", $body, $headers);
  } else {
    mail("unixadmins@intrado.com", "Unsupported Hardware Report", $body, $headers);
  }

?>
