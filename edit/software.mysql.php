<?php
# Script: software.mysql.php
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
    $package = "software.mysql.php";
    $formVars['update']          = clean($_GET['update'],           10);
    $formVars['sw_companyid']    = clean($_GET['sw_companyid'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sw_companyid'] == '') {
      $formVars['sw_companyid'] = 0;
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],               10);
        $formVars['sw_software']     = clean($_GET['sw_software'],     100);
        $formVars['sw_vendor']       = clean($_GET['sw_vendor'],       100);
        $formVars['sw_product']      = clean($_GET['sw_product'],       10);
        $formVars['sw_type']         = clean($_GET['sw_type'],          30);
        $formVars['sw_group']        = clean($_GET['sw_group'],         10);
        $formVars['sw_eol']          = clean($_GET['sw_eol'],           20);
        $formVars['sw_eolticket']    = clean($_GET['sw_eolticket'],     30);
        $formVars['sw_cert']         = clean($_GET['sw_cert'],          10);
        $formVars['sw_supportid']    = clean($_GET['sw_supportid'],     10);
        $formVars['sw_licenseid']    = clean($_GET['sw_licenseid'],     10);
        $formVars['sw_department']   = clean($_GET['sw_department'],    10);
        $formVars['sw_facing']       = clean($_GET['sw_facing'],        10);
        $formVars['sw_notification'] = clean($_GET['sw_notification'], 120);
        $formVars['sw_primary']      = clean($_GET['sw_primary'],       10);
        $formVars['sw_locked']       = clean($_GET['sw_locked'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['sw_product'] == '') {
          $formVars['sw_product'] = 0;
        }
        if ($formVars['sw_group'] == '') {
          $formVars['sw_group'] = 0;
        }
        if ($formVars['sw_facing'] == 'true') {
          $formVars['sw_facing'] = 1;
        } else {
          $formVars['sw_facing'] = 0;
        }
        if ($formVars['sw_primary'] == 'true') {
          $formVars['sw_primary'] = 1;
        } else {
          $formVars['sw_primary'] = 0;
        }
        if ($formVars['sw_locked'] == 'true') {
          $formVars['sw_locked'] = 1;
        } else {
          $formVars['sw_locked'] = 0;
        }

        if ($formVars['sw_companyid'] > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string = 
              "sw_companyid    =   " . $formVars['sw_companyid']    . "," . 
              "sw_software     = \"" . $formVars['sw_software']     . "\"," . 
              "sw_vendor       = \"" . $formVars['sw_vendor']       . "\"," . 
              "sw_product      =   " . $formVars['sw_product']      . "," . 
              "sw_type         = \"" . $formVars['sw_type']         . "\"," . 
              "sw_verified     =   " . "0"                          . "," . 
              "sw_user         =   " . $_SESSION['uid']             . "," . 
              "sw_update       = \"" . date('Y-m-d')                . "\"," . 
              "sw_group        =   " . $formVars['sw_group']        . "," . 
              "sw_eol          = \"" . $formVars['sw_eol']          . "\"," . 
              "sw_eolticket    = \"" . $formVars['sw_eolticket']    . "\"," . 
              "sw_cert         =   " . $formVars['sw_cert']         . "," .
              "sw_supportid    =   " . $formVars['sw_supportid']    . "," .
              "sw_licenseid    =   " . $formVars['sw_licenseid']    . "," . 
              "sw_department   =   " . $formVars['sw_department']   . "," . 
              "sw_facing       =   " . $formVars['sw_facing']       . "," .
              "sw_notification = \"" . $formVars['sw_notification'] . "\"," .
              "sw_primary      =   " . $formVars['sw_primary']      . "," . 
              "sw_locked       =   " . $formVars['sw_locked'];

          if ($formVars['update'] == 0) {
            $query = "insert into software set sw_id = NULL, " . $q_string;
            $message = "Software added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update software set " . $q_string . " where sw_id = " . $formVars['id'];
            $message = "Software updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sw_companyid']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      if ($formVars['update'] == -2) {
        $formVars['copyfrom']        = clean($_GET['copyfrom'],         10);

        if ($formVars['copyfrom'] > 0) {
          $q_string  = "select inv_appadmin ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_id = " . $formVars['sw_companyid'];
          $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_inventory = mysqli_fetch_array($q_inventory);

# so if you manage the system (inv_manager), copy everything. If you manage the applications (inv_appadmin), only copy the software managed by your group.
          $q_string  = "select sw_software,sw_vendor,sw_product,sw_type,sw_group,sw_eol,sw_cert,";
          $q_string .= "sw_licenseid,sw_department,sw_facing,sw_notification,sw_primary ";
          $q_string .= "from software ";
          $q_string .= "where sw_companyid = " . $formVars['copyfrom'];
# copy only stuff the app owner owns
          if ($_SESSION['group'] == $a_inventory['inv_appadmin']) {
            $q_string .= " and sw_group = " . $_SESSION['group'] . " ";
          }
          $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_software = mysqli_fetch_array($q_software)) {

            $q_string =
                "sw_companyid    =   " . $formVars['sw_companyid']      . "," . 
                "sw_software     = \"" . $a_software['sw_software']     . "\"," . 
                "sw_vendor       = \"" . $a_software['sw_vendor']       . "\"," . 
                "sw_product      =   " . $a_software['sw_product']      . "," . 
                "sw_type         = \"" . $a_software['sw_type']         . "\"," . 
                "sw_verified     =   " . "0"                            . "," . 
                "sw_group        =   " . $a_software['sw_group']        . "," . 
                "sw_eol          = \"" . $a_software['sw_eol']          . "\"," . 
                "sw_cert         =   " . $a_software['sw_cert']         . "," .
                "sw_licenseid    =   " . $a_software['sw_licenseid']    . "," . 
                "sw_department   =   " . $a_software['sw_department']   . "," . 
                "sw_facing       =   " . $a_software['sw_facing']       . "," .
                "sw_notification = \"" . $a_software['sw_notification'] . "\"," .
                "sw_primary      =   " . $a_software['sw_primary'];

            $query = "insert into software set sw_id = NULL, " . $q_string;
            mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
          }
        }
      }


      if ($formVars['update'] == -3) {
        logaccess($_SESSION['uid'], $package, "Creating the form for viewing.");

        $output  = "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"sw_refresh\" value=\"Refresh Software Listing\"    onClick=\"javascript:attach_software('software.mysql.php', -1);\">\n";
        $output .= "<input type=\"button\" name=\"sw_update\"  value=\"Update Software\"             onClick=\"javascript:attach_software('software.mysql.php', 1);hideDiv('software-hide');\">\n";
        $output .= "<input type=\"hidden\" name=\"sw_id\"      value=\"0\">\n";
        $output .= "<input type=\"button\" name=\"sw_addbtn\"  value=\"Add New Software\"            onClick=\"javascript:attach_software('software.mysql.php', 0);\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"button ui-widget-content\">\n";
        $output .= "<input type=\"button\" name=\"copyitem\" value=\"Copy Software Table From:\" onClick=\"javascript:attach_software('software.mysql.php', -2);\">\n";
        $output .= "<select name=\"sw_copyfrom\">\n";
        $output .= "<option value=\"0\">None</option>\n";

        $q_string  = "select inv_id,inv_name ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_status = 0 and (inv_manager = " . $_SESSION['group'] . " or inv_appadmin = " . $_SESSION['group'] . ") ";
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
        $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Software Form</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Group: <select name=\"sw_group\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select grp_id,grp_name ";
        $q_string .= "from groups ";
        $q_string .= "where grp_disabled = 0 ";
        $q_string .= "order by grp_name";
        $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_groups = mysqli_fetch_array($q_groups)) {
          if ($_SESSION['group'] == $a_groups['grp_id']) {
            $output .= "<option selected value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
          } else {
            $output .= "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
          }
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\" title=\"Software Title and Version Number\">Software <input type=\"text\" name=\"sw_software\" size=\"40\"></td>\n";
        $output .= "  <td class=\"ui-widget-content\" title=\"Yum Versionlock\">RHEL Yum Versionlocked? <input type=\"checkbox\" name=\"sw_locked\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Product <select name=\"sw_product\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select prod_id,prod_name ";
        $q_string .= "from products ";
        $q_string .= "where prod_id != 0 ";
        $q_string .= "order by prod_name";
        $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_products = mysqli_fetch_array($q_products)) {
          $output .= "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Vendor <input type=\"text\" name=\"sw_vendor\" size=\"40\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">License <select name=\"sw_licenseid\">\n";
        $output .= "<option value=\"0\">No License</option>\n";

        $q_string  = "select lic_id,lic_product,lic_quantity,lic_domain,prod_name ";
        $q_string .= "from licenses ";
        $q_string .= "left join products on products.prod_id = licenses.lic_project ";
        $q_string .= "order by prod_name,lic_key,lic_id";
        $q_licenses = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_licenses = mysqli_fetch_array($q_licenses)) {

          $q_string  = "select count(*) ";
          $q_string .= "from software ";
          $q_string .= "where sw_licenseid = " . $a_licenses['lic_id'];
          $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_software = mysqli_fetch_array($q_software);

          $available = $a_licenses['lic_quantity'] - $a_software['count(*)'];

          $output .= "<option value=\"" . $a_licenses['lic_id'] . "\">" . $a_licenses['prod_name'] . "-" . $a_licenses['lic_product'] . " " . $a_licenses['lic_domain'] . " (" . $available . " available)</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Type <input type=\"text\" name=\"sw_type\" size=\"40\"></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">SSL Certificate <select name=\"sw_cert\">\n";
        $output .= "<option value=\"0\">No Certificate</option>\n";

        $q_string  = "select cert_id,cert_desc ";
        $q_string .= "from certs ";
        $q_string .= "where cert_ca = 0 ";
        $q_string .= "order by cert_desc";
        $q_certs = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_certs = mysqli_fetch_array($q_certs)) {
          $output .= "<option value=\"" . $a_certs['cert_id'] . "\">" . $a_certs['cert_desc'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\" title=\"Support company and contract number\" colspan=\"2\">Support <select name=\"sw_supportid\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select sup_id,sup_company,sup_contract ";
        $q_string .= "from support ";
        $q_string .= "order by sup_company,sup_contract";
        $q_support = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while  ($a_support = mysqli_fetch_array($q_support)) {
          $output .= "<option value=\"" . $a_support['sup_id'] . "\">" . $a_support['sup_company'] . " (" . $a_support['sup_contract'] . ")</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Business Unit (Department) <select name=\"sw_department\">\n";
        $output .= "<option value=\"0\">Unassigned</option>\n";

        $q_string  = "select dep_id,dep_unit,dep_dept,dep_name,bus_name ";
        $q_string .= "from department ";
        $q_string .= "left join business_unit on business_unit.bus_unit = department.dep_unit ";
        $q_string .= "order by dep_unit,dep_name";
        $q_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_department = mysqli_fetch_array($q_department)) {
          $output .= "<option value=\"" . $a_department['dep_id'] . "\">" . $a_department['dep_unit'] . "-" . $a_department['dep_dept'] . " - " . $a_department['bus_name'] . "-" . $a_department['dep_name'] . "</option>\n";
        }

        $output .= "</select></td>\n";
        $output .= "  <td class=\"ui-widget-content\">End of Life <input type=\"text\" name=\"sw_eol\" size=\"20\">\n";
        $output .= "  <td class=\"ui-widget-content\">EOL Ticket <input type=\"text\" name=\"sw_eolticket\" size=\"20\">\n";
        $output .= "</tr>\n";
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\"><label><input type=\"checkbox\" name=\"sw_primary\"> Software Defines The System?</label></td>\n";
        $output .= "  <td class=\"ui-widget-content\"><label><input type=\"checkbox\" name=\"sw_facing\"> Software is Customer Facing?</label></td>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"2\">Notification Requirements: <input type=\"text\" name=\"sw_notification\" size=\"30\"></td>\n";
        $output .= "</table>\n";

        print "document.getElementById('software_form').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Software Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('software-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"software-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Software Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Remove</strong> - Clicking the <strong>Remove</strong> button will delete this software package from this server.</li>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a piece of software to edit it.</li>\n";
      $output .= "    <li><span class=\"ui-state-highlight\">Highlighted</span> - Identifies software that defines or is the focus of this server.</li>\n";
      $output .= "    <li><span class=\"ui-state-error\">Highlighted</span> - Identifies software that is customer facing.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>This list does not include the operating system software and utilities installed by package managers.</li>\n";
      $output .= "    <li>Rows marked with a checkmark in the Updated column have been automatically captured where possible.</li>\n";
      $output .= "    <li>Click the <strong>Software Management</strong> title bar to toggle the <strong>Software Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .=   "<th class=\"ui-state-default\">Del</th>\n";
      $output .=   "<th class=\"ui-state-default\">Product</th>\n";
      $output .=   "<th class=\"ui-state-default\">Vendor</th>\n";
      $output .=   "<th class=\"ui-state-default\">Software</th>\n";
      $output .=   "<th class=\"ui-state-default\">Locked</th>\n";
      $output .=   "<th class=\"ui-state-default\">Type</th>\n";
      $output .=   "<th class=\"ui-state-default\">Group</th>\n";
      $output .=   "<th class=\"ui-state-default\">Updated</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sw_id,sw_software,sw_vendor,sw_product,sw_type,sw_verified,sw_update,inv_manager,sw_group,sw_facing,grp_name,prod_name,sw_primary,sw_locked ";
      $q_string .= "from software ";
      $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
      $q_string .= "left join groups on groups.grp_id = software.sw_group ";
      $q_string .= "left join products on products.prod_id = software.sw_product ";
      $q_string .= "where sw_companyid = " . $formVars['sw_companyid'] . " ";
      $q_string .= "order by sw_software ";
      $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_software = mysqli_fetch_array($q_software)) {

        if (check_grouplevel($a_software['inv_manager']) || check_grouplevel($a_software['sw_group'])) {
          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('software.fill.php?id="      . $a_software['sw_id'] . "');showDiv('software-hide');\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onClick=\"javascript:delete_software('software.del.php?id=" . $a_software['sw_id'] . "');\">";
          $linkend   = "</a>";
        } else {
          $linkstart = "";
          $linkdel   = "--";
          $linkend   = "";
        }

        $class = "ui-widget-content";
        if ($a_software['sw_primary']) {
          $class = "ui-state-highlight";
        }
        if ($a_software['sw_facing']) {
          $class = "ui-state-error";
        }

        $checked = "";
        if ($a_software['sw_verified']) {
          $checked = "&#x2713;";
        }

        $locked = "No";
        if ($a_software['sw_locked']) {
          $locked = "Yes";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">"           . $linkdel                                                                   . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_software['prod_name']   . $linkend            . $strongend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_software['sw_vendor']   . $linkend            . $strongend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_software['sw_software'] . $linkend            . $strongend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $locked                    . $linkend            . $strongend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_software['sw_type']     . $linkend            . $strongend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_software['grp_name']    . $linkend            . $strongend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $strong . $linkstart . $a_software['sw_update']   . $linkend . $checked . $strongend . "</td>";
        $output .= "</tr>";

      }

      $output .= "</table>";

      mysqli_free_result($q_software);

      print "document.getElementById('software_table').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.edit.sw_update.disabled = true;\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
