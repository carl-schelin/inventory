<?php
# Script: tags.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "tags.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_manager "
            . "from inventory "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#tags\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
  }
  $output .= "Tag Information";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
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
  $output .= "    <li><strong>Tag Cloud</strong> - Tags that are viewable by all users of the Inventory software.</li>\n";
  $output .= "  </ul></li>\n";
  $output .= "</ul>\n";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<div class=\"main ui-widget-content\">\n";

  $output .= "<t4>Tag Cloud</t4>\n";

  $output .= "<ul id=\"cloud\">\n";

  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_type = 1 ";
  $q_string .= "group by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $linkstart = "<a href=\"" . $Reportroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "\">";
    $linkend   = "</a>";

    $output .= "  <li>" . $linkstart . $a_tags['tag_name'] . $linkend . "</li>\n";
  }

  $output .= "</ul>\n";

  $output .= "</div>\n";

?>

document.getElementById('tags_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

