<?php
# Script: servers.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "servers.mysql.php";
    $formVars['update']          = clean($_GET['update'],      10);
    $formVars['projectid']       = clean($_GET['projectid'],   10);
    $formVars['productid']       = clean($_GET['productid'],   10);
    $formVars['myrsdp']          = clean($_GET['myrsdp'],      10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['projectid'] == '') {
      $formVars['projectid'] = 0;
    }
    if ($formVars['productid'] == '') {
      $formVars['productid'] = 0;
    }
    if ($formVars['myrsdp'] == '') {
      $formVars['myrsdp'] = 'yes';
    }

    if (check_userlevel($db, $AL_Edit)) {

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Server Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('server-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"server-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Project</strong> - The name of the Project.</li>\n";
      $output .= "  <li><strong>Product</strong> - The primary Product this Project is building servers for.</li>\n";
      $output .= "  <li><strong>Server</strong> - The server in work.</li>\n";
      $output .= "  <li><strong>Tasks Completed</strong> - How many of the tasks have been completed.</li>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <li><strong>Delete Server</strong> - This deletes all data for this server.</li>\n";
        $output .= "  <li><strong>Close Tasks</strong> - This marks the server build as complete, closing all tasks.</li>\n";
      }
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">RSDP ID</th>\n";
      $output .= "  <th class=\"ui-state-default\">Server</th>\n";
      $output .= "  <th class=\"ui-state-default\">Project</th>\n";
      $output .= "  <th class=\"ui-state-default\">Product</th>\n";
      $output .= "  <th class=\"ui-state-default\">Tasks Completed</th>\n";
#      $output .= "  <th class=\"ui-state-default\">Duplicate</th>\n";
      if (check_userlevel($db, $AL_Admin) || $a_rsdp_server['rsdp_requestor'] == $_SESSION['uid'] || $a_rsdp_server['rsdp_platformspoc'] == $_SESSION['uid']) {
        $output .= "  <th class=\"ui-state-default\">Complete</th>\n";
        $output .= "  <th class=\"ui-state-default\">Delete</th>\n";
      }
      $output .= "</tr>\n";

      $q_string  = "select rsdp_id,prod_id,rsdp_product,prod_name,os_sysname,prj_name,prj_code,rsdp_requestor,rsdp_platformspoc,";
      $q_string .= "rsdp_sanpoc,rsdp_networkpoc,rsdp_virtpoc,rsdp_dcpoc,rsdp_srpoc,rsdp_monitorpoc,rsdp_apppoc,rsdp_backuppoc ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join products on products.prod_id = rsdp_server.rsdp_product ";
      $q_string .= "left join projects on projects.prj_id = rsdp_server.rsdp_project ";
      $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
      if ($formVars['projectid'] > 0) {
        $q_string .= "where rsdp_project = " . $formVars['projectid'] . " ";
      } else {
        $q_string .= "where rsdp_product = " . $formVars['productid'] . " ";
      }
      if ($formVars['myrsdp'] == 'yes') {
        $q_string .= "and (";
        $q_string .= "rsdp_requestor    = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_platformspoc = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_sanpoc       = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_networkpoc   = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_virtpoc      = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_dcpoc        = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_srpoc        = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_monitorpoc   = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_apppoc       = " . $_SESSION['uid'] . " or ";
        $q_string .= "rsdp_backuppoc    = " . $_SESSION['uid'];
        $q_string .= ") ";
      }
      $q_string .= "group by os_sysname ";
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {
        $mystep = 0;

        if ($a_rsdp_server['rsdp_requestor'] == $_SESSION['uid'] || $a_rsdp_server['rsdp_platformspoc'] == $_SESSION['uid']) {
          $mystep = 1;
        }

        for ($i = 3; $i < 19; $i++) {
          $q_string  = "select st_step ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = " . $i;
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          if (mysqli_num_rows($q_rsdp_status) == 0) {
            if ($formVars['myrsdp'] == 'yes') {
              if ($i == 3 || $i == 11) {
                if ($a_rsdp_server['rsdp_sanpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
              if ($i == 4) {
                if ($a_rsdp_server['rsdp_networkpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
              if ($i == 5) {
                if ($a_rsdp_server['rsdp_virtpoc'] == $_SESSION['uid'] || $a_rsdp_server['rsdp_dcpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
              if ($i == 10 || $i == 12 || $i == 18) {
                if ($a_rsdp_server['rsdp_platformspoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
              if ($i == 13) {
                if ($a_rsdp_server['rsdp_backuppoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
              if ($i == 14 || $i == 16) {
                if ($a_rsdp_server['rsdp_monitorpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
              if ($i == 15 || $i == 17) {
                if ($a_rsdp_server['rsdp_apppoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
            } else {
              $mystep = 1;
            }
          }
        }

        $linkstart = "<a href=\"tasks.php?id=" . $a_rsdp_server['rsdp_id'] . "\">";
        $linkend = "</a>";

        $q_string  = "select st_step ";
        $q_string .= "from rsdp_status ";
        $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " and st_step = 18 ";
        $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if (mysqli_num_rows($q_rsdp_status) == 0 && $mystep == 1) {

          $linkstart = "<a href=\"tasks.php?id=" . $a_rsdp_server['rsdp_id'] . "&myrsdp=" . $formVars['myrsdp'] . "\">";
          $linkclose = "<a href=\"#\" onclick=\"duplicate_line('build/servers.done.php?id=" . $a_rsdp_server['rsdp_id'] . "');\">";
          $linkend   = "</a>";
          if (check_userlevel($db, $AL_Admin) || $a_rsdp_server['rsdp_requestor'] == $_SESSION['uid'] || $a_rsdp_server['rsdp_platformspoc'] == $_SESSION['uid']) {
            $linkdel   = "<a href=\"#\" onclick=\"delete_line('servers.del.php?id=" . $a_rsdp_server['rsdp_id'] . "');\">";
            $linkclose = "<a href=\"#\" onclick=\"close_line('servers.done.php?id=" . $a_rsdp_server['rsdp_id'] . "');\">";
          }

          if ($a_rsdp_server['prj_name'] == '') {
            $project = 'Unknown';
          } else {
            $project = $a_rsdp_server['prj_name'] . " (" . $a_rsdp_server['prj_code'] . ")";
          }
          if ($a_rsdp_server['prod_name'] == '') {
            $a_rsdp_server['prod_name'] = 'Unknown-' . $a_rsdp_server['rsdp_id'];
          }
          if ($a_rsdp_server['os_sysname'] == '') {
            $a_rsdp_server['os_sysname'] = 'New Server-' . $a_rsdp_server['rsdp_id'];
          }

          $q_string  = "select COUNT(*) ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server['rsdp_id'] . " ";
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkstart . $a_rsdp_server['rsdp_id']             . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_rsdp_server['os_sysname']          . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $project                              . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"          . $linkstart . $a_rsdp_server['prod_name']           . $linkend . "</td>\n";
          $output .= "  <td class=\"ui-widget-content\">"                       . $a_rsdp_status['COUNT(*)'] . " of 14"            . "</td>\n";
#          $output .= "  <td class=\"ui-widget-content delete\">"   . $linkdup   . "Duplicate Server"                    . $linkend . "</td>\n";
          if (check_userlevel($db, $AL_Admin) || $a_rsdp_server['rsdp_requestor'] == $_SESSION['uid'] || $a_rsdp_server['rsdp_platformspoc'] == $_SESSION['uid']) {
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkclose . "Close Tasks"                         . $linkend . "</td>\n";
            $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "Delete Server"                       . $linkend . "</td>\n";
          }
          $output .= "</tr>\n";
        }
      }

      $output .= "</table>\n";

      if ($formVars['projectid'] > 0) {
        $output .= "<p class=\"ui-widget-content\">Click <a href=\"network.php?projectid=" . $formVars['projectid'] . "\" target=\"_blank\">here</a> for a view of all servers for this project.</p>\n";
      } else {
        $output .= "<p class=\"ui-widget-content\">Click <a href=\"network.php?productid=" . $formVars['productid'] . "\" target=\"_blank\">here</a> for a view of all servers for this product.</p>\n";
      }

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
