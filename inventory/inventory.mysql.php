<?php
# Script: inventory.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "inventory.mysql.php";
    $formVars['projectid'] = clean($_GET['projectid'],   10);
    $formVars['productid'] = clean($_GET['productid'],   10);
    $formVars['group']     = clean($_GET['group'],       10);
    $formVars['filter']    = clean($_GET['filter'],    1024);
    $formVars['location']  = clean($_GET['location'],    10);
    $formVars['csv']       = clean($_GET['csv'],         10);

    if ($formVars['projectid'] == '') {
      $formVars['projectid'] = 0;
    }
    if ($formVars['productid'] == '') {
      $formVars['productid'] = 0;
    }
    if ($formVars['group'] == '') {
      $formVars['group'] = 0;
    }
    if ($formVars['location'] == '') {
      $formVars['location'] = 0;
    }
    if ($formVars['csv'] == '') {
      $formVars['csv'] = 'false';
    }

    $a_rsdp_id['rsdp_id'] = 0;

    $filter = '';
    if (strlen($formVars['filter']) > 0) {

      $filterinv = explode(",", $formVars['filter']);

      $filter = 'and (';
      $or = '';
      for ($i = 0; $i < count($filterinv); $i++) {
        $filter .= $or . "rsdp_id = " . $filterinv[$i] . " ";
        $or = 'or ';
      }
      $filter .= ") ";
    }

    $formVars['URL'] = '';
    $systemurl = '';
    $hardwareurl = '';
    $interfaceurl = '';
    if (strlen($formVars['filter']) > 0) {
      $question = "?";
      $formVars['URL'] = "<p class=\"ui-widget-content\"><a href=\"" . $Invroot . "/network.php";
      if ($formVars['projectid'] > 0) {
        $formVars['URL'] .= $question . "projectid=" . $formVars['projectid'];
        $question = "&";
      }
      if ($formVars['productid'] > 0) {
        $formVars['URL'] .= $question . "productid=" . $formVars['productid'];
        $question = "&";
      }
      if ($formVars['filter'] != '') {
        $formVars['URL'] .= $question . "filter=" . $formVars['filter'];
        $question = "&";
      }
      $formVars['URL'] .= "\">Link</a></p>";
    }

    if (check_userlevel($db, $AL_Edit)) {

# prepopulate the small tables to increase lookup time.
      $q_string  = "select zone_id,zone_name ";
      $q_string .= "from ip_zones ";
      $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
        $ip_zones[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
      }

      $q_string  = "select med_id,med_text ";
      $q_string .= "from int_media ";
      $q_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_media = mysqli_fetch_array($q_int_media)) {
        $int_media[$a_int_media['med_id']] = $a_int_media['med_text'];
      }

      $q_string  = "select itp_id,itp_acronym ";
      $q_string .= "from inttype ";
      $q_inttype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_inttype = mysqli_fetch_array($q_inttype)) {
        $inttype[$a_inttype['itp_id']] = $a_inttype['itp_acronym'];
      }

      $q_string  = "select spd_id,spd_text ";
      $q_string .= "from int_speed ";
      $q_int_speed = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_speed = mysqli_fetch_array($q_int_speed)) {
        $int_speed[$a_int_speed['spd_id']] = $a_int_speed['spd_text'];
      }

      $q_string  = "select dup_id,dup_text ";
      $q_string .= "from int_duplex ";
      $q_int_duplex = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_duplex = mysqli_fetch_array($q_int_duplex)) {
        $int_duplex[$a_int_duplex['dup_id']] = $a_int_duplex['dup_text'];
      }

      $q_string  = "select red_id,red_text ";
      $q_string .= "from int_redundancy ";
      $q_int_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_int_redundancy = mysqli_fetch_array($q_int_redundancy)) {
        $int_redundancy[$a_int_redundancy['red_id']] = $a_int_redundancy['red_text'];
      }

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

