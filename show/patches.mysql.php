<?php
# Script: patches.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "patches.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_manager "
            . "from inventory "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $q_string = "select zone_id,zone_name from ip_zones";
  $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ': ' . mysqli_error($db));
  while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
    $zoneval[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
  }

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#firewall\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "BigFix Patches";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('bigfix-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"bigfix-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<p>This page provides a listing of all the patches that were scheduled to be applied to this server, the severity of the patch, and when it was made available by the vendor.<p>";

  $output .= "</div>";

  $output .= "</div>";

  $first = 0;
  $flagged = '';
  $q_string  = "select big_fixlet,big_severity,big_release,big_scheduled ";
  $q_string .= "from bigfix ";
  $q_string .= "where big_companyid = " . $formVars['id'] . " ";
  $q_string .= "order by big_scheduled desc,big_severity,big_fixlet";
  $q_bigfix = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_bigfix) > 0) {
    while ($a_bigfix = mysqli_fetch_array($q_bigfix)) {

      if ($flagged != $a_bigfix['big_scheduled']) {
        if ($first == 1) {
          $output .= "</table>";
        }
        $output .= "<table class=\"ui-styled-table\">";
        $output .= "<tr>";
        $output .= "<th class=\"ui-state-default\">Patch/Fixlet</th>";
        $output .= "<th class=\"ui-state-default\">Severity</th>";
        $output .= "<th class=\"ui-state-default\">Release Date</th>";
        $output .= "<th class=\"ui-state-default\">Scheduled Date</th>";
        $output .= "</tr>";

        $flagged = $a_bigfix['big_scheduled'];
        $first = 1;
      }

      if ($a_bigfix['big_severity'] == 1) {
        $bigfix = 'Unspecified';
      }
      if ($a_bigfix['big_severity'] == 2) {
        $bigfix = 'Critical';
      }
      if ($a_bigfix['big_severity'] == 3) {
        $bigfix = 'Important';
      }
      if ($a_bigfix['big_severity'] == 4) {
        $bigfix = 'Moderate';
      }
      if ($a_bigfix['big_severity'] == 5) {
        $bigfix = 'Low';
      }

      $output .= "<tr>";
      $output .= "  <td class=\"ui-widget-content\">" . $a_bigfix['big_fixlet']    . "</td>";
      $output .= "  <td class=\"ui-widget-content\">" . $bigfix                    . "</td>";
      $output .= "  <td class=\"ui-widget-content\">" . $a_bigfix['big_release']   . "</td>";
      $output .= "  <td class=\"ui-widget-content\">" . $a_bigfix['big_scheduled'] . "</td>";
      $output .= "</tr>";
    }
  }

  $output .= "</table>";

?>

document.getElementById('bigfix_mysql').innerHTML = '<?php print mysqli_real_escape_string($output); ?>';

