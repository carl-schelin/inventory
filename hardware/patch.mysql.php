<?php
# Script: patch.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "patch.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);
    $formVars['sort']           = clean($_GET['sort'],         30);
    $formVars['csv']            = clean($_GET['csv'],          30);
    $formVars['view']           = clean($_GET['view'],         30);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sort'] == '') {
      $orderby = "order by ast_name,port_text,pat_name ";
    } else {
      $orderby = "order by " . $formVars['sort'] . ",pat_name ";
    }
    if ($formVars['csv'] == '') {
      $formVars['csv'] = "false";
    }
    $where = "";
    if ($formVars['view'] != '') {
      $where = "where ast_id = " . $formVars['view'] . " ";
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],            10);
        $formVars['pat_deviceid']     = clean($_GET['pat_deviceid'],  10);
        $formVars['pat_name']         = clean($_GET['pat_name'],      60);
        $formVars['pat_type']         = clean($_GET['pat_type'],      10);
        $formVars['pat_active']       = clean($_GET['pat_active'],    10);
        $formVars['pat_desc']         = clean($_GET['pat_desc'],     100);
        $formVars['pat_office']       = clean($_GET['pat_office'],   100);
        $formVars['pat_verified']     = clean($_GET['pat_verified'],  10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['pat_active'] == 'true') {
          $formVars['pat_active'] = 1;
        } else {
          $formVars['pat_active'] = 0;
        }
        if ($formVars['pat_verified'] == 'true') {
          $formVars['pat_verified'] = 1;
        } else {
          $formVars['pat_verified'] = 0;
        }

        if (strlen($formVars['pat_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "pat_deviceid    =   " . $formVars['pat_deviceid']   . "," . 
            "pat_name        = \"" . $formVars['pat_name']       . "\"," .
            "pat_type        =   " . $formVars['pat_type']       . "," . 
            "pat_active      =   " . $formVars['pat_active']     . "," . 
            "pat_verified    =   " . $formVars['pat_verified']   . "," . 
            "pat_desc        = \"" . $formVars['pat_desc']       . "\"," .
            "pat_office      = \"" . $formVars['pat_office']     . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_patch set pat_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_patch set " . $q_string . " where pat_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['pat_name']);

          $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      if ($formVars['csv'] == 'true') {
        $output  = "\n";
        $output .= "\"Device\",";
        $output .= "\"Drop Name\",";
        $output .= "\"Port Type\",";
        $output .= "\"Active\",";
        $output .= "\"Verified\",";
        $output .= "\"Office\",";
        $output .= "\"Description\",";
        $output .= "\"Connection\"</br>\n";
      } else {
        $output  = "<table class=\"ui-styled-table\">";
        $output .= "<tr>";
        if (check_userlevel($db, $AL_Admin)) {
          $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Drop</th>";
        }
        $output .= "  <th class=\"ui-state-default\"><a href=\"patch.php?sort=ast_name"     . "\">" . "Device</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"patch.php?sort=pat_name"     . "\">" . "Drop Name</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"patch.php?sort=port_text"    . "\">" . "Port Type</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"patch.php?sort=pat_active"   . "\">" . "Active</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"patch.php?sort=pat_verified" . "\">" . "Verified</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"patch.php?sort=pat_office"   . "\">" . "Office</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"patch.php?sort=pat_desc"     . "\">" . "Description</a></th>";
        $output .= "  <th class=\"ui-state-default\">Connection</th>";
        $output .= "</tr>";
      }

      $q_string  = "select pat_id,pat_name,pat_active,pat_desc,pat_office,pat_verified,port_text,ast_id,ast_name ";
      $q_string .= "from inv_patch ";
      $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
      $q_string .= "left join inv_int_porttype on inv_int_porttype.port_id = inv_patch.pat_type ";
      $q_string .= $where;
      $q_string .= $orderby;
      $q_inv_patch = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_patch) > 0) {
        while ($a_inv_patch = mysqli_fetch_array($q_inv_patch)) {

          $class = $vclass = "ui-widget-content";

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('patch.fill.php?id="     . $a_inv_patch['pat_id']   . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('patch.del.php?id=" . $a_inv_patch['pat_id'] . "');\">";
          $linkend   = "</a>";
          $viewlink = "<a href=\"patch.php?view=" . $a_inv_patch['ast_id'] . "\">";
          $viewfilter = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/filter.webp\" height=\"10\">";

# see if we can find the target port, generally the patch panel not not necessarily
# display is the device
          $active = 'No';
          $conlink = '';
          $patch_name = "--";
          if ($a_inv_patch['pat_active']) {
            $active = 'Yes';
            $class = $vclass = "ui-state-highlight";

            $q_string  = "select con_sourceid ";
            $q_string .= "from inv_connect ";
            $q_string .= "left join inv_powertype on inv_powertype.pt_id = inv_connect.con_type ";
            $q_string .= "where con_targetid = " . $a_inv_patch['pat_id'] . " and con_type = 1 ";
            $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_inv_connect) > 0) {
              $a_inv_connect = mysqli_fetch_array($q_inv_connect);

              $q_string  = "select ast_id,pat_name,ast_name ";
              $q_string .= "from inv_patch ";
              $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
              $q_string .= "where pat_id = " . $a_inv_connect['con_sourceid'] . " ";
              $q_patch = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_patch) > 0) {
                $a_patch = mysqli_fetch_array($q_patch);
                $patch_name = $a_patch['ast_name'] . " - " . $a_patch['pat_name'];
                $conlink = "<a href=\"patch.php?view=" . $a_patch['ast_id'] . "\">";
              }
            }

