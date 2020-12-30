<?php
# Script: patching.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "patching.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']            = clean($_GET['id'],            10);
        $formVars['patch_name']    = clean($_GET['patch_name'],    60);
        $formVars['patch_user']    = clean($_GET['patch_user'],    10);
        $formVars['patch_group']   = clean($_GET['patch_group'],   10);
        $formVars['patch_date']    = clean($_GET['patch_date'],    12);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if (strlen($formVars['patch_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "patch_name   = \"" . $formVars['patch_name']   . "\"," .
            "patch_user   =   " . $formVars['patch_user']   . "," .
            "patch_group  =   " . $formVars['patch_group']  . "," .
            "patch_date   = \"" . $formVars['patch_date']   . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into patching set patch_id = NULL, " . $q_string;
            $message = "Patch Description added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update patching set " . $q_string . " where patch_id = " . $formVars['id'];
            $message = "Patch Description updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['dev_type']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Patch Description Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('patching-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"patching-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Patch Description Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Patch Description to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Name</th>";
      $output .= "  <th class=\"ui-state-default\">User</th>";
      $output .= "  <th class=\"ui-state-default\">Group</th>";
      $output .= "  <th class=\"ui-state-default\">Date</th>";
      $output .= "</tr>";

      $q_string  = "select patch_id,patch_name,usr_first,usr_last,grp_name,patch_date ";
      $q_string .= "from patching ";
      $q_string .= "left join users on users.usr_id = patching.patch_user ";
      $q_string .= "left join a_groups on a_groups.grp_id = patching.patch_group ";
      $q_string .= "order by patch_name";
      $q_patching = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_patching) > 0) {
        while ($a_patching = mysqli_fetch_array($q_patching)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('patching.fill.php?id="  . $a_patching['patch_id'] . "');jQuery('#dialogPatching').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_patch('patching.del.php?id=" . $a_patching['patch_id'] . "');\">";
          $linkend = "</a>";

          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_patching['patch_name']                                 . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_patching['usr_last'] . ", " . $a_patching['usr_first'] . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_patching['grp_name']                                   . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_patching['patch_date']                                 . $linkend . "</td>";
          $output .= "</tr>";

        }
        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No Patch Descriptions defined</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_patching);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.patching.patch_name.value = '';\n";
      print "document.patching.patch_user[0].selected = true;\n";
      print "document.patching.patch_group[0].selecgted = true;\n";
      print "document.patching.patch_date.value = '';\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
