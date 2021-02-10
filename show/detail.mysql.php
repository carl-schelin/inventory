<?php
# Script: detail.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "detail.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string = "select inv_name,inv_companyid,inv_function,inv_class,inv_manager,inv_appadmin,"
            . "grp_name,inv_callpath,inv_rack,inv_row,inv_unit,inv_front,inv_rear,loc_name,loc_addr1,"
            . "loc_addr2,loc_suite,ct_city,st_acronym,loc_zipcode,cn_acronym,loc_details,prod_name,prj_name "
            . "from inventory "
            . "left join locations on locations.loc_id = inventory.inv_location "
            . "left join products on products.prod_id = inventory.inv_product "
            . "left join projects on projects.prj_id = inventory.inv_project "
            . "left join a_groups on a_groups.grp_id = inventory.inv_manager "
            . "left join cities on cities.ct_id = locations.loc_city "
            . "left join states on states.st_id = locations.loc_state "
            . "left join country on country.cn_id = locations.loc_country "
            . "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  if ($a_inventory['inv_companyid'] > 0) {
    $q_string  = "select inv_name ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_id = " . $a_inventory['inv_companyid'];
    $q_chassis = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_chassis = mysqli_fetch_array($q_chassis);
  } else {
    $a_chassis['inv_name'] = '';
  }

  $cfg2html = '/cfg2html/' . date('Ym') . '/' . $a_inventory['inv_name'] . '.html';

  if ($a_inventory['inv_callpath'] == 1) {
    $class_header="ui-state-error";
    $class_detail="ui-state-highlight";
    $e911_detail = "Server is in the 911 call path.";
  } else {
    $class_header="ui-state-default";
    $class_detail="ui-widget-content";
    $e911_detail = "Server is <u>not</u> in the 911 call path.";
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"" . $class_header . "\">";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
    }
  }
  $output .= "System Information";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"" . $class_header . "\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('detail-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"detail-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>System Information</strong>";
  $output .= "  <ul>";
  $output .= "    <li><strong>911 Call Path</strong> - If a system is in the 911 call path, <span class=\"ui-state-error\">the header</span> and <span class=\"ui-state-highlight\">this text</span> will indicate it.</li>";
  $output .= "    <li><strong>Server</strong> - The hostname of a server according to DNS.</li>";
  $output .= "    <li><strong>Function</strong> - The system function; Oracle Database Server, JBoss Application Server, etc.</li>";
  $output .= "    <li><strong>Product</strong> - The product this server is a member of.</li>";
  $output .= "    <li><strong>Platform Managed By</strong> - The group that provides overall management of the physical or virtual system.</li>";
  $output .= "    <li><strong>Application Managed By</strong> - The group that provides overall management of the applications installed on the system.</li>";
  $output .= "    <li><strong>Class</strong> - The Product's service class. The system is one component of the Product. The Product may require 2 9's of uptime but a component like this server can have a higher failure rate.</li>";
  $output .= "    <li><strong>Availability </strong> - The hours and days the <strong>Product</strong> must be available for use.</li>";
  $output .= "    <li><strong>Downtime</strong> - The amount of downtime permitted for this <strong>Product</strong>.</li>";
  $output .= "    <li><strong>MTBF</strong> - The Mean Time Between Failure for this <strong>Product</strong>.</li>";
  $output .= "    <li><strong>Geographic Diversity</strong> - Does this <strong>Product</strong> require Geographic Diversity? This system may be one of a group located in diverse data centers.</li>";
  $output .= "    <li><strong>MTTR</strong> - The Mean Time To Recovery for this <strong>Product</strong>. How long it can take to recover this <strong>Product</strong>.</li>";
  $output .= "    <li><strong>Resource Sharing</strong> - Can other Products coexist on this server?</li>";
  $output .= "    <li><strong>Restore</strong> - Max time to Restore service for this <strong>Product</strong></li>";
  $output .= "  </ul></li>";
  $output .= "  <li><strong>Location Information</strong> - Data Center name, Address, Rack location if appropriate, and if available, a link to a document on data center access requirements.</li>";
  $output .= "  <li><strong>Children</strong> - Lists the systems that are associated with this device.";
  $output .= "  <li><strong>Pictures</strong> - Any pictures of the front or rear of a physical system.</li>";
  $output .= "  <li><strong>Cluster Membership Information</strong> - Lists the systems that are associated with this Cluster IP.";
  $output .= "  <li><strong>System Timeline</strong>";
  $output .= "  <ul>";
  $output .= "    <li><strong>Purchased</strong> - The date the system was purchased.</li>";
  $output .= "    <li><strong>Built</strong> - The date the Operating System was installed and the system accessible to users.</li>";
  $output .= "    <li><strong>Go Live</strong> - The date the system passed InfoSec scans and went live.</li>";
  $output .= "    <li><strong>End of Life</strong> - The anticipated date the system will be retired.</li>";
  $output .= "    <li><strong>Retired</strong> - The date the system was retired.</li>";
  $output .= "    <li><strong>Reallocated</strong> - The date the system was reused in another capacity. Generally systems are disposed of but in a few cases, systems are reused for other tasks. Note new use in Notes field.</li>";
  $output .= "  </ul></li>";
  $output .= "  <li><strong>Platform Specific Information</strong> - These fields are included by system custodians to provide group specific information.</li>";
  $output .= "  <li><strong>External Links</strong> - A list of external links. These links take you to web pages hosted here or on other systems.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<td class=\"" . $class_header . "\" style=\"text-align: center;\" colspan=\"8\"><strong>" . $e911_detail . "</strong></td>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"" . $class_detail . "\" colspan=\"3\"><strong>Server</strong>: " . $a_inventory['inv_name'] . "</td>";
  $output .= "<td class=\"" . $class_detail . "\" colspan=\"3\"><strong>Function</strong>: " . $a_inventory['inv_function'] . "</td>";
  $output .= "<td class=\"" . $class_detail . "\"><strong>Product</strong>: " . $a_inventory['prod_name'] . "</td>";
  $output .= "<td class=\"" . $class_detail . "\"><strong>Project</strong>: " . $a_inventory['prj_name'] . "</td>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"" . $class_detail . "\" colspan=\"3\"><strong>Platform Managed By</strong>: " . $a_inventory['grp_name'] . "</td>";

  $q_string  = "select grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_groups = mysqli_fetch_array($q_groups);

  $output .= "<td class=\"" . $class_detail . "\" colspan=\"5\"><strong>Application Managed By</strong>: " . $a_groups['grp_name'] . "</td>";
  $output .= "</tr>";

  $q_string  = "select svc_name,svc_availability,svc_downtime,svc_mtbf,svc_geographic,svc_mttr,svc_resource,svc_restore ";
  $q_string .= "from service ";
  $q_string .= "where svc_id = " . $a_inventory['inv_class'];
  $q_service = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_service = mysqli_fetch_array($q_service);

  $geographic = 'No';
  if ($a_service['svc_geographic']) {
    $geographic = 'Yes';
  }
  $resource = 'No';
  if ($a_service['svc_resource']) {
    $resource = 'Yes';
  }

  $output .= "<tr>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>Class</strong>: "                . $a_service['svc_name']         . "</td>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>Availability</strong>: "         . $a_service['svc_availability'] . "</td>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>Downtime</strong>: "             . $a_service['svc_downtime']     . "</td>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>MTBF</strong>: "                 . $a_service['svc_mtbf']         . "</td>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>Geographic Diversity</strong>: " . $geographic                    . "</td>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>MTTR</strong>: "                 . $a_service['svc_mttr']         . "</td>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>Resource Sharing</strong>: "     . $resource                      . "</td>\n";
  $output .= "  <td class=\"" . $class_detail . "\"><strong>Restore</strong>: "              . $a_service['svc_restore']      . "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>";

  if (return_Virtual($db, $formVars['id']) == 0) {
    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "<th class=\"ui-state-default\" colspan=\"7\">Location Information</th>";
    $output .= "</tr>";
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_name'] . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_addr1'] . "</td>";
    if (strlen($a_inventory['loc_addr2']) > 0) {
      $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_addr2'] . "</td>";
    }
    if (strlen($a_inventory['loc_suite']) > 0) {
      $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_suite'] . "</td>";
    }
    $output .= "<td class=\"ui-widget-content\">" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . " " .  $a_inventory['loc_zipcode'] . " " .  $a_inventory['cn_acronym'] . "</td>";
