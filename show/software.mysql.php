<?php
# Script: software.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "software.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string  = "select inv_manager ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_id = " . $formVars['id'] . " ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">";
  if (check_userlevel($db, $AL_Edit)) {
    $output .= "<a href=\"" . $Editroot . "/inventory.php?server=" . $formVars['id'] . "#software\" target=\"_blank\"><img src=\"" . $Imgsroot . "/pencil.gif\">";
  }
  $output .= "Software Information";
  if (check_userlevel($db, $AL_Edit)) {
    if (check_grouplevel($db, $a_inventory['inv_manager'])) {
      $output .= "</a>";
    }
  }
  $output .= "</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('software-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"software-help\" style=\"display: none\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<ul>";
  $output .= "  <li><strong>Product</strong> - The product this software is a member of.</li>";
  $output .= "  <li><strong>Vendor</strong> - The software vendor. Clicking on this will load a search page listing all systems associated with this vendor.</li>";
  $output .= "  <li><strong>Software</strong> - The software name and version. Clicking on this will load a search page listing all systems associated with this software package.</li>";
  $output .= "  <li><strong>Type</strong> - The type of software.  Clicking on this will load a search page listing all systems associated with this software type. This is used in various places for reporting (such as OS and Instance).</li>";
  $output .= "  <li><strong>Group</strong> - The group responsible for the software package.</li>";
  $output .= "  <li><strong>Updated</strong> - The last time this entry was updated. A checkmark indicates the software information was gathered automatically.</li>";
  $output .= "</ul>";

  $output .= "<p><span class=\"ui-state-highlight\">Highlighted software</span> have been identified as software that defines or is the focus of this system.</p>";

  $output .= "<p><span class=\"ui-state-error\">Highlighted software</span> have been identified as customer facing</p>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Product</th>";
  $output .= "<th class=\"ui-state-default\">Vendor</th>";
  $output .= "<th class=\"ui-state-default\">Software</th>";
  $output .= "<th class=\"ui-state-default\">Type</th>";
  $output .= "<th class=\"ui-state-default\">Group</th>";
  $output .= "<th class=\"ui-state-default\">Updated</th>";
  $output .= "</tr>";

  $q_string  = "select prod_name,ven_name,sw_software,typ_name,grp_name,svr_verified,svr_update,svr_primary,svr_facing ";
  $q_string .= "from software ";
  $q_string .= "left join svr_software on svr_software.svr_softwareid = software.sw_id ";
  $q_string .= "left join inv_sw_types     on inv_sw_types.typ_id             = software.sw_type ";
  $q_string .= "left join inv_vendors      on inv_vendors.ven_id              = software.sw_vendor ";
  $q_string .= "left join products     on products.prod_id            = software.sw_product ";
  $q_string .= "left join inv_groups     on inv_groups.grp_id             = svr_software.svr_groupid ";
  $q_string .= "where svr_companyid = " . $formVars['id'] . " and typ_name != \"Package\" ";
  $q_string .= "order by typ_name,sw_software";
  $q_software = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_software = mysqli_fetch_array($q_software)) {

    $link_vendor = "<a href=\"" . $Reportroot . "/search.software.php?search_by=3&search_for=" . $a_software['ven_name']    . "\" target=\"_blank\">";
    $link_name   = "<a href=\"" . $Reportroot . "/search.software.php?search_by=3&search_for=" . $a_software['sw_software'] . "\" target=\"_blank\">";
    $link_type   = "<a href=\"" . $Reportroot . "/search.software.php?search_by=3&search_for=" . $a_software['typ_name']    . "\" target=\"_blank\">";
    $linkend     = "</a>";

    $checkmark = "";
    if ($a_software['svr_verified']) {
      $checkmark = "&#x2713;";
    }

    $class = "ui-widget-content";
    if ($a_software['svr_primary']) {
      $class = "ui-state-highlight";
    }
    if ($a_software['svr_facing']) {
      $class = "ui-state-error";
    }

    $output .= "<tr>";
    $output .= "<td class=\"" . $class . "\">"                . $a_software['prod_name']                           . "</td>";
    $output .= "<td class=\"" . $class . "\">" . $link_vendor . $a_software['ven_name']                 . $linkend . "</td>";
    $output .= "<td class=\"" . $class . "\">" . $link_name   . $a_software['sw_software']              . $linkend . "</td>";
    $output .= "<td class=\"" . $class . "\">" . $link_type   . $a_software['typ_name']                 . $linkend . "</td>";
    $output .= "<td class=\"" . $class . "\">"                . $a_software['grp_name']                            . "</td>";
    $output .= "<td class=\"" . $class . "\">"                . $a_software['svr_update']  . $checkmark            . "</td>";
    $output .= "</tr>";

  }

  $output .= "</table>";


  $package  = "<br><a href=\"javascript:;\" onmousedown=\"toggleDiv('showpackages');\">+ View RPM/Package Listing</a>";
  $package .= "<div id=\"showpackages\" style=\"display:none\">";

  $package .= "<table class=\"ui-styled-table\">";
  $package .= "<tr>";
  $package .= "<th class=\"ui-state-default\">Package Information</th>";
  $package .= "<th class=\"ui-state-default\" width=\"5\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('package-help');\">Help</a></th>";
  $package .= "</tr>";
  $package .= "</table>";

  $package .= "<div id=\"package-help\" style=\"display: none\">";

  $package .= "<div class=\"main-help ui-widget-content\">";

  $package .= "<ul>";
  $package .= "  <li><strong>Package</strong> - The installed software package (rpm, pkg, deb, etc).</li>";
  $package .= "  <li><strong>Group</strong> - The group that is responsible for the package installation.</li>";
  $package .= "</ul>";

  $package .= "</div>";

  $package .= "</div>";

  $package .= "<table class=\"ui-styled-table\">";
  $package .= "<tr>";
  $package .= "<th class=\"ui-state-default\">Package Name</th>";
  $package .= "<th class=\"ui-state-default\">Last Updated</th>";
  $package .= "<th class=\"ui-state-default\">Group</th>";
  $package .= "<th class=\"ui-state-default\">Operating System</th>";
  $package .= "</tr>";

  $q_string  = "select pkg_name,pkg_update,pkg_os,grp_name ";
  $q_string .= "from inv_packages ";
  $q_string .= "left join inv_groups on inv_groups.grp_id = inv_packages.pkg_grp_id ";
  $q_string .= "where pkg_inv_id = " . $formVars['id'] . " ";
  $q_string .= "order by pkg_name,pkg_update ";
  $q_inv_packages = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_packages = mysqli_fetch_array($q_inv_packages)) {

    $checkmark = "";
    if ($a_software['svr_verified']) {
      $checkmark = "&#x2713;";
    }
    $package .= "<tr>";
    $package .= "<td class=\"ui-widget-content\">" . $a_inv_packages['pkg_name'] . "</td>";
    $package .= "<td class=\"ui-widget-content\">" . $a_inv_packages['pkg_update'] . "</td>";
    $package .= "<td class=\"ui-widget-content\">" . $a_inv_packages['grp_name'] . "</td>";
    $package .= "<td class=\"ui-widget-content\">" . $a_inv_packages['pkg_os'] . "</td>";
    $package .= "</tr>";

  }

  $package .= "</table>";

?>

document.getElementById('software_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

document.getElementById('package_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $package); ?>';

