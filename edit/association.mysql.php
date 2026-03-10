<?php
# Script: association.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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
        $formVars['clu_type']        = clean($_GET['clu_type'],         10);
        $formVars['clu_source']      = clean($_GET['clu_source'],      100);
        $formVars['clu_target']      = clean($_GET['clu_target'],      100);
        $formVars['clu_options']     = clean($_GET['clu_options'],      30);
        $formVars['clu_notes']       = clean($_GET['clu_notes'],       255);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['clu_association'] == '') {
          $formVars['clu_association'] = 0;
        }

        if ($formVars['clu_companyid'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "clu_companyid   =   " . $formVars['clu_companyid']   . "," .
            "clu_association =   " . $formVars['clu_association'] . "," .
            "clu_type        =   " . $formVars['clu_type']        . "," .
            "clu_source      = \"" . $formVars['clu_source']      . "\"," .
            "clu_target      = \"" . $formVars['clu_target']      . "\"," .
            "clu_options     = \"" . $formVars['clu_options']     . "\"," .
            "clu_notes       = \"" . $formVars['clu_notes']       . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into inv_cluster set clu_id = NULL," . $q_string;
            $message = "Association added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update inv_cluster set " . $q_string . " where clu_id = " . $formVars['id'];
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
          $q_string .= "from inv_cluster ";
          $q_string .= "where clu_companyid = " . $formVars['copyfrom'];
          $q_inv_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_inv_cluster = mysqli_fetch_array($q_inv_cluster)) {

            $q_string =
              "clu_companyid   =   " . $formVars['clu_companyid']    . "," .
              "clu_association =   " . $a_inv_cluster['clu_association'] . "," .
              "clu_notes       = \"" . $a_inv_cluster['clu_notes']       . "\"";
  
            $query = "insert into inv_cluster set clu_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Association</th>\n";
      $output .=   "<th class=\"ui-state-default\">Source Mount</th>\n";
      $output .=   "<th class=\"ui-state-default\">Target Mount</th>\n";
      $output .=   "<th class=\"ui-state-default\">Type</th>\n";
      $output .=   "<th class=\"ui-state-default\">Options</th>\n";
      $output .=   "<th class=\"ui-state-default\">Notes</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select clu_id,clu_association,clu_type,clu_source,clu_target,clu_options,clu_notes,grp_name,inv_name ";
      $q_string .= "from inv_cluster ";
      $q_string .= "left join inv_inventory on inv_inventory.inv_id = inv_cluster.clu_association ";
      $q_string .= "left join inv_groups    on inv_groups.grp_id    = inv_inventory.inv_manager ";
      $q_string .= "where clu_companyid = " . $formVars['clu_companyid'] . " ";
      $q_string .= "order by inv_name,clu_association,clu_port";
      $q_inv_cluster = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_cluster) > 0) {
        while ($a_inv_cluster = mysqli_fetch_array($q_inv_cluster)) {

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('association.fill.php?id=" . $a_inv_cluster['clu_id'] . "');jQuery('#dialogAssociationUpdate').dialog('open'); return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_association('association.del.php?id="  . $a_inv_cluster['clu_id'] . "');\">";
          $linkend   = "</a>";

          $type = "None";
          if ($a_inv_cluster['clu_type'] == 1) {
            $type = 'nfs';
          }
          if ($a_inv_cluster['clu_type'] == 2) {
            $type = 'Samba';
          }

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                          . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_cluster['inv_name']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_cluster['clu_source']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_cluster['clu_target']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $type    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_cluster['clu_options']    . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"        . $linkstart . $a_inv_cluster['clu_notes']   . $linkend . "</td>\n";
          $output .= "</tr>\n";

        }
      } else {
        $output .= "  <td class=\"ui-widget-content\" colspan=\"4\">No Associations created.</td>\n";
      }
      $output .= "</table>\n";

      mysqli_free_result($q_inv_cluster);

      print "document.getElementById('association_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

      print "document.edit.clu_update.disabled = true;\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
