<?php
# Script: tags.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "tags.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);
    $formVars['group']  = clean($_GET['group'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['tag_name']       = clean($_GET['tag_name'],       40);
        $formVars['tag_view']       = clean($_GET['tag_view'],       10);
        $formVars['tag_owner']      = clean($_GET['tag_owner'],      10);
        $formVars['tag_group']      = clean($_GET['tag_group'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['tag_name']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "tag_name    = \"" . $formVars['tag_name']     . "," .
            "tag_view    =   " . $formVars['tag_view']     . "," .
            "tag_owner   =   " . $formVars['tag_owner']    . "," .
            "tag_group   =   " . $formVars['tag_group'];

          if ($formVars['update'] == 0) {
            $query = "insert into tags set tag_id = NULL, " . $q_string;
            $message = "Tag added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update tags set " . $q_string . " where tag_id = " . $formVars['id'];
            $message = "Tag updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['tag_name']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Support Contract Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('support-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"support-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Support Contract Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a contract to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel(1)) {
        $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Server</th>\n";
      $output .= "  <th class=\"ui-state-default\">Tag Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">View</th>\n";
      $output .= "  <th class=\"ui-state-default\">Owner</th>\n";
      $output .= "  <th class=\"ui-state-default\">Group</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select tag_id,tag_name,inv_name,tag_view,usr_first,usr_last,grp_name ";
      $q_string .= "from tags ";
      $q_string .= "left join inventory on inventory.inv_id = tags.tag_companyid ";
      $q_string .= "left join users on users.usr_id = tags.tag_owner ";
      $q_string .= "left join groups on groups.grp_id = tags.tag_group ";
      $q_string .= "where inv_manager = " . $formVars['group'] . " ";
      $q_string .= "order by tag_name,inv_name,grp_name,usr_last,usr_first ";
      $q_tags = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_tags) > 0) {
        while ($a_tags = mysql_fetch_array($q_tags)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('tags.fill.php?id="  . $a_tags['tag_id'] . "');jQuery('#dialogTags').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('tags.del.php?id=" . $a_tags['tag_id'] . "');\">";
          $linkend   = "</a>";

          $tag_view = "Private";
          if ($a_tags['tag_view'] == 1) {
            $tag_view = "Group";
          }
          if ($a_tags['tag_view'] == 2) {
            $tag_view = "Public";
          }

          $output .= "<tr>";
          if (check_userlevel(1)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['tag_id']                                . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['inv_name']                              . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['tag_name']                              . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $tag_view                                        . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['usr_first'] . " " . $a_tags['usr_last'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_tags['grp_name']                              . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysql_free_result($q_tags);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.tags.tag_name.value = '';\n";
      print "document.tags.tag_companyid.value = 0;\n";
      print "document.tags.tag_view.value = 0;\n";
      print "document.tags.tag_owner.value = 0;\n";
      print "document.tags.tag_group.value = 0;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
