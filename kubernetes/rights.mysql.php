<?php
# Script: rights.mysql.php
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
    $package = "rights.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                    = clean($_GET['id'],                    10);
        $formVars['rgt_type']              = clean($_GET['rgt_type'],              10);
        $formVars['rgt_apigroup']          = clean($_GET['rgt_apigroup'],          10);
        $formVars['rgt_resource']          = clean($_GET['rgt_resource'],          10);
        $formVars['rgt_get']               = clean($_GET['rgt_get'],               10);
        $formVars['rgt_list']              = clean($_GET['rgt_list'],              10);
        $formVars['rgt_watch']             = clean($_GET['rgt_watch'],             10);
        $formVars['rgt_impersonate']       = clean($_GET['rgt_impersonate'],       10);
        $formVars['rgt_create']            = clean($_GET['rgt_create'],            10);
        $formVars['rgt_delete']            = clean($_GET['rgt_delete'],            10);
        $formVars['rgt_deletecollection']  = clean($_GET['rgt_deletecollection'],  10);
        $formVars['rgt_patch']             = clean($_GET['rgt_patch'],             10);
        $formVars['rgt_update']            = clean($_GET['rgt_update'],            10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['rgt_get'] == 'true') {
          $formVars['rgt_get'] = 1;
        } else {
          $formVars['rgt_get'] = 0;
        }
        if ($formVars['rgt_list'] == 'true') {
          $formVars['rgt_list'] = 1;
        } else {
          $formVars['rgt_list'] = 0;
        }
        if ($formVars['rgt_watch'] == 'true') {
          $formVars['rgt_watch'] = 1;
        } else {
          $formVars['rgt_watch'] = 0;
        }
        if ($formVars['rgt_impersonate'] == 'true') {
          $formVars['rgt_impersonate'] = 1;
        } else {
          $formVars['rgt_impersonate'] = 0;
        }
        if ($formVars['rgt_create'] == 'true') {
          $formVars['rgt_create'] = 1;
        } else {
          $formVars['rgt_create'] = 0;
        }
        if ($formVars['rgt_delete'] == 'true') {
          $formVars['rgt_delete'] = 1;
        } else {
          $formVars['rgt_delete'] = 0;
        }
        if ($formVars['rgt_deletecollection'] == 'true') {
          $formVars['rgt_deletecollection'] = 1;
        } else {
          $formVars['rgt_deletecollection'] = 0;
        }
        if ($formVars['rgt_patch'] == 'true') {
          $formVars['rgt_patch'] = 1;
        } else {
          $formVars['rgt_patch'] = 0;
        }
        if ($formVars['rgt_update'] == 'true') {
          $formVars['rgt_update'] = 1;
        } else {
          $formVars['rgt_update'] = 0;
        }

        if (strlen($formVars['rgt_apigroup']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "rgt_type               =   " . $formVars['rgt_type']               . "," . 
            "rgt_apigroup           =   " . $formVars['rgt_apigroup']           . "," . 
            "rgt_resource           =   " . $formVars['rgt_resource']           . "," . 
            "rgt_get                =   " . $formVars['rgt_get']                . "," . 
            "rgt_list               =   " . $formVars['rgt_list']               . "," . 
            "rgt_watch              =   " . $formVars['rgt_watch']              . "," . 
            "rgt_impersonate        =   " . $formVars['rgt_impersonate']        . "," . 
            "rgt_create             =   " . $formVars['rgt_create']             . "," . 
            "rgt_delete             =   " . $formVars['rgt_delete']             . "," . 
            "rgt_deletecollection   =   " . $formVars['rgt_deletecollection']   . "," . 
            "rgt_patch              =   " . $formVars['rgt_patch']              . "," . 
            "rgt_update             =   " . $formVars['rgt_update'];

          if ($formVars['update'] == 0) {
            $query = "insert into rights set rgt_id = NULL, " . $q_string;
            $message = "Rights added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update rights set " . $q_string . " where rgt_id = " . $formVars['id'];
            $message = "Rights updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rgt_id']);

          mysql_query($query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysql_error()));

#          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Rights Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('rights-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"rights-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Rights Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on Rights to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"60\">Del</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\" width=\"30\">Id</th>\n";
      $output .= "  <th class=\"ui-state-default\">Clusterrole</th>\n";
      $output .= "  <th class=\"ui-state-default\">apiGroups</th>\n";
      $output .= "  <th class=\"ui-state-default\">resources</th>\n";
      $output .= "  <th class=\"ui-state-default\">get</th>\n";
      $output .= "  <th class=\"ui-state-default\">list</th>\n";
      $output .= "  <th class=\"ui-state-default\">watch</th>\n";
      $output .= "  <th class=\"ui-state-default\">impersonate</th>\n";
      $output .= "  <th class=\"ui-state-default\">create</th>\n";
      $output .= "  <th class=\"ui-state-default\">delete</th>\n";
      $output .= "  <th class=\"ui-state-default\">deletecollection</th>\n";
      $output .= "  <th class=\"ui-state-default\">patch</th>\n";
      $output .= "  <th class=\"ui-state-default\">update</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select rgt_id,rgt_type,api_name,res_name,rgt_get,rgt_list,rgt_watch,rgt_impersonate,rgt_create,rgt_delete,rgt_deletecollection,rgt_patch,rgt_update ";
      $q_string .= "from rights ";
      $q_string .= "left join apigroups on apigroups.api_id = rights.rgt_apigroup ";
      $q_string .= "left join resources on resources.res_id = rights.rgt_resource ";
      $q_string .= "order by api_name,res_name ";
      $q_rights = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_rights) > 0) {
        while ($a_rights = mysql_fetch_array($q_rights)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('rights.fill.php?id=" . $a_rights['rgt_id'] . "');jQuery('#dialogRights').dialog('open');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('rights.del.php?id="  . $a_rights['rgt_id'] . "');\">";
          $linkend   = "</a>";

          if ($a_rights['rgt_type'] == 0) {
            $type = 'Admin';
            $admin_rgt_get              = ($a_rights['rgt_get'] ? "X" : " ");
            $admin_rgt_list             = ($a_rights['rgt_list'] ? "X" : " ");
            $admin_rgt_watch            = ($a_rights['rgt_watch'] ? "X" : " ");
            $admin_rgt_impersonate      = ($a_rights['rgt_impersonate'] ? "X" : " ");
            $admin_rgt_create           = ($a_rights['rgt_create'] ? "X" : " ");
            $admin_rgt_delete           = ($a_rights['rgt_delete'] ? "X" : " ");
            $admin_rgt_deletecollection = ($a_rights['rgt_deletecollection'] ? "X" : " ");
            $admin_rgt_patch            = ($a_rights['rgt_patch'] ? "X" : " ");
            $admin_rgt_update           = ($a_rights['rgt_update'] ? "X" : " ");
          }

          if ($a_rights['rgt_type'] == 1) {
            $type = 'Edit';
            $edit_rgt_get              = ($a_rights['rgt_get'] ? "X" : " ");
            $edit_rgt_list             = ($a_rights['rgt_list'] ? "X" : " ");
            $edit_rgt_watch            = ($a_rights['rgt_watch'] ? "X" : " ");
            $edit_rgt_impersonate      = ($a_rights['rgt_impersonate'] ? "X" : " ");
            $edit_rgt_create           = ($a_rights['rgt_create'] ? "X" : " ");
            $edit_rgt_delete           = ($a_rights['rgt_delete'] ? "X" : " ");
            $edit_rgt_deletecollection = ($a_rights['rgt_deletecollection'] ? "X" : " ");
            $edit_rgt_patch            = ($a_rights['rgt_patch'] ? "X" : " ");
            $edit_rgt_update           = ($a_rights['rgt_update'] ? "X" : " ");
          }

          if ($a_rights['rgt_type'] == 2) {
            $type = 'View';
            $view_rgt_get              = ($a_rights['rgt_get'] ? "X" : " ");
            $view_rgt_list             = ($a_rights['rgt_list'] ? "X" : " ");
            $view_rgt_watch            = ($a_rights['rgt_watch'] ? "X" : " ");
            $view_rgt_impersonate      = ($a_rights['rgt_impersonate'] ? "X" : " ");
            $view_rgt_create           = ($a_rights['rgt_create'] ? "X" : " ");
            $view_rgt_delete           = ($a_rights['rgt_delete'] ? "X" : " ");
            $view_rgt_deletecollection = ($a_rights['rgt_deletecollection'] ? "X" : " ");
            $view_rgt_patch            = ($a_rights['rgt_patch'] ? "X" : " ");
            $view_rgt_update           = ($a_rights['rgt_update'] ? "X" : " ");
          }

          $output .= "<tr>";
          if (check_userlevel($AL_Admin)) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
          }
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_rights['rgt_id']          . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_rights['api_name']        . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_rights['res_name']        . $linkend . "</td>";
          $output .= "  <td class=\"ui-widget-content\">"                       . $type                                   . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_get']              ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_list']             ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_watch']            ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_impersonate']      ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_create']           ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_delete']           ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_deletecollection'] ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_patch']            ? "X" : " ") . "</td>";
          $output .= "  <td class=\"ui-widget-content delete\">"                . ($a_rights['rgt_update']           ? "X" : " ") . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"13\">No records found.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysql_free_result($q_rights);

      print "document.getElementById('rights_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.rights.rgt_type['0'].checked = true;\n";
      print "document.rights.rgt_apigroup['0'].selected = true;\n";
      print "document.rights.rgt_resource['0'].selected = true;\n";
      print "document.rights.rgt_get.checked = false;\n";
      print "document.rights.rgt_list.checked = false;\n";
      print "document.rights.rgt_watch.checked = false;\n";
      print "document.rights.rgt_impersonate.checked = false;\n";
      print "document.rights.rgt_create.checked = false;\n";
      print "document.rights.rgt_delete.checked = false;\n";
      print "document.rights.rgt_deletecollection.checked = false;\n";
      print "document.rights.rgt_patch.checked = false;\n";
      print "document.rights.rgt_update.checked = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
