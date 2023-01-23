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
    $formVars['update']         = clean($_GET['update'],         10);
    $formVars['type']           = clean($_GET['type'],           40);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    $orderby = " order by ";
    if (isset($_GET['sort'])) {
      $formVars['sort'] = clean($_GET['sort'], 20);
      $orderby .= $formVars['sort'] . ",";
    }
    $orderby .= "sw_software ";

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']              = clean($_GET['id'],              10);
        $formVars['sw_software']     = clean($_GET['sw_software'],    255);
        $formVars['sw_vendor']       = clean($_GET['sw_vendor'],       10);
        $formVars['sw_product']      = clean($_GET['sw_product'],      10);
        $formVars['sw_licenseid']    = clean($_GET['sw_licenseid'],    10);
        $formVars['sw_supportid']    = clean($_GET['sw_supportid'],    10);
        $formVars['sw_type']         = clean($_GET['sw_type'],         10);
        $formVars['sw_department']   = clean($_GET['sw_department'],   10);
        $formVars['sw_tags']         = clean($_GET['sw_tags'],        255);
        $formVars['sw_eol']          = clean($_GET['sw_eol'],          15);
        $formVars['sw_eos']          = clean($_GET['sw_eos'],          15);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['sw_vendor'] == '') {
          $formVars['sw_vendor'] = 0;
        }
        if ($formVars['sw_product'] == '') {
          $formVars['sw_product'] = 0;
        }
        if ($formVars['sw_licenseid'] == '') {
          $formVars['sw_licenseid'] = 0;
        }
        if ($formVars['sw_supportid'] == '') {
          $formVars['sw_supportid'] = 0;
        }
        if ($formVars['sw_type'] == '') {
          $formVars['sw_type'] = 0;
        }
        if ($formVars['sw_department'] == '') {
          $formVars['sw_department'] = 0;
        }

        if (strlen($formVars['sw_software']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "sw_software     = \"" . $formVars['sw_software']       . "\"," . 
            "sw_vendor       =   " . $formVars['sw_vendor']         . "," . 
            "sw_product      =   " . $formVars['sw_product']        . "," . 
            "sw_licenseid    =   " . $formVars['sw_licenseid']      . "," . 
            "sw_supportid    =   " . $formVars['sw_supportid']      . "," . 
            "sw_type         =   " . $formVars['sw_type']           . "," .
            "sw_department   =   " . $formVars['sw_department']     . "," .
            "sw_eol          = \"" . $formVars['sw_eol']            . "\"," .
            "sw_eos          = \"" . $formVars['sw_eos']            . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_software set sw_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_software set " . $q_string . " where sw_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['sw_software']);

          mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
# need to get the new sw_id in case there is a tag too.
          if ($formVars['update'] == 0) {
            $formVars['id'] = last_insert_id($db);
          }

##################
# Tag Management
##################
#
# Step 1, remove all tags associated with this software. We only need to do this for
# software that are updates. New software will have all new tags.
          if ($formVars['update'] == 0 || $formVars['update'] == 1) {
            $q_string  = "delete ";
            $q_string .= "from inv_tags ";
            $q_string .= "where tag_type = 4 and tag_companyid = " . $formVars['id'] . " ";
            mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# Step 2, okay we've cleared all the tags from the tag system for this software.
# next is to parse the inputted data and create an array. remove any commas and duplicate spaces.
# as a note, the clean() function will remove any leading or trailing spaces so that
# prevents blank tags.
            $formVars['sw_tags'] = str_replace(',', ' ', $formVars['sw_tags']);
            $formVars['sw_tags'] = preg_replace('!\s+!', ' ', $formVars['sw_tags']);

# Step 3, now loop through the tags and add them to the tags table
            if (strlen($formVars['sw_tags']) > 0) {
              $sw_tags = explode(" ", $formVars['sw_tags']);
              for ($i = 0; $i < count($sw_tags); $i++) {

                $q_string =
                  "tag_companyid    =   " . $formVars['id'] . "," .
                  "tag_name         = \"" . $sw_tags[$i]  . "\"," .
                  "tag_type         =   " . "4"             . "," .
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

##############
### Now build the displayed table information
##############

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $linksort = "<a href=\"#\" onclick=\"javascript:show_file('license.mysql.php?update=-1";

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "<th class=\"ui-state-default\" width=\"160\">Delete Software</th>\n";
      }
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=sw_software');\">Software</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=ven_name');\">Vendor</a></th>\n";
      $output .= "<th class=\"ui-state-default\">"                                 . "Members</th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=prod_name');\">Product</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=lic_product');\">License</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=sup_company');\">Support</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=typ_name');\">Type</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=dep_name');\">Department</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=dep_name');\">Tags</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=sw_eol');\">EOL</a></th>\n";
      $output .= "<th class=\"ui-state-default\">" . $linksort . "&sort=sw_eos');\">EOL</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sw_id,sw_software,ven_name,prod_name,lic_product,sup_company,typ_name,";
      $q_string .= "dep_name,sw_eol,sw_eos ";
      $q_string .= "from inv_software ";
      $q_string .= "left join inv_vendors    on inv_vendors.ven_id        = inv_software.sw_vendor ";
      $q_string .= "left join inv_products   on inv_products.prod_id      = inv_software.sw_product ";
      $q_string .= "left join inv_licenses   on inv_licenses.lic_id       = inv_software.sw_licenseid ";
      $q_string .= "left join inv_support    on inv_support.sup_id        = inv_software.sw_supportid ";
      $q_string .= "left join inv_sw_types   on inv_sw_types.typ_id       = inv_software.sw_type ";
      $q_string .= "left join inv_department on inv_department.dep_id     = inv_software.sw_department ";
      if ($formVars['type'] != '') {
        $q_string .= "where typ_name = \"" . $formVars['type'] . "\" ";
      }
      $q_string .= $orderby;
      $q_inv_software = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_software) > 0) {
        while ($a_inv_software = mysqli_fetch_array($q_inv_software)) {

          $linkstart = "<a href=\"#\" onclick=\"show_file('software.fill.php?id="  . $a_inv_software['sw_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('software.del.php?id=" . $a_inv_software['sw_id'] . "');\">";
          $svrstart  = "<a href=\"servers.php?id=" . $a_inv_software['sw_id'] . "\" target=\"_blank\">";
          $linkend   = "</a>";

          $linktype = "<a href=\"software.php?type="  . $a_inv_software['typ_name'] . "\">";

          $sw_tags = '';
          $q_string  = "select tag_name ";
          $q_string .= "from inv_tags ";
          $q_string .= "where tag_companyid = " . $a_inv_software['sw_id'] . " and tag_type = 4 ";
          $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_tags) > 0) {
            while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {
              $sw_tags .= $a_inv_tags['tag_name'] . " ";
            }
          }

          $total = 0;
          $q_string  = "select svr_id ";
          $q_string .= "from inv_svr_software ";
          $q_string .= "where svr_softwareid = " . $a_inv_software['sw_id'] . " ";
          $q_inv_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_inv_svr_software) > 0) {
            while ($a_inv_svr_software = mysqli_fetch_array($q_inv_svr_software)) {
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
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['sw_software']   . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['ven_name']      . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content delete\">" . $svrstart  . $total                       . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['prod_name']     . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['lic_product']   . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['sup_company']   . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linktype  . $a_inv_software['typ_name']      . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['dep_name']      . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $sw_tags                     . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['sw_eol']        . $linkend . "</td>\n";
          $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_inv_software['sw_eos']        . $linkend . "</td>\n";
          $output .= "</tr>\n";
        }
      } else {
        $output .= "<tr>\n";
        $output .= "<td class=\"ui-widget-content\" colspan=\"12\">No Software to display.</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>";

      mysqli_free_result($q_inv_software);

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
