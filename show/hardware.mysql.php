<?php
# Script: hardware.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "hardware.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_manager "
            . "from inventory "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

# get the misc hardware info

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#hardware\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Hardware Information";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('hardware-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"hardware-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Hardware Information</strong>";
  $output .= "  <ul>";
  $output .= "    <li><strong>Asset</strong> - The Company Asset Tag.</li>";
  $output .= "    <li><strong>Serial</strong> - The Vendor serial number.</li>";
  $output .= "    <li><strong>Service</strong> - The Dell specific service tag.</li>";
  $output .= "    <li><strong>Model</strong> - Model information. Clicking on this will bring up a search listing containing all servers associated with this hardware model.</li>";
  $output .= "    <li><strong>Type</strong> - The general type of the listed hardware. Clicking on this will bring up a search listing containing all servers associated with this hardware model type.</li>";
  $output .= "    <li><strong>Size</strong> - The size of the hardware. Servers will be listed in rack unit heights, drives in drive size, CPUs in number of cores, Memory in memory size, etc.</li>";
  $output .= "    <li><strong>Speed</strong> - The speed of the hardware. Drives in drive speed, CPUs in Mhz or Ghz speed, etc.</li>";
  $output .= "    <li><strong>Update</strong> - The date of the last change. A checkmark indicates the change was automatically entered.</li>";
  $output .= "  </ul></li>";
  $output .= "</ul>";
  $output .= "<ul>";
  $output .= "  <li><span class=\"ui-state-highlight\">Primary Container</span> - Hardware highlighted in this manner indicate this device is the main system and will contain all the other listed devices.</li>";
  $output .= "  <li><strong>Asterisk (*)</strong> - Fields marked with an asterisk indicate the information was hidden from view so couldn't be verified.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Asset</th>";
  $output .= "<th class=\"ui-state-default\">Serial</th>";
  $output .= "<th class=\"ui-state-default\">Model</th>";
  $output .= "<th class=\"ui-state-default\">Type</th>";
  $output .= "<th class=\"ui-state-default\">Size</th>";
  $output .= "<th class=\"ui-state-default\">Speed</th>";
  $output .= "<th class=\"ui-state-default\">Updated</th>";
  $output .= "</tr>";

  $support = 0;
  $q_string  = "select hw_id,part_name,hw_serial,hw_asset,mod_name,mod_speed,mod_size,hw_supportid,hw_primary,hw_verified,hw_note,hw_update ";
  $q_string .= "from hardware ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
  $q_string .= "where hw_deleted = 0 and hw_companyid = " . $formVars['id'] . " and hw_hw_id = 0 and hw_hd_id = 0 ";
  $q_string .= "order by hw_type,hw_serial";
  $q_hardware = mysqli_query($db, $q_string) or die("Hardware:" . mysqli_error($db));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {

    $link_name   = "<a href=\"" . $Reportroot . "/search.hardware.php?search_by=4&search_for=" . $a_hardware['mod_name']  . "\" target=\"_blank\">";
    $link_type   = "<a href=\"" . $Reportroot . "/search.hardware.php?search_by=4&search_for=" . $a_hardware['part_name'] . "\" target=\"_blank\">";
    $linkend     = "</a>";

    $checkmark = "";
    if ($a_hardware['hw_verified']) {
      $checkmark = "&#x2713;";
    }

    if ($a_hardware['hw_primary']) {
      $support = $a_hardware['hw_supportid'];
      $class = " class=\"ui-state-highlight\"";
    } else {
      $class = " class=\"ui-widget-content\"";
    }

    $output .= "<tr>";
    $output .= "<td" . $class . ">"                                                       . $a_hardware['hw_asset']                           . "</td>";
    $output .= "<td" . $class . ">"                                                       . $a_hardware['hw_serial']                          . "</td>";
    $output .= "<td" . $class . " title=\"" . $a_hardware['hw_note'] . "\">" . $link_name . $a_hardware['mod_name']                . $linkend . "</td>";
    $output .= "<td" . $class . ">"                                          . $link_type . $a_hardware['part_name']               . $linkend . "</td>";
    $output .= "<td" . $class . ">"                                                       . $a_hardware['mod_size']                            . "</td>";
    $output .= "<td" . $class . ">"                                                       . $a_hardware['mod_speed']                           . "</td>";
    $output .= "<td" . $class . ">"                                                       . $a_hardware['hw_update'] . $checkmark             . "</td>";
    $output .= "</tr>";

    $q_string  = "select hw_id,part_name,hw_serial,hw_asset,mod_name,mod_speed,mod_size,hw_supportid,hw_primary,hw_verified,hw_note,hw_update ";
    $q_string .= "from hardware ";
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
    $q_string .= "where hw_deleted = 0 and hw_companyid = " . $formVars['id'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " and hw_hd_id = 0 ";
    $q_string .= "order by hw_type,hw_serial";
    $q_hwselect = mysqli_query($db, $q_string) or die("Hardware:" . mysqli_error($db));
    while ($a_hwselect = mysqli_fetch_array($q_hwselect)) {

      $link_name   = "<a href=\"" . $Reportroot . "/search.hardware.php?search_by=4&search_for=" . $a_hwselect['mod_name']  . "\" target=\"_blank\">";
      $link_type   = "<a href=\"" . $Reportroot . "/search.hardware.php?search_by=4&search_for=" . $a_hwselect['part_name'] . "\" target=\"_blank\">";
      $linkend     = "</a>";

      $checkmark = "";
      if ($a_hwselect['hw_verified']) {
        $checkmark = "&#x2713;";
      }

      if ($a_hwselect['hw_primary']) {
        $support = $a_hwselect['hw_supportid'];
        $class = " class=\"ui-state-highlight\"";
      } else {
        $class = " class=\"ui-widget-content\"";
      }

      $output .= "<tr>";
      $output .= "<td" . $class . ">"                                                            . $a_hwselect['hw_asset']                           . "</td>";
      $output .= "<td" . $class . ">"                                                            . $a_hwselect['hw_serial']                          . "</td>";
      $output .= "<td" . $class . " title=\"" . $a_hardware['hw_note'] . "\"> &gt;" . $link_name . $a_hwselect['mod_name']                . $linkend . "</td>";
      $output .= "<td" . $class . ">"                                               . $link_type . $a_hwselect['part_name']               . $linkend . "</td>";
      $output .= "<td" . $class . ">"                                                            . $a_hwselect['mod_size']                            . "</td>";
      $output .= "<td" . $class . ">"                                                            . $a_hwselect['mod_speed']                           . "</td>";
      $output .= "<td" . $class . ">"                                                            . $a_hwselect['hw_update'] . $checkmark             . "</td>";
      $output .= "</tr>";

      $q_string  = "select part_name,hw_serial,hw_asset,mod_name,mod_speed,mod_size,hw_supportid,hw_primary,hw_verified,hw_note,hw_update ";
      $q_string .= "from hardware ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
      $q_string .= "where hw_companyid = " . $formVars['id'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " and hw_hd_id = " . $a_hwselect['hw_id'] . " ";
      $q_string .= "order by hw_type,hw_serial";
      $q_hwdisk = mysqli_query($db, $q_string) or die("Hardware:" . mysqli_error($db));
      while ($a_hwdisk = mysqli_fetch_array($q_hwdisk)) {

        $link_name   = "<a href=\"" . $Reportroot . "/search.hardware.php?search_by=4&search_for=" . $a_hwdisk['mod_name']  . "\" target=\"_blank\">";
        $link_type   = "<a href=\"" . $Reportroot . "/search.hardware.php?search_by=4&search_for=" . $a_hwdisk['part_name'] . "\" target=\"_blank\">";
        $linkend     = "</a>";

        $checkmark = "";
        if ($a_hwdisk['hw_verified']) {
          $checkmark = "&#x2713;";
        }

        if ($a_hwdisk['hw_primary']) {
          $support = $a_hwdisk['hw_supportid'];
          $class = " class=\"ui-state-highlight\"";
        } else {
          $class = " class=\"ui-widget-content\"";
        }

        $output .= "<tr>";
        $output .= "<td" . $class . ">"                                                                . $a_hwdisk['hw_asset']                           . "</td>";
        $output .= "<td" . $class . ">"                                                                . $a_hwdisk['hw_serial']                          . "</td>";
        $output .= "<td" . $class . " title=\"" . $a_hardware['hw_note'] . "\"> &gt;&gt;" . $link_name . $a_hwdisk['mod_name']                . $linkend . "</td>";
        $output .= "<td" . $class . ">"                                                   . $link_type . $a_hwdisk['part_name']               . $linkend . "</td>";
        $output .= "<td" . $class . ">"                                                                . $a_hwdisk['mod_size']                            . "</td>";
        $output .= "<td" . $class . ">"                                                                . $a_hwdisk['mod_speed']                           . "</td>";
        $output .= "<td" . $class . ">"                                                                . $a_hwdisk['hw_update'] . $checkmark             . "</td>";
        $output .= "</tr>";
      }
    }
  }

  $output .= "</table>";

?>

document.getElementById('hardware_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

