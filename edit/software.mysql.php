<?php
# Script: software.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "software.mysql.php";
    $formVars['update']          = clean($_GET['update'],           10);
    $formVars['svr_companyid']   = clean($_GET['svr_companyid'],    10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['svr_companyid'] == '') {
      $formVars['svr_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],              10);
        $formVars['svr_softwareid']   = clean($_GET['svr_softwareid'],  10);
        $formVars['svr_groupid']      = clean($_GET['svr_groupid'],     10);
        $formVars['svr_certid']       = clean($_GET['svr_certid'],      10);
        $formVars['svr_facing']       = clean($_GET['svr_facing'],      10);
        $formVars['svr_primary']      = clean($_GET['svr_primary'],     10);
        $formVars['svr_locked']       = clean($_GET['svr_locked'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['svr_softwareid'] == '') {
          $formVars['svr_softwareid'] = 0;
        }
        if ($formVars['svr_certid'] == '') {
          $formVars['svr_certid'] = 0;
        }
        if ($formVars['svr_groupid'] == '') {
          $formVars['svr_groupid'] = 0;
        }
        if ($formVars['svr_facing'] == 'true') {
          $formVars['svr_facing'] = 1;
        } else {
          $formVars['svr_facing'] = 0;
        }
        if ($formVars['svr_primary'] == 'true') {
          $formVars['svr_primary'] = 1;
        } else {
          $formVars['svr_primary'] = 0;
        }
        if ($formVars['svr_locked'] == 'true') {
          $formVars['svr_locked'] = 1;
        } else {
          $formVars['svr_locked'] = 0;
        }

        if ($formVars['svr_companyid'] > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string = 
              "svr_companyid    =   " . $formVars['svr_companyid']    . "," . 
              "svr_softwareid   =   " . $formVars['svr_softwareid']   . "," . 
              "svr_verified     =   " . "0"                           . "," . 
              "svr_userid       =   " . $_SESSION['uid']              . "," . 
              "svr_update       = \"" . date('Y-m-d')                 . "\"," . 
              "svr_groupid      =   " . $formVars['svr_groupid']      . "," . 
              "svr_certid       =   " . $formVars['svr_certid']       . "," .
              "svr_facing       =   " . $formVars['svr_facing']       . "," .
              "svr_primary      =   " . $formVars['svr_primary']      . "," . 
              "svr_locked       =   " . $formVars['svr_locked'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into svr_software set svr_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update svr_software set " . $q_string . " where svr_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['svr_companyid']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }



      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\" width=\"160\">Delete Software</th>\n";
      $output .=   "<th class=\"ui-state-default\">Product</th>\n";
      $output .=   "<th class=\"ui-state-default\">Vendor</th>\n";
      $output .=   "<th class=\"ui-state-default\">Software</th>\n";
      $output .=   "<th class=\"ui-state-default\">Locked</th>\n";
      $output .=   "<th class=\"ui-state-default\">Type</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .=   "<th class=\"ui-state-default\">Updated</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select svr_id,sw_software,ven_name,prod_name,typ_name,svr_verified,svr_update,inv_manager,svr_facing,svr_primary,svr_locked ";
      $q_string .= "from svr_software ";
      $q_string .= "left join inventory on inventory.inv_id = svr_software.svr_companyid ";
      $q_string .= "left join software on software.sw_id = svr_software.svr_softwareid ";
      $q_string .= "left join vendors on vendors.ven_id = software.sw_vendor ";
      $q_string .= "left join inv_sw_types on inv_sw_types.typ_id = software.sw_type ";
      $q_string .= "left join inv_groups on inv_groups.grp_id = svr_software.svr_groupid ";
      $q_string .= "left join products on products.prod_id = software.sw_product ";
      $q_string .= "where svr_companyid = " . $formVars['svr_companyid'] . " ";
      $q_string .= "order by sw_software ";
      $q_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_svr_software) > 0) {
        while ($a_svr_software = mysqli_fetch_array($q_svr_software)) {

          if (check_grouplevel($db, $a_svr_software['inv_manager']) || check_grouplevel($db, $a_svr_software['svr_groupid'])) {
            $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('software.fill.php?id=" . $a_svr_software['svr_id'] . "');jQuery('#dialogSoftwareUpdate').dialog('open');return false;\">";
            $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_software('software.del.php?id=" . $a_svr_software['svr_id'] . "');\">";
            $linkend   = "</a>";
          } else {
            $linkstart = "";
            $linkdel   = "--";
            $linkend   = "";
          }

          $class = "ui-widget-content";
          if ($a_svr_software['svr_primary']) {
            $class = "ui-state-highlight";
          }
          if ($a_svr_software['svr_facing']) {
            $class = "ui-state-error";
          }

          $checked = "";
          if ($a_svr_software['svr_verified']) {
            $checked = "&#x2713;";
          }

          $locked = "No";
          if ($a_svr_software['svr_locked']) {
            $locked = "Yes";
          }

          $output .= "<tr>";
          $output .= "  <td class=\"" . $class . " delete\">"           . $linkdel                                                                       . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_svr_software['prod_name']   . $linkend            . $strongend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_svr_software['ven_name']    . $linkend            . $strongend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_svr_software['sw_software'] . $linkend            . $strongend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $locked                        . $linkend            . $strongend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_svr_software['typ_name']    . $linkend            . $strongend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_svr_software['grp_name']    . $linkend            . $strongend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_svr_software['svr_update']  . $linkend . $checked . $strongend . "</td>";
          $output .= "</tr>";

        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No software assigned</td>";
        $output .= "</tr>";
      }
      $output .= "</table>";

      mysqli_free_result($q_software);

      print "document.getElementById('software_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
