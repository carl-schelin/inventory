<?php
# Script: repos.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "repos.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],            10);
        $formVars['rep_version']      = clean($_GET['rep_version'],    5);
        $formVars['rep_group']        = clean($_GET['rep_group'],     60);
        $formVars['rep_name']         = clean($_GET['rep_name'],      60);
        $formVars['rep_grpdesc']      = clean($_GET['rep_grpdesc'],  255);
        $formVars['rep_type']         = clean($_GET['rep_type'],      20);
        $formVars['rep_package']      = clean($_GET['rep_package'],   60);
        $formVars['rep_pkgdesc']      = clean($_GET['rep_pkgdesc'],  255);
        $formVars['rep_included']     = clean($_GET['rep_included'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['rep_included'] == 'true') {
          $formVars['rep_included'] = 1;
        } else {
          $formVars['rep_included'] = 0;
        }

        if (strlen($formVars['rep_package']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "rep_version         = \"" . $formVars['rep_version']        . "\"," .
            "rep_group           = \"" . $formVars['rep_group']          . "\"," . 
            "rep_name            = \"" . $formVars['rep_name']           . "\"," . 
            "rep_grpdesc         = \"" . $formVars['rep_grpdesc']        . "\"," . 
            "rep_type            = \"" . $formVars['rep_type']           . "\"," . 
            "rep_package         = \"" . $formVars['rep_package']        . "\"," . 
            "rep_pkgdesc         = \"" . $formVars['rep_pkgdesc']        . "\"," . 
            "rep_included        =   " . $formVars['rep_included'];

          if ($formVars['update'] == 0) {
            $query = "insert into repos set rep_id = NULL, " . $q_string;
            $message = "Package added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update repos set " . $q_string . " where rep_id = " . $formVars['id'];
            $message = "Package updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rep_package']);

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
      $output .= "  <th class=\"ui-state-default\">Package Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('package-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"package-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Package Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Package to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Version</th>";
      $output .= "  <th class=\"ui-state-default\">Group ID</th>";
      $output .= "  <th class=\"ui-state-default\">Group Name</th>";
      $output .= "  <th class=\"ui-state-default\">Group Description</th>";
      $output .= "  <th class=\"ui-state-default\">Package Type</th>";
      $output .= "  <th class=\"ui-state-default\">Package Name</th>";
      $output .= "  <th class=\"ui-state-default\">Package Description</th>";
      $output .= "  <th class=\"ui-state-default\">Include?</th>";
      $output .= "</tr>";

      $q_string  = "select rep_version,rep_group,rep_name,rep_grpdesc,rep_type,rep_package,rep_pkgdesc,rep_included ";
      $q_string .= "from repos ";
      $q_string .= "where rep_version = '7.2' ";
#      $q_string .= "where rep_version = '7.2' and (rep_group = 'base' or rep_group = 'core' or rep_group = 'debugging' or rep_group = 'development' or rep_group = 'hardware-monitoring' or rep_group = 'large-systems' or rep_group = 'legacy-unix' or rep_group = 'network-tools' or rep_group = 'performance' or rep_group = 'perl-runtime' or rep_group = 'server-platform' or rep_group = 'server-platform-devel' or rep_group = 'system-admin-tools' or rep_group = 'system-management') ";
      $q_string .= "order by rep_name,rep_package ";
      $q_repos = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_repos) > 0) {
        while ($a_repos = mysqli_fetch_array($q_repos)) {

          if ($a_repos['rep_included']) {
            $included = 'Yes';
          } else {
            $included = 'No';
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('repos.fill.php?id="  . $a_repos['rep_id'] . "');jQuery('#dialogRepo').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_package('repos.del.php?id=" . $a_repos['rep_id'] . "');\">";
          $linkend = "</a>";

          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_repos['rep_version']   . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_repos['rep_group']     . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_repos['rep_name']      . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_repos['rep_grpdesc']   . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_repos['rep_type']      . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_repos['rep_package']   . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $a_repos['rep_pkgdesc']   . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkstart . $included                 . $linkend . "</td>";
          $output .= "</tr>";

        }
        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No Packages defined</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_repos);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.package.rep_version.value = '';\n";
      print "document.package.rep_group.value = '';\n";
      print "document.package.rep_name.value = '';\n";
      print "document.package.rep_grpdesc.value = '';\n";
      print "document.package.rep_type.value = '';\n";
      print "document.package.rep_package.value = '';\n";
      print "document.package.rep_pkgdesc.value = '';\n";
      print "document.package.rep_included.checked = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
