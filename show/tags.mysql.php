<?php
# Script: tags.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "tags.mysql.php";

  logaccess($formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_manager "
            . "from inventory "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_inventory = mysql_fetch_array($q_inventory);

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($AL_Edit)) {
    $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#tags\" target=\"_blank\"><img src=\"/inventory/imgs/pencil.gif\">";
  }
  $output .= "Tag Information";
  if (check_userlevel($AL_Edit)) {
    if (check_grouplevel($a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('tags-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"tags-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>\n";
  $output .= "  <li><strong>Tag Cloud</strong>\n";
  $output .= "  <ul>\n";
  $output .= "    <li><strong>Private Tag Cloud</strong> - Shows tags that only you can manipulate.</li>\n";
  $output .= "    <li><strong>Group Tag Cloud</strong> - Shows group tags manageable by your group.</li>\n";
  $output .= "    <li><strong>Public Tag Cloud</strong> - Tags that are viewable by all users of the Inventory software.</li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<div class=\"main ui-widget-content\">\n";

  $output .= "<t4>Private Tag Cloud</t4>\n";

  $output .= "<ul id=\"cloud\">\n";

  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_view = 0 and tag_owner = " . $formVars['uid'] . " ";
  $q_string .= "group by tag_name ";
  $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_tags = mysql_fetch_array($q_tags)) {
    $linkstart = "<a href=\"" . $Reportroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "\">";
    $linkend   = "</a>";

    $output .= "  <li>" . $linkstart . $a_tags['tag_name'] . $linkend . "</li>\n";
  }

  $output .= "</ul>\n";

  $output .= "</div>\n";

  $output .= "<div class=\"main ui-widget-content\">\n";

  $output .= "<t4>Group Tag Cloud</t4>\n";

  $output .= "<ul id=\"cloud\">\n";

  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_view = 1 and tag_group = " . $formVars['group'] . " ";
  $q_string .= "group by tag_name ";
  $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_tags = mysql_fetch_array($q_tags)) {
    $linkstart = "<a href=\"" . $Reportroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "\">";
    $linkend   = "</a>";

    $output .= "  <li>" . $linkstart . $a_tags['tag_name'] . $linkend . "</li>\n";
  }

  $output .= "</ul>\n";

  $output .= "</div>\n";

  $output .= "<div class=\"main ui-widget-content\">\n";

  $output .= "<t4>Public Tag Cloud</t4>\n";

  $output .= "<ul id=\"cloud\">\n";

  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_view = 2 ";
  $q_string .= "group by tag_name ";
  $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_tags = mysql_fetch_array($q_tags)) {
    $linkstart = "<a href=\"" . $Reportroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "\">";
    $linkend   = "</a>";

    $output .= "  <li>" . $linkstart . $a_tags['tag_name'] . $linkend . "</li>\n";
  }

  $output .= "</ul>\n";

  $output .= "</div>\n";

?>

document.getElementById('tags_mysql').innerHTML = '<?php print mysql_real_escape_string($output); ?>';

