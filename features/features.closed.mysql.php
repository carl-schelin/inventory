<?php
# Script: features.closed.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "features.closed.mysql.php";

  logaccess($db, $_SESSION['uid'], $package, "Creating the closed feature request listing.");

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);
    $where = "and feat_module = " . $formVars['id'] . " ";
  } else {
    $formVars['id'] = 0;
    $where = ' ';
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Closed Feature Request Listing</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('closed-help');\">Help</a></th>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<div id=\"closed-help\" style=\"display: none\">\n";

  $output .= "<div class=\"main-help ui-widget-content\">\n";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Closed Feature Request Listing</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li><strong>Editing</strong>\n";
  $output .= "    <ul>\n";
  $output .= "      <li><strong>Module Name</strong>  - Clicking on the Module Name will filter out all other open and closed feature requests so you can view just the features for the selected Module.</li>\n";
  $output .= "      <li><strong>Subject</strong> - Click on the Subject to view the Feature Request. You can reopen a Feature Request from within the request.</li>\n";
  $output .= "    </ul></li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "</div>\n";

  $output .= "</div>\n";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .=   "<th class=\"ui-state-default\">Module</th>";
  $output .=   "<th class=\"ui-state-default\">Requested</th>";
  $output .=   "<th class=\"ui-state-default\">Closed</th>";
  $output .=   "<th class=\"ui-state-default\">Subject</th>";
  $output .=   "<th class=\"ui-state-default\">Opened By</th>";
  $output .=   "<th class=\"ui-state-default\">Closed By</th>";
  $output .= "</tr>";

  $q_string  = "select feat_id,feat_module,feat_discovered,feat_closed,feat_closeby,feat_subject,mod_name,usr_name ";
  $q_string .= "from inv_features ";
  $q_string .= "left join inv_modules on inv_modules.mod_id = inv_features.feat_module ";
  $q_string .= "left join inv_users   on inv_users.usr_id   = inv_features.feat_openby ";
  $q_string .= "where feat_closed != '1971-01-01' " . $where;
  $q_string .= "order by mod_name,feat_discovered desc";
  $q_inv_features = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_features) > 0) {
    while ($a_inv_features = mysqli_fetch_array($q_inv_features)) {

      $q_string  = "select usr_name ";
      $q_string .= "from inv_users ";
      $q_string .= "where usr_id = " . $a_inv_features['feat_closeby'] . " ";
      $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_users = mysqli_fetch_array($q_inv_users);

      $linkstart = "<a href=\"" . $Featureroot . "/ticket.php?id="   . $a_inv_features['feat_id']     . "\">";
      $linklist  = "<a href=\"" . $Featureroot . "/features.php?id=" . $a_inv_features['feat_module'] . "#closed\">";
      $linkend   = "</a>";

      $output .= "<tr>";
      $output .=   "<td class=\"ui-widget-content\">" . $linklist  . $a_inv_features['mod_name']          . $linkend . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_inv_features['feat_discovered']              . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_inv_features['feat_closed']                  . "</td>";
      $output .=   "<td class=\"ui-widget-content\">" . $linkstart . $a_inv_features['feat_subject']      . $linkend . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_inv_features['usr_name']                     . "</td>";
      $output .=   "<td class=\"ui-widget-content\">"              . $a_inv_users['usr_name']                        . "</td>";
      $output .= "</tr>";
    }
  } else {
    $output .= "<tr>";
    $output .=   "<td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>";
    $output .= "</tr>";
  }

  $output .= "</table>";

  mysqli_free_result($q_inv_features);

  print "document.getElementById('closed_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n";

?>
