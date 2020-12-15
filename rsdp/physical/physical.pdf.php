<?php
# Script: physical.pdf.php
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

  $package = "physical.pdf.php";

  logaccess($db, $_SESSION['uid'], $package, "Data Center: " . $formVars['rsdp']);

  $q_string  = "select rsdp_application,rsdp_magic,rsdp_completion,";
  $q_string .= "usr_last,usr_first,usr_phone,bus_name,dep_name,";
  $q_string .= "loc_name,loc_addr1,loc_addr2,loc_suite,ct_city,st_state,loc_zipcode,cn_country,";
  $q_string .= "prj_name,prj_code,";
  $q_string .= "grp_name ";
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

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

  $q_string  = "select pf_serial,pf_asset,pf_redundant,pf_row,pf_rack,pf_unit,pf_circuita,pf_circuitb,";
  $q_string .= "mod_vendor,mod_name,part_name,mod_size,mod_plugs,plug_text,volt_text,mod_draw,mod_start ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
  $q_string .= "left join parts on parts.part_id = models.mod_type ";
  $q_string .= "left join int_volts on int_volts.volt_id = models.mod_volts ";
  $q_string .= "left join int_plugtype on int_plugtype.plug_id = models.mod_plugtype ";
  $q_string .= "where pf_rsdp = " . $formVars['rsdp'];
  $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

  $q_string  = "select grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $a_rsdp_server['rsdp_application'];
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_groups = mysqli_fetch_array($q_groups);

  $output  = "<h1>Data Center Request</h1>\n";

  $output .= "<p><strong>Helpdesk Ticket #</strong>: "       . $a_rsdp_server['rsdp_magic'];
  $output .= "<br><strong>Requested Install Date</strong>: " . $a_rsdp_server['rsdp_completion'];

  $output .= "<h2>Data Center Request</h2>\n";

  $output .= "<p><strong>Name</strong>: "           . $a_rsdp_server['usr_last'] . ", " . $a_rsdp_server['usr_first'];
  $output .= "<br><strong>Phone</strong>: "         . $a_rsdp_server['usr_phone'];
  $output .= "<br><strong>Business Unit</strong>: " . $a_rsdp_server['bus_name'];
  $output .= "<br><strong>Department</strong>: "    . $a_rsdp_server['dep_name'];

  $output .= "<h2>Equipment Information</h2>\n";

  $output .= "<p><strong>System Name</strong>: "       . $a_rsdp_osteam['os_sysname'];
  $output .= "<br><strong>Manufacturer</strong>: "     . $a_rsdp_platform['mod_vendor'];
  $output .= "<br><strong>Device Type</strong>: "      . $a_rsdp_platform['part_name'];
  $output .= "<br><strong>Model</strong>: "            . $a_rsdp_platform['mod_name'];
  $output .= "<br><strong>Asset Tag</strong>: "        . $a_rsdp_platform['pf_asset'];
  $output .= "<br><strong>Serial Number</strong>: "    . $a_rsdp_platform['pf_serial'];

  $output .= "<br><strong>People and departments Managing the platform</strong>: "     . $a_rsdp_server['grp_name'];
  $output .= "<br><strong>People and departments Managing the applications</strong>: " . $a_groups['grp_name'];

  $output .= "<h2>Location Information</h2>\n";

  $output .= "<p><strong>Site</strong>: "             . $a_rsdp_server['loc_name'];
  $output .= "<br><strong>Row</strong>: "             . $a_rsdp_platform['pf_row'];
  $output .= "<br><strong>Rack</strong>: "            . $a_rsdp_platform['pf_rack'];
  $output .= "<br><strong>Low Rack Unit #</strong>: " . $a_rsdp_platform['pf_unit'];
  $output .= "<br><strong># of RU's</strong>: "       . $a_rsdp_platform['mod_size'];
  $output .= "<br><strong>Address</strong>: "         . $a_rsdp_server['loc_addr1'];
  $output .= "<br><strong>Address</strong>: "         . $a_rsdp_server['loc_addr2'];
  $output .= "<br><strong>Suite</strong>: "           . $a_rsdp_server['loc_suite'];
  $output .= "<br><strong>City</strong>: "            . $a_rsdp_server['ct_city'];
  $output .= "<br><strong>State</strong>: "           . $a_rsdp_server['st_state'];
  $output .= "<br><strong>Zipcode</strong>: "         . $a_rsdp_server['loc_zipcode'];
  $output .= "<br><strong>Country</strong>: "         . $a_rsdp_server['cn_country'];

  $output .= "<h2>Power Requirements</h2>\n";

  $output .= "<p><strong>Power (Volts)</strong>: "        . $a_rsdp_platform['volt_text'];
  $output .= "<br><strong># of Power Supplies</strong>: " . $a_rsdp_platform['mod_plugs'];

  if ($a_rsdp_platform['pf_redundant'] == 1) {
    $answer = "Yes";
  } else {
    $answer = "No";
  }

  $output .= "<br><strong>Power Redundancy</strong>: "         . $answer;
  $output .= "<br><strong>Plug Type</strong>: "                . $a_rsdp_platform['plug_text'];
  $output .= "<br><strong>Power Draw (Amps)</strong>: "        . $a_rsdp_platform['mod_draw'] . " amps";
  $output .= "<br><strong>Startup Draw (Amps)</strong>: "      . $a_rsdp_platform['mod_start'] . " amps";
  $output .= "<br><strong>Power Circuit A assigned</strong>: " . $a_rsdp_platform['pf_circuita'];
  $output .= "<br><strong>Power Circuit B assigned</strong>: " . $a_rsdp_platform['pf_circuitb'];

  $output .= "<h2>Interface Information</h2>\n";

  $q_string  = "select if_name,med_text,if_sysport,if_switch,if_port ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
  $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_swcheck = 1 ";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_interface) > 0) {

    $output .= "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\">\n";
    $output .= "<tr>\n";
    $output .= "<th>Date Installed</th>\n";
    $output .= "<th>Network Switch</th>\n";
    $output .= "<th>Switch Port</th>\n";
    $output .= "<th>Patch Panel Assignment</th>\n";
    $output .= "<th>Patch Panel Assignment</th>\n";
    $output .= "<th>Interface Name</th>\n";
    $output .= "<th>Media Type</th>\n";
    $output .= "<th>System Slot/Port</th>\n";
    $output .= "<th>Cable #</th>\n";
    $output .= "</tr>\n";

    while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

      $output .= "<tr>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>" . $a_rsdp_interface['if_switch']  . "</td>\n";
      $output .= "<td>" . $a_rsdp_interface['if_port']    . "</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>" . $a_rsdp_interface['if_name']    . "</td>\n";
      $output .= "<td>" . $a_rsdp_interface['med_text']   . "</td>\n";
      $output .= "<td>" . $a_rsdp_interface['if_sysport'] . "</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";
  } else {
    $output .= "<p>No configured interfaces.</p>\n";
  }

  $output .= "<h2>SAN Information</h2>\n";

  $q_string  = "select san_sysport,san_switch,san_port,med_text ";
  $q_string .= "from rsdp_san ";
  $q_string .= "left join int_media on int_media.med_id = rsdp_san.san_media ";
  $q_string .= "where san_rsdp = " . $formVars['rsdp'];
  $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_san) > 0) {

    $output .= "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\">\n";
    $output .= "<tr>\n";
    $output .= "<th>Date Installed</th>\n";
    $output .= "<th>Network Switch</th>\n";
    $output .= "<th>Port</th>\n";
    $output .= "<th>Patch Panel Assignment</th>\n";
    $output .= "<th>Patch Panel Assignment</th>\n";
    $output .= "<th>Interface Name</th>\n";
    $output .= "<th>Media Type</th>\n";
    $output .= "<th>System Slot/Port</th>\n";
    $output .= "<th>Cable #</th>\n";
    $output .= "</tr>\n";

    while ($a_rsdp_san = mysqli_fetch_array($q_rsdp_san)) {

      $output .= "<tr>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>" . $a_rsdp_san['san_switch'] . "</td>\n";
      $output .= "<td>" . $a_rsdp_san['san_port'] . "</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "<td>" . $count . "</td>\n";
      $output .= "<td>" . $a_rsdp_san['med_text']    . "</td>\n";
      $output .= "<td>" . $a_rsdp_san['san_sysport'] . "</td>\n";
      $output .= "<td>&nbsp;</td>\n";
      $output .= "</tr>\n";
    }

    $output .= "</table>\n";
  } else {
    $output .= "<p>No configured HBA interfaces.</p>\n";
  }

####
# PDF setup.
####
  require_once($Sitedir . '/tcpdf/config/lang/eng.php');
  require_once($Sitedir . '/tcpdf/tcpdf.php');
  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetCreator('Rapid Server Deployment Process');
  $pdf->SetAuthor($_SESSION['username']);
  $pdf->SetTitle('Data Center Request Form');
  $pdf->SetSubject('Data Center request form for server: ');
  $pdf->SetKeywords('');
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->setLanguageArray($l);
  $pdf->setFontSubsetting(true);
  $pdf->SetFont('dejavusans', '', 6, '', true);
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $output, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
  $pdf->Output('data.center.request.' . $formVars['rsdp'] . '.pdf', 'I');
?>
