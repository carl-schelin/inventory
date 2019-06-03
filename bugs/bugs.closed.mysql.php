<?php
# Script: bugs.closed.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "bugs.closed.mysql.php";

  logaccess($_SESSION['uid'], $package, "Creating the closed bugs listing.");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
    $where = "and bug_module = " . $formVars['id'] . " ";
  } else {
    $formVars['id'] = 0;
    $where = ' ';
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Closed Bug Listing</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('closed-help');\">Help</a></th>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<div id=\"closed-help\" style=\"display: none\">\n";

  $output .= "<div class=\"main-help ui-widget-content\">\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Closed Bug Listing</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li><strong>Editing</strong>\n";
  $output .= "    <ul>\n";
  $output .= "      <li><strong>Module Name</strong>  - Clicking on the Module Name will filter out all other open and closed bugs so you can view just the bugs for the selected Module.</li>\n";
  $output .= "      <li><strong>Subject</strong> - Click on the Subject to view the Bug report. You can reopen a Bug report from within the report.</li>\n";
  $output .= "    </ul></li>\n";

  $output .= "    </ul></li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "</div>\n";

  $output .= "</div>\n";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Module</th>";
  $output .=   "<th class=\"ui-state-default\">Discovered</th>";
  $output .=   "<th class=\"ui-state-default\">Closed</th>";
  $output .=   "<th class=\"ui-state-default\">Subject</th>";
  $output .=   "<th class=\"ui-state-default\">Opened By</th>";
  $output .=   "<th class=\"ui-state-default\">Closed By</th>";
  $output .= "</tr>";

  $q_string  = "select bug_id,bug_module,bug_discovered,bug_closed,bug_closeby,bug_subject,mod_name,usr_name ";
  $q_string .= "from bugs ";
  $q_string .= "left join modules on modules.mod_id = bugs.bug_module ";
  $q_string .= "left join users   on users.usr_id   = bugs.bug_openby ";
  $q_string .= "where bug_closed != '0000-00-00' " . $where;
  $q_string .= "order by mod_name,bug_discovered desc";
  $q_bugs = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_bugs) > 0) {
    while ($a_bugs = mysql_fetch_array($q_bugs)) {

      $q_string  = "select usr_name ";
      $q_string .= "from users ";
      $q_string .= "where usr_id = " . $a_bugs['bug_closeby'] . " ";
      $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_users = mysql_fetch_array($q_users);

      $linkstart = "<a href=\"" . $Bugroot . "/ticket.php?id=" . $a_bugs['bug_id']     . "\">";
      $linklist  = "<a href=\"" . $Bugroot . "/bugs.php?id="   . $a_bugs['bug_module'] . "#closed\">";
      $linkend   = "</a>";

      $output .= "<tr>";
      $output .=   "<td class=\"ui-widget-content\">" . $linklist  . $a_bugs['mod_name']          . $linkend . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_bugs['bug_discovered']               . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_bugs['bug_closed']                   . "</td>";
      $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_bugs['bug_subject']       . $linkend . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_bugs['usr_name']                     . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_users['usr_name']                    . "</td>";
      $output .= "</tr>";
    }
  } else {
    $output .= "<tr>";
    $output .=   "<td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
    $output .= "</tr>";
  }

  $output .= "</table>";

  mysql_free_result($q_bugs);

  print "document.getElementById('closed_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n";

?>
