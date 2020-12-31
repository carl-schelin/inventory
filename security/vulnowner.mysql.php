<?php
# Script: vulnowner.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "vulnowner.mysql.php";
    $formVars['update']    = clean($_GET['update'],   10);
    $formVars['product']   = clean($_GET['product'],  10);
    $formVars['inwork']    = clean($_GET['inwork'],   10);
    $formVars['country']   = clean($_GET['country'],  10);
    $formVars['state']     = clean($_GET['state'],    10);
    $formVars['city']      = clean($_GET['city'],     10);
    $formVars['location']  = clean($_GET['location'], 10);
    $formVars['type']      = clean($_GET['type'],     10);
    $formVars['sort']      = clean($_GET["sort"],     20);
    $formVars['group']     = clean($_GET['group'],    10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['sort'] == 'undefined') {
      $formVars['sort'] = '';
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']               = clean($_GET['id'],                10);
        $formVars['int_id']           = clean($_GET['int_id'],            10);
        $formVars['sec_id']           = clean($_GET['sec_id'],            10);
        $formVars['vul_group']        = clean($_GET['vul_group'],         10);
        $formVars['vul_ticket']       = clean($_GET['vul_ticket'],        20);
        $formVars['vul_exception']    = clean($_GET['vul_exception'],     10);
        $formVars['vul_description']  = clean($_GET['vul_description'],  100);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['vul_exception'] == 'true') {
          $formVars['vul_exception'] = 1;
        } else {
          $formVars['vul_exception'] = 0;
        }

        if (strlen($formVars['int_id']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "vul_interface   =   " . $formVars['int_id']           . "," .
            "vul_security    =   " . $formVars['sec_id']           . "," . 
            "vul_group       =   " . $formVars['vul_group']        . "," . 
            "vul_ticket      = \"" . $formVars['vul_ticket']       . "\"," . 
            "vul_exception   =   " . $formVars['vul_exception']    . "," . 
            "vul_description = \"" . $formVars['vul_description']  . "\"";

          if ($formVars['update'] == 0) {
            $query = "insert into vulnowner set vul_id = NULL, " . $q_string;
            $message = "Ownership added.";
          }
          if ($formVars['update'] == 1) {
            $q_vulstr  = "select vul_id ";
            $q_vulstr .= "from vulnowner ";
            $q_vulstr .= "where vul_interface = " . $formVars['int_id'] . " and vul_security = " . $formVars['sec_id'] . " ";
            $q_vulnowner = mysqli_query($db, $q_vulstr) or die($q_vulstr . ": " . mysqli_error($db));
            $a_vulnowner = mysqli_fetch_array($q_vulnowner);
            $formVars['id'] = $a_vulnowner['vul_id'];

            $query = "update vulnowner set " . $q_string . " where vul_id = " . $formVars['id'];
            $message = "Ownership updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['id']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


        logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

        $help  = "<table class=\"ui-styled-table\">\n";
        $help .= "<tr>\n";
        $help .= "  <th class=\"ui-state-default\">Vulnerability Listing</th>\n";
        $help .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('vulnerability-listing-help');\">Help</a></th>\n";
        $help .= "</tr>\n";
        $help .= "</table>\n";
        $help .= "<div id=\"vulnerability-listing-help\" style=\"display: none\">\n";

        $help .= "<div class=\"main-help ui-widget-content\">\n";
        $help .= "<ul>\n";
        $help .= "  <li><strong>Vulnerability Listing</strong>\n";
        $help .= "  <ul>\n";
        $help .= "    <li><strong>Highlighted</strong> - This vulnerability is a <span class=\"ui-state-error\">Critical</span> vulnerability.</li>\n";
        $help .= "    <li><strong>Highlighted</strong> - This vulnerability is a <span class=\"ui-state-highlight\">High</span> vulnerability.</li>\n";
        $help .= "    <li><strong>Right Less-Than (&lt;)</strong> - This identifies a vulnerability as a duplicate for the system. The entry is also reduced to the default color vs being highlighed as above. The scanner scans each IP and some servers have more than one IP so the scanner identifies multiple copies for the same system.</li>\n";
        $help .= "    <li><strong>Editing</strong> - Click on a vulnerability to edit it.</li>\n";
        $help .= "  </ul></li>\n";
        $help .= "</ul>\n";

        $help .= "<ul>\n";
        $help .= "  <li><strong>Notes</strong>\n";
        $help .= "  <ul>\n";
        $help .= "    <li>Click the <strong>Vulnerability Management</strong> title bar to toggle the <strong>Vulnerability Form</strong>.</li>\n";
        $help .= "  </ul></li>\n";
        $help .= "</ul>\n";

        $help .= "</div>\n";

        $help .= "</div>\n";


        $header  = "<table class=\"ui-styled-table\">\n";
        $header .= "<tr>\n";
        $header .= "  <th class=\"ui-state-default\">Id</th>\n";
        $header .= "  <th class=\"ui-state-default\">Int ID</th>\n";
        $header .= "  <th class=\"ui-state-default\">Sec ID</th>\n";
        $header .= "  <th class=\"ui-state-default\">Server</th>\n";
        $header .= "  <th class=\"ui-state-default\">Interface</th>\n";
        $header .= "  <th class=\"ui-state-default\">IP Address</th>\n";
        $header .= "  <th class=\"ui-state-default\">Security Description</th>\n";
        $header .= "  <th class=\"ui-state-default\">Severity</th>\n";
        $header .= "  <th class=\"ui-state-default\">Group</th>\n";
        $header .= "  <th class=\"ui-state-default\">Ticket</th>\n";
        $header .= "  <th class=\"ui-state-default\">Exception</th>\n";
        $header .= "  <th class=\"ui-state-default\">Description</th>\n";
        $header .= "  <th class=\"ui-state-default\">Done</th>\n";
        $header .= "</tr>\n";

        if ($formVars['sort'] != '') {
          $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
          if ($_SESSION['sort'] == ' desc') {
            $_SESSION['sort'] = '';
          } else {
            $_SESSION['sort'] = ' desc';
          }
        } else {
#          $orderby = "order by prod_name,inv_name,int_addr,vuln_duplicate,sev_name,sec_name";
          $orderby = "order by inv_name,int_server,int_addr";
          $_SESSION['sort'] = '';
        }

        $and = " where";
        if ($formVars['product'] == 0) {
          $product = '';
        } else {
          if ($formVars['product'] == -1) {
            $product = $and . " inv_product = 0 ";
            $and = " and";
          } else {
            $product = $and . " inv_product = " . $formVars['product'] . " ";
            $and = " and";
          }
        }

        $group = '';
        if ($formVars['group'] > 0) {
          $group = $and . " (vul_group = " . $formVars['group'] . " or inv_manager = " . $formVars['group'] . ") ";
          $and = " and";
        }

        if ($formVars['inwork'] == 'false') {
          $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
          $and = " and";
        } else {
          $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
          $and = " and";
        }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

        $location = '';
        if ($formVars['country'] == 0 && $formVars['location'] > 0) {
          $location = $and . " inv_location = " . $formVars['location'] . " ";
          $and = " and";
        } else {
          if ($formVars['country'] > 0) {
            $location .= $and . " loc_country = " . $formVars['country'] . " ";
            $and = " and";
          }
          if ($formVars['state'] > 0) {
            $location .= $and . " loc_state = " . $formVars['state'] . " ";
            $and = " and";
          }
          if ($formVars['city'] > 0) {
            $location .= $and . " loc_city = " . $formVars['city'] . " ";
            $and = " and";
          }
          if ($formVars['location'] > 0) {
            $location .= $and . " inv_location = " . $formVars['location'] . " ";
            $and = " and";
          }
        }

        if ($formVars['type'] == -1) {
          $type = "";
        } else {
          $type = $and . " inv_status = 0 ";
          $and = " and";
        }

        $where = $product . $group . $inwork . $location . $type;
#        $where = $group;
#        $where = '';



# This listing is for the vulnerabilities that have no responsible groups

        $output = '';
        $product = '';
        $intid = 0;
        $secid = 0;
        $q_string  = "select vul_id,inv_name,int_server,int_addr,vul_interface,vul_security,sec_name,sev_name,grp_name,vul_ticket,vul_exception,vul_description ";
        $q_string .= "from vulnowner ";
        $q_string .= "left join a_groups on a_groups.grp_id = vulnowner.vul_group ";
        $q_string .= "left join interface on interface.int_id = vulnowner.vul_interface ";
        $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
        $q_string .= "left join security on security.sec_id = vulnowner.vul_security ";
        $q_string .= "left join severity on severity.sev_id = security.sec_severity ";
#        $q_string .= $where;
#        $q_string .= $orderby;
        $q_string .= "order by vul_interface,vul_security ";
        $q_vulnowner = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_vulnowner) > 0) {
          while ($a_vulnowner = mysqli_fetch_array($q_vulnowner)) {

            $linkstart = "<a href=\"#\" onclick=\"show_file('vulnowner.fill.php?id="  . $a_vulnowner['vul_id'] . "');showDiv('vulnerability-hide');\">";
            $linkend   = "</a>";

            if ($product != $a_vulnowner['prod_name']) {
              $output   .= "<tr>";
              $output .= "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_vulnerabilities['prod_name'] . "</th>\n";
              $output   .= "</tr>";
              $product = $a_vulnerabilities['prod_name'];
            }

            $class = "ui-widget-content";
            if ($a_vulnowner['vul_interface'] == $intid && $a_vulnowner['vul_security'] == $secid) {
              $class = "ui-state-error";
            }

#            if ($a_vulnowner['sev_name'] == 'Critical') {
#              $class = "ui-state-error";
#            }
#            if ($a_vulnowner['sev_name'] == 'High') {
#              $class = "ui-state-highlight";
#            }

            $output   .= "<tr>";
            $output   .= "  <td class=\"" . $class . " delete\">" . $linkstart . $a_vulnowner['vul_id']             . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['vul_interface']      . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['vul_security']       . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['inv_name']           . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['int_server']         . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['int_addr']           . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['sec_name']           . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['sev_name']           . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['grp_name']           . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['vul_ticket']         . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['vul_exception']      . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_vulnowner['vul_description']    . $linkend . "</td>";
            $output   .= "  <td class=\"" . $class . "\">"        . "<input type=\"checkbox\">"                                . "</td>";
            $output   .= "</tr>";

            $intid = $a_vulnowner['vul_interface'];
            $secid = $a_vulnowner['vul_security'];

          }
        } else {
          $output .= "<tr>";
          $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No records found</td>";
          $output .= "</tr>";
        }

        $output .= "</table>";

        mysqli_free_result($q_vulnowner);

        print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $help . $header . $output) . "';\n\n";

      } else {
        logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
      }

  }
?>