# in this case, it's the source device
            if ($patch_name == '--') {
              $q_string  = "select con_targetid ";
              $q_string .= "from inv_connect ";
              $q_string .= "left join inv_powertype on inv_powertype.pt_id = inv_connect.con_type ";
              $q_string .= "where con_sourceid = " . $a_inv_patch['pat_id'] . " and con_type = 1 ";
              $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_inv_connect) > 0) {
                $a_inv_connect = mysqli_fetch_array($q_inv_connect);

                $q_string  = "select ast_id,pat_name,ast_name ";
                $q_string .= "from inv_patch ";
                $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
                $q_string .= "where pat_id = " . $a_inv_connect['con_targetid'] . " ";
                $q_patch = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                if (mysqli_num_rows($q_patch) > 0) {
                  $a_patch = mysqli_fetch_array($q_patch);
                  $patch_name = $a_patch['ast_name'] . " - " . $a_patch['pat_name'];
                  $conlink = "<a href=\"patch.php?view=" . $a_patch['ast_id'] . "\">";
                }
              }
            }

          }

          $verified = 'No';
          $vclass = "ui-state-error";
          if ($a_inv_patch['pat_verified']) {
            $verified = 'Yes';
            $vclass = $class;
          }

          if ($formVars['csv'] == 'true') {
            $output .= "\"" . $a_inv_patch['ast_name'] . "\",";
            $output .= "\"" . $a_inv_patch['pat_name'] . "\",";
            $output .= "\"" . $a_inv_patch['port_text'] . "\",";
            $output .= "\"" . $active . "\",";
            $output .= "\"" . $verified . "\",";
            $output .= "\"" . $a_inv_patch['pat_office'] . "\",";
            $output .= "\"" . $a_inv_patch['pat_desc'] . "\",";
            $output .= "\"" . $patch_name . "\"</br>\n";
          } else {
            $output .= "<tr>";
            if (check_userlevel($db, $AL_Admin)) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            }
            $output .= "  <td class=\"" . $class . "\">" . $viewlink . $viewfilter . $linkend . $linkstart . $a_inv_patch['ast_name']  . $linkend . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_patch['pat_name']             . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_patch['port_text']            . "</td>";
            $output .= "  <td class=\"" . $class . " delete\">"              . $active                              . "</td>";
            $output .= "  <td class=\"" . $vclass . " delete\">"              . $verified                            . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_patch['pat_office']           . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_patch['pat_desc']             . "</td>";
            $output .= "  <td class=\"" . $class . " delete\">"              . $conlink . $patch_name . $linkend                         . "</td>";
            $output .= "</tr>";
          }
        }
      } else {
        if ($formVars['csv'] == 'true') {
          $output .= "\"No Drops found\"</br>\n";
        } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No Drops found</td>";
          $output .= "</tr>";
        }
      }

      if ($formVars['csv'] == 'true') {
        $output .= "</br>\n";
      } else {
        $output .= "</table>";
      }

      mysqli_free_result($q_inv_patch);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
