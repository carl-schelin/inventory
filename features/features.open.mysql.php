<?php
# Script: features.open.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login($AL_Guest);

  header('Content-Type: text/javascript');

  $package = "features.open.mysql.php";

  logaccess($db, $_SESSION['uid'], $package, "Creating the open feature request listing.");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
    $where = "and feat_module = " . $formVars['id'] . " ";
  } else {
    $formVars['id'] = 0;
    $where = ' ';
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Open Feature Request Listing</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('open-help');\">Help</a></th>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<div id=\"open-help\" style=\"display: none\">\n";

  $output .= "<div class=\"main-help ui-widget-content\">\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Open Feature Request Listing</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li><strong>Delete (x)</strong> - Click to delete this Feature Request.</li>\n";
  $output .= "    <li><strong>Editing</strong>\n";
  $output .= "    <ul>\n";
  $output .= "      <li><strong>Module Name</strong>  - Clicking on the Module Name will filter out all other open and closed feature requests so you can view just the requests for the selected Module.</li>\n";
  $output .= "      <li><strong>Subject</strong> - Click on the Subject to view the Feature Request.</li>\n";
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
  $output .=   "<th class=\"ui-state-default\">Requested</th>";
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
  $q_string  = "select feat_id,feat_module,feat_severity,feat_priority,feat_discovered,feat_subject,mod_name,feat_openby,usr_name ";
  $q_string .= "from features ";
  $q_string .= "left join users on users.usr_id = features.feat_openby ";
  $q_string .= "left join modules on modules.mod_id = features.feat_module ";
  $q_string .= "where feat_closed = '0000-00-00' " . $where;
  $q_string .= "order by feat_discovered desc,mod_name ";
  $q_features = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_features) > 0) {
    while ($a_features = mysqli_fetch_array($q_features)) {

      $q_string  = "select feat_timestamp ";
      $q_string .= "from features_detail ";
      $q_string .= "where feat_feat_id = " . $a_features['feat_id'] . " ";
      $q_string .= "order by feat_timestamp ";
      $q_string .= "limit 1 ";
      $q_features_detail = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_features_detail) > 0) {
        $a_features_detail = mysqli_fetch_array($q_features_detail);
        $detail_time = explode(" ", $a_features_detail['feat_timestamp']);
      } else {
        $detail_time[0] = 'No Details';
      }

      $linkstart = "<a href=\"" . $Featureroot . "/ticket.php?id="   . $a_features['feat_id']     . "#problem\">";
      $linklist  = "<a href=\"" . $Featureroot . "/features.php?id=" . $a_features['feat_module'] . "#open\">";
      $linkend   = "</a>";
      if ($a_features['feat_openby'] == $_SESSION['uid'] || check_userlevel($db, $AL_Admin)) {
        $delstart = "<a href=\"#\" onclick=\"javascript:delete_feature('" . $Featureroot . "/features.open.del.php?id=" . $a_features['feat_id'] . "');\">";
        $delend   = "</a>";
        $deltext  = 'x';
      } else {
        $delstart = "";
        $delend   = "";
        $deltext  = '--';
      }

      $sevclass = "ui-widget-content";
      if ($a_features['feat_severity'] == 2) {
        $sevclass = "ui-state-highlight";
      }
      if ($a_features['feat_severity'] == 3) {
        $sevclass = "ui-state-error";
      }
      $prclass = "ui-widget-content";
      if ($a_features['feat_priority'] == 1) {
        $prclass = "ui-state-highlight";
      }
      if ($a_features['feat_priority'] == 2) {
        $prclass = "ui-state-error";
      }

      $output .= "<tr>";
      $output .= "  <td class=\"ui-widget-content delete\">" . $delstart  . $deltext                                . $delend  . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"        . $linklist  . $a_features['mod_name']                 . $linkend . "</td>";
      $output .=   "<td class=\"" . $sevclass . "\">"                     . $severity[$a_features['feat_severity']]            . "</td>";
      $output .=   "<td class=\"" . $prclass  . "\">"                     . $priority[$a_features['feat_priority']]            . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"                     . $a_features['feat_discovered']                     . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"                     . $detail_time[0]                                    . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"        . $linkstart . $a_features['feat_subject']             . $linkend . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"                     . $a_features['usr_name']                            . "</td>";
      $output .= "</tr>";
    }
  } else {
    $output .= "<tr>";
    $output .=   "<td class=\"ui-widget-content\" colspan=\"8\">No records found.</td>";
    $output .= "</tr>";
  }

  $output .= "</table>";

  mysqli_free_result($q_features);

  print "document.getElementById('open_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

?>
