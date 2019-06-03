<?php
# Script: bulkedit.mysql.php
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
    $package = "network.mysql.php";
    $formVars['project']       = clean($_GET['project'],       10);
    $formVars['product']       = clean($_GET['product'],       10);

    if ($formVars['project'] == '') {
      $formVars['project'] = 0;
    }
    if ($formVars['product'] == '') {
      $formVars['product'] = 0;
    }

    if (check_userlevel(2)) {

      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $q_string  = "select grp_id,grp_name ";
      $q_string .= "from groups ";
      $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_groups = mysql_fetch_array($q_groups)) {
        $grpname[$a_groups['grp_id']] = $a_groups['grp_name'];
      }

      $q_string  = "select prod_id,prod_name ";
      $q_string .= "from products ";
      $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_products = mysql_fetch_array($q_products)) {
        $prodname[$a_products['prod_id']] = $a_products['prod_name'];
      }

      $q_string  = "select prj_id,prj_name ";
      $q_string .= "from projects ";
      $q_projects = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_projects = mysql_fetch_array($q_projects)) {
        $prjname[$a_projects['prj_id']] = $a_projects['prj_name'];
      }

      $q_string  = "select svc_id,svc_acronym ";
      $q_string .= "from service ";
      $q_service = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_service = mysql_fetch_array($q_service)) {
        $svcname[$a_service['svc_id']] = $a_service['svc_acronym'];
      }

      $q_string  = "select loc_id,loc_name ";
      $q_string .= "from locations ";
      $q_locations = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_locations = mysql_fetch_array($q_locations)) {
        $locname[$a_locations['loc_id']] = $a_locations['loc_name'];
      }

# details
      $details  = "<form name=\"details\">\n";
      $details .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $details .= "<tr>\n";
      $details .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $details .= "  <th class=\"ui-state-default\">Domain Name</th>\n";
      $details .= "  <th class=\"ui-state-default\">Function</th>\n";
      $details .= "  <th class=\"ui-state-default\">911 Callpath</th>\n";
      $details .= "  <th class=\"ui-state-default\">Documentation</th>\n";
      $details .= "  <th class=\"ui-state-default\">Location</th>\n";
      $details .= "  <th class=\"ui-state-default\">Row</th>\n";
      $details .= "  <th class=\"ui-state-default\">Rack</th>\n";
      $details .= "  <th class=\"ui-state-default\">Unit</th>\n";
      $details .= "  <th class=\"ui-state-default\">Platforms</th>\n";
      $details .= "  <th class=\"ui-state-default\">Applications</th>\n";
      $details .= "  <th class=\"ui-state-default\">Service Class</th>\n";
      $details .= "  <th class=\"ui-state-default\">Product</th>\n";
      $details .= "  <th class=\"ui-state-default\">Project</th>\n";
      $details .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_fqdn,inv_function,inv_callpath,inv_document,inv_location,inv_row,inv_rack,inv_unit,inv_manager,inv_appadmin,inv_class,inv_product,inv_project ";
      $q_string .= "from inventory ";
      $q_string .= "where ";
      if ($formVars['product'] > 0) {
        $q_string .= "inv_product = " . $formVars['product'] . " and ";
      }
      if ($formVars['project'] > 0) {
        $q_string .= "inv_project = " . $formVars['project'] . " and ";
      }
      $q_string .= "inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
      $q_string .= "order by inv_name ";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_inventory = mysql_fetch_array($q_inventory)) {

        if ($a_inventory['inv_callpath']) {
          $callpath = 'Yes';
        } else {
          $callpath = 'No';
        }

        $details .= "<tr>\n";
        $details .= "<td class=\"ui-widget-content\"><u>"                                                                                                                  . $a_inventory['inv_name']     . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\"><u>"                                                                                                                  . $a_inventory['inv_fqdn']     . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"psg" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"systems_Group('psg"        . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_inventory['inv_function'] . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"psa" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"platforms_Admin('psa"      . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $callpath      . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pag" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"applications_Group('pag"   . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_inventory['inv_document']     . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"paa" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"applications_Admin('paa"   . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $locname[$a_inventory['inv_location']] . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pss" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"SAN_Admin('pss"            . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_inventory['inv_row']      . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pna" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"network_Admin('pna"        . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_inventory['inv_rack']     . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pva" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"virtualization_Admin('pva" . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $a_inventory['inv_unit']     . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pma" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"monitoring_Admin('pma"     . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $grpname[$a_inventory['inv_manager']]  . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pba" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"backup_Admin('pba"         . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $grpname[$a_inventory['inv_appadmin']] . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pba" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"backup_Admin('pba"         . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $svcname[$a_inventory['inv_class']]    . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pba" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"backup_Admin('pba"         . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $prodname[$a_inventory['inv_product']]  . "</u></td>\n";
        $details .= "<td class=\"ui-widget-content\" id=\"pba" . $a_rsdp_id['rsdp_id'] . "\" onclick=\"backup_Admin('pba"         . $a_rsdp_id['rsdp_id'] . "');\"><u>" . $prjname[$a_inventory['inv_project']]  . "</u></td>\n";
        $details .= "</tr>\n";

      }

      $details .= "</table>\n";
      $details .= "</form>\n";

      print "document.getElementById('details_mysql').innerHTML = '" . mysql_real_escape_string($details) . "';\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