# detail
      if ($formVars['csv'] == 'true') {
        $detail  = "<p>\"Server Name\",";
        $detail .= "\"Parent\",";
        $detail .= "\"Function\",";
        $detail .= "\"Systems Admin\",";
        $detail .= "\"Applications Admin\",";
        $detail .= "\"Product\",";
        $detail .= "\"Project\",";
        $detail .= "\"Service Class\",";
        $detail .= "\"Maintenance Window\",";
        $detail .= "\"Location\",";
        $detail .= "\"Row\",";
        $detail .= "\"Rack\",";
        $detail .= "\"Unit\",";
        $detail .= "\"Live\",";
        $detail .= "\"Call Path\",";
        $detail .= "\"Ansible\",";
        $detail .= "\"Unixsvc\"</br>\n";
      } else {
        $detail  = "<form name=\"projects\">\n";
        $detail .= "<table id=\"project-table\" class=\"ui-styled-table\">\n";
        $detail .= "<tr>\n";
        $detail .= "  <th class=\"ui-state-default\">Server Name</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Parent</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Function</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Systems Admin</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Applications Admin</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Product</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Project</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Service Class</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Maintenance Window</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Location</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Row</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Rack</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Unit</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Live</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Call Path</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Ansible</th>\n";
        $detail .= "  <th class=\"ui-state-default\">Unixsvc</th>\n";
        $detail .= "</tr>\n";
      }

      $q_string  = "select inv_id,inv_companyid,inv_name,inv_function,inv_appadmin,grp_name,prod_name,prj_name,svc_name,loc_name,inv_row,inv_rack,inv_unit,inv_callpath,inv_ansible,inv_ssh,win_text ";
      $q_string .= "from inventory ";
      $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "left join maint_window on maint_window.win_id = inventory.inv_maint ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "where inv_status = 0 and inv_manager = " . $formVars['group'] . " ";
      if ($formVars['location'] > 0) {
        $q_string .= "and inv_location = " . $formVars['location'] . " ";
      }
      if ($formVars['productid'] > 0) {
        $q_string .= "and inv_product = " . $formVars['productid'] . " ";
      }
      if ($formVars['projectid'] > 0) {
        $q_string .= "and inv_project = " . $formVars['projectid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_inventory = mysqli_fetch_array($q_inventory)) {

        $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
        $linkend   = "</a>";

        $q_string  = "select inv_name ";
	$q_string .= "from inventory ";
	$q_string .= "where inv_id = " . $a_inventory['inv_companyid'] . " ";
        $q_parent = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
	if (mysqli_num_rows($q_parent) > 0) {
          $a_parent = mysqli_fetch_array($q_parent);
	} else {
	  $a_parent['inv_name'] = "orphan";
	}

        $q_string  = "select grp_name ";
        $q_string .= "from a_groups ";
        $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
        $q_appadmin = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_appadmin = mysqli_fetch_array($q_appadmin);

        $q_string  = "select hw_active ";
        $q_string .= "from hardware ";
        $q_string .= "where hw_primary = 1 and hw_deleted = 0 and hw_companyid = " . $a_inventory['inv_id'] . " ";
        $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_hardware = mysqli_fetch_array($q_hardware);

        if ($formVars['csv'] == 'true') {
          $is_live = 'No';
          if ($a_hardware['hw_active'] != '1971-01-01') {
            $is_live = 'Yes';
          }
          $is_callpath = 'No';
          if ($a_inventory['inv_callpath']) {
            $is_callpath = 'Yes';
          }
          $is_ansible = 'No';
          if ($a_inventory['inv_ansible']) {
            $is_ansible = 'Yes';
          }
          $is_ssh = 'No';
          if ($a_inventory['inv_ssh']) {
            $is_unixsvc = 'Yes';
          }

          $detail .= "\"" . $a_inventory['inv_name']     . "\",";
          $detail .= "\"" . $a_parent['inv_name']        . "\",";
          $detail .= "\"" . $a_inventory['inv_function'] . "\",";
          $detail .= "\"" . $a_inventory['grp_name']     . "\",";
          $detail .= "\"" . $a_appadmin['grp_name']      . "\",";
          $detail .= "\"" . $a_inventory['prod_name']    . "\",";
          $detail .= "\"" . $a_inventory['prj_name']     . "\",";
          $detail .= "\"" . $a_inventory['svc_name']     . "\",";
          $detail .= "\"" . $a_inventory['win_text']     . "\",";
          $detail .= "\"" . $a_inventory['loc_name']     . "\",";
          $detail .= "\"" . $a_inventory['inv_row']      . "\",";
          $detail .= "\"" . $a_inventory['inv_rack']     . "\",";
          $detail .= "\"" . $a_inventory['inv_unit']     . "\",";
          $detail .= "\"" . $is_live                     . "\",";
          $detail .= "\"" . $is_callpath                 . "\",";
          $detail .= "\"" . $is_ansible                  . "\",";
          $detail .= "\"" . $is_unixsvc                  . "\"</br>\n";

        } else {
          $is_live = '';
          if ($a_hardware['hw_active'] != '1971-01-01') {
            $is_live = 'checked';
          }
          $is_callpath = '';
          if ($a_inventory['inv_callpath']) {
            $is_callpath = 'checked';
          }
          $is_ansible = '';
          if ($a_inventory['inv_ansible']) {
            $is_ansible = 'checked';
          }
          $is_unixsvc = '';
          if ($a_inventory['inv_ssh']) {
            $is_unixsvc = 'checked';
          }

          $detail .= "<tr>\n";
          $detail .= "<td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name'] . $linkend . "</td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"ipt" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ipt');\"><u>" . $a_parent['inv_name']        . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"ifn" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ifn');\"><u>" . $a_inventory['inv_function'] . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"isa" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'isa');\"><u>" . $a_inventory['grp_name']     . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"iaa" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'iaa');\"><u>" . $a_appadmin['grp_name']      . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"ipr" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ipr');\"><u>" . $a_inventory['prod_name']    . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"ipj" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ipj');\"><u>" . $a_inventory['prj_name']     . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"isc" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'isc');\"><u>" . $a_inventory['svc_name']     . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"imw" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'imw');\"><u>" . $a_inventory['win_text']     . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"ilc" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ilc');\"><u>" . $a_inventory['loc_name']     . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"irw" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'irw');\"><u>" . $a_inventory['inv_row']      . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"irk" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'irk');\"><u>" . $a_inventory['inv_rack']     . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content\" id=\"iun" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'iun');\"><u>" . $a_inventory['inv_unit']     . "</u></td>\n";
          $detail .= "<td class=\"ui-widget-content delete\"><input type=\"checkbox\"" . $is_live     . " id=\"ilv" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ilv');\"></td>\n";
          $detail .= "<td class=\"ui-widget-content delete\"><input type=\"checkbox\"" . $is_callpath . " id=\"icp" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'icp');\"></td>\n";
          $detail .= "<td class=\"ui-widget-content delete\"><input type=\"checkbox\"" . $is_ansible  . " id=\"ian" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ian');\"></td>\n";
          $detail .= "<td class=\"ui-widget-content delete\"><input type=\"checkbox\"" . $is_unixsvc  . " id=\"ius" . $a_inventory['inv_id'] . "\" onclick=\"edit_Detail(" . $a_inventory['inv_id'] . ", 'ius');\"></td>\n";
          $detail .= "</tr>\n";
        }
      }

      if ($formVars['csv'] == 'true') {
        $detail .= "</p>\n";
      } else {
        $detail .= "</table>\n";
        $detail .= $formVars['URL'];
        $detail .= "</form>\n";
      }

      print "document.getElementById('detail_mysql').innerHTML = '" . mysqli_real_escape_string($db, $detail) . "';\n";

