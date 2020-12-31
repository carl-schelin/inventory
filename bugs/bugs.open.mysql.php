<?php
# Script: bugs.open.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Guest);

  header('Content-Type: text/javascript');

  $package = "bugs.open.mysql.php";

  logaccess($db, $_SESSION['uid'], $package, "Creating the open bugs listing.");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
    $where = "and bug_module = " . $formVars['id'] . " ";
  } else {
    $formVars['id'] = 0;
    $where = ' ';
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Open Bugs Listing</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('open-help');\">Help</a></th>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<div id=\"open-help\" style=\"display: none\">\n";

  $output .= "<div class=\"main-help ui-widget-content\">\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Open Bug Listing</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li><strong>Delete (x)</strong> - Click to delete this Bug.</li>\n";
  $output .= "    <li><strong>Editing</strong>\n";
  $output .= "    <ul>\n";
  $output .= "      <li><strong>Module Name</strong>  - Clicking on the Module Name will filter out all other open and closed bugs so you can view just the bugs for the selected Module.</li>\n";
  $output .= "      <li><strong>Subject</strong> - Click on the Subject to view the Bug report.</li>\n";
  $output .= "    </ul></li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "</div>\n";

  $output .= "</div>\n";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Del</th>";
  $output .=   "<th class=\"ui-state-default\">Module</th>";
  $output .=   "<th class=\"ui-state-default\">Severity</th>";
  $output .=   "<th class=\"ui-state-default\">Priority</th>";
  $output .=   "<th class=\"ui-state-default\">Discovered</th>";
  $output .=   "<th class=\"ui-state-default\">Last Update</th>";
  $output .=   "<th class=\"ui-state-default\">Subject</th>";
  $output .=   "<th class=\"ui-state-default\">Opened By</th>";
  $output .= "</tr>";

  $severity[0] = 'Note';
  $severity[1] = 'Minor';
  $severity[2] = 'Major';
  $severity[3] = 'Critical';
  $priority[0] = 'Low';
  $priority[1] = 'Medium';
  $priority[2] = 'High';
  $q_string  = "select bug_id,bug_module,bug_severity,bug_priority,bug_discovered,bug_subject,mod_name,bug_openby,usr_name ";
  $q_string .= "from bugs ";
  $q_string .= "left join users   on users.usr_id   = bugs.bug_openby ";
  $q_string .= "left join modules on modules.mod_id = bugs.bug_module ";
  $q_string .= "where bug_closed = '1971-01-01' " . $where;
  $q_string .= "order by bug_discovered desc,mod_name ";
  $q_bugs = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_bugs) > 0) {
    while ($a_bugs = mysqli_fetch_array($q_bugs)) {

      $q_string  = "select bug_timestamp ";
      $q_string .= "from bugs_detail ";
      $q_string .= "where bug_bug_id = " . $a_bugs['bug_id'] . " ";
      $q_string .= "order by bug_timestamp ";
      $q_string .= "limit 1 ";
      $q_bugs_detail = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_bugs_detail) > 0) {
        $a_bugs_detail = mysqli_fetch_array($q_bugs_detail);
        $detail_time = explode(" ", $a_bugs_detail['bug_timestamp']);
      } else {
        $detail_time[0] = 'No Details';
      }

      $linkstart = "<a href=\"" . $Bugroot . "/ticket.php?id=" . $a_bugs['bug_id']     . "#problem\">";
      $linklist  = "<a href=\"" . $Bugroot . "/bugs.php?id="   . $a_bugs['bug_module'] . "#open\">";
      $linkend   = "</a>";
      if ($a_bugs['bug_openby'] == $_SESSION['uid'] || check_userlevel($db, $AL_Admin)) {
        $delstart = "<a href=\"#\" onclick=\"javascript:delete_bug('" . $Bugroot . "/bugs.open.del.php?id=" . $a_bugs['bug_id'] . "');\">";
        $delend   = "</a>";
        $deltext  = 'x';
      } else {
        $delstart = "";
        $delend   = "";
        $deltext  = '--';
      }

      $sevclass = "ui-widget-content";
      if ($a_bugs['bug_severity'] == 2) {
        $sevclass = "ui-state-highlight";
      }
      if ($a_bugs['bug_severity'] == 3) {
        $sevclass = "ui-state-error";
      }
      $prclass = "ui-widget-content";
      if ($a_bugs['bug_priority'] == 1) {
        $prclass = "ui-state-highlight";
      }
      if ($a_bugs['bug_priority'] == 2) {
        $prclass = "ui-state-error";
      }

      $output .= "<tr>";
      $output .= "  <td class=\"ui-widget-content delete\">" . $delstart  . $deltext                            . $delend  . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"        . $linklist  . $a_bugs['mod_name']                 . $linkend . "</td>";
      $output .=   "<td class=\"" . $sevclass . "\">"                     . $severity[$a_bugs['bug_severity']]             . "</td>";
      $output .=   "<td class=\"" . $prclass  . "\">"                     . $priority[$a_bugs['bug_priority']]             . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"                     . $a_bugs['bug_discovered']                      . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"                     . $detail_time[0]                                . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_bugs['bug_subject']              . $linkend . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"                     . $a_bugs['usr_name']                            . "</td>";
      $output .= "</tr>";
    }
  } else {
    $output .= "<tr>";
    $output .=   "<td class=\"ui-widget-content\" colspan=\"8\">No records found.</td>";
    $output .= "</tr>";
  }

  $output .= "</table>";

  mysqli_free_result($q_bugs);

  print "document.getElementById('open_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

?>
