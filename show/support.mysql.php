<?php
# Script: support.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "support.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $slvval[0] = '';
  $q_string  = "select slv_id,slv_value ";
  $q_string .= "from inv_supportlevel";
  $q_inv_supportlevel = mysqli_query($db, $q_string);
  while ($a_inv_supportlevel = mysqli_fetch_array($q_inv_supportlevel)) {
    $slvval[$a_inv_supportlevel['slv_id']] = $a_inv_supportlevel['slv_value'];
  }

  $hardware  = "<p></p>";
  $hardware .= "<table class=\"ui-styled-table\">";
  $hardware .= "<tr>";
  $hardware .= "  <th class=\"ui-state-default\">Hardware Support Information</th>";
  $hardware .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('hwsupport-help');\">Help</a></th>";
  $hardware .= "</tr>";
  $hardware .= "</table>";

  $hardware .= "<div id=\"hwsupport-help\" style=\"display: none\">";

  $hardware .= "<div class=\"main-help ui-widget-content\">";

  $hardware .= "<p>The information below lists the support details for the hardware that make up this system. If the information is <span class=\"ui-state-highlight\">highlighted</span>, then the contract information has been verified via Lynda Lilly's contract spreadsheet.</p>\n";

  $hardware .= "</div>";

  $hardware .= "</div>";

  $hardware .= "<table class=\"ui-styled-table\">";
  $hardware .= "<tr>";
  $hardware .= "<th class=\"ui-state-default\">Company</th>";
  $hardware .= "<th class=\"ui-state-default\">Phone</th>";
  $hardware .= "<th class=\"ui-state-default\">E-Mail</th>";
  $hardware .= "<th class=\"ui-state-default\">Contract</th>";
  $hardware .= "<th class=\"ui-state-default\">Hardware</th>";
  $hardware .= "<th class=\"ui-state-default\">Date Ends</th>";
  $hardware .= "<th class=\"ui-state-default\">Software</th>";
  $hardware .= "</tr>";

  $q_string  = "select sup_company,sup_phone,sup_email,sup_web,sup_contract,sup_wiki,sup_hwresponse,sup_swresponse,hw_supid_verified,hw_supportend "
  $q_string .= "from inv_support "
  $q_string .= "left join inv_hardware on inv_hardware.hw_supportid = inv_support.sup_id "
  $q_string .= "where hw_companyid = " . $formVars['id'] . " and hw_primary = 1 ";
  $q_inv_support = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  if (mysqli_num_rows($q_inv_support) > 0) {
    while ($a_inv_support = mysqli_fetch_array($q_inv_support)) {

      if ($a_inv_support['hw_supid_verified']) {
        $class = "ui-state-highlight";
      } else {
        $class = "ui-widget-content";
      }

      $hardware .= "<tr>";
      $hardware .= "<td class=\"" . $class . "\" title=\"Link to Company website\"><a href=\"" . $a_inv_support['sup_web'] . "\">" . $a_inv_support['sup_company'] . "</a></td>";
      $hardware .= "<td class=\"" . $class . "\">" . $a_inv_support['sup_phone'] . "</td>";
      $hardware .= "<td class=\"" . $class . "\">" . $a_inv_support['sup_email'] . "</td>";
      $hardware .= "<td class=\"" . $class . "\" title=\"Link to hardware support page\"><a href=\"" . $a_inv_support['sup_wiki'] . "\">" . $a_inv_support['sup_contract'] . "</a></td>";
      $hardware .= "<td class=\"" . $class . "\">" . $slvval[$a_inv_support['sup_hwresponse']] . "</td>";
      $hardware .= "<td class=\"" . $class . "\">" . $a_inv_support['hw_supportend'] . "</td>";
      $hardware .= "<td class=\"" . $class . "\">" . $slvval[$a_inv_support['sup_swresponse']] . "</td>";
      $hardware .= "</tr>";
    }
  } else {
    $hardware .= "<tr>";
    $hardware .= "<td class=\"ui-widget-content\" colspan=\"7\">No Hardware Support Contracts</td>";
    $hardware .= "</tr>";
  }

  $hardware .= "</table>";

  $software  = "<p></p>";
  $software .= "<table class=\"ui-styled-table\">";
  $software .= "<tr>";
  $software .= "  <th class=\"ui-state-default\">Software Support Information</th>";
  $software .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('swsupport-help');\">Help</a></th>";
  $software .= "</tr>";
  $software .= "</table>";

  $software .= "<div id=\"swsupport-help\" style=\"display: none\">";

  $software .= "<div class=\"main-help ui-widget-content\">";

  $software .= "<p>The information below lists the support details for the software that is loaded on this system.</p>\n";

  $software .= "</div>";

  $software .= "</div>";

  $software .= "<table class=\"ui-styled-table\">";
  $software .= "<tr>";
  $software .= "<th class=\"ui-state-default\">Company</th>";
  $software .= "<th class=\"ui-state-default\">Phone</th>";
  $software .= "<th class=\"ui-state-default\">E-Mail</th>";
  $software .= "<th class=\"ui-state-default\">Contract</th>";
  $software .= "<th class=\"ui-state-default\">Hardware</th>";
  $software .= "<th class=\"ui-state-default\">Software</th>";
  $software .= "</tr>";

  $q_string  = "select sup_company,sup_phone,sup_email,sup_web,sup_contract,sup_wiki,sup_hwresponse,sup_swresponse ";
  $q_string .= "from inv_support ";
  $q_string .= "left join inv_software     on inv_software.sw_supportid       = inv_support.sup_id ";
  $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid = inv_software.sw_id ";
  $q_string .= "where svr_companyid = " . $formVars['id'];
  $q_inv_support = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  if (mysqli_num_rows($q_inv_support) > 0) {
    while ($a_inv_support = mysqli_fetch_array($q_inv_support)) {

      $software .= "<tr>";
      $software .= "<td class=\"ui-widget-content\" title=\"Link to Company website\"><a href=\"" . $a_inv_support['sup_web'] . "\">" . $a_inv_support['sup_company'] . "</a></td>";
      $software .= "<td class=\"ui-widget-content\">" . $a_inv_support['sup_phone'] . "</td>";
      $software .= "<td class=\"ui-widget-content\">" . $a_inv_support['sup_email'] . "</td>";
      $software .= "<td class=\"ui-widget-content\" title=\"Link to Software support page\"><a href=\"" . $a_inv_support['sup_wiki'] . "\">" . $a_inv_support['sup_contract'] . "</a></td>";
      $software .= "<td class=\"ui-widget-content\">" . $slvval[$a_inv_support['sup_hwresponse']] . "</td>";
      $software .= "<td class=\"ui-widget-content\">" . $slvval[$a_inv_support['sup_swresponse']] . "</td>";
      $software .= "</tr>";
    }
  } else {
    $software .= "<tr>";
    $software .= "<td class=\"ui-widget-content\" colspan=\"6\">No Software Support Contracts</td>";
    $software .= "</tr>";
  }

  $software .= "</table>";

?>

document.getElementById('hardware_support_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $hardware); ?>';

document.getElementById('software_support_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $software); ?>';