# hardware
      if ($formVars['csv'] == 'true') {
        $hardware  = "<p>\"Server Name\",";
        $hardware .= "\"Type\",";
        $hardware .= "\"Date Built\",";
        $hardware .= "\"Date Live\",";
        $hardware .= "\"Date Verified\",";
        $hardware .= "\"Vendor\",";
        $hardware .= "\"Model\",";
        $hardware .= "\"Size\",";
        $hardware .= "\"Speed\",";
        $hardware .= "\"Asset Tag\",";
        $hardware .= "\"SN/Service Tag\"\n</br>";
      } else {
        $hardware  = "<form name=\"hardware\">\n";
        $hardware .= "<table id=\"hardware-table\" class=\"ui-styled-table\">\n";
        $hardware .= "<tr>\n";
        $hardware .= "  <th class=\"ui-state-default\">Server Name</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Type</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Date Built</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Date Live</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Date Verified</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Vendor</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Model</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Size</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Speed</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">Asset Tag</th>\n";
        $hardware .= "  <th class=\"ui-state-default\">SN/Service Tag</th>\n";
        $hardware .= "</tr>\n";
      }

      $q_string  = "select inv_id,inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_status = 0 and inv_manager = " . $formVars['group'] . " ";
      if ($formVars['location'] > 0) {
        $q_string .= "and inv_location = " . $formVars['location'] . " ";
      }
      if ($formVars['productid'] > 0) {
        $q_string .= "and inv_product = " . $formVars['productid'] . " ";
      }
      if ($formVars['projectid'] > 0) {
        $q_string .= "and inv_project = " . $formVars['projectid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_inventory = mysqli_fetch_array($q_inventory)) {

        $linkstart       = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
        $linkend         = "</a>";

        $servername = $a_inventory['inv_name'];

        $q_string  = "select hw_id,hw_built,hw_active,hw_update,hw_verified,hw_asset,hw_serial,hw_size,hw_speed,part_name,mod_vendor,mod_name,mod_size,mod_speed ";
        $q_string .= "from hardware ";
        $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
        $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
        $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = 0 ";
        $q_string .= "order by hw_primary desc,part_id,hw_size ";
        $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_hardware = mysqli_fetch_array($q_hardware)) {

          if ($formVars['csv'] == 'true') {
            $hardware .= "\"" . $servername               . "\",";
            $hardware .= "\"" . $a_hardware['part_name']  . "\",";
            $hardware .= "\"" . $a_hardware['hw_built']   . "\",";
            $hardware .= "\"" . $a_hardware['hw_active']  . "\",";
            $hardware .= "\"" . $a_hardware['hw_update']  . "\",";
            $hardware .= "\"" . $a_hardware['mod_vendor'] . "\",";
            $hardware .= "\"" . $a_hardware['mod_name']   . "\",";
            $hardware .= "\"" . $a_hardware['hw_size']    . "\",";
            $hardware .= "\"" . $a_hardware['hw_speed']   . "\",";
            $hardware .= "\"" . $a_hardware['hw_asset']   . "\",";
            $hardware .= "\"" . $a_hardware['hw_serial']  . "\"\n</br>";
          } else {
            $hardware .= "<tr>\n";
            $hardware .= "  <td class=\"ui-widget-content\">" . $linkstart . $servername . $linkend . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hpn" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hpn');\">" . $a_hardware['part_name']  . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hpb" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hpb');\"><u>" . $a_hardware['hw_built']   . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hpa" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hpa');\"><u>" . $a_hardware['hw_active']  . "</u></td>\n";
            $hardware .= "  <td class=\"ui-widget-content\">" . $a_hardware['hw_update']  . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hmv" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hmv');\">" . $a_hardware['mod_vendor'] . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hmn" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hmn');\">" . $a_hardware['mod_name']   . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hsz" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hsz');\">" . $a_hardware['hw_size']    . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hsp" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hsp');\">" . $a_hardware['hw_speed']   . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"has" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'has');\">" . $a_hardware['hw_asset']   . "</td>\n";
            $hardware .= "  <td class=\"ui-widget-content\" id=\"hsn" . $a_hardware['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hardware['hw_id'] . ",'hsn');\">" . $a_hardware['hw_serial']  . "</td>\n";
            $hardware .= "</tr>\n";

            $servername = '';
          }


          $q_string  = "select hw_id,hw_built,hw_active,hw_update,hw_verified,hw_asset,hw_serial,hw_size,hw_speed,part_name,mod_vendor,mod_name,mod_size,mod_speed ";
          $q_string .= "from hardware ";
          $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
          $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
          $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_hw_id = " . $a_hardware['hw_id'] . " ";
          $q_string .= "order by hw_primary desc,part_id,hw_size ";
          $q_hw_child = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          while ($a_hw_child = mysqli_fetch_array($q_hw_child)) {

            if ($formVars['csv'] == 'true') {
              $hardware .= "\"" . $servername               . "\",";
              $hardware .= "\"" . $a_hw_child['part_name']  . "\",";
              $hardware .= "\"" . $a_hw_child['hw_built']   . "\",";
              $hardware .= "\"" . $a_hw_child['hw_active']  . "\",";
              $hardware .= "\"" . $a_hw_child['hw_update']  . "\",";
              $hardware .= "\"" . $a_hw_child['mod_vendor'] . "\",";
              $hardware .= "\"" . $a_hw_child['mod_name']   . "\",";
              $hardware .= "\"" . $a_hw_child['hw_size']    . "\",";
              $hardware .= "\"" . $a_hw_child['hw_speed']   . "\",";
              $hardware .= "\"" . $a_hw_child['hw_asset']   . "\",";
              $hardware .= "\"" . $a_hw_child['hw_serial']  . "\"\n</br>";
            } else {
              $hardware .= "<tr>\n";
              $hardware .= "  <td class=\"ui-widget-content\">" . $linkstart . $servername . $linkend . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hpn" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hpn');\">&gt;" . $a_hw_child['part_name']  . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hpb" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hpb');\"><u>" . $a_hw_child['hw_built']   . "</u></td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hpa" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hpa');\"><u>" . $a_hw_child['hw_active']  . "</u></td>\n";
              $hardware .= "  <td class=\"ui-widget-content\">" . $a_hw_child['hw_update']  . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hmv" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hmv');\">" . $a_hw_child['mod_vendor'] . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hmn" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hmn');\">" . $a_hw_child['mod_name']   . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hsz" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hsz');\">" . $a_hw_child['hw_size']    . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hsp" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hsp');\">" . $a_hw_child['hw_speed']   . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"has" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'has');\">" . $a_hw_child['hw_asset']   . "</td>\n";
              $hardware .= "  <td class=\"ui-widget-content\" id=\"hsn" . $a_hw_child['hw_id'] . "\" onclick=\"edit_Hardware(" . $a_hw_child['hw_id'] . ",'hsn');\">" . $a_hw_child['hw_serial']  . "</td>\n";
              $hardware .= "</tr>\n";
            }
          }
        }
      }

      if ($formVars['csv'] == 'true') {
        $hardware .= "</p>\n";
      } else {
        $hardware .= "</table>\n";
        $hardware .= $formVars['URL'] . $hardwareurl;
        $hardware .= "</form>\n";
      }

      print "document.getElementById('hardware_mysql').innerHTML = '" . mysqli_real_escape_string($db, $hardware) . "';\n";


