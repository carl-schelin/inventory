<?php
# Script: policies.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "policies.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $serverid = clean($_GET['id'], 10);

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">Type</th>";
  $output .= "  <th class=\"ui-state-default\">Description</th>";
  $output .= "  <th class=\"ui-state-default\">Status</th>";
  $output .= "  <th class=\"ui-state-default\">Version</th>";
  $output .= "  <th class=\"ui-state-default\">Date Added</th>";
  $output .= "</tr>";

  $q_string  = "select pt_type,pd_description,pol_status,pol_version,pol_date ";
  $q_string .= "from policy ";
  $q_string .= "left join policy_type on policy_type.pt_id = policy.pol_type ";
  $q_string .= "left join policy_description on policy_description.pd_id = policy.pol_description ";
  $q_string .= "where pol_companyid = " . $serverid . " ";
  $q_string .= "order by pt_type,pd_description ";
  $q_policy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_policy = mysqli_fetch_array($q_policy)) {

    $status = "disabled";
    $class = "ui-state-error";
    if ($a_policy['pol_status']) {
      $status = "enabled";
      $class = "ui-widget-content";
    }

    $output .= "<tr>";
    $output .= "<td class=\"" . $class . "\">" . $a_policy['pt_type']         . "</td>";
    $output .= "<td class=\"" . $class . "\">" . $a_policy['pd_description']  . "</td>";
    $output .= "<td class=\"" . $class . "\">" . $status                      . "</td>";
    $output .= "<td class=\"" . $class . "\">" . $a_policy['pol_version']     . "</td>";
    $output .= "<td class=\"" . $class . "\">" . $a_policy['pol_date']        . "</td>";
    $output .= "</tr>";

  }

  $output .= "</table>";
?>

document.getElementById('policies_mysql').innerHTML = '<?php print mysqli_real_escape_string($output); ?>';

