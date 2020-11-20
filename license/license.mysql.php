<?php
# Script: license.mysql.php
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
    $package = "license.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    $orderby = " order by ";
    if (isset($_GET['sort'])) {
      $formVars['sort'] = clean($_GET['sort'], 20);
      $orderby .= $formVars['sort'] . ",";
    }
    $orderby .= "lic_date";

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['lic_vendor']     = clean($_GET['lic_vendor'],     30);
        $formVars['lic_product']    = clean($_GET['lic_product'],    32);
        $formVars['lic_date']       = clean($_GET['lic_date'],       15);
        $formVars['lic_vendorpo']   = clean($_GET['lic_vendorpo'],   20);
        $formVars['lic_po']         = clean($_GET['lic_po'],         20);
        $formVars['lic_project']    = clean($_GET['lic_project'],    10);
        $formVars['lic_quantity']   = clean($_GET['lic_quantity'],   10);
        $formVars['lic_key']        = clean($_GET['lic_key'],       128);
        $formVars['lic_serial']     = clean($_GET['lic_serial'],    128);
        $formVars['lic_domain']     = clean($_GET['lic_domain'],     32);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['lic_project'] == '') {
          $formVars['lic_project'] = 0;
        }
        if ($formVars['lic_quantity'] == '') {
          $formVars['lic_quantity'] = 0;
        }

        if (strlen($formVars['lic_vendor']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "lic_vendor   = \"" . $formVars['lic_vendor']   . "\"," . 
            "lic_product  = \"" . $formVars['lic_product']  . "\"," . 
            "lic_date     = \"" . $formVars['lic_date']     . "\"," . 
            "lic_vendorpo = \"" . $formVars['lic_vendorpo'] . "\"," . 
            "lic_po       = \"" . $formVars['lic_po']       . "\"," . 
            "lic_project  =   " . $formVars['lic_project']  . "," .
            "lic_quantity =   " . $formVars['lic_quantity'] . "," .
            "lic_key      = \"" . $formVars['lic_key']      . "\"," .
            "lic_serial   = \"" . $formVars['lic_serial']   . "\"," .
            "lic_domain   = \"" . $formVars['lic_domain']   . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into licenses set lic_id = NULL, " . $q_string;
            $message = "License added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update licenses set " . $q_string . " where lic_id = " . $formVars['id'];
            $message = "License updated.";
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['lic_product']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

##############
### Now build the displayed table information
##############

      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $linksort = "<a href=\"#\" onclick=\"javascript:show_file('license.mysql.php?update=-1";

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">License Key Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('license-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"license-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>License Key Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a License to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>License Key Management</strong> title bar to toggle the <strong>License Key Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($AL_Admin)) {
        $output .= "<th class=\"ui-state-default\">Del</th>\n";
      }
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_vendor');\">Vendor</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_product');\">Product</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_date');\">Date Acquired</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_vendorpo');\">Vendor PO</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_po');\">PO</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_project');\">Project</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_quantity');\">Quantity</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_key');\">License Key</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_serial');\">Serial No.</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_domain');\">Domain</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select lic_id,lic_vendor,lic_product,lic_date,lic_vendorpo,lic_po,prod_name,lic_quantity,lic_key,lic_serial,lic_domain ";
      $q_string .= "from licenses ";
      $q_string .= "left join products on products.prod_id = licenses.lic_project ";
      $q_string .= $orderby;
      $q_licenses = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_licenses) > 0) {
        while ($a_licenses = mysqli_fetch_array($q_licenses)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('license.fill.php?id="  . $a_licenses['lic_id'] . "');showDiv('license-hide');\">";
          $linkdel   = "<a href=\"#\" onclick=\"delete_line('license.del.php?id=" . $a_licenses['lic_id'] . "');\">";
          $linkend   = "</a>";

          $output .= "<tr>\n";
          if (check_userlevel($AL_Admin)) {
            $output .= "<td class=\"ui-widget-content delete\">" . $linkdel   . "x"                         . $linkend . "</td>\n";
          }
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_vendor']   . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_product']  . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_date']     . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_vendorpo'] . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_po']       . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['prod_name']    . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_quantity'] . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_key']      . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_serial']   . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_licenses['lic_domain']   . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\" colspan=\"11\">No licenses to display.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_licenses);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

      print "document.license.update.disabled = true;\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
