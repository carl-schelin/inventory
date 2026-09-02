<?php
# Script: license.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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
    $orderby .= "ven_name,lic_product ";

    if (isset($_GET['product'])) {
      $formVars['product'] = clean($_GET['product'], 10);

      if ($formVars['product'] > 0) {
        $where = "where lic_parent = 0 and lic_id = " . $formVars['product'] . " ";
      } else {
        $where = "where lic_parent = 0 ";
      }
    } else {
      $where = "where lic_parent = 0 ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['lic_parent']     = clean($_GET['lic_parent'],     10);
        $formVars['lic_vendor']     = clean($_GET['lic_vendor'],     10);
        $formVars['lic_product']    = clean($_GET['lic_product'],   255);
        $formVars['lic_date']       = clean($_GET['lic_date'],       15);
        $formVars['lic_expire']     = clean($_GET['lic_expire'],     15);
        $formVars['lic_vendorpo']   = clean($_GET['lic_vendorpo'],   20);
        $formVars['lic_po']         = clean($_GET['lic_po'],         20);
        $formVars['lic_customer']   = clean($_GET['lic_customer'],   30);
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
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "lic_parent   =   " . $formVars['lic_parent']   . "," . 
            "lic_vendor   =   " . $formVars['lic_vendor']   . "," . 
            "lic_product  = \"" . $formVars['lic_product']  . "\"," . 
            "lic_date     = \"" . $formVars['lic_date']     . "\"," . 
            "lic_expire   = \"" . $formVars['lic_expire']   . "\"," . 
            "lic_vendorpo = \"" . $formVars['lic_vendorpo'] . "\"," . 
            "lic_po       = \"" . $formVars['lic_po']       . "\"," . 
            "lic_customer = \"" . $formVars['lic_customer'] . "\"," . 
            "lic_project  =   " . $formVars['lic_project']  . "," .
            "lic_quantity =   " . $formVars['lic_quantity'] . "," .
            "lic_key      = \"" . $formVars['lic_key']      . "\"," .
            "lic_serial   = \"" . $formVars['lic_serial']   . "\"," .
            "lic_domain   = \"" . $formVars['lic_domain']   . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_licenses set lic_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_licenses set " . $q_string . " where lic_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['lic_product']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

##############
### Now build the displayed table information
##############

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $linksort = "<a href=\"#\" onclick=\"javascript:show_file('license.mysql.php?update=-1";

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "<th class=\"ui-state-default\" width=\"160\">Delete License</th>\n";
      }
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=ven_name');\">Vendor</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_product');\">Product</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_date');\">Date Acquired</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_expire');\">Date Expires</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_vendorpo');\">Vendor PO</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_po');\">PO</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_customer');\">Company ID</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_project');\">Project</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_quantity');\">Quantity</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_key');\">License Key</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_serial');\">Serial No.</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_domain');\">Domain</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select lic_id,ven_name,lic_product,lic_date,lic_expire,lic_vendorpo,lic_po,";
      $q_string .= "lic_customer,prod_name,lic_quantity,lic_key,lic_serial,lic_domain ";
      $q_string .= "from inv_licenses ";
      $q_string .= "left join inv_products on inv_products.prod_id = inv_licenses.lic_project ";
      $q_string .= "left join inv_vendors  on inv_vendors.ven_id   = inv_licenses.lic_vendor ";
      $q_string .= $where;
      $q_string .= $orderby;
      $q_inv_licenses = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_licenses) > 0) {
        while ($a_inv_licenses = mysqli_fetch_array($q_inv_licenses)) {

          $linkme = "<a href=\"license.php?product=" . $a_inv_licenses['lic_id'] . "\">";
          $linkstart = "<a href=\"#\" onclick=\"show_file('license.fill.php?id="  . $a_inv_licenses['lic_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('license.del.php?id=" . $a_inv_licenses['lic_id'] . "');\">";
          $linkend   = "</a>";
          $viewfilter = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/filter.webp\" height=\"10\">";

          $class = "ui-widget-content";

          $output .= "<tr>\n";
          if (check_userlevel($db, $AL_Admin)) {
            $output .= "<td class=\"" . $class . " delete\">" . $linkdel . "</td>\n";
          }
          $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_inv_licenses['ven_name']     . $linkend . "</td>\n";
          $output .= "<td class=\"" . $class . "\">" . $viewfilter . $linkme    . $a_inv_licenses['lic_product']  . $linkend . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . "--"                                       . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . "--"                                       . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . $a_inv_licenses['lic_vendorpo']            . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . $a_inv_licenses['lic_po']                  . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . $a_inv_licenses['lic_customer']            . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . $a_inv_licenses['prod_name']               . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . " "                                        . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . $a_inv_licenses['lic_key']                 . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . $a_inv_licenses['lic_serial']              . "</td>\n";
          $output .= "<td class=\"" . $class . "\">"              . $a_inv_licenses['lic_domain']              . "</td>\n";
          $output .= "</tr>\n";


          if ($formVars['product'] > 0) {
            $q_string  = "select lic_id,ven_name,lic_product,lic_date,lic_expire,lic_vendorpo,lic_po,lic_customer,";
            $q_string .= "prod_name,lic_quantity,lic_key,lic_serial,lic_domain ";
            $q_string .= "from inv_licenses ";
            $q_string .= "left join inv_products on inv_products.prod_id = inv_licenses.lic_project ";
            $q_string .= "left join inv_vendors  on inv_vendors.ven_id   = inv_licenses.lic_vendor ";
            $q_string .= "where lic_parent = " . $a_inv_licenses['lic_id'] . " ";
            $q_string .= $orderby;
            $q_child = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
            if (mysqli_num_rows($q_child) > 0) {
              while ($a_child = mysqli_fetch_array($q_child)) {

                $linkstart = "<a href=\"#\" onclick=\"show_file('license.fill.php?id="  . $a_child['lic_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
                $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('license.del.php?id=" . $a_child['lic_id'] . "');\">";
                $linkend   = "</a>";

# if the expiration date -60 days in that window, change the color.
# let's do 90 days for highlight
                $date = new DateTime($a_child['lic_expire']);
                $days = 90;
                $interval = new DateInterval("P{$days}D");
                $date->sub($interval);
                $highlight_date = $date->format("Y-m-d");

# and 30 days for expiring soon!!!
                $date = new DateTime($a_child['lic_expire']);
                $days = 30;
                $interval = new DateInterval("P{$days}D");
                $date->sub($interval);
                $error_date = $date->format("Y-m-d");

                $today = date('Y-m-d');
                $class = "ui-widget-content";
                if ($today > $highlight_date) {
                  $class = "ui-state-highlight";
                }
                if ($today > $error_date) {
                  $class = "ui-state-error";
                }

                $output .= "<tr>\n";
                if (check_userlevel($db, $AL_Admin)) {
                  $output .= "<td class=\"" . $class . " delete\">" . $linkdel . "</td>\n";
                }
                $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_child['ven_name']     . $linkend . "</td>\n";
                $output .= "<td class=\"" . $class . "\">" . "&gt;"     . $a_child['lic_product']             . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_date']                . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_expire']              . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_vendorpo']            . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_po']                  . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_customer']            . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['prod_name']               . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_quantity']            . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_key']                 . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_serial']              . "</td>\n";
                $output .= "<td class=\"" . $class . "\">"              . $a_child['lic_domain']              . "</td>\n";
                $output .= "</tr>\n";
              }
            }
          }
        }

      } else {
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\" colspan=\"13\">No licenses to display.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_licenses);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
