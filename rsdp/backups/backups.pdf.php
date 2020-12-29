<?php
# Script: backups.pdf.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  if (isset($_GET['rsdp'])) {
    $formVars['rsdp'] = clean($_GET['rsdp'],10);
  } else {
    $formVars['rsdp'] = 0;
  }

  $package = "backups.pdf.php";

  logaccess($db, $_SESSION['uid'], $package, "Backup: " . $formVars['rsdp']);

  $retention[0] = "None";
  $retention[1] = "Less than 6 Months (Details Required)";
  $retention[2] = "6 Months";
  $retention[3] = "1 Year";
  $retention[4] = "3 Years (Standard)";
  $retention[5] = "7 Years";

  $q_string  = "select usr_last,usr_first,bus_name,dep_name,loc_name,prj_name,prj_code,grp_name ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join users         on users.usr_id         = rsdp_server.rsdp_requestor ";
  $q_string .= "left join department    on department.dep_id    = users.usr_deptname ";
  $q_string .= "left join business_unit on business_unit.bus_id = department.dep_unit ";
  $q_string .= "left join a_groups        on a_groups.grp_id        = rsdp_server.rsdp_platform ";
  $q_string .= "left join service       on service.svc_id       = rsdp_server.rsdp_service ";
  $q_string .= "left join locations     on locations.loc_id     = rsdp_server.rsdp_location ";
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

  $q_string  = "select if_ip ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_type = 1";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);

  $q_string  = "select bu_id,bu_rsdp,bu_start,bu_include,bu_retention,bu_sunday,bu_monday,";
  $q_string .= "bu_tuesday,bu_wednesday,bu_thursday,bu_friday,bu_saturday,bu_suntime,";
  $q_string .= "bu_montime,bu_tuetime,bu_wedtime,bu_thutime,bu_fritime,bu_sattime ";
  $q_string .= "from rsdp_backups ";
  $q_string .= "where bu_rsdp = " . $formVars['rsdp'];
  $q_rsdp_backups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups);



  $output  = '';
  $output .= "<h3>Requestor Information</h3>\n";
  $output .= "<br><strong>Requestor's Name</strong>: " . $a_rsdp_server['usr_last'] . ", " . $a_rsdp_server['usr_first'] . "\n";
  $output .= "<br><strong>Business Unit/Group</strong>: " . $a_rsdp_server['bus_name'] . "/" . $a_rsdp_server['dep_name'] . "\n";

  $output .= "<h3>Support Information</h3>\n";
  $output .= "<br><strong>System Administrator(s)</strong>: " . $a_rsdp_server['grp_name'] . "\n";

  $output .= "<h3>Project Information</h3>\n";
  $output .= "<br><strong>Project Name</strong>: " . $a_rsdp_server['prj_name'] . "\n";
  $output .= "<br><strong>Project Number</strong>: " . $a_rsdp_server['prj_code'] . "\n";

  $output .= "<h3>System Information</h3>\n";
  $output .= "<br><strong>Hostname</strong>: " . $a_rsdp_osteam['os_sysname'] . "\n";
  $output .= "<br><strong>Management IP</strong>: " . $a_rsdp_interface['if_ip'] . "\n";
  $output .= "<br><strong>Entered in DNS</strong>: Yes\n";
  $output .= "<br><strong>Host OS</strong>: " . $a_rsdp_osteam['os_software'] . "\n";

  $output .= "<h3>Retention Information</h3>\n";
  $output .= "<br><strong>Backup Start Date</strong>: " . $a_rsdp_backups['bu_start'] . "\n";
  $output .= "<br><strong>Retention Length</strong>: " . $retention[$a_rsdp_backups['bu_retention']] . "\n";
  $output .= "<br><strong>Data Center Location</strong>: " . $a_rsdp_server['loc_name'] . "\n";
  $output .= "<br><br><strong>Files/drives/volumes to include</strong>:\n";
  $output .= "<br>ALL LOCAL DRIVES\n";

  $q_string  = "select fs_volume,fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $formVars['rsdp'] . " and fs_backup = 1";
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
    $output .= "<br>" . $a_rsdp_filesystem['fs_volume'] . " (" . $a_rsdp_filesystem['fs_size'] . ")\n";
  }

  $output .= "<br><br><strong>Files/drives/volumes to exclude</strong>:\n";

  $q_string  = "select fs_volume,fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $formVars['rsdp'] . " and fs_backup = 0";
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
    $output .= "<br>" . $a_rsdp_filesystem['fs_volume'] . " (" . $a_rsdp_filesystem['fs_size'] . ")\n";
  }

  $output .= "<h3>Backup Window</h3>\n";

  $output .= "<br><strong>Sunday</strong>:\n";
  if ($a_rsdp_backups['bu_sunday']) {
    $output .= "Full <strong>Incremental</strong>\n";
  } else {
    $output .= "<strong>Full</strong> Incremental\n";
  }
  $output .= "Times: " . $a_rsdp_backups['bu_suntime'] . "\n";

  $output .= "<br><strong>Monday</strong>:\n";
  if ($a_rsdp_backups['bu_monday']) {
    $output .= "Full <strong>Incremental</strong>\n";
  } else {
    $output .= "<strong>Full</strong> Incremental\n";
  }
  $output .= "Times: " . $a_rsdp_backups['bu_montime'] . "\n";

  $output .= "<br><strong>Tuesday</strong>:\n";
  if ($a_rsdp_backups['bu_tuesday']) {
    $output .= "Full <strong>Incremental</strong>\n";
  } else {
    $output .= "<strong>Full</strong> Incremental\n";
  }
  $output .= "Times: " . $a_rsdp_backups['bu_montime'] . "\n";

  $output .= "<br><strong>Wednesday</strong>:\n";
  if ($a_rsdp_backups['bu_wednesday']) {
    $output .= "Full <strong>Incremental</strong>\n";
  } else {
    $output .= "<strong>Full</strong> Incremental\n";
  }
  $output .= "Times: " . $a_rsdp_backups['bu_montime'] . "\n";

  $output .= "<br><strong>Thusday</strong>:\n";
  if ($a_rsdp_backups['bu_thursday']) {
    $output .= "Full <strong>Incremental</strong>\n";
  } else {
    $output .= "<strong>Full</strong> Incremental\n";
  }
  $output .= "Times: " . $a_rsdp_backups['bu_montime'] . "\n";

  $output .= "<br><strong>Friday</strong>:\n";
  if ($a_rsdp_backups['bu_friday']) {
    $output .= "Full <strong>Incremental</strong>\n";
  } else {
    $output .= "<strong>Full</strong> Incremental\n";
  }
  $output .= "Times: " . $a_rsdp_backups['bu_montime'] . "\n";

  $output .= "<br><strong>Saturday</strong>:\n";
  if ($a_rsdp_backups['bu_saturday']) {
    $output .= "Full <strong>Incremental</strong>\n";
  } else {
    $output .= "<strong>Full</strong> Incremental\n";
  }
  $output .= "Times: " . $a_rsdp_backups['bu_montime'] . "\n";


####
# PDF setup.
####
  require_once($Sitedir . '/tcpdf/config/lang/eng.php');
  require_once($Sitedir . '/tcpdf/tcpdf.php');
  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetCreator('Rapid Server Deployment Process');
  $pdf->SetAuthor($_SESSION['username']);
  $pdf->SetTitle('Backup Client Request');
  $pdf->SetSubject('Backup client request form for server: ');
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
  $pdf->Output($a_rsdp_osteam['os_sysname'] . '.backup.request' . $formVars['rsdp'] . '.pdf', 'I');

?>
