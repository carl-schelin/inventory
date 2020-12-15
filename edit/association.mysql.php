<?php
# Script: association.mysql.php
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
    $package = "association.mysql.php";
    $formVars['update']          = clean($_GET['update'],        10);
    $formVars['clu_companyid']   = clean($_GET['clu_companyid'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['clu_companyid'] == '') {
      $formVars['clu_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],               10);
        $formVars['clu_association'] = clean($_GET['clu_association'],  10);
        $formVars['clu_notes']       = clean($_GET['clu_notes'],       255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['clu_companyid'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "clu_companyid   =   " . $formVars['clu_companyid']   . "," .
            "clu_association =   " . $formVars['clu_association'] . "," .
            "clu_notes       = \"" . $formVars['clu_notes']       . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into cluster set clu_id = NULL," . $q_string;
            $message = "Association added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update cluster set " . $q_string . " where clu_id = " . $formVars['id'];
            $message = "Association updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['clu_association']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -2) {
        $formVars['copyfrom']        = clean($_GET['copyfrom'],      10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select clu_association,clu_notes ";
          $q_string .= "from cluster ";
          $q_string .= "where clu_companyid = " . $formVars['copyfrom'];
          $q_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_cluster = mysqli_fetch_array($q_cluster)) {

            $q_string =
              "clu_companyid   =   " . $formVars['clu_companyid']    . "," .
              "clu_association =   " . $a_cluster['clu_association'] . "," .
              "clu_notes       = \"" . $a_cluster['clu_notes']       . "\"";
  
            $query = "insert into cluster set clu_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      if ($formVars['update'] == -3) {

        logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"clu_refresh\" value=\"Refresh Association Listing\" onClick=\"javascript:attach_association('association.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"clu_update\"  value=\"Update Association\"          onClick=\"javascript:attach_association('association.mysql.php', 1);hideDiv('assocation-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"clu_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"clu_addbtn\"  value=\"Add Association\"             onClick=\"javascript:attach_association('association.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\"  value=\"Copy Association Table From:\" onClick=\"javascript:attach_association('association.mysql.php', -2);\">\n";
        $output .= "<select name=\"clu_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
        $q_string .= "order by inv_name";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $q_string  = "select clu_id ";
          $q_string .= "from cluster ";
          $q_string .= "where clu_companyid = " . $a_inventory['inv_id'] . " ";
          $q_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $clu_total = mysqli_num_rows($q_cluster);

          if ($clu_total > 0) {
            $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . " (" . $clu_total . ")</option>\n";
          }
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">Association Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Associate With: <select name=\"clu_association\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 ";
        $q_string .= "order by inv_name ";
        $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $output .= "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">Notes <input type=\"text\" name=\"clu_notes\" size=\"60\"></td>\n";
        $output .= "</tr>\n";
        $output .= "</table>\n";

        print "document.getElementById('association_form').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" colspan=\"6\">Association Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('association-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"association-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Association Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Delete (x)</strong> - Clicking the <strong>x</strong> will delete this association from this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on an association to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Association Management</strong> title bar to toggle the <strong>Association Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Association</th>\n";
      $output .=   "<th class=\"ui-state-default\">Platform</th>\n";
      $output .=   "<th class=\"ui-state-default\">Notes</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select clu_id,clu_association,clu_notes,grp_name,inv_name ";
      $q_string .= "from cluster ";
      $q_string .= "left join inventory on inventory.inv_id = cluster.clu_companyid ";
      $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
      $q_string .= "where clu_companyid = " . $formVars['clu_companyid'] . " ";
      $q_string .= "order by inv_name,clu_association,clu_port";
      $q_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_cluster) > 0) {
        while ($a_cluster = mysqli_fetch_array($q_cluster)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('association.fill.php?id=" . $a_cluster['clu_id'] . "');showDiv('association-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_association('association.del.php?id="  . $a_cluster['clu_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                          . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_cluster['inv_name']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_cluster['grp_name']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_cluster['clu_notes']   . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No Associations created.</td>\n";
      }
      $output .= "</table>\n";

      mysqli_free_result($q_cluster);

      print "document.getElementById('association_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.edit.clu_update.disabled = true;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
