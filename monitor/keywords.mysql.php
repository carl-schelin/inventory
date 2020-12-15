<?php
# Script: keywords.mysql.php
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
    $package = "keywords.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (isset($_SESSION['sort'])) {
      $orderby = "order by " . clean($_SESSION['sort'], 20) . " ";
    } else {
      $orderby = "order by key_description ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                    = clean($_GET['id'],                      10);
        $formVars['key_description']       = clean($_GET['key_description'],        255);
        $formVars['key_page']              = clean($_GET['key_page'],               255);
        $formVars['key_email']             = clean($_GET['key_email'],              255);
        $formVars['key_annotate']          = clean($_GET['key_annotate'],           255);
        $formVars['key_critical_annotate'] = clean($_GET['key_critical_annotate'],  255);
        $formVars['key_deleted']           = clean($_GET['key_deleted'],             10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['key_deleted'] == 'true') {
          $formVars['key_deleted'] = 1;
        } else {
          $formVars['key_deleted'] = 0;
        }

        if (strlen($formVars['key_description']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "key_description       = \"" . $formVars['key_description']       . "\"," .
            "key_page              = \"" . $formVars['key_page']              . "\"," .
            "key_email             = \"" . $formVars['key_email']             . "\"," . 
            "key_annotate          = \"" . $formVars['key_annotate']          . "\"," .
            "key_critical_annotate = \"" . $formVars['key_critical_annotate'] . "\"," .
            "key_deleted           =   " . $formVars['key_deleted'];

          if ($formVars['update'] == 0) {
            $query = "insert into keywords set key_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update keywords set " . $q_string . " where key_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['key_email']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Keyword Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('keyword-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"keyword-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Keyword Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Keyword to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Keyword Management</strong> title bar to toggle the <strong>Keyword Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"keywords.php?sort=key_description\">Description</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"keywords.php?sort=key_page\">Page</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"keywords.php?sort=key_email\">E-Mail</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"keywords.php?sort=key_annotate\">Annotate</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"keywords.php?sort=key_critical_annotate\">Critical Annotate</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"keywords.php?sort=key_deleted\">Deleted</a></th>";
      $output .= "</tr>";

      $q_string  = "select key_id,key_description,key_page,key_email,key_annotate,key_critical_annotate,key_deleted ";
      $q_string .= "from keywords ";
      $q_string .= $orderby;
      $q_keywords = mysqli_query($db, $q_string) or die (mysqli_error($db));
      while ($a_keywords = mysqli_fetch_array($q_keywords)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('keywords.fill.php?id="  . $a_keywords['key_id'] . "');jQuery('#dialogKeyword').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Delete\" onclick=\"delete_line('keywords.del.php?id=" . $a_keywords['key_id'] . "');\">";
        $linkend = "</a>";

        if ($a_keywords['key_deleted']) {
          $delete = "Yes";
          $class = "ui-state-highlight";
        } else {
          $delete = "No";
          $class = "ui-widget-content";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                                      . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_keywords['key_description']        . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"                     . $a_keywords['key_page']                          . "</td>";
        $output .= "  <td class=\"" . $class . "\">"                     . $a_keywords['key_email']                         . "</td>";
        $output .= "  <td class=\"" . $class . "\">"                     . $a_keywords['key_annotate']                      . "</td>";
        $output .= "  <td class=\"" . $class . "\">"                     . $a_keywords['key_critical_annotate']             . "</td>";
        $output .= "  <td class=\"" . $class . " delete\">"              . $delete                                          . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysqli_free_result($q_keywords);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.keywords.key_description.value = '';\n";
      print "document.keywords.key_page.value = '';\n";
      print "document.keywords.key_email.value = '';\n";
      print "document.keywords.key_annotate.value = '';\n";
      print "document.keywords.key_critical_annotate.value = '';\n";
      print "document.keywords.key_deleted.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
