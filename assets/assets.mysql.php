<?php
# Script: assets.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "assets.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);
    $formVars['sort']           = clean($_GET['sort'],         30);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sort'] == '') {
      $orderby = "order by ast_asset,ast_facing,mod_name ";
    } else {
      $orderby = "order by " . $formVars['sort'] . " ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                 = clean($_GET['id'],               10);
        $formVars['ast_name']           = clean($_GET['ast_name'],        100);
        $formVars['ast_asset']          = clean($_GET['ast_asset'],        10);
        $formVars['ast_serial']         = clean($_GET['ast_serial'],      100);
        $formVars['ast_parentid']       = clean($_GET['ast_parentid'],    100);
        $formVars['ast_modelid']        = clean($_GET['ast_modelid'],      20);
        $formVars['ast_unit']           = clean($_GET['ast_unit'],         10);
        $formVars['ast_vendor']         = clean($_GET['ast_vendor'],       10);
        $formVars['ast_managed']        = clean($_GET['ast_managed'],      10);
        $formVars['ast_endsupport']     = clean($_GET['ast_endsupport'],   20);
        $formVars['ast_facing']         = clean($_GET['ast_facing'],       10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['ast_parentid'] == '') {
          $formVars['ast_parentid'] = "0";
        }
        if ($formVars['ast_modelid'] == '') {
          $formVars['ast_modelid'] = "0";
        }
        if ($formVars['ast_unit'] == '') {
          $formVars['ast_unit'] = "0";
        }
        if ($formVars['ast_facing'] == "true") {
          $formVars['ast_facing'] = 1;
        } else {
          $formVars['ast_facing'] = 0;
        }
        if ($formVars['ast_vendor'] == "true") {
          $formVars['ast_vendor'] = 1;
        } else {
          $formVars['ast_vendor'] = 0;
        }
        if ($formVars['ast_managed'] == "true") {
          $formVars['ast_managed'] = 1;
        } else {
          $formVars['ast_managed'] = 0;
        }
        if ($formVars['ast_endsupport'] == '') {
          $formVars['ast_endsupport'] = '1971-01-01';
        }

        if (strlen($formVars['ast_modelid']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "ast_name        = \"" . $formVars['ast_name']        . "\"," .
            "ast_asset       = \"" . $formVars['ast_asset']       . "\"," .
            "ast_serial      = \"" . $formVars['ast_serial']      . "\"," .
            "ast_parentid    =   " . $formVars['ast_parentid']    . "," .
            "ast_modelid     =   " . $formVars['ast_modelid']     . "," .
            "ast_unit        =   " . $formVars['ast_unit']        . "," . 
            "ast_vendor      =   " . $formVars['ast_vendor']      . "," . 
            "ast_managed     =   " . $formVars['ast_managed']     . "," .
            "ast_endsupport  = \"" . $formVars['ast_endsupport']  . "\"," .
            "ast_facing      =   " . $formVars['ast_facing'];

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_assets set ast_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_assets set " . $q_string . " where ast_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ast_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Asset</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\"><a href=\"assets.php?sort=ast_name"   . "\">" . "Name or Label" . "</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"assets.php?sort=mod_name"   . "\">" . "Device"        . "</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"assets.php?sort=ast_unit"   . "\">" . "Starting Unit" . "</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"assets.php?sort=ast_facing" . "\">" . "Facing"        . "</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"assets.php?sort=ast_asset"  . "\">" . "Asset Tag"     . "</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"assets.php?sort=ast_serial" . "\">" . "Serial Number" . "</a></th>\n";
      $output .= "  <th class=\"ui-state-default\">Managed</th>\n";
      $output .= "  <th class=\"ui-state-default\">Vendor</th>\n";
      $output .= "  <th class=\"ui-state-default\">End of Support</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select ast_id,ast_name,ast_asset,ast_serial,ast_unit,ast_vendor,ast_managed,ast_endsupport,ast_facing,mod_name,ven_name ";
      $q_string .= "from inv_assets ";
      $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
      $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
      $q_string .= "where ast_parentid = 0 ";
      $q_string .= $orderby;
      $q_inv_assets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_assets) > 0) {
        while ($a_inv_assets = mysqli_fetch_array($q_inv_assets)) {

          $class = "ui-widget-content";
          if ($a_inv_assets['ast_endsupport'] != "1971-01-01") {
            $givenDate = new DateTime($a_inv_assets['ast_endsupport']);
            $today = new DateTime();
            if ($givenDate < $today) {
              $class = "ui-state-error";
            }
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('assets.fill.php?id="  . $a_inv_assets['ast_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('assets.del.php?id=" . $a_inv_assets['ast_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;

          $totallink = '';
          if ($total > 0) {
            $totallink = "<a href=\"assets.members.php?id=" . $formVars['ast_id'] . "\" target=\"_blank\">";
          }

          $unit = $a_inv_assets['ast_unit'] . "U";
          if ($a_inv_assets['ast_unit'] == 0) {
            $unit = "--";
            $facing = "--";
          } else {
            $facing = "Rear";
            if ($a_inv_assets['ast_facing'] == 1) {
              $facing = "Front";
            }
          }

          $vendor = "No";
          if ($a_inv_assets['ast_vendor'] == 1) {
            $vendor = "Yes";
          }
          $managed = "No";
          if ($a_inv_assets['ast_managed'] == 1) {
            $managed = "Yes";
          }

          $output .= "<tr>";
          if (check_userlevel($db, $AL_Admin)) {
            if ($total == 0) {
              $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
            } else {
              $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_assets['ast_name']                                   . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_inv_assets['ven_name'] . " " . $a_inv_assets['mod_name'] . $linkend . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $unit                                                                  . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $facing                                                                . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_assets['ast_asset']                                             . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_assets['ast_serial']                                            . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $managed                                                               . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $vendor                                                                . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">"              . $a_inv_assets['ast_endsupport']                                        . "</td>";
          $output .= "  <td class=\"" . $class . " delete\">" . $totallink . $total                                                      . $linkend . "</td>";
          $output .= "</tr>";

####
# Any children?
####
          $q_string  = "select ast_id,ast_name,ast_asset,ast_serial,ast_unit,ast_managed,ast_vendor,ast_endsupport,ast_facing,mod_name,ven_name ";
          $q_string .= "from inv_assets ";
          $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
          $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
          $q_string .= "where ast_parentid = " . $a_inv_assets['ast_id'] . " ";
          $q_string .= "order by ast_unit,ast_name ";
          $q_child = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_child) > 0) {
            while ($a_child = mysqli_fetch_array($q_child)) {

              $class = "ui-widget-content";
              if ($a_child['ast_endsupport'] != "1971-01-01") {
                $givenDate = new DateTime($a_child['ast_endsupport']);
                $today = new DateTime();
                if ($givenDate < $today) {
                  $class = "ui-state-error";
                }
              }

              $linkstart = "<a href=\"#\" onclick=\"show_file('assets.fill.php?id="  . $a_child['ast_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
              $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('assets.del.php?id=" . $a_child['ast_id'] . "');\">";
              $linkend   = "</a>";

              $childtotal = 0;

              $totallink = '';
              if ($childtotal > 0) {
                $totallink = "<a href=\"assets.members.php?id=" . $formVars['ast_id'] . "\" target=\"_blank\">";
              }

              $unit = $a_child['ast_unit'] . "U";
              if ($a_child['ast_unit'] == 0) {
                $unit = "--";
                $facing = "--";
              } else {
                $facing = "Rear";
                if ($a_child['ast_facing'] == 1) {
                  $facing = "Front";
                }
              }

              $vendor = "No";
              if ($a_child['ast_vendor'] == 1) {
                $vendor = "Yes";
              }
              $managed = "No";
              if ($a_child['ast_managed'] == 1) {
                $managed = "Yes";
              }

              $output .= "<tr>";
              if (check_userlevel($db, $AL_Admin)) {
                if ($childtotal == 0) {
                  $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
                } else {
                  $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
                }
              }
              $output .= "  <td class=\"" . $class . "\">&gt; "   . $linkstart . $a_child['ast_name']                              . $linkend . "</td>";
              $output .= "  <td class=\"" . $class . "\">&gt; "   . $linkstart . $a_child['ven_name'] . " " . $a_child['mod_name'] . $linkend . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">"              . $unit                                                        . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">"              . $facing                                                      . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">"              . $a_child['ast_asset']                                        . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">"              . $a_child['ast_serial']                                       . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">"              . $managed                                                     . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">"              . $vendor                                                      . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">"              . $a_child['ast_endsupport']                                   . "</td>";
              $output .= "  <td class=\"" . $class . " delete\">" . $totallink . $childtotal                                       . $linkend . "</td>";
              $output .= "</tr>";

#####
## Any Grandchildren
#####
              $q_string  = "select ast_id,ast_name,ast_asset,ast_serial,ast_unit,ast_managed,ast_vendor,ast_endsupport,ast_facing,mod_name,ven_name ";
              $q_string .= "from inv_assets ";
              $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
              $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
              $q_string .= "where ast_parentid = " . $a_child['ast_id'] . " ";
              $q_string .= "order by ast_unit,ast_name ";
              $q_grandchild = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_grandchild) > 0) {
                while ($a_grandchild = mysqli_fetch_array($q_grandchild)) {

                  $class = "ui-widget-content";
                  if ($a_grandchild['ast_endsupport'] != "1971-01-01") {
                    $givenDate = new DateTime($a_grandchild['ast_endsupport']);
                    $today = new DateTime();
                    if ($givenDate < $today) {
                      $class = "ui-state-error";
                    }
                  }

                  $linkstart = "<a href=\"#\" onclick=\"show_file('assets.fill.php?id="  . $a_grandchild['ast_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
                  $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('assets.del.php?id=" . $a_grandchild['ast_id'] . "');\">";
                  $linkend   = "</a>";

                  $grandchildtotal = 0;

                  $totallink = '';
                  if ($grandchildtotal > 0) {
                    $totallink = "<a href=\"assets.members.php?id=" . $formVars['ast_id'] . "\" target=\"_blank\">";
                  }

                  $unit = $a_grandchild['ast_unit'] . "U";
                  if ($a_grandchild['ast_unit'] == 0) {
                    $unit = "--";
                    $facing = "--";
                  } else {
                    $facing = "Rear";
                    if ($a_grandchild['ast_facing'] == 1) {
                      $facing = "Front";
                    }
                  }

                  $vendor = "No";
                  if ($a_grandchild['ast_vendor'] == 1) {
                    $vendor = "Yes";
                  }
                  $managed = "No";
                  if ($a_grandchild['ast_managed'] == 1) {
                    $managed = "Yes";
                  }

                  $output .= "<tr>";
                  if (check_userlevel($db, $AL_Admin)) {
                    if ($grandchildtotal == 0) {
                      $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
                    } else {
                      $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
                    }
                  }
                  $output .= "  <td class=\"" . $class . "\">&gt;&gt; " . $linkstart . $a_grandchild['ast_name']                                   . $linkend . "</td>";
                  $output .= "  <td class=\"" . $class . "\">&gt;&gt; " . $linkstart . $a_grandchild['ven_name'] . " " . $a_grandchild['mod_name'] . $linkend . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"                . $unit                                                                  . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"                . $facing                                                                . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"                . $a_grandchild['ast_asset']                                             . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"                . $a_grandchild['ast_serial']                                            . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"                . $managed                                                               . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"                . $vendor                                                                . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"                . $a_grandchild['ast_endsupport']                                        . "</td>";
                  $output .= "  <td class=\"" . $class . " delete\">"   . $totallink . $grandchildtotal                                            . $linkend . "</td>";
                  $output .= "</tr>";

#####
## Start of Greatgrandchildren
#####
                  $q_string  = "select ast_id,ast_name,ast_asset,ast_serial,ast_unit,ast_managed,ast_vendor,ast_endsupport,ast_facing,mod_name,ven_name ";
                  $q_string .= "from inv_assets ";
                  $q_string .= "left join inv_models on inv_models.mod_id = inv_assets.ast_modelid ";
                  $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
                  $q_string .= "where ast_parentid = " . $a_grandchild['ast_id'] . " ";
                  $q_string .= "order by ast_unit,ast_name ";
                  $q_greatgrand = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                  if (mysqli_num_rows($q_greatgrand) > 0) {
                    while ($a_greatgrand = mysqli_fetch_array($q_greatgrand)) {

                      $class = "ui-widget-content";
                      if ($a_greatgrand['ast_endsupport'] != "1971-01-01") {
                        $givenDate = new DateTime($a_greatgrand['ast_endsupport']);
                        $today = new DateTime();
                        if ($givenDate < $today) {
                          $class = "ui-state-error";
                        }
                      }

                      $linkstart = "<a href=\"#\" onclick=\"show_file('assets.fill.php?id="  . $a_greatgrand['ast_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
                      $linkdel   = "<input type=\"button\" value=\"Remove\"  onclick=\"delete_line('assets.del.php?id=" . $a_greatgrand['ast_id'] . "');\">";
                      $linkend   = "</a>";

                      $greatgrandtotal = 0;

                      $totallink = '';
                      if ($greatgrandtotal > 0) {
                        $totallink = "<a href=\"assets.members.php?id=" . $formVars['ast_id'] . "\" target=\"_blank\">";
                      }

                      $unit = $a_greatgrand['ast_unit'] . "U";
                      if ($a_greatgrand['ast_unit'] == 0) {
                        $unit = "--";
                        $facing = "--";
                      } else {
                        $facing = "Rear";
                        if ($a_greatgrand['ast_facing'] == 1) {
                          $facing = "Front";
                        }
                      }

                      $vendor = "No";
                      if ($a_greatgrand['ast_vendor'] == 1) {
                        $vendor = "Yes";
                      }
                      $managed = "No";
                      if ($a_greatgrand['ast_managed'] == 1) {
                        $managed = "Yes";
                      }

                      $output .= "<tr>";
                      if (check_userlevel($db, $AL_Admin)) {
                        if ($greatgrandtotal == 0) {
                          $output .= "  <td class=\"" . $class . " delete\">" . $linkdel . "</td>";
                        } else {
                          $output .= "  <td class=\"" . $class . " delete\">Members &gt; 0</td>";
                        }
                      }
                      $output .= "  <td class=\"" . $class . "\">&gt;&gt;&gt; " . $linkstart . $a_greatgrand['ast_name']                                   . $linkend . "</td>";
                      $output .= "  <td class=\"" . $class . "\">&gt;&gt;&gt; " . $linkstart . $a_greatgrand['ven_name'] . " " . $a_greatgrand['mod_name'] . $linkend . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"                    . $unit                                                                  . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"                    . $facing                                                                . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"                    . $a_greatgrand['ast_asset']                                             . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"                    . $a_greatgrand['ast_serial']                                            . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"                    . $managed                                                               . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"                    . $vendor                                                                . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"                    . $a_greatgrand['ast_endsupport']                                        . "</td>";
                      $output .= "  <td class=\"" . $class . " delete\">"       . $totallink . $greatgrandtotal                                            . $linkend . "</td>";
                      $output .= "</tr>";

                    }
                  }
####
# End of Greatgrandchildren
####

                }
              }
####
# End of Grandchildren
####

            }
          }
####
# End of primary children
####

        }

        $output .= "</table>";
      } else {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">No Assets found</td>";
        $output .= "</tr>";
      }

      mysqli_free_result($q_inv_assets);

      print "document.getElementById('mysql_table').innerHTML = '"    . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
