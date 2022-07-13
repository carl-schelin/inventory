<?php
# Script: product.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],          10);
        $formVars['prod_name']    = clean($_GET['prod_name'],  100);
        $formVars['prod_code']    = strtoupper(clean($_GET['prod_code'],   2));
        $formVars['prod_desc']    = clean($_GET['prod_desc'],  100);
        $formVars['prod_tags']    = clean($_GET['prod_tags'],  255);
        $formVars['prod_unit']    = clean($_GET['prod_unit'],   10);
        $formVars['prod_service'] = clean($_GET['prod_service'],10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['prod_unit'] == '') {
          $formVars['prod_unit'] = 0;
        }

        $q_string  = "select prod_code ";
        $q_string .= "from products ";
        $q_string .= "where prod_code = \"" . $formVars['prod_code'] . "\" ";
        $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        if (mysqli_num_rows($q_products) > 0 && $formVars['prod_code'] != '' && $formVars['update'] == 0) {
          print "alert(\"Product Code must be unique!\");\n";
        } else {
          if (strlen($formVars['prod_name']) > 0) {
            logaccess($db, $_SESSION['uid'], $package, "Building the query.");

            $q_string =
              "prod_name      = \"" . $formVars['prod_name']      . "\"," .
              "prod_code      = \"" . $formVars['prod_code']      . "\"," .
              "prod_desc      = \"" . $formVars['prod_desc']      . "\"," .
              "prod_unit      =   " . $formVars['prod_unit']      . "," .
              "prod_service   =   " . $formVars['prod_service'];

            if ($formVars['update'] == 0) {
              $q_string = "insert into products set prod_id = NULL, " . $q_string;
            }
            if ($formVars['update'] == 1) {
              $q_string = "update products set " . $q_string . " where prod_id = " . $formVars['id'];
            }

            logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['prod_name']);

            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
# need to get the new prod_id in case there is a tag too.
            if ($formVars['update'] == 0) {
              $formVars['id'] = last_insert_id($db);
            }

##################
# Tag Management
##################
#
# Step 1, remove all tags associated with this product. We only need to do this for
# products that are updates. New products will have all new tags.
            if ($formVars['updatre'] == 0 || $formVars['update'] == 1) {
              $q_string  = "delete ";
              $q_string .= "from tags ";
              $q_string .= "where tag_type = 3 and tag_companyid = " . $formVars['id'] . " ";
              mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# Step 2, okay we've cleared all the tags from the tag system for this server.
# next is to parse the inputted data and create an array. remove any commas and duplicate spaces.
# as a note, the clean() function will remove any leading or trailing spaces so that
# prevents blank tags.
              $formVars['prod_tags'] = str_replace(',', ' ', $formVars['prod_tags']);
              $formVars['prod_tags'] = preg_replace('!\s+!', ' ', $formVars['prod_tags']);

# Step 3, now loop through the tags and add them to the tags table
              if (strlen($formVars['prod_tags']) > 0) {
                $prod_tags = explode(" ", $formVars['prod_tags']);
                for ($i = 0; $i < count($prod_tags); $i++) {

                  $q_string =
                    "tag_companyid    =   " . $formVars['id'] . "," .
                    "tag_name         = \"" . $prod_tags[$i]  . "\"," .
                    "tag_type         =   " . "3"             . "," .
                    "tag_owner        =   " . $_SESSION['uid'] . "," .
                    "tag_group        =   " . $_SESSION['group'];

                  $q_string = "insert into tags set tag_id = NULL, " . $q_string;
                  mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
                }
              }
            }

          } else {
            print "alert('You must input data before saving changes.');\n";
          }
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Product/Service</th>";
      }
      $output .= "  <th class=\"ui-state-default\">Product Code</th>";
      $output .= "  <th class=\"ui-state-default\">Product Name</th>";
      $output .= "  <th class=\"ui-state-default\">Product Description</th>";
      $output .= "  <th class=\"ui-state-default\">Product Tags</th>";
      $output .= "  <th class=\"ui-state-default\">Members</th>";
      $output .= "  <th class=\"ui-state-default\">Business</th>";
      $output .= "  <th class=\"ui-state-default\">Svc Class</th>";
      $output .= "</tr>";

      $q_string  = "select prod_id,prod_name,prod_code,prod_desc,bus_name,svc_acronym ";
      $q_string .= "from products ";
      $q_string .= "left join business on business.bus_id = products.prod_unit ";
      $q_string .= "left join service on service.svc_id = products.prod_service ";
      $q_string .= "order by prod_name ";
      $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_products) > 0) {
        while ($a_products = mysqli_fetch_array($q_products)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('product.fill.php?id="  . $a_products['prod_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('product.del.php?id=" . $a_products['prod_id'] . "');\">";
          $prodstart = "<a href=\"servers.php?id=" . $a_products['prod_id'] . "\" target=\"_blank\">";
          $linkend = "</a>";

          $prod_tags = '';
          $q_string  = "select tag_name ";
          $q_string .= "from tags ";
          $q_string .= "where tag_companyid = " . $a_products['prod_id'] . " and tag_type = 3 ";
          $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_tags) > 0) {
            while ($a_tags = mysqli_fetch_array($q_tags)) {
              $prod_tags .= $a_tags['tag_name'] . " ";
            }
          }

          $class = "ui-widget-content";

          $total = 0;
          $q_string  = "select inv_id ";
          $q_string .= "from inventory ";
          $q_string .= "where inv_product = " . $a_products['prod_id'] . " ";
          $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inventory) > 0) {
            while ($a_inventory = mysqli_fetch_array($q_inventory)) {
              $total++;
            }
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . "\">"                     . $a_products['prod_code']                 . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_name']      . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_products['prod_desc']                 . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $prod_tags                               . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">" . $prodstart . $total                        . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_products['bus_name']                  . "</td>";
          $output .= "  <td class=\"" . $class . "\">"                     . $a_products['svc_acronym']               . "</td>";
          $output .= "</tr>";
        }
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No POroducts Found</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      mysqli_free_result($q_products);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
