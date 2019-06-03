<?php
# Script: designed.pdf.php
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

  $package = "designed.pdf.php";

  logaccess($_SESSION['uid'], $package, "SAN: " . $formVars['id']);

  $q_string  = "select rsdp_application,rsdp_completion,";
  $q_string .= "usr_last,usr_first,bus_name,dep_name,";
  $q_string .= "loc_name,";
  $q_string .= "prj_name,prj_code,";
  $q_string .= "grp_name ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join users         on users.usr_id         = rsdp_server.rsdp_requestor ";
  $q_string .= "left join department    on department.dep_id    = users.usr_deptname ";
  $q_string .= "left join business_unit on business_unit.bus_id = department.dep_unit ";
  $q_string .= "left join groups        on groups.grp_id        = rsdp_server.rsdp_application ";
  $q_string .= "left join locations     on locations.loc_id     = rsdp_server.rsdp_location ";
  $q_string .= "left join projects      on projects.prj_id      = rsdp_server.rsdp_project ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

  $q_string  = "select os_sysname,operatingsystem.os_software ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "left join operatingsystem on operatingsystem.os_id = rsdp_osteam.os_software ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_rsdp_osteam = mysql_fetch_array($q_rsdp_osteam);

  $q_string = "select mod_size,pf_row,pf_rack,pf_unit ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
  $q_string .= "where pf_rsdp = " . $formVars['rsdp'];
  $q_rsdp_platform = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_rsdp_platform = mysql_fetch_array($q_rsdp_platform);


  $output  = "<h1>Storage Request</h1>";

  $output .= "<h2>Requestor Information</h2>\n";
  $output .= "<p><strong>Requestor's Name</strong>: "           . $a_rsdp_server['usr_last'] . ", " . $a_rsdp_server['usr_first'];
  $output .= "<br><strong>Business Unit</strong>: "             . $a_rsdp_server['bus_name'] . " " . $a_rsdp_server['dep_name'];
  $output .= "<br><strong>Requested Completion Date</strong>: " . $a_rsdp_server['rsdp_completion'] . "</p>";

  $output .= "<h2>Support Information</h2>\n";
  $output .= "<p><strong>Application Administrator(s)</strong>: " . $a_rsdp_server['grp_name'] . "</p>\n";

  $output .= "<h2>Project Information</h2>\n";
  $output .= "<p><strong>Project Name</strong>: "    . $a_rsdp_server['prj_name'];
  $output .= "<br><strong>Project Number</strong>: " . $a_rsdp_server['prj_code'];

  $output .= "<h2>System Information</h2>\n";
  $output .= "<p><strong>Hostname</strong>: " . $a_rsdp_osteam['os_sysname'];
  $output .= "<br><strong>Host OS</strong>: " . $a_rsdp_osteam['os_software'] . "</p>\n";

  $output .= "<h2>Location Information</h2>\n";
  $output .= "<p><strong>Data Center</strong>: "      . $a_rsdp_server['loc_name'];
  $output .= "<br><strong>Row</strong>: "             . $a_rsdp_platform['pf_row'];
  $output .= "<br><strong>Rack</strong>: "            . $a_rsdp_platform['pf_rack'];
  $output .= "<br><strong>Low Rack Unit #</strong>: " . $a_rsdp_platform['pf_unit'];
  $output .= "<br><strong># of RU's</strong>: "       . $a_rsdp_platform['mod_size'] . "</p>\n";

  $output .= "<h2>Filesystem Information</h2>\n";

  $q_string  = "select fs_volume,fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $formVars['rsdp'];
  $q_rsdp_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_rsdp_filesystem) > 0) {

    $output .= "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\">\n";
    $output .= "<tr>\n";
    $output .= "<th><strong>Mount Point</strong></th>\n";
    $output .= "<th><strong>Volume Size</strong></th>\n";
    $output .= "<th><strong>RAID</strong></th>\n";
    $output .= "<th><strong># HBA Ports</strong></th>\n";
    $output .= "<th><strong>MultiPathing Driver</strong></th>\n";
    $output .= "<th><strong>Volume Purpose</strong></th>\n";
    $output .= "</tr>\n";

    while ($a_rsdp_filesystem = mysql_fetch_array($q_rsdp_filesystem)) {

      $output .= "<tr>\n";
      $output .= "<td>" . $a_rsdp_filesystem['fs_volume'] . "</td>\n";
      $output .= "<td>" . $a_rsdp_filesystem['fs_size']   . " GB</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>&nbsp;</td>\n";
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
  $pdf->SetCreator('Inventory Database');
  $pdf->SetAuthor($_SESSION['username']);
  $pdf->SetTitle('Storage Request');
  $pdf->SetSubject('Storage request form for server: ');
  $pdf->SetKeywords('');
#  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
#  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
#  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->setLanguageArray($l);
  $pdf->setFontSubsetting(true);
  $pdf->SetFont('dejavusans', '', 6, '', true);
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $output, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
  $pdf->Output('storage.request.' . $formVars['id'] . '.pdf', 'I');
?>
