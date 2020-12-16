<?php
# Script: errors.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "errors.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $serverid = clean($_GET['id'], 10);

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">Error</th>";
  $output .= "  <th class=\"ui-state-default\">Error Priority</th>";
  $output .= "  <th class=\"ui-state-default\">Server Priority</th>";
  $output .= "</tr>";

  $q_string  = "select chk_id,ce_error,ce_priority,chk_priority ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "where chk_companyid = " . $serverid . " and chk_closed = '0000-00-00 00:00:00' and ce_delete = 0 ";
  $q_string .= "order by ce_priority,chk_priority,ce_error ";
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\">" . $a_chkserver['ce_error']     . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_chkserver['ce_priority']  . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_chkserver['chk_priority'] . "</td>";
    $output .= "</tr>";

  }

  $output .= "</table>";
?>

document.getElementById('chkserver_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

