<?php
# Script: logs.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "logs.mysql.php";

  logaccess($formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  if (isset($_GET["sort"])) {
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by log_date desc ";
    $_SESSION['sort'] = '';
  }

  if (isset($_POST['search_a'])) {
    $formVars['search_a'] = clean($_POST['search_a'], 40) ;
  } else {
    $formVars['search_a'] = '';
  }

  if (isset($_GET['startdate'])) {
    $formVars['startdate'] = clean($_GET['startdate'], 20);
  } else {
    $formVars['startdate'] = date('Y-m-d', strtotime("-14 days"));
  }

  if (isset($_GET['enddate'])) {
    $formVars['enddate'] = clean($_GET['enddate'], 20);
  } else {
    $formVars['enddate'] = date('Y-m-d', strtotime("+1 day"));
  }

  if (isset($_GET['user'])) {
    $formVars['user'] = clean($_GET['user'], 10);
  } else {
    if (isset($_POST['user'])) {
      $formVars['user'] = clean($_POST['user'], 10);
    } else {
      $formVars['user'] = 0;
    }
  }

  $where  = "where (log_detail like '%" . $a_inventory['inv_name'] . "%' or log_detail like '%" . $a_inventory['inv_id'] . "%') ";
  $where  = "where (log_detail like '%" . $a_inventory['inv_name'] . "%') ";
  $where .= "and log_date >= '" . $formVars['startdate'] . "' and log_date <= '" . $formVars['enddate'] . "' ";

  if ($formVars['user'] != 0) {
    $where .= " and log_user = " . $formVars['user'] . " ";
  }

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">Log Listing</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('logs-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"logs-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<p>This page lists all the Inventory log entries for this server. By default, it lists only the last week's worth of logs but you can use the Start Date and End Date drop fields to broaden your search.</p>";
  $output .= "<ul>";
  $output .= "  <li><strong>ID</strong> - This is the log id.</li>";
  $output .= "  <li><strong>User</strong> - The user who made generated the entry.</li>";
  $output .= "  <li><strong>Date</strong> - The date the log entry was made.</li>";
  $output .= "  <li><strong>Source Script</strong> - What script generated the log entry.</li>";
  $output .= "  <li><strong>Detail</strong> - A description of the task.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<form name=\"logs\">\n\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content button\"><input type=\"button\" value=\"Generate Listing\" onClick=\"javascript:attach_logs('logs.mysql.php');\"></td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\">Start Date <input type=\"text\" name=\"startdate\" id=\"startpick\" value=\"" . $formVars['startdate'] . "\"></td>\n";
  $output .= "  <td class=\"ui-widget-content\">End Date <input type=\"text\" name=\"enddate\" id=\"endpick\" value=\"" . $formVars['enddate'] . "\"></td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n\n";

  $output .= "</form>\n\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\">Id</th>\n";
  $output .= "  <th class=\"ui-state-default\">User</th>\n";
  $output .= "  <th class=\"ui-state-default\">Date</th>\n";
  $output .= "  <th class=\"ui-state-default\">Script</th>\n";
  $output .= "  <th class=\"ui-state-default\">Detail</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select log_id,log_user,log_source,log_date,log_detail,usr_name ";
  $q_string .= "from log ";
  $q_string .= "left join users on users.usr_id = log.log_user ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_log = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_log = mysqli_fetch_array($q_log)) {

    $output .= "<tr>\n";
    $output .= "  <td class=\"ui-widget-content\">" . $a_log['log_id']     . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\">" . $a_log['usr_name']   . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\">" . $a_log['log_date']   . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\">" . $a_log['log_source'] . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\">" . $a_log['log_detail'] . "</td>\n";
    $output .= "</tr>\n";

  }

  mysqli_free_result($q_log);

  print "document.getElementById('logs_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

?>
