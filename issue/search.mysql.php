<?php
# Script: search.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "search.mysql.php";
    $formVars['search_by']   = clean($_GET['search_by'],   10);
    $formVars['search_for']  = clean($_GET['search_for'],  80);
    $formVars['sort']        = clean($_GET['sort'],        20);

    if (check_userlevel($AL_Edit)) {
       if (strlen($formVars['search_for']) > 0) {
        logaccess($_SESSION['uid'], $package, "Building the query.");

# server name or all - search the inventory and interface
        if ($formVars['search_by'] == 1 || $formVars['search_by'] == 0) {

          if (strlen($formVars['sort']) > 0) {
            $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
            if ($_SESSION['sort'] == '') {
              $_SESSION['sort'] = 'desc';
            } else {
              $_SESSION['sort'] = '';
            }
          } else {
            $orderby = " order by int_server ";
            $_SESSION['sort'] = '';
          }

          $output  = "<table class=\"ui-styled-table\">";
          $output .= "<tr>";
          $output .=   "<th class=\"ui-state-default\">Open Issue Listing</th>";
          $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('open-help');\">Help</a></th>\n";
          $output .= "</tr>\n";
          $output .= "</table>\n";

          $output .= "<div id=\"open-help\" style=\"display: none\">\n";

          $output .= "<div class=\"main-help ui-widget-content\">\n";

          $output .= "<ul>\n";
          $output .= "  <li><strong>Open Issue Listing</strong>\n";
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

          $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Issueroot . "/search.mysql.php?search_by=1&search_for=" . $formVars['search_for'];

          $output .= "<table class=\"ui-styled-table\">";
          $output .= "<tr>";
          $output .=   "<th class=\"ui-state-default\">Server</th>";
          $output .=   "<th class=\"ui-state-default\">Discovered</th>";
          $output .=   "<th class=\"ui-state-default\">Last Update</th>";
          $output .=   "<th class=\"ui-state-default\">Subject</th>";
          $output .=   "<th class=\"ui-state-default\">Requestor</th>";
          $output .= "</tr>";

          $q_string  = "select iss_id,iss_companyid,iss_discovered,iss_subject,usr_name,usr_group,inv_name ";
          $q_string .= "from issue ";
          $q_string .= "left join inventory on issue.iss_companyid = inventory.inv_id ";
          $q_string .= "left join users on users.usr_id = issue.iss_user ";
          $q_string .= "where iss_closed = '0000-00-00' and inv_name like '%" . $formVars['search_for'] . "%' ";
          $q_string .= "order by iss_discovered desc,inv_name";
          $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_issue = mysqli_fetch_array($q_issue)) {

            $q_string  = "select det_timestamp ";
            $q_string .= "from issue_detail ";
            $q_string .= "where det_issue = " . $a_issue['iss_id'] . " ";
            $q_string .= "order by det_timestamp ";
            $q_string .= "limit 1 ";
            $q_issue_detail = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            if (mysqli_num_rows($q_issue_detai) > 0) {
              $a_issue_detail = mysqli_fetch_array($q_issue_detail);
              $detail_time = explode(" ", $a_issue_detail['det_timestamp']);
            } else {
              $detail_time[0] = 'No Details';
            }

            $linkstart = "<a href=\"" . $Issueroot . "/ticket.php?id="    . $a_issue['iss_id']        . "&server=" . $a_issue['iss_companyid'] . "#problem\">";
            $linklist  = "<a href=\"" . $Issueroot . "/issue.php?server=" . $a_issue['iss_companyid'] . "\">";
            $linkend   = "</a>";

            if ($a_issue['iss_user'] == $_SESSION['uid'] || $a_issue['grp_id'] == $_SESSION['group'] || check_userlevel($AL_Admin)) {
              $delstart = "<a href=\"#\" onclick=\"javascript:delete_issue('" . $Issueroot . "/issue.open.del.php?id=" . $a_issue['iss_id'] . "');\">";
              $delend   = "</a>";
            } else {
              $delstart = '';
              $delend   = '';
            }

            $output .= "<tr>";
            $output .=   "<td class=\"ui-widget-content\">" . $linklist  . $a_issue['inv_name']       . $linkend . "</td>";
            $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['iss_discovered']            . "</td>";
            $output .=   "<td class=\"ui-widget-content\">"              . $detail_time[0]                       . "</td>";
            $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_issue['iss_subject']    . $linkend . "</td>";
            $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['usr_name']                  . "</td>";
            $output .= "</tr>";
          }

          $output .= "</table>";

          mysqli_free_result($q_issue);

          $output .= "<p></p>";
          $output .= "<table class=\"ui-styled-table\">";
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

          $q_string  = "select iss_id,iss_companyid,iss_discovered,iss_closed,iss_subject,usr_name,inv_name ";
          $q_string .= "from issue ";
          $q_string .= "left join inventory on issue.iss_companyid = inventory.inv_id ";
          $q_string .= "left join users on users.usr_id = issue.iss_user ";
          $q_string .= "where iss_closed != '0000-00-00' and inv_name like '%" . $formVars['search_for'] . "%' ";
          $q_string .= "order by inv_name,iss_discovered desc";
          $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_issue = mysqli_fetch_array($q_issue)) {

            $linkstart = "<a href=\"" . $Issueroot . "/ticket.php?id="    . $a_issue['iss_id']        . "&server=" . $a_issue['iss_companyid'] . "\">";
            $linklist  = "<a href=\"" . $Issueroot . "/issue.php?server=" . $a_issue['iss_companyid'] . "\">";
            $linkend   = "</a>";

            $output .= "<tr>";
            $output .=   "<td class=\"ui-widget-content\">" . $linklist  . $a_issue['inv_name']       . $linkend . "</td>";
            $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['iss_discovered']            . "</td>";
            $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['iss_closed']                . "</td>";
            $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_issue['iss_subject']    . $linkend . "</td>";
            $output .=   "<td class=\"ui-widget-content\">"              . $a_issue['usr_name']                  . "</td>";
            $output .= "</tr>";
          }

          mysqli_free_result($q_issue);

          $output .= "</table>";

          print "document.getElementById('server_search_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";
        }
      }

      print "document.search.search_for.focus();\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