# interfaces
      if ($formVars['csv'] == 'true') {
        $interface  = "<p>\"Server Name\",";
        $interface .= "\"Interface Name\",";
        $interface .= "\"Domain\",";
        $interface .= "\"Management\",";
        $interface .= "\"Secure Shell\",";
        $interface .= "\"Backup\",";
        $interface .= "\"OMI\",";
        $interface .= "\"Nagios\",";
        $interface .= "\"Type\",";
        $interface .= "\"Logical Interface\",";
        $interface .= "\"IP Address\",";
        $interface .= "\"Netmask\",";
        $interface .= "\"Zone\",";
        $interface .= "\"Gateway\",";
        $interface .= "\"VLAN\",";
        $interface .= "\"Physical Port\",";
        $interface .= "\"Media\",";
        $interface .= "\"Switch\",";
        $interface .= "\"Port\"\n</br>";
      } else {
        $interface  = "<table id=\"interface-table\" class=\"ui-styled-table\">\n";
        $interface .= "<tr>\n";
        $interface .= "  <th class=\"ui-state-default\">Server Name</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Interface Name</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Domain</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Mgt</th>\n";
        $interface .= "  <th class=\"ui-state-default\">SSH</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Bkup</th>\n";
        $interface .= "  <th class=\"ui-state-default\">OMI</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Nag</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Type</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Logical Interface</th>\n";
        $interface .= "  <th class=\"ui-state-default\">IP Address</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Netmask</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Zone</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Gateway</th>\n";
        $interface .= "  <th class=\"ui-state-default\">VLAN</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Physical Port</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Media</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Switch</th>\n";
        $interface .= "  <th class=\"ui-state-default\">Port</th>\n";
        $interface .= "</tr>\n";
      }

      $servername = '&nbsp;';
      $q_string  = "select inv_id,inv_name,inv_function,inv_appadmin,inv_ssh,grp_name,prod_name,prj_name,svc_name,loc_name,inv_row,inv_rack,inv_unit,inv_callpath,inv_ansible,inv_ssh ";
      $q_string .= "from inventory ";
      $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "where inv_status = 0 and inv_manager = " . $formVars['group'] . " ";
      if ($formVars['location'] > 0) {
        $q_string .= "and inv_location = " . $formVars['location'] . " ";
      }
      if ($formVars['productid'] > 0) {
        $q_string .= "and inv_product = " . $formVars['productid'] . " ";
      }
      if ($formVars['projectid'] > 0) {
        $q_string .= "and inv_project = " . $formVars['projectid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_inventory = mysqli_fetch_array($q_inventory)) {

        $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
        $linkend   = "</a>";
        $servername = $a_inventory['inv_name'];

        $q_string  = "select int_id,int_server,int_domain,int_openview,int_nagios,int_management,int_backup,int_face,int_login,";
        $q_string .= "int_sysport,int_addr,int_mask,zone_name,int_gate,int_switch,int_port,itp_acronym,int_virtual,med_text,int_vlan ";
        $q_string .= "from interface ";
        $q_string .= "left join ip_zones  on ip_zones.zone_id = interface.int_zone ";
        $q_string .= "left join inttype   on inttype.itp_id   = interface.int_type ";
        $q_string .= "left join int_media on int_media.med_id = interface.int_media ";
        $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = 0 ";
        $q_string .= "order by int_server,int_face";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_interface) > 0) {
          while ($a_interface = mysqli_fetch_array($q_interface)) {

            $class = "ui-widget-content";
            $virtual = '';
            if ($a_interface['int_virtual']) {
              $class = "ui-state-highlight";
              $virtual = ' (v)';
            }

            if ($formVars['csv'] == 'true') {
              $is_management = "No";
              if ($a_interface['int_management']) {
                $is_management = "Yes";
              }
              $is_ssh = "No";
              if ($a_interface['int_login']) {
                $is_ssh = "Yes";
              }
              $is_backedup = "No";
              if ($a_interface['int_backup']) {
                $is_backedup = "Yes";
              }
              $is_omi = "No";
              if ($a_interface['int_openview']) {
                $is_omi = "Yes";
              }
              $is_nagios = "No";
              if ($a_interface['int_nagios']) {
                $is_nagios = "Yes";
              }

              $interface .= "\"" . $servername                  . "\",";
              $interface .= "\"" . $a_interface['int_server']   . "\",";
              $interface .= "\"" . $is_management               . "\",";
              $interface .= "\"" . $a_interface['int_domain']   . "\",";
              $interface .= "\"" . $is_ssh                      . "\",";
              $interface .= "\"" . $is_backedup                 . "\",";
              $interface .= "\"" . $is_omi                      . "\",";
              $interface .= "\"" . $is_nagios                   . "\",";
              $interface .= "\"" . $a_interface['itp_acronym']  . "\",";
              $interface .= "\"" . $a_interface['int_face']     . "\",";
              $interface .= "\"" . $a_interface['int_addr']     . "\",";
              $interface .= "\"" . $a_interface['int_mask']     . "\",";
              $interface .= "\"" . $a_interface['zone_name']    . "\",";
              $interface .= "\"" . $a_interface['int_gate']     . "\",";
              $interface .= "\"" . $a_interface['int_vlan']     . "\",";
              $interface .= "\"" . $a_interface['int_sysport']  . "\",";
              $interface .= "\"" . $a_interface['med_text']     . "\",";
              $interface .= "\"" . $a_interface['int_switch']   . "\",";
              $interface .= "\"" . $a_interface['int_port']     . "\"\n</br>";
            } else {
              if ($a_interface['int_server'] == '') {
                $a_interface['int_server'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_domain'] == '') {
                $a_interface['int_domain'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              $is_management = "";
              if ($a_interface['int_management']) {
                $is_management = " checked";
              }
              $is_ssh = "";
              if ($a_interface['int_login']) {
                $is_ssh = " checked";
              }
              $is_backedup = "";
              if ($a_interface['int_backup']) {
                $is_backedup = " checked";
              }
              $is_omi = "";
              if ($a_interface['int_openview']) {
                $is_omi = " checked";
              }
              $is_nagios = "";
              if ($a_interface['int_nagios']) {
                $is_nagios = " checked";
              }

              if ($a_interface['itp_acronym'] == '') {
                $a_interface['itp_acronym'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_face'] == '') {
                $a_interface['int_face'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_addr'] == '') {
                $a_interface['int_addr'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['zone_name'] == '') {
                $a_interface['zone_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_gate'] == '') {
                $a_interface['int_gate'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_vlan'] == '') {
                $a_interface['int_vlan'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_sysport'] == '') {
                $a_interface['int_sysport'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['med_text'] == '') {
                $a_interface['med_text'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_switch'] == '') {
                $a_interface['int_switch'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }
              if ($a_interface['int_port'] == '') {
                $a_interface['int_port'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
              }

              $interface .= "<tr>\n";
              $interface .= "  <td class=\"" . $class . "\">" . $linkstart . $servername                       . $linkend . "</td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fsn" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fsn');\"><u>" . $a_interface['int_server']       . "</u>" . $virtual . "</td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fdn" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fdn');\"><u>" . $a_interface['int_domain']       . "</u>" . $virtual . "</td>\n";
              $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_management . " id=\"fmg" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fmg');\"></td>\n";
              $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_ssh        . " id=\"fsh" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fsh');\"></td>\n";
              $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_backedup   . " id=\"fbu" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fbu');\"></td>\n";
              $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_omi        . " id=\"fov" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fov');\"></td>\n";
              $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_nagios     . " id=\"fng" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fng');\"></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fia" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fia');\"><u>" . $a_interface['itp_acronym']      . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"ffc" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'ffc');\"><u>" . $a_interface['int_face']         . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fad" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fad');\"><u>" . $a_interface['int_addr']         . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fan" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fan');\"><u>" . $a_interface['int_mask']         . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fzn" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fzn');\"><u>" . $a_interface['zone_name']        . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fgw" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fgw');\"><u>" . $a_interface['int_gate']         . "</u></td>\n";
              $interface .= "  <td class=\"" . $class . "\" id=\"fvl" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fvl');\"><u>" . $a_interface['int_vlan']         . "</u></td>\n";
              if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
                $interface .= "  <td class=\"" . $class . "\" id=\"fsp" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fsp');\"><u>" . $a_interface['int_sysport']    . "</u></td>\n";
                $interface .= "  <td class=\"" . $class . "\" id=\"fmt" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fmt');\"><u>" . $a_interface['med_text']       . "</u></td>\n";
                $interface .= "  <td class=\"" . $class . "\" id=\"fsw" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fsw');\"><u>" . $a_interface['int_switch']     . "</u></td>\n";
                $interface .= "  <td class=\"" . $class . "\" id=\"fpt" . $a_interface['int_id'] . "\" onclick=\"edit_Interface(" . $a_interface['int_id'] . ",'fpt');\"><u>" . $a_interface['int_port']       . "</u></td>\n";
              } else {
                $interface .= "  <td class=\"delete " . $class . "\" colspan=\"4\">Virtual Machine</td>\n";
              }
              $interface .= "</tr>\n";
            }

            $q_string  = "select int_id,int_server,int_domain,int_face,int_sysport,int_addr,int_mask,zone_name,int_gate,int_openview,int_login,";
            $q_string .= "int_switch,int_port,itp_acronym,int_virtual,med_text,int_vlan,int_management,int_backup,int_nagios ";
            $q_string .= "from interface ";
            $q_string .= "left join ip_zones  on ip_zones.zone_id = interface.int_zone ";
            $q_string .= "left join inttype   on inttype.itp_id   = interface.int_type ";
            $q_string .= "left join int_media on int_media.med_id = interface.int_media ";
            $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = " . $a_interface['int_id'] . " ";
            $q_string .= "order by int_server,int_face";
            $q_int_child = mysqli_query($db, $q_string);
            if (mysqli_num_rows($q_int_child) > 0) {
              while ($a_int_child = mysqli_fetch_array($q_int_child)) {

                $class = "ui-widget-content";
                $virtual = '';
                if ($a_int_child['int_virtual']) {
                  $class = "ui-state-highlight";
                  $virtual = ' (v)';
                }

                if ($formVars['csv'] == 'true') {
                  $is_management = "No";
                  if ($a_int_child['int_management']) {
                    $is_management = "Yes";
                  }
                  $is_ssh = "No";
                  if ($a_int_child['int_login']) {
                    $is_ssh = "Yes";
                  }
                  $is_backedup = "No";
                  if ($a_int_child['int_backup']) {
                    $is_backedup = "Yes";
                  }
                  $is_omi = "No";
                  if ($a_int_child['int_openview']) {
                    $is_omi = "Yes";
                  }
                  $is_nagios = "No";
                  if ($a_int_child['int_nagios']) {
                    $is_nagios = "Yes";
                  }

                  $interface .= "\"" . $servername                   . "\",";
                  $interface .= "\"" . $a_int_child['int_server']    . "\",";
                  $interface .= "\"" . $a_int_child['int_domain']    . "\",";
                  $interface .= "\"" . $is_management                . "\",";
                  $interface .= "\"" . $is_ssh                       . "\",";
                  $interface .= "\"" . $is_backedup                  . "\",";
                  $interface .= "\"" . $is_omi                       . "\",";
                  $interface .= "\"" . $is_nagios                    . "\",";
                  $interface .= "\"" . $a_int_child['itp_acronym']   . "\",";
                  $interface .= "\"" . $a_int_child['int_face']      . "\",";
                  $interface .= "\"" . $a_int_child['int_addr']      . "\",";
                  $interface .= "\"" . $a_int_child['int_mask']      . "\",";
                  $interface .= "\"" . $a_int_child['zone_name']     . "\",";
                  $interface .= "\"" . $a_int_child['int_gate']      . "\",";
                  $interface .= "\"" . $a_int_child['int_vlan']      . "\",";
                  $interface .= "\"" . $a_int_child['int_sysport']   . "\",";
                  $interface .= "\"" . $a_int_child['med_text']      . "\",";
                  $interface .= "\"" . $a_int_child['int_switch']    . "\",";
                  $interface .= "\"" . $a_int_child['int_port']      . "\"\n</br>";
                } else {
                  if ($a_int_child['int_server'] == '') {
                    $a_int_child['int_server'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_domain'] == '') {
                    $a_int_child['int_domain'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  $is_management = "";
                  if ($a_int_child['int_management']) {
                    $is_management = " checked";
                  }
                  $is_ssh = "";
                  if ($a_int_child['int_login']) {
                    $is_ssh = " checked";
                  }
                  $is_backedup = "";
                  if ($a_int_child['int_backup']) {
                    $is_backedup = " checked";
                  }
                  $is_omi = "";
                  if ($a_int_child['int_openview']) {
                    $is_omi = " checked";
                  }
                  $is_nagios = "";
                  if ($a_int_child['int_nagios']) {
                    $is_nagios = " checked";
                  }

                  if ($a_int_child['itp_acronym'] == '') {
                    $a_int_child['itp_acronym'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_face'] == '') {
                    $a_int_child['int_face'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_addr'] == '') {
                    $a_int_child['int_addr'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['zone_name'] == '') {
                    $a_int_child['zone_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_gate'] == '') {
                    $a_int_child['int_gate'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_vlan'] == '') {
                    $a_int_child['int_vlan'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_sysport'] == '') {
                    $a_int_child['int_sysport'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['med_text'] == '') {
                    $a_int_child['med_text'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_switch'] == '') {
                    $a_int_child['int_switch'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if ($a_int_child['int_port'] == '') {
                    $a_int_child['int_port'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }

                  $interface .= "<tr>\n";
                  $interface .= "  <td class=\"" . $class . "\">"      . $servername                              . "</td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fsn" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fsn');\">&gt; <u>" . $a_int_child['int_server']      . "</u>" . $virtual . "</td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fdn" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fdn');\">&gt; <u>" . $a_int_child['int_domain']      . "</u>" . $virtual . "</td>\n";
                  $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_management . " id=\"fmg" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fmg');\"></td>\n";
                  $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_ssh        . " id=\"fsh" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fsh');\"></td>\n";
                  $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_backedup   . " id=\"fbu" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fbu');\"></td>\n";
                  $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_omi        . " id=\"fov" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fov');\"></td>\n";
                  $interface .= "  <td class=\"" . $class . " delete\"><input type=\"checkbox\"" . $is_nagios     . " id=\"fng" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fng');\"></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fia" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fia');\"><u>"      . $a_int_child['itp_acronym']             . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"ffc" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'ffc');\"><u>"      . $a_int_child['int_face']            . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fad" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fad');\"><u>"      . $a_int_child['int_addr']                   . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fan" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fan');\"><u>"      . $a_int_child['int_mask']                 . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fzn" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fzn');\"><u>"      . $a_int_child['zone_name']               . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fgw" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fgw');\"><u>"      . $a_int_child['int_gate']                 . "</u></td>\n";
                  $interface .= "  <td class=\"" . $class . "\" id=\"fvl" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fvl');\"><u>"      . $a_int_child['int_vlan']                 . "</u></td>\n";
                  if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
                    $interface .= "  <td class=\"" . $class . "\" id=\"fsp" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fsp');\"><u>"      . $a_int_child['int_sysport']              . "</u></td>\n";
                    $interface .= "  <td class=\"" . $class . "\" id=\"fmt" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fmt');\"><u>"      . $a_int_child['med_text']                . "</u></td>\n";
                    $interface .= "  <td class=\"" . $class . "\" id=\"fsw" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fsw');\"><u>"      . $a_int_child['int_switch']               . "</u></td>\n";
                    $interface .= "  <td class=\"" . $class . "\" id=\"fpt" . $a_int_child['int_id'] . "\" onclick=\"edit_Interface(" . $a_int_child['int_id'] . ",'fpt');\"><u>"      . $a_int_child['int_port']                 . "</u></td>\n";
                  } else {
                    $interface .= "  <td class=\"delete " . $class . "\" colspan=\"4\">Virtual Machine</td>\n";
                  }
                  $interface .= "</tr>\n";
                }
              }
              $servername = '&nbsp;';
            }
            $linkstart = '';
            $linkend = '';
          }
        }
      }

      if ($formVars['csv'] == 'true') {
        $interface .= "</p>\n";
      } else {
        $interface .= "</table>\n";
        $interface .= $formVars['URL'] . $interfaceurl;
      }

      print "document.getElementById('interface_mysql').innerHTML = '" . mysqli_real_escape_string($db, $interface) . "';\n";


# need to pull up a list of all the physical boxes these servers are connected to
# list them in order
# then list the other servers under each of the physical boxes.

      $q_string  = "select inv_companyid ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_manager = " . $formVars['group'] . " ";












      print "document.getElementById('tree_mysql').innerHTML = '" . mysqli_real_escape_string($db, "testing") . "';\n";


      $tags  = "<table id=\"interface-table\" class=\"ui-styled-table\">\n";
      $tags .= "<tr>\n";
      $tags .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $tags .= "  <th class=\"ui-state-default\">Private</th>\n";
      $tags .= "  <th class=\"ui-state-default\">Group</th>\n";
      $tags .= "  <th class=\"ui-state-default\">Public</th>\n";
      $tags .= "</tr>\n";

      $q_string  = "select inv_id,inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "where inv_status = 0 and inv_manager = " . $formVars['group'] . " ";
      if ($formVars['location'] > 0) {
        $q_string .= "and inv_location = " . $formVars['location'] . " ";
      }
      if ($formVars['productid'] > 0) {
        $q_string .= "and inv_product = " . $formVars['productid'] . " ";
      }
      if ($formVars['projectid'] > 0) {
        $q_string .= "and inv_project = " . $formVars['projectid'] . " ";
      }
      $q_string .= $filter;
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_inventory = mysqli_fetch_array($q_inventory)) {

        $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
        $linkend   = "</a>";

        $tags .= "<tr>\n";
        $tags .= "<td class=\"ui-widget-content\">" . $linkstart . $a_inventory['inv_name'] . $linkend . "</td>\n";

        $tmp_tags = '';
        $q_string  = "select tag_id,tag_name ";
        $q_string .= "from tags ";
        $q_string .= "where tag_companyid = " . $a_inventory['inv_id'] . " and tag_type = 0 and tag_view = 0 and tag_owner = " . $_SESSION['uid'] . " ";
        $q_string .= "order by tag_name ";
        $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_tags) > 0) {
          $comma = "";
          while ($a_tags = mysqli_fetch_array($q_tags)) {
            $tmp_tags .= $comma . "<u>" . $a_tags['tag_name'] . "</u>";
            $comma = ", ";
          }
        } else {
          $tmp_tags = "<u>No Private Tags</u>";
        }
        $tags .= "  <td class=\"ui-widget-content\" id=\"tagv" . $a_inventory['inv_id'] . "\" onclick=\"edit_Tags(" . $a_inventory['inv_id'] . ",'tagv');\">" . $tmp_tags . "</td>\n";
        
        $tmp_tags = '';
        $q_string  = "select tag_id,tag_name ";
        $q_string .= "from tags ";
        $q_string .= "where tag_companyid = " . $a_inventory['inv_id'] . " and tag_type = 0 and tag_view = 1 and tag_group = " . $formVars['group'] . " ";
        $q_string .= "order by tag_name ";
        $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_tags) > 0) {
          $comma = "";
          while ($a_tags = mysqli_fetch_array($q_tags)) {
            $tmp_tags .= $comma . "<u>" . $a_tags['tag_name'] . "</u>";
            $comma = ", ";
          }
        } else {
          $tmp_tags = "<u>No Group Tags</u>";
        }
        $tags .= "  <td class=\"ui-widget-content\" id=\"tagg" . $a_inventory['inv_id'] . "\" onclick=\"edit_Tags(" . $a_inventory['inv_id'] . ",'tagg');\">" . $tmp_tags . "</td>\n";
        
        $tmp_tags = '';
        $q_string  = "select tag_id,tag_name ";
        $q_string .= "from tags ";
        $q_string .= "where tag_companyid = " . $a_inventory['inv_id'] . " and tag_type = 0 and tag_view = 2 ";
        $q_string .= "order by tag_name ";
        $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_tags) > 0) {
          $comma = "";
          while ($a_tags = mysqli_fetch_array($q_tags)) {
            $tmp_tags .= $comma . "<u>" . $a_tags['tag_name'] . "</u>";
            $comma = ", ";
          }
        } else {
          $tmp_tags = "<u>No Public Tags</u>";
        }
        $tags .= "  <td class=\"ui-widget-content\" id=\"tagu" . $a_inventory['inv_id'] . "\" onclick=\"edit_Tags(" . $a_inventory['inv_id'] . ",'tagu');\">" . $tmp_tags . "</td>\n";

        $tags .= "</tr>\n";
      }

      $tags .= "</table>\n";

      print "document.getElementById('tags_mysql').innerHTML = '" . mysqli_real_escape_string($db, $tags) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
