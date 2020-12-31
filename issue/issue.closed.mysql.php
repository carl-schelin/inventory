<?php
# Script: issue.closed.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Sitepath . "/guest.php");

  $package = "issue.closed.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Creating the closed issues listing.");

  if (isset($_GET['server'])) {
    $formVars['server'] = clean($_GET['server'], 10);
  } else {
    $formVars['server'] = 0;
  }

  if (isset($_GET['tag'])) {
    $formVars['tag'] = clean($_GET['tag'], 20);
  } else {
    $formVars['tag'] = '';
  }

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 20);
  } else {
    $formVars['type'] = 0;
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Closed Issue Listing</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('closed-help');\">Help</a></th>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<div id=\"closed-help\" style=\"display: none\">\n";

  $output .= "<div class=\"main-help ui-widget-content\">\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Closed Issue Listing</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li><strong>Editing</strong> - \n";
  $output .= "    <ul>\n";
  $output .= "      <li><strong>Server Name</strong>  - Click on the Server Name to see a list of open and closed issues for the selected server.</li>\n";
  $output .= "      <li><strong>Subject</strong> - Click on the Subject to view the Issue.</li>\n";
  $output .= "    </ul></li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "</div>\n";

  $output .= "</div>\n";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Server</th>";
  $output .=   "<th class=\"ui-state-default\">Discovered</th>";
  $output .=   "<th class=\"ui-state-default\">Closed</th>";
  $output .=   "<th class=\"ui-state-default\">Subject</th>";
  $output .=   "<th class=\"ui-state-default\">Requestor</th>";
  $output .= "</tr>";

  $leftjoin = '';
  if ($formVars['server'] == 0) {
    if (strlen($formVars['tag']) > 0) {
      $leftjoin = "left join tags on tags.tag_companyid = issue.iss_companyid ";
      $where = " and tag_name = '" . $formVars['tag'] . "' ";
      if ($formVars['type'] == 0) {
        $where .= " and tag_view = 0 and tag_owner = " . $_SESSION['uid'] . " ";
      }
      if ($formVars['type'] == 1) {
        $where .= " and tag_view = 1 and tag_group = " . $_SESSION['group'] . " ";
      }
      if ($formVars['type'] == 2) {
        $where = " and tag_view = 2 ";
      }
    } else {
      $where = " and (inv_manager = " . $_SESSION['group'] . " or iss_user = " . $_SESSION['uid'] . ") ";
    }
  } else {
    $where = " and iss_companyid = " . $formVars['server'] . " ";
  }

  $q_string  = "select iss_id,iss_companyid,iss_discovered,iss_closed,iss_subject,usr_name,inv_name ";
  $q_string .= "from issue ";
  $q_string .= "left join inventory on issue.iss_companyid = inventory.inv_id ";
  $q_string .= "left join users on users.usr_id = issue.iss_user ";
  $q_string .= $leftjoin;
  $q_string .= "where iss_closed != '1971-01-01' " . $where;
  $q_string .= "order by inv_name,iss_discovered desc";
  $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_issue = mysqli_fetch_array($q_issue)) {

    $linkstart = "<a href=\"" . $Issueroot . "/ticket.php?id="    . $a_issue['iss_id']        . "&server=" . $a_issue['iss_companyid'] . "\">";
    $linklist  = "<a href=\"" . $Issueroot . "/issue.php?server=" . $a_issue['iss_companyid'] . "#closed\">";
    $linkend   = "</a>";

    $output .= "<tr>";
    $output .=   "<td class=\"ui-widget-content\">" . $linklist  . $a_issue['inv_name']          . $linkend . "</td>";
    $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['iss_discovered']               . "</td>";
    $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['iss_closed']                   . "</td>";
    $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_issue['iss_subject']       . $linkend . "</td>";
    $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['usr_name']                     . "</td>";
    $output .= "</tr>";
  }

  $output .= "</table>";

  if ($formVars['server'] > 0) {
    $output .= "<p><a href=\"" . $Issueroot . "/issue.php?server=" . $formVars['server'] . "\" target=\"_blank\">Link to Issue Tracker</a></p>";
  }

  mysqli_free_result($q_issue);

  print "document.getElementById('closed_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

?>
