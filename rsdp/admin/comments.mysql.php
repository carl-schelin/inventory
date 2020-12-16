<?php
# Script: comments.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "comments.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars["com_rsdp"]       = clean($_GET["com_rsdp"],       10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars["com_task"]       = clean($_GET["com_task"],       10);
        $formVars["com_text"]       = clean($_GET["com_text"],     2000);
        $formVars['com_timestamp']  = date("Y-m-d H:i:s");
        $formVars['com_user']       = $_SESSION['uid'];

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['com_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

# if a new server is being entered, we'll need to create a server entry so the comment can have the correct association
          if ($formVars['com_rsdp'] == 0) {
            $q_string  = "insert ";
            $q_string .= "into rsdp_server ";
            $q_string .= "set rsdp_id = null,rsdp_requestor = " . $_SESSION['uid'];
            $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

            $formVars['com_rsdp'] = last_insert_id($db);

            print "document.rsdp.rsdp.value = "          . $formVars['com_rsdp'] . ";\n";
            print "document.comments.com_rsdp.value = "  . $formVars['com_rsdp'] . ";\n";
# a new server will only be made from the Initial page and 'fs_rsdp' is only on the initial.php page.
# unless someone tries to add a comment in a task farther along on a new, unsaved server, this should 
# never cause a problem (cross-fingers).
            print "document.filesystem.fs_rsdp.value = " . $formVars['com_rsdp'] . ";\n";
          }

          $q_string =
            "com_rsdp      =   " . $formVars['com_rsdp']      . "," . 
            "com_task      =   " . $formVars['com_task']      . "," . 
            "com_text      = \"" . $formVars['com_text']      . "\"," . 
            "com_timestamp = \"" . $formVars['com_timestamp'] . "\"," . 
            "com_user      =   " . $formVars['com_user'];

          if ($formVars['update'] == 0) {
            $query = "insert into rsdp_comments set com_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update rsdp_comments set " . $q_string . " where com_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['com_rsdp']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Comment Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('comment-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"comment-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Comment Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Comment to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Comment Management</strong> title bar to toggle the <strong>Comment Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Del</th>\n";
      $output .= "  <th class=\"ui-state-default\">Last Update</th>\n";
      $output .= "  <th class=\"ui-state-default\">Comment</th>\n";
      $output .= "</tr>";

      $q_string  = "select com_id,com_text,com_timestamp,usr_name ";
      $q_string .= "from rsdp_comments ";
      $q_string .= "left join users on users.usr_id = rsdp_comments.com_user ";
      $q_string .= "where com_rsdp = " . $formVars['com_rsdp'] . " ";
      $q_string .= "order by com_timestamp";
      $q_rsdp_comments = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_rsdp_comments) > 0) {
        while ($a_rsdp_comments = mysqli_fetch_array($q_rsdp_comments)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('" . $RSDProot . "/admin/comments.fill.php?id="     . $a_rsdp_comments['com_id'] . "');jQuery('html,body').scrollTop(0);jQuery('#dialogComment').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_comment('" . $RSDProot . "/admin/comments.del.php?id=" . $a_rsdp_comments['com_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>";
          $output .= "  <td class=\"delete ui-widget-content\">"                      . $linkdel                                             . "</td>";
          $output .= "  <td class=\"ui-widget-content\" align=\"left\">" . $linkstart . "by: <strong>" . $a_rsdp_comments['usr_name'] . "</strong> on <strong>" . $a_rsdp_comments['com_timestamp'] . "</strong>" . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\" align=\"left\">" . $linkstart . $a_rsdp_comments['com_text']                     . $linkend . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No comments found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_rsdp_comments);

      print "document.getElementById('comment_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.comments.com_text.value = '';\n";
      print "document.comments.com_rsdp.value = " . $formVars['com_rsdp'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