#
# show the blade chassis with a link to it and slot
#
    if ($a_inventory['inv_companyid'] > 0) {
      $output .= "<td class=\"ui-widget-content\">";
      $output .= "<strong>Blade Chassis</strong>: ";
      $output .= "<a href=\"inventory.php?server=" . $a_inventory['inv_companyid'] . "\" target=\"_blank\">";
      $output .= $a_chassis['inv_name'] . "</a> ";
      $output .= "<strong>Slot</strong>: " . $a_inventory['inv_unit'] . "</td>\n";
    } else {
      if ($a_inventory['inv_row'] == '') {
        $output .= "<td class=\"ui-widget-content\"><strong>Rack:</strong> Unknown</td>";
      } else {
        $output .= "<td class=\"ui-widget-content\"><strong>Rack:</strong> " . $a_inventory['inv_row'] . $a_inventory['inv_rack'] . "/U" . $a_inventory['inv_unit'] . "</td>";
      }
    }
    if ($a_inventory['loc_details'] == '') {
      $output .= "<td class=\"ui-widget-content\" align=center>&nbsp;</td>";
    } else {
      $output .= "<td class=\"ui-widget-content\" align=center><a href=\"" . $a_inventory['loc_details'] . "\" target=\"_blank\">Data Center Access details</a></td>";
    }
    $output .= "</tr>";
    $output .= "</table>";

    $q_string  = "select inv_id,inv_name,inv_function,grp_name,inv_unit,inv_appadmin,prod_name ";
    $q_string .= "from inventory ";
    $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
    $q_string .= "left join products on products.prod_id = inventory.inv_product ";
    $q_string .= "where inv_companyid = " . $formVars['id'] . " and inv_status = 0 ";
    $q_string .= "order by inv_unit ";
    $q_children = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_children) > 0) {
      $output .= "<table class=\"ui-styled-table\">";
      $output .= "<tr>";
      $output .= "<th class=\"ui-state-default\" colspan=\"6\">Children</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "<th class=\"ui-state-default\">Slot</th>";
      $output .= "<th class=\"ui-state-default\">Server Name</th>";
      $output .= "<th class=\"ui-state-default\">Product</th>";
      $output .= "<th class=\"ui-state-default\">Function</th>";
      $output .= "<th class=\"ui-state-default\">Platform</th>";
      $output .= "<th class=\"ui-state-default\">Application</th>";
      $output .= "</tr>";
      while ($a_children = mysqli_fetch_array($q_children)) {
        $linkstart = "<a href=\"inventory.php?server=" . $a_children['inv_id'] . "\" target=\"_blank\">";
        $linkend   = "</a>";

        $q_string  = "select grp_name ";
        $q_string .= "from a_groups ";
        $q_string .= "where grp_id = " . $a_children['inv_appadmin'] . " ";
        $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_groups = mysqli_fetch_array($q_groups);

        $output .= "<tr>";
        $output .= "<td class=\"ui-widget-content delete\">" . $linkstart . $a_children['inv_unit'] . $linkend . "</td>";
        $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_children['inv_name'] . $linkend . "</td>";
        $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_children['prod_name'] . $linkend . "</td>";
        $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_children['inv_function'] . $linkend . "</td>";
        $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_children['grp_name'] . $linkend . "</td>";
        $output .= "<td class=\"ui-widget-content\">"        . $linkstart . $a_groups['grp_name']   . $linkend . "</td>";
        $output .= "</tr>";
      }
      $output .= "</table>";
    }

    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "<th class=\"ui-state-default\">Pictures</th>";
    $output .= "</tr>";
    if ($a_inventory['inv_front'] > 0) {
      $output .= "<tr>";
      $output .= "<td class=\"ui-widget-content\">Front Picture:</td>";
      $output .= "</tr>";
      $output .= "<tr>";
      $q_string  = "select img_file ";
      $q_string .= "from images ";
      $q_string .= "where img_id = " . $a_inventory['inv_front'];
      $q_images = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_images = mysqli_fetch_array($q_images);

      $output .= "<td class=\"ui-widget-content\" style=\"text-align: center;\">";
        $output .= "<a href=\"" . $Siteroot . "/pictures/" . $a_images['img_file'] . "\">";
          $output .= "<img src=\"" . $Siteroot . "/pictures/" . $a_images['img_file'] . "\" width=\"800\">";
        $output .= "</a>";
      $output .= "</td>";
      $output .= "</tr>";
    }
    if ($a_inventory['inv_rear'] > 0) {
      $output .= "<tr>";
      $output .= "<td class=\"ui-widget-content\">Rear Picture:</td>";
      $output .= "</tr>";
      $output .= "<tr>";
      $q_string  = "select img_file ";
      $q_string .= "from images ";
      $q_string .= "where img_id = " . $a_inventory['inv_rear'];
      $q_images = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_images = mysqli_fetch_array($q_images);

      $output .= "<td class=\"ui-widget-content\" style=\"text-align: center;\">";
        $output .= "<a href=\"" . $Siteroot . "/pictures/" . $a_images['img_file']  . "\">";
          $output .= "<img src=\"" . $Siteroot . "/pictures/" . $a_images['img_file']  . "\" width=\"800\">";
        $output .= "</a>";
      $output .= "</td>";
      $output .= "</tr>";
    }
    $output .= "</table>";
  } else {
    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "<th class=\"ui-state-default\" colspan=\"7\">Location Information</th>";
    $output .= "</tr>";
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_name'] . "</td>";
    $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_addr1'] . "</td>";
    if (strlen($a_inventory['loc_addr2']) > 0) {
      $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_addr2'] . "</td>";
    }
    if (strlen($a_inventory['loc_suite']) > 0) {
      $output .= "<td class=\"ui-widget-content\">" . $a_inventory['loc_suite'] . "</td>";
    }
    $output .= "<td class=\"ui-widget-content\">" . $a_inventory['ct_city'] . ", " . $a_inventory['st_acronym'] . " " .  $a_inventory['loc_zipcode'] . " " .  $a_inventory['cn_acronym'] . "</td>";
    if ($a_inventory['inv_companyid'] > 0) {
      $output .= "<td class=\"ui-widget-content\">";
      $output .= "<strong>Guest Of</strong>: ";
      $output .= "<a href=\"inventory.php?server=" . $a_inventory['inv_companyid'] . "\" target=\"_blank\">";
      $output .= $a_chassis['inv_name'] . "</a></td>\n";
    }
    $output .= "</table>";
  }

  $q_string  = "select inv_id,inv_name,inv_function,grp_name,inv_appadmin ";
  $q_string .= "from inventory ";
  $q_string .= "left join a_groups on a_groups.grp_id = inventory.inv_manager ";
  $q_string .= "where inv_clusterid = " . $formVars['id'] . " and inv_status = 0 ";
  $q_string .= "order by inv_name ";
  $q_children = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_children) > 0) {
    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "<th class=\"ui-state-default\" colspan=\"4\">Cluster Membership Information</th>";
    $output .= "</tr>";
    $output .= "<tr>";
    $output .= "<th class=\"ui-state-default\">Server Name</th>";
    $output .= "<th class=\"ui-state-default\">Function</th>";
    $output .= "<th class=\"ui-state-default\">Platform</th>";
    $output .= "<th class=\"ui-state-default\">Application</th>";
    $output .= "</tr>";
    while ($a_children = mysqli_fetch_array($q_children)) {
      $linkstart = "<a href=\"inventory.php?server=" . $a_children['inv_id'] . "\" target=\"_blank\">";
      $linkend   = "</a>";

      $q_string  = "select grp_name ";
      $q_string .= "from a_groups ";
      $q_string .= "where grp_id = " . $a_children['inv_appadmin'] . " ";
      $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_groups = mysqli_fetch_array($q_groups);

      $output .= "<tr>";
      $output .= "<td class=\"ui-widget-content\">" . $linkstart . $a_children['inv_name']     . $linkend . "</td>";
      $output .= "<td class=\"ui-widget-content\">" . $linkstart . $a_children['inv_function'] . $linkend . "</td>";
      $output .= "<td class=\"ui-widget-content\">" . $linkstart . $a_children['grp_name']     . $linkend . "</td>";
      $output .= "<td class=\"ui-widget-content\">" . $linkstart . $a_groups['grp_name']       . $linkend . "</td>";
      $output .= "</tr>";
    }
    $output .= "</table>";
  }

  $q_string = "select hw_purchased,hw_built,hw_active,hw_eol,hw_retired,hw_reused "
            . "from hardware "
            . "where hw_companyid = " . $formVars['id'] . " "
            . "and hw_primary = 1";
  $q_hardware = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_hardware = mysqli_fetch_array($q_hardware);

  if ($a_hardware['hw_active'] == '1971-01-01') {
    $class = "ui-state-error";
  } else {
    $class = "ui-widget-content";
  }

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\" colspan=\"6\">System Timeline</th>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\"><strong>Purchased</strong>: "   . $a_hardware['hw_purchased'] . "</td>";
  $output .= "<td class=\"ui-widget-content\"><strong>Built</strong>: "       . $a_hardware['hw_built']     . "</td>";
  $output .= "<td class=\"" . $class    . "\"><strong>Go Live</strong>: "     . $a_hardware['hw_active']    . "</td>";
  $output .= "<td class=\"ui-widget-content\"><strong>End of Life</strong>: " . $a_hardware['hw_eol']       . "</td>";
  $output .= "<td class=\"ui-widget-content\"><strong>Retired</strong>: "     . $a_hardware['hw_retired']   . "</td>";
  $output .= "<td class=\"ui-widget-content\"><strong>Reallocated</strong>: " . $a_hardware['hw_reused']    . "</td>";
  $output .= "</tr>";
  $output .= "</table>";


  $q_string = "select inv_name,inv_ssh,inv_centrify,inv_document,inv_adzone,inv_domain,inv_notes "
            . "from inventory "
            . "where inv_id = " . $formVars['id'];
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\" colspan=\"4\">Platform Specific Information</th>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\"><strong>Date Added to Centrify</strong>: " . $a_inventory['inv_centrify'] . "</td>";
  $output .= "<td class=\"ui-widget-content\"><strong>Active Directory Zone</strong>: "  . $a_inventory['inv_adzone']   . "</td>";
  $output .= "<td class=\"ui-widget-content\"><strong>Centrify Domain</strong>: "        . $a_inventory['inv_domain']   . "</td>";

  $output .= "<td class=\"ui-widget-content\">This server is ";
  if ($a_inventory['inv_ssh'] == 0) {
    $output .= "not ";
  }
  $output .= "accessible via SSH.</td>";
  $output .= "</tr>";
  $output .= "<tr>";
  $output .= "<td class=\"ui-widget-content\" colspan=\"4\"><strong>Notes</strong>: " . $a_inventory['inv_notes'] . "</td>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">External Links</th>";
  $output .= "</tr>";
  if (strlen($a_inventory['inv_document']) > 0) {
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\"><a href=\"" . $a_inventory['inv_document'] . "\" target=\"_blank\">Link to Project Documentation</a></td>";
    $output .= "</tr>";
  }
  if (file_exists($Sitedir . $cfg2html)) {
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\"><a href=\"" . $Siteurl . $cfg2html . "\" target=\"_blank\">Link to Configuration Documentation (cfg2html)</a></td>";
    $output .= "</tr>";
  }
  if (file_exists($Sitedir . "/servers/" . $a_inventory['inv_name'])) {
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\"><a href=\"" . $Siteurl . "/servers/" . $a_inventory['inv_name'] . "\" target=\"_blank\">Link to raw server data (messages for 30 days, config files, output information, etc)</a></td>";
    $output .= "</tr>";
  }
  $output .= "</table>";

?>

document.getElementById('detail_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

