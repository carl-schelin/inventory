<?php
# Script: search.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "search.php";
  $editpencil = "<img class=\"ui-icon-edit\" height=\"10\" src=\"" . $Imgsroot . "/pencil.gif\">";
  $formVars['search_by']   = clean($_GET['search_by'],    10);
  $formVars['search_for']  = clean($_GET['search_for'],  255);

  if (isset($_GET['sort'])) {
    $formVars['sort']        = clean($_GET['sort'],         20);
  } else {
    $formVars['sort'] = '';
  }

  if (isset($_GET['search_on'])) {
    $formVars['search_on']   = clean($_GET['search_on'],   255);
  } else {
    $formVars['search_on']   = '';
  }

  if (isset($_GET['retired'])) {
    $formVars['retired'] = clean($_GET['retired'], 10);
  } else {
    $formVars['retired'] = '';
  }

  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
  }

  if ($formVars['csv'] == 'true') {
    $formVars['csv'] = 1;
  } else {
    $formVars['csv'] = 0;
  }

  $wait = wait_Process('Please Wait...');

  if ($formVars['search_by'] == 1 || $formVars['search_by'] == 0) {
    print "document.getElementById('server_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }
  if ($formVars['search_by'] == 2 || $formVars['search_by'] == 0) {
    print "document.getElementById('address_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }
  if ($formVars['search_by'] == 3 || $formVars['search_by'] == 0) {
    print "document.getElementById('software_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }
  if ($formVars['search_by'] == 4 || $formVars['search_by'] == 0) {
    print "document.getElementById('hardware_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }
  if ($formVars['search_by'] == 5 || $formVars['search_by'] == 0) {
    print "document.getElementById('asset_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }
  if ($formVars['search_by'] == 6 || $formVars['search_by'] == 0) {
    print "document.getElementById('location_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }
  if ($formVars['search_by'] == 7 || $formVars['search_by'] == 0) {
    print "document.getElementById('user_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }
  if ($formVars['search_by'] == 8 || $formVars['search_by'] == 0) {
    print "document.getElementById('packages_search_mysql').innerHTML = '" . mysql_real_escape_string($wait) . "';\n\n";
  }

  if (strlen($formVars['search_for']) > 0) {
    logaccess($formVars['uid'], $package, "Search: " . $formVars['search_for']);

# clean up search_for replacing commas with spaces
    $formVars['search_for'] = str_replace(',', ' ', $formVars['search_for']);
# now replace duplicate spaces with a single space
    $formVars['search_for'] = preg_replace('!\s+!', ' ', $formVars['search_for']);

# server name or all - search the inventory and interface
    if ($formVars['search_by'] == 1 || $formVars['search_by'] == 0) {

      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by int_server ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "(" . 
              "   inv_name   like '%" . $search_for[$i] . "%' " . 
              "or int_server like '%" . $search_for[$i] . "%' " . 
              "or int_addr   like '%" . $search_for[$i] . "%' " . 
              "or int_eth    like '%" . $search_for[$i] . "%' " . 
              "or ct_city    like '%" . $search_for[$i] . "%' " .
              "or ct_state   like '%" . $search_for[$i] . "%' " .
            ") ";
            $or = 'or ';
          }
        } else {
          $search_on = "(" . 
            "   inv_name   like '%" . $formVars['search_for'] . "%' " . 
            "or int_server like '%" . $formVars['search_for'] . "%' " . 
            "or int_addr   like '%" . $formVars['search_for'] . "%' " . 
            "or int_eth    like '%" . $formVars['search_for'] . "%' " . 
            "or ct_city    like '%" . $formVars['search_for'] . "%' " .
            "or ct_state   like '%" . $formVars['search_for'] . "%' " .
          ") ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Server Name Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"Server Name\",";
        $output .= "\"Function\",";
        $output .= "\"Project\",";
        $output .= "\"Product\",";
        $output .= "\"Platform Managed By\",";
        $output .= "\"Applications Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=1&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=int_server');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_function');\">Function</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=prj_name');\">Project</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=prod_name');\">Product</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Platform Managed By</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_appadmin');\">Applications Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select int_server,int_companyid,int_addr,int_eth,itp_name,grp_name,IFNULL(inv_appadmin,0) as inv_appadmin,inv_status,inv_function,inv_project,prj_name,inv_product,prod_name ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join cities    on cities.ct_id     = locations.loc_city ";
      $q_string .= "left join states    on states.st_id     = locations.loc_state ";
      $q_string .= "left join inttype   on inttype.itp_id   = interface.int_type ";
      $q_string .= "left join products  on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects  on projects.prj_id  = inventory.inv_project ";
      $q_string .= "left join groups    on groups.grp_id    = inventory.inv_manager ";
      if ($formVars['retired'] == 'true') {
        $q_string .= "where " . $search_on . " ";
      } else {
        $q_string .= "where inv_status = 0 and " . $search_on . " ";
      }
      $q_string .= $orderby;
      $q_interface = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_interface) > 0) {
        while ($a_interface = mysql_fetch_array($q_interface)) {

          $q_string  = "select grp_name ";
          $q_string .= "from groups ";
          $q_string .= "where grp_id = " . $a_interface['inv_appadmin'] . " ";
          $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          $a_groups = mysql_fetch_array($q_groups);

          $editstart     = "<a href=\"" . $Editroot   . "/inventory.php?server=" . $a_interface['int_companyid'] . "\" target=\"_blank\">" . $editpencil . "</a> ";
          $linkstart     = "<a href=\"" . $Showroot   . "/inventory.php?server=" . $a_interface['int_companyid'] . "\" target=\"_blank\">";
          $linkprjstart  = "<a href=\"" . $Reportroot . "/show.project.php?id="  . $a_interface['inv_project']   . "\" target=\"_blank\">";
          $linkprodstart = "<a href=\"" . $Reportroot . "/show.product.php?id="  . $a_interface['inv_product']   . "\" target=\"_blank\">";
          $linkend       = "</a>";

          $class = "ui-widget-content";
          if ($a_interface['inv_status']) {
            $class = "ui-state-error";
          }

          if ($formVars['csv']) {
            $output .= "\"" . $a_interface['int_server']   . "\",";
            $output .= "\"" . $a_interface['inv_function'] . "\",";
            $output .= "\"" . $a_interface['prj_name']     . "\",";
            $output .= "\"" . $a_interface['prod_name']    . "\",";
            $output .= "\"" . $a_interface['grp_name']     . "\",";
            $output .= "\"" . $a_groups['grp_name']        . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">" . $editstart . $linkstart     . $a_interface['int_server']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $linknwstart   . $a_interface['inv_function'] . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $linkprjstart  . $a_interface['prj_name']     . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $linkprodstart . $a_interface['prod_name']    . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $linkstart     . $a_interface['grp_name']     . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"              . $linkstart     . $a_groups['grp_name']        . $linkend . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.<br>";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      }

# add in an RSDP search as well
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">RSDP Server Name Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"Server Name\",";
        $output .= "\"IP Address\",";
        $output .= "\"MAC\",";
        $output .= "\"Type\",";
        $output .= "\"Platform Managed By\",";
        $output .= "\"Applications Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $RSDProot . "/search.php?search_by=1&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=if_name');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=if_ip');\">IP Address</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=if_mac');\">MAC</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=itp_name');\">Type</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Platform Managed By</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=rsdp_application');\">Applications Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

# search for name in rsdp_osteam: os_sysname
# link ip address: if_name where if_rsdp = os_rsdp
# link group: grp_name, need rsdp_server for both rsdp_platform and rsdp_application
# get group information for application
# link to build/initial.php for servers
# link to network/ task for servers

      $q_string  = "select rsdp_id,if_ip,if_mac,if_name,itp_name,grp_name,rsdp_application ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "left join rsdp_server on rsdp_server.rsdp_id = rsdp_interface.if_rsdp ";
      $q_string .= "left join groups on groups.grp_id = rsdp_server.rsdp_platform ";
      $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
      $q_string .= "where if_name like \"%" . $formVars['search_for'] . "%\" or if_ip like \"%" . $formVars['search_for'] . "%\" ";
      $q_string .= "order by if_name ";
      $q_rsdp_interface = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_rsdp_interface) > 0) {
        while ($a_rsdp_interface = mysql_fetch_array($q_rsdp_interface)) {

          if ($a_rsdp_interface['rsdp_application'] > 0) {
            $q_string  = "select grp_name ";
            $q_string .= "from groups ";
            $q_string .= "where grp_id = " . $a_rsdp_interface['rsdp_application'] . " ";
            $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            $a_groups = mysql_fetch_array($q_groups);
          }

          $linkstart = "<a href=\"" . $RSDProot . "/build/initial.php?rsdp=" . $a_rsdp_interface['rsdp_id'] . "\" target=\"_blank\">";
          $linknwstart = "<a href=\"" . $RSDProot . "/network/network.php?rsdp=" . $a_rsdp_interface['rsdp_id'] . "\" target=\"_blank\">";
          $linkend   = "</a>";

          if ($formVars['csv']) {
            $output .= "\"" . $a_rsdp_interface['if_name']  . "\",";
            $output .= "\"" . $a_rsdp_interface['if_ip']    . "\",";
            $output .= "\"" . $a_rsdp_interface['if_mac']   . "\",";
            $output .= "\"" . $a_rsdp_interface['itp_name'] . "\",";
            $output .= "\"" . $a_rsdp_interface['grp_name'] . "\",";
            $output .= "\"" . $a_groups['grp_name']         . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $linkstart   . $a_rsdp_interface['if_name']  . $linkend . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $linknwstart . $a_rsdp_interface['if_ip']    . $linkend . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $linknwstart . $a_rsdp_interface['if_mac']   . $linkend . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $linknwstart . $a_rsdp_interface['itp_name'] . $linkend . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $linkstart   . $a_rsdp_interface['grp_name'] . $linkend . "</td>\n";
            $output .= "  <td class=\"ui-widget-content\">" . $linkstart   . $a_groups['grp_name']         . $linkend . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.<br>";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      }

      print "document.getElementById('server_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }

# IP address or all - search the inventory and interface
    if ($formVars['search_by'] == 2 || $formVars['search_by'] == 0) {

      $count = 0;
      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by int_addr ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_on = '';
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "(" . 
              "   inv_name    like '%" . $search_for[$i] . "%' " . 
              "or int_addr    like '%" . $search_for[$i] . "%' " . 
              "or int_eth     like '%" . $search_for[$i] . "%' " . 
              "or ct_city     like '%" . $search_for[$i] . "%' " .
              "or ct_state    like '%" . $search_for[$i] . "%' " .
            ") ";
            $or = 'or ';
          }
        } else {
          $search_on = "(" . 
            "   inv_name    like '%" . $formVars['search_for'] . "%' " . 
            "or int_eth     like '%" . $formVars['search_for'] . "%' " . 
            "or ct_city     like '%" . $formVars['search_for'] . "%' " .
            "or ct_state    like '%" . $formVars['search_for'] . "%' " .
          ") ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">IP Address Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"IP Address\",";
        $output .= "\"MAC\",";
        $output .= "\"Type\",";
        $output .= "\"Server Name\",";
        $output .= "\"Platform Managed By\",";
        $output .= "\"Applications Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=2&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=int_addr');\">IP Address</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=int_eth');\">MAC</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=itp_name');\">Type</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=int_server');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Platform Managed By</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_appadmin');\">Applications Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select int_server,int_companyid,int_addr,int_eth,itp_name,grp_name,IFNULL(inv_appadmin, 0) as inv_appadmin,inv_status ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join cities    on cities.ct_id     = locations.loc_city ";
      $q_string .= "left join states    on states.st_id     = locations.loc_state ";
      $q_string .= "left join inttype on inttype.itp_id = interface.int_type ";
      $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
      if ($formVars['retired'] == 'true') {
        $q_string .= "where " . $search_on . " ";
      } else {
        $q_string .= "where inv_status = 0 and " . $search_on . " ";
      }
      $q_string .= $orderby;
      $q_interface = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_interface) > 0) {
        while ($a_interface = mysql_fetch_array($q_interface)) {

          $q_string  = "select grp_name ";
          $q_string .= "from groups ";
          $q_string .= "where grp_id = " . $a_interface['inv_appadmin'] . " ";
          $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          $a_groups = mysql_fetch_array($q_groups);

          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_interface['int_companyid'] . "\" target=\"_blank\">";
          $linknwstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_interface['int_companyid'] . "#network\" target=\"_blank\">";
          $linkend   = "</a>";

          $class = "ui-widget-content";
          if ($a_interface['inv_status']) {
            $class = "ui-state-error";
          }

          if ($formVars['csv']) {
            $output .= "\"" . $a_interface['int_addr']   . "\",";
            $output .= "\"" . $a_interface['int_eth']    . "\",";
            $output .= "\"" . $a_interface['itp_name']   . "\",";
            $output .= "\"" . $a_interface['int_server'] . "\",";
            $output .= "\"" . $a_interface['grp_name']   . "\",";
            $output .= "\"" . $a_groups['grp_name']      . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linknwstart . $a_interface['int_addr']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linknwstart . $a_interface['int_eth']    . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linknwstart . $a_interface['itp_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkstart   . $a_interface['int_server'] . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkstart   . $a_interface['grp_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkstart   . $a_groups['grp_name']      . $linkend . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.</br>\n";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      }

      print "document.getElementById('address_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }

# software or all - search the software and inventory
    if ($formVars['search_by'] == 3 || $formVars['search_by'] == 0) {

      $count = 0;
      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by inv_name ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_on = '';
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "(" . 
              "   inv_name    like '%" . $search_for[$i] . "%' " . 
              "or sw_vendor   like '%" . $search_for[$i] . "%' " . 
              "or sw_software like '%" . $search_for[$i] . "%' " . 
              "or sw_type     like '%" . $search_for[$i] . "%' " .
              "or ct_city     like '%" . $search_for[$i] . "%' " .
              "or ct_state    like '%" . $search_for[$i] . "%' " .
            ") ";
            $or = 'or ';
          }
        } else {
          $search_on = "(" . 
            "   inv_name    like '%" . $formVars['search_for'] . "%' " . 
            "or sw_vendor   like '%" . $formVars['search_for'] . "%' " . 
            "or sw_software like '%" . $formVars['search_for'] . "%' " . 
            "or sw_type     like '%" . $formVars['search_for'] . "%' " .
            "or ct_city     like '%" . $formVars['search_for'] . "%' " .
            "or ct_state    like '%" . $formVars['search_for'] . "%' " .
          ") ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Software Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"Server Name\",";
        $output .= "\"Vendor\",";
        $output .= "\"Software\",";
        $output .= "\"Type\",";
        $output .= "\"Applications Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=3&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=sw_vendor');\">Vendor</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=sw_software');\">Software</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=sw_type');\">Type</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Applications Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select inv_name,sw_companyid,sw_software,sw_vendor,sw_type,grp_name,inv_status ";
      $q_string .= "from software ";
      $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join cities    on cities.ct_id     = locations.loc_city ";
      $q_string .= "left join states    on states.st_id     = locations.loc_state ";
      $q_string .= "left join groups on groups.grp_id = software.sw_group ";
      if ($formVars['retired'] == 'true') {
        $q_string .= "where " . $search_on . " ";
      } else {
        $q_string .= "where inv_status = 0 and " . $search_on . " ";
      }
      $q_string .= $orderby;
      $q_software = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_software) > 0) {
        while ($a_software = mysql_fetch_array($q_software)) {

          $linkswstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_software['sw_companyid'] . "#software\" target=\"_blank\">";
          $link_vendor = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=3&search_on=sw_vendor&search_for="   . $a_software['sw_vendor']   . "&retired=" . $formVars['retired'] . "');\">";
          $link_name   = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=3&search_on=sw_software&search_for=" . $a_software['sw_software'] . "&retired=" . $formVars['retired'] . "');\">";
          $link_type   = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=3&search_on=sw_type&search_for="     . $a_software['sw_type']     . "&retired=" . $formVars['retired'] . "');\">";
          $linkstart   = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_software['sw_companyid'] . "\" target=\"_blank\">";
          $linkend     = "</a>";

          $class = "ui-widget-content";
          if ($a_software['inv_status']) {
            $class = "ui-state-error";
          }

          if ($formVars['csv']) {
            $output .= "\"" . $a_software['inv_name']    . "\",";
            $output .= "\"" . $a_software['sw_vendor']   . "\",";
            $output .= "\"" . $a_software['sw_software'] . "\",";
            $output .= "\"" . $a_software['sw_type']     . "\",";
            $output .= "\"" . $a_software['grp_name']    . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkswstart . $a_software['inv_name']    . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_vendor . $a_software['sw_vendor']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_name   . $a_software['sw_software'] . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_type   . $a_software['sw_type']     . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                . $a_software['grp_name']               . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.</br>\n";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      }

      print "document.getElementById('software_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }

# hardware or all - search the hardware and inventory
    if ($formVars['search_by'] == 4 || $formVars['search_by'] == 0) {

      $count = 0;
      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by inv_name ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_on = '';
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "(" . 
              "   inv_name     like '%" . $search_for[$i] . "%' " . 
              "or mod_vendor   like '%" . $search_for[$i] . "%' " . 
              "or mod_name     like '%" . $search_for[$i] . "%' " . 
              "or part_name    like '%" . $search_for[$i] . "%' " .
              "or ct_city      like '%" . $search_for[$i] . "%' " .
              "or ct_state     like '%" . $search_for[$i] . "%' " .
            ") ";
            $or = 'or ';
          }
        } else {
          $search_on = "(" . 
            "   inv_name     like '%" . $formVars['search_for'] . "%' " . 
            "or mod_vendor   like '%" . $formVars['search_for'] . "%' " . 
            "or mod_name     like '%" . $formVars['search_for'] . "%' " . 
            "or part_name    like '%" . $formVars['search_for'] . "%' " .
            "or ct_city      like '%" . $formVars['search_for'] . "%' " .
            "or ct_state     like '%" . $formVars['search_for'] . "%' " .
          ") ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Hardware Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"Server Name\",";
        $output .= "\"Vendor\",";
        $output .= "\"Model\",";
        $output .= "\"Type\",";
        $output .= "\"Asset Tag\",";
        $output .= "\"Serial Number\",";
        $output .= "\"Platform Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=4&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=mod_vendor');\">Vendor</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=mod_name');\">Model</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=mod_type');\">Type</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=mod_type');\">Asset Tag</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=mod_type');\">Serial Number</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Platform Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select hw_companyid,inv_name,grp_name,mod_vendor,mod_name,part_name,mod_type,inv_status,hw_serial,hw_asset ";
      $q_string .= "from hardware ";
      $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join cities    on cities.ct_id     = locations.loc_city ";
      $q_string .= "left join states    on states.st_id     = locations.loc_state ";
      $q_string .= "left join groups on groups.grp_id = hardware.hw_group ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "left join parts on parts.part_id = models.mod_type ";
      if ($formVars['retired'] == 'true') {
        $q_string .= "where " . $search_on . " ";
      } else {
        $q_string .= "where inv_status = 0 and " . $search_on . " ";
      }
      $q_string .= $orderby;
      $q_hardware = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_hardware) > 0) {
        while ($a_hardware = mysql_fetch_array($q_hardware)) {

          $linkhwstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_hardware['hw_companyid'] . "#hardware\" target=\"_blank\">";
          $link_vendor = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=4&search_on=mod_vendor&search_for=" . $a_hardware['mod_vendor'] . "&retired=" . $formVars['retired'] . "');\">";
          $link_name   = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=4&search_on=mod_name&search_for="   . $a_hardware['mod_name']   . "&retired=" . $formVars['retired'] . "');\">";
          $link_type   = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=4&search_on=part_name&search_for="  . $a_hardware['part_name']  . "&retired=" . $formVars['retired'] . "');\">";
          $linkstart   = "<a href=\"" . $Showroot . "/inventory.php?server="   . $a_hardware['hw_companyid'] . "\" target=\"_blank\">";
          $linkend     = "</a>";

          $class = "ui-widget-content";
          if ($a_hardware['inv_status']) {
            $class = "ui-state-error";
          }

          if ($formVars['csv']) {
            $output .= "\"" . $a_hardware['inv_name']   . "\",";
            $output .= "\"" . $a_hardware['mod_vendor'] . "\",";
            $output .= "\"" . $a_hardware['mod_name']   . "\",";
            $output .= "\"" . $a_hardware['part_name']  . "\",";
            $output .= "\"" . $a_hardware['hw_asset']  . "\",";
            $output .= "\"" . $a_hardware['hw_serial']  . "\",";
            $output .= "\"" . $a_hardware['grp_name']   . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkhwstart . $a_hardware['inv_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_vendor . $a_hardware['mod_vendor'] . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_name   . $a_hardware['mod_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_type   . $a_hardware['part_name']  . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                . $a_hardware['hw_asset']              . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                . $a_hardware['hw_serial']             . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                . $a_hardware['grp_name']              . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.<br>\n";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      }

      print "document.getElementById('hardware_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }

# asset or all - search the hardware and inventory
    if ($formVars['search_by'] == 5 || $formVars['search_by'] == 0) {

      $count = 0;
      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by inv_name ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_on = '';
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "(" . 
              "   inv_name   like '%" . $search_for[$i] . "%' " . 
              "or hw_asset   like '%" . $search_for[$i] . "%' " . 
              "or hw_serial  like '%" . $search_for[$i] . "%' " . 
              "or ct_city    like '%" . $search_for[$i] . "%' " .
              "or ct_state   like '%" . $search_for[$i] . "%' " .
            ") ";
            $or = 'or ';
          }
        } else {
          $search_on = "(" . 
            "   inv_name   like '%" . $formVars['search_for'] . "%' " . 
            "or hw_asset   like '%" . $formVars['search_for'] . "%' " . 
            "or hw_serial  like '%" . $formVars['search_for'] . "%' " . 
            "or ct_city    like '%" . $formVars['search_for'] . "%' " .
            "or ct_state   like '%" . $formVars['search_for'] . "%' " .
          ") ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Asset Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"Server Name\",";
        $output .= "\"Asset Tag\",";
        $output .= "\"Serial Number\",";
        $output .= "\"Location\",";
        $output .= "\"Platform Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=5&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=hw_asset');\">Asset Tag</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=hw_serial');\">Serial Number</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=loc_name');\">Location</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Platform Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select hw_asset,hw_companyid,hw_serial,inv_name,loc_name,grp_name,inv_status ";
      $q_string .= "from hardware ";
      $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join cities    on cities.ct_id     = locations.loc_city ";
      $q_string .= "left join states    on states.st_id     = locations.loc_state ";
      $q_string .= "left join groups    on groups.grp_id    = inventory.inv_manager ";
      $q_string .= "where " . $search_on . " ";
      $q_string .= $orderby;
      $q_hardware = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_hardware) > 0) {
        while ($a_hardware = mysql_fetch_array($q_hardware)) {

          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_hardware['hw_companyid'] . "\" target=\"_blank\">";
          $linkhwstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_hardware['hw_companyid'] . "#hardware\" target=\"_blank\">";
          $linkend   = "</a>";

          $class = "ui-widget-content";
          if ($a_hardware['inv_status']) {
            $class = "ui-state-error";
          }

          if ($formVars['csv']) {
            $output .= "\"" . $a_hardware['inv_name']   . "\",";
            $output .= "\"" . $a_hardware['hw_asset']   . "\",";
            $output .= "\"" . $a_hardware['hw_serial']  . "\",";
            $output .= "\"" . $a_hardware['loc_name']   . "\",";
            $output .= "\"" . $a_hardware['grp_name']   . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkstart   . $a_hardware['inv_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkhwstart . $a_hardware['hw_asset']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkhwstart . $a_hardware['hw_serial']  . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkstart   . $a_hardware['loc_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                . $a_hardware['grp_name']              . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.</br>\n";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
     }

      print "document.getElementById('asset_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }

# location or all - search the locations and inventory
    if ($formVars['search_by'] == 6 || $formVars['search_by'] == 0) {

      $count = 0;
      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by inv_name ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_on = '';
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "(" . 
              "   inv_name   like '%" . $search_for[$i] . "%' " . 
              "or loc_name   like '%" . $search_for[$i] . "%' " . 
              "or ct_city    like '%" . $search_for[$i] . "%' " . 
              "or st_acronym like '%" . $search_for[$i] . "%' " .
              "or cn_acronym like '%" . $search_for[$i] . "%' " . 
              "or typ_name   like '%" . $search_for[$i] . "%' " . 
            ") ";
            $or = 'or ';
          }
        } else {
          $search_on = "(" . 
            "   inv_name   like '%" . $formVars['search_for'] . "%' " . 
            "or loc_name   like '%" . $formVars['search_for'] . "%' " . 
            "or ct_city    like '%" . $formVars['search_for'] . "%' " . 
            "or st_acronym like '%" . $formVars['search_for'] . "%' " .
            "or cn_acronym like '%" . $formVars['search_for'] . "%' " . 
            "or typ_name   like '%" . $formVars['search_for'] . "%' " . 
          ") ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Location Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"Server Name\",";
        $output .= "\"Data Center\",";
        $output .= "\"Type\",";
        $output .= "\"City\",";
        $output .= "\"State\",";
        $output .= "\"Country\",";
        $output .= "\"Platform Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=6&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=loc_name');\">Data Center</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=typ_name');\">Type</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=ct_city');\">City</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=st_acronym');\">State</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=cn_acronym');\">Country</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Platform Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select inv_id,loc_name,ct_city,st_acronym,cn_acronym,inv_name,grp_name,typ_name,inv_status ";
      $q_string .= "from inventory ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
      $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
      $q_string .= "left join states on states.st_id = locations.loc_state ";
      $q_string .= "left join country on country.cn_id = locations.loc_country ";
      $q_string .= "left join loc_types on loc_types.typ_id = locations.loc_type ";
      if ($formVars['retired'] == 'true') {
        $q_string .= "where " . $search_on . " ";
      } else {
        $q_string .= "where inv_status = 0 and " . $search_on . " ";
      }
      $q_string .= $orderby;
      $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_inventory) > 0) {
        while ($a_inventory = mysql_fetch_array($q_inventory)) {

          $linkstart     = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
          $link_name     = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=6&search_on=loc_name&search_for="   . $a_inventory['loc_name']   . "&retired=" . $formVars['retired'] . "');\">";
          $link_type     = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=6&search_on=typ_name&search_for="   . $a_inventory['typ_name']   . "&retired=" . $formVars['retired'] . "');\">";
          $link_city     = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=6&search_on=ct_city&search_for="    . $a_inventory['ct_city']    . "&retired=" . $formVars['retired'] . "');\">";
          $link_state    = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=6&search_on=st_acronym&search_for=" . $a_inventory['st_acronym'] . "&retired=" . $formVars['retired'] . "');\">";
          $link_country  = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=6&search_on=cn_acronym&search_for=" . $a_inventory['cn_acronym'] . "&retired=" . $formVars['retired'] . "');\">";
          $linkend       = "</a>";

          $class = "ui-widget-content";
          if ($a_inventory['inv_status']) {
            $class = "ui-state-error";
          }

          if ($formVars['csv']) {
            $output .= "\"" . $a_inventory['inv_name']   . "\",";
            $output .= "\"" . $a_inventory['loc_name']   . "\",";
            $output .= "\"" . $a_inventory['typ_name']   . "\",";
            $output .= "\"" . $a_inventory['ct_city']    . "\",";
            $output .= "\"" . $a_inventory['st_acronym'] . "\",";
            $output .= "\"" . $a_inventory['cn_acronym'] . "\",";
            $output .= "\"" . $a_inventory['grp_name']   . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkstart    . $a_inventory['inv_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_name    . $a_inventory['loc_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_type    . $a_inventory['typ_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_city    . $a_inventory['ct_city']    . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_state   . $a_inventory['st_acronym'] . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_country . $a_inventory['cn_acronym'] . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['grp_name']              . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.</br>\n";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      }

      print "document.getElementById('location_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }

# users or all - search for a user
    if ($formVars['search_by'] == 7 || $formVars['search_by'] == 0) {
      $count = 0;
      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by inv_name ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          $search_on = " ( ";
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "pwd_user like '%" . $search_for[$i] . "%' ";
            $or = 'or ';
          }
          $search_on .= ") ";
        } else {
          $search_on = " or pwd_user like '%" . $formVars['search_for'] . "%' ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">User Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"User Name\",";
        $output .= "\"Updated\",";
        $output .= "\"Groups\",";
        $output .= "\"Server Name\",";
        $output .= "\"Centrify Zone\",";
        $output .= "\"Platform Managed By\",";
        $output .= "\"Application Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=7&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=pwd_user');\">User Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=pwd_update');\">Updated</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Groups</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Centrify Zone</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Platform Managed By</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Application Managed By</a></th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select pwd_id,pwd_user,pwd_update,inv_id,inv_name,inv_domain,IFNULL(inv_appadmin, 0) as inv_appadmin,grp_name ";
      $q_string .= "from inventory ";
      $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
      $q_string .= "left join syspwd on syspwd.pwd_companyid = inventory.inv_id ";
      if ($formVars['retired'] == 'true') {
        $q_string .= "where " . $search_on . " ";
      } else {
        $q_string .= "where inv_status = 0 and " . $search_on . " ";
      }
      $q_string .= $orderby;
      $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_inventory) > 0) {
        while ($a_inventory = mysql_fetch_array($q_inventory)) {

          $q_string  = "select grp_name ";
          $q_string .= "from groups ";
          $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
          $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          $a_groups = mysql_fetch_array($q_groups);

          $groups = '';
          $q_string  = "select grp_name ";
          $q_string .= "from sysgrp_members ";
          $q_string .= "left join sysgrp on sysgrp.grp_id = sysgrp_members.mem_gid ";
          $q_string .= "where mem_uid = " . $a_inventory['pwd_id'] . " ";
          $q_sysgrp_members = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          while ($a_sysgrp_members = mysql_fetch_array($q_sysgrp_members)) {
            $groups .= " " . $a_sysgrp_members['grp_name'];
          }

          $linkstart     = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
          $linkend       = "</a>";

          $class = "ui-widget-content";
          if (strpos($groups, "tmproot") !== false) {
            $class = "ui-state-error";
          }
          if (strpos($groups, "sysadmin") !== false) {
            $class = "ui-state-error";
          }
          if (strpos($groups, "uxadmin") !== false) {
            $class = "ui-state-error";
          }

          if ($formVars['csv']) {
            $output .= "\"" . $a_inventory['pwd_user']   . "\",";
            $output .= "\"" . $a_inventory['pwd_update'] . "\",";
            $output .= "\"" . $groups                    . "\",";
            $output .= "\"" . $a_inventory['inv_name']   . "\",";
            $output .= "\"" . $a_inventory['inv_domain'] . "\",";
            $output .= "\"" . $a_inventory['grp_name']   . "\",";
            $output .= "\"" . $a_groups['grp_name']      . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['pwd_user']              . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['pwd_update']            . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $groups                               . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkstart    . $a_inventory['inv_name']   . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['inv_domain']            . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['grp_name']              . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_groups['grp_name']                 . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {

# if the results are blank for users, use the 'search_for' string as servers and provide a list of users per server
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          $search_on = " ( ";
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "inv_name like '%" . $search_for[$i] . "%' ";
            $or = 'or ';
          }
          $search_on .= ") ";
        } else {
          $search_on = " or inv_name like '%" . $formVars['search_for'] . "%' ";
        }

        $q_string  = "select pwd_id,pwd_user,pwd_update,inv_id,inv_name,inv_domain,IFNULL(inv_appadmin, 0) as inv_appadmin,grp_name ";
        $q_string .= "from inventory ";
        $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
        $q_string .= "left join syspwd on syspwd.pwd_companyid = inventory.inv_id ";
        if ($formVars['retired'] == 'true') {
          $q_string .= "where " . $search_on . " ";
        } else {
          $q_string .= "where inv_status = 0 and " . $search_on . " ";
        }
        $q_string .= str_replace('order by ', 'order by pwd_user,', $orderby);
        $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
        if (mysql_num_rows($q_inventory) > 0) {
          while ($a_inventory = mysql_fetch_array($q_inventory)) {

            $q_string  = "select grp_name ";
            $q_string .= "from groups ";
            $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
            $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            $a_groups = mysql_fetch_array($q_groups);

            $groups = '';
            if ($a_inventory['pwd_id'] == '') {
              $a_inventory['pwd_id'] = 0;
            }
            $q_string  = "select grp_name ";
            $q_string .= "from sysgrp_members ";
            $q_string .= "left join sysgrp on sysgrp.grp_id = sysgrp_members.mem_gid ";
            $q_string .= "where mem_uid = " . $a_inventory['pwd_id'] . " ";
            $q_sysgrp_members = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
            while ($a_sysgrp_members = mysql_fetch_array($q_sysgrp_members)) {
              $groups .= " " . $a_sysgrp_members['grp_name'];
            }

            $linkstart     = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
            $linkend       = "</a>";

            $class = "ui-widget-content";
            if (strpos($groups, "tmproot") !== false) {
              $class = "ui-state-error";
            }
            if (strpos($groups, "sysadmin") !== false) {
              $class = "ui-state-error";
            }
            if (strpos($groups, "uxadmin") !== false) {
              $class = "ui-state-error";
            }

            if ($formVars['csv']) {
              $output .= "\"" . $a_inventory['pwd_user']   . "\",";
              $output .= "\"" . $a_inventory['pwd_update'] . "\",";
              $output .= "\"" . $groups                    . "\",";
              $output .= "\"" . $a_inventory['inv_name']   . "\",";
              $output .= "\"" . $a_inventory['inv_domain'] . "\",";
              $output .= "\"" . $a_inventory['grp_name']   . "\",";
              $output .= "\"" . $a_groups['grp_name']      . "\"<br>";
            } else {
              $output .= "<tr>\n";
              $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['pwd_user']              . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['pwd_update']            . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">"                 . $groups                               . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">" . $linkstart    . $a_inventory['inv_name']   . $linkend . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['inv_domain']            . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">"                 . $a_inventory['grp_name']              . "</td>\n";
              $output .= "  <td class=\"" . $class . "\">"                 . $a_groups['grp_name']                 . "</td>\n";
              $output .= "</tr>\n";
            }
          }
        } else {
          if ($formVars['csv']) {
            $output .= "Search results not found.</br>\n";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">Search results not found.</td>\n";
            $output .= "</tr>\n";
          }
        }
      }
      if ($formVars['csv']) {
        $output .= "</p>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      }

      print "document.getElementById('user_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }

# packages or all - search the packages and inventory
    if ($formVars['search_by'] == 8 || $formVars['search_by'] == 0) {

      $count = 0;
      if (strlen($formVars['sort']) > 0) {
        $orderby = " order by " . $formVars['sort'] . " " . $_SESSION['sort'];
        if ($_SESSION['sort'] == '') {
          $_SESSION['sort'] = 'desc';
        } else {
          $_SESSION['sort'] = '';
        }
      } else {
        $orderby = " order by inv_name ";
        $_SESSION['sort'] = '';
      }

      if (strlen($formVars['search_on']) > 0) {
        $search_on = $formVars['search_on'] . " like '%" . $formVars['search_for'] . "%' ";
      } else {
        if (strpos($formVars['search_for'], " ") !== 0) {
          $search_on = '';
          $search_for = explode(" ", $formVars['search_for']);
          $or = '';
          for ($i = 0; $i < count($search_for); $i++) {
            $search_on .= $or . "(" . 
              "   inv_name    like '%" . $search_for[$i] . "%' " . 
              "or pkg_name    like '%" . $search_for[$i] . "%' " .
              "or ct_city     like '%" . $search_for[$i] . "%' " .
              "or ct_state    like '%" . $search_for[$i] . "%' " .
            ") ";
            $or = 'or ';
          }
        } else {
          $search_on = "(" . 
            "   inv_name    like '%" . $formVars['search_for'] . "%' " . 
            "or pkg_name    like '%" . $formVars['search_for'] . "%' " . 
            "or ct_city     like '%" . $formVars['search_for'] . "%' " .
            "or ct_state    like '%" . $formVars['search_for'] . "%' " .
          ") ";
        }
      }

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Package Search</th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n\n";

      if ($formVars['csv']) {
        $output .= "<p>\"Server Name\",";
        $output .= "\"Product\",";
        $output .= "\"Package\",";
        $output .= "\"Operating System\",";
        $output .= "\"Managed By\"<br>";
      } else {
        $linkstart = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=8&search_for=" . $formVars['search_for'] . "&retired=" . $formVars['retired'];

        $output .= "<table class=\"ui-styled-table\">\n";
        $output .= "<tr>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=inv_name');\">Server Name</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=prod_name');\">Product</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=pkg_name');\">Package</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=pkg_os');\">Operating System</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">" . $linkstart . "&sort=grp_name');\">Managed By</a></th>\n";
        $output .= "  <th class=\"ui-state-default\">Applications</th>\n";
        $output .= "</tr>\n";
      }

      $q_string  = "select inv_name,pkg_inv_id,pkg_name,pkg_os,inv_status,prod_name,grp_name,IFNULL(inv_appadmin, 0) as inv_appadmin ";
      $q_string .= "from packages ";
      $q_string .= "left join inventory on inventory.inv_id = packages.pkg_inv_id ";
      $q_string .= "left join products  on products.prod_id = inventory.inv_product ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join cities    on cities.ct_id     = locations.loc_city ";
      $q_string .= "left join states    on states.st_id     = locations.loc_state ";
      $q_string .= "left join groups    on groups.grp_id    = inventory.inv_manager ";
      if ($formVars['retired'] == 'true') {
        $q_string .= "where " . $search_on . " ";
      } else {
        $q_string .= "where inv_status = 0 and " . $search_on . " ";
      }
      $q_string .= $orderby;
      $q_packages = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
      if (mysql_num_rows($q_packages) > 0) {
        while ($a_packages = mysql_fetch_array($q_packages)) {

          $linkpkgstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_packages['pkg_inv_id'] . "#software\" target=\"_blank\">";
          $link_name    = "<a href=\"#\" onClick=\"javascript:show_file('" . $Reportroot . "/search.php?search_by=8&search_on=pkg_name&search_for=" . $a_packages['pkg_name'] . "&retired=" . $formVars['retired'] . "');\">";
          $linkend      = "</a>";

          $class = "ui-widget-content";
          if ($a_packages['inv_status']) {
            $class = "ui-state-error";
          }

          $q_string  = "select grp_name ";
          $q_string .= "from groups ";
          $q_string .= "where grp_id = " . $a_packages['inv_appadmin'] . " ";
          $q_groups = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
          $a_groups = mysql_fetch_array($q_groups);

          if ($formVars['csv']) {
            $output .= "\"" . $a_packages['inv_name']    . "\",";
            $output .= "\"" . $a_packages['prod_name']   . "\",";
            $output .= "\"" . $a_packages['pkg_name']    . "\",";
            $output .= "\"" . $a_packages['pkg_os']      . "\",";
            $output .= "\"" . $a_packages['grp_name']    . "\",";
            $output .= "\"" . $a_groups['grp_name']      . "\"<br>";
          } else {
            $output .= "<tr>\n";
            $output .= "  <td class=\"" . $class . "\">" . $linkpkgstart . $a_packages['inv_name']    . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_packages['prod_name']              . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">" . $link_name    . $a_packages['pkg_name']    . $linkend . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_packages['pkg_os']                 . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_packages['grp_name']               . "</td>\n";
            $output .= "  <td class=\"" . $class . "\">"                 . $a_groups['grp_name']                 . "</td>\n";
            $output .= "</tr>\n";
          }
        }
      } else {
        if ($formVars['csv']) {
          $output .= "Search results not found.</br>\n";
        } else {
          $output .= "<tr>\n";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">Search results not found.</td>\n";
          $output .= "</tr>\n";
        }
      }
      if ($formVars['csv']) {
        $output .= "</csv>\n";
      } else {
        $output .= "</tr>\n";
        $output .= "</table>\n\n";
      } 

      print "document.getElementById('packages_search_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";
    }
  }

  print "document.index.search_for.focus();\n";

?>
