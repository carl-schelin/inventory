<?php
# Script: virtual.pdf.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  if (isset($_GET['rsdp'])) {
    $formVars['rsdp'] = clean($_GET['rsdp'],10);
  } else {
    $formVars['rsdp'] = 0;
  }

  $package = "virtual.pdf.php";

  logaccess($db, $_SESSION['uid'], $package, "VM: " . $formVars['rsdp']);

  $q_string  = "select rsdp_application,rsdp_magic,rsdp_completion,rsdp_function,rsdp_processors,";
  $q_string .= "rsdp_memory,rsdp_ossize,svc_name,usr_last,usr_first,usr_phone,usr_email,bus_name,dep_name,dep_unit,dep_dept,loc_name,prj_name,prj_code,grp_name ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join users         on users.usr_id         = rsdp_server.rsdp_requestor ";
  $q_string .= "left join department    on department.dep_id    = users.usr_deptname ";
  $q_string .= "left join business_unit on business_unit.bus_id = department.dep_unit ";
  $q_string .= "left join groups        on groups.grp_id        = rsdp_server.rsdp_platform ";
  $q_string .= "left join service       on service.svc_id       = rsdp_server.rsdp_service ";
  $q_string .= "left join locations     on locations.loc_id     = rsdp_server.rsdp_location ";
  $q_string .= "left join cities        on cities.ct_id         = locations.loc_city ";
  $q_string .= "left join states        on states.st_id         = locations.loc_state ";
  $q_string .= "left join country       on country.cn_id        = locations.loc_country ";
  $q_string .= "left join projects      on projects.prj_id      = rsdp_server.rsdp_project ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $q_string  = "select os_sysname,operatingsystem.os_software ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "left join operatingsystem on operatingsystem.os_id = rsdp_osteam.os_software ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);


  $output  = "<h1>New Virtual Machine Server Request</h1>";

  $output .= "<h2>Requestor Information</h2>";
  $output .= "<ul>";
  $output .= "  <li><strong>Requestor's Name</strong>: " . $a_rsdp_server['usr_last'] . ", " . $a_rsdp_server['usr_first'] . "</li>";
  $output .= "  <li><strong>Email</strong>: "            . $a_rsdp_server['usr_email'] . "</li>";
  $output .= "  <li><strong>Extension</strong>: "        . $a_rsdp_server['usr_phone'] . "</li>";
  $output .= "  <li><strong>Department</strong>: "       . $a_rsdp_server['bus_name'] . "-" . $a_rsdp_server['dep_name'] . "</li>";
  $output .= "  <li><strong>Dept Code</strong>: "        . $a_rsdp_server['dep_unit'] . "-" . $a_rsdp_server['dep_dept'] . "</li>";
  $output .= "</ul>";

  $output .= "<h2>Project Information</h2>";
  $output .= "<ul>";
  $output .= "  <li><strong>Project Name</strong>: " . $a_rsdp_server['prj_name'] . "</li>";
  $output .= "  <li><strong>Project Code</strong>: " . $a_rsdp_server['prj_code'] . "</li>";
  $output .= "</ul>";

  $output .= "<h2>Support Information</h2>";
  $output .= "<ul>";
  $output .= "  <li><strong>Service Class Definition</strong>: " . $a_rsdp_server['svc_name'] . "</li>";
  $output .= "</ul>";

  $output .= "<h2>Virtual Machine Server Information</h2>";
  $output .= "<ul>";
  $output .= "  <li><strong>Hostname</strong>: "         . $a_rsdp_osteam['os_sysname'] . "</li>";
  $output .= "  <li><strong>System Function</strong>: "  . $a_rsdp_server['rsdp_function'] . "</li>";
  $output .= "  <li><strong># Virtual CPUs</strong>: "   . $a_rsdp_server['rsdp_processors'] . "</li>";
  $output .= "  <li><strong>RAM</strong>: "              . $a_rsdp_server['rsdp_memory'] . " GB</li>";
  $output .= "  <li><strong>Operating System</strong>: " . $a_rsdp_osteam['os_software'] . "</li>";
  $output .= "  <li><strong>OS Disk Storage</strong>: "  . $a_rsdp_server['rsdp_ossize'] . " GB</li>";
  $output .= "</ul>";

  $output .= "<h3>Filesystem Details</h3>";
  $q_string  = "select fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $formVars['rsdp'];
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
    $output .= "<ul>";
    while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
      $output .= "  <li><strong>Additional Disk Storage</strong>: " . $a_rsdp_filesystem['fs_size'] . " GB</li>";
    }
    $output .= "</ul>";
  } else {
    $output .= "<p>No additional disk space.</p>\n";
  } 

  $output .= "<h3>Interface Information</h3>";

  $q_string  = "select if_ip,if_mask,if_gate,if_vlan ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_ipcheck = 1 ";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_interface) > 0) {

    $output .= "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\">\n";
    $output .= "<tr>\n";
    $output .= "<th><strong>IP Address</strong></th>\n";
    $output .= "<th><strong>Subnet</strong></th>\n";
    $output .= "<th><strong>Gateway</strong></th>\n";
    $output .= "<th><strong>VLAN</strong></th>\n";
    $output .= "</tr>\n";

    while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

      $output .= "<tr>\n";
      $output .= "<td>" . $a_rsdp_interface['if_ip'] . "</td>\n";
      $output .= "<td>" . $a_rsdp_interface['if_mask']   . "</td>\n";
      $output .= "<td>" . $a_rsdp_interface['if_gate']   . "</td>\n";
      $output .= "<td>" . $a_rsdp_interface['if_vlan']  . "</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";
  } else {
    $output .= "<p>No configured interfaces.</p>\n";
  }


# PDF setup.
  require_once($Sitedir . '/tcpdf/config/lang/eng.php');
  require_once($Sitedir . '/tcpdf/tcpdf.php');
  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetCreator('Rapid Server Deployment Process');
  $pdf->SetAuthor($_SESSION['username']);
  $pdf->SetTitle('Virtual Machine Request');
  $pdf->SetSubject('Virtual Machine request form for server: ');
  $pdf->SetKeywords('');
#    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
#    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
#    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->setLanguageArray($l);
  $pdf->setFontSubsetting(true);
  $pdf->SetFont('dejavusans', '', 6, '', true);
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $output, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
  $pdf->Output($a_rsdp_osteam['os_sysname'] . '.virtual.machine.request.' . $formVars['rsdp'] . '.pdf', 'I');
?>
