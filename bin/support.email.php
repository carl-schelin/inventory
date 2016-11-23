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

# In debug mode, it prints out the email vs sending it.
  $debug = 'yes';
  $debug = 'no';

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";
  $color[2] = "yellow";
  $color[3] = "#fa8072";

  $output  = "<html>\n";
  $output .= "<body>\n";

  $output .= "<p>The following systems were not found in the Support Contract spreadsheet managed by Lynda Lilly. This does not ";
  $output .= "mean the system is not supported. There could be missing or invalid Serial or Service Tag numbers. If there is an error ";
  $output .= "in the Serial or Service Tag numbers in the Inventory, updating or adding it will be noted in the weekly update.</p>\n";

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
  $q_string .= "where inv_status = 0 and mod_virtual = 0 and hw_primary = 1 and hw_supid_verified = 0 and inv_manager = 1 ";
  $q_string .= "order by inv_name ";
  $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_hardware) > 0) {
    while ($a_hardware = mysql_fetch_array($q_hardware)) {

      if ($a_hardware['hw_serial'] == '' && $a_hardware['hw_service'] == '') {
        $bgcolor = $color[3];
      } else {
        $bgcolor = $color[0];
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
