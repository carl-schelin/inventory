<?php
# Script: filesystem.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "filesystem.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_manager "
            . "from inventory "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

# get the filesystem information

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#filesystem\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Filesystem Information";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('filesystem-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"filesystem-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Device</strong> - The system device.</li>";
  $output .= "  <li><strong>Mount</strong> - Where the file system is mounted.</li>";
  $output .= "  <li><strong>Size</strong> - The size of the file system.</li>";
  $output .= "  <li><strong>Volume</strong> - In a multipathed SAN environment, the volume this path is connected to.</li>";
  $output .= "  <li><strong>WWID</strong> - The World Wide Identifier.</li>";
  $output .= "  <li><strong>Updated</strong> - When this device was last updated. A checkmark indicates the device was updated automatically.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Device</th>";
  $output .= "<th class=\"ui-state-default\">Mount</th>";
  $output .= "<th class=\"ui-state-default\">Size</th>";
  $output .= "<th class=\"ui-state-default\">Used</th>";
  $output .= "<th class=\"ui-state-default\">Available</th>";
  $output .= "<th class=\"ui-state-default\">% Used</th>";
  $output .= "<th class=\"ui-state-default\">Volume</th>";
  $output .= "<th class=\"ui-state-default\">WWID</th>";
  $output .= "<th class=\"ui-state-default\">Updated</th>";
  $output .= "</tr>";

  $q_string  = "select fs_device,fs_size,fs_volume,fs_mount,fs_wwid,fs_verified,fs_update,fs_used,fs_avail,fs_percent ";
  $q_string .= "from filesystem ";
  $q_string .= "where fs_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by fs_device,fs_mount";
  $q_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  while ( $a_filesystem = mysqli_fetch_array($q_filesystem) ) {

    $checkmark = "";
    if ($a_filesystem['fs_verified']) {
      $checkmark = "&#x2713;";
    }

    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_device']              . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_mount']               . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_size']                . " K</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_used']                . " K</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_avail']               . " K</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_percent']             . " %</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_volume']              . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_wwid']                . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_filesystem['fs_update'] . $checkmark . "</td>";
    $output .= "</tr>";

  }

  $output .= "</table>";
?>

document.getElementById('filesystem_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

