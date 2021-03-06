<?php
# Script: tags.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "tags.mysql.php";
    $formVars['update']         = clean($_GET['update'],            10);
    $formVars['tag_companyid']  = clean($_GET['tag_companyid'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['tag_companyid'] == '') {
      $formVars['tag_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],            10);
        $formVars['tag_name']       = str_replace(' ', '_', clean($_GET['tag_name'], 40));
        $formVars['tag_owner']      = clean($_SESSION['uid'],       10);
        $formVars['tag_group']      = clean($_SESSION['group'],     10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['tag_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "tag_companyid =   " . $formVars['tag_companyid'] . "," .
            "tag_name      = \"" . $formVars['tag_name']      . "\"," .
            "tag_type      =   " . "1"                        . "," .
            "tag_owner     =   " . $formVars['tag_owner']     . "," .
            "tag_group     =   " . $formVars['tag_group'];

          if ($formVars['update'] == 0) {
            $query = "insert into tags set tag_id = NULL," . $q_string;
            $message = "Tag added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update tags set " . $q_string . " where tag_id = " . $formVars['id'];
            $message = "Tag updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['tag_name']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -2) {
        $formVars['copyfrom'] = clean($_GET['copyfrom'], 10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select tag_name,tag_type,tag_owner,tag_group ";
          $q_string .= "from tags ";
          $q_string .= "where tag_companyid = " . $formVars['copyfrom'] . " and tag_type = 1 ";
          $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_tags = mysqli_fetch_array($q_tags)) {

            $q_string =
              "tag_companyid   =   " . $formVars['tag_companyid']   . "," .
              "tag_name        = \"" . $a_tags['tag_name']      . "\"," .
              "tag_type        =   " . $a_tags['tag_type']      . "," .
              "tag_owner       =   " . $a_tags['tag_owner']     . "," .
              "tag_group       =   " . $a_tags['tag_group'];

            $query = "insert into tags set tag_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"tag_refresh\" value=\"Refresh Tags Listing\" onClick=\"javascript:attach_tags('tags.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"tag_update\"  value=\"Update Tag\"           onClick=\"javascript:attach_tags('tags.mysql.php', 1);hideDiv('tags-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"tag_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"tag_addbtn\"  value=\"Add New Tag\"          onClick=\"javascript:attach_tags('tags.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\" value=\"Copy Tags From:\" onClick=\"javascript:attach_tags('tags.mysql.php', -2);\">\n";
        $output .= "<select name=\"tag_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
        }
        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\" colspan=\"2\">Tag Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Tag Name <input type=\"text\" name=\"tag_name\" size=\"40\"> <input type=\"hidden\" name=\"tag_companyid\" value=\"" . $formVars['tag_companyid'] . "\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<t4>Tags</t4>\n";

        $output .= "<p>\n";

        if (new_Mysql($db)) {
          $q_string  = "select ANY_VALUE(tag_id) as tagid,tag_name ";
        } else {
          $q_string  = "select tag_id as tagid,tag_name ";
        }
        $q_string .= "from tags ";
        $q_string .= "where tag_type = 1 ";
        $q_string .= "group by tag_name ";
        $q_string .= "order by tag_name ";
        $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_tags = mysqli_fetch_array($q_tags)) {
          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('tags.fill.php?id="  . $a_tags['tagid'] . "');\">";
          $linkend   = "</a>";

          $output .= $linkstart . $a_tags['tag_name'] . $linkend . "&nbsp;&nbsp;";
        }

        $output .= "</p>\n";


        print "document.getElementById('tags_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" colspan=\"5\">Tag Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('tags-listing-help');\">Help</a></th>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"tags-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Tag Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Clicking the <strong>x</strong> will delete this tag from this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a tag to toggle the form for editing.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Tag Management</strong> title bar to toggle the <strong>Tag Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Tag</th>\n";
      $output .=   "<th class=\"ui-state-default\">Owner</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select tag_id,tag_name,tag_group,usr_first,usr_last,grp_name ";
      $q_string .= "from tags ";
      $q_string .= "left join inventory on inventory.inv_id = tags.tag_companyid ";
      $q_string .= "left join a_groups on a_groups.grp_id = tags.tag_group ";
      $q_string .= "left join users on users.usr_id = tags.tag_owner ";
      $q_string .= "where tag_companyid = " . $formVars['tag_companyid'] . " and tag_type = 1 ";
      $q_string .= "order by tag_name";
      $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_tags) > 0) {
        while ($a_tags = mysqli_fetch_array($q_tags)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('tags.fill.php?id="  . $a_tags['tag_id'] . "');showDiv('tags-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_tags('tags.del.php?id=" . $a_tags['tag_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_grouplevel($db, $a_tags['tag_group'])) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                                                 . "</td>\n";
          } else {
            $output .= "  <td class=\"ui-widget-content delete\">--</td>\n";
          }
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_tags['tag_name']                              . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_tags['usr_first'] . " " . $a_tags['usr_last'] . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_tags['grp_name']                              . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No Tags defined.</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n";

      mysqli_free_result($q_tags);

      print "document.getElementById('tags_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.edit.tag_update.disabled = true;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
