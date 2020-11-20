<?php
# Script: location.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "location.mysql.php";

  logaccess($formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $serverid = clean($_GET['id'], 10);

  $q_string = "select inv_location,inv_rack,inv_row,inv_unit,inv_front,inv_rear from inventory where inv_id = $serverid";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $q_string  = "select loc_name,loc_addr1,loc_addr2,loc_suite,loc_city,loc_state,";
  $q_string .= "loc_zipcode,loc_country,loc_details from locations where loc_id = " . $a_inventory['inv_location'];
  $q_locations = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_locations = mysqli_fetch_array($q_locations);

  if ($a_locations['loc_details'] == '') {
    $details = "&nbsp;";
  } else {
    $details .= "<a href=\"" . $a_locations['loc_details'] . "\" target=\"_blank\">Click Here for Data Center Access details</a>";
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\"><strong>Name:</strong> " . $a_locations['loc_name'] . "</td>";
  $output .= "<td class=\"ui-widget-content\"><strong>Rack:</strong> " . $a_inventory['inv_row'] . $a_inventory['inv_rack'] . "/U" . $a_inventory['inv_unit'] . "</td>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\"><strong>Street:</strong> " . $a_locations['loc_addr1'] . "</td>";
  $output .= "<td class=\"ui-widget-content\">&nbsp;</td>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\"><strong>Street:</strong> " . $a_locations['loc_addr2'] . "</td>";
  $output .= "<td class=\"ui-widget-content\" align=center>" . $details . "</td>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\"><strong>Suite:</strong> " . $a_locations['loc_suite'] . "</td>";
  $output .= "<td class=\"ui-widget-content\">&nbsp;</td>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\"><strong>City/State/Zip:</strong> " . $a_locations['loc_city'] . ", ";
  $output .= $a_locations['loc_state'] . " ";
  $output .= $a_locations['loc_zipcode'] . " ";
  $output .= $a_locations['loc_country'] . "</td>";
  $output .= "<td class=\"ui-widget-content\">&nbsp;</td>";
  $output .= "</tr>";

  if ($a_inventory['inv_front'] > 0) {
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\">Front Picture:</td>";
    $output .= "</tr>";
    $output .= "<tr>";
    $q_string  = "select img_file ";
    $q_string .= "from images ";
    $q_string .= "where img_id = " . $a_inventory['inv_front'];
    $q_images = mysqli_query($db, $q_string . ": " . mysqli_error($db));
    $a_images = mysqli_fetch_array($q_images);

    $output .= "<td class=\"ui-widget-content\" colspan=3><a href=\"" . $Siteroot . "/pictures/" . $a_images['img_file'] . "\"><img src=\"" . $Siteroot . "/pictures/" . $a_images['img_file'] . "\" width=800></a></td>";
    $output .= "</tr>";
  }
  if ($a_inventory['inv_rear'] > 0) {
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\">Rear Picture:</td>";
    $output .= "</tr>";
    $output .= "<tr>";
    $q_string  = "select img_file ";
    $q_string .= "from images ";
    $q_string .= "where img_id = " . $a_inventory['inv_rear'];
    $q_images = mysqli_query($db, $q_string . ": " . mysqli_error($db));
    $a_images = mysqli_fetch_array($q_images);

    $output .= "<td class=\"ui-widget-content\" colspan=3><a href=\"" . $Siteroot . "/pictures/" . $a_images['img_file']  . "\"><img src=\"" . $Siteroot . "/pictures/" . $a_images['img_file']  . "\" width=800></a></td>";
    $output .= "</tr>";
  }

  $output .= "</table>";
?>

document.getElementById('location_mysql').innerHTML = '<?php print mysqli_real_escape_string($output); ?>';

