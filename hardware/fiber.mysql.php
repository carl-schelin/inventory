<?php
# Script: fiber.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "fiber.mysql.php";
    $formVars['update']         = clean($_GET['update'],       10);
    $formVars['sort']           = clean($_GET['sort'],         30);
    $formVars['csv']            = clean($_GET['csv'],          30);
    $formVars['view']           = clean($_GET['view'],         30);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sort'] == '') {
      $orderby = "order by ast_name,fib_name ";
    } else {
      $orderby = "order by " . $formVars['sort'] . ",fib_name ";
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
        $formVars['fib_deviceid']     = clean($_GET['fib_deviceid'],  10);
        $formVars['fib_name']         = clean($_GET['fib_name'],      60);
        $formVars['fib_type']         = clean($_GET['fib_type'],      10);
        $formVars['fib_active']       = clean($_GET['fib_active'],    10);
        $formVars['fib_desc']         = clean($_GET['fib_desc'],     100);
        $formVars['fib_office']       = clean($_GET['fib_office'],   100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['fib_active'] == 'true') {
          $formVars['fib_active'] = 1;
        } else {
          $formVars['fib_active'] = 0;
        }

        if (strlen($formVars['fib_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "fib_deviceid    =   " . $formVars['fib_deviceid']   . "," . 
            "fib_name        = \"" . $formVars['fib_name']       . "\"," .
            "fib_type        =   " . $formVars['fib_type']       . "," . 
            "fib_active      =   " . $formVars['fib_active']     . "," . 
            "fib_desc        = \"" . $formVars['fib_desc']       . "\"," .
            "fib_office      = \"" . $formVars['fib_office']     . "\"";

          if ($formVars['update'] == 0) {
            $q_string = "insert into inv_fiber set fib_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $q_string = "update inv_fiber set " . $q_string . " where fib_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['fib_name']);

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
        $output .= "\"Office\",";
        $output .= "\"Description\",";
        $output .= "\"Connection\"</br>\n";
      } else {
        $output  = "<table class=\"ui-styled-table\">";
        $output .= "<tr>";
        if (check_userlevel($db, $AL_Admin)) {
          $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Drop</th>";
        }
        $output .= "  <th class=\"ui-state-default\"><a href=\"fiber.php?sort=ast_name"   . "\">" . "Device</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"fiber.php?sort=fib_name"   . "\">" . "Drop Name</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"fiber.php?sort=port_text"  . "\">" . "Port Type</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"fiber.php?sort=fib_active" . "\">" . "Active</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"fiber.php?sort=fib_office" . "\">" . "Office</a></th>";
        $output .= "  <th class=\"ui-state-default\"><a href=\"fiber.php?sort=fib_desc"   . "\">" . "Description</a></th>";
        $output .= "  <th class=\"ui-state-default\">Connection</th>";
        $output .= "</tr>";
      }

      $q_string  = "select fib_id,fib_name,fib_active,fib_desc,fib_office,ft_name,ast_id,ast_name ";
      $q_string .= "from inv_fiber ";
      $q_string .= "left join inv_assets on inv_assets.ast_id = inv_fiber.fib_deviceid ";
      $q_string .= "left join inv_fibertype on inv_fibertype.ft_id = inv_fiber.fib_type ";
      $q_string .= $where;
      $q_string .= $orderby;
      $q_inv_fiber = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_fiber) > 0) {
        while ($a_inv_fiber = mysqli_fetch_array($q_inv_fiber)) {

          $class = "ui-widget-content";

          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('fiber.fill.php?id="     . $a_inv_fiber['fib_id']   . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('fiber.del.php?id=" . $a_inv_fiber['fib_id'] . "');\">";
          $linkend   = "</a>";
          $viewlink = "<a href=\"fiber.php?view=" . $a_inv_fiber['ast_id'] . "\">";
          $viewfilter = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/filter.webp\" height=\"10\">";

# see if we can find the target port, generally the fiber panel not not necessarily
# display is the device
          $active = 'No';
          $conlink = '';
          $fiber_name = "--";
          if ($a_inv_fiber['fib_active']) {
            $active = 'Yes';
            $class = "ui-state-highlight";

            $q_string  = "select con_sourceid ";
            $q_string .= "from inv_connect ";
            $q_string .= "left join inv_fibertype on inv_fibertype.ft_id = inv_connect.con_type ";
            $q_string .= "where con_targetid = " . $a_inv_fiber['fib_id'] . " and con_type = 2 ";
            $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_inv_connect) > 0) {
              $a_inv_connect = mysqli_fetch_array($q_inv_connect);

              $q_string  = "select ast_id,fib_name,ast_name ";
              $q_string .= "from inv_fiber ";
              $q_string .= "left join inv_assets on inv_assets.ast_id = inv_fiber.fib_deviceid ";
              $q_string .= "where fib_id = " . $a_inv_connect['con_sourceid'] . " ";
              $q_fiber = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_fiber) > 0) {
                $a_fiber = mysqli_fetch_array($q_fiber);
                $fiber_name = $a_fiber['ast_name'] . " - " . $a_fiber['fib_name'];
                $conlink = "<a href=\"fiber.php?view=" . $a_fiber['ast_id'] . "\">";
              }
            }

# in this case, it's the source device
            if ($fiber_name == '--') {
              $q_string  = "select con_targetid ";
              $q_string .= "from inv_connect ";
              $q_string .= "left join inv_powertype on inv_powertype.pt_id = inv_connect.con_type ";
              $q_string .= "where con_sourceid = " . $a_inv_fiber['fib_id'] . " and con_type = 2 ";
              $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_inv_connect) > 0) {
                $a_inv_connect = mysqli_fetch_array($q_inv_connect);

                $q_string  = "select ast_id,fib_name,ast_name ";
                $q_string .= "from inv_fiber ";
                $q_string .= "left join inv_assets on inv_assets.ast_id = inv_fiber.fib_deviceid ";
                $q_string .= "where fib_id = " . $a_inv_connect['con_targetid'] . " ";
                $q_fiber = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                if (mysqli_num_rows($q_fiber) > 0) {
                  $a_fiber = mysqli_fetch_array($q_fiber);
                  $fiber_name = $a_fiber['ast_name'] . " - " . $a_fiber['fib_name'];
                  $conlink = "<a href=\"fiber.php?view=" . $a_fiber['ast_id'] . "\">";
                }
              }
            }

          }

          if ($formVars['csv'] == 'true') {
            $output .= "\"" . $a_inv_fiber['ast_name'] . "\",";
            $output .= "\"" . $a_inv_fiber['fib_name'] . "\",";
            $output .= "\"" . $a_inv_fiber['ft_name'] . "\",";
            $output .= "\"" . $active . "\",";
            $output .= "\"" . $a_inv_fiber['fib_office'] . "\",";
            $output .= "\"" . $a_inv_fiber['fib_desc'] . "\",";
            $output .= "\"" . $fiber_name . "\"</br>\n";
          } else {
            $output .= "<tr>";
            if (check_userlevel($db, $AL_Admin)) {
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel . "</td>";
            }
            $output .= "  <td class=\"" . $class . "\">" . $viewlink . $viewfilter . $linkend . $linkstart . $a_inv_fiber['ast_name']  . $linkend . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_fiber['fib_name']             . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_fiber['ft_name']            . "</td>";
            $output .= "  <td class=\"" . $class . " delete\">"              . $active                              . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_fiber['fib_office']           . "</td>";
            $output .= "  <td class=\"" . $class . "\">"                     . $a_inv_fiber['fib_desc']             . "</td>";
            $output .= "  <td class=\"" . $class . " delete\">"              . $conlink . $fiber_name . $linkend                         . "</td>";
            $output .= "</tr>";
          }
        }
      } else {
        if ($formVars['csv'] == 'true') {
          $output .= "\"No Drops found\"</br>\n";
        } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"8\">No Drops found</td>";
          $output .= "</tr>";
        }
      }

      if ($formVars['csv'] == 'true') {
        $output .= "</br>\n";
      } else {
        $output .= "</table>";
      }

      mysqli_free_result($q_inv_fiber);

      print "document.getElementById('mysql_table').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
