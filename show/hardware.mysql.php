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

  $q_string  = "select inv_manager ";
  $q_string .= "from inv_inventory ";
  $q_string .= "where inv_id = " . $formVars['id'] . " ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inv_inventory = mysqli_fetch_array($q_inv_inventory);

  $output  = "<p></p>\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inv_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#hardware\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "Asset Listing";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inv_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('asset-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"asset-help\" style=\"display: none\">";

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
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\">Name</th>\n";
  $output .= "  <th class=\"ui-state-default\">Asset Tag</th>\n";
  $output .= "  <th class=\"ui-state-default\">Serial</th>\n";
  $output .= "  <th class=\"ui-state-default\">Model</th>\n";
  $output .= "  <th class=\"ui-state-default\">Type</th>\n";
  $output .= "  <th class=\"ui-state-default\">Size</th>\n";
  $output .= "  <th class=\"ui-state-default\">Speed</th>\n";
  $output .= "  <th class=\"ui-state-default\">Updated</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select ast_id,ast_name,ast_serial,ast_asset,ast_updated,part_name,mod_name,mod_speed,mod_size ";
  $q_string .= "from inv_assets ";
  $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
  $q_string .= "left join inv_parts  on inv_parts.part_id = inv_models.mod_type ";
  $q_string .= "where ast_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by ast_name ";
  $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_assets) > 0) {
    while ($a_inv_assets = mysqli_fetch_array($q_inv_assets)) {

      $class = "class=\"ui-widget-content";

      $output .= "<tr>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['ast_name']    . "</td>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['ast_asset']   . "</td>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['ast_serial']  . "</td>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['mod_name']    . "</td>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['part_name']   . "</td>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['mod_size']    . "</td>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['mod_speed']   . "</td>\n";
      $output .= "<td " . $class .        "\">" . $a_inv_assets['ast_updated'] . "</td>\n";
      $output .= "</tr>\n";

# get child

      $q_string  = "select ast_id,ast_name,ast_serial,ast_asset,ast_name,ast_updated,part_name,mod_name,mod_size,mod_speed ";
      $q_string .= "from inv_assets ";
      $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
      $q_string .= "left join inv_parts  on inv_parts.part_id = inv_models.mod_type ";
      $q_string .= "where ast_parentid = " . $a_inv_assets['ast_id'] . " ";
      $q_string .= "order by ast_name ";

      $q_child = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_child) > 0) {
        while ($a_child = mysqli_fetch_array($q_child)) {

          $class = "class=\"ui-widget-content";
          $greaterthan = '&gt;';

          $output .= "<tr>\n";
          $output .= "<td " . $class .        "\">" . $greaterthan . $a_child['ast_name']    . "</td>\n";
          $output .= "<td " . $class .        "\">" . $greaterthan . $a_child['ast_asset']   . "</td>\n";
          $output .= "<td " . $class .        "\">" . $greaterthan . $a_child['ast_serial']  . "</td>\n";
          $output .= "<td " . $class .        "\">"                . $a_child['mod_name']    . "</td>\n";
          $output .= "<td " . $class .        "\">"                . $a_child['part_name']   . "</td>\n";
          $output .= "<td " . $class .        "\">"                . $a_child['mod_size']    . "</td>\n";
          $output .= "<td " . $class .        "\">"                . $a_child['mod_speed']   . "</td>\n";
          $output .= "<td " . $class .        "\">"                . $a_child['ast_updated'] . "</td>\n";
          $output .= "</tr>\n";


          $q_string  = "select ast_id,ast_name,ast_serial,ast_asset,ast_name,ast_updated,part_name,mod_name,mod_size,mod_speed ";
          $q_string .= "from inv_assets ";
          $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
          $q_string .= "left join inv_parts  on inv_parts.part_id = inv_models.mod_type ";
          $q_string .= "where ast_parentid = " . $a_child['ast_id'] . " ";
          $q_string .= "order by ast_name ";

          $q_grand = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_grand) > 0) {
            while ($a_grand = mysqli_fetch_array($q_grand)) {

              $class = "class=\"ui-widget-content";
              $greaterthan = '&gt;&gt;';

              $output .= "<tr>\n";
              $output .= "<td " . $class .        "\">" . $greaterthan . $a_grand['ast_name']    . "</td>\n";
              $output .= "<td " . $class .        "\">" . $greaterthan . $a_grand['ast_asset']   . "</td>\n";
              $output .= "<td " . $class .        "\">" . $greaterthan . $a_grand['ast_serial']  . "</td>\n";
              $output .= "<td " . $class .        "\">"                . $a_grand['mod_name']    . "</td>\n";
              $output .= "<td " . $class .        "\">"                . $a_grand['part_name']   . "</td>\n";
              $output .= "<td " . $class .        "\">"                . $a_grand['mod_size']    . "</td>\n";
              $output .= "<td " . $class .        "\">"                . $a_grand['mod_speed']   . "</td>\n";
              $output .= "<td " . $class .        "\">"                . $a_grand['ast_updated'] . "</td>\n";
              $output .= "</tr>\n";


              $q_string  = "select ast_id,ast_name,ast_serial,ast_asset,ast_name,ast_updated,part_name,mod_name,mod_size,mod_speed ";
              $q_string .= "from inv_assets ";
              $q_string .= "left join inv_models on inv_models.mod_id    = inv_assets.ast_modelid ";
              $q_string .= "left join inv_parts  on inv_parts.part_id = inv_models.mod_type ";
              $q_string .= "where ast_parentid = " . $a_grand['ast_id'] . " ";
              $q_string .= "order by ast_name ";

              $q_great = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_great) > 0) {
                while ($a_great = mysqli_fetch_array($q_great)) {

                  $class = "class=\"ui-widget-content";
                  $greaterthan = '&gt;&gt;';

                  $output .= "<tr>\n";
                  $output .= "<td " . $class .        "\">" . $greaterthan . $a_great['ast_name']    . "</td>\n";
                  $output .= "<td " . $class .        "\">" . $greaterthan . $a_great['ast_asset']   . "</td>\n";
                  $output .= "<td " . $class .        "\">" . $greaterthan . $a_great['ast_serial']  . "</td>\n";
                  $output .= "<td " . $class .        "\">"                . $a_great['mod_name']    . "</td>\n";
                  $output .= "<td " . $class .        "\">"                . $a_great['part_name']   . "</td>\n";
                  $output .= "<td " . $class .        "\">"                . $a_great['mod_size']    . "</td>\n";
                  $output .= "<td " . $class .        "\">"                . $a_great['mod_speed']   . "</td>\n";
                  $output .= "<td " . $class .        "\">"                . $a_great['ast_updated'] . "</td>\n";
                  $output .= "</tr>\n";

                }
              }
            }
          }
        }
      }
    }
  }
  $output .= "</table>\n";

?>

document.getElementById('hardware_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

