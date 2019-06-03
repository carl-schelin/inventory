<?php
# Script: product.mysql.php
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
    $package = "product.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel(2)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],          10);
        $formVars['prod_name']    = clean($_GET['prod_name'],  100);
        $formVars['prod_code']    = strtoupper(clean($_GET['prod_code'],   2));
        $formVars['prod_oldcode'] = strtoupper(clean($_GET['prod_oldcode'],   10));
        $formVars['prod_desc']    = clean($_GET['prod_desc'],  100);
        $formVars['prod_group']   = clean($_GET['prod_group'],  10);
        $formVars['prod_type']    = clean($_GET['prod_type'],   30);
        $formVars['prod_citype']  = clean($_GET['prod_citype'], 30);
        $formVars['prod_tier1']   = clean($_GET['prod_tier1'],  30);
        $formVars['prod_tier2']   = clean($_GET['prod_tier2'],  30);
        $formVars['prod_tier3']   = clean($_GET['prod_tier3'],  30);
        $formVars['prod_remedy']  = clean($_GET['prod_remedy'], 10);
        $formVars['prod_unit']    = clean($_GET['prod_unit'],   10);
        $formVars['prod_service'] = clean($_GET['prod_service'],10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['prod_remedy'] == 'true') {
          $formVars['prod_remedy'] = 1;
        } else {
          $formVars['prod_remedy'] = 0;
        }

        $q_string  = "select prod_code ";
        $q_string .= "from products ";
        $q_string .= "where prod_code = \"" . $formVars['prod_code'] . "\" ";
        $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());

        if (mysql_num_rows($q_products) > 0 && $formVars['prod_code'] != '' && $formVars['update'] == 0) {
          print "alert(\"Product Code must be unique!\");\n";
        } else {
          if (strlen($formVars['prod_name']) > 0) {
            logaccess($_SESSION['uid'], $package, "Building the query.");

            $q_string =
              "prod_name      = \"" . $formVars['prod_name']      . "\"," .
              "prod_code      = \"" . $formVars['prod_code']      . "\"," .
              "prod_oldcode   = \"" . $formVars['prod_oldcode']   . "\"," .
              "prod_desc      = \"" . $formVars['prod_desc']      . "\"," .
              "prod_group     =   " . $formVars['prod_group']     . "," . 
              "prod_type      = \"" . $formVars['prod_type']      . "\"," .
              "prod_citype    = \"" . $formVars['prod_citype']    . "\"," .
              "prod_tier1     = \"" . $formVars['prod_tier1']     . "\"," .
              "prod_tier2     = \"" . $formVars['prod_tier2']     . "\"," .
              "prod_tier3     = \"" . $formVars['prod_tier3']     . "\"," .
              "prod_remedy    =   " . $formVars['prod_remedy']    . "," .
              "prod_unit      =   " . $formVars['prod_unit']      . "," .
              "prod_service   =   " . $formVars['prod_service'];

            if ($formVars['update'] == 0) {
              $query = "insert into products set prod_id = NULL, " . $q_string;
              $message = $Sitecompany . "Product added.";
            }
            if ($formVars['update'] == 1) {
              $query = "update products set " . $q_string . " where prod_id = " . $formVars['id'];
              $message = $Sitecompany . "Product updated.";
            }

            logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['prod_name']);

            mysql_query($query) or die($query . ": " . mysql_error());

            print "alert('" . $message . "');\n";
          } else {
            print "alert('You must input data before saving changes.');\n";
          }
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">" . $Sitecompany . "Product Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('product-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"product-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>" . $Sitecompany . "Product Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a " . $Sitecompany . "Product to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>" . $Sitecompany . "Product Management</strong> title bar to toggle the <strong>" . $Sitecompany . "Product Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">Product Name</th>";
      $output .= "  <th class=\"ui-state-default\">Product Code</th>";
      $output .= "  <th class=\"ui-state-default\">Old Code</th>";
      $output .= "  <th class=\"ui-state-default\">Product Description</th>";
      $output .= "  <th class=\"ui-state-default\">Group</th>";
      $output .= "  <th class=\"ui-state-default\">Business</th>";
      $output .= "  <th class=\"ui-state-default\">Svc Class</th>";
      $output .= "  <th class=\"ui-state-default\">Product Type</th>";
      $output .= "  <th class=\"ui-state-default\">CI Type</th>";
      $output .= "  <th class=\"ui-state-default\">Tier 1</th>";
      $output .= "  <th class=\"ui-state-default\">Tier 2</th>";
      $output .= "  <th class=\"ui-state-default\">Tier 3</th>";
      $output .= "</tr>";

      $q_string  = "select prod_id,prod_name,prod_code,prod_oldcode,prod_desc,grp_name,prod_type,prod_citype,prod_tier1,prod_tier2,prod_tier3,prod_remedy,bus_name,svc_acronym ";
      $q_string .= "from products ";
      $q_string .= "left join groups on groups.grp_id = products.prod_group ";
      $q_string .= "left join business_unit on business_unit.bus_id = products.prod_unit ";
      $q_string .= "left join service on service.svc_id = products.prod_service ";
      $q_string .= "order by prod_name ";
      $q_products = mysql_query($q_string) or die (mysql_error());
      while ($a_products = mysql_fetch_array($q_products)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('product.fill.php?id="  . $a_products['prod_id'] . "');jQuery('#dialogProduct').dialog('open');return false;\">";
        $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('product.del.php?id=" . $a_products['prod_id'] . "');\">";
        $linkend = "</a>";

        if ($a_products['prod_remedy']) {
          $class = "ui-state-highlight";
        } else {
          $class = "ui-widget-content";
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                              . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_name']      . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_code']      . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_oldcode']   . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_desc']      . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['grp_name']       . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['bus_name']       . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['svc_acronym']    . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_type']      . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_citype']    . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_tier1']     . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_tier2']     . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_tier3']     . $linkend . "</td>";
        $output .= "</tr>";

      }
      $output .= "</table>";

      mysql_free_result($q_products);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.products.prod_name.value = '';\n";
      print "document.products.prod_code.value = '';\n";
      print "document.products.prod_oldcode.value = '';\n";
      print "document.products.prod_desc.value = '';\n";
      print "document.products.prod_group['0'].selected = true;\n";
      print "document.products.prod_remedy.checked = false;\n";
      print "document.products.prod_unit['0'].selected = true;\n";
      print "document.products.prod_service['0'].selected = true;\n";
      print "document.products.prod_type.value = '';\n";
      print "document.products.prod_citype.value = '';\n";
      print "document.products.prod_tier1.value = '';\n";
      print "document.products.prod_tier2.value = '';\n";
      print "document.products.prod_tier3.value = '';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
